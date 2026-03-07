@props([
    'label' => null,
    'icon' => null,
])

<div class="relative inline-block text-left w-full font-inter text-[var(--secondary-color)] h-[var(--h-filter)]">

    @if($label)
        <div class="flex items-center gap-2 mb-1  text-[length:var(--s-sub-header)] font-medium">

            @if(isset($iconSlot))
                {{ $iconSlot }}
            @endif

            {{ $label }}

        </div>
    @endif

    <button
        id="{{ $name }}-btn"
        type="button"
        {{ $attributes->merge(['class' => 'dropdown']) }}
    >
        <span id="{{ $name }}-label">
            {{ $selected }}
        </span>

        <x-lucide-chevron-down class="w-[var(--s-icon)] h-[var(--s-icon)] ml-2 chevron"/>
    </button>

    <div id="{{ $name }}-menu" class="dropdown-menu">

        <ul class="py-1  ">

            <li
                class="{{ $name }}-dropdown-item dropdown-item"
                data-value="{{ $default_value }}"
            >
                {{ $default_label }}
            </li>

            @foreach($options as $option)

                @php
                    if (is_object($option) || is_array($option)) {
                        $val    = data_get($option, $valueKey ?? 'id');
                        $label  = data_get($option, $labelKey ?? 'name');
                    } else {
                        $val = $option;
                        $label = $option;
                    }
                @endphp

                <li
                    data-value="{{ $val }}"
                    class="{{ $name }}-dropdown-item dropdown-item"
                >
                    {{ $label }}
                </li>

            @endforeach

        </ul>

    </div>

</div>
