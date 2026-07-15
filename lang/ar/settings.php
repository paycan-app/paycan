<?php

return [
    // Success messages
    'saved' => [
        'title' => 'تم حفظ الإعدادات بنجاح',
        'body' => 'تم تحديث إعداداتك.',
    ],

    // App settings
    'app' => [
        'label' => 'إعدادات التطبيق',
        'description' => 'إعدادات التطبيق الأساسية',
        'name' => [
            'label' => 'اسم التطبيق',
            'helper' => 'اسم تطبيقك',
        ],
        'url' => [
            'label' => 'رابط التطبيق',
            'helper' => 'الرابط الأساسي لتطبيقك',
        ],
        'timezone' => [
            'label' => 'المنطقة الزمنية',
            'helper' => 'المنطقة الزمنية للتطبيق (مثل: UTC، America/New_York)',
        ],
        'locale' => [
            'label' => 'اللغة الافتراضية',
            'helper' => 'اللغة الافتراضية لجميع المستخدمين. يمكن للمستخدمين تغيير هذا حسب تفضيلاتهم.',
        ],
        'api_key' => [
            'label' => 'مفتاح API السري',
            'helper' => 'مفتاح API السري للتكاملات الخارجية',
        ],
    ],

    // Payment gateway settings
    'stripe' => [
        'label' => 'بوابة الدفع Stripe',
        'description' => 'تكوين إعدادات بوابة الدفع Stripe',
        'enabled' => [
            'label' => 'تفعيل Stripe',
            'helper' => 'تبديل لتفعيل أو تعطيل مدفوعات Stripe',
        ],
        'api_key' => [
            'label' => 'المفتاح السري',
            'helper' => 'مفتاح Stripe السري (مشفر ولا يظهر علنًا)',
        ],
        'publishable_key' => [
            'label' => 'المفتاح القابل للنشر',
            'helper' => 'مفتاح Stripe القابل للنشر',
        ],
        'enable_subscriptions' => [
            'label' => 'تفعيل دعم الاشتراكات',
            'helper' => 'السماح لهذه البوابة بمعالجة المدفوعات المتكررة',
        ],
        'webhook_secret' => [
            'label' => 'سر Webhook',
            'helper' => 'سر التوقيع للتحقق من أحداث Stripe',
        ],
    ],

    'paypal' => [
        'label' => 'بوابة الدفع PayPal',
        'description' => 'تكوين إعدادات بوابة الدفع PayPal',
        'enabled' => [
            'label' => 'تفعيل PayPal',
            'helper' => 'تبديل لتفعيل أو تعطيل مدفوعات PayPal',
        ],
        'client_id' => [
            'label' => 'معرف العميل',
            'helper' => 'معرف عميل تطبيق PayPal الخاص بك',
        ],
        'secret' => [
            'label' => 'سر العميل',
            'helper' => 'سر تطبيق PayPal (مشفر)',
        ],
        'mode' => [
            'label' => 'الوضع',
            'helper' => 'اختر sandbox للاختبار أو live للإنتاج',
            'options' => [
                'sandbox' => 'Sandbox (اختبار)',
                'live' => 'Live (إنتاج)',
            ],
        ],
        'enable_subscriptions' => [
            'label' => 'تفعيل دعم الاشتراكات',
            'helper' => 'السماح لهذه البوابة بمعالجة المدفوعات المتكررة',
        ],
        'webhook_id' => [
            'label' => 'معرف Webhook',
            'helper' => 'معرف webhook الخاص بـ PayPal للتحقق من الأحداث',
        ],
    ],

    // Email settings
    'email' => [
        'label' => 'إعدادات البريد الإلكتروني',
        'description' => 'تكوين إعدادات تسليم البريد الإلكتروني',
        'default_provider' => [
            'label' => 'مزود البريد الإلكتروني الافتراضي',
            'helper' => 'اختر خدمة البريد الإلكتروني لإرسال الرسائل',
        ],
        'from_address' => [
            'label' => 'عنوان البريد الإلكتروني للمرسل',
            'helper' => 'عنوان البريد الإلكتروني الذي سيتم إرسال الرسائل منه',
        ],
        'from_name' => [
            'label' => 'اسم المرسل',
            'helper' => 'الاسم الذي سيظهر في حقل "من"',
        ],
    ],

    'smtp' => [
        'label' => 'SMTP',
        'description' => 'تكوين إعدادات خادم SMTP لتسليم البريد الإلكتروني المباشر',
        'host' => [
            'label' => 'مضيف SMTP',
            'helper' => 'اسم مضيف خادم SMTP الخاص بك',
        ],
        'port' => [
            'label' => 'منفذ SMTP',
            'helper' => 'منفذ خادم SMTP (عادة 587 لـ TLS أو 465 لـ SSL)',
        ],
        'encryption' => [
            'label' => 'التشفير',
            'helper' => 'طريقة التشفير',
            'options' => [
                'none' => 'بدون',
            ],
        ],
        'username' => [
            'label' => 'اسم مستخدم SMTP',
            'helper' => 'اسم مستخدم مصادقة SMTP',
        ],
        'password' => [
            'label' => 'كلمة مرور SMTP',
            'helper' => 'كلمة مرور مصادقة SMTP (مشفرة)',
        ],
    ],

    'mailgun' => [
        'label' => 'Mailgun',
        'description' => 'تكوين خدمة البريد الإلكتروني Mailgun',
        'domain' => [
            'label' => 'نطاق Mailgun',
            'helper' => 'نطاق Mailgun الخاص بك',
        ],
        'secret' => [
            'label' => 'مفتاح API',
            'helper' => 'مفتاح API الخاص بـ Mailgun (مشفر)',
        ],
        'endpoint' => [
            'label' => 'نقطة النهاية',
            'helper' => 'اختر منطقة Mailgun الخاصة بك',
            'options' => [
                'us' => 'المنطقة الأمريكية',
                'eu' => 'المنطقة الأوروبية',
            ],
        ],
    ],

    'postmark' => [
        'label' => 'Postmark',
        'description' => 'تكوين خدمة البريد الإلكتروني Postmark',
        'token' => [
            'label' => 'رمز الخادم',
            'helper' => 'رمز API لخادم Postmark الخاص بك (مشفر)',
        ],
        'message_stream_id' => [
            'label' => 'معرف تدفق الرسائل',
            'helper' => 'معرف تدفق الرسائل (عادة "outbound")',
        ],
    ],

    'ses' => [
        'label' => 'Amazon SES',
        'description' => 'تكوين خدمة البريد الإلكتروني البسيطة من Amazon',
        'key' => [
            'label' => 'معرف مفتاح الوصول',
            'helper' => 'معرف مفتاح الوصول إلى AWS الخاص بك',
        ],
        'secret' => [
            'label' => 'مفتاح الوصول السري',
            'helper' => 'مفتاح الوصول السري لـ AWS (مشفر)',
        ],
        'region' => [
            'label' => 'منطقة AWS',
            'helper' => 'منطقة AWS حيث تم تكوين SES الخاص بك',
        ],
    ],

    'resend' => [
        'label' => 'Resend',
        'description' => 'تكوين خدمة البريد الإلكتروني Resend',
        'key' => [
            'label' => 'مفتاح API',
            'helper' => 'مفتاح API الخاص بـ Resend (مشفر)',
        ],
    ],

    'sendmail' => [
        'label' => 'Sendmail',
        'description' => 'تكوين Sendmail لتسليم البريد الإلكتروني المحلي',
        'path' => [
            'label' => 'مسار Sendmail',
            'helper' => 'المسار إلى ملف sendmail التنفيذي',
        ],
    ],

    // Social authentication
    'google' => [
        'label' => 'Google OAuth',
        'description' => 'تكوين المصادقة الاجتماعية من Google',
        'enabled' => [
            'label' => 'تفعيل تسجيل الدخول بـ Google',
            'helper' => 'السماح للمستخدمين بتسجيل الدخول باستخدام حساب Google الخاص بهم',
        ],
        'client_id' => [
            'label' => 'معرف العميل',
            'helper' => 'معرف عميل Google OAuth الخاص بك',
        ],
        'client_secret' => [
            'label' => 'سر العميل',
            'helper' => 'سر عميل Google OAuth (مشفر)',
        ],
        'redirect' => [
            'label' => 'رابط إعادة التوجيه',
            'helper' => 'رابط إعادة توجيه OAuth (قم بتكوين هذا في Google Console)',
        ],
    ],

    'facebook' => [
        'label' => 'Facebook OAuth',
        'description' => 'تكوين المصادقة الاجتماعية من Facebook',
        'enabled' => [
            'label' => 'تفعيل تسجيل الدخول بـ Facebook',
            'helper' => 'السماح للمستخدمين بتسجيل الدخول باستخدام حساب Facebook الخاص بهم',
        ],
        'client_id' => [
            'label' => 'معرف التطبيق',
            'helper' => 'معرف تطبيق Facebook الخاص بك',
        ],
        'client_secret' => [
            'label' => 'سر التطبيق',
            'helper' => 'سر تطبيق Facebook (مشفر)',
        ],
        'redirect' => [
            'label' => 'رابط إعادة التوجيه',
            'helper' => 'رابط إعادة توجيه OAuth (قم بتكوين هذا في Facebook Developer Console)',
        ],
    ],

    'github' => [
        'label' => 'GitHub OAuth',
        'description' => 'تكوين المصادقة الاجتماعية من GitHub',
        'enabled' => [
            'label' => 'تفعيل تسجيل الدخول بـ GitHub',
            'helper' => 'السماح للمستخدمين بتسجيل الدخول باستخدام حساب GitHub الخاص بهم',
        ],
        'client_id' => [
            'label' => 'معرف العميل',
            'helper' => 'معرف عميل GitHub OAuth الخاص بك',
        ],
        'client_secret' => [
            'label' => 'سر العميل',
            'helper' => 'سر عميل GitHub OAuth (مشفر)',
        ],
        'redirect' => [
            'label' => 'رابط إعادة التوجيه',
            'helper' => 'رابط إعادة توجيه OAuth (قم بتكوين هذا في GitHub Developer Settings)',
        ],
    ],

    // Fulfillment providers
    'downloader' => [
        'label' => 'تنزيل الملفات',
        'description' => 'تكوين إعدادات تنزيل الملفات',
        'enabled' => [
            'label' => 'تفعيل تنزيل الملفات',
            'helper' => 'السماح بتوفير المنتجات عبر تنزيل الملفات',
        ],
        'link_expiry' => [
            'label' => 'انتهاء صلاحية الرابط (ساعات)',
            'helper' => 'عدد الساعات التي تظل فيها روابط التنزيل صالحة',
        ],
        'max_downloads' => [
            'label' => 'الحد الأقصى للتنزيلات',
            'helper' => 'الحد الأقصى لعدد مرات تنزيل الملف',
        ],
        'require_authentication' => [
            'label' => 'يتطلب المصادقة',
            'helper' => 'يجب على المستخدمين تسجيل الدخول لتنزيل الملفات',
        ],
    ],

    'license_generator' => [
        'label' => 'مولد التراخيص',
        'description' => 'تكوين إعدادات إنشاء مفاتيح الترخيص',
        'enabled' => [
            'label' => 'تفعيل إنشاء التراخيص',
            'helper' => 'السماح بتوفير المنتجات عبر مفاتيح الترخيص',
        ],
        'prefix' => [
            'label' => 'بادئة الترخيص',
            'helper' => 'البادئة المضافة لجميع مفاتيح الترخيص المنشأة',
        ],
        'length' => [
            'label' => 'طول الترخيص',
            'helper' => 'الطول الإجمالي لمفتاح الترخيص (بما في ذلك البادئة)',
        ],
        'format' => [
            'label' => 'التنسيق',
            'helper' => 'أنواع الأحرف المستخدمة في مفاتيح الترخيص',
            'options' => [
                'alphanumeric' => 'أبجدي رقمي (A-Z، 0-9)',
                'numeric' => 'رقمي فقط (0-9)',
                'alphabetic' => 'أبجدي فقط (A-Z)',
            ],
        ],
        'separator' => [
            'label' => 'الفاصل',
            'helper' => 'الحرف المستخدم لفصل أجزاء مفتاح الترخيص',
            'options' => [
                'dash' => 'شرطة (-)',
                'underscore' => 'شرطة سفلية (_)',
                'none' => 'بدون',
            ],
        ],
    ],
];
