@php
    $colorMap = [
        'approved' => '#16a34a',
        'pending'=> '#f59e0b',
        'not evaluated' => '#9ca3af'
    ]
@endphp


<div class="relative inline-block text-left w-44">
    <button id="{{ $name }}-btn" type="button" {{ $attributes }}>

        <span id="{{ $name }}-label"> {{ $selected }}</span>
        <svg class="w-4 h-4 ml-2 text-gray-500 dark:text-gray-500"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div id="{{$name}}-menu" class="dropdown-menu hidden">
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
            <li class="{{ $name }}-dropdown-item flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="{{ $default_value }}">
                <span class="inline-block rounded-full h-3 w-3 "></span> {{$default_label}}
            </li>
            <li></li>
        @foreach($options as $option)
            @php
                $count = null;
                if (is_object($option) || is_array($option)) {
                    $val    = data_get($option, $labelKey ?? 'id');
                    $label  = data_get($option, $labelKey ?? 'name');
                    $count  = data_get($option, $countKey ?? 'count');

                } else {
                    $val = $option;
                    $label = $option;

                }
            @endphp

            <li  data-value="{{ $val }}" class="{{ $name }}-dropdown-item dropdown-item">
                <span class="inline-block rounded-full h-3 w-3 "></span>
                {{ $label }}
            </li>
        @endforeach
        </ul>
    </div>
</div>


