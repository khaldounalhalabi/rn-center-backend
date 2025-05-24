<?php

namespace Database\Seeders;

use App\FormulaParser\SystemVariables\SystemVariable;
use App\Models\FormulaVariable;
use App\Serializers\Translatable;
use Illuminate\Database\Seeder;

class FormulaVariableSeeder extends Seeder
{
    private function insertOrNot(array $data): void
    {
        if (!FormulaVariable::where('slug', $data['slug'])->first()) {
            FormulaVariable::insert($data);
        }
    }

    public function run(): void
    {
        $this->insertOrNot(
            [
                'name' => (new Translatable([
                    'en' => 'Absence days count',
                    'ar' => 'عدد أيام الغياب',
                ]))->toJson(),
                'description' => (new Translatable([
                    'en' => 'The absence days count for an employee per the applied pay cycle',
                    'ar' => 'عدد أيام الغياب للموظف خلال دورة الدفع المطبقة'
                ]))->toJson(),
                'slug' => SystemVariable::ABSENCE_DAYS_COUNT
            ]
        );

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Attendance days count',
                'ar' => 'عدد أيام الحضور',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The attendance days count for an employee per the applied pay cycle',
                'ar' => 'عدد أيام الحضور للموظف خلال دورة الدفع المطبقة'
            ]))->toJson(),
            'slug' => SystemVariable::ATTENDANCE_DAYS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Overtime days count',
                'ar' => 'عدد أيام العمل الإضافي',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The days the employee worked and was not required to work (overtime days) per the applied pay cycle',
                'ar' => 'عدد الأيام التي عمل فيها الموظف ولم يكن مطلوبًا منه العمل (أيام العمل الإضافي) خلال دورة الدفع المطبقة'
            ]))->toJson(),
            'slug' => SystemVariable::OVERTIME_DAYS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Worked hours',
                'ar' => 'ساعات العمل',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The total hours an employee worked per the applied pay cycle even those outside the center working hours',
                'ar' => 'إجمالي الساعات التي عمل فيها الموظف خلال دورة الدفع المطبقة حتى تلك الساعات خارج ساعات عمل المركز'
            ]))->toJson(),
            'slug' => SystemVariable::TOTAL_ATTENDANCE_HOURS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Absence hours',
                'ar' => 'ساعات الغياب',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => "The absence hours for an employee per the applied pay cycle",
                'ar' => 'ساعات الغياب للموظف خلال دورة الدفع المطبقة'
            ]))->toJson(),
            'slug' => SystemVariable::ABSENCE_HOURS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Overtime hours',
                'ar' => 'ساعات العمل الإضافي',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The overtime hours an employee worked per the applied pay cycle',
                'ar' => 'ساعات العمل الإضافي التي عمل فيها الموظف خلال دورة الدفع المطبقة'
            ]))->toJson(),
            'slug' => SystemVariable::OVERTIME_HOURS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Expected pay cycle working hours',
                'ar' => 'ساعات العمل المتوقعة في دورة الدفع',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The expected hours to be worked by an employee per the applied pay cycle',
                'ar' => 'الساعات المتوقع أن يعملها الموظف خلال دورة الدفع المطبقة'
            ]))->toJson(),
            'slug' => SystemVariable::EXPECTED_ATTENDANCE_HOURS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Expected pay cycle working days',
                'ar' => 'أيام العمل المتوقعة في دورة الدفع',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The expected days to be attended by an employee per the applied pay cycle',
                'ar' => 'الأيام المتوقع حضورها من قبل الموظف خلال دورة الدفع المطبقة'
            ]))->toJson(),
            'slug' => SystemVariable::EXPECTED_ATTENDANCE_DAYS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Total attendance hours without overtime hours',
                'ar' => 'إجمالي ساعات الحضور بدون ساعات العمل الإضافي',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The total hours an employee worked per the applied pay cycle without those outside the center working hours',
                'ar' => 'إجمالي الساعات التي عمل فيها الموظف خلال دورة الدفع المطبقة بدون تلك الساعات خارج ساعات عمل المركز'
            ]))->toJson(),
            'slug' => SystemVariable::TOTAL_ATTENDANCE_HOURS_COUNT_WITHOUT_OVERTIME_HOURS
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Total evening overtime hours count',
                'ar' => 'إجمالي ساعات العمل الإضافي المسائية',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The total hours an employee worked outside the center working hours and within the evening',
                'ar' => 'إجمالي الساعات التي عمل فيها الموظف خارج ساعات عمل المركز وخلال الفترة المسائية'
            ]))->toJson(),
            'slug' => SystemVariable::TOTAL_EVENING_OVERTIME_HOURS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Total morning overtime hours count',
                'ar' => 'إجمالي ساعات العمل الإضافي الصباحية',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'The total hours an employee worked outside the center working hours and within the morning',
                'ar' => 'إجمالي الساعات التي عمل فيها الموظف خارج ساعات عمل المركز وخلال الفترة الصباحية'
            ]))->toJson(),
            'slug' => SystemVariable::TOTAL_MORNING_OVERTIME_HOURS_COUNT
        ]);

        $this->insertOrNot([
            'name' => (new Translatable([
                'en' => 'Completed appointments',
                'ar' => 'الحجوزات المكتملة',
            ]))->toJson(),
            'description' => (new Translatable([
                'en' => 'Total completed appointments for the doctor in the selected pay period',
                'ar' => 'إجمالي المواعيد المكتملة للطبيب خلال فترة الدفع المحددة'
            ]))->toJson(),
            'slug' => SystemVariable::COMPLETED_APPOINTMENTS
        ]);
    }
}
