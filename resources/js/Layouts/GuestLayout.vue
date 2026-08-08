<template>
    <div class="retro-guest-shell">
        <div class="retro-guest-brand">
            <div class="retro-guest-logo">📚</div>
            <span class="font-cabinet retro-guest-title">LibraryHub</span>
        </div>

        <div class="retro-guest-card">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.retro-guest-shell {
    min-height: 100vh;
    background: var(--retro-bg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.retro-guest-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 32px;
}
.retro-guest-logo {
    width: 48px;
    height: 48px;
    background: var(--retro-orange);
    border-radius: 14px;
    border: 2px solid var(--retro-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.retro-guest-title {
    font-size: 26px;
    font-weight: 800;
    color: #1a1a1a;
}

.retro-guest-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border: 2px solid var(--retro-border);
    box-shadow: 8px 8px 0 0 var(--retro-border);
    border-radius: 32px;
    padding: 32px;
}
</style>
