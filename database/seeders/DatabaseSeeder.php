<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Office;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Media;
use App\Enums\PropertyStatus;
use App\Enums\ListingType;
use App\Enums\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تشغيل سييدر المحافظات أولاً
        $this->call([
            GovernorateSeeder::class,
        ]);

        // 2. إنشاء مستخدم ومكتب تجريبي خاص باختبارات الـ API مع Sanctum token ثابت
        $testUser = User::firstOrCreate(
            ['email' => 'office@darna.test'],
            [
                'name' => 'مكتب الدار العقاري',
                'password' => Hash::make('password')
            ]
        );

        // إنشاء توكن بسيط للمكتب كما طلب التاسك
        $token = $testUser->createToken('office-token')->plainTextToken;
        $this->command->info("Test Office Sanctum Token: " . $token);

        $testOffice = Office::firstOrCreate(
            ['user_id' => $testUser->id],
            [
                'governorate_id' => 1,
                'name' => 'مكتب الدار الهندسي العقاري',
                'phone' => '+963911111111'
            ]
        );

        // ربط شعار للمكتب باستخدام الـ Polymorphic Media
        $testOffice->logo()->updateOrCreate(
            ['mediable_id' => $testOffice->id, 'mediable_type' => Office::class],
            ['file_path' => 'offices/logos/default-logo.png']
        );

        // 3. توليد مكاتب وملاك وعقارات وهمية بضخامة (3000 عقار لاختبار الكاش والـ Pagination)
        $this->command->info('جاري توليد البيانات الضخمة للعقارات...');

        // سننشئ عدة ملاك افتراضيين
        $owners = Owner::factory(50)->create();

        // سننشئ عقارات موزعة على المحافظات
        // لتسريع العملية، سنستخدم الـ chunk أو التوليد المباشر بمساعدة الـ Factories أو الحلقات
        for ($i = 1; $i <= 3000; $i++) {
            $property = Property::create([
                'office_id' => $testOffice->id,
                'owner_id' => $owners->random()->id,
                'governorate_id' => rand(1, 5),
                'title' => "عقاري مميز رقم {$i}",
                'description' => "وصف تفصيلي للعقار رقم {$i} يتضمن كافة التفاصيل والمواصفات العالية.",
                'price' => rand(5000000, 500000000),
                'currency' => Currency::SYP->value,
                'listing_type' => rand(0, 1) ? ListingType::SALE->value : ListingType::RENT->value,
                'status' => PropertyStatus::AVAILABLE->value,
                'commission_percentage' => 2.5,
            ]);

            // إضافة صور متعددة للعقار عبر الـ Polymorphic
            $property->images()->create([
                'file_path' => "properties/images/prop-{$i}-1.jpg"
            ]);
            $property->images()->create([
                'file_path' => "properties/images/prop-{$i}-2.jpg"
            ]);
        }

        $this->command->info('تم الانتهاء من توليد البيانات بنجاح!');
    }
}
