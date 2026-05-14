<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // أنواع الكوبونات الممكنة
        $types = ['fixed', 'percentage'];
        
        // قيم الخصم المختلفة (ثابت أو نسبة)
        $fixedValues = [10, 20, 30, 40, 50, 75, 100];
        $percentageValues = [5, 10, 15, 20, 25, 30, 40, 50];
        
        // الحد الأدنى للطلب
        $minAmounts = [null, 50, 100, 150, 200, 300, 500];
        
        for ($i = 1; $i <= 30; $i++) {
            // اختيار نوع الكوبون عشوائياً
            $type = $types[array_rand($types)];
            
            // اختيار قيمة الخصم حسب النوع
            if ($type === 'fixed') {
                $value = $fixedValues[array_rand($fixedValues)];
            } else {
                $value = $percentageValues[array_rand($percentageValues)];
            }
            
            // تحديد صلاحية الكوبون (بعضها منتهي، بعضها مستقبلي، بعضها حالي)
            $dateStatus = rand(1, 3);
            
            switch ($dateStatus) {
                case 1: // كوبون ساري حالياً
                    $startDate = Carbon::now()->subDays(rand(1, 30));
                    $endDate = Carbon::now()->addDays(rand(1, 60));
                    break;
                case 2: // كوبون منتهي
                    $startDate = Carbon::now()->subDays(rand(60, 365));
                    $endDate = Carbon::now()->subDays(rand(1, 30));
                    break;
                default: // كوبون مستقبلي
                    $startDate = Carbon::now()->addDays(rand(1, 30));
                    $endDate = Carbon::now()->addDays(rand(31, 90));
                    break;
            }
            
            // 80% من الكوبونات مفعلة، 20% غير مفعلة
            $isActive = rand(1, 100) <= 80;
            
            // الحد الأدنى للطلب (70% من الكوبونات عندها حد أدنى)
            $minimumOrderAmount = null;
            if (rand(1, 100) <= 70) {
                $minimumOrderAmount = $minAmounts[array_rand($minAmounts)];
            }
            
            // عدد مرات الاستخدام المسموح بها (60% من الكوبونات عندها تحديد)
            $usageLimit = null;
            if (rand(1, 100) <= 60) {
                $usageLimit = rand(10, 1000);
            }
            
            // عدد مرات الاستخدام الفعلية (عشوائي، لا يتجاوز الـ usage_limit)
            $usedCount = rand(0, $usageLimit ?? rand(5, 50));
            if ($usageLimit && $usedCount > $usageLimit) {
                $usedCount = $usageLimit;
            }
            
            Coupon::create([
                'code' => $this->generateUniqueCode($i),
                'type' => $type,
                'value' => $value,
                'minimum_order_amount' => $minimumOrderAmount,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usage_limit' => $usageLimit,
                'used_count' => $usedCount,
                'is_active' => $isActive,
                'description' => $this->generateDescription($type, $value),
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now(),
            ]);
        }
        
        $this->command->info('✅ تم إدخال ' . Coupon::count() . ' كوبون بنجاح');
        
        // عرض إحصائيات عن الكوبونات
        $this->command->newLine();
        $this->command->info('📊 إحصائيات الكوبونات:');
        $this->command->table(
            ['النوع', 'العدد', 'متوسط القيمة', 'أعلى قيمة', 'أقل قيمة'],
            [
                [
                    'fixed',
                    Coupon::where('type', 'fixed')->count(),
                    Coupon::where('type', 'fixed')->avg('value'),
                    Coupon::where('type', 'fixed')->max('value'),
                    Coupon::where('type', 'fixed')->min('value'),
                ],
                [
                    'percentage',
                    Coupon::where('type', 'percentage')->count(),
                    Coupon::where('type', 'percentage')->avg('value'),
                    Coupon::where('type', 'percentage')->max('value'),
                    Coupon::where('type', 'percentage')->min('value'),
                ],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('📌 حالة الكوبونات:');
        $this->command->table(
            ['الحالة', 'العدد'],
            [
                ['مفعل', Coupon::where('is_active', true)->count()],
                ['غير مفعل', Coupon::where('is_active', false)->count()],
                ['ساري حالياً', Coupon::where('start_date', '<=', Carbon::now())
                                    ->where('end_date', '>=', Carbon::now())
                                    ->where('is_active', true)
                                    ->count()],
                ['منتهي الصلاحية', Coupon::where('end_date', '<', Carbon::now())->count()],
                ['مستقبلي', Coupon::where('start_date', '>', Carbon::now())->count()],
            ]
        );
    }
    
    /**
     * توليد كود فريد للكوبون
     */
    private function generateUniqueCode($index): string
    {
        $prefixes = ['SAVE', 'DEAL', 'OFFER', 'DISCOUNT', 'SPECIAL', 'WELCOME', 'FLASH', 'SUMMER', 'WINTER', 'FALL'];
        
        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = rand(10, 99);
        
        $code = $prefix . $suffix;
        
        // التأكد من أن الكود غير مكرر
        if (Coupon::where('code', $code)->exists()) {
            return $this->generateUniqueCode($index + 1);
        }
        
        return $code;
    }
    
    /**
     * توليد وصف مناسب للكوبون
     */
    private function generateDescription($type, $value): string
    {
        $descriptions = [
            'fixed' => [
                "خصم {$value} جنيه على طلبك الأول",
                "وفر {$value} جنيه عند الشراء",
                "خصم {$value} جنيه لفترة محدودة",
                "عرض خاص: خصم {$value} جنيه",
                "احصل على خصم {$value} جنيه الآن",
            ],
            'percentage' => [
                "خصم {$value}% على كل المنتجات",
                "وفر {$value}% من قيمة مشترياتك",
                "خصم {$value}% لفترة محدودة",
                "عرض خاص: خصم {$value}%",
                "احصل على خصم {$value}% الآن",
            ],
        ];
        
        $options = $descriptions[$type];
        return $options[array_rand($options)];
    }
}