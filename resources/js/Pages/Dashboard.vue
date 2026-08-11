<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import StatCard from "@/Components/Retro/StatCard.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    stats: { type: Array, default: () => [] },
    statusBuku: { type: Array, default: () => [] },
    bukuSeringDipinjam: { type: Array, default: () => [] },
    totalPeminjamanBulanIni: { type: Number, default: 0 },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-cabinet retro-title">Dashboard</h2>
        </template>

        <div class="retro-stat-grid stagger-fade">
            <StatCard v-for="s in stats" :key="s.label" v-bind="s" />
        </div>

        <div class="retro-row stagger-fade">
            <RetroCard>
                <h3 class="font-cabinet retro-panel-title">
                    Buku Sedang Dipinjam
                </h3>

                <div v-if="statusBuku.length > 0">
                    <div class="retro-progress">
                        <div
                            v-for="s in statusBuku"
                            :key="s.label"
                            class="retro-progress__seg"
                            :style="{
                                width: s.percent + '%',
                                background: `var(--retro-${s.color})`,
                            }"
                        >
                            {{ s.percent }}%
                        </div>
                    </div>

                    <div>
                        <div
                            v-for="s in statusBuku"
                            :key="s.label"
                            class="retro-status-row"
                        >
                            <span class="retro-status-row__label">{{
                                s.label
                            }}</span>
                            <span class="retro-status-row__durasi"
                                >{{ s.jumlah }} buku</span
                            >
                            <span class="retro-status-row__percent"
                                >{{ s.percent }}%</span
                            >
                        </div>
                    </div>
                </div>
                <p v-else class="retro-empty-inline">
                    Tidak ada buku yang sedang dipinjam saat ini.
                </p>
            </RetroCard>

            <RetroCard>
                <h3 class="font-cabinet retro-panel-title">
                    Peminjaman Bulan Ini
                </h3>
                <p class="retro-panel-subtitle">
                    Total transaksi peminjaman bulan berjalan
                </p>
                <div class="retro-big-number">
                    {{ totalPeminjamanBulanIni }}
                    <span class="retro-big-number__label">peminjaman</span>
                </div>
            </RetroCard>
        </div>

        <RetroCard class="retro-mt">
            <h3 class="font-cabinet retro-panel-title retro-panel-title--bleed">
                Buku Paling Sering Dipinjam
            </h3>
            <div v-if="bukuSeringDipinjam.length > 0" class="retro-table-bleed">
                <table class="retro-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Buku</th>
                            <th>Jumlah Pinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="b in bukuSeringDipinjam" :key="b.no">
                            <td>{{ b.no }}</td>
                            <td>{{ b.nama }}</td>
                            <td>{{ b.jumlah }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else class="retro-empty-inline">
                Belum ada riwayat peminjaman.
            </p>
        </RetroCard>
    </AuthenticatedLayout>
</template>

<style scoped>
.retro-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1a1a;
}

.retro-stat-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-bottom: 24px;
}
@media (min-width: 640px) {
    .retro-stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .retro-stat-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.retro-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}
@media (min-width: 1024px) {
    .retro-row {
        grid-template-columns: 1fr 1fr;
    }
}

.retro-mt {
    margin-top: 24px;
}

.retro-panel-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 4px;
}
.retro-panel-title--bleed {
    margin-bottom: 16px;
}

.retro-table-bleed {
    margin: 0 -24px -24px;
    overflow-x: auto;
}
.retro-panel-subtitle {
    font-size: 13px;
    color: var(--retro-text-secondary);
    margin-bottom: 16px;
}

.retro-empty-inline {
    font-size: 13px;
    color: var(--retro-muted);
    padding: 20px 0;
}

.retro-progress {
    display: flex;
    height: 40px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid var(--retro-border);
    margin: 16px 0;
    font-size: 12px;
    font-weight: 700;
    color: #1a1a1a;
}
.retro-progress__seg {
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 2px solid var(--retro-border);
}
.retro-progress__seg:last-child {
    border-right: none;
}

.retro-status-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
.retro-status-row:last-child {
    border-bottom: none;
}
.retro-status-row__label {
    font-weight: 600;
}
.retro-status-row__durasi {
    color: var(--retro-text-secondary);
}
.retro-status-row__percent {
    font-weight: 700;
}

.retro-big-number {
    font-size: 48px;
    font-weight: 800;
    color: #1a1a1a;
    margin-top: 8px;
}
.retro-big-number__label {
    font-size: 16px;
    font-weight: 500;
    color: var(--retro-text-secondary);
    margin-left: 8px;
}

.retro-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}
.retro-table thead th {
    text-align: left;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--retro-text-secondary);
    padding: 14px 24px;
    border-bottom: 2px solid var(--retro-border);
    background: #fafafa;
}
.retro-table tbody td {
    padding: 14px 24px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
.retro-table tbody tr:last-child td {
    border-bottom: none;
}
</style>
