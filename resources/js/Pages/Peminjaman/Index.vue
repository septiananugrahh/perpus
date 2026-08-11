<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RetroCard from "@/Components/Retro/RetroCard.vue";
import RetroButton from "@/Components/Retro/RetroButton.vue";
import PinjamModal from "@/Components/Retro/PinjamModal.vue";
import KembaliModal from "@/Components/Retro/KembaliModal.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    loans: { type: Array, default: () => [] },
    batasHariPinjam: { type: Number, default: 14 },
});

const showPinjamModal = ref(false);
const showKembaliModal = ref(false);

function statusBadge(sisaHari) {
    if (sisaHari === null)
        return { label: "Tanggal tidak valid", class: "badge--muted" };
    if (sisaHari < 0)
        return {
            label: `Terlambat ${Math.abs(sisaHari)} hari`,
            class: "badge--danger",
        };
    if (sisaHari <= 3)
        return { label: `Sisa ${sisaHari} hari`, class: "badge--warning" };
    return { label: `Sisa ${sisaHari} hari`, class: "badge--ok" };
}
</script>

<template>
    <Head title="Peminjaman Buku" />

    <AuthenticatedLayout>
        <template #header>
            <div class="retro-header-row">
                <div>
                    <h2 class="font-cabinet retro-title">Peminjaman Buku</h2>
                    <p class="retro-subtitle">
                        {{ loans.length }} buku sedang dipinjam • batas
                        {{ batasHariPinjam }} hari
                    </p>
                </div>
                <div class="retro-header-actions">
                    <RetroButton
                        variant="color"
                        color="cyan"
                        @click="showKembaliModal = true"
                    >
                        📗 Kembalikan Buku
                    </RetroButton>
                    <RetroButton
                        variant="primary"
                        @click="showPinjamModal = true"
                    >
                        📖 Pinjamkan Buku
                    </RetroButton>
                </div>
            </div>
        </template>

        <RetroCard padding="0" rounded="24px" class="stagger-fade">
            <div class="retro-table-bleed-full">
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
                        <tr v-for="loan in loans" :key="loan.id_peminjaman">
                            <td>
                                {{ loan.judul_buku }}
                                <span class="retro-id-tag"
                                    >#{{ loan.id_barang }}</span
                                >
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
                                    {{
                                        loan.peminjam_tipe === "guru"
                                            ? "Guru"
                                            : "Santri"
                                    }}
                                </span>
                                <p class="retro-meta">
                                    {{ loan.peminjam_meta }}
                                </p>
                            </td>
                            <td>{{ loan.tgl_pinjam }}</td>
                            <td>
                                <span
                                    class="status-badge"
                                    :class="statusBadge(loan.sisa_hari).class"
                                >
                                    {{ statusBadge(loan.sisa_hari).label }}
                                </span>
                            </td>
                            <td>{{ loan.keterangan_peminjaman || "-" }}</td>
                        </tr>
                        <tr v-if="loans.length === 0">
                            <td colspan="5" class="retro-empty">
                                Tidak ada buku yang sedang dipinjam. Klik
                                "Pinjamkan Buku" untuk mulai.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </RetroCard>

        <PinjamModal :show="showPinjamModal" @close="showPinjamModal = false" />
        <KembaliModal
            :show="showKembaliModal"
            @close="showKembaliModal = false"
        />
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

.retro-empty {
    text-align: center;
    padding: 40px;
    color: var(--retro-muted);
}
</style>
