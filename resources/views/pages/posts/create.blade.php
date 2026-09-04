<x-layouts::app :title="__('Criar post')">
    <div class="mx-auto flex w-full max-w-xl flex-col gap-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ __('Criar post') }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Escreva o conteúdo para publicar.') }}</p>
        </div>

        @session('status')
            <div class="rounded-md border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ $value }}
            </div>
        @endsession

        @if ($errors->any())
            <div class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('posts.store') }}" class="flex flex-col gap-6">
            @csrf

            <div class="flex flex-col gap-2">
                <label for="content" class="text-sm font-medium">{{ __('Conteúdo') }}</label>
                <textarea
                    name="content"
                    id="content"
                    rows="8"
                    required
                    autofocus
                    class="rounded-md border border-gray-300 px-4 py-2 text-sm focus:border-blue-300 focus:outline-none focus:ring dark:border-gray-600 dark:bg-gray-900"
                >{{ old('content') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('posts.index') }}" class="rounded-sm border border-[#19140035] px-5 py-1.5 text-sm hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]">
                    {{ __('Cancelar') }}
                </a>

                <button type="submit" class="rounded-sm bg-black px-5 py-1.5 text-sm text-white hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200">
                    {{ __('Publicar') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::app>
