<script setup>
// Animated puppy video - Shane's custom After Effects animation
</script>

<template>
    <div class="puppy-container">
        <video
            autoplay
            loop
            muted
            playsinline
            class="puppy-video"
        >
            <source src="/images/puppy.webm" type="video/webm">
            <source src="/images/puppy.mp4" type="video/mp4">
        </video>
    </div>
</template>

<style scoped>
.puppy-container {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.puppy-video {
    width: auto;
    height: 100%;
    display: block;
    object-fit: contain;
}
</style>
