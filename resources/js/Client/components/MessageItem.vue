<template>
  <div class="flex w-full" :class="isCustomer ? 'justify-start' : 'justify-end'">
    <div
      class="group max-w-[85%] sm:max-w-[75%]"
      :class="isCustomer ? 'items-start' : 'items-end'"
    >
      <div
        class="rounded-2xl px-4 py-2.5 shadow-sm transition-shadow"
        :class="
          isCustomer
            ? 'rounded-br-md bg-white text-gray-900 ring-1 ring-gray-200/80'
            : 'rounded-bl-md bg-[#0a7c42] text-white'
        "
      >
        <p class="whitespace-pre-wrap break-words text-[15px] leading-relaxed">{{ message.message }}</p>
        <div
          class="mt-1 flex items-center gap-1.5"
          :class="isCustomer ? 'justify-end text-gray-400' : 'justify-end text-white/80'"
        >
          <span class="text-[11px]">{{ timeOnly }}</span>
          <span v-if="!isCustomer && message.read_at" class="text-[10px]" title="مقروءة">✓✓</span>
        </div>
      </div>
      <p v-if="showSenderName" class="mt-0.5 mr-1 text-[11px] text-gray-500">
        {{ message.employee_name ?? 'موظف' }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  message: {
    sender: string;
    message: string;
    employee_name?: string | null;
    created_at?: string | null;
    read_at?: string | null;
  };
}>();

const isCustomer = computed(() => props.message.sender === 'customer');

const showSenderName = computed(() => !isCustomer.value && props.message.employee_name);

function toEnglishDigits(s: string): string {
  return s.replace(/[٠-٩]/g, (d) => String('٠١٢٣٤٥٦٧٨٩'.indexOf(d)));
}

function formatTime(value: string | null | undefined): string {
  if (!value) return '—';
  try {
    const formatted = new Date(value).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
    return toEnglishDigits(formatted);
  } catch {
    return '—';
  }
}

const timeOnly = computed(() => formatTime(props.message.created_at));
</script>
