<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function update(Request $request){

        $user = auth()->user();

        $validation = Validator::make($request->all(), [
            'fullName' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'img' => 'required',
            'status' => 'required'
        ]);

        if($validation->fails()){
            return response()->json([
                'status' => 400, 
                'success'=> false, 
                'message' => $validation->errors()], 
                400);
        }

        $data = User::where('id', $user->id)->update([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'img' => $request->img,
            'status' => $request->status,
        ]);

        if(!$data){
            return response()->json([
                'status' => 400, 
                'success'=> false, 
                'message' => 'failed update data user, try again!'], 
                400);
        }

        return response()->json([
            'status' => 200, 
            'success'=> true, 
            'message' => 
            'update user successfuly'], 
            200);
    }
}
