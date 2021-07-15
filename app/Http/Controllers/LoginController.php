<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function redirect()
    {
        // $user = Auth::guard()->user();

        // if (Auth::guard()->user()) {
        //     $user->id_fb = null;
        //     $user->save();
        // }
        return Socialite::driver('facebook')->redirect();
    }

    public function signinFacebook()
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();
            dd($fbUser->avatar);
            $hasUser = User::where('id_fb', $fbUser->id)->first();

            if($hasUser){
                Auth::login($hasUser);
                return redirect('/dashboard');
            }else{
                $password = Str::random(8);
                $user = User::create([
                    'name' => $fbUser->name,
                    'email' => $fbUser->email,
                    'id_fb' => $fbUser->id,
                    'password' => Hash::make($password)
                ]);
                $user->avatar_path = $fbUser->avatar;
                $user->fb_token = $fbUser->token;
                $user->email_verified_at = Carbon::now();
                $user->save();

                // Mail::to($user)->send(new SendGithubUserPasswordEmail($user, $password));
            }
            
        } catch (ClientException $e) {
            return redirect()->away('/' . '?error=Failed to authenticate with github');
        }
    }
}
