<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import RetroButton from "@/Components/Retro/RetroButton.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    gurus: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
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
        route("guru.index"),
        { search: value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["gurus", "filters"],
        },
    );
}

function refreshData() {
    refreshing.value = true;
    router.post(
        route("guru.refresh"),
        {},
        { onFinish: () => (refreshing.value = false) },
    );
}

function goToPage(page) {
    router.get(
        route("guru.index"),
        { search: search.value, page },
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <Head title="Daftar Guru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="retro-header-row">
                <div>
                    <h2 class="font-cabinet retro-title">Guru List</h2>
                    <p class="retro-subtitle">
                        {{ gurus.total }} guru terdaftar
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
                            : "🔄 Refresh Data Guru"
                    }}
                </RetroButton>
            </div>

            <div class="retro-search-wrap">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama guru... (min. 3 huruf)"
                    class="retro-search"
                />
            </div>
        </template>

        <RetroCard padding="0" rounded="24px" class="stagger-fade">
            <Link
                v-for="g in gurus.data"
                :key="g.guru_no"
                :href="route('guru.show', g.guru_no)"
                class="guru-item"
            >
                <div class="guru-item__info">
                    <h4 class="guru-item__name">{{ g.guru_nama }}</h4>
                    <p class="guru-item__meta">No. Guru: {{ g.guru_no }}</p>
                </div>
                <div class="guru-item__right">
                    <span
                        v-if="g.buku_dipinjam_aktif > 0"
                        class="guru-item__badge guru-item__badge--active"
                    >
                        📕 {{ g.buku_dipinjam_aktif }} dipinjam
                    </span>
                    <span
                        v-if="g.buku_sudah_kembali > 0"
                        class="guru-item__badge guru-item__badge--done"
                    >
                        📗 {{ g.buku_sudah_kembali }} kembali
                    </span>
                    <span
                        v-if="
                            g.buku_dipinjam_aktif === 0 &&
                            g.buku_sudah_kembali === 0
                        "
                        class="guru-item__badge guru-item__badge--none"
                    >
                        Belum pernah pinjam
                    </span>
                    <span class="guru-item__chevron">›</span>
                </div>
            </Link>

            <div v-if="gurus.data.length === 0" class="retro-empty">
                {{
                    search
                        ? `Tidak ada hasil untuk "${search}"`
                        : "Data guru kosong. Coba klik Refresh Data Guru."
                }}
            </div>
        </RetroCard>

        <div class="retro-pagination">
            <button
                v-for="p in gurus.last_page"
                :key="p"
                class="retro-page-link"
                :class="{ 'retro-page-link--active': p === gurus.current_page }"
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

.guru-item {
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
.guru-item:last-child {
    border-bottom: none;
}
.guru-item:hover {
    background: #fafafa;
}

.guru-item__name {
    font-weight: 700;
    color: #1a1a1a;
}
.guru-item__meta {
    font-size: 13px;
    color: var(--retro-text-secondary);
    margin-top: 2px;
}

.guru-item__right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    flex-wrap: wrap;
    justify-content: flex-end;
    max-width: 260px;
}
.guru-item__badge {
    font-size: 12px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    white-space: nowrap;
}
.guru-item__badge--active {
    background: var(--retro-pink);
}
.guru-item__badge--done {
    background: var(--retro-green);
}
.guru-item__badge--none {
    background: #f5f5f5;
    color: var(--retro-muted);
}
.guru-item__chevron {
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
