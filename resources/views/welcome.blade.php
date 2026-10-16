<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PHILADELPHIA CHURCH') }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Caveat:wght@700&family=Poppins:wght@400;600;700;800;900&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Primary Dominant Colors */
            --bg-mint: #e2f2e6;
            --mint-dot: #b7e4c7;
            --card-cream: #fffdfa;
            --text-black: #111827;
            --text-muted: #4b5563;
            
            /* Structural Dark */
            --header-dark-green: #1b4332;
            
            /* Accent Colors for Focus Points */
            --button-terracotta: #c85a32;
            --button-terracotta-hover: #b04924;
            --link-navy: #1d3557;
            --link-navy-hover: #0f2b48;
            
            --glass-border: rgba(29, 53, 87, 0.12);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            max-width: 100%;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-mint);
            background-image: radial-gradient(var(--mint-dot) 1.5px, transparent 1.5px);
            background-size: 24px 24px;
            color: var(--text-black);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar in Dark Green (#1b4332) */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 14px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--header-dark-green);
            backdrop-filter: blur(10px);
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        nav .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        nav .brand-logo {
            height: 46px;
            width: auto;
            object-fit: contain;
            background: #ffffff;
            padding: 3px 8px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        nav .logo-text {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--card-cream);
            letter-spacing: 1px;
        }

        nav .links a {
            color: var(--card-cream);
            text-decoration: none;
            margin-left: 24px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        nav .links a:hover {
            color: #fbbf24;
        }

        .nav-toggle {
            display: none;
            width: 44px;
            height: 44px;
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 12px;
            background: rgba(255,255,255,.08);
            color: #fffdfa;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            cursor: pointer;
        }

        /* Button Terracotta (#c85a32) */
        .btn-terracotta {
            background-color: var(--button-terracotta);
            color: #ffffff !important;
            padding: 11px 28px;
            border-radius: 50px;
            font-weight: 700 !important;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            box-shadow: 0 4px 15px rgba(200, 90, 50, 0.35);
            display: inline-block;
        }

        .btn-terracotta:hover {
            background-color: var(--button-terracotta-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(200, 90, 50, 0.5);
        }

        /* HERO SECTION - PHOTO COLLAGE CLUSTER */
        .hero-collage-container {
            min-height: 100vh;
            padding-top: 100px;
            padding-bottom: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            isolation: isolate;
            background-image: linear-gradient(rgba(226, 242, 230, 0.72), rgba(226, 242, 230, 0.82)), url('{{ asset('images/19.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .hero-collage-container::before {
            content: "";
            position: absolute;
            inset: 70px auto auto 50%;
            width: min(760px, 82vw);
            height: min(760px, 82vw);
            transform: translateX(-50%);
            background:
                radial-gradient(circle at 50% 50%, rgba(27, 67, 50, 0.16), transparent 56%),
                conic-gradient(from 210deg, rgba(200, 90, 50, 0.22), rgba(29, 53, 87, 0.18), rgba(27, 67, 50, 0.18), rgba(200, 90, 50, 0.22));
            border-radius: 50%;
            filter: blur(20px);
            opacity: 0.8;
            z-index: -1;
            animation: haloPulse 8s ease-in-out infinite;
        }

        .hero-header-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .hero-header-badge img {
            height: 65px;
            width: auto;
            background: #ffffff;
            padding: 5px 14px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 10px;
        }

        .hero-header-badge span {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--link-navy);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .collage-wrapper {
            position: relative;
            width: 100%;
            max-width: 1080px;
            height: 560px;
            margin: 0 auto;
            animation: heroStageDrift 12s cubic-bezier(0.45, 0, 0.2, 1) infinite;
        }

        .collage-wrapper::before {
            content: "";
            position: absolute;
            top: 32px;
            left: 50%;
            width: 390px;
            height: 390px;
            transform: translateX(-50%);
            border: 16px solid rgba(255, 253, 250, 0.9);
            border-left-color: rgba(200, 90, 50, 0.22);
            border-bottom-color: rgba(27, 67, 50, 0.18);
            border-radius: 50%;
            filter: drop-shadow(0 18px 32px rgba(27, 67, 50, 0.14));
            z-index: 1;
            animation: ringOrbit 12s linear infinite;
        }

        .collage-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            text-align: center;
            background: rgba(255, 253, 250, 0.95);
            padding: 30px 42px;
            border-radius: 32px;
            box-shadow: 0 20px 50px rgba(29, 53, 87, 0.18);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(200, 90, 50, 0.25);
            width: 390px;
            overflow: hidden;
            animation: centerCardIn 1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .collage-center-text::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 0%, rgba(255,255,255,0.7) 46%, transparent 65%);
            transform: translateX(-120%);
            animation: cardSweep 4.8s ease-in-out infinite;
            pointer-events: none;
        }

        .hero-kicker {
            color: var(--link-navy);
            font-size: 0.73rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .hero-copy {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.55;
            margin: -10px auto 18px auto;
            max-width: 280px;
        }

        .hero-location {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--header-dark-green);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.6px;
            margin-top: 16px;
            text-transform: uppercase;
        }

        .hero-location i {
            color: var(--button-terracotta);
        }

        .collage-center-text.is-changing .hero-kicker,
        .collage-center-text.is-changing .script-welcome,
        .collage-center-text.is-changing h2,
        .collage-center-text.is-changing .hero-copy,
        .collage-center-text.is-changing .btn-terracotta,
        .collage-center-text.is-changing .hero-location {
            animation: textSwap 0.72s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .script-welcome {
            font-family: 'Caveat', cursive;
            font-size: 3.5rem;
            color: var(--button-terracotta);
            line-height: 1;
            margin-bottom: 4px;
        }

        .collage-center-text h2 {
            font-size: 1.65rem;
            font-weight: 900;
            color: var(--header-dark-green);
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            line-height: 1.2;
        }

        .collage-img {
            position: absolute;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 14px 35px rgba(0,0,0,0.15);
            border: 4px solid var(--card-cream);
            transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease, opacity 0.6s ease, filter 0.6s ease;
            animation: photoReveal 0.95s cubic-bezier(0.16, 1, 0.3, 1) both, photoFloat var(--float-speed, 7s) ease-in-out infinite;
            animation-delay: var(--delay, 0s), calc(var(--delay, 0s) + 0.8s);
            z-index: 2;
        }

        .collage-img:hover {
            transform: translate3d(var(--hover-x, 0), var(--hover-y, -8px), 0) scale(1.06);
            box-shadow: 0 20px 45px rgba(29, 53, 87, 0.28);
            z-index: 15;
        }

        .collage-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.08);
            transition: opacity 0.5s ease, transform 1.1s cubic-bezier(0.16, 1, 0.3, 1);
            animation: imageBreath 8s ease-in-out infinite;
        }

        .collage-img.is-swapping img {
            opacity: 0;
            transform: scale(1.2) translateX(-14px);
        }

        .collage-img.is-featured {
            z-index: 8;
            box-shadow: 0 24px 55px rgba(200, 90, 50, 0.25);
            border-color: #ffffff;
            filter: saturate(1.08) contrast(1.04);
        }

        .collage-img.is-dimmed {
            opacity: 0.72;
            filter: saturate(0.82);
        }

        .photo-caption {
            display: none !important;
        }

        .img-top-center { width: 220px; height: 230px; top: 0; left: 50%; transform: translateX(-50%); --delay: .1s; --float-speed: 7.4s; }
        .img-top-left { width: 230px; height: 210px; top: 15px; left: 9%; --delay: .18s; --float-speed: 8s; }
        .img-top-right { width: 230px; height: 210px; top: 15px; right: 9%; --delay: .26s; --float-speed: 7.7s; }
        .img-middle-left { width: 220px; height: 240px; top: 35%; left: -2%; --delay: .34s; --float-speed: 8.3s; }
        .img-middle-right { width: 220px; height: 240px; top: 35%; right: -2%; --delay: .42s; --float-speed: 7.9s; }
        .img-bottom-left { width: 230px; height: 210px; bottom: 10px; left: 8%; --delay: .5s; --float-speed: 8.1s; }
        .img-bottom-center { width: 220px; height: 210px; bottom: 0; left: 50%; transform: translateX(-50%); --delay: .58s; --float-speed: 7.5s; }
        .img-bottom-right { width: 220px; height: 220px; bottom: 10px; right: 9%; --delay: .66s; --float-speed: 8.2s; }

        .hero-story-progress {
            position: absolute;
            left: 50%;
            bottom: -4px;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 20;
        }

        .hero-story-dot {
            width: 32px;
            height: 4px;
            border: 0;
            border-radius: 999px;
            background: rgba(29, 53, 87, 0.18);
            cursor: pointer;
            overflow: hidden;
            padding: 0;
        }

        .hero-story-dot span {
            display: block;
            width: 0;
            height: 100%;
            background: var(--button-terracotta);
            border-radius: inherit;
        }

        .hero-story-dot.active span {
            animation: progressFill 4.8s linear both;
        }

        @keyframes haloPulse {
            0%, 100% { transform: translateX(-50%) scale(0.94); opacity: 0.55; }
            50% { transform: translateX(-50%) scale(1.04); opacity: 0.9; }
        }

        @keyframes heroStageDrift {
            0%, 100% { transform: translateX(0) scale(1); }
            35% { transform: translateX(-10px) scale(1.012); }
            70% { transform: translateX(10px) scale(1.006); }
        }

        @keyframes ringOrbit {
            from { transform: translateX(-50%) rotate(0deg); }
            to { transform: translateX(-50%) rotate(360deg); }
        }

        @keyframes centerCardIn {
            from { opacity: 0; transform: translate(-50%, -44%) scale(0.94); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        @keyframes cardSweep {
            0%, 55% { transform: translateX(-120%); }
            78%, 100% { transform: translateX(120%); }
        }

        @keyframes textSwap {
            0% { opacity: 0; transform: translateY(16px); filter: blur(8px); }
            100% { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        @keyframes photoReveal {
            from { opacity: 0; clip-path: inset(18% 18% 18% 18% round 26px); transform: translateY(26px) scale(0.94); }
            to { opacity: 1; clip-path: inset(0 0 0 0 round 26px); }
        }

        @keyframes photoFloat {
            0%, 100% { margin-top: 0; }
            50% { margin-top: -14px; }
        }

        @keyframes imageBreath {
            0%, 100% { transform: scale(1.08) translateX(0); }
            50% { transform: scale(1.16) translateX(-8px); }
        }

        @keyframes progressFill {
            from { width: 0; }
            to { width: 100%; }
        }

        /* Section Container */
        .section {
            padding: 90px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.6rem;
            margin-bottom: 35px;
            color: var(--header-dark-green);
            font-weight: 800;
            letter-spacing: 1px;
        }

        /* 3D COVERFLOW CAROUSEL FOR IDARA ZETU */
        .coverflow-container {
            position: relative;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 0 70px 0;
            perspective: 1200px;
        }

        .coverflow-track {
            position: relative;
            height: 480px;
            display: flex;
            justify-content: center;
            align-items: center;
            transform-style: preserve-3d;
        }

        .coverflow-card {
            position: absolute;
            width: 350px;
            background: var(--card-cream);
            border: 2px solid var(--link-navy);
            border-radius: 24px;
            padding: 22px;
            transition: transform 0.7s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.7s ease, filter 0.7s ease, border-color 0.5s ease;
            box-shadow: 0 15px 35px rgba(29, 53, 87, 0.1);
            cursor: pointer;
            text-align: center;
            user-select: none;
        }

        .card-img-holder {
            width: 100%;
            height: 220px;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .card-img-holder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .coverflow-card:hover .card-img-holder img {
            transform: scale(1.08);
        }

        .card-info h3 {
            font-size: 1.6rem;
            margin-bottom: 10px;
            color: var(--link-navy);
            font-weight: 700;
        }

        .card-info p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .coverflow-card.active {
            transform: translateX(0) translateZ(160px) scale(1.18);
            z-index: 10;
            opacity: 1;
            border-color: var(--button-terracotta);
            box-shadow: 0 20px 45px rgba(200, 90, 50, 0.25);
            filter: brightness(1);
        }

        .coverflow-card.prev-1 {
            transform: translateX(-260px) translateZ(0px) rotateY(32deg) scale(0.85);
            z-index: 5;
            opacity: 0.8;
            filter: brightness(0.9);
        }

        .coverflow-card.prev-2 {
            transform: translateX(-460px) translateZ(-160px) rotateY(42deg) scale(0.7);
            z-index: 2;
            opacity: 0.5;
            filter: brightness(0.75);
        }

        .coverflow-card.next-1 {
            transform: translateX(260px) translateZ(0px) rotateY(-32deg) scale(0.85);
            z-index: 5;
            opacity: 0.8;
            filter: brightness(0.9);
        }

        .coverflow-card.next-2 {
            transform: translateX(460px) translateZ(-160px) rotateY(-42deg) scale(0.7);
            z-index: 2;
            opacity: 0.5;
            filter: brightness(0.75);
        }

        .nav-btn {
            position: absolute;
            top: 45%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--button-terracotta);
            color: #ffffff;
            border: none;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 20;
            transition: background 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 15px rgba(200, 90, 50, 0.3);
        }

        .nav-btn:hover {
            background: var(--button-terracotta-hover);
            transform: translateY(-50%) scale(1.1);
        }

        .prev-btn { left: 10px; }
        .next-btn { right: 10px; }

        .coverflow-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 25px;
        }

        .dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: rgba(29, 53, 87, 0.2);
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .dot.active {
            background: var(--button-terracotta);
            transform: scale(1.3);
        }

        .department-story-modal {
            position: fixed;
            inset: 0;
            background: rgba(8, 15, 33, 0.68);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 9999;
        }
        .department-story-modal.open { display: flex; }
        .department-story-box {
            width: min(860px, 96vw);
            max-height: 90vh;
            overflow: auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 28px 70px rgba(0,0,0,.25);
            position: relative;
        }
        .department-story-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 50%;
            background: rgba(255,255,255,.92);
            color: #1f2937;
            cursor: pointer;
            z-index: 2;
            box-shadow: 0 8px 22px rgba(0,0,0,.16);
        }
        .department-story-hero {
            min-height: 250px;
            background-size: cover;
            background-position: center;
            color: #fff;
            display: flex;
            align-items: flex-end;
            padding: 34px;
            position: relative;
        }
        .department-story-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,.12), rgba(0,0,0,.74));
        }
        .department-story-hero > div { position: relative; z-index: 1; }
        .department-story-hero h3 {
            font-size: 2rem;
            margin: 0 0 8px;
            color: #fff;
        }
        .department-story-content {
            padding: 24px 28px 30px;
            display: grid;
            gap: 16px;
        }
        .department-story-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 14px;
        }
        .department-story-panel {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            background: #f8fafc;
        }
        .department-story-panel h4 {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            margin-bottom: 8px;
        }
        .department-story-panel p,
        .department-story-panel li {
            font-size: .92rem;
            line-height: 1.55;
            color: #334155;
        }

        /* ---------------------------------------------------- */
        /* TWIN-BUBBLE INFINITY SCHEDULE LAYOUT                  */
        /* ---------------------------------------------------- */

        /* Wrapper for entire schedule */
        .schedule-wrapper {
            max-width: 980px;
            margin: 40px auto 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px;
        }

        .schedule-poster {
            display: none;
        }

        .schedule-wrapper > .schedule-sunday-row,
        .schedule-wrapper > .schedule-pairs-grid {
            display: none;
        }

        .schedule-banner-list {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .schedule-banner {
            position: relative;
            min-height: 148px;
            overflow: hidden;
            border-radius: 8px;
            background-color: var(--schedule-color);
            background-image: var(--schedule-image);
            background-size: cover;
            background-position: var(--schedule-position, center top);
            box-shadow: 0 24px 48px rgba(29, 53, 87, 0.16);
            isolation: isolate;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .schedule-banner:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 60px rgba(29, 53, 87, 0.22);
        }

        .schedule-banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(10, 13, 34, 0.82) 0%, rgba(10, 13, 34, 0.48) 42%, rgba(10, 13, 34, 0.1) 100%),
                linear-gradient(135deg, var(--schedule-color) 0%, rgba(255, 255, 255, 0.06) 58%, rgba(255, 255, 255, 0) 100%);
            opacity: 0.9;
            z-index: -1;
        }

        .schedule-banner::after {
            content: "";
            position: absolute;
            inset: auto -80px -120px auto;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .schedule-banner-content {
            position: relative;
            min-height: 148px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 28px;
            padding: 24px 42px;
            color: #ffffff;
            z-index: 1;
        }

        .schedule-day-label {
            font-size: 0.85rem;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            opacity: 0.92;
        }

        .schedule-title {
            margin-top: 4px;
            font-family: 'Bebas Neue', 'Poppins', sans-serif;
            font-size: clamp(3.2rem, 7.2vw, 5.4rem);
            font-weight: 400;
            line-height: 0.88;
            text-transform: uppercase;
            text-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
        }

        .schedule-place {
            margin-top: 12px;
            max-width: 440px;
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.25;
            text-transform: uppercase;
            opacity: 0.92;
        }

        .schedule-time-block {
            min-width: 250px;
            text-align: right;
            font-weight: 900;
            text-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
        }

        .schedule-time-block span {
            display: block;
            font-size: 0.88rem;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .schedule-time-block strong {
            display: block;
            font-family: 'JetBrains Mono', monospace;
            font-size: clamp(1.85rem, 4vw, 3rem);
            line-height: 0.95;
        }

        .schedule-time-row {
            display: grid;
            grid-template-columns: auto 128px;
            align-items: baseline;
            justify-content: end;
            gap: 12px;
        }

        .schedule-time-row small {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            opacity: 0.88;
        }

        /* --- JUMAPILI solo row (centered, wider) --- */
        .schedule-sunday-row {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .twin-bubble-item-sunday {
            display: flex;
            width: 680px;
            max-width: 95%;
            height: 120px;
            filter: drop-shadow(0 14px 30px rgba(217, 119, 6, 0.28));
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .twin-bubble-item-sunday:hover {
            transform: scale(1.04);
            filter: drop-shadow(0 20px 38px rgba(217, 119, 6, 0.45));
        }

        /* --- Weekday pairs grid --- */
        .schedule-pairs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px 30px;
            width: 100%;
            max-width: 1200px;
        }

        .twin-bubble-item {
            display: flex;
            width: 100%;
            height: 108px;
            filter: drop-shadow(0 10px 22px rgba(29, 53, 87, 0.08));
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .twin-bubble-item:hover {
            transform: scale(1.03);
            filter: drop-shadow(0 15px 30px rgba(29, 53, 87, 0.16));
        }

        .bubble-part {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 14px 22px;
        }

        .bubble-left {
            border-top-left-radius: 55px;
            border-bottom-left-radius: 55px;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .bubble-right {
            border-top-right-radius: 55px;
            border-bottom-right-radius: 55px;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        /* White Cream Bubble Style */
        .bubble-white {
            background: var(--card-cream);
            color: var(--text-black);
            border: 1px solid rgba(29, 53, 87, 0.1);
            align-items: center;
            text-align: center;
        }

        .bubble-white .bubble-day {
            font-size: 0.78rem;
            letter-spacing: 2px;
            font-weight: 700;
            color: var(--button-terracotta);
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .bubble-white .bubble-time-big {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--link-navy);
        }

        /* Color Bubble Styles */
        .bubble-terracotta { background: linear-gradient(135deg, #c85a32, #d97706); color: #ffffff; }
        .bubble-green { background: linear-gradient(135deg, #1b4332, #2d6a4f); color: #ffffff; }
        .bubble-teal { background: linear-gradient(135deg, #0d9488, #115e59); color: #ffffff; }
        .bubble-navy { background: linear-gradient(135deg, #1d3557, #457b9d); color: #ffffff; }
        .bubble-charcoal { background: linear-gradient(135deg, #2b2b2b, #404040); color: #ffffff; }
        .bubble-crimson { background: linear-gradient(135deg, #dc2626, #991b1b); color: #ffffff; }
        .bubble-gold { background: linear-gradient(135deg, #d97706, #b45309); color: #ffffff; }

        .bubble-title {
            font-size: 1.08rem;
            font-weight: 800;
            line-height: 1.25;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 4px;
        }

        .bubble-sub {
            font-size: 0.82rem;
            opacity: 0.92;
            font-weight: 500;
        }

        /* Footer in Dark Green (#1b4332) */
        footer {
            background: var(--header-dark-green);
            padding: 50px 20px;
            text-align: center;
            color: var(--card-cream);
        }

        footer .footer-logo {
            height: 60px;
            width: auto;
            margin-bottom: 15px;
            background: #ffffff;
            padding: 4px 12px;
            border-radius: 10px;
        }

        footer p {
            color: #d1d5db;
            font-size: 0.95rem;
            font-weight: 500;
        }

        @media (max-width: 900px) {
            .collage-wrapper { height: auto; display: flex; flex-direction: column; align-items: center; gap: 15px; }
            .collage-center-text { position: static; transform: none; width: 100%; }
            .collage-img { position: static; width: 100%; height: 220px; transform: none !important; }
            .collage-wrapper::before, .hero-story-progress { display: none; }
        }

        @media (max-width: 768px) {
            nav {
                padding: 12px 14px;
                align-items: center;
            }
            .nav-toggle { display: inline-flex; }
            nav .links {
                display: none;
                position: absolute;
                top: calc(100% + 8px);
                left: 14px;
                right: 14px;
                background: var(--header-dark-green);
                border: 1px solid rgba(255,255,255,.14);
                border-radius: 16px;
                padding: 12px;
                flex-direction: column;
                gap: 8px;
                box-shadow: 0 18px 40px rgba(0,0,0,.22);
            }
            nav .links.open { display: flex; }
            nav .links a,
            nav .links .btn-terracotta {
                margin-left: 0 !important;
                width: 100%;
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
            }
            nav .logo-text { font-size: 1rem; }
            nav .brand-logo { height: 40px; }
            .hero-collage-container { padding: 92px 18px 46px; }
            .hero-header-badge span { font-size: 0.78rem; text-align: center; }
            .collage-center-text { padding: 28px 22px; border-radius: 24px; }
            .script-welcome { font-size: 3rem; }
            .collage-center-text h2 { font-size: 1.35rem; }
            .coverflow-card { width: 290px; }
            .coverflow-card.prev-1 { transform: translateX(-150px) scale(0.8); }
            .coverflow-card.next-1 { transform: translateX(150px) scale(0.8); }
            .coverflow-card.prev-2, .coverflow-card.next-2 { opacity: 0; }

            .schedule-pairs-grid { grid-template-columns: 1fr; }
            .twin-bubble-item, .twin-bubble-item-sunday { width: 100%; height: auto; flex-direction: column; }
            .bubble-part { border-radius: 20px !important; text-align: center !important; }
            .schedule-wrapper { padding: 0 14px; }
            .schedule-banner { min-height: 190px; }
            .schedule-banner-content {
                min-height: 190px;
                grid-template-columns: 1fr;
                align-items: end;
                gap: 16px;
                padding: 24px 22px;
            }
            .schedule-time-block {
                min-width: 0;
                text-align: left;
            }
            .schedule-time-row {
                justify-content: start;
                grid-template-columns: auto minmax(92px, 1fr);
            }
            .schedule-title { font-size: 3.05rem; }
            .schedule-place { font-size: 0.82rem; }
            section,
            .section,
            .schedule-wrapper {
                max-width: 100%;
                overflow-x: hidden;
            }
            img {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            nav .logo-text { font-size: .82rem; letter-spacing: .3px; }
            nav .brand { gap: 8px; min-width: 0; }
            .hero-collage-container { padding-left: 12px; padding-right: 12px; }
            .script-welcome { font-size: 2.45rem; }
            .collage-center-text h2 { font-size: 1.18rem; }
            .btn-terracotta {
                padding: 11px 18px;
                width: 100%;
                text-align: center;
            }
            .coverflow-card { width: min(290px, 86vw); }
            .schedule-title { font-size: 2.35rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav>
        <a href="#home" class="brand">
            <img src="{{ asset('images/eagt_logo.png') }}" alt="PHILADELPHIA CHURCH Logo" class="brand-logo">
            <span class="logo-text">PHILADELPHIA CHURCH</span>
        </a>
        <button type="button" class="nav-toggle" onclick="toggleLandingNav()" aria-label="Fungua menu" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="links" id="landingNavLinks">
            <a href="#home">Mwanzo</a>
            <a href="#idara">Idara Zetu</a>
            <a href="#ratiba">Ratiba</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-terracotta">Dashboard</a>
                    <a href="{{ route('register') }}" class="btn-terracotta" style="background:transparent;border:2px solid #c85a32;color:#fbbf24;margin-left:8px;">Jisajili</a>
                @else
                    <a href="{{ route('login') }}" style="color:#fffdfa;font-weight:600;">Ingia</a>
                    <a href="{{ route('register') }}" class="btn-terracotta" style="margin-left:12px;">Jisajili</a>
                @endauth
            @endif
        </div>
    </nav>

    <!-- HERO SECTION - PHOTO COLLAGE CLUSTER -->
    <header id="home" class="hero-collage-container">
        
        <div class="hero-header-badge">
            <img src="{{ asset('images/eagt_logo.png') }}" alt="EAGT Logo">
            <span>PHILADELPHIA CHURCH (EAGT)</span>
        </div>

        <div class="collage-wrapper">
            
            <!-- Floating Photos Cluster -->
            <div class="collage-img img-top-center">
                <img src="{{ asset('images/1.png') }}" alt="Watu wakiomba pamoja kanisani">
                <span class="photo-caption">Ibada na Maombi</span>
            </div>

            <div class="collage-img img-top-left">
                <img src="{{ asset('images/2.png') }}" alt="Vijana katika ushirika wa kanisa">
                <span class="photo-caption">Karibu Kanisani</span>
            </div>

            <div class="collage-img img-top-right">
                <img src="{{ asset('images/3.png') }}" alt="Kwaya na huduma ya sifa">
                <span class="photo-caption">Sifa na Kuabudu</span>
            </div>

            <!-- Center Welcome Badge -->
            <div class="collage-center-text">
                <div class="hero-kicker" data-hero-kicker>Philadelphia Church EAGT</div>
                <div class="script-welcome">Karibu</div>
                <h2>PHILADELPHIA CHURCH</h2>
                <p class="hero-copy" data-hero-copy>Tunakukaribisha nyumbani kwa ibada, maombi, sifa na Neno la Mungu.</p>
                <a href="#ratiba" class="btn-terracotta" data-hero-button>Angalia Ratiba za Ibada</a>
                <div class="hero-location"><i class="fa-solid fa-location-dot"></i> Dar es Salaam, Mbagala Kingugi</div>
            </div>

            <div class="collage-img img-middle-left">
                <img src="{{ asset('images/4.png') }}" alt="Wamama katika huduma ya kanisa">
                <span class="photo-caption">Ushirika wa Wamama</span>
            </div>

            <div class="collage-img img-middle-right">
                <img src="{{ asset('images/5.png') }}" alt="Watoto wakifurahia huduma kanisani">
                <span class="photo-caption">Watoto na Familia</span>
            </div>

            <div class="collage-img img-bottom-left">
                <img src="{{ asset('images/6.png') }}" alt="Mafundisho ya Neno la Mungu">
                <span class="photo-caption">Mafundisho</span>
            </div>

            <div class="collage-img img-bottom-center">
                <img src="{{ asset('images/7.png') }}" alt="Kikundi cha huduma kikijadiliana">
                <span class="photo-caption">Tunapopatikana</span>
            </div>

            <div class="collage-img img-bottom-right">
                <img src="{{ asset('images/8.png') }}" alt="Ibada na sifa za pamoja Kingugi">
                <span class="photo-caption">Mbagala Kingugi</span>
            </div>

            <div class="hero-story-progress" aria-label="Hero slides">
                <button class="hero-story-dot active" type="button" aria-label="Karibu Kanisani"><span></span></button>
                <button class="hero-story-dot" type="button" aria-label="Huduma za Kanisa"><span></span></button>
                <button class="hero-story-dot" type="button" aria-label="Tunapopatikana"><span></span></button>
                <button class="hero-story-dot" type="button" aria-label="Karibu Ibada"><span></span></button>
            </div>

        </div>

    </header>

    <!-- Idara Section - 3D Cover Flow Carousel -->
    <section id="idara" class="section">
        <h2 class="section-title">Idara Zetu</h2>
        
        <div class="coverflow-container">
            <button class="nav-btn prev-btn" onclick="prevSlide()"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="nav-btn next-btn" onclick="nextSlide()"><i class="fa-solid fa-chevron-right"></i></button>
            
            <div class="coverflow-track">
                
                <!-- Card 1: Wamama (WWI) -->
                <div class="coverflow-card" onclick="selectDepartmentCard(0)">
                    <div class="card-img-holder">
                        <img src="{{ asset('images/4.png') }}" alt="Idara ya Wamama WWI">
                    </div>
                    <div class="card-info">
                        <h3>Idara ya Wamama (WWI)</h3>
                        <p>Ushirika wa wamama katika maombi, mafundisho na kusaidiana kiroho na kimwili, kulea familia bora.</p>
                    </div>
                </div>

                <!-- Card 2: Wababa -->
                <div class="coverflow-card" onclick="selectDepartmentCard(1)">
                    <div class="card-img-holder">
                        <img src="{{ asset('images/18.png') }}" alt="Idara ya Wababa">
                    </div>
                    <div class="card-info">
                        <h3>Idara ya Wababa</h3>
                        <p>Ushirika wa wababa katika mafundisho, maombi na kujenga familia imara zenye msingi wa Neno la Mungu.</p>
                    </div>
                </div>

                <!-- Card 3: Vijana (CGM) -->
                <div class="coverflow-card" onclick="selectDepartmentCard(2)">
                    <div class="card-img-holder">
                        <img src="{{ asset('images/20.jpg') }}" alt="Idara ya Vijana CGM">
                    </div>
                    <div class="card-info">
                        <h3>Idara ya Vijana (CGM)</h3>
                        <p>Kukutanisha vijana kwa ajili ya kumtumikia Mungu, kusifu, kujifunza Neno na kuendeleza vipaji vyao.</p>
                    </div>
                </div>

                <!-- Card 4: Watoto (CWI) -->
                <div class="coverflow-card" onclick="selectDepartmentCard(3)">
                    <div class="card-img-holder">
                        <img src="{{ asset('images/21.jpg') }}" alt="Idara ya Watoto CWI">
                    </div>
                    <div class="card-info">
                        <h3>Idara ya Watoto (CWI)</h3>
                        <p>Malezi ya kiroho kwa watoto kuanzia wakiwa wadogo ili wakue katika kumjua Mungu na maadili ya Kikristo.</p>
                    </div>
                </div>

                <!-- Card 5: Praise & Kwaya (KUSKI) -->
                <div class="coverflow-card" onclick="selectDepartmentCard(4)">
                    <div class="card-img-holder">
                        <img src="{{ asset('images/8.png') }}" alt="Idara ya Praise & Kwaya KUSKI">
                    </div>
                    <div class="card-info">
                        <h3>Praise &amp; Kwaya (KUSKI)</h3>
                        <p>Huduma ya uimbaji, sifa na kuabudu katika ibada mbalimbali za kanisa letu zote kwa nguvu ya Roho Mtakatifu.</p>
                    </div>
                </div>

            </div>

            <div class="coverflow-dots">
                <span class="dot" onclick="goToSlide(0)"></span>
                <span class="dot" onclick="goToSlide(1)"></span>
                <span class="dot" onclick="goToSlide(2)"></span>
                <span class="dot" onclick="goToSlide(3)"></span>
                <span class="dot" onclick="goToSlide(4)"></span>
            </div>
        </div>
    </section>

    <div class="department-story-modal" id="departmentStoryModal" onclick="closeDepartmentStory()">
        <div class="department-story-box" onclick="event.stopPropagation()">
            <button class="department-story-close" type="button" onclick="closeDepartmentStory()">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="department-story-hero" id="departmentStoryHero">
                <div>
                    <h3 id="departmentStoryTitle"></h3>
                    <p id="departmentStoryIntro"></p>
                </div>
            </div>
            <div class="department-story-content">
                <div class="department-story-panel">
                    <h4>Documentary ya Idara</h4>
                    <p id="departmentStoryDocumentary"></p>
                </div>
                <div class="department-story-grid">
                    <div class="department-story-panel">
                        <h4>Ratiba na Muda</h4>
                        <p id="departmentStorySchedule"></p>
                    </div>
                    <div class="department-story-panel">
                        <h4>Malengo ya Idara</h4>
                        <p id="departmentStoryGoals"></p>
                    </div>
                    <div class="department-story-panel">
                        <h4>Mambo ya Kuvutia</h4>
                        <ul id="departmentStoryHighlights" style="padding-left:18px;"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RATIBA SECTION - TWIN BUBBLE INFINITY SCHEDULE -->
    <section id="ratiba" class="section">
        <h2 class="section-title">Ratiba za Ibada</h2>
        
        <div class="schedule-wrapper">
            <img src="{{ asset('images/agenda_poster.png') }}" alt="Agenda du week-end" class="schedule-poster">

            <div class="schedule-banner-list">
                <article class="schedule-banner" style="--schedule-color: rgba(141, 66, 220, 0.96); --schedule-position: center 22%; --schedule-image: url('{{ asset('images/15.png') }}');">
                    <div class="schedule-banner-content">
                        <div>
                            <div class="schedule-day-label">Jumapili</div>
                            <div class="schedule-title">Ibada Kuu</div>
                            <div class="schedule-place">Philadelphia Church EAGT, Mbagala Kingugi</div>
                        </div>
                        <div class="schedule-time-block">
                            <span>Kila Jumapili</span>
                            <div class="schedule-time-row"><small>Asubuhi</small><strong>02:30</strong></div>
                            <div class="schedule-time-row"><small>Mchana</small><strong>12:30</strong></div>
                        </div>
                    </div>
                </article>

                <article class="schedule-banner" style="--schedule-color: rgba(4, 103, 185, 0.96); --schedule-position: center 18%; --schedule-image: url('{{ asset('images/4.png') }}');">
                    <div class="schedule-banner-content">
                        <div>
                            <div class="schedule-day-label">Jumatatu</div>
                            <div class="schedule-title">Ibada ya Wamama</div>
                            <div class="schedule-place">WWK, maombi na mafundisho ya wanawake</div>
                        </div>
                        <div class="schedule-time-block">
                            <span>Kila Jumatatu</span>
                            <div class="schedule-time-row"><small>Jioni</small><strong>10:00</strong></div>
                            <div class="schedule-time-row"><small>Jioni</small><strong>12:00</strong></div>
                        </div>
                    </div>
                </article>

                <article class="schedule-banner" style="--schedule-color: rgba(0, 162, 183, 0.96); --schedule-position: center 20%; --schedule-image: url('{{ asset('images/8.png') }}');">
                    <div class="schedule-banner-content">
                        <div>
                            <div class="schedule-day-label">Jumanne</div>
                            <div class="schedule-title">Mazoezi ya Praise</div>
                            <div class="schedule-place">Huduma ya sifa na kuabudu</div>
                        </div>
                        <div class="schedule-time-block">
                            <span>Kila Jumanne</span>
                            <div class="schedule-time-row"><small>Jioni</small><strong>12:00</strong></div>
                            <div class="schedule-time-row"><small>Usiku</small><strong>02:00</strong></div>
                        </div>
                    </div>
                </article>

                <article class="schedule-banner" style="--schedule-color: rgba(15, 176, 188, 0.96); --schedule-position: center 26%; --schedule-image: url('{{ asset('images/13.png') }}');">
                    <div class="schedule-banner-content">
                        <div>
                            <div class="schedule-day-label">Jumatano</div>
                            <div class="schedule-title">Ibada ya Mafundisho</div>
                            <div class="schedule-place">Mafundisho ya Neno la Mungu</div>
                        </div>
                        <div class="schedule-time-block">
                            <span>Kila Jumatano</span>
                            <div class="schedule-time-row"><small>Jioni</small><strong>10:30</strong></div>
                            <div class="schedule-time-row"><small>Jioni</small><strong>12:00</strong></div>
                        </div>
                    </div>
                </article>

                <article class="schedule-banner" style="--schedule-color: rgba(1, 110, 197, 0.96); --schedule-position: center 20%; --schedule-image: url('{{ asset('images/17.png') }}');">
                    <div class="schedule-banner-content">
                        <div>
                            <div class="schedule-day-label">Alhamisi</div>
                            <div class="schedule-title">Mazoezi ya Kwaya</div>
                            <div class="schedule-place">Kwaya na maandalizi ya huduma</div>
                        </div>
                        <div class="schedule-time-block">
                            <span>Kila Alhamisi</span>
                            <div class="schedule-time-row"><small>Jioni</small><strong>12:00</strong></div>
                            <div class="schedule-time-row"><small>Usiku</small><strong>02:00</strong></div>
                        </div>
                    </div>
                </article>

                <article class="schedule-banner" style="--schedule-color: rgba(234, 88, 12, 0.96); --schedule-position: center 24%; --schedule-image: url('{{ asset('images/11.png') }}');">
                    <div class="schedule-banner-content">
                        <div>
                            <div class="schedule-day-label">Ijumaa</div>
                            <div class="schedule-title">Maombi na Maombezi</div>
                            <div class="schedule-place">Ibada ya maombi ya pamoja</div>
                        </div>
                        <div class="schedule-time-block">
                            <span>Kila Ijumaa</span>
                            <div class="schedule-time-row"><small>Jioni</small><strong>10:30</strong></div>
                            <div class="schedule-time-row"><small>Jioni</small><strong>12:30</strong></div>
                        </div>
                    </div>
                </article>

                <article class="schedule-banner" style="--schedule-color: rgba(239, 92, 42, 0.96); --schedule-position: center 16%; --schedule-image: url('{{ asset('images/17.png') }}');">
                    <div class="schedule-banner-content">
                        <div>
                            <div class="schedule-day-label">Jumamosi</div>
                            <div class="schedule-title">Maandalizi ya Ibada</div>
                            <div class="schedule-place">Maandalizi ya ibada na huduma za Jumapili</div>
                        </div>
                        <div class="schedule-time-block">
                            <span>Kila Jumamosi</span>
                            <div class="schedule-time-row"><small>Jioni</small><strong>09:00</strong></div>
                            <div class="schedule-time-row"><small>Usiku</small><strong>02:00</strong></div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- ★ JUMAPILI - Peke yake, juu, katikati ★ -->
            <div class="schedule-sunday-row">
                <div class="twin-bubble-item-sunday">
                    <div class="bubble-part bubble-left bubble-white">
                        <div class="bubble-day">Jumapili</div>
                        <div class="bubble-time-big">02:30</div>
                    </div>
                    <div class="bubble-part bubble-right bubble-gold">
                        <div class="bubble-title">Ibada Kuu</div>
                        <div class="bubble-sub">Saa 2:30 Asubuhi - Saa 12:30 Mchana</div>
                    </div>
                </div>
            </div>

            <!-- Siku za wiki - mbili mbili kwa safu -->
            <div class="schedule-pairs-grid">

                <!-- Jumatatu -->
                <div class="twin-bubble-item">
                    <div class="bubble-part bubble-left bubble-white">
                        <div class="bubble-day">Jumatatu</div>
                        <div class="bubble-time-big">10:00</div>
                    </div>
                    <div class="bubble-part bubble-right bubble-terracotta">
                        <div class="bubble-title">Ibada ya Wamama (WWK)</div>
                        <div class="bubble-sub">Saa 10:00 Jioni - Saa 12:00 Jioni</div>
                    </div>
                </div>

                <!-- Jumanne -->
                <div class="twin-bubble-item">
                    <div class="bubble-part bubble-left bubble-green">
                        <div class="bubble-title">Mazoezi ya Praise</div>
                        <div class="bubble-sub">Saa 12:00 Jioni - Saa 2:00 Usiku</div>
                    </div>
                    <div class="bubble-part bubble-right bubble-white">
                        <div class="bubble-day">Jumanne</div>
                        <div class="bubble-time-big">12:00</div>
                    </div>
                </div>

                <!-- Jumatano -->
                <div class="twin-bubble-item">
                    <div class="bubble-part bubble-left bubble-white">
                        <div class="bubble-day">Jumatano</div>
                        <div class="bubble-time-big">10:30</div>
                    </div>
                    <div class="bubble-part bubble-right bubble-teal">
                        <div class="bubble-title">Ibada ya Mafundisho</div>
                        <div class="bubble-sub">Saa 10:30 Jioni - Saa 12:00 Jioni</div>
                    </div>
                </div>

                <!-- Alhamisi -->
                <div class="twin-bubble-item">
                    <div class="bubble-part bubble-left bubble-navy">
                        <div class="bubble-title">Mazoezi ya Kwaya</div>
                        <div class="bubble-sub">Saa 12:00 Jioni - Saa 2:00 Usiku</div>
                    </div>
                    <div class="bubble-part bubble-right bubble-white">
                        <div class="bubble-day">Alhamisi</div>
                        <div class="bubble-time-big">12:00</div>
                    </div>
                </div>

                <!-- Ijumaa -->
                <div class="twin-bubble-item">
                    <div class="bubble-part bubble-left bubble-white">
                        <div class="bubble-day">Ijumaa</div>
                        <div class="bubble-time-big">10:30</div>
                    </div>
                    <div class="bubble-part bubble-right bubble-charcoal">
                        <div class="bubble-title">Ibada ya Maombi na Maombezi</div>
                        <div class="bubble-sub">Saa 10:30 Jioni - Saa 12:30 Jioni</div>
                    </div>
                </div>

                <!-- Jumamosi -->
                <div class="twin-bubble-item">
                    <div class="bubble-part bubble-left bubble-crimson">
                        <div class="bubble-title">Maandalizi ya Ibada</div>
                        <div class="bubble-sub">Saa 9:00 Jioni - Saa 2:00 Usiku</div>
                    </div>
                    <div class="bubble-part bubble-right bubble-white">
                        <div class="bubble-day">Jumamosi</div>
                        <div class="bubble-time-big">09:00</div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer>
        <img src="{{ asset('images/eagt_logo.png') }}" alt="EAGT Logo" class="footer-logo">
        <p>&copy; {{ date('Y') }} PHILADELPHIA CHURCH (EAGT). Haki zote zimehifadhiwa.</p>
    </footer>

    <!-- Scripts -->
    <script>
        // Hero story motion — Pic 2 photos applied to all themes
        const heroStories = [
            {
                kicker: 'Philadelphia Church EAGT',
                script: 'Karibu',
                title: 'PHILADELPHIA CHURCH',
                copy: 'Tunakukaribisha nyumbani kwa ibada, maombi, sifa na Neno la Mungu.',
                button: 'Angalia Ratiba za Ibada',
                featured: [3, 4, 2],
                photos: [
                    ["{{ asset('images/1.png') }}", 'Watu wakiomba pamoja kanisani', 'Ibada na Maombi'],
                    ["{{ asset('images/2.png') }}", 'Vijana katika ushirika wa kanisa', 'Karibu Kanisani'],
                    ["{{ asset('images/3.png') }}", 'Kwaya na huduma ya sifa', 'Sifa na Kuabudu'],
                    ["{{ asset('images/4.png') }}", 'Wamama katika huduma ya kanisa', 'Ushirika wa Wamama'],
                    ["{{ asset('images/5.png') }}", 'Watoto na huduma ya familia', 'Watoto na Familia'],
                    ["{{ asset('images/6.png') }}", 'Mafundisho ya Neno la Mungu', 'Mafundisho'],
                    ["{{ asset('images/7.png') }}", 'Kikundi cha huduma kikijadiliana', 'Tunapopatikana'],
                    ["{{ asset('images/8.png') }}", 'Ibada na sifa za pamoja', 'Mbagala Kingugi']
                ]
            },
            {
                kicker: 'Huduma za Kanisa',
                script: 'Huduma',
                title: 'TUMIKIA PAMOJA NASI',
                copy: 'Watoto, vijana, wamama, kwaya, maombi na mafundisho kwa ukuaji wa kiroho.',
                button: 'Tazama Idara Zetu',
                featured: [3, 4, 2],
                target: '#idara',
                photos: [
                    ["{{ asset('images/9.png') }}", 'Watu wakiomba pamoja', 'Maombi'],
                    ["{{ asset('images/10.png') }}", 'Vijana katika ushirika', 'Vijana CGM'],
                    ["{{ asset('images/11.png') }}", 'Kwaya na sifa za KUSKI', 'Kwaya KUSKI'],
                    ["{{ asset('images/12.png') }}", 'Wamama wa WWI wakihudumu', 'Wamama WWI'],
                    ["{{ asset('images/13.png') }}", 'Watoto wa CWI wakifurahia', 'Watoto CWI'],
                    ["{{ asset('images/15.png') }}", 'Mafundisho na maandiko', 'Mafundisho'],
                    ["{{ asset('images/16.png') }}", 'Ujenzi na huduma za kanisa', 'Idara ya Ujenzi'],
                    ["{{ asset('images/18.png') }}", 'Wababa katika ushirika', 'Idara ya Wababa']
                ]
            },
            {
                kicker: 'Tunapopatikana',
                script: 'Mbagala',
                title: 'DAR ES SALAAM',
                copy: 'Tupo Mbagala Kingugi, tukikukaribisha wewe na familia yako kumwabudu Mungu.',
                button: 'Njoo Ibada Yetu',
                featured: [6, 7],
                target: '#ratiba',
                photos: [
                    ["{{ asset('images/15.png') }}", 'Watu wakiomba pamoja', 'Maombi ya Asubuhi'],
                    ["{{ asset('images/16.png') }}", 'Vijana wa kanisa', 'Vijana wa Dar'],
                    ["{{ asset('images/18.png') }}", 'Muimbaji kwenye huduma', 'Sifa'],
                    ["{{ asset('images/9.png') }}", 'Watu katika ibada', 'Ibada'],
                    ["{{ asset('images/10.png') }}", 'Familia na watoto', 'Familia'],
                    ["{{ asset('images/11.png') }}", 'Mafundisho ya Neno', 'Mbagala'],
                    ["{{ asset('images/12.png') }}", 'Muonekano wa jiji Dar es Salaam', 'Tunapopatikana'],
                    ["{{ asset('images/13.png') }}", 'Njia ya kuelekea Kingugi', 'Karibu Kingugi']
                ]
            },
            {
                kicker: 'Karibu Nyumbani',
                script: 'Twende',
                title: 'TUKUABUDU PAMOJA',
                copy: 'Mlango uko wazi kwa wote wanaotafuta faraja, ushirika na nguvu mpya.',
                button: 'Karibu Jumapili',
                featured: [2, 5, 0],
                photos: [
                    ["{{ asset('images/11.png') }}", 'Watu wakiomba pamoja Jumapili', 'Ibada Jumapili'],
                    ["{{ asset('images/12.png') }}", 'Vijana katika ushirika wa Jumapili', 'Ushirika'],
                    ["{{ asset('images/13.png') }}", 'Sauti na muziki wa sifa', 'Sifa'],
                    ["{{ asset('images/15.png') }}", 'Familia katika ibada ya Jumapili', 'Familia'],
                    ["{{ asset('images/16.png') }}", 'Watoto wakifurahia huduma', 'Watoto'],
                    ["{{ asset('images/18.png') }}", 'Kikundi kikijifunza Neno', 'Tumikia'],
                    ["{{ asset('images/9.png') }}", 'Watu wakishirikiana kanisani', 'Kanisa Letu'],
                    ["{{ asset('images/10.png') }}", 'Ibada ya pamoja Kingugi', 'Njoo Uabudu']
                ]
            }
        ];

        const heroCard = document.querySelector('.collage-center-text');
        const heroKicker = document.querySelector('[data-hero-kicker]');
        const heroScript = document.querySelector('.script-welcome');
        const heroTitle = document.querySelector('.collage-center-text h2');
        const heroCopy = document.querySelector('[data-hero-copy]');
        const heroButton = document.querySelector('[data-hero-button]');
        const heroPhotos = Array.from(document.querySelectorAll('.collage-img'));
        const heroDots = Array.from(document.querySelectorAll('.hero-story-dot'));
        let heroIndex = 0;
        let heroTimer;

        function setHeroStory(index) {
            heroIndex = index;
            const story = heroStories[heroIndex];
            heroCard.classList.remove('is-changing');
            void heroCard.offsetWidth;
            heroCard.classList.add('is-changing');

            heroKicker.textContent = story.kicker;
            heroScript.textContent = story.script;
            heroTitle.textContent = story.title;
            heroCopy.textContent = story.copy;
            heroButton.textContent = story.button;
            heroButton.href = story.target || '#ratiba';

            heroPhotos.forEach((photo, photoIndex) => {
                const isFeatured = story.featured.includes(photoIndex);
                const image = photo.querySelector('img');
                const caption = photo.querySelector('.photo-caption');
                const nextPhoto = story.photos[photoIndex];

                photo.classList.add('is-swapping');

                window.setTimeout(() => {
                    image.src = nextPhoto[0];
                    image.alt = nextPhoto[1];
                    if (caption) caption.textContent = nextPhoto[2];
                    photo.classList.remove('is-swapping');
                }, 220 + (photoIndex * 28));

                photo.classList.toggle('is-featured', isFeatured);
                photo.classList.toggle('is-dimmed', !isFeatured);
            });

            heroDots.forEach((dot, dotIndex) => {
                dot.classList.toggle('active', dotIndex === heroIndex);
            });
        }

        function nextHeroStory() {
            setHeroStory((heroIndex + 1) % heroStories.length);
        }

        function restartHeroStory(index) {
            clearInterval(heroTimer);
            setHeroStory(index);
            heroTimer = setInterval(nextHeroStory, 4800);
        }

        heroDots.forEach((dot, index) => {
            dot.addEventListener('click', () => restartHeroStory(index));
        });

        setHeroStory(0);
        heroTimer = setInterval(nextHeroStory, 4800);

        // 3D Cover Flow Logic
        let currentIndex = 0;
        const cards = document.querySelectorAll('.coverflow-card');
        const dots = document.querySelectorAll('.dot');
        const totalCards = cards.length;
        const departmentStories = [
            {
                title: 'Idara ya Wamama (WWI)',
                image: "{{ asset('images/4.png') }}",
                intro: 'Ushirika wa wamama katika maombi, mafundisho na kusaidiana.',
                documentary: 'Idara ya Wamama inawaunganisha wamama katika maombi, mafundisho ya Neno, malezi ya familia na huduma za kusaidiana. Ni sehemu ya kujenga wanawake imara kiroho na kijamii.',
                schedule: 'Kila Jumatatu saa 10:00 jioni hadi 12:00 jioni kwa maombi, mafundisho na ushirika.',
                goals: 'Kukuza maisha ya kiroho, kujenga familia zenye msingi wa Neno, na kushiriki huduma za kanisa kwa upendo.',
                highlights: ['Ushirika wa karibu wa wamama', 'Mafundisho ya familia na imani', 'Huduma za kusaidiana']
            },
            {
                title: 'Idara ya Wababa',
                image: "{{ asset('images/18.png') }}",
                intro: 'Wababa katika mafundisho, maombi na uongozi wa familia.',
                documentary: 'Idara ya Wababa inalenga kuwajenga wanaume kuwa viongozi bora wa kiroho, familia na jamii. Inahamasisha uwajibikaji, maombi na mshikamano.',
                schedule: 'Jumamosi saa 10:00 jioni kwa ushirika, maombi, majadiliano na mafundisho.',
                goals: 'Kujenga wanaume wenye msimamo wa imani, hekima ya familia na moyo wa huduma.',
                highlights: ['Ushauri wa kifamilia', 'Mafundisho ya uongozi', 'Mshikamano wa wanaume']
            },
            {
                title: 'Idara ya Vijana (CGM)',
                image: "{{ asset('images/20.jpg') }}",
                intro: 'Vijana katika huduma, vipaji, sifa na kujifunza Neno.',
                documentary: 'Idara ya Vijana ni jukwaa la vijana kumtumikia Mungu, kukuza vipaji, kujifunza Neno na kushiriki shughuli za kiroho na kijamii kwa nidhamu.',
                schedule: 'Jumamosi saa 9:00 jioni kwa mazoezi, mafundisho, ibada za vijana na maandalizi ya huduma.',
                goals: 'Kuinua kizazi cha vijana wenye imani, maadili, vipaji na bidii katika huduma.',
                highlights: ['Vipaji na ubunifu', 'Ushirika wa vijana', 'Huduma na matamasha ya vijana']
            },
            {
                title: 'Idara ya Watoto (CWI)',
                image: "{{ asset('images/21.jpg') }}",
                intro: 'Malezi ya watoto katika imani na maadili ya Kikristo.',
                documentary: 'Idara ya Watoto inahakikisha watoto wanalelewa katika msingi wa Neno la Mungu kupitia masomo, nyimbo, michezo ya kujifunza na malezi bora.',
                schedule: 'Jumapili wakati wa ibada kuu kwa darasa la watoto, nyimbo na masomo ya Biblia.',
                goals: 'Kuwajenga watoto wamjue Mungu mapema, wawe na nidhamu na wajiamini katika imani.',
                highlights: ['Nyimbo na michezo ya kujifunza', 'Masomo ya Biblia kwa watoto', 'Malezi ya kiroho mapema']
            },
            {
                title: 'Praise & Kwaya (KUSKI)',
                image: "{{ asset('images/8.png') }}",
                intro: 'Huduma ya sifa, kuabudu na uimbaji wa kwaya.',
                documentary: 'Idara ya Praise na Kwaya inaongoza kanisa katika sifa na kuabudu, ikiandaa waimbaji kwa nidhamu, ubora na moyo wa huduma.',
                schedule: 'Jumanne na Alhamisi saa 12:00 jioni hadi 2:00 usiku kwa mazoezi ya praise na kwaya.',
                goals: 'Kuongoza ibada kwa ubora, kukuza waimbaji na kueneza injili kupitia muziki.',
                highlights: ['Mazoezi ya sauti na vyombo', 'Huduma za ibada na matukio', 'Kukuza vipaji vya uimbaji']
            }
        ];

        function updateCoverflow() {
            cards.forEach((card, i) => {
                card.className = 'coverflow-card';
                
                let diff = i - currentIndex;
                
                if (diff < -2) diff += totalCards;
                if (diff > 2) diff -= totalCards;

                if (diff === 0) {
                    card.classList.add('active');
                } else if (diff === -1) {
                    card.classList.add('prev-1');
                } else if (diff === -2 || diff < -2) {
                    card.classList.add('prev-2');
                } else if (diff === 1) {
                    card.classList.add('next-1');
                } else if (diff === 2 || diff > 2) {
                    card.classList.add('next-2');
                }
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentIndex);
            });
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % totalCards;
            updateCoverflow();
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + totalCards) % totalCards;
            updateCoverflow();
        }

        function goToSlide(index) {
            currentIndex = index;
            updateCoverflow();
        }

        function selectDepartmentCard(index) {
            if (currentIndex !== index) {
                goToSlide(index);
                return;
            }
            openDepartmentStory(index);
        }

        function openDepartmentStory(index) {
            const story = departmentStories[index];
            if (!story) return;

            document.getElementById('departmentStoryHero').style.backgroundImage = `url('${story.image}')`;
            document.getElementById('departmentStoryTitle').textContent = story.title;
            document.getElementById('departmentStoryIntro').textContent = story.intro;
            document.getElementById('departmentStoryDocumentary').textContent = story.documentary;
            document.getElementById('departmentStorySchedule').textContent = story.schedule;
            document.getElementById('departmentStoryGoals').textContent = story.goals;

            const highlights = document.getElementById('departmentStoryHighlights');
            highlights.innerHTML = story.highlights.map(item => `<li>${item}</li>`).join('');
            document.getElementById('departmentStoryModal').classList.add('open');
        }

        function closeDepartmentStory() {
            document.getElementById('departmentStoryModal').classList.remove('open');
        }

        function toggleLandingNav(force) {
            const links = document.getElementById('landingNavLinks');
            const button = document.querySelector('.nav-toggle');
            if (!links) return;
            const shouldOpen = typeof force === 'boolean' ? force : !links.classList.contains('open');
            links.classList.toggle('open', shouldOpen);
            if (button) button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
        }

        document.querySelectorAll('#landingNavLinks a').forEach(link => {
            link.addEventListener('click', () => toggleLandingNav(false));
        });

        updateCoverflow();

        let autoLoop = setInterval(nextSlide, 3500);

        const container = document.querySelector('.coverflow-container');
        container.addEventListener('mouseenter', () => clearInterval(autoLoop));
        container.addEventListener('mouseleave', () => {
            clearInterval(autoLoop);
            autoLoop = setInterval(nextSlide, 3500);
        });
    </script>
</body>
</html>
