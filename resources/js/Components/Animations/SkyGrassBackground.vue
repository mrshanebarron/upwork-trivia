<script setup>
// Sky and grass animated background
</script>

<template>
    <div class="background-container">
        <!-- Sky gradient with subtle animation -->
        <div class="sky"></div>

        <!-- Grass with subtle sway -->
        <div class="grass-container">
            <div class="grass-layer grass-back"></div>
            <div class="grass-layer grass-middle"></div>
            <div class="grass-layer grass-front"></div>

            <!-- Individual grass blades for detail -->
            <div class="grass-blades">
                <div class="grass-blade" v-for="i in 30" :key="i"
                     :style="{
                         left: `${Math.random() * 100}%`,
                         animationDelay: `${Math.random() * 3}s`,
                         height: `${20 + Math.random() * 15}px`
                     }">
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.background-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    overflow: hidden;
}

/* Sky */
.sky {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        to bottom,
        #87CEEB 0%,
        #87CEEB 50%,
        #B0E0E6 70%,
        #ADD8E6 100%
    );
    animation: sky-shift 30s ease-in-out infinite;
}

@keyframes sky-shift {
    0%, 100% {
        background: linear-gradient(
            to bottom,
            #87CEEB 0%,
            #87CEEB 50%,
            #B0E0E6 70%,
            #ADD8E6 100%
        );
    }
    50% {
        background: linear-gradient(
            to bottom,
            #7EC8E3 0%,
            #7EC8E3 50%,
            #A8D8E8 70%,
            #9DD0DE 100%
        );
    }
}

/* Grass container */
.grass-container {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 40%;
    overflow: hidden;
}

/* Grass layers for depth */
.grass-layer {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.grass-back {
    background: linear-gradient(
        to top,
        #2d5016 0%,
        #3a6b1f 30%,
        #4a7c2a 100%
    );
    transform: translateY(20px);
    opacity: 0.6;
}

.grass-middle {
    background: linear-gradient(
        to top,
        #3a6b1f 0%,
        #4a7c2a 40%,
        #5a8d35 100%
    );
    transform: translateY(10px);
    opacity: 0.8;
}

.grass-front {
    background: linear-gradient(
        to top,
        #4a7c2a 0%,
        #5a8d35 50%,
        #6b9e40 100%
    );
}

/* Individual grass blades */
.grass-blades {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 40px;
    pointer-events: none;
}

.grass-blade {
    position: absolute;
    bottom: 0;
    width: 3px;
    background: linear-gradient(
        to top,
        #4a7c2a 0%,
        #6b9e40 50%,
        #7fb350 100%
    );
    border-radius: 50% 50% 0 0;
    transform-origin: bottom center;
    animation: sway 3s ease-in-out infinite;
    opacity: 0.8;
}

@keyframes sway {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(2deg);
    }
    75% {
        transform: rotate(-2deg);
    }
}

/* Add some flowers/details */
.grass-blades::before {
    content: '';
    position: absolute;
    bottom: 10px;
    left: 20%;
    width: 8px;
    height: 8px;
    background: #FFD700;
    border-radius: 50%;
    box-shadow:
        50px 5px 0 #FFD700,
        120px -3px 0 #FF69B4,
        200px 8px 0 #FFD700,
        300px 2px 0 #FF69B4,
        400px 6px 0 #FFD700,
        500px -2px 0 #FF69B4;
}

/* Responsive */
@media (max-width: 640px) {
    .grass-container {
        height: 30%;
    }

    .grass-blades {
        height: 25px;
    }

    .grass-blade {
        width: 2px;
    }
}
</style>
