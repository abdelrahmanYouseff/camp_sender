<template>
  <div class="text-right">
    <h1 class="mb-6 text-2xl font-semibold text-gray-800">العملاء المحتملون</h1>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- جدول المهتمين -->
      <div class="overflow-hidden rounded-lg border border-emerald-200 bg-white shadow">
        <div class="border-b border-emerald-100 bg-emerald-50 px-4 py-3">
          <h2 class="text-lg font-semibold text-emerald-800">مهتمون</h2>
          <p class="text-sm text-emerald-700">إجمالي: <strong>{{ interestedLeads.length }}</strong></p>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">العميل</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">الهاتف</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">الحالة</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">المُعيَّن</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">إجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="lead in interestedLeads" :key="lead.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-800">{{ lead.customer_name ?? '—' }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ lead.customer_phone }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ lead.status === 'assigned' ? 'معيَّن' : 'غير معيَّن' }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ lead.assigned_employee?.name ?? '—' }}</td>
              <td class="px-4 py-3 text-left text-sm">
                <template v-if="editingInterestId === lead.id">
                  <button type="button" class="ml-2 text-amber-700 underline hover:text-amber-900" @click="setInterest(lead, 'not_interested')">تحويل لغير مهتم</button>
                  <button type="button" class="ml-2 text-gray-500 underline hover:text-gray-700" @click="editingInterestId = null">إلغاء</button>
                </template>
                <template v-else>
                  <button v-if="lead.status !== 'assigned'" type="button" class="ml-2 text-gray-700 underline hover:text-gray-900" @click="openAssign(lead)">تعيين</button>
                  <button type="button" class="ml-2 text-gray-700 underline hover:text-gray-900">تم</button>
                  <button v-if="lead.conversation_id" type="button" class="ml-2 text-gray-700 underline hover:text-gray-900" @click="editingInterestId = lead.id">تعديل</button>
                </template>
              </td>
            </tr>
            <tr v-if="interestedLeads.length === 0 && !loading">
              <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">لا يوجد مهتمون</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- جدول غير المهتمين -->
      <div class="overflow-hidden rounded-lg border border-amber-200 bg-white shadow">
        <div class="border-b border-amber-100 bg-amber-50 px-4 py-3">
          <h2 class="text-lg font-semibold text-amber-800">غير مهتمين</h2>
          <p class="text-sm text-amber-700">إجمالي: <strong>{{ notInterestedLeads.length }}</strong></p>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">العميل</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">الهاتف</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">الحالة</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">المُعيَّن</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">إجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="lead in notInterestedLeads" :key="lead.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-800">{{ lead.customer_name ?? '—' }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ lead.customer_phone }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ lead.status === 'assigned' ? 'معيَّن' : 'غير معيَّن' }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ lead.assigned_employee?.name ?? '—' }}</td>
              <td class="px-4 py-3 text-left text-sm">
                <template v-if="editingInterestId === lead.id">
                  <button type="button" class="ml-2 text-emerald-700 underline hover:text-emerald-900" @click="setInterest(lead, 'interested')">تحويل لمهتم</button>
                  <button type="button" class="ml-2 text-gray-500 underline hover:text-gray-700" @click="editingInterestId = null">إلغاء</button>
                </template>
                <template v-else>
                  <button v-if="lead.status !== 'assigned'" type="button" class="ml-2 text-gray-700 underline hover:text-gray-900" @click="openAssign(lead)">تعيين</button>
                  <button type="button" class="ml-2 text-gray-700 underline hover:text-gray-900">تم</button>
                  <button v-if="lead.conversation_id" type="button" class="ml-2 text-gray-700 underline hover:text-gray-900" @click="editingInterestId = lead.id">تعديل</button>
                </template>
              </td>
            </tr>
            <tr v-if="notInterestedLeads.length === 0 && !loading">
              <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">لا يوجد غير مهتمين</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { get, patch } from '../api';

const leads = ref<any[]>([]);
const loading = ref(true);
const editingInterestId = ref<number | null>(null);

const interestedLeads = computed(() => leads.value.filter((l) => l.interest === 'interested'));
const notInterestedLeads = computed(() => leads.value.filter((l) => l.interest === 'not_interested'));

function openAssign(lead: any) {
  // Placeholder: open modal or form to select employee
}

async function setInterest(lead: any, interest: 'interested' | 'not_interested') {
  try {
    await patch<{ data: { interest: string } }>(`/leads/${lead.id}/interest`, { interest });
    lead.interest = interest;
    editingInterestId.value = null;
  } catch {
    // يمكن إظهار رسالة خطأ للمستخدم
  }
}

async function fetchLeads() {
  loading.value = true;
  try {
    const res = await get<{ data: any[] }>('/leads');
    leads.value = res.data ?? [];
  } catch {
    leads.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(fetchLeads);
</script>
