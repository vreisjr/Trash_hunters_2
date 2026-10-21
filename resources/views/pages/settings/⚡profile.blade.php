<?php

use App\Concerns\ProfileValidationRules;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?TemporaryUploadedFile $profilePhoto = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    public function updateProfilePhoto(): void
    {
        $this->validate([
            'profilePhoto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = Auth::user();
        $oldPath = $user->profile_photo_path;
        $user->profile_photo_path = $this->profilePhoto->store('profile-photos', 'public');
        $user->save();

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $this->reset('profilePhoto');
        Flux::toast(variant: 'success', text: __('Profile photo updated.'));
    }

}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="flex items-center gap-4">
                @if (Auth::user()->profilePhotoUrl())
                    <img
                        src="{{ Auth::user()->profilePhotoUrl() }}"
                        alt="{{ Auth::user()->name }}"
                        class="h-20 w-20 rounded-full border-4 object-cover profile-avatar-{{ Auth::user()->isModerator() ? 'moderator' : 'user' }}"
                    >
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 bg-emerald-100 text-lg font-semibold text-emerald-800 profile-avatar-{{ Auth::user()->isModerator() ? 'moderator' : 'user' }}">
                        {{ Auth::user()->initials() }}
                    </div>
                @endif
                <div class="flex-1">
                    <flux:label>{{ __('Profile photo') }}</flux:label>
                    <input type="file" wire:model="profilePhoto" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm">
                    @error('profilePhoto') <flux:text class="mt-1 text-red-600">{{ $message }}</flux:text> @enderror
                    <flux:button wire:click="updateProfilePhoto" type="button" variant="primary" class="mt-3" wire:loading.attr="disabled">
                        {{ __('Save photo') }}
                    </flux:button>
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

            </div>
        </form>

            <livewire:pages::settings.delete-user-form />
    </x-pages::settings.layout>
</section>
