<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // Fungsi untuk mengarahkan user ke Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Fungsi untuk menangani kembalian dari Google
    public function handleProviderCallback()
    {
        try {
            // Ambil data user dari Google
            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();

            if($finduser){
                Auth::login($finduser);
                return redirect('/home');
            } else {
                $newuser = User::create([
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'google_id'=> $user->id,
                    'password' => Hash::make(Str::random(24)),
                    'profile_picture' => $user->avatar,
                    'email_verified_at' => now(),
                ]);

                Auth::login($newuser);
                return redirect('/home');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}