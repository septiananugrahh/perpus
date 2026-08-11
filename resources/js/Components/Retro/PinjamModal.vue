<script setup>
import RetroButton from "@/Components/Retro/RetroButton.vue";
import SmartPickerInput from "@/Components/Retro/SmartPickerInput.vue";
import { router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import axios from "axios";

const props = defineProps({ show: { type: Boolean, default: false } });
const emit = defineEmits(["close"]);

const buku = ref(null);
const bukuError = ref("");
const peminjam = ref(null);
const peminjamError = ref("");
const keterangan = ref("");
const saving = ref(false);
const bukuInputRef = ref(null);
const peminjamInputRef = ref(null);

watch(
    () => props.show,
    (val) => {
        if (val) reset();
    },
);

function reset() {
    buku.value = null;
    bukuError.value = "";
    peminjam.value = null;
    peminjamError.value = "";
    keterangan.value = "";
    saving.value = false;
}

async function onScanBuku(kode) {
    bukuError.value = "";
    try {
        const { data } = await axios.get(route("peminjaman.cari-buku"), {
            params: { kode },
        });
        if (!data.found) {
            bukuError.value = data.message;
            return;
        }
        if (data.sedang_dipinjam) {
            bukuError.value = `Buku ini sedang dipinjam oleh ${data.peminjam_aktif.nama}. Belum bisa dipinjamkan lagi.`;
            return;
        }
        buku.value = data.buku;
        peminjamInputRef.value?.focus();
    } catch (e) {
        bukuError.value = "Gagal memeriksa buku. Coba lagi.";
    }
}

function onSelectBuku(item) {
    bukuError.value = "";
    buku.value = item; // sudah berupa objek IndukBuku (judul_buku, id_buku, klasifikasi_buku)
    peminjamInputRef.value?.focus();
}

async function onScanPeminjam(kode) {
    peminjamError.value = "";
    try {
        const { data } = await axios.get(route("peminjaman.cari-peminjam"), {
            params: { kode },
        });
        if (!data.found) {
            peminjamError.value = data.message;
            return;
        }
        peminjam.value = data;
    } catch (e) {
        peminjamError.value = "Gagal memeriksa peminjam. Coba lagi.";
    }
}

function onSelectPeminjam(item) {
    peminjamError.value = "";
    peminjam.value = item; // { tipe, kode, nama, meta }
}

const canSubmit = computed(() => buku.value && peminjam.value && !saving.value);

function submit() {
    if (!canSubmit.value) return;
    saving.value = true;
    router.post(
        route("peminjaman.store"),
        {
            id_barang: buku.value.id_buku,
            penanggung_jawab: peminjam.value.kode,
            keterangan_peminjaman: keterangan.value,
        },
        {
            onSuccess: () => emit("close"),
            onFinish: () => (saving.value = false),
        },
    );
}

function resetBuku() {
    buku.value = null;
    bukuError.value = "";
    bukuInputRef.value?.focus();
}
function resetPeminjam() {
    peminjam.value = null;
    peminjamError.value = "";
    peminjamInputRef.value?.focus();
}
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="pm-overlay" @click.self="$emit('close')">
            <div class="pm-modal">
                <h2 class="font-cabinet pm-title">📖 Pinjamkan Buku</h2>

                <!-- Step 1: Buku -->
                <div class="pm-step">
                    <label class="pm-label">1. Buku</label>
                    <p class="pm-hint">
                        Scan barcode, atau ketik judul/nomor panggil untuk cari.
                    </p>

                    <div v-if="!buku">
                        <SmartPickerInput
                            ref="bukuInputRef"
                            placeholder="Scan barcode atau ketik judul buku..."
                            :autocomplete-url="
                                route('peminjaman.cari-buku-nama')
                            "
                            @scan="onScanBuku"
                            @select="onSelectBuku"
                        >
                            <template #item="{ item }">
                                <strong>{{ item.judul_buku }}</strong>
                                <p class="pm-item-meta">
                                    {{ item.klasifikasi_buku }} • ID:
                                    {{ item.id_buku }}
                                </p>
                            </template>
                        </SmartPickerInput>
                        <p v-if="bukuError" class="pm-error">{{ bukuError }}</p>
                    </div>
                    <div v-else class="pm-found-card">
                        <div>
                            <strong>{{ buku.judul_buku }}</strong>
                            <p class="pm-found-meta">
                                {{ buku.klasifikasi_buku }} • ID:
                                {{ buku.id_buku }}
                            </p>
                        </div>
                        <button class="pm-change-btn" @click="resetBuku">
                            Ganti
                        </button>
                    </div>
                </div>

                <!-- Step 2: Peminjam -->
                <div class="pm-step" :class="{ 'pm-step--disabled': !buku }">
                    <label class="pm-label">2. Peminjam</label>
                    <p class="pm-hint">
                        Scan ID santri/guru, atau ketik nama untuk cari (guru &
                        santri tergabung).
                    </p>

                    <div v-if="!peminjam">
                        <SmartPickerInput
                            ref="peminjamInputRef"
                            placeholder="Scan ID atau ketik nama peminjam..."
                            :autocomplete-url="
                                route('peminjaman.cari-peminjam-nama')
                            "
                            :disabled="!buku"
                            @scan="onScanPeminjam"
                            @select="onSelectPeminjam"
                        >
                            <template #item="{ item }">
                                <strong>{{ item.nama }}</strong>
                                <p class="pm-item-meta">{{ item.meta }}</p>
                            </template>
                        </SmartPickerInput>
                        <p v-if="peminjamError" class="pm-error">
                            {{ peminjamError }}
                        </p>
                    </div>
                    <div v-else class="pm-found-card">
                        <div>
                            <strong>{{ peminjam.nama }}</strong>
                            <p class="pm-found-meta">{{ peminjam.meta }}</p>
                        </div>
                        <button class="pm-change-btn" @click="resetPeminjam">
                            Ganti
                        </button>
                    </div>
                </div>

                <!-- Step 3: Keterangan -->
                <div class="pm-step">
                    <label class="pm-label">3. Keterangan (opsional)</label>
                    <input
                        v-model="keterangan"
                        type="text"
                        class="pm-text-input"
                        placeholder="Misal: untuk pembelajaran, upacara, dll"
                    />
                </div>

                <div class="pm-actions">
                    <RetroButton variant="secondary" @click="$emit('close')"
                        >Batal</RetroButton
                    >
                    <RetroButton
                        variant="primary"
                        :disabled="!canSubmit"
                        @click="submit"
                    >
                        {{ saving ? "Menyimpan..." : "Simpan Peminjaman" }}
                    </RetroButton>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.pm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(26, 26, 26, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 100;
}
.pm-modal {
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 8px 8px 0 0 var(--retro-border);
    border-radius: 32px;
    padding: 36px;
    max-width: 640px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}
.pm-title {
    font-size: 24px;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 28px;
}

.pm-step {
    margin-bottom: 24px;
}
.pm-step--disabled {
    opacity: 0.5;
    pointer-events: none;
}
.pm-label {
    display: block;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 4px;
    color: #1a1a1a;
}
.pm-hint {
    font-size: 12px;
    color: var(--retro-text-secondary);
    margin-bottom: 10px;
}

.pm-error {
    font-size: 12px;
    color: var(--retro-red);
    font-weight: 600;
    margin-top: 6px;
}
.pm-item-meta {
    font-size: 11px;
    color: var(--retro-text-secondary);
    margin-top: 2px;
}

.pm-found-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    padding: 14px 18px;
    background: var(--retro-green);
}
.pm-found-meta {
    font-size: 13px;
    color: #333;
    margin-top: 2px;
}
.pm-change-btn {
    font-size: 12px;
    font-weight: 700;
    background: #fff;
    border: 2px solid var(--retro-border);
    border-radius: 10px;
    padding: 6px 12px;
    cursor: pointer;
    flex-shrink: 0;
}
.pm-change-btn:hover {
    background: var(--retro-yellow);
}

.pm-text-input {
    width: 100%;
    box-sizing: border-box;
    padding: 12px 16px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    font-size: 14px;
    outline: none;
}
.pm-text-input:focus {
    background: var(--retro-yellow);
}

.pm-actions {
    display: flex;
    gap: 12px;
    margin-top: 8px;
}
.pm-actions > * {
    flex: 1;
    justify-content: center;
}
</style>
