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
    <div class="overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-neutral-200/60 bg-[#f5f5f7]/80">
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">العميل</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">الهاتف</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">الحالة</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">المُعيَّن</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">آخر رسالة</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">تاريخ الرسالة</th>
            <th class="px-5 py-4 text-right text-[13px] font-semibold tracking-tight text-neutral-500">بدون رد / تعيين منذ</th>
            <th class="w-12 px-2 py-4 text-center text-[13px] font-semibold text-neutral-500" title="جديدة">.</th>
            <th class="px-5 py-4 text-left text-[13px] font-semibold tracking-tight text-neutral-500">إجراءات</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <tr
            v-for="c in conversations"
            :key="c.id"
            class="border-b border-neutral-100 transition-colors duration-150 last:border-b-0 hover:bg-neutral-50/80"
          >
            <td class="px-5 py-4 text-[15px] font-medium text-neutral-900">{{ c.customer_name ?? '—' }}</td>
            <td class="px-5 py-4 text-[15px] text-neutral-600">{{ c.customer_phone }}</td>
            <td class="px-5 py-4">
              <span
                class="inline-block rounded-full px-3 py-1 text-[13px] font-medium"
                :class="statusBadgeClass(c.status)"
              >
                {{ statusLabel(c.status) }}
              </span>
            </td>
            <td class="px-5 py-4 text-[15px] text-neutral-600">{{ c.assigned_employee?.name ?? '—' }}</td>
            <td class="max-w-xs truncate px-5 py-4 text-[15px] text-neutral-600">{{ c.last_message ?? '—' }}</td>
            <td class="whitespace-nowrap px-5 py-4 text-[15px] text-neutral-600">{{ formatMessageDate(c.last_message_at) }}</td>
            <td class="px-5 py-4 text-[15px]">
              <span v-if="waitingLabel(c)" class="whitespace-nowrap font-medium text-amber-600">{{ waitingLabel(c) }}</span>
              <span v-else class="text-neutral-400">—</span>
            </td>
            <td class="px-2 py-4 text-center">
              <span v-if="c.has_unread" class="inline-block h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-emerald-200/60" title="رسالة جديدة" />
            </td>
            <td class="px-5 py-4 text-left">
              <router-link
                :to="{ name: 'conversation', params: { id: c.id } }"
                class="inline-flex items-center rounded-[10px] px-3 py-1.5 text-[15px] font-medium text-blue-500 transition-colors hover:bg-blue-500/10 hover:text-blue-600"
              >
                عرض
              </router-link>
            </td>
          </tr>
          <tr v-if="conversations.length === 0 && !loading">
            <td colspan="9" class="px-5 py-12 text-center text-[15px] text-neutral-500">لا توجد محادثات</td>
          </tr>
        </tbody>
      </table>
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
