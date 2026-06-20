<template>
    <div>
        <label v-if="label" :for="id" class="block type-body-sm font-medium text-charcoal dark:text-slate-300 mb-1">
            {{ label }}
        </label>
        <input 
            :id="id"
            :type="type" 
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'block w-full h-[44px] px-3 rounded-lg border border-hairline bg-canvas text-ink type-body-md focus:outline-none focus:ring-2 focus:ring-fb-blue focus:border-transparent shadow-none dark:bg-slate-800 dark:border-slate-600 dark:text-white',
                { 'border-critical-strong focus:ring-critical-strong focus:border-transparent': error }
            ]"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
        >
        <p v-if="error" class="mt-1 type-body-sm text-critical-strong">{{ error }}</p>
    </div>
</template>

<script setup>
defineProps({
    id: {
        type: String,
        default: () => `input-${Math.random().toString(36).substring(2, 9)}`
    },
    modelValue: {
        type: [String, Number],
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);
</script>
