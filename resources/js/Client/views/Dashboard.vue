<template>
  <div class="text-right">
    <h1 class="mb-6 text-2xl font-semibold text-gray-800">لوحة التحكم</h1>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="h-10 w-10 animate-spin rounded-full border-2 border-gray-800 border-t-transparent" />
    </div>

    <template v-else>
      <!-- بطاقات الإجماليات -->
      <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="text-sm font-medium text-gray-500">إجمالي الرسائل</div>
          <div class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ totalMessages ?? '—' }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="text-sm font-medium text-gray-500">إجمالي الموظفين</div>
          <div class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ totalEmployees ?? '—' }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="text-sm font-medium text-gray-500">إجمالي العملاء المحتملين</div>
          <div class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ totalLeads ?? '—' }}</div>
        </div>
      </div>

      <!-- رسوم بيانية -->
      <div class="mb-8 grid gap-6 lg:grid-cols-5">
        <!-- الرسم البياني الشهري -->
        <div class="lg:col-span-3 rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-4 text-base font-semibold text-gray-800">الرسائل كل شهر</h2>
          <div class="h-72">
            <canvas ref="barCanvas" />
          </div>
        </div>
        <!-- توزيع الرسائل (عميل / ردود) -->
        <div class="lg:col-span-2 rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-4 text-base font-semibold text-gray-800">توزيع الرسائل</h2>
          <div class="mx-auto h-64 w-64">
            <canvas ref="doughnutCanvas" />
          </div>
          <div class="mt-2 flex justify-center gap-6 text-sm">
            <span class="flex items-center gap-1.5">
              <span class="h-3 w-3 rounded-full bg-[#0ea5e9]" /> رسائل العملاء
            </span>
            <span class="flex items-center gap-1.5">
              <span class="h-3 w-3 rounded-full bg-[#10b981]" /> الردود
            </span>
          </div>
        </div>
      </div>

      <!-- جدول التفاعلات -->
      <section class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-4 text-base font-semibold text-gray-800">التفاعلات (المحادثات والرسائل والردود)</h2>
        <div class="overflow-hidden rounded-xl border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">العميل</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">الهاتف</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">رسائل العميل</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">الردود</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">آخر نشاط</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">إجراء</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="row in interactions" :key="row.id" class="hover:bg-gray-50/80">
                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ row.customer_name ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ row.customer_phone }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ row.customer_messages }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ row.agent_messages }}</td>
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ formatDate(row.last_message_at) }}</td>
                <td class="px-4 py-3 text-left text-sm">
                  <router-link :to="{ name: 'conversation', params: { id: row.id } }" class="font-medium text-emerald-600 hover:text-emerald-700">عرض</router-link>
                </td>
              </tr>
              <tr v-if="!interactions?.length">
                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">لا توجد تفاعلات</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { get } from '../api';
import Chart from 'chart.js/auto';

const totalMessages = ref<number | null>(null);
const totalEmployees = ref<number | null>(null);
const totalLeads = ref<number | null>(null);
const totalCustomerMessages = ref(0);
const totalAgentMessages = ref(0);
const messagesPerMonth = ref<Array<{ year: number; month: number; count: number; label: string }>>([]);
const interactions = ref<Array<{
  id: number;
  customer_name: string | null;
  customer_phone: string;
  customer_messages: number;
  agent_messages: number;
  last_message_at: string | null;
}>>([]);
const loading = ref(true);

const barCanvas = ref<HTMLCanvasElement | null>(null);
const doughnutCanvas = ref<HTMLCanvasElement | null>(null);
let barChart: Chart | null = null;
let doughnutChart: Chart | null = null;

function formatDate(iso: string | null | undefined): string {
  if (!iso) return '—';
  try {
    const d = new Date(iso);
    return d.toLocaleString('ar-EG', { dateStyle: 'short', timeStyle: 'short' });
  } catch {
    return '—';
  }
}

function buildCharts() {
  if (barChart) {
    barChart.destroy();
    barChart = null;
  }
  if (doughnutChart) {
    doughnutChart.destroy();
    doughnutChart = null;
  }

  const labels = messagesPerMonth.value.map((r) => r.label);
  const counts = messagesPerMonth.value.map((r) => r.count);

  if (barCanvas.value && labels.length) {
    barChart = new Chart(barCanvas.value, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'عدد الرسائل',
            data: counts,
            backgroundColor: 'rgba(16, 185, 129, 0.75)',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 1,
            borderRadius: 6,
            borderSkipped: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: { top: 12, right: 12, bottom: 12, left: 12 } },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(255,255,255,0.96)',
            titleColor: '#111',
            bodyColor: '#374151',
            borderColor: '#e5e7eb',
            borderWidth: 1,
            padding: 12,
            displayColors: true,
            callbacks: {
              label: (ctx) => `الرسائل: ${ctx.raw}`,
            },
          },
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              maxRotation: 45,
              font: { size: 11 },
              color: '#6b7280',
            },
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.06)' },
            ticks: {
              font: { size: 11 },
              color: '#6b7280',
              stepSize: 1,
            },
          },
        },
      },
    });
  }

  const customer = totalCustomerMessages.value;
  const agent = totalAgentMessages.value;
  if (doughnutCanvas.value && (customer > 0 || agent > 0)) {
    doughnutChart = new Chart(doughnutCanvas.value, {
      type: 'doughnut',
      data: {
        labels: ['رسائل العملاء', 'الردود'],
        datasets: [
          {
            data: [customer, agent],
            backgroundColor: ['#0ea5e9', '#10b981'],
            borderColor: '#fff',
            borderWidth: 2,
            hoverOffset: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '58%',
        layout: { padding: 8 },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(255,255,255,0.96)',
            titleColor: '#111',
            bodyColor: '#374151',
            borderColor: '#e5e7eb',
            borderWidth: 1,
            padding: 12,
            callbacks: {
              label: (ctx) => {
                const total = ctx.dataset.data.reduce((a: number, b: number) => a + b, 0);
                const pct = total ? Math.round((ctx.raw as number / total) * 100) : 0;
                return `${ctx.label}: ${ctx.raw} (${pct}%)`;
              },
            },
          },
        },
      },
    });
  }
}

onMounted(async () => {
  try {
    const res = await get<{
      total_messages: number;
      total_employees: number;
      total_leads: number;
      total_customer_messages: number;
      total_agent_messages: number;
      messages_per_month: Array<{ year: number; month: number; count: number; label: string }>;
      interactions: Array<{
        id: number;
        customer_name: string | null;
        customer_phone: string;
        customer_messages: number;
        agent_messages: number;
        last_message_at: string | null;
      }>;
    }>('/dashboard');
    totalMessages.value = res.total_messages ?? 0;
    totalEmployees.value = res.total_employees ?? 0;
    totalLeads.value = res.total_leads ?? 0;
    totalCustomerMessages.value = res.total_customer_messages ?? 0;
    totalAgentMessages.value = res.total_agent_messages ?? 0;
    messagesPerMonth.value = res.messages_per_month ?? [];
    interactions.value = res.interactions ?? [];
  } catch {
    totalMessages.value = null;
    totalEmployees.value = null;
    totalLeads.value = null;
    messagesPerMonth.value = [];
    interactions.value = [];
  } finally {
    loading.value = false;
    buildCharts();
  }
});

onUnmounted(() => {
  if (barChart) barChart.destroy();
  if (doughnutChart) doughnutChart.destroy();
});
</script>
