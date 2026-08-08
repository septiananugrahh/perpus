<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    bukus: { type: Object, required: true },
    subkategori: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Daftar Buku" />

    <AuthenticatedLayout>
        <template #header>
            <div class="rb-header-row">
                <h2 class="rb-h2">DAFTAR INDUK BUKU</h2>
                <Link
                    :href="route('buku.create')"
                    class="rb-btn rb-btn--primary"
                >
                    + INPUT BUKU
                </Link>
            </div>
        </template>

        <div class="rb-table-wrap">
            <table class="rb-table">
                <thead>
                    <tr>
                        <th class="rb-table__no">ID</th>
                        <th>JUDUL BUKU</th>
                        <th>KLASIFIKASI</th>
                        <th>PENGARANG</th>
                        <th>PENERBIT</th>
                        <th>TH. TERBIT</th>
                        <th>SUBKATEGORI</th>
                        <th>SUMBER</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="b in bukus.data" :key="b.id_buku">
                        <td class="rb-table__no">{{ b.id_buku }}</td>
                        <td>{{ b.judul_buku }}</td>
                        <td>{{ b.klasifikasi_buku }}</td>
                        <td>{{ b.pengarang_buku }}</td>
                        <td>{{ b.penerbit_buku }}</td>
                        <td>{{ b.tahunterbit_buku }}</td>
                        <td>{{ subkategori[b.kode_subkategori] ?? "-" }}</td>
                        <td>{{ b.sumber_buku || "-" }}</td>
                    </tr>
                    <tr v-if="bukus.data.length === 0">
                        <td colspan="8" class="rb-empty">
                            BELUM ADA DATA BUKU
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="rb-pagination">
            <Link
                v-for="(link, i) in bukus.links"
                :key="i"
                :href="link.url || '#'"
                class="rb-page-link"
                :class="{
                    'rb-page-link--active': link.active,
                    'rb-page-link--disabled': !link.url,
                }"
                v-html="link.label"
            />
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.rb-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.rb-h2 {
    font-family: "Archivo Black", sans-serif;
    font-size: 32px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #000;
}

.rb-btn {
    font-family: "Work Sans", sans-serif;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 10px 24px;
    border: 3px solid #000;
    text-decoration: none;
    display: inline-block;
}
.rb-btn--primary {
    background: #000;
    color: #fff;
}
.rb-btn--primary:hover {
    background: #fff;
    color: #000;
}

.rb-table-wrap {
    overflow-x: auto;
    border: 3px solid #000;
}

.rb-table {
    width: 100%;
    border-collapse: collapse;
}
.rb-table thead th {
    background: #000;
    color: #fff;
    font-family: "Archivo Black", sans-serif;
    font-size: 12px;
    letter-spacing: 1px;
    text-align: left;
    padding: 10px 12px;
    white-space: nowrap;
}
.rb-table__no {
    width: 70px;
    text-align: center;
}
.rb-table tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid #000;
    font-family: "Work Sans", sans-serif;
    font-size: 14px;
}
.rb-empty {
    text-align: center;
    padding: 40px;
    font-family: "Space Mono", monospace;
    color: #555;
}

.rb-pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 16px;
}
.rb-page-link {
    font-family: "Space Mono", monospace;
    font-size: 13px;
    padding: 8px 14px;
    border: 2px solid #000;
    text-decoration: none;
    color: #000;
}
.rb-page-link:hover {
    background: #000;
    color: #fff;
}
.rb-page-link--active {
    background: #000;
    color: #fff;
}
.rb-page-link--disabled {
    color: #ccc;
    border-color: #ccc;
    pointer-events: none;
}
</style>
