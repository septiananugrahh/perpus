<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import RetroButton from "@/Components/Retro/RetroButton.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps({
    bukus: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const page = usePage();
const errorMsg = computed(() => page.props.errors?.ids);
const csrfToken = computed(
    () => document.querySelector('meta[name="csrf-token"]')?.content ?? "",
);

// ==== Search debounce (sama pola dengan Inventory) ====
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
        route("label.index"),
        { search: value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["bukus", "filters"],
        },
    );
}

// ==== Mode rentang ID ====
const exportAwal = ref("");
const exportAkhir = ref("");

// ==== Mode checkbox manual ====
const selectedIds = ref([]);
const allOnPageSelected = computed(
    () =>
        props.bukus.data.length > 0 &&
        props.bukus.data.every((b) => selectedIds.value.includes(b.id_buku)),
);

function toggleId(id) {
    const i = selectedIds.value.indexOf(id);
    if (i === -1) selectedIds.value.push(id);
    else selectedIds.value.splice(i, 1);
}

function toggleAllOnPage() {
    if (allOnPageSelected.value) {
        selectedIds.value = selectedIds.value.filter(
            (id) => !props.bukus.data.some((b) => b.id_buku === id),
        );
    } else {
        props.bukus.data.forEach((b) => {
            if (!selectedIds.value.includes(b.id_buku))
                selectedIds.value.push(b.id_buku);
        });
    }
}

const formEl = ref(null);
function submitExport() {
    formEl.value?.submit();
}
</script>

<template>
    <Head title="Export Label" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-cabinet retro-title">Export Label Barcode</h2>
            <p class="retro-subtitle">
                Cetak label barcode untuk penanda fisik buku (rak/punggung
                buku).
            </p>
        </template>

        <div v-if="errorMsg" class="retro-alert">{{ errorMsg }}</div>

        <!-- form asli, submit native (bukan Inertia) supaya PDF terbuka di tab baru -->
        <form
            ref="formEl"
            :action="route('label.export')"
            method="POST"
            target="_blank"
        >
            <input type="hidden" name="_token" :value="csrfToken" />
            <input
                v-for="id in selectedIds"
                :key="id"
                type="hidden"
                name="ids[]"
                :value="id"
            />

            <div class="retro-row">
                <RetroCard>
                    <h3 class="font-cabinet retro-panel-title">
                        Mode 1: Rentang ID Buku
                    </h3>
                    <p class="retro-panel-subtitle">
                        Dipakai kalau tidak ada buku yang dicentang di bawah.
                    </p>
                    <div class="retro-range-fields">
                        <div class="retro-field">
                            <label class="retro-label">ID Awal</label>
                            <input
                                v-model="exportAwal"
                                name="export_awal"
                                type="number"
                                class="retro-input"
                                placeholder="Contoh: 1001"
                            />
                        </div>
                        <div class="retro-field">
                            <label class="retro-label">ID Akhir</label>
                            <input
                                v-model="exportAkhir"
                                name="export_akhir"
                                type="number"
                                class="retro-input"
                                placeholder="Contoh: 1050"
                            />
                        </div>
                    </div>
                </RetroCard>

                <RetroCard>
                    <h3 class="font-cabinet retro-panel-title">
                        Mode 2: Pilih Manual
                    </h3>
                    <p class="retro-panel-subtitle">
                        {{ selectedIds.length }} buku dipilih.
                        <span v-if="selectedIds.length > 0"
                            >Mode ini akan dipakai (mengabaikan rentang
                            ID).</span
                        >
                    </p>
                    <RetroButton
                        v-if="selectedIds.length > 0"
                        variant="secondary"
                        type="button"
                        @click="selectedIds = []"
                    >
                        Kosongkan Pilihan
                    </RetroButton>
                </RetroCard>
            </div>

            <div class="retro-toolbar">
                <RetroButton
                    type="submit"
                    variant="primary"
                    @click.prevent="submitExport"
                >
                    🖨️ Export Label (PDF)
                </RetroButton>
            </div>
        </form>

        <!-- Tabel checkbox manual -->
        <RetroCard padding="0" rounded="24px" class="retro-mt">
            <div class="retro-table-header">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari buku... (min. 3 huruf)"
                    class="retro-search"
                />
            </div>

            <div class="retro-table-bleed-full">
                <table class="retro-select-table">
                    <thead>
                        <tr>
                            <th class="retro-col-check">
                                <input
                                    type="checkbox"
                                    :checked="allOnPageSelected"
                                    @change="toggleAllOnPage"
                                />
                            </th>
                            <th>ID</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Klasifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="b in bukus.data"
                            :key="b.id_buku"
                            @click="toggleId(b.id_buku)"
                            class="retro-select-row"
                        >
                            <td class="retro-col-check" @click.stop>
                                <input
                                    type="checkbox"
                                    :checked="selectedIds.includes(b.id_buku)"
                                    @change="toggleId(b.id_buku)"
                                />
                            </td>
                            <td>{{ b.id_buku }}</td>
                            <td>{{ b.judul_buku }}</td>
                            <td>{{ b.pengarang_buku }}</td>
                            <td>{{ b.klasifikasi_buku }}</td>
                        </tr>
                        <tr v-if="bukus.data.length === 0">
                            <td colspan="5" class="retro-empty">
                                Tidak ada buku ditemukan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </RetroCard>

        <div class="retro-pagination">
            <button
                v-for="(link, i) in bukus.links"
                :key="i"
                class="retro-page-link"
                :class="{
                    'retro-page-link--active': link.active,
                    'retro-page-link--disabled': !link.url,
                }"
                :disabled="!link.url"
                v-html="link.label"
                @click="
                    link.url &&
                    router.get(
                        link.url,
                        { search },
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['bukus', 'filters'],
                        },
                    )
                "
            />
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.retro-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1a1a;
}
.retro-subtitle {
    font-size: 14px;
    color: var(--retro-text-secondary);
    margin-top: 4px;
}

