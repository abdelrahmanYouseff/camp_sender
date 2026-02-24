<template>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
      <div class="text-sm text-gray-500">Total Revenue</div>
      <div class="mt-1 text-2xl font-semibold text-gray-800">{{ formatMoney(summary.total_revenue) }}</div>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
      <div class="text-sm text-gray-500">Pending Revenue</div>
      <div class="mt-1 text-2xl font-semibold text-amber-600">{{ formatMoney(summary.pending_revenue) }}</div>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
      <div class="text-sm text-gray-500">Paying Companies</div>
      <div class="mt-1 text-2xl font-semibold text-green-600">{{ summary.paying_companies_count ?? '—' }}</div>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
      <div class="text-sm text-gray-500">Pending Companies</div>
      <div class="mt-1 text-2xl font-semibold text-red-600">{{ summary.pending_companies_count ?? '—' }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  summary: {
    total_revenue: number;
    pending_revenue: number;
    paying_companies_count: number;
    pending_companies_count: number;
  };
}>();

function formatMoney(value: number): string {
  if (value == null || Number.isNaN(value)) return '—';
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
}
</script>
