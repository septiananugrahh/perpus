<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'left',
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'retro-dropdown--left';
    } else if (props.align === 'right') {
        return 'retro-dropdown--right';
    }
    return '';
});

const open = ref(false);
</script>

<template>
    <div class="retro-dropdown-wrapper">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <div
            v-show="open"
            class="retro-dropdown-overlay"
            @click="open = false"
        ></div>

        <Transition
            enter-active-class="retro-dropdown-enter-active"
            enter-from-class="retro-dropdown-enter-from"
            enter-to-class="retro-dropdown-enter-to"
            leave-active-class="retro-dropdown-leave-active"
            leave-from-class="retro-dropdown-leave-from"
            leave-to-class="retro-dropdown-leave-to"
        >
            <div
                v-show="open"
                class="retro-dropdown-content"
                :class="alignmentClasses"
                @click="open = false"
            >
                <slot name="content" />
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.retro-dropdown-wrapper {
    position: relative;
}

.retro-dropdown-overlay {
    position: fixed;
    inset: 0;
    z-index: 40;
}

.retro-dropdown-content {
    position: absolute;
    z-index: 50;
    margin-top: 8px;
    min-width: 200px;
    border: 2px solid var(--retro-border);
    border-radius: 16px;
    background: #fff;
    overflow: hidden;
}

.retro-dropdown--left {
    left: 0;
    transform-origin: top left;
}

.retro-dropdown--right {
    right: 0;
    transform-origin: top right;
}

.retro-dropdown-enter-active {
    transition: opacity 0.2s ease-out, transform 0.2s ease-out;
}

.retro-dropdown-enter-from {
    opacity: 0;
    transform: scale(0.95) translateY(-4px);
}

.retro-dropdown-enter-to {
    opacity: 1;
    transform: scale(1) translateY(0);
}

.retro-dropdown-leave-active {
    transition: opacity 0.15s ease-in, transform 0.15s ease-in;
}

.retro-dropdown-leave-from {
    opacity: 1;
    transform: scale(1) translateY(0);
}

.retro-dropdown-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(-4px);
}
</style>
