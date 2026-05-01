<?php 
// require_once 'config/koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kesultanan Aceh — AcehEdu Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    :root {
        --gold: #c9a84c;
        --gold-light: #f0d080;
        --gold-dim: rgba(201,168,76,0.12);
        --gold-glow: rgba(201,168,76,0.45);
        --ink: #08090d;
        --ink-2: #111318;
        --surface: rgba(255,255,255,0.03);
        --border: rgba(201,168,76,0.18);
        --text: #e8e0d0;
        --text-dim: #8a7f70;
        --red: #c0392b;
        --red-glow: rgba(192,57,43,0.4);
        --radius: 20px;
        --font-display: 'Cinzel', serif;
        --font-body: 'Inter', sans-serif;
    }
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; overflow-x: hidden; }
    body { font-family: var(--font-body); background: var(--ink); color: var(--text); line-height: 1.75; overflow-x: hidden; }

    /* STAR FIELD */
    #starCanvas { position: fixed; top:0; left:0; width:100vw; height:100vh; z-index:0; pointer-events:none; }
    body::before {
        content:''; position:fixed; inset:0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
        z-index:0; pointer-events:none; opacity:0.6;
    }

    /* SHOOTING STAR CANVAS */
    #shootCanvas { position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:0; pointer-events:none; }

    /* NAVBAR */
    .navbar { position:fixed; top:0; left:0; right:0; z-index:1000; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; background:rgba(8,9,13,0.85); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border-bottom:1px solid var(--border); }
    .nav-brand { font-family:var(--font-display); font-size:1.2rem; font-weight:700; color:var(--gold); text-decoration:none; letter-spacing:2px; text-shadow:0 0 20px var(--gold-glow); }
    .nav-menu-btn { background:none; border:1px solid var(--border); border-radius:10px; padding:8px 12px; cursor:pointer; color:var(--text); transition:all 0.3s; }
    .nav-menu-btn:hover { border-color:var(--gold); color:var(--gold); }
    .nav-spacer { width:44px; }
    #readingProgress { position:fixed; top:0; left:0; height:3px; width:0%; background:linear-gradient(90deg, var(--gold), var(--gold-light)); z-index:1100; box-shadow:0 0 12px var(--gold-glow); transition:width 0.1s linear; border-radius:0 2px 2px 0; }

    /* SIDEBAR */
    .offcanvas { background:rgba(8,9,13,0.97)!important; backdrop-filter:blur(30px); border-right:1px solid var(--border)!important; max-width:280px; }
    .offcanvas-header { border-bottom:1px solid var(--border); padding:24px; }
    .offcanvas-title { font-family:var(--font-display); color:var(--gold); font-size:0.8rem; letter-spacing:3px; text-transform:uppercase; }
    .sidebar-link { display:flex; align-items:center; gap:14px; padding:14px 24px; color:var(--text-dim); text-decoration:none; font-size:0.9rem; font-weight:500; border-left:3px solid transparent; transition:all 0.3s; margin:2px 0; }
    .sidebar-link:hover, .sidebar-link.active { color:var(--gold); border-left-color:var(--gold); background:var(--gold-dim); }
    .sidebar-link svg { flex-shrink:0; }

    /* MAIN WRAP */
    .main-wrap { position:relative; z-index:1; padding-top:80px; max-width:860px; margin:0 auto; padding-left:20px; padding-right:20px; padding-bottom:80px; }

    /* HERO */
    .hero { text-align:center; padding:60px 0 40px; position:relative; }
    .hero-emblem { width:90px; height:90px; margin:0 auto 28px; position:relative; }
    .hero-emblem svg { width:100%; height:100%; }
    .emblem-ring { position:absolute; inset:-10px; border:1px solid var(--border); border-radius:50%; animation:spinRing 20s linear infinite; }
    .emblem-ring::before { content:''; position:absolute; top:-3px; left:50%; width:6px; height:6px; background:var(--gold); border-radius:50%; transform:translateX(-50%); box-shadow:0 0 10px var(--gold-glow); }
    /* Second ring - counter rotate */
    .emblem-ring-2 { position:absolute; inset:-22px; border:1px dashed rgba(201,168,76,0.15); border-radius:50%; animation:spinRing 35s linear infinite reverse; }
    @keyframes spinRing { to { transform:rotate(360deg); } }
    .hero-year { font-family:var(--font-display); font-size:0.75rem; letter-spacing:6px; color:var(--gold); text-transform:uppercase; margin-bottom:16px; opacity:0.8; }
    .hero-title { font-family:var(--font-display); font-size:clamp(2.4rem, 6vw, 4rem); font-weight:900; line-height:1.1; margin-bottom:16px; background:linear-gradient(180deg, #f5edd8 0%, var(--gold) 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .hero-sub { font-size:1rem; color:var(--text-dim); font-weight:300; letter-spacing:1px; margin-bottom:40px; }

    /* HERO STATS */
    .hero-stats { display:flex; justify-content:center; gap:0; flex-wrap:nowrap; margin:0 auto; max-width:560px; border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; background:var(--surface); }
    .stat-item { flex:1; padding:18px 10px; text-align:center; border-right:1px solid var(--border); position:relative; }
    .stat-item:last-child { border-right:none; }
    .stat-num { font-family:var(--font-display); font-size:1.5rem; color:var(--gold); font-weight:700; display:block; line-height:1; margin-bottom:4px; }
    .stat-label { font-size:0.7rem; color:var(--text-dim); letter-spacing:1px; text-transform:uppercase; }

    /* ★ NEW: GLOWING PARTICLE CURSOR TRAIL (canvas-based, handled in JS) */

    /* ORNAMENT */
    .ornament { text-align:center; margin:40px 0; color:var(--gold); opacity:0.4; font-size:1.2rem; letter-spacing:12px; font-family:var(--font-display); }

    /* SECTION CARD */
    .section-card { background:linear-gradient(145deg, rgba(20,18,14,0.9), rgba(8,9,13,0.95)); border:1px solid var(--border); border-radius:var(--radius); padding:36px; margin-bottom:28px; position:relative; overflow:hidden; transition:border-color 0.4s, box-shadow 0.4s; }
    .section-card::before { content:''; position:absolute; top:0; left:0; right:0; height:1px; background:linear-gradient(90deg, transparent, var(--gold), transparent); opacity:0.5; }
    .section-card:hover { border-color:rgba(201,168,76,0.35); box-shadow:0 20px 60px -20px rgba(0,0,0,0.8), 0 0 40px -20px var(--gold-glow); }
    .section-card.danger-card { border-color:rgba(192,57,43,0.25); }
    .section-card.danger-card::before { background:linear-gradient(90deg, transparent, var(--red), transparent); }
    .section-card.danger-card:hover { border-color:rgba(192,57,43,0.45); box-shadow:0 20px 60px -20px rgba(0,0,0,0.8), 0 0 40px -20px var(--red-glow); }
    .section-card::after { content:''; position:absolute; top:12px; right:12px; width:30px; height:30px; border-top:1px solid var(--border); border-right:1px solid var(--border); border-radius:0 6px 0 0; opacity:0.5; }

    .section-tag { font-family:var(--font-display); font-size:0.65rem; letter-spacing:4px; color:var(--gold); text-transform:uppercase; margin-bottom:20px; display:inline-flex; align-items:center; gap:10px; opacity:0.75; }
    .section-tag::before { content:''; display:inline-block; width:20px; height:1px; background:var(--gold); }
    .section-tag.danger { color:#e05a4a; }
    .section-tag.danger::before { background:#e05a4a; }

    .section-title { font-family:var(--font-display); font-size:clamp(1.3rem, 3vw, 1.7rem); font-weight:700; color:var(--text); margin-bottom:18px; display:flex; align-items:center; gap:14px; }
    .section-title svg { color:var(--gold); flex-shrink:0; }
    .section-title.danger { color:#e8a09a; }
    .section-title.danger svg { color:#e05a4a; }

    .body-text { font-size:0.95rem; color:#b0a898; line-height:1.85; margin-bottom:24px; }
    .body-text strong { color:var(--gold); font-weight:600; }

    /* FEAT ITEMS */
    .feat-item { background:rgba(255,255,255,0.025); border:1px solid var(--border); border-radius:14px; padding:22px; height:100%; transition:all 0.35s; }
    .feat-item:hover { background:var(--gold-dim); border-color:rgba(201,168,76,0.4); transform:translateY(-4px); box-shadow:0 12px 30px -10px rgba(0,0,0,0.6); }
    .feat-item strong { display:block; font-family:var(--font-display); font-size:0.85rem; color:var(--gold); margin-bottom:8px; letter-spacing:0.5px; }
    .feat-item p { font-size:0.88rem; color:var(--text-dim); margin:0; line-height:1.7; }
    .feat-item.feat-danger { border-left:3px solid var(--red); }
    .feat-item.feat-danger:hover { background:rgba(192,57,43,0.06); border-color:rgba(192,57,43,0.4); }
    .feat-item.feat-danger strong { color:#e05a4a; }

    /* ★ NEW: ANIMATED COUNTER on hero stats */
    .stat-num.counting { animation: goldFlicker 0.1s ease infinite; }
    @keyframes goldFlicker { 0%,100%{opacity:1} 50%{opacity:0.7} }

    /* ★ NEW: KNOWLEDGE METER */
    .knowledge-meter { margin-top:8px; }
    .km-item { margin-bottom:18px; }
    .km-label { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:0.88rem; color:var(--text); font-weight:500; }
    .km-pct { font-family:var(--font-display); font-size:0.75rem; color:var(--gold); }
    .km-track { height:6px; background:rgba(255,255,255,0.05); border-radius:100px; overflow:hidden; border:1px solid var(--border); }
    .km-fill { height:100%; border-radius:100px; background:linear-gradient(90deg, var(--gold), var(--gold-light)); width:0%; box-shadow:0 0 10px var(--gold-glow); transition:width 1.5s cubic-bezier(0.16,1,0.3,1); }

    /* ★ NEW: RIPPLE EFFECT on feat-item click */
    .feat-item { position:relative; overflow:hidden; }
    .ripple { position:absolute; border-radius:50%; background:rgba(201,168,76,0.25); transform:scale(0); animation:rippleAnim 0.6s linear; pointer-events:none; }
    @keyframes rippleAnim { to { transform:scale(4); opacity:0; } }

    /* PREMIUM TABLE */
    .table-wrap { border-radius:14px; overflow:hidden; border:1px solid var(--border); margin-top:20px; }
    .table { margin:0; font-size:0.9rem; --bs-table-bg:transparent; --bs-table-color:var(--text); --bs-table-hover-bg:var(--gold-dim); }
    .table thead th { background:rgba(201,168,76,0.08)!important; color:var(--gold)!important; font-family:var(--font-display); font-size:0.72rem; letter-spacing:2px; text-transform:uppercase; border-bottom:1px solid var(--border)!important; padding:16px 20px; font-weight:600; }
    .table tbody td { padding:16px 20px; border-color:var(--border)!important; color:#b0a898; vertical-align:middle; transition:color 0.3s; }
    .table tbody tr:hover td { color:var(--text); }
    .table tbody td strong { color:var(--gold); font-weight:600; }
    .table tbody tr:last-child td { border-bottom:none!important; }

    /* ★ NEW: TABLE ROW SLIDE-IN */
    .table tbody tr { opacity:0; transform:translateX(-20px); transition:opacity 0.5s, transform 0.5s; }
    .table tbody tr.row-visible { opacity:1; transform:translateX(0); }

    /* HERITAGE CARDS */
    .heritage-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:28px 20px; text-align:center; height:100%; transition:all 0.4s; position:relative; overflow:hidden; }
    .heritage-card::after { content:''; position:absolute; bottom:0; left:0; right:0; height:2px; background:linear-gradient(90deg, transparent, var(--gold), transparent); opacity:0; transition:opacity 0.4s; }
    .heritage-card:hover { background:var(--gold-dim); transform:translateY(-6px); box-shadow:0 20px 40px -15px rgba(0,0,0,0.7); }
    .heritage-card:hover::after { opacity:1; }
    .heritage-icon { width:52px; height:52px; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:14px; background:rgba(201,168,76,0.07); transition:all 0.4s; }
    .heritage-card:hover .heritage-icon { border-color:var(--gold); background:var(--gold-dim); box-shadow:0 0 20px var(--gold-glow); }
    .heritage-card strong { display:block; font-family:var(--font-display); font-size:0.92rem; color:var(--text); margin-bottom:8px; letter-spacing:0.5px; }
    .heritage-card p { font-size:0.83rem; color:var(--text-dim); margin:0; line-height:1.65; }

    /* ★ NEW: HERITAGE CARD TILT (handled via JS mousemove) */

    /* CTA */
    .cta-wrap { text-align:center; margin-top:50px; }
    .btn-cta { display:inline-flex; align-items:center; gap:14px; background:transparent; color:var(--gold); border:1px solid var(--gold); font-family:var(--font-display); font-size:0.85rem; letter-spacing:3px; text-transform:uppercase; padding:18px 50px; border-radius:100px; text-decoration:none; transition:all 0.4s; position:relative; overflow:hidden; animation:pulseCta 3s infinite; }
    .btn-cta::before { content:''; position:absolute; inset:0; background:var(--gold); transform:scaleX(0); transform-origin:left; transition:transform 0.4s cubic-bezier(0.16,1,0.3,1); }
    .btn-cta:hover { color:var(--ink); box-shadow:0 0 40px var(--gold-glow); animation:none; }
    .btn-cta:hover::before { transform:scaleX(1); }
    .btn-cta span, .btn-cta svg { position:relative; z-index:1; transition:color 0.4s; }
    .btn-cta:hover svg, .btn-cta:hover span { color:var(--ink); }
    @keyframes pulseCta { 0%,100%{box-shadow:0 0 0 0 var(--gold-glow)} 50%{box-shadow:0 0 0 10px rgba(201,168,76,0)} }

    /* FOOTER */
    .footer { text-align:center; padding:40px 0 0; border-top:1px solid var(--border); margin-top:50px; font-size:0.82rem; color:var(--text-dim); font-weight:400; letter-spacing:0.5px; }

    /* BACK TO TOP */
    #backToTop { position:fixed; bottom:28px; right:28px; width:48px; height:48px; border-radius:50%; background:var(--ink-2); border:1px solid var(--border); color:var(--gold); cursor:pointer; opacity:0; visibility:hidden; transition:all 0.4s; z-index:999; display:flex; align-items:center; justify-content:center; }
    #backToTop.show { opacity:1; visibility:visible; }
    #backToTop:hover { background:var(--gold-dim); border-color:var(--gold); transform:translateY(-4px); box-shadow:0 8px 20px var(--gold-glow); }

    /* SCROLL REVEAL */
    .reveal { opacity:0; transform:translateY(36px); transition:opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1); }
    .reveal.active { opacity:1; transform:translateY(0); }
    .reveal-delay-1 { transition-delay:0.1s; }
    .reveal-delay-2 { transition-delay:0.2s; }
    .reveal-delay-3 { transition-delay:0.3s; }

    /* ★ NEW: MAGNETIC HOVER on section cards (subtle float toward cursor via JS) */

    /* ★ NEW: GOLD SCAN LINE on section card hover */
    .section-card .scan-line { position:absolute; left:0; right:0; height:1px; background:linear-gradient(90deg, transparent, var(--gold-light), transparent); opacity:0; top:0; transition:none; pointer-events:none; }
    .section-card:hover .scan-line { animation:scanDown 0.8s ease forwards; }
    @keyframes scanDown { 0%{top:0;opacity:0.6} 100%{top:100%;opacity:0} }

    /* ★ TUGAS BADGE in NAV */
    .nav-tugas-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border:1px solid rgba(201,168,76,0.35); border-radius:100px; font-family:var(--font-display); font-size:0.6rem; letter-spacing:3px; color:var(--gold); text-transform:uppercase; background:rgba(201,168,76,0.06); white-space:nowrap; }
    .nav-tugas-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--gold); box-shadow:0 0 8px var(--gold-glow); animation:pulseDot 2s ease-in-out infinite; }
    @keyframes pulseDot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.7)} }

    /* ★ TUGAS CARD — official document style */
    .tugas-card { position:relative; border-radius:var(--radius); overflow:hidden; margin-bottom:28px; background:linear-gradient(145deg, rgba(25,20,10,0.95), rgba(12,10,6,0.97)); }
    .tugas-card-outer { padding:3px; border-radius:calc(var(--radius) + 3px); background:linear-gradient(135deg, rgba(201,168,76,0.5), rgba(201,168,76,0.1) 40%, rgba(201,168,76,0.4) 60%, rgba(201,168,76,0.1)); }
    .tugas-card-inner { background:linear-gradient(145deg, rgba(22,18,10,0.97), rgba(8,9,13,0.98)); border-radius:var(--radius); padding:40px 36px; position:relative; overflow:hidden; }
    .tugas-card-inner::before { content:''; position:absolute; inset:8px; border:1px dashed rgba(201,168,76,0.12); border-radius:14px; pointer-events:none; }
    .tugas-corner { position:absolute; width:40px; height:40px; }
    .tugas-corner.tl { top:16px; left:16px; border-top:1px solid rgba(201,168,76,0.45); border-left:1px solid rgba(201,168,76,0.45); border-radius:4px 0 0 0; }
    .tugas-corner.tr { top:16px; right:16px; border-top:1px solid rgba(201,168,76,0.45); border-right:1px solid rgba(201,168,76,0.45); border-radius:0 4px 0 0; }
    .tugas-corner.bl { bottom:16px; left:16px; border-bottom:1px solid rgba(201,168,76,0.45); border-left:1px solid rgba(201,168,76,0.45); border-radius:0 0 0 4px; }
    .tugas-corner.br { bottom:16px; right:16px; border-bottom:1px solid rgba(201,168,76,0.45); border-right:1px solid rgba(201,168,76,0.45); border-radius:0 0 4px 0; }
    .tugas-seal { position:absolute; top:30px; right:36px; width:80px; height:80px; opacity:0.18; }
    .tugas-seal svg { width:100%; height:100%; }
    .tugas-header { display:flex; align-items:flex-start; gap:20px; margin-bottom:28px; }
    .tugas-icon-wrap { flex-shrink:0; width:56px; height:56px; border:1px solid rgba(201,168,76,0.3); border-radius:14px; display:flex; align-items:center; justify-content:center; background:rgba(201,168,76,0.06); }
    .tugas-label-sup { font-family:var(--font-display); font-size:0.6rem; letter-spacing:4px; color:var(--gold); text-transform:uppercase; opacity:0.7; margin-bottom:6px; }
    .tugas-title-main { font-family:var(--font-display); font-size:1.2rem; font-weight:700; color:var(--text); line-height:1.3; }
    .tugas-title-main span { color:var(--gold); }
    .tugas-divider { height:1px; background:linear-gradient(90deg, transparent, rgba(201,168,76,0.3), transparent); margin:24px 0; }
    .tugas-grid { display:grid; grid-template-columns:repeat(2, 1fr); gap:16px; }
    @media(min-width:640px) { .tugas-grid { grid-template-columns:repeat(4,1fr); } }
    .tugas-field { }
    .tugas-field-label { font-size:0.62rem; letter-spacing:2px; text-transform:uppercase; color:var(--text-dim); margin-bottom:6px; font-weight:500; }
    .tugas-field-value { font-size:0.9rem; color:var(--text); font-weight:500; line-height:1.4; }
    .tugas-field-value strong { color:var(--gold); font-weight:600; }
    .tugas-kata-pengantar { margin-top:24px; padding:18px 20px; background:rgba(201,168,76,0.04); border-left:2px solid rgba(201,168,76,0.3); border-radius:0 10px 10px 0; font-size:0.88rem; color:#a09880; line-height:1.8; font-style:italic; }
    .tugas-kata-pengantar cite { display:block; margin-top:10px; font-style:normal; font-size:0.78rem; color:var(--text-dim); letter-spacing:1px; }

    /* MOBILE */
    @media (max-width: 768px) {
        .section-card { padding:24px 18px; }
        .hero { padding:40px 0 30px; }
        .hero-stats { max-width:100%; }
        .stat-num { font-size:1.2rem; }
        .btn-cta { padding:16px 30px; font-size:0.78rem; letter-spacing:2px; width:100%; justify-content:center; }
        .table-wrap { background:transparent; border:none; }
        .table-responsive { overflow:visible; }
        .table thead { display:none; }
        .table tbody tr { display:block; margin-bottom:16px; border:1px solid var(--border)!important; border-radius:12px; padding:14px; background:rgba(15,12,8,0.6); }
        .table tbody td { display:flex; flex-direction:column; padding:8px 0; border-bottom:1px dashed rgba(255,255,255,0.06)!important; }
        .table tbody td:last-child { border-bottom:none!important; }
        .table tbody td::before { content:attr(data-label); font-size:0.68rem; text-transform:uppercase; letter-spacing:1.5px; color:var(--gold); font-weight:700; margin-bottom:4px; opacity:0.7; }
    }
    </style>
</head>
<body>

<canvas id="starCanvas"></canvas>
<canvas id="shootCanvas"></canvas>
<div id="readingProgress"></div>

<!-- NAVBAR -->
<nav class="navbar">
    <button class="nav-menu-btn" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-label="Buka menu">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <a class="nav-brand" href="index.php">Aceh<span style="color:#f0d080">Edu</span></a>
    <div class="nav-tugas-badge">Tugas Sejarah</div>
</nav>

<!-- SIDEBAR -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header">
        <span class="offcanvas-title">Menu Utama</span>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body p-0 mt-2">
        <a class="sidebar-link active" href="index.php">
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
        <a class="sidebar-link" href="hasil.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Hasil & Review
        </a>
    </div>
</div>

<!-- MAIN -->
<div class="main-wrap">

    <!-- HERO -->
    <div class="hero reveal active">
        <div class="hero-emblem">
            <div class="emblem-ring-2"></div>
            <div class="emblem-ring"></div>
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="40" cy="40" r="32" stroke="rgba(201,168,76,0.3)" stroke-width="1"/>
                <circle cx="40" cy="40" r="22" stroke="rgba(201,168,76,0.5)" stroke-width="1"/>
                <path d="M40 18 L44 30 L40 26 L36 30 Z" fill="rgba(201,168,76,0.8)"/>
                <path d="M40 62 L44 50 L40 54 L36 50 Z" fill="rgba(201,168,76,0.8)"/>
                <path d="M18 40 L30 44 L26 40 L30 36 Z" fill="rgba(201,168,76,0.8)"/>
                <path d="M62 40 L50 44 L54 40 L50 36 Z" fill="rgba(201,168,76,0.8)"/>
                <circle cx="40" cy="40" r="6" fill="rgba(201,168,76,0.6)" stroke="rgba(201,168,76,0.9)" stroke-width="1"/>
                <circle cx="40" cy="40" r="2" fill="#c9a84c"/>
            </svg>
        </div>
        <div class="hero-year">1496 — 1903</div>
        <h1 class="hero-title">Kesultanan Aceh</h1>
        <p class="hero-sub">Pusat Peradaban dan Perjuangan Islam Nusantara</p>
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-num" data-target="407">0</span>
                <span class="stat-label">Tahun</span>
            </div>
            <div class="stat-item">
                <span class="stat-num" data-target="35">0</span>
                <span class="stat-label">Sultan</span>
            </div>
            <div class="stat-item">
                <span class="stat-num" data-target="1607">0</span>
                <span class="stat-label">Puncak</span>
            </div>
            <div class="stat-item">
                <span class="stat-num" data-target="5">0</span>
                <span class="stat-label">Bagian</span>
            </div>
        </div>
    </div>

    <div class="ornament reveal">· · ✦ · ·</div>

    <!-- KETERANGAN TUGAS -->
    <div class="tugas-card-outer reveal">
        <div class="tugas-card-inner">
            <div class="tugas-corner tl"></div>
            <div class="tugas-corner tr"></div>
            <div class="tugas-corner bl"></div>
            <div class="tugas-corner br"></div>
            <!-- Watermark Seal -->
            <div class="tugas-seal">
                <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="46" stroke="#c9a84c" stroke-width="1.5" stroke-dasharray="4 3"/>
                    <circle cx="50" cy="50" r="36" stroke="#c9a84c" stroke-width="1"/>
                    <path d="M50 14 L53.5 24 L50 21 L46.5 24 Z" fill="#c9a84c"/>
                    <path d="M50 86 L53.5 76 L50 79 L46.5 76 Z" fill="#c9a84c"/>
                    <path d="M14 50 L24 46.5 L21 50 L24 53.5 Z" fill="#c9a84c"/>
                    <path d="M86 50 L76 46.5 L79 50 L76 53.5 Z" fill="#c9a84c"/>
                    <circle cx="50" cy="50" r="8" stroke="#c9a84c" stroke-width="1.5" fill="rgba(201,168,76,0.15)"/>
                    <circle cx="50" cy="50" r="3" fill="#c9a84c"/>
                </svg>
            </div>
            <div class="tugas-header">
                <div class="tugas-icon-wrap">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div>
                    <div class="tugas-label-sup">Dokumen Akademik · Keterangan Resmi</div>
                    <div class="tugas-title-main">Disusun untuk Memenuhi<br><span>Tugas Mata Pelajaran Sejarah</span></div>
                </div>
            </div>
            <div class="tugas-divider"></div>
            <div class="tugas-grid">
                <div class="tugas-field">
                    <div class="tugas-field-label">Mata Pelajaran</div>
                    <div class="tugas-field-value"><strong>Sejarah Indonesia</strong></div>
                </div>
                <div class="tugas-field">
                    <div class="tugas-field-label">Topik / Tema</div>
                    <div class="tugas-field-value">Kesultanan Aceh Darussalam</div>
                </div>
                <div class="tugas-field">
                    <div class="tugas-field-label">Jenjang / Kelas</div>
                    <div class="tugas-field-value"><strong>SMA / X-7</strong></div>
                </div>
                <div class="tugas-field">
                    <div class="tugas-field-label">Tahun Ajaran</div>
                    <div class="tugas-field-value">2025 / 2026</div>
                </div>
            </div>
            <div class="tugas-divider"></div>
            <div class="tugas-kata-pengantar">
                Modul digital interaktif ini disusun sebagai pemenuhan tugas akademik sejarah, sekaligus sebagai media pembelajaran yang menyajikan informasi tentang Kesultanan Aceh Darussalam secara komprehensif, dan berbeda dari lainnya, meliputi sejarah berdirinya, masa kejayaan, tokoh-tokoh penting, hingga peninggalan bersejarah yang masih dapat kita saksikan hingga kini.
                <cite>— Disusun dengan penuh tanggung jawab · Ahmad Ali Harozim · 2026</cite>
            </div>
        </div>
    </div>
    <div class="ornament reveal" style="margin:20px 0 28px;">· · ✦ · ·</div>

    <div class="section-card reveal">
        <div class="scan-line"></div>
        <div class="section-tag">Bagian I</div>
        <h4 class="section-title">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            Awal Berdiri
        </h4>
        <p class="body-text">Didirikan pada tahun <strong>1496</strong> oleh <strong>Sultan Ali Mughayat Syah</strong>. Terbentuk dari penyatuan beberapa kerajaan kecil di wilayah Aceh, seperti Lamuri dan Aceh Darul Kamal.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="feat-item">
                    <strong>🌍 Konteks Sejarah</strong>
                    <p>Jatuhnya Malaka (1511) ke Portugis mendorong pedagang beralih ke pelabuhan Aceh, menjadikannya pusat perdagangan alternatif yang sangat kuat.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feat-item">
                    <strong>🏛️ Ibu Kota Strategis</strong>
                    <p>Kutaraja (Banda Aceh) dipilih menjadi pusat pemerintahan dengan pelabuhan alami yang aman dari serangan dan badai.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- BAGIAN II: MASA KEJAYAAN -->
    <div class="section-card reveal">
        <div class="scan-line"></div>
        <div class="section-tag">Bagian II</div>
        <h4 class="section-title">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
            Masa Kejayaan
        </h4>
        <p class="body-text">Puncak kejayaan diraih di bawah kepemimpinan <strong>Sultan Iskandar Muda (1607–1636)</strong>, yang membawa Aceh menjadi kekuatan militer dan ekonomi yang disegani bangsa Eropa.</p>
        <div class="row g-3">
            <div class="col-sm-6 col-lg-3"><div class="feat-item text-center"><strong>🗺️ Wilayah</strong><p>Membentang di Sumatera & Semenanjung Malaya</p></div></div>
            <div class="col-sm-6 col-lg-3"><div class="feat-item text-center"><strong>💰 Ekonomi</strong><p>Pusat monopoli perdagangan lada dunia</p></div></div>
            <div class="col-sm-6 col-lg-3"><div class="feat-item text-center"><strong>⚓ Militer</strong><p>Armada laut terkuat di Nusantara saat itu</p></div></div>
            <div class="col-sm-6 col-lg-3"><div class="feat-item text-center"><strong>🕋 Budaya</strong><p>Mendapat julukan mulia "Serambi Mekkah"</p></div></div>
        </div>
        <!-- KNOWLEDGE METER -->
        <div class="knowledge-meter mt-4 pt-4" style="border-top:1px solid var(--border);">
            <p style="font-size:0.78rem;color:var(--text-dim);letter-spacing:2px;text-transform:uppercase;margin-bottom:16px;font-family:var(--font-display);">Indikator Kekuatan Kerajaan</p>
            <div class="km-item">
                <div class="km-label"><span>Kekuatan Militer</span><span class="km-pct">92%</span></div>
                <div class="km-track"><div class="km-fill" data-width="92"></div></div>
            </div>
            <div class="km-item">
                <div class="km-label"><span>Kekuatan Ekonomi</span><span class="km-pct">88%</span></div>
                <div class="km-track"><div class="km-fill" data-width="88"></div></div>
            </div>
            <div class="km-item">
                <div class="km-label"><span>Pengaruh Diplomatik</span><span class="km-pct">78%</span></div>
                <div class="km-track"><div class="km-fill" data-width="78"></div></div>
            </div>
            <div class="km-item" style="margin-bottom:0">
                <div class="km-label"><span>Kekuatan Budaya & Ilmu</span><span class="km-pct">85%</span></div>
                <div class="km-track"><div class="km-fill" data-width="85"></div></div>
            </div>
        </div>
    </div>

    <!-- BAGIAN III: MASA AKHIR -->
    <div class="section-card danger-card reveal">
        <div class="scan-line"></div>
        <div class="section-tag danger">Bagian III</div>
        <h4 class="section-title danger">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#e05a4a" stroke-width="2" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Masa Akhir & Kemunduran
        </h4>
        <p class="body-text">Kemunduran perlahan dimulai setelah wafatnya Sultan Iskandar Muda (1636), diperparah oleh intrik politik internal dan intervensi asing yang tak henti.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="feat-item feat-danger">
                    <strong>🏛️ Konflik Internal</strong>
                    <p>Perebutan kekuasaan antara kaum bangsawan (Teuku) dan ulama (Tengku) yang melemahkan stabilitas pemerintahan dari dalam.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feat-item feat-danger">
                    <strong>⚔️ Tekanan Eksternal</strong>
                    <p>Agresi militer Belanda memicu Perang Aceh (1873–1904) yang menguras sumber daya dan sangat menghancurkan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- BAGIAN IV: SULTAN PENTING (TABLE) — renumbered -->
    <div class="section-card reveal">
        <div class="scan-line"></div>
        <div class="section-tag">Bagian IV</div>
        <h4 class="section-title">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Tokoh & Sultan Penting
        </h4>
        <div class="table-wrap">
            <div class="table-responsive">
                <table class="table" id="sultanTable">
                    <thead>
                        <tr>
                            <th style="width:38%">Nama Sultan</th>
                            <th>Kontribusi Utama</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td data-label="Nama Sultan"><strong>Ali Mughayat Syah</strong></td><td data-label="Kontribusi" style="color:var(--text-dim)">Pendiri dan penyatu kerajaan-kerajaan Aceh (1496–1530)</td></tr>
                        <tr><td data-label="Nama Sultan"><strong>Alaudin Riayat Syah</strong></td><td data-label="Kontribusi" style="color:var(--text-dim)">Menjalin hubungan diplomatik & militer dengan Turki Utsmaniyah (1537–1571)</td></tr>
                        <tr><td data-label="Nama Sultan"><strong>Iskandar Muda</strong></td><td data-label="Kontribusi" style="color:var(--text-dim)">Membawa Aceh menjadi kekaisaran maritim terkuat di Nusantara (1607–1636)</td></tr>
                        <tr><td data-label="Nama Sultan"><strong>Iskandar Thani</strong></td><td data-label="Kontribusi" style="color:var(--text-dim)">Mempromosikan hukum Islam, ekonomi, dan melahirkan karya sastra besar (1636–1641)</td></tr>
                        <tr><td data-label="Nama Sultan"><strong>Muhammad Daud Syah</strong></td><td data-label="Kontribusi" style="color:var(--text-dim)">Sultan terakhir Aceh yang bergerilya melawan kolonial Belanda (1874–1903)</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BAGIAN V: PENINGGALAN -->
    <div class="section-card reveal">
        <div class="scan-line"></div>
        <div class="section-tag">Bagian V</div>
        <h4 class="section-title">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            Peninggalan Bersejarah
        </h4>
        <div class="row g-3 mt-1">
            <div class="col-md-4">
                <div class="heritage-card">
                    <div class="heritage-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
                    <strong>Masjid Baiturrahman</strong>
                    <p>Simbol keteguhan iman rakyat Aceh, dibangun megah oleh Sultan Iskandar Muda pada abad ke-17.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="heritage-card">
                    <div class="heritage-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg></div>
                    <strong>Taman Gunongan</strong>
                    <p>Taman indah peninggalan Sultan Iskandar Muda, dibangun untuk permaisurinya dari Pahang.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="heritage-card">
                    <div class="heritage-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M14.8 9A2 2 0 0 0 13 8h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4H9"/><line x1="12" y1="6" x2="12" y2="8"/><line x1="12" y1="16" x2="12" y2="18"/></svg></div>
                    <strong>Koin Dirham Emas</strong>
                    <p>Mata uang kuno peninggalan sultan, bukti tak terbantahkan kemakmuran ekonomi Aceh.</p>
                </div>
            </div>
            <div class="col-md-4 reveal-delay-1">
                <div class="heritage-card">
                    <div class="heritage-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg></div>
                    <strong>Naskah Bustanus Salatin</strong>
                    <p>Karya agung Nuruddin ar-Raniri; ensiklopedia ilmu pengetahuan Islam terlengkap di Nusantara abad ke-17.</p>
                </div>
            </div>
            <div class="col-md-4 reveal-delay-2">
                <div class="heritage-card">
                    <div class="heritage-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"/><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"/><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"/><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"/><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"/><path d="M10 9.5C10 8.67 9.33 8 8.5 8H3.5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"/><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"/></svg></div>
                    <strong>Meriam Kesultanan</strong>
                    <p>Puluhan meriam besar peninggalan Iskandar Muda yang ditempatkan di benteng pertahanan sepanjang pesisir.</p>
                </div>
            </div>
            <div class="col-md-4 reveal-delay-3">
                <div class="heritage-card">
                    <div class="heritage-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
                    <strong>Museum Aceh</strong>
                    <p>Menyimpan ribuan artefak asli kesultanan: pakaian kebesaran, senjata pusaka, dan dokumen bersejarah.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="cta-wrap reveal">
        <a href="peraturan.php" class="btn-cta">
            <span>Uji Pengetahuan Sekarang</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    <div class="footer reveal">
        <div style="margin-bottom:16px;">
            <svg width="28" height="28" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:0.4; margin:0 auto 8px; display:block;">
                <circle cx="40" cy="40" r="32" stroke="#c9a84c" stroke-width="1"/>
                <circle cx="40" cy="40" r="22" stroke="#c9a84c" stroke-width="1"/>
                <path d="M40 18 L44 30 L40 26 L36 30 Z" fill="#c9a84c"/>
                <path d="M40 62 L44 50 L40 54 L36 50 Z" fill="#c9a84c"/>
                <path d="M18 40 L30 44 L26 40 L30 36 Z" fill="#c9a84c"/>
                <path d="M62 40 L50 44 L54 40 L50 36 Z" fill="#c9a84c"/>
                <circle cx="40" cy="40" r="4" fill="rgba(201,168,76,0.5)" stroke="#c9a84c" stroke-width="1"/>
            </svg>
        </div>
        <p style="font-family:var(--font-display); font-size:0.7rem; letter-spacing:3px; color:var(--gold); opacity:0.6; text-transform:uppercase; margin-bottom:10px;">Tugas Sejarah · Kesultanan Aceh Darussalam</p>
        <p style="margin-bottom:6px;">Sejarah Indonesia · SMA Kelas XI · Tahun Ajaran 2025/2026</p>
        <p>© 2026 AcehEdu Premium — Modul Sejarah Interaktif by ZM</p>
    </div>

</div>

<button id="backToTop" aria-label="Kembali ke atas">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ============================================================
// STAR FIELD
// ============================================================
(function() {
    const canvas = document.getElementById('starCanvas');
    const ctx = canvas.getContext('2d');
    let stars = [];
    function resize() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
    function createStars(count) {
        stars = [];
        for (let i = 0; i < count; i++) {
            stars.push({ x: Math.random()*canvas.width, y: Math.random()*canvas.height, r: Math.random()*1.2+0.2, alpha: Math.random()*0.6+0.1, speed: Math.random()*0.003+0.001, phase: Math.random()*Math.PI*2 });
        }
    }
    function drawStars(t) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        stars.forEach(s => {
            const a = s.alpha + Math.sin(t * s.speed + s.phase) * 0.2;
            ctx.beginPath(); ctx.arc(s.x, s.y, s.r, 0, Math.PI*2);
            ctx.fillStyle = `rgba(240,220,160,${a})`; ctx.fill();
        });
        requestAnimationFrame(drawStars);
    }
    resize(); createStars(160); requestAnimationFrame(drawStars);
    window.addEventListener('resize', () => { resize(); createStars(160); });
})();

// ============================================================
// ★ NEW: SHOOTING STARS
// ============================================================
(function() {
    const canvas = document.getElementById('shootCanvas');
    const ctx = canvas.getContext('2d');
    function resize() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
    resize();
    window.addEventListener('resize', resize);

    let meteors = [];
    function spawnMeteor() {
        const x = Math.random() * canvas.width * 1.5;
        const y = Math.random() * canvas.height * 0.5;
        meteors.push({ x, y, len: Math.random()*120+60, speed: Math.random()*8+6, alpha: 1, angle: Math.PI/5 });
    }
    setInterval(spawnMeteor, 2800);
    spawnMeteor();

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        meteors.forEach((m, i) => {
            m.x += Math.cos(m.angle) * m.speed;
            m.y += Math.sin(m.angle) * m.speed;
            m.alpha -= 0.018;
            if (m.alpha <= 0) { meteors.splice(i, 1); return; }
            const grd = ctx.createLinearGradient(m.x, m.y, m.x - Math.cos(m.angle)*m.len, m.y - Math.sin(m.angle)*m.len);
            grd.addColorStop(0, `rgba(240,220,160,${m.alpha})`);
            grd.addColorStop(1, 'rgba(240,220,160,0)');
            ctx.beginPath();
            ctx.strokeStyle = grd; ctx.lineWidth = 1.5;
            ctx.moveTo(m.x, m.y);
            ctx.lineTo(m.x - Math.cos(m.angle)*m.len, m.y - Math.sin(m.angle)*m.len);
            ctx.stroke();
        });
        requestAnimationFrame(draw);
    }
    draw();
})();

