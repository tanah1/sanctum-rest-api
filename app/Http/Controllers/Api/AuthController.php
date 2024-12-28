<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{


    /**
     * register a new user
     */
    public function register(RegisterRequest $request)
    {
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);

            if($user){
                return ResponseHelper::success('success', 'User registered successfully', $user, 201);
            }
            return ResponseHelper::error('error', 'Unable to register user! Please try again', 400);
        }
        catch(\Exception $e){
            Log::error('Error registering user: ' . $e->getMessage(). ' on line: ' . $e->getLine());
            return ResponseHelper::error('error', 'Unable to register user! Please try again', 500);

        }
    }

    /**
     * login a user
     */
    public function login(LoginRequest $request)
    {
        try{
            if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                return ResponseHelper::error('error', 'Invalid login credentials', 400);
            };

            $user = Auth::user();
            $token = $user->createToken('My Api Token')->plainTextToken;
            return ResponseHelper::success('success', 'User logged in successfully', ['user' => $user, 'token' => $token], 200);
        }
        catch(\Exception $e){
            Log::error('Error Login user: ' . $e->getMessage(). ' on line: ' . $e->getLine());
            return ResponseHelper::error('error', 'Unable to Login user! Please try again', 500);

        }
    }

    public function userProfile()
    {
        try{
            $user = Auth::user();

            if($user){
                return ResponseHelper::success('success', 'User profile fetched successfully', $user, 200);
            }
            return ResponseHelper::error('error', 'Unable to fetch user profile! Please try again', 400);
        }
        catch(\Exception $e){
            Log::error('Error fetching user profile: ' . $e->getMessage(). ' on line: ' . $e->getLine());
            return ResponseHelper::error('error', 'Unable to fetch user profile! Please try again', 500);

        }

    }

    public function logout()
    {
        try{
            $user = Auth::user();
            if($user){
                $user->currentAccessToken()->delete();
                return ResponseHelper::success('success', 'User logged out successfully', [], 200);    
            }
        }
        catch(\Exception $e){
            Log::error('Error logging out user: ' . $e->getMessage(). ' on line: ' . $e->getLine());
            return ResponseHelper::error('error', 'Unable to logout user! Please try again', 500);

        }
    }

}
