<script setup>
import { Head, Link } from '@inertiajs/vue3';
import RetroButton from '@/Components/Retro/RetroButton.vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});

const features = [
    { icon: '📖', title: 'Katalog Lengkap', desc: 'Kelola ribuan judul buku dengan data yang rapi dan terstruktur.', color: 'pink' },
    { icon: '🔄', title: 'Peminjaman Realtime', desc: 'Pantau status pinjam, terlambat, dan pengembalian secara langsung.', color: 'cyan' },
    { icon: '📊', title: 'Laporan & Statistik', desc: 'Lihat buku paling sering dipinjam dan tren peminjaman harian.', color: 'yellow' },
];
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="retro-welcome">
        <header class="retro-welcome__header">
            <div class="retro-welcome__brand">
                <div class="retro-welcome__logo">📚</div>
                <span class="font-cabinet retro-welcome__title">LibraryHub</span>
            </div>
            <div class="retro-welcome__actions">
                <Link v-if="canLogin" :href="route('login')">
                    <RetroButton variant="secondary">Login</RetroButton>
                </Link>
                <Link v-if="canRegister" :href="route('register')">
                    <RetroButton variant="primary">Daftar</RetroButton>
                </Link>
            </div>
        </header>

        <section class="retro-hero">
            <h1 class="font-cabinet retro-hero__title">
                Sistem Perpustakaan yang <span class="retro-hero__highlight">Rapi &amp; Menyenangkan</span>
            </h1>
            <p class="retro-hero__subtitle">
                Kelola induk buku, peminjaman, dan laporan perpustakaan dalam satu dashboard yang simpel.
            </p>
            <Link v-if="canLogin" :href="route('login')">
                <RetroButton variant="color" color="yellow" class="retro-hero__cta">Masuk ke Dashboard →</RetroButton>
            </Link>
        </section>

        <section class="retro-features">
            <div v-for="f in features" :key="f.title" class="retro-feature-card">
                <div class="retro-feature-card__icon" :style="{ background: `var(--retro-${f.color})` }">
                    {{ f.icon }}
                </div>
                <h3 class="font-cabinet retro-feature-card__title">{{ f.title }}</h3>
                <p class="retro-feature-card__desc">{{ f.desc }}</p>
            </div>
        </section>
    </div>
</template>

<style scoped>
.retro-welcome {
    min-height: 100vh;
    background: var(--retro-bg);
}

.retro-welcome__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 40px;
    border-bottom: 2px solid var(--retro-border);
    background: #fff;
}
.retro-welcome__brand {
    display: flex;
    align-items: center;
    gap: 10px;
}
.retro-welcome__logo {
    width: 44px;
    height: 44px;
    background: var(--retro-orange);
    border-radius: 12px;
    border: 2px solid var(--retro-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.retro-welcome__title {
    font-size: 22px;
    font-weight: 800;
    color: #1a1a1a;
}
.retro-welcome__actions {
    display: flex;
    gap: 12px;
}

.retro-hero {
    max-width: 780px;
    margin: 0 auto;
    padding: 80px 24px 60px;
    text-align: center;
}
.retro-hero__title {
    font-size: 44px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.15;
}
.retro-hero__highlight {
    background: var(--retro-pink);
    padding: 0 8px;
    border-radius: 8px;
    border: 2px solid var(--retro-border);
    display: inline-block;
}
.retro-hero__subtitle {
    margin-top: 20px;
    font-size: 16px;
    color: var(--retro-text-secondary);
}
.retro-hero__cta {
    margin-top: 32px;
}

.retro-features {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 24px 80px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}
@media (min-width: 768px) {
    .retro-features { grid-template-columns: repeat(3, 1fr); }
}

.retro-feature-card {
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 4px 4px 0 0 var(--retro-border);
    border-radius: 24px;
    padding: 24px;
}
.retro-feature-card__icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    border: 2px solid var(--retro-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 16px;
}
.retro-feature-card__title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 6px;
}
.retro-feature-card__desc {
    font-size: 14px;
    color: var(--retro-text-secondary);
    line-height: 1.5;
}
</style>
