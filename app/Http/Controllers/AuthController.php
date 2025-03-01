<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);


        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return ResponseHelper::success('User registered successfully', [
            'token' => $token,
            'user' => $user->only(['id', 'name', 'email']),
        ], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = auth()->user()->createToken('API Token')->plainTextToken;
            $user = auth()->user();

            return ResponseHelper::success('Login successful', [
                'token' => $token,
                'user' => $user->only(['id', 'name', 'email']),
            ]);
        }

        return ResponseHelper::error('Unauthorized', 401);
    }

    public function user(Request $request)
    {
        return ResponseHelper::success('User details retrieved successfully', $request->user()->only(['id', 'name', 'email']));
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ResponseHelper::success('User logged out successfully');
    }
}
