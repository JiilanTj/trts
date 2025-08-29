<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Current Photo -->
        @if($user->photo)
        <div>
            <x-input-label :value="__('Current Photo')" />
            <div class="mt-2">
                <img src="{{ asset('storage/profiles/' . $user->photo) }}" 
                     alt="Profile Photo" 
                     class="w-20 h-20 rounded-full object-cover">
            </div>
        </div>
        @endif

        <!-- Photo Upload -->
        <div>
            <x-input-label for="photo" :value="__('Profile Photo')" />
            <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="full_name" :value="__('Full Name')" />
            <x-text-input id="full_name" name="full_name" type="text" class="mt-1 block w-full" :value="old('full_name', $user->full_name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <!-- Read-only fields -->
        <div>
            <x-input-label for="balance" :value="__('Balance')" />
            <x-text-input id="balance" type="text" class="mt-1 block w-full bg-gray-100" :value="'Rp ' . number_format($user->balance, 0, ',', '.')" readonly />
        </div>

        <div>
            <x-input-label for="level" :value="__('Level')" />
            <x-text-input id="level" type="text" class="mt-1 block w-full bg-gray-100" :value="$user->level" readonly />
        </div>

        <div>
            <x-input-label for="role" :value="__('Role')" />
            <x-text-input id="role" type="text" class="mt-1 block w-full bg-gray-100" :value="ucfirst($user->role)" readonly />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
