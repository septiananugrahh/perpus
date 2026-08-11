<script setup>
import RetroButton from "@/Components/Retro/RetroButton.vue";
import BarcodeScanInput from "@/Components/Retro/BarcodeScanInput.vue";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({ show: { type: Boolean, default: false } });
const emit = defineEmits(["close"]);

const found = ref(null);
const error = ref("");
const processing = ref(false);
const inputRef = ref(null);

watch(
    () => props.show,
    (val) => {
        if (val) reset();
    },
);

function reset() {
    found.value = null;
    error.value = "";
    processing.value = false;
}

async function onScan(kode) {
    error.value = "";
    try {
        const { data } = await axios.get(route("peminjaman.cari-buku"), {
            params: { kode },
        });
        if (!data.found) {
            error.value = data.message;
            return;
        }
        if (!data.sedang_dipinjam) {
            error.value =
                "Buku ini tidak sedang dipinjam (tidak perlu dikembalikan).";
            return;
        }
        found.value = data;
    } catch (e) {
        error.value = "Gagal memeriksa buku. Coba lagi.";
    }
}

function confirmReturn() {
    if (!found.value) return;
    processing.value = true;
    router.post(
        route("peminjaman.kembalikan"),
        { id_peminjaman: found.value.id_peminjaman_aktif },
        {
            onSuccess: () => emit("close"),
            onFinish: () => (processing.value = false),
        },
    );
}

function scanLagi() {
    found.value = null;
    error.value = "";
    inputRef.value?.focus();
}
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="km-overlay" @click.self="$emit('close')">
            <div class="km-modal">
                <h2 class="font-cabinet km-title">📗 Kembalikan Buku</h2>

                <div v-if="!found">
                    <label class="km-label">Scan / Ketik Kode Buku</label>
                    <BarcodeScanInput
                        ref="inputRef"
                        placeholder="Scan barcode buku..."
                        @scan="onScan"
                    />
                    <p v-if="error" class="km-error">{{ error }}</p>
                </div>

                <div v-else class="km-confirm">
                    <div class="km-confirm-card">
                        <strong>{{ found.buku.judul_buku }}</strong>
                        <p class="km-confirm-meta">
                            Dipinjam oleh: {{ found.peminjam_aktif.nama }}
                        </p>
                        <p class="km-confirm-meta">
                            {{ found.peminjam_aktif.meta }}
                        </p>
                    </div>
                    <p class="km-question">Buku ini sudah dikembalikan?</p>
                    <div class="km-actions">
                        <RetroButton variant="secondary" @click="scanLagi"
                            >Scan Ulang</RetroButton
                        >
                        <RetroButton
                            variant="color"
                            color="cyan"
                            :disabled="processing"
                            @click="confirmReturn"
                        >
                            {{
                                processing
                                    ? "Memproses..."
                                    : "Ya, Sudah Kembali"
                            }}
                        </RetroButton>
                    </div>
                </div>

                <div class="km-footer">
                    <RetroButton variant="secondary" @click="$emit('close')"
                        >Tutup</RetroButton
                    >
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.km-overlay {
    position: fixed;
    inset: 0;
    background: rgba(26, 26, 26, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 100;
}
.km-modal {
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 8px 8px 0 0 var(--retro-border);
    border-radius: 32px;
    padding: 36px;
    max-width: 520px;
    width: 100%;
}
.km-title {
    font-size: 22px;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 20px;
}
.km-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 6px;
    color: #1a1a1a;
}
.km-error {
    font-size: 12px;
    color: var(--retro-red);
    font-weight: 600;
    margin-top: 6px;
}

.km-confirm-card {
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    padding: 16px;
    background: var(--retro-cyan);
}
.km-confirm-meta {
    font-size: 13px;
    color: #333;
    margin-top: 4px;
}
.km-question {
    font-size: 14px;
    font-weight: 700;
    margin: 16px 0 12px;
    text-align: center;
}

.km-actions {
    display: flex;
    gap: 12px;
}
.km-actions > * {
    flex: 1;
    justify-content: center;
}

.km-footer {
    margin-top: 20px;
}
.km-footer > * {
    width: 100%;
    justify-content: center;
}
</style>
