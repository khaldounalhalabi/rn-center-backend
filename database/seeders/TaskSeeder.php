<?php

namespace Database\Seeders;

use App\Enums\RolesPermissionEnum;
use App\Enums\TaskLabelEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = [
            [
                'title' => 'تحضير غرفة العمليات',
                'description' => 'تجهيز الأدوات الجراحية وتعقيم الغرفة قبل العملية.',
                'due_date' => Carbon::now()->addDays(1),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'إدخال بيانات المرضى الجدد',
                'description' => 'تسجيل المعلومات الشخصية والتاريخ المرضي في النظام.',
                'due_date' => Carbon::now()->addDays(2),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'متابعة نتائج التحاليل',
                'description' => 'مراجعة تقارير التحاليل وتسليمها للأطباء.',
                'due_date' => Carbon::now()->addDays(3),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'طلب أدوية من المستودع',
                'description' => 'إرسال طلب لتجديد مخزون الأدوية الناقصة.',
                'due_date' => Carbon::now()->addDays(5),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'تحديث جدول المناوبات',
                'description' => 'تنظيم دوام الأطباء والممرضين للأسبوع القادم.',
                'due_date' => Carbon::now()->addDays(7),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'فحص أجهزة الأشعة',
                'description' => 'إجراء صيانة دورية على أجهزة الأشعة والتأكد من عملها بشكل جيد.',
                'due_date' => Carbon::now()->addDays(10),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'إرسال تقارير مالية',
                'description' => 'تحضير تقارير الإيرادات والمصروفات وتسليمها للإدارة.',
                'due_date' => Carbon::now()->addDays(4),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'التواصل مع المرضى للمواعيد',
                'description' => 'الاتصال بالمرضى لتأكيد أو تغيير مواعيدهم القادمة.',
                'due_date' => Carbon::now()->addDays(1),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'إعداد تقرير إحصائي شهري',
                'description' => 'جمع بيانات العمليات والزيارات وتحضير تقرير للإدارة.',
                'due_date' => Carbon::now()->addDays(15),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
            [
                'title' => 'تنظيم الملفات الورقية',
                'description' => 'ترتيب الملفات القديمة في قسم الأرشيف.',
                'due_date' => Carbon::now()->addDays(20),
                'status' => fake()->randomElement(TaskStatusEnum::getAllValues()),
                'label' => fake()->randomElement(TaskLabelEnum::getAllValues()),
                'user_id' => 1,
            ],
        ];

        foreach ($tasks as &$taskData) {
            $taskData['created_at'] = now();
            $taskData['updated_at'] = now();
            $task = Task::create($taskData);
            $users = User::role(RolesPermissionEnum::SECRETARY['role'])
                ->inRandomOrder()
                ->limit(2)
                ->get();
            $task->users()->attach($users);
        }
    }
}
