<?php
/**
 * MARVEL SİNEMATİK EVRENİ - WEB PROGRAMLAMA FİNAL ÖDEVİ
 * Tüm sayfalar, formlar, koşul yapıları ve döngüler tek dosyada birleştirilmiştir.
 */

// 1. VERİ SAKLAMA ALTYAPISI (Final 5.3)
$jsonDosya = 'veri.json';
// Eğer veri.json dosyası yoksa veya içi boşsa otomatik olarak oluşturur
if (!file_exists($jsonDosya) || filesize($jsonDosya) == 0) {
    file_put_contents($jsonDosya, json_encode([], JSON_PRETTY_PRINT));
}

// 2. SAYFA YÖNLENDİRME KONTROLÜ (Dinamik Sayfa Kontrolü - Final 5.2)
// URL'den gelen 'sayfa' parametresine göre hangi içeriğin yükleneceğini seçer
$aktifSayfa = isset($_GET['sayfa']) ? $_GET['sayfa'] : 'anasayfa';

// 3. FORM İŞLEMLERİ VE YÖNETİM (Vize 4.3 - 4.4 & Final 5.4)
$mesaj = "";
$mesajTuru = "";

// Yorum Gönderme Formu İşleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['yorum_gonder'])) {
    $isim = strip_tags(trim($_POST['isim']));
    $filmAdi = strip_tags(trim($_POST['film_adi']));
    $yorumMetni = strip_tags(trim($_POST['yorum_metni']));

    // Koşul Yapısı ile Form Doğrulama (Vize 4.4)
    if (empty($isim) || empty($filmAdi) || empty($yorumMetni)) {
        $mesaj = "Lütfen tüm alanları (Ad, Film ve Yorum) eksiksiz doldurunuz!";
        $mesajTuru = "danger";
    } else {
        $mevcutYorumlar = json_decode(file_get_contents($jsonDosya), true);
        if (!is_array($mevcutYorumlar)) { $mevcutYorumlar = []; }

        $yeniYorum = [
            "id" => uniqid(),
            "isim" => $isim,
            "film_adi" => $filmAdi,
            "yorum_metni" => $yorumMetni,
            "tarih" => date('d.m.Y H:i')
        ];

        $mevcutYorumlar[] = $yeniYorum;
        file_put_contents($jsonDosya, json_encode($mevcutYorumlar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $mesaj = "Yorumunuz başarıyla veri.json dosyasına kaydedildi!";
        $mesajTuru = "success";
    }
}

// İletişim Formu İşleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iletisim_gonder'])) {
    $email = strip_tags(trim($_POST['email']));
    $iletisimMesaj = strip_tags(trim($_POST['mesaj']));

    if (empty($email) || empty($iletisimMesaj)) {
        $mesaj = "Lütfen e-posta adresinizi ve mesajınızı yazın.";
        $mesajTuru = "danger";
    } else {
        $mesaj = "Mesajınız başarıyla alındı! (Simüle Edildi)";
        $mesajTuru = "success";
    }
}

