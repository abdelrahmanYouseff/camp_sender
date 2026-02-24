<template>
  <div class="text-right">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold text-gray-800">الموظفون</h1>
      <button
        type="button"
        class="rounded bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
        @click="showAddForm = !showAddForm"
      >
        {{ showAddForm ? 'إلغاء' : 'إضافة موظف' }}
      </button>
    </div>

    <!-- نموذج إضافة موظف -->
    <div v-show="showAddForm" class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-medium text-gray-800">إضافة موظف جديد</h2>
      <form class="max-w-xl space-y-4" @submit.prevent="submitAdd">
        <div>
          <label for="add-name" class="mb-1 block text-sm font-medium text-gray-700">الاسم *</label>
          <input id="add-name" v-model="addForm.name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
          <p v-if="addErrors.name" class="mt-1 text-sm text-red-600">{{ addErrors.name }}</p>
        </div>
        <div>
          <label for="add-email" class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني *</label>
          <input id="add-email" v-model="addForm.email" type="email" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
          <p v-if="addErrors.email" class="mt-1 text-sm text-red-600">{{ addErrors.email }}</p>
        </div>
        <div>
          <label for="add-password" class="mb-1 block text-sm font-medium text-gray-700">كلمة المرور *</label>
          <input id="add-password" v-model="addForm.password" type="password" required minlength="8" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800" />
          <p v-if="addErrors.password" class="mt-1 text-sm text-red-600">{{ addErrors.password }}</p>
        </div>
        <div>
          <label for="add-role" class="mb-1 block text-sm font-medium text-gray-700">الصلاحية *</label>
          <select id="add-role" v-model="addForm.role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-800">
            <option value="agent">رد على الرسائل فقط</option>
            <option value="employee">موظف</option>
            <option value="company_admin">مدير شركة</option>
          </select>
          <p class="mt-1 text-xs text-gray-500">«رد على الرسائل فقط»: يقتصر على الرد على محادثات العملاء دون صلاحيات إدارية.</p>
        </div>
        <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700" :disabled="addSaving">
          {{ addSaving ? 'جاري الحفظ…' : 'إضافة' }}
        </button>
        <p v-if="addErrors.submit" class="text-sm text-red-600">{{ addErrors.submit }}</p>
      </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">الاسم</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">البريد الإلكتروني</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">الصلاحية</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">الحالة</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">إجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          <tr v-for="emp in employees" :key="emp.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-800">{{ emp.name }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ emp.email }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ roleLabel(emp.role) }}</td>
            <td class="px-4 py-3">
              <span :class="emp.status === 'active' ? 'text-green-600' : 'text-red-600'" class="text-sm font-medium">{{ emp.status === 'active' ? 'نشط' : 'غير نشط' }}</span>
            </td>
            <td class="px-4 py-3 text-left text-sm">
              <button type="button" class="ml-2 text-gray-700 underline hover:text-gray-900">تعديل</button>
              <button type="button" class="text-gray-700 underline hover:text-gray-900">تفعيل / إيقاف</button>
            </td>
          </tr>
          <tr v-if="employees.length === 0 && !loading">
            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">لا يوجد موظفون</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { get, post } from '../api';

const employees = ref<any[]>([]);
const loading = ref(true);
const showAddForm = ref(false);
const addSaving = ref(false);
const addForm = reactive({
  name: '',
  email: '',
  password: '',
  role: 'agent',
});
const addErrors = reactive<Record<string, string>>({});

function roleLabel(role: string): string {
  if (role === 'company_admin') return 'مدير شركة';
  if (role === 'agent') return 'رد على الرسائل فقط';
  return 'موظف';
}

async function fetchEmployees() {
  loading.value = true;
  try {
    const res = await get<{ data: any[] }>('/employees');
    employees.value = res.data ?? [];
  } catch {
    employees.value = [];
  } finally {
    loading.value = false;
  }
}

async function submitAdd() {
  addSaving.value = true;
  addErrors.name = '';
  addErrors.email = '';
  addErrors.password = '';
  addErrors.submit = '';
  try {
    await post('/employees', {
      name: addForm.name,
      email: addForm.email,
      password: addForm.password,
      role: addForm.role,
    });
    addForm.name = '';
    addForm.email = '';
    addForm.password = '';
    addForm.role = 'agent';
    showAddForm.value = false;
    await fetchEmployees();
  } catch (e: unknown) {
    const err = e as { message?: string };
    addErrors.submit = err?.message ?? 'فشل في إضافة الموظف';
  } finally {
    addSaving.value = false;
  }
}

onMounted(fetchEmployees);
</script>
