<x-layouts::app :title="__('Posts salvos')">
    <div class="mx-auto w-full max-w-4xl px-6 py-8">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                {{ __('Histórico') }}
            </p>
            <h1 class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Posts salvos') }}</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Aqui ficam as publicações que você marcou para consultar depois.') }}
            </p>
        </div>

        <div class="space-y-4">
            @forelse ($posts as $post)
                <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $post->user->name }}</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $post->pivot->created_at?->diffForHumans() }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('posts.salvar', $post) }}">
                            @csrf
                            <button type="submit" class="rounded-full px-3 py-1 text-sm font-medium text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-950">
                                {{ __('Remover dos salvos') }}
                            </button>
                        </form>
                    </div>

                    <p class="mt-4 whitespace-pre-line text-zinc-700 dark:text-zinc-200">{{ $post->content }}</p>

                    <a href="{{ route('posts.index') }}#post-{{ $post->id }}" class="mt-4 inline-flex text-sm font-semibold text-emerald-700 hover:underline dark:text-emerald-300">
                        {{ __('Ver publicação') }}
                    </a>
                </article>
            @empty
                <div class="rounded-2xl bg-white p-8 text-center text-zinc-600 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-700">
                    {{ __('Você ainda não salvou nenhum post.') }}
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $posts->links() }}</div>
    </div>
</x-layouts::app>
