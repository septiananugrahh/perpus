<script setup>
import RetroButton from "@/Components/Retro/RetroButton.vue";
import { computed, ref, watch } from "vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: "Konfirmasi Hapus" },
    message: { type: String, default: "" },
    confirmText: { type: String, default: "hapus buku" },
    processing: { type: Boolean, default: false },
});

const emit = defineEmits(["close", "confirm"]);

const typed = ref("");
const isMatch = computed(
    () => typed.value.trim().toLowerCase() === props.confirmText.toLowerCase(),
);

watch(
    () => props.show,
    (val) => {
        if (val) typed.value = "";
    },
);

function confirm() {
    if (isMatch.value) emit("confirm");
}
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="confirm-overlay" @click.self="$emit('close')">
            <div class="confirm-modal">
                <div class="confirm-modal__icon">⚠️</div>
                <h2 class="font-cabinet confirm-modal__title">{{ title }}</h2>
                <p class="confirm-modal__message">{{ message }}</p>

                <p class="confirm-modal__instruction">
                    Ketik <strong>"{{ confirmText }}"</strong> di bawah untuk
                    konfirmasi:
                </p>
                <input
                    v-model="typed"
                    type="text"
                    class="confirm-modal__input"
                    :placeholder="confirmText"
                    @keyup.enter="confirm"
                />

                <div class="confirm-modal__actions">
                    <RetroButton
                        variant="secondary"
                        :disabled="processing"
                        @click="$emit('close')"
                    >
                        Batal
                    </RetroButton>
                    <RetroButton
                        variant="color"
                        color="red"
                        :disabled="!isMatch || processing"
                        @click="confirm"
                    >
                        {{ processing ? "Menghapus..." : "Ya, Hapus" }}
                    </RetroButton>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.confirm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(26, 26, 26, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 100;
}

.confirm-modal {
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 8px 8px 0 0 var(--retro-border);
    border-radius: 32px;
    padding: 32px;
    max-width: 420px;
    width: 100%;
    text-align: center;
}

.confirm-modal__icon {
    width: 56px;
    height: 56px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    background: var(--retro-red);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin: 0 auto 16px;
}

.confirm-modal__title {
    font-size: 22px;
    font-weight: 800;
    color: #1a1a1a;
}

.confirm-modal__message {
    font-size: 14px;
    color: var(--retro-text-secondary);
    margin-top: 8px;
}

.confirm-modal__instruction {
    font-size: 13px;
    color: #1a1a1a;
    margin-top: 20px;
    margin-bottom: 8px;
}

.confirm-modal__input {
    width: 100%;
    box-sizing: border-box;
    padding: 12px 16px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    font-size: 14px;
    text-align: center;
    outline: none;
}
.confirm-modal__input:focus {
    background: var(--retro-yellow);
}

.confirm-modal__actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}
.confirm-modal__actions > * {
    flex: 1;
    justify-content: center;
}
</style>