.retro-alert {
    background: var(--retro-red);
    color: #fff;
    border: 2px solid var(--retro-border);
    border-radius: 12px;
    padding: 12px 16px;
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 16px;
}

.retro-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
@media (min-width: 1024px) {
    .retro-row {
        grid-template-columns: 1fr 1fr;
    }
}

.retro-panel-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
}
.retro-panel-subtitle {
    font-size: 13px;
    color: var(--retro-text-secondary);
    margin: 4px 0 16px;
}

.retro-range-fields {
    display: flex;
    gap: 16px;
}
.retro-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}
.retro-label {
    font-size: 12px;
    font-weight: 700;
}
.retro-input {
    padding: 10px 14px;
    border: 2px solid var(--retro-border);
    border-radius: 12px;
    font-size: 14px;
    outline: none;
}
.retro-input:focus {
    background: var(--retro-yellow);
}

.retro-toolbar {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 24px;
}

.retro-mt {
    margin-top: 0;
}

.retro-table-header {
    padding: 20px 24px 0;
}
.retro-search {
    width: 100%;
    max-width: 380px;
    padding: 10px 16px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    font-size: 13px;
    outline: none;
}
.retro-search:focus {
    background: var(--retro-yellow);
}

.retro-table-bleed-full {
    margin-top: 16px;
    overflow-x: auto;
}

.retro-select-table {
    width: 100%;
    border-collapse: collapse;
}
.retro-select-table thead th {
    background: #fafafa;
    text-align: left;
    font-size: 11px;
    text-transform: uppercase;
    color: var(--retro-text-secondary);
    padding: 12px 16px;
    border-bottom: 2px solid var(--retro-border);
    white-space: nowrap;
}
.retro-col-check {
    width: 44px;
    text-align: center;
}
.retro-select-row {
    cursor: pointer;
}
.retro-select-row:hover {
    background: #fafafa;
}
.retro-select-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}
.retro-empty {
    text-align: center;
    padding: 32px;
    color: var(--retro-muted);
}

.retro-pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 20px;
}
.retro-page-link {
    padding: 8px 14px;
    border: 2px solid var(--retro-border);
    border-radius: 10px;
    background: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
.retro-page-link:hover {
    background: var(--retro-yellow);
}
.retro-page-link--active {
    background: #1a1a1a;
    color: #fff;
}
.retro-page-link--disabled {
    color: #ccc;
    border-color: #ddd;
    cursor: not-allowed;
}
</style>
