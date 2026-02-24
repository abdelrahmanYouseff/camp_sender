<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-100">
    <div class="w-full max-w-sm rounded-lg border border-gray-200 bg-white p-6 shadow">
      <h1 class="mb-4 text-xl font-semibold text-gray-800">Admin Login</h1>
      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label for="email" class="mb-1 block text-sm text-gray-600">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
          />
          <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
        </div>
        <div>
          <label for="password" class="mb-1 block text-sm text-gray-600">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
          />
          <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
        </div>
        <button
          type="submit"
          class="w-full rounded bg-gray-800 py-2 text-sm font-medium text-white hover:bg-gray-700"
          :disabled="loading"
        >
          {{ loading ? 'Signing in…' : 'Sign in' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const loading = ref(false);
const form = reactive({ email: '', password: '' });
const errors = reactive<Record<string, string>>({});

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function submit() {
  loading.value = true;
  errors.email = '';
  errors.password = '';
  try {
    const r = await fetch('/admin/login', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken ?? '',
      },
      body: JSON.stringify(form),
    });
    const data = await r.json().catch(() => ({}));
    if (!r.ok) {
      errors.email = data?.message ?? data?.errors?.email?.[0] ?? 'Invalid credentials';
      return;
    }
    const redirect = (route.query.redirect as string) || '/admin/dashboard';
    window.location.href = redirect;
  } catch (e: unknown) {
    const err = e as { message?: string };
    errors.email = err?.message ?? 'Invalid credentials';
  } finally {
    loading.value = false;
  }
}
</script>
