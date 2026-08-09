<script setup>
defineProps({
    judul: { type: String, required: true },
    pengarang: { type: String, default: "" },
    tahun: { type: String, default: "" },
    isbn: { type: String, default: "" },
    penerbit: { type: String, default: "" },
    klasifikasi: { type: String, default: "" },
    nomorPanggil: { type: String, default: "" },
    subkategoriLabel: { type: String, default: "-" },
    selectable: { type: Boolean, default: false },
    selected: { type: Boolean, default: false },
});

defineEmits(["click", "toggle-select"]);
</script>

<template>
    <div
        class="retro-book-item"
        :class="{ 'retro-book-item--selected': selectable && selected }"
        @click="$emit('click')"
    >
        <input
            v-if="selectable"
            type="checkbox"
            class="retro-book-item__checkbox"
            :checked="selected"
            @click.stop
            @change="$emit('toggle-select')"
        />
        <div class="retro-book-item__info">
            <h4 class="retro-book-item__title">{{ judul }}</h4>
            <p class="retro-book-item__meta">
                {{ pengarang || "-" }} • {{ tahun || "-"
                }}<span v-if="isbn"> • ISBN: {{ isbn }}</span>
            </p>
            <p class="retro-book-item__meta-secondary">
                {{ penerbit || "-"
                }}<span v-if="nomorPanggil">
                    • No. Panggil: {{ nomorPanggil }}</span
                >
            </p>
        </div>
        <div class="retro-book-item__right">
            <span v-if="klasifikasi" class="retro-book-item__tag">{{
                klasifikasi
            }}</span>
            <span class="retro-book-item__status">{{ subkategoriLabel }}</span>
            <span class="retro-book-item__chevron">›</span>
        </div>
    </div>
</template>

<style scoped>
.retro-book-item {
    border-bottom: 1px solid #e5e5e5;
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    transition: background 0.1s ease;
    cursor: pointer;
}
.retro-book-item:hover {
    background: #fafafa;
}
.retro-book-item:last-child {
    border-bottom: none;
}
.retro-book-item--selected {
    background: #fff8e1;
}

.retro-book-item__checkbox {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    cursor: pointer;
}

.retro-book-item__info {
    min-width: 0;
}
.retro-book-item__title {
    font-weight: 700;
    color: #1a1a1a;
}
.retro-book-item__meta {
    font-size: 14px;
    color: var(--retro-text-secondary);
    margin-top: 2px;
}
.retro-book-item__meta-secondary {
    font-size: 12px;
    color: var(--retro-muted);
    margin-top: 2px;
}

.retro-book-item__right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.retro-book-item__tag {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    background: var(--retro-yellow);
    white-space: nowrap;
}
.retro-book-item__status {
    font-size: 13px;
    font-weight: 700;
    color: #1a1a1a;
    white-space: nowrap;
    display: none;
}
@media (min-width: 640px) {
    .retro-book-item__status {
        display: inline;
    }
}
.retro-book-item__chevron {
    font-size: 20px;
    color: var(--retro-muted);
}
</style>
