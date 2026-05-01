<?php
session_start();
require_once 'config/koneksi.php';

// KUNCI JAWABAN 20 SOAL
$kunci = [
    1 => 'a', 2 => 'a', 3 => 'a', 4 => 'a', 5 => 'a',
    6 => 'b', 7 => 'b', 8 => 'b', 9 => 'b', 10 => 'b',
    11 => 'c', 12 => 'c', 13 => 'c', 14 => 'c', 15 => 'c',
    16 => 'd', 17 => 'd', 18 => 'd', 19 => 'd', 20 => 'd'
];

try {
    $stmt = $pdo->query("SELECT * FROM tugas_aceh ORDER BY CAST(no_absen AS UNSIGNED) ASC");
    $semua_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("<p style='color:#ef4444;padding:40px;font-family:sans-serif;'>Terjadi kesalahan dalam mengambil data.</p>");
}

// Inisialisasi statistik
$total_skor = 0;
$jumlah_tuntas = 0;
$skor_tertinggi = 0;
$skor_terendah = 100;
$jawaban_benar_per_soal = array_fill(1, 20, 0);
$detail_per_soal = array_fill(1, 20, []);
$data_valid = [];
$count_tinggi = $count_sedang = $count_rendah = 0;

foreach ($semua_data as $row) {
    if (trim($row['no_absen']) === '') continue;

    $benar = 0;
    $jawaban_siswa = [];
    for ($i = 1; $i <= 20; $i++) {
        $col   = 'jawaban_' . $i;
        $jawab = isset($row[$col]) ? (string)$row[$col] : '-';
        $jawaban_siswa[$i] = $jawab;
        if ($jawab !== '-' && $jawab === (string)$kunci[$i]) {
            $benar++;
            $jawaban_benar_per_soal[$i]++;
            $detail_per_soal[$i][] = ['absen' => $row['no_absen'], 'nama' => $row['nama_murid']];
        }
    }
    $skor = $benar * 5;

    $row['benar']   = $benar;
    $row['salah']   = 20 - $benar;
    $row['skor']    = $skor;
    $row['jawaban'] = $jawaban_siswa;
    $data_valid[]   = $row;

    $total_skor += $skor;
    if ($skor >= 70) $jumlah_tuntas++;
    if ($skor > $skor_tertinggi) $skor_tertinggi = $skor;
    if ($skor < $skor_terendah)  $skor_terendah  = $skor;

    if ($skor >= 75)      $count_tinggi++;
    elseif ($skor >= 50)  $count_sedang++;
    else                  $count_rendah++;
}

$total_murid  = count($data_valid);
$rata_rata    = $total_murid ? round($total_skor / $total_murid, 1) : 0;
$skor_terendah = $total_murid ? $skor_terendah : 0;
$pct_tuntas   = $total_murid ? round($jumlah_tuntas / $total_murid * 100, 1) : 0;
$tidak_tuntas = $total_murid - $jumlah_tuntas;

// Top 3
$sorted = $data_valid;
usort($sorted, fn($a, $b) => $b['skor'] - $a['skor']);
$top3 = array_slice($sorted, 0, 3);

