<?php

declare(strict_types=1);

namespace Database\Seeders\Support;

/**
 * Proje blog içerikleri (her yazı en fazla ~600 kelime).
 *
 * @return list<array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}>
 */
final class ProjectBlogArticles
{
    public static function all(): array
    {
        return [
            self::article1(),
            self::article2(),
            self::article3(),
            self::article4(),
            self::article5(),
            self::article6(),
            self::article7(),
            self::article8(),
            self::article9(),
            self::article10(),
            self::article11(),
            self::article12(),
            self::article13(),
            self::article14(),
            self::article15(),
            self::article16(),
            self::article17(),
            self::article18(),
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article1(): array
    {
        return [
            'slug' => 'sma-hastalari-icin-kampanya-nasil-baslatilir',
            'title' => 'SMA hastaları için kampanya nasıl başlatılır?',
            'category' => 'sosyal-kampanyalar',
            'excerpt' => 'Spinal müsküler atrofi (SMA) tedavi ve erişim süreçlerinde toplumsal görünürlük için simdibildir.com üzerinden kampanya açma rehberi.',
            'meta_description' => 'SMA hastaları ve aileleri için sosyal sorumluluk kampanyası başlatma adımları, moderasyon ve destekçi hedefi ipuçları.',
            'days_ago' => 52,
            'body' => <<<'HTML'
<p>Spinal müsküler atrofi (SMA), kas gücünü ve hareketi etkileyen nadir bir nöromüsküler hastalıktır. Birçok aile, tedavi erişimi, rehabilitasyon ve bakım süreçlerinde yalnız hissedebilir. simdibildir.com, yalnızca kent sorunlarını bildirmek için değil; <strong>toplumsal dayanışma kampanyaları</strong> başlatmak için de tasarlandı. SMA ile mücadele eden bireyler ve yakınları, platformda kampanya açarak hikâyelerini geniş kitlelere ulaştırabilir.</p>

<h2>Kampanya ne işe yarar?</h2>
<p>Kampanya sayfası; başlık, özet, açıklama ve isteğe bağlı görsel ile destekçilerin tek tıkla yanınızda olduğunu göstermesini sağlar. Bu, bağış toplama aracı değildir: amaç <strong>farkındalık, kamuoyu baskısı ve bilinçlendirme</strong> oluşturmaktır. Örneğin ilaç erişimi, merkezi rehabilitasyon talebi veya okul/ulaşım engellerinin giderilmesi gibi konular kampanyayla görünür kılınabilir.</p>

<h2>Adım adım başlangıç</h2>
<p>Önce üye olun ve telefon doğrulamasını tamamlayın. Ardından menüden <strong>«Kampanya başlat»</strong> seçeneğine gidin. Başlığı net yazın: «Ankara’da SMA tedavisine erişim için farkındalık» gibi. Özette üç dört cümleyle durumu özetleyin. Ana metinde ailenin hikâyesini, hedefi ve kurumlardan beklentiyi açıkça belirtin. İl seçimi, kampanyanızın yerel odaklı listelenmesine yardımcı olur.</p>

<h2>Moderasyon ve yayın</h2>
<p>Kampanyalar süper yönetici onayından geçer; bu sayede yanlış bilgi ve kötüye kullanım azaltılır. Onay sonrası kampanya herkese açılır; destekçi sayısı ve görüntülenme istatistikleri panelinizden takip edilir. Reddedilirse gerekçe notu paylaşılır; metni düzelterek yeniden gönderebilirsiniz.</p>

<h2>Etik ve doğru dil</h2>
<p>Tıbbi iddialarda kaynak belirtin, kişisel veri paylaşmayın. Resmî başvuru yollarının yerini almadığını, kampanyanın bilinçlendirme amaçlı olduğunu hatırlatın. Bu yaklaşım hem güven hem de sürdürülebilir etki sağlar.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article2(): array
    {
        return [
            'slug' => 'zolgensma-ve-toplumsal-farkindalik-kampanyalari',
            'title' => 'Zolgensma ve toplumsal farkındalık: dijital kampanya ile sesinizi yükseltin',
            'category' => 'saglik-dayanisma',
            'excerpt' => 'Nadir hastalık tedavilerinde kamuoyu desteği ve erişim tartışmalarında kampanyaların rolü.',
            'meta_description' => 'Zolgensma ve SMA tedavisinde farkındalık kampanyası nasıl kurgulanır? simdibildir.com rehberi.',
            'days_ago' => 48,
            'body' => <<<'HTML'
<p>Zolgensma ve benzeri gen tedavileri, SMA tedavisinde umut verici seçenekler sunar; ancak erişim süreçleri çoğu zaman karmaşık, uzun ve duygusal açıdan yıpratıcıdır. Aileler bazen yalnızca sağlık bakanlığı veya sigorta kurumlarıyla değil, kamuoyunun bilgisi ve desteğiyle de ilerleme kaydeder. Dijital kampanyalar, bu süreçte <strong>şeffaf bir anlatım kanalı</strong> oluşturur.</p>

<h2>Kampanya metninde nelere değinilmeli?</h2>
<p>Önce hastalığın aile üzerindeki etkisini insan odaklı anlatın. Ardından mevcut hukuki ve idari çerçeveyi (reçete, SUT, başvuru tarihleri) abartmadan özetleyin. «Ne istiyoruz?» sorusuna net yanıt verin: örneğin sürecin hızlandırılması, merkez sayısının artırılması veya bilgilendirme toplantısı. Rakamları doğrulayın; spekülasyon yerine belgelenebilir bilgi kullanın.</p>

<h2>Destekçi hedefi ve görünürlük</h2>
<p>Platformda destekçi sayısı, kampanyanıza ilgi gösteren kayıtlı kullanıcıları yansıtır. Hedef koyarken gerçekçi olun; ilk haftada yakın çevrenizle paylaşım yapın. Kampanya görüntülenme sayısı panelden izlenir; sosyal medyada kısa link ve özet kartı paylaşmak erişimi artırır.</p>

<h2>Kent bildirimi ile birlikte kullanım</h2>
<p>Bazı talepler hem kampanya hem de somut kent sorunu niteliği taşır: hastane rampası, asansör arızası, kaldırım engeli. Bu durumda kampanyayla farkındalık, bildirimle ise kuruma doğrudan kayıt açabilirsiniz. İki kanal birbirini tamamlar.</p>

<h2>Son söz</h2>
<p>Her kampanya bir mucize vaadi değil; <strong>toplumsal vicdan ve kurumsal hesap verebilirlik</strong> için bir çağrıdır. Doğru dil, saygılı ton ve sürdürülebilir paylaşım uzun vadede en güçlü etkiyi yaratır.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article3(): array
    {
        return [
            'slug' => 'kent-bildirimi-ve-kampanya-birlikte-kullanim',
            'title' => 'Kent bildirimi ve kampanya: İki aracı birlikte kullanmak',
            'category' => 'rehber',
            'excerpt' => 'Şikâyet kaydı ile sosyal kampanyayı aynı platformda nasıl birleştirirsiniz?',
            'meta_description' => 'simdibildir.com’da bildirim ve kampanya birlikte kullanım rehberi. SMA ve kent sorunları için pratik örnekler.',
            'days_ago' => 44,
            'body' => <<<'HTML'
<p>simdibildir.com iki temel güce sahiptir: <strong>kent sorunu bildirimi</strong> ve <strong>sosyal sorumluluk kampanyası</strong>. Birincisi fotoğraf ve konumla somut problemleri kurumlara görünür kılar; ikincisi ise geniş kamuoyundan destek toplayarak meselenin gündeme taşınmasını sağlar. SMA aileleri için ikisi de anlamlıdır: okula erişim, hastane çevresi düzenlemesi veya ilaç sürecine dair toplumsal destek.</p>

<h2>Ne zaman bildirim?</h2>
<p>Kaldırımda engel, bozuk rampa, aydınlatma eksikliği veya trafik düzenlemesi gibi <strong>yerinde tespit edilebilir</strong> sorunlarda bildirim idealdir. Konum pin’i, kategori seçimi ve kısa açıklama yeterlidir. Kurum ataması ve moderasyon sonrası kayıt kamuya açılır; destek ve yorumlarla süreç takip edilir.</p>

<h2>Ne zaman kampanya?</h2>
<p>Tedavi erişimi, politika değişikliği talebi, ulusal farkındalık veya «bin destekçi» gibi soyut ama güçlü hedefler kampanyaya uygundur. Metin alanı geniştir; hikâyenizi detaylı anlatırsınız. Destekçiler kayıt olup tek tıkla yanınızda olduğunu gösterir.</p>

<h2>Örnek senaryo</h2>
<p>Bir aile, çocuğunun okulundaki asansör arızası için bildirim açar; aynı hafta «engelli öğrenciler için erişilebilir okul» kampanyası başlatır. Bildirim belediyeye somut iş emri üretirken kampanya medyada ve sosyal çevrede konuşulur. Panelden her iki içeriğin görüntülenme ve destek sayılarını izleyebilirsiniz.</p>

<h2>Panel ve hesap yönetimi</h2>
<p>Kullanıcı panelinde bildirimlerinizi ve kampanyalarınızı ayrı listelerde görürsünüz. Yayında olan içerikler düzenlenemez; bekleyen veya reddedilen kayıtlar güncellenebilir. Profil ve güvenlik ayarlarınızı tek yerden yönetirsiniz.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article4(): array
    {
        return [
            'slug' => 'sma-aileleri-destekci-toplama-rehberi',
            'title' => 'SMA aileleri için destekçi toplama rehberi',
            'category' => 'sosyal-kampanyalar',
            'excerpt' => 'Kampanyanıza kayıtlı destekçi kazanmanın etik ve etkili yolları.',
            'meta_description' => 'SMA kampanyasında destekçi sayısını artırma: paylaşım, hedef ve iletişim ipuçları.',
            'days_ago' => 40,
            'body' => <<<'HTML'
<p>Bir kampanyanın gücü, yalnızca metnin kalitesinden değil; ona <strong>katılan insan sayısından</strong> da gelir. simdibildir.com’da destek vermek için üyelik gerekir; böylece sahte tıklamalar azalır, gerçek bir topluluk oluşur. SMA aileleri için destekçi toplarken duygusal çağrı ile bilgilendirilmiş katılımı dengelemek önemlidir.</p>

<h2>Hedef belirleme</h2>
<p>Kampanya oluştururken isteğe bağlı destekçi hedefi girebilirsiniz. İlk kampanyanızda 500 veya 1000 gibi ulaşılabilir rakamlar motivasyon sağlar. Hedefe yaklaştıkça paylaşım metnini güncelleyin. Hedef zorunlu değildir; yalnızca görünürlük için de kampanya açılabilir.</p>

<h2>Paylaşım kanalları</h2>
<p>WhatsApp grupları, SMA dernekleri, okul aile birlikleri ve yerel basın kampanyanızı duyurmak için uygun kanallardır. Linki kısa tutun, özet cümleyi net yazın. «Destek = imza niteliğinde dayanışma» mesajını ekleyin; bağış beklentisi yaratmayın.</p>

<h2>Güven inşası</h2>
<p>Kampanya sayfasında görüntülenme sayısı şeffaflık sağlar. Yorum alanı (varsa) sorulara yanıt vermek için kullanılabilir. Kişisel sağlık verisi, T.C. kimlik numarası veya üçüncü kişilerin özel bilgilerini paylaşmayın.</p>

<h2>Teşekkür ve kapanış</h2>
<p>Hedefe ulaşıldığında veya süreç tamamlandığında kampanya metnine kısa bir güncelleme ekleyin (düzenleme moderasyon sonrası mümkünse). Destekçilere teşekkür, topluluğun bir sonraki adımda da yanınızda kalmasını sağlar.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article5(): array
    {
        return [
            'slug' => 'kampanya-moderasyon-sureci-ne-kadar-surer',
            'title' => 'Kampanya moderasyon süreci: Ne zaman yayına girer?',
            'category' => 'rehber',
            'excerpt' => 'Kampanyanızın onay, red ve yayından kaldırma adımları hakkında bilmeniz gerekenler.',
            'meta_description' => 'simdibildir.com kampanya moderasyonu: onay süreci, red gerekçesi ve yeniden başvuru.',
            'days_ago' => 36,
            'body' => <<<'HTML'
<p>Toplumsal kampanyaların herkese açık listede yer alması, platformun güvenilir kalması için <strong>ön incelemeden</strong> geçer. Bu, SMA dahil tüm konulardaki kampanyalar için geçerlidir. Süreç şeffaftır: panelinizde durum etiketi, görüntülenme ve destek sayıları görünür.</p>

<h2>Onay öncesi</h2>
<p>Kampanya gönderildiğinde durum «onay bekliyor» olur. Bu aşamada yalnızca siz ve yöneticiler içeriği görür. Metinde hakaret, yanlış tıbbi iddia, kişisel veri ihlali veya yasa dışı çağrı varsa red edilebilir. Red gerekçesi not olarak iletilir.</p>

<h2>Onay sonrası</h2>
<p>Yayına alınan kampanyalar kampanya listesinde ve doğrudan bağlantıyla erişilebilir. Destek ve yorum özellikleri açılır. Görüntülenme sayacı her oturumda bir kez artar; istatistikler panelde özetlenir.</p>

<h2>Yayından kaldırma</h2>
<p>Nadiren de olsa kampanyalar sonradan yayından kaldırılabilir. Bu durumda gerekçe notu paylaşılır. Düzeltip yeniden onaya sunmak mümkündür. Süreç boyunca resmî başvuru kanallarını da kullanmanız önerilir; platform resmî makam değildir.</p>

<h2>Beklenti yönetimi</h2>
<p>Onay süresi iş yüküne göre değişir; acil sağlık meselelerinde metinde «zaman hassasiyeti» belirtmek faydalıdır. Sabırlı ve net bir dil, incelemeyi hızlandırmaya yardımcı olur.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article6(): array
    {
        return [
            'slug' => 'fotografli-kent-bildirimi-rehberi',
            'title' => 'Fotoğraflı kent bildirimi: Sorunu kanıtla, süreci hızlandır',
            'category' => 'kent-yasami',
            'excerpt' => 'Engelli erişimi ve kent düzenlemesi sorunlarında görsel kanıtın önemi.',
            'meta_description' => 'Fotoğraf ve konumlu şikâyet bildirimi nasıl yapılır? SMA ve engelli erişimi örnekleri.',
            'days_ago' => 33,
            'body' => <<<'HTML'
<p>Kent bildirimlerinde <strong>fotoğraf ve konum</strong>, kurumların sorunu hızlı anlamasını sağlar. Tekerlekli sandalye kullanıcıları, SMA’lı bireyler ve yaşlı vatandaşlar için bir basamak, park yasağı veya rampa eğimi somut bir risk oluşturabilir. simdibildir.com bu kayıtları moderasyondan geçirerek yayınlar.</p>

<h2>İyi bir fotoğraf nasıl olmalı?</h2>
<p>Gündüz çekim, net odak ve sorunu gösteren kadraj yeterlidir. Mümkünse yakın ve geniş açıdan iki kare ekleyin. Kişi yüzü veya plaka görünmesin; mahremiyete dikkat edin. Açıklamada «ne olduğu», «ne kadar süredir devam ettiği» ve «risk» üçlüsünü yazın.</p>

<h2>Kategori ve kurum</h2>
<p>Ulaşım, kaldırım, park veya kamu binası gibi uygun kategoriyi seçin. İlgili belediye veya kurum otomatik eşleşebilir. Birden fazla kurum ilişkiliyse hepsini işaretleyin.</p>

<h2>Kampanya ile destek</h2>
<p>Tekrarlayan veya sistematik sorunlarda bildirime ek kampanya açmak gündemi büyütür. Örneğin «ilçede engelli erişimi denetimi» kampanyası, onlarca bildirimi bir araya getiren bir çatı anlatı sunabilir.</p>

<h2>Takip</h2>
<p>Bildirim durumu (açık, işlemde, çözüldü) ve destek sayısı sayfada görünür. Çözüm sonrası topluluğa teşekkür etmek güven oluşturur.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article7(): array
    {
        return [
            'slug' => 'kurumsal-seffaflik-dijital-katilim',
            'title' => 'Kurumsal şeffaflık ve dijital katılım',
            'category' => 'kent-yasami',
            'excerpt' => 'Vatandaş bildirimleri kurum panellerinde nasıl değer kazanır?',
            'meta_description' => 'Kurum hesapları ve vatandaş bildirimleri: şeffaf kent yönetimi için dijital araçlar.',
            'days_ago' => 30,
            'body' => <<<'HTML'
<p>Belediyeler, hastaneler ve kamu kurumları giderek dijital kanalları benimsiyor. simdibildir.com’da <strong>kurum hesapları</strong>, kendilerine yöneltilen onaylı bildirimleri panelde görebilir: sayı, il dağılımı, görüntülenme ve destek özetleri. Bu, SMA ailelerinin hastane çevresi veya sosyal tesis erişimi taleplerinin kurum tarafından görülmesine katkı sağlar.</p>

<h2>Vatandaş tarafı</h2>
<p>Bildirim açarken doğru kurumu seçmek süreci hızlandırır. Fotoğraf ve adres bilgisi eksiksiz olmalıdır. «Resmî başvurunun yerine geçmez» uyarısı platformda yer alır; yine de dijital kayıt, resmî süreçlere ek kanıt oluşturabilir.</p>

<h2>Kampanya baskısı</h2>
<p>Kurumlar bazen yüksek görüntülenme ve destek alan kayıtlara daha hızlı yanıt verir. Kampanya, konunun medyada yer bulmasını kolaylaştırır. İki kanalın birlikte kullanımı önerilir.</p>

<h2>Güven</h2>
<p>Moderasyon, spam ve yanlış konum girişlerini azaltır. Kurumlar yalnızca onaylı içerikleri görür; bu da veriye dayalı karar almayı kolaylaştırır.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article8(): array
    {
        return [
            'slug' => 'il-bazli-kampanya-hedefi-belirleme',
            'title' => 'İl bazlı kampanya hedefi belirleme ipuçları',
            'category' => 'rehber',
            'excerpt' => 'Kampanyanızı İstanbul, Ankara veya tüm Türkiye odaklı konumlandırma.',
            'meta_description' => 'Kampanyada il seçimi ve yerel görünürlük: SMA ve kent kampanyaları için rehber.',
            'days_ago' => 27,
            'body' => <<<'HTML'
<p>Kampanya oluştururken <strong>il alanı</strong> isteğe bağlıdır. Boş bırakırsanız kampanya genel (Türkiye) listesinde yer alır; il seçerseniz o şehir filtrelerinde öne çıkar. SMA tedavi merkezi, il sağlık müdürlüğü veya yerel basın için doğru il seçimi kritiktir.</p>

<h2>Yerel mi ulusal mı?</h2>
<p>Yerel sorunlar (tek hastane rampası) için il seçin. Ulusal erişim veya SUT tartışmaları için genel kampanya daha uygundur. Karışık meselelerde genel açıp metinde şehir vurgusu yapabilirsiniz.</p>

<h2>Filtreleme</h2>
<p>Ziyaretçiler kampanya listesinde il filtresi kullanır. Doğru etiket, hedef kitlenin sizi bulmasını kolaylaştırır. Konu (topic) alanı varsa «sağlık» veya ilgili başlığı seçin.</p>

<h2>Örnek</h2>
<p>«İzmir’de SMA tanı merkezi talebi» başlığı, İzmir filtresiyle açılır; metinde ulusal politika bağlantısı kurulabilir. Böylece hem yerel hem ulusal dikkat çekilir.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article9(): array
    {
        return [
            'slug' => 'nadir-hastaliklarda-topluluk-destegi',
            'title' => 'Nadir hastalıklarda topluluk desteği ve dijital dayanışma',
            'category' => 'saglik-dayanisma',
            'excerpt' => 'SMA dışında nadir hastalık toplulukları için kampanya kültürü.',
            'meta_description' => 'Nadir hastalıklarda dijital kampanya ve topluluk desteği rehberi.',
            'days_ago' => 24,
            'body' => <<<'HTML'
<p>SMA, binlerce nadir hastalıktan yalnızca biridir. Ortak nokta: ailelerin bilgi, destek ve görünürlük arayışı. simdibildir.com yalnızca SMA’ya özel değildir; <strong>her toplumsal mesele</strong> için kampanya açılabilir. Platform, sağlık odaklı anlatımları moderasyonla güvenli tutar.</p>

<h2>Topluluk liderliği</h2>
<p>Dernek yöneticileri veya gönüllüler, üyeler adına kampanya koordine edebilir. İzin ve gizlilik için ailelerden onay alın. Kampanya metninde dernek iletişim bilgisi verilebilir.</p>

<h2>Ortak kampanyalar</h2>
<p>«Nadir hastalıklar günü» gibi ortak başlıklar birden fazla hastalık topluluğunu bir araya getirir. Destekçi hedefi paylaşılan başarı hikâyesi yaratır.</p>

<h2>Sınırlar</h2>
<p>Platform tıbbi tavsiye vermez, tedavi satmaz. Kampanyalar bilinçlendirme ve kamuoyu oluşturma içindir. Acil tıbbi durumlarda 112 ve hastane acilleri geçerlidir.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article10(): array
    {
        return [
            'slug' => 'kampanya-metninde-dogru-bilgi',
            'title' => 'Kampanya metninde doğru bilgi: Yanlış iddialardan kaçının',
            'category' => 'rehber',
            'excerpt' => 'Sağlık kampanyalarında güvenilir kaynak ve dil kullanımı.',
            'meta_description' => 'SMA ve sağlık kampanyasında doğru bilgi, kaynak ve etik dil rehberi.',
            'days_ago' => 21,
            'body' => <<<'HTML'
<p>Dijital kampanyalarda duygu güçlüdür; ancak <strong>doğruluk</strong> güvenin temelidir. Özellikle SMA gibi konularda sosyal medyada dolaşan yanlış bilgiler, aileleri yanıltabilir. simdibildir.com moderasyonu bu riski azaltmaya çalışır; siz de metni yazarken kontrol listesi kullanabilirsiniz.</p>

<h2>Kaynak kullanımı</h2>
<p>İlaç adı, onay tarihi ve resmî açıklamalar için Sağlık Bakanlığı, TİTCK veya saygın dernek kaynaklarını gösterin. «Duydum ki» ifadelerinden kaçının. Güncel olmayan haberleri kopyalamayın.</p>

<h2>Kişisel veri</h2>
<p>Çocuğunuzun tam adı, okul bilgisi veya hastane kayıt numarası gibi hassas verileri düşünerek paylaşın. Hikâyeyi anonimleştirmek bazen daha güvenlidir.</p>

<h2>Red ve düzeltme</h2>
<p>Kampanya reddedilirse notu okuyun, metni düzeltin, yeniden gönderin. Bu süreç platform kalitesini korur; size de uzun vadede daha güçlü bir kayıt bırakır.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article11(): array
    {
        return [
            'slug' => 'gencler-icin-kent-katilimi-kampanya',
            'title' => 'Gençler için kent katılımı: Kampanya başlatmak',
            'category' => 'kent-yasami',
            'excerpt' => 'Üniversite öğrencileri ve genç gönüllüler için platform rehberi.',
            'meta_description' => 'Gençler kent sorunu ve sosyal kampanya nasıl başlatır? Adım adım rehber.',
            'days_ago' => 18,
            'body' => <<<'HTML'
<p>Gençler, kent sorunlarına ve sosyal adalete duyarlılığı yüksek bir gruptur. Okul çevresindeki engeller, toplu taşıma veya park alanları için bildirim açabilir; SMA veya çevre konularında kampanya başlatabilirler. simdibildir.com’da üyelik ücretsizdir; telefon doğrulaması güvenlik içindir.</p>

<h2>Okul projeleri</h2>
<p>Sosyal sorumluluk dersleri kapsamında gruplar, «engelli erişimi denetimi» projesi yapıp bulguları bildirim olarak yükleyebilir. Öğretmen onayı ve mahremiyet kurallarına uyulmalıdır.</p>

<h2>Kampanya dili</h2>
<p>Genç dilinde samimi anlatım işe yarar; ancak saygısız ifade moderasyonda red sebebidir. Hedef ve paylaşım planını sınıfça hazırlamak öğreticidir.</p>

<h2>Dijital vatandaşlık</h2>
<p>Yorumlarda nezaket, destekte samimiyet ve kişisel veri koruması dijital vatandaşlığın parçasıdır. Platform bu kültürü teşvik eder.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article12(): array
    {
        return [
            'slug' => 'engelli-erisimi-bildirim-ve-kampanya-ornekleri',
            'title' => 'Engelli erişimi: Bildirim ve kampanya örnekleri',
            'category' => 'kent-yasami',
            'excerpt' => 'Fiziksel erişilebilirlik sorunlarını platformda görünür kılma.',
            'meta_description' => 'Engelli erişimi şikâyeti ve kampanya örnekleri: rampa, asansör, kaldırım.',
            'days_ago' => 15,
            'body' => <<<'HTML'
<p>Engelli erişimi, SMA’lı bireylerden tekerlekli sandalye kullanıcılarına kadar geniş bir kesimi kapsar. Türkiye’de hâlâ birçok kaldırımda rampa eksikliği, dar geçit veya park ihlali vardır. simdibildir.com bu sorunları <strong>harita ve fotoğrafla</strong> kayıt altına alır.</p>

<h2>Örnek bildirim başlıkları</h2>
<ul>
<li>«Metro istasyonu asansörü 3 haftadır arızalı»</li>
<li>«Okul önü rampa park nedeniyle kullanılamıyor»</li>
<li>«Belediye binası girişinde eğim standart dışı»</li>
</ul>

<h2>Örnek kampanya</h2>
<p>«İl genelinde erişilebilirlik denetimi talebi» başlığı altında topluluk desteği toplanabilir. Hedef: bin destekçi veya medya dikkati. Metinde yasal çerçeve (5378 sayılı kanun) kısaca anılabilir.</p>

<h2>Sonuç takibi</h2>
<p>Çözülen bildirimler «çözüldü» statüsüne alınır; bu, kurumların çalıştığını gösterir. Kampanya metnine çözüm güncellemesi eklemek motivasyon sağlar.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article13(): array
    {
        return [
            'slug' => 'aileler-icin-kampanya-kontrol-listesi',
            'title' => 'Aileler için kampanya kontrol listesi',
            'category' => 'sosyal-kampanyalar',
            'excerpt' => 'Yayına göndermeden önce son kontrol: başlık, özet, gizlilik, hedef.',
            'meta_description' => 'SMA aileleri için kampanya yayına alma kontrol listesi.',
            'days_ago' => 12,
            'body' => <<<'HTML'
<p>Kampanyanızı göndermeden önce bu listeyi gözden geçirin; moderasyon sürecini hızlandırır ve güven oluşturur.</p>

<h2>İçerik</h2>
<ul>
<li>Başlık tek cümlede mesajı veriyor mu?</li>
<li>Özet 2–4 cümle ve net mi?</li>
<li>Ana metinde hedef, durum ve çağrı var mı?</li>
<li>Tıbbi iddialar kaynaklı mı?</li>
</ul>

<h2>Gizlilik</h2>
<ul>
<li>Çocuğun tam kimliği gereksiz yere paylaşıldı mı?</li>
<li>Üçüncü kişilerin bilgisi var mı? (varsa kaldırın)</li>
</ul>

<h2>Teknik</h2>
<ul>
<li>İl ve konu seçimi doğru mu?</li>
<li>Kapak görseli telifsiz ve uygun mu?</li>
<li>Destekçi hedefi gerçekçi mi?</li>
</ul>

<h2>Paylaşım planı</h2>
<p>Yayın sonrası ilk 48 saatte en az üç kanalda (WhatsApp, dernek, sosyal medya) duyuru yapın. Panelden görüntülenmeyi izleyin.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article14(): array
    {
        return [
            'slug' => 'destekci-sayisi-hedefi-nasil-konur',
            'title' => 'Destekçi sayısı hedefi nasıl konur?',
            'category' => 'rehber',
            'excerpt' => 'Kampanya hedefi psikolojisi ve gerçekçi rakamlar.',
            'meta_description' => 'Kampanyada destekçi hedefi belirleme: SMA ve sosyal kampanyalar.',
            'days_ago' => 10,
            'body' => <<<'HTML'
<p>Destekçi hedefi zorunlu değildir; ancak konduğunda <strong>motivasyon ve ölçüm</strong> sağlar. Hedef, kayıtlı kullanıcıların kampanyanıza «destek ol» demesidir; para toplama değildir.</p>

<h2>Küçük başlayın</h2>
<p>İlk kampanyada 100–300 arası hedef mantıklıdır. Tanıdık çevre hızla dolar; organik büyüme zaman alır. Başarı, bir sonraki kampanyada hedefi artırmanızı sağlar.</p>

<h2>Orta ölçek</h2>
<p>Dernek ağınız veya medya görünürlüğünüz varsa 1000–5000 hedefi düşünülebilir. Metinde hedefe ne kadar kaldığını ara ara paylaşın.</p>

<h2>Hedefsiz kampanya</h2>
<p>Bazı meseleler süreklilik gerektirir; hedef koymadan açık kampanya da etkilidir. Önemli olan düzenli güncelleme ve şeffaflıktır.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article15(): array
    {
        return [
            'slug' => 'kampanya-konusu-secimi-saglik-ulasim-cevre',
            'title' => 'Kampanya konusu seçimi: Sağlık, ulaşım, çevre',
            'category' => 'sosyal-kampanyalar',
            'excerpt' => 'Konu etiketleri ve anlatınızın uyumu.',
            'meta_description' => 'simdibildir.com kampanya konuları: doğru etiket seçimi rehberi.',
            'days_ago' => 8,
            'body' => <<<'HTML'
<p>Platformda kampanyalar <strong>konu (topic)</strong> ile gruplanabilir. SMA ve sağlık erişimi, ulaşım, çevre veya eğitim gibi başlıklar filtrelemede kullanılır. Doğru konu, ilgili okuyucunun sizi bulmasını sağlar.</p>

<h2>Sağlık odaklı</h2>
<p>Tedavi erişimi, tanı merkezi, ilaç süreci veya rehabilitasyon talepleri sağlık konusuna girer. Metinde duygusal denge ve kaynak önemlidir.</p>

<h2>Ulaşım ve erişim</h2>
<p>Engelli erişimi çoğu zaman ulaşım kategorisiyle örtüşür. Bildirimlerle destekleyin.</p>

<h2>Çevre ve kent</h2>
<p>Park, gürültü, atık gibi konular geniş kitle çeker. SMA aileleri de temiz ve erişilebilir kent ister; konuyu daraltmayın, metinde bağlantı kurun.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article16(): array
    {
        return [
            'slug' => 'paylasim-ve-gizlilik-kvkk',
            'title' => 'Paylaşım ve gizlilik: Verileriniz güvende mi?',
            'category' => 'rehber',
            'excerpt' => 'KVKK, gizlilik politikası ve kampanyada kişisel veri sınırları.',
            'meta_description' => 'simdibildir.com gizlilik ve KVKK: kampanya ve bildirimde veri güvenliği.',
            'days_ago' => 6,
            'body' => <<<'HTML'
<p>Dijital platformlarda paylaştığınız her bilgi bir kayıt oluşturur. simdibildir.com <strong>KVKK</strong> ve gizlilik politikası çerçevesinde çalışır; yine de kampanya ve bildirimde minimum veri ilkesi sizin elinizdedir.</p>

<h2>Ne paylaşmalı?</h2>
<p>Durumu anlatmak için genel bilgi yeterlidir. T.C. kimlik no, tam adres, hastane protokol numarası gibi hassas verilerden kaçının. Fotoğraflarda yüz ve plaka bulanıklaştırılabilir.</p>

<h2>Çocuk verisi</h2>
<p>18 yaş altı bireylerin verisi özel koruma altındadır. Veli olarak paylaşım yapıyorsanız çocuğunuzun uzun vadeli dijital izini düşünün.</p>

<h2>Hesap güvenliği</h2>
<p>Güçlü şifre, e-posta doğrulama ve ortak cihazlarda çıkış yapma alışkanlığı önerilir. Panelden profil güncellemesi yapılabilir.</p>

<h2>Yasal sayfalar</h2>
<p>Site alt bilgisinde gizlilik, KVKK ve kullanım koşullarına ulaşabilirsiniz. Sorularınız için iletişim formunu kullanın.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article17(): array
    {
        return [
            'slug' => 'sma-tedavi-yolculugunda-dijital-dayanisma',
            'title' => 'SMA tedavi yolculuğunda dijital dayanışma hikayeleri',
            'category' => 'saglik-dayanisma',
            'excerpt' => 'Kampanyaların ailelere moral ve görünürlük katkısı — kurgusal örnek senaryolar.',
            'meta_description' => 'SMA tedavi sürecinde dijital dayanışma ve kampanya hikayeleri.',
            'days_ago' => 4,
            'body' => <<<'HTML'
<p>Her SMA yolculuğu farklıdır; ortak olan ise bilgi ihtiyacı ve yalnızlık hissi. Dijital dayanışma, coğrafi mesafeyi kısaltır. Aşağıda <strong>örnek senaryolar</strong> (kurgusal) platform kullanımını gösterir.</p>

<h2>Senaryo 1: Tanı sonrası bilinç</h2>
<p>Young ailesi (kurgu), tanı sonrası «SMA hakkında doğru bilgi» kampanyası açar. Hedef: ailelerin dernek kaynaklarına ulaşması. Bin destekçi, yerel basında kısa haber olur.</p>

<h2>Senaryo 2: Hastane erişimi</h2>
<p>Demir ailesi, çocuk hastanesi otopark ve rampa sorununu bildirimle kaydeder; «çocuk hastanelerinde erişim standardı» kampanyası açar. Belediye ve hastane yönetimi gündeme gelir.</p>

<h2>Senaryo 3: Okul adaptasyonu</h2>
<p>Okul aile birliği, engelli öğrenciler için kampanya düzenler; bildirimlerle somut eksiklikler listelenir.</p>

<h2>Öğrenilen ders</h2>
<p>Dijital araç tek başına çözüm değildir; <strong>sürekli ve ölçülü</strong> kullanıldığında güçlenir. Gerçek hikâyenizi paylaşarak topluluğa ilham verebilirsiniz.</p>
HTML,
        ];
    }

    /**
     * @return array{slug: string, title: string, category: string, excerpt: string, meta_description: string, body: string, days_ago: int}
     */
    private static function article18(): array
    {
        return [
            'slug' => 'simdibildir-ile-sosyal-sorumluluk-kampanyasi-ornekleri',
            'title' => 'simdibildir.com ile sosyal sorumluluk kampanyası: Beş örnek fikir',
            'category' => 'haberler',
            'excerpt' => 'Platformda hemen başlayabileceğiniz kampanya fikirleri — SMA ve kent odaklı.',
            'meta_description' => 'simdibildir.com sosyal kampanya örnekleri: SMA, erişim ve kent sorunları.',
            'days_ago' => 2,
            'body' => <<<'HTML'
<p>simdibildir.com’da bugün başlayabileceğiniz <strong>beş kampanya fikri</strong>:</p>

<ol>
<li><strong>Ulusal SMA farkındalık haftası</strong> — Eğitim materyali ve dernek yönlendirmesi.</li>
<li><strong>İl bazında tanı merkezi talebi</strong> — İl sağlık müdürlüğüne çağrı.</li>
<li><strong>Engelsiz okul yolu</strong> — Okul çevresi bildirimleri + kampanya.</li>
<li><strong>Rehabilitasyon cihazı erişimi</strong> — Politika ve sigorta süreçlerine dikkat.</li>
<li><strong>Gönüllü destek ağı</strong> — Ulaşım ve bakımda dayanışma (bağış değil, gönüllülük çağrısı).</li>
</ol>

<h2>Nasıl başlarım?</h2>
<p>Üye olun, «Kampanya başlat» deyin, başlık ve metni yazın, onay bekleyin. Yayın sonrası panelden istatistikleri izleyin. Kent bildirimi için «Yeni bildir» yolunu kullanın.</p>

<h2>Topluluk</h2>
<p>Dernekler, gönüllüler ve aileler birlikte hareket ettiğinde dijital kayıt güçlenir. Siz de hikâyenizi ekleyin; moderasyon sonrası binlerce kişiye ulaşabilir.</p>

<p><em>simdibildir.com resmî kurum değildir; vatandaş katılımı ve toplumsal dayanışma platformudur.</em></p>
HTML,
        ];
    }
}
