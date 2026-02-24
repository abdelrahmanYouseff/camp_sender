<template>
  <div class="flex h-[100vh] min-h-0 -m-6" dir="ltr">
    <!-- لوحة بيانات العميل — على اليسار -->
    <aside
      v-if="conversation"
      class="flex h-full w-72 shrink-0 flex-col border-l border-gray-200 bg-white"
      dir="rtl"
    >
      <div class="flex flex-1 flex-col overflow-auto p-5">
        <div class="mb-4 flex items-center gap-3">
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-lg font-semibold text-white shadow-md">
            {{ customerInitial }}
          </div>
          <div class="min-w-0 flex-1 text-right">
            <h2 class="truncate text-lg font-semibold text-gray-900">{{ conversation.customer_name || 'عميل' }}</h2>
            <p class="truncate text-sm text-gray-500">{{ conversation.customer_phone }}</p>
          </div>
        </div>
        <div class="space-y-3 border-t border-gray-100 pt-4">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">الحالة</span>
            <span
              class="rounded-full px-2.5 py-0.5 text-xs font-medium"
              :class="statusClass"
            >
              {{ statusLabel }}
            </span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">المُعيَّن</span>
            <span class="font-medium text-gray-800">{{ conversation.assigned_employee?.name ?? '—' }}</span>
          </div>
          <div v-if="firstMessageAt" class="flex flex-col gap-0.5 border-t border-gray-100 pt-3 text-sm">
            <span class="text-gray-500">بداية المحادثة</span>
            <span class="font-medium text-gray-800">{{ firstMessageAt }}</span>
          </div>
          <div v-if="lastMessageAt" class="flex flex-col gap-0.5 text-sm">
            <span class="text-gray-500">آخر نشاط</span>
            <span class="font-medium text-gray-800">{{ lastMessageAt }}</span>
          </div>
        </div>
      </div>
      <div class="shrink-0 border-t border-gray-100 p-4">
        <button
          v-if="conversation.status !== 'closed'"
          type="button"
          class="w-full rounded-lg bg-red-600 px-3 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700 disabled:opacity-50"
          :disabled="closing"
          @click="showCloseModal = true"
        >
          {{ closing ? 'جاري الإغلاق…' : 'إنهاء المحادثة' }}
        </button>
        <p v-else class="text-center text-sm text-gray-500">تم إغلاق المحادثة</p>
        <p v-if="conversation.status === 'closed' && conversation.closure_interest" class="mt-2 text-center text-xs font-medium" :class="conversation.closure_interest === 'interested' ? 'text-emerald-600' : 'text-amber-600'">
          {{ conversation.closure_interest === 'interested' ? 'مهتم' : 'غير مهتم' }}
        </p>
      </div>
    </aside>

    <!-- بوب أب إنهاء المحادثة: مهتم / غير مهتم -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showCloseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showCloseModal = false">
          <div class="absolute inset-0 bg-black/50" />
          <div class="relative w-full max-w-sm rounded-2xl border border-gray-200 bg-white p-6 shadow-xl" dir="rtl">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">كيف كان تقييم العميل؟</h3>
            <p class="mb-5 text-sm text-gray-500">حدد إن كان العميل مهتماً أو غير مهتم قبل إغلاق المحادثة.</p>
            <div class="flex gap-3">
              <button
                type="button"
                class="flex-1 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-medium text-white transition-colors hover:bg-emerald-700 disabled:opacity-50"
                :disabled="closing"
                @click="confirmClose('interested')"
              >
                مهتم
              </button>
              <button
                type="button"
                class="flex-1 rounded-xl bg-amber-500 px-4 py-3 text-sm font-medium text-white transition-colors hover:bg-amber-600 disabled:opacity-50"
                :disabled="closing"
                @click="confirmClose('not_interested')"
              >
                غير مهتم
              </button>
            </div>
            <button
              type="button"
              class="mt-4 w-full rounded-lg border border-gray-300 py-2 text-sm text-gray-600 hover:bg-gray-50"
              @click="showCloseModal = false"
            >
              إلغاء
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- منطقة الشات — محيط المحادثة -->
    <section class="flex min-w-0 flex-1 flex-col bg-[#e5ddd5] bg-chat-pattern" dir="rtl">
      <!-- شريط أعلى الشات -->
      <header class="flex shrink-0 items-center gap-3 border-b border-gray-200/60 bg-[#f0f2f5] px-4 py-3 shadow-sm">
        <router-link
          :to="{ name: 'inbox' }"
          class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-600 transition-colors hover:bg-gray-200 hover:text-gray-900"
          title="العودة لصندوق الوارد"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </router-link>
        <div class="min-w-0 flex-1 text-right">
          <h1 class="truncate text-base font-semibold text-gray-900">
            {{ conversation?.customer_name || conversation?.customer_phone || 'المحادثة' }}
          </h1>
          <p class="truncate text-xs text-gray-500">{{ conversation?.customer_phone }}</p>
        </div>
      </header>

      <!-- قائمة الرسائل -->
      <div
        ref="messagesEl"
        class="flex-1 overflow-y-auto overflow-x-hidden px-4 py-4"
      >
        <template v-if="loading">
          <div class="flex justify-center py-12">
            <div class="h-8 w-8 animate-spin rounded-full border-2 border-[#0a7c42] border-t-transparent" />
          </div>
        </template>
        <template v-else>
          <div v-if="messages.length === 0" class="flex flex-col items-center justify-center py-16 text-center text-gray-500">
            <span class="text-sm">لا توجد رسائل بعد.</span>
            <span class="mt-1 text-xs">ابدأ بإرسال رسالة للعميل.</span>
          </div>
          <div v-else class="space-y-3">
            <template v-for="(m, i) in messages" :key="m.id">
              <div v-if="showDateSeparator(i)" class="flex justify-center py-2">
                <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-medium text-gray-600 shadow-sm">
                  {{ formatDateLabel(m.created_at) }}
                </span>
              </div>
              <MessageItem :message="m" />
            </template>
          </div>
        </template>
      </div>

      <!-- حقل الإدخال -->
      <div class="shrink-0 border-t border-gray-200/60 bg-[#f0f2f5] p-3">
        <div class="flex items-end gap-2 rounded-2xl bg-white px-4 py-2 shadow-sm ring-1 ring-gray-200/80">
          <textarea
            v-model="replyText"
            placeholder="اكتب رسالتك…"
            class="min-h-[44px] max-h-32 flex-1 resize-none border-0 bg-transparent py-2.5 text-[15px] text-gray-900 placeholder-gray-400 focus:outline-none"
            rows="1"
            :disabled="sending"
            @keydown.enter.exact.prevent="sendReply"
          />
          <button
            type="button"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#0a7c42] text-white transition-colors hover:bg-[#086b38] disabled:opacity-50"
            :disabled="sending || !replyText.trim()"
            :title="sending ? 'جاري الإرسال…' : 'إرسال'"
            @click="sendReply"
          >
            <svg v-if="sending" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            <svg v-else class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
            </svg>
          </button>
        </div>
        <p v-if="replyError" class="mt-2 text-center text-sm text-red-600">{{ replyError }}</p>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { get, post } from '../api';
