<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/Retro/RetroDropdownLink.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);

const menu = [
    { label: "Dashboard", route: "dashboard" },
    { label: "Peminjaman Buku", route: "peminjaman.index" },
    { label: "Inventory", route: "buku.index" },
    { label: "Input Buku", route: "buku.create" },
    { label: "Santri List", route: "santri.index" },
    { label: "Guru List", route: "guru.index" },
    { label: "Reports", route: "dashboard" },
];
</script>

<template>
    <div class="retro-shell">
        <header class="retro-header">
            <div class="retro-header__brand">
                <div class="retro-header__logo">📚</div>
                <span class="font-cabinet retro-header__title"
                    >LibraryHub Admin</span
                >
            </div>

            <nav class="retro-header__nav">
                <Link
                    v-for="item in menu"
                    :key="item.label"
                    :href="route(item.route)"
                    class="retro-header__link"
                    :class="{
                        'retro-header__link--active': route().current(
                            item.route,
                        ),
                    }"
                >
                    {{ item.label }}
                </Link>
            </nav>

            <div class="retro-header__actions">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <div class="retro-avatar">
                            <img
                                src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix"
                                alt="User"
                            />
                        </div>
                    </template>
                    <template #content>
                        <div class="retro-dropdown">
                            <DropdownLink :href="route('profile.edit')"
                                >Profile</DropdownLink
                            >
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                >Log Out</DropdownLink
                            >
                        </div>
                    </template>
                </Dropdown>

                <button
                    class="retro-burger"
                    @click="
                        showingNavigationDropdown = !showingNavigationDropdown
                    "
                >
                    ☰
                </button>
            </div>
        </header>

        <div v-show="showingNavigationDropdown" class="retro-nav-mobile">
            <Link
                v-for="item in menu"
                :key="item.label"
                :href="route(item.route)"
                class="retro-header__link"
            >
                {{ item.label }}
            </Link>
        </div>

        <div v-if="$slots.header" class="retro-page-header">
            <slot name="header" />
        </div>

        <main class="retro-main">
            <slot />
        </main>
    </div>
</template>

<style scoped>
.retro-shell {
    min-height: 100vh;
    background: var(--retro-bg);
}

.retro-header {
    width: 100%;
    height: 80px;
    border-bottom: 2px solid var(--retro-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 32px;
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 50;
}

.retro-header__brand {
    display: flex;
    align-items: center;
    gap: 8px;
}
.retro-header__logo {
    width: 40px;
    height: 40px;
    background: var(--retro-orange);
    border-radius: 12px;
    border: 2px solid var(--retro-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.retro-header__title {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.5px;
    color: #1a1a1a;
}

.retro-header__nav {
    display: none;
    align-items: center;
    gap: 40px;
}
@media (min-width: 1024px) {
    .retro-header__nav {
        display: flex;
    }
}
.retro-header__link {
    color: #999;
    text-decoration: none;
    font-weight: 500;
    padding-bottom: 4px;
}
.retro-header__link:hover {
    color: #1a1a1a;
}
.retro-header__link--active {
    color: #1a1a1a;
    font-weight: 700;
    border-bottom: 2px solid var(--retro-pink);
}

.retro-header__actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.retro-avatar {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    overflow: hidden;
    background: #eee;
    cursor: pointer;
}
.retro-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.retro-dropdown {
    border: 2px solid var(--retro-border);
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
}

.retro-burger {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 2px solid var(--retro-border);
    background: #fff;
    cursor: pointer;
}
@media (min-width: 1024px) {
    .retro-burger {
        display: none;
    }
}

.retro-nav-mobile {
    display: flex;
    flex-direction: column;
    padding: 16px 32px;
    border-bottom: 2px solid var(--retro-border);
    background: #fff;
}
@media (min-width: 1024px) {
    .retro-nav-mobile {
        display: none;
    }
}
.retro-nav-mobile .retro-header__link {
    padding: 10px 0;
}

.retro-page-header {
    padding: 32px 32px 0;
    max-width: 1400px;
    margin: 0 auto;
}

.retro-main {
    padding: 32px;
    max-width: 1400px;
    margin: 0 auto;
}
</style>
