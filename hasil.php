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
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data) { header("Location: kuis.php"); exit; }

    $jawaban = [];
    for ($i = 1; $i <= 20; $i++) $jawaban[$i] = $data["jawaban_$i"] ?? '-';

    $waktu = new DateTime($data['waktu_submit']);
    $waktu_formatted = $waktu->format('d M Y | H:i');
} catch (PDOException $e) { die("Gagal mengambil data."); }

$kunci = [
    1=>'a',2=>'a',3=>'a',4=>'a',5=>'a',
    6=>'b',7=>'b',8=>'b',9=>'b',10=>'b',
    11=>'c',12=>'c',13=>'c',14=>'c',15=>'c',
    16=>'d',17=>'d',18=>'d',19=>'d',20=>'d'
];

$benar = 0; $salah = 0; $tidak_dijawab = 0;
$detail = [];
for ($i = 1; $i <= 20; $i++) {
    $jawab = $jawaban[$i];
    $kunc  = $kunci[$i];
    if ($jawab === '-')         { $tidak_dijawab++; $status = 'kosong'; }
    elseif ($jawab === $kunc)   { $benar++;         $status = 'benar';  }
    else                        { $salah++;          $status = 'salah';  }
    $detail[$i] = ['jawab' => strtoupper($jawab), 'kunci' => strtoupper($kunc), 'status' => $status];
}

$total_soal = 20;
$skor = round(($benar / $total_soal) * 100);
$pct_benar  = round($benar  / $total_soal * 100);
$pct_salah  = round($salah  / $total_soal * 100);
$pct_kosong = round($tidak_dijawab / $total_soal * 100);

