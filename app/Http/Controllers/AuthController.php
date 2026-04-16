<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    public function login()
    {
        return view('backend.auth.login');
    }

    public function login_action(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $session = User::where('email', $request->email)->first();
            // dd($session);
            Session::put('full_name', $session->full_name);

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withInput()->withErrors([
            'password' => 'Wrong username or password',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function forgetPassword()
    {
        return view('backend.auth.forgetPassword');
    }
    public function forgetPasswordAction(Request $request)
    {
        $cekUsers = DB::table('users')->where('email', $request->email)->first();
        $cekUsersToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        $token = hash('sha256', $cekUsers->id = Str::random(40));
        if ($cekUsers == true) {
            $data = [
                'email' => $cekUsers->email,
                'token' => $token,
                'created_at' => now(),
            ];
            Http::get('https://wa.dlhcode.com/send-message?api_key=hZdj1cXOBd9kKEln6dIhE0SOhrUtg9sa&sender=6289636337580&number=' . $cekUsers->no_tlp . '&message=Link reset Password '.url('/resetPassword/'.$token.'').'');
            Alert::success('Maaf, Silahkan Hubungi Admin LP. Maarif Gunungkidul.');
            if ($cekUsersToken == true) {
                DB::table('password_reset_tokens')->where('email', $cekUsersToken->email)->update($data);
            } else {
                DB::table('password_reset_tokens')->insert($data);
            }
            
            return redirect('/login');
        } else {
            Alert::error('Periksa Email anda apakah benar?');
            return Redirect::back()->withInput();
        }
    }
    public function resetPassword($token)
    {
        // dd($token);
        $cekUser = DB::table('password_reset_tokens')->where('token', $token)->first();
        if ($cekUser == true) {
            $data['email'] = $cekUser->email;
            return view('backend.auth.resetPassword', $data);
        } else {
            return redirect('/login');
        }
        
    }
    public function resetPasswordAction(Request $request) {
        $data = [
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ];
        // dd($data);
        Alert::success('Password berhasil direset');
        DB::table('users')->where('email', $request->email)->update($data);
        return redirect('/login');
    }
}
