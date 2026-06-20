<template>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50">
        <!-- Ornamen pojok kanan atas -->
        <div class="absolute right-0 top-0 w-20 h-20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110" :class="bgDecoClass"></div>
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ title }}</p>
                <h3 class="text-xl font-extrabold text-slate-800 dark:text-slate-100" :class="valueColor">{{ value }}</h3>
                <p v-if="sub" class="text-xs mt-1" :class="subColorClass">{{ sub }}</p>
            </div>
            <!-- Ikon aksen -->
            <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-sm transition-transform group-hover:scale-110" :class="iconWrapClass">
                <!-- Fallback icon if no slot or dynamic svg -->
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: String,
    value: [String, Number],
    sub: String,
    valueColor: { type: String, default: '' },
    subColor: { type: String, default: 'slate' },
    icon: String,
    color: { type: String, default: 'blue' }
});

const colorMaps = {
    blue: { bg: 'bg-blue-500/10 dark:bg-blue-500/20', icon: 'bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400', sub: 'text-blue-500' },
    emerald: { bg: 'bg-emerald-500/10 dark:bg-emerald-500/20', icon: 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 dark:text-emerald-400', sub: 'text-emerald-500' },
    rose: { bg: 'bg-rose-500/10 dark:bg-rose-500/20', icon: 'bg-rose-50 dark:bg-rose-900/30 text-rose-500 dark:text-rose-400', sub: 'text-rose-500' },
    amber: { bg: 'bg-amber-500/10 dark:bg-amber-500/20', icon: 'bg-amber-50 dark:bg-amber-900/30 text-amber-500 dark:text-amber-400', sub: 'text-amber-500' },
    cyan: { bg: 'bg-cyan-500/10 dark:bg-cyan-500/20', icon: 'bg-cyan-50 dark:bg-cyan-900/30 text-cyan-500 dark:text-cyan-400', sub: 'text-cyan-500' },
    purple: { bg: 'bg-purple-500/10 dark:bg-purple-500/20', icon: 'bg-purple-50 dark:bg-purple-900/30 text-purple-500 dark:text-purple-400', sub: 'text-purple-500' },
    pink: { bg: 'bg-pink-500/10 dark:bg-pink-500/20', icon: 'bg-pink-50 dark:bg-pink-900/30 text-pink-500 dark:text-pink-400', sub: 'text-pink-500' },
    teal: { bg: 'bg-teal-500/10 dark:bg-teal-500/20', icon: 'bg-teal-50 dark:bg-teal-900/30 text-teal-500 dark:text-teal-400', sub: 'text-teal-500' },
    slate: { bg: 'bg-slate-500/10 dark:bg-slate-500/20', icon: 'bg-slate-50 dark:bg-slate-900/30 text-slate-500 dark:text-slate-400', sub: 'text-slate-400' }
};

const bgDecoClass = computed(() => colorMaps[props.color]?.bg || colorMaps.blue.bg);
const iconWrapClass = computed(() => colorMaps[props.color]?.icon || colorMaps.blue.icon);

const subColorClass = computed(() => {
    if (props.subColor === 'slate') return 'text-slate-400';
    return colorMaps[props.subColor]?.sub || colorMaps[props.color]?.sub || 'text-slate-400';
});
</script>
