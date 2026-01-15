@vite(['resources/css/table.css', 'resources/js/form.js', 'resources/js/patient-nav/patientNav.js'])

<style>
    .card {
        background: #ffffff;
        border: 1px solid #e6e7eb;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        border-radius: 12px;
    }

    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<x-app-layout>

    <div class="relative flex flex-col min-h-screen h-auto w-full rounded-xl bg-[#F9F8F6] px-2 pt-2  border-2 shadow-xl border-gray-100">


        {{-- Header --}}
        <div class="flex flex-row px-6 py-3 z-20 sticky top-0 justify-between">
            <div class="flex items-center">
                <p class="circular text-3xl tracking-tighter">Heart Health Initiative Form</p>
            </div>


            <button id="goDashboardBtn2" class="hhi-btn-back hhi-btn">
                <i class="fa-solid fa-house mr-2"></i> Back to Dashboard
            </button>

        </div>


        <!-- FORM -->
        <form id="mainForm" method="POST" action="{{ route('records.store') }}" class="space-y-8 p-6">
            @csrf
            <input type="hidden" id="patient_id" name="patient_id">
            <input type="hidden" id="staff_id" name="staff_id" value="{{ Auth::user()->name }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                <!-- LEFT SIDE ---------------------------------------------->
                <div class="space-y-6">

                    <section id="patientInfoSection" class="card p-6 h-full relative shadow-lg">

                        <div id="patientOverlay"
                             class="absolute inset-0 bg-black/40 rounded-xl hidden z-10"></div>

                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold">Patient Information</h2>
                            <button id="editPatientBtn" class=" hhi-btn-edit hhi-btn text-sm hidden z-20"> <i class="fa-solid fa-pen-to-square mr-2"></i>Edit</button>
                        </div>

                        <!-- EXPANDED FORM ONLY (no summary) -->
                        <div id="patientForm" >
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="last_name" value="Last Name" />
                                    <x-text-input id="last_name" name="last_name" class="mt-1 w-full"/>
                                </div>
                                <div>
                                    <x-input-label for="first_name" value="First Name" />
                                    <x-text-input id="first_name" name="first_name" class="mt-1 w-full"/>
                                </div>

                                <div>
                                    <x-input-label for="middle_name" value="Middle Name" />
                                    <x-text-input id="middle_name" name="middle_name" class="mt-1 w-full"/>
                                </div>
                                <div>
                                    <x-input-label for="suffix" value="Suffix" />
                                    <x-text-input id="suffix" name="suffix" class="mt-1 w-full"/>
                                </div>

                                <div>
                                    <x-input-label for="age" value="Age"/>
                                    <x-text-input id="age" name="age" type="number" class="mt-1 w-full"/>
                                </div>


                                <div>
                                    <x-input-label value="Sex"/>
                                    <div class="flex gap-3 mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="sex" value="Male" class="sex-input">
                                            <span class="ml-2">Male</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="sex" value="Female" class="sex-input">
                                            <span class="ml-2">Female</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-span-2">
                                    <x-input-label for="birth_date" value="birth_date"/>
                                    <x-text-input type="date" id="birth_date" name="birth_date" class="mt-1 w-full bg-gray-50 text-gray-600"/>
                                </div>



                                <div>
                                    <x-input-label for="weight" value="Weight (kg)"/>
                                    <x-text-input id="weight" name="weight" type="number" step="0.1" class="mt-1 w-full"/>
                                </div>

                                <div>
                                    <x-input-label for="height" value="Height (cm)"/>
                                    <x-text-input id="height" name="height" type="number" step="0.1" class="mt-1 w-full"/>
                                </div>

                                <div class="col-span-2">
                                    <x-input-label for="bmi" value="BMI"/>
                                    <x-text-input id="bmi" name="bmi" class="mt-1 w-full bg-gray-50 text-gray-600" readonly/>
                                </div>

                                <div>
                                    <x-input-label for="unit" value="Unit"/>
                                    <x-text-input id="unit" name="unit" class="mt-1 w-full"/>
                                </div>

                                <div>
                                    <x-input-label for="contact" value="Contact #"/>
                                    <x-text-input id="contact" name="contact" class="mt-1 w-full"/>
                                </div>

                            </div>
                        </div>


                    </section>



                    <!-- FAMILY HISTORY -->


                </div>

                <!-- RIGHT SIDE ---------------------------------------------->
                <div class="space-y-6">

                    <section id="familyHistorySection" class="card p-6 relative shadow-lg">

                        <div id="familyOverlay"
                             class="absolute inset-0 bg-black/40 rounded-xl hidden z-10"></div>

                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold">Family History</h2>
                            <button id="editFamilyBtn" type="button" class="z-20 text-sm hidden hhi-btn-edit hhi-btn"> <i class="fa-solid fa-pen-to-square mr-2"></i>Edit</button>
                        </div>

                        <div id="familyForm">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach(['Hypertension','Diabetes Mellitus','Heart attack under 60y','Cholesterol'] as $history)
                                    <div>
                                        <label class="block text-sm font-medium">{{ $history }}</label>
                                        <div class="flex gap-3 mt-2">
                                            <label><input type="radio" name="family_{{ Str::slug($history) }}" value="y"> Yes</label>
                                            <label><input type="radio" name="family_{{ Str::slug($history) }}" value="n" checked> No</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <!-- RISK FACTORS -->
                    <section class="card p-6 shadow-lg">
                        <div class="flex justify-between mb-4">
                            <h2 class="text-lg font-semibold">Risk Factors</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach([
                                'total_cholesterol'=>'Total Cholesterol (mg/dl)',
                                'hdl_cholesterol'=>'HDL Cholesterol (mg/dl)',
                                'systolic_bp'=>'Systolic BP (mmHg)',
                                'fbs'=>'FBS',
                                'hba1c'=>'HBA1c'
                            ] as $id=>$lbl)
                                <div>
                                    <x-input-label for="{{ $id }}" value="{{ $lbl }}"/>
                                    <x-text-input id="{{ $id }}" name="{{ $id }}" type="number" class="mt-1 w-full"/>
                                </div>
                            @endforeach

                            @foreach([
                                'hypertension_tx'=>'Hypertension Tx',
                                'diabetes_m'=>'Diabetes M',
                                'smoker'=>'Current Smoker'
                            ] as $id=>$lbl)
                                <div>
                                    <x-input-label value="{{ $lbl }}"/>
                                    <div class="flex gap-3 mt-2">
                                        <label><input name="{{ $id }}" type="radio" value="y"> Yes</label>
                                        <label><input name="{{ $id }}" type="radio" value="n" checked> No</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <!-- SAVE BUTTON -->
                    <div class="flex justify-end">
                        <button id="saveBtn" type="button"
                                class="px-4 py-2 hhi-btn hhi-btn-save text-lg ">
                            <i class="fas fa-save mr-2"></i>
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </form>

         <div
                id="tableLoading"
                class="
                    absolute inset-0
                    z-30
                    bg-white/70
                    h-full w-full
                    backdrop-blur-[1px]
                    hidden
                    flex items-center justify-center
                    pointer-events-none
                "
            >
                <div class="flex flex-col items-center gap-3">
                    <div class="w-8 h-8 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <span class="text-sm text-gray-600 font-medium">
                        Saving records
                    </span>
                </div>
            </div>

    </div>

    <!-- SAVE MODAL -->
    <div id="saveModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-[1px] hidden flex items-center justify-center">
        <div class="bg-white w-[420px] rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-2">Record saved</h3>
            <p class="text-sm text-gray-600 mb-4">What would you like to do next?</p>

            <div class="flex justify-end gap-3">
                <button id="createAnotherBtn" class="px-4 py-2 hhi-btn-create-another hhi-btn">Create another</button>
                <button id="goDashboardBtn" class="hhi-btn-back hhi-btn">
                    <i class="fa-solid fa-house mr-2"></i> Back to Dashboard
                </button>
            </div>
        </div>
    </div>

</x-app-layout>

@if(session('success'))
    setTimeout(() => $("#saveModal").removeClass("hidden"), 300);
@endif
