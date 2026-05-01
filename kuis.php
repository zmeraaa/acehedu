<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['sudah_kuis'])) {
    header("Location: hasil.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$soal_asli = [
    ["id" => 1,  "t" => "Siapakah pendiri sekaligus Sultan pertama Kesultanan Aceh Darussalam?",                                                     "a" => "Sultan Ali Mughayat Syah",                              "b" => "Sultan Iskandar Muda",                           "c" => "Sultan Iskandar Thani",                              "d" => "Sultan Malik Al-Saleh",                                          "k" => "a"],
    ["id" => 2,  "t" => "Pada tahun berapakah Kesultanan Aceh Darussalam didirikan?",                                                                "a" => "1496",                                                  "b" => "1511",                                           "c" => "1607",                                               "d" => "1873",                                                           "k" => "a"],
    ["id" => 3,  "t" => "Di manakah pusat pemerintahan pertama Kesultanan Aceh berlokasi?",                                                          "a" => "Kutaraja (Banda Aceh)",                                 "b" => "Lamuri",                                         "c" => "Aceh Darul Kamal",                                   "d" => "Johor",                                                          "k" => "a"],
    ["id" => 4,  "t" => "Salah satu kerajaan kecil yang bergabung membentuk Kesultanan Aceh adalah?",                                               "a" => "Lamuri",                                                "b" => "Samudera Pasai",                                 "c" => "Perlak",                                             "d" => "Indrapuri",                                                      "k" => "a"],
    ["id" => 5,  "t" => "Peristiwa jatuhnya Malaka ke tangan Portugis pada tahun 1511 menjadi latar belakang berdirinya Aceh karena?",              "a" => "Pedagang Muslim memindahkan jalur perdagangan ke Aceh", "b" => "Aceh ingin menaklukkan Malaka",                  "c" => "Portugis menyerang Aceh",                            "d" => "Aceh bersekutu dengan Portugis",                                 "k" => "a"],
    ["id" => 6,  "t" => "Sultan yang membawa Kesultanan Aceh mencapai puncak kejayaan adalah?",                                                     "a" => "Sultan Ali Mughayat Syah",                              "b" => "Sultan Iskandar Muda",                           "c" => "Sultan Alaudin Riayat Syah",                         "d" => "Sultan Iskandar Thani",                                          "k" => "b"],
    ["id" => 7,  "t" => "Pada rentang tahun berapakah Sultan Iskandar Muda memerintah Aceh?",                                                       "a" => "1496-1528",                                             "b" => "1607-1636",                                      "c" => "1636-1641",                                          "d" => "1874-1903",                                                      "k" => "b"],
    ["id" => 8,  "t" => "Wilayah kekuasaan Aceh pada masa kejayaan meluas hingga ke?",                                                              "a" => "Pulau Jawa",                                            "b" => "Semenanjung Malaya",                             "c" => "Kalimantan",                                         "d" => "Sulawesi",                                                       "k" => "b"],
    ["id" => 9,  "t" => "Komoditas perdagangan utama yang membuat Aceh menjadi pusat perdagangan internasional adalah?",                            "a" => "Rempah-rempah",                                         "b" => "Lada",                                           "c" => "Emas",                                               "d" => "Kain sutra",                                                     "k" => "b"],
    ["id" => 10, "t" => "Julukan apa yang diberikan kepada Aceh karena perannya sebagai pusat penyebaran Islam?",                                   "a" => "Kota Serambi",                                          "b" => "Serambi Mekkah",                                 "c" => "Negeri Islam",                                       "d" => "Kesultanan Islam",                                               "k" => "b"],
    ["id" => 11, "t" => "Sultan yang memerintah setelah Iskandar Muda dan dikenal fokus pada pembangunan hukum dan agama adalah?",                  "a" => "Sultan Ali Mughayat Syah",                              "b" => "Sultan Alaudin Riayat Syah",                     "c" => "Sultan Iskandar Thani",                              "d" => "Sultan Muhammad Daud Syah",                                      "k" => "c"],
    ["id" => 12, "t" => "Kitab Bustanussalatin yang menjadi karya sastra peninggalan Aceh dikarang oleh?",                                          "a" => "Hamzah Fansuri",                                        "b" => "Syamsuddin as-Sumatrani",                        "c" => "Nuruddin ar-Raniri",                                 "d" => "Abdurrauf as-Singkili",                                          "k" => "c"],
    ["id" => 13, "t" => "Taman Sari Gunongan dibangun oleh Sultan Iskandar Muda untuk?",                                                            "a" => "Tempat peristirahatan prajurit",                        "b" => "Upacara kenegaraan",                             "c" => "Permaisuri Putroe Phang",                            "d" => "Benteng pertahanan",                                             "k" => "c"],
    ["id" => 14, "t" => "Mata uang emas yang digunakan pada masa Kesultanan Aceh disebut?",                                                         "a" => "Dinar",                                                 "b" => "Ringgit",                                        "c" => "Dirham",                                             "d" => "Rupiah",                                                         "k" => "c"],
    ["id" => 15, "t" => "Meriam pemberian Inggris yang terkenal sebagai peninggalan Kesultanan Aceh bernama?",                                       "a" => "Meriam Si Jagur",                                       "b" => "Meriam Ki Amuk",                                 "c" => "Meriam Raja James",                                  "d" => "Meriam Banten",                                                  "k" => "c"],
    ["id" => 16, "t" => "Siapa Sultan terakhir Kesultanan Aceh yang menyerah kepada Belanda pada tahun 1903?",                                      "a" => "Sultan Iskandar Thani",                                 "b" => "Sultan Alaudin Riayat Syah",                     "c" => "Sultan Mahmud Syah",                                 "d" => "Sultan Muhammad Daud Syah",                                      "k" => "d"],
    ["id" => 17, "t" => "Pada tahun berapakah Kesultanan Aceh secara resmi jatuh ke tangan Belanda setelah Perang Aceh?",                           "a" => "1873",                                                  "b" => "1896",                                           "c" => "1900",                                               "d" => "1903",                                                           "k" => "d"],
    ["id" => 18, "t" => "Masjid Raya Baiturrahman merupakan peninggalan Kesultanan Aceh yang menjadi simbol?",                                      "a" => "Kekuatan militer",                                      "b" => "Kemakmuran ekonomi",                             "c" => "Keindahan arsitektur",                               "d" => "Kekuatan dan religiusitas rakyat Aceh",                          "k" => "d"],
    ["id" => 19, "t" => "Sultan Aceh yang menjalin hubungan diplomatik dengan Kesultanan Utsmaniyah (Turki) adalah?",                               "a" => "Sultan Ali Mughayat Syah",                              "b" => "Sultan Iskandar Muda",                           "c" => "Sultan Iskandar Thani",                              "d" => "Sultan Alaudin Riayat Syah al-Kahar",                            "k" => "d"],
    ["id" => 20, "t" => "Faktor eksternal yang mempercepat kemunduran Kesultanan Aceh adalah?",                                                     "a" => "Serangan Portugis",                                     "b" => "Pemberontakan rakyat",                           "c" => "Bencana alam",                                       "d" => "Munculnya VOC Belanda yang ingin memonopoli perdagangan",        "k" => "d"],
];

shuffle($soal_asli);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kuis Interaktif — AcehEdu Premium</title>
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
        --danger:      #ef4444;
        --danger-dim:  rgba(239,68,68,0.15);
        --danger-glow: rgba(239,68,68,0.4);
        --correct:     #4ade80;
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
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
        width: 100%; max-width: 680px;
        padding: 36px 20px 80px;
        display: flex; flex-direction: column; align-items: center;
    }

    /* ============================================
       MISSION FLOW
    ============================================ */
    .mission-flow {
        display: flex; align-items: center; justify-content: center;
        gap: 0; margin-bottom: 36px; width: 100%;
    }
    .flow-step { display: flex; flex-direction: column; align-items: center; gap: 8px; z-index: 1; }
    .flow-dot {
        width: 38px; height: 38px; border-radius: 50%;
        border: 1px solid var(--border); background: var(--ink-2);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-size: 0.7rem; color: var(--text-dim);
        transition: all 0.4s;
    }
    .flow-dot.done { background: var(--gold-dim); border-color: var(--gold); color: var(--gold); }
    .flow-dot.done::after { content: '✓'; font-size: 0.8rem; color: var(--gold); }
    .flow-dot.active {
        background: var(--gold); border-color: var(--gold-light); color: var(--ink);
        box-shadow: 0 0 0 6px rgba(201,168,76,0.15), 0 0 25px var(--gold-glow);
        animation: dotPulse 2s infinite;
    }
    .flow-dot.locked { opacity: 0.28; }
    @keyframes dotPulse {
        0%,100% { box-shadow: 0 0 0 4px rgba(201,168,76,0.15), 0 0 20px var(--gold-glow); }
        50%      { box-shadow: 0 0 0 10px rgba(201,168,76,0.05), 0 0 35px var(--gold-glow); }
    }
    .flow-label {
        font-family: var(--font-display); font-size: 0.6rem;
        letter-spacing: 1.5px; text-transform: uppercase;
        color: var(--text-dim); white-space: nowrap;
    }
    .flow-step.done  .flow-label { color: var(--gold); opacity: 0.7; }
    .flow-step.active .flow-label { color: var(--gold); }
    .flow-line {
        height: 1px; width: 55px;
        background: linear-gradient(90deg, var(--gold), var(--border));
        margin-bottom: 22px; opacity: 0.4; flex-shrink: 0;
    }
    .flow-line.done { background: var(--gold); opacity: 0.6; }

    /* ============================================
       QUIZ CARD
    ============================================ */
    .quiz-card {
        background: linear-gradient(145deg, rgba(20,18,14,0.92), rgba(2,6,23,0.97));
        border: 1px solid var(--border); border-radius: 28px;
        padding: 40px 36px;
        width: 100%; position: relative; overflow: hidden;
        box-shadow: 0 30px 60px -20px rgba(0,0,0,0.85);
        transition: box-shadow 0.4s;
    }
    .quiz-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, var(--gold), transparent);
        opacity: 0.5;
    }
    .quiz-card::after {
        content: ''; position: absolute; top: 12px; right: 12px;
        width: 28px; height: 28px;
        border-top: 1px solid var(--border); border-right: 1px solid var(--border);
        border-radius: 0 6px 0 0; opacity: 0.4;
    }

    /* ============================================
       ERROR MESSAGE
    ============================================ */
    .error-msg {
        background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.3);
        border-radius: 14px; padding: 14px 20px; margin-bottom: 22px;
        color: #fca5a5; font-size: 0.88rem; font-weight: 500;
        display: flex; align-items: center; gap: 10px;
        animation: slideDown 0.3s ease;
    }
    .error-msg-label {
        font-family: var(--font-display); font-size: 0.6rem;
        letter-spacing: 2px; color: var(--danger); text-transform: uppercase;
        margin-right: 4px; flex-shrink: 0;
    }
    @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }

    /* ============================================
       STEP 1 — IDENTITAS
    ============================================ */
    .step1-emblem {
        width: 80px; height: 80px; margin: 0 auto 24px;
        position: relative; display: flex; align-items: center; justify-content: center;
    }
    .emblem-ring {
        position: absolute; inset: 0; border-radius: 50%;
        border: 1px dashed rgba(201,168,76,0.3);
        animation: spinRing 20s linear infinite;
    }
    .emblem-ring::before {
        content: ''; position: absolute; top: -3px; left: 50%;
        width: 6px; height: 6px; background: var(--gold); border-radius: 50%;
        transform: translateX(-50%); box-shadow: 0 0 8px var(--gold-glow);
    }
    .emblem-ring-2 {
        position: absolute; inset: 8px; border-radius: 50%;
        border: 1px solid rgba(201,168,76,0.12);
        animation: spinRing 14s linear infinite reverse;
    }
    .emblem-core {
        width: 52px; height: 52px; border-radius: 50%;
        background: var(--gold-dim); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        color: var(--gold); position: relative; z-index: 1;
    }
    @keyframes spinRing { to { transform: rotate(360deg); } }

    .step1-title {
        font-family: var(--font-display); font-size: 1.5rem; font-weight: 900;
        text-align: center; margin-bottom: 6px;
        background: linear-gradient(180deg, #f5edd8, var(--gold));
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .step1-sub {
        text-align: center; font-size: 0.85rem; color: var(--text-dim);
        margin-bottom: 32px; letter-spacing: 0.3px;
    }

    /* Input fields */
    .input-group-custom { margin-bottom: 18px; }
    .input-field-wrap { position: relative; }
    .input-icon {
        position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
        color: var(--text-dim); transition: color 0.3s; z-index: 2; pointer-events: none;
    }
    .input-label {
        font-family: var(--font-display); font-size: 0.6rem;
        letter-spacing: 3px; color: var(--text-dim); text-transform: uppercase;
        margin-bottom: 8px; display: block; padding-left: 2px;
    }
    .input-custom {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        padding: 16px 18px 16px 50px;
        border-radius: 14px;
        color: var(--text) !important;
        font-family: var(--font-body); font-weight: 500; font-size: 0.97rem;
        width: 100%;
        transition: all 0.3s;
    }
    .input-custom:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(201,168,76,0.12), 0 0 20px rgba(201,168,76,0.08);
        background: var(--gold-dim);
        outline: none;
    }
    .input-custom:focus ~ .input-icon { color: var(--gold); }
    .input-custom::placeholder { color: rgba(255,255,255,0.2); }

    /* Tombol Mulai */
    .btn-mulai {
        width: 100%; margin-top: 8px; padding: 18px;
        border-radius: 100px; border: 1px solid var(--gold);
        background: transparent; color: var(--gold);
        font-family: var(--font-display); font-size: 0.82rem;
        letter-spacing: 3px; text-transform: uppercase;
        cursor: pointer; position: relative; overflow: hidden;
        display: flex; align-items: center; justify-content: center; gap: 12px;
        transition: color 0.4s, box-shadow 0.4s;
    }
    .btn-mulai::before {
        content: ''; position: absolute; inset: 0;
        background: var(--gold);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.45s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-mulai:hover { color: var(--ink); box-shadow: 0 0 40px var(--gold-glow); }
    .btn-mulai:hover::before { transform: scaleX(1); }
    .btn-mulai span, .btn-mulai svg { position: relative; z-index: 1; transition: color 0.4s; }
    .btn-mulai:hover svg { color: var(--ink); }

    /* Gold spinner */
    .gold-spinner {
        width: 28px; height: 28px; border-radius: 50%;
        border: 2px solid var(--border);
        border-top-color: var(--gold);
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ============================================
       STEP 2 — KUIS
    ============================================ */

    /* Header row */
    .quiz-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 18px;
    }
    .q-badge {
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 3px; text-transform: uppercase;
        color: var(--text-dim); padding: 8px 18px;
        border: 1px solid var(--border); border-radius: 100px;
        background: rgba(255,255,255,0.02);
    }
    .timer-pill {
        display: flex; align-items: center; gap: 8px;
        background: var(--gold-dim); border: 1px solid var(--border);
        padding: 8px 16px; border-radius: 100px;
        font-family: var(--font-display); font-weight: 700;
        font-size: 0.9rem; color: var(--gold);
        transition: all 0.3s;
    }
    .timer-pill.danger {
        background: var(--danger-dim); border-color: rgba(239,68,68,0.4);
        color: var(--danger);
        animation: pulseDanger 1s infinite;
    }
    @keyframes pulseDanger {
        0%,100% { box-shadow: 0 0 0 0 var(--danger-glow); }
        50%      { box-shadow: 0 0 0 8px rgba(239,68,68,0); }
    }

    /* Dot navigator */
    .dot-nav {
        display: flex; flex-wrap: wrap; gap: 7px;
        justify-content: center; margin-bottom: 20px;
    }
    .nav-dot {
        width: 26px; height: 26px; border-radius: 50%;
        border: 1px solid var(--border);
        background: rgba(255,255,255,0.02);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-size: 0.52rem;
        color: var(--text-dim); transition: all 0.35s;
        cursor: default;
    }
    .nav-dot.answered {
        background: var(--gold-dim); border-color: var(--gold);
        color: var(--gold);
    }
    .nav-dot.current {
        background: var(--gold); border-color: var(--gold-light);
        color: var(--ink); font-weight: 700;
        box-shadow: 0 0 12px var(--gold-glow);
        animation: dotPulse 1.8s infinite;
    }

    /* Progress bar */
    .progress-track {
        width: 100%; height: 4px;
        background: rgba(255,255,255,0.04); border-radius: 100px;
        overflow: hidden; margin-bottom: 28px;
        border: 1px solid rgba(255,255,255,0.03);
    }
    .progress-fill {
        height: 100%; width: 0%;
        background: linear-gradient(90deg, var(--gold), var(--gold-light));
        border-radius: 100px;
        transition: width 0.5s cubic-bezier(0.4,0,0.2,1);
        box-shadow: 0 0 10px var(--gold-glow);
    }

    /* Question text */
    .q-text {
        font-family: var(--font-display); font-size: 1.1rem; font-weight: 700;
        line-height: 1.65; color: var(--text); margin-bottom: 26px;
        letter-spacing: 0.2px;
    }

    /* Option buttons */
    .opt-container { display: flex; flex-direction: column; gap: 12px; }
    .option-btn {
        display: flex; align-items: center; gap: 16px;
        background: rgba(255,255,255,0.02); border: 1px solid var(--border);
        padding: 15px 18px; border-radius: 16px;
        cursor: pointer; transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        position: relative; overflow: hidden;
        text-align: left;
    }
    .option-btn::before {
        content: ''; position: absolute; inset: 0;
        background: var(--gold-dim);
        opacity: 0; transition: opacity 0.3s;
    }
    .option-btn:hover { border-color: rgba(201,168,76,0.35); transform: translateY(-2px); }
    .option-btn:hover::before { opacity: 1; }

    .opt-letter {
        width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
        background: rgba(255,255,255,0.04); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-size: 0.82rem; font-weight: 700;
        color: var(--text-dim); transition: all 0.3s;
        position: relative; z-index: 1;
    }
    .opt-text {
        flex-grow: 1; font-size: 0.93rem; font-weight: 500;
        color: var(--text-dim); transition: all 0.3s; line-height: 1.5;
        position: relative; z-index: 1;
    }
    .opt-radio {
        width: 20px; height: 20px; border: 1.5px solid var(--border);
        border-radius: 50%; flex-shrink: 0; position: relative;
        transition: all 0.3s; z-index: 1;
    }

    /* Selected state */
    .option-btn.selected { border-color: var(--gold); box-shadow: 0 0 0 1px rgba(201,168,76,0.3), 0 8px 24px -8px var(--gold-glow); }
    .option-btn.selected::before { opacity: 1; }
    .option-btn.selected .opt-letter {
        background: var(--gold); border-color: var(--gold-light);
        color: var(--ink); box-shadow: 0 0 12px var(--gold-glow);
    }
    .option-btn.selected .opt-text { color: var(--text); font-weight: 600; }
    .option-btn.selected .opt-radio {
        background: var(--gold); border-color: var(--gold);
        box-shadow: 0 0 8px var(--gold-glow);
    }
    .option-btn.selected .opt-radio::after {
        content: ''; position: absolute; left: 6px; top: 2px;
        width: 5px; height: 10px;
        border: solid var(--ink); border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        animation: checkPop 0.3s cubic-bezier(0.4,0,0.2,1) forwards;
    }
    @keyframes checkPop {
        0%   { opacity:0; transform: rotate(45deg) scale(0.4); }
        100% { opacity:1; transform: rotate(45deg) scale(1); }
    }

    /* Next button */
    .btn-next {
        width: 100%; margin-top: 24px; padding: 17px;
        border-radius: 100px; border: 1px solid var(--gold);
        background: transparent; color: var(--gold);
        font-family: var(--font-display); font-size: 0.78rem;
        letter-spacing: 3px; text-transform: uppercase;
        cursor: pointer; position: relative; overflow: hidden;
        display: none; opacity: 0; transform: translateY(10px);
        transition: color 0.4s;
    }
    .btn-next::before {
        content: ''; position: absolute; inset: 0;
        background: var(--gold);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-next .btn-inner { position: relative; z-index: 1; display: flex; align-items: center; justify-content: center; gap: 10px; transition: color 0.4s; }
    .btn-next.show { display: block; animation: slideUpFade 0.4s ease forwards; }
    .btn-next:hover { color: var(--ink); box-shadow: 0 0 30px var(--gold-glow); }
    .btn-next:hover::before { transform: scaleX(1); }
    .btn-next:hover .btn-inner { color: var(--ink); }
    @keyframes slideUpFade { to { opacity:1; transform:translateY(0); } }

    /* ============================================
       TRANSITIONS
    ============================================ */
    .hidden { display: none !important; }
    .fade-enter { animation: fadeEnter 0.4s cubic-bezier(0.4,0,0.2,1) forwards; }
    .fade-exit  { animation: fadeExit  0.3s cubic-bezier(0.4,0,0.2,1) forwards; }
    @keyframes fadeEnter { from { opacity:0; transform:translateX(28px); } to { opacity:1; transform:translateX(0); } }
    @keyframes fadeExit  { from { opacity:1; transform:translateX(0); }   to { opacity:0; transform:translateX(-28px); } }

    /* ============================================
       ★ ANTI-NYONTEK — USER SELECT LOCK
    ============================================ */
    #step-2,
    .q-text,
    .opt-container,
    .option-btn,
    .opt-text,
    .opt-letter,
    .quiz-header,
    .dot-nav {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
        -webkit-touch-callout: none !important;
    }

    /* ============================================
       ★ WATERMARK NAMA SISWA
    ============================================ */
    .quiz-watermark {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 0;
        overflow: hidden;
        border-radius: inherit;
    }
    .quiz-watermark-text {
        font-family: var(--font-display);
        font-size: clamp(1rem, 3vw, 1.6rem);
        font-weight: 700;
        color: rgba(201,168,76,0.045);
        letter-spacing: 4px;
        text-transform: uppercase;
        transform: rotate(-25deg);
        white-space: nowrap;
        pointer-events: none;
        user-select: none;
        text-align: center;
        line-height: 2.8;
        width: 200%;
        word-spacing: 60px;
    }

    /* ============================================
       ★ WARNING TOAST — TAB SWITCH
    ============================================ */
    .cheat-toast-container {
        position: fixed;
        top: 88px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        pointer-events: none;
    }
    .cheat-toast {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 22px;
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.5);
        border-radius: 100px;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 8px 32px -8px rgba(239,68,68,0.4), 0 0 0 1px rgba(239,68,68,0.1);
        color: #fca5a5;
        font-size: 0.85rem;
        font-weight: 500;
        pointer-events: none;
        white-space: nowrap;
        animation: toastIn 0.4s cubic-bezier(0.16,1,0.3,1) forwards;
        max-width: 90vw;
    }
    .cheat-toast.hiding {
        animation: toastOut 0.3s ease forwards;
    }
    .cheat-toast-icon {
        flex-shrink: 0;
        width: 28px; height: 28px;
        border-radius: 50%;
        background: rgba(239,68,68,0.2);
        display: flex; align-items: center; justify-content: center;
        color: #ef4444;
    }
    .cheat-toast-counter {
        font-family: var(--font-display);
        font-size: 0.68rem;
        letter-spacing: 1px;
        color: rgba(239,68,68,0.7);
        margin-left: 4px;
    }
    @keyframes toastIn {
        from { opacity: 0; transform: translateY(-16px) scale(0.92); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes toastOut {
        from { opacity: 1; transform: translateY(0) scale(1); }
        to   { opacity: 0; transform: translateY(-8px) scale(0.95); }
    }

    /* Flash overlay saat tab kembali */
    .tab-return-flash {
        position: fixed;
        inset: 0;
        background: rgba(239,68,68,0.08);
        z-index: 9998;
        pointer-events: none;
        animation: flashFade 0.6s ease forwards;
    }
    @keyframes flashFade {
        0%   { opacity: 1; }
        100% { opacity: 0; }
    }

    /* ============================================
       MOBILE
    ============================================ */
    @media (max-width: 600px) {
        .main-wrap { padding: 24px 14px 80px; }
        .quiz-card { padding: 28px 20px; border-radius: 22px; }
        .flow-line { width: 28px; }
        .flow-label { font-size: 0.5rem; }
        .q-text { font-size: 1rem; }
        .opt-text { font-size: 0.88rem; }
        .option-btn { padding: 13px 14px; gap: 12px; }
        .opt-letter { width: 34px; height: 34px; font-size: 0.75rem; }
        .nav-dot { width: 22px; height: 22px; font-size: 0.46rem; }
    }
    </style>
</head>
<body>

<canvas id="starCanvas"></canvas>

<!-- ★ TOAST CONTAINER ANTI-NYONTEK -->
<div class="cheat-toast-container" id="cheatToastContainer"></div>

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
        <a class="sidebar-link active" href="kuis.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Mulai Kuis
        </a>
        <a class="sidebar-link" href="hasil.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Hasil & Review
        </a>
    </div>
</div>

<!-- MAIN -->
<div class="main-wrap">

    <!-- MISSION FLOW — hanya tampil di step 1, disembunyikan JS saat kuis mulai -->
    <div class="mission-flow" id="missionFlow">
        <div class="flow-step done">
            <div class="flow-dot done"></div>
            <span class="flow-label">Materi</span>
        </div>
        <div class="flow-line done"></div>
        <div class="flow-step done">
            <div class="flow-dot done"></div>
            <span class="flow-label">Peraturan</span>
        </div>
        <div class="flow-line done"></div>
        <div class="flow-step active">
            <div class="flow-dot active">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span class="flow-label">Kuis</span>
        </div>
        <div class="flow-line"></div>
        <div class="flow-step">
            <div class="flow-dot locked">🔒</div>
            <span class="flow-label">Hasil</span>
        </div>
    </div>

    <!-- QUIZ CARD -->
    <div class="quiz-card" id="quizCard">
        <!-- ★ WATERMARK NAMA SISWA — diisi JS setelah identitas diisi -->
        <div class="quiz-watermark" id="quizWatermark" aria-hidden="true">
            <div class="quiz-watermark-text" id="watermarkText">AcehEdu Premium</div>
        </div>

        <div id="error-message" class="error-msg hidden">
            <span class="error-msg-label">Peringatan</span>
            <span id="error-text"></span>
        </div>

        <form id="quizForm" action="proses_kuis.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="tab_switch_count" id="tabSwitchInput" value="0">

            <!-- ══ STEP 1: IDENTITAS ══ -->
            <div id="step-1">
                <div class="step1-emblem">
                    <div class="emblem-ring"></div>
                    <div class="emblem-ring-2"></div>
                    <div class="emblem-core">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                </div>

                <h2 class="step1-title">Identitas Peserta</h2>
                <p class="step1-sub">AcehEdu Premium · Pastikan data kamu benar sebelum mulai</p>

                <div class="input-group-custom">
                    <label class="input-label" for="no_absen">Nomor Absen</label>
                    <div class="input-field-wrap">
                        <input type="number" name="no_absen" id="no_absen"
                               class="input-custom" placeholder="Contoh: 12"
                               required autocomplete="off" min="1">
                        <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                </div>

                <div class="input-group-custom">
                    <label class="input-label" for="nama_murid">Nama Lengkap</label>
                    <div class="input-field-wrap">
                        <input type="text" name="nama_murid" id="nama_murid"
                               class="input-custom" placeholder="Nama kamu"
                               required autocomplete="off">
                        <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                </div>

                <button type="button" class="btn-mulai" onclick="cekAbsen()" id="btn-mulai">
                    <span>Mulai Kuis Sekarang</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>

                <div id="loading-spinner" class="hidden" style="display:flex;justify-content:center;align-items:center;margin-top:20px;gap:12px;">
                    <div class="gold-spinner"></div>
                    <span style="font-family:var(--font-display);font-size:0.7rem;letter-spacing:3px;color:var(--text-dim);text-transform:uppercase;">Memuat Kuis...</span>
                </div>
            </div>

            <!-- ══ STEP 2: KUIS ══ -->
            <div id="step-2" class="hidden">

                <!-- Header -->
                <div class="quiz-header">
                    <div id="q-count" class="q-badge">Soal 1 / 20</div>
                    <div id="timer-pill" class="timer-pill">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span id="timer-text">60s</span>
                    </div>
                </div>

                <!-- Dot navigator -->
                <div class="dot-nav" id="dotNav">
                    <?php for($i=1;$i<=20;$i++): ?>
                    <div class="nav-dot <?= $i===1?'current':'' ?>" id="dot-<?= $i ?>"><?= $i ?></div>
                    <?php endfor; ?>
                </div>

                <!-- Progress bar -->
                <div class="progress-track">
                    <div class="progress-fill" id="overall-fill"></div>
                </div>

                <!-- Question + Options -->
                <div id="quiz-content">
                    <div id="q-text" class="q-text">Memuat pertanyaan...</div>
                    <div id="opt-container" class="opt-container"></div>
                </div>

                <!-- Next button -->
                <button type="button" id="btn-next" class="btn-next" onclick="nextSoal()">
                    <div class="btn-inner">
                        <span id="btn-next-text">Simpan & Lanjut</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </div>
                </button>

                <?php for($i=1;$i<=20;$i++): ?>
                    <input type="hidden" name="jawaban_<?= $i ?>" id="ans_<?= $i ?>" value="-">
                <?php endfor; ?>
                <input type="hidden" name="total_soal" value="20">
            </div>

        </form>
    </div><!-- /quiz-card -->
</div><!-- /main-wrap -->

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
   QUIZ LOGIC — dipertahankan penuh, hanya UI yang diupgrade
============================================================ */
const soal        = <?= json_encode($soal_asli) ?>;
let idx           = 0;
let timerInterval;
const waktuPerSoal = 60;

function showError(msg) {
    const errDiv  = document.getElementById('error-message');
    const errText = document.getElementById('error-text');
    errText.textContent = msg;
    errDiv.classList.remove('hidden');
    setTimeout(() => errDiv.classList.add('hidden'), 5000);
}

function cekAbsen() {
    const noAbsen = document.getElementById('no_absen').value.trim();
    const nama    = document.getElementById('nama_murid').value.trim();
    if (!noAbsen || !nama) { showError('Harap isi Nomor Absen dan Nama Lengkap!'); return; }

    document.getElementById('btn-mulai').classList.add('hidden');
    const spinner = document.getElementById('loading-spinner');
    spinner.classList.remove('hidden');
    spinner.style.display = 'flex';

    setTimeout(() => {
        spinner.classList.add('hidden');
        sessionStorage.setItem('no_absen',   noAbsen);
        sessionStorage.setItem('nama_murid', nama);
        mulaiKuisUI();
    }, 900);
}

function mulaiKuisUI() {
    // ★ Aktifkan watermark nama siswa
    const nama = sessionStorage.getItem('nama_murid') || '';
    if (window._antiNyontek) {
        window._antiNyontek.setWatermark(nama);
        window._antiNyontek.activateKuis();
    }

    // Sembunyikan mission flow saat kuis dimulai
    const mf = document.getElementById('missionFlow');
    mf.style.transition = 'opacity 0.4s, transform 0.4s';
    mf.style.opacity    = '0';
    mf.style.transform  = 'translateY(-10px)';
    setTimeout(()=> mf.classList.add('hidden'), 400);

    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');

    step1.classList.add('fade-exit');
    setTimeout(() => {
        step1.classList.add('hidden');
        step2.classList.remove('hidden');
        step2.classList.add('fade-enter');
        renderSoal();
    }, 300);
}

function updateDots() {
    for(let i=1; i<=20; i++){
        const dot = document.getElementById('dot-'+i);
        if(!dot) continue;
        dot.classList.remove('current','answered');
        if(i === idx+1)         dot.classList.add('current');
        else if(i <= idx)       dot.classList.add('answered');
    }
}

function renderSoal() {
    if (idx >= soal.length) {
        document.getElementById('quizForm').submit();
        return;
    }

    const btnNext = document.getElementById('btn-next');
    btnNext.classList.remove('show');

    // Progress
    const pct = (idx / soal.length) * 100;
    document.getElementById('overall-fill').style.width = pct + '%';
    document.getElementById('q-count').textContent = `Soal ${idx+1} / ${soal.length}`;

    // Dots
    updateDots();

    // Timer
    clearInterval(timerInterval);
    let sisa = waktuPerSoal;
    const timerPill = document.getElementById('timer-pill');
    const timerText = document.getElementById('timer-text');
    timerPill.classList.remove('danger');
    timerText.textContent = sisa + 's';

    timerInterval = setInterval(() => {
        sisa--;
        timerText.textContent = sisa + 's';
        if (sisa <= 5) timerPill.classList.add('danger');
        if (sisa <= 0) { clearInterval(timerInterval); nextSoal(); }
    }, 1000);

    // Render soal
    const s           = soal[idx];
    const quizContent = document.getElementById('quiz-content');
    quizContent.classList.remove('fade-enter');
    void quizContent.offsetWidth;
    quizContent.classList.add('fade-enter');

    document.getElementById('q-text').textContent = s.t;

    const labels = ['A','B','C','D'];
    let html = '';
    ['a','b','c','d'].forEach((opt, index) => {
        html += `
        <div class="option-btn" onclick="pilih(this, ${s.id}, '${opt}')">
            <div class="opt-letter">${labels[index]}</div>
            <div class="opt-text">${s[opt]}</div>
            <div class="opt-radio"></div>
        </div>`;
    });
    document.getElementById('opt-container').innerHTML = html;
}

function pilih(btn, id, val) {
    document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('ans_' + id).value = val;

    const btnNext = document.getElementById('btn-next');
    if(!btnNext.classList.contains('show')) btnNext.classList.add('show');
}

function nextSoal() {
    // Tandai dot sebagai answered sebelum pindah
    const dot = document.getElementById('dot-'+(idx+1));
    if(dot){ dot.classList.remove('current'); dot.classList.add('answered'); }

    const quizContent = document.getElementById('quiz-content');
    quizContent.classList.remove('fade-enter');
    quizContent.classList.add('fade-exit');
    document.getElementById('btn-next').classList.remove('show');

    setTimeout(() => {
        idx++;
        quizContent.classList.remove('fade-exit');
        renderSoal();
    }, 300);
}

document.getElementById('quizForm').addEventListener('submit', function(e) {
    const noAbsen = sessionStorage.getItem('no_absen');
    const nama    = sessionStorage.getItem('nama_murid');
    if (!noAbsen || !nama) {
        e.preventDefault();
        showError('Data sesi hilang, mohon ulangi dari awal.');
        return;
    }
    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="no_absen"   value="${noAbsen}">`);
    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="nama_murid" value="${nama}">`);

    // Loading state pada tombol
    const btnNext     = document.getElementById('btn-next');
    const btnInner    = btnNext.querySelector('.btn-inner');
    btnInner.innerHTML= `<div class="gold-spinner"></div><span style="font-family:var(--font-display);font-size:0.7rem;letter-spacing:2px;">Menghitung Skor...</span>`;
    btnNext.style.pointerEvents = 'none';
    btnNext.style.opacity = '0.75';
});

// Enter key support di form identitas
document.addEventListener('keydown', e => {
    if(e.key === 'Enter') {
        const step1 = document.getElementById('step-1');
        if(!step1.classList.contains('hidden')) cekAbsen();
    }
});

/* ============================================================
   ★ ANTI-NYONTEK — SEMUA LAPISAN
============================================================ */
(function() {

    /* ── Lapis 1: Blokir context menu + long-press ── */
    document.addEventListener('contextmenu', e => {
        const quizCard = document.getElementById('quizCard');
        if (quizCard && quizCard.contains(e.target)) {
            e.preventDefault();
        }
    });

    /* ── Lapis 2: Auto-clear selection yang lolos ── */
    document.addEventListener('selectionchange', () => {
        const sel = window.getSelection();
        if (!sel || sel.isCollapsed) return;
        const node = sel.anchorNode;
        if (!node) return;
        const quizCard = document.getElementById('quizCard');
        const step2 = document.getElementById('step-2');
        if (quizCard && quizCard.contains(node) && step2 && !step2.classList.contains('hidden')) {
            sel.removeAllRanges();
        }
    });

    /* ── Lapis 3: Blokir copy/cut keyboard shortcut ── */
    document.addEventListener('keydown', e => {
        const step2 = document.getElementById('step-2');
        if (!step2 || step2.classList.contains('hidden')) return;
        if ((e.ctrlKey || e.metaKey) && ['c','x','a','u'].includes(e.key.toLowerCase())) {
            e.preventDefault();
        }
    });

    /* ── Lapis 4: Watermark nama siswa ── */
    function setWatermark(nama) {
        const el = document.getElementById('watermarkText');
        if (!el || !nama) return;
        // Repeat name across watermark
        const repeated = Array(6).fill(nama.toUpperCase()).join('   ·   ');
        el.textContent = repeated;
    }

    /* ── Lapis 5: Tab/window switch detection ── */
    let tabSwitchCount = 0;
    let kuisAktif = false; // hanya hitung saat kuis berjalan

    function showCheatToast(count) {
        const container = document.getElementById('cheatToastContainer');
        if (!container) return;

        // Flash overlay
        const flash = document.createElement('div');
        flash.className = 'tab-return-flash';
        document.body.appendChild(flash);
        setTimeout(() => flash.remove(), 700);

        const messages = [
            'Jangan buka aplikasi lain saat kuis!',
            'Tetap fokus — kuis masih berjalan.',
            'Kecurangan tercatat oleh sistem.',
            'Timer terus berjalan saat kamu pergi.',
            'Integritas adalah nilai terbaik.',
        ];
        const msg = messages[Math.min(count - 1, messages.length - 1)];

        const toast = document.createElement('div');
        toast.className = 'cheat-toast';
        toast.innerHTML = `
            <div class="cheat-toast-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <span>${msg}</span>
            <span class="cheat-toast-counter">#${count}x</span>`;

        container.appendChild(toast);

        // Auto-hide after 4s
        setTimeout(() => {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 350);
        }, 4000);
    }

    document.addEventListener('visibilitychange', () => {
        if (!kuisAktif) return;
        if (document.hidden) return; // saat pergi, tidak hitung dulu

        // Saat kembali ke tab
        tabSwitchCount++;
        document.getElementById('tabSwitchInput').value = tabSwitchCount;
        showCheatToast(tabSwitchCount);
    });

    window.addEventListener('blur', () => {
        // Window blur — misal klik ke app lain di desktop
        if (!kuisAktif) return;
        // visibilitychange sudah handle ini di mobile
        // blur untuk desktop fallback
    });

    /* ── Expose fungsi ke global scope ── */
    window._antiNyontek = {
        setWatermark,
        activateKuis: () => { kuisAktif = true; },
    };

})();
</script>
</body>
</html>
