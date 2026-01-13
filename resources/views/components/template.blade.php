@props(['action' => ' '])

<div class="w-full">
    @if($action == 'edit')
        <div contenteditable="true" class="editableArea w-full">
            {{ $slot }}
        </div>
    @else
        <div class="w-full">
            {{ $slot }}
        </div>
    @endif
</div>
