<p>Hi {{ $name  }},</p>
<p>Thank you for creating an account with {{ config('app.name') }}. You can login into your account with github or using the password below.</p>
<div style="width: 100%;">
    <h3 class="otp-code-holder">{{ $password }}</h3>
</div>
<p>Team {{ config('app.name') }}</p>