// ============================================================
// ★ NEW: HERO STAT COUNTER ANIMATION
// ============================================================
function animateCounters() {
    document.querySelectorAll('.stat-num[data-target]').forEach(el => {
        const target = parseInt(el.dataset.target);
        const suffix = el.dataset.suffix || '';
        const dur = target > 1000 ? 1800 : 1000;
        let start = null;
        const step = (ts) => {
            if (!start) start = ts;
            const p = Math.min((ts - start) / dur, 1);
            const ease = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.floor(ease * target) + suffix;
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = target + suffix;
        };
        requestAnimationFrame(step);
    });
}
// Trigger once hero is visible
const heroObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { animateCounters(); heroObserver.disconnect(); } });
}, { threshold: 0.3 });
heroObserver.observe(document.querySelector('.hero-stats'));

// ============================================================
// READING PROGRESS + BACK TO TOP
// ============================================================
window.addEventListener('scroll', () => {
    const st = document.documentElement.scrollTop;
    const sh = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    document.getElementById('readingProgress').style.width = (st / sh * 100) + '%';
    document.getElementById('backToTop').classList.toggle('show', st > 400);
});
document.getElementById('backToTop').addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

// ============================================================
// SCROLL REVEAL + KNOWLEDGE METERS + TABLE ROWS
// ============================================================
function checkReveal() {
    document.querySelectorAll('.reveal').forEach(el => {
        if (el.getBoundingClientRect().top < window.innerHeight - 100) {
            el.classList.add('active');
            el.querySelectorAll('.km-fill').forEach(fill => {
                const w = fill.dataset.width;
                if (w) setTimeout(() => { fill.style.width = w + '%'; }, 300);
            });
        }
    });
    // Table row slide-in
    document.querySelectorAll('#sultanTable tbody tr').forEach((tr, i) => {
        const rect = tr.getBoundingClientRect();
        if (rect.top < window.innerHeight - 60) {
            setTimeout(() => tr.classList.add('row-visible'), i * 100);
        }
    });
}
window.addEventListener('scroll', checkReveal);
checkReveal();

