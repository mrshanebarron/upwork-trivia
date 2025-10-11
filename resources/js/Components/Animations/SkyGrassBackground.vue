<script setup>
// Sky and grass background using Shane's custom artwork
</script>

<template>
    <div class="background-container"></div>
</template>

<style scoped>
.background-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    margin: 0;
    padding: 0;
    z-index: 0;
    overflow: hidden;
    background-color: #a8d5a8;
    background-image: url('/images/background.svg');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
}
</style>
