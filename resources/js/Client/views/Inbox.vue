<template>
  <div class="text-right relative">
    <!-- إشعار رسالة جديدة -->
    <Transition name="toast">
      <div
        v-if="notificationText"
        class="fixed left-4 top-4 z-50 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 shadow-lg ring-2 ring-emerald-200/50"
        role="alert"
      >
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </span>
        <p class="font-medium text-emerald-900">{{ notificationText }}</p>
      </div>
    </Transition>
    <h1 class="mb-6 text-2xl font-semibold tracking-tight text-neutral-900">صندوق الوارد</h1>
    <div class="mb-6 flex flex-wrap items-center gap-3 rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
      <input
        v-model="searchName"
        type="text"
        placeholder="البحث باسم العميل…"
        class="rounded-[10px] border border-neutral-200 bg-neutral-50/80 px-4 py-2.5 text-[15px] text-neutral-900 placeholder:text-neutral-400 focus:border-blue-500/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
        @input="debouncedFetch"
      />
      <input
        v-model="searchPhone"
        type="text"
        placeholder="البحث بالهاتف…"
        class="rounded-[10px] border border-neutral-200 bg-neutral-50/80 px-4 py-2.5 text-[15px] text-neutral-900 placeholder:text-neutral-400 focus:border-blue-500/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
        @input="debouncedFetch"
      />
      <button type="button" class="rounded-[10px] bg-blue-500 px-4 py-2.5 text-[15px] font-medium text-white transition-colors hover:bg-blue-600 active:scale-[0.98]" @click="fetchConversations">بحث</button>
    </div>
    <!-- شبكة كروت المحادثات -->
    <div v-if="loading" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="i in 6" :key="i" class="rounded-2xl border border-neutral-200/80 bg-white p-5 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
        <div class="flex items-center gap-3">
          <div class="h-12 w-12 shrink-0 animate-pulse rounded-full bg-neutral-200" />
          <div class="min-w-0 flex-1 space-y-2">
            <div class="h-4 w-32 animate-pulse rounded bg-neutral-200" />
            <div class="h-3 w-24 animate-pulse rounded bg-neutral-100" />
          </div>
        </div>
        <div class="mt-4 h-3 w-full animate-pulse rounded bg-neutral-100" />
        <div class="mt-2 h-3 w-3/4 animate-pulse rounded bg-neutral-100" />
      </div>
    </div>
    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <router-link
        v-for="c in conversations"
        :key="c.id"
        :to="{ name: 'conversation', params: { id: c.id } }"
        class="group flex flex-col rounded-2xl border border-neutral-200/80 bg-white p-5 shadow-[0_1px_3px_rgba(0,0,0,0.06)] transition-all duration-200 hover:border-blue-200 hover:shadow-[0_4px_12px_rgba(59,130,246,0.12)]"
      >
        <div class="flex items-start gap-3">
          <div class="relative shrink-0">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-lg font-semibold text-white shadow-sm">
              {{ customerInitial(c.customer_name) }}
            </div>
            <span
              v-if="c.has_unread"
              class="absolute -top-0.5 -left-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 ring-2 ring-white text-[10px] text-white"
              title="رسالة جديدة"
            />
          </div>
          <div class="min-w-0 flex-1">
            <h3 class="truncate text-[16px] font-semibold text-neutral-900 group-hover:text-blue-600">
              {{ c.customer_name || 'عميل' }}
            </h3>
            <p class="mt-0.5 truncate text-[13px] text-neutral-500">{{ c.customer_phone }}</p>
            <div class="mt-2 flex flex-wrap items-center gap-2">
              <span
                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[12px] font-medium"
                :class="statusBadgeClass(c.status)"
              >
                {{ statusLabel(c.status) }}
              </span>
              <span v-if="c.assigned_employee?.name" class="truncate text-[12px] text-neutral-500">
                → {{ c.assigned_employee.name }}
              </span>
            </div>
          </div>
        </div>
        <p class="mt-3 line-clamp-2 min-h-[2.5rem] text-[14px] text-neutral-600">
          {{ c.last_message || 'لا توجد رسائل بعد' }}
        </p>
        <div class="mt-auto flex items-center justify-between gap-2 border-t border-neutral-100 pt-3">
          <span class="text-[12px] text-neutral-400">{{ formatMessageDate(c.last_message_at) }}</span>
          <span v-if="waitingLabel(c)" class="truncate text-[12px] font-medium text-amber-600">{{ waitingLabel(c) }}</span>
        </div>
        <div class="mt-2 flex justify-start">
          <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[13px] font-medium text-blue-500 transition-colors group-hover:bg-blue-50 group-hover:text-blue-600">
            عرض المحادثة
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </span>
        </div>
      </router-link>
    </div>
    <div v-if="!loading && conversations.length === 0" class="rounded-2xl border border-dashed border-neutral-200 bg-neutral-50/50 py-16 text-center">
      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-neutral-200/80 text-neutral-400">
        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
      </div>
      <p class="mt-4 text-[15px] font-medium text-neutral-600">لا توجد محادثات</p>
      <p class="mt-1 text-[14px] text-neutral-500">ستظهر هنا المحادثات القادمة من واتساب</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { get } from '../api';

