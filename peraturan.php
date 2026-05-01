<?php
// require_once 'config/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Peraturan Kuis — AcehEdu Premium</title>
    <meta name="description" content="Baca dan pahami seluruh aturan sebelum memulai kuis Kesultanan Aceh.">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
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
        --red:        #c0392b;
        --red-glow:   rgba(192,57,43,0.4);
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

    /* CUSTOM SCROLLBAR */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--ink); }
    ::-webkit-scrollbar-thumb { background: rgba(201,168,76,0.3); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--gold); }

    /* PRELOADER */
    #preloader { position: fixed; inset: 0; background: var(--ink); z-index: 9999; display: flex; justify-content: center; align-items: center; transition: opacity 0.8s ease, visibility 0.8s ease; }
    .loader-ring { width: 60px; height: 60px; border: 2px solid var(--border); border-top: 2px solid var(--gold); border-radius: 50%; animation: spinLoader 1s linear infinite; box-shadow: 0 0 20px var(--gold-glow); }
    @keyframes spinLoader { to { transform: rotate(360deg); } }

    /* STAR FIELD & GEOMETRIC OVERLAY */
    #starCanvas { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none; }
    body::before { content: ''; position: fixed; inset: 0; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E"); z-index: 0; pointer-events: none; opacity: 0.6; }
    body::after { content: ''; position: fixed; inset: 0; background-image: radial-gradient(var(--border) 1px, transparent 1px); background-size: 40px 40px; opacity: 0.1; z-index: 0; pointer-events: none; }

    /* NAVBAR */
    .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; background: rgba(2,6,23,0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid var(--border); transition: background 0.3s, box-shadow 0.3s, padding 0.3s; }
    .navbar.shrunk { padding: 10px 24px; background: rgba(2,6,23,0.92); box-shadow: 0 4px 30px rgba(0,0,0,0.4); }
    .nav-brand { font-family: var(--font-display); font-size: 1.2rem; font-weight: 700; color: var(--gold); text-decoration: none; letter-spacing: 2px; text-shadow: 0 0 20px var(--gold-glow); }
    .nav-menu-btn { background: none; border: 1px solid var(--border); border-radius: 10px; padding: 8px 12px; cursor: pointer; color: var(--text); transition: all 0.3s; }
    .nav-menu-btn:hover { border-color: var(--gold); color: var(--gold); box-shadow: 0 0 15px var(--gold-glow); }
    .nav-spacer { width: 44px; }
    #readingProgress { position: fixed; top: 0; left: 0; height: 3px; width: 0%; background: linear-gradient(90deg, transparent, var(--gold), var(--gold-light)); z-index: 1100; box-shadow: 0 0 12px var(--gold-glow); transition: width 0.1s linear; border-radius: 0 2px 2px 0; }

    /* SIDEBAR */
    .offcanvas { background: rgba(2,6,23,0.97) !important; backdrop-filter: blur(30px); border-right: 1px solid var(--border) !important; max-width: 280px; }
    .offcanvas-header { border-bottom: 1px solid var(--border); padding: 24px; }
    .offcanvas-title { font-family: var(--font-display); color: var(--gold); font-size: 0.8rem; letter-spacing: 3px; text-transform: uppercase; }
    .sidebar-link { display: flex; align-items: center; gap: 14px; padding: 14px 24px; color: var(--text-dim); text-decoration: none; font-size: 0.9rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.3s; margin: 2px 0; }
    .sidebar-link:hover, .sidebar-link.active { color: var(--gold); border-left-color: var(--gold); background: var(--gold-dim); padding-left: 30px; }

    /* MAIN WRAPPER */
    .main-wrap { position: relative; z-index: 1; max-width: 860px; margin: 0 auto; padding: 60px 20px 80px; }

    /* MISSION FLOW STEP INDICATOR */
    .mission-flow { display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 48px; position: relative; }
    .flow-step { display: flex; flex-direction: column; align-items: center; gap: 8px; position: relative; z-index: 1; }
    .flow-dot { width: 38px; height: 38px; border-radius: 50%; border: 1px solid var(--border); background: var(--ink-2); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 0.7rem; color: var(--text-dim); transition: all 0.4s; position: relative; }
    .flow-dot.done { background: var(--gold-dim); border-color: var(--gold); color: var(--gold); }
    .flow-dot.done::after { content: '✓'; font-size: 0.8rem; color: var(--gold); }
    .flow-dot.active { background: var(--gold); border-color: var(--gold-light); color: var(--ink); box-shadow: 0 0 0 6px rgba(201,168,76,0.15), 0 0 25px var(--gold-glow); animation: dotPulse 2s infinite; }
    @keyframes dotPulse { 0%, 100% { box-shadow: 0 0 0 4px rgba(201,168,76,0.15), 0 0 20px var(--gold-glow); } 50% { box-shadow: 0 0 0 10px rgba(201,168,76,0.05), 0 0 35px var(--gold-glow); } }
    .flow-dot.locked { opacity: 0.3; }
    .flow-label { font-family: var(--font-display); font-size: 0.6rem; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-dim); white-space: nowrap; }
    .flow-step.done .flow-label { color: var(--gold); opacity: 0.7; }
    .flow-step.active .flow-label { color: var(--gold); }
    .flow-line { height: 1px; width: 60px; background: linear-gradient(90deg, var(--gold), var(--border)); margin-bottom: 22px; opacity: 0.4; flex-shrink: 0; position: relative; overflow: hidden; }
    .flow-line.done { background: var(--gold); opacity: 0.6; }
    .flow-line.done::after { content:''; position:absolute; top:0; left:0; height:100%; width:50%; background:linear-gradient(90deg, transparent, #fff, transparent); animation: scanLine 2s infinite linear; }
    @keyframes scanLine { 0% { transform: translateX(-100%); } 100% { transform: translateX(200%); } }

    /* HERO */
    .hero { text-align: center; margin-bottom: 48px; position: relative; }
    .clock-wrap { width: 100px; height: 100px; margin: 0 auto 28px; position: relative; }
    .clock-face { width: 100%; height: 100%; border-radius: 50%; background: radial-gradient(circle at 40% 35%, rgba(201,168,76,0.12), rgba(2,6,23,0.8)); border: 1px solid var(--border); position: relative; box-shadow: 0 0 40px rgba(201,168,76,0.15), inset 0 0 20px rgba(0,0,0,0.5); }
    .clock-ring { position: absolute; inset: -10px; border-radius: 50%; border: 1px dashed rgba(201,168,76,0.25); animation: spinRing 30s linear infinite; }
    .clock-ring-2 { position: absolute; inset: -18px; border-radius: 50%; border: 1px solid rgba(201,168,76,0.08); animation: spinRing 20s linear infinite reverse; }
    @keyframes spinRing { to { transform: rotate(360deg); } }
    .clock-ticks { position: absolute; inset: 0; border-radius: 50%; }
    .tick { position: absolute; width: 1px; height: 6px; background: rgba(201,168,76,0.4); left: 50%; transform-origin: bottom center; top: 8px; }
    .hand { position: absolute; bottom: 50%; left: 50%; transform-origin: bottom center; border-radius: 4px; transition: transform 0.1s cubic-bezier(0.4, 2.08, 0.55, 0.44); }
    .hand-hour { width: 3px; height: 28px; background: var(--gold); margin-left: -1.5px; box-shadow: 0 0 8px var(--gold-glow); }
    .hand-minute { width: 2px; height: 36px; background: var(--gold-light); margin-left: -1px; box-shadow: 0 0 6px rgba(240,208,128,0.5); }
    .hand-second { width: 1px; height: 40px; background: #ef4444; margin-left: -0.5px; }
    .clock-center { position: absolute; width: 8px; height: 8px; border-radius: 50%; background: var(--gold); top: 50%; left: 50%; transform: translate(-50%, -50%); box-shadow: 0 0 10px var(--gold-glow); z-index: 10; }

    .hero-eyebrow { font-family: var(--font-display); font-size: 0.68rem; letter-spacing: 5px; color: var(--gold); text-transform: uppercase; margin-bottom: 14px; opacity: 0; animation: fadeInTop 1s 1s forwards; }
    .hero-title { font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 900; background: linear-gradient(180deg, #fff 0%, var(--gold) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1.1; margin-bottom: 14px; opacity: 0; animation: trackingIn 1.2s 1.2s cubic-bezier(0.215, 0.610, 0.355, 1.000) forwards; }
    .hero-sub { font-size: 0.95rem; color: var(--text-dim); font-weight: 300; letter-spacing: 0.5px; max-width: 420px; margin: 0 auto; opacity: 0; animation: fadeInBot 1s 1.5s forwards; }

    @keyframes trackingIn { 0% { letter-spacing: -0.5em; opacity: 0; } 40% { opacity: 0.6; } 100% { opacity: 1; letter-spacing: -0.5px; } }
    @keyframes fadeInTop { 0% { opacity: 0; transform: translateY(-20px); } 100% { opacity: 0.75; transform: translateY(0); } }
    @keyframes fadeInBot { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }

    /* SECTION CARD & SPOTLIGHT */
    .section-card { background: linear-gradient(145deg, rgba(20,18,14,0.8), rgba(2,6,23,0.9)); border: 1px solid var(--border); border-radius: var(--radius); padding: 40px; margin-bottom: 28px; position: relative; overflow: hidden; transition: border-color 0.4s, box-shadow 0.4s; }
    .section-card::before { content: ""; position: absolute; inset: 0; pointer-events: none; opacity: 0; background: radial-gradient(800px circle at var(--mouse-x, 0) var(--mouse-y, 0), rgba(201, 168, 76, 0.08), transparent 40%); transition: opacity 0.5s; z-index: 1; }
    .section-card:hover::before { opacity: 1; }
    .section-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); opacity: 0.3; transition: opacity 0.4s; }
    .section-card:hover { border-color: rgba(201,168,76,0.35); box-shadow: 0 20px 60px -20px rgba(0,0,0,0.8), 0 0 40px -20px var(--gold-glow); }
    .section-card:hover::after { opacity: 0.8; }

    .card-content { position: relative; z-index: 2; }
    .section-tag { font-family: var(--font-display); font-size: 0.65rem; letter-spacing: 4px; color: var(--gold); text-transform: uppercase; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 10px; opacity: 0.75; }
    .section-tag::before { content: ''; display: inline-block; width: 20px; height: 1px; background: var(--gold); }
    .section-title { font-family: var(--font-display); font-size: clamp(1.2rem, 2.5vw, 1.6rem); font-weight: 700; color: var(--text); margin-bottom: 24px; display: flex; align-items: center; gap: 14px; text-shadow: 0 0 20px rgba(255,255,255,0.1); }
    .section-title svg { color: var(--gold); flex-shrink: 0; filter: drop-shadow(0 0 8px var(--gold-glow)); }

    /* RULE CARDS */
    .rules-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .rule-card { background: rgba(255,255,255,0.015); border: 1px solid var(--border); border-radius: 16px; padding: 24px 20px; display: flex; gap: 16px; align-items: flex-start; position: relative; overflow: hidden; opacity: 0; transform: translateY(20px); z-index: 1; transition: background 0.4s, border-color 0.4s, box-shadow 0.4s; }
    .rule-card::before { content: ""; position: absolute; inset: 0; pointer-events: none; opacity: 0; background: radial-gradient(400px circle at var(--mouse-x, 0) var(--mouse-y, 0), rgba(201, 168, 76, 0.1), transparent 40%); transition: opacity 0.5s; z-index: -1; }
    .rule-card.revealed { opacity: 1; transform: translateY(0); transition: opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1); }
    .rule-card:hover { background: var(--gold-dim); border-color: rgba(201,168,76,0.5); box-shadow: 0 15px 35px -10px rgba(0,0,0,0.7); z-index: 2; }
    .rule-card:hover::before { opacity: 1; }
    .rule-card::after { content: attr(data-badge); position: absolute; top: 12px; right: 14px; font-family: var(--font-display); font-size: 0.55rem; letter-spacing: 2px; padding: 3px 8px; border-radius: 4px; border: 1px solid; text-transform: uppercase; z-index: 2; }
    .rule-card[data-badge="WAJIB"]::after { color: var(--gold); border-color: rgba(201,168,76,0.4); background: rgba(201,168,76,0.07); }
    .rule-card[data-badge="KRITIS"]::after { color: #ef4444; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.07); }
    .rule-card[data-badge="OTOMATIS"]::after { color: #a78bfa; border-color: rgba(167,139,250,0.4); background: rgba(167,139,250,0.07); }

    .rule-icon { flex-shrink: 0; width: 48px; height: 48px; background: rgba(20,18,14,0.8); border: 1px solid var(--border); border-radius: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .rule-card:hover .rule-icon { background: var(--gold); border-color: var(--gold-light); transform: scale(1.1) rotate(8deg); box-shadow: 0 0 25px var(--gold-glow); }
    .rule-icon svg { width: 22px; height: 22px; color: var(--gold); stroke-width: 2; transition: color 0.4s; }
    .rule-card:hover .rule-icon svg { color: var(--ink); }

    .rule-content { position: relative; z-index: 2; }
    .rule-content h5 { font-family: var(--font-display); font-size: 0.9rem; font-weight: 700; color: var(--text); margin-bottom: 8px; letter-spacing: 0.3px; padding-right: 60px; }
    .rule-content p { font-size: 0.85rem; color: var(--text-dim); margin: 0; line-height: 1.65; }
    .rule-content p strong { color: var(--gold); font-weight: 600; }

    /* HOLD TO CONFIRM */
    .hold-instruction { text-align: center; margin-bottom: 36px; }
    .hold-instruction-label { font-family: var(--font-display); font-size: 0.7rem; letter-spacing: 3px; color: var(--text-dim); text-transform: uppercase; display: block; margin-bottom: 8px; opacity: 0.6; }
    .hold-instruction-text { font-size: 0.9rem; color: var(--text-dim); font-weight: 300; }

    .hold-btn-wrap { display: flex; flex-direction: column; align-items: center; gap: 20px; margin-bottom: 32px; position: relative; }
    .hold-btn-outer { position: relative; width: 140px; height: 140px; display: flex; align-items: center; justify-content: center; }
    .hold-ring-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; transform: rotate(-90deg); pointer-events: none; }
    .hold-ring-bg { fill: none; stroke: rgba(201,168,76,0.1); stroke-width: 3; }
    .hold-ring-fill { fill: none; stroke: var(--gold); stroke-width: 3; stroke-linecap: round; stroke-dasharray: 376; stroke-dashoffset: 376; filter: drop-shadow(0 0 8px var(--gold-glow)); transition: stroke-dashoffset 0.05s linear, stroke 0.3s; }

    .hold-btn { width: 110px; height: 110px; border-radius: 50%; border: 1px solid var(--border); background: radial-gradient(circle at 40% 35%, rgba(201,168,76,0.08), rgba(2,6,23,0.9)); cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275); user-select: none; -webkit-user-select: none; touch-action: none; position: relative; z-index: 2; }
    .hold-btn:hover { border-color: rgba(201,168,76,0.5); background: radial-gradient(circle at 40% 35%, rgba(201,168,76,0.15), rgba(2,6,23,0.9)); box-shadow: 0 0 20px rgba(201,168,76,0.2); transform: scale(1.05); }
    .hold-btn.pressing { border-color: var(--gold); background: radial-gradient(circle at 40% 35%, rgba(201,168,76,0.25), rgba(2,6,23,0.85)); box-shadow: 0 0 40px rgba(201,168,76,0.3); transform: scale(0.95); }
    .hold-btn.confirmed { border-color: var(--gold); background: var(--gold); cursor: default; transform: scale(1.04); box-shadow: 0 0 50px var(--gold-glow); animation: confirmPop 0.5s cubic-bezier(0.16,1,0.3,1); }
    @keyframes confirmPop { 0% { transform: scale(0.95); } 60% { transform: scale(1.1); } 100% { transform: scale(1.04); } }
    .hold-btn.shake { animation: holdShake 0.4s cubic-bezier(0.36,0.07,0.19,0.97); }
    @keyframes holdShake { 10%, 90% { transform: translateX(-2px); } 20%, 80% { transform: translateX(4px); } 30%, 50%, 70% { transform: translateX(-4px); } 40%, 60% { transform: translateX(4px); } 100% { transform: translateX(0); } }

    .hold-btn-icon { width: 28px; height: 28px; color: var(--gold); transition: all 0.35s; }
    .hold-btn.confirmed .hold-btn-icon { color: var(--ink); }
    .hold-btn.pressing .hold-btn-icon { transform: scale(0.85); }

    .hold-btn-label { font-family: var(--font-display); font-size: 0.6rem; letter-spacing: 2px; color: var(--text-dim); text-transform: uppercase; transition: color 0.35s; text-align: center; line-height: 1.3; font-weight: 600; }
    .hold-btn.pressing .hold-btn-label { color: var(--gold); }
    .hold-btn.confirmed .hold-btn-label { color: var(--ink); }

    .hold-status { font-family: var(--font-display); font-size: 0.65rem; letter-spacing: 2px; color: var(--text-dim); text-transform: uppercase; text-align: center; transition: color 0.4s; height: 18px; }
    .hold-status.active { color: var(--gold); text-shadow: 0 0 10px var(--gold-glow); }
    .hold-status.success { color: #4ade80; text-shadow: 0 0 10px rgba(74,222,128,0.5); }

    .hold-orbit { position: absolute; inset: -20px; border-radius: 50%; pointer-events: none; opacity: 0; transition: opacity 0.3s; }
    .hold-btn-outer.pressing .hold-orbit { opacity: 1; animation: orbitSpin 1.5s linear infinite; }
    @keyframes orbitSpin { to { transform: rotate(360deg); } }
    .hold-orbit::before, .hold-orbit::after { content: ''; position: absolute; width: 6px; height: 6px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 12px var(--gold-glow); }
    .hold-orbit::before { top: 0; left: 50%; transform: translateX(-50%); }
    .hold-orbit::after { bottom: 0; left: 50%; transform: translateX(-50%); }

    /* SYSTEM CHECK */
    .syscheck-wrap { border: 1px solid var(--border); border-radius: 16px; padding: 24px; background: rgba(0,0,0,0.3); margin-bottom: 32px; display: none; overflow: hidden; position: relative; }
    .syscheck-wrap::before { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 2px; background: linear-gradient(90deg, transparent, var(--gold), transparent); animation: scanTop 3s linear infinite; }
    @keyframes scanTop { to { left: 200%; } }
    .syscheck-wrap.visible { display: block; animation: fadeSlideIn 0.6s cubic-bezier(0.16,1,0.3,1); }
    @keyframes fadeSlideIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    .syscheck-title { font-family: var(--font-display); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); text-transform: uppercase; margin-bottom: 16px; opacity: 0.8; font-weight: 700; }
    .syscheck-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px dashed rgba(255,255,255,0.05); font-size: 0.85rem; color: var(--text-dim); opacity: 0; transform: translateX(-15px); transition: opacity 0.4s, transform 0.4s; }
    .syscheck-item:last-child { border-bottom: none; }
    .syscheck-item.show { opacity: 1; transform: translateX(0); }
    .sys-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--text-dim); flex-shrink: 0; transition: background 0.4s; }
    .syscheck-item.ok .sys-dot { background: #4ade80; box-shadow: 0 0 10px rgba(74,222,128,0.6); }
    .syscheck-item.ok { color: var(--text); }
    .sys-status { margin-left: auto; font-family: var(--font-display); font-size: 0.65rem; letter-spacing: 1.5px; opacity: 0; transition: opacity 0.4s; font-weight: 700; }
    .syscheck-item.ok .sys-status { opacity: 1; color: #4ade80; text-shadow: 0 0 8px rgba(74,222,128,0.4); }

    /* CTA BUTTON */
    .btn-ignition-wrap { text-align: center; }
    .btn-ignition { display: inline-flex; align-items: center; justify-content: center; gap: 14px; background: transparent; color: var(--gold); border: 2px solid var(--gold); font-family: var(--font-display); font-size: 0.85rem; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; padding: 20px 55px; border-radius: 100px; text-decoration: none; transition: all 0.4s; position: relative; overflow: hidden; opacity: 0.35; pointer-events: none; min-width: 300px; box-shadow: 0 0 20px rgba(201,168,76,0.1); }
    .btn-ignition.active { opacity: 1; pointer-events: all; animation: ignitePulse 2s infinite; }
    @keyframes ignitePulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(201,168,76,0.4); } 50% { box-shadow: 0 0 0 15px rgba(201,168,76,0); } }
    .btn-ignition::before { content: ''; position: absolute; inset: 0; background: var(--gold); transform: scaleX(0); transform-origin: right; transition: transform 0.5s cubic-bezier(0.86, 0, 0.07, 1); z-index: 0; }
    .btn-ignition.active:hover { color: var(--ink); animation: none; box-shadow: 0 0 50px var(--gold-glow); border-color: var(--gold-light); transform: translateY(-3px); }
    .btn-ignition.active:hover::before { transform: scaleX(1); transform-origin: left; }
    .btn-ignition span, .btn-ignition svg { position: relative; z-index: 1; transition: color 0.4s; }
    .btn-ignition.active:hover svg, .btn-ignition.active:hover span { color: var(--ink); }
    .btn-ignition.launching { pointer-events: none; animation: none; border-color: var(--gold); background: var(--gold-dim); color: var(--gold); }

    .btn-note { text-align: center; margin-top: 16px; font-size: 0.8rem; color: var(--text-dim); transition: color 0.4s; }
    .btn-note.ready { color: var(--gold); opacity: 0.9; text-shadow: 0 0 8px rgba(201,168,76,0.3); }

    /* HARTA KARUN */
    .treasure-card { text-align: center; padding: 50px 36px; margin-top: 40px; border-color: rgba(201,168,76,0.06); background: linear-gradient(145deg, rgba(201,168,76,0.015), rgba(2,6,23,0.6)); position: relative; }
    .treasure-card::before { background: radial-gradient(800px circle at var(--mouse-x,0) var(--mouse-y,0), rgba(201,168,76,0.05), transparent 40%); }
    .treasure-glyph { font-size: 2rem; margin-bottom: 20px; opacity: 0.5; display: block; filter: drop-shadow(0 0 12px var(--gold-glow)); animation: glyphFloat 4s ease-in-out infinite; }
    @keyframes glyphFloat { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-6px);} }
    .treasure-tag { font-family: var(--font-display); font-size: 0.62rem; letter-spacing: 5px; color: var(--gold); text-transform: uppercase; opacity: 0.5; margin-bottom: 16px; display: block; }
    .treasure-title { font-family: var(--font-display); font-size: clamp(1rem, 2vw, 1.25rem); font-weight: 700; color: var(--text); margin-bottom: 18px; letter-spacing: 0.5px; }
    .treasure-body { color: var(--text-dim); font-size: 0.9rem; line-height: 1.95; max-width: 460px; margin: 0 auto 24px; font-weight: 300; }
    .treasure-divider { font-family: var(--font-display); font-size: 0.62rem; letter-spacing: 4px; color: var(--gold); opacity: 0.25; margin-top: 8px; display: block; }

    /* ORNAMENT & FOOTER */
    .ornament { text-align: center; margin: 40px 0; color: var(--gold); opacity: 0.4; font-size: 1.2rem; letter-spacing: 15px; font-family: var(--font-display); text-shadow: 0 0 10px var(--gold-glow); }
    .footer { text-align: center; padding: 50px 0 30px; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 70px; font-size: 0.85rem; color: var(--text-dim); letter-spacing: 1px; }
    .footer-brand { font-family: var(--font-display); color: var(--gold); opacity: 0.6; font-size: 0.75rem; letter-spacing: 3px; display: block; margin-bottom: 8px; }

    /* BACK TO TOP */
    #backToTop { position: fixed; bottom: 30px; right: 30px; width: 52px; height: 52px; border-radius: 50%; background: rgba(20,18,14,0.8); backdrop-filter: blur(5px); border: 1px solid var(--border); color: var(--gold); cursor: pointer; opacity: 0; visibility: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 999; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(0,0,0,0.5); }
    #backToTop.show { opacity: 1; visibility: visible; }
    #backToTop:hover { background: var(--gold); border-color: var(--gold); color: var(--ink); transform: translateY(-5px) scale(1.1); box-shadow: 0 10px 25px var(--gold-glow); }

    /* SCROLL REVEAL */
    .reveal { opacity: 0; transform: translateY(35px); transition: opacity 0.9s cubic-bezier(0.25, 1, 0.5, 1), transform 0.9s cubic-bezier(0.25, 1, 0.5, 1); }
    .reveal.active { opacity: 1; transform: translateY(0); }

    /* MOBILE */
    @media (max-width: 768px) {
        .rules-grid { grid-template-columns: 1fr; }
        .section-card { padding: 30px 20px; }
        .treasure-card { padding: 36px 20px; }
        .flow-line { width: 30px; }
        .flow-label { font-size: 0.55rem; letter-spacing: 0.5px; }
        .btn-ignition { padding: 18px 30px; font-size: 0.8rem; min-width: unset; width: 100%; }
        .clock-wrap { width: 85px; height: 85px; }
        .hero-title { font-size: 2.2rem; }
        .rule-card::after { display: none; }
        .rule-content h5 { padding-right: 0; }
    }

    @media (max-width: 480px) {
        .mission-flow { gap: 0; }
        .flow-line { width: 18px; }
        .hold-btn-outer { width: 120px; height: 120px; }
        .hold-btn { width: 92px; height: 92px; }
    }
    </style>
