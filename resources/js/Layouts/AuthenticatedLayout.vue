<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);

const menu = [
    { label: "Dashboard", route: "dashboard" },
    { label: "Peminjaman Buku", route: "dashboard" },
    { label: "Buku", route: "buku.index" },
    { label: "Input Buku", route: "buku.create" },
    { label: "Export", route: "dashboard" },
    { label: "Riwayat", route: "dashboard" },
];
</script>

<template>
    <div class="rb-shell">
        <nav class="rb-nav">
            <div class="rb-nav__top">
                <Link :href="route('dashboard')" class="rb-brand"
                    >PERPUSTAKAAN</Link
                >

                <div class="rb-nav__user">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button type="button" class="rb-user-btn">
                                {{ $page.props.auth.user.name }}
                            </button>
                        </template>
                        <template #content>
                            <div class="rb-dropdown">
                                <DropdownLink :href="route('profile.edit')"
                                    >PROFILE</DropdownLink
                                >
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    >LOG OUT</DropdownLink
                                >
                            </div>
                        </template>
                    </Dropdown>
                </div>

                <button
                    class="rb-burger"
                    @click="
                        showingNavigationDropdown = !showingNavigationDropdown
                    "
                >
                    {{ showingNavigationDropdown ? "CLOSE" : "MENU" }}
                </button>
            </div>

            <div class="rb-nav__menu">
                <Link
                    v-for="item in menu"
                    :key="item.label"
                    :href="route(item.route)"
                    class="rb-nav__link"
                    :class="{
                        'rb-nav__link--active': route().current(item.route),
                    }"
                >
                    {{ item.label }}
                </Link>
            </div>

            <div v-show="showingNavigationDropdown" class="rb-nav__menu-mobile">
                <Link
                    v-for="item in menu"
                    :key="item.label"
                    :href="route(item.route)"
                    class="rb-nav__link"
                >
                    {{ item.label }}
                </Link>
            </div>
        </nav>

        <header class="rb-header" v-if="$slots.header">
            <slot name="header" />
        </header>

        <main class="rb-main">
            <slot />
        </main>
    </div>
</template>

<style scoped>
.rb-shell {
    min-height: 100vh;
    background: #ffffff;
    font-family: "Work Sans", sans-serif;
}

.rb-nav {
    border-bottom: 5px solid #000;
    background: #fff;
}

.rb-nav__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    border-bottom: 1px solid #000;
}

.rb-brand {
    font-family: "Archivo Black", sans-serif;
    font-size: 22px;
    letter-spacing: 1px;
    color: #000;
    text-decoration: none;
    text-transform: uppercase;
}

.rb-nav__user {
    display: none;
}
@media (min-width: 640px) {
    .rb-nav__user {
        display: block;
    }
}

.rb-user-btn {
    font-family: "Space Mono", monospace;
    font-size: 13px;
    text-transform: uppercase;
    background: #fff;
    border: 3px solid #000;
    padding: 8px 16px;
    cursor: pointer;
}
.rb-user-btn:hover {
    background: #000;
    color: #fff;
}

.rb-dropdown {
    border: 3px solid #000;
    background: #fff;
}

.rb-burger {
    border: 3px solid #000;
    background: #fff;
    padding: 8px 14px;
    font-family: "Work Sans", sans-serif;
    font-weight: 600;
    letter-spacing: 1px;
    cursor: pointer;
}
@media (min-width: 640px) {
    .rb-burger {
        display: none;
    }
}

.rb-nav__menu {
    display: none;
    gap: 0;
}
@media (min-width: 640px) {
    .rb-nav__menu {
        display: flex;
    }
}

.rb-nav__menu-mobile {
    display: flex;
    flex-direction: column;
}
@media (min-width: 640px) {
    .rb-nav__menu-mobile {
        display: none;
    }
}

.rb-nav__link {
    font-family: "Work Sans", sans-serif;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-decoration: none;
    color: #000;
    padding: 14px 20px;
    border-right: 1px solid #000;
}
.rb-nav__menu-mobile .rb-nav__link {
    border-right: none;
    border-bottom: 1px solid #000;
}
.rb-nav__link:hover {
    background: #000;
    color: #fff;
}
.rb-nav__link--active {
    background: #000;
    color: #fff;
}

.rb-header {
    border-bottom: 3px solid #000;
    padding: 24px;
}

.rb-main {
    padding: 24px;
    max-width: 1600px;
    margin: 0 auto;
}
</style>
