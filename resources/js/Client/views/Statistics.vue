<template>
  <div class="text-right">
    <!-- الهيدر -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold tracking-tight text-neutral-900 md:text-3xl">الإحصائيات</h1>
      <p class="mt-1 text-[15px] text-neutral-500">نظرة شاملة على نشاط المحادثات والموظفين</p>
    </div>

    <!-- بطاقات المؤشرات -->
    <div v-if="loading" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div v-for="i in 4" :key="i" class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
        <div class="h-4 w-24 animate-pulse rounded bg-neutral-200" />
        <div class="mt-3 h-8 w-16 animate-pulse rounded bg-neutral-100" />
      </div>
    </div>
    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)] transition-shadow hover:shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[13px] font-medium uppercase tracking-wide text-neutral-500">إجمالي المحادثات</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-neutral-900 md:text-3xl">{{ formatNum(stats.total_conversations) }}</p>
          </div>
          <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1263cf]/10 text-[#1263cf]">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </span>
        </div>
      </div>
      <div class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)] transition-shadow hover:shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[13px] font-medium uppercase tracking-wide text-neutral-500">عملاء محتملون معلّقون</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-amber-600 md:text-3xl">{{ formatNum(stats.pending_leads) }}</p>
          </div>
          <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </span>
        </div>
      </div>
      <div class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)] transition-shadow hover:shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[13px] font-medium uppercase tracking-wide text-neutral-500">رسائل مقروءة</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-emerald-600 md:text-3xl">{{ formatNum(stats.read_messages) }}</p>
          </div>
          <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
        </div>
      </div>
      <div class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)] transition-shadow hover:shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[13px] font-medium uppercase tracking-wide text-neutral-500">رسائل معلّقة</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-rose-600 md:text-3xl">{{ formatNum(stats.pending_messages) }}</p>
          </div>
          <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
        </div>
      </div>
    </div>

    <!-- إجمالي الرسائل (شريط معلومات) -->
    <div v-if="!loading && (stats.total_messages != null)" class="mt-6 rounded-2xl border border-neutral-200/80 bg-[#1263cf]/5 px-6 py-4">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1263cf]/10 text-[#1263cf]">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </span>
          <div>
            <p class="text-[14px] font-medium text-neutral-700">إجمالي الرسائل</p>
            <p class="text-xl font-bold text-[#1263cf]">{{ formatNum(stats.total_messages) }} رسالة</p>
          </div>
        </div>
        <div v-if="stats.total_messages > 0" class="h-2 flex-1 max-w-xs rounded-full bg-[#1263cf]/20 overflow-hidden">
          <div
            class="h-full rounded-full bg-[#1263cf] transition-all duration-500"
            :style="{ width: `${readPercent}%` }"
          />
        </div>
      </div>
    </div>

    <!-- المحادثات لكل موظف -->
    <div class="mt-10">
      <div class="mb-4 flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-neutral-900">المحادثات لكل موظف</h2>
          <p class="mt-0.5 text-[14px] text-neutral-500">توزيع المحادثات المعيّنة على الموظفين</p>
        </div>
      </div>
      <div class="overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
        <div v-if="loading" class="p-6">
          <div class="space-y-4">
            <div v-for="i in 3" :key="i" class="flex items-center gap-4">
              <div class="h-10 w-10 animate-pulse rounded-full bg-neutral-200" />
              <div class="h-4 flex-1 animate-pulse rounded bg-neutral-100" />
              <div class="h-6 w-12 animate-pulse rounded bg-neutral-100" />
            </div>
          </div>
        </div>
        <template v-else>
          <ul class="divide-y divide-neutral-100">
            <li
              v-for="(row, index) in stats.conversations_per_employee"
              :key="row.employee_id"
              class="flex items-center gap-4 px-6 py-4 transition-colors hover:bg-neutral-50/80"
            >
              <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#1263cf]/10 text-[15px] font-semibold text-[#1263cf]">
                {{ index + 1 }}
              </div>
              <div class="min-w-0 flex-1">
                <p class="font-medium text-neutral-900">{{ row.employee_name }}</p>
                <p class="text-[13px] text-neutral-500">{{ row.count }} محادثة</p>
              </div>
              <div class="flex items-center gap-3">
                <div class="h-2 w-24 overflow-hidden rounded-full bg-neutral-100">
                  <div
                    class="h-full rounded-full bg-[#1263cf] transition-all duration-500"
                    :style="{ width: `${employeeBarPercent(row.count)}%` }"
                  />
                </div>
                <span class="w-10 text-left text-lg font-bold text-neutral-900">{{ row.count }}</span>
              </div>
            </li>
          </ul>
          <div v-if="!stats.conversations_per_employee?.length" class="px-6 py-16 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-neutral-100 text-neutral-400">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <p class="mt-4 text-[15px] font-medium text-neutral-600">لا توجد بيانات بعد</p>
            <p class="mt-1 text-[14px] text-neutral-500">ستظهر هنا المحادثات المعيّنة للموظفين</p>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { get } from '../api';

const stats = ref<{
  total_conversations?: number;
  pending_leads?: number;
  read_messages?: number;
  pending_messages?: number;
  total_messages?: number;
  conversations_per_employee?: { employee_id: number; employee_name: string; count: number }[];
}>({});
const loading = ref(true);

const readPercent = computed(() => {
  const total = stats.value.total_messages ?? 0;
  const read = stats.value.read_messages ?? 0;
  if (total <= 0) return 0;
  return Math.round((read / total) * 100);
});

const maxEmployeeCount = computed(() => {
  const rows = stats.value.conversations_per_employee ?? [];
  if (rows.length === 0) return 1;
  return Math.max(...rows.map((r) => r.count), 1);
});

function formatNum(n: number | null | undefined): string {
  if (n == null) return '—';
  return String(n);
}

function employeeBarPercent(count: number): number {
  const max = maxEmployeeCount.value;
  if (max <= 0) return 0;
  return Math.min(100, Math.round((count / max) * 100));
}

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
