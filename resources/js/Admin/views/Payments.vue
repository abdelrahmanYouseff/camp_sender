<template>
  <div>
    <h1 class="mb-1 text-2xl font-semibold text-gray-800">Payments</h1>
    <p class="mb-6 text-4xl font-bold text-black">
      {{ formatNumber(revenueSummary?.total_revenue ?? 0) }}
    </p>

    <RevenueSummary v-if="revenueSummary" :summary="revenueSummary" class="mb-8" />

    <!-- Search & filter -->
    <div class="mb-4 flex flex-wrap items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2">
        <label for="filter-name" class="text-sm text-gray-600">Company</label>
        <input
          id="filter-name"
          v-model="filterName"
          type="text"
          placeholder="Search by name…"
          class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-800"
          @input="debouncedFetch"
        />
      </div>
      <div class="flex items-center gap-2">
        <label for="filter-status" class="text-sm text-gray-600">Subscription</label>
        <select
          id="filter-status"
          v-model="filterStatus"
          class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-800"
          @change="fetchData"
        >
          <option value="">All</option>
          <option value="active">Active</option>
          <option value="suspended">Suspended</option>
        </select>
      </div>
      <button
        type="button"
        class="rounded bg-gray-800 px-3 py-1.5 text-sm text-white hover:bg-gray-700"
        @click="fetchData"
      >
        Search
      </button>
    </div>

    <h2 class="mb-4 text-lg font-medium text-gray-800">Upcoming Payments</h2>
    <PaymentsTable
      :payments="payments"
      :loading="loading"
      :loading-id="loadingId"
      @mark-paid="onMarkPaid"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { get, post } from '../api';
import RevenueSummary from '../components/payments/RevenueSummary.vue';
import PaymentsTable from '../components/payments/PaymentsTable.vue';

interface RevenueSummaryData {
  total_revenue: number;
  pending_revenue: number;
  paying_companies_count: number;
  pending_companies_count: number;
}

interface PaymentRow {
  id: number;
  company_id: number;
  company_name: string | null;
  subscription_status: string;
  amount: number;
  status: string;
  payment_date: string | null;
  next_payment_date: string | null;
}

interface PaymentsResponse {
  revenue_summary: RevenueSummaryData;
  payments: PaymentRow[];
}

const revenueSummary = ref<RevenueSummaryData | null>(null);
const payments = ref<PaymentRow[]>([]);
const loading = ref(true);
const loadingId = ref<number | null>(null);
const filterName = ref('');
const filterStatus = ref('');

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

function formatNumber(value: number): string {
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
}

function buildQuery(): string {
  const params = new URLSearchParams();
  if (filterName.value.trim()) params.set('name', filterName.value.trim());
  if (filterStatus.value) params.set('subscription_status', filterStatus.value);
  const q = params.toString();
  return q ? `?${q}` : '';
}

async function fetchData() {
  loading.value = true;
  try {
    const res = await get<PaymentsResponse>(`/payments${buildQuery()}`);
    revenueSummary.value = res.revenue_summary ?? null;
    payments.value = res.payments ?? [];
  } catch {
    revenueSummary.value = null;
    payments.value = [];
  } finally {
    loading.value = false;
  }
}

function debouncedFetch() {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(fetchData, 300);
}

async function onMarkPaid(row: { id: number }) {
  loadingId.value = row.id;
  try {
    await post(`/payments/${row.id}/mark-paid`);
    await fetchData();
  } finally {
    loadingId.value = null;
  }
}

onMounted(fetchData);
</script>
