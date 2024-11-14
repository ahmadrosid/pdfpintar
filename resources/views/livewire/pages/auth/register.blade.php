
<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
]);

$register = function () {
    $validated = $this->validate();

    $validated['password'] = Hash::make($validated['password']);

    event(new Registered($user = User::create($validated)));

    Auth::login($user);

    $this->redirect(route('dashboard', absolute: false), navigate: true);
};

?>

<div class="text-center">
    <p class="pt-2 text-xl font-bold">{{__('Welcome to Pdfpintar')}}</p>
    <p class="pt-2 pb-6">{{__('Register now it\'s free.')}}</p>
    
    <x-button-google-login onclick="loginWithGoogle()" class="justify-center">
        {{__('Register with Google')}}
    </x-button-google-login>

    <form id="social-login-form" action="/login/google/callback" method="POST" style="display: none;">
        {{ csrf_field() }}
        <input id="social-login-access-token" name="social-login-access-token" type="text">
        <input id="social-login-tokenId" name="social-login-tokenId" type="text">
    </form>
</div>
