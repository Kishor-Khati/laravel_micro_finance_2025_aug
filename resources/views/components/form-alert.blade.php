@props([
    'type' => 'success',
    'message' => null,
    'dismissible' => true,
    'class' => ''
])

@php
    $typeClasses = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700',
    ][$type] ?? 'bg-green-100 border-green-400 text-green-700';
    
    $icon = [
        'success' => 'fas fa-check-circle',
        'error' => 'fas fa-exclamation-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info' => 'fas fa-info-circle',
    ][$type] ?? 'fas fa-check-circle';
@endphp

<div {{ $attributes->merge(['class' => $typeClasses . ' border px-4 py-3 rounded relative mb-4 ' . $class]) }} role="alert">
    <div class="flex items-center">
        <i class="{{ $icon }} mr-2"></i>
        <span class="block sm:inline">{{ $message ?? $slot }}</span>
    </div>
    
    @if($dismissible)
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>