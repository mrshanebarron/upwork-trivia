<script setup>
// Clouds animation with multiple layers for parallax effect
</script>

<template>
    <div class="clouds-container">
        <!-- Background layer (slow) -->
        <div class="cloud-layer cloud-layer-back">
            <div class="cloud cloud-1" style="top: 10%; left: 10%;"></div>
            <div class="cloud cloud-2" style="top: 25%; left: 40%;"></div>
            <div class="cloud cloud-3" style="top: 15%; left: 70%;"></div>
        </div>

        <!-- Middle layer (medium speed) -->
        <div class="cloud-layer cloud-layer-middle">
            <div class="cloud cloud-4" style="top: 35%; left: 5%;"></div>
            <div class="cloud cloud-5" style="top: 40%; left: 50%;"></div>
            <div class="cloud cloud-6" style="top: 30%; left: 85%;"></div>
        </div>

        <!-- Foreground layer (faster) -->
        <div class="cloud-layer cloud-layer-front">
            <div class="cloud cloud-7" style="top: 5%; left: 20%;"></div>
            <div class="cloud cloud-8" style="top: 45%; left: 60%;"></div>
        </div>
    </div>
</template>

<style scoped>
.clouds-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 60%;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}

.cloud-layer {
    position: absolute;
    width: 200%;
    height: 100%;
    top: 0;
    left: 0;
}

.cloud-layer-back {
    animation: drift 180s linear infinite;
    opacity: 0.4;
}

.cloud-layer-middle {
    animation: drift 120s linear infinite;
    opacity: 0.6;
}

.cloud-layer-front {
    animation: drift 80s linear infinite;
    opacity: 0.8;
}

@keyframes drift {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}

/* Cloud shapes */
.cloud {
    position: absolute;
    background: white;
    border-radius: 100px;
    width: 120px;
    height: 40px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.cloud::before,
.cloud::after {
    content: '';
    position: absolute;
    background: white;
    border-radius: 50%;
}

.cloud::before {
    width: 50px;
    height: 50px;
    top: -25px;
    left: 20px;
}

.cloud::after {
    width: 70px;
    height: 70px;
    top: -35px;
    right: 20px;
}

/* Cloud size variations */
.cloud-1, .cloud-4, .cloud-7 {
    width: 100px;
    height: 35px;
}

.cloud-2, .cloud-5 {
    width: 140px;
    height: 45px;
}

.cloud-3, .cloud-6, .cloud-8 {
    width: 110px;
    height: 38px;
}

.cloud-1::before {
    width: 40px;
    height: 40px;
    top: -20px;
}

.cloud-1::after {
    width: 60px;
    height: 60px;
    top: -30px;
}

.cloud-2::before {
    width: 55px;
    height: 55px;
    top: -28px;
}

.cloud-2::after {
    width: 75px;
    height: 75px;
    top: -38px;
}

/* Individual cloud animations for more organic movement */
.cloud-1 { animation: float-1 8s ease-in-out infinite; }
.cloud-2 { animation: float-2 10s ease-in-out infinite; }
.cloud-3 { animation: float-3 7s ease-in-out infinite; }
.cloud-4 { animation: float-1 9s ease-in-out infinite; }
.cloud-5 { animation: float-2 11s ease-in-out infinite; }
.cloud-6 { animation: float-3 8.5s ease-in-out infinite; }
.cloud-7 { animation: float-1 7.5s ease-in-out infinite; }
.cloud-8 { animation: float-2 9.5s ease-in-out infinite; }

@keyframes float-1 {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

@keyframes float-2 {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@keyframes float-3 {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Responsive */
@media (max-width: 640px) {
    .cloud {
        width: 80px;
        height: 30px;
    }

    .cloud::before {
        width: 35px;
        height: 35px;
        top: -18px;
    }

    .cloud::after {
        width: 50px;
        height: 50px;
        top: -25px;
    }
}
</style>
