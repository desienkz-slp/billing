<template>
    <div :class="{'dark': isDark}" class="min-h-screen transition-colors duration-700 font-sans overflow-hidden">
        <div class="min-h-screen text-slate-900 dark:text-slate-100 flex flex-col items-center justify-center relative overflow-hidden transition-colors duration-700">

            <!-- Cyberpunk HUD Background -->
            <CyberpunkHud
                class="absolute inset-0 w-full h-full z-0 pointer-events-none"
                :is-dark="isDark"
            />

            <!-- Mouse Spotlight Effect -->
            <div ref="spotlight" class="pointer-events-none fixed inset-0 z-0 transition-opacity duration-300"
                 :style="{ background: `radial-gradient(circle 400px at ${mouseX}px ${mouseY}px, ${isDark ? 'rgba(59, 130, 246, 0.08)' : 'rgba(59, 130, 246, 0.06)'}, transparent 80%)` }">
            </div>

            <!-- Vignette overlay to fade out the edges and keep contrast high -->
            <div class="absolute inset-0 z-10 transition-colors duration-700"
                 :class="isDark ? 'bg-[radial-gradient(circle_at_center,transparent_0%,rgba(11,17,32,0.6)_100%)]' : 'bg-[radial-gradient(circle_at_center,transparent_0%,rgba(248,250,252,0.6)_100%)]'">
            </div>

            <!-- Top Right Controls -->
            <div class="absolute top-6 right-6 z-20 flex items-center gap-4">
                <!-- User Welcome Badge -->
                <div class="flex items-center gap-3 px-4 h-12 rounded-2xl bg-white/40 dark:bg-slate-800/40 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 shadow-lg cursor-default group hover:bg-white/60 dark:hover:bg-slate-800/60 transition-colors">
                    <div class="relative">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-base shadow-sm group-hover:scale-105 transition-transform duration-300">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-[#10b981] rounded-full ring-2 ring-white dark:ring-[#0b1120]"></div>
                    </div>
                    <div class="text-left pr-2">
                        <div class="text-[14px] font-bold text-slate-800 dark:text-white leading-none">{{ user.name }}</div>
                    </div>
                </div>

                <!-- Theme Toggle -->
                <button @click="toggleTheme"
                        class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white/40 dark:bg-slate-800/40 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300 group">
                    <svg v-if="!isDark" class="w-5 h-5 text-amber-500 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg v-else class="w-5 h-5 text-blue-400 group-hover:-rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Logout Form -->
                <form @submit.prevent="logout">
                    <button type="submit"
                            class="flex items-center h-12 gap-2 px-5 rounded-2xl bg-white/40 dark:bg-slate-800/40 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 shadow-lg hover:bg-red-50/80 dark:hover:bg-red-900/30 hover:text-red-500 hover:border-red-200/50 dark:hover:border-red-800/50 transition-all duration-300 group">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-semibold text-sm">Logout</span>
                    </button>
                </form>
            </div>



            <!-- Circular Module Arrangement -->
            <div class="relative z-10 w-full max-w-4xl aspect-square flex items-center justify-center">
                
                <!-- Center Hover Label -->
                <transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 scale-50 blur-sm"
                    enter-to-class="opacity-100 scale-100 blur-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 scale-100 blur-0"
                    leave-to-class="opacity-0 scale-50 blur-sm"
                >
                    <div v-if="hoveredIndex !== null" 
                         class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none z-0">
                        <div class="font-graffiti text-5xl md:text-7xl tracking-widest text-center px-8 transition-colors duration-300"
                             :style="{ 
                                 color: modules[hoveredIndex].color,
                                 WebkitTextStroke: isDark ? '2px #0b1120' : '3px #ffffff',
                                 filter: `drop-shadow(0 0 25px ${modules[hoveredIndex].color}) drop-shadow(0 0 10px ${modules[hoveredIndex].color})`
                             }">
                            {{ modules[hoveredIndex].label }}
                        </div>
                    </div>
                </transition>

                <!-- Module Cards in Circle -->
                <a v-for="(mod, index) in modules"
                   :key="mod.key"
                   :href="mod.url"
                   class="module-card absolute group cursor-pointer"
                   :style="getModuleStyle(index)"
                   @mouseenter="handleMouseEnter(index)"
                   @mouseleave="handleMouseLeave"
                   @mousemove="handleMouseMove"
                   @mousedown="handleMouseDown($event, index)"
                   @mouseup="handleMouseUp">

                    <!-- Card Container -->
                    <div class="relative flex flex-col items-center justify-center p-6 w-36 h-36 rounded-3xl transition-all duration-300 ease-out"
                         :class="{ 'pressed': pressedIndex === index }"
                         :style="getCardStyle(index)">

                        <!-- Hover Glow Background -->
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl"
                             :style="{ background: `radial-gradient(circle at center, ${mod.color}20, transparent 70%)` }">
                        </div>

                        <!-- Icon Container -->
                        <div class="relative flex items-center justify-center transition-all duration-300">
                            <img v-if="mod.image_url" :src="mod.image_url" :alt="mod.label"
                                 class="z-10 w-28 h-28 object-contain transition-all duration-300"
                                 :style="getImageStyle(index, mod.color)" />
                            <div v-else class="z-10 w-16 h-16 transition-all duration-300"
                                 :style="{ color: mod.color }"
                                 v-html="mod.icon">
                            </div>
                        </div>

                        <!-- Ripple Effect Container -->
                        <div v-if="ripple.active && ripple.index === index"
                             class="absolute ripple rounded-full bg-current opacity-20 pointer-events-none"
                             :style="{
                                 left: ripple.x + 'px',
                                 top: ripple.y + 'px',
                                 width: ripple.size + 'px',
                                 height: ripple.size + 'px',
                                 color: mod.color,
                                 transform: 'translate(-50%, -50%) scale(0)'
                             }">
                        </div>
                    </div>
                </a>
            </div>

            <!-- Footer -->
            <div class="absolute bottom-6 text-xs text-slate-400 dark:text-slate-500 font-medium tracking-wide z-10">
                &copy; 2026 upluk-upluk_dev version 2.0
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import CyberpunkHud from '../Components/CyberpunkHud.vue';

