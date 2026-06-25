<template>
    <transition name="fade">
        <div v-if="state.isOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-sm overflow-hidden transform transition-all relative">
                
                <div class="p-5 flex gap-4">
                    <!-- Icon based on confirmColor -->
                    <div class="shrink-0 flex items-start mt-0.5">
                        <div v-if="state.confirmColor === 'rose' || state.confirmColor === 'red'" class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div v-else-if="state.confirmColor === 'emerald' || state.confirmColor === 'green'" class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div v-else class="w-10 h-10 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-800 dark:text-white mb-1.5">{{ state.title }}</h3>
                        <p class="text-[13.5px] text-slate-500 dark:text-slate-400 leading-relaxed">{{ state.message }}</p>
                    </div>
                </div>

                <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700">
                    <button @click="cancel" class="px-3.5 py-1.5 rounded-lg text-[13px] font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors focus:outline-none">
                        {{ state.cancelText }}
                    </button>
                    <button @click="proceed" :class="[
                        'px-3.5 py-1.5 rounded-lg text-[13px] font-semibold text-white shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-slate-800',
                        state.confirmColor === 'rose' || state.confirmColor === 'red' ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-500/20 focus:ring-rose-500' :
                        state.confirmColor === 'emerald' || state.confirmColor === 'green' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/20 focus:ring-emerald-500' :
                        'bg-sky-600 hover:bg-sky-700 shadow-sky-500/20 focus:ring-sky-500'
                    ]">
                        {{ state.confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { useConfirm } from '../Composables/useConfirm';
import { onMounted, onUnmounted } from 'vue';

const { state, proceed, cancel } = useConfirm();

// Handle escape key
const handleKeydown = (e) => {
    if (e.key === 'Escape' && state.isOpen) {
        cancel();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
