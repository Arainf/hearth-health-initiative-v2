@props(['icon'])

<i
    {{ $attributes->merge([
        'class' => "
            {$icon} w-5
            text-gray-400
            group-hover:text-current
            transition-colors duration-200
        "
    ]) }}
></i>
