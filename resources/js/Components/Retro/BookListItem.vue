<script setup>
defineProps({
    judul: { type: String, required: true },
    pengarang: { type: String, default: '' },
    tahun: { type: String, default: '' },
    isbn: { type: String, default: '' },
    status: { type: String, default: 'Tersedia' },
});
</script>

<template>
    <div class="retro-book-item">
        <div class="retro-book-item__info">
            <h4 class="retro-book-item__title">{{ judul }}</h4>
            <p class="retro-book-item__meta">
                {{ pengarang || '-' }} • {{ tahun || '-' }}<span v-if="isbn"> • ISBN: {{ isbn }}</span>
            </p>
        </div>
        <div class="retro-book-item__status">
            <span>{{ status }}</span>
        </div>
    </div>
</template>

<style scoped>
.retro-book-item {
    border-bottom: 1px solid #e5e5e5;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background 0.1s ease;
}
.retro-book-item:hover {
    background: #fafafa;
}
.retro-book-item:last-child {
    border-bottom: none;
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
.retro-book-item__status {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a1a;
    white-space: nowrap;
}
</style>
