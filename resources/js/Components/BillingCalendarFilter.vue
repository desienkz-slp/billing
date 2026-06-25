<template>
  <div class="relative w-full max-w-sm font-sans">
    <!-- Trigger Button -->
    <button 
      @click="togglePopover" 
      class="flex items-center justify-between w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all"
      type="button"
    >
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
        </svg>
        <span>{{ displayText }}</span>
      </div>
      <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Popover Calendar -->
    <div 
      v-show="isOpen" 
      class="absolute top-full right-0 mt-2 w-[340px] bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 p-4 z-50 transform origin-top transition-all"
    >
      <!-- Header -->
      <div class="flex items-center justify-between mb-4">
        <button @click="prevMonth" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </button>
        <h2 class="font-bold text-slate-800 dark:text-white text-base">
          {{ monthName }} {{ currentYear }}
        </h2>
        <button @click="nextMonth" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>
      </div>

      <!-- Days Header -->
      <div class="grid grid-cols-7 mb-2">
        <div v-for="day in ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']" :key="day" class="text-center text-xs font-bold text-slate-500 dark:text-slate-400 py-1">
          {{ day }}
        </div>
      </div>

      <!-- Calendar Grid -->
      <div class="grid grid-cols-7 gap-y-2">
        <!-- Empty slots before month starts -->
        <div v-for="empty in emptyCells" :key="'empty-'+empty" class="h-12"></div>
        
        <!-- Days -->
        <div 
          v-for="date in daysInMonth" 
          :key="date"
          class="relative flex flex-col items-center justify-center h-12 cursor-pointer group"
          @click="selectDate(date)"
          @mouseenter="hoverDate = date"
          @mouseleave="hoverDate = null"
        >
          <!-- Range Background -->
          <div 
            v-if="isInRange(date)" 
            class="absolute inset-y-0 w-full bg-blue-100 dark:bg-blue-900/40"
            :class="{
              'rounded-l-full': isSelectionStart(date),
              'rounded-r-full': isSelectionEnd(date)
            }"
          ></div>

          <!-- Date Circle -->
          <div 
            class="relative z-10 w-8 h-8 flex items-center justify-center rounded-full text-sm transition-all"
            :class="[
              isSelectionBoundary(date) ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 group-hover:bg-slate-100 dark:group-hover:bg-slate-700',
              {'font-bold': isSelectionBoundary(date)}
            ]"
          >
            {{ date }}
          </div>

          <!-- Projection Nominal -->
          <div 
            v-if="getProjection(date)" 
            class="relative z-10 text-[9px] font-medium tracking-tight mt-0.5"
            :class="[
              isSelectionBoundary(date) ? 'text-blue-200' : getProjectionColor(date)
            ]"
          >
            {{ formatNominal(getProjection(date)) }}
          </div>
        </div>
      </div>

      <!-- Footer / Clear -->
      <div class="mt-4 flex justify-between items-center border-t border-slate-100 dark:border-slate-700 pt-3">
        <button @click="clearSelection" class="text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium px-2 py-1">
          Clear
        </button>
        <button @click="applySelection" class="text-xs bg-slate-800 dark:bg-slate-700 text-white hover:bg-slate-700 dark:hover:bg-slate-600 font-medium px-4 py-1.5 rounded-lg transition-colors">
          Terapkan
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({ start: null, end: null })
  },
  projections: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['update:modelValue', 'apply']);

const isOpen = ref(false);
const currentDate = ref(new Date());
const selectingStart = ref(true);

const selection = ref({
  start: props.modelValue.start,
  end: props.modelValue.end
});

const hoverDate = ref(null);

watch(() => props.modelValue, (newVal) => {
  selection.value.start = newVal.start;
  selection.value.end = newVal.end;
}, { deep: true });

// Calendar Calculations
const currentYear = computed(() => currentDate.value.getFullYear());
const currentMonthIndex = computed(() => currentDate.value.getMonth());

const monthName = computed(() => {
  const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  return months[currentMonthIndex.value];
});

const daysInMonth = computed(() => {
  return new Date(currentYear.value, currentMonthIndex.value + 1, 0).getDate();
});

const emptyCells = computed(() => {
  const firstDay = new Date(currentYear.value, currentMonthIndex.value, 1).getDay();
  // Adjust so Monday is 0
  return firstDay === 0 ? 6 : firstDay - 1;
});

// Navigation
const prevMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonthIndex.value - 1, 1);
};

const nextMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonthIndex.value + 1, 1);
};

// Selection Logic
const selectDate = (date) => {
  if (selectingStart.value) {
    selection.value.start = date;
    selection.value.end = null;
    selectingStart.value = false;
  } else {
    if (date < selection.value.start) {
      selection.value.end = selection.value.start;
      selection.value.start = date;
    } else {
      selection.value.end = date;
    }
    selectingStart.value = true;
  }
  emit('update:modelValue', selection.value);
};

const isSelectionStart = (date) => date === selection.value.start;
const isSelectionEnd = (date) => date === selection.value.end || (date === selection.value.start && !selection.value.end);
const isSelectionBoundary = (date) => isSelectionStart(date) || isSelectionEnd(date);

const isInRange = (date) => {
  if (selection.value.start && selection.value.end) {
    return date >= selection.value.start && date <= selection.value.end;
  }
  if (selection.value.start && hoverDate.value && !selectingStart.value) {
    const start = Math.min(selection.value.start, hoverDate.value);
    const end = Math.max(selection.value.start, hoverDate.value);
    return date >= start && date <= end;
  }
  return false;
};

// Data Formatting
const getProjection = (date) => {
  return props.projections[date] || 0;
};

const formatNominal = (value) => {
  if (!value) return '';
  if (value >= 1000000) {
    return (value / 1000000).toFixed(1).replace('.0', '') + 'M';
  }
  if (value >= 1000) {
    return Math.floor(value / 1000) + 'K';
  }
  return value;
};

const getProjectionColor = (date) => {
  const value = getProjection(date);
  if (!value) return 'text-transparent';
  // Example threshold: If revenue > 300K, show in red/orange like the mockup
  if (value >= 350000) return 'text-rose-500 dark:text-rose-400';
  return 'text-slate-400 dark:text-slate-500';
};

// Popover Logic
const togglePopover = () => isOpen.value = !isOpen.value;
const closePopover = () => {
  if (isOpen.value) {
    isOpen.value = false;
    selectingStart.value = !selection.value.end; // Reset state if clicked outside halfway
  }
};

const clearSelection = () => {
  selection.value.start = null;
  selection.value.end = null;
  selectingStart.value = true;
  emit('update:modelValue', selection.value);
  emit('apply');
  isOpen.value = false;
};

const applySelection = () => {
  emit('apply');
  isOpen.value = false;
};

const displayText = computed(() => {
  if (selection.value.start && selection.value.end) {
    return `Tgl ${selection.value.start} - ${selection.value.end}`;
  }
  if (selection.value.start) {
    return `Tgl ${selection.value.start}`;
  }
  return 'Filter Tagihan';
});

// Click Outside Directive Implementation
const clickOutsideEvent = (event) => {
  if (isOpen.value && !event.target.closest('.relative.w-full.max-w-sm')) {
    closePopover();
  }
};

onMounted(() => {
  document.addEventListener('click', clickOutsideEvent);
});

onUnmounted(() => {
  document.removeEventListener('click', clickOutsideEvent);
});
</script>
