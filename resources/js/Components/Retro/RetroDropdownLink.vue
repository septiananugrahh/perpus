<script setup>
defineProps({
    href: { type: String, default: "" },
    method: { type: String, default: "get" },
    as: { type: String, default: "a" },
});
</script>

<template>
    <Link :href="href" :method="method" :as="as" class="retro-dropdown-link">
        <slot />
    </Link>
</template>

<script>
import { Link } from "@inertiajs/vue3";
export default { components: { Link } };
</script>

<style scoped>
.retro-dropdown-link {
    display: block;
    width: 100%;
    text-align: left;
    padding: 12px 20px;
    font-size: 13px;
    font-weight: 600;
    color: #1a1a1a;
    text-decoration: none;
    background: #fff;
    border: none;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}
.retro-dropdown-link:last-child {
    border-bottom: none;
}
.retro-dropdown-link:hover {
    background: var(--retro-yellow);
}
</style>
a
