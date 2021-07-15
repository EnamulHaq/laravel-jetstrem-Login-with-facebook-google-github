<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class SendGoogleUserPassword extends Mailable
{
    use Queueable, SerializesModels;
    protected $user, $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = sprintf('Your account password for %s', config('app.name'));

        return $this
            ->subject($subject)
            ->view('auth.google-auth-password')
            ->with([
                'subject' => $subject,
                'name' => $this->user->name,
                'password' => $this->password,
            ]);
    }
}
