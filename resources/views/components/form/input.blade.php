@props([
    'label' => '',
    'name',
    'type' => 'text',
    'value' => '',
    'readonly' => false,
    'disabled' => false,
])

<div>
    {{-- Label --}}
    <label for="{{ $name }}"
           class="block text-sm font-medium text-gray-600">
        {{ $label }}
    </label>

    {{-- Input --}}
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        {{ $readonly ? 'readonly' : '' }}
        {{ $disabled ? 'disabled' : '' }}

        class="
            mt-1 w-full rounded-lg border border-gray-200
            bg-white px-3 py-2 text-sm text-gray-900
            placeholder-gray-400

            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
            disabled:bg-gray-100 disabled:text-gray-500
            readonly:bg-gray-50 readonly:text-gray-600
        "
    />

    {{-- Validation error --}}
    @error($name)
        <p class="mt-1 text-xs text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>
