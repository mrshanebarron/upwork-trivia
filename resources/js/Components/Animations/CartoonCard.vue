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
    <div class="cartoon-card" :class="`variant-${variant}`">
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
.cartoon-card {
    position: relative;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 20px;
    border: 4px solid #2c5f2d;
    padding: 2rem;
    box-shadow:
        0 8px 0 rgba(26, 58, 27, 0.8),
        0 12px 20px rgba(0, 0, 0, 0.3);
}

/* Variants */
.variant-primary {
    background: rgba(255, 248, 220, 0.9);
    border-color: #8b6914;
    box-shadow:
        0 8px 0 rgba(107, 78, 15, 0.8),
        0 12px 20px rgba(0, 0, 0, 0.3);
}

.variant-success {
    background: rgba(232, 245, 233, 0.9);
    border-color: #2e7d32;
    box-shadow:
        0 8px 0 rgba(27, 94, 32, 0.8),
        0 12px 20px rgba(0, 0, 0, 0.3);
}

.variant-warning {
    background: rgba(255, 243, 224, 0.9);
    border-color: #e65100;
    box-shadow:
        0 8px 0 rgba(191, 54, 12, 0.8),
        0 12px 20px rgba(0, 0, 0, 0.3);
}

/* Card title */
.card-title {
    margin-bottom: 1.5rem;
}

.card-title h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c5f2d;
}

/* Card content */
.card-content {
    color: #333;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 640px) {
    .cartoon-card {
        padding: 1.5rem;
        border-radius: 15px;
        border-width: 3px;
    }

    .card-title h3 {
        font-size: 1.25rem;
    }
}
</style>
