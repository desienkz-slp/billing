<template>
  <div :style="containerStyle">

    <!-- Fallback solid gradient background -->
    <div :style="fallbackStyle"></div>

    <!-- Day Image -->
    <img
      :src="cityDayImage"
      alt=""
      :style="{
        position: 'absolute',
        inset: '0',
        width: '100%',
        height: '100%',
        objectFit: 'cover',
        transition: 'opacity 1s ease',
        opacity: isDark ? 0 : 0.3,
        zIndex: 1
      }"
    />

    <!-- Night Image -->
    <img
      :src="cityNightImage"
      alt=""
      :style="{
        position: 'absolute',
        inset: '0',
        width: '100%',
        height: '100%',
        objectFit: 'cover',
        objectPosition: 'center',
        transition: 'opacity 1s ease',
        opacity: isDark ? 0.2 : 0,
        zIndex: 1
      }"
    />

    <!-- Animated Data Links & Packets Layer -->
    <div :style="{ position: 'absolute', inset: '0', zIndex: 2, pointerEvents: 'none', ...getParallaxStyle(0.04) }">
      <svg style="width:100%;height:100%" preserveAspectRatio="xMidYMid slice" viewBox="0 0 1920 1080">
        <!-- Connecting Fibers / Wireless Links -->
        <g fill="none" :stroke="isDark ? '#22d3ee' : '#38bdf8'" stroke-width="2" :opacity="isDark ? 0.6 : 0.8">
          <path d="M400 700 Q 600 500 800 600 T 1200 500" class="path-animate-slow" />
          <path d="M1200 500 Q 1400 400 1600 550 T 1900 450" class="path-animate-med" />
          <path d="M100 800 Q 500 450 900 700" class="path-animate-fast" />
          <path d="M300 900 Q 700 600 1000 800 T 1700 700" class="path-animate-slow" />
        </g>

        <!-- Animated Data Packets along paths (Neon glow) -->
        <g fill="none" :stroke="isDark ? '#a855f7' : '#0ea5e9'" stroke-width="4" stroke-linecap="round" :filter="isDark ? 'url(#glow-neon)' : ''" :opacity="isDark ? 0.9 : 0.6">
          <path d="M400 700 Q 600 500 800 600 T 1200 500" stroke-dasharray="15 1000" class="packet-animate-slow" />
          <path d="M1200 500 Q 1400 400 1600 550 T 1900 450" stroke-dasharray="20 800" class="packet-animate-med" />
          <path d="M100 800 Q 500 450 900 700" stroke-dasharray="10 600" class="packet-animate-fast" />
          <path d="M300 900 Q 700 600 1000 800 T 1700 700" stroke-dasharray="25 1200" class="packet-animate-slow" />
        </g>

        <defs>
          <filter id="glow-neon" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur stdDeviation="4" result="blur" />
            <feMerge>
              <feMergeNode in="blur" />
              <feMergeNode in="SourceGraphic" />
            </feMerge>
          </filter>
        </defs>
      </svg>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  isDark: {
    type: Boolean,
    default: true
  },
  mouseX: {
    type: Number,
    default: 0
  },
  mouseY: {
    type: Number,
    default: 0
  },
  cityDayImage: {
    type: String,
    default: '/images/city_day.png'
  },
  cityNightImage: {
    type: String,
    default: '/images/city_night.png'
  }
});

const containerStyle = computed(() => ({
  overflow: 'hidden'
}));

const fallbackStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  zIndex: 0,
  background: props.isDark
    ? 'linear-gradient(to bottom, #0b1120, #1e293b)'
    : 'linear-gradient(to bottom, #60a5fa, #bfdbfe)',
  transition: 'background 1s ease'
}));

// Calculate parallax transform based on mouse position and depth multiplier
const getParallaxStyle = (depthMultiplier) => {
  const windowCenterX = typeof window !== 'undefined' ? window.innerWidth / 2 : 960;
  const windowCenterY = typeof window !== 'undefined' ? window.innerHeight / 2 : 540;

  const offsetX = (windowCenterX - props.mouseX) * depthMultiplier;
  const offsetY = (windowCenterY - props.mouseY) * depthMultiplier * 0.5;

  return {
    transform: `translate3d(${offsetX}px, ${offsetY}px, 0)`,
    willChange: 'transform',
    transition: 'transform 0.1s cubic-bezier(0.25, 0.46, 0.45, 0.94)'
  };
};
</script>

<style scoped>
.path-animate-slow {
  stroke-dasharray: 1000;
  stroke-dashoffset: 1000;
  animation: drawPath 15s linear infinite;
}

.path-animate-med {
  stroke-dasharray: 800;
  stroke-dashoffset: 800;
  animation: drawPath 10s linear infinite;
}

.path-animate-fast {
  stroke-dasharray: 600;
  stroke-dashoffset: 600;
  animation: drawPath 8s linear infinite;
}

.packet-animate-slow {
  animation: movePacket 8s linear infinite;
}

.packet-animate-med {
  animation: movePacket 6s linear infinite reverse;
}

.packet-animate-fast {
  animation: movePacket 4s linear infinite;
}

@keyframes drawPath {
  0% { stroke-dashoffset: 2000; }
  100% { stroke-dashoffset: 0; }
}

@keyframes movePacket {
  0% { stroke-dashoffset: 2000; }
  100% { stroke-dashoffset: 0; }
}
</style>
