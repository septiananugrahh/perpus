<script setup>
defineProps({
    padding: { type: String, default: 'p-6' },
    rounded: { type: String, default: '24px' },
});
</script>

<template>
    <div class="retro-card" :class="padding" :style="{ borderRadius: rounded }">
        <slot />
    </div>
</template>

<style scoped>
.retro-card {
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 4px 4px 0 0 var(--retro-border);
}
</style>
