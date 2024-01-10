<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as Validate;
use App\Notifications\VerifyEmailNotification;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::where('email', $request->email)->first();

        // token based login check
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error(null, 'Credentials Invalid');
        }

        $userResource = UserResource::make($user);
        $token = $user->createToken('secret')->plainTextToken;

        return $this->success([
            'user' => $userResource,
            'token' => $token
        ], 'Logged in successfully');
    }

    public function register(Request $request)
    {
        try {
            $validator = Validate::make($request->all(), [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->messages(), 400);
            }

            $validatedData = $request->all();
            $validatedData['password'] = Hash::make($validatedData['password']);

            $user = User::create($validatedData);

            $user->notify(new VerifyEmailNotification($user));

            $token = $user->createToken('secret')->plainTextToken;

            $userRefresh = $user->refresh();

            $userResource = UserResource::make($userRefresh);

            return $this->success([
                'user' => $userResource,
                'token' => $token,
            ], 'sign up successfully!', 201);
        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage(), 500);
        }
    }

    public function logout()
    {
        try {
            $user = request()->user();
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

            return $this->success(null, 'Logged out!');
        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage(), 500);
        }
    }

    public function forgotPassword(Request $request) //sends email of the password to the user
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error(null, 'Unable to process request!', 404);
        }
        $user->notify(new forgotPasswordNotification($user));
        return $this->success(null, 'An email has been sent to you!');
    }

    public function changeGeneratedForgotPassword(Request $request) //changes the password sent to the user and also changes password for user
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'password' => 'required|string',
            'new_password' => 'required|string|min:8'
        ]);
        $user = User::findOrFail($validatedData['user_id']);
        if (empty($validatedData['password']) || empty($validatedData['new_password'])) return $this->error(null, 400, 'An entry is required!');
        if (!Hash::check($validatedData['password'], $user->password)) return $this->error(null, 422, 'Password invalid!');

        $hashedPassword = Hash::make($validatedData['new_password']);
        $user->update([
            'password' => $hashedPassword,
        ]);

        return $this->success([
            'user' => $user->refresh(),
        ], 'Password changed successfully!');
    }

    public function checkAuth(Request $request)
    {
        try {
            $token = $request->token;

            if (!$token) {
                throw new \Exception('Bearer token not found.');
            }

            $user = request()->user();
            $userToken = $user->tokens()->where('id', $user->currentAccessToken()->id)->first();

            if (!$user || !$userToken) {
                throw new \Exception('Invalid or unauthorized token.');
            }

            return response()->json(['message' => 'Authentication successful', 'isAuthenticated' => true]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'isAuthenticated' => false], 500);
        }
    }
}
