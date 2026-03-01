<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chatly — إدارة محادثات واتساب للشركات</title>
    <meta name="description" content="منصة متكاملة لإدارة محادثات واتساب، العملاء المحتملين، والرد السريع. اربط واتساب بيسنس بحسابك وادير كل المحادثات من مكان واحد.">
    <link rel="icon" type="image/png" href="{{ asset('assets/White_Black_Monogram_M_Business_Logo_-removebg-preview(2).png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#1263cf',
                        'brand-light': '#1a75e6',
                        'brand-dark': '#0d4d9e',
                    },
                    fontFamily: {
                        arabic: ['Noto Kufi Arabic', 'sans-serif'],
                        sans: ['Noto Kufi Arabic', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Noto Kufi Arabic', sans-serif; }
        .gradient-brand { background: linear-gradient(135deg, #1263cf 0%, #0d4d9e 100%); }
        .shadow-soft { box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 12px rgba(18,99,207,0.08); }
        .section-padding { padding-top: 5rem; padding-bottom: 5rem; }
        @media (min-width: 1024px) { .section-padding { padding-top: 7rem; padding-bottom: 7rem; } }
    </style>
</head>
<body class="bg-[#f5f5f7] text-neutral-900 antialiased">
    <!-- زر واتساب ثابت — أسفل يمين -->
    <a
        href="https://wa.me/{{ config('services.whatsapp.contact_number') }}?text=مرحباً، أريد الاستفسار عن خدمة Chatly"
        target="_blank"
        rel="noopener noreferrer"
        class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition-all hover:scale-110 hover:shadow-xl"
        aria-label="تواصل معنا على واتساب"
    >
        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>

    <!-- الهيدر -->
    <header class="sticky top-0 z-40 bg-brand shadow-sm">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="/" class="flex items-center">
                <img src="{{ asset('assets/White Black Monogram M Business Logo (3).png') }}" alt="Chatly" class="h-14 w-auto md:h-16" />
            </a>
            <nav class="flex items-center gap-6">
                <a href="#المميزات" class="text-[15px] font-medium text-white/90 transition hover:text-white">المميزات</a>
                <a href="#حالات-الاستخدام" class="text-[15px] font-medium text-white/90 transition hover:text-white">حالات الاستخدام</a>
                <a href="#كيف-تعمل" class="text-[15px] font-medium text-white/90 transition hover:text-white">كيف تعمل</a>
                <a href="#الأسعار" class="text-[15px] font-medium text-white/90 transition hover:text-white">الأسعار</a>
                <a href="/client/login" class="rounded-[12px] bg-white px-5 py-2.5 text-[15px] font-medium text-brand shadow-soft transition hover:bg-neutral-100">تسجيل الدخول</a>
            </nav>
        </div>
    </header>

    <main>
        <!-- الهيرو -->
        <section class="section-padding px-5 lg:px-8">
            <div class="mx-auto max-w-6xl text-center">
                <h1 class="text-4xl font-bold tracking-tight text-neutral-900 md:text-5xl lg:text-6xl">
                    كل محادثات واتساب.<br />
                    <span class="text-brand">في مكان واحد.</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-[18px] leading-relaxed text-neutral-600">
                    اربط واتساب بزنس بحسابك، واستقبل رسائل العملاء، ورد بسرعة، واتبع العملاء المحتملين من لوحة تحكم واحدة. بسيط، آمن، واحترافي.
                </p>
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <a href="/client/login" class="inline-flex items-center rounded-[12px] bg-brand px-6 py-3.5 text-[16px] font-semibold text-white shadow-soft transition hover:bg-brand-dark">
                        ابدأ مجاناً
                    </a>
                    <a href="#كيف-تعمل" class="inline-flex items-center rounded-[12px] border border-neutral-300 bg-white px-6 py-3.5 text-[16px] font-medium text-neutral-700 transition hover:bg-neutral-50">
                        كيف يعمل؟
                    </a>
                </div>
            </div>
        </section>

        <!-- المميزات -->
        <section id="المميزات" class="section-padding border-t border-neutral-200/60 bg-white">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <h2 class="text-center text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">لماذا Chatly؟</h2>
                <p class="mx-auto mt-3 max-w-xl text-center text-[17px] text-neutral-600">أدوات تساعدك على البيع والدعم عبر واتساب دون تعقيد.</p>
                <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-neutral-200/80 bg-[#f5f5f7]/50 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand/10 text-brand">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </div>
                        <h3 class="mt-4 text-[18px] font-semibold text-neutral-900">صندوق وارد موحّد</h3>
                        <p class="mt-2 text-[15px] leading-relaxed text-neutral-600">كل محادثات واتساب في جدول واحد. ابحث، صنّف، ورد من الويب دون فتح الهاتف.</p>
                    </div>
                    <div class="rounded-2xl border border-neutral-200/80 bg-[#f5f5f7]/50 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand/10 text-brand">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <h3 class="mt-4 text-[18px] font-semibold text-neutral-900">فريقك كلّه على نفس المنصة</h3>
                        <p class="mt-2 text-[15px] leading-relaxed text-neutral-600">موظفون ومديرون. عيّن المحادثات، تابع الردود، واحصائيات لكل موظف.</p>
                    </div>
                    <div class="rounded-2xl border border-neutral-200/80 bg-[#f5f5f7]/50 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand/10 text-brand">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <h3 class="mt-4 text-[18px] font-semibold text-neutral-900">عملاء محتملون وإحصائيات</h3>
                        <p class="mt-2 text-[15px] leading-relaxed text-neutral-600">تتبع الليدرز، حالة الاهتمام، ورسوم بيانية للرسائل والردود شهرياً.</p>
                    </div>
                    <div class="rounded-2xl border border-neutral-200/80 bg-[#f5f5f7]/50 p-6 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand/10 text-brand">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <h3 class="mt-4 text-[18px] font-semibold text-neutral-900">آمن ومتعدد الشركات</h3>
                        <p class="mt-2 text-[15px] leading-relaxed text-neutral-600">كل شركة لها بياناتها ومنظورها. ادارة صلاحيات وربط واتساب لكل شركة.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- من يناسب Chatly؟ — قسم تسويقي مع صورة -->
        <section id="حالات-الاستخدام" class="section-padding border-t border-neutral-200/60 bg-[#f5f5f7]">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                    <div class="order-2 lg:order-1">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&q=80" alt="فريق عمل يتعاون" class="rounded-2xl shadow-[0_8px_30px_rgba(0,0,0,0.08)]" />
                    </div>
                    <div class="order-1 lg:order-2">
                        <h2 class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">من يناسب Chatly؟</h2>
                        <p class="mt-4 text-[17px] leading-relaxed text-neutral-600">كل شركة تتواصل مع عملائها عبر واتساب تحتاج لوحة تحكم واحدة — سواء كنت تبيع، تدعم، أو تتابع الليدرز.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-4 rounded-xl bg-white p-4 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">🛒</span>
                                <div>
                                    <h3 class="font-semibold text-neutral-900">تجارة إلكترونية ومتاجر</h3>
                                    <p class="mt-1 text-[15px] text-neutral-600">رد على استفسارات الطلبات، التتبع، والاسترجاع من مكان واحد.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4 rounded-xl bg-white p-4 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">📞</span>
                                <div>
                                    <h3 class="font-semibold text-neutral-900">خدمة العملاء والمبيعات</h3>
                                    <p class="mt-1 text-[15px] text-neutral-600">لا تفوت أي رسالة. صنّف المحادثات وعيّنها لفريقك بثوانٍ.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4 rounded-xl bg-white p-4 shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">📊</span>
                                <div>
                                    <h3 class="font-semibold text-neutral-900">وكالات وفرق تسويق</h3>
                                    <p class="mt-1 text-[15px] text-neutral-600">ادير حسابات عدة عملاء من منصة واحدة بأمان وكفاءة.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- كيف يغيّر Chatly يومك؟ — صورة + موضوع -->
        <section class="section-padding border-t border-neutral-200/60 bg-white">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">رد أسرع. عملاء أوفر.</h2>
                        <p class="mt-4 text-[17px] leading-relaxed text-neutral-600">بدون Chatly، الرسائل تتوزع على الهواتف والواتساب الشخصي. مع Chatly، كل المحادثات في صندوق وارد واحد — من الكمبيوتر أو الموبايل — مع إشعارات وتصنيف تلقائي.</p>
                        <div class="mt-8 flex flex-wrap gap-6">
                            <div class="flex items-center gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                </span>
                                <div>
                                    <span class="block font-semibold text-neutral-900">رد فوري</span>
                                    <span class="text-[14px] text-neutral-500">من أي جهاز</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand/10 text-brand">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                </span>
                                <div>
                                    <span class="block font-semibold text-neutral-900">لا رسالة تضيع</span>
                                    <span class="text-[14px] text-neutral-500">تتبع وتصنيف</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&q=80" alt="شاشة عمل على اللابتوب" class="rounded-2xl shadow-[0_8px_30px_rgba(0,0,0,0.08)]" />
                    </div>
                </div>
            </div>
        </section>

        <!-- أرقام تتحدث — إحصائيات تسويقية -->
        <section class="section-padding border-t border-neutral-200/60 bg-brand">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <h2 class="text-center text-3xl font-bold tracking-tight text-white md:text-4xl">لماذا يختارون Chatly؟</h2>
                <p class="mx-auto mt-3 max-w-xl text-center text-[17px] text-white/85">أرقام ونتائج حقيقية من منصتنا وعملائنا.</p>
                <div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="text-center">
                        <div class="text-4xl font-bold tracking-tight text-white md:text-5xl">واحد</div>
                        <p class="mt-2 text-[17px] font-medium text-white/90">صندوق وارد</p>
                        <p class="text-[15px] text-white/75">كل المحادثات في مكان واحد</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold tracking-tight text-white md:text-5xl">دقائق</div>
                        <p class="mt-2 text-[17px] font-medium text-white/90">إعداد سريع</p>
                        <p class="text-[15px] text-white/75">اربط واتساب وابدأ فوراً</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold tracking-tight text-white md:text-5xl">24/7</div>
                        <p class="mt-2 text-[17px] font-medium text-white/90">متاح دائماً</p>
                        <p class="text-[15px] text-white/75">من الويب والجوال</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold tracking-tight text-white md:text-5xl">آمن</div>
                        <p class="mt-2 text-[17px] font-medium text-white/90">بياناتك محمية</p>
                        <p class="text-[15px] text-white/75">صلاحيات ونسخ احتياطي</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- شهادات أو ثقة — قسم قصير -->
        <section class="section-padding border-t border-neutral-200/60 bg-[#f5f5f7]">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                    <div>
                        <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?w=800&q=80" alt="شريك عمل يثق بالخدمة" class="rounded-2xl shadow-[0_8px_30px_rgba(0,0,0,0.08)]" />
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">منصة تثق بها فرق العمل</h2>
                        <p class="mt-4 text-[17px] leading-relaxed text-neutral-600">شركات من قطاعات مختلفة تستخدم Chatly يومياً للرد على العملاء، متابعة الليدرز، وتنظيم المحادثات. انضم لهم واختصر الوقت والجهد.</p>
                        <ul class="mt-6 space-y-3 text-[16px] text-neutral-700">
                            <li class="flex items-center gap-3">
                                <span class="h-2 w-2 rounded-full bg-brand"></span>
                                دعم فني بالعربي وسريع الاستجابة
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="h-2 w-2 rounded-full bg-brand"></span>
                                تحديثات مستمرة ومميزات جديدة
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="h-2 w-2 rounded-full bg-brand"></span>
                                لا قيود على عدد المحادثات أو الرسائل
                            </li>
                        </ul>
                        <a href="/client/login" class="mt-8 inline-flex rounded-xl bg-brand px-6 py-3 text-[16px] font-semibold text-white transition hover:bg-brand-dark">جرّب الآن</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- كيف تعمل -->
        <section id="كيف-تعمل" class="section-padding border-t border-neutral-200/60 bg-white">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <h2 class="text-center text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">كيف تبدأ؟</h2>
                <p class="mx-auto mt-3 max-w-xl text-center text-[17px] text-neutral-600">ثلاث خطوات وتكون جاهز لاستقبال العملاء على واتساب.</p>
                <div class="mt-16 grid gap-10 md:grid-cols-3">
                    <div class="text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand text-[22px] font-bold text-white">1</div>
                        <h3 class="mt-4 text-[18px] font-semibold text-neutral-900">اربط واتساب بزنس</h3>
                        <p class="mt-2 text-[15px] text-neutral-600">أضف رقم واتساب بزنس ورمز الوصول من تطبيقك في Meta. نربطك بالمنصة آلياً.</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand text-[22px] font-bold text-white">2</div>
                        <h3 class="mt-4 text-[18px] font-semibold text-neutral-900">استقبل المحادثات</h3>
                        <p class="mt-2 text-[15px] text-neutral-600">كل رسالة من العملاء تظهر في صندوق الوارد. صنّفها، عيّنها لموظفيك، ورد فوراً.</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand text-[22px] font-bold text-white">3</div>
                        <h3 class="mt-4 text-[18px] font-semibold text-neutral-900">اتبع النتائج</h3>
                        <p class="mt-2 text-[15px] text-neutral-600">إحصائيات، عملاء محتملون، ووقت الرد. كل ما تحتاجه لتحسين خدمة العملاء.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- الأسعار -->
        <section id="الأسعار" class="section-padding border-t border-neutral-200/60 bg-[#f5f5f7]">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <h2 class="text-center text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">الأسعار</h2>
                <p class="mx-auto mt-3 max-w-xl text-center text-[17px] text-neutral-600">اشتراك واحد. كل المميزات بدون حدود.</p>
                <div class="mt-14 flex justify-center">
                    <div class="relative w-full max-w-[420px] overflow-hidden rounded-[28px] bg-white shadow-[0_2px_8px_rgba(0,0,0,0.04),0_24px_48px_rgba(0,0,0,0.06)]">
                        <!-- بادج عرض رمضان -->
                        <div class="bg-gradient-to-l from-amber-400/90 to-amber-500/90 px-6 py-3 text-center">
                            <span class="inline-flex items-center gap-2 text-[15px] font-bold text-white drop-shadow-sm">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                عرض رمضان — خصم 50% لمدة سنة كاملة
                            </span>
                        </div>
                        <div class="p-8 md:p-10">
                            <p class="text-[15px] font-medium text-neutral-500">الاشتراك الشهري</p>
                            <!-- السعر الرئيسي: عرض رمضان -->
                            <div class="mt-3 flex items-baseline gap-2">
                                <span class="text-5xl font-bold tracking-tight text-neutral-900 md:text-6xl">500</span>
                                <span class="text-[19px] font-medium text-neutral-600">ريال</span>
                                <span class="text-[17px] text-neutral-400">/ شهر</span>
                            </div>
                            <p class="mt-1.5 flex items-center gap-2 text-[15px] text-neutral-400">
                                <span class="line-through">1,000 ريال</span>
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[13px] font-semibold text-emerald-700">وفر 6,000 ريال سنوياً</span>
                            </p>
                            <p class="mt-4 text-[15px] leading-relaxed text-neutral-600">
                                الاشتراك خلال رمضان يمنحك <strong class="text-neutral-800">نصف السعر</strong> لمدة 12 شهراً. بعدها يعود السعر العادي 1,000 ريال/شهر.
                            </p>
                            <ul class="mt-6 space-y-3 text-[15px] text-neutral-700">
                                <li class="flex items-center gap-3">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand/10 text-brand"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                    صندوق وارد موحد ومحادثات واتساب
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand/10 text-brand"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                    عملاء محتملون وإحصائيات
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand/10 text-brand"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                    فريق عمل ودعم فني
                                </li>
                            </ul>
                            <a href="/client/login" class="mt-8 flex w-full items-center justify-center gap-2 rounded-2xl bg-brand py-4 text-[17px] font-semibold text-white shadow-[0_4px_14px_rgba(18,99,207,0.35)] transition hover:bg-brand-dark hover:shadow-[0_6px_20px_rgba(18,99,207,0.4)]">
                                ابدأ الآن — 500 ريال/شهر
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- قسم تسويقي إضافي -->
        <section class="section-padding border-t border-neutral-200/60 bg-white">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="overflow-hidden rounded-3xl gradient-brand px-8 py-16 text-center text-white shadow-soft md:px-16">
                    <h2 class="text-3xl font-bold tracking-tight md:text-4xl">جاهز لربط واتساب بحسابك؟</h2>
                    <p class="mx-auto mt-4 max-w-xl text-[17px] text-white/90">انضم لشركات تستخدم Chatly كل يوم للرد على العملاء وإدارة المحادثات باحترافية.</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="/client/login" class="inline-flex rounded-[12px] bg-white px-6 py-3.5 text-[16px] font-semibold text-brand transition hover:bg-neutral-100">ابدأ الآن</a>
                        <a href="https://wa.me/{{ config('services.whatsapp.contact_number') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-[12px] border-2 border-white/80 bg-transparent px-6 py-3.5 text-[16px] font-semibold text-white transition hover:bg-white/10">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            تواصل عبر واتساب
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- الفوتر -->
        <footer class="border-t border-neutral-200/60 bg-white py-12">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('assets/White Black Monogram M Business Logo (3).png') }}" alt="Chatly" class="h-10 w-auto opacity-90" />
                    </a>
                    <div class="flex gap-8 text-[15px] text-neutral-600">
                        <a href="#المميزات" class="transition hover:text-brand">المميزات</a>
                        <a href="#كيف-تعمل" class="transition hover:text-brand">كيف تعمل</a>
                        <a href="/client/login" class="transition hover:text-brand">تسجيل الدخول</a>
                    </div>
                </div>
                <p class="mt-8 text-center text-[14px] text-neutral-500">© {{ date('Y') }} iTap Solution LTD. جميع الحقوق محفوظة.</p>
            </div>
        </footer>
    </main>
</body>
</html>