// ============================================================
// ★ NEW: RIPPLE EFFECT on feat-item click
// ============================================================
document.querySelectorAll('.feat-item').forEach(item => {
    item.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height) * 1.5;
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        ripple.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX - rect.left - size/2}px;top:${e.clientY - rect.top - size/2}px;`;
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

// ============================================================
// ★ NEW: 3D TILT on heritage cards (desktop only)
// ============================================================
if (window.matchMedia('(hover: hover)').matches) {
    document.querySelectorAll('.heritage-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            card.style.transform = `translateY(-6px) rotateX(${-y * 8}deg) rotateY(${x * 8}deg)`;
            card.style.transition = 'transform 0.1s ease, box-shadow 0.4s';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.transition = 'all 0.4s';
        });
    });
}

// ============================================================
// ★ NEW: MAGNETIC HOVER on section cards
// ============================================================
if (window.matchMedia('(hover: hover)').matches) {
    document.querySelectorAll('.section-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left - rect.width / 2) / rect.width;
            const y = (e.clientY - rect.top - rect.height / 2) / rect.height;
            card.style.transform = `translate(${x * 4}px, ${y * 4}px)`;
            card.style.transition = 'transform 0.15s ease';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.transition = 'transform 0.5s cubic-bezier(0.16,1,0.3,1), border-color 0.4s, box-shadow 0.4s';
        });
    });
}
</script>
</body>
</html>
