<?php
session_start();
require_once 'config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0 && isset($_SESSION['id_hasil'])) {
    $id = (int)$_SESSION['id_hasil'];
}
if ($id <= 0) { header("Location: kuis.php"); exit; }

try {
    $stmt = $pdo->prepare("SELECT * FROM tugas_aceh WHERE id = ?");
    $stmt->execute([$id]);
    $data_user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data_user) { header("Location: kuis.php"); exit; }

    $jawaban_user = [];
    for ($i = 1; $i <= 20; $i++) {
        $jawaban_user[$i] = strtolower($data_user["jawaban_$i"] ?? '-');
    }
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}

$soal_data = [
    ["id"=>1,  "t"=>"Siapakah pendiri sekaligus Sultan pertama Kesultanan Aceh Darussalam?",                                                    "a"=>"Sultan Ali Mughayat Syah",                             "b"=>"Sultan Iskandar Muda",                        "c"=>"Sultan Iskandar Thani",                           "d"=>"Sultan Malik Al-Saleh",                                         "k"=>"a"],
    ["id"=>2,  "t"=>"Pada tahun berapakah Kesultanan Aceh Darussalam didirikan?",                                                               "a"=>"1496",                                                "b"=>"1511",                                        "c"=>"1607",                                            "d"=>"1873",                                                          "k"=>"a"],
    ["id"=>3,  "t"=>"Di manakah pusat pemerintahan pertama Kesultanan Aceh berlokasi?",                                                         "a"=>"Kutaraja (Banda Aceh)",                               "b"=>"Lamuri",                                      "c"=>"Aceh Darul Kamal",                                "d"=>"Johor",                                                         "k"=>"a"],
    ["id"=>4,  "t"=>"Salah satu kerajaan kecil yang bergabung membentuk Kesultanan Aceh adalah?",                                              "a"=>"Lamuri",                                              "b"=>"Samudera Pasai",                              "c"=>"Perlak",                                          "d"=>"Indrapuri",                                                     "k"=>"a"],
    ["id"=>5,  "t"=>"Peristiwa jatuhnya Malaka ke tangan Portugis pada tahun 1511 menjadi latar belakang berdirinya Aceh karena?",             "a"=>"Pedagang Muslim memindahkan jalur perdagangan ke Aceh","b"=>"Aceh ingin menaklukkan Malaka",                "c"=>"Portugis menyerang Aceh",                         "d"=>"Aceh bersekutu dengan Portugis",                                "k"=>"a"],
    ["id"=>6,  "t"=>"Sultan yang membawa Kesultanan Aceh mencapai puncak kejayaan adalah?",                                                    "a"=>"Sultan Ali Mughayat Syah",                             "b"=>"Sultan Iskandar Muda",                        "c"=>"Sultan Alaudin Riayat Syah",                      "d"=>"Sultan Iskandar Thani",                                         "k"=>"b"],
    ["id"=>7,  "t"=>"Pada rentang tahun berapakah Sultan Iskandar Muda memerintah Aceh?",                                                      "a"=>"1496-1528",                                           "b"=>"1607-1636",                                   "c"=>"1636-1641",                                       "d"=>"1874-1903",                                                     "k"=>"b"],
    ["id"=>8,  "t"=>"Wilayah kekuasaan Aceh pada masa kejayaan meluas hingga ke?",                                                             "a"=>"Pulau Jawa",                                          "b"=>"Semenanjung Malaya",                          "c"=>"Kalimantan",                                      "d"=>"Sulawesi",                                                      "k"=>"b"],
    ["id"=>9,  "t"=>"Komoditas perdagangan utama yang membuat Aceh menjadi pusat perdagangan internasional adalah?",                           "a"=>"Rempah-rempah",                                       "b"=>"Lada",                                        "c"=>"Emas",                                            "d"=>"Kain sutra",                                                    "k"=>"b"],
    ["id"=>10, "t"=>"Julukan apa yang diberikan kepada Aceh karena perannya sebagai pusat penyebaran Islam?",                                  "a"=>"Kota Serambi",                                        "b"=>"Serambi Mekkah",                              "c"=>"Negeri Islam",                                    "d"=>"Kesultanan Islam",                                              "k"=>"b"],
    ["id"=>11, "t"=>"Sultan yang memerintah setelah Iskandar Muda dan dikenal fokus pada pembangunan hukum dan agama adalah?",                 "a"=>"Sultan Ali Mughayat Syah",                             "b"=>"Sultan Alaudin Riayat Syah",                  "c"=>"Sultan Iskandar Thani",                           "d"=>"Sultan Muhammad Daud Syah",                                     "k"=>"c"],
    ["id"=>12, "t"=>"Kitab Bustanussalatin yang menjadi karya sastra peninggalan Aceh dikarang oleh?",                                         "a"=>"Hamzah Fansuri",                                      "b"=>"Syamsuddin as-Sumatrani",                     "c"=>"Nuruddin ar-Raniri",                              "d"=>"Abdurrauf as-Singkili",                                         "k"=>"c"],
    ["id"=>13, "t"=>"Taman Sari Gunongan dibangun oleh Sultan Iskandar Muda untuk?",                                                           "a"=>"Tempat peristirahatan prajurit",                       "b"=>"Upacara kenegaraan",                          "c"=>"Permaisuri Putroe Phang",                         "d"=>"Benteng pertahanan",                                            "k"=>"c"],
    ["id"=>14, "t"=>"Mata uang emas yang digunakan pada masa Kesultanan Aceh disebut?",                                                        "a"=>"Dinar",                                               "b"=>"Ringgit",                                     "c"=>"Dirham",                                          "d"=>"Rupiah",                                                        "k"=>"c"],
    ["id"=>15, "t"=>"Meriam pemberian Inggris yang terkenal sebagai peninggalan Kesultanan Aceh bernama?",                                      "a"=>"Meriam Si Jagur",                                     "b"=>"Meriam Ki Amuk",                              "c"=>"Meriam Raja James",                               "d"=>"Meriam Banten",                                                 "k"=>"c"],
    ["id"=>16, "t"=>"Siapa Sultan terakhir Kesultanan Aceh yang menyerah kepada Belanda pada tahun 1903?",                                     "a"=>"Sultan Iskandar Thani",                               "b"=>"Sultan Alaudin Riayat Syah",                  "c"=>"Sultan Mahmud Syah",                              "d"=>"Sultan Muhammad Daud Syah",                                     "k"=>"d"],
    ["id"=>17, "t"=>"Pada tahun berapakah Kesultanan Aceh secara resmi jatuh ke tangan Belanda setelah Perang Aceh?",                          "a"=>"1873",                                                "b"=>"1896",                                        "c"=>"1900",                                            "d"=>"1903",                                                          "k"=>"d"],
    ["id"=>18, "t"=>"Masjid Raya Baiturrahman merupakan peninggalan Kesultanan Aceh yang menjadi simbol?",                                     "a"=>"Kekuatan militer",                                    "b"=>"Kemakmuran ekonomi",                          "c"=>"Keindahan arsitektur",                            "d"=>"Kekuatan dan religiusitas rakyat Aceh",                         "k"=>"d"],
    ["id"=>18, "t"=>"Sultan Aceh yang menjalin hubungan diplomatik dengan Kesultanan Utsmaniyah (Turki) adalah?",                              "a"=>"Sultan Ali Mughayat Syah",                             "b"=>"Sultan Iskandar Muda",                        "c"=>"Sultan Iskandar Thani",                           "d"=>"Sultan Alaudin Riayat Syah al-Kahar",                           "k"=>"d"],
    ["id"=>20, "t"=>"Faktor eksternal yang mempercepat kemunduran Kesultanan Aceh adalah?",                                                    "a"=>"Serangan Portugis",                                   "b"=>"Pemberontakan rakyat",                        "c"=>"Bencana alam",                                    "d"=>"Munculnya VOC Belanda yang ingin memonopoli perdagangan",       "k"=>"d"],
];

