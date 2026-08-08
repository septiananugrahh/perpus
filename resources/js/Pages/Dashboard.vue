<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const stats = ref([
    {
        label: "BUKU DIMILIKI",
        value: 1112,
        delta: "+18.2%",
        deltaText: "DARI MINGGU LALU",
        tone: "up",
    },
    {
        label: "DIPINJAM HARI INI",
        value: 8,
        delta: "-8.7%",
        deltaText: "DARI KEMARIN",
        tone: "down",
    },
    {
        label: "ASATIDZ",
        value: 45,
        delta: "+4.3%",
        deltaText: "DARI TAHUN LALU",
        tone: "up",
    },
    {
        label: "SANTRI",
        value: 268,
        delta: "-2.5%",
        deltaText: "DARI TAHUN LALU",
        tone: "down",
    },
]);

const statusBuku = ref([
    { label: "DIPINJAM", durasi: "2 HARI", percent: 39.7 },
    { label: "TERLAMBAT", durasi: "3 HARI", percent: 28.3 },
    { label: "DIPERPANJANG", durasi: "1 HARI", percent: 17.4 },
    { label: "MENUNGGU KONFIRMASI", durasi: "5 HARI", percent: 14.6 },
]);

const bukuSeringDipinjam = ref([
    { no: 1, nama: "TATA SURYA", jumlah: 20 },
    { no: 2, nama: "FIQIH IBADAH", jumlah: 17 },
    { no: 3, nama: "SEJARAH NABI", jumlah: 15 },
    { no: 4, nama: "BAHASA ARAB DASAR", jumlah: 12 },
]);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="rb-h2">DASHBOARD</h2>
        </template>

        <!-- Stat cards -->
        <div class="rb-stat-grid">
            <div v-for="s in stats" :key="s.label" class="rb-stat-card">
                <div class="rb-stat-card__value">{{ s.value }}</div>
                <div class="rb-stat-card__label">{{ s.label }}</div>
                <div class="rb-stat-card__delta">
                    <span
                        :class="s.tone === 'up' ? 'rb-tone-up' : 'rb-tone-down'"
                        >{{ s.delta }}</span
                    >
                    <span class="rb-stat-card__delta-text">
                        {{ s.deltaText }}</span
                    >
                </div>
            </div>
        </div>

        <div class="rb-row">
            <!-- Status buku -->
            <div class="rb-panel">
                <div class="rb-panel__title">BUKU BELUM DIKEMBALIKAN</div>

                <div class="rb-progress">
                    <div
                        v-for="s in statusBuku"
                        :key="s.label"
                        class="rb-progress__seg"
                        :style="{ width: s.percent + '%' }"
                    >
                        {{ s.percent }}%
                    </div>
                </div>

                <div class="rb-status-list">
                    <div
                        v-for="s in statusBuku"
                        :key="s.label"
                        class="rb-status-list__row"
                    >
                        <div class="rb-status-list__label">{{ s.label }}</div>
                        <div class="rb-status-list__durasi">{{ s.durasi }}</div>
                        <div class="rb-status-list__percent">
                            {{ s.percent }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik placeholder -->
            <div class="rb-panel">
                <div class="rb-panel__title">GRAFIK PEMINJAMAN HARIAN</div>
                <div class="rb-panel__subtitle">TOTAL BULAN INI: 238</div>
                <div class="rb-chart-placeholder">
                    [ CHART PEMINJAMAN HARIAN ]
                </div>
            </div>
        </div>

        <!-- Buku paling sering dipinjam -->
        <div class="rb-panel rb-panel--full">
            <div class="rb-panel__title">BUKU PALING SERING DIPINJAM</div>
            <table class="rb-table">
                <thead>
                    <tr>
                        <th class="rb-table__no">NO.</th>
                        <th>NAMA BUKU</th>
                        <th>JUMLAH PINJAM</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="b in bukuSeringDipinjam" :key="b.no">
                        <td class="rb-table__no">{{ b.no }}</td>
                        <td>{{ b.nama }}</td>
                        <td>{{ b.jumlah }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.rb-h2 {
    font-family: "Archivo Black", sans-serif;
    font-size: 32px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #000;
}

.rb-stat-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 0;
    border: 3px solid #000;
    margin-bottom: 24px;
}
@media (min-width: 640px) {
    .rb-stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .rb-stat-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.rb-stat-card {
    padding: 24px;
    border-right: 1px solid #000;
    border-bottom: 1px solid #000;
}
.rb-stat-card:last-child {
    border-right: none;
}

.rb-stat-card__value {
    font-family: "Archivo Black", sans-serif;
    font-size: 40px;
    color: #000;
    line-height: 1;
}
.rb-stat-card__label {
    font-family: "Work Sans", sans-serif;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 1px;
    color: #000;
    margin-top: 8px;
}
.rb-stat-card__delta {
    margin-top: 8px;
    font-family: "Space Mono", monospace;
    font-size: 12px;
}
.rb-tone-up {
    color: #008000;
    font-weight: bold;
}
.rb-tone-down {
    color: #ff0000;
    font-weight: bold;
}
.rb-stat-card__delta-text {
    color: #555;
}

.rb-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-bottom: 24px;
}
@media (min-width: 1024px) {
    .rb-row {
        grid-template-columns: 1fr 1fr;
    }
}

.rb-panel {
    border: 3px solid #000;
    padding: 24px;
}
.rb-panel--full {
    margin-bottom: 24px;
}

.rb-panel__title {
    font-family: "Archivo Black", sans-serif;
    font-size: 18px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #000;
    margin-bottom: 4px;
}
.rb-panel__subtitle {
    font-family: "Space Mono", monospace;
    font-size: 12px;
    color: #555;
    margin-bottom: 16px;
}

.rb-progress {
    display: flex;
    height: 40px;
    border: 2px solid #000;
    margin: 16px 0;
    font-family: "Space Mono", monospace;
    font-size: 12px;
    color: #fff;
}
.rb-progress__seg {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #000;
    border-right: 2px solid #fff;
}
.rb-progress__seg:nth-child(2) {
    background: #0000ff;
}
.rb-progress__seg:nth-child(3) {
    background: #555;
}
.rb-progress__seg:last-child {
    border-right: none;
}

.rb-status-list__row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #000;
    font-family: "Work Sans", sans-serif;
    font-size: 13px;
    letter-spacing: 0.5px;
}
.rb-status-list__row:last-child {
    border-bottom: none;
}
.rb-status-list__label {
    font-weight: 600;
}
.rb-status-list__durasi {
    color: #555;
    font-family: "Space Mono", monospace;
}
.rb-status-list__percent {
    font-weight: 700;
}

.rb-chart-placeholder {
    height: 220px;
    border: 2px dashed #000;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: "Space Mono", monospace;
    font-size: 13px;
    color: #555;
}

.rb-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}
.rb-table thead th {
    background: #000;
    color: #fff;
    font-family: "Archivo Black", sans-serif;
    font-size: 12px;
    letter-spacing: 1px;
    text-align: left;
    padding: 10px 12px;
}
.rb-table__no {
    width: 60px;
    text-align: center;
}
.rb-table tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid #000;
    font-family: "Work Sans", sans-serif;
    font-size: 14px;
}
</style>
