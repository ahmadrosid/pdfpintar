
<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('layouts.guest');

form(LoginForm::class);

$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>
<div class="dark:bg-neutral-700">
    <div class="pb-6 pt-2 text-xl font-bold dark:text-neutral-300 text-neutral-900 text-center">
        <p>{{__('Login to your account')}}</p>
    </div>

    <x-button-google-login onclick="loginWithGoogle()" class="justify-center w-full">
        {{__('Login with Google')}}
    </x-button-google-login>

    <div class="text-center my-4 text-sm tracking-tight">{{__('Or log in with your email and password')}}</div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex justify-between">
                <x-input-label for="password" :value="__('Password')" />

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-neutral-600 hover:text-neutral-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-500 dark:text-neutral-400 dark:hover:text-neutral-600" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="mt-4 flex justify-between">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-neutral-300 text-neutral-600 shadow-sm focus:ring-neutral-500 dark:border-neutral-600 dark:text-neutral-500" name="remember">
                <span class="ms-2 text-sm text-neutral-600 dark:text-neutral-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-neutral-600 hover:text-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-500 dark:text-neutral-400 dark:hover:text-neutral-300" href="{{ route('register') }}" wire:navigate>
                {{__('Don\'t have an account?')}}
            </a>

            <x-primary-button class="ms-3">
                {{ __('Login') }}
            </x-primary-button>
        </div>
    </form>

    <form id="social-login-form" action="/login/google/callback" method="POST" style="display: none;">
        {{ csrf_field() }}
        <input id="social-login-access-token" name="social-login-access-token" type="text">
        <input id="social-login-tokenId" name="social-login-tokenId" type="text">
    </form>
</div>
