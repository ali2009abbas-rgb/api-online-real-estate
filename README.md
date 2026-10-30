# 🏠 Darna Real Estate API - Laravel Enterprise Architecture

منصة عقارية متقدمة مبنية باستخدام أحدث إصدارات **Laravel** ومصممة وفق معايير المعمارية النظيفة (Clean Architecture)، مع دعم الـ API Versioning، ونظام الكاش الذكي باستخدام **Redis**، والتحكم بالصلاحيات عبر **Laravel Sanctum**.

---

## 🛠️ التقنيات المستخدمة (Tech Stack)

- **Framework:** Laravel (API-first architecture)
- **Authentication:** Laravel Sanctum (Token-based)
- **Caching & Performance:** Redis (In-memory data store)
- **Database Management:** MySQL / Eloquent ORM (Polymorphic Relations, Global & Local Scopes)
- **Data Validation:** Form Requests with Custom Arabic Error Messages
- **Design Pattern:** Service-Repository Pattern (Thin Controllers, Reusable Services)

---

## 🚀 تفاصيل النسخ (API Versioning: v1 & v2)

التطبيق يدعم نسختين متوازيتين لضمان مرونة استهلاك الـ API من قبل تطبيقات الموبايل أو الويب دون كسر التوافقية:

1. **النسخة الأولى (`/api/v1/`)**:
    - ترجع الأسعار كقيم عددية عادية (`float`).
    - مخصصة للعملاء والمستخدمين عبر البطاقات الخفيفة (Listing Properties).

2. **النسخة الثانية (`/api/v2/`)**:
    - ترجع الأسعار كـ `Object` متكامل يضم القيمة، رمز العملة، واسمها (مثلاً: `{"amount": 5000000, "currency": "SYP", "symbol": "ل.س"}`).
    - تضمن استجابات أكثر ثراءً لتطبيقات الواجهات الحديثة.

---

## ⚡ آلية الكاش (Redis Caching Strategy)

لضمان أعلى أداء واستجابة سريعة تحت الضغط، تم دمج **Redis** لتخزين البيانات التي تُقرأ بكثافة وتتغير قليلاً:

- **العقارات المميزة (Featured Properties):**
    - تُخزن مؤقتاً في Redis بمدة صلاحية (TTL) قدرها **60 دقيقة** (`3600 ثانية`).
    - مفتاح الكاش مفصول بذكاء بين النسختين لعدم تداخل شكل الأسعار (`properties:featured:v1` و `properties:featured:v2`).
- **إحصائيات المحافظات (Governorates Stats):**
    - تخزين عدد العقارات المتاحة لكل محافظة لمدة **60 دقيقة**.
- **تفاصيل العقار (Property Details):**
    - تخزين تفاصيل العقار للزوار غير المسجلين لمدة **30 دقيقة** (`1800 ثانية`).
    - **ملاحظة أمنية:** يتم تجاوز الكاش فوراً في حال كان المستخدم مسجلاً دخول (`Authenticated`) لضمان عرض رقم التواصل الشخصي للمالك والمكتب بحسب الصلاحيات.

---

## 🔄 تفريغ وتحديث الكاش الذكي (Cache Invalidation)

عندما يقوم أي مكتب بتعديل تفاصيل عقار أو تغيير حالته، يجب ألا تظل البيانات القديمة ظاهرة للمستخدمين. تم تطبيق آلية ذكية لتفريغ الكاش (`CacheService::clearPropertyCache`):

1. مسح كاش تفاصيل العقار المحدد فوراً للنسختين (`v1` و `v2`).
2. مسح كاش العقارات المميزة والإحصائيات لتتحدث تلقائياً.
3. استخدام **Redis Keys Pattern (`property:*:*`)** للبحث عن المفاتيح المرتبطة وحذفها بذكاء وسرعة فائقة دون الحاجة لمعرفة كل مفتاح يدوياً.

---

## 🔒 الأمان والصلاحيات (Authentication & Authorization)

- **Sanctum Authentication:** حماية المسارات الحساسة مثل عرض بيانات المالك ورقم التواصل الشخصي بحيث لا تظهر إلا للمستخدمين المسجلين.
- **Global & Local Scopes:** تطبيق فلترة تلقائية تظهر العقارات المتاحة فقط للزوار، وتسمح للمكاتب برؤية كافة الحالات الخاصة بعقاراتهم.
- **Form Requests Validation:** حماية الخادم من طلبات البحث الضخمة بوضع حد أقصى لعدد النتائج في الصفحة الواحدة (`per_page` بحد أقصى 50) مع إرسال رسائل خطأ واضحة باللغة العربية.

---

## 📥 تشغيل المشروع محلياً (Installation)

تثبيت الحزم:

composer install

إعداد ملف البيئة .env وضبط اتصال قاعدة البيانات وـ Redis:

CACHE_DRIVER=redis

REDIS_HOST=127.0.0.1

REDIS_PORT=6379

تشغيل الهجرة والبذار لتوليد البيانات الضخمة (3000 عقار + الـ Token):

php artisan migrate:fresh --seed

تشغيل خادم التطوير:

php artisan serve
