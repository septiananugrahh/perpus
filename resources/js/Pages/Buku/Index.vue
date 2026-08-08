<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RetroCard from '@/Components/Retro/RetroCard.vue';
import RetroButton from '@/Components/Retro/RetroButton.vue';
import BookListItem from '@/Components/Retro/BookListItem.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    bukus: { type: Object, required: true },
    subkategori: { type: Object, default: () => ({}) },
});
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
        </template>

        <RetroCard padding="p-0" rounded="24px">
            <BookListItem
                v-for="b in bukus.data"
                :key="b.id_buku"
                :judul="b.judul_buku"
                :pengarang="b.pengarang_buku"
                :tahun="b.tahunterbit_buku"
                :isbn="b.isbn_buku"
                :status="subkategori[b.kode_subkategori] ?? '-'"
            />
            <div v-if="bukus.data.length === 0" class="retro-empty">Belum ada data buku</div>
        </RetroCard>

        <div class="retro-pagination">
            <Link
                v-for="(link, i) in bukus.links"
                :key="i"
                :href="link.url || '#'"
                class="retro-page-link"
                :class="{ 'retro-page-link--active': link.active, 'retro-page-link--disabled': !link.url }"
                v-html="link.label"
            />
        </div>
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
.retro-page-link:hover { background: var(--retro-yellow); }
.retro-page-link--active { background: #1a1a1a; color: #fff; }
.retro-page-link--disabled { color: #ccc; border-color: #ddd; pointer-events: none; }
</style>