import MessageItem from '../components/MessageItem.vue';

const route = useRoute();
const router = useRouter();
const conversation = ref<any>(null);
const messages = ref<any[]>([]);
const loading = ref(true);
const replyText = ref('');
const sending = ref(false);
const replyError = ref('');
const closing = ref(false);
const showCloseModal = ref(false);
const messagesEl = ref<HTMLElement | null>(null);
const id = computed(() => route.params.id);

const customerInitial = computed(() => {
  const name = conversation.value?.customer_name?.trim();
  if (name) return name.charAt(0).toUpperCase();
  const phone = conversation.value?.customer_phone;
  if (phone) return phone.slice(-1);
  return '?';
});

const statusLabel = computed(() => {
  const s = conversation.value?.status;
  if (s === 'new') return 'جديدة';
  if (s === 'open') return 'مفتوحة';
  if (s === 'closed') return 'تم الإغلاق';
  return s ?? '—';
});

const statusClass = computed(() => {
  const s = conversation.value?.status;
  if (s === 'new') return 'bg-amber-100 text-amber-800';
  if (s === 'open') return 'bg-emerald-100 text-emerald-800';
  if (s === 'closed') return 'bg-gray-100 text-gray-600';
  return 'bg-gray-100 text-gray-600';
});

const firstMessageAt = computed(() => {
  const first = messages.value[0]?.created_at;
  return first ? formatFullDate(first) : null;
});

const lastMessageAt = computed(() => {
  const last = messages.value[messages.value.length - 1]?.created_at;
  return last ? formatFullDate(last) : null;
});

function toEnglishDigits(s: string): string {
  return s.replace(/[٠-٩]/g, (d) => String('٠١٢٣٤٥٦٧٨٩'.indexOf(d)));
}

function formatFullDate(value: string): string {
  try {
    const formatted = new Date(value).toLocaleString('ar-EG', {
      dateStyle: 'medium',
      timeStyle: 'short',
    });
    return toEnglishDigits(formatted);
  } catch {
    return '—';
  }
}

function formatDateLabel(value: string | undefined): string {
  if (!value) return '';
  try {
    const d = new Date(value);
    const today = new Date();
    if (d.toDateString() === today.toDateString()) return 'اليوم';
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    if (d.toDateString() === yesterday.toDateString()) return 'أمس';
    const formatted = d.toLocaleDateString('ar-EG', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    return toEnglishDigits(formatted);
  } catch {
    return '';
  }
}

function showDateSeparator(index: number): boolean {
  if (index === 0) return true;
  const prev = messages.value[index - 1]?.created_at;
  const curr = messages.value[index]?.created_at;
  if (!prev || !curr) return false;
  return new Date(prev).toDateString() !== new Date(curr).toDateString();
}

async function confirmClose(interest: 'interested' | 'not_interested') {
  if (conversation.value?.status === 'closed' || closing.value) return;
  closing.value = true;
  showCloseModal.value = false;
  try {
    await post(`/inbox/${id.value}/close`, { interest: interest });
    router.push({ name: 'inbox' });
  } catch {
    // ignore
  } finally {
    closing.value = false;
  }
}

async function loadConversation() {
  loading.value = true;
  try {
    const res = await get<{ conversation: any; messages: any[] }>(`/inbox/${id.value}`);
    conversation.value = res.conversation;
    messages.value = res.messages ?? [];
  } catch {
    conversation.value = null;
    messages.value = [];
  } finally {
    loading.value = false;
  }
}

function scrollToBottom() {
  nextTick(() => {
    const el = messagesEl.value;
    if (el) el.scrollTop = el.scrollHeight;
  });
}

watch(messages, () => scrollToBottom(), { deep: true });

async function sendReply() {
  const text = replyText.value.trim();
  if (!text || sending.value) return;
  sending.value = true;
  replyError.value = '';
  try {
    const res = await post<{ data: any }>(`/inbox/${id.value}/reply`, { message: text });
    messages.value = [...messages.value, res.data];
    replyText.value = '';
  } catch (e: unknown) {
    replyError.value = (e as Error)?.message ?? 'فشل الإرسال';
  } finally {
    sending.value = false;
  }
}

onMounted(loadConversation);
</script>

<style scoped>
.bg-chat-pattern {
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4cdc4' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
</style>
<style>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.2s ease;
}
.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
}
</style>
