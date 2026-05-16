<?php

declare(strict_types=1);

return [
    'intro_highlight' => 'Resmî bildirimlerde yetkili kurum kanallarını kullanın; bu sayfa BildirFIX iletişim ve destek süreçlerini özetler.',

    /*
    | Genel SLA metinleri (yasal zorunluluk değil; beklenti yönetimi).
    */
    'response_sla' => [
        ['label' => 'Genel destek', 'value' => '2–5 iş günü'],
        ['label' => 'Topluluk & ilişkiler', 'value' => '3–7 iş günü'],
        ['label' => 'Güvenlik bildirimi', 'value' => '24–72 saat içinde ilk yanıt (öncelikli kuyruk)'],
        ['label' => 'Kampanya moderasyonu', 'value' => '1–14 iş günü (sıraya bağlı)'],
        ['label' => 'Kurumsal iş birliği', 'value' => 'takvimli görüşme'],
    ],

    /*
    | İletişim formu konu listesi — value gönderilir, label kullanıcıya gösterilir.
    */
    'form_topics' => [
        ['value' => '', 'label' => 'Genel bildirim'],
        ['value' => 'kampanya-onay-sureci', 'label' => 'Kampanya onay süreci'],
        ['value' => 'sikayet-moderasyonu', 'label' => 'Kent bildirimi moderasyonu'],
        ['value' => 'kurumsal-isbirligi', 'label' => 'Kurum / belediye iş birliği'],
        ['value' => 'teknik-erisimibilirlik', 'label' => 'Teknik sorun ve erişilebilirlik'],
        ['value' => 'kvkk-ve-veri', 'label' => 'KVKK ve kişisel veri'],
        ['value' => 'diger', 'label' => 'Diğer'],
    ],

    'channels' => [
        ['icon' => 'mail', 'label' => 'E-posta (genel)', 'value' => 'destek@bildirfix.local', 'note' => 'Kampanya, şikâyet doğruluğu ve teknik içerikli talepler için formu kullanmanızı öneririz.'],
        ['icon' => 'community', 'label' => 'Topluluk & kampanyalar', 'value' => 'kampanya@bildirfix.local', 'note' => 'Kampanya açılış süreçleri ve moderasyon hakkında.'],
        ['icon' => 'partnership', 'label' => 'Kurum iş birliği', 'value' => 'kurumsal@bildirfix.local', 'note' => 'Belediye veya düzenlenmiş kurumlardan veri-paylaşım protokolü talepleri.'],
        ['icon' => 'legal', 'label' => 'Hukuki & KVKK', 'value' => 'kvkk@bildirfix.local', 'note' => 'Veri sübjesi başvuru ve hukuki bildirimler (noter veya yazılı iletişim yönergelerimize tabi olabilir).'],
    ],

    'departments' => [
        [
            'title' => 'Kent verisi ve moderasyon',
            'summary' => 'Şikâyet ve içerik onayı sırasına ilişkin durum özeti için.',
            'bullets' => ['Topluluktan gelen medya doğruluğu ilkeleri', 'Tekrar bildirilen konuların konsolidasyonu', 'Kent haritasında öne çıkarılan aks'],
        ],
        [
            'title' => 'Kampanya denetimi',
            'summary' => 'Sosyal fayda kampanyaları — süper yönetici onayı gerektirir.',
            'bullets' => ['Şeffaf destekçi sayısı', 'Bağlantı politikası uyumu', 'Kent veya Türkiye geneli kampanya etiketi'],
        ],
        [
            'title' => 'Ürün ve erişilebilirlik geri bildirimi',
            'summary' => 'Ekran okuyucu, kontrast ve zorunlu alanlar için erişilebilirlik bildirimi.',
            'bullets' => ['WCAG 2.x hedefi ile iyileştirme kayıtları', 'Klavye kısayollarına özel notlar'],
        ],
    ],

    'faq' => [
        [
            'q' => 'İletişim formu hukuken bağlayıcı mı?',
            'a' => 'Hayır; yasal talepler için ilgili resmî süreçler ve kanıtlanabilir teslim kanalları esas alınır. Bu form bildiriminizi kayda geçirir.',
        ],
        [
            'q' => 'Kent sorununuzun çözümü için burayı mı kullanmalıyım?',
            'a' => 'Çözüm resmî olarak ilgili kuruma kalır; BildirFIX kamusal görünürlük ve süreç takibi için bir araç sunar.',
        ],
        [
            'q' => 'Kampanyam reddedilirse?',
            'a' => 'Red gerekçesi moderasyon kaydına düşer. Politikaya uygun düzeltmeden sonra tekrar başvurabilirsiniz.',
        ],
        [
            'q' => 'Şikâyet fotoğrafı kişiyi gösteriyorsa?',
            'a' => 'Belirsiz yüz görüntüsü kullanın ve kişisel veri gereksiz ise kırpın. İhlâl şüphesi durumunda moderasyon bildiriminizi günceller.',
        ],
        [
            'q' => 'Forge / üretim ortamında Reverb gerekiyor mu?',
            'a' => 'Gerçek zamanlı özelliği kullanmıyorsanız yapılandırmayı sade tutabilir veya devre dışı bırakabilirsiniz; dağıtım notları için README içinde Forge bölümüne bakın.',
        ],
    ],

    /*
    | Statik kartlar — sayfa tasarımı için; dinamik sayılar controller’da tamamlanır.
    */
    'metric_labels' => [
        ['key' => 'complaints_public', 'label' => 'Yayına alınan kent bildirimi (örnek veri dahil)', 'accent' => 'from-emerald-500 to-teal-600'],
        ['key' => 'campaigns_public', 'label' => 'Onaylı destek kampanyası', 'accent' => 'from-[#6C5CE7] to-violet-700'],
        ['key' => 'cities_seeded', 'label' => 'Örnek veride şehir kapsaması', 'accent' => 'from-amber-500 to-orange-600'],
    ],
];
