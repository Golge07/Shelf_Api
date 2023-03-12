<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use \Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class VerifyController extends Controller
{
    public function verify_mail($token)
    {
        $user = User::where('remember_token', $token)->first();
        if ($user) {
            $user->email_verified = true;
            $user->remember_token = null;
            $user->save();
            return response()->json([
                'message' => 'Successfully verified user!',
            ], 200);
        } else {
            return response()->json([
                'message' => 'User not found!',
            ], 401);
        }
    }


    public function send_verify_mail(Request $request)
    {
        $user = $request->user();
        $token = Str::random(60);
        $domain = URL::to('/');
        $link = $domain . '/api/user/verify/email/' . $token;
        $data['link'] = $link;

        Mail::send('mail.verify', ['user' => $user, 'data' => $data], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Verify Mail');
        });

        $user->remember_token = $token;
        $user->save();

        return response()->json([
            'message' => 'Verification link sent to your email'
        ]);
    }

    public function sendVerifyEmail($email)
    {
        $user = User::where('email', $email)->first();
        $token = Str::random(60);
        $domain = URL::to('/');
        $link = $domain . '/api/user/verify/email/' . $token;
        $data['link'] = $link;

        Mail::send('mail.verify', ['user' => $user, 'data' => $data], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Verify Mail');
        });

        $user->remember_token = $token;
        $user->save();

        return response()->json([
            'message' => 'Verification link sent to your email'
        ]);
    }
}