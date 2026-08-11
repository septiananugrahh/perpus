<script setup>
import { onMounted, onBeforeUnmount, ref } from "vue";

const emit = defineEmits(["detected", "close"]);

const scannerEl = ref(null);
const error = ref("");
let html5QrCode = null;

onMounted(async () => {
    try {
        const { Html5Qrcode } = await import("html5-qrcode");
        html5QrCode = new Html5Qrcode("camera-scanner-view");

        await html5QrCode.start(
            { facingMode: "environment" }, // kamera belakang di HP
            {
                fps: 10,
                qrbox: { width: 250, height: 150 },
                formatsToSupport: [Html5Qrcode ? undefined : undefined].filter(
                    Boolean,
                ), // biarkan auto-detect semua format
            },
            (decodedText) => {
                emit("detected", decodedText);
            },
            () => {
                // callback error per-frame (tidak ketemu barcode), diabaikan — ini normal, terjadi tiap frame kosong
            },
        );
    } catch (e) {
        error.value =
            "Tidak bisa mengakses kamera. Pastikan izin kamera diaktifkan, atau gunakan HTTPS.";
    }
});

onBeforeUnmount(async () => {
    try {
        if (html5QrCode?.isScanning) {
            await html5QrCode.stop();
        }
        html5QrCode?.clear();
    } catch (e) {
        // abaikan error saat cleanup
    }
});
</script>

<template>
    <Teleport to="body">
        <div class="cam-overlay" @click.self="$emit('close')">
            <div class="cam-modal">
                <div class="cam-header">
                    <span class="cam-title">📷 Arahkan ke Barcode</span>
                    <button class="cam-close" @click="$emit('close')">✕</button>
                </div>

                <div
                    id="camera-scanner-view"
                    ref="scannerEl"
                    class="cam-view"
                ></div>

                <p v-if="error" class="cam-error">{{ error }}</p>
                <p v-else class="cam-hint">
                    Posisikan barcode di dalam kotak sampai terbaca otomatis.
                </p>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.cam-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 200;
}
.cam-modal {
    background: #fff;
    border: 2px solid var(--retro-border);
    border-radius: 24px;
    padding: 20px;
    max-width: 420px;
    width: 100%;
}
.cam-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.cam-title {
    font-weight: 800;
    font-size: 16px;
}
.cam-close {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    background: #fff;
    cursor: pointer;
}
.cam-view {
    width: 100%;
    min-height: 250px;
    border-radius: 14px;
    overflow: hidden;
    background: #000;
}
.cam-hint,
.cam-error {
    font-size: 12px;
    text-align: center;
    margin-top: 12px;
}
.cam-error {
    color: var(--retro-red);
    font-weight: 600;
}
</style>
