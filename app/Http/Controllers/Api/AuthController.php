<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function register(Request $request) {
    $validated=$request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
    ]);
    $user=User::create($validated);
    $token=$user->createToken('access_token')->plainTextToken;
    return response()->json([
        'token'=>$token,
        'message'=>'User registered successfully'
    ],201);

   }

   public function login(Request $request) {
    $credentials=$request->validate([
        'email'=>'required|email',
        'password'=>'required'
    ]);
    if(!Auth::attempt(($credentials))){
        return response()->json([
            'message'=>'Invalid credentials'
        ],401);
    } else {
        $user=Auth::user();
        $token=$user->createToken('access_token')->plainTextToken;
        return response()->json([
            'token'=>$token,
            'message'=>'User logged in successfully'
        ],200);
    }
   }
   public function logout(Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json([
        'message'=>'User logged out successfully'
    ],200);
   }
}
