<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import axios from "axios";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import RetroButton from "@/Components/Retro/RetroButton.vue";

const props = defineProps({
    loans: { type: Object, default: () => ({ data: [], next_page_url: null }) },
    tanggal: { type: String, default: "" },
    tanggal_mulai: { type: String, default: "" },
    tanggal_selesai: { type: String, default: "" },
});

const list = ref([...props.loans.data]);
const nextPageUrl = ref(props.loans.next_page_url);
const loadingMore = ref(false);
const initialLoading = ref(false);

// Filter tanggal — dua mode: "semua" (tanpa filter) atau "rentang"
const modeAll = ref(!props.tanggal_mulai && !props.tanggal_selesai);
const dari = ref(props.tanggal_mulai || "");
const sampai = ref(props.tanggal_selesai || "");

function terapkanFilter() {
    initialLoading.value = true;
    const params = {};
    if (!modeAll.value) {
        if (dari.value) params.tanggal_mulai = dari.value;
        if (sampai.value) params.tanggal_selesai = sampai.value;
    }
    router.get(route("peminjaman.riwayat"), params, {
        preserveState: false,
        replace: true,
    });
}

function toggleMode() {
    modeAll.value = !modeAll.value;
    if (modeAll.value) {
        dari.value = "";
        sampai.value = "";
    } else {
        // Default ke hari ini kalau switch ke mode filter
        const t = new Date().toISOString().slice(0, 10);
        dari.value = t;
        sampai.value = t;
    }
    terapkanFilter();
}

function setHariIni() {
    const t = new Date().toISOString().slice(0, 10);
    dari.value = t;
    sampai.value = t;
    modeAll.value = false;
    terapkanFilter();
}

function setMingguIni() {
    modeAll.value = false;
    const now = new Date();
    const day = now.getDay();
    const monday = new Date(now);
    monday.setDate(now.getDate() - ((day + 6) % 7));
    const sunday = new Date(monday);
    sunday.setDate(monday.getDate() + 6);
    dari.value = monday.toISOString().slice(0, 10);
    sampai.value = sunday.toISOString().slice(0, 10);
    terapkanFilter();
}

function setBulanIni() {
    modeAll.value = false;
    const now = new Date();
    dari.value = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0, 10);
    sampai.value = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().slice(0, 10);
    terapkanFilter();
}

const hasActiveFilter = computed(
    () => !modeAll.value && (dari.value || sampai.value)
);

const filterLabel = computed(() => {
    if (modeAll.value) return "Semua data";
    if (dari.value && sampai.value && dari.value === sampai.value) {
        return dari.value;
    }
    if (dari.value && sampai.value) return `${dari.value} s.d. ${sampai.value}`;
    if (dari.value) return `Dari ${dari.value}`;
    return `Sampai ${sampai.value}`;
});

// Infinite scroll
function handleScroll() {
    if (loadingMore.value || !nextPageUrl.value) return;
    const scrollBottom =
        window.innerHeight + window.scrollY >=
        document.documentElement.scrollHeight - 200;
    if (!scrollBottom) return;
    loadMore();
}

async function loadMore() {
    if (!nextPageUrl.value || loadingMore.value) return;
    loadingMore.value = true;
    try {
        // paksa same-origin: next_page_url bisa bawa host lain (perpus.test vs localhost)
        const url = new URL(nextPageUrl.value, window.location.origin);
        const resp = await axios.get(url.pathname + url.search);
        const rows = Array.isArray(resp.data?.data) ? resp.data.data : [];
        list.value = [...list.value, ...rows];
        nextPageUrl.value = resp.data?.next_page_url ?? null;
        if (rows.length === 0) nextPageUrl.value = null; // hentikan loop saat payload tak terduga
    } catch (e) {
        console.error("Gagal load data riwayat:", e);
        nextPageUrl.value = null; // jangan retry tanpa henti
    } finally {
        loadingMore.value = false;
    }
}

onMounted(() => window.addEventListener("scroll", handleScroll));
onUnmounted(() => window.removeEventListener("scroll", handleScroll));

