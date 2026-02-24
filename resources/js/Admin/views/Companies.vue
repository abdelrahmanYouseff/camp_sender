<template>
  <div>
    <!-- Header: title + Add Company button on the right -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
      <h1 class="text-2xl font-semibold text-gray-800">Companies</h1>
      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-lg bg-gray-800 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
        @click="showAddForm = !showAddForm"
      >
        <span class="text-lg leading-none">+</span>
        <span>Add Company</span>
      </button>
    </div>

    <!-- Add Company form (shown when button clicked) -->
    <div
      v-show="showAddForm"
      class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
    >
      <h2 class="mb-4 text-lg font-medium text-gray-800">New Company</h2>
      <form class="grid grid-cols-1 gap-4 sm:grid-cols-2" @submit.prevent="submitAdd">
        <div>
          <label for="add-name" class="mb-1 block text-sm font-medium text-gray-700">Name *</label>
          <input
            id="add-name"
            v-model="addForm.name"
            type="text"
            required
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
          />
          <p v-if="addErrors.name" class="mt-1 text-xs text-red-600">{{ addErrors.name }}</p>
        </div>
        <div>
          <label for="add-email" class="mb-1 block text-sm font-medium text-gray-700">Email *</label>
          <input
            id="add-email"
            v-model="addForm.email"
            type="email"
            required
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
          />
          <p v-if="addErrors.email" class="mt-1 text-xs text-red-600">{{ addErrors.email }}</p>
        </div>
        <div>
          <label for="add-phone" class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
          <input
            id="add-phone"
            v-model="addForm.phone"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
          />
        </div>
        <div>
          <label for="add-whatsapp_id" class="mb-1 block text-sm font-medium text-gray-700">WhatsApp Phone Number ID</label>
          <input
            id="add-whatsapp_id"
            v-model="addForm.whatsapp_phone_number_id"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
          />
        </div>
        <div class="sm:col-span-2">
          <label for="add-token" class="mb-1 block text-sm font-medium text-gray-700">WhatsApp Access Token</label>
          <textarea
            id="add-token"
            v-model="addForm.whatsapp_access_token"
            rows="2"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
          />
        </div>
        <div>
          <label for="add-status" class="mb-1 block text-sm font-medium text-gray-700">Subscription status *</label>
          <select
            id="add-status"
            v-model="addForm.subscription_status"
            required
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
          >
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
        <div>
          <label for="add-end-date" class="mb-1 block text-sm font-medium text-gray-700">Subscription end date</label>
          <input
            id="add-end-date"
            v-model="addForm.subscription_end_date"
            type="date"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
          />
        </div>
        <div class="flex flex-wrap items-center gap-3 pt-2 sm:col-span-2">
          <button
            type="submit"
            class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-60"
            :disabled="addSaving"
          >
            {{ addSaving ? 'Saving…' : 'Create Company' }}
          </button>
          <button
            type="button"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            @click="showAddForm = false"
          >
            Cancel
          </button>
          <p v-if="addErrors.submit" class="text-sm text-red-600">{{ addErrors.submit }}</p>
        </div>
      </form>
    </div>

    <!-- Search & filter -->
    <div class="mb-4 flex flex-wrap items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
      <div class="flex items-center gap-2">
        <label for="filter-name" class="text-sm text-gray-600">Name</label>
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
        <label for="filter-status" class="text-sm text-gray-600">Status</label>
        <select
          id="filter-status"
          v-model="filterStatus"
          class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-800"
          @change="fetchCompanies"
        >
          <option value="">All</option>
          <option value="active">Active</option>
          <option value="suspended">Suspended</option>
        </select>
      </div>
      <button
        type="button"
        class="rounded bg-gray-800 px-3 py-1.5 text-sm text-white hover:bg-gray-700"
        @click="fetchCompanies"
      >
        Search
      </button>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">WhatsApp Phone ID</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Subscription</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Last activity</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          <tr v-for="company in companies" :key="company.id" class="hover:bg-gray-50">
            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-800">{{ company.name }}</td>
            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ company.whatsapp_phone_number_id ?? '—' }}</td>
            <td class="whitespace-nowrap px-4 py-3">
              <span
                :class="company.subscription_status === 'active' ? 'text-green-600' : 'text-red-600'"
                class="text-sm font-medium"
              >
                {{ company.subscription_status === 'active' ? 'Active' : 'Suspended' }}
              </span>
            </td>
            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
              {{ formatDate(company.last_activity) }}
            </td>
            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
              <router-link
                :to="{ name: 'companies.edit', params: { id: company.id } }"
                class="text-gray-700 underline hover:text-gray-900"
              >
                Edit
              </router-link>
            </td>
          </tr>
          <tr v-if="companies.length === 0 && !loading">
            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No companies yet.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { get, post } from '../api';

interface CompanyRow {
  id: number;
  name: string;
  email: string;
  whatsapp_phone_number_id: string | null;
  subscription_status: string;
  subscription_end_date: string | null;
  last_activity: string | null;
}

const companies = ref<CompanyRow[]>([]);
const loading = ref(true);
const filterName = ref('');
const filterStatus = ref('');
const showAddForm = ref(false);
const addSaving = ref(false);

const addForm = reactive({
  name: '',
  email: '',
  phone: '',
  whatsapp_phone_number_id: '',
  whatsapp_access_token: '',
  subscription_status: 'active',
  subscription_end_date: '' as string | null,
});
const addErrors = reactive<Record<string, string>>({});

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

function formatDate(value: string | null | undefined): string {
  if (!value) return '—';
  try {
    return new Date(value).toLocaleString();
  } catch {
    return '—';
  }
}

function buildQuery(): string {
  const params = new URLSearchParams();
  if (filterName.value.trim()) params.set('name', filterName.value.trim());
  if (filterStatus.value) params.set('subscription_status', filterStatus.value);
  const q = params.toString();
  return q ? `?${q}` : '';
}

async function fetchCompanies() {
  loading.value = true;
  try {
    const res = await get<{ data: CompanyRow[] }>(`/companies${buildQuery()}`);
    companies.value = res.data ?? [];
  } catch {
    companies.value = [];
  } finally {
    loading.value = false;
  }
}

function debouncedFetch() {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(fetchCompanies, 300);
}

function resetAddForm() {
  addForm.name = '';
  addForm.email = '';
  addForm.phone = '';
  addForm.whatsapp_phone_number_id = '';
  addForm.whatsapp_access_token = '';
  addForm.subscription_status = 'active';
  addForm.subscription_end_date = null;
  addErrors.name = '';
  addErrors.email = '';
  addErrors.submit = '';
}

async function submitAdd() {
  addSaving.value = true;
  addErrors.name = '';
  addErrors.email = '';
  addErrors.submit = '';
  try {
    const payload: Record<string, unknown> = {
      name: addForm.name,
      email: addForm.email,
      subscription_status: addForm.subscription_status,
    };
    if (addForm.phone) payload.phone = addForm.phone;
    if (addForm.whatsapp_phone_number_id) payload.whatsapp_phone_number_id = addForm.whatsapp_phone_number_id;
    if (addForm.whatsapp_access_token) payload.whatsapp_access_token = addForm.whatsapp_access_token;
    if (addForm.subscription_end_date) payload.subscription_end_date = addForm.subscription_end_date;

    await post<{ data: { id: number } }>('/companies', payload);
    resetAddForm();
    showAddForm.value = false;
    await fetchCompanies();
  } catch (e: unknown) {
    const err = e as { message?: string };
    addErrors.submit = err?.message ?? 'Failed to create company';
  } finally {
    addSaving.value = false;
  }
}

onMounted(fetchCompanies);
</script>
