<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Lead;
use App\Models\User;
use App\Services\WhatsAppService\Models\AutomationSetting;
use App\Services\WhatsAppService\Models\Conversation;
use App\Services\WhatsAppService\Models\Message;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCompanySeeder extends Seeder
{
    /**
     * يملأ البورتال الخاص بشركة Demo (company_demo@gmail.com) ببيانات تجريبية:
     * موظفون، محادثات، رسائل، عملاء محتملون، إعدادات أتمتة، وإحصائيات.
     */
    public function run(): void
    {
        $company = Company::query()->where('email', 'company_demo@gmail.com')->first();
        if (!$company) {
            $this->command->warn('Company company_demo@gmail.com not found. Run DatabaseSeeder first.');
            return;
        }

        $companyId = $company->id;

        // المستخدم الرئيسي (company_admin) — موجود من DatabaseSeeder
        $admin = User::query()->where('email', 'company_demo@gmail.com')->first();
        if (!$admin) {
            $this->command->warn('Demo company user not found.');
            return;
        }

        // ——— موظفون (employees) ———
        $employees = $this->seedEmployees($companyId, $admin->id);

        // ——— إعدادات الأتمتة ———
        $this->seedAutomationSettings($companyId, $employees->first()?->id);

        // ——— محادثات + رسائل ———
        $conversationsData = $this->getConversationsData();
        $conversations = [];
        $now = now();

        foreach ($conversationsData as $i => $data) {
            $assignedTo = $data['assigned_to'] !== null && isset($employees[$data['assigned_to']])
                ? $employees[$data['assigned_to']]->id
                : null;

            $conv = Conversation::query()->updateOrCreate(
                [
                    'company_id' => $companyId,
                    'customer_phone' => $data['customer_phone'],
                ],
                [
                    'customer_name' => $data['customer_name'],
                    'status' => $data['status'],
                    'closure_interest' => $data['closure_interest'] ?? null,
                    'assigned_to' => $assignedTo,
                    'last_message_at' => $data['last_message_at'] ?? $now->copy()->subMinutes($i * 30),
                ]
            );
            $conversations[] = $conv;

            if ($conv->messages()->count() > 0) {
                continue;
            }

            foreach ($data['messages'] as $j => $msgData) {
                $createdAt = $msgData['created_at'] ?? $conv->last_message_at?->copy()->subMinutes(count($data['messages']) - $j);
                Message::query()->create([
                    'company_id' => $companyId,
                    'conversation_id' => $conv->id,
                    'sender_type' => $msgData['sender_type'],
                    'sender_id' => $msgData['sender_type'] === 'agent' ? ($employees[$msgData['sender_index']]->id ?? $admin->id) : null,
                    'message_type' => 'text',
                    'message_body' => $msgData['body'],
                    'status' => $msgData['status'] ?? 'sent',
                    'read_at' => $msgData['read_at'] ?? null,
                    'created_at' => $createdAt,
                ]);
            }
        }

        // ——— عملاء محتملون (Leads) مرتبطون ببعض المحادثات ———
        $this->seedLeads($companyId, $conversations, $employees, $admin);

        // ——— إحصائيات يومية (daily_stats) إن وُجد الجدول ———
        if (DB::getSchemaBuilder()->hasTable('daily_stats')) {
            $this->seedDailyStats($companyId);
        }

        $this->command->info('Demo data for company_demo@gmail.com has been seeded (employees, conversations, messages, leads, automation).');
    }

    private function seedEmployees(int $companyId, int $adminId): \Illuminate\Support\Collection
    {
        $list = [
            ['name' => 'أحمد محمد', 'email' => 'ahmed.demo@example.com'],
            ['name' => 'سارة علي', 'email' => 'sara.demo@example.com'],
            ['name' => 'خالد العميل', 'email' => 'khalid.demo@example.com'],
        ];

        $employees = collect();
        foreach ($list as $i => $row) {
            $user = User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => bcrypt('password123'),
                    'role' => 'employee',
                    'status' => $i === 2 ? 'inactive' : 'active',
                    'company_id' => $companyId,
                ]
            );
            $employees->push($user);
        }
        return $employees;
    }

    private function seedAutomationSettings(int $companyId, ?int $defaultEmployeeId): void
    {
        AutomationSetting::query()->updateOrCreate(
            ['company_id' => $companyId],
            [
                'welcome_message' => 'مرحباً بك! كيف يمكننا مساعدتك اليوم؟',
                'fallback_message' => 'شكراً لتواصلك. سنرد عليك في أقرب وقت.',
                'auto_assign_after_minutes' => 2,
                'default_employee_id' => $defaultEmployeeId,
            ]
        );
    }

    private function getConversationsData(): array
    {
        $baseTime = now()->subDays(5);
        return [
            [
                'customer_phone' => '966501234567',
                'customer_name' => 'محمد العتيبي',
                'status' => 'assigned',
                'closure_interest' => null,
                'assigned_to' => 0,
                'last_message_at' => $baseTime->copy()->addHours(2),
                'messages' => [
                    ['sender_type' => 'customer', 'body' => 'السلام عليكم، عندكم شحن لدبي؟', 'created_at' => $baseTime->copy(), 'read_at' => $baseTime->copy()->addMinutes(5)],
                    ['sender_type' => 'agent', 'body' => 'وعليكم السلام. نعم نوفر الشحن لدبي خلال 3–5 أيام.', 'sender_index' => 0, 'created_at' => $baseTime->copy()->addMinutes(3)],
                    ['sender_type' => 'customer', 'body' => 'كم تكلفة الشحن لصندوق واحد؟', 'created_at' => $baseTime->copy()->addMinutes(10), 'read_at' => $baseTime->copy()->addMinutes(15)],
                    ['sender_type' => 'agent', 'body' => 'حسب الوزن والوجهة. راسلنا بالوزن التقريبي وسنرسل السعر.', 'sender_index' => 0, 'created_at' => $baseTime->copy()->addMinutes(12)],
                ],
            ],
            [
                'customer_phone' => '966509876543',
                'customer_name' => 'فاطمة الزهراني',
                'status' => 'new',
                'closure_interest' => null,
                'assigned_to' => null,
                'last_message_at' => $baseTime->copy()->addDay()->addHours(1),
                'messages' => [
                    ['sender_type' => 'customer', 'body' => 'هل الباقة الشهرية تشمل الدعم الفني؟', 'created_at' => $baseTime->copy()->addDay(), 'read_at' => null],
                ],
            ],
            [
                'customer_phone' => '966551112233',
                'customer_name' => 'عمر القحطاني',
                'status' => 'closed',
                'closure_interest' => 'interested',
                'assigned_to' => 1,
                'last_message_at' => $baseTime->copy()->addDays(2)->addHours(3),
                'messages' => [
                    ['sender_type' => 'customer', 'body' => 'عايز أعرف أسعار الباقات', 'created_at' => $baseTime->copy()->addDays(2), 'read_at' => $baseTime->copy()->addDays(2)->addMinutes(2)],
                    ['sender_type' => 'agent', 'body' => 'أهلاً. الباقات تبدأ من 199 ريال. تحب نرسلك التفاصيل على واتساب؟', 'sender_index' => 1, 'created_at' => $baseTime->copy()->addDays(2)->addMinutes(1)],
                    ['sender_type' => 'customer', 'body' => 'ايوه ارسل التفاصيل', 'created_at' => $baseTime->copy()->addDays(2)->addHours(1), 'read_at' => $baseTime->copy()->addDays(2)->addHours(1)->addMinutes(1)],
                    ['sender_type' => 'agent', 'body' => 'تم إرسال التفاصيل. أي استفسار نحن هنا.', 'sender_index' => 1, 'created_at' => $baseTime->copy()->addDays(2)->addHours(3)],
                ],
            ],
            [
                'customer_phone' => '966577788899',
                'customer_name' => null,
                'status' => 'assigned',
                'closure_interest' => null,
                'assigned_to' => 0,
                'last_message_at' => $baseTime->copy()->addDays(3),
                'messages' => [
                    ['sender_type' => 'customer', 'body' => 'متى يصل الطلب؟', 'created_at' => $baseTime->copy()->addDays(3)->subMinutes(30), 'read_at' => $baseTime->copy()->addDays(3)],
                    ['sender_type' => 'agent', 'body' => 'تم شحنه أمس. متوقع خلال غداً.', 'sender_index' => 0, 'created_at' => $baseTime->copy()->addDays(3)],
                ],
            ],
            [
                'customer_phone' => '966533344455',
                'customer_name' => 'نورة الدوسري',
                'status' => 'closed',
                'closure_interest' => 'not_interested',
                'assigned_to' => 1,
                'last_message_at' => $baseTime->copy()->addDays(4),
                'messages' => [
                    ['sender_type' => 'customer', 'body' => 'شكراً، حالياً لا أحتاج الخدمة', 'created_at' => $baseTime->copy()->addDays(4), 'read_at' => $baseTime->copy()->addDays(4)->addMinutes(5)],
                    ['sender_type' => 'agent', 'body' => 'حاضر. نبقى في الخدمة لو احتجتينا.', 'sender_index' => 1, 'created_at' => $baseTime->copy()->addDays(4)->addMinutes(2)],
                ],
            ],
        ];
    }

    private function seedLeads(int $companyId, array $conversations, \Illuminate\Support\Collection $employees, User $admin): void
    {
        $statuses = ['new', 'contacted', 'converted', 'lost'];
        foreach ($conversations as $i => $conv) {
            $firstMsg = $conv->messages()->where('sender_type', 'customer')->orderBy('created_at')->first();
            $firstMessageAt = $firstMsg?->created_at ?? $conv->last_message_at ?? now();

            Lead::query()->updateOrCreate(
                [
                    'company_id' => $companyId,
                    'conversation_id' => $conv->id,
                ],
                [
                    'customer_phone' => $conv->customer_phone,
                    'customer_name' => $conv->customer_name,
                    'source' => 'whatsapp',
                    'first_message_at' => $firstMessageAt,
                    'assigned_to' => $conv->assigned_to ?? ($i < 2 ? $employees[0]->id : $admin->id),
                    'status' => $statuses[$i % count($statuses)],
                ]
            );
        }
    }

    private function seedDailyStats(int $companyId): void
    {
        for ($d = 7; $d >= 0; $d--) {
            $date = Carbon::today()->subDays($d);
            DB::table('daily_stats')->updateOrInsert(
                ['company_id' => $companyId, 'date' => $date],
                [
                    'total_messages' => rand(5, 25),
                    'new_conversations' => rand(1, 5),
                    'avg_response_time' => rand(60, 300),
                ]
            );
        }
    }
}
