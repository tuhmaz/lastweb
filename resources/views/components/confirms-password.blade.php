@props(['title' => __('Confirm Password'), 'description' => __('For security reasons, please confirm your password before continuing.')])

<x-dialog-modal wire:model="confirmingPassword">
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    <x-slot name="content">
        <p class="mb-4 text-sm text-gray-600">{{ $description }}</p>

        <div class="mt-4">
            <x-input type="password" class="mt-1 block w-3/4" placeholder="{{ __('Password') }}"
                     wire:model.defer="password"
                     wire:keydown.enter="confirmPassword" />

            <x-input-error for="password" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="$set('confirmingPassword', false)">
            {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ml-2" wire:click="confirmPassword">
            {{ __('Confirm') }}
        </x-button>
    </x-slot>
</x-dialog-modal>
