<?php

namespace App\Http\Controllers\API;

use Session;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function register(Request $request) {
      try {
          $user = new User();
          $user->name = $request->name;
          $user->email = $request->email;
          $user->password = Hash::make($request->password);
          $user->save();

          $success = true;
          $message = 'User register successfully';
      } catch (\Illuminate\Database\QueryException $ex) {
          $success = false;
          $message = $ex->getMessage();
          if(strpos($message, '1062')) {
            $message = "Email is already used.";
          }
      }

      // response
      $data = [
          'response' => $success,
          'message' => $message,
      ];
      return response()->json($data);
  }
  
  public function login(Request $request) {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $success = true;
            $message = 'User login successfully';
        } else {
            $success = false;
            $message = 'Username and password is invalid.';
        }

        // response
        $data = [
            'response' => $success,
            'message' => $message,
        ];
        return response()->json($data);
    }
    
  public function logout() {
      try {
          Session::flush();
          $success = true;
          $message = 'Successfully logged out';
      } catch (\Illuminate\Database\QueryException $ex) {
          $success = false;
          $message = $ex->getMessage();
      }

      // response
      $data = [
          'response' => $success,
          'message' => $message,
      ];
      return response()->json($data);
  }
  
  public function list() {
    $users = User::all()->toArray();
    
    $data = [
        'response' => true,
        'data' => $users,
    ];
    return response()->json($data);
  }
}
