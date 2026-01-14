<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AllUsersPayoutsDataTable;
use App\Models\UserPayout;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\UserPayoutsDataTable;

class UserPayoutController extends Controller
{
    public function index(UserPayoutsDataTable $dataTable){
        $type = request()->get('type') ?? null;
        $user_id = authUser('member')->id ?? null;
        return $dataTable->with(['type' => $type,'user_id' => $user_id])->render('user-payouts.index',compact('type'));
    }
    public function payoutRequest($id){
        $payout = UserPayout::findOrfail(decrypt($id));
        try {
            if($payout->update(['is_requested' => now()])){
                return back()->with('success','Success|Request is generated successfully');
            }
        } catch (\Throwable $th) {
            return back()->with('error','Error|'.$th->getMessage());
        }
    }
    public function allPayouts(AllUsersPayoutsDataTable $dataTable){
        $type = 'income';
        return $dataTable->with(['type' => $type])->render('admin.user-payouts.index',compact('type'));
    }
    public function requestedPayouts(AllUsersPayoutsDataTable $dataTable){
        $type = 'withdrawal';
        return $dataTable->with(['type' => $type])->render('admin.user-payouts.index',compact('type'));
    }
    public function payPayoutsView($id){
        $payout = UserPayout::find(decrypt($id));
        if($payout == null){
            return ['status' => false,'message' => 'Payout not found','code' => 400];
        }
        $html = view('admin.user-payouts.transaction',compact('id','payout'))->render();
        return ['status' => true,'message' => 'Working','html' => $html,'code' => 200];
    }
    public function payPayouts(Request $request,$id){
        $request->validate([
            'transaction_id' => 'required|unique:user_payouts,transaction_id',
        ]);
        $payout = UserPayout::findOrFail(decrypt($id));
        $data = [
            'transaction_id' => $request->transaction_id,
            'is_paid' => now(),
            'status' => 'paid',
        ];
        try {
            DB::beginTransaction();
            if($payout->update($data)){
                // Check if it's a withdrawal and update linked records
                if ($payout->income_type == 'withdrawal') {
                    // Update linked Transaction model if exists
                    \App\Models\Transaction::where('user_id', $payout->user_id)
                        ->where('type', 'withdrawl')
                        ->where('amount', $payout->amount)
                        ->where('status', 'pending')
                        ->update(['status' => 'success', 'transaction_hash' => $request->transaction_id]);

                    // Update linked WalletTransaction if exists
                    \App\Models\WalletTransaction::where('transaction_id', $payout->transaction_id)
                        ->where('keyword', 'withdrawal')
                        ->where('status', 0)
                        ->update(['status' => 1, 'transaction_id' => $request->transaction_id]);
                }
                
                DB::commit();
                return ['status' => true,'message' => 'User is paid','modal' => true,'code' => 200];
            }
            throw new \Exception("Error Processing Request", 1);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['status' => false,'message' => $th->getMessage(),'code' => 400];
        }
    }

    public function rejectPayout($id){
        $payout = UserPayout::findOrFail(decrypt($id));
        try {
            DB::beginTransaction();
            if($payout->update(['status' => 'rejected', 'is_requested' => null])){
                // If it's a withdrawal, mark linked transactions as rejected
                if ($payout->income_type == 'withdrawal') {
                    \App\Models\Transaction::where('user_id', $payout->user_id)
                        ->where('type', 'withdrawl')
                        ->where('amount', $payout->amount)
                        ->where('status', 'pending')
                        ->update(['status' => 'rejected']);

                    \App\Models\WalletTransaction::where('transaction_id', $payout->transaction_id)
                        ->where('keyword', 'withdrawal')
                        ->where('status', 0)
                        ->update(['status' => 2]); // Use 2 for rejected
                }
                
                DB::commit();
                return back()->with('success','Success|Payout request rejected successfully');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error','Error|'.$th->getMessage());
        }
    }
}
