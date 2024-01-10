<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Notifications\VerifyEmailNotification;
use App\Models\User;
use App\Notifications\SignUpDoneNotification;
use App\Traits\HttpResponses;

class EmailVerificationController extends Controller
{
    use HttpResponses;

    public function verifyOtpEmail(Request $request)
    {
        $user_id = $request->user_id;
        $otp = $request->otp;

        $checkUserOtp = User::where([
            ['id', $user_id],
            ['otp', $otp]
        ])->first();

        // Check if the user has already been verified
        if(!$checkUserOtp){
            return $this->error(null, 'OTP is invalid');
        }

        if ($checkUserOtp->email_verified_at === null) {
            $checkUserOtp->email_verified_at = now();
            $checkUserOtp->save();
            $checkUserOtp->notify(new SignUpDoneNotification($checkUserOtp));
            return $this->success(null, 'verification confirmed', 201);
        } else {
            return $this->error(null, 'Email already verified');
        }

    }
    
    public function resendOtpVerification(Request $request)
    {
        // Check if the user has already been verified
        $user = $request->user();
    
        if ($user->hasVerifiedEmail()) {
            return $this->error(null, 'Email already verified');
        }
        
        $user->notify(new VerifyEmailNotification($user));

        return $this->success(null, 'Email verification sent');
            
    }
}