watch(
    () => [props.loans, props.tanggal_mulai, props.tanggal_selesai],
    ([newLoans, newMulai, newSelesai]) => {
        list.value = [...newLoans.data];
        nextPageUrl.value = newLoans.next_page_url;
        dari.value = newMulai || "";
        sampai.value = newSelesai || "";
        modeAll.value = !newMulai && !newSelesai;
        initialLoading.value = false;
    }
);

function statusBadge(loan) {
    const kembali = loan.tgl_kembali;
    if (kembali) return { label: "Sudah kembali", class: "badge--ok" };
    return { label: "Sedang dipinjam", class: "badge--cyan" };
}

function formatJam(val) {
    if (!val) return "-";
    return val;
}
</script>

<template>
    <Head title="Riwayat Peminjaman" />

    <AuthenticatedLayout>
        <template #header>
            <div class="retro-header-row">
                <div>
                    <h2 class="font-cabinet retro-title">Riwayat Peminjaman</h2>
                    <p class="retro-subtitle">{{ list.length }} peminjaman — {{ filterLabel }}</p>
                </div>
                <div class="retro-header-actions">
                    <Link :href="route('peminjaman.index')">
                        <RetroButton variant="secondary">
                            ← Kembali
                        </RetroButton>
                    </Link>
                </div>
            </div>
        </template>

        <!-- Filter tanggal -->
        <RetroCard padding="16px 24px" rounded="20px" class="stagger-fade filter-card">
            <div class="filter-row">
                <div class="filter-toggle">
                    <label class="toggle-label">
                        <input type="checkbox" :checked="modeAll" @change="toggleMode" />
                        <span>Tampilkan semua data</span>
                    </label>
                </div>

                <div v-if="!modeAll" class="range-inputs">
                    <div class="range-group">
                        <label>Dari:</label>
                        <input type="date" v-model="dari" class="date-input" />
                    </div>
                    <div class="range-group">
                        <label>Sampai:</label>
                        <input type="date" v-model="sampai" class="date-input" />
                    </div>
                    <RetroButton variant="primary" @click="terapkanFilter">Terapkan</RetroButton>
                </div>

                <div v-if="!modeAll" class="quick-filters">
                    <RetroButton variant="secondary" size="sm" @click="setHariIni">Hari Ini</RetroButton>
                    <RetroButton variant="secondary" size="sm" @click="setMingguIni">Minggu Ini</RetroButton>
                    <RetroButton variant="secondary" size="sm" @click="setBulanIni">Bulan Ini</RetroButton>
                </div>
            </div>
        </RetroCard>

        <!-- Table -->
        <RetroCard padding="0" rounded="24px" class="stagger-fade">
            <!-- Skeleton loading (initial) -->
            <div v-if="initialLoading">
                <table class="retro-table">
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Peminjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="i in 5" :key="i" class="skeleton-row">
                            <td>
                                <div class="skeleton-box" style="width: 80%; height: 16px;"></div>
                            </td>
                            <td>
                                <div class="skeleton-box" style="width: 60%; height: 16px;"></div>
                                <div class="skeleton-box" style="width: 40%; height: 10px; margin-top: 4px;"></div>
                            </td>
                            <td>
                                <div class="skeleton-box" style="width: 70%; height: 14px;"></div>
                            </td>
                            <td>
                                <div class="skeleton-box skeleton-badge" style="width: 80px; height: 22px;"></div>
                            </td>
                            <td>
                                <div class="skeleton-box" style="width: 50%; height: 14px;"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Data -->
            <div v-else class="retro-table-bleed-full">
                <table class="retro-table">
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Peminjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="loan in list" :key="loan.id_peminjaman">
                            <td>
                                {{ loan.judul_buku }}
                                <span class="retro-id-tag">#{{ loan.id_barang }}</span>
                            </td>
                            <td>
                                {{ loan.peminjam_nama }}
                                <span
                                    :class="
                                        loan.peminjam_tipe === 'guru'
                                            ? 'retro-tipe-tag retro-tipe-tag--guru'
                                            : 'retro-tipe-tag retro-tipe-tag--santri'
                                    "
                                >
                                    {{ loan.peminjam_tipe === "guru" ? "Guru" : "Santri" }}
                                </span>
                                <p class="retro-meta">{{ loan.peminjam_meta }}</p>
                            </td>
                            <td>{{ formatJam(loan.tgl_pinjam) }}</td>
                            <td>
                                <span class="status-badge" :class="statusBadge(loan).class">
                                    {{ statusBadge(loan).label }}
                                </span>
                            </td>
                            <td>{{ loan.keterangan_peminjaman || "-" }}</td>
                        </tr>

                        <tr v-if="list.length === 0 && !loadingMore">
                            <td colspan="5" class="retro-empty">
                                Tidak ada peminjaman{{ hasActiveFilter ? " pada periode ini" : "" }}.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Skeleton infinite scroll -->
                <div v-if="loadingMore">
                    <table class="retro-table">
                        <tbody>
                            <tr v-for="i in 5" :key="'sk-' + i" class="skeleton-row">
                                <td>
                                    <div class="skeleton-box" style="width: 80%; height: 16px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton-box" style="width: 60%; height: 16px;"></div>
                                    <div class="skeleton-box" style="width: 40%; height: 10px; margin-top: 4px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton-box" style="width: 70%; height: 14px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton-box skeleton-badge" style="width: 80px; height: 22px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton-box" style="width: 50%; height: 14px;"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p v-if="!nextPageUrl && list.length > 0 && !loadingMore" class="retro-end-note">
                    Semua data sudah dimuat ✓
                </p>
            </div>
        </RetroCard>
    </AuthenticatedLayout>
