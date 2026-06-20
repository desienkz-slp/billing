<template>
    <div class="bg-canvas dark:bg-slate-800 rounded-xl border border-hairline-soft dark:border-slate-700 overflow-hidden">
        <div v-if="$slots.header || title" class="px-6 py-4 border-b border-hairline-soft dark:border-slate-700 flex justify-between items-center bg-surface-soft dark:bg-slate-800/50">
            <h3 v-if="title" class="type-heading-sm text-ink-deep dark:text-slate-100">{{ title }}</h3>
            <slot name="header"></slot>
        </div>
        <div :class="['px-6 py-5', bodyClass]">
            <slot></slot>
        </div>
        <div v-if="$slots.footer" class="px-6 py-4 bg-surface-soft dark:bg-slate-800/80 border-t border-hairline-soft dark:border-slate-700">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: ''
    },
    bodyClass: {
        type: String,
        default: ''
    }
});
</script>
