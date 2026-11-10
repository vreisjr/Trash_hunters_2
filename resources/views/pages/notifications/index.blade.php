<x-layouts::app :title="__('Notificações')">
    <div class="mx-auto w-full max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('Notificações') }}</flux:heading>
                <flux:text>{{ __('Acompanhe as novidades e interações da plataforma.') }}</flux:text>
            </div>

            @if (auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <flux:button type="submit" variant="subtle">{{ __('Marcar todas como lidas') }}</flux:button>
                </form>
            @endif
        </div>

        <div class="space-y-3">
            @forelse ($notifications as $notification)
                @php($data = $notification->data)
                <div class="rounded-xl border p-4 {{ $notification->read_at ? 'border-zinc-200 bg-white' : 'border-emerald-200 bg-emerald-50' }}">
                    <div class="flex items-start justify-between gap-4">
                        @if (! empty($data['url']))
                            <a href="{{ $data['url'] }}" class="font-medium text-zinc-900 hover:underline">
                                {{ $data['message'] }}
                            </a>
                        @else
                            <span class="font-medium text-zinc-900">{{ $data['message'] }}</span>
                        @endif

                        <span class="shrink-0 text-xs text-zinc-500">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>

                    @if (! $notification->read_at)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}" class="mt-3">
                            @csrf
                            <flux:button type="submit" size="sm" variant="subtle">{{ __('Marcar como lida') }}</flux:button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="rounded-xl border border-dashed p-8 text-center">
                    <flux:heading>{{ __('Você ainda não tem notificações.') }}</flux:heading>
                </div>
            @endforelse
        </div>

        {{ $notifications->links() }}
    </div>
</x-layouts::app>
