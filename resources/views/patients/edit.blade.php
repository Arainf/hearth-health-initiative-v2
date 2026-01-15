{{-- ================= EDIT PAGE ================= --}}
@vite(['resources/js/patient-edit.js', 'resources/js/form-flow.js'])

<x-app-layout>
<form
    id="patientForm"
    method="POST"
    action="{{ url('/patients/'.$patient->id) }}"
    class="h-full max-w-7xl mx-auto px-6 py-4 grid grid-rows-[auto_1fr] gap-4 overflow-hidden"
>
    @csrf
    @method('PUT')

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ $patient->last_name }}, {{ $patient->first_name }}
            </h1>
            <p id="dirtyIndicator" class="text-sm text-amber-600 hidden">
                You have unsaved changes
            </p>
        </div>

        <button
            class="hhi-btn hhi-btn-back"
            id="backBtn"
            type="button"
        >

            Back
        </button>
    </div>

    {{-- CONTENT --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 h-full">

        {{-- LEFT --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-5">Basic Information</h2>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input label="Last Name" name="last_name" :value="$patient->last_name" autofocus />
                        <x-form.input label="First Name" name="first_name" :value="$patient->first_name" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input label="Middle Name" name="middle_name" :value="$patient->middle_name" />
                        <x-form.input label="Suffix" name="suffix" :value="$patient->suffix" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input label="Birth Date" type="date" name="birth_date"
                            :value="optional($patient->birth_date)->format('Y-m-d')" />
                        <x-form.input label="Age" type="number" name="age" :value="$patient->age" readonly />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input label="Unit" name="unit" :value="$patient->unit" />
                        <x-form.input label="Phone" name="phone_number" :value="$patient->phone_number" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-5">Family History</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        'Hypertension' => 'Hypertension',
                        'Heart_Attack' => 'Heart Attack < 60y',
                        'Diabetes' => 'Diabetes',
                        'Cholesterol' => 'Cholesterol'
                    ] as $key => $label)
                    <div class="flex items-center justify-between px-4 py-3 border rounded-lg">
                        <span class="text-sm text-gray-600">{!! $label !!}</span>
                        <div class="flex gap-4 text-sm">
                            @foreach ([1 => 'Yes', 0 => 'No'] as $val => $text)
                                <label class="flex items-center gap-1">
                                    <input type="radio" name="family[{{ $key }}]" value="{{ $val }}"
                                        {{ optional($patient->family_history)->$key == $val ? 'checked' : '' }}>
                                    {{ $text }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-5">Physical</h2>
                <div class="space-y-4">
                    <x-form.input label="Height (cm)" name="height" :value="$patient->height" />
                    <x-form.input label="Weight (kg)" name="weight" :value="$patient->weight" />
                    <x-form.input label="BMI" name="bmi" :value="$patient->bmi" readonly />
                </div>
            </div>

            <div class="bg-white rounded-xl border shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-5">Sex</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach (['Male','Female'] as $sex)
                        <label class="flex items-center gap-3 px-4 py-3 rounded-lg border cursor-pointer transition
                            {{ $patient->sex === $sex ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                            <input type="radio" name="sex" value="{{ $sex }}"
                                {{ $patient->sex === $sex ? 'checked' : '' }}>
                            {{ $sex }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl border shadow-sm p-6 flex justify-end gap-3">
                <x-button.button
                    variant="primary"
                    id="openSaveModal"
                    type="button"
                    disabled
                >
                    Save Changes
                </x-button.button>
            </div>
        </div>
    </div>
</form>

{{-- CONFIRM SAVE MODAL --}}
<div id="confirmModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-[400px] space-y-4">
        <h3 class="text-lg font-semibold">Save Changes?</h3>
        <p class="text-sm text-gray-600">This will update the patient record.</p>

        <div class="flex justify-end gap-3">
            <x-button.button variant="ghost" id="cancelSave" type="button">
                Cancel
            </x-button.button>

            <x-button.button variant="primary" id="confirmSave" type="button">
                <span class="save-text">Save</span>
                <span class="save-loader hidden w-4 h-4 border-2 border-white/60 border-t-white rounded-full animate-spin"></span>
            </x-button.button>
        </div>
    </div>
</div>
</x-app-layout>
