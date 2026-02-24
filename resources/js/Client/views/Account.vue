<template>
  <div class="text-right">
    <h1 class="mb-6 text-2xl font-semibold text-gray-800">الحساب</h1>

    <!-- عرض الموظف: اسم الشركة + إيميله فقط -->
    <div v-if="!loading && data && isAgent" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">اسم الشركة</label>
        <p class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800">{{ data.company_name ?? '—' }}</p>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
        <p class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800">{{ data.user_email ?? '—' }}</p>
      </div>
    </div>

    <!-- عرض المدير: نموذج كامل مع الهاتف وواتساب -->
    <form v-else-if="!loading && data && form" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm" @submit.prevent="save">
      <div>
        <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم الشركة</label>
        <input id="name" v-model="form.name" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
      </div>
      <div>
        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني (الشركة)</label>
        <input id="email" v-model="form.email" type="email" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
      </div>
      <div>
        <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">الهاتف</label>
        <input id="phone" v-model="form.phone" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
      </div>
      <div>
        <label for="whatsapp_phone_number_id" class="mb-1 block text-sm font-medium text-gray-700">رقم واتساب (Phone Number ID)</label>
        <input id="whatsapp_phone_number_id" v-model="form.whatsapp_phone_number_id" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
      </div>
      <div>
        <label for="whatsapp_access_token" class="mb-1 block text-sm font-medium text-gray-700">رمز واتساب (Access Token)</label>
        <textarea id="whatsapp_access_token" v-model="form.whatsapp_access_token" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
      </div>
      <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700" :disabled="saving">حفظ</button>
    </form>

    <p v-if="loading" class="text-gray-500">جاري التحميل…</p>
    <p v-else-if="!data" class="text-red-600">الحساب غير موجود.</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { get, post } from '../api';

const data = ref<any>(null);
const form = ref<any>(null);
const loading = ref(true);
const saving = ref(false);

const isAgent = computed(() => data.value?.role === 'agent');

onMounted(async () => {
  try {
    const res = await get<{ data: any }>('/account');
    data.value = res.data ?? null;
    if (res.data?.company) {
      form.value = { ...res.data.company };
    } else {
      form.value = null;
    }
  } catch {
    data.value = null;
    form.value = null;
  } finally {
    loading.value = false;
  }
});

async function save() {
  if (!form.value) return;
  saving.value = true;
  try {
    const res = await post<{ data: any }>('/account/update', form.value);
    if (res.data) {
      data.value = res.data;
      if (res.data.company) form.value = { ...res.data.company };
    }
  } finally {
    saving.value = false;
  }
}
</script>
