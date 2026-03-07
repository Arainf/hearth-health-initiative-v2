<section class="space-y-6">

    <header>
        <h2 class="text-xl font-semibold">
            Update Password
        </h2>

        <p class="text-sm text-[var(--text-muted)]">
            Use a strong password to keep your account secure.
        </p>
    </header>

    <form method="POST"
          action="{{ route('update',['token'=>$encryption->encrypt('password')]) }}"
          class="space-y-4">

        @csrf
        @method('PUT')

        <div class="grid gap-4">

            <x-input-label for="current_password" value="Current Password"/>
            <x-text-input
                type="password"
                name="current_password"
                class="w-full"/>

            <x-input-error :messages="$errors->updatePassword->get('current_password')"/>

        </div>

        <div class="grid sm:grid-cols-2 gap-4">

            <div>
                <x-input-label for="password" value="New Password"/>
                <x-text-input
                    type="password"
                    name="password"
                    class="w-full"/>
                <x-input-error :messages="$errors->updatePassword->get('password')"/>
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirm Password"/>
                <x-text-input
                    type="password"
                    name="password_confirmation"
                    class="w-full"/>
            </div>

        </div>

        <x-secondary-button type="submit">
            Update Password
        </x-secondary-button>

    </form>

</section>