</head>
<body>

<!-- PRELOADER -->
<div id="preloader">
    <div class="loader-ring"></div>
</div>

<canvas id="starCanvas"></canvas>
<div id="readingProgress"></div>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <button class="nav-menu-btn" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-label="Buka menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
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
    <div class="offcanvas-body p-0 mt-3">
        <a class="sidebar-link" href="index.php">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
            Materi Belajar
        </a>
        <a class="sidebar-link active" href="peraturan.php">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Peraturan Kuis
        </a>
        <a class="sidebar-link" href="kuis.php">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Mulai Kuis
        </a>
        <a class="sidebar-link" href="hasil.php">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Hasil & Review
        </a>
    </div>
</div>

<!-- MAIN -->
<div class="main-wrap">

    <!-- MISSION FLOW STEP INDICATOR -->
    <div class="mission-flow reveal active">
        <div class="flow-step done">
            <div class="flow-dot done"></div>
            <span class="flow-label">Materi</span>
        </div>
        <div class="flow-line done"></div>
        <div class="flow-step active">
            <div class="flow-dot active">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <span class="flow-label">Peraturan</span>
        </div>
        <div class="flow-line"></div>
        <div class="flow-step locked">
            <div class="flow-dot locked">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <span class="flow-label">Kuis</span>
        </div>
        <div class="flow-line"></div>
        <div class="flow-step locked">
            <div class="flow-dot locked">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span class="flow-label">Hasil</span>
        </div>
    </div>

    <!-- HERO -->
    <div class="hero">
        <div class="clock-wrap">
            <div class="clock-ring-2"></div>
            <div class="clock-ring"></div>
            <div class="clock-face">
                <div class="clock-ticks" id="clockTicks"></div>
                <div class="hand hand-hour" id="hourHand"></div>
                <div class="hand hand-minute" id="minuteHand"></div>
                <div class="hand hand-second" id="secondHand"></div>
                <div class="clock-center"></div>
            </div>
        </div>
        <p class="hero-eyebrow">Briefing Misi</p>
        <h1 class="hero-title">Peraturan Kuis</h1>
        <p class="hero-sub">Baca dan pahami setiap ketentuan sebelum memasuki arena. Setiap detik berharga.</p>
    </div>

    <!-- SECTION: INFO KUIS -->
    <div class="section-card reveal">
        <div class="card-content">
            <span class="section-tag">Informasi Umum</span>
            <h2 class="section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Tentang Kuis Ini
            </h2>
            <p style="color:var(--text-dim);font-size:0.92rem;line-height:1.9;margin-bottom:20px;">
                Kuis ini dirancang untuk menguji pemahaman kamu tentang <strong style="color:var(--gold)">Kesultanan Aceh Darussalam</strong> — salah satu kerajaan Islam terbesar di Nusantara. Setiap soal disusun berdasarkan materi yang telah kamu pelajari sebelumnya.
            </p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;">
                <div style="background:var(--gold-dim);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;">
                    <div style="font-family:var(--font-display);font-size:1.4rem;color:var(--gold);font-weight:900;">20</div>
                    <div style="font-size:0.75rem;color:var(--text-dim);margin-top:4px;letter-spacing:1px;">Soal</div>
                </div>
                <div style="background:var(--gold-dim);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;">
                    <div style="font-family:var(--font-display);font-size:1.4rem;color:var(--gold);font-weight:900;">30<span style="font-size:0.8rem">dt</span></div>
                    <div style="font-size:0.75rem;color:var(--text-dim);margin-top:4px;letter-spacing:1px;">Per Soal</div>
                </div>
                <div style="background:var(--gold-dim);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;">
                    <div style="font-family:var(--font-display);font-size:1.4rem;color:var(--gold);font-weight:900;">5<span style="font-size:0.8rem">pt</span></div>
                    <div style="font-size:0.75rem;color:var(--text-dim);margin-top:4px;letter-spacing:1px;">Per Benar</div>
                </div>
                <div style="background:var(--gold-dim);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;">
                    <div style="font-family:var(--font-display);font-size:1.4rem;color:var(--gold);font-weight:900;">4</div>
                    <div style="font-size:0.75rem;color:var(--text-dim);margin-top:4px;letter-spacing:1px;">Pilihan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION: RULES GRID -->
    <div class="section-card reveal">
        <div class="card-content">
            <span class="section-tag">Ketentuan</span>
            <h2 class="section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Aturan Main
            </h2>
            <div class="rules-grid">

                <div class="rule-card" data-badge="WAJIB">
                    <div class="rule-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="rule-content">
                        <h5>Batas Waktu</h5>
                        <p>Setiap soal memiliki batas waktu <strong>30 detik</strong>. Soal otomatis berlanjut jika waktu habis.</p>
                    </div>
                </div>

                <div class="rule-card" data-badge="WAJIB">
                    <div class="rule-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div class="rule-content">
                        <h5>Satu Jawaban</h5>
                        <p>Pilih <strong>satu jawaban</strong> terbaik dari empat pilihan yang tersedia untuk setiap soal.</p>
                    </div>
                </div>

                <div class="rule-card" data-badge="KRITIS">
                    <div class="rule-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="rule-content">
                        <h5>Tidak Bisa Kembali</h5>
                        <p>Soal yang telah dilewati <strong>tidak dapat ditinjau ulang</strong>. Putuskan dengan bijak.</p>
                    </div>
                </div>

                <div class="rule-card" data-badge="KRITIS">
                    <div class="rule-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <div class="rule-content">
                        <h5>Sesi Terkunci</h5>
                        <p>Menutup atau meninggalkan halaman selama kuis berlangsung akan <strong>mengakhiri sesi</strong>.</p>
                    </div>
                </div>

                <div class="rule-card" data-badge="OTOMATIS">
                    <div class="rule-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.49-4.58"/></svg>
                    </div>
                    <div class="rule-content">
                        <h5>Pengulangan</h5>
                        <p>Kuis dapat diulang setelah sesi berakhir. Tapi harus <strong>konfirmasi ke ozim gantengg</strong>.</p>
                    </div>
                </div>

                <div class="rule-card" data-badge="KRITIS">
                    <div class="rule-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="rule-content">
                        <h5>Keamanan Kuis</h5>
                        <p>Berpindah tab, keluar web <strong>dapat terdeteksi sistem</strong> dan<strong> tidak bisa menyalin teks saat kuis</strong>.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- SECTION: HOLD TO CONFIRM -->
    <div class="section-card reveal">
        <div class="card-content">
            <span class="section-tag">Konfirmasi</span>
            <h2 class="section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Saya Sudah Membaca
            </h2>

            <div class="hold-instruction">
                <span class="hold-instruction-label">Langkah Selanjutnya</span>
                <p class="hold-instruction-text">Tahan tombol di bawah untuk mengkonfirmasi kamu telah memahami seluruh peraturan.</p>
            </div>

            <div class="hold-btn-wrap">
                <div class="hold-btn-outer" id="holdOuter">
                    <div class="hold-orbit"></div>
                    <svg class="hold-ring-svg" viewBox="0 0 140 140">
                        <circle class="hold-ring-bg" cx="70" cy="70" r="60"/>
                        <circle class="hold-ring-fill" id="holdRingFill" cx="70" cy="70" r="60"/>
                    </svg>
                    <button class="hold-btn" id="holdBtn" aria-label="Tahan untuk konfirmasi">
                        <svg class="hold-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>
                        </svg>
                        <span class="hold-btn-label">Tahan<br>Tombol</span>
                    </button>
                </div>
                <div class="hold-status" id="holdStatus">Siap dikonfirmasi</div>
            </div>

            <!-- SYSTEM CHECK -->
            <div class="syscheck-wrap" id="syscheckWrap">
                <div class="syscheck-title">Inisialisasi Sistem</div>
                <div class="syscheck-item" id="sc1">
                    <div class="sys-dot"></div>
                    <span>Sesi pengguna diverifikasi</span>
                    <span class="sys-status">OK</span>
                </div>
                <div class="syscheck-item" id="sc2">
                    <div class="sys-dot"></div>
                    <span>Bank soal dimuat</span>
                    <span class="sys-status">OK</span>
                </div>
                <div class="syscheck-item" id="sc3">
                    <div class="sys-dot"></div>
                    <span>Timer dikalibrasi</span>
                    <span class="sys-status">OK</span>
                </div>
                <div class="syscheck-item" id="sc4">
                    <div class="sys-dot"></div>
                    <span>Arena kuis siap</span>
                    <span class="sys-status">READY</span>
                </div>
            </div>

            <!-- CTA -->
            <div class="btn-ignition-wrap">
                <a href="kuis.php" class="btn-ignition" id="btnIgnition">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    <span>Mulai Kuis</span>
                </a>
                <p class="btn-note" id="btnNote">Konfirmasi terlebih dahulu untuk melanjutkan</p>
            </div>
        </div>
    </div>

    <!-- HARTA KARUN -->
    <div class="section-card treasure-card reveal">
        <div class="card-content">
            <span class="treasure-glyph">⚱</span>
            <span class="treasure-tag">Catatan Tersembunyi</span>
            <h3 class="treasure-title">Ada Sesuatu yang Menunggu</h3>
            <p class="treasure-body">
                Di balik setiap halaman yang kau jelajahi, tersimpan sebuah rahasia kecil
                dan dasar yaitu harta yang tidak tertulis dalam sejarah manapun.
                Hanya yang benar-benar jeli dan faham yang akan menemukannya,
                dan hanya ada <strong style="color:var(--gold)">satu kesempatan</strong> untuk mengklaimnya, setelah itu otomatis di tutup atau di fix.
            </p>
            <span class="treasure-divider">✦ &nbsp;&nbsp; SATU PINTU · SATU WAKTU &nbsp;&nbsp; ✦</span>
        </div>
    </div>

    <div class="ornament reveal">✦ &nbsp; ✦ &nbsp; ✦</div>

    <footer class="footer reveal">
        <span class="footer-brand">AcehEdu Premium</span>
        &copy; <?= date('Y') ?> &nbsp;·&nbsp; Kesultanan Aceh Darussalam &nbsp;·&nbsp; Dibuat oleh ZM
    </footer>

