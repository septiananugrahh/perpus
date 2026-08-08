<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import RetroButton from '@/Components/Retro/RetroButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Log in" />
    <GuestLayout>
        <h2 class="font-cabinet retro-form-title">Selamat Datang</h2>
        <p class="retro-form-subtitle">Login untuk masuk ke dashboard perpustakaan</p>

        <div v-if="status" class="retro-status">{{ status }}</div>

        <form @submit.prevent="submit" class="retro-form">
            <div class="retro-field">
                <label class="retro-label">Email</label>
                <input v-model="form.email" type="email" required autofocus class="retro-input" />
                <span v-if="form.errors.email" class="retro-error">{{ form.errors.email }}</span>
            </div>

            <div class="retro-field">
                <label class="retro-label">Password</label>
                <input v-model="form.password" type="password" required class="retro-input" />
                <span v-if="form.errors.password" class="retro-error">{{ form.errors.password }}</span>
            </div>

            <label class="retro-checkbox-row">
                <input v-model="form.remember" type="checkbox" />
                <span>Ingat saya</span>
            </label>

            <RetroButton variant="primary" as="button" type="submit" :disabled="form.processing" class="retro-submit">
                {{ form.processing ? 'Memproses...' : 'Login' }}
            </RetroButton>

            <Link v-if="canResetPassword" :href="route('password.request')" class="retro-link">
                Lupa password?
            </Link>
        </form>
    </GuestLayout>
</template>


<style scoped>
.retro-form-title {
    font-size: 26px;
    font-weight: 800;
    color: #1a1a1a;
}
.retro-form-subtitle {
    font-size: 14px;
    color: var(--retro-text-secondary);
    margin-top: 4px;
    margin-bottom: 24px;
}

.retro-status {
    background: var(--retro-green);
    border: 2px solid var(--retro-border);
    border-radius: 12px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 16px;
}

.retro-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.retro-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.retro-label {
    font-size: 13px;
    font-weight: 700;
    color: #1a1a1a;
}
.retro-input {
    padding: 12px 16px;
    border: 2px solid var(--retro-border);
    border-radius: 14px;
    font-size: 14px;
    outline: none;
}
.retro-input:focus {
    background: var(--retro-yellow);
}
.retro-error {
    font-size: 12px;
    color: var(--retro-red);
    font-weight: 600;
}

.retro-checkbox-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #1a1a1a;
    cursor: pointer;
}

.retro-submit {
    width: 100%;
    justify-content: center;
    margin-top: 8px;
}

.retro-link {
    text-align: center;
    font-size: 13px;
    color: var(--retro-text-secondary);
    text-decoration: underline;
}
</style>
