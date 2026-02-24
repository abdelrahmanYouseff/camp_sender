<template>
  <aside class="fixed left-0 top-0 z-10 h-full w-56 border-r border-gray-200 bg-white shadow-sm">
    <div class="flex h-full flex-col p-4">
      <div class="mb-6 text-lg font-semibold text-gray-800">Admin</div>
      <nav class="flex flex-1 flex-col gap-1">
        <router-link
          :to="{ name: 'dashboard' }"
          class="rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
          active-class="bg-gray-200 font-medium"
        >
          Dashboard
        </router-link>
        <router-link
          :to="{ name: 'companies' }"
          class="rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
          active-class="bg-gray-200 font-medium"
        >
          Companies
        </router-link>
        <router-link
          :to="{ name: 'payments' }"
          class="rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
          active-class="bg-gray-200 font-medium"
        >
          Payments
        </router-link>
      </nav>
      <div class="border-t border-gray-200 pt-4">
        <button
          type="button"
          class="w-full rounded-md px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
          @click="logout"
        >
          Logout
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function logout() {
  await fetch('/admin/logout', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken ?? '',
    },
  });
  window.location.href = '/admin/login';
}
</script>
