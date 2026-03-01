<template>
  <aside dir="rtl" class="fixed right-0 top-0 z-10 flex h-full w-64 flex-col bg-[#1263cf] shadow-[0_0_0_0.5px_rgba(0,0,0,0.08)]">
    <!-- الشعار -->
    <div class="flex items-center justify-center px-4 pt-7 pb-5">
      <img
        src="/assets/White Black Monogram M Business Logo (3).png"
        :alt="isAgentView ? 'الرد على الرسائل' : (companyName || 'لوحة العميل')"
        class="h-24 w-auto object-contain"
      />
    </div>

    <!-- التنقل الرئيسي — قائمة على طراز macOS/iOS -->
    <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-2">
      <router-link
        v-if="!isAgentView"
        :to="{ name: 'dashboard' }"
        class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[15px] text-white/90 transition-[background-color] duration-150 hover:bg-white/10"
        active-class="!bg-white/15 !text-white font-medium"
      >
        <LayoutDashboard class="h-5 w-5 shrink-0 opacity-90" />
        <span>لوحة التحكم</span>
      </router-link>
      <router-link
        :to="{ name: 'inbox' }"
        class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[15px] text-white/90 transition-[background-color] duration-150 hover:bg-white/10"
        active-class="!bg-white/15 !text-white font-medium"
      >
        <Inbox class="h-5 w-5 shrink-0 opacity-90" />
        <span>صندوق الوارد</span>
      </router-link>
      <!-- تبويبات المحادثات عند عرض محادثة -->
      <div v-if="isConversationRoute && sidebarConversations.length" class="mr-1 mt-1 max-h-44 space-y-0.5 overflow-y-auto pr-2">
        <router-link
          v-for="c in sidebarConversations"
          :key="c.id"
          :to="{ name: 'conversation', params: { id: c.id } }"
          class="flex items-center gap-2.5 rounded-[8px] px-3 py-2 text-[14px] text-white/85 transition-[background-color] duration-150 hover:bg-white/10"
          :class="{ '!bg-white/15 font-medium text-white': currentConversationId === String(c.id) }"
        >
          <span v-if="c.has_unread" class="h-1.5 w-1.5 shrink-0 rounded-full bg-[#34c759]" />
          <span class="min-w-0 truncate">{{ c.customer_name || c.customer_phone || 'محادثة ' + c.id }}</span>
        </router-link>
      </div>
      <template v-if="!isAgentView">
        <router-link
          :to="{ name: 'leads' }"
          class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[15px] text-white/90 transition-[background-color] duration-150 hover:bg-white/10"
          active-class="!bg-white/15 !text-white font-medium"
        >
          <Users class="h-5 w-5 shrink-0 opacity-90" />
          <span>العملاء المحتملون</span>
        </router-link>
        <router-link
          :to="{ name: 'employees' }"
          class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[15px] text-white/90 transition-[background-color] duration-150 hover:bg-white/10"
          active-class="!bg-white/15 !text-white font-medium"
        >
          <UserCog class="h-5 w-5 shrink-0 opacity-90" />
          <span>الموظفون</span>
        </router-link>
        <router-link
          :to="{ name: 'automation' }"
          class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[15px] text-white/90 transition-[background-color] duration-150 hover:bg-white/10"
          active-class="!bg-white/15 !text-white font-medium"
        >
          <Settings class="h-5 w-5 shrink-0 opacity-90" />
          <span>إعدادات الأتمتة</span>
        </router-link>
        <router-link
          :to="{ name: 'statistics' }"
          class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[15px] text-white/90 transition-[background-color] duration-150 hover:bg-white/10"
          active-class="!bg-white/15 !text-white font-medium"
        >
          <BarChart3 class="h-5 w-5 shrink-0 opacity-90" />
          <span>الإحصائيات</span>
        </router-link>
        <router-link
          :to="{ name: 'campaigns' }"
          class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[15px] text-white/90 transition-[background-color] duration-150 hover:bg-white/10"
          active-class="!bg-white/15 !text-white font-medium"
        >
          <Megaphone class="h-5 w-5 shrink-0 opacity-90" />
          <span>حملات إعلانية</span>
        </router-link>
      </template>

      <div class="my-3 pt-3">
        <p class="mb-1.5 px-3 text-[12px] font-medium uppercase tracking-widest text-white/50">الحساب</p>
        <router-link
          :to="{ name: 'account' }"
          class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[15px] text-white/90 transition-[background-color] duration-150 hover:bg-white/10"
          active-class="!bg-white/15 !text-white font-medium"
        >
          <UserCircle class="h-5 w-5 shrink-0 opacity-90" />
          <span>الحساب</span>
        </router-link>
      </div>
    </nav>

    <!-- المستخدم وتسجيل الخروج -->
    <div class="p-3">
      <div class="rounded-2xl bg-white/15 px-4 py-3.5">
        <div class="flex items-center gap-3">
          <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/25 text-[13px] font-semibold text-white">
            {{ userInitial }}
          </div>
          <div class="min-w-0 flex-1">
            <p class="truncate text-[13px] font-medium text-white">{{ userName }}</p>
            <p class="truncate text-[11px] text-white/70">{{ userEmail }}</p>
          </div>
        </div>
        <button
          type="button"
          class="mt-3 flex w-full items-center justify-center gap-2 rounded-[10px] bg-white/15 py-2.5 text-[13px] font-medium text-white transition-colors hover:bg-white/25 active:scale-[0.98]"
          @click="logout"
        >
          <LogOut class="h-[16px] w-[16px]" />
          <span>تسجيل الخروج</span>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { get } from '../api';
import {
  LayoutDashboard,
  Inbox,
  Users,
  UserCog,
  Settings,
  BarChart3,
  Megaphone,
  UserCircle,
  LogOut,
} from 'lucide-vue-next';

const route = useRoute();
const user = ref<{ name?: string; email?: string; role?: string } | null>(null);
const companyName = ref<string>('');
const isAgentView = ref(false);
const sidebarConversations = ref<any[]>([]);

const isConversationRoute = computed(() => route.name === 'conversation');
const currentConversationId = computed(() => (route.params.id as string) ?? '');

const userName = computed(() => user.value?.name ?? 'مستخدم');
const userEmail = computed(() => user.value?.email ?? '');
const userInitial = computed(() => {
  const n = user.value?.name?.trim();
  if (!n) return '؟';
  const parts = n.split(/\s+/);
  if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  return n[0].toUpperCase();
});

async function fetchSidebarConversations() {
  if (route.name !== 'conversation') return;
  try {
    const res = await get<{ data: any[] }>('/inbox');
    sidebarConversations.value = res?.data ?? [];
  } catch {
    sidebarConversations.value = [];
  }
}

onMounted(async () => {
  try {
    const r = await fetch('/client/api/user', { credentials: 'same-origin' });
    const data = await r.json();
    user.value = data?.user ?? null;
    companyName.value = data?.company?.name ?? '';
    isAgentView.value = user.value?.role === 'agent';
  } catch {
    isAgentView.value = false;
  }
  await fetchSidebarConversations();
});

watch(
  () => route.name,
  (name) => {
    if (name === 'conversation') fetchSidebarConversations();
    else sidebarConversations.value = [];
  }
);

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function logout() {
  await fetch('/client/logout', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken ?? '',
    },
  });
  window.location.href = '/client/login';
}
</script>
