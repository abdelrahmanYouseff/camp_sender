<template>
  <div class="flex min-h-screen w-full flex-col md:flex-row">
    <!-- النصف الأيسر: الترحيب — يملأ الشاشة -->
    <div
      class="relative flex min-h-[40vh] w-full flex-col justify-between rounded-b-3xl bg-gradient-to-br from-[#3b82f6] via-[#2563eb] to-[#1d4ed8] px-8 py-10 text-white md:min-h-screen md:w-1/2 md:rounded-b-none md:rounded-r-3xl md:px-16 lg:px-20"
    >
      <!-- شكل دائري خفيف في الخلفية -->
      <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10" />
      <div class="pointer-events-none absolute bottom-20 left-1/2 h-64 w-64 rounded-full bg-white/5" />

      <div class="relative flex flex-1 flex-col justify-center">
        <h1 class="mb-3 text-3xl font-bold leading-tight md:text-4xl lg:text-5xl">
          مرحباً بكم في Chatly
        </h1>
        <p class="mb-6 text-lg font-semibold italic opacity-95 md:text-xl">
          "إدارة المحادثات والعملاء في مكان واحد"
        </p>
        <p class="max-w-md text-sm leading-relaxed text-white/90 md:text-base">
          لوحة التحكم تتيح لك متابعة المحادثات، إدارة العملاء المحتملين، وتعزيز التواصل مع عملائك بكل سلاسة وشفافية.
        </p>
      </div>

      <p class="relative mt-6 text-left text-xs text-white/80">© {{ new Date().getFullYear() }}. جميع الحقوق محفوظة.</p>
    </div>

    <!-- النصف الأيمن: نموذج الدخول — يملأ الشاشة -->
    <div class="relative flex min-h-[60vh] w-full flex-1 flex-col bg-white md:min-h-0 md:w-1/2">
      <!-- الشعار — أعلى اليسار -->
      <div class="absolute left-6 top-3 md:left-8 md:top-4">
        <img
          src="/assets/White_Black_Monogram_M_Business_Logo_-removebg-preview(2).png"
          alt="لوحة العميل"
          class="h-32 object-contain md:h-36"
        />
      </div>
      <div class="flex flex-1 flex-col items-center justify-center px-8 py-12 md:px-12 lg:px-16">
        <div class="w-full max-w-sm text-right">
          <h2 class="mb-8 text-center text-2xl font-bold text-gray-900">تسجيل الدخول إلى حسابك</h2>

          <form class="space-y-5" @submit.prevent="submit">
            <div>
              <label for="email" class="mb-1.5 block text-sm text-gray-600">البريد الإلكتروني</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                placeholder="أدخل البريد الإلكتروني"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-800 placeholder:text-gray-400 focus:border-[#2563eb] focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20"
              />
              <p v-if="errors.email" class="mt-1.5 text-sm text-red-600">{{ errors.email }}</p>
            </div>

            <div>
              <label for="password" class="mb-1.5 block text-sm text-gray-600">كلمة المرور</label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                placeholder="أدخل كلمة المرور"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-800 placeholder:text-gray-400 focus:border-[#2563eb] focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20"
              />
            </div>

            <button
              type="submit"
              class="w-full rounded-lg bg-[#2563eb] py-3.5 text-base font-semibold text-white transition hover:bg-[#1d4ed8] disabled:opacity-60"
              :disabled="loading"
            >
              {{ loading ? 'جاري تسجيل الدخول…' : 'تسجيل الدخول' }}
            </button>
          </form>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const loading = ref(false);
const form = reactive({ email: '', password: '' });
const errors = reactive<Record<string, string>>({});
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function submit() {
  loading.value = true;
  errors.email = '';
  try {
    const r = await fetch('/client/login', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken ?? '',
      },
      body: JSON.stringify(form),
    });
    const data = await r.json().catch(() => ({}));
    if (!r.ok) {
      errors.email = data?.message ?? data?.errors?.email?.[0] ?? 'بيانات الدخول غير صحيحة';
      return;
    }
    const redirect = (route.query.redirect as string) || '/client/inbox';
    window.location.href = redirect;
  } catch {
    errors.email = 'بيانات الدخول غير صحيحة';
  } finally {
    loading.value = false;
  }
}
</script>
