<template>
  <div>
    <div class="mb-6 flex items-center gap-4">
      <router-link :to="{ name: 'companies' }" class="text-sm text-gray-600 hover:text-gray-800">← Companies</router-link>
      <h1 class="text-2xl font-semibold text-gray-800">Edit Company</h1>
    </div>
    <form v-if="form" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm" @submit.prevent="submit">
      <div>
        <label for="name" class="mb-1 block text-sm text-gray-600">Name</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
        />
      </div>
      <div>
        <label for="email" class="mb-1 block text-sm text-gray-600">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
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
        <label for="webhook_verify_token" class="mb-1 block text-sm text-gray-600">Webhook Verify Token (رمز التحقق من الويب هوك)</label>
        <input
          id="webhook_verify_token"
          v-model="form.webhook_verify_token"
          type="text"
          placeholder="النص الذي تضعه في Meta عند إعداد Callback URL"
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800 placeholder:text-gray-400"
        />
        <p class="mt-1 text-xs text-gray-500">يجب أن يطابق القيمة المُدخلة في حقل Verify token في إعدادات واتساب بـ Meta.</p>
      </div>
      <div>
        <label for="subscription_status" class="mb-1 block text-sm text-gray-600">Subscription status</label>
        <select
          id="subscription_status"
          v-model="form.subscription_status"
          class="w-full rounded border border-gray-300 px-3 py-2 text-gray-800"
        >
          <option value="active">active</option>
          <option value="suspended">suspended</option>
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
        {{ saving ? 'Saving…' : 'Save' }}
      </button>
    </form>
    <p v-else-if="loading" class="text-gray-500">Loading…</p>
    <p v-else class="text-red-600">Company not found.</p>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { get, put } from '../api';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const saving = ref(false);
const form = ref<{
  name: string;
  email: string;
  phone: string | null;
  whatsapp_phone_number_id: string | null;
  whatsapp_business_account_id: string | null;
  whatsapp_access_token: string | null;
  webhook_verify_token: string | null;
  subscription_status: string;
  subscription_end_date: string | null;
} | null>(null);

const id = computed(() => Number(route.params.id));

onMounted(async () => {
  try {
    const res = await get<{ data: typeof form.value }>(`/companies/${id.value}`);
    form.value = res.data ? { ...res.data } : null;
  } catch {
    form.value = null;
  } finally {
    loading.value = false;
  }
});

async function submit() {
  if (!form.value) return;
  saving.value = true;
  try {
    await put(`/companies/${id.value}`, form.value);
    router.push({ name: 'companies' });
  } finally {
    saving.value = false;
  }
}
</script>