$total_soal   = count($soal_data);
$total_benar  = 0;
$total_salah  = 0;
$total_kosong = 0;

foreach ($soal_data as $s) {
    $jwb = $jawaban_user[$s['id']];
    if ($jwb === '-')                         $total_kosong++;
    elseif ($jwb === strtolower($s['k']))     $total_benar++;
    else                                      $total_salah++;
}
$akurasi = round(($total_benar / $total_soal) * 100);
$target_scroll = isset($_GET['no']) ? (int)$_GET['no'] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pembahasan — AcehEdu Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    /* ============================================
       ROOT — IDENTIK EKOSISTEM
    ============================================ */
    :root {
        --gold:        #c9a84c;
        --gold-light:  #f0d080;
        --gold-dim:    rgba(201,168,76,0.12);
        --gold-glow:   rgba(201,168,76,0.45);
        --ink:         #020617;
        --ink-2:       #0f172a;
        --border:      rgba(201,168,76,0.18);
        --text:        #e8e0d0;
        --text-dim:    #8a7f70;
        --correct:     #4ade80;
        --wrong:       #ef4444;
        --empty:       #64748b;
        --radius:      20px;
        --font-display:'Cinzel', serif;
        --font-body:   'Inter', sans-serif;
    }

    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; overflow-x: hidden; }

    body {
        font-family: var(--font-body);
        background: var(--ink);
        color: var(--text);
        min-height: 100vh;
        overflow-x: hidden;
        padding-top: 72px;
        padding-bottom: 80px;
    }

    /* ============================================
       STAR FIELD
    ============================================ */
    #starCanvas {
        position: fixed; top: 0; left: 0;
        width: 100vw; height: 100vh;
        z-index: 0; pointer-events: none;
    }
    body::before {
        content: '';
        position: fixed; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
        z-index: 0; pointer-events: none; opacity: 0.6;
    }

    /* ============================================
       NAVBAR
    ============================================ */
    .navbar {
        position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
        padding: 16px 24px;
        display: flex; align-items: center; justify-content: space-between;
        background: rgba(2,6,23,0.85);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid var(--border);
    }
    .nav-brand {
        font-family: var(--font-display); font-size: 1.2rem; font-weight: 700;
        color: var(--gold); text-decoration: none; letter-spacing: 2px;
        text-shadow: 0 0 20px var(--gold-glow);
    }
    .nav-menu-btn {
        background: none; border: 1px solid var(--border); border-radius: 10px;
        padding: 8px 12px; cursor: pointer; color: var(--text); transition: all 0.3s;
    }
    .nav-menu-btn:hover { border-color: var(--gold); color: var(--gold); }
    .nav-spacer { width: 44px; }
    #readingProgress {
        position: fixed; top: 0; left: 0; height: 3px; width: 0%;
        background: linear-gradient(90deg, var(--gold), var(--gold-light));
        z-index: 1100; box-shadow: 0 0 12px var(--gold-glow);
        transition: width 0.1s linear; border-radius: 0 2px 2px 0;
    }

    /* ============================================
       SIDEBAR
    ============================================ */
    .offcanvas {
        background: rgba(2,6,23,0.97) !important;
        backdrop-filter: blur(30px);
        border-right: 1px solid var(--border) !important;
        max-width: 280px;
    }
    .offcanvas-header { border-bottom: 1px solid var(--border); padding: 24px; }
    .offcanvas-title {
        font-family: var(--font-display); color: var(--gold);
        font-size: 0.8rem; letter-spacing: 3px; text-transform: uppercase;
    }
    .sidebar-link {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 24px; color: var(--text-dim);
        text-decoration: none; font-size: 0.9rem; font-weight: 500;
        border-left: 3px solid transparent; transition: all 0.3s; margin: 2px 0;
    }
    .sidebar-link:hover, .sidebar-link.active {
        color: var(--gold); border-left-color: var(--gold); background: var(--gold-dim);
    }

    /* ============================================
       MAIN WRAP
    ============================================ */
    .main-wrap {
        position: relative; z-index: 1;
        max-width: 900px; margin: 0 auto;
        padding: 36px 20px 40px;
    }

    /* ============================================
       SECTION CARD
    ============================================ */
    .section-card {
        background: linear-gradient(145deg, rgba(20,18,14,0.9), rgba(2,6,23,0.95));
        border: 1px solid var(--border); border-radius: var(--radius);
        padding: 28px 32px; margin-bottom: 20px;
        position: relative; overflow: hidden;
        transition: border-color 0.4s, box-shadow 0.4s;
    }
    .section-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, var(--gold), transparent);
        opacity: 0.5;
    }
    .section-card::after {
        content: ''; position: absolute; top: 12px; right: 12px;
        width: 26px; height: 26px;
        border-top: 1px solid var(--border); border-right: 1px solid var(--border);
        border-radius: 0 6px 0 0; opacity: 0.35;
    }
    .section-tag {
        font-family: var(--font-display); font-size: 0.65rem;
        letter-spacing: 4px; color: var(--gold); text-transform: uppercase;
        margin-bottom: 20px; display: inline-flex; align-items: center;
        gap: 10px; opacity: 0.75;
    }
    .section-tag::before {
        content: ''; display: inline-block; width: 20px; height: 1px; background: var(--gold);
    }

    /* ============================================
       PAGE HEADER
    ============================================ */
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 28px; flex-wrap: wrap; gap: 14px;
    }
    .page-title {
        font-family: var(--font-display);
        font-size: clamp(1.2rem, 3vw, 1.6rem); font-weight: 900;
        background: linear-gradient(180deg, #f5edd8, var(--gold));
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .btn-back-hasil {
        display: inline-flex; align-items: center; gap: 10px;
        background: transparent; color: var(--gold);
        border: 1px solid var(--border); border-radius: 100px;
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 2px; text-transform: uppercase;
        padding: 10px 22px; text-decoration: none;
        transition: all 0.3s; white-space: nowrap;
    }
    .btn-back-hasil:hover { border-color: var(--gold); background: var(--gold-dim); color: var(--gold); box-shadow: 0 0 20px -5px var(--gold-glow); }

    /* ============================================
       SUMMARY CARD
    ============================================ */
    .stat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 22px; }
    .stat-mini {
        background: rgba(255,255,255,0.02); border: 1px solid var(--border);
        border-radius: 14px; padding: 18px; text-align: center; transition: all 0.3s;
    }
    .stat-mini:hover { transform: translateY(-3px); background: rgba(255,255,255,0.04); }
    .stat-mini-label {
        font-family: var(--font-display); font-size: 0.6rem;
        letter-spacing: 3px; color: var(--text-dim); text-transform: uppercase;
        margin-bottom: 8px;
    }
    .stat-mini-val {
        font-family: var(--font-display); font-size: 2.2rem;
        font-weight: 900; line-height: 1;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .val-benar  { color: var(--correct); text-shadow: 0 0 15px rgba(74,222,128,0.35); }
    .val-salah  { color: var(--wrong);   text-shadow: 0 0 15px rgba(239,68,68,0.35); }
    .val-kosong { color: var(--empty); }

    /* Accuracy bar */
    .acc-row { display: flex; align-items: center; gap: 14px; }
    .acc-track {
        flex: 1; height: 8px; background: rgba(255,255,255,0.04);
        border-radius: 100px; overflow: hidden; border: 1px solid rgba(255,255,255,0.04);
    }
    .acc-fill {
        height: 100%; width: 0%;
        background: linear-gradient(90deg, var(--gold), var(--gold-light));
        border-radius: 100px;
        transition: width 1.6s cubic-bezier(0.16,1,0.3,1);
        box-shadow: 0 0 10px var(--gold-glow);
    }
    .acc-label {
        font-family: var(--font-display); font-size: 0.78rem;
        font-weight: 700; color: var(--gold); white-space: nowrap;
    }

    /* ============================================
       FILTER PILLS
    ============================================ */
    .filter-wrap {
        display: flex; gap: 10px; flex-wrap: wrap;
        margin-bottom: 28px;
    }
    .filter-btn {
        background: rgba(255,255,255,0.02); border: 1px solid var(--border);
        color: var(--text-dim); padding: 9px 20px; border-radius: 100px;
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 2px; text-transform: uppercase;
        cursor: pointer; white-space: nowrap; transition: all 0.3s;
    }
    .filter-btn:hover { border-color: rgba(201,168,76,0.4); color: var(--text); }
    .filter-btn.active {
        background: var(--gold-dim); border-color: var(--gold);
        color: var(--gold); box-shadow: 0 0 16px -5px var(--gold-glow);
    }
    .filter-btn.active.f-benar  { background: rgba(74,222,128,0.1);  border-color: rgba(74,222,128,0.5);  color: var(--correct); }
    .filter-btn.active.f-salah  { background: rgba(239,68,68,0.1);   border-color: rgba(239,68,68,0.5);   color: var(--wrong); }

    /* ============================================
       QUESTION CARDS
    ============================================ */
    .question-card {
        background: linear-gradient(145deg, rgba(20,18,14,0.88), rgba(2,6,23,0.95));
        border: 1px solid var(--border); border-radius: var(--radius);
        padding: 28px 30px; margin-bottom: 16px;
        position: relative; overflow: hidden;
        transition: border-color 0.4s, box-shadow 0.4s, transform 0.4s;
        /* Reveal */
        opacity: 0; transform: translateY(28px);
    }
    .question-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, var(--border), transparent);
        opacity: 0.6; transition: opacity 0.4s;
    }
    .question-card.revealed { opacity: 1; transform: translateY(0); transition: opacity 0.7s cubic-bezier(0.16,1,0.3,1), transform 0.7s cubic-bezier(0.16,1,0.3,1), border-color 0.4s, box-shadow 0.4s; }
    .question-card:hover { border-color: rgba(201,168,76,0.3); box-shadow: 0 12px 40px -16px rgba(0,0,0,0.7); }

    /* Status stripe — left border */
    .question-card.is-benar  { border-left: 3px solid rgba(74,222,128,0.5); }
    .question-card.is-benar::before  { background: linear-gradient(90deg, rgba(74,222,128,0.4), transparent); }
    .question-card.is-salah  { border-left: 3px solid rgba(239,68,68,0.5); }
    .question-card.is-salah::before  { background: linear-gradient(90deg, rgba(239,68,68,0.4), transparent); }
    .question-card.is-kosong { border-left: 3px solid rgba(100,116,139,0.5); }
    .question-card.is-kosong::before { background: linear-gradient(90deg, rgba(100,116,139,0.3), transparent); }

    /* Target highlight — gold pulse */
    .question-card.target-highlight {
        border-color: var(--gold) !important;
        box-shadow: 0 0 0 2px rgba(201,168,76,0.2), 0 0 40px -10px var(--gold-glow) !important;
        animation: goldPulse 0.9s ease-in-out 3;
    }
    @keyframes goldPulse {
        0%,100% { box-shadow: 0 0 0 2px rgba(201,168,76,0.2), 0 0 30px -10px var(--gold-glow); }
        50%      { box-shadow: 0 0 0 6px rgba(201,168,76,0.08), 0 0 55px -5px var(--gold-glow); }
    }

    /* Q header row */
    .q-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px; gap: 12px;
    }
    .num-badge {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        background: var(--gold-dim); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-size: 0.88rem; font-weight: 700;
        color: var(--gold);
        animation: none;
    }
    .num-badge.pop { animation: popIn 0.5s cubic-bezier(0.175,0.885,0.32,1.275) forwards; }
    @keyframes popIn { 0% { transform: scale(0.5); opacity:0; } 100% { transform: scale(1); opacity:1; } }

    /* Status badge */
    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 100px;
        font-family: var(--font-display); font-size: 0.62rem;
        font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
    }
    .badge-benar  { background: rgba(74,222,128,0.1);  color: var(--correct); border: 1px solid rgba(74,222,128,0.3); }
    .badge-salah  { background: rgba(239,68,68,0.1);   color: var(--wrong);   border: 1px solid rgba(239,68,68,0.3); }
    .badge-kosong { background: rgba(100,116,139,0.1); color: var(--empty);   border: 1px solid rgba(100,116,139,0.3); }

    /* Question text */
    .q-text {
        font-family: var(--font-display); font-size: 0.98rem; font-weight: 600;
        line-height: 1.7; color: var(--text); margin-bottom: 20px; letter-spacing: 0.2px;
    }

    /* Options */
    .options-list { display: flex; flex-direction: column; gap: 10px; }
    .option-block {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; border-radius: 14px;
        border: 1px solid var(--border);
        background: rgba(255,255,255,0.02);
        font-size: 0.9rem; color: var(--text-dim);
        transition: all 0.3s; gap: 12px;
    }
    .opt-left { display: flex; align-items: flex-start; gap: 14px; flex: 1; }
    .opt-letter {
        font-family: var(--font-display); font-size: 0.75rem; font-weight: 700;
        color: var(--text-dim); flex-shrink: 0; margin-top: 2px; min-width: 16px;
    }
    .opt-text { line-height: 1.55; }
    .opt-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

    /* Correct option */
    .option-block.is-correct {
        background: rgba(74,222,128,0.06);
        border-color: rgba(74,222,128,0.35);
        color: var(--text);
    }
    .option-block.is-correct .opt-letter { color: var(--correct); }
    .option-block.is-correct:hover { box-shadow: 0 4px 20px -8px rgba(74,222,128,0.3); }

    /* Wrong option (user picked wrong) */
    .option-block.is-wrong {
        background: rgba(239,68,68,0.05);
        border-color: rgba(239,68,68,0.3);
        color: var(--text);
        animation: shakeWrong 0.5s cubic-bezier(.36,.07,.19,.97) 1;
    }
    .option-block.is-wrong .opt-letter { color: var(--wrong); }
    @keyframes shakeWrong {
        10%,90%  { transform: translateX(-2px); }
        20%,80%  { transform: translateX(3px); }
        30%,50%,70% { transform: translateX(-3px); }
        40%,60%  { transform: translateX(3px); }
        100%     { transform: translateX(0); }
    }

    /* Answer badges */
    .badge-ans {
        font-family: var(--font-display); font-size: 0.58rem;
        padding: 4px 10px; border-radius: 8px; letter-spacing: 1px; text-transform: uppercase;
    }
    .b-correct-self { background: rgba(74,222,128,0.2); color: var(--correct); }
    .b-correct-key  { background: transparent; color: var(--correct); border: 1px solid rgba(74,222,128,0.4); }
    .b-wrong-self   { background: rgba(239,68,68,0.2); color: var(--wrong); }

    /* ============================================
       ORNAMENT + FOOTER
    ============================================ */
    .ornament { text-align: center; margin: 32px 0; color: var(--gold); opacity: 0.3; font-size: 1rem; letter-spacing: 12px; font-family: var(--font-display); }
    .footer { text-align: center; padding: 36px 0 0; border-top: 1px solid var(--border); margin-top: 40px; font-size: 0.82rem; color: var(--text-dim); letter-spacing: 0.5px; }

    /* Back to top */
    #backToTop {
        position: fixed; bottom: 28px; right: 28px;
        width: 48px; height: 48px; border-radius: 50%;
        background: var(--ink-2); border: 1px solid var(--border);
        color: var(--gold); cursor: pointer; opacity: 0; visibility: hidden;
        transition: all 0.4s; z-index: 999;
        display: flex; align-items: center; justify-content: center;
    }
    #backToTop.show { opacity: 1; visibility: visible; }
    #backToTop:hover { background: var(--gold-dim); border-color: var(--gold); transform: translateY(-4px); box-shadow: 0 8px 20px var(--gold-glow); }

    /* ============================================
       MOBILE
    ============================================ */
    @media (max-width: 768px) {
        .main-wrap { padding: 24px 14px 60px; }
        .section-card { padding: 20px 16px; }
        .question-card { padding: 20px 16px; }
        .stat-grid { gap: 8px; }
        .stat-mini-val { font-size: 1.8rem; }
        .page-header { flex-direction: column; align-items: flex-start; }
        .option-block { flex-direction: column; align-items: flex-start; }
        .opt-right { width: 100%; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px; }
        .filter-wrap { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px; scrollbar-width: none; }
        .filter-wrap::-webkit-scrollbar { display: none; }
    }
    </style>