// ── TIER SYSTEM ──────────────────────────────────────────────
if ($skor >= 90) {
    $tier_icon  = '👑';
    $tier_label = 'Sultan Ahli Sejarah';
    $tier_color = '#f0d080';
    $tier_glow  = 'rgba(240,208,128,0.45)';
    $tier_dim   = 'rgba(240,208,128,0.12)';
    $tier_border= 'rgba(240,208,128,0.5)';
    $pesan      = 'Luar biasa. Pemahaman sejarahmu mencapai puncak kejayaan.';
    $confetti_tier = 'sultan';
} elseif ($skor >= 75) {
    $tier_icon  = '🎖️';
    $tier_label = 'Panglima Perang';
    $tier_color = '#c9a84c';
    $tier_glow  = 'rgba(201,168,76,0.45)';
    $tier_dim   = 'rgba(201,168,76,0.12)';
    $tier_border= 'rgba(201,168,76,0.5)';
    $pesan      = 'Kerja bagus. Sedikit lagi menuju puncak kesultanan.';
    $confetti_tier = 'panglima';
} else {
    $tier_icon  = '⚔️';
    $tier_label = 'Prajurit Perintis';
    $tier_color = '#94a3b8';
    $tier_glow  = 'rgba(148,163,184,0.35)';
    $tier_dim   = 'rgba(148,163,184,0.08)';
    $tier_border= 'rgba(148,163,184,0.35)';
    $pesan      = 'Jangan menyerah. Pelajari ulasannya dan bangkit kembali.';
    $confetti_tier = 'none';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Hasil Evaluasi — AcehEdu Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
    /* ============================================
       ROOT — IDENTIK EKOSISTEM
    ============================================ */
    :root {
        --gold:       #c9a84c;
        --gold-light: #f0d080;
        --gold-dim:   rgba(201,168,76,0.12);
        --gold-glow:  rgba(201,168,76,0.45);
        --ink:        #020617;
        --ink-2:      #0f172a;
        --surface:    rgba(255,255,255,0.03);
        --border:     rgba(201,168,76,0.18);
        --text:       #e8e0d0;
        --text-dim:   #8a7f70;
        --correct:    #4ade80;
        --wrong:      #ef4444;
        --empty:      #64748b;

        /* Tier dinamis dari PHP */
        --tier:       <?= $tier_color ?>;
        --tier-glow:  <?= $tier_glow ?>;
        --tier-dim:   <?= $tier_dim ?>;
        --tier-border:<?= $tier_border ?>;

        --radius: 20px;
        --font-display: 'Cinzel', serif;
        --font-body:    'Inter', sans-serif;
    }

    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; overflow-x: hidden; }

    body {
        font-family: var(--font-body);
        background: var(--ink);
        color: var(--text);
        line-height: 1.75;
        overflow-x: hidden;
        padding-top: 72px;
        min-height: 100vh;
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
    .sidebar-link svg { flex-shrink: 0; }

    /* ============================================
       MAIN WRAP
    ============================================ */
    .main-wrap {
        position: relative; z-index: 1;
        max-width: 860px; margin: 0 auto;
        padding: 40px 20px 120px;
    }

    /* ============================================
       MISSION FLOW
    ============================================ */
    .mission-flow {
        display: flex; align-items: center; justify-content: center;
        gap: 0; margin-bottom: 44px;
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
        height: 1px; width: 60px;
        background: linear-gradient(90deg, var(--gold), var(--border));
        margin-bottom: 22px; opacity: 0.4; flex-shrink: 0;
    }
    .flow-line.done { background: var(--gold); opacity: 0.6; }

    /* ============================================
       SECTION CARD — IDENTIK INDEX V2
    ============================================ */
    .section-card {
        background: linear-gradient(145deg, rgba(20,18,14,0.9), rgba(2,6,23,0.95));
        border: 1px solid var(--border); border-radius: var(--radius);
        padding: 36px; margin-bottom: 24px;
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
        width: 28px; height: 28px;
        border-top: 1px solid var(--border); border-right: 1px solid var(--border);
        border-radius: 0 6px 0 0; opacity: 0.4;
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
       HERO — RANK BADGE
    ============================================ */
    .hero-section { text-align: center; padding: 10px 0 0; }

    .rank-badge {
        display: inline-flex; align-items: center; gap: 12px;
        background: var(--tier-dim); border: 1px solid var(--tier-border);
        padding: 12px 32px; border-radius: 100px;
        font-family: var(--font-display); font-size: 1rem; font-weight: 700;
        color: var(--tier); letter-spacing: 1px;
        box-shadow: 0 0 30px var(--tier-glow);
        position: relative; overflow: hidden;
        margin-bottom: 16px;
    }
    /* Shimmer */
    .rank-badge::after {
        content: ''; position: absolute; top: 0; left: -150%; width: 50%; height: 100%;
        background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
        transform: skewX(-25deg); animation: shimmer 3s infinite;
    }
    @keyframes shimmer { 0% { left: -150%; } 100% { left: 250%; } }

    .hero-title {
        font-family: var(--font-display);
        font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 900;
        background: linear-gradient(180deg, #f5edd8 0%, var(--tier) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text; line-height: 1.1; margin-bottom: 10px;
    }
    .hero-pesan {
        font-size: 0.92rem; color: var(--text-dim);
        max-width: 420px; margin: 0 auto;
    }

    /* ============================================
       INFO BAR
    ============================================ */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .info-item {
        background: rgba(255,255,255,0.02); border: 1px solid var(--border);
        border-radius: 14px; padding: 18px 20px;
        transition: all 0.3s;
    }
    .info-item:hover { border-color: rgba(201,168,76,0.35); background: var(--gold-dim); }
    .info-label {
        font-family: var(--font-display); font-size: 0.6rem;
        letter-spacing: 3px; color: var(--text-dim); text-transform: uppercase;
        margin-bottom: 8px;
    }
    .info-value {
        font-family: var(--font-display); font-size: 1rem;
        color: var(--text); font-weight: 700; letter-spacing: 0.5px;
    }

    /* ============================================
       SCORE CIRCLE — DOUBLE RING
    ============================================ */
    .score-section { text-align: center; padding: 10px 0; }

    .score-wrap {
        position: relative; display: inline-block;
        width: 220px; height: 220px;
        margin: 0 auto 36px;
    }

    /* Outer spinning ring — sama seperti emblem index v2 */
    .score-ring-outer {
        position: absolute; inset: -14px; border-radius: 50%;
        border: 1px dashed rgba(201,168,76,0.25);
        animation: spinRing 25s linear infinite;
    }
    .score-ring-outer::before {
        content: ''; position: absolute;
        top: -4px; left: 50%;
        width: 8px; height: 8px;
        background: var(--gold); border-radius: 50%;
        transform: translateX(-50%);
        box-shadow: 0 0 10px var(--gold-glow);
    }
    .score-ring-mid {
        position: absolute; inset: -24px; border-radius: 50%;
        border: 1px solid rgba(201,168,76,0.08);
        animation: spinRing 18s linear infinite reverse;
    }
    @keyframes spinRing { to { transform: rotate(360deg); } }

    .score-svg { transform: rotate(-90deg); width: 100%; height: 100%; }
    .score-bg   { fill: rgba(255,255,255,0.02); stroke: rgba(201,168,76,0.08); stroke-width: 10; }
    .score-fill {
        fill: none; stroke: var(--tier); stroke-width: 12; stroke-linecap: round;
        stroke-dasharray: 628; stroke-dashoffset: 628;
        transition: stroke-dashoffset 2.5s cubic-bezier(0.16, 1, 0.3, 1);
        filter: drop-shadow(0 0 8px var(--tier-glow));
    }
    .score-center {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        text-align: center;
    }
    .score-number {
        font-family: var(--font-display); font-size: 4.5rem; font-weight: 900;
        line-height: 1; color: var(--text);
        text-shadow: 0 0 30px var(--tier-glow);
    }
    .score-label {
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 4px; color: var(--tier); opacity: 0.85;
        text-transform: uppercase; margin-top: 4px;
    }

    /* Glow pulse around circle */
    .score-wrap { animation: scoreGlow 3s infinite alternate; }
    @keyframes scoreGlow {
        0%   { filter: drop-shadow(0 0 20px var(--tier-glow)); }
        100% { filter: drop-shadow(0 0 45px var(--tier-glow)); }
    }

    /* ============================================
       ANIMATED PROGRESS BARS
    ============================================ */
    .prog-list { display: flex; flex-direction: column; gap: 16px; }
    .prog-item {}
    .prog-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 8px;
    }
    .prog-label {
        font-family: var(--font-display); font-size: 0.7rem;
        letter-spacing: 2px; text-transform: uppercase;
        display: flex; align-items: center; gap: 8px;
    }
    .prog-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .prog-count {
        font-family: var(--font-display); font-size: 0.75rem;
        font-weight: 700;
    }
    .prog-track {
        height: 6px; background: rgba(255,255,255,0.04);
        border-radius: 100px; overflow: hidden; border: 1px solid rgba(255,255,255,0.04);
    }
    .prog-fill {
        height: 100%; border-radius: 100px; width: 0%;
        transition: width 1.4s cubic-bezier(0.16,1,0.3,1);
    }
    .prog-benar  .prog-dot  { background: var(--correct); }
    .prog-benar  .prog-label{ color: var(--correct); }
    .prog-benar  .prog-count{ color: var(--correct); }
    .prog-benar  .prog-fill { background: linear-gradient(90deg, #4ade80, #86efac); box-shadow: 0 0 8px rgba(74,222,128,0.5); }

    .prog-salah  .prog-dot  { background: var(--wrong); }
    .prog-salah  .prog-label{ color: var(--wrong); }
    .prog-salah  .prog-count{ color: var(--wrong); }
    .prog-salah  .prog-fill { background: linear-gradient(90deg, #ef4444, #fca5a5); box-shadow: 0 0 8px rgba(239,68,68,0.5); }

    .prog-kosong .prog-dot  { background: var(--empty); }
    .prog-kosong .prog-label{ color: var(--empty); }
    .prog-kosong .prog-count{ color: var(--empty); }
    .prog-kosong .prog-fill { background: linear-gradient(90deg, #64748b, #94a3b8); }

    /* ============================================
       STAT MINI CARDS
    ============================================ */
    .stat-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-top: 24px; }
    .stat-mini {
        background: rgba(255,255,255,0.02); border: 1px solid var(--border);
        border-radius: 14px; padding: 18px; text-align: center; transition: all 0.3s;
    }
    .stat-mini:hover { transform: translateY(-4px); background: rgba(255,255,255,0.04); }
    .stat-mini-val {
        font-family: var(--font-display); font-size: 2.2rem; font-weight: 900;
        line-height: 1; margin-bottom: 6px;
    }
    .stat-mini-val.correct { color: var(--correct); text-shadow: 0 0 15px rgba(74,222,128,0.4); }
    .stat-mini-val.wrong   { color: var(--wrong);   text-shadow: 0 0 15px rgba(239,68,68,0.4); }
    .stat-mini-val.empty   { color: var(--empty); }
    .stat-mini-label {
        font-family: var(--font-display); font-size: 0.6rem;
        letter-spacing: 2.5px; color: var(--text-dim); text-transform: uppercase;
    }

    /* ============================================
       HEATMAP GRID
    ============================================ */
    .heatmap-label {
        font-family: var(--font-display); font-size: 0.65rem;
        letter-spacing: 4px; color: var(--gold); text-transform: uppercase;
        text-align: center; margin-bottom: 24px; opacity: 0.75;
        display: flex; align-items: center; justify-content: center; gap: 10px;
    }
    .heatmap-label::before, .heatmap-label::after {
        content: ''; display: inline-block;
        width: 40px; height: 1px; background: var(--gold); opacity: 0.5;
    }

    .jawaban-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(78px, 1fr));
        gap: 12px; margin-bottom: 24px;
    }
    .jawaban-link { text-decoration: none; display: block; outline: none; perspective: 1000px; }

    /* Base box */
    .jawaban-box {
        border-radius: 16px; padding: 14px 8px; text-align: center;
        position: relative; overflow: hidden;
        background: rgba(255,255,255,0.02); border: 1px solid var(--border);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        height: 100%; min-height: 78px;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .jawaban-link:hover .jawaban-box:not(.flip-container) { transform: translateY(-5px) scale(1.07); z-index: 10; }

    .jawaban-num {
        font-family: var(--font-display); font-size: 0.58rem; letter-spacing: 1px;
        color: rgba(255,255,255,0.45); font-weight: 600; margin-bottom: 6px;
    }
    .ans-text { font-family: var(--font-display); font-size: 1.4rem; font-weight: 900; line-height: 1; }

    /* Benar */
    .benar {
        background: linear-gradient(145deg, rgba(74,222,128,0.1), rgba(74,222,128,0.02));
        border-color: rgba(74,222,128,0.3);
        box-shadow: inset 0 0 14px rgba(74,222,128,0.15);
    }
    .benar:hover { box-shadow: 0 10px 25px -5px rgba(74,222,128,0.35); border-color: rgba(74,222,128,0.6); }
    .benar::before { content: '✓'; position: absolute; font-size: 3rem; color: rgba(74,222,128,0.07); bottom: -10px; right: -4px; font-weight: 900; }
    .benar .ans-text { color: var(--correct); text-shadow: 0 0 10px rgba(74,222,128,0.3); }

    /* Kosong */
    .kosong {
        background: repeating-linear-gradient(45deg, rgba(255,255,255,0.01), rgba(255,255,255,0.01) 10px, rgba(255,255,255,0.025) 10px, rgba(255,255,255,0.025) 20px);
        opacity: 0.6;
    }
    .kosong .ans-text { color: var(--empty); }

    /* Salah — 3D Flip Card */
    .flip-container { background: transparent !important; border: none !important; padding: 0 !important; }
    .flipper {
        position: relative; width: 100%; height: 100%;
        transition: transform 0.6s cubic-bezier(0.4,0.2,0.2,1);
        transform-style: preserve-3d; min-height: 78px;
    }
    .jawaban-link:hover .flipper { transform: rotateY(180deg); z-index: 20; }

    .front, .back {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        backface-visibility: hidden;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); padding: 14px 8px;
    }
    .front.salah {
        background: linear-gradient(135deg, rgba(239,68,68,0.14), rgba(239,68,68,0.04));
        border-color: rgba(239,68,68,0.3);
        box-shadow: inset 0 0 14px rgba(239,68,68,0.1);
        overflow: hidden;
    }
    .front.salah::before { content: '✗'; position: absolute; font-size: 3rem; color: rgba(239,68,68,0.07); bottom: -10px; right: 0; font-weight: 900; }
    .front .ans-text { color: var(--wrong); }

    .back {
        transform: rotateY(180deg);
        background: linear-gradient(135deg, rgba(74,222,128,0.18), rgba(74,222,128,0.04));
        border-color: rgba(74,222,128,0.5);
        box-shadow: 0 0 20px rgba(74,222,128,0.25);
    }
    .ans-key { font-family: var(--font-display); font-size: 1.4rem; font-weight: 900; color: var(--correct); text-shadow: 0 0 10px rgba(74,222,128,0.5); }

    /* Cascading wave */
    @keyframes popInWave {
        0%   { opacity: 0; transform: scale(0.5) translateY(18px); }
        100% { opacity: 1; transform: scale(1)   translateY(0); }
    }

    /* Legend */
    .heatmap-legend {
        display: flex; gap: 24px; justify-content: center;
        font-size: 0.82rem; color: var(--text-dim); flex-wrap: wrap;
    }
    .legend-item { display: flex; align-items: center; gap: 8px; font-weight: 500; }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; }

    /* ============================================
       TOOLTIP
    ============================================ */
    .tooltip-inner {
        background: var(--gold); color: var(--ink) !important;
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 1px; border-radius: 8px; padding: 6px 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    .bs-tooltip-top .tooltip-arrow::before { border-top-color: var(--gold); }

    /* ============================================
       CTA BUTTONS
    ============================================ */
    .btn-row {
        display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;
        padding: 0 10px;
    }
    .btn-cta {
        display: inline-flex; align-items: center; gap: 12px;
        background: transparent; border: 1px solid var(--gold);
        color: var(--gold); font-family: var(--font-display);
        font-size: 0.78rem; letter-spacing: 2.5px; text-transform: uppercase;
        padding: 16px 36px; border-radius: 100px; text-decoration: none;
        transition: all 0.4s; position: relative; overflow: hidden;
    }
    .btn-cta::before {
        content: ''; position: absolute; inset: 0;
        background: var(--gold); transform: scaleX(0);
        transform-origin: left; transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-cta:hover { color: var(--ink); box-shadow: 0 0 40px var(--gold-glow); }
    .btn-cta:hover::before { transform: scaleX(1); }
    .btn-cta span, .btn-cta svg { position: relative; z-index: 1; transition: color 0.4s; }
    .btn-cta:hover svg, .btn-cta:hover span { color: var(--ink); }

    .btn-cta.secondary {
        border-color: var(--border); color: var(--text-dim);
    }
    .btn-cta.secondary::before { background: var(--surface); }
    .btn-cta.secondary:hover { color: var(--text); box-shadow: none; }

    /* Ornament */
    .ornament { text-align: center; margin: 28px 0; color: var(--gold); opacity: 0.3; font-size: 1rem; letter-spacing: 12px; font-family: var(--font-display); }

    /* Footer */
    .footer { text-align: center; padding: 40px 0 0; border-top: 1px solid var(--border); margin-top: 50px; font-size: 0.82rem; color: var(--text-dim); letter-spacing: 0.5px; }

    /* Reveal */
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1); }
    .reveal.active { opacity: 1; transform: translateY(0); }
    .reveal-d1 { transition-delay: 0.1s; }
    .reveal-d2 { transition-delay: 0.2s; }
    .reveal-d3 { transition-delay: 0.3s; }

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
        .main-wrap { padding-bottom: 140px; }
        .section-card { padding: 22px 16px; }
        .info-grid { grid-template-columns: 1fr 1fr; }
        .info-grid .info-item:last-child { grid-column: span 2; }
        .flow-line { width: 28px; }
        .flow-label { font-size: 0.5rem; }
        .score-wrap { width: 180px; height: 180px; }
        .score-number { font-size: 3.8rem; }
        .stat-row { gap: 8px; }
        .stat-mini-val { font-size: 1.8rem; }
        .jawaban-grid { grid-template-columns: repeat(5, 1fr); gap: 8px; }
        .jawaban-box, .flipper, .front, .back { min-height: 66px; padding: 10px 4px; border-radius: 12px; }
        .jawaban-num { font-size: 0.52rem; margin-bottom: 4px; }
        .ans-text, .ans-key { font-size: 1.2rem; }

        /* Sticky bottom bar */
        .btn-row {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(2,6,23,0.92); backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid var(--border);
            padding: 14px 16px; margin: 0; z-index: 500;
            flex-wrap: nowrap;
        }
        .btn-cta { flex: 1; justify-content: center; padding: 14px 10px; font-size: 0.7rem; letter-spacing: 1.5px; border-radius: 14px; }
    }

    @media (max-width: 400px) {
        .rank-badge { font-size: 0.85rem; padding: 10px 20px; }
        .hero-title { font-size: 1.6rem; }
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

    <!-- MISSION FLOW -->
    <div class="mission-flow reveal active">
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
        <div class="flow-step done">
            <div class="flow-dot done"></div>
            <span class="flow-label">Kuis</span>
        </div>
        <div class="flow-line done"></div>
        <div class="flow-step active">
            <div class="flow-dot active">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span class="flow-label">Hasil</span>
        </div>
    </div>

    <!-- HERO — RANK -->
    <div class="section-card reveal reveal-d1">
        <div class="section-tag">Evaluasi Selesai</div>
        <div class="hero-section">
            <div class="rank-badge">
                <span style="font-size:1.3rem"><?= $tier_icon ?></span>
                <span><?= $tier_label ?></span>
            </div>
            <h2 class="hero-title">Hasil Kuis</h2>
            <p class="hero-pesan"><?= $pesan ?></p>
        </div>
    </div>

    <!-- INFO BAR -->
    <div class="section-card reveal reveal-d2">
        <div class="section-tag">Data Peserta</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nama Peserta</div>
                <div class="info-value"><?= htmlspecialchars($data['nama_murid']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">No. Absen</div>
                <div class="info-value"><?= str_pad($data['no_absen'], 2, '0', STR_PAD_LEFT) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Waktu Submit</div>
                <div class="info-value" style="font-size:0.88rem"><?= $waktu_formatted ?></div>
            </div>
        </div>
    </div>

    <!-- SCORE CIRCLE + PROGRESS BARS -->
    <div class="section-card reveal reveal-d3">
        <div class="section-tag">Skor Akhir</div>

        <div class="score-section">
            <div class="score-wrap">
                <div class="score-ring-mid"></div>
                <div class="score-ring-outer"></div>
                <svg class="score-svg" viewBox="0 0 220 220">
                    <circle class="score-bg"   cx="110" cy="110" r="95"/>
                    <circle class="score-fill" id="scoreFill" cx="110" cy="110" r="95"/>
                </svg>
                <div class="score-center">
                    <div class="score-number" id="scoreCounter">0</div>
                    <div class="score-label">Skor</div>
                </div>
            </div>
        </div>

        <!-- Stat mini cards -->
        <div class="stat-row">
            <div class="stat-mini">
                <div class="stat-mini-val correct"><?= $benar ?></div>
                <div class="stat-mini-label">Benar</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-val wrong"><?= $salah ?></div>
                <div class="stat-mini-label">Salah</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-val empty"><?= $tidak_dijawab ?></div>
                <div class="stat-mini-label">Kosong</div>
            </div>
        </div>

        <!-- Animated progress bars -->
        <div class="prog-list mt-4" id="progList">
            <div class="prog-item prog-benar">
                <div class="prog-header">
                    <div class="prog-label"><span class="prog-dot"></span>Jawaban Benar</div>
                    <div class="prog-count"><?= $benar ?> / <?= $total_soal ?></div>
                </div>
                <div class="prog-track"><div class="prog-fill" data-width="<?= $pct_benar ?>"></div></div>
            </div>
            <div class="prog-item prog-salah">
                <div class="prog-header">
                    <div class="prog-label"><span class="prog-dot"></span>Jawaban Salah</div>
                    <div class="prog-count"><?= $salah ?> / <?= $total_soal ?></div>
                </div>
                <div class="prog-track"><div class="prog-fill" data-width="<?= $pct_salah ?>"></div></div>
            </div>
            <div class="prog-item prog-kosong">
                <div class="prog-header">
                    <div class="prog-label"><span class="prog-dot"></span>Tidak Dijawab</div>
                    <div class="prog-count"><?= $tidak_dijawab ?> / <?= $total_soal ?></div>
                </div>
                <div class="prog-track"><div class="prog-fill" data-width="<?= $pct_kosong ?>"></div></div>
            </div>
        </div>
    </div>

    <div class="ornament reveal">· · ✦ · ·</div>

    <!-- HEATMAP GRID -->
    <div class="section-card reveal">
        <div class="section-tag">Mini Heatmap</div>
        <div class="heatmap-label">Peta Jawaban</div>

        <div class="jawaban-grid">
        <?php for ($i = 1; $i <= 20; $i++):
            $stat   = $detail[$i]['status'];
            $j_user = $detail[$i]['jawab'];
            $j_kunci= $detail[$i]['kunci'];
            $delay  = $i * 0.045;
        ?>
            <a href="review.php?id=<?= $id ?>&no=<?= $i ?>"
               class="jawaban-link"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               title="Pembahasan No. <?= $i ?>"
               style="animation: popInWave 0.5s cubic-bezier(0.175,0.885,0.32,1.275) forwards; opacity:0; animation-delay:<?= $delay ?>s;">

                <?php if ($stat === 'salah'): ?>
                <div class="jawaban-box flip-container">
                    <div class="flipper">
                        <div class="front salah">
                            <span class="jawaban-num">NO. <?= $i ?></span>
                            <span class="ans-text"><?= $j_user ?></span>
                        </div>
                        <div class="back">
                            <span class="jawaban-num">KUNCI</span>
                            <span class="ans-key"><?= $j_kunci ?></span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="jawaban-box <?= $stat ?>">
                    <span class="jawaban-num">NO. <?= $i ?></span>
                    <span class="ans-text"><?= ($j_user === '-') ? '—' : $j_user ?></span>
                </div>
                <?php endif; ?>

            </a>
        <?php endfor; ?>
        </div>

        <div class="heatmap-legend">
            <div class="legend-item"><div class="legend-dot" style="background:var(--correct)"></div>Tepat</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--wrong)"></div>Keliru (hover/tap)</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--empty)"></div>Kosong</div>
        </div>
    </div>

    <!-- CTA BUTTONS -->
    <div class="btn-row reveal mt-2">
        <a href="review.php?id=<?= $id ?>" class="btn-cta secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <span>Pembahasan</span>
        </a>
        <a href="index.php" class="btn-cta">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Kembali ke Materi</span>
        </a>
    </div>

    <div class="footer reveal">
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
   SCROLL REVEAL
============================================================ */
function checkReveal(){
    document.querySelectorAll('.reveal').forEach(el=>{
        if(el.getBoundingClientRect().top < window.innerHeight - 80) el.classList.add('active');
    });
}
window.addEventListener('scroll', checkReveal);
checkReveal();

/* ============================================================
   BOOTSTRAP TOOLTIPS
============================================================ */
document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});

/* ============================================================
   SCORE CIRCLE ANIMATION
============================================================ */
window.addEventListener('DOMContentLoaded', ()=>{
    const targetSkor  = <?= $skor ?>;
    const counter     = document.getElementById('scoreCounter');
    const fill        = document.getElementById('scoreFill');
    const circumference = 2 * Math.PI * 95; // r=95

    // Animate SVG fill
    setTimeout(()=>{
        const offset = circumference - (targetSkor / 100) * circumference;
        fill.style.strokeDasharray  = circumference;
        fill.style.strokeDashoffset = circumference; // start empty
        requestAnimationFrame(()=>{
            requestAnimationFrame(()=>{
                fill.style.strokeDashoffset = offset;
            });
        });
    }, 400);

    // Animate counter number
    setTimeout(()=>{
        if(targetSkor <= 0){ counter.textContent = '0'; return; }
        const dur  = 2000;
        const step = Math.max(Math.floor(dur / targetSkor), 12);
        let cur = 0;
        const t = setInterval(()=>{
            cur++;
            counter.textContent = cur;
            if(cur >= targetSkor) clearInterval(t);
        }, step);
    }, 300);

    /* ============================================================
       PROGRESS BARS — animate after short delay
    ============================================================ */
    setTimeout(()=>{
        document.querySelectorAll('.prog-fill').forEach(bar=>{
            bar.style.width = (bar.dataset.width || 0) + '%';
        });
    }, 600);

    /* ============================================================
       CONFETTI — warna emas
    ============================================================ */
    const tier = '<?= $confetti_tier ?>';
    setTimeout(()=>{
        if(tier === 'sultan'){
            const end = Date.now() + 3000;
            const colors = ['#c9a84c','#f0d080','#ffffff','#fde68a'];
            function burst(){
                confetti({ particleCount: 45, startVelocity: 32, spread: 360, ticks: 65, zIndex: 200,
                    origin: { x: Math.random()*0.4+0.1, y: Math.random()-0.2 }, colors });
                confetti({ particleCount: 45, startVelocity: 32, spread: 360, ticks: 65, zIndex: 200,
                    origin: { x: Math.random()*0.4+0.5, y: Math.random()-0.2 }, colors });
                if(Date.now() < end) setTimeout(burst, 260);
            }
            burst();
        } else if(tier === 'panglima'){
            const end = Date.now() + 1800;
            const colors = ['#c9a84c','#f0d080','#ffffff'];
            (function frame(){
                confetti({ particleCount: 4, angle: 60, spread: 55, origin:{ x:0 }, colors });
                confetti({ particleCount: 4, angle: 120, spread: 55, origin:{ x:1 }, colors });
                if(Date.now() < end) requestAnimationFrame(frame);
            })();
        }
    }, 900);
});
</script>
</body>
</html>
