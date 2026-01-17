<?php

use App\Core\UseCases\UserManagement\CreateMassageTherapistUser;
use App\Models\User;
use App\Models\Users\UserData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $last_name = '';
    public string $dni = '';
    public string $phone = '';
    public string $birth_date = '';
    public string $gender = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function registerTherapist(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'string', 'max:255', 'unique:'. UserData::class],
            'phone' => ['required', 'string', 'max:255', 'unique:'. UserData::class],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = app(CreateMassageTherapistUser::class)->execute($validated);

        Auth::login($user);

        redirect()->route('filament.app.pages.dashboard');
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth.create_therapist_account')" :description="__('auth.enter_details')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="registerTherapist" class="flex flex-col gap-6">
        <div class="grid grid-cols-2 gap-x-4 gap-y-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('auth.label.name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')" />

        <!-- Last Name -->
        <flux:input
            wire:model="last_name"
            :label="__('auth.label.last_name')"
            type="text"
            required
            autocomplete="last_name"
            :placeholder="__('Last name')" />

        </div>
        <!-- DNI -->
        <flux:input
            wire:model="dni"
            :label="__('auth.label.dni')"
            type="text"
            required
            autocomplete="dni"
            :placeholder="__('DNI number')" />

        <!-- Phone -->
        <flux:input
            wire:model="phone"
            :label="__('auth.label.phone')"
            type="text"
            required
            autocomplete="phone"
            :placeholder="__('Phone number')" />

        <div class="grid grid-cols-2 gap-x-4 gap-y-6">
        <!-- Birth Date -->
        <flux:input
            wire:model="birth_date"
            :label="__('auth.label.birth_date')"
            type="date"
            required
            autocomplete="birth_date" />

        <!-- Gender -->
         <div class="">
        <flux:select wire:model="gender" :label="__('auth.label.gender')" placeholder="Choose gender...">
            <flux:select.option value="male">Masculino</flux:select.option>
            <flux:select.option value="female">Femenino</flux:select.option>
            <flux:select.option value="other">Othro</flux:select.option>
        </flux:select>
         </div>
        </div>
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('auth.label.email')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com" />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('auth.label.password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')" />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('auth.label.confirm_password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')" />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>