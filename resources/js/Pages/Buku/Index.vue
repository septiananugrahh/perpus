<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import RetroButton from "@/Components/Retro/RetroButton.vue";
import BookListItem from "@/Components/Retro/BookListItem.vue";
import BookDetailModal from "@/Components/Retro/BookDetailModal.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    bukus: { type: Object, required: true },
    subkategori: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

// ==== Search dengan debounce, trigger mulai huruf ke-3 ====
const search = ref(props.filters?.search ?? "");
let debounceTimer = null;

watch(search, (value) => {
    clearTimeout(debounceTimer);

    // reset ke daftar penuh saat input dikosongkan
    if (value.length === 0) {
        debounceTimer = setTimeout(() => runSearch(""), 300);
        return;
    }

    // baru cari setelah minimal 3 huruf
    if (value.length < 3) return;

    debounceTimer = setTimeout(() => runSearch(value), 400);
});

function runSearch(value) {
    router.get(
        route("buku.index"),
        { search: value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["bukus", "filters"],
        },
    );
}

// ==== Modal detail ====
const showModal = ref(false);
const selectedBuku = ref(null);

function openDetail(buku) {
    selectedBuku.value = buku;
    showModal.value = true;
}

function closeDetail() {
    showModal.value = false;
    selectedBuku.value = null;
}
</script>

<template>
    <Head title="Daftar Buku" />

    <AuthenticatedLayout>
        <template #header>
            <div class="retro-header-row">
                <h2 class="font-cabinet retro-title">Inventory Buku</h2>
                <Link :href="route('buku.create')">
                    <RetroButton variant="primary">+ Input Buku</RetroButton>
                </Link>
            </div>

            <div class="retro-search-wrap">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari judul, pengarang, penerbit, ISBN... (min. 3 huruf)"
                    class="retro-search"
                />
            </div>
        </template>

        <RetroCard padding="0" rounded="24px">
            <BookListItem
                v-for="b in bukus.data"
                :key="b.id_buku"
                :judul="b.judul_buku"
                :pengarang="b.pengarang_buku"
                :tahun="b.tahunterbit_buku"
                :isbn="b.isbn_buku"
                :penerbit="b.penerbit_buku"
                :klasifikasi="b.klasifikasi_buku"
                :nomor-panggil="b.nomerpanggil_buku"
                :subkategori-label="subkategori[b.kode_subkategori] ?? '-'"
                @click="openDetail(b)"
            />
            <div v-if="bukus.data.length === 0" class="retro-empty">
                {{
                    search
                        ? `Tidak ada hasil untuk "${search}"`
                        : "Belum ada data buku"
                }}
            </div>
        </RetroCard>

        <div class="retro-pagination">
            <Link
                v-for="(link, i) in bukus.links"
                :key="i"
                :href="link.url || '#'"
                class="retro-page-link"
                :class="{
                    'retro-page-link--active': link.active,
                    'retro-page-link--disabled': !link.url,
                }"
                v-html="link.label"
            />
        </div>

        <BookDetailModal
            :show="showModal"
            :buku="selectedBuku"
            :subkategori-label="
                subkategori[selectedBuku?.kode_subkategori] ?? '-'
            "
            @close="closeDetail"
        />
    </AuthenticatedLayout>
</template>

<style scoped>
.retro-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.retro-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1a1a;
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
    text-decoration: none;
    color: #1a1a1a;
    font-size: 13px;
    font-weight: 600;
}
.retro-page-link:hover {
    background: var(--retro-yellow);
}
.retro-page-link--active {
    background: #1a1a1a;
    color: #fff;
}
.retro-page-link--disabled {
    color: #ccc;
    border-color: #ddd;
    pointer-events: none;
}
</style>
