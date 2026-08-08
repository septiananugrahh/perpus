<script setup>
defineProps({
    padding: { type: String, default: "24px" }, // sekarang nilai CSS langsung, bukan class Tailwind
    rounded: { type: String, default: "24px" },
});
</script>

<template>
    <div
        class="retro-card"
        :style="{ borderRadius: rounded, padding: padding }"
    >
        <slot />
    </div>
</template>

<style scoped>
.retro-card {
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 4px 4px 0 0 var(--retro-border);
    overflow: hidden;
}
</style>
