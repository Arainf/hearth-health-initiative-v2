<x-app-layout>
    <div class="py-8 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid gap-6 lg:grid-cols-2">

                {{-- LEFT COLUMN --}}
                <div class="space-y-6">

                    <div class="card shadow rounded-xl p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="card shadow rounded-xl p-6">
                        @include('profile.partials.update-password-form')
                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="space-y-6">

                    @can('use-ai')
                        <div class="card shadow rounded-xl p-6">
                            @include('profile.partials.ai-settings-form')
                        </div>
                    @endcan

                    <div class="card shadow rounded-xl p-6 border border-red-500/20">
                        @include('profile.partials.delete-user-form')
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
