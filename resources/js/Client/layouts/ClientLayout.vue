<template>
  <div class="flex min-h-screen flex-row-reverse bg-[#f5f5f7]">
    <Sidebar />
    <main class="mr-64 flex-1 overflow-auto p-6">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import Sidebar from '../components/Sidebar.vue';
</script>
