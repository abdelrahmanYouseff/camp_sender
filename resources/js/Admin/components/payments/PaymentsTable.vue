<template>
  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Company</th>
          <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Subscription Status</th>
          <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Last Payment Date</th>
          <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Next Payment Due</th>
          <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Amount Due</th>
          <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white">
        <tr v-for="row in payments" :key="row.id" class="hover:bg-gray-50">
          <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-800">{{ row.company_name ?? '—' }}</td>
          <td class="whitespace-nowrap px-4 py-3">
            <span
              :class="row.subscription_status === 'active' ? 'text-green-600' : 'text-red-600'"
              class="text-sm font-medium"
            >
              {{ row.subscription_status === 'active' ? 'Active' : 'Suspended' }}
            </span>
          </td>
          <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ row.payment_date ?? '—' }}</td>
          <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ row.next_payment_date ?? '—' }}</td>
          <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-800">{{ formatMoney(row.amount) }}</td>
          <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
            <button
              v-if="row.status !== 'paid'"
              type="button"
              class="mr-2 text-gray-700 underline hover:text-gray-900"
              :disabled="loadingId === row.id"
              @click="$emit('mark-paid', row)"
            >
              {{ loadingId === row.id ? '…' : 'Mark Paid' }}
            </button>
            <router-link
              :to="{ name: 'companies.edit', params: { id: row.company_id } }"
              class="text-gray-700 underline hover:text-gray-900"
            >
              Suspend
            </router-link>
          </td>
        </tr>
        <tr v-if="payments.length === 0 && !loading">
          <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No payments</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  payments: Array<{
    id: number;
    company_id: number;
    company_name: string | null;
    subscription_status: string;
    amount: number;
    status: string;
    payment_date: string | null;
    next_payment_date: string | null;
  }>;
  loading?: boolean;
  loadingId?: number | null;
}>();

defineEmits<{
  'mark-paid': [row: { id: number; company_id: number }];
}>();

function formatMoney(value: number): string {
  if (value == null || Number.isNaN(value)) return '—';
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
}
</script>
