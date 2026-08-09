<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import RetroButton from "@/Components/Retro/RetroButton.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    santri: { type: Object, required: true },
    riwayatPeminjaman: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="`Detail Santri - ${santri.nama}`" />

    <AuthenticatedLayout>
        <template #header>
            <Link :href="route('santri.index')" class="retro-back"
                >← Kembali ke Santri List</Link
            >
            <h2 class="font-cabinet retro-title">{{ santri.nama }}</h2>
        </template>

        <div class="retro-row">
            <RetroCard>
                <h3 class="font-cabinet retro-panel-title">Data Santri</h3>
                <div class="retro-info-grid">
                    <div class="retro-info-field">
                        <span class="retro-info-label">NIS</span>
                        <span class="retro-info-value">{{ santri.nis }}</span>
                    </div>
                    <div class="retro-info-field">
                        <span class="retro-info-label">NISN</span>
                        <span class="retro-info-value">{{
                            santri.nisn || "-"
                        }}</span>
                    </div>
                    <div class="retro-info-field">
                        <span class="retro-info-label">Kelas</span>
                        <span class="retro-info-value"
                            >{{ santri.kelas_nama }} (Tingkat
                            {{ santri.kelas_tingkat }})</span
                        >
                    </div>
                    <div class="retro-info-field">
                        <span class="retro-info-label">Wali Kelas</span>
                        <span class="retro-info-value">{{
                            santri.guru_nama || "-"
                        }}</span>
                    </div>
                    <div class="retro-info-field">
                        <span class="retro-info-label">Nama Orang Tua</span>
                        <span class="retro-info-value">{{
                            santri.nama_ortu || "-"
                        }}</span>
                    </div>
                    <div class="retro-info-field">
                        <span class="retro-info-label">Jenis Kelamin</span>
                        <span class="retro-info-value">{{
                            santri.jenis_kelamin === "L"
                                ? "Laki-laki"
                                : "Perempuan"
                        }}</span>
                    </div>
                    <div class="retro-info-field">
                        <span class="retro-info-label">Telepon</span>
                        <span class="retro-info-value">{{
                            santri.telp || "-"
                        }}</span>
                    </div>
                    <div class="retro-info-field retro-info-field--full">
                        <span class="retro-info-label">Alamat</span>
                        <span class="retro-info-value">{{
                            santri.alamat || "-"
                        }}</span>
                    </div>
                </div>
            </RetroCard>

            <RetroCard>
                <h3 class="font-cabinet retro-panel-title">
                    Ringkasan Peminjaman
                </h3>
                <div class="retro-summary-row">
                    <div class="retro-summary-box">
                        <span class="retro-summary-value">{{
                            riwayatPeminjaman.length
                        }}</span>
                        <span class="retro-summary-label">Total Riwayat</span>
                    </div>
                    <div class="retro-summary-box retro-summary-box--active">
                        <span class="retro-summary-value">{{
                            riwayatPeminjaman.filter((r) => !r.tgl_kembali)
                                .length
                        }}</span>
                        <span class="retro-summary-label"
                            >Belum Dikembalikan</span
                        >
                    </div>
                </div>
            </RetroCard>
        </div>

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

.retro-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
@media (min-width: 1024px) {
    .retro-row {
        grid-template-columns: 1.4fr 1fr;
    }
}

.retro-panel-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 16px;
}
.retro-panel-title--bleed {
    margin-bottom: 16px;
}

.retro-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.retro-info-field {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.retro-info-field--full {
    grid-column: span 2;
}
.retro-info-label {
    font-size: 11px;
    text-transform: uppercase;
    color: var(--retro-muted);
    font-weight: 600;
}
.retro-info-value {
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
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
    margin-top: 0;
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

.retro-empty {
    text-align: center;
    padding: 32px;
    color: var(--retro-muted);
}
</style>