</template>

<style scoped>
.retro-header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.retro-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1a1a;
}
.retro-subtitle {
    font-size: 13px;
    color: var(--retro-text-secondary);
    margin-top: 4px;
}
.retro-header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.filter-card {
    margin-bottom: 16px;
}
.filter-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Filter */
.filter-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.filter-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
}
.toggle-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    color: var(--retro-text);
}
.toggle-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--retro-cyan);
    cursor: pointer;
}
.range-inputs {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
}
.range-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.range-group label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--retro-muted);
}
.quick-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.date-input {
    font-family: "Satoshi", sans-serif;
    font-size: 15px;
    font-weight: 700;
    padding: 8px 14px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    background: #fff;
    cursor: pointer;
    outline: none;
}
.date-input:focus {
    border-color: #000;
}

/* Table */
.retro-table-bleed-full {
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

.retro-id-tag {
    display: inline-block;
    margin-left: 6px;
    font-size: 10px;
    font-family: "Space Mono", monospace;
    color: var(--retro-muted);
}
.retro-tipe-tag {
    display: inline-block;
    margin-left: 8px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid var(--retro-border);
}
.retro-tipe-tag--santri {
    background: var(--retro-cyan);
}
.retro-tipe-tag--guru {
    background: var(--retro-purple);
}
.retro-meta {
    font-size: 12px;
    color: var(--retro-text-secondary);
    margin-top: 2px;
}

.status-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    white-space: nowrap;
}
.badge--ok {
    background: var(--retro-green);
}
.badge--warning {
    background: var(--retro-yellow);
}
.badge--danger {
    background: var(--retro-red);
    color: #fff;
}
.badge--muted {
    background: #f5f5f5;
    color: var(--retro-muted);
}
.badge--cyan {
    background: var(--retro-cyan);
}

.retro-empty {
    text-align: center;
    padding: 40px;
    color: var(--retro-muted);
}

.retro-end-note {
    text-align: center;
    padding: 12px;
    font-size: 13px;
    color: var(--retro-muted);
    font-weight: 600;
}

/* Skeleton */
@keyframes shimmer {
    0% { background-position: -400px 0; }
    100% { background-position: 400px 0; }
}
.skeleton-box {
    background: linear-gradient(90deg, #eee 25%, #f5f5f5 50%, #eee 75%);
    background-size: 800px 100%;
    animation: shimmer 1.5s infinite linear;
    border-radius: 6px;
    display: inline-block;
    width: 100%;
}
.skeleton-badge {
    border-radius: 999px;
}
.skeleton-row td {
    padding: 14px 24px !important;
    border-bottom: 1px solid #eee !important;
}
</style>
