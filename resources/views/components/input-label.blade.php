@props([
    'value',
    'required' => false
])

<label {{ $attributes->merge(['class' => 'block font-medium font-inter text-[14px] text-black']) }}>
    {{ $value ?? $slot }}

    @if($required)
        <span class="text-red-500 ml-1">*</span>
    @endif
</label>
