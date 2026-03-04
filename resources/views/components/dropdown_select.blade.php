@props([
    'label' => '',
    'name',
    'options' => [],
    'selected' => null,
    'valueKey' => 'id',
    'labelKey' => 'name',
    'placeholder' => null,
    'multiple' => false,
     'icon' => null,
])

<div {{$attributes}}>
    @if($label)
        <div class="flex font-inter items-center gap-2 mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">

            {{-- Icon Slot --}}
            @if(isset($iconSlot))
                {{ $iconSlot }}
            @endif

            {{-- Named Slot Override (Optional) --}}
            {{ $label }}

        </div>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $multiple ? $name.'[]' : $name }}"
        {{ $multiple ? 'multiple' : '' }}
        {{ $attributes->merge([
            'class' => 'select2 mt-1 w-full rounded-lg border border-gray-200 text-sm shadow-lg bg-transparent dropdown-item'
        ]) }}
    >

        @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $option)

            @php
                if (is_object($option) || is_array($option)) {
                    $value = data_get($option, $valueKey);
                    $text  = data_get($option, $labelKey);
                } else {
                    $value = $option;
                    $text  = $option;
                }

                $isSelected = $multiple
                    ? in_array($text, (array) old($name, $selected ?? []))
                    : old($name, $selected) == $text;
            @endphp

            <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
                {{ $text }}
            </option>

        @endforeach
    </select>

</div>
