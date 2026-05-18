<?php

declare(strict_types=1);

return [
    'groups' => [
        'kamu' => 'Kamu ve politika',
        'cevre' => 'Çevre',
        'saglik' => 'Sağlık',
        'egitim' => 'Eğitim',
        'adalet' => 'Adalet ve haklar',
        'hayvan' => 'Hayvan',
        'diger' => 'Diğer',
    ],

    /** Change.org tarzı konu listesi (slug => [name, group]) */
    'items' => [
        ['slug' => 'kamu-politikasi', 'name' => 'Kamu Politikası', 'group' => 'kamu'],
        ['slug' => 'politika', 'name' => 'Politika', 'group' => 'kamu'],
        ['slug' => 'yerel-yonetim', 'name' => 'Yerel Yönetim', 'group' => 'kamu'],
        ['slug' => 'devlet-politikalari', 'name' => 'Devlet Politikaları', 'group' => 'kamu'],
        ['slug' => 'kamu-guvenligi', 'name' => 'Kamu Güvenliği', 'group' => 'kamu'],
        ['slug' => 'cevre', 'name' => 'Çevre', 'group' => 'cevre'],
        ['slug' => 'yaban-hayati-biyocesitlilik', 'name' => 'Yaban Hayatı ve Biyoçeşitliliği', 'group' => 'cevre'],
        ['slug' => 'biyocesitlilik', 'name' => 'Biyoçeşitlilik', 'group' => 'cevre'],
        ['slug' => 'ormansizlasma', 'name' => 'Ormansızlaşma', 'group' => 'cevre'],
        ['slug' => 'iklim-degisikligi', 'name' => 'İklim Değişikliği', 'group' => 'cevre'],
        ['slug' => 'saglik', 'name' => 'Sağlık', 'group' => 'saglik'],
        ['slug' => 'halk-sagligi', 'name' => 'Halk Sağlığı', 'group' => 'saglik'],
        ['slug' => 'hasta-haklari', 'name' => 'Hasta Hakları', 'group' => 'saglik'],
        ['slug' => 'egitim', 'name' => 'Eğitim', 'group' => 'egitim'],
        ['slug' => 'ogrenci-haklari', 'name' => 'Öğrenci Hakları', 'group' => 'egitim'],
        ['slug' => 'universite-ogrencileri', 'name' => 'Üniversite Öğrencileri', 'group' => 'egitim'],
        ['slug' => 'egitim-sistemi', 'name' => 'Eğitim Sistemi', 'group' => 'egitim'],
        ['slug' => 'ceza-adaleti', 'name' => 'Ceza Adaleti', 'group' => 'adalet'],
        ['slug' => 'ekonomik-adalet', 'name' => 'Ekonomik Adalet', 'group' => 'adalet'],
        ['slug' => 'kuresel-sosyal-adalet', 'name' => 'Küresel Sosyal Adalet Sorunları', 'group' => 'adalet'],
        ['slug' => 'adil-yargilanma', 'name' => 'Adil Yargılanma', 'group' => 'adalet'],
        ['slug' => 'kadin-haklari', 'name' => 'Kadın Hakları', 'group' => 'adalet'],
        ['slug' => 'cocuk-haklari', 'name' => 'Çocuk Hakları', 'group' => 'adalet'],
        ['slug' => 'hayvan-haklari', 'name' => 'Hayvan Hakları', 'group' => 'hayvan'],
        ['slug' => 'hayvan-refahi', 'name' => 'Hayvan Refahı', 'group' => 'hayvan'],
        ['slug' => 'hayvana-siddet', 'name' => 'Hayvana Şiddet', 'group' => 'hayvan'],
        ['slug' => 'cocuk-aile-refahi', 'name' => 'Çocuk ve Aile Refahı', 'group' => 'diger'],
        ['slug' => 'kultur-sanat-medya', 'name' => 'Kültür ve Sanat ve Medya', 'group' => 'diger'],
        ['slug' => 'ekonomi-politikasi', 'name' => 'Ekonomi Politikası', 'group' => 'diger'],
        ['slug' => 'teknoloji-internet', 'name' => 'Teknoloji ve İnternet', 'group' => 'diger'],
    ],
];
