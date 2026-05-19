<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\PlatformSetting;

final class LegalContent
{
    public const PRIVACY = 'privacy';

    public const KVKK = 'kvkk';

    public const TERMS = 'terms';

    public static function html(string $page): string
    {
        $platform = PlatformSetting::current();
        $column = match ($page) {
            self::PRIVACY => 'legal_privacy_html',
            self::KVKK => 'legal_kvkk_html',
            self::TERMS => 'legal_terms_html',
            default => null,
        };

        if ($column === null) {
            return '';
        }

        $stored = $platform->{$column};

        return is_string($stored) && trim($stored) !== ''
            ? $stored
            : self::defaultHtml($page);
    }

    public static function defaultHtml(string $page): string
    {
        return match ($page) {
            self::PRIVACY => self::defaultPrivacyHtml(),
            self::KVKK => self::defaultKvkkHtml(),
            self::TERMS => self::defaultTermsHtml(),
            default => '',
        };
    }

    private static function defaultPrivacyHtml(): string
    {
        return <<<'HTML'
<h1>Gizlilik politikası özet taslağı</h1>
<p>Kent sorunu bildirenlerin fotoğraf, konum ve iletişim verilerinin nasıl kullanılabileceğini yazılı bir politika olarak yayımlamanız beklenir. Bu sayfa taslağıdır; hukuk uzmanına danışılarak özelleştirin.</p>
<h2>Toplanan veriler</h2>
<p>Hesap, bildirilen kayıtların içeriği, moderasyon günlükleri ve (varsa) cihaz bilgisi.</p>
<h2>Amaç</h2>
<p>Kent sorununu çözmeye yönlendirmek, moderasyon yapmak ve yasal yükümlülükleri yerine getirmek.</p>
HTML;
    }

    private static function defaultKvkkHtml(): string
    {
        return <<<'HTML'
<h1>KVKK başvuru süreç taslağı</h1>
<p>Veri sorumlusu sıfatına kayıtlı bilgiler, başvuru yolları, saklama süreleri ve ilgili mevzuat atıfları profesyonelce tamamlanmalıdır.</p>
<h2>İlgili kişi hakları</h2>
<p>Kişisel veriler hakkında bilgi talebi, düzeltme, silme ve itiraz yollarının net şekilde anlatılması gerekir.</p>
HTML;
    }

    private static function defaultTermsHtml(): string
    {
        return <<<'HTML'
<h1>Kullanım koşulları taslağı</h1>
<p>Kent sorunları bildirimin resmi başvuru yerini almadığı, kullanıcıların doğru içerik yüklemekle yükümlü olduğu ve hakaret / kişisel veri ihlali yapmamanın gerektiği maddeleri ekleyin.</p>
<h2>Yasaklı kullanımlar</h2>
<p>Spam, suistimal, yanlış konum bilgisi, üçüncü kişiyi hedef gösterme vb.</p>
HTML;
    }
}
