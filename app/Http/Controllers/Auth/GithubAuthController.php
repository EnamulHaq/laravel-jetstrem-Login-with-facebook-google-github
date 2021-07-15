<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendGithubUserPasswordEmail;
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

class GithubAuthController extends Controller
{
    public function gitRedirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function gitCallback()
    {
        try {
            $gitHubUser = Socialite::driver('github')->user();
            // Check user email exist or not
            $user = User::where([
                'email' => $gitHubUser->getEmail(),
            ])->first();

            if (!$user) {
                // Create user
                $password = Str::random(8);

                $user = User::create([
                    'name' => $gitHubUser->getName(),
                    'email' => $gitHubUser->getEmail(),
                    'password' => Hash::make( $password ),
                ]);

                $user->avatar_path = $gitHubUser->getAvatar();
                $user->github_token = $gitHubUser->token;
                $user->email_verified_at = Carbon::now();
                $user->save();

                Mail::to($user)->send(new SendGithubUserPasswordEmail($user, $password));
            }

            if (!$user->github_token) {
                $user->github_token = $gitHubUser->token;
                $user->save();
            }

            return redirect('/login')->with('status', 'Github authentication successfylly');
        } catch (ClientException $e) {
            return redirect('/login')->with('status', 'Failed to authenticate with github');
        }
    }
}