</div>

<!-- BACK TO TOP -->
<button id="backToTop" aria-label="Kembali ke atas">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    'use strict';

    /* ── PRELOADER ── */
    window.addEventListener('load', () => {
        const pl = document.getElementById('preloader');
        pl.style.opacity = '0';
        pl.style.visibility = 'hidden';
        setTimeout(() => pl.remove(), 900);
    });

    /* ── STAR CANVAS ── */
    const canvas = document.getElementById('starCanvas');
    const ctx = canvas.getContext('2d');
    let stars = [];

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    function initStars() {
        stars = Array.from({ length: 120 }, () => ({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            r: Math.random() * 1.2 + 0.2,
            a: Math.random(),
            da: (Math.random() * 0.004 + 0.001) * (Math.random() < 0.5 ? 1 : -1),
            vx: (Math.random() - 0.5) * 0.08,
            vy: (Math.random() - 0.5) * 0.08,
        }));
    }

    function drawStars() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        stars.forEach(s => {
            s.a = Math.max(0.05, Math.min(1, s.a + s.da));
            if (s.a <= 0.05 || s.a >= 1) s.da *= -1;
            s.x = (s.x + s.vx + canvas.width) % canvas.width;
            s.y = (s.y + s.vy + canvas.height) % canvas.height;
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(201,168,76,${s.a * 0.6})`;
            ctx.fill();
        });
        requestAnimationFrame(drawStars);
    }

    resizeCanvas();
    initStars();
    drawStars();
    window.addEventListener('resize', () => { resizeCanvas(); initStars(); }, { passive: true });

    /* ── CLOCK ── */
    function buildTicks() {
        const ticks = document.getElementById('clockTicks');
        if (!ticks) return;
        for (let i = 0; i < 12; i++) {
            const t = document.createElement('div');
            t.className = 'tick';
            t.style.transform = `translateX(-50%) rotate(${i * 30}deg)`;
            ticks.appendChild(t);
        }
    }

    function updateClock() {
        const now = new Date();
        const s = now.getSeconds() + now.getMilliseconds() / 1000;
        const m = now.getMinutes() + s / 60;
        const h = (now.getHours() % 12) + m / 60;
        const hHand = document.getElementById('hourHand');
        const mHand = document.getElementById('minuteHand');
        const sHand = document.getElementById('secondHand');
        if (hHand) hHand.style.transform = `rotate(${h * 30}deg)`;
        if (mHand) mHand.style.transform = `rotate(${m * 6}deg)`;
        if (sHand) sHand.style.transform = `rotate(${s * 6}deg)`;
    }

    buildTicks();
    setInterval(updateClock, 100);
    updateClock();

    /* ── NAVBAR SHRINK ── */
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('shrunk', window.scrollY > 60);
    }, { passive: true });

    /* ── READING PROGRESS ── */
    const progress = document.getElementById('readingProgress');
    window.addEventListener('scroll', () => {
        const doc = document.documentElement;
        const pct = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
        progress.style.width = Math.min(100, pct) + '%';
    }, { passive: true });

    /* ── SCROLL REVEAL ── */
    const revealEls = document.querySelectorAll('.reveal');
    const revealObs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('active'); revealObs.unobserve(e.target); } });
    }, { threshold: 0.12 });
    revealEls.forEach(el => revealObs.observe(el));

    /* ── RULE CARDS STAGGER ── */
    const ruleCards = document.querySelectorAll('.rule-card');
    const cardObs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                setTimeout(() => e.target.classList.add('revealed'), parseInt(e.target.dataset.delay || 0));
                cardObs.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    ruleCards.forEach((c, i) => { c.dataset.delay = i * 80; cardObs.observe(c); });

    /* ── MOUSE SPOTLIGHT ── */
    document.querySelectorAll('.section-card, .rule-card').forEach(el => {
        el.addEventListener('mousemove', e => {
            const r = el.getBoundingClientRect();
            el.style.setProperty('--mouse-x', `${e.clientX - r.left}px`);
            el.style.setProperty('--mouse-y', `${e.clientY - r.top}px`);
        }, { passive: true });
    });

    /* ── HOLD TO CONFIRM ── */
    const holdBtn    = document.getElementById('holdBtn');
    const holdOuter  = document.getElementById('holdOuter');
    const holdRing   = document.getElementById('holdRingFill');
    const holdStatus = document.getElementById('holdStatus');
    const btnIgnition = document.getElementById('btnIgnition');
    const btnNote    = document.getElementById('btnNote');
    const syscheckWrap = document.getElementById('syscheckWrap');

    const HOLD_DURATION = 2000;
    const CIRCUMFERENCE = 376;
    let holdTimer = null;
    let holdStart = null;
    let confirmed = false;
    let rafId = null;

    function setRingProgress(pct) {
        holdRing.style.strokeDashoffset = CIRCUMFERENCE - (CIRCUMFERENCE * pct);
    }

    function startHold(e) {
        if (confirmed) return;
        e.preventDefault();
        holdStart = performance.now();
        holdBtn.classList.add('pressing');
        holdOuter.classList.add('pressing');
        holdStatus.textContent = 'Tahan...';
        holdStatus.classList.add('active');

        function tick(now) {
            const elapsed = now - holdStart;
            const pct = Math.min(elapsed / HOLD_DURATION, 1);
            setRingProgress(pct);
            if (pct < 1) { rafId = requestAnimationFrame(tick); }
            else { completeHold(); }
        }
        rafId = requestAnimationFrame(tick);
    }

    function cancelHold() {
        if (confirmed) return;
        cancelAnimationFrame(rafId);
        holdStart = null;
        holdBtn.classList.remove('pressing');
        holdOuter.classList.remove('pressing');
        holdStatus.textContent = 'Siap dikonfirmasi';
        holdStatus.classList.remove('active');
        setRingProgress(0);
    }

    function completeHold() {
        confirmed = true;
        cancelAnimationFrame(rafId);
        holdBtn.classList.remove('pressing');
        holdOuter.classList.remove('pressing');
        holdBtn.classList.add('confirmed');
        holdStatus.textContent = 'Dikonfirmasi ✓';
        holdStatus.classList.remove('active');
        holdStatus.classList.add('success');
        setRingProgress(1);
        holdRing.style.stroke = '#4ade80';
        runSystemCheck();
    }

    holdBtn.addEventListener('mousedown', startHold);
    holdBtn.addEventListener('touchstart', startHold, { passive: false });
    window.addEventListener('mouseup', cancelHold);
    window.addEventListener('touchend', cancelHold);
    holdBtn.addEventListener('mouseleave', cancelHold);

    /* ── SYSTEM CHECK ── */
    function runSystemCheck() {
        syscheckWrap.classList.add('visible');
        const items = ['sc1','sc2','sc3','sc4'];
        items.forEach((id, i) => {
            setTimeout(() => {
                const el = document.getElementById(id);
                el.classList.add('show');
                setTimeout(() => el.classList.add('ok'), 300);
                if (i === items.length - 1) {
                    setTimeout(() => {
                        btnIgnition.classList.add('active');
                        btnNote.textContent = 'Arena siap — selamat berjuang';
                        btnNote.classList.add('ready');
                    }, 500);
                }
            }, i * 450);
        });
    }

    /* ── BTN RIPPLE ── */
    btnIgnition.addEventListener('click', function(e) {
        if (!this.classList.contains('active')) return;
        this.classList.add('launching');
        const ripple = document.createElement('span');
        ripple.style.cssText = `position:absolute;border-radius:50%;background:rgba(255,255,255,0.3);width:10px;height:10px;transform:scale(0);animation:rippleAnim 0.6s linear;left:${e.offsetX}px;top:${e.offsetY}px;pointer-events:none;`;
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 700);
    });

    const rippleStyle = document.createElement('style');
    rippleStyle.textContent = `@keyframes rippleAnim{to{transform:scale(30);opacity:0;}}`;
    document.head.appendChild(rippleStyle);

    /* ── SECURITY ── */
    // Disable text selection & copy
    document.addEventListener('selectstart', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('contextmenu', e => e.preventDefault());

    // Detect tab switch / window blur
    let warnCount = 0;
    let lastWarnTime = 0;

    function onVisibilityChange() {
        if (document.hidden) triggerSecurityWarning('Berpindah tab terdeteksi');
    }
    function onWindowBlur() {
        if (document.hidden) return;
        triggerSecurityWarning('Keluar halaman terdeteksi');
    }

    function triggerSecurityWarning(reason) {
        warnCount++;
        // Flash navbar red
        navbar.style.borderBottomColor = '#ef4444';
        navbar.style.boxShadow = '0 0 30px rgba(239,68,68,0.3)';
        setTimeout(() => {
            navbar.style.borderBottomColor = '';
            navbar.style.boxShadow = '';
        }, 1500);
        // Show toast
        showSecurityToast(reason, warnCount);
    }

    function showSecurityToast(reason, count) {
        const existing = document.getElementById('secToast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'secToast';
        toast.innerHTML = `
            <div style="display:flex;align-items:center;gap:12px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <div>
                    <div style="font-family:'Cinzel',serif;font-size:0.65rem;letter-spacing:2px;color:#ef4444;text-transform:uppercase;margin-bottom:2px;">Peringatan Keamanan</div>
                    <div style="font-size:0.82rem;color:#e8e0d0;">${reason} <span style="color:#ef4444;font-weight:600;">(${count}x)</span></div>
                </div>
            </div>`;
        toast.style.cssText = `
            position:fixed;bottom:30px;left:50%;transform:translateX(-50%) translateY(20px);
            background:rgba(20,10,10,0.95);border:1px solid rgba(239,68,68,0.5);
            border-radius:14px;padding:16px 22px;z-index:9999;
            backdrop-filter:blur(20px);box-shadow:0 10px 40px rgba(239,68,68,0.2);
            opacity:0;transition:all 0.4s cubic-bezier(0.16,1,0.3,1);white-space:nowrap;`;
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
        });
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(10px)';
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    document.addEventListener('visibilitychange', onVisibilityChange);
    window.addEventListener('blur', onWindowBlur);

    /* ── BACK TO TOP ── */
    const btt = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        btt.classList.toggle('show', window.scrollY > 400);
    }, { passive: true });
    btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

})();
</script>
</body>
</html>
