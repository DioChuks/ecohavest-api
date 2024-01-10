<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HttpResponses;

    public function update(Request $request, User $user)
    {
        try {
            $this->validate($request, [
                'first_name' => 'required|string|min:3|max:100',
                'last_name' => 'required|string|min:3|max:100',
                'email' => 'required|email|unique:users,email,' . $user->id, // Ensure uniqueness for existing user
                'phone' => 'nullable|string|min:10', // Allow nullable phone
                'address' => 'nullable|string',
                'zip' => 'nullable|string',
            ]);
            $user->update($request->all());
            return $this->success([
                'user' => $user->refresh(),
            ], 'Profile updated successfully!', 201);
        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage(), 500);
        }
    }
}
