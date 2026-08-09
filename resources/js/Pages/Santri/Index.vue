<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import RetroButton from "@/Components/Retro/RetroButton.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    santris: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    tahunAjar: { type: String, default: "" },
});

const search = ref(props.filters?.search ?? "");
const refreshing = ref(false);
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
        route("santri.index"),
        { search: value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["santris", "filters"],
        },
    );
}

function refreshData() {
    refreshing.value = true;
    router.post(
        route("santri.refresh"),
        {},
        { onFinish: () => (refreshing.value = false) },
    );
}

function goToPage(page) {
    router.get(
        route("santri.index"),
        { search: search.value, page },
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <Head title="Daftar Santri" />

    <AuthenticatedLayout>
        <template #header>
            <div class="retro-header-row">
                <div>
                    <h2 class="font-cabinet retro-title">Santri List</h2>
                    <p class="retro-subtitle">
                        Tahun Ajar {{ tahunAjar }} • {{ santris.total }} santri
                    </p>
                </div>
                <RetroButton
                    variant="secondary"
                    :disabled="refreshing"
                    @click="refreshData"
                >
                    {{
                        refreshing
                            ? "⏳ Memperbarui..."
                            : "🔄 Refresh Data Santri"
                    }}
                </RetroButton>
            </div>

            <div class="retro-search-wrap">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama, NIS, atau kelas... (min. 3 huruf)"
                    class="retro-search"
                />
            </div>
        </template>

        <RetroCard padding="0" rounded="24px" class="stagger-fade">
            <Link
                v-for="s in santris.data"
                :key="s.id"
                :href="route('santri.show', s.id)"
                class="santri-item"
            >
                <div class="santri-item__info">
                    <h4 class="santri-item__name">{{ s.nama }}</h4>
                    <p class="santri-item__meta">
                        {{ s.kelas_nama }} • Tingkat {{ s.kelas_tingkat }} •
                        NIS: {{ s.nis }}
                    </p>
                </div>
                <div class="santri-item__right">
                    <span
                        v-if="s.buku_dipinjam_aktif > 0"
                        class="santri-item__badge santri-item__badge--active"
                    >
                        📕 {{ s.buku_dipinjam_aktif }} dipinjam
                    </span>
                    <span
                        v-if="s.buku_sudah_kembali > 0"
                        class="santri-item__badge santri-item__badge--done"
                    >
                        📗 {{ s.buku_sudah_kembali }} kembali
                    </span>
                    <span
                        v-if="
                            s.buku_dipinjam_aktif === 0 &&
                            s.buku_sudah_kembali === 0
                        "
                        class="santri-item__badge santri-item__badge--none"
                    >
                        Belum pernah pinjam
                    </span>
                    <span class="santri-item__chevron">›</span>
                </div>
            </Link>

            <div v-if="santris.data.length === 0" class="retro-empty">
                {{
                    search
                        ? `Tidak ada hasil untuk "${search}"`
                        : "Data santri kosong. Coba klik Refresh Data Santri."
                }}
            </div>
        </RetroCard>

        <div class="retro-pagination">
            <button
                v-for="p in santris.last_page"
                :key="p"
                class="retro-page-link"
                :class="{
                    'retro-page-link--active': p === santris.current_page,
                }"
                @click="goToPage(p)"
            >
                {{ p }}
            </button>
        </div>
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

.retro-search-wrap {
    margin-top: 20px;
}
.retro-search {
    width: 100%;
    max-width: 480px;
    padding: 12px 20px;
    border: 2px solid var(--retro-border);
    border-radius: 16px;
    font-size: 14px;
    outline: none;
}
.retro-search:focus {
    background: var(--retro-yellow);
}

.santri-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 24px;
    border-bottom: 1px solid #e5e5e5;
    text-decoration: none;
    color: inherit;
    transition: background 0.1s ease;
}
.santri-item:last-child {
    border-bottom: none;
}
.santri-item:hover {
    background: #fafafa;
}

.santri-item__name {
    font-weight: 700;
    color: #1a1a1a;
}
.santri-item__meta {
    font-size: 13px;
    color: var(--retro-text-secondary);
    margin-top: 2px;
}

.santri-item__right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    flex-wrap: wrap;
    justify-content: flex-end;
    max-width: 260px;
}
.santri-item__badge {
    font-size: 12px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    white-space: nowrap;
}
.santri-item__badge--active {
    background: var(--retro-pink);
}
.santri-item__badge--done {
    background: var(--retro-green);
}
.santri-item__badge--none {
    background: #f5f5f5;
    color: var(--retro-muted);
}
.santri-item__chevron {
    font-size: 20px;
    color: var(--retro-muted);
}

.retro-empty {
    padding: 40px;
    text-align: center;
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
</style>
