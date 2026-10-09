@props([
    'title',
    'description',
])

<div class="auth-split-auth-header flex w-full flex-col text-center">
    <flux:heading size="xl">{{ $title }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
