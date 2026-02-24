<template>
  <div>
    <div class="mb-6 flex items-center gap-4">
      <router-link :to="{ name: 'companies' }" class="text-sm text-gray-600 hover:text-gray-800">← Companies</router-link>
      <h1 class="text-2xl font-semibold text-gray-800">Add Company</h1>
    </div>
    <form class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm" @submit.prevent="submit">
      <div>
        <label for="name" class="mb-1 block text-sm text-gray-600">Name *</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
        />
        <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
      </div>
      <div>
        <label for="email" class="mb-1 block text-sm text-gray-600">Email *</label>
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
        <label for="phone" class="mb-1 block text-sm text-gray-600">Phone</label>
        <input
          id="phone"
          v-model="form.phone"
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
        />
      </div>
      <div>
        <label for="whatsapp_phone_number_id" class="mb-1 block text-sm text-gray-600">WhatsApp Phone Number ID</label>
        <input
          id="whatsapp_phone_number_id"
          v-model="form.whatsapp_phone_number_id"
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
        />
      </div>
      <div>
        <label for="whatsapp_access_token" class="mb-1 block text-sm text-gray-600">WhatsApp Access Token</label>
        <textarea
          id="whatsapp_access_token"
          v-model="form.whatsapp_access_token"
          rows="3"
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
        />
      </div>
      <div>
        <label for="subscription_status" class="mb-1 block text-sm text-gray-600">Subscription status *</label>
        <select
          id="subscription_status"
          v-model="form.subscription_status"
          required
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
        >
          <option value="active">Active</option>
          <option value="suspended">Suspended</option>
        </select>
      </div>
      <div>
        <label for="subscription_end_date" class="mb-1 block text-sm text-gray-600">Subscription end date</label>
        <input
          id="subscription_end_date"
          v-model="form.subscription_end_date"
          type="date"
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
        />
      </div>
      <button
        type="submit"
        class="rounded bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
        :disabled="saving"
      >
        {{ saving ? 'Saving…' : 'Create Company' }}
      </button>
      <p v-if="errors.submit" class="text-sm text-red-600">{{ errors.submit }}</p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { post } from '../api';

const router = useRouter();
const saving = ref(false);
const form = reactive({
  name: '',
  email: '',
  phone: '',
  whatsapp_phone_number_id: '',
  whatsapp_access_token: '',
  subscription_status: 'active',
  subscription_end_date: '' as string | null,
});
const errors = reactive<Record<string, string>>({});

async function submit() {
  saving.value = true;
  errors.name = '';
  errors.submit = '';
  try {
    const payload: Record<string, unknown> = {
      name: form.name,
      email: form.email,
      subscription_status: form.subscription_status,
    };
    if (form.phone) payload.phone = form.phone;
    if (form.whatsapp_phone_number_id) payload.whatsapp_phone_number_id = form.whatsapp_phone_number_id;
    if (form.whatsapp_access_token) payload.whatsapp_access_token = form.whatsapp_access_token;
    if (form.subscription_end_date) payload.subscription_end_date = form.subscription_end_date;

    await post<{ data: { id: number } }>('/companies', payload);
    router.push({ name: 'companies' });
  } catch (e: unknown) {
    const err = e as { message?: string };
    errors.submit = err?.message ?? 'Failed to create company';
  } finally {
    saving.value = false;
  }
}
</script>
