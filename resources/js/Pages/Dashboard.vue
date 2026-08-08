<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/Retro/StatCard.vue';
import RetroCard from '@/Components/Retro/RetroCard.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const stats = ref([
    { label: 'Buku Dimiliki', value: 1112, delta: '18.2%', deltaText: 'dari minggu lalu', tone: 'up', color: 'pink', icon: '📖' },
    { label: 'Dipinjam Hari Ini', value: 8, delta: '8.7%', deltaText: 'dari kemarin', tone: 'down', color: 'cyan', icon: '🔄' },
    { label: 'Asatidz', value: 45, delta: '4.3%', deltaText: 'dari tahun lalu', tone: 'up', color: 'yellow', icon: '🎓' },
    { label: 'Santri', value: 268, delta: '2.5%', deltaText: 'dari tahun lalu', tone: 'down', color: 'purple', icon: '👥' },
]);

const statusBuku = ref([
    { label: 'Dipinjam', durasi: '2 hari', percent: 39.7, color: 'pink' },
    { label: 'Terlambat', durasi: '3 hari', percent: 28.3, color: 'red' },
    { label: 'Diperpanjang', durasi: '1 hari', percent: 17.4, color: 'cyan' },
    { label: 'Menunggu Konfirmasi', durasi: '5 hari', percent: 14.6, color: 'yellow' },
]);

const bukuSeringDipinjam = ref([
    { no: 1, nama: 'Tata Surya', jumlah: 20 },
    { no: 2, nama: 'Fiqih Ibadah', jumlah: 17 },
    { no: 3, nama: 'Sejarah Nabi', jumlah: 15 },
    { no: 4, nama: 'Bahasa Arab Dasar', jumlah: 12 },
]);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-cabinet retro-title">Dashboard</h2>
        </template>

        <div class="retro-stat-grid">
            <StatCard v-for="s in stats" :key="s.label" v-bind="s" />
        </div>

        <div class="retro-row">
            <RetroCard>
                <h3 class="font-cabinet retro-panel-title">Buku Belum Dikembalikan</h3>

                <div class="retro-progress">
                    <div
                        v-for="s in statusBuku"
                        :key="s.label"
                        class="retro-progress__seg"
                        :style="{ width: s.percent + '%', background: `var(--retro-${s.color})` }"
                    >
                        {{ s.percent }}%
                    </div>
                </div>

                <div>
                    <div v-for="s in statusBuku" :key="s.label" class="retro-status-row">
                        <span class="retro-status-row__label">{{ s.label }}</span>
                        <span class="retro-status-row__durasi">{{ s.durasi }}</span>
                        <span class="retro-status-row__percent">{{ s.percent }}%</span>
                    </div>
                </div>
            </RetroCard>

            <RetroCard>
                <h3 class="font-cabinet retro-panel-title">Grafik Peminjaman Harian</h3>
                <p class="retro-panel-subtitle">Total bulan ini: 238</p>
                <div class="retro-chart-placeholder">[ Chart Peminjaman Harian ]</div>
            </RetroCard>
        </div>

        <RetroCard class="retro-mt">
            <h3 class="font-cabinet retro-panel-title">Buku Paling Sering Dipinjam</h3>
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
@media (min-width: 640px) { .retro-stat-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .retro-stat-grid { grid-template-columns: repeat(4, 1fr); } }

.retro-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}
@media (min-width: 1024px) { .retro-row { grid-template-columns: 1fr 1fr; } }

.retro-mt { margin-top: 24px; }

.retro-panel-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 4px;
}
.retro-panel-subtitle {
    font-size: 13px;
    color: var(--retro-text-secondary);
    margin-bottom: 16px;
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
.retro-progress__seg:last-child { border-right: none; }

.retro-status-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
.retro-status-row:last-child { border-bottom: none; }
.retro-status-row__label { font-weight: 600; }
.retro-status-row__durasi { color: var(--retro-text-secondary); }
.retro-status-row__percent { font-weight: 700; }

.retro-chart-placeholder {
    height: 220px;
    border: 2px dashed var(--retro-border);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--retro-muted);
    font-size: 14px;
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
    padding: 8px 12px;
    border-bottom: 2px solid var(--retro-border);
}
.retro-table tbody td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
</style>
