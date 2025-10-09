<script setup>
defineProps({
    title: String,
    variant: {
        type: String,
        default: 'default', // 'default', 'primary', 'success', 'warning'
    },
});
</script>

<template>
    <div class="glassmorphism-card" :class="`variant-${variant}`">
        <div v-if="title" class="card-title">
            <slot name="title">
                <h3>{{ title }}</h3>
            </slot>
        </div>
        <div class="card-content">
            <slot></slot>
        </div>
    </div>
</template>

<style scoped>
.glassmorphism-card {
    position: relative;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 2rem;
    box-shadow:
        0 8px 32px 0 rgba(31, 38, 135, 0.15),
        inset 0 1px 0 0 rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
}

.glassmorphism-card:hover {
    transform: translateY(-5px);
    box-shadow:
        0 12px 40px 0 rgba(31, 38, 135, 0.25),
        inset 0 1px 0 0 rgba(255, 255, 255, 0.5);
}

/* Variants */
.variant-primary {
    background: rgba(66, 153, 225, 0.15);
    border: 1px solid rgba(66, 153, 225, 0.3);
}

.variant-success {
    background: rgba(72, 187, 120, 0.15);
    border: 1px solid rgba(72, 187, 120, 0.3);
}

.variant-warning {
    background: rgba(237, 137, 54, 0.15);
    border: 1px solid rgba(237, 137, 54, 0.3);
}

/* Card title */
.card-title {
    margin-bottom: 1.5rem;
}

.card-title h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: rgba(0, 0, 0, 0.9);
    text-shadow: 0 2px 4px rgba(255, 255, 255, 0.5);
}

/* Card content */
.card-content {
    color: rgba(0, 0, 0, 0.8);
    line-height: 1.6;
}

/* Add subtle shimmer effect */
.glassmorphism-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    transition: left 0.5s ease;
}

.glassmorphism-card:hover::before {
    left: 100%;
}

/* Responsive */
@media (max-width: 640px) {
    .glassmorphism-card {
        padding: 1.5rem;
        border-radius: 15px;
    }

    .card-title h3 {
        font-size: 1.25rem;
    }
}
</style>
