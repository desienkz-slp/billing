<template>
    <button :type="type" :class="[
        'inline-flex items-center justify-center border border-transparent rounded-full type-button-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
        variantClasses[variant]
    ]" :disabled="disabled">
        <slot></slot>
    </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'primary' // primary, secondary, danger, warning, success
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const variantClasses = {
    primary: 'bg-ink-button text-on-ink-button hover:bg-charcoal px-[30px] py-[14px]',
    buy: 'bg-primary text-on-primary hover:bg-primary-deep px-[30px] py-[14px]',
    secondary: 'bg-transparent text-ink-deep border-[2px] !border-ink-deep px-[28px] py-[12px] hover:bg-surface-soft',
    ghost: 'bg-transparent text-ink-deep border-[2px] !border-[rgba(10,19,23,0.12)] px-[22px] py-[10px] hover:bg-surface-soft',
    danger: 'bg-critical text-canvas hover:bg-critical-strong px-[30px] py-[14px]',
    warning: 'bg-warning text-ink-deep hover:bg-warning-bg px-[30px] py-[14px]',
    success: 'bg-success text-canvas hover:bg-success-bg px-[30px] py-[14px]',
    outline: 'bg-transparent text-ink-deep border border-hairline px-[30px] py-[14px] hover:bg-surface-soft',
};
</script>
