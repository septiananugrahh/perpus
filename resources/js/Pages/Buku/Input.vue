<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    subkategori: { type: Object, default: () => ({}) },
});

const today = new Date().toISOString().slice(0, 10);

function emptyRow() {
    return {
        tanggalpembukuan_buku: today,
        kode_subkategori: "",
        judul_buku: "",
        klasifikasi_buku: "",
        penerbit_buku: "",
        pengarang_buku: "",
        keterangan_buku: "",
        edisi_buku: "",
        tahunterbit_buku: "",
        kotaterbit_buku: "",
        isbn_buku: "",
        nomerpanggil_buku: "",
        eksemplar_buku: "",
        series_buku: "",
        sumber_buku: "",
    };
}

const columns = [
    { key: "tanggalpembukuan_buku", label: "Tgl Pembukuan", width: "140px" },
    { key: "kode_subkategori", label: "Subkategori", width: "130px" },
    { key: "judul_buku", label: "Judul Buku", width: "220px" },
    { key: "klasifikasi_buku", label: "Klasifikasi", width: "110px" },
    { key: "penerbit_buku", label: "Penerbit", width: "140px" },
    { key: "pengarang_buku", label: "Pengarang", width: "140px" },
    { key: "keterangan_buku", label: "Keterangan", width: "110px" },
    { key: "edisi_buku", label: "Edisi", width: "80px" },
    { key: "tahunterbit_buku", label: "Th. Terbit", width: "90px" },
    { key: "kotaterbit_buku", label: "Kota Terbit", width: "110px" },
    { key: "isbn_buku", label: "ISBN", width: "130px" },
    { key: "nomerpanggil_buku", label: "No. Panggil", width: "130px" },
    { key: "eksemplar_buku", label: "Eksemplar", width: "90px" },
    { key: "series_buku", label: "Series", width: "90px" },
    { key: "sumber_buku", label: "Sumber", width: "100px" },
];

const rows = ref(Array.from({ length: 5 }, emptyRow));
const processing = ref(false);

function addRow() {
    rows.value.push(emptyRow());
}

function removeRow(index) {
    if (rows.value.length === 1) return;
    rows.value.splice(index, 1);
}

function duplicateRow(index) {
    rows.value.splice(index + 1, 0, { ...rows.value[index] });
}

// Paste dari Excel: tab = kolom baru, newline = baris baru
function handlePaste(e, rowIndex, colIndex) {
    const text = (e.clipboardData || window.clipboardData).getData("text");
    if (!text.includes("\t") && !text.includes("\n")) return; // biarkan paste normal

    e.preventDefault();
    const grid = text
        .trim()
        .split("\n")
        .map((r) => r.split("\t"));

    grid.forEach((cells, rOffset) => {
        let targetRow = rows.value[rowIndex + rOffset];
        if (!targetRow) {
            targetRow = emptyRow();
            rows.value.push(targetRow);
        }
        cells.forEach((cellVal, cOffset) => {
            const col = columns[colIndex + cOffset];
            if (col) targetRow[col.key] = cellVal.trim();
        });
    });
}

function submit() {
    processing.value = true;
    router.post(
        route("buku.store"),
        { rows: rows.value },
        {
            onFinish: () => (processing.value = false),
        },
    );
}
</script>

