<script setup>
defineProps({
    variant: { type: String, default: 'secondary' }, // primary | secondary | color
    color: { type: String, default: 'pink' }, // dipakai jika variant=color
    as: { type: String, default: 'button' }, // button | a (pakai Link dari luar via slot jika perlu)
});
</script>

<template>
    <component
        :is="as"
        class="retro-btn"
        :class="[
            variant === 'primary' && 'retro-btn--primary',
            variant === 'secondary' && 'retro-btn--secondary',
            variant === 'color' && 'retro-btn--color',
        ]"
        :style="variant === 'color' ? { background: `var(--retro-${color})` } : {}"
    >
        <slot />
    </component>
</template>

<style scoped>
.retro-btn {
    padding: 12px 24px;
    border-radius: 16px;
    border: 2px solid var(--retro-border);
    font-weight: 700;
    font-family: 'Satoshi', sans-serif;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.1s ease;
    text-decoration: none;
}

.retro-btn--primary {
    background: #000;
    color: #fff;
    box-shadow: 4px 4px 0 0 var(--retro-border);
}
.retro-btn--secondary {
    background: #fff;
    color: #000;
}
.retro-btn--secondary:hover {
    background: #f5f5f5;
}
.retro-btn--color {
    color: #000;
    box-shadow: 4px 4px 0 0 var(--retro-border);
}

.retro-btn--primary:active,
.retro-btn--color:active {
    box-shadow: 0px 0px 0 0 var(--retro-border);
    transform: translate(4px, 4px);
}
</style>
