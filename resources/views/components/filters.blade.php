{{-- FILTER BUTTON --}}
<div class="relative">
    <button id="filter-btn"
            class="flex items-center gap-2 px-4 py-2  text-black rounded-lg border border-gray-200 shadow">
        <i class="fa-solid fa-filter"></i>
        Filters
        <span id="filter-count"
              class="ml-1 bg-white text-blue-600 text-xs font-bold px-2 py-0.5 rounded-full hidden">
            0
        </span>
    </button>

    {{-- FILTER PANEL --}}
    <div id="filter-panel"
         class="hidden absolute right-0 mt-2 w-[350px] bg-white rounded-xl shadow-xl border p-4 z-1">

        <h3 class="text-lg font-semibold mb-3">Filters</h3>

        {{-- Birth Date --}}
        <details  class="mb-3">
            <summary class="cursor-pointer font-medium">Birth Date</summary>
            <div class="mt-2 flex gap-2">
                <input type="date" id="birth-from"
                       class="w-full border rounded px-2 py-1">
                <input type="date" id="birth-to"
                       class="w-full border rounded px-2 py-1">
            </div>
        </details>

        {{-- Age --}}
        <details class="mb-3">
            <summary class="cursor-pointer font-medium">Age</summary>
            <div class="mt-2 flex gap-2">
                <input type="number" id="age-min" placeholder="Min"
                       class="w-full border rounded px-2 py-1">
                <input type="number" id="age-max" placeholder="Max"
                       class="w-full border rounded px-2 py-1">
            </div>
        </details>


        {{-- Gender --}}
        <details class="mb-3">
            <summary class="cursor-pointer font-medium">Gender</summary>
            <div class="mt-2 space-y-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="gender-filter" value="Male">
                    Male
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="gender-filter" value="Female">
                    Female
                </label>
            </div>
        </details>


{{--        --}}{{-- Unit --}}
{{--        <details class="mb-3">--}}
{{--            <summary class="cursor-pointer font-medium">Unit</summary>--}}
{{--            <div class="mt-2 space-y-2">--}}
{{--                @foreach($units as $unit)--}}
{{--                    <label class="flex items-center gap-2">--}}
{{--                        <input type="checkbox"--}}
{{--                               class="unit-filter"--}}
{{--                               value="{{ $unit }}">--}}
{{--                        {{ $unit }}--}}
{{--                    </label>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </details>--}}

        <div class="flex justify-end mt-4">


            <button id="apply-filters"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                Apply
            </button>
        </div>
    </div>
</div>