<template>
    <Head title="Input Buku" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="rb-h2">INPUT INDUK BUKU</h2>
        </template>

        <div class="rb-toolbar">
            <button class="rb-btn rb-btn--secondary" @click="addRow">
                + TAMBAH BARIS
            </button>
            <div class="rb-toolbar__info">
                {{ rows.length }} BARIS SIAP DISIMPAN
            </div>
            <button
                class="rb-btn rb-btn--primary"
                :disabled="processing"
                @click="submit"
            >
                {{ processing ? "MENYIMPAN..." : "SIMPAN SEMUA" }}
            </button>
        </div>

        <div class="rb-table-wrap">
            <table class="rb-table">
                <thead>
                    <tr>
                        <th class="rb-table__no">NO</th>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            :style="{ minWidth: col.width }"
                        >
                            {{ col.label }}
                        </th>
                        <th class="rb-table__action">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, rIndex) in rows" :key="rIndex">
                        <td class="rb-table__no">{{ rIndex + 1 }}</td>

                        <td v-for="(col, cIndex) in columns" :key="col.key">
                            <select
                                v-if="col.key === 'kode_subkategori'"
                                v-model="row[col.key]"
                                class="rb-cell rb-cell--select"
                            >
                                <option value="">-</option>
                                <option
                                    v-for="(label, code) in subkategori"
                                    :key="code"
                                    :value="code"
                                >
                                    {{ code }} - {{ label }}
                                </option>
                            </select>
                            <input
                                v-else
                                v-model="row[col.key]"
                                type="text"
                                class="rb-cell"
                                @paste="handlePaste($event, rIndex, cIndex)"
                            />
                        </td>

                        <td class="rb-table__action">
                            <button
                                class="rb-icon-btn"
                                title="Duplikat"
                                @click="duplicateRow(rIndex)"
                            >
                                ⧉
                            </button>
                            <button
                                class="rb-icon-btn rb-icon-btn--danger"
                                title="Hapus"
                                @click="removeRow(rIndex)"
                            >
                                ✕
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="rb-toolbar rb-toolbar--bottom">
            <button class="rb-btn rb-btn--secondary" @click="addRow">
                + TAMBAH BARIS
            </button>
            <button
                class="rb-btn rb-btn--primary"
                :disabled="processing"
                @click="submit"
            >
                {{ processing ? "MENYIMPAN..." : "SIMPAN SEMUA" }}
            </button>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* ==== RawBlock Design System ==== */
.rb-h2 {
    font-family: "Archivo Black", sans-serif;
    font-size: 32px;
    line-height: 1.1;
    color: #000;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.rb-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 3px solid #000;
    margin-bottom: 16px;
}

.rb-toolbar--bottom {
    border-bottom: none;
    border-top: 3px solid #000;
    margin-top: 0;
}

.rb-toolbar__info {
    font-family: "Space Mono", monospace;
    font-size: 13px;
    letter-spacing: 1px;
    color: #000;
}

.rb-btn {
    font-family: "Work Sans", sans-serif;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 10px 24px;
    border: 3px solid #000;
    border-radius: 0;
    cursor: pointer;
    transition: none;
}

.rb-btn--primary {
    background: #000;
    color: #fff;
}
.rb-btn--primary:hover:not(:disabled) {
    background: #fff;
    color: #000;
}
.rb-btn--primary:active:not(:disabled) {
    background: #000;
    color: #fff;
    border-width: 5px;
}
.rb-btn--primary:disabled {
    background: #f5f5f5;
    color: #999;
    border-color: #ccc;
    cursor: not-allowed;
}

.rb-btn--secondary {
    background: #fff;
    color: #000;
}
.rb-btn--secondary:hover {
    background: #000;
    color: #fff;
}

.rb-table-wrap {
    overflow-x: auto;
    border: 3px solid #000;
}

.rb-table {
    border-collapse: collapse;
    width: 100%;
}

.rb-table thead th {
    background: #000;
    color: #fff;
    font-family: "Archivo Black", sans-serif;
    font-size: 12px;
    letter-spacing: 1px;
    text-transform: uppercase;
    text-align: left;
    padding: 10px 8px;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 1;
}

.rb-table__no {
    width: 40px;
    text-align: center;
    font-family: "Space Mono", monospace;
    background: #f0f0f0;
}

.rb-table__action {
    width: 90px;
    text-align: center;
}

.rb-table tbody tr {
    border-bottom: 1px solid #000;
}

.rb-table tbody td {
    padding: 0;
    border-right: 1px solid #ccc;
}

.rb-cell {
    width: 100%;
    box-sizing: border-box;
    border: none;
    outline: none;
    background: transparent;
    font-family: "Space Mono", monospace;
    font-size: 13px;
    padding: 10px 8px;
    border-radius: 0;
}

.rb-cell:focus {
    background: #f0f0f0;
    box-shadow: inset 0 0 0 2px #000;
}

.rb-cell--select {
    cursor: pointer;
}

.rb-icon-btn {
    border: 2px solid #000;
    background: #fff;
    width: 28px;
    height: 28px;
    margin: 0 2px;
    cursor: pointer;
    font-size: 13px;
    line-height: 1;
}
.rb-icon-btn:hover {
    background: #000;
    color: #fff;
}
.rb-icon-btn--danger:hover {
    background: #ff0000;
    color: #fff;
    border-color: #ff0000;
}
</style>
