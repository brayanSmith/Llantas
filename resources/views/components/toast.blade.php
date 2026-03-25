@props([
    'visiblePath' => 'visible',
    'messagePath' => 'text',
    'typePath' => 'type'
])

<!-- Toast de notificación -->
<template x-if="{{ $visiblePath }}">
    <div class="fixed top-4 right-4 z-50 animate-slide-in-right">
        <div :class="{{ $typePath }} === 'success'
            ? 'bg-green-500'
            : 'bg-red-500'"
            class="text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <template x-if="{{ $typePath }} === 'success'">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </template>
            <template x-if="{{ $typePath }} === 'error'">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </template>
            <span x-text="{{ $messagePath }}" class="font-medium"></span>
        </div>
    </div>
</template>

<style>
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-slide-in-right {
        animation: slideInRight 0.3s ease-out;
    }
</style>
