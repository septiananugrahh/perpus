<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RetroButton from '@/Components/Retro/RetroButton.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    subkategori: { type: Object, default: () => ({}) },
});

const today = new Date().toISOString().slice(0, 10);

function emptyRow() {
    return {
        tanggalpembukuan_buku: today, kode_subkategori: '', judul_buku: '',
        klasifikasi_buku: '', penerbit_buku: '', pengarang_buku: '',
        keterangan_buku: '', edisi_buku: '', tahunterbit_buku: '',
        kotaterbit_buku: '', isbn_buku: '', nomerpanggil_buku: '',
        eksemplar_buku: '', series_buku: '', sumber_buku: '',
    };
}

const columns = [
    { key: 'tanggalpembukuan_buku', label: 'Tgl Pembukuan', width: '140px' },
    { key: 'kode_subkategori', label: 'Subkategori', width: '150px' },
    { key: 'judul_buku', label: 'Judul Buku', width: '220px' },
    { key: 'klasifikasi_buku', label: 'Klasifikasi', width: '110px' },
    { key: 'penerbit_buku', label: 'Penerbit', width: '140px' },
    { key: 'pengarang_buku', label: 'Pengarang', width: '140px' },
    { key: 'keterangan_buku', label: 'Keterangan', width: '110px' },
    { key: 'edisi_buku', label: 'Edisi', width: '80px' },
    { key: 'tahunterbit_buku', label: 'Th. Terbit', width: '90px' },
    { key: 'kotaterbit_buku', label: 'Kota Terbit', width: '110px' },
    { key: 'isbn_buku', label: 'ISBN', width: '130px' },
    { key: 'nomerpanggil_buku', label: 'No. Panggil', width: '130px' },
    { key: 'eksemplar_buku', label: 'Eksemplar', width: '90px' },
    { key: 'series_buku', label: 'Series', width: '90px' },
    { key: 'sumber_buku', label: 'Sumber', width: '100px' },
];

const rows = ref(Array.from({ length: 5 }, emptyRow));
const processing = ref(false);

function addRow() { rows.value.push(emptyRow()); }
function removeRow(i) { if (rows.value.length > 1) rows.value.splice(i, 1); }
function duplicateRow(i) { rows.value.splice(i + 1, 0, { ...rows.value[i] }); }

function handlePaste(e, rowIndex, colIndex) {
    const text = (e.clipboardData || window.clipboardData).getData('text');
    if (!text.includes('\t') && !text.includes('\n')) return;
    e.preventDefault();
    const grid = text.trim().split('\n').map((r) => r.split('\t'));
    grid.forEach((cells, rOffset) => {
        let targetRow = rows.value[rowIndex + rOffset];
        if (!targetRow) { targetRow = emptyRow(); rows.value.push(targetRow); }
        cells.forEach((cellVal, cOffset) => {
            const col = columns[colIndex + cOffset];
            if (col) targetRow[col.key] = cellVal.trim();
        });
    });
}

function submit() {
    processing.value = true;
    router.post(route('buku.store'), { rows: rows.value }, { onFinish: () => (processing.value = false) });
}
</script>

<template>
    <Head title="Input Buku" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-cabinet retro-title">Input Induk Buku</h2>
        </template>

        <div class="retro-toolbar">
            <RetroButton variant="secondary" @click="addRow">+ Tambah Baris</RetroButton>
            <span class="retro-toolbar__info">{{ rows.length }} baris siap disimpan</span>
            <RetroButton variant="primary" :disabled="processing" @click="submit">
                {{ processing ? 'Menyimpan...' : 'Simpan Semua' }}
            </RetroButton>
        </div>

        <div class="retro-table-wrap">
            <table class="retro-input-table">
                <thead>
                    <tr>
                        <th class="retro-col-no">No</th>
                        <th v-for="col in columns" :key="col.key" :style="{ minWidth: col.width }">{{ col.label }}</th>
                        <th class="retro-col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, rIndex) in rows" :key="rIndex">
                        <td class="retro-col-no">{{ rIndex + 1 }}</td>
                        <td v-for="(col, cIndex) in columns" :key="col.key">
                            <select v-if="col.key === 'kode_subkategori'" v-model="row[col.key]" class="retro-cell retro-cell--select">
                                <option value="">-</option>
                                <option v-for="(label, code) in subkategori" :key="code" :value="code">{{ code }} - {{ label }}</option>
                            </select>
                            <input v-else v-model="row[col.key]" type="text" class="retro-cell" @paste="handlePaste($event, rIndex, cIndex)" />
                        </td>
                        <td class="retro-col-action">
                            <button class="retro-icon-btn" title="Duplikat" @click="duplicateRow(rIndex)">⧉</button>
                            <button class="retro-icon-btn retro-icon-btn--danger" title="Hapus" @click="removeRow(rIndex)">✕</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="retro-toolbar retro-toolbar--bottom">
            <RetroButton variant="secondary" @click="addRow">+ Tambah Baris</RetroButton>
            <RetroButton variant="primary" :disabled="processing" @click="submit">
                {{ processing ? 'Menyimpan...' : 'Simpan Semua' }}
            </RetroButton>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.retro-title { font-size: 32px; font-weight: 800; color: #1a1a1a; }

.retro-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 0;
    margin-bottom: 16px;
}
.retro-toolbar--bottom { margin-top: 16px; }
.retro-toolbar__info { font-size: 13px; color: var(--retro-text-secondary); font-weight: 600; }

.retro-table-wrap {
    overflow-x: auto;
    border: 2px solid var(--retro-border);
    border-radius: 20px;
}

.retro-input-table { border-collapse: collapse; width: 100%; }
.retro-input-table thead th {
    background: #1a1a1a;
    color: #fff;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: left;
    padding: 12px 10px;
    white-space: nowrap;
}
.retro-input-table thead th:first-child { border-top-left-radius: 18px; }
.retro-col-no { width: 44px; text-align: center; background: #fafafa; }
.retro-col-action { width: 90px; text-align: center; }

.retro-input-table tbody tr { border-bottom: 1px solid #eee; }
.retro-input-table tbody td { padding: 0; border-right: 1px solid #f0f0f0; }

.retro-cell {
    width: 100%;
    box-sizing: border-box;
    border: none;
    outline: none;
    background: transparent;
    font-size: 13px;
    padding: 10px 8px;
}
.retro-cell:focus { background: var(--retro-yellow); }
.retro-cell--select { cursor: pointer; }

.retro-icon-btn {
    border: 2px solid var(--retro-border);
    border-radius: 8px;
    background: #fff;
    width: 28px;
    height: 28px;
    margin: 0 2px;
    cursor: pointer;
    font-size: 12px;
}
.retro-icon-btn:hover { background: var(--retro-cyan); }
.retro-icon-btn--danger:hover { background: var(--retro-red); }
</style>
