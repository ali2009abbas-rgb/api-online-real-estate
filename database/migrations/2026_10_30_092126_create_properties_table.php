<?php

use App\Enums\Currency;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('governorate_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description');

            // السعر والعملة (ندعم تخزين الرقم والعملة لخدمة v1 و v2)
            $table->decimal('price', 12, 2);
            $table->string('currency')->default(Currency::SYP->value);

            // استخدام الـ Enums عبر قيمها النصية
            $table->string('listing_type')->default(ListingType::SALE->value); // للبيع أو للإيجار
            $table->string('status')->default(PropertyStatus::AVAILABLE->value); // متاح، محجوز، مباع

            $table->decimal('commission_percentage', 5, 2); // نسبة عمولة المكتب
            $table->timestamps();
            // Indexes أداء الفلاتر المطلوبة بالأسبوع الرابع
            $table->index('governorate_id');
            $table->index('listing_type');
            $table->index('price');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
