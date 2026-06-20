<template>
    <teleport to="body">
        <div v-if="show" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" @click="closeOnBackdrop ? $emit('close') : null"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-visible shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full" :class="maxWidthClass">
                    
                    <div v-if="title" class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/80 rounded-t-xl">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white" id="modal-title">
                            {{ title }}
                        </h3>
                        <button @click="$emit('close')" class="text-slate-400 hover:text-slate-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div :class="noPadding ? 'bg-white dark:bg-slate-800' : 'bg-white dark:bg-slate-800 px-6 py-5'">
                        <slot></slot>
                    </div>

                    <div v-if="!noPadding" class="bg-slate-50 dark:bg-slate-800/80 px-6 py-4 border-t border-slate-200 dark:border-slate-700 sm:flex sm:flex-row-reverse rounded-b-xl">
                        <slot name="footer">
                            <button type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-slate-600 text-base font-medium text-white hover:bg-slate-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" @click="$emit('close')">
                                Close
                            </button>
                        </slot>
                    </div>

                </div>
            </div>
        </div>
    </teleport>
</template>

<script setup>
import { onMounted, onUnmounted, watch, computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true
    },
    maxWidth: {
        type: String,
        default: 'lg'
    },
    noPadding: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const maxWidthClass = computed(() => {
    return {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        '6xl': 'sm:max-w-6xl',
        '7xl': 'sm:max-w-7xl',
        'full': 'sm:max-w-full sm:mx-4',
    }[props.maxWidth];
});

watch(() => props.show, (value) => {
    if (value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
});

onUnmounted(() => {
    document.body.style.overflow = 'auto';
});
</script>
