@props([
    'disabled' => false,
    'leftIcon' => null,
    'rightIcon' => null,
])

<div class="w-full">

    <div class="relative w-full">
        <div class="flex flex-col  h-[44px]">

            {{-- Left Icon --}}
            @if($leftIcon)
                <span class="absolute left-3 top-3 flex items-center self-center text-slate-400">
                    {!! $leftIcon !!}
                </span>
            @endif

            <input
                @disabled($disabled)
                {{ $attributes->class([
                    "font-inter ",
                    "w-full h-[44px]",
                    "rounded-[8px]",
                    "border",
                    "px-3",
                    "text-sm",
                    "transition-all",
                    "bg-white",
                    "focus:outline-none",
                    "disabled:cursor-not-allowed disabled:opacity-50",

                    !$errors->has($attributes->get('name'))
                        ? "border-[#E9EAEB] focus:ring-2 focus:ring-[#1E88E5]"
                        : "",

                    $errors->has($attributes->get('name'))
                        ? "border-red-500 focus:ring-2 focus:ring-red-500"
                        : "",

                    $leftIcon ? "pl-10" : "",
                    $rightIcon ? "pr-10" : "",
                ]) }}
                autocomplete="off"
            >

            {{-- Right Icon --}}
            @if($rightIcon)
                <span class="absolute right-3 top-3 flex items-center self-center text-slate-400">
                    {!! $rightIcon !!}
                </span>
            @endif

        </div>


    </div>



</div>