</head>
<body>

<canvas id="starCanvas"></canvas>
<div id="readingProgress"></div>

<!-- NAVBAR -->
<nav class="navbar">
    <button class="nav-menu-btn" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-label="Buka menu">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>
    <a class="nav-brand" href="index.php">Aceh<span style="color:var(--gold-light)">Edu</span></a>
    <div class="nav-spacer"></div>
</nav>

<!-- SIDEBAR -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header">
        <span class="offcanvas-title">Menu Utama</span>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body p-0 mt-2">
        <a class="sidebar-link" href="index.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
            Materi Belajar
        </a>
        <a class="sidebar-link" href="peraturan.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Peraturan Kuis
        </a>
        <a class="sidebar-link" href="kuis.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Mulai Kuis
        </a>
        <a class="sidebar-link active" href="hasil.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Hasil & Review
        </a>
    </div>
</div>

<!-- MAIN -->
<div class="main-wrap">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">Pembahasan Jawaban</h1>
        <a href="hasil.php?id=<?= $id ?>" class="btn-back-hasil">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali ke Hasil
        </a>
    </div>

    <!-- SUMMARY CARD -->
    <div class="section-card">
        <div class="section-tag">Ringkasan Nilai</div>
        <div class="stat-grid">
            <div class="stat-mini">
                <div class="stat-mini-label">Benar</div>
                <div class="stat-mini-val val-benar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="count-up" data-target="<?= $total_benar ?>">0</span>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-label">Salah</div>
                <div class="stat-mini-val val-salah">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <span class="count-up" data-target="<?= $total_salah ?>">0</span>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-label">Kosong</div>
                <div class="stat-mini-val val-kosong">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span class="count-up" data-target="<?= $total_kosong ?>">0</span>
                </div>
            </div>
        </div>
        <div class="acc-row">
            <div class="acc-track">
                <div class="acc-fill" id="accFill" data-target="<?= $akurasi ?>"></div>
            </div>
            <span class="acc-label">Akurasi <?= $akurasi ?>%</span>
        </div>
    </div>

    <!-- FILTER PILLS -->
    <div class="filter-wrap" id="filterWrap">
        <button class="filter-btn active" data-filter="all">Semua (<?= $total_soal ?>)</button>
        <button class="filter-btn f-benar" data-filter="benar">Benar (<?= $total_benar ?>)</button>
        <button class="filter-btn f-salah" data-filter="salah">Salah + Kosong (<?= $total_salah + $total_kosong ?>)</button>
    </div>

    <!-- QUESTION CARDS -->
    <div id="questionList">
    <?php
    foreach ($soal_data as $index => $soal):
        $no         = $soal['id'];
        $jawab_user = $jawaban_user[$no];
        $kunci      = strtolower($soal['k']);

        if ($jawab_user === '-') {
            $card_status  = 'is-kosong';
            $badge_class  = 'badge-kosong';
            $badge_text   = 'Tidak Dijawab';
            $badge_icon   = '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>';
            $filter_type  = 'salah';
        } elseif ($jawab_user === $kunci) {
            $card_status  = 'is-benar';
            $badge_class  = 'badge-benar';
            $badge_text   = 'Benar';
            $badge_icon   = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>';
            $filter_type  = 'benar';
        } else {
            $card_status  = 'is-salah';
            $badge_class  = 'badge-salah';
            $badge_text   = 'Salah';
            $badge_icon   = '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>';
            $filter_type  = 'salah';
        }

        $is_target    = ($target_scroll === $no);
        $target_class = $is_target ? 'target-highlight' : '';
        $delay_ms     = $index * 80;
    ?>
    <div class="question-card <?= $card_status ?> <?= $filter_type ?> <?= $target_class ?>"
         id="soal-<?= $no ?>"
         data-filter="<?= $filter_type ?>"
         style="transition-delay: <?= $delay_ms ?>ms;">

        <div class="q-head">
            <div class="num-badge" id="num-<?= $no ?>"><?= $no ?></div>
            <div class="status-badge <?= $badge_class ?>">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><?= $badge_icon ?></svg>
                <?= $badge_text ?>
            </div>
        </div>

        <div class="q-text"><?= htmlspecialchars($soal['t']) ?></div>

        <div class="options-list">
        <?php foreach (['a','b','c','d'] as $huruf):
            $teks_opsi    = $soal[$huruf];
            $is_kunci     = ($huruf === $kunci);
            $is_user_jawab= ($huruf === $jawab_user);

            $opt_class = '';
            $badge_html= '';
            $icon_html = '';

            if ($is_kunci && $is_user_jawab) {
                $opt_class  = 'is-correct';
                $badge_html = '<span class="badge-ans b-correct-self">Jawaban Anda ✓</span>';
                $icon_html  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>';
            } elseif ($is_kunci && !$is_user_jawab) {
                $opt_class  = 'is-correct';
                $badge_html = '<span class="badge-ans b-correct-key">Kunci Jawaban</span>';
                $icon_html  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>';
            } elseif ($is_user_jawab && !$is_kunci) {
                $opt_class  = 'is-wrong';
                $badge_html = '<span class="badge-ans b-wrong-self">Jawaban Anda ✗</span>';
                $icon_html  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
            }
        ?>
            <div class="option-block <?= $opt_class ?>">
                <div class="opt-left">
                    <span class="opt-letter"><?= strtoupper($huruf) ?>.</span>
                    <span class="opt-text"><?= htmlspecialchars($teks_opsi) ?></span>
                </div>
                <?php if($badge_html || $icon_html): ?>
                <div class="opt-right">
                    <?= $badge_html ?>
                    <?= $icon_html ?>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>

    </div>
    <?php endforeach; ?>
    </div>

    <div class="ornament">· · ✦ · ·</div>

    <!-- BACK BUTTON -->
    <div style="text-align:center; margin-top: 8px;">
        <a href="hasil.php?id=<?= $id ?>" class="btn-back-hasil" style="padding: 16px 40px; font-size:0.78rem; letter-spacing:3px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali ke Hasil
        </a>
    </div>

    <div class="footer">
        <p>© 2026 AcehEdu Premium — Modul Sejarah Interaktif by ZM</p>
    </div>

