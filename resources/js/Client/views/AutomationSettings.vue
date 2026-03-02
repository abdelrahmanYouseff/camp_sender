<template>
  <div class="text-right">
    <!-- الهيدر -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold tracking-tight text-neutral-900 md:text-3xl">إعدادات الأتمتة</h1>
      <p class="mt-1 text-[15px] text-neutral-500">رسالة ترحيب تلقائية وتعيين المحادثات للموظفين دون تدخل يدوي</p>
    </div>

    <div v-if="loading" class="max-w-2xl space-y-6">
      <div class="h-48 animate-pulse rounded-2xl bg-neutral-100" />
      <div class="h-32 animate-pulse rounded-2xl bg-neutral-100" />
      <div class="h-24 animate-pulse rounded-2xl bg-neutral-100" />
    </div>

    <form v-else class="max-w-2xl space-y-6" @submit.prevent="save">
      <!-- رسالة الترحيب -->
      <div class="overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
        <div class="border-b border-neutral-100 bg-neutral-50/80 px-6 py-4">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1263cf]/10 text-[#1263cf]">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </span>
            <div>
              <h2 class="font-semibold text-neutral-900">رسالة الترحيب</h2>
              <p class="text-[13px] text-neutral-500">تُرسل تلقائياً للعميل عند أول اتصال</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <label for="welcome_message" class="sr-only">رسالة الترحيب</label>
          <textarea
            id="welcome_message"
            v-model="form.welcome_message"
            rows="4"
            placeholder="مثال: مرحباً بك! سنرد عليك في أقرب وقت."
            class="w-full rounded-xl border border-neutral-200 bg-neutral-50/50 px-4 py-3 text-[15px] text-neutral-900 placeholder-neutral-400 transition-colors focus:border-[#1263cf] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1263cf]/20"
          />
        </div>
      </div>

      <!-- التعيين التلقائي -->
      <div class="overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
        <div class="border-b border-neutral-100 bg-neutral-50/80 px-6 py-4">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </span>
            <div>
              <h2 class="font-semibold text-neutral-900">التعيين التلقائي</h2>
              <p class="text-[13px] text-neutral-500">تعيين المحادثة لموظف بعد مرور وقت محدد دون رد</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <label for="auto_assign_after_minutes" class="mb-2 block text-[14px] font-medium text-neutral-700">بعد (دقائق) بدون رد</label>
          <input
            id="auto_assign_after_minutes"
            v-model.number="form.auto_assign_after_minutes"
            type="number"
            min="0"
            class="w-full max-w-[140px] rounded-xl border border-neutral-200 bg-neutral-50/50 px-4 py-3 text-[15px] text-neutral-900 transition-colors focus:border-[#1263cf] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1263cf]/20"
          />
          <p class="mt-2 text-[13px] text-neutral-500">إذا لم يُرد أحد خلال هذه المدة، تُعيَّن المحادثة للموظف الافتراضي.</p>
        </div>
      </div>

      <!-- الموظف الافتراضي -->
      <div class="overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
        <div class="border-b border-neutral-100 bg-neutral-50/80 px-6 py-4">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </span>
            <div>
              <h2 class="font-semibold text-neutral-900">الموظف الافتراضي</h2>
              <p class="text-[13px] text-neutral-500">يُعيَّن له المحادثات عند تفعيل التعيين التلقائي</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <label for="default_employee_id" class="mb-2 block text-[14px] font-medium text-neutral-700">اختر الموظف</label>
          <select
            id="default_employee_id"
            v-model="form.default_employee_id"
            class="w-full rounded-xl border border-neutral-200 bg-neutral-50/50 px-4 py-3 text-[15px] text-neutral-900 transition-colors focus:border-[#1263cf] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1263cf]/20"
          >
            <option :value="null">— لا تعيين افتراضي —</option>
            <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }}</option>
          </select>
        </div>
      </div>

      <!-- زر الحفظ والتنبيه -->
      <div class="flex flex-wrap items-center gap-4">
        <button
          type="submit"
          class="inline-flex items-center gap-2 rounded-xl bg-[#1263cf] px-6 py-3 text-[15px] font-semibold text-white shadow-[0_2px_8px_rgba(18,99,207,0.3)] transition-colors hover:bg-[#0d4d9e] disabled:opacity-60"
          :disabled="saving"
        >
          <svg v-if="saving" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
          </svg>
          <span>{{ saving ? 'جاري الحفظ…' : 'حفظ الإعدادات' }}</span>
        </button>
        <Transition name="fade">
          <p v-if="saveSuccess" class="flex items-center gap-2 text-[14px] font-medium text-emerald-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            تم حفظ الإعدادات
          </p>
        </Transition>
      </div>
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
const loading = ref(true);
const saving = ref(false);
const saveSuccess = ref(false);

onMounted(async () => {
  loading.value = true;
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
  } catch {
    // ignore
  } finally {
    loading.value = false;
  }
});

async function save() {
  saving.value = true;
  saveSuccess.value = false;
  try {
    await post('/automation/update', form);
    saveSuccess.value = true;
    setTimeout(() => { saveSuccess.value = false; }, 3000);
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