const props = defineProps({
    modules: Array,
    user: Object,
    tenant: Object,
    city_day_url: String,
    city_night_url: String,
});

const isDark = ref(true);
const mouseX = ref(0);
const mouseY = ref(0);
const hoveredIndex = ref(null);
const pressedIndex = ref(null);
const cardPositions = ref([]);

const ripple = ref({
    active: false,
    index: null,
    x: 0,
    y: 0,
    size: 0
});

onMounted(() => {
    // Theme setup
    if (localStorage.getItem('theme') === 'light') {
        isDark.value = false;
    } else if (localStorage.getItem('theme') === 'dark') {
        isDark.value = true;
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    applyTheme();

    // Mouse tracking for spotlight
    window.addEventListener('mousemove', handleGlobalMouseMove);

    // Calculate initial card positions
    calculateCardPositions();

    // Start idle animations
    startIdleAnimations();
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleGlobalMouseMove);
});

const applyTheme = () => {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('data-theme', 'light');
    }
};

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
    applyTheme();
};

const logout = () => {
    router.post(route('logout'));
};

const handleGlobalMouseMove = (e) => {
    mouseX.value = e.clientX;
    mouseY.value = e.clientY;
};

const calculateCardPositions = () => {
    const total = props.modules.length;
    const radius = Math.min(280, 200 + total * 10); // Dynamic radius based on module count
    const centerX = 0;
    const centerY = 0;

    cardPositions.value = props.modules.map((_, index) => {
        // Distribute cards in a circle
        const angle = (index / total) * 2 * Math.PI - Math.PI / 2; // Start from top
        const x = centerX + radius * Math.cos(angle);
        const y = centerY + radius * Math.sin(angle);
        return { x, y, angle, baseX: x, baseY: y };
    });
};

const getModuleStyle = (index) => {
    if (!cardPositions.value[index]) return {};
    const pos = cardPositions.value[index];
    return {
        top: '50%',
        left: '50%',
        transform: `translate(calc(-50% + ${pos.x}px), calc(-50% + ${pos.y}px))`,
        transition: 'transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)'
    };
};

const getCardStyle = (index) => {
    const isHovered = hoveredIndex.value === index;
    const isPressed = pressedIndex.value === index;
    const pos = cardPositions.value[index];
    const idleScale = pos && pos.scale ? pos.scale : 1;

    return {
        transform: isPressed ? 'scale(0.95)' : isHovered ? 'scale(1.25)' : `scale(${idleScale})`,
        boxShadow: 'none',
        zIndex: isHovered ? 30 : 10
    };
};