</div><!-- /main-wrap -->

<button id="backToTop" aria-label="Kembali ke atas">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
    </svg>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ============================================================
   STAR FIELD
============================================================ */
(function(){
    const canvas = document.getElementById('starCanvas');
    const ctx    = canvas.getContext('2d');
    let stars    = [];
    function resize(){ canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
    function createStars(n){
        stars = [];
        for(let i=0;i<n;i++) stars.push({
            x: Math.random()*canvas.width, y: Math.random()*canvas.height,
            r: Math.random()*1.2+0.2, alpha: Math.random()*0.6+0.1,
            speed: Math.random()*0.003+0.001, phase: Math.random()*Math.PI*2
        });
    }
    function draw(t){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        stars.forEach(s=>{
            const a = s.alpha + Math.sin(t*s.speed+s.phase)*0.2;
            ctx.beginPath(); ctx.arc(s.x,s.y,s.r,0,Math.PI*2);
            ctx.fillStyle=`rgba(240,220,160,${a})`; ctx.fill();
        });
        requestAnimationFrame(draw);
    }
    resize(); createStars(160); requestAnimationFrame(draw);
    window.addEventListener('resize',()=>{ resize(); createStars(160); });
})();

/* ============================================================
   READING PROGRESS + BACK TO TOP
============================================================ */
window.addEventListener('scroll',()=>{
    const st = document.documentElement.scrollTop;
    const sh = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    document.getElementById('readingProgress').style.width = (st/sh*100)+'%';
    document.getElementById('backToTop').classList.toggle('show', st > 300);
});
document.getElementById('backToTop').addEventListener('click',()=> window.scrollTo({top:0,behavior:'smooth'}));

/* ============================================================
   COUNT-UP ANIMATION
============================================================ */
function animateCountUp(){
    document.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.dataset.target);
        if(target === 0){ el.textContent = '0'; return; }
        const dur = 1200; let start = null;
        const step = ts => {
            if(!start) start = ts;
            const p = Math.min((ts-start)/dur, 1);
            el.textContent = Math.floor(p * target);
            if(p < 1) requestAnimationFrame(step);
            else el.textContent = target;
        };
        requestAnimationFrame(step);
    });
}

