<template>
  <div class="text-right">
    <h1 class="mb-6 text-2xl font-semibold text-gray-800">إعدادات الأتمتة</h1>
    <form class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm" @submit.prevent="save">
      <div>
        <label for="welcome_message" class="mb-1 block text-sm font-medium text-gray-700">رسالة الترحيب</label>
        <textarea id="welcome_message" v-model="form.welcome_message" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
      </div>
      <div>
        <label for="auto_assign_after_minutes" class="mb-1 block text-sm font-medium text-gray-700">التعيين التلقائي بعد (دقائق)</label>
        <input id="auto_assign_after_minutes" v-model.number="form.auto_assign_after_minutes" type="number" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
      </div>
      <div>
        <label for="default_employee_id" class="mb-1 block text-sm font-medium text-gray-700">الموظف الافتراضي للتعيين التلقائي</label>
        <select id="default_employee_id" v-model="form.default_employee_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800">
          <option :value="null">— لا يوجد —</option>
          <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }}</option>
        </select>
      </div>
      <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700" :disabled="saving">حفظ</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { get, post } from '../api';

const form = reactive({
  welcome_message: '',
  auto_assign_after_minutes: 2,
  default_employee_id: null as number | null,
});
const employees = ref<any[]>([]);
const saving = ref(false);

onMounted(async () => {
  try {
    const [settingsRes, employeesRes] = await Promise.all([
      get<{ data: any }>('/automation'),
      get<{ data: any[] }>('/employees'),
    ]);
    if (settingsRes.data) {
      form.welcome_message = settingsRes.data.welcome_message ?? '';
      form.auto_assign_after_minutes = settingsRes.data.auto_assign_after_minutes ?? 2;
      form.default_employee_id = settingsRes.data.default_employee_id ?? null;
    }
    employees.value = employeesRes.data ?? [];
  } catch {}
});

async function save() {
  saving.value = true;
  try {
    await post('/automation/update', form);
  } finally {
    saving.value = false;
  }
}
</script>
