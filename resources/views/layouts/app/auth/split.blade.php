<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="auth-split-brand-panel relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                <div class="auth-split-eyebrow relative z-20">
                    <span aria-hidden="true">→</span>
                    UM MOVIMENTO PELO PLANETA
                </div>

                <a href="{{ route('home') }}" class="auth-split-brand-link relative z-20 flex items-center justify-center text-lg font-medium" wire:navigate>
                    <img src="{{ asset('images/trash-hunters-logo.png') }}" alt="{{ __('Símbolo Trash Hunters') }}" class="auth-split-logo">
                    <span class="auth-split-wordmark" aria-label="Trash Hunters">
                        <span class="auth-split-wordmark-line">
                            <span class="auth-split-letter-white">T</span><span class="auth-split-letter-green">R</span><span class="auth-split-letter-a auth-split-letter-white" aria-label="A">Λ</span><span class="auth-split-letter-green">S</span><span class="auth-split-letter-white">H</span>
                        </span>
                        <span class="auth-split-wordmark-line auth-split-wordmark-subline">
                            <span class="auth-split-letter-green">H</span><span class="auth-split-letter-white">U</span><span class="auth-split-letter-green">N</span><span class="auth-split-letter-white">T</span><span class="auth-split-letter-green">E</span><span class="auth-split-letter-white">R</span><span class="auth-split-letter-green">S</span>
                        </span>
                    </span>
                </a>

                <div class="auth-split-message relative z-20">
                    <h2>Transformando o hoje<br>para um amanhã<br><span>mais sustentável.</span></h2>
                    <p>Transformar o mundo começa com quem<br>escolhe fazer a diferença. Começa com você.</p>
                </div>

                <div class="auth-split-footer relative z-20">
                    <span>✦ &nbsp; Inteligência. Ação. Futuro circular.</span>
                    <span aria-hidden="true">→</span>
                </div>
            </div>
            <div class="auth-split-form-panel w-full lg:p-8">
                <div class="auth-split-form-topline">
                    <span aria-hidden="true">→</span>
                    Juntos, fazemos a diferença.
                </div>
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="auth-split-mobile-brand z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <img src="{{ asset('images/trash-hunters-logo.png') }}" alt="{{ config('app.name', 'Trash Hunters') }}" class="auth-split-logo-mobile">
                        <span class="auth-split-wordmark auth-split-wordmark-mobile" aria-hidden="true">
                            <span class="auth-split-wordmark-line">
                                <span class="auth-split-letter-white">T</span><span class="auth-split-letter-green">R</span><span class="auth-split-letter-a auth-split-letter-white" aria-label="A">Λ</span><span class="auth-split-letter-green">S</span><span class="auth-split-letter-white">H</span>
                            </span>
                            <span class="auth-split-wordmark-line auth-split-wordmark-subline">
                                <span class="auth-split-letter-green">H</span><span class="auth-split-letter-white">U</span><span class="auth-split-letter-green">N</span><span class="auth-split-letter-white">T</span><span class="auth-split-letter-green">E</span><span class="auth-split-letter-white">R</span><span class="auth-split-letter-green">S</span>
                            </span>
                        </span>
                    </a>
                    <div class="auth-split-form-leaf" aria-hidden="true">
                        <span>✦</span>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
