<template>
  <div class="flex min-h-screen bg-gray-100">
    <Sidebar />
    <main class="ml-56 flex-1 overflow-auto p-6">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import Sidebar from '../components/Sidebar.vue';
</script>
