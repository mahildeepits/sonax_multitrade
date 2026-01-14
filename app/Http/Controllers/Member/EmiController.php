<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Emi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmiController extends Controller
{
    public function index()
    {
        $user = Auth::guard('member')->user();
        // Auto-generation logic moved to Console Command: MakeMonthlyEmi
        // $joinDate = Carbon::parse($user->created_at);
        // ... loop removed ...
        
        $emis = Emi::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        
        // Hardcoded bank details for now, or fetch from settings if implemented
        $bankDetails = [
            'account_holder' => 'Sonax Digital',
            'bank_name' => 'HDFC Bank',
            'account_number' => '50200012345678',
            'ifsc_code' => 'HDFC0001234',
            'upi_id' => 'sonax@hdfcbank'
        ];

        return view('member.emis.index', compact('emis', 'bankDetails'));
    }

    public function pay(Request $request)
    {
        $request->validate([
            'emi_id' => 'required|exists:emis,id',
            'screenshot' => 'required|image|max:5120', // 5MB max
        ]);

        $emi = Emi::where('id', $request->emi_id)
                  ->where('user_id', Auth::guard('member')->id())
                  ->firstOrFail();

        if ($emi->status == 'approved') {
             return response()->json(['success' => false, 'message' => 'EMI already approved.']);
        }

        if ($request->hasFile('screenshot')) {
            // Delete old valid screenshot if exists? Maybe keep for history.
            $path = $request->file('screenshot')->store('emi_screenshots', 'public');
            
            $emi->screenshot = $path;
            $emi->status = 'submitted'; // Status: submitted for verification
            $emi->paid_at = now();
            $emi->save();

            return response()->json(['success' => true, 'message' => 'Payment submitted for verification.']);
        }

        return response()->json(['success' => false, 'message' => 'Please upload a screenshot.']);
    }
}
