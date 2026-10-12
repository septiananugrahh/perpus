<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroButton from "@/Components/Retro/RetroButton.vue";
import { ref, computed, watch } from "vue";

const props = defineProps({
    preview: { type: Array, required: true },
    total: { type: Number, default: 0 },
    columns: { type: Object, required: true },
    subkategori: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters?.search ?? "");
let debounceTimer = null;
watch(search, (value) => {
    clearTimeout(debounceTimer);
    if (value.length === 0) {
        debounceTimer = setTimeout(() => runSearch(""), 300);
        return;
    }
    if (value.length < 3) return;
    debounceTimer = setTimeout(() => runSearch(value), 400);
});
function runSearch(value) {
    router.get(
        route("buku.export.form"),
        { search: value },
        { preserveState: true, preserveScroll: true, replace: true, only: ["preview", "total", "filters"] }
    );
}

const availableColumns = ref([]);
const selectedColumns = ref(Object.keys(props.columns).map(k => ({ key: k, label: props.columns[k] })));

function moveRight(key) {
    if (!availableColumns.value.find(c => c.key === key)) return;
    const idx = availableColumns.value.findIndex(c => c.key === key);
    const [item] = availableColumns.value.splice(idx, 1);
    selectedColumns.value.push(item);
}

function moveLeft(key) {
    const idx = selectedColumns.value.findIndex(c => c.key === key);
    if (idx === -1 || selectedColumns.value.length === 1) return;
    const [item] = selectedColumns.value.splice(idx, 1);
    availableColumns.value.push(item);
}

// Drag untuk reorder selectedColumns
const draggedIndex = ref(null);
function handleDragStart(index, e) {
    draggedIndex.value = index;
    e.dataTransfer.effectAllowed = "move";
}
function handleDragOver(index, e) {
    e.preventDefault();
    if (draggedIndex.value === null || draggedIndex.value === index) return;
    const items = [...selectedColumns.value];
    const [removed] = items.splice(draggedIndex.value, 1);
    items.splice(index, 0, removed);
    selectedColumns.value = items;
    draggedIndex.value = index;
}
function handleDragEnd() {
    draggedIndex.value = null;
}
function moveUp(index) {
    if (index === 0) return;
    const items = [...selectedColumns.value];
    [items[index], items[index - 1]] = [items[index - 1], items[index]];
    selectedColumns.value = items;
}
function moveDown(index) {
    if (index === selectedColumns.value.length - 1) return;
    const items = [...selectedColumns.value];
    [items[index], items[index + 1]] = [items[index + 1], items[index]];
    selectedColumns.value = items;
}
function exportExcel() {
    if (selectedColumns.value.length === 0) return;
    const params = new URLSearchParams();
    selectedColumns.value.forEach(c => params.append("columns[]", c.key));
    if (search.value) params.append("search", search.value);
    window.location.href = route("buku.export.download") + "?" + params.toString();
}
</script>

<template>
    <Head title="Export Buku" />
    <AuthenticatedLayout>
        <template #header>
            <div class="retro-header-row">
                <h2 class="font-cabinet retro-title">Export Data Buku</h2>
                <div class="retro-header-actions">
                    <Link :href="route('buku.index')">
                        <RetroButton variant="secondary">Kembali</RetroButton>
                    </Link>
                    <RetroButton variant="primary" @click="exportExcel" :disabled="selectedColumns.length === 0">
                        📥 Export Excel
                    </RetroButton>
                </div>
            </div>
            <div class="retro-search-wrap">
                <input v-model="search" type="text" placeholder="Cari judul, pengarang, penerbit, ISBN... (min. 3 huruf)" class="retro-search" />
            </div>
        </template>

        <div class="retro-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="retro-card" style="padding: 20px; border: 2px solid var(--retro-border); border-radius: 24px;">
                <h3 style="margin: 0 0 12px 0;">Kolom Tersedia</h3>
                <div style="display: flex; flex-direction: column; gap: 6px; max-height: 320px; overflow-y: auto;">
                    <div v-for="c in availableColumns" :key="c.key" class="retro-item" @click="moveRight(c.key)">
                        <span>{{ c.label }}</span>
                        <span class="retro-drag-handle">→</span>
                    </div>
                </div>
            </div>

            <div class="retro-card" style="padding: 20px; border: 2px solid var(--retro-border); border-radius: 24px;">
                <h3 style="margin: 0 0 12px 0;">Kolom Terpilih (Urutkan dengan Drag)</h3>
                <div style="display: flex; flex-direction: column; gap: 6px; max-height: 320px; overflow-y: auto;">
                    <div
                        v-for="(c, idx) in selectedColumns"
                        :key="c.key"
                        :class="['retro-item', 'retro-item--selected', { 'retro-item--dragging': draggedIndex === idx }]"
                        :draggable="true"
                        @dragstart="handleDragStart(idx, $event)"
                        @dragover="handleDragOver(idx, $event)"
                        @drop.prevent
                        @dragend="handleDragEnd"
                    >
                        <span class="retro-drag-handle" title="Drag untuk memindah">☰</span>
                        <span style="flex: 1;">{{ c.label }}</span>
                        <button class="retro-mini" title="Naik" @click="moveUp(idx)">▲</button>
                        <button class="retro-mini" title="Turun" @click="moveDown(idx)">▼</button>
                        <button class="retro-mini" title="Hapus kolom" @click="moveLeft(c.key)">✕</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="retro-card" style="padding: 20px; border: 2px solid var(--retro-border); border-radius: 24px;">
            <h3 style="margin: 0 0 12px 0;">Preview (5 baris pertama)</h3>
            <div v-if="preview.length > 0" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr>
                            <th v-for="c in selectedColumns" :key="c.key" style="padding: 8px 10px; border-bottom: 2px solid #1a1a1a; background: #1a1a1a; color: #fff; text-align: left; font-weight: 700;">
                                {{ c.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(b, idx) in preview" :key="idx">
                            <td v-for="c in selectedColumns" :key="c.key" style="padding: 7px 10px; border-bottom: 1px solid #ddd;">
                                <template v-if="c.key === 'kode_subkategori'">{{ subkategori[b[c.key]] ?? '-' }}</template>
                                <template v-else-if="b[c.key] === null || b[c.key] === ''">-</template>
                                <template v-else>{{ b[c.key] }}</template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="retro-empty">Tidak ada data buku</div>
            <p style="margin-top: 10px; font-size: 12px; color: var(--retro-muted);">Total: {{ total }}</p>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.retro-header-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.retro-title { font-size: 32px; font-weight: 800; color: #1a1a1a; }
.retro-header-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.retro-search-wrap { margin-top: 20px; }
.retro-search { width: 100%; max-width: 480px; padding: 12px 20px; border: 2px solid var(--retro-border); border-radius: 16px; font-size: 14px; outline: none; }
.retro-search:focus { background: var(--retro-yellow); }
.retro-item { padding: 9px 12px; border: 2px solid var(--retro-border); border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
.retro-item:hover { background: var(--retro-yellow); }
.retro-item--selected { cursor: grab; }
.retro-drag-handle { font-size: 14px; }
@media (max-width: 960px) { .retro-grid { grid-template-columns: 1fr !important; } }
.retro-mini {
    min-width: 28px;
    height: 28px;
    padding: 0;
    border: 1px solid var(--retro-border);
    border-radius: 8px;
    background: #fff;
    color: #1a1a1a;
    font-size: 11px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 4px;
    transition: all 0.1s;
}
.retro-mini:hover { background: var(--retro-yellow); }
.retro-item--dragging { opacity: 0.5; }
.retro-drag-handle { cursor: grab; }
</style>