/* ============================================================
   ACCURACY BAR ANIMATION
============================================================ */
function animateAccBar(){
    const fill = document.getElementById('accFill');
    if(fill){
        setTimeout(()=>{
            fill.style.width = fill.dataset.target + '%';
        }, 400);
    }
}

/* ============================================================
   SCROLL REVEAL — IntersectionObserver per card
============================================================ */
function initReveal(){
    const cards = document.querySelectorAll('.question-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting){
                const card = entry.target;
                card.classList.add('revealed');
                // Pop-in for num badge
                const badge = card.querySelector('.num-badge');
                if(badge) setTimeout(()=> badge.classList.add('pop'), 120);
                observer.unobserve(card);
            }
        });
    }, { threshold: 0.08 });
    cards.forEach(card => observer.observe(card));
}

/* ============================================================
   FILTER
============================================================ */
function initFilter(){
    const btns      = document.querySelectorAll('.filter-btn');
    const cards     = document.querySelectorAll('.question-card');

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.dataset.filter;

            cards.forEach((card, i) => {
                const show = filter === 'all' || card.dataset.filter === filter;
                if(show){
                    card.style.display = '';
                    card.style.transitionDelay = (i * 40) + 'ms';
                    // Re-trigger reveal if not yet revealed
                    if(!card.classList.contains('revealed')){
                        setTimeout(()=> card.classList.add('revealed'), i * 40);
                    }
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

/* ============================================================
   TARGET SCROLL — smooth scroll ke soal dari heatmap
============================================================ */
function initTargetScroll(){
    const targetId = '<?= $target_scroll ? "soal-".$target_scroll : "" ?>';
    if(!targetId) return;
    const el = document.getElementById(targetId);
    if(!el) return;
    setTimeout(()=>{
        const y = el.getBoundingClientRect().top + window.pageYOffset - 100;
        window.scrollTo({ top: y, behavior: 'smooth' });
    }, 500);
}

/* ============================================================
   INIT
============================================================ */
window.addEventListener('DOMContentLoaded', ()=>{
    animateCountUp();
    animateAccBar();
    initReveal();
    initFilter();
    initTargetScroll();
});
</script>
</body>
</html>