// Yorum Silme İşlemi (Final 5.4 Basit Yönetim İşlemleri - İçerik/Yorum Silme)
if (isset($_GET['sil_id'])) {
    $silId = $_GET['sil_id'];
    $mevcutYorumlar = json_decode(file_get_contents($jsonDosya), true);
    
    if (is_array($mevcutYorumlar)) {
        foreach ($mevcutYorumlar as $anahtar => $deger) {
            if ($deger['id'] == $silId) {
                unset($mevcutYorumlar[$anahtar]);
                break;
            }
        }
        $mevcutYorumlar = array_values($mevcutYorumlar);
        file_put_contents($jsonDosya, json_encode($mevcutYorumlar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: index.php?sayfa=yorumlar"); // Silindikten sonra yorum sayfasına dön
        exit;
    }
}

// Tüm yorumları listelemek için dosyadan çekiyoruz
$tumYorumlar = json_decode(file_get_contents($jsonDosya), true);

// 4. MARVEL FİLM VERİLERİ (PHP Dizisi - Final 5.2)
$marvelFilmleri = [
    [
        "ad" => "Iron Man", "yil" => 2008, "imdb" => "7.9", "renk" => "#9B111E",
        "ozet" => "Milyarder endüstrici Tony Stark, esir düştüğü bir mağarada gelişmiş zırhlı bir giysi üreterek dünyayı kurtaran bir kahramana dönüşür.",
        "link" => "https://www.imdb.com/title/tt0371746/"
    ],
    [
        "ad" => "The Avengers", "yil" => 2012, "imdb" => "8.0", "renk" => "#1C3B57",
        "ozet" => "Dünyanın en güçlü kahramanları, küresel güvenliği tehdit eden Loki ve uzaylı ordusuna karşı tek bir ekip olarak birleşmek zorundadır.",
        "link" => "https://www.imdb.com/title/tt0848228/"
    ],
    [
        "ad" => "Captain America: Winter Soldier", "yil" => 2014, "imdb" => "7.8", "renk" => "#2A4B7C",
        "ozet" => "Steve Rogers, S.H.I.E.L.D. içindeki derin bir komployu açığa çıkarmak için geçmişinden gelen gizemli bir suikastçıya karşı savaşır.",
        "link" => "https://www.imdb.com/title/tt1843866/"
    ],
    [
        "ad" => "Avengers: Infinity War", "yil" => 2018, "imdb" => "8.4", "renk" => "#4B0082",
        "ozet" => "Yenilmezler ve müttefikleri, evrenin yarısını yok etmeyi amaçlayan yıkıcı Thanos'u durdurmak için her şeyi feda etmeye hazır olmalıdır.",
        "link" => "https://www.imdb.com/title/tt4154756/"
    ],
    [
        "ad" => "Avengers: Endgame", "yil" => 2019, "imdb" => "8.4", "renk" => "#4A0E17",
        "ozet" => "Evrenin harap olmasının ardından, kalan müttefikler Thanos'un eylemlerini geri almak ve evrende düzeni yeniden sağlamak için bir araya geliyor.",
        "link" => "https://www.imdb.com/title/tt4154664/"
    ]
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCU Küresel Hayran Platformu | PHP Final</title>
    <style>
        :root { --marvel-red: #E23636; --dark-bg: #121212; --card-bg: #1E1E1E; --text-color: #FFFFFF; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--dark-bg); color: var(--text-color); display: flex; flex-direction: column; min-height: 100vh; }
        
        /* Navigasyon Barı */
        .navbar { background-color: #000000; display: flex; justify-content: space-between; align-items: center; padding: 15px 50px; border-bottom: 3px solid var(--marvel-red); box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
        .logo { font-weight: 900; font-size: 26px; letter-spacing: 1px; color: #fff; background-color: var(--marvel-red); padding: 5px 15px; transform: skewX(-10deg); text-transform: uppercase; }
        .nav-links { display: flex; list-style: none; }
        .nav-links li { margin-left: 15px; }
        .nav-links a { color: #bbb; text-decoration: none; font-weight: 600; font-size: 15px; transition: 0.3s; padding: 8px 12px; border-radius: 4px; }
        .nav-links a:hover, .nav-links a.active { color: #fff; background-color: rgba(226, 54, 54, 0.2); border-bottom: 2px solid var(--marvel-red); }
        
        /* Ana Kapsayıcı */
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; flex-grow: 1; width: 100%; }
        
        /* Hero Alanı (Ana Sayfa) */
        .hero { text-align: center; padding: 80px 20px; background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('https://via.placeholder.com/1200x400/000000/FFFFFF?text=MARVEL+STUDIOS'); background-size: cover; border-radius: 10px; margin-bottom: 40px; border: 1px solid #333; }
        .hero h1 { font-size: 44px; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; color: #fff; }
        .hero p { font-size: 17px; color: #aaa; max-width: 750px; margin: 0 auto 25px auto; line-height: 1.6; }
        
        /* Kart Yapıları */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .card { background-color: var(--card-bg); border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3); transition: 0.3s; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #292929; }
        .card:hover { transform: translateY(-5px); border-color: var(--marvel-red); }
        .card-img-placeholder { height: 180px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; text-align: center; padding: 10px; text-transform: uppercase; letter-spacing: 1px; text-shadow: 2px 2px 4px rgba(0,0,0,0.6); }
        .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .card-title { font-size: 20px; margin-bottom: 10px; color: #fff; font-weight: 700; }
        .card-text { color: #b3b3b3; font-size: 14px; line-height: 1.6; margin-bottom: 20px; }
        .meta-info { display: flex; justify-content: space-between; font-size: 13px; color: #fff; background: rgba(0,0,0,0.4); padding: 6px 10px; border-radius: 4px; margin-bottom: 15px; }
        
        /* Butonlar */
        .btn { display: inline-block; background-color: var(--marvel-red); color: #fff; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 4px; transition: 0.3s; border: none; cursor: pointer; text-align: center; font-size: 14px; }
        .btn:hover { background-color: #b32525; box-shadow: 0 0 10px rgba(226, 54, 54, 0.5); }
        
        /* Form Elemanları */
        .form-panel { background-color: var(--card-bg); padding: 30px; border-radius: 8px; border: 1px solid #292929; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #ccc; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; background-color: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; font-size: 15px; }
        .form-control:focus { border-color: var(--marvel-red); outline: none; }
        
        /* Yorum Kutuları */
        .comment-layout { display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px; }
        .comment-box { background-color: var(--card-bg); padding: 20px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid var(--marvel-red); position: relative; border-top: 1px solid #292929; border-right: 1px solid #292929; border-bottom: 1px solid #292929; }
        .comment-meta { font-size: 12px; color: #999; margin-bottom: 8px; }
        .comment-text { color: #e0e0e0; font-size: 14px; line-height: 1.5; }
        .comment-delete { position: absolute; top: 15px; right: 15px; color: var(--marvel-red); text-decoration: none; font-weight: bold; font-size: 13px; transition: 0.2s; }
        .comment-delete:hover { color: #fff; }
        
        /* Bildirimler */
        .alert { padding: 15px; margin-bottom: 25px; border-radius: 4px; font-weight: 600; font-size: 15px; text-align: center; }
        .alert-success { background-color: #1b5e20; color: #fff; border: 1px solid #2e7d32; }
        .alert-danger { background-color: #b71c1c; color: #fff; border: 1px solid #c62828; }
        
        /* Footer */
        footer { background-color: #000; text-align: center; padding: 20px; color: #555; font-size: 13px; border-top: 1px solid #222; margin-top: auto; }
        
        /* Hakkında Bölümü Sayfa Tasarımı */
        .about-box { background-color: var(--card-bg); padding: 40px; border-radius: 8px; border: 1px solid #292929; line-height: 1.8; }
        .about-box h2 { color: var(--marvel-red); margin-bottom: 15px; }
        .about-box ul { margin-left: 25px; margin-top: 10px; color: #ccc; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">MCU Portal v2.0</div>
        <ul class="nav-links">
            <?php
            // Sayfa başlıkları ve parametre eşleşmeleri dizisi
            $navigasyon = [
                'anasayfa' => 'Ana Sayfa',
                'filmler' => 'Marvel Filmleri',
                'yorumlar' => 'Yorum Yap & Yönet',
                'hakkinda' => 'Hakkında',
                'iletisim' => 'İletişim'
            ];
            foreach ($navigasyon as $key => $value) {
                // Aktif olan sayfaya ".active" CSS sınıfını ekleyen koşul kontrolü
                $activeClass = ($aktifSayfa === $key) ? 'class="active"' : '';
                echo "<li><a href='index.php?sayfa={$key}' {$activeClass}>{$value}</a></li>";
            }
            ?>
        </ul>
    </nav>

    <div class="container">
        
        <?php if (!empty($mesaj)): ?>
            <div class="alert alert-<?php echo $mesajTuru; ?>">
                <?php echo $mesaj; ?>
            </div>
        <?php endif; ?>

        <?php
        // PHP KOŞUL YAPISI (İstenen sayfaya göre ilgili kod bloğunu ekrana basar) - Vize 4.4 & Final 5.2
        if ($aktifSayfa === 'anasayfa') {
            ?>
            <div class="hero">
                <h1>Marvel Sinematik Evreni</h1>
                <p>Bu web uygulaması, PHP dilinin dinamik yeteneklerini sergilemek amacıyla hazırlanmış bir Web Programlama Dönem Projesidir. Tek dosya mimarisi üzerinde, form yönetimleri, döngüler ve JSON veri tabanı simülasyonları entegre edilmiştir.</p>
                <a href="index.php?sayfa=filmler" class="btn">Gelişmiş Arşivi İncele</a>
            </div>

            <h2 style="margin-bottom: 25px; border-left: 4px solid var(--marvel-red); padding-left: 10px; font-transform: uppercase;">Sistem Yetenekleri</h2>
            <div class="grid">
                <div class="card">
                    <div class="card-img-placeholder" style="background-color: #333;">Dinamik Yapı</div>
                    <div class="card-body">
                        <h3 class="card-title">Tek Dosya Modülü</h3>
                        <p class="card-text">URL Parametre kontrolü ile tüm sayfalar tek bir script dosyası üzerinden yönlendirilir ve hiyerarşi korunur.</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-img-placeholder" style="background-color: #444;">JSON Veri</div>
                    <div class="card-body">
                        <h3 class="card-title">Dosya Tabanlı Depolama</h3>
                        <p class="card-text">Veritabanı bağımlılığı olmadan çalışabilmesi adına, hocanın yönergelerine uygun şekilde veri okuma/yazma JSON ile sağlanır.</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-img-placeholder" style="background-color: #555;">Dış Bağlantı</div>
                    <div class="card-body">
                        <h3 class="card-title">IMDb Entegrasyonu</h3>
                        <p class="card-text">Her filmin kendine ait gerçek dünya IMDb sayfasına, güvenli yönlendirme protokolü eklenmiştir.</p>
                    </div>
                </div>
            </div>
            <?php
        } 
        
        elseif ($aktifSayfa === 'filmler') {
            ?>
            <h1 style="margin-bottom: 30px; border-left: 4px solid var(--marvel-red); padding-left: 10px;">Dinamik MCU Film Kataloğu</h1>
            <div class="grid">
                <?php 
                // Film verilerini döngü yardımıyla dinamik kartlar halinde basıyoruz (Vize 4.5 & Final 5.2)
                foreach ($marvelFilmleri as $film): 
                ?>
                    <div class="card">
                        <div class="card-img-placeholder" style="background-color: <?php echo $film['renk']; ?>;">
                            <?php echo $film['ad']; ?>
                        </div>
                        <div class="card-body">
                            <div class="meta-info">
                                <span>Vizyon: <?php echo $film['yil']; ?></span>
                                <span>Puan: ⭐ <?php echo $film['imdb']; ?></span>
                            </div>
                            <h3 class="card-title"><?php echo $film['ad']; ?></h3>
                            <p class="card-text"><?php echo $film['ozet']; ?></p>
                            
                            <a href="<?php echo $film['link']; ?>" target="_blank" class="btn" style="width: 100%;">IMDb Sayfasına Git ↗</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php
        } 
        
        elseif ($aktifSayfa === 'yorumlar') {
            ?>
            <div class="comment-layout">
                
                <div class="form-panel">
                    <h2 style="color: var(--marvel-red); margin-bottom: 20px;">Topluluk Değerlendirmesi</h2>
                    <form action="index.php?sayfa=yorumlar" method="POST">
                        <div class="form-group">
                            <label>Adınız Soyadınız:</label>
                            <input type="text" name="isim" class="form-control" placeholder="Örn: Bruce Banner" required>
                        </div>
                        <div class="form-group">
                            <label>Değerlendirilecek Film:</label>
                            <select name="film_adi" class="form-control">
                                <?php foreach($marvelFilmleri as $f): ?>
                                    <option value="<?php echo $f['ad']; ?>"><?php echo $f['ad']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Eleştiriniz / Yorumunuz:</label>
                            <textarea name="yorum_metni" rows="6" class="form-control" placeholder="Film hakkındaki düşünceleriniz..." required></textarea>
                        </div>
                        <button type="submit" name="yorum_gonder" class="btn" style="width: 100%;">Değerlendirmeyi Gönder</button>
                    </form>
                </div>

                <div>
                    <h2 style="margin-bottom: 20px; border-left: 4px solid var(--marvel-red); padding-left: 10px;">Kayıtlı Canlı Yorumlar (JSON Verisi)</h2>
                    
                    <?php if (empty($tumYorumlar)): ?>
                        <p style="color: #666; font-style: italic; padding: 20px;">Sistemde kayıtlı yorum bulunamadı. İlk yorumu form üzerinden siz ekleyebilirsiniz.</p>
                    <?php else: ?>
                        <?php foreach ($tumYorumlar as $y): ?>
                            <div class="comment-box">
                                <div class="comment-meta">
                                    <strong><?php echo htmlspecialchars($y['isim']); ?></strong> &bull; 
                                    <span style="color: var(--marvel-red); font-weight:bold;"><?php echo htmlspecialchars($y['film_adi']); ?></span> 
                                    (<?php echo $y['tarih']; ?>)
                                </div>
                                <div class="comment-text">
                                    <?php echo nl2br(htmlspecialchars($y['yorum_metni'])); ?>
                                </div>
                                <a href="index.php?sayfa=yorumlar&sil_id=<?php echo $y['id']; ?>" class="comment-delete" onclick="return confirm('Bu yorum kaydını kalıcı olarak silmek istediğinize emin misiniz?');">Sil 🗑️</a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
            <?php
        } 
        
        elseif ($aktifSayfa === 'hakkinda') {
            ?>
            <div class="about-box">
                <h2>Proje Geliştirme Detayları</h2>
                <p>Bu proje, <strong>PHP ile Web Programlama Dersi</strong> final sınavı isterleri doğrultusunda yapılandırılmıştır. Tüm mimari tek bir merkezi kontrol dosyası üzerinden yürütülmektedir.</p>
                <p style="margin-top: 10px;">Projede kullanılan mimari yaklaşımlar:</p>
                <ul>
                    <li><strong>Tek Dosya Kontrolü (Single Page Application Logic):</strong> `switch/case` ya da `if/else` koşulları ile URL parametre okuması yapılarak sayfa yönetim yükü azaltılmıştır.</li>
                    <li><strong>JSON Entegrasyonu:</strong> Sunucuda harici bir SQL motoruna ihtiyaç duyulmadan veri transferi kalıcı hale getirilmiştir.</li>
                    <li><strong>Güvenlik Katmanı:</strong> `strip_tags()` ve `htmlspecialchars()` kullanılarak form girdilerindeki XSS açıkları engellenmiştir.</li>
                </ul>
            </div>
            <?php
        } 
        
        elseif ($aktifSayfa === 'iletisim') {
            ?>
            <div class="form-panel" style="max-width: 600px; margin: 0 auto;">
                <h2 style="color: var(--marvel-red); margin-bottom: 15px; text-align: center;">Akademik İletişim</h2>
                <p style="color: #aaa; text-align: center; margin-bottom: 25px;">Sistem mimarisi veya kod blokları hakkında sorularınızı iletebilirsiniz.</p>
                <form action="index.php?sayfa=iletisim" method="POST">
                    <div class="form-group">
                        <label>E-Posta Adresiniz:</label>
                        <input type="email" name="email" class="form-control" placeholder="ornek@universite.edu.tr" required>
                    </div>
                    <div class="form-group">
                        <label>Mesaj İçeriği:</label>
                        <textarea name="mesaj" rows="5" class="form-control" placeholder="Mesajınızı bu alana giriniz..." required></textarea>
                    </div>
                    <button type="submit" name="iletisim_gonder" class="btn" style="width: 100%;">Mesajı İlet</button>
                </form>
            </div>
            <?php
        } 
        
        else {
            // Hatalı sayfa istekleri için koruma paneli
            echo "<h2 style='text-align:center; color:var(--marvel-red); margin-top:50px;'>404 Sayfa Bulunamadı!</h2>";
        }
        ?>

    </div>

    <footer>
        <p>&copy; 2026 Marvel Web Tasarım Portalı. Tüm Hakları Saklıdır. | PHP Final Proje Ödevi Teslimi</p>
    </footer>

</body>
</html>