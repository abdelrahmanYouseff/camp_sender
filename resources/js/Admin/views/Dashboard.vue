<template>
  <div>
    <h1 class="mb-2 text-2xl font-semibold text-gray-800">Dashboard</h1>
    <p class="mb-6 text-sm text-gray-500">Quick overview of all companies and statistics</p>

    <div class="grid gap-4 sm:grid-cols-3">
      <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-gray-500">Total Companies</div>
        <div class="mt-1 text-2xl font-semibold text-gray-800">{{ stats.total_companies ?? '—' }}</div>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-gray-500">Active Companies</div>
        <div class="mt-1 text-2xl font-semibold text-green-600">{{ stats.active_companies ?? '—' }}</div>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-gray-500">Suspended Companies</div>
        <div class="mt-1 text-2xl font-semibold text-red-600">{{ stats.suspended_companies ?? '—' }}</div>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="mb-4 text-lg font-medium text-gray-800">Last activity per company</h2>
      <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Company</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Last activity</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="row in stats.companies_activity" :key="row.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-800">{{ row.name }}</td>
              <td class="px-4 py-3">
                <span
                  :class="row.subscription_status === 'active' ? 'text-green-600' : 'text-red-600'"
                  class="text-sm font-medium"
                >
                  {{ row.subscription_status === 'active' ? 'Active' : 'Suspended' }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ row.last_activity ?? '—' }}</td>
            </tr>
            <tr v-if="!stats.companies_activity?.length && !loading">
              <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No companies</td>
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

interface CompanyActivity {
  id: number;
  name: string;
  subscription_status: string;
  last_activity: string | null;
}

interface DashboardStats {
  total_companies: number;
  active_companies: number;
  suspended_companies: number;
  companies_activity?: CompanyActivity[];
}

const stats = ref<Partial<DashboardStats>>({});
const loading = ref(true);

onMounted(async () => {
  try {
    stats.value = await get<DashboardStats>('/dashboard');
  } catch {
    stats.value = {};
  } finally {
    loading.value = false;
  }
});
</script>
