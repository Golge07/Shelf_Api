<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string|min:4',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                ], 401);
            }
            if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
                $token = auth()->user()->createToken('AccessToken')->plainTextToken;
                return response()->json([
                    'message' => 'Successfully logged in!',
                    'token' => $token
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_user_by_token(Request $request)
    {
        if ($request->user()) {
            return response()->json([
                'user' => auth()->user()
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not logged in!',
            ], 401);
        }
    }

    public function get_users_by_permission(Request $request)
    {
        if (auth()->user()) {
            $per = [
                'str' => substr(auth()->user()->permission, 0),
                'int' => substr(auth()->user()->permission, 1)
            ];
            if ($per['str'] == 'A') {
                $users = User::all();
            } else {
                $users = User::where('permission', 'like', $per['str'] . '%')->where('permission', '>', $per['str'] . $per['int'])->get();
            }
            return response()->json([
                'users' => $users
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not logged in!',
            ], 401);
        }
    }

    public function search_user_by_permission(Request $request)
    {
        try {
            if (auth()->user()) {
                $per = [
                    'str' => substr(auth()->user()->permission, 0),
                    'int' => substr(auth()->user()->permission, 1)
                ];
                if ($per['str'] == 'A') {
                    $users = User::where($request->type, 'like', '%' . $request->value . '%')->get();
                } else {
                    $users = User::where($request->type, 'like', '%' . $request->value . '%')->where('permission', 'like', $per['str'] . '%')->where('permission', '>', $per['str'] . $per['int'])->get();
                }
                return response()->json([
                    'users' => $users
                ], 200);
            } else {
                return response()->json([
                    'message' => 'You are not logged in!',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            if ($request->user()) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string',
                    'email' => 'required|string|email',
                    'phone' => 'required|string',
                    'permission' => 'required|string',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'message' => $validator->errors()->first(),
                    ], 401);
                }
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    $user->name = $request->name;
                    $user->phone = $request->phone;
                    $user->permission = $request->permission;
                    $user->save();
                    return response()->json([
                        'message' => 'Successfully updated user!',
                        'user' => $user
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'User not found!',
                    ], 401);
                }
            } else {
                return response()->json([
                    'message' => 'You are not logged in!',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            if ($request->user()) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|string|email',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'message' => $validator->errors()->first(),
                    ], 401);
                }
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    $user->delete();
                    return response()->json([
                        'message' => 'Successfully deleted user!',
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'User not found!',
                    ], 401);
                }
            } else {
                return response()->json([
                    'message' => 'You are not logged in!',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:4',
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->save();

        $token = Str::random(60);
        $domain = URL::to('/');
        $link = $domain . '/verify/' . $token;
        $data['user'] = $user;
        $data['link'] = $link;
        $user->remember_token = $token;
        $user->save();
        Mail::send('mail.verify', ['data' => $data], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Verify Mail');
        });
        $token = $user->createToken('Token')->plainTextToken;
        return response()->json([
            'message' => 'Successfully created user!',
            'login_token' => $token
        ], 200);
    }

    public function verify($token)
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

    public function verify_send(Request $request)
    {
        $user = auth()->user();
        $token = Str::random(60);
        $domain = URL::to('/');
        $link = $domain . '/api/user/verify/email/' . $token;
        $data['user'] = $user;
        $data['link'] = $link;

        Mail::send('mail.verify', ['data' => $data], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Verify Mail');
        });

        $user->remember_token = $token;
        $user->save();

        return response()->json([
            'message' => 'Verification link sent to your email'
        ]);
    }
}
