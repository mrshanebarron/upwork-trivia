<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const plane = ref(null);
const isVisible = ref(false);
let intervalId = null;

const flyPlane = () => {
    isVisible.value = true;

    // Hide after animation completes
    setTimeout(() => {
        isVisible.value = false;
    }, 15000);
};

onMounted(() => {
    // Fly plane every 20-40 seconds
    const scheduleNextFlight = () => {
        const delay = 20000 + Math.random() * 20000;
        intervalId = setTimeout(() => {
            flyPlane();
            scheduleNextFlight();
        }, delay);
    };

    // First flight after 5 seconds
    setTimeout(() => {
        flyPlane();
        scheduleNextFlight();
    }, 5000);
});

onUnmounted(() => {
    if (intervalId) {
        clearTimeout(intervalId);
    }
});
</script>

<template>
    <div v-if="isVisible" class="plane-container">
        <!-- Placeholder plane SVG - replace with Shane's custom asset -->
        <div class="plane" ref="plane">
            <!-- Plane body -->
            <div class="plane-body">
                <!-- Fuselage -->
                <div class="fuselage"></div>

                <!-- Wings -->
                <div class="wing wing-main"></div>
                <div class="wing wing-tail"></div>

                <!-- Tail -->
                <div class="tail-fin"></div>

                <!-- Windows -->
                <div class="window window-1"></div>
                <div class="window window-2"></div>
                <div class="window window-3"></div>

                <!-- Propeller -->
                <div class="propeller">
                    <div class="propeller-blade propeller-blade-1"></div>
                    <div class="propeller-blade propeller-blade-2"></div>
                    <div class="propeller-center"></div>
                </div>
            </div>

            <!-- Contrail -->
            <div class="contrail"></div>
        </div>
    </div>
</template>

<style scoped>
.plane-container {
    position: fixed;
    top: 15%;
    left: -200px;
    width: 200px;
    height: 100px;
    z-index: 10;
    pointer-events: none;
    animation: fly-across 15s linear forwards;
}

@keyframes fly-across {
    0% {
        left: -200px;
        top: 15%;
    }
    100% {
        left: calc(100% + 200px);
        top: 20%;
    }
}

.plane {
    position: relative;
    width: 100%;
    height: 100%;
    transform: scale(0.8);
}

/* Plane body */
.plane-body {
    position: relative;
    width: 100%;
    height: 100%;
}

/* Fuselage */
.fuselage {
    position: absolute;
    top: 35px;
    left: 20px;
    width: 120px;
    height: 30px;
    background: linear-gradient(135deg, #4169e1 0%, #1e90ff 100%);
    border-radius: 15px 50px 50px 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.fuselage::before {
    content: '';
    position: absolute;
    top: -5px;
    right: 10px;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #87ceeb 0%, #4169e1 100%);
    border-radius: 50% 50% 0 0;
}

/* Main wing */
.wing-main {
    position: absolute;
    top: 45px;
    left: 50px;
    width: 80px;
    height: 12px;
    background: linear-gradient(90deg, #1e90ff 0%, #4169e1 50%, #1e90ff 100%);
    border-radius: 50% 50% 50% 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Tail wing */
.wing-tail {
    position: absolute;
    top: 40px;
    left: 25px;
    width: 30px;
    height: 8px;
    background: linear-gradient(90deg, #1e90ff 0%, #4169e1 100%);
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Tail fin */
.tail-fin {
    position: absolute;
    top: 20px;
    left: 25px;
    width: 20px;
    height: 25px;
    background: linear-gradient(135deg, #4169e1 0%, #1e90ff 100%);
    border-radius: 50% 0 0 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Windows */
.window {
    position: absolute;
    width: 10px;
    height: 10px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    top: 38px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3);
}

.window-1 { left: 80px; }
.window-2 { left: 95px; }
.window-3 { left: 110px; }

/* Propeller */
.propeller {
    position: absolute;
    top: 45px;
    left: 135px;
    width: 20px;
    height: 20px;
    animation: spin 0.1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.propeller-blade {
    position: absolute;
    width: 20px;
    height: 3px;
    background: linear-gradient(90deg, rgba(139, 139, 139, 0.8) 0%, rgba(169, 169, 169, 0.8) 100%);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform-origin: center;
}

.propeller-blade-1 {
    transform: translate(-50%, -50%) rotate(45deg);
}

.propeller-blade-2 {
    transform: translate(-50%, -50%) rotate(-45deg);
}

.propeller-center {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #333;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Contrail */
.contrail {
    position: absolute;
    top: 50px;
    left: -100px;
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.6) 50%, transparent 100%);
    border-radius: 50%;
    animation: contrail-fade 2s ease-in-out infinite;
}

@keyframes contrail-fade {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.7; }
}

/* Responsive */
@media (max-width: 640px) {
    .plane-container {
        top: 10%;
    }

    .plane {
        transform: scale(0.6);
    }
}
</style>
