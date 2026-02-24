# صندوق الوارد: شكل طلب رسالة العميل وطريقة رد الموظف

## 1️⃣ وصول رسالة من العميل (Webhook)

عندما يرسل العميل رسالة على واتساب، منصة Meta ترسل طلب **POST** لـ Webhook الخاص بك.

### الرابط (URL)

```
POST https://your-domain.com/webhook/whatsapp
```

(في التطوير المحلي يمكن استخدام ngrok مثلاً: `https://xxxx.ngrok.io/webhook/whatsapp`)

### شكل الطلب (Request Body) — WhatsApp Cloud API

الطلب يكون JSON بهذا الهيكل العام:

```json
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "BUSINESS_ACCOUNT_ID",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "15550001234",
              "phone_number_id": "PHONE_NUMBER_ID"
            },
            "contacts": [
              {
                "profile": { "name": "أحمد العميل" },
                "wa_id": "966501234567"
              }
            ],
            "messages": [
              {
                "from": "966501234567",
                "id": "wamid.xxxxxx",
                "timestamp": "1730000000",
                "type": "text",
                "text": {
                  "body": "مرحبا عايز اعرف اسعار المنتجات"
                }
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}
```

### الحقول المهمة للتطبيق

| المسار في الـ payload | الاستخدام في النظام |
|------------------------|----------------------|
| `value.metadata.phone_number_id` | لربط الطلب بالشركة (`company_id`) |
| `value.contacts[0].profile.name` | اختياري: اسم العميل في المحادثة |
| `value.messages[].from` | رقم العميل → يُخزَّن في `conversations.customer_phone` |
| `value.messages[].id` | معرف الرسالة من واتساب → `messages.meta_message_id` |
| `value.messages[].type` | نوع الرسالة: `text`, `image`, `document`, ... |
| `value.messages[].text.body` | نص الرسالة → `messages.message_body` |

### ماذا يحدث في الباكند؟

1. **WebhookController::handle** يستقبل الـ POST.
2. يستخرج من الـ payload: `phone_number_id` → `company_id`، و`from` → رقم العميل.
3. **ConversationRepository::findOrCreateByCustomerPhone** يبحث عن محادثة لهذا الرقم والشركة، أو ينشئ محادثة جديدة.
4. **MessageRepository::createFromIncoming** ينشئ رسالة في جدول `messages`:
   - `sender_type` = `customer`
   - `sender_id` = null
   - `message_body` = نص الرسالة (أو `[media]` للملفات)
   - `meta_message_id` = معرف واتساب
5. يُحدَّث `conversation.last_message_at`.
6. يُطلق حدث `WhatsAppMessageReceived` (لأتمتة الرد التلقائي إن وُجدت).

بعد ذلك المحادثة تظهر في **صندوق الوارد** للموظفين عند فتح:

```
GET http://127.0.0.1:8000/client/inbox
```

---

## 2️⃣ رد الموظف على العميل

الموظف يفتح المحادثة من الواجهة ثم يكتب الرد ويضغط «إرسال».

### من الواجهة (Vue)

1. المستخدم يفتح: `http://127.0.0.1:8000/client/inbox` ثم يختار محادثة.
2. ينتقل إلى: `http://127.0.0.1:8000/client/inbox/{conversation_id}`.
3. يكتب النص في حقل الرد ويضغط «إرسال».

### طلب الرد (Request) من الفرونت إلى الباكند

```
POST http://127.0.0.1:8000/client/api/inbox/{conversation_id}/reply
Content-Type: application/json
Cookie: (جلسة الموظف بعد تسجيل الدخول)

Body:
{
  "message": "أهلاً بيك، أسعار المنتجات متوفرة على الرابط التالي: ..."
}
```

### ماذا يحدث في الباكند؟

1. **InboxController::reply** يتأكد أن المستخدم مسجّل وداخل شركته، ويحصل على `conversation` بنفس الـ `company_id`.
2. ينشئ سجل في جدول `messages`:
   - `sender_type` = `agent`
   - `sender_id` = `auth()->id()` (الموظف الحالي)
   - `message_body` = النص المُرسل
   - `message_type` = `text`
   - `status` = `sent`
3. يُحدَّث `conversation.last_message_at`.
4. الـ API يرجع الرسالة المُنشأة (مع `employee_name`) ليعرضها الواجهة فوراً.

بهذا يكون **شكل الطلب من العميل** = Webhook POST بالهيكل أعلاه، و**طريقة رد الموظف** = POST من الواجهة إلى `/client/api/inbox/{id}/reply` مع `message` في الـ body، ويُسجَّل الموظف تلقائياً في `sender_id`.

---

## 3️⃣ إرسال الرد فعلياً إلى واتساب (خارج نطاق الـ MVP)

حالياً الرد يُخزَّن فقط في قاعدة البيانات. لإرساله إلى واتساب تحتاج لاحقاً إلى:

- استدعاء **WhatsApp Cloud API** (مثلاً من داخل `InboxController::reply` أو من Job بعد حفظ الرسالة).
- استخدام رقم المحادثة `conversation.customer_phone` كـ `to` وإرسال النص عبر رسالة نصية.

يمكن وضع هذا المنطق في `WhatsAppService` أو Listener لحدث مثل `AgentRepliedToConversation` عند الرغبة في ربط الرد بالواتساب.
