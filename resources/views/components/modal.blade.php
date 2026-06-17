@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<dialog
    id="modal-{{ $name }}"
    data-modal="{{ $name }}"
    class="fixed inset-0 z-50 m-0 max-h-none max-w-none w-full h-full bg-transparent p-4 sm:p-6 backdrop:bg-slate-900/50 open:flex open:items-center open:justify-center"
    @if($show) open @endif
>
    <div class="w-full {{ $maxWidth }} bg-white rounded-lg overflow-hidden shadow-xl">
        {{ $slot }}
    </div>
</dialog>
