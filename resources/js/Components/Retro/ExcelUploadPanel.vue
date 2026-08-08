<script setup>
import RetroButton from "@/Components/Retro/RetroButton.vue";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import axios from "axios";

const props = defineProps({
    subkategori: { type: Object, default: () => ({}) },
});

const columns = [
    { key: "tanggalpembukuan_buku", label: "Tgl Pembukuan" },
    { key: "kode_subkategori", label: "Subkategori" },
    { key: "judul_buku", label: "Judul Buku" },
    { key: "klasifikasi_buku", label: "Klasifikasi" },
    { key: "penerbit_buku", label: "Penerbit" },
    { key: "pengarang_buku", label: "Pengarang" },
    { key: "keterangan_buku", label: "Keterangan" },
    { key: "edisi_buku", label: "Edisi" },
    { key: "tahunterbit_buku", label: "Th. Terbit" },
    { key: "kotaterbit_buku", label: "Kota Terbit" },
    { key: "isbn_buku", label: "ISBN" },
    { key: "nomerpanggil_buku", label: "No. Panggil" },
    { key: "eksemplar_buku", label: "Eksemplar" },
    { key: "series_buku", label: "Series" },
    { key: "sumber_buku", label: "Sumber" },
];

const fileInput = ref(null);
const fileName = ref("");
const verifying = ref(false);
const saving = ref(false);
const uploadError = ref("");

const previewRows = ref([]);
const previewErrors = ref({}); // { rowIndex: [msg, msg, ...] }
const isValid = ref(false);
const hasPreview = ref(false);

const errorRowCount = computed(() => Object.keys(previewErrors.value).length);

function pickFile() {
    fileInput.value?.click();
}

function onFileChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    fileName.value = file.name;
    resetPreview();
    verifyFile(file);
}

function resetPreview() {
    previewRows.value = [];
    previewErrors.value = {};
    isValid.value = false;
    hasPreview.value = false;
    uploadError.value = "";
}

async function verifyFile(file) {
    verifying.value = true;
    uploadError.value = "";

    const formData = new FormData();
    formData.append("file", file);

    try {
        const { data } = await axios.post(
            route("buku.upload.preview"),
            formData,
            {
                headers: { "Content-Type": "multipart/form-data" },
            },
        );

        previewRows.value = data.rows;
        previewErrors.value = data.errors;
        isValid.value = data.valid;
        hasPreview.value = true;

        if (data.rows.length === 0) {
            uploadError.value =
                "File tidak berisi data (baris kosong semua atau format salah).";
        }
    } catch (err) {
        uploadError.value =
            err.response?.data?.message ||
            "Gagal memproses file. Pastikan format sesuai template.";
    } finally {
        verifying.value = false;
    }
}

function confirmSave() {
    if (!isValid.value || previewRows.value.length === 0) return;
    saving.value = true;
    router.post(
        route("buku.store"),
        { rows: previewRows.value },
        { onFinish: () => (saving.value = false) },
    );
}

function cancelPreview() {
    resetPreview();
    fileName.value = "";
    if (fileInput.value) fileInput.value.value = "";
}
</script>

<template>
    <div class="excel-panel">
        <div class="excel-panel__actions">
            <a :href="route('buku.template')" class="excel-link">
                <RetroButton variant="secondary"
                    >⬇ Download Template Excel</RetroButton
                >
            </a>

            <input
                ref="fileInput"
                type="file"
                accept=".xlsx,.xls"
                class="excel-hidden-input"
                @change="onFileChange"
            />
            <RetroButton variant="color" color="cyan" @click="pickFile">
                {{ fileName || "📤 Pilih File Excel..." }}
            </RetroButton>
        </div>

        <p class="excel-panel__hint">
            1) Download template &rarr; 2) Isi data sesuai kolom &rarr; 3)
            Upload file &rarr; 4) Sistem verifikasi otomatis &rarr; 5) Simpan
            jika tidak ada error.
        </p>

        <div v-if="verifying" class="excel-status excel-status--loading">
            Memverifikasi file...
        </div>
        <div v-if="uploadError" class="excel-status excel-status--error">
            {{ uploadError }}
        </div>

        <template v-if="hasPreview && previewRows.length > 0">
            <div
                class="excel-summary"
                :class="isValid ? 'excel-summary--ok' : 'excel-summary--bad'"
            >
                <span v-if="isValid">
                    ✅ Semua {{ previewRows.length }} baris valid. Siap
                    disimpan.
                </span>
                <span v-else>
                    ⚠️ Ditemukan {{ errorRowCount }} baris bermasalah dari
                    {{ previewRows.length }} baris. Perbaiki file lalu upload
                    ulang.
                </span>
            </div>

            <div class="excel-table-wrap">
                <table class="excel-table">
                    <thead>
                        <tr>
                            <th class="excel-col-no">No</th>
                            <th v-for="col in columns" :key="col.key">
                                {{ col.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(row, i) in previewRows" :key="i">
                            <tr
                                :class="{
                                    'excel-row--error': previewErrors[i],
                                }"
                            >
                                <td class="excel-col-no">{{ i + 1 }}</td>
                                <td v-for="col in columns" :key="col.key">
                                    {{ row[col.key] || "-" }}
                                </td>
                            </tr>
                            <tr
                                v-if="previewErrors[i]"
                                class="excel-row--error-msg"
                            >
                                <td :colspan="columns.length + 1">
                                    ⚠️ {{ previewErrors[i].join(" • ") }}
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="excel-panel__footer">
                <RetroButton variant="secondary" @click="cancelPreview"
                    >Batal</RetroButton
                >
                <RetroButton
                    variant="primary"
                    :disabled="!isValid || saving"
                    @click="confirmSave"
                >
                    {{
                        saving
                            ? "Menyimpan..."
                            : `Simpan ${previewRows.length} Buku`
                    }}
                </RetroButton>
            </div>
        </template>
    </div>
</template>

<style scoped>
.excel-panel__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}

.excel-link {
    text-decoration: none;
}

.excel-hidden-input {
    display: none;
}

.excel-panel__hint {
    margin-top: 12px;
    font-size: 13px;
    color: var(--retro-text-secondary);
}

.excel-status {
    margin-top: 16px;
    padding: 10px 16px;
    border-radius: 12px;
    border: 2px solid var(--retro-border);
    font-size: 13px;
    font-weight: 600;
}
.excel-status--loading {
    background: var(--retro-yellow);
}
.excel-status--error {
    background: var(--retro-red);
    color: #fff;
}

.excel-summary {
    margin-top: 20px;
    padding: 12px 20px;
    border-radius: 16px;
    border: 2px solid var(--retro-border);
    font-weight: 700;
    font-size: 14px;
}
.excel-summary--ok {
    background: var(--retro-green);
}
.excel-summary--bad {
    background: var(--retro-red);
    color: #fff;
}

.excel-table-wrap {
    margin-top: 16px;
    overflow-x: auto;
    border: 2px solid var(--retro-border);
    border-radius: 16px;
}

.excel-table {
    width: 100%;
    border-collapse: collapse;
}
.excel-table thead th {
    background: #1a1a1a;
    color: #fff;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: left;
    padding: 10px 12px;
    white-space: nowrap;
}
.excel-col-no {
    width: 44px;
    text-align: center;
}
.excel-table tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
    white-space: nowrap;
}

.excel-row--error td {
    background: #ffecec;
}
.excel-row--error-msg td {
    background: var(--retro-red);
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    padding: 8px 12px;
    white-space: normal;
}

.excel-panel__footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
}
</style>
