<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request){

        $validation = Validator::make($request->all(), [
            'fullName' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        if($validation->fails()){
            return response()->json([
                'status' => 400, 
                'success'=> false, 
                'message' => $validation->errors()], 
                400);
        }

        $data = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'img' => 'default.jpg',
            'status' => 'Active',
        ]);

        if(!$data){
            return response()->json([
                'status' => 400, 
                'success'=> false, 
                'message' => 'failed signup, try again!'], 
                400);
        }

        return response()->json([
            'status' => 200, 
            'success'=> true, 
            'message' => 
            'signup user successfuly'], 
            200);
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if($validation->fails()){
            return response()->json([
                'status' => 400, 
                'success'=> false, 
                'message' => $validation->errors()], 
                400);
        }

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => 'Email or Password Wrong!'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json([
            'status' => 200,
            'success' => true,
            'data' => auth()->user()
        ], 200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);

    }
}
