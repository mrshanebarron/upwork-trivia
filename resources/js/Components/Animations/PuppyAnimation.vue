<script setup>
import { ref, onMounted } from 'vue';

const puppy = ref(null);
const isWagging = ref(true);
const isBlinking = ref(false);

// Random blink animation
onMounted(() => {
    setInterval(() => {
        isBlinking.value = true;
        setTimeout(() => {
            isBlinking.value = false;
        }, 150);
    }, 3000 + Math.random() * 2000);
});
</script>

<template>
    <div class="puppy-container" ref="puppy">
        <!-- Placeholder puppy SVG - replace with Shane's custom asset -->
        <div class="puppy-body">
            <!-- Head -->
            <div class="puppy-head">
                <!-- Ears -->
                <div class="ear ear-left"></div>
                <div class="ear ear-right"></div>

                <!-- Face -->
                <div class="face">
                    <!-- Eyes -->
                    <div class="eye eye-left" :class="{ blinking: isBlinking }">
                        <div class="pupil"></div>
                    </div>
                    <div class="eye eye-right" :class="{ blinking: isBlinking }">
                        <div class="pupil"></div>
                    </div>

                    <!-- Nose -->
                    <div class="nose"></div>

                    <!-- Mouth -->
                    <div class="mouth"></div>
                </div>
            </div>

            <!-- Body -->
            <div class="body"></div>

            <!-- Tail (wagging) -->
            <div class="tail" :class="{ wagging: isWagging }"></div>

            <!-- Legs -->
            <div class="leg leg-front-left"></div>
            <div class="leg leg-front-right"></div>
            <div class="leg leg-back-left"></div>
            <div class="leg leg-back-right"></div>
        </div>
    </div>
</template>

<style scoped>
.puppy-container {
    position: relative;
    width: 150px;
    height: 150px;
    animation: puppy-bounce 2s ease-in-out infinite;
}

@keyframes puppy-bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.puppy-body {
    position: relative;
    width: 100%;
    height: 100%;
}

/* Head */
.puppy-head {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #f4a460 0%, #d2691e 100%);
    border-radius: 50% 50% 45% 45%;
    z-index: 3;
    animation: head-tilt 3s ease-in-out infinite;
}

@keyframes head-tilt {
    0%, 100% { transform: translateX(-50%) rotate(0deg); }
    50% { transform: translateX(-50%) rotate(-5deg); }
}

/* Ears */
.ear {
    position: absolute;
    width: 20px;
    height: 30px;
    background: linear-gradient(135deg, #8b4513 0%, #654321 100%);
    border-radius: 50% 50% 0 0;
}

.ear-left {
    top: -5px;
    left: 5px;
    transform: rotate(-20deg);
    animation: ear-flop-left 2s ease-in-out infinite;
}

.ear-right {
    top: -5px;
    right: 5px;
    transform: rotate(20deg);
    animation: ear-flop-right 2s ease-in-out infinite;
}

@keyframes ear-flop-left {
    0%, 100% { transform: rotate(-20deg); }
    50% { transform: rotate(-30deg); }
}

@keyframes ear-flop-right {
    0%, 100% { transform: rotate(20deg); }
    50% { transform: rotate(30deg); }
}

/* Face */
.face {
    position: relative;
    width: 100%;
    height: 100%;
}

/* Eyes */
.eye {
    position: absolute;
    width: 12px;
    height: 12px;
    background: white;
    border-radius: 50%;
    top: 20px;
    transition: transform 0.15s ease;
}

.eye-left { left: 12px; }
.eye-right { right: 12px; }

.eye.blinking {
    transform: scaleY(0.1);
}

.pupil {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #000;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: pupil-look 4s ease-in-out infinite;
}

@keyframes pupil-look {
    0%, 100% { transform: translate(-50%, -50%); }
    25% { transform: translate(-30%, -50%); }
    75% { transform: translate(-70%, -50%); }
}

/* Nose */
.nose {
    position: absolute;
    width: 10px;
    height: 8px;
    background: #000;
    border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
    top: 35px;
    left: 50%;
    transform: translateX(-50%);
}

/* Mouth */
.mouth {
    position: absolute;
    width: 20px;
    height: 10px;
    border: 2px solid #000;
    border-top: none;
    border-radius: 0 0 50% 50%;
    top: 40px;
    left: 50%;
    transform: translateX(-50%);
}

/* Body */
.body {
    position: absolute;
    top: 70px;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 40px;
    background: linear-gradient(135deg, #f4a460 0%, #d2691e 100%);
    border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
    z-index: 2;
}

/* Tail */
.tail {
    position: absolute;
    top: 75px;
    right: 15px;
    width: 30px;
    height: 8px;
    background: linear-gradient(90deg, #d2691e 0%, #f4a460 100%);
    border-radius: 0 50% 50% 0;
    transform-origin: left center;
    z-index: 1;
}

.tail.wagging {
    animation: tail-wag 0.5s ease-in-out infinite;
}

@keyframes tail-wag {
    0%, 100% { transform: rotate(-10deg); }
    50% { transform: rotate(30deg); }
}

/* Legs */
.leg {
    position: absolute;
    width: 8px;
    height: 25px;
    background: linear-gradient(180deg, #d2691e 0%, #8b4513 100%);
    border-radius: 0 0 4px 4px;
    z-index: 1;
}

.leg-front-left {
    top: 100px;
    left: 35px;
}

.leg-front-right {
    top: 100px;
    right: 35px;
}

.leg-back-left {
    top: 100px;
    left: 45px;
}

.leg-back-right {
    top: 100px;
    right: 45px;
}

/* Responsive */
@media (max-width: 640px) {
    .puppy-container {
        width: 120px;
        height: 120px;
    }
}
</style>
