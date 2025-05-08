<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request){
        $validatior = Validator::make($request->all(), [
            "email"=> "required|email",
            "password"=> "required|min:6",
        ],[
            "email.required" => "Email is required",
            "email.email" => "Email is not valid",
            "password.required" => "Password is required",
            "password.min" => "Password must be at least 6 characters",
        ]);

        if($validatior->fails()){
            return redirect()->back()
                ->withErrors($validatior)
                ->withInput($request->only("email", "remember"));
        }

        $credentials = $request->only(  "email","password");
        $remember = $request->has("remember");

        if(Auth::attempt($credentials, $remember)){
            $user = Auth::user();
            if ($user->is_delete == 0 && $user->is_active == 1) {
                $request->session()->put('user',[
                    'id'=> $user->id,
                    'email' => $user->email,
                    'group_role' => $user->group_role
                ]);
    
                $user->last_login_at = now();
                $user->last_login_ip = $request->ip();
                $user->save();
    
                $request->session()->regenerate();
                return redirect()->route('product');

            }else{
                return redirect()->back()
                ->withErrors(['email' => 'Account has been clocked'])
                ->withInput($request->only('email', 'remember'));
            }
        }

        return redirect()->back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->withInput($request->only('email', 'remember'));
    }
}
