<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'required_without' => 'حقل :attribute مطلوب عند عدم توفر :values.',
    'string' => 'يجب أن يكون حقل :attribute نصاً.',
    'array' => 'يجب أن يكون حقل :attribute مصفوفة.',
    'integer' => 'يجب أن يكون حقل :attribute رقماً صحيحاً.',
    'boolean' => 'يجب أن يكون حقل :attribute من نوع منطقي (صح/خطأ).',
    'max' => [
        'numeric' => 'يجب ألا يتجاوز حقل :attribute :max.',
        'file' => 'يجب ألا يتجاوز حجم الملف :max كيلوبايت.',
        'string' => 'يجب ألا يتجاوز طول النص :max حرفاً.',
        'array' => 'يجب ألا يتجاوز عدد العناصر :max.',
    ],
    'mimes' => 'يجب أن يكون حقل :attribute ملفاً من نوع: :values.',
    'exists' => 'الحقل :attribute المختار غير صالح.',
    'numeric' => 'يجب أن يكون حقل :attribute رقماً.',
    'min' => [
        'numeric' => 'يجب أن يكون حقل :attribute على الأقل :min.',
        'file' => 'يجب أن يكون حجم الملف على الأقل :min كيلوبايت.',
        'string' => 'يجب أن يكون طول النص على الأقل :min حرفاً.',
        'array' => 'يجب أن يحتوي الحقل :attribute على الأقل على :min عناصر.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'tax_id' => [
            'required' => 'الرقم الضريبي مطلوب.',
            'size'     => 'الرقم الضريبي يجب أن يكون 15 رقماً بالضبط.',
            'regex'    => 'الرقم الضريبي يجب أن يكون 15 رقماً ويبدأ وينتهي برقم 3.',
            'unique'   => 'الرقم الضريبي مسجل مسبقاً.',
        ],
        'city_id' => [
            'exists' => 'المدينة المختارة غير موجودة.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'company_name' => 'اسم الشركة',
        'tax_id' => 'الرقم الضريبي',
        'commercial_record' => 'السجل التجاري',
        'national_id' => 'الهوية الوطنية',
        'city_id' => 'المدينة',
        'password' => 'كلمة المرور',
        'mobile' => 'رقم الجوال',
        'email' => 'البريد الإلكتروني',
        'company_id' => 'الشركة',
        'is_meter_number_required' => 'رقم العداد مطلوب',
        'is_meter_image_required' => 'صورة العداد مطلوبة',
        'is_code_changeable' => 'إمكانية تغيير الكود',
        'is_code_generator' => 'توليد الكود',
        'code' => 'الكود',
        'login_with_otp' => 'الدخول برمز التحقق',
        'vehicle_limit_type' => 'نوع حد المركبة',
        'vehicle_balance_min' => 'الحد الأدنى لرصيد المركبة',
        'vehicles_can_use_fuel_balance' => 'إمكانية استخدام رصيد الوقود',
        'type_of_wallet' => 'نوع المحفظة',
        'auto_balance' => 'الرصيد التلقائي',
        'fuel_pull_limit' => 'حد سحب الوقود',
        'fuel_pull_limit_days' => 'أيام حد سحب الوقود',
        'wash_count' => 'عدد الغسلات',
        'vehicle_receiving_terms' => 'شروط استلام المركبة',
        'allow_multiple_assignments' => 'السماح بتعيينات متعددة',
    ],
];
