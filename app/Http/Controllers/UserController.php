<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\VerifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


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

    public function get_user(Request $request)
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

    public function get_users(Request $request)
    {

        if (auth()->user()) {
            $per = [
                'str' => substr(auth()->user()->permission, 0, 1),
                'int' => substr(auth()->user()->permission, 1, 1)
            ];
            if ($per['str'] == 'A' || $per['str'] == 'S') {
                $users = User::all();
            } else {
                $users = User::where('permission', 'like', $per['str'] . '%')->where('permission', '>=', $per['str'] . $per['int'])->get();
            }
            return response()->json([
                'response' => $users,
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not logged in!',
            ], 401);
        }
    }

    public function search_user(Request $request)
    {
        try {
            if (auth()->user()) {
                $per = [
                    'str' => substr(auth()->user()->permission, 0),
                    'int' => substr(auth()->user()->permission, 1)
                ];
                if ($per['str'] == 'A' || $per['str'] == 'S') {
                    if ($request->type == 'all') {
                        $users = User::where('name', 'like', '%' . $request->value . '%')
                            ->orWhere('email', 'like', '%' . $request->value . '%')
                            ->orWhere('phone', 'like', '%' . $request->value . '%')
                            ->orWhere('permission', 'like', '%' . $request->value . '%')
                            ->get();
                    } else {
                        $users = User::where($request->type, 'like', '%' . $request->value . '%')->get();
                    }
                } else {
                    if ($request->type == 'all') {
                        $users = User::where('name', 'like', '%' . $request->value . '%')
                            ->orWhere('email', 'like', '%' . $request->value . '%')
                            ->orWhere('phone', 'like', '%' . $request->value . '%')
                            ->orWhere('permission', 'like', '%' . $request->value . '%')
                            ->where('permission', '>=', $per['int'] . '%')
                            ->get();
                    } else {
                        $users = User::where($request->type, 'like', '%' . $request->value . '%')->where('permission', '>', $per['int'] . '%')->get();
                    }
                }
                return response()->json([
                    'response' => $users
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
            if (auth()->user()) {
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
        $phone = $request->phone != null ? $request->phone : null;
        $per = $request->permission != null ? $request->permission : 'D1';
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $phone,
            'permission' => $per,
            'password' => Hash::make($request->password),
        ]);
        $user->save();
        $verCtrl = new VerifyController();
        $verCtrl->sendVerifyEmail($user->email);
        if (!$request->add) {
            $token = $user->createToken('Token')->plainTextToken;
        }
        return response()->json([
            'message' => 'Successfully created user!',
            'login_token' => $token
        ], 200);
    }

    public function info()
    {
        if (auth()->user()) {
            return response()->json([
                'total_users' => User::count()
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not logged in!',
            ], 401);
        }
    }

    public function logout()
    {
        if (auth()->user()) {
            auth()->user()->tokens()->delete();
            return response()->json([
                'message' => 'Successfully logged out!',
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not logged in!',
            ], 401);
        }
    }
    public function changePhoto(Request $request)
    {
        if (auth()->user()) {
            auth()->user()->pp = $request->img;
            auth()->user()->save();
            return response()->json([
                'message' => 'Successfully changed photo!',
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not logged in!',
            ], 401);
        }
    }

    public function checkUser(Request $request)
    {
        if (auth()->user()) {
            return response()->json([
                'message' => 'success',
            ], 200);
        } else {
            return response()->json([
                'message' => 'failed',
            ], 402);
        }
    }
}