// ★ Hitung indikasi curang (tab_switch >= 3)
$count_curang = 0;
foreach ($data_valid as $row) {
    $switches = isset($row['tab_switch_count']) ? (int)$row['tab_switch_count'] : 0;
    if ($switches >= 3) $count_curang++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard Rekap — AcehEdu Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
    /* ============================================
       ROOT — IDENTIK SELURUH EKOSISTEM
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
        --green:      #4ade80;
        --red:        #ef4444;
        --yellow:     #facc15;
        --radius:     20px;
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

    /* ============================================
       MAIN WRAP
    ============================================ */
    .main-wrap {
        position: relative; z-index: 1;
        max-width: 1200px; margin: 0 auto;
        padding: 36px 20px 80px;
    }

    /* ============================================
       STEP INDICATOR
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
    .flow-dot.locked { opacity: 0.3; }
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
       SECTION CARD
    ============================================ */
    .section-card {
        background: linear-gradient(145deg, rgba(20,18,14,0.9), rgba(2,6,23,0.95));
        border: 1px solid var(--border); border-radius: var(--radius);
        padding: 32px; margin-bottom: 24px;
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
    .section-card:hover {
        border-color: rgba(201,168,76,0.35);
        box-shadow: 0 20px 60px -20px rgba(0,0,0,0.8), 0 0 40px -20px var(--gold-glow);
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
    .section-title {
        font-family: var(--font-display);
        font-size: clamp(1.1rem, 2.5vw, 1.4rem); font-weight: 700;
        color: var(--text); margin-bottom: 24px;
        display: flex; align-items: center; gap: 12px;
    }
    .section-title svg { color: var(--gold); flex-shrink: 0; }

    /* ============================================
       HERO HEADER
    ============================================ */
    .page-hero {
        text-align: center; margin-bottom: 40px;
    }
    .hero-eyebrow {
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 5px; color: var(--gold); text-transform: uppercase;
        margin-bottom: 12px; opacity: 0.75;
    }
    .hero-title {
        font-family: var(--font-display);
        font-size: clamp(2rem, 5vw, 3rem); font-weight: 900;
        background: linear-gradient(180deg, #f5edd8 0%, var(--gold) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text; line-height: 1.1; margin-bottom: 12px;
    }
    .hero-sub { font-size: 0.9rem; color: var(--text-dim); font-weight: 300; letter-spacing: 0.5px; }

    /* Peserta pill */
    .peserta-pill {
        display: inline-flex; align-items: center; gap: 10px;
        margin-top: 18px; padding: 10px 22px;
        border: 1px solid var(--border); border-radius: 100px;
        font-family: var(--font-display); font-size: 0.75rem;
        letter-spacing: 2px; color: var(--gold); background: var(--gold-dim);
    }

    /* ============================================
       STAT CARDS
    ============================================ */
    .stat-card {
        background: linear-gradient(145deg, rgba(20,18,14,0.85), rgba(2,6,23,0.95));
        border: 1px solid var(--border); border-radius: 18px;
        padding: 26px 22px; position: relative; overflow: hidden;
        transition: all 0.35s; height: 100%;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, var(--gold), transparent);
        opacity: 0.4;
    }
    .stat-card:hover {
        border-color: rgba(201,168,76,0.4);
        transform: translateY(-4px);
        box-shadow: 0 16px 40px -12px rgba(0,0,0,0.7), 0 0 30px -15px var(--gold-glow);
    }
    .stat-label {
        font-family: var(--font-display); font-size: 0.62rem;
        letter-spacing: 3px; color: var(--text-dim); text-transform: uppercase;
        margin-bottom: 10px;
    }
    .stat-value {
        font-family: var(--font-display); font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 900; line-height: 1; color: var(--gold);
        text-shadow: 0 0 30px var(--gold-glow);
    }
    .stat-value.danger { color: var(--red); text-shadow: 0 0 30px rgba(239,68,68,0.4); }
    .stat-value.success { color: var(--green); text-shadow: 0 0 30px rgba(74,222,128,0.4); }
    .stat-sub {
        font-size: 0.78rem; color: var(--text-dim); margin-top: 6px; font-weight: 400;
    }
    .stat-icon {
        position: absolute; bottom: 16px; right: 18px;
        opacity: 0.08; color: var(--gold);
    }

    /* ============================================
       ★ TOP 3 PODIUM
    ============================================ */
    .podium-wrap {
        display: flex; align-items: flex-end; justify-content: center;
        gap: 16px; padding: 10px 0 0;
    }
    .podium-item {
        flex: 1; max-width: 200px; text-align: center;
        display: flex; flex-direction: column; align-items: center;
    }
    .podium-avatar {
        width: 56px; height: 56px; border-radius: 50%;
        background: var(--gold-dim); border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-size: 1.1rem;
        color: var(--gold); margin-bottom: 10px;
        transition: all 0.3s;
    }
    .podium-item:hover .podium-avatar {
        border-color: var(--gold);
        box-shadow: 0 0 20px var(--gold-glow);
        transform: scale(1.08);
    }
    .podium-item.rank-1 .podium-avatar {
        width: 68px; height: 68px; font-size: 1.4rem;
        border-color: var(--gold);
        box-shadow: 0 0 25px var(--gold-glow);
        background: rgba(201,168,76,0.18);
    }
    .podium-item.rank-2 .podium-avatar { border-color: rgba(148,163,184,0.6); color: #94a3b8; background: rgba(148,163,184,0.08); }
    .podium-item.rank-3 .podium-avatar { border-color: rgba(180,120,70,0.6); color: #b47846; background: rgba(180,120,70,0.08); }

    .podium-name {
        font-family: var(--font-display); font-size: 0.78rem;
        color: var(--text); letter-spacing: 0.5px; margin-bottom: 4px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 160px;
    }
    .podium-score-badge {
        font-family: var(--font-display); font-size: 0.85rem; font-weight: 700;
        padding: 4px 14px; border-radius: 100px; margin-bottom: 12px;
    }
    .rank-1 .podium-score-badge { background: var(--gold); color: var(--ink); box-shadow: 0 0 15px var(--gold-glow); }
    .rank-2 .podium-score-badge { background: rgba(148,163,184,0.2); color: #94a3b8; border: 1px solid rgba(148,163,184,0.3); }
    .rank-3 .podium-score-badge { background: rgba(180,120,70,0.2); color: #b47846; border: 1px solid rgba(180,120,70,0.3); }

    .podium-block {
        width: 100%; border-radius: 12px 12px 0 0;
        display: flex; align-items: flex-start; justify-content: center;
        padding-top: 12px;
        font-family: var(--font-display); font-size: 1.2rem; color: rgba(255,255,255,0.15);
    }
    .rank-1 .podium-block { height: 90px; background: linear-gradient(180deg, rgba(201,168,76,0.2), rgba(201,168,76,0.05)); border: 1px solid rgba(201,168,76,0.3); border-bottom: none; }
    .rank-2 .podium-block { height: 65px; background: linear-gradient(180deg, rgba(148,163,184,0.1), rgba(148,163,184,0.02)); border: 1px solid rgba(148,163,184,0.15); border-bottom: none; }
    .rank-3 .podium-block { height: 48px; background: linear-gradient(180deg, rgba(180,120,70,0.1), rgba(180,120,70,0.02)); border: 1px solid rgba(180,120,70,0.15); border-bottom: none; }

    /* ============================================
       CHART CONTAINERS
    ============================================ */
    .chart-box {
        background: rgba(0,0,0,0.25); border: 1px solid var(--border);
        border-radius: 16px; padding: 24px; transition: all 0.35s; height: 100%;
    }
    .chart-box:hover { border-color: rgba(201,168,76,0.35); box-shadow: 0 0 30px -10px var(--gold-glow); }
    .chart-label {
        font-family: var(--font-display); font-size: 0.7rem;
        letter-spacing: 3px; color: var(--gold); text-transform: uppercase;
        margin-bottom: 16px; opacity: 0.75;
        display: flex; align-items: center; gap: 10px;
    }
    .chart-label::before { content: ''; display: inline-block; width: 16px; height: 1px; background: var(--gold); }
    .chart-hint {
        font-size: 0.72rem; color: var(--text-dim); margin-top: 10px; text-align: center;
    }

    /* Donut legend */
    .donut-legend { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; }
    .legend-item { display: flex; align-items: center; gap: 10px; font-size: 0.83rem; color: var(--text-dim); }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

    /* ============================================
       TOOLBAR
    ============================================ */
    .toolbar {
        display: flex; flex-wrap: wrap; gap: 12px;
        align-items: center; justify-content: space-between;
        margin-bottom: 20px;
    }
    .dist-pills {
        display: flex; gap: 10px; flex-wrap: wrap;
    }
    .dist-pill {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 100px;
        border: 1px solid var(--border);
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 1px; text-transform: uppercase;
        background: rgba(255,255,255,0.02); color: var(--text-dim);
    }
    .pill-dot { width: 8px; height: 8px; border-radius: 50%; }
    .pill-gold   { background: var(--gold);   box-shadow: 0 0 8px var(--gold-glow); }
    .pill-yellow { background: var(--yellow); box-shadow: 0 0 8px rgba(250,204,21,0.4); }
    .pill-red    { background: var(--red);    box-shadow: 0 0 8px rgba(239,68,68,0.4); }

    .search-input {
        background: rgba(255,255,255,0.04); border: 1px solid var(--border);
        border-radius: 100px; padding: 10px 20px; color: var(--text);
        font-family: var(--font-body); font-size: 0.88rem;
        flex: 1; min-width: 200px; max-width: 280px; transition: all 0.3s;
    }
    .search-input:focus { outline: none; border-color: var(--gold); background: var(--gold-dim); box-shadow: 0 0 20px -5px var(--gold-glow); }
    .search-input::placeholder { color: var(--text-dim); }

    .slider-wrap {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 18px; border: 1px solid var(--border);
        border-radius: 100px; background: rgba(255,255,255,0.02);
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 1px; color: var(--text-dim);
    }
    .slider-val { color: var(--gold); font-weight: 700; min-width: 28px; }
    input[type=range] {
        -webkit-appearance: none; width: 120px; background: transparent;
    }
    input[type=range]::-webkit-slider-runnable-track {
        height: 3px; background: rgba(201,168,76,0.2); border-radius: 2px;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none; width: 14px; height: 14px;
        background: var(--gold); border-radius: 50%; margin-top: -5.5px;
        box-shadow: 0 0 8px var(--gold-glow); cursor: pointer;
    }

    /* ============================================
       TABLE
    ============================================ */
    .table-wrap { border-radius: 16px; overflow: hidden; border: 1px solid var(--border); }
    .table {
        margin: 0; font-size: 0.88rem;
        --bs-table-bg: transparent;
        --bs-table-color: var(--text);
        --bs-table-hover-bg: var(--gold-dim);
    }
    .table thead th {
        background: rgba(201,168,76,0.08) !important;
        color: var(--gold) !important;
        font-family: var(--font-display); font-size: 0.68rem;
        letter-spacing: 2px; text-transform: uppercase;
        border-bottom: 1px solid var(--border) !important;
        padding: 16px 20px; font-weight: 600; white-space: nowrap;
    }
    .table tbody td {
        padding: 16px 20px; border-color: var(--border) !important;
        color: #b0a898; vertical-align: middle; transition: color 0.3s;
    }
    .table tbody tr:hover td { color: var(--text); }
    .table tbody tr:last-child td { border-bottom: none !important; }

    /* Row tints */
    .row-super  td { background: rgba(201,168,76,0.06) !important; }
    .row-good   td { background: rgba(74,222,128,0.04) !important; }
    .row-medium td { background: rgba(250,204,21,0.04) !important; }
    .row-poor   td { background: rgba(239,68,68,0.05) !important; }

    .table tbody tr { animation: fadeInRow 0.4s ease forwards; opacity: 0; }
    @keyframes fadeInRow { from { opacity:0; transform: translateX(-8px); } to { opacity:1; transform:translateX(0); } }
    .table tbody tr:hover { box-shadow: inset 3px 0 0 var(--gold); }

    /* Absen badge */
    .absen-tag {
        font-family: var(--font-display); font-size: 0.75rem;
        color: var(--gold); font-weight: 700;
    }

    /* Score badge */
    .score-badge {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 52px; padding: 6px 14px; border-radius: 10px;
        font-family: var(--font-display); font-size: 0.9rem; font-weight: 700;
        color: var(--ink);
    }
    .badge-gold   { background: linear-gradient(135deg, var(--gold), var(--gold-light)); box-shadow: 0 4px 12px var(--gold-glow); }
    .badge-yellow { background: linear-gradient(135deg, #facc15, #fde68a); box-shadow: 0 4px 12px rgba(250,204,21,0.4); }
    .badge-red    { background: linear-gradient(135deg, #ef4444, #fca5a5); box-shadow: 0 4px 12px rgba(239,68,68,0.35); }

    /* ★ Tab switch badge */
    .tab-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 100px;
        font-family: var(--font-display); font-size: 0.68rem;
        font-weight: 700; letter-spacing: 1px; white-space: nowrap;
    }
    .tab-badge svg { flex-shrink: 0; }
    .tab-badge.clean {
        color: #4ade80;
        background: rgba(74,222,128,0.08);
        border: 1px solid rgba(74,222,128,0.2);
    }
    .tab-badge.warn {
        color: #facc15;
        background: rgba(250,204,21,0.08);
        border: 1px solid rgba(250,204,21,0.25);
    }
    .tab-badge.danger {
        color: #ef4444;
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.35);
        box-shadow: 0 0 12px rgba(239,68,68,0.15);
        animation: suspectPulse 2.5s infinite;
    }
    @keyframes suspectPulse {
        0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.3); }
        50%      { box-shadow: 0 0 0 5px rgba(239,68,68,0); }
    }
    /* Highlight row mencurigakan */
    .row-suspect td { background: rgba(239,68,68,0.04) !important; }
    /* Suspect icon di kolom nama */
    .suspect-icon {
        display: inline-flex; align-items: center;
        vertical-align: middle; margin-left: 6px;
        color: #ef4444;
    }

    /* ============================================
       MODAL — REDESIGN
    ============================================ */
    .modal-content {
        background: linear-gradient(145deg, rgba(20,18,14,0.97), rgba(2,6,23,0.98));
        backdrop-filter: blur(30px); border: 1px solid var(--border);
        border-radius: 24px; color: var(--text);
        box-shadow: 0 40px 80px rgba(0,0,0,0.8), 0 0 60px -20px var(--gold-glow);
    }
    .modal-header {
        border-bottom: 1px solid var(--border); padding: 24px 28px;
        background: linear-gradient(90deg, var(--gold-dim), transparent);
    }
    .modal-title-wrap { display: flex; flex-direction: column; gap: 4px; }
    .modal-eyebrow {
        font-family: var(--font-display); font-size: 0.6rem;
        letter-spacing: 4px; color: var(--gold); opacity: 0.7; text-transform: uppercase;
    }
    .modal-title {
        font-family: var(--font-display) !important; font-size: 1.1rem !important;
        color: var(--text) !important; letter-spacing: 0.5px;
    }
    .modal-body { padding: 24px 28px; }
    .modal-footer { border-top: 1px solid var(--border); padding: 16px 28px; }
    .modal-stat {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 18px; border-radius: 100px;
        border: 1px solid var(--border); background: var(--gold-dim);
        font-family: var(--font-display); font-size: 0.75rem;
        color: var(--gold); letter-spacing: 1px; margin-bottom: 18px;
    }
    .modal-list { list-style: none; padding: 0; margin: 0; max-height: 220px; overflow-y: auto; }
    .modal-list li {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.04);
        font-size: 0.85rem; color: var(--text-dim);
    }
    .modal-list li:last-child { border-bottom: none; }
    .modal-list li:hover { color: var(--text); }
    .absen-mini {
        font-family: var(--font-display); font-size: 0.68rem;
        color: var(--gold); background: var(--gold-dim);
        border: 1px solid var(--border); border-radius: 6px;
        padding: 2px 8px; flex-shrink: 0;
    }
    .btn-modal-close {
        background: transparent; border: 1px solid var(--border);
        border-radius: 100px; padding: 10px 28px;
        font-family: var(--font-display); font-size: 0.72rem;
        letter-spacing: 2px; color: var(--gold); cursor: pointer;
        transition: all 0.3s; text-transform: uppercase;
    }
    .btn-modal-close:hover { background: var(--gold-dim); border-color: var(--gold); box-shadow: 0 0 20px -5px var(--gold-glow); }

    /* ============================================
       BACK HOME BUTTON
    ============================================ */
    .btn-back {
        display: inline-flex; align-items: center; gap: 12px;
        background: transparent; color: var(--gold);
        border: 1px solid var(--gold); font-family: var(--font-display);
        font-size: 0.78rem; letter-spacing: 3px; text-transform: uppercase;
        padding: 16px 40px; border-radius: 100px; text-decoration: none;
        transition: all 0.4s; position: relative; overflow: hidden;
    }
    .btn-back::before {
        content: ''; position: absolute; inset: 0;
        background: var(--gold); transform: scaleX(0);
        transform-origin: left; transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .btn-back:hover { color: var(--ink); box-shadow: 0 0 40px var(--gold-glow); }
    .btn-back:hover::before { transform: scaleX(1); }
    .btn-back span, .btn-back svg { position: relative; z-index: 1; transition: color 0.4s; }
    .btn-back:hover span, .btn-back:hover svg { color: var(--ink); }

    /* ============================================
       EMPTY STATE
    ============================================ */
    .empty-state {
        text-align: center; padding: 60px 20px;
        color: var(--text-dim);
    }
    .empty-state svg { opacity: 0.3; margin-bottom: 16px; }
    .empty-state p { font-family: var(--font-display); font-size: 0.9rem; letter-spacing: 1px; }

    /* Ornament */
    .ornament { text-align: center; margin: 32px 0; color: var(--gold); opacity: 0.3; font-size: 1rem; letter-spacing: 12px; font-family: var(--font-display); }

    /* Footer */
    .footer { text-align: center; padding: 40px 0 0; border-top: 1px solid var(--border); margin-top: 50px; font-size: 0.82rem; color: var(--text-dim); letter-spacing: 0.5px; }

    /* Reveal */
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1); }
    .reveal.active { opacity: 1; transform: translateY(0); }

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
        .section-card { padding: 22px 16px; }
        .podium-wrap { gap: 8px; }
        .podium-name { font-size: 0.68rem; max-width: 90px; }
        .flow-line { width: 28px; }
        .flow-label { font-size: 0.5rem; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .search-input { max-width: 100%; }
        .slider-wrap { justify-content: space-between; }
        input[type=range] { flex: 1; }
        .dist-pills { justify-content: center; }
        .btn-back { width: 100%; justify-content: center; }

        /* Table → Cards */
        .table-wrap { background: transparent; border: none; }
        .table-responsive { overflow: visible; }
        .table thead { display: none; }
        .table tbody tr {
            display: block; margin-bottom: 14px;
            border: 1px solid var(--border) !important;
            border-radius: 14px; padding: 14px;
            background: rgba(15,12,8,0.7) !important;
        }
        .table tbody td {
            display: flex; justify-content: space-between; align-items: center;
            padding: 8px 0; border-bottom: 1px dashed rgba(255,255,255,0.05) !important;
        }
        .table tbody td:last-child { border-bottom: none !important; }
        .table tbody td::before {
            content: attr(data-label);
            font-size: 0.65rem; text-transform: uppercase;
            letter-spacing: 1.5px; color: var(--gold);
            font-weight: 700; opacity: 0.7;
            font-family: var(--font-display);
        }
        .row-super td, .row-good td, .row-medium td, .row-poor td { background: transparent !important; }
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
        <a class="sidebar-link active" href="rekap.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Dashboard Rekap
        </a>
    </div>
</div>

<!-- MAIN -->
<div class="main-wrap">

    <!-- STEP INDICATOR -->
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
            <span class="flow-label">Rekap</span>
        </div>
    </div>

    <!-- HERO -->
    <div class="page-hero reveal">
        <div class="hero-eyebrow">Command Center</div>
        <h1 class="hero-title">Dashboard Rekap</h1>
        <p class="hero-sub">Analisis Nilai Kuis Kesultanan Aceh · 20 Soal</p>
        <div class="peserta-pill">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span class="count-up" data-target="<?= $total_murid ?>">0</span> Peserta Terdaftar
        </div>
    </div>

    <div class="ornament reveal">· · ✦ · ·</div>

    <!-- STAT CARDS -->
    <div class="section-card reveal">
        <div class="section-tag">Statistik Utama</div>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Rata-rata Kelas</div>
                    <div class="stat-value count-up-float" data-target="<?= $rata_rata ?>">0.0</div>
                    <div class="stat-sub">dari skor maksimal 100</div>
                    <div class="stat-icon"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Tuntas (≥70)</div>
                    <div class="stat-value success">
                        <span class="count-up" data-target="<?= $jumlah_tuntas ?>">0</span><span style="font-size:1rem;opacity:0.4;font-weight:400"> /<?= $total_murid ?></span>
                    </div>
                    <div class="stat-sub"><?= $pct_tuntas ?>% tingkat ketuntasan</div>
                    <div class="stat-icon"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Skor Tertinggi</div>
                    <div class="stat-value count-up" data-target="<?= $skor_tertinggi ?>">0</div>
                    <div class="stat-sub">nilai terbaik dicapai</div>
                    <div class="stat-icon"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Skor Terendah</div>
                    <div class="stat-value danger count-up" data-target="<?= $skor_terendah ?>">0</div>
                    <div class="stat-sub">perlu perhatian lebih</div>
                    <div class="stat-icon"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ★ STAT ROW 2: INTEGRITAS -->
    <div class="section-card reveal">
        <div class="section-tag">Integritas Ujian</div>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Indikasi Curang</div>
                    <div class="stat-value <?= $count_curang > 0 ? 'danger' : 'success' ?> count-up" data-target="<?= $count_curang ?>">0</div>
                    <div class="stat-sub"><?= $count_curang > 0 ? 'siswa ≥3x keluar tab' : 'Semua bersih ✓' ?></div>
                    <div class="stat-icon"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total Siswa Bersih</div>
                    <div class="stat-value success count-up" data-target="<?= $total_murid - $count_curang ?>">0</div>
                    <div class="stat-sub">tidak ada indikasi</div>
                    <div class="stat-icon"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Belum Mengerjakan</div>
                    <?php
                    // Hitung siswa yang ada di DB tapi skor 0 semua karena belum submit
                    // Berdasarkan data yang masuk saja
                    $belum = max(0, 0); // placeholder — bisa diisi dari jumlah total kelas
                    ?>
                    <div class="stat-value count-up" data-target="<?= $tidak_tuntas ?>">0</div>
                    <div class="stat-sub">siswa tidak tuntas</div>
                    <div class="stat-icon"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Peserta Hadir</div>
                    <div class="stat-value count-up" data-target="<?= $total_murid ?>">0</div>
                    <div class="stat-sub">telah submit kuis</div>
                    <div class="stat-icon"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                </div>
            </div>
        </div>

    <!-- ★ TOP 3 PODIUM -->
    <?php if($total_murid >= 1): ?>
    <div class="section-card reveal">
        <div class="section-tag">Hall of Fame</div>
        <h4 class="section-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Peringkat Teratas
        </h4>
        <div class="podium-wrap">
            <?php
            $podium_order = [1, 0, 2]; // tampilkan 2nd, 1st, 3rd
            $rank_classes = ['rank-2', 'rank-1', 'rank-3'];
            $rank_labels  = ['🥈', '🥇', '🥉'];
            $rank_nums    = [2, 1, 3];
            foreach ([0,1,2] as $pi):
                $ri = $podium_order[$pi];
                if (!isset($top3[$ri])) continue;
                $p = $top3[$ri];
                $initials = mb_strtoupper(mb_substr($p['nama_murid'], 0, 1));
                if ($p['skor'] >= 75) $bc = 'badge-gold';
                elseif ($p['skor'] >= 50) $bc = 'badge-yellow';
                else $bc = 'badge-red';
            ?>
            <div class="podium-item <?= $rank_classes[$pi] ?>">
                <div class="podium-avatar"><?= $initials ?></div>
                <div class="podium-name"><?= strtoupper(htmlspecialchars($p['nama_murid'])) ?></div>
                <div class="podium-score-badge"><?= $p['skor'] ?></div>
                <div class="podium-block"><?= $rank_labels[$pi] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- CHARTS -->
    <div class="section-card reveal">
        <div class="section-tag">Analisis Visual</div>
        <div class="row g-3">
            <!-- Bar Chart -->
            <div class="col-lg-8">
                <div class="chart-box">
                    <div class="chart-label">Analisis Jawaban per Soal</div>
                    <canvas id="chartBar" style="width:100%;height:240px;max-height:240px;"></canvas>
                    <p class="chart-hint">✦ Tap / klik batang untuk melihat detail siswa</p>
                </div>
            </div>
            <!-- Donut Chart -->
            <div class="col-lg-4">
                <div class="chart-box" style="display:flex;flex-direction:column;justify-content:center;">
                    <div class="chart-label">Distribusi Nilai</div>
                    <canvas id="chartDonut" style="width:100%;max-height:180px;"></canvas>
                    <div class="donut-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:var(--gold)"></div>
                            <span>≥75 (Tinggi) — <?= $count_tinggi ?> siswa</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:var(--yellow)"></div>
                            <span>50–74 (Sedang) — <?= $count_sedang ?> siswa</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:var(--red)"></div>
                            <span>&lt;50 (Rendah) — <?= $count_rendah ?> siswa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="section-card reveal">
        <div class="section-tag">Data Lengkap</div>
        <h4 class="section-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
            Rekap Seluruh Peserta
        </h4>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="dist-pills">
                <div class="dist-pill"><span class="pill-dot pill-gold"></span>≥75: <?= $count_tinggi ?></div>
                <div class="dist-pill"><span class="pill-dot pill-yellow"></span>50–74: <?= $count_sedang ?></div>
                <div class="dist-pill"><span class="pill-dot pill-red"></span>&lt;50: <?= $count_rendah ?></div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <input type="text" id="searchInput" class="search-input" placeholder="Cari nama atau absen…" aria-label="Cari peserta">
                <div class="slider-wrap">
                    <span>Min Skor</span>
                    <input type="range" id="minScore" min="0" max="100" value="0" step="5" aria-label="Filter skor minimum">
                    <span class="slider-val" id="scoreValue">0</span>
                </div>
            </div>
        </div>

        <?php if($total_murid > 0): ?>
        <div class="table-wrap">
            <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Absen</th>
                            <th>Nama Lengkap</th>
                            <th class="text-center">Benar</th>
                            <th class="text-center">Salah</th>
                            <th class="text-center">Skor</th>
                            <th class="text-center">Keluar Tab</th>
                            <th class="text-end">Waktu Submit</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                    <?php
                    foreach ($data_valid as $idx => $row):
                        $switches = isset($row['tab_switch_count']) ? (int)$row['tab_switch_count'] : 0;

                        if ($row['skor'] >= 80)      $rc = 'row-super';
                        elseif ($row['skor'] >= 70)  $rc = 'row-good';
                        elseif ($row['skor'] >= 50)  $rc = 'row-medium';
                        else                         $rc = 'row-poor';

                        // Override row class jika mencurigakan
                        if ($switches >= 3) $rc .= ' row-suspect';

                        if ($row['skor'] >= 75)      $bc = 'badge-gold';
                        elseif ($row['skor'] >= 50)  $bc = 'badge-yellow';
                        else                         $bc = 'badge-red';

                        // Tab badge class
                        if ($switches === 0)      $tc = 'clean';
                        elseif ($switches <= 2)   $tc = 'warn';
                        else                      $tc = 'danger';
                    ?>
                    <tr class="<?= $rc ?>"
                        style="animation-delay:<?= $idx * 0.04 ?>s"
                        data-skor="<?= $row['skor'] ?>"
                        data-nama="<?= strtolower(htmlspecialchars($row['nama_murid'])) ?>"
                        data-absen="<?= htmlspecialchars($row['no_absen']) ?>"
                        data-switches="<?= $switches ?>">
                        <td data-label="Absen">
                            <span class="absen-tag">#<?= sprintf("%02d", (int)$row['no_absen']) ?></span>
                        </td>
                        <td data-label="Nama" style="font-weight:600;color:var(--text)">
                            <?= strtoupper(htmlspecialchars($row['nama_murid'])) ?>
                            <?php if ($switches >= 3): ?>
                            <span class="suspect-icon" title="Indikasi mencurigakan">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Benar" class="text-md-center" style="color:#4ade80;font-weight:700;">
                            <?= $row['benar'] ?>
                        </td>
                        <td data-label="Salah" class="text-md-center" style="color:#ef4444;font-weight:700;">
                            <?= $row['salah'] ?>
                        </td>
                        <td data-label="Skor" class="text-md-center">
                            <span class="score-badge <?= $bc ?>"><?= $row['skor'] ?></span>
                        </td>
                        <td data-label="Keluar Tab" class="text-md-center">
                            <span class="tab-badge <?= $tc ?>">
                                <?php if ($switches === 0): ?>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Bersih
                                <?php elseif ($tc === 'warn'): ?>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <?= $switches ?>x
                                <?php else: ?>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    <?= $switches ?>x
                                <?php endif; ?>
                            </span>
                        </td>
                        <td data-label="Waktu Submit" class="text-md-end" style="font-size:0.82rem;color:var(--text-dim);">
                            <?= date('d M Y, H:i', strtotime($row['waktu_submit'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p>Belum ada peserta yang menyelesaikan kuis.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- BACK BUTTON -->
    <div class="text-center mt-4 reveal">
        <a href="index.php" class="btn-back">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <div class="footer reveal">
        <p>© 2026 AcehEdu Premium — Modul Sejarah Interaktif by ZM</p>
    </div>

</div><!-- /main-wrap -->

<!-- ★ MODAL DETAIL SOAL -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title-wrap">
                    <span class="modal-eyebrow">Analisis Soal</span>
                    <h5 class="modal-title" id="modalTitle">—</h5>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="modal-stat" id="modalStat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <span id="modalStatText">—</span>
                </div>
                <ul class="modal-list" id="modalList"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

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
    const ctx = canvas.getContext('2d');
    let stars = [];
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
    window.addEventListener('resize',()=>{resize();createStars(160);});
})();

/* ============================================================
   READING PROGRESS + BACK TO TOP
============================================================ */
window.addEventListener('scroll',()=>{
    const st = document.documentElement.scrollTop;
    const sh = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    document.getElementById('readingProgress').style.width = (st/sh*100)+'%';
    document.getElementById('backToTop').classList.toggle('show', st>300);
});
document.getElementById('backToTop').addEventListener('click',()=>window.scrollTo({top:0,behavior:'smooth'}));

/* ============================================================
   SCROLL REVEAL
============================================================ */
function checkReveal(){
    document.querySelectorAll('.reveal').forEach(el=>{
        if(el.getBoundingClientRect().top < window.innerHeight-80) el.classList.add('active');
    });
}
window.addEventListener('scroll',checkReveal); checkReveal();

/* ============================================================
   NUMBER COUNTERS
============================================================ */
function animateCounters(){
    document.querySelectorAll('.count-up').forEach(el=>{
        const target = +el.dataset.target;
        const dur = 1600; let start = null;
        const step = ts => {
            if(!start) start = ts;
            const p = Math.min((ts-start)/dur,1);
            el.textContent = Math.floor(p*target);
            if(p<1) requestAnimationFrame(step); else el.textContent = target;
        };
        requestAnimationFrame(step);
    });
    document.querySelectorAll('.count-up-float').forEach(el=>{
        const target = parseFloat(el.dataset.target);
        const dur = 1600; let start = null;
        const step = ts => {
            if(!start) start = ts;
            const p = Math.min((ts-start)/dur,1);
            el.textContent = (p*target).toFixed(1);
            if(p<1) requestAnimationFrame(step); else el.textContent = target.toFixed(1);
        };
        requestAnimationFrame(step);
    });
}
window.addEventListener('load', animateCounters);

/* ============================================================
   CHART DATA FROM PHP
============================================================ */
const jumlahBenar = <?= json_encode(array_values($jawaban_benar_per_soal)) ?>;
const detailPerSoal = <?php
    $js = [];
    foreach($detail_per_soal as $soal => $siswa)
        $js[$soal] = array_slice($siswa, 0, 15);
    echo json_encode($js);
?>;

/* ============================================================
   ★ BAR CHART
============================================================ */
const ctxBar = document.getElementById('chartBar').getContext('2d');
const barChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: <?php $labs=[]; for($i=1;$i<=20;$i++) $labs[]="S$i"; echo json_encode($labs); ?>,
        datasets: [{
            label: 'Jawaban Benar',
            data: jumlahBenar,
            backgroundColor: 'rgba(201,168,76,0.55)',
            hoverBackgroundColor: '#f0d080',
            borderColor: 'rgba(201,168,76,0.9)',
            borderWidth: 1,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(201,168,76,0.06)' },
                ticks: { color: '#8a7f70', stepSize: 5, font: { family: 'Inter', size: 11 } }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#8a7f70', font: { family: 'Inter', size: 10 } }
            }
        },
        onClick: (e, items) => {
            if(!items.length) return;
            const idx = items[0].index + 1;
            const jml = jumlahBenar[idx-1];
            const list = detailPerSoal[idx] || [];

            document.getElementById('modalTitle').textContent = `Soal Nomor ${idx}`;
            document.getElementById('modalStatText').textContent = `${jml} peserta menjawab benar`;

            const ul = document.getElementById('modalList');
            ul.innerHTML = '';
            if(list.length > 0){
                list.forEach(s => {
                    ul.innerHTML += `<li>
                        <span class="absen-mini">#${String(s.absen).padStart(2,'0')}</span>
                        ${s.nama.toUpperCase()}
                    </li>`;
                });
                if(jml > list.length)
                    ul.innerHTML += `<li style="color:var(--text-dim);font-style:italic;justify-content:center;">...dan ${jml-list.length} lainnya</li>`;
            } else {
                ul.innerHTML = '<li style="color:var(--red);font-style:italic;justify-content:center;">Tidak ada yang menjawab benar.</li>';
            }
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }
    }
});

/* ============================================================
   ★ DONUT CHART
============================================================ */
const ctxDonut = document.getElementById('chartDonut').getContext('2d');
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: ['Tinggi (≥75)', 'Sedang (50-74)', 'Rendah (<50)'],
        datasets: [{
            data: [<?= $count_tinggi ?>, <?= $count_sedang ?>, <?= $count_rendah ?>],
            backgroundColor: ['rgba(201,168,76,0.75)', 'rgba(250,204,21,0.65)', 'rgba(239,68,68,0.65)'],
            borderColor: ['#c9a84c', '#facc15', '#ef4444'],
            borderWidth: 1,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.raw} siswa`
                }
            }
        }
    }
});

/* ============================================================
   SEARCH + FILTER
============================================================ */
const searchInput  = document.getElementById('searchInput');
const minScore     = document.getElementById('minScore');
const scoreValue   = document.getElementById('scoreValue');
const tableRows    = document.querySelectorAll('#tableBody tr');

function filterTable(){
    const term = searchInput.value.toLowerCase().trim();
    const minVal = parseInt(minScore.value) || 0;
    scoreValue.textContent = minVal;
    tableRows.forEach(row => {
        const skor    = parseInt(row.dataset.skor);
        const nama    = row.dataset.nama;
        const absen   = row.dataset.absen;
        const match   = (!term || nama.includes(term) || absen.includes(term)) && skor >= minVal;
        row.style.display = match ? '' : 'none';
    });
}
searchInput.addEventListener('input', filterTable);
minScore.addEventListener('input', filterTable);
</script>
</body>
</html>
