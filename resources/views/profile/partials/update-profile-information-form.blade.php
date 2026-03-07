<section class="space-y-6">

    <header>
        <h2 class="text-xl font-semibold">
            Profile Information
        </h2>

        <p class="text-sm text-[var(--text-muted)]">
            Update your account details.
        </p>
    </header>

    <form method="POST"
          action="{{ route('profile',['token'=>$encryption->encrypt('edit')]) }}"
          enctype="multipart/form-data"
          class="space-y-6">

        @csrf
        @method('PATCH')

        <div class="grid sm:grid-cols-2 gap-4">

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-1 w-full"
                    :value="old('name',$user->name)"
                    required />
                <x-input-error :messages="$errors->get('name')" class="mt-2"/>
            </div>

            <div>
                <x-input-label for="username" value="Username" />
                <x-text-input
                    id="username"
                    name="username"
                    type="text"
                    class="mt-1 w-full"
                    :value="old('username',$user->username)"
                    required />
                <x-input-error :messages="$errors->get('username')" class="mt-2"/>
            </div>

        </div>

        {{-- Signature --}}
        <div class="space-y-2">

            <x-input-label for="signature" value="Signature"/>

            <div class="flex flex-col sm:flex-row gap-4 items-start">

                <input
                    type="file"
                    name="signature"
                    accept="image/png,image/jpeg,image/webp"
                    class="block w-full text-sm text-gray-400
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-gray-700 file:text-white
                        hover:file:bg-gray-600" />

                @if($user->signature)
                    <img
                        src="{{ asset('storage/'.$user->signature) }}"
                        class="h-16 min-w-16 bg-white p-2 rounded border border-gray-600"/>
                @endif

            </div>

            <x-input-error :messages="$errors->get('signature')" class="mt-2"/>

        </div>

        <div class="flex items-center gap-4">

            <x-secondary-button type="submit">
                Save Changes
            </x-secondary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-500">
                    Saved
                </p>
            @endif

        </div>

    </form>

</section>
