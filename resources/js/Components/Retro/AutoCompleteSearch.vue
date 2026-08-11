<script setup>
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
    apiUrl: { type: String, required: true },
    placeholder: { type: String, default: "Ketik untuk mencari..." },
    minChars: { type: Number, default: 2 },
});

const emit = defineEmits(["select"]);

const query = ref("");
const results = ref([]);
const loading = ref(false);
const showDropdown = ref(false);
let debounceTimer = null;

watch(query, (value) => {
    clearTimeout(debounceTimer);
    if (value.trim().length < props.minChars) {
        results.value = [];
        showDropdown.value = false;
        return;
    }
    debounceTimer = setTimeout(() => search(value), 350);
});

async function search(q) {
    loading.value = true;
    try {
        const { data } = await axios.get(props.apiUrl, { params: { q } });
        results.value = data;
        showDropdown.value = true;
    } catch (e) {
        results.value = [];
    } finally {
        loading.value = false;
    }
}

function select(item) {
    emit("select", item);
    query.value = "";
    results.value = [];
    showDropdown.value = false;
}

function onBlur() {
    // beri jeda supaya klik pada item sempat kedaftar sebelum dropdown ditutup
    setTimeout(() => (showDropdown.value = false), 150);
}
</script>

<template>
    <div class="ac-wrap">
        <input
            v-model="query"
            type="text"
            :placeholder="placeholder"
            class="ac-input"
            @focus="results.length && (showDropdown = true)"
            @blur="onBlur"
        />

        <div v-if="loading" class="ac-loading">Mencari...</div>

        <div v-if="showDropdown && results.length > 0" class="ac-dropdown">
            <button
                v-for="(item, i) in results"
                :key="i"
                type="button"
                class="ac-item"
                @mousedown.prevent="select(item)"
            >
                <slot name="item" :item="item" />
            </button>
        </div>

        <div
            v-if="showDropdown && !loading && results.length === 0"
            class="ac-dropdown"
        >
            <p class="ac-empty">Tidak ada hasil.</p>
        </div>
    </div>
</template>

<style scoped>
.ac-wrap {
    position: relative;
}

.ac-input {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 16px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    font-size: 14px;
    outline: none;
}
.ac-input:focus {
    background: var(--retro-yellow);
}

.ac-loading {
    font-size: 12px;
    color: var(--retro-text-secondary);
    margin-top: 4px;
}

.ac-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: #fff;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    box-shadow: 4px 4px 0 0 var(--retro-border);
    max-height: 220px;
    overflow-y: auto;
    z-index: 50;
}

.ac-item {
    display: block;
    width: 100%;
    text-align: left;
    padding: 10px 14px;
    background: #fff;
    border: none;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}
.ac-item:last-child {
    border-bottom: none;
}
.ac-item:hover {
    background: var(--retro-yellow);
}

.ac-empty {
    padding: 12px 14px;
    font-size: 13px;
    color: var(--retro-muted);
}
</style>
