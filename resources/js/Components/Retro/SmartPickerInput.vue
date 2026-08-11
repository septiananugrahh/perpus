<script setup>
import { nextTick, onMounted, ref } from "vue";
import axios from "axios";
import CameraScanner from "./CameraScanner.vue";

const props = defineProps({
    placeholder: { type: String, default: "Scan barcode atau ketik nama..." },
    disabled: { type: Boolean, default: false },
    autocompleteUrl: { type: String, required: true },
    minChars: { type: Number, default: 2 },
});

// emit 'scan' saat Enter ditekan (dari USB scanner atau user pencet Enter)
// emit 'select' saat user klik salah satu hasil dropdown pencarian nama
const emit = defineEmits(["scan", "select"]);

const value = ref("");
const inputEl = ref(null);
const showCamera = ref(false);

const results = ref([]);
const loading = ref(false);
const showDropdown = ref(false);
let debounceTimer = null;

function onInput() {
    clearTimeout(debounceTimer);
    const q = value.value.trim();

    if (q.length < props.minChars) {
        results.value = [];
        showDropdown.value = false;
        return;
    }

    debounceTimer = setTimeout(() => search(q), 350);
}

async function search(q) {
    loading.value = true;
    try {
        const { data } = await axios.get(props.autocompleteUrl, {
            params: { q },
        });
        results.value = data;
        showDropdown.value = true;
    } catch (e) {
        results.value = [];
    } finally {
        loading.value = false;
    }
}

function handleEnter() {
    // Kalau lagi nunjuk dropdown hasil pencarian, Enter tidak dianggap kode scan
    if (showDropdown.value && results.value.length > 0) return;

    const kode = value.value.trim();
    if (!kode) return;

    emit("scan", kode);
    clear();
}

function selectItem(item) {
    emit("select", item);
    clear();
}

function clear() {
    value.value = "";
    results.value = [];
    showDropdown.value = false;
}

function onBlur() {
    setTimeout(() => (showDropdown.value = false), 150);
}

function focus() {
    nextTick(() => inputEl.value?.focus());
}

function onCameraDetected(kode) {
    showCamera.value = false;
    emit("scan", kode);
}

onMounted(focus);
defineExpose({ focus, clear });
</script>

<template>
    <div class="smart-input-wrap">
        <div class="smart-input-row">
            <input
                ref="inputEl"
                v-model="value"
                type="text"
                :placeholder="placeholder"
                :disabled="disabled"
                class="smart-input"
                @input="onInput"
                @keyup.enter="handleEnter"
                @focus="results.length && (showDropdown = true)"
                @blur="onBlur"
            />
            <button
                type="button"
                class="smart-input__camera"
                :disabled="disabled"
                title="Scan pakai kamera"
                @click="showCamera = true"
            >
                📷
            </button>
        </div>

        <div v-if="loading" class="smart-loading">Mencari...</div>

        <div v-if="showDropdown && results.length > 0" class="smart-dropdown">
            <button
                v-for="(item, i) in results"
                :key="i"
                type="button"
                class="smart-item"
                @mousedown.prevent="selectItem(item)"
            >
                <slot name="item" :item="item" />
            </button>
        </div>

        <div v-else-if="showDropdown && !loading" class="smart-dropdown">
            <p class="smart-empty">
                Tidak ada hasil. Kalau ini kode barcode, tekan Enter untuk scan
                langsung.
            </p>
        </div>

        <CameraScanner
            v-if="showCamera"
            @detected="onCameraDetected"
            @close="showCamera = false"
        />
    </div>
</template>

<style scoped>
.smart-input-wrap {
    position: relative;
}

.smart-input-row {
    display: flex;
    gap: 8px;
}

.smart-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    font-size: 15px;
    outline: none;
}
.smart-input:focus {
    background: var(--retro-yellow);
}
.smart-input:disabled {
    background: #f5f5f5;
    color: var(--retro-muted);
}

.smart-input__camera {
    width: 48px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    background: #fff;
    font-size: 18px;
    cursor: pointer;
    flex-shrink: 0;
}
.smart-input__camera:hover:not(:disabled) {
    background: var(--retro-cyan);
}
.smart-input__camera:disabled {
    background: #f5f5f5;
    opacity: 0.5;
    cursor: not-allowed;
}

.smart-loading {
    font-size: 12px;
    color: var(--retro-text-secondary);
    margin-top: 4px;
}

.smart-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 56px; /* jangan nutupin tombol kamera */
    background: #fff;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    box-shadow: 4px 4px 0 0 var(--retro-border);
    max-height: 240px;
    overflow-y: auto;
    z-index: 50;
}

.smart-item {
    display: block;
    width: 100%;
    text-align: left;
    padding: 10px 14px;
    background: #fff;
    border: none;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}
.smart-item:last-child {
    border-bottom: none;
}
.smart-item:hover {
    background: var(--retro-yellow);
}

.smart-empty {
    padding: 12px 14px;
    font-size: 12px;
    color: var(--retro-muted);
}
</style>
