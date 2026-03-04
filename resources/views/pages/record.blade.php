@vite(['resources/css/table.css', 'resources/js/page/record.js', 'resources/js/patient-nav/patientNav.js'])

<style>
    .card {
        background: #ffffff;
        border: 1px solid #e6e7eb;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        border-radius: 12px;
    }

    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<x-app-layout :title="__('Records')">

    <div class="relative flex flex-col min-h-screen h-auto w-full rounded-xl px-2 pt-2 bg-white">

        {{-- Header --}}
        <div class="flex flex-row px-6 py-3 z-20 justify-between">
            <div>
                <p class="circular text-3xl tracking-tighter">
                    New Heart Health Record
                </p>
                <span class="text-sm">
                Please complete the following to assess and record the patient's cardiovascular health
            </span>

                {{-- Required Legend --}}
                <p class="text-xs text-gray-500 mt-2">
                    <span class="text-red-500">*</span> Required fields
                </p>
            </div>

            <div class="flex-1 flex justify-end">
                @if(auth()->user()->is_Doctor())
                    <a href="{{ route('page' ,['token' => $encryption->encrypt('doctor')]) }}"
                       class="hhi-btn-back h-[32px] hhi-btn ">
                        <x-lucide-house class="h-4 w-4 mr-2"/> Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('page' ,['token' => $encryption->encrypt('dashboard')]) }}"
                       class="hhi-btn-back h-[32px] hhi-btn ">
                        <x-lucide-house class="h-4 w-4 mr-2"/> Back to Dashboard
                    </a>
                @endif

            </div>
        </div>

        <hr>

        <!-- FORM -->
        <form id="mainForm"
              method="POST"
              action="{{ route('store' , ['token' => $encryption->encrypt('record') , 'mode' => $encryption->encrypt('store')]) }}"
              class="space-y-3 px-6 pb-24">

            @csrf
            <input type="hidden" id="patient_id" name="patient_id">
            <input type="hidden" id="staff_id" name="staff_id" value="{{ Auth::user()->name }}">

            {{-- Editing Indicator --}}
            <div id="editingIndicator"
                 class="hidden bg-blue-50 border border-blue-200 text-blue-700 text-sm px-4 py-2 rounded-md mb-4">
                Editing existing patient record
            </div>

            {{-- ============================= --}}
            {{-- PATIENT DEMOGRAPHICS --}}
            {{-- ============================= --}}
            <div class="flex flex-col lg:flex-row relative py-4" id="patientInfoSection">

                <div class="w-full lg:w-[30%]">
                    <div class="flex items-center mb-4">
                        <div>
                            <div class="flex flex-row items-center">
                                <h2 class="text-xl font-bold">Patient’s Demographics</h2>
                                <div id="editPatientBtn"
                                     class="bg-transparent hhi-btn icon-only text-sm text-gray-400 hidden z-20">
                                    <i id="patientIcon" class="fa-solid fa-lock mr-2"></i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                Enter the patient’s personal and physical information.
                            </p>
                        </div>


                    </div>
                </div>

                <div class="w-full lg:w-[70%]">
                    <div id="patientForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">


                        <div >
                            <x-input-label for="first_name" value="First Name" required/>
                            <x-text-input id="first_name" name="first_name" class="w-full" placeholder="e.g Juan">
                                <x-slot:rightIcon>
                                    <x-lucide-case-sensitive class="w-5 h-5 text-slate-400" />
                                </x-slot:rightIcon>
                            </x-text-input>
                        </div>

                        <div class="data-[invalid=true]:text-destructive  flex  flex-col *:w-full ">
                            <x-input-label for="middle_name" value="Middle Name" required />
                            <x-text-input type="text" id="middle_name" name="middle_name" class=" w-full" placeholder="e.g Santos">
                                <x-slot:rightIcon>
                                    <x-lucide-case-sensitive class="w-5 h-5 text-slate-400" />
                                </x-slot:rightIcon>
                            </x-text-input>
                        </div>

                        <div class="flex flex-row gap-4">
                            <div class="data-[invalid=true]:text-destructive flex w-[80%] flex-col *:w-full ">
                                <x-input-label for="last_name" value="Last Name" required />
                                <x-text-input id="last_name" name="last_name" class="w-full" placeholder="e.g Dela Cruz">
                                    <x-slot:rightIcon>
                                        <x-lucide-case-sensitive class="w-5 h-5 text-slate-400" />
                                    </x-slot:rightIcon>
                                </x-text-input>
                            </div>
                            <div class="data-[invalid=true]:text-destructive flex w-[20%] flex-col *:w-full ">
                                <x-input-label for="suffix" value="Suffix" />
                                <x-text-input type="text" id="suffix" name="suffix" class=" w-full"  placeholder="e.g Jr."/>
                            </div>
                        </div>



                        <div>
                            <x-input-label for="contact" value="Contact Number"/>
                            <x-text-input id="contact" name="contact" class="w-full" placeholder="e.g 09123456789">
                                <x-slot:rightIcon>
                                    <x-lucide-phone class="w-4 h-4 text-slate-400" />
                                </x-slot:rightIcon>
                            </x-text-input>
                        </div>

                        <div>
                            <x-input-label for="birth_date" value="Birthday" required/>
                            <x-text-input type="date" id="birth_date" name="birth_date"
                                          class="w-full bg-gray-50 text-gray-600 appearance-none">
                                <x-slot:rightIcon>
                                    <x-lucide-calendar-days class="w-4 h-4 text-slate-400" />
                                </x-slot:rightIcon>
                            </x-text-input>
                        </div>

                        <div>
                            <x-input-label value="Sex" required/>
                            <div class="flex gap-3 mt-2">
                                <label><input type="radio" name="sex" value="Male"> Male</label>
                                <label><input type="radio" name="sex" value="Female"> Female</label>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="weight" value="Weight (kg)" required/>
                            <x-text-input id="weight" name="weight" type="number" step="0.1" class="w-full" placeholder="e.g 50 kg">
                                <x-slot:rightIcon>
                                    <x-lucide-info class="w-4 h-4 text-slate-400" />
                                </x-slot:rightIcon>
                            </x-text-input>
                        </div>

                        <div>
                            <x-input-label for="height" value="Height (cm)" required/>
                            <x-text-input id="height" name="height" type="number" step="0.1" class="w-full"  placeholder="e.g 150 cm">
                                <x-slot:rightIcon>
                                    <x-lucide-info class="w-4 h-4 text-slate-400" />
                                </x-slot:rightIcon>
                            </x-text-input>
                        </div>

                        <div>
                            <x-input-label for="bmi" value="BMI"/>
                            <x-text-input id="bmi" name="bmi"
                                          class="w-full bg-gray-50 text-gray-600"
                                          readonly>
                                <x-slot:rightIcon>
                                    <x-lucide-gauge class="w-4 h-4 text-slate-400" />
                                </x-slot:rightIcon>
                            </x-text-input>
                            <p class="text-xs text-gray-500 mt-1">
                                <i>BMI is automatically calculated from height and weight.</i>
                            </p>
                        </div>

                        <div class="md:col-span-2 lg:col-span-3">
                            <x-input-label for="unit_code"
                                           value="Unit/Office/Department/College"
                                           required/>
                            <x-form.dropdown
                                name="unit_code"
                                :options="$UNITS"
                                valueKey="unit_code"
                                labelKey="unit_name"
                                placeholder="Select Unit" />
                        </div>

                    </div>
                </div>
            </div>

            <hr>

            {{-- ============================= --}}
            {{-- FAMILY HISTORY --}}
            {{-- ============================= --}}
            <div class="flex flex-col lg:flex-row relative py-4" id="familyHistorySection" >

                <div class="w-full lg:w-[30%]">
                    <div class="mb-4">
                        <div class="flex flex-row items-center">
                            <h2 class="text-xl font-bold">Family History</h2>
                            <div id="editFamilyBtn"
                                 class="bg-transparent hhi-btn icon-only text-sm text-gray-400 hidden z-20">
                                <i id="familyIcon" class="fa-solid fa-lock mr-2"></i>
                            </div>
                        </div>

                        <p class="text-sm text-gray-500 mt-1">
                            Indicate whether immediate family members have a history of the following conditions.
                        </p>
                    </div>

                </div>

                <div class="w-full lg:w-[70%]">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach(['Hypertension','Diabetes Mellitus','Heart attack under 60y','Cholesterol'] as $history)
                            @php $fieldName = 'family_' . Str::slug($history); @endphp
                            <div>
                                <p class="text-sm font-normal">
                                    Is there a family history of <strong>{{ $history }}</strong>?
                                </p>
                                <div class="flex gap-6 mt-2">
                                    <label><input type="radio" name="{{ $fieldName }}" value="y"> Yes</label>
                                    <label><input type="radio" name="{{ $fieldName }}" value="n" checked> No</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <hr>

            {{-- ============================= --}}
            {{-- RISK FACTORS --}}
            {{-- ============================= --}}
            <div class="flex flex-col lg:flex-row relative py-4">

                <div class="w-full lg:w-[30%]">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold">Risk Factors</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Provide the most recent laboratory and clinical measurements.
                        </p>
                    </div>
                </div>

                <div class="w-full lg:w-[70%]">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                        @foreach([
                           'total_cholesterol'=>['label'=>'Total Cholesterol (mg/dl)','placeholder'=>'e.g 180'],
                           'hdl_cholesterol'=>['label'=>'HDL Cholesterol (mg/dl)','placeholder'=>'e.g 55'],
                           'systolic_bp'=>['label'=>'Systolic BP (mmHg)','placeholder'=>'e.g 120'],
                           'fbs'=>['label'=>'FBS (mg/dl)','placeholder'=>'e.g 95'],
                           'hba1c'=>['label'=>'HbA1c (%)','placeholder'=>'e.g 5.6']
                       ] as $id=>$data)
                            <div>
                                <x-input-label for="{{ $id }}" value="{{ $data['label'] }}"/>
                                <x-text-input
                                    id="{{ $id }}"
                                    name="{{ $id }}"
                                    type="number"
                                    step="0.1"
                                    class="w-full"
                                    placeholder="{{ $data['placeholder'] }}"
                                >
                                    <x-slot:rightIcon>
                                        <x-lucide-info class="w-4 h-4 text-slate-400" />
                                    </x-slot:rightIcon>
                                </x-text-input>
                            </div>
                        @endforeach
                        <div class="flex flex-row justify-between">
                            @foreach([ 'hypertension_tx'=>'Hypertension Tx', 'diabetes_m'=>'Diabetes M', 'smoker'=>'Current Smoker' ] as $id=>$lbl)
                                <div>
                                    <x-input-label value="{{ $lbl }}"/>
                                    <div class="flex gap-3 mt-2">
                                        <label>
                                            <input name="{{ $id }}" type="radio" value="y"> Yes</label>
                                        <label>
                                            <input name="{{ $id }}" type="radio" value="n" checked> No</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

            {{-- Sticky Save --}}
            <div class="sticky bottom-0 py-3 flex justify-end mt-6">
                <button id="saveBtn"
                        type="submit"
                        class="relative px-6 py-2 hhi-btn hhi-btn-save text-lg flex items-center justify-center gap-2 transition">

                    {{-- Normal State --}}
                    <span id="saveText" class="flex items-center gap-2">
            <x-lucide-save class="w-5 h-5" />
            Save
        </span>

                    {{-- Loading State --}}
                    <span id="saveSpinner"
                          class="hidden absolute inset-0 flex items-center justify-center gap-2">

            <x-lucide-loader-circle
                class="w-5 h-5 animate-spin text-white" />

            <span class="text-white text-sm">Saving...</span>
        </span>

                </button>
            </div>



        </form>

        <!-- SUCCESS MODAL -->
        <div id="saveModal"
             class="fixed inset-0 z-50 bg-black/40 backdrop-blur-[1px] hidden flex items-center justify-center">
            <div class="bg-white w-[420px] rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-2">Record saved</h3>
                <p class="text-sm text-gray-600 mb-4">
                    What would you like to do next?
                </p>

                <div class="flex justify-end gap-3">
                    <button id="createAnotherBtn"
                            class="px-4 py-2 hhi-btn-create-another hhi-btn">
                        Create another
                    </button>

                    <a href="{{ route('page' ,['token' => $encryption->encrypt('dashboard')]) }}"
                       class="hhi-btn-back hhi-btn">
                        <i class="fa-solid fa-house mr-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>


    </div>

</x-app-layout>

<script>
    window.page = {
        table : "/table/{{$TABLE}}"
    }
</script>