const getImageStyle = (index, color) => {
    const isHovered = hoveredIndex.value === index;
    const pos = cardPositions.value[index];
    
    // When hovered, the glow is stronger, larger, and much brighter
    if (isHovered) {
        return {
            filter: `drop-shadow(0 0 15px ${color}) drop-shadow(0 0 30px ${color})`,
            transform: 'scale(1.15)',
            opacity: '1'
        };
    }
    
    // When idle, pulsate the glow based on the heartbeat
    const intensity = pos && pos.glowIntensity ? pos.glowIntensity : 0;
    const radius = 10 + (intensity * 45); // 10px to 55px (approx 200% wider than before)
    const opacity = 0.4 + (intensity * 0.6); // 0.4 to 1.0
    
    // Helper to convert hex to rgba for drop-shadow
    const hexToRgb = (hex) => {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}` : '255, 255, 255';
    };
    
    return {
        filter: `drop-shadow(0 0 ${radius}px rgba(${hexToRgb(color)}, ${opacity}))`,
        transform: 'scale(1)',
        opacity: '1'
    };
};

const isHovered = (index) => {
    return hoveredIndex.value === index;
};

const handleMouseEnter = (index) => {
    hoveredIndex.value = index;
};

const handleMouseLeave = () => {
    hoveredIndex.value = null;
    pressedIndex.value = null;
};

const handleMouseMove = (e) => {
    // Magnetic attraction effect
    const card = e.currentTarget.querySelector('.module-card > div');
    if (!card) return;

    const rect = card.getBoundingClientRect();
    const cardCenterX = rect.left + rect.width / 2;
    const cardCenterY = rect.top + rect.height / 2;

    const deltaX = (e.clientX - cardCenterX) * 0.1;
    const deltaY = (e.clientY - cardCenterY) * 0.1;

    card.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(1.1)`;
};

const handleMouseDown = (e, index) => {
    pressedIndex.value = index;

    // Trigger ripple effect
    const card = e.currentTarget.querySelector('.module-card > div');
    if (!card) return;

    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const size = Math.max(rect.width, rect.height) * 2;

    ripple.value = { active: true, index, x, y, size };

    // Reset ripple after animation
    setTimeout(() => {
        ripple.value.active = false;
    }, 600);
};

const handleMouseUp = () => {
    pressedIndex.value = null;
};



const startIdleAnimations = () => {
    let time = 0;

    const animate = () => {
        time += 0.02;

        props.modules.forEach((_, index) => {
            const basePos = cardPositions.value[index];
            if (!basePos) return;

            // Floating animation with different phases for each card
            const floatOffsetY = Math.sin(time + index * 0.5) * 5;
            const floatOffsetX = Math.cos(time * 0.8 + index * 0.7) * 2;
            
            // Slow motion heartbeat / breathing scale
            // Varies scale between 0.88 and 1.12 slowly (increased range)
            const sinVal = Math.sin(time * 1.5 + index * 0.8);
            const pulseScale = 1 + sinVal * 0.12;
            
            // Normalized intensity from 0 to 1 for the glowing effect
            const glowIntensity = (sinVal + 1) / 2;

            cardPositions.value[index] = {
                ...basePos,
                y: basePos.baseY + floatOffsetY,
                x: basePos.baseX + floatOffsetX,
                scale: pulseScale,
                glowIntensity: glowIntensity
            };
        });

        requestAnimationFrame(animate);
    };

    animate();
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap');

.font-graffiti {
    font-family: 'Permanent Marker', cursive;
}

@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

@keyframes floatParticle {
    0%, 100% { transform: translateY(0) translateX(0); opacity: 0.2; }
    25% { transform: translateY(-30px) translateX(10px); opacity: 0.4; }
    50% { transform: translateY(-50px) translateX(-10px); opacity: 0.2; }
    75% { transform: translateY(-30px) translateX(15px); opacity: 0.4; }
}

.animate-spin-extremely-slow {
    animation: spinSlow 60s linear infinite;
}

@keyframes spinSlow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes isoPan {
    0% { background-position: 0px 0px; }
    100% { background-position: 100px 100px; }
}

.module-card > div.pressed {
    transition: transform 0.1s ease-out !important;
}

/* Ripple animation */
.ripple {
    animation: rippleEffect 0.6s ease-out forwards;
}

@keyframes rippleEffect {
    0% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 0.3;
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0;
    }
}

/* Removed GPU acceleration hacks to prevent drop-shadow blurriness in WebKit */
.module-card {
    position: absolute;
}

/* Smooth transitions */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Ensure SVG icons fill their container */
.module-card .z-10 svg {
    width: 100%;
    height: 100%;
}
</style>
