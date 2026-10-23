<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="auth-page antialiased">
        <div class="auth-layout">
            <section class="auth-hero">
                <header class="auth-hero-header">
                    <span class="auth-status"><span></span> Plataforma ativa</span>
                </header>

                <a href="{{ route('home') }}" class="auth-logo-wrap" wire:navigate>
                    <div class="auth-logo-frame">
                        <img src="{{ asset('images/trash-hunters-logo-alpha.png') }}" alt="{{ __('Trash Hunters') }}" class="auth-logo">
                        <span class="auth-scanline" aria-hidden="true"></span>
                    </div>
                </a>

                <div class="auth-hero-copy">
                    <h2>Transformando o hoje<br>para um amanhã<br><span>mais sustentável.</span></h2>
                </div>

                <div class="auth-ticker" aria-label="Valores da plataforma">
                    <div>
                        @foreach (['Inteligência', 'Ação', 'Futuro circular', 'Reciclagem', 'Comunidade', 'Impacto real'] as $item)
                            <span>{{ $item }} <b aria-hidden="true">✦</b></span>
                        @endforeach
                        @foreach (['Inteligência', 'Ação', 'Futuro circular', 'Reciclagem', 'Comunidade', 'Impacto real'] as $item)
                            <span>{{ $item }} <b aria-hidden="true">✦</b></span>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="auth-form-panel">
                <a href="{{ route('home') }}" class="auth-mobile-brand" wire:navigate>
                    <img src="{{ asset('images/trash-hunters-logo-alpha.png') }}" alt="{{ config('app.name', 'Trash Hunters') }}">
                </a>
                <div class="auth-form-content">
                    {{ $slot }}
                </div>
                <p class="auth-secure-note"><span aria-hidden="true">✓</span> Conexão segura e criptografada</p>
            </section>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
