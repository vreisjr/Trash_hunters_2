<x-layouts::auth :title="__('Log in')">
    <div class="auth-login">
        <div class="auth-login-heading">
            <p class="auth-kicker">Acesso</p>
            <h1>Bem-vindo de volta<span>.</span></h1>
            <p>Entre na sua conta e continue fazendo a diferença.</p>
        </div>

        <x-auth-session-status class="auth-session-status" :status="session('status')" />

        <div class="auth-passkey">
            <x-passkey-verify
                :label="__('Entrar com passkey')"
                :separator="__('OU COM E-MAIL')"
            />
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="auth-login-form">
            @csrf

            <flux:input
                name="email"
                :label="__('E-mail')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="voce@email.com"
            />

            <div class="relative auth-password-field">
                <flux:input
                    name="password"
                    :label="__('Senha')"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0 auth-forgot-link" :href="route('password.request')" wire:navigate>
                        {{ __('Esqueceu?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" :label="__('Lembrar de mim')" :checked="old('remember')" />

            <div>
                <flux:button variant="primary" type="submit" class="auth-submit-button w-full" data-test="login-button">
                    <span>{{ __('Entrar na minha conta') }}</span>
                    <span aria-hidden="true">→</span>
                </flux:button>
            </div>
        </form>

        <div class="auth-register-link">
            <span>{{ __('Ainda não faz parte?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Crie uma conta') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
