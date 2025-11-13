<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $users;
    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    public function register(Request $r)
    {
        try {
            $data = $r->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);

            $user = $this->users->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'user'
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully!',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function login(Request $r)
    {
        $credentials = $r->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => Auth::guard('api')->user()
        ]);
    }

    public function me()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json(['error' => 'User not found or token invalid'], 401);
            }
            return response()->json([
                'message' => 'Authenticated user fetched successfully',
                'user' => $user
            ], 200);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is missing'], 401);
        }
    }


    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
