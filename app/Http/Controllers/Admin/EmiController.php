<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Emi;
use App\Models\User;

class EmiController extends Controller
{
    public function index(Request $request)
    {
        $query = Emi::with('user');

        if ($request->has('search') && $request->search) {
             $search = $request->search;
             $query->whereHas('user', function($q) use($search){
                 $q->where('name', 'like', "%{$search}%")
                   ->orWhere('member_id', 'like', "%{$search}%");
             });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $emis = $query->latest()->paginate(20);

        return view('admin.emis.index', compact('emis'));
    }

    public function verify(Request $request)
    {
        $request->validate(['id' => 'required|exists:emis,id']);
        
        $emi = Emi::findOrFail($request->id);

        if ($emi->status === 'approved') {
            return response()->json(['success' => false, 'message' => 'EMI already verified']);
        }

        $emi->status = 'approved';
        $emi->approved_at = now();
        $emi->save();
        
        $user = $emi->user;
        // Update User is_paid to 1 if not already
        if ($user && $user->is_paid == 0) {
            $user->update([
                'is_paid' => 1,
                'user_icon' => 'userpaid.png'
            ]);
        }

        // Generate Income for Sponsor
        if ($user && $user->sponsor_id) {
            $sponsor = User::where('member_id', $user->sponsor_id)->first();
            if ($sponsor) {
                // Count how many EMIs are ALREADY approved for this user (including the current one)
                $approvedEmisCount = Emi::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->count();
                
                $incomeAmount = 0;
                if ($approvedEmisCount == 1) {
                    $incomeAmount = 500;
                } elseif ($approvedEmisCount > 1 && $approvedEmisCount <= 16) {
                    $incomeAmount = 100;
                }

                if ($incomeAmount > 0) {
                    $adminCharge = \App\Models\AdminCharge::first();
                    // $tds = round(($incomeAmount * ($adminCharge->tds_charges ?? 0)) / 100);
                    // $admin_charges = round(($incomeAmount * ($adminCharge->admin_charges ?? 0)) / 100);
                    // $net_amount = $incomeAmount - $tds - $admin_charges;
                    $net_amount = $incomeAmount;
                    \App\Models\UserPayout::create([
                        'user_id' => $sponsor->id,
                        'income_type' => 'direct_income',
                        'amount' => $incomeAmount,
                        'tds' => 0,
                        'admin_charges' => 0,
                        'net_amount' => $net_amount,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'EMI Verified and Income Generated Successfully']);
    }
    
    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|exists:emis,id']);
        
        $emi = Emi::findOrFail($request->id);
        $emi->status = 'rejected'; // Set to rejected (User can still re-upload)
        $emi->save();

        return response()->json(['success' => true, 'message' => 'EMI Rejected. User can upload again.']);
    }
}
