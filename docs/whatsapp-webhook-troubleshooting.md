# استكشاف أخطاء واتساب: الرسائل لا تصل لصندوق الوارد

إذا حفظت للشركة (مثل Demo Company) **Access Token** و **WhatsApp Phone Number ID** وجربت إرسال رسالة من هاتفك للرقم ولم تظهر في صندوق الوارد، راجع التالي:

---

## 1. رابط الويب هوك يجب أن يكون **عاماً** (Meta تصل له من الإنترنت)

منصة Meta ترسل طلبات POST إلى السيرفر الخاص بك. إذا كنت تشغّل التطبيق على **localhost** (`http://127.0.0.1:8000`) فإن Meta **لا تستطيع الوصول** لهذا العنوان.

**الحل:**

- **في التطوير:** استخدم أداة مثل **ngrok** لتعريض localhost لعنوان عام:
  ```bash
  ngrok http 8000
  ```
  ثم استخدم الرابط الذي يعطيك إياه (مثل `https://abc123.ngrok.io`) في إعدادات الويب هوك:
  ```
  https://abc123.ngrok.io/webhook/whatsapp
  ```
- **في الإنتاج:** ضع التطبيق على سيرفر له دومين وعنوان HTTPS (مثل `https://yourdomain.com`) واستخدم:
  ```
  https://yourdomain.com/webhook/whatsapp
  ```

---

## 2. إعداد الويب هوك في لوحة Meta (Facebook Developers)

1. ادخل إلى [developers.facebook.com](https://developers.facebook.com) → تطبيقك → **WhatsApp** → **Configuration** (الإعدادات).
2. في قسم **Webhook**:
   - **Callback URL:** ضع الرابط العام أعلاه (مثل `https://yourdomain.com/webhook/whatsapp` أو رابط ngrok).
   - **Verify token:** اختر أي نص سري (مثلاً `my_secret_verify_123`) واحفظه.
3. اضغط **Verify and Save**. عندها Meta يرسل طلب **GET** لرابطك؛ التطبيق يقارن `hub_verify_token` مع القيمة في `.env` (انظر التالي). إذا تطابقا يرجع الـ challenge وتنجح التحقق.

---

## 3. متغير Verify Token في ملف `.env`

يجب أن تكون القيمة في السيرفر **نفسها** التي أدخلتها في حقل **Verify token** في لوحة Meta.

أضف في `.env` (أو عدّلها):

```env
WHATSAPP_WEBHOOK_VERIFY_TOKEN=my_secret_verify_123
```

(استبدل `my_secret_verify_123` بما وضعته في Meta.) ثم أعد تشغيل السيرفر إذا لزم.

---

## 4. تطابق WhatsApp Phone Number ID

الشركة في النظام (مثل Demo Company) يجب أن يكون فيها **رقم واتساب (Phone Number ID)** مطابق **بالضبط** لما ترسله Meta في الـ webhook.

- من أين تأخذ القيمة الصحيحة؟
  - من [Meta Business Suite](https://business.facebook.com) أو من لوحة التطبيق: **WhatsApp** → **API Setup** أو **Phone numbers** → اختر الرقم → انسخ **Phone number ID** (رقم طويل، مثل `1234567890123456`).
- أين تضعه في النظام؟
  - من لوحة العميل: **الحساب** → حقل "رقم واتساب (Phone Number ID)"، أو من لوحة المدير عند تعديل الشركة.
- لا تضف مسافات زائدة في بداية أو نهاية القيمة. النظام يطابق بعد قص المسافات، لكن الأفضل حفظ القيمة كما هي من Meta.

---

## 5. التحقق من اللوج

عند وصول طلب POST من Meta للويب هوك، التطبيق يكتب في لوج واتساب. راجع الملف:

```
storage/logs/whatsapp.log
```

- إذا ظهر **"Webhook POST received"** مع `has_entry: true` فالمشكلة غالباً ليست في وصول الطلب، بل في ربط `phone_number_id` بالشركة.
- إذا ظهر **"No company found for webhook phone_number_id"** مع رقم معيّن، فمعناه أنه لا توجد شركة لديها **WhatsApp Phone Number ID** مطابق لهذا الرقم. راجع الفقرة 4 أعلاه وتأكد أن شركتك (Demo Company) تحتوي نفس القيمة.

---

## 6. ملخص سريع

| المشكلة | ما الذي تفعله؟ |
|--------|-----------------|
| التطبيق على localhost | استخدم ngrok أو انشر على سيرفر بعنوان عام HTTPS |
| التحقق (Verify) يفشل في Meta | ضع `WHATSAPP_WEBHOOK_VERIFY_TOKEN` في `.env` مطابقاً لـ Verify token في Meta |
| الطلب يصل لكن لا تظهر محادثة | تأكد أن **Phone Number ID** في إعدادات الشركة = القيمة من Meta بالضبط، وراجع `storage/logs/whatsapp.log` |

بعد أي تعديل على `.env` أو إعدادات الويب هوك في Meta، جرّب إرسال رسالة جديدة من واتساب وراجع اللوج وصندوق الوارد مرة أخرى.
