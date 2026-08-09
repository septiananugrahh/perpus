<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    guru: { type: Object, required: true },
    riwayatPeminjaman: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="`Detail Guru - ${guru.guru_nama}`" />

    <AuthenticatedLayout>
        <template #header>
            <Link :href="route('guru.index')" class="retro-back"
                >← Kembali ke Guru List</Link
            >
            <h2 class="font-cabinet retro-title">{{ guru.guru_nama }}</h2>
            <p class="retro-subtitle">No. Guru: {{ guru.guru_no }}</p>
        </template>

        <RetroCard>
            <div class="retro-summary-row">
                <div class="retro-summary-box">
                    <span class="retro-summary-value">{{
                        riwayatPeminjaman.length
                    }}</span>
                    <span class="retro-summary-label">Total Riwayat</span>
                </div>
                <div class="retro-summary-box retro-summary-box--active">
                    <span class="retro-summary-value">{{
                        riwayatPeminjaman.filter((r) => !r.tgl_kembali).length
                    }}</span>
                    <span class="retro-summary-label">Belum Dikembalikan</span>
                </div>
            </div>
        </RetroCard>

        <RetroCard class="retro-mt">
            <h3 class="font-cabinet retro-panel-title retro-panel-title--bleed">
                Riwayat Peminjaman Buku
            </h3>
            <div class="retro-table-bleed">
                <table class="retro-table">
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="r in riwayatPeminjaman"
                            :key="r.id_peminjaman"
                        >
                            <td>
                                {{
                                    r.buku?.judul_buku ??
                                    `#${r.id_barang} (buku tidak ditemukan)`
                                }}
                                <span
                                    v-if="r.buku?.deleted_at"
                                    class="retro-deleted-tag"
                                    >Buku Dihapus</span
                                >
                            </td>
                            <td>{{ r.tgl_pinjam }}</td>
                            <td>{{ r.tgl_kembali || "-" }}</td>
                            <td>
                                <span
                                    class="retro-status-badge"
                                    :class="
                                        r.tgl_kembali
                                            ? 'retro-status-badge--done'
                                            : 'retro-status-badge--active'
                                    "
                                >
                                    {{
                                        r.tgl_kembali
                                            ? "Sudah Kembali"
                                            : "Dipinjam"
                                    }}
                                </span>
                            </td>
                            <td>{{ r.keterangan_peminjaman || "-" }}</td>
                        </tr>
                        <tr v-if="riwayatPeminjaman.length === 0">
                            <td colspan="5" class="retro-empty">
                                Belum ada riwayat peminjaman
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </RetroCard>
    </AuthenticatedLayout>
</template>

<style scoped>
.retro-back {
    font-size: 13px;
    font-weight: 600;
    color: var(--retro-text-secondary);
    text-decoration: none;
}
.retro-back:hover {
    color: #1a1a1a;
}
.retro-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1a1a;
    margin-top: 8px;
}
.retro-subtitle {
    font-size: 13px;
    color: var(--retro-text-secondary);
    margin-top: 4px;
}

.retro-summary-row {
    display: flex;
    gap: 16px;
}
.retro-summary-box {
    flex: 1;
    border: 2px solid var(--retro-border);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
}
.retro-summary-box--active {
    background: var(--retro-pink);
}
.retro-summary-value {
    display: block;
    font-size: 32px;
    font-weight: 800;
}
.retro-summary-label {
    font-size: 12px;
    color: var(--retro-text-secondary);
    font-weight: 600;
}

.retro-mt {
    margin-top: 20px;
}
.retro-panel-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 16px;
}
.retro-table-bleed {
    margin: 0 -24px -24px;
    overflow-x: auto;
}

.retro-table {
    width: 100%;
    border-collapse: collapse;
}
.retro-table thead th {
    text-align: left;
    font-size: 12px;
    text-transform: uppercase;
    color: var(--retro-text-secondary);
    padding: 14px 24px;
    border-bottom: 2px solid var(--retro-border);
    background: #fafafa;
    white-space: nowrap;
}
.retro-table tbody td {
    padding: 14px 24px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
.retro-table tbody tr:last-child td {
    border-bottom: none;
}

.retro-status-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
}
.retro-status-badge--active {
    background: var(--retro-red);
    color: #fff;
}
.retro-status-badge--done {
    background: var(--retro-green);
}

.retro-deleted-tag {
    display: inline-block;
    margin-left: 8px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    background: #f5f5f5;
    color: var(--retro-muted);
    border: 1px solid #ddd;
}

.retro-empty {
    text-align: center;
    padding: 32px;
    color: var(--retro-muted);
}
</style>
