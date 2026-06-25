<script setup>
import { onMounted, ref, onUnmounted } from 'vue';

const props = defineProps({
    isDark: {
        type: Boolean,
        default: true
    }
});

// Binary Rain logic
const columns = ref([]);
const canvasRef = ref(null);
let animationFrameId;

const initMatrix = () => {
    const canvas = canvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Set canvas to full screen
    const resizeCanvas = () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    };
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Characters for the matrix rain (binary)
    const chars = '01';
    const fontSize = 14;
    const numColumns = canvas.width / fontSize;
    
    // Array to track the y coordinate of each column
    const drops = [];
    for(let x = 0; x < numColumns; x++) {
        drops[x] = 1;
    }

    const draw = () => {
        // Black background with slight opacity for trailing effect
        // Using slate-900 color: #0f172a
        // If light mode, use a very light color
        if (props.isDark) {
            ctx.fillStyle = 'rgba(15, 23, 42, 0.1)'; // Dark slate trail
        } else {
            ctx.fillStyle = 'rgba(248, 250, 252, 0.1)'; // Light slate trail
        }
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Text color
        ctx.fillStyle = props.isDark ? '#00f3ff' : '#0284c7'; // Darker Cyan/Blue for light mode
        ctx.font = fontSize + 'px monospace';

        for(let i = 0; i < drops.length; i++) {
            const text = chars.charAt(Math.floor(Math.random() * chars.length));
            
            // Randomly make some text white/dark or glowing orange
            if(Math.random() > 0.95) {
                ctx.fillStyle = props.isDark ? '#fff' : '#ea580c'; // Darker orange accent
            } else {
                ctx.fillStyle = props.isDark ? 'rgba(0, 243, 255, 0.5)' : 'rgba(2, 132, 199, 0.8)';
            }
            
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);

            // Reset drop to top randomly
            if(drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                drops[i] = 0;
            }
            drops[i]++;
        }
        
        // Control speed
        setTimeout(() => {
            animationFrameId = requestAnimationFrame(draw);
        }, 50);
    };

    draw();

    return () => {
        window.removeEventListener('resize', resizeCanvas);
        cancelAnimationFrame(animationFrameId);
    };
};

onMounted(() => {
    const cleanup = initMatrix();
    onUnmounted(() => {
        if (cleanup) cleanup();
    });
});
</script>

<template>
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0 transition-colors duration-1000"
         :class="isDark ? 'bg-slate-950' : 'bg-slate-50'">
        
        <!-- Background Grid with 3D Perspective -->
        <div class="absolute inset-0 perspective-grid opacity-30">
            <div class="grid-lines" :class="isDark ? 'grid-dark' : 'grid-light'"></div>
        </div>

        <!-- Binary Rain Canvas -->
        <canvas ref="canvasRef" class="absolute inset-0 opacity-40 mix-blend-multiply dark:mix-blend-lighten"></canvas>

        <!-- Decorative Glowing SVG Waveforms -->
        <svg class="absolute inset-0 w-full h-full opacity-60" preserveAspectRatio="none" viewBox="0 0 1000 500">
            <!-- Orange Waveform -->
            <path class="waveform waveform-orange" 
                  d="M0,400 Q100,300 200,450 T400,200 T600,350 T800,100 T1000,300" 
                  fill="none" stroke="#f97316" stroke-width="3" />
                  
            <!-- Cyan Waveform -->
            <path class="waveform waveform-cyan" 
                  d="M0,350 Q150,400 250,250 T500,400 T700,150 T900,350 T1000,200" 
                  fill="none" stroke="#00f3ff" stroke-width="2" />
                  
            <!-- Gold Accent Line -->
            <path class="waveform waveform-gold" 
                  d="M0,450 L200,450 L220,380 L280,380 L300,450 L1000,450" 
                  fill="none" stroke="#eab308" stroke-width="1.5" />
        </svg>

        <!-- Dynamic Circular Rings (from the mockup layout) -->
        <div class="absolute right-0 top-0 translate-x-1/4 -translate-y-1/4 w-[800px] h-[800px] rounded-full border border-dashed border-cyan-500/20 animate-spin-slow"></div>
        <div class="absolute right-0 top-0 translate-x-1/4 -translate-y-1/4 w-[600px] h-[600px] rounded-full border border-orange-500/10 animate-spin-reverse-slow"></div>

        <!-- Fog / Vignette Overlay to blend the edges -->
        <div class="absolute inset-0 bg-radial-gradient"></div>
    </div>
</template>

<style scoped>
/* 3D Perspective Grid */
.perspective-grid {
    perspective: 1000px;
    overflow: hidden;
}

.grid-lines {
    position: absolute;
    width: 200%;
    height: 200%;
    left: -50%;
    top: -50%;
    transform: rotateX(60deg) translateY(100px);
    background-size: 50px 50px;
    animation: gridMove 20s linear infinite;
}

.grid-dark {
    background-image: 
        linear-gradient(to right, rgba(0, 243, 255, 0.1) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0, 243, 255, 0.1) 1px, transparent 1px);
}

.grid-light {
    background-image: 
        linear-gradient(to right, rgba(14, 165, 233, 0.2) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(14, 165, 233, 0.2) 1px, transparent 1px);
}

@keyframes gridMove {
    0% { transform: rotateX(60deg) translateY(0); }
    100% { transform: rotateX(60deg) translateY(50px); }
}

/* Waveform Animations & Glow */
.waveform {
    filter: drop-shadow(0 0 10px currentColor);
    stroke-dasharray: 2000;
    stroke-dashoffset: 2000;
    animation: drawWaveform 5s ease-in-out infinite alternate;
}

.waveform-orange { filter: drop-shadow(0 0 15px rgba(249, 115, 22, 0.8)); }
.waveform-cyan { filter: drop-shadow(0 0 20px rgba(0, 243, 255, 0.8)); animation-delay: -2s; }
.waveform-gold { filter: drop-shadow(0 0 10px rgba(234, 179, 8, 0.8)); animation-duration: 3s; }

@keyframes drawWaveform {
    0% { stroke-dashoffset: 2000; }
    100% { stroke-dashoffset: 0; }
}

/* Slow Rotations */
.animate-spin-slow {
    animation: spin 30s linear infinite;
}
.animate-spin-reverse-slow {
    animation: spin 40s linear infinite reverse;
}

/* Dark Vignette to fade edges */
.bg-radial-gradient {
    background: radial-gradient(circle at center, transparent 30%, var(--bg-color, rgba(15, 23, 42, 0.9)) 100%);
}
:deep(.dark) .bg-radial-gradient {
    --bg-color: rgba(2, 6, 23, 0.95); /* Deep slate-950 */
}
</style>
