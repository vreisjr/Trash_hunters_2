<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />
	<!-- Tipo de conta -->
	<flux:select
    	name="role"
    	:label="__('Account type')"
    	required
	>
    	<option value="user">Usuário</option>
    	<option value="premium">Premium</option>
	</flux:select>

	<!-- Distrito -->
<flux:input
    name="district"
    :label="__('Distrito')"
    :value="old('district')"
    type="text"
    autocomplete="address-level2"
    placeholder="Ex.: Centro"
/>

	<!-- Tipo de perfil -->
	<flux:select
    	name="tipo_perfil"
    	:label="__('Tipo de perfil')"
    	:placeholder="__('Selecione um tipo')"
    	required
	>
    	@foreach (\App\Models\User::TIPOS_PERFIL as $valor => $rotulo)
        	<option value="{{ $valor }}" @selected(old('tipo_perfil') === $valor)>
            	{{ $rotulo }}
        	</option>
    	@endforeach
	</flux:select>
            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>