const conversations = ref<any[]>([]);
const loading = ref(true);
const searchName = ref('');
const searchPhone = ref('');
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
/** معرفات المحادثات التي كانت فيها رسائل غير مقروءة في آخر جلب (للكشف عن الجديد) */
const previousUnreadIds = ref<Set<number>>(new Set());
let pollInterval: ReturnType<typeof setInterval> | null = null;
/** يحدَّث كل دقيقة لتحديث عرض «منذ X» */
const tick = ref(0);
let tickInterval: ReturnType<typeof setInterval> | null = null;
/** نص إشعار «رسالة جديدة» (يُعرض لثوانٍ ثم يُخفى) */
const notificationText = ref('');
let notificationTimeout: ReturnType<typeof setTimeout> | null = null;

function toEnglishDigits(s: string): string {
  return s.replace(/[٠-٩]/g, (d) => String('٠١٢٣٤٥٦٧٨٩'.indexOf(d)));
}

function formatMessageDate(iso: string | null | undefined): string {
  if (!iso) return '—';
  try {
    const formatted = new Date(iso).toLocaleString('ar-EG', { dateStyle: 'short', timeStyle: 'short' });
    return toEnglishDigits(formatted);
  } catch {
    return '—';
  }
}

function relativeTimeSince(iso: string | null | undefined): string {
  if (!iso) return '';
  try {
    const d = new Date(iso).getTime();
    const now = Date.now();
    const diffMs = now - d;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    if (diffMins < 1) return 'الآن';
    if (diffMins < 60) return `منذ ${diffMins} دقيقة`;
    if (diffHours < 24) return `منذ ${diffHours} ساعة`;
    if (diffDays < 7) return `منذ ${diffDays} يوم`;
    return toEnglishDigits(formatMessageDate(iso));
  } catch {
    return '';
  }
}

function customerInitial(name: string | null | undefined): string {
  if (!name || !name.trim()) return '؟';
  const trimmed = name.trim();
  return trimmed.charAt(0).toUpperCase();
}

function statusLabel(status: string | null | undefined): string {
  if (status === 'new') return 'جديد';
  if (status === 'open') return 'مفتوحة';
  if (status === 'closed') return 'تم الإغلاق';
  return status ?? '—';
}

function statusBadgeClass(status: string | null | undefined): string {
  if (status === 'new') return 'bg-green-600 text-white';
  if (status === 'open') return 'bg-emerald-100 text-emerald-800';
  if (status === 'closed') return 'bg-red-600 text-white';
  return 'bg-gray-100 text-gray-600';
}

function waitingLabel(c: any): string {
  void tick.value;
  const hasAssignment = c.assigned_employee != null;
  const lastFromCustomer = c.last_message_sender === 'customer';
  if (!hasAssignment && c.conversation_created_at) {
    return 'بدون تعيين ' + relativeTimeSince(c.conversation_created_at);
  }
  if (hasAssignment && lastFromCustomer && c.last_message_at) {
    return 'بدون رد ' + relativeTimeSince(c.last_message_at);
  }
  return '';
}

function showNewMessageNotification(customerName: string | null) {
  const name = customerName?.trim() || 'عميل';
  notificationText.value = `رسالة جديدة من ${name}`;
  if (notificationTimeout) clearTimeout(notificationTimeout);
  notificationTimeout = setTimeout(() => {
    notificationText.value = '';
    notificationTimeout = null;
  }, 5000);
}

function playNotificationSound() {
  try {
    const Ctx = window.AudioContext || (window as any).webkitAudioContext;
    if (!Ctx) return;
    const ctx = new Ctx();
    if (ctx.state === 'suspended') ctx.resume();
    const playBeep = (frequency: number, start: number, duration: number) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.frequency.value = frequency;
      osc.type = 'sine';
      gain.gain.setValueAtTime(0.2, start);
      gain.gain.exponentialRampToValueAtTime(0.01, start + duration);
      osc.start(start);
      osc.stop(start + duration);
    };
    playBeep(880, 0, 0.1);
    playBeep(880, 0.18, 0.1);
  } catch {
    // ignore if AudioContext not supported
  }
}

function buildQuery(): string {
  const params = new URLSearchParams();
  if (searchName.value.trim()) params.set('name', searchName.value.trim());
  if (searchPhone.value.trim()) params.set('phone', searchPhone.value.trim());
  const q = params.toString();
  return q ? `?${q}` : '';
}

async function fetchConversations(isPoll = false) {
  if (!isPoll) loading.value = true;
  try {
    const res = await get<{ data: any[] }>(`/inbox${buildQuery()}`);
    const data = res.data ?? [];

    if (isPoll) {
      const currentUnreadIds = new Set(data.filter((c: any) => c.has_unread).map((c: any) => c.id));
      for (const id of currentUnreadIds) {
        if (!previousUnreadIds.value.has(id)) {
          const conv = data.find((c: any) => c.id === id);
          playNotificationSound();
          showNewMessageNotification(conv?.customer_name ?? null);
          break;
        }
      }
      previousUnreadIds.value = currentUnreadIds;
    } else {
      previousUnreadIds.value = new Set(data.filter((c: any) => c.has_unread).map((c: any) => c.id));
    }

    conversations.value = data;
  } catch {
    if (!isPoll) conversations.value = [];
  } finally {
    if (!isPoll) loading.value = false;
  }
}

function debouncedFetch() {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => fetchConversations(false), 300);
}

onMounted(() => {
  fetchConversations(false);
  pollInterval = setInterval(() => fetchConversations(true), 12000);
  tickInterval = setInterval(() => { tick.value += 1; }, 60000);
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
  if (tickInterval) clearInterval(tickInterval);
  if (debounceTimer) clearTimeout(debounceTimer);
  if (notificationTimeout) clearTimeout(notificationTimeout);
});
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
