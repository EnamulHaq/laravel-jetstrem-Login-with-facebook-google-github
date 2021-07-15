<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendGoogleUserPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
        
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {
      
            $googleUser = Socialite::driver('google')->user();
            $user = User::where([
                'email' => $googleUser->getEmail()
            ])->first();

            if(!$user) {
                $password = Str::random(8);
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->getEmail(),
                    'google_id'=> $googleUser->id,
                    'password' => Hash::make($password)
                ]);

                Mail::to($newUser)->send(new SendGoogleUserPassword($newUser, $password));
            }
            return redirect('/login')->with('status', 'Google authentication successfylly');
      
        } catch (ClientException $e) {
            return redirect('/login')->with('status', 'Failed to authenticate with google');
        }
    }
}
