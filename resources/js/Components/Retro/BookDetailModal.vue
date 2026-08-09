<script setup>
defineProps({
    show: { type: Boolean, default: false },
    buku: { type: Object, default: null },
    subkategoriLabel: { type: String, default: "-" },
});

defineEmits(["close"]);
</script>

<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="retro-modal-overlay"
            @click.self="$emit('close')"
        >
            <div class="retro-modal">
                <div class="retro-modal__header">
                    <div class="retro-modal__icon">📘</div>
                    <button class="retro-modal__close" @click="$emit('close')">
                        ✕
                    </button>
                </div>

                <h2 class="font-cabinet retro-modal__title">
                    {{ buku?.judul_buku }}
                </h2>
                <p class="retro-modal__subtitle">
                    {{ buku?.pengarang_buku }} • {{ buku?.tahunterbit_buku }}
                </p>

                <div class="retro-modal__grid">
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Klasifikasi</span>
                        <span class="retro-modal__value">{{
                            buku?.klasifikasi_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Subkategori</span>
                        <span class="retro-modal__value">{{
                            subkategoriLabel
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Penerbit</span>
                        <span class="retro-modal__value">{{
                            buku?.penerbit_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Kota Terbit</span>
                        <span class="retro-modal__value">{{
                            buku?.kotaterbit_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Edisi</span>
                        <span class="retro-modal__value">{{
                            buku?.edisi_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">ISBN</span>
                        <span class="retro-modal__value">{{
                            buku?.isbn_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Nomor Panggil</span>
                        <span class="retro-modal__value">{{
                            buku?.nomerpanggil_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Eksemplar</span>
                        <span class="retro-modal__value">{{
                            buku?.eksemplar_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Series</span>
                        <span class="retro-modal__value">{{
                            buku?.series_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Sumber</span>
                        <span class="retro-modal__value">{{
                            buku?.sumber_buku || "-"
                        }}</span>
                    </div>
                    <div class="retro-modal__field">
                        <span class="retro-modal__label">Tgl Pembukuan</span>
                        <span class="retro-modal__value">{{
                            buku?.tanggalpembukuan_buku || "-"
                        }}</span>
                    </div>
                    <div
                        class="retro-modal__field"
                        v-if="buku?.keterangan_buku"
                    >
                        <span class="retro-modal__label">Keterangan</span>
                        <span class="retro-modal__value">{{
                            buku?.keterangan_buku
                        }}</span>
                    </div>
                </div>

                <div v-if="$slots.footer" class="retro-modal__footer">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.retro-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(26, 26, 26, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 100;
}

.retro-modal {
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 8px 8px 0 0 var(--retro-border);
    border-radius: 32px;
    padding: 32px;
    max-width: 560px;
    width: 100%;
    max-height: 85vh;
    overflow-y: auto;
}

.retro-modal__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.retro-modal__icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    border: 2px solid var(--retro-border);
    background: var(--retro-cyan);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.retro-modal__close {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    background: #fff;
    cursor: pointer;
}
.retro-modal__close:hover {
    background: var(--retro-red);
}

.retro-modal__title {
    font-size: 24px;
    font-weight: 800;
    color: #1a1a1a;
    margin-top: 20px;
}
.retro-modal__subtitle {
    color: var(--retro-text-secondary);
    font-size: 14px;
    margin-top: 4px;
    margin-bottom: 20px;
}

.retro-modal__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    border-top: 2px dashed #e5e5e5;
    padding-top: 20px;
}

.retro-modal__field {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.retro-modal__label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--retro-muted);
    font-weight: 600;
}
.retro-modal__value {
    font-size: 14px;
    color: #1a1a1a;
    font-weight: 600;
}

.retro-modal__footer {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 2px dashed #e5e5e5;
    display: flex;
    justify-content: flex-end;
}
</style>
