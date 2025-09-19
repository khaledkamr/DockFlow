<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $valid = Auth::attempt([
            "email" => $request->email, 
            "password" => $request->password
        ]);

        if($valid) {
            $user = User::where('email', $request->email)->first();
            return redirect((route('admin.home')))->with('success', "$user->name, مربحاً بك من جديد");
        } 
        else {
            return redirect(route('login.form'))->with('error', 'البريد الإلكتروني او كلمة السر غير صحيحة');
        }
    }

    public function logout() {
        Auth::logout();
        return redirect(route('login.form'))->with('success', 'تم تسجيل الخروج من الحساب');
    }
}
