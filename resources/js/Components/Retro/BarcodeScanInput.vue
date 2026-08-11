<script setup>
import { nextTick, onMounted, ref } from "vue";
import CameraScanner from "./CameraScanner.vue";

const props = defineProps({
    placeholder: { type: String, default: "Scan atau ketik kode..." },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(["scan"]);

const value = ref("");
const inputEl = ref(null);
const showCamera = ref(false);

function handleEnter() {
    const kode = value.value.trim();
    if (!kode) return;
    emit("scan", kode);
    value.value = "";
}

function focus() {
    nextTick(() => inputEl.value?.focus());
}

function onCameraDetected(kode) {
    showCamera.value = false;
    emit("scan", kode);
}

onMounted(focus);
defineExpose({ focus });
</script>

<template>
    <div class="barcode-input-wrap">
        <input
            ref="inputEl"
            v-model="value"
            type="text"
            :placeholder="placeholder"
            :disabled="disabled"
            class="barcode-input"
            @keyup.enter="handleEnter"
        />
        <button
            type="button"
            class="barcode-input__camera"
            :disabled="disabled"
            title="Scan pakai kamera"
            @click="showCamera = true"
        >
            📷
        </button>

        <CameraScanner
            v-if="showCamera"
            @detected="onCameraDetected"
            @close="showCamera = false"
        />
    </div>
</template>

<style scoped>
.barcode-input-wrap {
    display: flex;
    gap: 8px;
}

.barcode-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    font-size: 15px;
    font-family: "Space Mono", monospace;
    outline: none;
}
.barcode-input:focus {
    background: var(--retro-yellow);
}
.barcode-input:disabled {
    background: #f5f5f5;
    color: var(--retro-muted);
}

.barcode-input__camera {
    width: 48px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    background: #fff;
    font-size: 18px;
    cursor: pointer;
    flex-shrink: 0;
}
.barcode-input__camera:hover:not(:disabled) {
    background: var(--retro-cyan);
}
.barcode-input__camera:disabled {
    background: #f5f5f5;
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
