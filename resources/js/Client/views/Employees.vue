<template>
  <div class="text-right">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">الموظفون</h1>
      <button
        type="button"
        class="rounded-[10px] bg-blue-500 px-4 py-2.5 text-[15px] font-medium text-white transition-colors hover:bg-blue-600 active:scale-[0.98]"
        @click="showAddForm = !showAddForm"
      >
        {{ showAddForm ? 'إلغاء' : 'إضافة موظف' }}
      </button>
    </div>

    <!-- نموذج إضافة موظف -->
    <div v-show="showAddForm" class="mb-6 rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
      <h2 class="mb-4 text-lg font-semibold tracking-tight text-neutral-900">إضافة موظف جديد</h2>
      <form class="max-w-xl space-y-4" @submit.prevent="submitAdd">
        <div>
          <label for="add-name" class="mb-1.5 block text-[15px] font-medium text-neutral-700">الاسم *</label>
          <input id="add-name" v-model="addForm.name" type="text" required class="w-full rounded-[10px] border border-neutral-200 bg-neutral-50/80 px-4 py-2.5 text-[15px] text-neutral-900 focus:border-blue-500/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
          <p v-if="addErrors.name" class="mt-1 text-[14px] text-red-600">{{ addErrors.name }}</p>
        </div>
        <div>
          <label for="add-email" class="mb-1.5 block text-[15px] font-medium text-neutral-700">البريد الإلكتروني *</label>
          <input id="add-email" v-model="addForm.email" type="email" required class="w-full rounded-[10px] border border-neutral-200 bg-neutral-50/80 px-4 py-2.5 text-[15px] text-neutral-900 focus:border-blue-500/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
          <p v-if="addErrors.email" class="mt-1 text-[14px] text-red-600">{{ addErrors.email }}</p>
        </div>
        <div>
          <label for="add-password" class="mb-1.5 block text-[15px] font-medium text-neutral-700">كلمة المرور *</label>
          <input id="add-password" v-model="addForm.password" type="password" required minlength="8" class="w-full rounded-[10px] border border-neutral-200 bg-neutral-50/80 px-4 py-2.5 text-[15px] text-neutral-900 focus:border-blue-500/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
          <p v-if="addErrors.password" class="mt-1 text-[14px] text-red-600">{{ addErrors.password }}</p>
        </div>
        <div>
          <label for="add-role" class="mb-1.5 block text-[15px] font-medium text-neutral-700">الصلاحية *</label>
          <select id="add-role" v-model="addForm.role" required class="w-full rounded-[10px] border border-neutral-200 bg-neutral-50/80 px-4 py-2.5 text-[15px] text-neutral-900 focus:border-blue-500/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="agent">رد على الرسائل فقط</option>
            <option value="employee">موظف</option>
            <option value="company_admin">مدير شركة</option>
          </select>
          <p class="mt-1.5 text-[13px] text-neutral-500">«رد على الرسائل فقط»: يقتصر على الرد على محادثات العملاء دون صلاحيات إدارية.</p>
        </div>
        <button type="submit" class="rounded-[10px] bg-blue-500 px-4 py-2.5 text-[15px] font-medium text-white transition-colors hover:bg-blue-600 active:scale-[0.98] disabled:opacity-60" :disabled="addSaving">
          {{ addSaving ? 'جاري الحفظ…' : 'إضافة' }}
        </button>
        <p v-if="addErrors.submit" class="text-[14px] text-red-600">{{ addErrors.submit }}</p>
      </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-neutral-200/60 bg-[#f5f5f7]/80">
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">الاسم</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">البريد الإلكتروني</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">الصلاحية</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">الحالة</th>
            <th class="px-5 py-4 text-left text-[13px] font-semibold tracking-tight text-neutral-500">إجراءات</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <tr
            v-for="emp in employees"
            :key="emp.id"
            class="border-b border-neutral-100 transition-colors duration-150 last:border-b-0 hover:bg-neutral-50/80"
          >
            <td class="px-5 py-4 text-[15px] font-medium text-neutral-900">{{ emp.name }}</td>
            <td class="px-5 py-4 text-[15px] text-neutral-600">{{ emp.email }}</td>
            <td class="px-5 py-4 text-[15px] text-neutral-600">{{ roleLabel(emp.role) }}</td>
            <td class="px-5 py-4">
              <span :class="emp.status === 'active' ? 'text-emerald-600 font-medium' : 'text-red-600 font-medium'" class="text-[15px]">{{ emp.status === 'active' ? 'نشط' : 'غير نشط' }}</span>
            </td>
            <td class="px-5 py-4 text-left">
              <button type="button" class="ml-2 inline-flex items-center rounded-[10px] px-3 py-1.5 text-[15px] font-medium text-blue-500 transition-colors hover:bg-blue-500/10 hover:text-blue-600">تعديل</button>
              <button type="button" class="inline-flex items-center rounded-[10px] px-3 py-1.5 text-[15px] font-medium text-blue-500 transition-colors hover:bg-blue-500/10 hover:text-blue-600">تفعيل / إيقاف</button>
            </td>
          </tr>
          <tr v-if="employees.length === 0 && !loading">
            <td colspan="5" class="px-5 py-12 text-center text-[15px] text-neutral-500">لا يوجد موظفون</td>
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
