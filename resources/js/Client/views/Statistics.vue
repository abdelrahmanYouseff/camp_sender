<template>
  <div class="text-right">
    <h1 class="mb-6 text-2xl font-semibold text-gray-800">الإحصائيات</h1>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-gray-500">إجمالي المحادثات</div>
        <div class="mt-1 text-2xl font-semibold text-gray-800">{{ stats.total_conversations ?? '—' }}</div>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-gray-500">العملاء المحتملون المعلّقون</div>
        <div class="mt-1 text-2xl font-semibold text-amber-600">{{ stats.pending_leads ?? '—' }}</div>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-gray-500">الرسائل المقروءة</div>
        <div class="mt-1 text-2xl font-semibold text-green-600">{{ stats.read_messages ?? '—' }}</div>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-gray-500">الرسائل المعلّقة</div>
        <div class="mt-1 text-2xl font-semibold text-red-600">{{ stats.pending_messages ?? '—' }}</div>
      </div>
    </div>
    <div class="mt-8">
      <h2 class="mb-4 text-lg font-medium text-gray-800">المحادثات لكل موظف</h2>
      <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">الموظف</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">العدد</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="row in stats.conversations_per_employee" :key="row.employee_id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-800">{{ row.employee_name }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ row.count }}</td>
            </tr>
            <tr v-if="!stats.conversations_per_employee?.length && !loading">
              <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">لا توجد بيانات</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { get } from '../api';

const stats = ref<any>({});
const loading = ref(true);

onMounted(async () => {
  try {
    stats.value = await get('/statistics');
  } catch {
    stats.value = {};
  } finally {
    loading.value = false;
  }
});
</script>
