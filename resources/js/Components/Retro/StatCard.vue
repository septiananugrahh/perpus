<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    delta: { type: String, default: '' },
    deltaText: { type: String, default: '' },
    tone: { type: String, default: 'up' }, // up | down
    color: { type: String, default: 'pink' },
    icon: { type: String, default: '' }, // emoji/simbol pengganti iconify (tanpa dependency baru)
});
</script>

<template>
    <div class="retro-stat">
        <div class="retro-stat__text">
            <span class="retro-stat__label">{{ label }}</span>
            <span class="retro-stat__value font-cabinet">{{ value }}</span>
            <span v-if="delta" class="retro-stat__delta" :class="tone === 'up' ? 'retro-stat__delta--up' : 'retro-stat__delta--down'">
                {{ tone === 'up' ? '↑' : '↓' }} {{ delta }} {{ deltaText }}
            </span>
        </div>
        <div class="retro-stat__icon" :style="{ background: `var(--retro-${color})` }">
            <span>{{ icon }}</span>
        </div>
    </div>
</template>

<style scoped>
.retro-stat {
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 4px 4px 0 0 var(--retro-border);
    border-radius: 24px;
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.retro-stat__text {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.retro-stat__label {
    color: var(--retro-text-secondary);
    font-size: 14px;
    font-weight: 500;
}

.retro-stat__value {
    font-size: 36px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1;
}

.retro-stat__delta {
    font-size: 12px;
    font-weight: 700;
}
.retro-stat__delta--up { color: #16a34a; }
.retro-stat__delta--down { color: #dc2626; }

.retro-stat__icon {
    width: 72px;
    height: 72px;
    border-radius: 16px;
    border: 2px solid var(--retro-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
}
</style>
