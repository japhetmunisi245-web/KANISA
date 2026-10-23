<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard — PHILADELPHIA CHURCH</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-w: 250px;
            --sidebar-bg: #1b1f3b;
            --sidebar-hover: rgba(255,255,255,.07);
            --sidebar-active: rgba(99,102,241,.25);
            --sidebar-active-border: #6366f1;
            --accent: #6366f1;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --accent-amber: #f59e0b;
            --bg: #f5f6fa;
            --card: #ffffff;
            --text: #1e2130;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body { max-width: 100%; overflow-x: hidden; }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); display:flex; min-height:100vh; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 22px 20px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand .icon {
            width: 38px; height: 38px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            color: #fff;
        }
        .sidebar-brand .brand-name {
            font-size: .85rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .sidebar-brand .brand-sub {
            font-size: .68rem;
            color: rgba(255,255,255,.5);
            font-weight: 400;
        }
        .sidebar-section-label {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
            padding: 18px 20px 6px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: rgba(255,255,255,.65);
            font-size: .88rem;
            font-weight: 500;
            cursor: pointer;
            border-left: 3px solid transparent;
            transition: all .2s;
            text-decoration: none;
        }
        .nav-item:hover { background: var(--sidebar-hover); color: #fff; }
        .nav-item.active {
            background: var(--sidebar-active);
            color: #fff;
            border-left-color: var(--sidebar-active-border);
            font-weight: 600;
        }
        .nav-item .nav-icon { width: 18px; text-align: center; font-size: .9rem; }
        .nav-item .badge {
            margin-left: auto;
            background: var(--accent-red);
            color: #fff;
            font-size: .65rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            min-width: 20px;
            text-align: center;
        }
        .sidebar-spacer { flex: 1; }
        .sidebar-logout {
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOP BAR ── */
        .topbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
        }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .mobile-menu-btn {
            display: none;
            width: 42px;
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #ffffff;
            color: var(--accent);
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            cursor: pointer;
        }
        .mobile-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .48);
            z-index: 90;
        }
        .topbar-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .9rem;
            border: none;
            cursor: pointer;
            overflow: hidden;
            padding: 0;
            box-shadow: 0 4px 14px rgba(99,102,241,.18);
            transition: transform .2s, box-shadow .2s;
        }
        .topbar-avatar:hover { transform: translateY(-1px); box-shadow: 0 7px 18px rgba(99,102,241,.25); }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .topbar-user { font-size: .88rem; font-weight: 600; color: var(--text); }
        .topbar-role {
            font-size: .72rem; font-weight: 700; padding: 3px 10px; border-radius: 999px;
        }
        .role-kiongozi { background: #fef3c7; color: #92400e; }
        .role-mshirika { background: #dbeafe; color: #1e40af; }
        .notification-wrap { position: relative; }
        .notification-trigger {
            border: 1px solid #c7d2fe;
            border-radius: 10px;
            background: #eef2ff;
            color: #4338ca;
            padding: 8px 11px;
            font: inherit;
            font-size: .82rem;
            font-weight: 800;
            cursor: pointer;
        }
        .notification-trigger:hover { background: #e0e7ff; }
        .notification-count { margin-left: 5px; background: #4f46e5; color: #fff; border-radius: 999px; padding: 1px 6px; font-size: .7rem; }
        .notification-panel {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            width: min(440px, calc(100vw - 32px));
            max-height: min(620px, calc(100vh - 92px));
            overflow: auto;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 18px 42px rgba(15,23,42,.2);
            z-index: 80;
        }
        .notification-panel.open { display: block; }
        .notification-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
        .notification-panel-head strong { color: var(--text); font-size: 1rem; }
        .notification-close { border: 0; background: transparent; color: var(--text-muted); font-size: 1.35rem; cursor: pointer; }
        .notification-item { padding: 14px 0; border-bottom: 1px solid var(--border); }
        .notification-item:last-child { border-bottom: 0; }
        .notification-item-title { font-weight: 800; color: var(--text); }
        .notification-item-content { margin: 5px 0; color: var(--text-muted); line-height: 1.45; white-space: pre-line; }
        .notification-item-meta { font-size: .74rem; color: var(--text-muted); }
        /* Secretary-to-leader messages stay available while a leader works in
           any dashboard panel instead of being hidden in the top navigation. */
        .notification-wrap.notification-wrap-floating {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 90;
        }
        .notification-wrap-floating .notification-trigger {
            border: 0;
            border-radius: 999px;
            background: #4338ca;
            color: #fff;
            padding: 13px 18px;
            font-size: .82rem;
            box-shadow: 0 12px 28px rgba(67, 56, 202, .35);
        }
        .notification-wrap-floating .notification-trigger:hover { background: #3730a3; }
        .notification-wrap-floating .notification-count { background: #ef4444; }
        .notification-wrap-floating .notification-panel {
            top: auto;
            bottom: calc(100% + 12px);
            left: auto;
            right: 0;
        }
        @media (max-width: 700px) {
            .notification-wrap.notification-wrap-floating { right: 16px; bottom: 16px; }
            .notification-wrap-floating .notification-trigger { padding: 12px 15px; }
            .notification-wrap-floating .notification-panel { right: 0; }
        }

        /* ── CONTENT PANELS ── */
        .content { padding: 28px 32px; flex: 1; }
        .panel { display: none; }
        .panel.active { display: block; }

        /* ── STAT CARDS ── */
        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 18px; margin-bottom: 28px; }
        .stat-card {
            background: var(--card);
            border-radius: 16px;
            padding: 20px 22px;
            box-shadow: var(--shadow);
            border-bottom: 3px solid var(--border);
            transition: transform .2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-label { font-size: .78rem; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
        .stat-value { font-size: 2rem; font-weight: 800; color: var(--text); }
        .stat-card.blue  { border-bottom-color: #3b82f6; }
        .stat-card.green { border-bottom-color: var(--accent-green); }
        .stat-card.amber { border-bottom-color: var(--accent-amber); }
        .stat-card.purple{ border-bottom-color: #8b5cf6; }

        /* ── CARDS ── */
        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 22px;
        }
        .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-title .icon-box {
            width: 34px; height: 34px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem;
        }

        /* ── TABLE ── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            text-align: left;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
        }
        .data-table td { padding: 12px 16px; font-size: .88rem; border-bottom: 1px solid #f3f4f6; }
        .data-table tr:hover td { background: #fafafa; }
        .data-table tr:last-child td { border-bottom: none; }

        /* ── MEMBER AVATAR ── */
        .member-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .8rem;
            flex-shrink: 0;
            overflow: hidden;
        }
        .member-avatar img { width:100%; height:100%; object-fit:cover; }
        .member-info { display: flex; align-items: center; gap: 10px; }

        /* ── MEMBER CARDS GRID ── */
        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }
        .member-card {
            background: var(--card);
            border-radius: 18px;
            padding: 24px 18px 18px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
            border: 1.5px solid var(--border);
            transition: transform .2s, box-shadow .2s;
            position: relative;
        }
        .member-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(99,102,241,.15); }
        .member-card-photo {
            width: 72px; height: 72px;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 800; color: #fff;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(99,102,241,.25);
        }
        .member-card-photo img { width:100%; height:100%; object-fit:cover; }
        .member-card-name { font-size: .95rem; font-weight: 700; color: var(--text); }
        .member-card-email { font-size: .75rem; color: var(--text-muted); word-break: break-all; }
        .member-card-phone { font-size: .78rem; color: var(--text-muted); }
        .member-card-remove {
            position: absolute; top: 10px; right: 10px;
            background: #fee2e2; color: var(--accent-red);
            border: none; border-radius: 8px;
            padding: 5px 9px; font-size: .72rem; font-weight: 700;
            cursor: pointer; transition: background .2s;
        }
        .member-card-remove:hover { background: var(--accent-red); color: #fff; }
        .member-card-self {
            position: absolute; top: 10px; right: 10px;
            background: #ede9fe; color: var(--accent);
            border-radius: 8px; padding: 4px 9px;
            font-size: .68rem; font-weight: 700;
        }

        /* ── BADGE ── */
        .badge-pill {
            font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 999px;
            display: inline-block;
        }
        .badge-kiongozi { background: #fef3c7; color: #92400e; }
        .badge-mshirika { background: #dbeafe; color: #1e40af; }
        .badge-pending  { background: #fef9c3; color: #854d0e; }
        .badge-read     { background: #d1fae5; color: #065f46; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 10px; font-size: .85rem;
            font-weight: 600; cursor: pointer; border: none; transition: all .2s;
            text-decoration: none;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #4f46e5; transform: translateY(-1px); }
        .btn-danger  { background: var(--accent-red); color: #fff; font-size: .8rem; padding: 6px 14px; }
        .btn-danger:hover { background: #dc2626; }
        .btn-success { background: var(--accent-green); color: #fff; }
        .btn-success:hover { background: #059669; }
        .btn-secondary { background: #e5e7eb; color: var(--text); }
        .btn-secondary:hover { background: #d1d5db; transform: translateY(-1px); }
        .btn-amber   { background: var(--accent-amber); color: #fff; }
        .btn-amber:hover { background: #d97706; }
        .btn-outline { background: transparent; color: var(--accent); border: 1.5px solid var(--accent); }
        .btn-outline:hover { background: var(--accent); color: #fff; }
        .btn-sm { padding: 6px 13px; font-size: .78rem; border-radius: 8px; }

        /* ── FORM ── */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: .82rem; font-weight: 600; color: var(--text); margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 10px 14px; border: 1.5px solid var(--border);
            border-radius: 10px; font-size: .9rem; font-family: inherit;
            transition: border-color .2s;
            background: #fff;
        }
        .form-control:focus { outline: none; border-color: var(--accent); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

        /* ── POSITION COUNTER ── */
        .position-counter {
            display: flex; flex-direction: column; align-items: center;
            background: #fffbeb; border: 1.5px solid #fcd34d;
            border-radius: 14px; padding: 14px 22px;
        }
        .position-counter .pc-label { font-size: .7rem; color: #92400e; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
        .position-counter .pc-value { font-size: 2rem; font-weight: 900; color: #d97706; }
        .position-counter .pc-badge { font-size: .68rem; font-weight: 700; padding: 2px 10px; border-radius: 999px; margin-top: 4px; }

        /* ── DEPT CARD ── */
        .dept-card-list { display: grid; grid-template-columns: 1fr; gap: 14px; }
        .dept-card-item {
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: 14px; padding: 16px;
            border-left: 4px solid var(--accent);
            transition: box-shadow .2s;
        }
        .dept-card-item:hover { box-shadow: 0 4px 16px rgba(99,102,241,.12); }
        .dept-card-item h5 { font-size: .95rem; font-weight: 700; color: var(--text); }
        .dept-card-item p { font-size: .78rem; color: var(--text-muted); margin-top: 3px; }

        /* ── ANNOUNCEMENT / LETTER CARD ── */
        .item-card {
            border-left: 4px solid var(--accent);
            background: #f8f9ff;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }
        .item-card.green { border-left-color: var(--accent-green); background: #f0fdf4; }
        .item-card.amber { border-left-color: var(--accent-amber); background: #fffbeb; }
        .item-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .item-card-title { font-weight: 700; font-size: .9rem; color: var(--text); }
        .item-card-date { font-size: .75rem; color: var(--text-muted); }
        .item-card-body { font-size: .85rem; color: #4b5563; }

        /* ── WORSHIP SCHEDULE ── */
        .worship-schedule-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
        }
        .worship-schedule-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }
        .worship-schedule-head .icon-box {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef2ff;
            color: var(--accent);
        }
        .worship-schedule-head h4 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text);
        }
        .worship-schedule-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            background: #ffffff;
        }
        .worship-sunday-row {
            display: flex;
            justify-content: center;
        }
        .worship-schedule-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 18px;
        }
        .worship-schedule-item {
            min-height: 96px;
            display: grid;
            grid-template-columns: minmax(150px, .9fr) minmax(180px, 1.1fr);
            border-radius: 48px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid var(--border);
            box-shadow: 0 10px 26px rgba(30, 33, 48, .06);
        }
        .worship-sunday-row .worship-schedule-item {
            width: min(100%, 700px);
        }
        .worship-time,
        .worship-detail {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 16px 24px;
        }
        .worship-time {
            align-items: center;
            text-align: center;
            background: #ffffff;
        }
        .worship-day {
            font-size: .72rem;
            font-weight: 900;
            color: #c85a32;
            letter-spacing: 2.4px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .worship-hour {
            font-family: monospace;
            font-size: 1.75rem;
            line-height: 1;
            font-weight: 900;
            color: #1d3557;
        }
        .worship-detail {
            color: #ffffff;
            background: var(--schedule-accent);
        }
        .worship-title {
            font-size: 1rem;
            line-height: 1.15;
            font-weight: 900;
        }
        .worship-sub {
            margin-top: 6px;
            font-size: .78rem;
            line-height: 1.25;
            font-weight: 700;
            opacity: .95;
        }
        .worship-schedule-item.flip {
            grid-template-columns: minmax(180px, 1.1fr) minmax(150px, .9fr);
        }
        .worship-schedule-item.flip .worship-detail {
            order: 1;
        }
        .worship-schedule-item.flip .worship-time {
            order: 2;
        }

        /* ── HELP ── */
        .help-sent-card { background: #f0fdf4; border-left: 4px solid var(--accent-green); border-radius: 10px; padding: 14px; margin-bottom: 10px; }

        /* ── TOP REMINDERS ── */
        .announcement-ticker {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-left: 4px solid var(--accent-green);
            border-radius: 14px;
            box-shadow: var(--shadow);
            margin-bottom: 18px;
            overflow: hidden;
        }
        .announcement-ticker-label {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px;
            background: #ecfdf5;
            color: #047857;
            font-size: .78rem;
            font-weight: 900;
            text-transform: uppercase;
        }
        .announcement-ticker-track {
            min-width: 0;
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
        }
        .announcement-ticker-line {
            display: inline-flex;
            align-items: center;
            gap: 32px;
            min-width: 100%;
            padding: 0 16px;
            animation: ticker-scroll 28s linear infinite;
        }
        .announcement-ticker:hover .announcement-ticker-line { animation-play-state: paused; }
        .ticker-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text);
            font-size: .86rem;
            font-weight: 700;
        }
        .ticker-item small {
            color: var(--text-muted);
            font-weight: 600;
        }
        @keyframes ticker-scroll {
            from { transform: translateX(100%); }
            to { transform: translateX(-100%); }
        }
        .contribution-reminder-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }
        .contribution-reminder-card {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 4px solid var(--accent-amber);
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: var(--shadow);
        }

        /* ── MODAL ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.55);
            z-index: 200;
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: #fff; border-radius: 20px; padding: 28px;
            width: 480px; max-width: 95vw;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
            position: relative;
            max-height: 90vh; overflow-y: auto;
        }
        .modal-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 18px; color: var(--text); }
        .modal-close {
            position: absolute; top: 18px; right: 18px;
            background: none; border: none; cursor: pointer;
            font-size: 1.1rem; color: var(--text-muted);
        }
        .modal-close:hover { color: var(--accent-red); }

        /* ── FLASH MESSAGES ── */
        .flash { padding: 13px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .flash-success { background: #d1fae5; color: #065f46; border-left: 4px solid var(--accent-green); }
        .flash-error   { background: #fee2e2; color: #991b1b; border-left: 4px solid var(--accent-red); }

        /* ── LIMIT INDICATOR ── */
        .limit-ok   { background: #d1fae5; color: #065f46; }
        .limit-warn { background: #fef9c3; color: #92400e; }
        .limit-full { background: #fee2e2; color: #991b1b; }

        .admin-search-wrap {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
        }
        .admin-search-row {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) auto;
            gap: 14px;
            align-items: center;
        }
        .search-field { position: relative; }
        .search-field i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        .search-field input {
            padding-left: 42px;
            height: 44px;
            font-weight: 600;
        }
        .member-documentary {
            display: none;
            margin-top: 16px;
            border: 1px solid #dbeafe;
            border-radius: 14px;
            overflow: hidden;
            background: #f8fbff;
        }
        .member-documentary.open { display: block; }
        .member-documentary-head {
            display: flex;
            gap: 14px;
            align-items: center;
            padding: 16px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
        }
        .documentary-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            overflow: hidden;
            flex-shrink: 0;
        }
        .documentary-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .member-documentary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 10px;
            padding: 16px;
        }
        .documentary-metric {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px;
        }
        .documentary-metric span {
            display: block;
            font-size: .68rem;
            color: var(--text-muted);
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .documentary-metric strong {
            color: var(--text);
            font-size: .9rem;
            line-height: 1.35;
        }
        .live-update-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .live-update-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-left: 4px solid var(--accent-green);
            border-radius: 14px;
            padding: 14px;
            box-shadow: var(--shadow);
        }
        .dept-filter-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .department-full-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--shadow);
            margin-bottom: 16px;
        }
        .department-full-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 16px;
            margin-top: 14px;
        }
        .compact-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .compact-list-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: .82rem;
        }

        /* ── ONBOARDING MODAL (dark overlay) ── */
        #onboardingModal { display: flex; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .form-row, .form-row-3, .dept-card-list, .worship-schedule-grid, .department-full-grid, .admin-search-row { grid-template-columns: 1fr; }
            .topbar { padding: 0 18px; }
            .content { padding: 24px 18px; }
        }
        @media (max-width: 640px) {
            body { display: block; font-size: 15px; }
            .mobile-menu-btn { display: inline-flex; flex-shrink: 0; }
            .sidebar {
                transform: translateX(-100%);
                transition: transform .25s ease;
                width: min(82vw, var(--sidebar-w));
                box-shadow: 10px 0 30px rgba(15, 23, 42, .2);
            }
            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-open .mobile-sidebar-backdrop { display: block; }
            .main { margin-left: 0; }
            .topbar {
                height: auto;
                min-height: 64px;
                padding: 10px 12px;
                gap: 10px;
            }
            .topbar-title {
                font-size: clamp(1rem, 5vw, 1.18rem);
                line-height: 1.2;
            }
            .topbar-right { gap: 8px; }
            .topbar-user { display: none; }
            .topbar-role { font-size: .68rem; padding: 3px 8px; white-space: nowrap; }
            .notification-trigger { padding: 8px 9px; font-size: 0; }
            .notification-trigger i { font-size: .95rem; }
            .notification-count { font-size: .68rem; }
            .notification-panel { position: fixed; top: 72px; left: 16px; }
            .content { padding: 18px 12px; }
            .stats-grid { grid-template-columns: 1fr; }
            .card { padding: 16px; border-radius: 12px; }
            .members-grid { grid-template-columns: 1fr; }
            .data-table {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
            }
            .form-control,
            select.form-control,
            input.form-control,
            textarea.form-control,
            .btn {
                min-height: 42px;
                font-size: .9rem;
            }
            .btn { justify-content: center; }
            .modal-box {
                width: calc(100vw - 24px);
                max-width: calc(100vw - 24px);
                border-radius: 16px;
                padding: 20px;
            }
            .content [style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
            .content [style*="width:260px"],
            .content [style*="width: 260px"],
            .content [style*="max-width:390px"],
            .content [style*="max-width:360px"] {
                width: 100% !important;
                max-width: 100% !important;
            }
            .content [style*="display:flex"] {
                min-width: 0;
            }
            .item-card-header,
            .compact-list-item {
                align-items: flex-start;
                flex-direction: column;
            }
            .announcement-ticker { border-radius: 10px; }
            .worship-schedule-card { padding: 16px; }
            .worship-schedule-item,
            .worship-schedule-item.flip {
                grid-template-columns: 1fr;
                border-radius: 24px;
            }
            .worship-schedule-item.flip .worship-detail,
            .worship-schedule-item.flip .worship-time {
                order: initial;
            }
            .worship-time,
            .worship-detail {
                padding: 14px 18px;
                text-align: center;
                align-items: center;
            }
        }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════ --}}
{{-- SIDEBAR                                     --}}
{{-- ═══════════════════════════════════════════ --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="icon" style="background:#ffffff;padding:4px;border-radius:10px;display:flex;align-items:center;justify-content:center;width:44px;height:44px;">
            <img src="{{ asset('images/eagt_logo.png') }}" alt="EAGT Logo" style="max-height:36px;width:auto;object-fit:contain;">
        </div>
        <div>
            <div class="brand-name">PHILADELPHIA</div>
            <div class="brand-sub">E.A.G.T CHURCH SYSTEM</div>
        </div>
    </div>

    @if($user->isAdmin())
        <div class="sidebar-section-label">Super Admin</div>
        <a class="nav-item active" onclick="showPanel('overview',this)">
            <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span> Overview
        </a>
        <a class="nav-item" onclick="showPanel('members',this)">
            <span class="nav-icon"><i class="fa-solid fa-users"></i></span> Wanachama
        </a>
        @if($user->isMchungaji())
        <a class="nav-item" onclick="showPanel('members',this)">
            <span class="nav-icon"><i class="fa-solid fa-building-columns"></i></span> Idara Zote
        </a>
        @endif
        <a class="nav-item" onclick="showPanel('committees',this)">
            <span class="nav-icon"><i class="fa-solid fa-sitemap"></i></span> Kamati / Idara
        </a>
        <a class="nav-item" onclick="showPanel('payments',this)">
            <span class="nav-icon"><i class="fa-solid fa-money-bill-wave"></i></span> Malipo
        </a>
        <a class="nav-item" onclick="showPanel('letters',this)">
            <span class="nav-icon"><i class="fa-solid fa-envelope-open-text"></i></span> Barua Rasmi
        </a>
        <a class="nav-item" onclick="showPanel('reports',this)">
            <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span> Ripoti
        </a>
        <a class="nav-item" onclick="showPanel('settings',this)">
            <span class="nav-icon"><i class="fa-solid fa-sliders"></i></span> Mipangilio Mfumo
        </a>
        <a class="nav-item" onclick="showPanel('audit',this)">
            <span class="nav-icon"><i class="fa-solid fa-shield-halved"></i></span> Audit Log
        </a>
    @elseif($user->isKiongozi())
        <div class="sidebar-section-label">{{ $user->isMchungaji() ? 'Mchungaji / Kiongozi' : 'Kiongozi wa Idara' }}</div>
        <a class="nav-item active" onclick="showPanel('overview',this)">
            <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span> Overview
        </a>
        <a class="nav-item" onclick="showPanel('members',this)">
            <span class="nav-icon"><i class="fa-solid fa-users"></i></span> Wanachama
        </a>
        @if($user->isMchungaji())
        <a class="nav-item" onclick="showPanel('members',this)">
            <span class="nav-icon"><i class="fa-solid fa-building-columns"></i></span> Idara Zote
        </a>
        @endif
        <a class="nav-item" onclick="showPanel('announcements',this)">
            <span class="nav-icon"><i class="fa-solid fa-bullhorn"></i></span> Matangazo
        </a>
        <a class="nav-item" onclick="showPanel('reports',this)">
            <span class="nav-icon"><i class="fa-solid fa-chart-bar"></i></span> Ripoti
        </a>
        <a class="nav-item" onclick="showPanel('letters',this)">
            <span class="nav-icon"><i class="fa-solid fa-envelope-open-text"></i></span> Barua
        </a>
        <a class="nav-item" onclick="showPanel('sms',this)">
            <span class="nav-icon"><i class="fa-solid fa-mobile-screen-button"></i></span> Tuma SMS
        </a>
        <a class="nav-item" onclick="showPanel('payments',this)">
            <span class="nav-icon"><i class="fa-solid fa-money-bill-wave"></i></span> Malipo
        </a>
    @else
        <div class="sidebar-section-label">Mshirika</div>
        <a class="nav-item active" onclick="showPanel('overview',this)">
            <span class="nav-icon"><i class="fa-solid fa-house"></i></span> Overview
        </a>
        <a class="nav-item" onclick="showPanel('payments',this)">
            <span class="nav-icon"><i class="fa-solid fa-wallet"></i></span> Malipo Yangu
        </a>
        <a class="nav-item" onclick="showPanel('letters',this)">
            <span class="nav-icon"><i class="fa-solid fa-envelope-open-text"></i></span> Barua Zangu
        </a>
        <a class="nav-item" onclick="showPanel('announcements',this)">
            <span class="nav-icon"><i class="fa-solid fa-bullhorn"></i></span> Matangazo
        </a>
        <a class="nav-item" onclick="showPanel('profile',this)">
            <span class="nav-icon"><i class="fa-solid fa-id-card"></i></span> Taarifa Zangu
        </a>
    @endif

    <div class="sidebar-section-label">Zaidi</div>
    <a class="nav-item" onclick="showPanel('help',this)">
        <span class="nav-icon"><i class="fa-solid fa-circle-question"></i></span> Msaada
        @php $pendingHelp = isset($myHelpMessages) ? $myHelpMessages->where('status','pending')->count() : 0; @endphp
        @if($pendingHelp > 0)
            <span class="badge">{{ $pendingHelp }}</span>
        @endif
    </a>
    <a class="nav-item" onclick="showPanel('account-settings',this)">
        <span class="nav-icon"><i class="fa-solid fa-gear"></i></span> {{ __('Mipangilio') }}
    </a>

    <div class="sidebar-spacer"></div>

    <div class="sidebar-logout">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;">
                <span class="nav-icon"><i class="fa-solid fa-right-from-bracket"></i></span> Toka / Logout
            </button>
        </form>
    </div>
</aside>
<div class="mobile-sidebar-backdrop" onclick="toggleMobileSidebar(false)" aria-hidden="true"></div>

{{-- ═══════════════════════════════════════════ --}}
{{-- MAIN CONTENT                                --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="main">

    @php
        $departmentAnnouncements = collect($announcements ?? collect())
            ->filter(fn ($announcement) => ! $announcement instanceof \App\Models\ChurchAnnouncement && ! empty($announcement->department_id));
        $churchNotices = collect($churchAnnouncements ?? collect());
        $displayAnnouncements = $departmentAnnouncements
            ->merge($churchNotices)
            ->sortByDesc('created_at')
            ->values();
        $isLeaderNotificationRecipient = $user->isAdmin() || $user->isMchungaji() || $user->isKiongozi();
        // A secretary's announcements addressed to leaders are kept separate
        // from department notices and presented in the floating leader inbox.
        $notificationAnnouncements = $isLeaderNotificationRecipient
            ? $churchNotices
            : $displayAnnouncements;
        $notificationsReadAt = session('notifications_read_at_'.$user->id);
        $unreadNotificationCount = $notificationsReadAt
            ? $notificationAnnouncements->filter(fn ($announcement) => $announcement->created_at?->gt(\Illuminate\Support\Carbon::parse($notificationsReadAt)))->count()
            : $notificationAnnouncements->count();
    @endphp

    {{-- TOP BAR --}}
    <div class="topbar">
        <button type="button" class="mobile-menu-btn" onclick="toggleMobileSidebar()" aria-label="Fungua menu" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="notification-wrap {{ $isLeaderNotificationRecipient ? 'notification-wrap-floating' : '' }}">
            <button type="button" class="notification-trigger" id="notification-trigger" aria-expanded="false" aria-controls="notification-panel">
                <i class="fa-solid fa-bell"></i> <span class="notification-trigger-text">{{ $isLeaderNotificationRecipient ? 'Ujumbe wa Katibu' : 'Taarifa' }}</span><span class="notification-count">{{ $unreadNotificationCount }}</span>
            </button>
            <aside class="notification-panel" id="notification-panel" aria-label="{{ $isLeaderNotificationRecipient ? 'Ujumbe wa Katibu' : 'Taarifa na matangazo' }}">
                <div class="notification-panel-head">
                    <strong>{{ $isLeaderNotificationRecipient ? 'Ujumbe wa Katibu' : 'Taarifa na Matangazo' }}</strong>
                    <button type="button" class="notification-close" id="notification-close" aria-label="Funga">&times;</button>
                </div>
                @forelse($notificationAnnouncements as $announcement)
                    <article class="notification-item">
                        <div class="notification-item-title">{{ $announcement->title }}</div>
                        <div class="notification-item-content">{{ $announcement->content }}</div>
                        @if($announcement instanceof \App\Models\ChurchAnnouncement && $announcement->media_path)
                            <a href="{{ asset('storage/'.$announcement->media_path) }}" target="_blank" rel="noopener" class="notification-item-content" style="display:inline-block;margin-top:6px;color:var(--primary);font-weight:700;">Fungua {{ $announcement->media_type === 'video' ? 'video' : 'picha' }} ya tangazo</a>
                        @endif
                        <div class="notification-item-meta">
                            {{ optional($announcement->created_at)->format('d/m/Y H:i') }}
                            &middot; {{ $announcement instanceof \App\Models\ChurchAnnouncement ? ($announcement->user->name ?? 'Katibu wa Kanisa') : ($announcement->department->name_sw ?? 'Kanisa') }}
                        </div>
                    </article>
                @empty
                    <p class="notification-item-content">{{ $isLeaderNotificationRecipient ? 'Hakuna ujumbe mpya kutoka kwa katibu.' : 'Hakuna taarifa au tangazo kwa sasa.' }}</p>
                @endforelse
            </aside>
        </div>
        <div class="topbar-title" id="topbar-title">Overview &amp; Takwimu</div>
        <div class="topbar-right">
            <span class="topbar-role {{ $user->isAdmin() || $user->isMchungaji() || $user->isKiongozi() ? 'role-kiongozi' : 'role-mshirika' }}">
                {{ $user->isAdmin() ? 'Super Admin' : ($user->isMchungaji() ? 'Mchungaji' : ($user->isKiongozi() ? 'Kiongozi' : 'Mshirika')) }}
            </span>
            @if($canSwitchDashboardMode ?? false)
                <form method="POST" action="{{ route('dashboard.view-mode') }}" style="margin:0;">
                    @csrf
                    <input type="hidden" name="mode" value="{{ ($dashboardViewMode ?? 'leader') === 'member' ? 'leader' : 'member' }}">
                    <button type="submit" class="btn btn-outline btn-sm" title="Badilisha mwonekano bila kubadilisha akaunti">
                        <i class="fa-solid fa-repeat"></i>
                        {{ ($dashboardViewMode ?? 'leader') === 'member' ? 'Fungua Kiongozi' : 'Fungua Mshirika' }}
                    </button>
                </form>
            @endif
            <button type="button" class="topbar-avatar" onclick="document.getElementById('userDetailsModal').classList.add('open')" title="Tazama taarifa zako">
                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name,0,1)) }}
                @endif
            </button>
            <span class="topbar-user">{{ $user->name }}</span>
        </div>
    </div>

    <div class="content">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="flash flash-success"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
        @endif
        @if(($profileIncomplete ?? false) && $user->isMshirika())
            <div class="flash" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;display:flex;align-items:center;justify-content:space-between;gap:14px;">
                <span><i class="fa-solid fa-circle-info"></i> Kumbusho: tafadhali jaza taarifa zako binafsi ili ofisi ya Katibu iwe na kumbukumbu kamili.</span>
                <button type="button" class="btn btn-amber btn-sm" onclick="document.getElementById('personalDetailsModal').classList.add('open')">Jaza taarifa</button>
            </div>
        @endif

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: OVERVIEW                        --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="panel active" id="panel-overview">

            @if(!$user->isKiongozi() && ($pendingContributionReminders ?? collect())->isNotEmpty())
                <div class="contribution-reminder-grid">
                    @foreach($pendingContributionReminders as $campaign)
                        <div class="contribution-reminder-card">
                            <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">
                                <div style="min-width:0;">
                                    <div style="font-size:.72rem;font-weight:900;color:#b45309;text-transform:uppercase;margin-bottom:4px;">Kumbusho la Mchango</div>
                                    <div style="font-weight:900;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $campaign->title }}</div>
                                    <div style="font-size:.78rem;color:var(--text-muted);margin-top:3px;">{{ $campaign->department->name_sw ?? 'Idara' }} · Mwisho {{ $campaign->due_date->format('d/m/Y') }}</div>
                                </div>
                                <span class="badge-pill limit-warn" style="white-space:nowrap;">Haijakamilika</span>
                            </div>
                            <div style="margin-top:10px;display:flex;justify-content:space-between;gap:12px;align-items:end;">
                                <div>
                                    <div style="font-size:.7rem;font-weight:800;color:#92400e;text-transform:uppercase;">Kiasi kilichobaki</div>
                                    <div style="font-size:1.15rem;font-weight:900;color:#b45309;">Tsh {{ number_format($campaign->balance_amount ?? 0, 2) }}</div>
                                </div>
                                <button type="button" onclick="showPanel('payments', document.querySelector('.nav-item[onclick*=payments]'))" class="btn btn-amber btn-sm">
                                    <i class="fa-solid fa-wallet"></i> Kamilisha
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($user->isAdmin())
                {{-- ADMIN OVERVIEW --}}
                <div class="card" style="border-left: 4px solid var(--accent); margin-bottom:24px; background: linear-gradient(135deg, #1b1f3b 0%, #2e3458 100%); color:#fff;">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
                        <div>
                            <span class="badge-pill badge-kiongozi" style="margin-bottom:8px;display:inline-block;background:rgba(255,255,255,0.2);color:#fff;">
                                SUPER ADMIN
                            </span>
                            <h3 style="font-size:1.5rem;font-weight:800;color:#fff;margin-bottom:4px;">
                                Karibu, {{ $user->name }}!
                            </h3>
                            <p style="color:rgba(255,255,255,0.8);font-size:.9rem;">
                                E.A.G.T KIBURUGWA PHILADELPHIA CHURCH — Management System
                            </p>
                            <form method="GET" action="{{ route('dashboard') }}" style="margin-top:14px;max-width:390px;">
                                <label class="form-label" style="color:rgba(255,255,255,.8);font-size:.72rem;margin-bottom:6px;">Chagua Idara ya Kusimamia</label>
                                <select name="department_id" class="form-control" onchange="this.form.submit()" style="height:40px;padding:8px 12px;font-size:.88rem;">
                                    @foreach(($leaderDepartmentsForSwitch ?? $allDepartments ?? collect()) as $leaderDept)
                                        <option value="{{ $leaderDept->id }}" {{ isset($department) && $department?->id === $leaderDept->id ? 'selected' : '' }}>
                                            {{ $leaderDept->name_sw }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div style="display:flex;gap:10px;">
                            <button onclick="showPanel('letters', document.querySelector('[onclick*=\"letters\"]'))" class="btn btn-primary" style="background:#6366f1;border:none;">
                                <i class="fa-solid fa-file-signature"></i> Barua Rasmi
                            </button>
                            <button onclick="showPanel('payments', document.querySelector('[onclick*=\"payments\"]'))" class="btn btn-success" style="background:#10b981;border:none;">
                                <i class="fa-solid fa-money-bill-wave"></i> Tazama Malipo
                            </button>
                        </div>
                    </div>
                </div>

                <div class="admin-search-wrap">
                    <div class="admin-search-row">
                        <div>
                            <div style="font-size:.74rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;margin-bottom:7px;">Live Search ya Mwanachama</div>
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="adminMemberSearch" class="form-control" placeholder="Andika jina, email, simu au namba ya mshirika..." autocomplete="off">
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline" onclick="clearAdminMemberSearch()">
                            <i class="fa-solid fa-rotate-left"></i> Safisha
                        </button>
                    </div>
                    <div id="memberDocumentary" class="member-documentary">
                        <div class="member-documentary-head">
                            <div class="documentary-avatar" id="memberDocAvatar"></div>
                            <div style="min-width:0;">
                                <h4 id="memberDocName" style="font-size:1.05rem;font-weight:900;color:var(--text);"></h4>
                                <p id="memberDocSummary" style="font-size:.84rem;color:var(--text-muted);margin-top:3px;"></p>
                            </div>
                        </div>
                        <div class="member-documentary-grid" id="memberDocGrid"></div>
                    </div>
                    <p id="memberSearchHint" style="font-size:.78rem;color:var(--text-muted);margin-top:10px;">Matokeo yataonekana papo hapo ukianza kuandika jina la mtu.</p>
                </div>

                <div class="live-update-grid">
                    <div class="live-update-card">
                        <div style="font-size:.72rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;">Malipo mapya</div>
                        @php $latestPayment = ($allPayments ?? collect())->first(); @endphp
                        <div style="font-weight:900;margin-top:5px;">{{ $latestPayment?->user?->name ?? 'Hakuna bado' }}</div>
                        <div style="font-size:.8rem;color:var(--text-muted);margin-top:3px;">
                            @if($latestPayment)
                                Tsh {{ number_format($latestPayment->amount ?? 0) }} · {{ $latestPayment->contributionCampaign->title ?? $latestPayment->type }}
                            @else
                                Hakuna rekodi mpya ya malipo.
                            @endif
                        </div>
                    </div>
                    <div class="live-update-card" style="border-left-color:#6366f1;">
                        <div style="font-size:.72rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;">Barua mpya</div>
                        @php $latestLetter = ($allLetters ?? collect())->first(); @endphp
                        <div style="font-weight:900;margin-top:5px;">{{ $latestLetter?->subject ?? 'Hakuna bado' }}</div>
                        <div style="font-size:.8rem;color:var(--text-muted);margin-top:3px;">
                            {{ $latestLetter?->user?->name ?? 'Hakuna ombi jipya' }} {{ $latestLetter ? '· '.$latestLetter->status : '' }}
                        </div>
                    </div>
                    <div class="live-update-card" style="border-left-color:#f59e0b;">
                        <div style="font-size:.72rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;">Audit ya mwisho</div>
                        @php $latestAudit = ($auditLogs ?? collect())->first(); @endphp
                        <div style="font-weight:900;margin-top:5px;">{{ $latestAudit?->action ?? 'Hakuna bado' }}</div>
                        <div style="font-size:.8rem;color:var(--text-muted);margin-top:3px;">{{ Str::limit($latestAudit?->description ?? 'Hakuna tukio jipya.', 70) }}</div>
                    </div>
                </div>

                {{-- Mchungaji Stats Grid --}}
                <div class="stats-grid" style="margin-bottom:24px;">
                    <div class="stat-card blue">
                        <div class="stat-label">Jumla ya Wanachama</div>
                        <div class="stat-value">{{ $totalMembers ?? 0 }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Hali ya Active: {{ $activeMembers ?? 0 }}</div>
                    </div>
                    <div class="stat-card purple">
                        <div class="stat-label">Viongozi wa Idara</div>
                        <div class="stat-value">{{ $deptLeadersCount ?? 0 }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Kamati 9 za Kanisa</div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-label">Jumla ya Mapato (Tsh)</div>
                        <div class="stat-value">{{ number_format($totalPaymentsSum ?? 0) }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Sadaka, Fungu la 10 & Ahadi</div>
                    </div>
                    <div class="stat-card amber">
                        <div class="stat-label">Ombi la Barua Zilizosubiri</div>
                        <div class="stat-value">{{ $pendingLetters ?? 0 }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Zilizoidhinishwa: {{ $approvedLetters ?? 0 }}</div>
                    </div>
                </div>

                {{-- Interactive Charts (Chart.js) --}}
                <div style="display:grid;grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));gap:20px;margin-bottom:24px;">
                    <div class="card">
                        <div class="card-title">
                            <span class="icon-box" style="background:#e0e7ff;color:#4338ca;"><i class="fa-solid fa-chart-line"></i></span>
                            Mapato ya Kanisa (Kulingana na Aina)
                        </div>
                        <canvas id="paymentsChart" height="200"></canvas>
                    </div>

                    <div class="card">
                        <div class="card-title">
                            <span class="icon-box" style="background:#dcfce7;color:#15803d;"><i class="fa-solid fa-chart-pie"></i></span>
                            Wanachama kwa Kila Kamati/Idara
                        </div>
                        <canvas id="deptChart" height="200"></canvas>
                    </div>
                </div>

                {{-- Chart initialization script --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctxPay = document.getElementById('paymentsChart');
                        if (ctxPay) {
                            new Chart(ctxPay, {
                                type: 'bar',
                                data: {
                                    labels: {!! json_encode(($paymentsByType ?? collect())->pluck('type')) !!},
                                    datasets: [{
                                        label: 'Mapato (Tsh)',
                                        data: {!! json_encode(($paymentsByType ?? collect())->pluck('total')) !!},
                                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                                        borderRadius: 8
                                    }]
                                },
                                options: { responsive: true, plugins: { legend: { display: false } } }
                            });
                        }

                        const ctxDept = document.getElementById('deptChart');
                        if (ctxDept) {
                            new Chart(ctxDept, {
                                type: 'doughnut',
                                data: {
                                    labels: {!! json_encode(($membersByDept ?? collect())->pluck('name_sw')) !!},
                                    datasets: [{
                                        data: {!! json_encode(($membersByDept ?? collect())->pluck('users_count')) !!},
                                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f97316', '#64748b']
                                    }]
                                },
                                options: { responsive: true }
                            });
                        }
                    });
                </script>

            @elseif($user->isKiongozi())

                {{-- Welcome + Position counter (only if more than 1) --}}
                <div class="card" style="border-left: 4px solid var(--accent-amber); margin-bottom:24px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
                        <div>
                            <h3 style="font-size:1.3rem;font-weight:800;color:var(--text);margin-bottom:4px;">
                                Karibu, {{ $user->name }}!
                            </h3>
                            <p style="color:var(--text-muted);font-size:.9rem;">
                                Unasimamia idara: <strong>{{ $department->name_sw ?? 'Bado haujajiunga' }}</strong>
                            </p>
                            <p style="color:var(--text-muted);font-size:.85rem;margin-top:4px;">
                                Cheo chako: <strong>{{ $activeDepartmentCheo ?? 'Kiongozi' }}</strong>
                            </p>
                            @if($leaderPositionCount > 1)
                                <form method="GET" action="{{ route('dashboard') }}" style="margin-top:12px;max-width:360px;">
                                    <label class="form-label" style="font-size:.72rem;margin-bottom:6px;">Ingia kwenye Idara</label>
                                    <select name="department_id" class="form-control" onchange="this.form.submit()" style="height:40px;padding:8px 12px;font-size:.88rem;">
                                        @foreach(($leaderDepartmentsForSwitch ?? $user->departments) as $leaderDept)
                                            <option value="{{ $leaderDept->id }}" {{ isset($department) && $department?->id === $leaderDept->id ? 'selected' : '' }}>
                                                {{ $leaderDept->name_sw }}{{ $leaderDept->pivot?->cheo ? ' - '.$leaderDept->pivot->cheo : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                        </div>

                        {{-- Show position counter ONLY if leader has more than 1 position --}}
                        @if($showPositionCounter)
                        <div class="position-counter">
                            <span class="pc-label">Nafasi za Uongozi</span>
                            <span class="pc-value">{{ $leaderPositionCount }} / 4</span>
                            <span class="pc-badge {{ $leaderPositionCount >= 4 ? 'limit-full' : ($leaderPositionCount >= 3 ? 'limit-warn' : 'limit-ok') }}">
                                {{ $leaderPositionCount >= 4 ? 'Kikomo kimefikiwa' : (4 - $leaderPositionCount) . ' zimebaki' }}
                            </span>
                        </div>
                        @elseif(!$user->isAdmin() && !$user->isMchungaji() && $leaderPositionCount < 4)
                        <button onclick="document.getElementById('addPositionModal').classList.add('open')"
                            class="btn btn-outline" style="font-size:.82rem;">
                            <i class="fa-solid fa-plus"></i> Ongeza Nafasi ya Uongozi
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                <div class="stats-grid">
                    <div class="stat-card blue">
                        <div class="stat-label">Wanachama wa Idara</div>
                        <div class="stat-value">{{ $members->count() }}</div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-label">Matangazo</div>
                        <div class="stat-value">{{ $announcements->count() }}</div>
                    </div>
                    <div class="stat-card amber">
                        <div class="stat-label">Ripoti</div>
                        <div class="stat-value">{{ $reports->count() }}</div>
                    </div>
                    <div class="stat-card purple">
                        <div class="stat-label">SMS Zilizotumwa</div>
                        <div class="stat-value">{{ $smsLogs->count() }}</div>
                    </div>
                </div>

                {{-- Recent Members preview --}}
                <div class="card">
                    <div class="card-title">
                        <span class="icon-box" style="background:#ede9fe;color:#6d28d9;"><i class="fa-solid fa-users"></i></span>
                        Wanachama wa Hivi Karibuni
                        <button onclick="showPanel('members', document.querySelector('[onclick*=\"members\"]'))" class="btn btn-outline btn-sm" style="margin-left:auto;">Wote <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Jina</th>
                                <th>Email</th>
                                <th>Aina</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members->take(5) as $m)
                                <tr>
                                    <td>
                                        <div class="member-info">
                                            <div class="member-avatar">
                                                @if($m->avatar)
                                                    <img src="{{ asset('storage/'.$m->avatar) }}" alt="{{ $m->name }}">
                                                @else
                                                    {{ strtoupper(substr($m->name,0,1)) }}
                                                @endif
                                            </div>
                                            <strong>{{ $m->name }}</strong>
                                        </div>
                                    </td>
                                    <td style="color:var(--text-muted)">{{ $m->email }}</td>
                                    <td><span class="badge-pill {{ $m->role === 'kiongozi' ? 'badge-kiongozi' : 'badge-mshirika' }}">{{ ucfirst($m->role) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:20px">Hakuna wanachama bado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @else
                {{-- MSHIRIKA OVERVIEW & MEMBER DETAILS --}}
                @php $memberCount = $user->departments->count(); $memberMax = 5; @endphp
                <div class="card" style="border-left:4px solid #3b82f6;margin-bottom:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:20px;">
                        
                        {{-- Left: User details & Avatar upload placeholder --}}
                        <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
                            
                            {{-- Profile Avatar & Upload trigger --}}
                            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                                <div style="width:85px;height:85px;border-radius:50%;overflow:hidden;border:3px solid var(--accent);background:#f1f5f9;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <i class="fa-solid fa-user-large" style="font-size:2.4rem;color:#94a3b8;"></i>
                                    @endif
                                </div>
                                
                                <button type="button" onclick="document.getElementById('profileAvatarModal').classList.add('open')" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:4px 8px;">
                                    <i class="fa-solid fa-camera"></i> {{ $user->avatar ? 'Badili Picha' : 'Pakia Picha' }}
                                </button>
                            </div>

                            {{-- Member details under name --}}
                            <div>
                                <h3 style="font-size:1.4rem;font-weight:800;margin-bottom:4px;color:var(--text);">
                                    {{ $user->name }}
                                </h3>
                                <div style="display:flex;flex-direction:column;gap:4px;font-size:.85rem;color:var(--text-muted);margin-top:6px;">
                                    <div><i class="fa-solid fa-id-card" style="color:var(--accent);margin-right:6px;"></i> <strong>Member ID:</strong> <span style="font-family:monospace;font-weight:700;color:var(--accent);">{{ $user->member_id ?? 'EAGT-KBP' }}</span></div>
                                    <div><i class="fa-solid fa-envelope" style="color:var(--accent);margin-right:6px;"></i> <strong>Barua Pepe:</strong> {{ $user->email }}</div>
                                    <div><i class="fa-solid fa-phone" style="color:var(--accent-green);margin-right:6px;"></i> <strong>Simu:</strong> {{ $user->phone ?? 'Bado haijawekwa' }}</div>
                                    <div><i class="fa-solid fa-user-shield" style="color:var(--accent-amber);margin-right:6px;"></i> <strong>Nafasi:</strong> {{ ucfirst($user->role) }}</div>
                                </div>

                                {{-- Photo upload prompt if avatar is missing --}}
                                @if(!$user->avatar)
                                    <div style="margin-top:10px;background:#fffbeb;border:1px solid #fef3c7;color:#b45309;padding:8px 12px;border-radius:8px;font-size:.78rem;display:flex;align-items:center;gap:8px;">
                                        <i class="fa-solid fa-circle-info" style="font-size:1rem;"></i>
                                        <span>Haujaweka picha ya profilu. Bonyeza <strong>Pakia Picha</strong> kuweka picha yako.</span>
                                    </div>
                                @endif
                            </div>

                        </div>

                        {{-- Right: Dept counter --}}
                        <div style="display:flex;flex-direction:column;align-items:center;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:14px;padding:14px 22px;">
                            <span style="font-size:.7rem;color:#1e40af;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Idara Zangu</span>
                            <span style="font-size:2rem;font-weight:900;color:{{ $memberCount >= $memberMax ? '#ef4444' : '#2563eb' }}">{{ $memberCount }} / {{ $memberMax }}</span>
                            <span class="badge-pill {{ $memberCount >= $memberMax ? 'limit-full' : ($memberCount >= 4 ? 'limit-warn' : 'limit-ok') }}" style="margin-top:4px;font-size:.68rem;">
                                {{ $memberCount >= $memberMax ? 'Kikomo' : ($memberMax - $memberCount) . ' zimebaki' }}
                            </span>
                        </div>
                    </div>
                </div>

                <section class="card" style="border-left:4px solid #10b981;margin:0 0 24px;padding:18px;">
                    <div class="card-title" style="margin-bottom:12px;">
                        Viongozi wa Zamu ya Wiki
                        <small style="margin-left:auto;color:var(--text-muted);font-weight:600;">Jumapili hadi Jumapili</small>
                    </div>
                    @forelse(($weeklyDutySchedules ?? collect()) as $schedule)
                        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;padding:10px 0;border-bottom:{{ $loop->last ? '0' : '1px solid var(--border)' }};">
                            <div><strong>{{ $schedule->duty }}</strong><div style="font-size:.86rem;color:var(--text-muted);margin-top:3px;">{{ $schedule->person_name }}</div></div>
                            @if($schedule->notes)
                                <a href="tel:{{ $schedule->notes }}" style="font-size:.84rem;font-weight:700;color:var(--accent);text-decoration:none;">{{ $schedule->notes }}</a>
                            @endif
                        </div>
                    @empty
                        <p style="color:var(--text-muted);margin:0;">Zamu za wiki hii bado hazijachapishwa.</p>
                    @endforelse
                </section>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <h4 style="font-size:1rem;font-weight:700;">Idara Zangu</h4>
                    @if($memberCount < $memberMax)
                        <button onclick="document.getElementById('addDeptModal').classList.add('open')" class="btn btn-primary btn-sm">
                            Jiunge na Idara Nyingine
                        </button>
                    @endif
                </div>

                <div class="dept-card-list" style="margin-bottom:24px;">
                    @forelse($departments as $dept)
                        @php
                            $deptSchedule = match (true) {
                                str_contains($dept->name_sw, 'Wamama') => 'Jumatatu saa 10:00 jioni - maombi, mafundisho na ushirika.',
                                str_contains($dept->name_sw, 'Vijana') => 'Jumamosi saa 9:00 jioni - mazoezi, mafundisho na huduma za vijana.',
                                str_contains($dept->name_sw, 'Watoto') => 'Jumapili wakati wa ibada kuu - darasa na malezi ya watoto.',
                                str_contains($dept->name_sw, 'Praise') || str_contains($dept->name_sw, 'Kwaya') => 'Jumanne na Alhamisi saa 12:00 jioni - mazoezi ya praise na kwaya.',
                                str_contains($dept->name_sw, 'Wababa') => 'Jumamosi saa 10:00 jioni - ushirika, maombi na mafundisho.',
                                default => 'Ratiba hutangazwa na viongozi wa idara kulingana na shughuli zilizopangwa.',
                            };
                            $deptGoal = match (true) {
                                str_contains($dept->name_sw, 'Wamama') => 'Kujenga wamama kiroho, kifamilia na kihuduma.',
                                str_contains($dept->name_sw, 'Vijana') => 'Kukuza vijana katika Neno, vipaji na huduma.',
                                str_contains($dept->name_sw, 'Watoto') => 'Kulea watoto katika imani, nidhamu na kumjua Mungu.',
                                str_contains($dept->name_sw, 'Praise') || str_contains($dept->name_sw, 'Kwaya') => 'Kuongoza sifa na ibada kwa ubora na nidhamu.',
                                str_contains($dept->name_sw, 'Wababa') => 'Kujenga wanaume imara katika familia, kanisa na jamii.',
                                default => 'Kuwaunganisha washirika katika huduma na ukuaji wa kiroho.',
                            };
                        @endphp
                        <div class="dept-card-item" style="cursor:pointer;" onclick="toggleMemberDepartment({{ $dept->id }})">
                            <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;">
                                <div>
                                    <h5>{{ $dept->name_sw }}</h5>
                                    <p>{{ $dept->name_en }}</p>
                                </div>
                                <span class="badge-pill" style="background:#eef2ff;color:#4f46e5;white-space:nowrap;"><i class="fa-solid fa-chevron-up"></i> Imefunguka</span>
                            </div>
                            <p style="margin-top:6px;font-size:.8rem;color:#6b7280;">{{ $dept->description_sw }}</p>
                            <div id="member-dept-details-{{ $dept->id }}" style="display:block;margin-top:14px;border-top:1px solid var(--border);padding-top:14px;" onclick="event.stopPropagation()">
                                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px;">
                                    <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:12px;">
                                        <div style="font-size:.72rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Viongozi na Mawasiliano</div>
                                        @php
                                            $leaderRoleOrder = ['Mwenyekiti', 'Makamu Mwenyekiti', 'Katibu', 'Katibu Msaidizi', 'Mhasibu'];
                                            $departmentLeaders = $dept->users->sortBy(function ($leader) use ($leaderRoleOrder) {
                                                $index = array_search($leader->pivot->cheo ?? '', $leaderRoleOrder, true);
                                                return $index === false ? 99 : $index;
                                            });
                                        @endphp
                                        @forelse($departmentLeaders as $leader)
                                            <div style="margin-bottom:9px;">
                                                <div style="font-weight:900;color:var(--text);">{{ $leader->name }}</div>
                                                <div style="font-size:.75rem;color:var(--accent);font-weight:800;">{{ $leader->pivot->cheo ?? 'Kiongozi' }}</div>
                                                <div style="font-size:.75rem;color:var(--text-muted);">{{ $leader->phone ?? $leader->email }}</div>
                                            </div>
                                        @empty
                                            <div style="font-size:.8rem;color:var(--text-muted);">Bado viongozi hawajawekwa.</div>
                                        @endforelse
                                    </div>
                                    <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:12px;">
                                        <div style="font-size:.72rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Ratiba ya Idara</div>
                                        <div style="font-size:.84rem;color:var(--text);line-height:1.5;">{{ $deptSchedule }}</div>
                                    </div>
                                    <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:12px;">
                                        <div style="font-size:.72rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Malengo</div>
                                        <div style="font-size:.84rem;color:var(--text);line-height:1.5;">{{ $deptGoal }}</div>
                                    </div>
                                </div>
                                <div style="margin-top:12px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px;">
                                    <div style="font-size:.72rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Ripoti za Idara</div>
                                    @forelse($dept->reports->take(3) as $report)
                                        <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;border-bottom:{{ $loop->last ? '0' : '1px solid #eef2f7' }};padding:8px 0;">
                                            <div>
                                                <div style="font-weight:800;">{{ $report->title }}</div>
                                                <div style="font-size:.75rem;color:var(--text-muted);">{{ $report->report_date ? $report->report_date->format('d/m/Y') : $report->created_at->format('d/m/Y') }}</div>
                                            </div>
                                            <a href="{{ route('reports.show', $report->id) }}" class="btn btn-outline btn-sm" style="white-space:nowrap;">Fungua</a>
                                        </div>
                                    @empty
                                        <div style="font-size:.8rem;color:var(--text-muted);">Hakuna ripoti zilizowekwa kwa sasa.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column:span 2;text-align:center;color:var(--text-muted);padding:20px;">Haujajiunga na idara yoyote bado.</div>
                    @endforelse
                </div>

                <div class="worship-schedule-card">
                    <div class="worship-schedule-head">
                        <span class="icon-box"><i class="fa-solid fa-calendar-days"></i></span>
                        <h4>Ratiba za Ibada</h4>
                    </div>

                    <div class="worship-schedule-list">
                        <div class="worship-sunday-row">
                            <div class="worship-schedule-item" style="--schedule-accent:#c85a00;">
                                <div class="worship-time">
                                    <div class="worship-day">Jumapili</div>
                                    <div class="worship-hour">02:30</div>
                                </div>
                                <div class="worship-detail">
                                    <div class="worship-title">Ibada Kuu</div>
                                    <div class="worship-sub">Saa 2:30 Asubuhi - Saa 12:30 Mchana</div>
                                </div>
                            </div>
                        </div>

                        <div class="worship-schedule-grid">
                            <div class="worship-schedule-item" style="--schedule-accent:#d97706;">
                                <div class="worship-time">
                                    <div class="worship-day">Jumatatu</div>
                                    <div class="worship-hour">10:00</div>
                                </div>
                                <div class="worship-detail">
                                    <div class="worship-title">Ibada ya Wamama (WWK)</div>
                                    <div class="worship-sub">Saa 10:00 Jioni - Saa 12:00 Jioni</div>
                                </div>
                            </div>

                            <div class="worship-schedule-item flip" style="--schedule-accent:#1f6f4a;">
                                <div class="worship-time">
                                    <div class="worship-day">Jumanne</div>
                                    <div class="worship-hour">12:00</div>
                                </div>
                                <div class="worship-detail">
                                    <div class="worship-title">Mazoezi ya Praise</div>
                                    <div class="worship-sub">Saa 12:00 Jioni - Saa 2:00 Usiku</div>
                                </div>
                            </div>

                            <div class="worship-schedule-item" style="--schedule-accent:#0f8b7d;">
                                <div class="worship-time">
                                    <div class="worship-day">Jumatano</div>
                                    <div class="worship-hour">10:30</div>
                                </div>
                                <div class="worship-detail">
                                    <div class="worship-title">Ibada ya Mafundisho</div>
                                    <div class="worship-sub">Saa 10:30 Jioni - Saa 12:00 Jioni</div>
                                </div>
                            </div>

                            <div class="worship-schedule-item flip" style="--schedule-accent:#376f94;">
                                <div class="worship-time">
                                    <div class="worship-day">Alhamisi</div>
                                    <div class="worship-hour">12:00</div>
                                </div>
                                <div class="worship-detail">
                                    <div class="worship-title">Mazoezi ya Kwaya</div>
                                    <div class="worship-sub">Saa 12:00 Jioni - Saa 2:00 Usiku</div>
                                </div>
                            </div>

                            <div class="worship-schedule-item" style="--schedule-accent:#333333;">
                                <div class="worship-time">
                                    <div class="worship-day">Ijumaa</div>
                                    <div class="worship-hour">10:30</div>
                                </div>
                                <div class="worship-detail">
                                    <div class="worship-title">Ibada ya Maombi na Maombezi</div>
                                    <div class="worship-sub">Saa 10:30 Jioni - Saa 12:30 Jioni</div>
                                </div>
                            </div>

                            <div class="worship-schedule-item flip" style="--schedule-accent:#c91f26;">
                                <div class="worship-time">
                                    <div class="worship-day">Jumamosi</div>
                                    <div class="worship-hour">09:00</div>
                                </div>
                                <div class="worship-detail">
                                    <div class="worship-title">Maandalizi ya Ibada</div>
                                    <div class="worship-sub">Saa 9:00 Jioni - Saa 2:00 Usiku</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Announcements preview --}}
                <div class="card">
                    <div class="card-title">
                        Matangazo ya Hivi Karibuni
                    </div>
                    @forelse($displayAnnouncements->take(3) as $ann)
                        <div class="item-card green">
                            <div class="item-card-header">
                                <span class="item-card-title">{{ $ann->title }}</span>
                                <span class="item-card-date">{{ $ann->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="item-card-body">{{ Str::limit($ann->content, 100) }}</div>
                        </div>
                    @empty
                        <p style="color:var(--text-muted);text-align:center;padding:14px;">Hakuna matangazo kwa sasa.</p>
                    @endforelse
                </div>
            @endif
        </div>

        @if($user->isAdmin())
        <div class="panel" id="panel-committees">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
                <div>
                    <h4 style="font-size:1.2rem;font-weight:900;color:var(--text);"><i class="fa-solid fa-sitemap" style="color:var(--accent);margin-right:8px;"></i> Utendaji wa Kamati / Idara</h4>
                    <p style="font-size:.85rem;color:var(--text-muted);margin-top:3px;">Idara zinazofanya vizuri zinapanda juu kulingana na wanachama, ripoti, michango na kiwango kilichokusanywa.</p>
                </div>
                <span class="badge-pill" style="background:#eef2ff;color:#4f46e5;">Active: {{ $department->name_sw ?? 'Idara' }}</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;">
                @forelse(($deptPerformance ?? collect()) as $dept)
                    <a href="{{ route('dashboard', ['department_id' => $dept->id, 'panel' => 'members']) }}" class="card" style="display:block;text-decoration:none;margin-bottom:0;border-left:4px solid {{ $loop->first ? '#10b981' : '#6366f1' }};cursor:pointer;">
                        <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">
                            <div>
                                <div style="font-size:.72rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;">Nafasi #{{ $loop->iteration }}</div>
                                <h5 style="font-size:1rem;font-weight:900;color:var(--text);margin-top:3px;">{{ $dept->name_sw }}</h5>
                                <p style="font-size:.78rem;color:var(--text-muted);margin-top:2px;">{{ $dept->name_en }}</p>
                                <div style="font-size:.72rem;font-weight:800;color:var(--accent);margin-top:7px;">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Ingia kwenye idara hii
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:1.5rem;font-weight:900;color:{{ ($dept->performance_percent ?? 0) >= 70 ? '#15803d' : '#4f46e5' }};">{{ $dept->performance_percent ?? 0 }}%</div>
                                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;">Utendaji</div>
                            </div>
                        </div>
                        <div style="height:8px;background:#e5e7eb;border-radius:999px;overflow:hidden;margin:14px 0;">
                            <div style="height:100%;width:{{ $dept->performance_percent ?? 0 }}%;background:{{ ($dept->performance_percent ?? 0) >= 70 ? '#10b981' : '#6366f1' }};"></div>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:10px;">
                                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;">Wanachama</div>
                                <div style="font-weight:900;">{{ $dept->members_count ?? 0 }}</div>
                            </div>
                            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:10px;">
                                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;">Ripoti</div>
                                <div style="font-weight:900;">{{ $dept->reports_count ?? 0 }}</div>
                            </div>
                            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:10px;">
                                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;">Michango</div>
                                <div style="font-weight:900;">{{ $dept->contribution_campaigns_count ?? 0 }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="card" style="text-align:center;color:var(--text-muted);">Hakuna idara zilizowekwa bado.</div>
                @endforelse
            </div>

            <div style="margin-top:24px;">
                <h4 style="font-size:1.1rem;font-weight:900;color:var(--text);margin-bottom:12px;">
                    <i class="fa-solid fa-table-list" style="color:var(--accent);margin-right:8px;"></i> View Kamili ya Kila Idara
                </h4>
                @forelse(($adminDepartmentFullView ?? collect()) as $dept)
                    <div class="department-full-card">
                        <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                            <div>
                                <h5 style="font-size:1.05rem;font-weight:900;color:var(--text);">{{ $dept->name_sw }}</h5>
                                <p style="font-size:.84rem;color:var(--text-muted);margin-top:3px;">{{ $dept->description_sw ?? $dept->name_en }}</p>
                            </div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <span class="badge-pill badge-mshirika">{{ $dept->members_count ?? 0 }} wanachama</span>
                                <span class="badge-pill badge-pending">{{ $dept->reports_count ?? 0 }} ripoti</span>
                                <span class="badge-pill badge-read">{{ $dept->contribution_campaigns_count ?? 0 }} michango</span>
                            </div>
                        </div>
                        <div class="department-full-grid">
                            <div>
                                <div style="font-size:.74rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Wanachama na Viongozi</div>
                                <div class="compact-list">
                                    @forelse($dept->users->take(10) as $member)
                                        <div class="compact-list-item">
                                            <div style="min-width:0;">
                                                <strong>{{ $member->name }}</strong>
                                                <div style="font-size:.75rem;color:var(--text-muted);">{{ $member->email }} {{ $member->phone ? '· '.$member->phone : '' }}</div>
                                            </div>
                                            <span class="badge-pill {{ $member->pivot->role === 'kiongozi' ? 'badge-kiongozi' : 'badge-mshirika' }}" style="white-space:nowrap;">{{ $member->pivot->cheo ?? ucfirst($member->pivot->role ?? $member->role) }}</span>
                                        </div>
                                    @empty
                                        <div class="compact-list-item">Hakuna wanachama kwenye idara hii.</div>
                                    @endforelse
                                </div>
                            </div>
                            <div>
                                <div style="font-size:.74rem;font-weight:900;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Ripoti na Michango</div>
                                <div class="compact-list">
                                    @forelse($dept->reports->take(3) as $report)
                                        <div class="compact-list-item">
                                            <div>
                                                <strong>{{ $report->title }}</strong>
                                                <div style="font-size:.75rem;color:var(--text-muted);">Ripoti · {{ $report->report_date ? $report->report_date->format('d/m/Y') : $report->created_at->format('d/m/Y') }}</div>
                                            </div>
                                            <a href="{{ route('reports.show', $report->id) }}" class="btn btn-outline btn-sm">View</a>
                                        </div>
                                    @empty
                                        <div class="compact-list-item">Hakuna ripoti zilizowekwa.</div>
                                    @endforelse
                                    @foreach($dept->contributionCampaigns->take(3) as $campaign)
                                        <div class="compact-list-item">
                                            <div>
                                                <strong>{{ $campaign->title }}</strong>
                                                <div style="font-size:.75rem;color:var(--text-muted);">Mchango · Tsh {{ number_format($campaign->payments->sum('amount')) }} zimekusanywa</div>
                                            </div>
                                            <span class="badge-pill badge-read">{{ ucfirst($campaign->status) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card" style="text-align:center;color:var(--text-muted);">Hakuna taarifa kamili za idara bado.</div>
                @endforelse
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: MEMBERS (Kiongozi only)         --}}
        {{-- ══════════════════════════════════════ --}}
        @if($user->isKiongozi())
        @php
            $canManageLeaders = $user->isAdmin() || $user->isMchungaji() || $user->isMwenyekiti($department ?? null);
            $leaderRoleOrder = ['mwenyekiti', 'makamu mwenyekiti', 'mwenyekiti msaidizi', 'katibu', 'katibu msaidizi', 'mhasibu', 'mhazini', 'mjumbe wa kamati', 'mjumbe'];
            
            $departmentLeaders = $members->filter(function ($m) {
                return ($m->pivot?->role === 'kiongozi') || ($m->role === 'kiongozi');
            })->sortBy(function ($m) use ($leaderRoleOrder) {
                $cheo = mb_strtolower(trim((string)($m->pivot?->cheo ?? $m->cheo ?? '')));
                $idx = array_search($cheo, $leaderRoleOrder, true);
                return $idx === false ? 99 : $idx;
            });

            $departmentWashirika = $members->reject(function ($m) {
                return ($m->pivot?->role === 'kiongozi') || ($m->role === 'kiongozi');
            });
        @endphp
        <div class="panel" id="panel-members">

            {{-- Header --}}
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:22px;">
                <div>
                    <h4 style="font-size:1.15rem;font-weight:800;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-users-gear" style="color:var(--accent);"></i> 
                        {{ $user->isMchungaji() ? 'Wanachama na Viongozi wa Kanisa' : 'Wanachama na Viongozi wa Idara' }}
                    </h4>
                    <p style="font-size:.82rem;color:var(--text-muted);margin-top:2px;">
                        {{ $user->isMchungaji() ? 'Idara zote' : ($department->name_sw ?? '') }} · Viongozi: <strong>{{ $departmentLeaders->count() }}</strong> · Washirika: <strong>{{ $departmentWashirika->count() }}</strong>
                    </p>
                </div>
                <div class="dept-filter-bar" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    @if($user->isAdmin() || $user->isMchungaji())
                        <select id="memberDepartmentFilter" class="form-control" style="width:230px;" onchange="filterMembersByDepartment(this.value, this.options[this.selectedIndex].text)">
                            <option value="all">Idara zote</option>
                            @foreach(($allDepartments ?? collect()) as $filterDept)
                                <option value="{{ $filterDept->id }}" {{ isset($department) && $department?->id === $filterDept->id ? 'selected' : '' }}>{{ $filterDept->name_sw }}</option>
                            @endforeach
                        </select>
                    @else
                        <form method="GET" action="{{ route('dashboard') }}">
                            <select name="department_id" class="form-control" style="width:230px;" onchange="this.form.submit()">
                                @foreach(($leaderDepartmentsForSwitch ?? collect([$department])) as $filterDept)
                                    <option value="{{ $filterDept->id }}" {{ isset($department) && $department?->id === $filterDept->id ? 'selected' : '' }}>{{ $filterDept->name_sw }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                    @if($canManageLeaders)
                        <button onclick="document.getElementById('registerLeaderModal').classList.add('open')" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);border:none;box-shadow:0 4px 14px rgba(30,58,138,.3);font-size:.82rem;display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-user-shield"></i> Sajili Kiongozi
                        </button>
                    @endif

                    <button onclick="document.getElementById('addMemberModal').classList.add('open')" class="btn btn-outline" style="font-size:.82rem;display:flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-user-plus"></i> Ongeza Mshirika
                    </button>
                </div>
            </div>

            {{-- ════════════════════════════════════════════ --}}
            {{-- 1. VIONGOZI WA IDARA (Kamati Tendaji)         --}}
            {{-- ════════════════════════════════════════════ --}}
            <div style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:20px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,.03);">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="width:34px;height:34px;border-radius:10px;background:#fef3c7;color:#92400e;display:inline-flex;align-items:center;justify-content:center;font-size:1.05rem;">
                            <i class="fa-solid fa-crown"></i>
                        </span>
                        <div>
                            <div style="font-size:1rem;font-weight:800;color:var(--text);">Viongozi wa Idara (Kamati Tendaji)</div>
                            <div style="font-size:.76rem;color:var(--text-muted);">Viongozi wanaosimamia uendeshaji wa idara hii</div>
                        </div>
                        <span class="badge-pill" style="background:#eef2ff;color:#4f46e5;font-weight:800;font-size:.75rem;">{{ $departmentLeaders->count() }}</span>
                    </div>

                    @if($canManageLeaders)
                        <div style="display:flex;align-items:center;gap:6px;background:#fefce8;border:1px solid #fef08a;border-radius:999px;padding:4px 12px;font-size:.76rem;color:#854d0e;font-weight:700;">
                            <i class="fa-solid fa-shield-halved" style="color:#ca8a04;"></i> Mamlaka ya Mwenyekiti: Kusajili & Kuwapa Password
                        </div>
                    @else
                        <div style="font-size:.76rem;color:var(--text-muted);">
                            <i class="fa-solid fa-circle-info"></i> Viongozi husajiliwa na kupewa password na Kiongozi Mwenyekiti.
                        </div>
                    @endif
                </div>

                @if($departmentLeaders->count() > 0)
                <div class="members-grid">
                    @foreach($departmentLeaders as $m)
                    <div class="member-card" data-departments="{{ $m->departments->pluck('id')->join(',') }}" style="border-top:3px solid #3b82f6;position:relative;">
                        {{-- Remove button for Mwenyekiti --}}
                        @if($canManageLeaders && $m->id !== $user->id && !$m->isAdmin() && !$m->isMchungaji())
                            <form method="POST" action="{{ route('department-leaders.remove', $m->id) }}"
                                onsubmit="return confirm('Una uhakika wa kuondoa kiongozi {{ addslashes($m->name) }} kwenye uongozi wa idara hii?')"
                                style="margin:0">
                                @csrf
                                <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">
                                <button type="submit" class="member-card-remove" title="Ondoa kwenye Uongozi">
                                    <i class="fa-solid fa-user-minus"></i> Ondoa
                                </button>
                            </form>
                        @elseif($m->id === $user->id)
                            <span class="member-card-self">Wewe</span>
                        @endif

                        {{-- Photo --}}
                        <div class="member-card-photo" style="border:2.5px solid #3b82f6;box-shadow:0 4px 10px rgba(59,130,246,.2);">
                            @if($m->avatar)
                                <img src="{{ asset('storage/'.$m->avatar) }}" alt="{{ $m->name }}">
                            @else
                                {{ strtoupper(substr($m->name, 0, 1)) }}
                            @endif
                        </div>

                        {{-- Name & Cheo Badge --}}
                        <div class="member-card-name" style="font-weight:800;">{{ $m->name }}</div>
                        <span class="badge-pill badge-kiongozi" style="font-weight:800;letter-spacing:.3px;background:#fef3c7;color:#92400e;border:1px solid #fde68a;">
                            <i class="fa-solid fa-award" style="margin-right:4px;"></i> {{ $m->pivot?->cheo ?? $m->cheo ?? 'Kiongozi' }}
                        </span>

                        {{-- Contact info --}}
                        <div class="member-card-email"><i class="fa-solid fa-envelope" style="margin-right:4px;color:var(--accent)"></i>{{ $m->email }}</div>
                        @if($m->phone)
                            <div class="member-card-phone"><i class="fa-solid fa-phone" style="margin-right:4px;color:var(--accent-green)"></i>{{ $m->phone }}</div>
                        @endif

                        {{-- Action for Mwenyekiti to reset/assign password --}}
                        @if($canManageLeaders && !$m->isAdmin() && !$m->isMchungaji())
                            <div style="margin-top:12px;padding-top:10px;border-top:1px solid #eef2f7;display:flex;justify-content:center;">
                                <button type="button" class="btn btn-outline btn-sm" style="font-size:.76rem;padding:5px 12px;display:flex;align-items:center;gap:6px;color:#b45309;border-color:#fde68a;background:#fffbeb;font-weight:700;border-radius:8px;" onclick="openChangeLeaderPasswordModal('{{ $m->id }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->email) }}', '{{ $department->id ?? '' }}', '{{ addslashes($m->pivot?->cheo ?? $m->cheo ?? 'Kiongozi') }}')">
                                    <i class="fa-solid fa-key" style="color:#d97706;"></i> Badili Nenosiri
                                </button>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div style="padding:24px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:12px;text-align:center;color:var(--text-muted);font-size:.85rem;">
                    <i class="fa-solid fa-user-shield" style="font-size:2rem;color:#94a3b8;margin-bottom:8px;display:block;"></i>
                    Bado hakuna viongozi waliosajiliwa kwenye idara hii.
                    @if($canManageLeaders)
                        <div style="margin-top:10px;">
                            <button onclick="document.getElementById('registerLeaderModal').classList.add('open')" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-plus"></i> Sajili Kiongozi wa Kwanza
                            </button>
                        </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- ════════════════════════════════════════════ --}}
            {{-- 2. WASHIRIKA WA IDARA (Members)              --}}
            {{-- ════════════════════════════════════════════ --}}
            <div style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.03);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="width:34px;height:34px;border-radius:10px;background:#e0f2fe;color:#0369a1;display:inline-flex;align-items:center;justify-content:center;font-size:1.05rem;">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <div>
                            <div style="font-size:1rem;font-weight:800;color:var(--text);">Washirika wa Idara</div>
                            <div style="font-size:.76rem;color:var(--text-muted);">Wanachama wanaoshiriki katika huduma za idara hii</div>
                        </div>
                        <span class="badge-pill" style="background:#f1f5f9;color:#475569;font-weight:800;font-size:.75rem;">{{ $departmentWashirika->count() }}</span>
                    </div>

                    <button onclick="document.getElementById('addMemberModal').classList.add('open')" class="btn btn-outline btn-sm" style="display:flex;align-items:center;gap:6px;font-size:.78rem;">
                        <i class="fa-solid fa-user-plus"></i> Ongeza Mshirika
                    </button>
                </div>

                @if($departmentWashirika->count() > 0)
                <div class="members-grid">
                    @foreach($departmentWashirika as $m)
                    <div class="member-card" data-departments="{{ $m->departments->pluck('id')->join(',') }}">
                        @if(!$user->isAdmin() && $m->id !== $user->id)
                            <form method="POST" action="{{ route('members.remove') }}"
                                onsubmit="return confirm('Una uhakika wa kuondoa {{ addslashes($m->name) }}?')"
                                style="margin:0">
                                @csrf
                                <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">
                                <input type="hidden" name="user_id" value="{{ $m->id }}">
                                <button type="submit" class="member-card-remove">
                                    <i class="fa-solid fa-user-minus"></i> Ondoa
                                </button>
                            </form>
                        @elseif($m->id === $user->id)
                            <span class="member-card-self">Wewe</span>
                        @endif

                        <div class="member-card-photo">
                            @if($m->avatar)
                                <img src="{{ asset('storage/'.$m->avatar) }}" alt="{{ $m->name }}">
                            @else
                                {{ strtoupper(substr($m->name, 0, 1)) }}
                            @endif
                        </div>

                        <div class="member-card-name">{{ $m->name }}</div>
                        <span class="badge-pill badge-mshirika">
                            {{ $m->pivot?->cheo ?? 'Mshirika' }}
                        </span>

                        <div class="member-card-email"><i class="fa-solid fa-envelope" style="margin-right:4px;color:var(--accent)"></i>{{ $m->email }}</div>
                        @if($m->phone)
                            <div class="member-card-phone"><i class="fa-solid fa-phone" style="margin-right:4px;color:var(--accent-green)"></i>{{ $m->phone }}</div>
                        @endif

                        @if($user->isMchungaji())
                            <details style="margin-top:10px;text-align:left;border-top:1px solid var(--border);padding-top:9px;">
                                <summary style="cursor:pointer;color:var(--accent);font-weight:800;font-size:.78rem;">Tazama taarifa kamili</summary>
                                <div style="display:grid;gap:5px;margin-top:9px;font-size:.75rem;color:var(--text-muted);">
                                    <div><strong style="color:var(--text);">Idara:</strong> {{ $m->departments->pluck('name_sw')->join(', ') ?: 'Hana idara' }}</div>
                                    <div><strong style="color:var(--text);">Jinsia:</strong> {{ $m->gender === 'male' ? 'Mwanaume' : ($m->gender === 'female' ? 'Mwanamke' : 'Haijawekwa') }}</div>
                                    <div><strong style="color:var(--text);">Hali ya ndoa:</strong> {{ $m->marital_status === 'married' ? 'Nimeoa / Nimeolewa' : ($m->marital_status === 'single' ? 'Sijaoa / Sijaolewa' : ($m->marital_status === 'widowed' ? 'Mjane' : 'Haijawekwa')) }}</div>
                                    <div><strong style="color:var(--text);">Mwenza / Watoto:</strong> {{ $m->spouse_names ?: '—' }} / {{ $m->children_count ?? 0 }}</div>
                                    <div><strong style="color:var(--text);">Kuokoka / Kubatizwa:</strong> {{ $m->saved_date?->format('d/m/Y') ?? '—' }} / {{ $m->baptism_date?->format('d/m/Y') ?? '—' }}</div>
                                    <div><strong style="color:var(--text);">Huduma:</strong> {{ $m->service ?: 'Haijawekwa' }}</div>
                                    <div><strong style="color:var(--text);">Elimu:</strong> {{ $m->education ?: 'Haijawekwa' }}</div>
                                    <div><strong style="color:var(--text);">Mtaa / Kata:</strong> {{ collect([$m->street_name, $m->ward])->filter()->join(', ') ?: 'Haijawekwa' }}</div>
                                </div>
                            </details>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align:center;padding:36px;color:var(--text-muted);">
                    <i class="fa-solid fa-users" style="font-size:2.2rem;opacity:.3;margin-bottom:10px;display:block;"></i>
                    <p style="font-size:.88rem;">Hakuna washirika wengine bado katika idara hii.</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: MATANGAZO                       --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="panel" id="panel-announcements">
            @if($user->isKiongozi())
            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#d1fae5;color:#059669;"><i class="fa-solid fa-plus"></i></span>
                    Tengeneza Tangazo Jipya
                </div>
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">
                    <div class="form-group">
                        <label class="form-label">Kichwa cha Tangazo</label>
                        <input type="text" name="title" class="form-control" required placeholder="Andika kichwa hapa...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Maelezo</label>
                        <textarea name="content" class="form-control" rows="4" required placeholder="Andika maelezo ya tangazo..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tangazo lionekane hadi</label>
                        <input type="date" name="expires_at" class="form-control" min="{{ now()->toDateString() }}" required>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-paper-plane"></i> Chapisha Tangazo</button>
                </form>
            </div>
            @endif

            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#d1fae5;color:#059669;"><i class="fa-solid fa-bullhorn"></i></span>
                    {{ $user->isKiongozi() ? 'Matangazo ya Idara na Kanisa' : 'Matangazo ya Idara Zangu na Kanisa' }}
                </div>
                @forelse($displayAnnouncements as $ann)
                    <div class="item-card green">
                        <div class="item-card-header">
                            <span class="item-card-title">{{ $ann->title }}</span>
                            <span class="item-card-date">
                                {{ $ann->created_at->format('d/m/Y H:i') }}
                                @if($ann->expires_at)
                                    · Hadi {{ $ann->expires_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                        <div class="item-card-body">{{ $ann->content }}</div>
                        @if($ann instanceof \App\Models\ChurchAnnouncement && $ann->media_path)
                            <div style="margin-top:12px;">
                                @if($ann->media_type === 'video')
                                    <video controls preload="metadata" style="display:block;max-width:100%;max-height:300px;border-radius:8px;" src="{{ asset('storage/'.$ann->media_path) }}"></video>
                                    <a href="{{ asset('storage/'.$ann->media_path) }}" target="_blank" rel="noopener" style="display:inline-block;margin-top:7px;">Fungua video kwa ukubwa kamili</a>
                                @else
                                    <a href="{{ asset('storage/'.$ann->media_path) }}" target="_blank" rel="noopener" title="Fungua picha kwa ukubwa kamili">
                                        <img src="{{ asset('storage/'.$ann->media_path) }}" alt="Picha ya tangazo: {{ $ann->title }}" style="display:block;max-width:100%;max-height:300px;object-fit:contain;border-radius:8px;">
                                    </a>
                                    <a href="{{ asset('storage/'.$ann->media_path) }}" target="_blank" rel="noopener" style="display:inline-block;margin-top:7px;">Fungua picha kwa ukubwa kamili</a>
                                @endif
                            </div>
                        @endif
                        <span class="badge-pill badge-mshirika" style="margin-top:8px;display:inline-block;">{{ $ann instanceof \App\Models\ChurchAnnouncement ? 'Tangazo la Kanisa' : ($ann->department->name_sw ?? 'Tangazo la Kanisa') }}</span>
                    </div>
                @empty
                    <p style="color:var(--text-muted);text-align:center;padding:20px;">Hakuna matangazo kwa sasa.</p>
                @endforelse
            </div>
        </div>

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: RIPOTI (Kiongozi only)          --}}
        {{-- ══════════════════════════════════════ --}}
        @if($user->isKiongozi())
        <div class="panel" id="panel-reports">
            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-plus"></i></span>
                    Tengeneza Ripoti Mpya
                </div>
                <form action="{{ route('reports.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">
                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label">Kichwa cha Ripoti</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Aina ya Ripoti</label>
                            <select name="type" class="form-control" required>
                                <option value="weekly">Ripoti ya Wiki</option>
                                <option value="monthly">Ripoti ya Mwezi</option>
                                <option value="special">Ripoti Maalumu</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tarehe</label>
                            <input type="date" name="report_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Maelezo ya Ripoti</label>
                        <textarea name="content" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-amber"><i class="fa-solid fa-floppy-disk"></i> Hifadhi Ripoti</button>
                </form>
            </div>

            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-chart-bar"></i></span>
                    Ripoti Zilizopo ({{ $reports->count() }})
                </div>
                @forelse($reports as $r)
                    <div class="item-card amber">
                        <div class="item-card-header">
                            <span class="item-card-title">{{ $r->title }}</span>
                            <span class="item-card-date">{{ $r->report_date }} · {{ ucfirst($r->type) }}</span>
                        </div>
                        <div class="item-card-body">{{ Str::limit($r->content, 150) }}</div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
                            <a href="{{ route('reports.show', $r->id) }}" class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-eye"></i> Fungua
                            </a>
                            <a href="{{ route('reports.download', $r->id) }}" class="btn btn-outline btn-sm" target="_blank">
                                <i class="fa-solid fa-file-pdf" style="color:#ef4444;"></i> Pakua PDF
                            </a>
                        </div>
                    </div>
                @empty
                    <p style="color:var(--text-muted);text-align:center;padding:20px;">Hakuna ripoti bado.</p>
                @endforelse
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: BARUA / OFFICIAL LETTERS        --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="panel" id="panel-letters">
            
            {{-- Form Card: Andika / Omba Barua Rasmi (Viongozi & Mchungaji & Admin Only) --}}
            @if($user->isKiongozi() || $user->isMchungaji() || $user->isAdmin())
            <div class="card" style="margin-bottom:24px;">
                <div class="card-title">
                    <span class="icon-box" style="background:#e0e7ff;color:#4338ca;"><i class="fa-solid fa-pen-to-square"></i></span>
                    Andika Barua Rasmi ya Kanisa
                </div>

                <form action="{{ route('official_letters.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">

                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:16px;margin-bottom:12px;">
                        
                        {{-- Sender --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Mtumaji</label>
                            <input type="text" class="form-control" value="{{ $user->name }}{{ $user->email ? ' (' . $user->email . ')' : '' }}" readonly>
                        </div>

                        {{-- Letter Type --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Aina ya Barua Rasmi</label>
                            <select name="letter_type" class="form-control" required>
                                <option value="Barua ya Utambulisho">Barua ya Utambulisho</option>
                                <option value="Barua ya Uthibitisho">Barua ya Uthibitisho</option>
                                <option value="Barua ya Uanachama">Barua ya Uanachama</option>
                                <option value="Barua ya Mapendekezo">Barua ya Mapendekezo</option>
                                <option value="Barua ya Mwaliko">Barua ya Mwaliko</option>
                                <option value="Barua ya Shukrani">Barua ya Shukrani</option>
                                <option value="Barua ya Maombi">Barua ya Maombi</option>
                                <option value="Barua ya Kuhudhuria Kanisa">Barua ya Kuhudhuria Kanisa</option>
                                <option value="Barua Nyingine Rasmi">Barua Nyingine Rasmi</option>
                            </select>
                        </div>

                        {{-- Recipient --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Barua Kwenda Kwa (Mpokeaji / Taasisi na Anwani Yake)</label>
                            <textarea name="recipient" class="form-control" rows="3" required placeholder="Mfano:&#10;Mkurugenzi wa Benki X&#10;S.L.P 12345&#10;Dar es Salaam"></textarea>
                        </div>

                    </div>

                    {{-- Subject --}}
                    <div class="form-group">
                        <label class="form-label">Kichwa cha Barua (Subject)</label>
                        <input type="text" name="subject" class="form-control" required placeholder="mfano: YAH: UTAMBULISHO WA MWANACHAMA WETU">
                    </div>

                    {{-- Body --}}
                    <div class="form-group">
                        <label class="form-label">Maudhui ya Barua / Maelezo</label>
                        <textarea name="body" class="form-control" rows="6" required placeholder="Andika maudhui ya barua au maelezo yatakayoingizwa kwenye barua rasmi..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding:12px 24px;"><i class="fa-solid fa-paper-plane"></i> Wasilisha Barua kwa Mchungaji</button>
                </form>
            </div>
            @endif

            {{-- Table Card: Barua Zilizotumwa / Maombi --}}
            <div class="card" style="padding:0;overflow:hidden;">
                <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-weight:700;display:flex;align-items:center;gap:10px;">
                    <span class="icon-box" style="background:#e0e7ff;color:#4338ca;"><i class="fa-solid fa-envelope-open-text"></i></span>
                    Barua Zilizotumwa na Hali Zake (Status)
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="padding-left:20px;">Mwombaji / Mshiriki</th>
                            <th>Aina ya Barua</th>
                            <th>Kumb. (Ref No.)</th>
                            <th>Kwenda Kwa (Recipient)</th>
                            <th>Hali / Status</th>
                            <th style="text-align:right;padding-right:20px;">Hatua / PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lettersList = ($user->isMchungaji() || $user->isAdmin()) ? ($allLetters ?? collect()) : (isset($myLetters) ? $myLetters : (isset($letters) ? $letters : collect()));
                        @endphp
                        @forelse($lettersList as $l)
                            <tr>
                                <td style="padding-left:20px;font-weight:600;">{{ $l->user->name ?? '—' }}</td>
                                <td><strong style="color:var(--text);">{{ $l->letter_type ?? 'Barua Rasmi' }}</strong></td>
                                <td style="font-family:monospace;font-size:.85rem;color:var(--accent);">
                                    <button type="button" style="background:none;border:none;color:var(--accent);font-family:monospace;font-size:.85rem;cursor:pointer;text-decoration:underline;padding:0;" onclick="previewLetterModal({{ json_encode([
                                        'id' => $l->id,
                                        'type' => $l->letter_type ?? 'Barua Rasmi',
                                        'ref' => $l->reference_number ?? 'Bado haijaidhinishwa',
                                        'sender' => $l->user->name ?? '—',
                                        'recipient' => $l->recipient ?? '—',
                                        'subject' => $l->subject ?? '—',
                                        'body' => $l->body ?? '—',
                                        'status' => $l->status,
                                        'rejection_reason' => $l->rejection_reason ?? null,
                                        'date' => $l->created_at ? $l->created_at->format('d/m/Y H:i') : '',
                                        'view_url' => ($l->status === 'generated' || $l->status === 'approved') ? route('official_letters.view', $l->id) : null,
                                        'download_url' => ($l->status === 'generated' || $l->status === 'approved') ? route('official_letters.download', $l->id) : null,
                                    ]) }})">
                                        {{ $l->reference_number ?? '— (Linasubiri)' }}
                                    </button>
                                </td>
                                <td style="color:var(--text-muted);">{{ Str::limit($l->recipient ?? $l->recipient_type, 25) }}</td>
                                <td>
                                    @if($l->status === 'approved' || $l->status === 'generated')
                                        <span class="badge-pill badge-kiongozi" style="background:#dcfce7;color:#15803d;">✓ Imeidhinishwa</span>
                                    @elseif($l->status === 'rejected')
                                        <span class="badge-pill" style="background:#fee2e2;color:#b91c1c;">✗ Imekataliwa</span>
                                    @else
                                        <span class="badge-pill badge-pending">⏳ Inasubiri Idhini</span>
                                    @endif
                                </td>
                                <td style="text-align:right;padding-right:20px;white-space:nowrap;">
                                    @if(($user->isMchungaji() || $user->isAdmin()) && ($l->status === 'pending' || $l->status === 'under_review'))
                                        <form action="{{ route('official_letters.approve', $l->id) }}" method="POST" style="display:inline-block;margin-right:4px;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i> Idhinisha & Weka Muhuri</button>
                                        </form>
                                    @endif
                                    @if($l->status === 'generated' || $l->status === 'approved')
                                        <a href="{{ route('official_letters.view', $l->id) }}" class="btn btn-primary btn-sm" target="_blank" style="margin-right:4px;background:#4f46e5;border-color:#4f46e5;color:#fff;" title="Fungua Barua PDF kwenye Tab Mpya">
                                            <i class="fa-solid fa-eye"></i> Tazama
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-outline btn-sm" style="margin-right:4px;" onclick="previewLetterModal({{ json_encode([
                                        'id' => $l->id,
                                        'type' => $l->letter_type ?? 'Barua Rasmi',
                                        'ref' => $l->reference_number ?? 'Bado haijaidhinishwa',
                                        'sender' => $l->user->name ?? '—',
                                        'recipient' => $l->recipient ?? '—',
                                        'subject' => $l->subject ?? '—',
                                        'body' => $l->body ?? '—',
                                        'status' => $l->status,
                                        'rejection_reason' => $l->rejection_reason ?? null,
                                        'date' => $l->created_at ? $l->created_at->format('d/m/Y H:i') : '',
                                        'view_url' => ($l->status === 'generated' || $l->status === 'approved') ? route('official_letters.view', $l->id) : null,
                                        'download_url' => ($l->status === 'generated' || $l->status === 'approved') ? route('official_letters.download', $l->id) : null,
                                        ]) }})" title="Tazama Maelezo ya Barua">
                                            <i class="fa-solid fa-eye" style="color:#6366f1;"></i> Tazama
                                        </button>
                                    @endif

                                    @if($l->status === 'generated' || $l->status === 'approved')
                                        <a href="{{ route('official_letters.download', $l->id) }}" class="btn btn-outline btn-sm" target="_blank" title="Pakua Barua (PDF)">
                                            <i class="fa-solid fa-download" style="color:#ef4444;"></i> Pakua PDF
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:30px;">Hakuna barua bado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: SMS (Kiongozi only)             --}}
        {{-- ══════════════════════════════════════ --}}
        @if($user->isKiongozi())
        <div class="panel" id="panel-sms">
            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#f3e8ff;color:#7c3aed;"><i class="fa-solid fa-mobile-screen-button"></i></span>
                    Tuma SMS kwa Wanachama ({{ $members->count() }}) wa {{ $department->name_sw ?? '' }}
                </div>
                <form action="{{ route('sms.send') }}" method="POST">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">
                    <div class="form-group">
                        <label class="form-label">Ujumbe wa SMS <span style="color:var(--text-muted);font-weight:400;">(Max 160 herufi)</span></label>
                        <textarea name="message" class="form-control" rows="4" maxlength="160" required placeholder="Andika ujumbe wako hapa..." id="smsText" oninput="updateSmsCount()"></textarea>
                        <p style="text-align:right;font-size:.78rem;color:var(--text-muted);margin-top:4px;"><span id="smsCount">0</span>/160</p>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Tuma SMS</button>
                </form>
            </div>
            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#f3e8ff;color:#7c3aed;"><i class="fa-solid fa-history"></i></span>
                    Historia ya SMS ({{ $smsLogs->count() }})
                </div>
                @forelse($smsLogs as $sms)
                    <div class="item-card" style="border-left-color:#8b5cf6;background:#faf5ff;">
                        <div class="item-card-header">
                            <span class="item-card-title">{{ Str::limit($sms->message, 60) }}</span>
                            <span class="item-card-date">{{ $sms->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="item-card-body" style="color:var(--text-muted)">
                            Waliopokea: <strong>{{ $sms->recipients_count }}</strong> ·
                            <span class="badge-pill {{ $sms->status === 'sent' ? 'limit-ok' : 'limit-warn' }}">{{ ucfirst($sms->status) }}</span>
                        </div>
                    </div>
                @empty
                    <p style="color:var(--text-muted);text-align:center;padding:20px;">Hakuna SMS zilizotumwa bado.</p>
                @endforelse
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: PAYMENTS & RECEIPTS             --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="panel" id="panel-payments">
            @if($user->isAdmin() || $user->isMchungaji() || $user->isKiongozi())
                @php $visibleCampaigns = ($contributionCampaigns ?? collect()); @endphp
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
                    <div>
                        <h4 style="font-size:1.2rem;font-weight:800;color:var(--text);"><i class="fa-solid fa-money-bill-wave" style="color:var(--accent-green);margin-right:8px;"></i> Michango na Malipo ya Idara</h4>
                        <p style="font-size:.85rem;color:var(--text-muted);margin-top:2px;">Kiongozi anaweka aina ya mchango, kiasi cha kila mwanachama, mwisho wa kukusanya, kisha anarekodi waliotoa.</p>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <button onclick="document.getElementById('addContributionModal').classList.add('open')" class="btn btn-primary">
                            <i class="fa-solid fa-bullseye"></i> Anzisha Mchango
                        </button>
                        <button onclick="document.getElementById('addPaymentModal').classList.add('open')" class="btn btn-success">
                            <i class="fa-solid fa-plus"></i> Jaza Aliyetoa
                        </button>
                    </div>
                </div>

                @if($user->isMchungaji())
                <div class="card" style="padding:0;overflow:hidden;margin-top:20px;">
                    <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-weight:700;display:flex;align-items:center;justify-content:space-between;">
                        <span><i class="fa-solid fa-hand-holding-heart" style="margin-right:6px;color:#d97706;"></i> Sadaka zote za Kanisa</span>
                        <span style="font-size:.8rem;color:var(--text-muted);font-weight:400;">Jumla: {{ ($allOfferings ?? collect())->count() }}</span>
                        <span style="display:flex;gap:8px;"><a href="{{ route('church-office.offerings.report.pdf', ['period' => 'week', 'date' => now()->format('Y-m-d')]) }}" target="_blank" style="font-size:.8rem;color:#13794d;text-decoration:none;">PDF ya wiki</a><a href="{{ route('church-office.offerings.report.pdf', ['period' => 'month', 'date' => now()->format('Y-m-d')]) }}" target="_blank" style="font-size:.8rem;color:#13794d;text-decoration:none;">PDF ya mwezi</a></span>
                    </div>
                    <table class="data-table">
                        <thead><tr><th style="padding-left:20px;">Aina ya Sadaka</th><th>Kiasi</th><th>Njia ya Malipo</th><th>Tarehe</th></tr></thead>
                        <tbody>@forelse(($allOfferings ?? collect()) as $offering)<tr><td style="padding-left:20px;font-weight:700;">{{ $offering->offering_type }}</td><td style="font-weight:800;color:#166534;">Tsh {{ number_format($offering->amount, 2) }}</td><td>{{ $offering->payment_method }}</td><td>{{ $offering->offering_date->format('d/m/Y') }}</td></tr>@empty<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">Hakuna sadaka iliyorekodiwa bado.</td></tr>@endforelse</tbody>
                    </table>
                </div>
                @endif

                <div class="card" style="margin:20px 0;">
                    <div class="card-title"><span class="icon-box" style="background:#e0e7ff;color:#4338ca;"><i class="fa-solid fa-building-columns"></i></span> Chagua Idara Kuona Michango Yake</div>
                    <p style="font-size:.84rem;color:var(--text-muted);margin:-4px 0 15px;">Bofya idara ili kuona michango na waliotoa ndani ya idara hiyo pekee.</p>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:12px;">
                        @foreach(($allDepartments ?? collect()) as $contributionDepartment)
                            @php $departmentCampaignCount = $visibleCampaigns->where('department_id', $contributionDepartment->id)->count(); @endphp
                            <button type="button" class="btn btn-outline" style="justify-content:flex-start;text-align:left;padding:14px;min-height:74px;" onclick="showDepartmentContributions({{ $contributionDepartment->id }}, @json($contributionDepartment->name_sw))"><span><i class="fa-solid fa-building-columns" style="color:var(--accent);margin-right:7px;"></i>{{ $contributionDepartment->name_sw }}</span><br><small style="color:var(--text-muted);font-weight:600;margin-left:22px;">Michango: {{ $departmentCampaignCount }} · Bofya kufungua</small></button>
                        @endforeach
                    </div>
                </div>

                <div class="card" id="departmentContributionDetails" style="padding:0;overflow:hidden;display:none;">
                    <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-weight:700;display:flex;align-items:center;justify-content:space-between;">
                        <span><i class="fa-solid fa-bullseye" style="margin-right:6px;color:var(--accent);"></i> Michango ya <span id="selectedContributionDepartment"></span></span>
                        <span id="selectedContributionCount" style="font-size:.8rem;color:var(--text-muted);font-weight:400;"></span>
                    </div>
                    <div style="display:flex;flex-direction:column;">
                        @forelse($visibleCampaigns as $campaign)
                            @php
                                $campaignMembers = $campaign->department?->users ?? collect();
                                $campaignPayments = $campaign->payments->where('status', 'approved');
                                $campaignPaid = $campaignPayments->sum('amount');
                                $campaignTarget = (float) $campaign->amount_per_member * max(1, $campaignMembers->count());
                                $campaignPercent = $campaignTarget > 0 ? min(100, round(($campaignPaid / $campaignTarget) * 100)) : 0;
                            @endphp
                            <div class="contribution-record" data-campaign-record data-department-id="{{ $campaign->department_id }}">
                                <div style="display:grid;grid-template-columns:2fr 1.2fr 1.2fr .8fr 1fr auto;gap:16px;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border);">
                                    <div>
                                        <div style="font-weight:900;color:var(--text);">{{ $campaign->title }}</div>
                                        @if($campaign->notes)
                                            <div style="font-size:.75rem;color:var(--text-muted);margin-top:3px;">{{ Str::limit($campaign->notes, 70) }}</div>
                                        @endif
                                    </div>
                                    <div style="font-weight:700;">{{ $campaign->department->name_sw ?? 'Idara' }}</div>
                                    <div>
                                        <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Jumla iliyopatikana</div>
                                        <div style="font-weight:900;">Tsh {{ number_format($campaignPaid, 2) }}</div>
                                        <div style="height:6px;background:#e5e7eb;border-radius:999px;margin-top:6px;overflow:hidden;">
                                            <div style="width:{{ $campaignPercent }}%;height:100%;background:#10b981;"></div>
                                        </div>
                                    </div>
                                    <div style="font-weight:900;color:{{ $campaignPercent >= 100 ? '#15803d' : '#4f46e5' }};">{{ $campaignPercent }}%</div>
                                    <span class="badge-pill {{ $campaign->status === 'active' ? 'limit-ok' : 'badge-pending' }}" style="justify-self:start;">
                                        {{ $campaign->status === 'active' ? 'Inaendelea' : 'Imefungwa' }}
                                    </span>
                                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleContributionDetails({{ $campaign->id }})">
                                        <i class="fa-solid fa-users"></i> Jaza Waliotoa
                                    </button>
                                </div>

                                <div id="campaign-details-{{ $campaign->id }}" style="display:none;background:#fbfdff;border-bottom:1px solid var(--border);padding:10px 14px 16px;">
                                    <div style="display:grid;grid-template-columns:1.7fr 1fr 1fr 1fr 1.1fr 2.1fr;gap:12px;padding:10px 12px;color:var(--text-muted);font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;">
                                        <div>Mwanachama</div>
                                        <div>Kiasi Alichopangiwa</div>
                                        <div>Kiasi Alichotoa</div>
                                        <div>Kilichobaki</div>
                                        <div>Asilimia</div>
                                        <div>Jaza Malipo</div>
                                    </div>
                                    @forelse($campaignMembers as $member)
                                        @php
                                            $memberPaid = $campaignPayments->where('user_id', $member->id)->sum('amount');
                                            $memberBalance = max(0, (float) $campaign->amount_per_member - (float) $memberPaid);
                                            $memberPercent = (float) $campaign->amount_per_member > 0 ? min(100, round(($memberPaid / (float) $campaign->amount_per_member) * 100)) : 0;
                                        @endphp
                                        <div class="campaign-member-row" style="display:grid;grid-template-columns:1.7fr 1fr 1fr 1fr 1.1fr 2.1fr;gap:12px;align-items:center;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin-bottom:8px;">
                                            <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#10b981);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;flex-shrink:0;overflow:hidden;">
                                                    @if($member->avatar)
                                                        <img src="{{ asset('storage/'.$member->avatar) }}" alt="{{ $member->name }}" style="width:100%;height:100%;object-fit:cover;">
                                                    @else
                                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div style="min-width:0;">
                                                    <div style="font-weight:900;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $member->name }}</div>
                                                    <div style="font-size:.75rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $member->member_id ?? $member->email }}</div>
                                                </div>
                                            </div>
                                            <div style="font-weight:800;">Tsh {{ number_format($campaign->amount_per_member, 2) }}</div>
                                            <div style="font-weight:900;color:#15803d;">Tsh {{ number_format($memberPaid, 2) }}</div>
                                            <div style="font-weight:900;color:{{ $memberBalance > 0 ? '#b45309' : '#15803d' }};">Tsh {{ number_format($memberBalance, 2) }}</div>
                                            <div>
                                                <div style="font-weight:900;margin-bottom:5px;">{{ $memberPercent }}%</div>
                                                <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                                                    <div style="width:{{ $memberPercent }}%;height:100%;background:{{ $memberPercent >= 100 ? '#10b981' : '#6366f1' }};"></div>
                                                </div>
                                            </div>
                                            <form action="{{ route('payments.store') }}" method="POST" style="display:grid;grid-template-columns:1fr auto;gap:8px;align-items:center;margin:0;">
                                                @csrf
                                                <input type="hidden" name="contribution_campaign_id" value="{{ $campaign->id }}">
                                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                <input type="hidden" name="type" value="Mchango wa Idara">
                                                <input type="hidden" name="payment_method" value="Cash">
                                                <input type="hidden" name="payment_date" value="{{ date('Y-m-d') }}">
                                                <input type="number" name="amount" class="form-control" min="100" step="100" required placeholder="Tsh" style="height:36px;padding:8px 10px;">
                                                <button type="submit" class="btn btn-success btn-sm" style="height:36px;white-space:nowrap;">
                                                    <i class="fa-solid fa-plus"></i> Hifadhi
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <div style="text-align:center;color:var(--text-muted);padding:24px;">Hakuna members waliosajiliwa kwenye idara hii.</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center;color:var(--text-muted);padding:28px;">Hakuna mchango wa idara ulioanzishwa bado.</div>
                        @endforelse
                        <div id="noDepartmentContributions" style="display:none;text-align:center;color:var(--text-muted);padding:28px;">Hakuna mchango ulioanzishwa kwenye idara hii.</div>
                    </div>
                </div>

                <div class="card" style="display:none;">
                    <div>
                        <span>Latest Members Waliotoa Michango ya Idara</span>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="position:relative;">
                                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.8rem;"></i>
                                <input type="search" id="paymentMemberSearch" class="form-control" placeholder="Search mwanachama..." oninput="filterRecordedPayments()" style="height:36px;width:260px;padding-left:34px;">
                            </div>
                            <span style="font-size:.8rem;color:var(--text-muted);font-weight:400;">Jumla: {{ count($allPayments ?? []) }}</span>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="padding-left:20px;">Mshirika</th>
                                <th>Mchango / Malipo</th>
                                <th>Kiasi (Tsh)</th>
                                <th>Njia &amp; Kumb.</th>
                                <th>Tarehe</th>
                                <th style="text-align:right;padding-right:20px;">Risiti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allPayments ?? [] as $p)
                                <tr class="recorded-payment-row" data-member="{{ strtolower(($p->user->name ?? '') . ' ' . ($p->user->member_id ?? '') . ' ' . ($p->user->email ?? '')) }}">
                                    <td style="padding-left:20px;">
                                        <div style="font-weight:700;">{{ $p->user->name ?? 'Mwanachama' }}</div>
                                        <div style="font-size:.75rem;color:var(--text-muted);">{{ $p->user->member_id ?? 'EAGT-KBP' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge-pill" style="background:#e0e7ff;color:#3730a3;font-weight:700;">{{ $p->type }}</span>
                                        @if($p->contributionCampaign?->department)
                                            <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">{{ $p->contributionCampaign->department->name_sw }}</div>
                                        @endif
                                    </td>
                                    <td style="font-weight:800;color:#166534;">Tsh {{ number_format($p->amount, 2) }}</td>
                                    <td style="font-size:.85rem;color:var(--text-muted);">
                                        {{ $p->payment_method }}
                                        @if($p->transaction_id)
                                            <br><span style="font-family:monospace;font-size:.78rem;color:var(--accent);">{{ $p->transaction_id }}</span>
                                        @endif
                                    </td>
                                    <td style="font-size:.85rem;">{{ \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') }}</td>
                                    <td style="text-align:right;padding-right:20px;">
                                        <a href="{{ route('receipts.download', $p->id) }}" class="btn btn-outline btn-sm" target="_blank">
                                            <i class="fa-solid fa-receipt" style="color:#2563eb;"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:30px;">Hakuna malipo yaliyosajiliwa bado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div style="margin-bottom:20px;">
                    <h4 style="font-size:1.2rem;font-weight:800;color:var(--text);"><i class="fa-solid fa-wallet" style="color:var(--accent-green);margin-right:8px;"></i> Malipo Yangu</h4>
                    <p style="font-size:.85rem;color:var(--text-muted);margin-top:2px;">Hapa unaona kiwango ulichotoa na kilichobaki kwa michango ya idara zako.</p>
                </div>

                @if(($standalonePayments ?? collect())->isNotEmpty())
                    <div class="card" style="margin-bottom:18px;padding:0;overflow:hidden;">
                        <div class="card-title" style="padding:16px 20px;margin:0;border-bottom:1px solid var(--border);">
                            <span class="icon-box" style="background:#dcfce7;color:#15803d;"><i class="fa-solid fa-receipt"></i></span>
                            Malipo Yaliyothibitishwa
                        </div>
                        @foreach($standalonePayments as $payment)
                            <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;padding:14px 20px;border-bottom:{{ $loop->last ? '0' : '1px solid #eef2f7' }};">
                                <div><strong>{{ $payment->type }}</strong><br><span style="font-size:.78rem;color:var(--text-muted);">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }} · {{ $payment->payment_method }}</span></div>
                                <strong style="color:#15803d;white-space:nowrap;">Tsh {{ number_format($payment->amount, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                @endif

                @php
                    $myCampaignGroups = ($myContributionCampaigns ?? collect())->groupBy('department_id');
                @endphp

                @if($myCampaignGroups->isNotEmpty())
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:18px;">
                        @foreach($myCampaignGroups as $departmentId => $deptCampaigns)
                            @php
                                $dept = $deptCampaigns->first()->department;
                                $deptAssigned = $deptCampaigns->sum('amount_per_member');
                                $deptPaid = $deptCampaigns->sum(fn($campaign) => (float) ($campaign->paid_amount ?? 0));
                                $deptBalance = max(0, (float) $deptAssigned - (float) $deptPaid);
                                $deptPercent = $deptAssigned > 0 ? min(100, round(($deptPaid / $deptAssigned) * 100)) : 0;
                            @endphp
                            <div style="background:#fff;border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);overflow:hidden;">
                                <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;gap:14px;align-items:flex-start;">
                                    <div>
                                        <div style="display:flex;align-items:center;gap:9px;font-weight:900;font-size:1.05rem;color:var(--text);">
                                            <span style="width:34px;height:34px;border-radius:10px;background:#e0e7ff;color:#4f46e5;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa-solid fa-building-columns"></i>
                                            </span>
                                            {{ $dept->name_sw ?? 'Idara' }}
                                        </div>
                                        <div style="font-size:.78rem;color:var(--text-muted);margin-top:7px;">
                                            Michango {{ $deptCampaigns->count() }} inayokuhusu
                                        </div>
                                    </div>
                                    <span class="badge-pill {{ $deptBalance > 0 ? 'limit-warn' : 'limit-ok' }}" style="white-space:nowrap;">
                                        {{ $deptBalance > 0 ? 'Una salio' : 'Umekamilisha' }}
                                    </span>
                                </div>

                                <div style="padding:16px 20px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;border-bottom:1px solid var(--border);">
                                    <div>
                                        <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Ulipangiwa</div>
                                        <div style="font-weight:900;color:var(--text);">Tsh {{ number_format($deptAssigned, 2) }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Umetoa</div>
                                        <div style="font-weight:900;color:#15803d;">Tsh {{ number_format($deptPaid, 2) }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Imebaki</div>
                                        <div style="font-weight:900;color:{{ $deptBalance > 0 ? '#b45309' : '#15803d' }};">Tsh {{ number_format($deptBalance, 2) }}</div>
                                    </div>
                                    <div style="grid-column:1 / -1;">
                                        <div style="display:flex;justify-content:space-between;font-size:.75rem;font-weight:800;color:var(--text-muted);margin-bottom:6px;">
                                            <span>Maendeleo ya Idara</span>
                                            <span>{{ $deptPercent }}%</span>
                                        </div>
                                        <div style="height:8px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                                            <div style="width:{{ $deptPercent }}%;height:100%;background:{{ $deptPercent >= 100 ? '#10b981' : '#6366f1' }};"></div>
                                        </div>
                                    </div>
                                </div>

                                <div style="padding:8px 0;">
                                    @foreach($deptCampaigns as $campaign)
                                        @php
                                            $campaignPercent = (float) $campaign->amount_per_member > 0
                                                ? min(100, round(((float) ($campaign->paid_amount ?? 0) / (float) $campaign->amount_per_member) * 100))
                                                : 0;
                                        @endphp
                                        <div style="display:grid;grid-template-columns:1.4fr 1fr 1fr 70px;gap:12px;align-items:center;padding:12px 20px;border-bottom:{{ $loop->last ? '0' : '1px solid #eef2f7' }};">
                                            <div style="min-width:0;">
                                                <div style="font-weight:900;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $campaign->title }}</div>
                                                <div style="font-size:.75rem;color:var(--text-muted);">Mwisho: {{ $campaign->due_date ? $campaign->due_date->format('d/m/Y') : '-' }}</div>
                                            </div>
                                            <div>
                                                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;">Umetoa</div>
                                                <div style="font-weight:900;color:#15803d;">Tsh {{ number_format($campaign->paid_amount ?? 0, 2) }}</div>
                                            </div>
                                            <div>
                                                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;">Imebaki</div>
                                                <div style="font-weight:900;color:{{ ($campaign->balance_amount ?? 0) > 0 ? '#b45309' : '#15803d' }};">Tsh {{ number_format($campaign->balance_amount ?? 0, 2) }}</div>
                                            </div>
                                            <div style="text-align:right;font-weight:900;color:{{ $campaignPercent >= 100 ? '#15803d' : '#4f46e5' }};">{{ $campaignPercent }}%</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="background:#fff;border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);text-align:center;color:var(--text-muted);padding:34px;">
                        Hakuna mchango wa idara unaokuhusu kwa sasa.
                    </div>
                @endif
            @endif
        </div>

        @if($user->isAdmin() || $user->isMchungaji())
        <div class="panel" id="panel-settings">
            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#e0e7ff;color:#4338ca;"><i class="fa-solid fa-sliders"></i></span>
                    Mipangilio ya Kanisa, Muhuri na Saini
                </div>
                @php
                    $settingValue = fn($key, $default = '') => \App\Models\ChurchSetting::where('key', $key)->value('value') ?? $default;
                @endphp
                <form action="{{ route('church_settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jina la Kanisa</label>
                            <input type="text" name="church_name" class="form-control" value="{{ $settingValue('church_name', 'E.A.G.T KIBURUGWA PHILADELPHIA CHURCH') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Anwani</label>
                            <input type="text" name="address" class="form-control" value="{{ $settingValue('address', 'P.O. BOX 3236, DAR ES SALAAM') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jina la Muidhinishaji</label>
                            <input type="text" name="authorized_name" class="form-control" value="{{ $settingValue('authorized_name', 'REV. MAGDALENA MKINGA') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cheo cha Muidhinishaji</label>
                            <input type="text" name="authorized_title" class="form-control" value="{{ $settingValue('authorized_title', 'MCHUNGAJI') }}">
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Muhuri (PNG)</label>
                            <input type="file" name="stamp" class="form-control" accept="image/png">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Saini</label>
                            <input type="file" name="signature" class="form-control" accept="image/png,image/jpeg">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Hifadhi Mipangilio</button>
                </form>
            </div>
        </div>

        <div class="panel" id="panel-audit">
            <div class="card" style="padding:0;overflow:hidden;">
                <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-weight:800;display:flex;align-items:center;gap:10px;">
                    <span class="icon-box" style="background:#ede9fe;color:#6d28d9;"><i class="fa-solid fa-shield-halved"></i></span>
                    Audit Log & Usalama
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="padding-left:20px;">Mtumiaji</th>
                            <th>Tendo</th>
                            <th>Maelezo</th>
                            <th>IP / Idara</th>
                            <th>Tarehe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($auditLogs ?? collect()) as $log)
                            <tr>
                                <td style="padding-left:20px;font-weight:700;">{{ $log->user->name ?? 'System' }}</td>
                                <td><span class="badge-pill" style="background:#eef2ff;color:#4f46e5;">{{ $log->action }}</span></td>
                                <td>{{ $log->description }}</td>
                                <td style="color:var(--text-muted);">{{ $log->ip_address ?? '-' }}</td>
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            @foreach(($allPayments ?? collect())->take(5) as $payment)
                                <tr>
                                    <td style="padding-left:20px;font-weight:700;">{{ $payment->user->name ?? 'Mwanachama' }}</td>
                                    <td><span class="badge-pill limit-ok">PAYMENT_APPROVED</span></td>
                                    <td>Malipo ya {{ $payment->type }}: Tsh {{ number_format($payment->amount, 2) }}</td>
                                    <td style="color:var(--text-muted);">{{ $payment->contributionCampaign->department->name_sw ?? '-' }}</td>
                                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                            @foreach(($reports ?? collect())->take(5) as $report)
                                <tr>
                                    <td style="padding-left:20px;font-weight:700;">{{ $report->user->name ?? 'Kiongozi' }}</td>
                                    <td><span class="badge-pill badge-pending">REPORT_CREATED</span></td>
                                    <td>Ripoti imewekwa: {{ $report->title }}</td>
                                    <td style="color:var(--text-muted);">{{ $report->department->name_sw ?? $department->name_sw ?? '-' }}</td>
                                    <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                            @if(($allPayments ?? collect())->isEmpty() && ($reports ?? collect())->isEmpty())
                                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:30px;">Bado hakuna shughuli zilizorekodiwa.</td></tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: HELP / MSAADA                   --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="panel" id="panel-help">
            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-headset"></i></span>
                    Tuma Ujumbe kwa Admin
                </div>
                <p style="font-size:.88rem;color:var(--text-muted);margin-bottom:18px;">
                    Kama una tatizo, swali au maombi maalum, wasiliana na Admin wa mfumo hapa chini. Ujumbe wako utafika moja kwa moja kwa Admin.
                </p>
                <form action="{{ route('help.send') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Mada / Subject</label>
                        <input type="text" name="subject" class="form-control" required placeholder="Mfano: Tatizo la kuingia, Ombi la kuongezwa idara...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Maelezo ya Ujumbe</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Elezea tatizo au ombi lako kwa undani..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-amber"><i class="fa-solid fa-paper-plane"></i> Tuma kwa Admin</button>
                </form>
            </div>

            @if($myHelpMessages->count() > 0)
            <div class="card">
                <div class="card-title">
                    <span class="icon-box" style="background:#d1fae5;color:#059669;"><i class="fa-solid fa-clock-rotate-left"></i></span>
                    Ujumbe Wangu Uliotumwa
                </div>
                @foreach($myHelpMessages as $hm)
                    <div class="help-sent-card">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <strong style="font-size:.9rem;">{{ $hm->subject }}</strong>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <span class="badge-pill {{ $hm->status === 'read' ? 'badge-read' : 'badge-pending' }}">
                                    {{ $hm->status === 'read' ? '✓ Imesomwa' : '⏳ Inasubiri' }}
                                </span>
                                <span style="font-size:.75rem;color:var(--text-muted);">{{ $hm->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <p style="font-size:.85rem;color:#4b5563;">{{ Str::limit($hm->message, 120) }}</p>
                    </div>
                @endforeach
            </div>
            @endif
        </div>{{-- /panel-help --}}

        {{-- ══════════════════════════════════════ --}}
        {{-- PANEL: TAARIFA ZANGU (Mshirika Profile) --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="panel" id="panel-account-settings">
            <div style="margin-bottom:18px;"><h3 style="font-size:1.3rem;font-weight:800;color:var(--text);">{{ __('Mipangilio ya Akaunti') }}</h3><p style="color:var(--text-muted);margin-top:4px;">{{ __('Simamia taarifa za akaunti yako.') }}</p></div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px;">
                <section class="card"><h4 style="font-size:1rem;font-weight:800;margin-bottom:14px;">{{ __('Taarifa za Akaunti') }}</h4><form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">@csrf @method('PATCH')<input type="hidden" name="return_to_dashboard" value="1"><div class="form-group"><label class="form-label">{{ __('Jina la mtumiaji') }}</label><input class="form-control" name="name" value="{{ $user->name }}" required></div><div class="form-group"><label class="form-label">{{ __('Barua pepe') }}</label><input class="form-control" type="email" name="email" value="{{ $user->email }}" required></div><div class="form-group"><label class="form-label">{{ __('Namba ya simu') }}</label><input class="form-control" name="phone" value="{{ $user->phone }}"></div><div class="form-group"><label class="form-label">{{ __('Picha ya mtumiaji') }}</label><input class="form-control" type="file" name="avatar" accept="image/*"></div><div class="form-group"><label class="form-label">{{ __('Lugha ya mfumo') }}</label><select class="form-control" name="language"><option value="sw" @selected(($user->language ?? 'sw') === 'sw')>{{ __('Kiswahili') }}</option><option value="en" @selected($user->language === 'en')>English</option></select></div><button class="btn btn-primary" style="width:100%;">{{ __('Hifadhi mabadiliko') }}</button></form></section>
                <section class="card"><h4 style="font-size:1rem;font-weight:800;margin-bottom:14px;">{{ __('Kubadilisha Nenosiri') }}</h4><form method="POST" action="{{ route('password.update') }}">@csrf @method('PUT')<div class="form-group"><label class="form-label">{{ __('Nenosiri la sasa') }}</label><input class="form-control" type="password" name="current_password" required></div><div class="form-group"><label class="form-label">{{ __('Nenosiri jipya') }}</label><input class="form-control" type="password" name="password" required></div><div class="form-group"><label class="form-label">{{ __('Rudia nenosiri jipya') }}</label><input class="form-control" type="password" name="password_confirmation" required></div><button class="btn btn-primary" style="width:100%;">{{ __('Badilisha nenosiri') }}</button></form><hr style="border:0;border-top:1px solid var(--border);margin:20px 0;"><button type="button" class="btn btn-outline" style="width:100%;" onclick="document.getElementById('personalDetailsModal').classList.add('open')">{{ __('Badilisha taarifa za akaunti') }}</button></section>
            </div>
        </div>{{-- /panel-account-settings --}}

        @if(!$user->isAdmin() && !$user->isMchungaji() && !$user->isKiongozi())
        <div class="panel" id="panel-profile">
            <div class="card" style="max-width:680px;margin:0 auto;">
                <div class="card-title">
                    <span class="icon-box" style="background:#ede9fe;color:#6d28d9;"><i class="fa-solid fa-id-card"></i></span>
                    Taarifa Zangu za Kibinafsi
                </div>

                {{-- Profile Photo --}}
                <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:28px;gap:10px;">
                    <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:3px solid var(--accent);background:#f1f5f9;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(99,102,241,.2);">
                        @if($user->avatar)
                            <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <i class="fa-solid fa-user-large" style="font-size:2.8rem;color:#94a3b8;"></i>
                        @endif
                    </div>
                    @if(!$user->avatar)
                        <div style="background:#fffbeb;border:1px solid #fef3c7;color:#b45309;padding:8px 14px;border-radius:8px;font-size:.78rem;display:flex;align-items:center;gap:8px;max-width:360px;text-align:center;">
                            <i class="fa-solid fa-circle-info" style="font-size:1rem;"></i>
                            <span>Bado hujapakia picha ya profilu. Bonyeza kitufe hapa chini kukipakia.</span>
                        </div>
                    @endif
                    <button type="button" onclick="document.getElementById('profileAvatarModal').classList.add('open')" class="btn btn-outline btn-sm" style="font-size:.8rem;">
                        <i class="fa-solid fa-camera"></i> {{ $user->avatar ? 'Badili Picha' : 'Pakia Picha Yako' }}
                    </button>
                </div>

                <div style="text-align:center;margin:0 0 22px;"><strong style="font-size:1.12rem;color:var(--text);">{{ $user->name }}</strong><br><small style="color:var(--text-muted);">Taarifa binafsi huhifadhiwa kwa siri na huonekana kwa Katibu wa Kanisa pekee.</small><br><button type="button" class="btn btn-outline btn-sm" style="margin-top:10px;" onclick="document.getElementById('personalDetailsModal').classList.add('open')"><i class="fa-solid fa-pen"></i> Jaza / hariri taarifa binafsi</button></div>

                @if(!$user->isMshirika())
                {{-- Personal Info Grid --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1.5px solid var(--border);">
                        <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Jina Kamili</div>
                        <div style="font-size:1rem;font-weight:700;color:var(--text);">{{ $user->name }}</div>
                    </div>
                    <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1.5px solid var(--border);">
                        <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Member ID</div>
                        <div style="font-size:1rem;font-weight:700;color:var(--accent);font-family:monospace;">{{ $user->member_id ?? 'EAGT-KBP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1.5px solid var(--border);">
                        <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Barua Pepe</div>
                        <div style="font-size:.88rem;font-weight:600;color:var(--text);word-break:break-all;">{{ $user->email }}</div>
                    </div>
                    <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1.5px solid var(--border);">
                        <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Namba ya Simu</div>
                        <div style="font-size:1rem;font-weight:700;color:var(--accent-green);">{{ $user->phone ?? '—' }}</div>
                    </div>
                    <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1.5px solid var(--border);">
                        <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Aina ya Mtumiaji</div>
                        <div><span class="badge-pill badge-mshirika" style="font-size:.82rem;">Mshirika</span></div>
                    </div>
                    <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1.5px solid var(--border);">
                        <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Hali ya Akaunti</div>
                        <div><span class="badge-pill limit-ok" style="font-size:.78rem;">✓ Akaunti Hai</span></div>
                    </div>
                </div>

                @endif
                {{-- Departments --}}
                <div style="margin-bottom:20px;">
                    <div style="font-size:.82rem;font-weight:700;color:var(--text);margin-bottom:10px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-building-columns" style="color:var(--accent);"></i> Idara Zangu
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @forelse($departments as $dept)
                            <span class="badge-pill" style="background:#e0e7ff;color:#3730a3;font-size:.8rem;font-weight:700;padding:6px 14px;">
                                <i class="fa-solid fa-building-columns" style="margin-right:4px;"></i> {{ $dept->name_sw }}
                            </span>
                        @empty
                            <span style="font-size:.85rem;color:var(--text-muted);">Bado hujajiunga na idara yoyote.</span>
                        @endforelse
                    </div>
                </div>

                @if(!$user->isMshirika())
                {{-- Payment summary --}}
                <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #bbf7d0;border-radius:12px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:.7rem;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;">Jumla ya Malipo Yaliyoidhinishwa</div>
                        <div style="font-size:1.5rem;font-weight:900;color:#15803d;">Tsh {{ number_format($totalPaidAmount ?? 0) }}</div>
                    </div>
                    <i class="fa-solid fa-coins" style="font-size:2rem;color:#16a34a;opacity:.5;"></i>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- /content --}}
</div>{{-- /main --}}

{{-- Modal for Current User Details --}}
<div class="modal-overlay" id="userDetailsModal">
    <div class="modal-box" style="max-width:520px;">
        <button class="modal-close" onclick="document.getElementById('userDetailsModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-id-card" style="color:var(--accent)"></i> Taarifa za Mtumiaji</div>

        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
            <div style="width:82px;height:82px;border-radius:50%;overflow:hidden;border:3px solid var(--accent);background:linear-gradient(135deg,var(--accent),#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.8rem;font-weight:800;flex-shrink:0;">
                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    {{ strtoupper(substr($user->name,0,1)) }}
                @endif
            </div>
            <div>
                <div style="font-size:1.12rem;font-weight:800;color:var(--text);">{{ $user->name }}</div>
                @if(!$user->isMshirika())<div style="font-size:.84rem;color:var(--text-muted);word-break:break-all;">{{ $user->email }}</div>@endif
                @if(!$user->isMshirika())<span class="badge-pill {{ $user->isAdmin() || $user->isMchungaji() || $user->isKiongozi() ? 'badge-kiongozi' : 'badge-mshirika' }}" style="margin-top:8px;">
                    {{ $user->isAdmin() ? 'Super Admin' : ($user->isMchungaji() ? 'Mchungaji' : ($user->isKiongozi() ? 'Kiongozi' : 'Mshirika')) }}
                </span>@endif
            </div>
        </div>

        @if(!$user->isMshirika())
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px;">
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:12px;padding:12px;">
                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">Member ID</div>
                <div style="font-size:.9rem;font-weight:700;color:var(--accent);font-family:monospace;">{{ $user->member_id ?? 'EAGT-KBP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:12px;padding:12px;">
                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">Simu</div>
                <div style="font-size:.9rem;font-weight:700;color:var(--text);">{{ $user->phone ?? 'Haijajazwa' }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:12px;padding:12px;">
                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">Jinsia</div>
                <div style="font-size:.9rem;font-weight:700;color:var(--text);">{{ $user->gender ?? 'Haijajazwa' }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:12px;padding:12px;">
                <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">Tarehe ya Kuzaliwa</div>
                <div style="font-size:.9rem;font-weight:700;color:var(--text);">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d/m/Y') : 'Haijajazwa' }}</div>
            </div>
        </div>

        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:12px;padding:12px;margin-bottom:14px;">
            <div style="font-size:.68rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">Anwani</div>
            <div style="font-size:.9rem;font-weight:700;color:var(--text);">{{ $user->address ?? 'Haijajazwa' }}</div>
        </div>
        @endif

        <div style="margin-bottom:18px;">
            <div style="font-size:.72rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Idara</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @if($user->isAdmin() || $user->isMchungaji())
                    <span class="badge-pill" style="background:#e0e7ff;color:#3730a3;">
                        <i class="fa-solid fa-unlock-keyhole" style="margin-right:4px;"></i> Ana access ya idara zote
                    </span>
                @else
                    @forelse($user->departments as $dept)
                        <span class="badge-pill" style="background:#e0e7ff;color:#3730a3;">
                            <i class="fa-solid fa-building-columns" style="margin-right:4px;"></i> {{ $dept->name_sw ?? $dept->name }}
                        </span>
                    @empty
                        <span style="font-size:.85rem;color:var(--text-muted);">Bado hajajiunga na idara.</span>
                    @endforelse
                @endif
            </div>
        </div>

        <button type="button" onclick="document.getElementById('userDetailsModal').classList.remove('open');document.getElementById('profileAvatarModal').classList.add('open')" class="btn btn-outline" style="width:100%;justify-content:center;">
            <i class="fa-solid fa-camera"></i> {{ $user->avatar ? 'Badili Picha' : 'Pakia Picha' }}
        </button>
    </div>
</div>

{{-- Private profile completion form: details are visible to the church secretary, not on the member card. --}}
<div class="modal-overlay" id="personalDetailsModal">
    <div class="modal-box" style="max-width:760px;">
        <button class="modal-close" onclick="document.getElementById('personalDetailsModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-user-pen" style="color:var(--accent)"></i> Jaza Taarifa Zako Binafsi</div>
        <p style="color:var(--text-muted);font-size:.85rem;margin:-5px 0 18px;">Taarifa hizi zinahifadhiwa kwa siri na kusimamiwa na Katibu wa Kanisa.</p>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <input type="hidden" name="return_to_dashboard" value="1">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:13px;">
                <div class="form-group"><label class="form-label">Jina kamili</label><input class="form-control" name="name" value="{{ $user->name }}" required></div>
                <div class="form-group"><label class="form-label">Barua pepe</label><input class="form-control" type="email" name="email" value="{{ $user->email }}" required></div>
                <div class="form-group"><label class="form-label">Namba ya simu</label><input class="form-control" name="phone" value="{{ $user->phone }}" required></div>
                <div class="form-group"><label class="form-label">Jinsia</label><select class="form-control" name="gender" required><option value="">Chagua</option><option value="male" @selected($user->gender==='male')>Mwanaume</option><option value="female" @selected($user->gender==='female')>Mwanamke</option></select></div>
                <div class="form-group"><label class="form-label">Tarehe ya kuzaliwa</label><input class="form-control" type="date" name="dob" value="{{ $user->dob }}" required></div>
                <div class="form-group"><label class="form-label">Picha yako</label><input class="form-control" type="file" name="avatar" accept="image/*"></div>
                <div class="form-group"><label class="form-label">Jina la mtaa</label><input class="form-control" name="street_name" value="{{ $user->street_name }}"></div>
                <div class="form-group"><label class="form-label">Kata</label><input class="form-control" name="ward" value="{{ $user->ward }}"></div>
                <div class="form-group"><label class="form-label">Hali ya ndoa</label><select class="form-control" name="marital_status" id="marital-status" onchange="toggleMarriageFields()"><option value="">Chagua</option><option value="single" @selected($user->marital_status === 'single')>Sijaoa / Sijaolewa</option><option value="married" @selected($user->marital_status === 'married')>Nimeoa / Nimeolewa</option><option value="widowed" @selected($user->marital_status === 'widowed')>Mjane</option></select></div>
                <div class="form-group marriage-field"><label class="form-label">Majina ya mwenza</label><input class="form-control" name="spouse_names" value="{{ $user->spouse_names }}"></div>
                <div class="form-group marriage-field"><label class="form-label">Watoto (wangapi)</label><input class="form-control" type="number" name="children_count" min="0" max="50" value="{{ $user->children_count }}"></div>
                <div class="form-group"><label class="form-label">Tarehe ya kuokoka</label><input class="form-control" type="date" name="saved_date" value="{{ optional($user->saved_date)->format('Y-m-d') }}"></div>
                <div class="form-group"><label class="form-label">Tarehe ya kubatizwa</label><input class="form-control" type="date" name="baptism_date" value="{{ optional($user->baptism_date)->format('Y-m-d') }}"></div>
                <div class="form-group"><label class="form-label">Elimu</label><input class="form-control" name="education" value="{{ $user->education }}" placeholder="Mfano: Sekondari, Stashahada"></div>
                <div class="form-group"><label class="form-label">Huduma</label><input class="form-control" name="service" value="{{ $user->service }}" placeholder="Mfano: Uimbaji, Uinjilisti"></div>
                <div class="form-group" style="grid-column:1/-1;"><label class="form-label">Idara unazoshiriki</label><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:8px;">@foreach($allDepartments as $departmentOption)<label style="display:flex;align-items:center;gap:9px;padding:10px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;color:var(--text);font-size:.9rem;"><input type="checkbox" name="department_ids[]" value="{{ $departmentOption->id }}" @checked($user->departments->contains('id', $departmentOption->id)) style="width:16px;height:16px;accent-color:var(--accent);"> {{ $departmentOption->name_sw }}</label>@endforeach</div><small style="display:block;color:var(--text-muted);margin-top:5px;">Bonyeza idara unayotaka kuchagua au kuondoa.</small></div>
            </div>
            <button class="btn btn-primary" style="width:100%;margin-top:10px;"><i class="fa-solid fa-floppy-disk"></i> Hifadhi taarifa binafsi</button>
        </form>
    </div>
</div>

<style>
    /* Mfumo hutumia maandishi bila emoji au alama za picha. */
    .fa-solid, .fa-regular, .fa-brands, .nav-icon, .icon-box, .dept-chip-icon, .role-icon { display: none !important; }
</style>
<script>
function toggleMarriageFields() {
    const isMarried = document.getElementById('marital-status')?.value === 'married';
    document.querySelectorAll('.marriage-field').forEach((field) => {
        field.style.display = isMarried ? '' : 'none';
        field.querySelector('input').required = isMarried;
    });
}
document.addEventListener('DOMContentLoaded', toggleMarriageFields);
</script>

{{-- Modal for Quick Profile Avatar Upload --}}
<div class="modal-overlay" id="profileAvatarModal">
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close" onclick="document.getElementById('profileAvatarModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-camera" style="color:var(--accent)"></i> Pakia / Badili Picha ya Profilu</div>
        <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;margin-bottom:18px;">
                <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:3px solid var(--accent);background:#f1f5f9;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    @if($user->avatar)
                        <img id="quick-avatar-preview" src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <img id="quick-avatar-preview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none;">
                        <i class="fa-solid fa-user" id="quick-avatar-placeholder" style="font-size:2.5rem;color:#cbd5e1;"></i>
                    @endif
                </div>
                <input type="file" name="avatar" accept="image/*" class="form-control" required onchange="previewQuickAvatar(this)">
                <span style="font-size:.75rem;color:var(--text-muted);">PNG, JPG (max 2MB)</span>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%"><i class="fa-solid fa-floppy-disk"></i> Hifadhi Picha</button>
        </form>
    </div>
</div>

<script>
    function previewQuickAvatar(input) {
        const preview = document.getElementById('quick-avatar-preview');
        const placeholder = document.getElementById('quick-avatar-placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if(placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleContributionDetails(campaignId) {
        const panel = document.getElementById(`campaign-details-${campaignId}`);
        if (!panel) return;
        panel.style.display = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
    }

    function showDepartmentContributions(departmentId, departmentName) {
        const panel = document.getElementById('departmentContributionDetails');
        const title = document.getElementById('selectedContributionDepartment');
        if (!panel || !title) return;

        title.textContent = departmentName;
        let count = 0;
        document.querySelectorAll('[data-campaign-record]').forEach((record) => {
            const matches = String(record.dataset.departmentId) === String(departmentId);
            record.style.display = matches ? '' : 'none';
            if (matches) count++;
        });
        document.getElementById('selectedContributionCount').textContent = `Jumla ya michango: ${count}`;
        document.getElementById('noDepartmentContributions').style.display = count ? 'none' : 'block';
        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function filterRecordedPayments() {
        const input = document.getElementById('paymentMemberSearch');
        const term = (input?.value || '').trim().toLowerCase();
        document.querySelectorAll('.recorded-payment-row').forEach(row => {
            row.style.display = row.dataset.member.includes(term) ? '' : 'none';
        });
    }

    function toggleMemberDepartment(departmentId) {
        const panel = document.getElementById(`member-dept-details-${departmentId}`);
        if (!panel) return;
        panel.style.display = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
    }

    const occupiedLeadershipRoles = @json(($allDepartments ?? collect())->mapWithKeys(function ($dept) {
        return [
            $dept->id => $dept->users
                ->filter(fn($deptUser) => ($deptUser->pivot->role ?? null) === 'kiongozi' && filled($deptUser->pivot->cheo ?? null))
                ->pluck('pivot.cheo')
                ->values()
                ->all()
        ];
    }));

    function updateLeadershipRoleOptions(departmentSelectId, roleSelectId) {
        const departmentSelect = document.getElementById(departmentSelectId);
        const roleSelect = document.getElementById(roleSelectId);
        if (!departmentSelect || !roleSelect) return;

        const occupied = occupiedLeadershipRoles[departmentSelect.value] || [];
        Array.from(roleSelect.options).forEach(option => {
            if (!option.value) return;
            const taken = occupied.includes(option.value);
            option.disabled = taken;
            option.textContent = taken ? `${option.value} - tayari kimechukuliwa` : option.value;
        });

        if (roleSelect.selectedOptions[0]?.disabled) {
            roleSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateLeadershipRoleOptions('add_position_department', 'add_position_cheo');
        updateLeadershipRoleOptions('onb_leader_department', 'onb_leader_cheo');
    });
</script>

{{-- Modal for Create Department Contribution --}}
@if($user->isAdmin() || $user->isMchungaji() || $user->isKiongozi())
<div class="modal-overlay" id="addContributionModal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('addContributionModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-bullseye" style="color:var(--accent)"></i> Anzisha Mchango wa Idara</div>
        <form action="{{ route('contributions.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Idara</label>
                @if($user->isAdmin() || $user->isMchungaji())
                    <select name="department_id" class="form-control" required>
                        @foreach($allDepartments as $dept)
                            <option value="{{ $dept->id }}" {{ isset($department) && $department?->id === $dept->id ? 'selected' : '' }}>{{ $dept->name_sw }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">
                    <input type="text" class="form-control" value="{{ $department->name_sw ?? 'Idara haijachaguliwa' }}" disabled>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label">Aina / Jina la Mchango</label>
                <input type="text" name="title" class="form-control" required placeholder="mfano: Mchango wa sare za kwaya">
            </div>
            <div class="form-group">
                <label class="form-label">Kiasi cha Kila Mwanachama (Tsh)</label>
                <input type="number" name="amount_per_member" class="form-control" min="100" step="100" required placeholder="mfano: 20000">
            </div>
            <div class="form-group">
                <label class="form-label">Mwisho wa Kukusanya</label>
                <input type="date" name="due_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Maelezo (si lazima)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Maelezo mafupi ya mchango huu"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%"><i class="fa-solid fa-floppy-disk"></i> Hifadhi Mchango</button>
        </form>
    </div>
</div>
@endif

{{-- Modal for Record Payment --}}
@if($user->isAdmin() || $user->isMchungaji() || $user->isKiongozi())
<div class="modal-overlay" id="addPaymentModal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('addPaymentModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-money-bill-wave" style="color:var(--accent-green)"></i> Jaza Taarifa za Aliyetoa Mchango</div>
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            @php $paymentCampaignOptions = ($contributionCampaigns ?? collect()); @endphp
            <div class="form-group">
                <label class="form-label">Mchango wa Idara</label>
                <select name="contribution_campaign_id" class="form-control" required>
                    <option value="">Chagua mchango...</option>
                    @foreach($paymentCampaignOptions as $campaign)
                        <option value="{{ $campaign->id }}">{{ $campaign->title }} - {{ $campaign->department->name_sw ?? 'Idara' }} (Tsh {{ number_format($campaign->amount_per_member, 0) }})</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="type" value="Mchango wa Idara">
            @if(isset($members) && $members->count() > 0)
                <div class="form-group">
                    <label class="form-label">Mshirika Aliyetoa</label>
                    <select name="user_id" class="form-control" required>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} - {{ $member->email }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group">
                <label class="form-label">Kiasi (Tsh)</label>
                <input type="number" name="amount" class="form-control" min="100" step="100" required placeholder="mfano: 50000">
            </div>
            <div class="form-group">
                <label class="form-label">Njia ya Malipo</label>
                <select name="payment_method" class="form-control" required>
                    <option value="Cash">Cash (Pesa Mkononi)</option>
                    <option value="Bank">Benki</option>
                    <option value="Simu">Simu kwa Kiongozi</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kumbukumbu ya Malipo (Kama ipo)</label>
                <input type="text" name="transaction_id" class="form-control" placeholder="mfano: risiti, kumbukumbu ya simu au benki">
            </div>
            <div class="form-group">
                <label class="form-label">Tarehe ya Malipo</label>
                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Maelezo (si lazima)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Mfano: amelipa nusu ya mchango"></textarea>
            </div>
            <button type="submit" class="btn btn-success" style="width:100%"><i class="fa-solid fa-receipt"></i> Hifadhi &amp; Tengeneza Risiti</button>
        </form>
    </div>
</div>
@endif

{{-- Modal for Official Letter Request --}}
<div class="modal-overlay" id="requestLetterModal">
    <div class="modal-box" style="max-width:550px;">
        <button class="modal-close" onclick="document.getElementById('requestLetterModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-envelope-open-text" style="color:var(--accent)"></i> Ombi la Barua Rasmi ya Kanisa</div>
        <form action="{{ route('official_letters.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Aina ya Barua</label>
                <select name="letter_type" class="form-control" required>
                    <option value="Barua ya Utambulisho">Barua ya Utambulisho</option>
                    <option value="Barua ya Uthibitisho">Barua ya Uthibitisho</option>
                    <option value="Barua ya Uanachama">Barua ya Uanachama</option>
                    <option value="Barua ya Mapendekezo">Barua ya Mapendekezo</option>
                    <option value="Barua ya Mwaliko">Barua ya Mwaliko</option>
                    <option value="Barua ya Shukrani">Barua ya Shukrani</option>
                    <option value="Barua ya Maombi">Barua ya Maombi</option>
                    <option value="Barua ya Kuhudhuria Kanisa">Barua ya Kuhudhuria Kanisa</option>
                    <option value="Barua Nyingine Rasmi">Barua Nyingine Rasmi</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Inaenda Kwa (Mpokeaji / Taasisi na Anwani Yake)</label>
                <textarea name="recipient" class="form-control" rows="3" required placeholder="Mfano:&#10;Mkurugenzi, Benki X&#10;S.L.P 12345&#10;Dar es Salaam"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Kichwa cha Barua (Subject)</label>
                <input type="text" name="subject" class="form-control" required placeholder="mfano: UTAMBULISHO WA MWANACHAMA WETU">
            </div>
            <div class="form-group">
                <label class="form-label">Maelezo ya Barua / Sababu ya Ombi</label>
                <textarea name="body" class="form-control" rows="5" required placeholder="Andika maelezo ya kina au utambulisho unaotaka uwemo kwenye barua..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%"><i class="fa-solid fa-paper-plane"></i> Wasilisha Ombi kwa Mchungaji</button>
        </form>
    </div>
</div>

{{-- Modal for Viewing Letter Details --}}
<div class="modal-overlay" id="viewLetterModal">
    <div class="modal-box" style="max-width:650px;">
        <button class="modal-close" onclick="document.getElementById('viewLetterModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title" style="display:flex;align-items:center;justify-content:space-between;padding-right:30px;">
            <span><i class="fa-solid fa-envelope-open-text" style="color:var(--accent)"></i> Taarifa za Barua Rasmi</span>
            <span id="vlm_status_badge"></span>
        </div>

        <div style="background:#f8fafc;border:1.5px solid var(--border);border-radius:12px;padding:16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <div style="font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;">Kumb. (Ref No.)</div>
                <div id="vlm_ref" style="font-weight:800;color:var(--accent);font-family:monospace;font-size:1rem;margin-top:2px;"></div>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;">Aina ya Barua</div>
                <div id="vlm_type" style="font-weight:700;color:var(--text);margin-top:2px;"></div>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;">Mwombaji / Mtumaji</div>
                <div id="vlm_sender" style="font-weight:600;color:var(--text);margin-top:2px;"></div>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;">Tarehe ya Ombi</div>
                <div id="vlm_date" style="font-weight:600;color:var(--text-muted);margin-top:2px;"></div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div style="font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Inaenda Kwa (Recipient)</div>
            <div id="vlm_recipient" style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:.88rem;white-space:pre-line;color:var(--text);"></div>
        </div>

        <div style="margin-bottom:14px;">
            <div style="font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Kichwa cha Barua (Subject)</div>
            <div id="vlm_subject" style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-weight:700;color:var(--text);font-size:.95rem;"></div>
        </div>

        <div style="margin-bottom:18px;">
            <div style="font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Maudhui / Maelezo ya Barua</div>
            <div id="vlm_body" style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:14px;font-size:.9rem;line-height:1.6;white-space:pre-line;max-height:220px;overflow-y:auto;color:#334155;"></div>
        </div>

        <div id="vlm_rejection_container" style="display:none;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px;margin-bottom:16px;">
            <div style="font-size:.78rem;font-weight:800;color:#991b1b;margin-bottom:2px;"><i class="fa-solid fa-circle-exclamation"></i> Sababu ya Kukataliwa:</div>
            <div id="vlm_rejection_reason" style="font-size:.85rem;color:#7f1d1d;"></div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid var(--border);padding-top:14px;">
            <a id="vlm_view_pdf_btn" href="#" target="_blank" class="btn btn-primary" style="display:none;background:#4f46e5;border-color:#4f46e5;color:#fff;">
                <i class="fa-solid fa-file-pdf"></i> Tazama PDF (Inline)
            </a>
            <a id="vlm_download_pdf_btn" href="#" target="_blank" class="btn btn-outline" style="display:none;">
                <i class="fa-solid fa-download" style="color:#ef4444;"></i> Pakua PDF
            </a>
            <button type="button" class="btn btn-outline" onclick="document.getElementById('viewLetterModal').classList.remove('open')">
                Funga
            </button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════ --}}
{{-- MODALS                              --}}
{{-- ════════════════════════════════════ --}}

{{-- Add Member Modal (Kiongozi) --}}
@if($user->isKiongozi())
<div class="modal-overlay" id="addMemberModal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('addMemberModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-user-plus" style="color:var(--accent)"></i> Ongeza Mshirika kwenye Idara</div>
        <form action="{{ route('members.add') }}" method="POST">
            @csrf
            <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">
            <div class="form-group">
                <label class="form-label">Email ya Mshirika</label>
                <input type="email" name="email" class="form-control" required placeholder="mfano@barua.com">
            </div>
            <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px;">Mshirika lazima awe na akaunti kwenye mfumo huu. Atapewa taarifa za idara yako.</p>
            <button type="submit" class="btn btn-primary" style="width:100%"><i class="fa-solid fa-check"></i> Ongeza Mshirika</button>
        </form>
    </div>
</div>

{{-- Add Leadership Position Modal (Kiongozi) --}}
<div class="modal-overlay" id="addPositionModal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('addPositionModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-crown" style="color:var(--accent-amber)"></i> Ongeza Nafasi ya Uongozi</div>
        @php $leaderJoinedIds = $user->departments->pluck('id')->toArray(); $leaderRemaining = 4 - count($leaderJoinedIds); @endphp
        <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:16px;">Unabaki na nafasi <strong style="color:var(--accent-amber)">{{ $leaderRemaining }}</strong> za uongozi.</p>
        <form action="{{ route('onboarding.complete') }}" method="POST">
            @csrf
            <input type="hidden" name="role" value="kiongozi">
            <div class="form-group">
                <label class="form-label">Chagua Idara</label>
                <select name="department_id" id="add_position_department" class="form-control" required onchange="updateLeadershipRoleOptions('add_position_department', 'add_position_cheo')">
                    @foreach($allDepartments as $dept)
                        @if(!in_array($dept->id, $leaderJoinedIds))
                            <option value="{{ $dept->id }}">{{ $dept->name_sw }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Cheo cha Uongozi</label>
                <select name="cheo" id="add_position_cheo" class="form-control" required>
                    <option value="">-- Chagua Cheo --</option>
                    <option value="Mwenyekiti">Mwenyekiti</option>
                    <option value="Makamu Mwenyekiti">Makamu Mwenyekiti</option>
                    <option value="Katibu">Katibu</option>
                    <option value="Katibu Msaidizi">Katibu Msaidizi</option>
                    <option value="Mhasibu">Mhasibu</option>
                </select>
            </div>
            <button type="submit" class="btn btn-amber" style="width:100%"><i class="fa-solid fa-floppy-disk"></i> Hifadhi Nafasi</button>
        </form>
    </div>
</div>
@endif

{{-- Register Leader Modal (Kiongozi Mwenyekiti / Mchungaji / Admin) --}}
@if($user->isAdmin() || $user->isMchungaji() || $user->isMwenyekiti($department ?? null))
<div class="modal-overlay" id="registerLeaderModal">
    <div class="modal-box" style="max-width:540px;">
        <button class="modal-close" onclick="document.getElementById('registerLeaderModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title" style="display:flex;align-items:center;gap:10px;">
            <span style="width:40px;height:40px;border-radius:10px;background:#eef2ff;color:#4f46e5;display:inline-flex;align-items:center;justify-content:center;font-size:1.2rem;">
                <i class="fa-solid fa-user-shield"></i>
            </span>
            <div>
                <div style="font-size:1.15rem;font-weight:800;color:var(--text);">Sajili Kiongozi wa Idara</div>
                <div style="font-size:.78rem;font-weight:500;color:var(--text-muted);">Mwenyekiti husajili viongozi na kuwapa nenosiri lao</div>
            </div>
        </div>

        <form action="{{ route('department-leaders.store') }}" method="POST" style="margin-top:16px;">
            @csrf
            
            @if($user->isAdmin() || $user->isMchungaji())
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Chagua Idara <span style="color:#ef4444;">*</span></label>
                    <select name="department_id" class="form-control" required>
                        @foreach($allDepartments as $dept)
                            <option value="{{ $dept->id }}" {{ isset($department) && $department->id === $dept->id ? 'selected' : '' }}>{{ $dept->name_sw }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="department_id" value="{{ $department->id ?? '' }}">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px 14px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:.85rem;color:var(--text-muted);">Idara Inayohusika:</span>
                    <strong style="color:var(--accent);font-size:.9rem;">{{ $department->name_sw ?? 'Idara Yako' }}</strong>
                </div>
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group" style="grid-column:span 2;margin-bottom:12px;">
                    <label class="form-label">Jina Kamili la Kiongozi <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="mfano: Emmanuel Yohana" value="{{ old('name') }}">
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Barua Pepe (Email) <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="email" class="form-control" required placeholder="mfano@kanisa.com" value="{{ old('email') }}">
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Namba ya Simu <span style="color:#ef4444;">*</span></label>
                    <input type="tel" name="phone" class="form-control" required placeholder="07xxxxxxxx" value="{{ old('phone') }}">
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Cheo cha Uongozi <span style="color:#ef4444;">*</span></label>
                    <select name="cheo" class="form-control" required>
                        <option value="">-- Chagua Cheo --</option>
                        @if($user->isAdmin() || $user->isMchungaji())
                            <option value="Mwenyekiti">Mwenyekiti</option>
                        @endif
                        <option value="Makamu Mwenyekiti">Makamu Mwenyekiti</option>
                        <option value="Katibu">Katibu</option>
                        <option value="Katibu Msaidizi">Katibu Msaidizi</option>
                        <option value="Mhasibu">Mhasibu</option>
                        <option value="Mjumbe wa Kamati">Mjumbe wa Kamati</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Jinsia</label>
                    <select name="gender" class="form-control">
                        <option value="">-- Chagua Jinsia --</option>
                        <option value="male">Mwanaume</option>
                        <option value="female">Mwanamke</option>
                    </select>
                </div>
            </div>

            <div style="background:#fdf4ff;border:1px dashed #d946ef;border-radius:12px;padding:14px;margin:14px 0;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <label class="form-label" style="margin:0;font-weight:800;color:#86198f;">
                        <i class="fa-solid fa-key" style="margin-right:4px;"></i> Weka Password ya Kiongozi <span style="color:#ef4444;">*</span>
                    </label>
                    <button type="button" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:3px 8px;" onclick="generateLeaderPassword('reg_lead_pwd', 'reg_lead_pwd_confirm')">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Tengeneza Password
                    </button>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="position:relative;">
                        <input type="password" name="password" id="reg_lead_pwd" class="form-control" required minlength="6" placeholder="Nenosiri (angalau herufi 6)">
                        <button type="button" onclick="togglePasswordVisibility('reg_lead_pwd')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;">
                            <i class="fa-solid fa-eye" id="reg_lead_pwd_eye"></i>
                        </button>
                    </div>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="reg_lead_pwd_confirm" class="form-control" required minlength="6" placeholder="Thibitisha Nenosiri">
                        <button type="button" onclick="togglePasswordVisibility('reg_lead_pwd_confirm')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;">
                            <i class="fa-solid fa-eye" id="reg_lead_pwd_confirm_eye"></i>
                        </button>
                    </div>
                </div>
                <div style="font-size:.75rem;color:#701a75;margin-top:6px;">
                    <i class="fa-solid fa-circle-info"></i> Utapokea taarifa zote pamoja na nenosiri hili ili umtumie kiongozi mara tu baada ya kuhifadhi.
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="button" class="btn btn-secondary" style="flex:1;" onclick="document.getElementById('registerLeaderModal').classList.remove('open')">Ghairi</button>
                <button type="submit" class="btn btn-primary" style="flex:2;background:linear-gradient(135deg,#1e3a8a,#3b82f6);border:none;box-shadow:0 4px 14px rgba(30,58,138,.3);">
                    <i class="fa-solid fa-check"></i> Sajili Kiongozi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Change Leader Password Modal --}}
<div class="modal-overlay" id="changeLeaderPasswordModal">
    <div class="modal-box" style="max-width:480px;">
        <button class="modal-close" onclick="document.getElementById('changeLeaderPasswordModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title" style="display:flex;align-items:center;gap:10px;">
            <span style="width:40px;height:40px;border-radius:10px;background:#fef3c7;color:#b45309;display:inline-flex;align-items:center;justify-content:center;font-size:1.2rem;">
                <i class="fa-solid fa-key"></i>
            </span>
            <div>
                <div style="font-size:1.15rem;font-weight:800;color:var(--text);">Weka Nenosiri Jipya</div>
                <div style="font-size:.78rem;font-weight:500;color:var(--text-muted);" id="clp_leader_info">Kiongozi: —</div>
            </div>
        </div>

        <form id="changeLeaderPasswordForm" method="POST" action="" style="margin-top:16px;">
            @csrf
            <input type="hidden" name="department_id" id="clp_department_id" value="{{ $department->id ?? '' }}">

            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;margin-bottom:14px;">
                <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:4px;">Kiongozi anayebadilishiwa:</div>
                <div style="font-weight:800;color:var(--text);font-size:.95rem;" id="clp_leader_name">—</div>
                <div style="font-size:.78rem;color:var(--accent);font-weight:700;" id="clp_leader_cheo">—</div>
                <div style="font-size:.78rem;color:var(--text-muted);" id="clp_leader_email">—</div>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <label class="form-label" style="margin:0;">Nenosiri Jipya <span style="color:#ef4444;">*</span></label>
                    <button type="button" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:3px 8px;" onclick="generateLeaderPassword('clp_new_pwd', 'clp_new_pwd_confirm')">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Tengeneza Password
                    </button>
                </div>
                <div style="position:relative;">
                    <input type="password" name="password" id="clp_new_pwd" class="form-control" required minlength="6" placeholder="Nenosiri jipya (angalau herufi 6)">
                    <button type="button" onclick="togglePasswordVisibility('clp_new_pwd')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;">
                        <i class="fa-solid fa-eye" id="clp_new_pwd_eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Thibitisha Nenosiri Jipya <span style="color:#ef4444;">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="clp_new_pwd_confirm" class="form-control" required minlength="6" placeholder="Rudia nenosiri jipya">
                    <button type="button" onclick="togglePasswordVisibility('clp_new_pwd_confirm')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;">
                        <i class="fa-solid fa-eye" id="clp_new_pwd_confirm_eye"></i>
                    </button>
                </div>
            </div>

            <p style="font-size:.78rem;color:var(--text-muted);margin-bottom:16px;line-height:1.4;">
                <i class="fa-solid fa-circle-exclamation" style="color:#f59e0b;"></i> Mara baada ya kubadili, utapewa taarifa hizi za siri ili umpatie kiongozi.
            </p>

            <div style="display:flex;gap:10px;">
                <button type="button" class="btn btn-secondary" style="flex:1;" onclick="document.getElementById('changeLeaderPasswordModal').classList.remove('open')">Ghairi</button>
                <button type="submit" class="btn btn-primary" style="flex:2;background:#b45309;border-color:#b45309;">
                    <i class="fa-solid fa-floppy-disk"></i> Hifadhi Nenosiri
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Credentials Success Modal (Displays credentials to copy/send) --}}
@php
    $leaderCreds = session('registered_leader_credentials') ?? session('updated_leader_credentials');
    $isNewRegistration = session()->has('registered_leader_credentials');
@endphp
@if($leaderCreds)
<div class="modal-overlay open" id="leaderCredentialsModal">
    <div class="modal-box" style="max-width:500px;text-align:center;">
        <div style="width:60px;height:60px;border-radius:50%;background:#ecfdf5;color:#059669;display:inline-flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 12px;box-shadow:0 6px 18px rgba(5,150,105,.2);">
            <i class="fa-solid fa-check"></i>
        </div>
        <h3 style="font-size:1.25rem;font-weight:800;color:var(--text);margin-bottom:6px;">
            {{ $isNewRegistration ? 'Kiongozi Amesajiliwa Kikamilifu!' : 'Nenosiri Limewekwa Kikamilifu!' }}
        </h3>
        <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:16px;">
            Mpe kiongozi taarifa zifuatazo ili aweze kuingia kwenye mfumo wa kanisa:
        </p>

        <div id="credentialsCopyBlock" style="background:#f8fafc;border:2px dashed #cbd5e1;border-radius:14px;padding:16px;text-align:left;margin-bottom:18px;">
            <div style="display:flex;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding-bottom:8px;margin-bottom:8px;">
                <span style="font-size:.8rem;color:var(--text-muted);">Jina:</span>
                <strong style="color:var(--text);font-size:.88rem;">{{ $leaderCreds['name'] ?? '' }}</strong>
            </div>
            @if(!empty($leaderCreds['cheo']))
            <div style="display:flex;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding-bottom:8px;margin-bottom:8px;">
                <span style="font-size:.8rem;color:var(--text-muted);">Cheo:</span>
                <span class="badge-pill badge-kiongozi" style="font-size:.78rem;background:#fef3c7;color:#92400e;">{{ $leaderCreds['cheo'] }}</span>
            </div>
            @endif
            @if(!empty($leaderCreds['department']))
            <div style="display:flex;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding-bottom:8px;margin-bottom:8px;">
                <span style="font-size:.8rem;color:var(--text-muted);">Idara:</span>
                <strong style="color:var(--accent);font-size:.85rem;">{{ $leaderCreds['department'] }}</strong>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding-bottom:8px;margin-bottom:8px;">
                <span style="font-size:.8rem;color:var(--text-muted);">Barua Pepe (Email):</span>
                <strong style="color:var(--text);font-size:.85rem;font-family:monospace;">{{ $leaderCreds['email'] ?? '' }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding-top:2px;">
                <span style="font-size:.8rem;color:var(--text-muted);">Nenosiri (Password):</span>
                <span style="background:#fef08a;color:#854d0e;padding:4px 10px;border-radius:6px;font-family:monospace;font-size:1rem;font-weight:800;letter-spacing:1px;">
                    {{ $leaderCreds['password'] ?? '' }}
                </span>
            </div>
        </div>

        @php
            $waMessage = "Habari " . ($leaderCreds['name'] ?? '') . ",\n\n"
                . "Umesajiliwa kama kiongozi (" . ($leaderCreds['cheo'] ?? 'Kiongozi') . ") wa " . ($leaderCreds['department'] ?? 'Idara') . " kwenye mfumo wa Philadelphia Church.\n\n"
                . "Taarifa zako za kuingilia (Login Credentials):\n"
                . "• Barua Pepe: " . ($leaderCreds['email'] ?? '') . "\n"
                . "• Nenosiri: " . ($leaderCreds['password'] ?? '') . "\n"
                . "• Kiungo cha Mfumo: " . url('/login') . "\n\n"
                . "Tafadhali ingia na utumie mfumo kikamilifu. Barikiwa!";
            $waUrl = "https://wa.me/" . preg_replace('/\D+/', '', $leaderCreds['phone'] ?? '') . "?text=" . rawurlencode($waMessage);
        @endphp

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
            <button type="button" class="btn btn-outline" onclick="copyCredentialsText()" id="copyCredBtn" style="display:flex;align-items:center;justify-content:center;gap:6px;">
                <i class="fa-solid fa-copy"></i> <span id="copyCredBtnText">Nakili Taarifa</span>
            </button>
            <a href="{{ $waUrl }}" target="_blank" class="btn btn-green" style="display:flex;align-items:center;justify-content:center;gap:6px;background:#25d366;border-color:#25d366;color:#fff;text-decoration:none;">
                <i class="fa-brands fa-whatsapp"></i> Tuma WhatsApp
            </a>
        </div>

        <button type="button" class="btn btn-secondary" style="width:100%;" onclick="document.getElementById('leaderCredentialsModal').classList.remove('open')">
            Nimemaliza / Funga
        </button>
    </div>
</div>
@endif

{{-- Add Department Modal (Mshirika) --}}
@if($user->isMshirika())
<div class="modal-overlay" id="addDeptModal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('addDeptModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title"><i class="fa-solid fa-building-columns" style="color:var(--accent)"></i> Jiunge na Idara Nyingine</div>
        @php $joinedIds = $user->departments->pluck('id')->toArray(); $remaining = 5 - count($joinedIds); @endphp
        <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:12px;">Unabaki na nafasi <strong style="color:var(--accent)">{{ $remaining }}</strong> za idara.</p>
        <form action="{{ route('onboarding.complete') }}" method="POST">
            @csrf
            <input type="hidden" name="role" value="mshirika">

            {{-- Horizontal scrollable chip cards --}}
            <div class="dept-scroll-track" id="add-dept-track" style="margin-bottom:14px;">
                @foreach($allDepartments as $dept)
                    @if(!in_array($dept->id, $joinedIds))
                        @php
                            $icon2 = 'fa-building-columns'; $code2 = '';
                            foreach(['Wamama'=>['fa-female','WWI'],'Wababa'=>['fa-male',''],'Vijana'=>['fa-users','CGM'],'Watoto'=>['fa-child','CWI'],'Praise'=>['fa-music','KUSKI']] as $kw2=>[$ic2,$cd2]) {
                                if(str_contains($dept->name_sw, $kw2)) { $icon2 = $ic2; $code2 = $cd2; break; }
                            }
                        @endphp
                        <label class="dept-chip-label">
                            <div class="dept-chip-check"><i class="fa-solid fa-check"></i></div>
                            <div class="dept-chip-icon"><i class="fa-solid {{ $icon2 }}"></i></div>
                            <input type="checkbox" name="department_ids[]" value="{{ $dept->id }}" class="add-dept-check" style="display:none;" onchange="handleChipChange(this, {{ $remaining }}, 'add-dept-check')">
                            <div class="dept-chip-name">{{ Str::limit($dept->name_sw, 22) }}</div>
                            @if($code2) <div class="dept-chip-code">{{ $code2 }}</div> @endif
                        </label>
                    @endif
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%"><i class="fa-solid fa-check"></i> Hifadhi</button>
        </form>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════════ --}}
{{-- WELCOME / FIRST-TIME ONBOARDING MODAL              --}}
{{-- ════════════════════════════════════════════════════ --}}
@if(in_array($user->role, ['mshirika', 'kiongozi'], true) && $user->departments->isEmpty() && is_null($user->onboarding_completed_at))
<div class="modal-overlay open" id="onboardingModal" style="z-index:300;">
    <div class="modal-box" style="max-width:500px;">
        <div style="text-align:center;margin-bottom:22px;">
            <img src="{{ asset('images/eagt_logo.png') }}" alt="EAGT Logo" style="height:75px;width:auto;margin-bottom:8px;object-fit:contain;">
            <h3 style="font-size:1.4rem;font-weight:800;margin-top:6px;">Karibu Philadelphia Church!</h3>
            <p style="font-size:.88rem;color:var(--text-muted);margin-top:6px;">Akaunti ya <strong>{{ $user->name }}</strong> iko tayari. Kamilisha uchaguzi wa Idara.</p>
        </div>

        <form action="{{ route('onboarding.complete') }}" method="POST" id="onboardingForm" enctype="multipart/form-data" onsubmit="return validateOnboardingSubmit()">
            @csrf
            
            <!-- STEP 1: Role Selection -->
            <div id="onb_step_role">
                <label class="form-label" style="font-size:1.1rem;text-align:center;display:block;margin-bottom:10px;">Chagua nafasi yako</label>
                <select name="role" id="onb_role" class="form-control" required onchange="selectOnboardingRole(this.value)" style="display:none;">
                    <option value="">-- Chagua Nafasi --</option>
                    <option value="mshirika">Mshirika</option>
                    <option value="kiongozi">Kiongozi</option>
                </select>

                <div class="onb-role-grid">
                    <button type="button" class="onb-role-card" data-role-card="mshirika" onclick="selectOnboardingRole('mshirika')">
                        <div class="role-icon"><i class="fa-solid fa-user"></i></div>
                        <h4>Mshirika wa Kanisa</h4>
                        <p>Jiunge na idara unazoshiriki, hadi idara 5.</p>
                    </button>
                    @if($user->isAdmin() || $user->isMchungaji() || $user->role === 'kiongozi')
                    <button type="button" class="onb-role-card" data-role-card="kiongozi" onclick="selectOnboardingRole('kiongozi')">
                        <div class="role-icon"><i class="fa-solid fa-user-tie"></i></div>
                        <h4>Kiongozi wa Idara</h4>
                        <p>Chagua idara unayoiongoza na cheo chako.</p>
                    </button>
                    @else
                    <div class="onb-role-card" style="opacity:.65;cursor:not-allowed;background:#f8fafc;" title="Viongozi wa idara husajiliwa na kupewa password na Mwenyekiti">
                        <div class="role-icon" style="background:#f1f5f9;color:#64748b;"><i class="fa-solid fa-lock"></i></div>
                        <h4 style="color:#64748b;">Kiongozi wa Idara</h4>
                        <p style="font-size:.76rem;color:#94a3b8;">Husajiliwa moja kwa moja na Kiongozi Mwenyekiti wa Idara.</p>
                    </div>
                    @endif
                </div>

                <button type="button" id="onb_role_next" onclick="goToOnboardingDepartments()" class="btn btn-primary" style="width:100%;margin-top:6px;padding:13px;font-size:.95rem;display:flex;align-items:center;justify-content:center;gap:8px;opacity:.55;" disabled>
                    ENDELEA <i class="fa-solid fa-check"></i>
                </button>
            </div>

            <!-- STEP 2: Department Selection -->
            <div id="onb_step_1" style="display:none;">
                {{-- Mshirika: horizontal scrollable dept chips --}}
                <div id="onb_member_section">
                    <label class="form-label" style="font-size:1.1rem;text-align:center;display:block;margin-bottom:10px;">Chagua Idara yako (hadi <span style="color:var(--accent)">5</span>)</label>

                    <style>
                        .dept-scroll-track {
                            display: flex;
                            flex-direction: row;
                            gap: 15px;
                            overflow-x: auto;
                            padding: 15px 10%;
                            scroll-snap-type: x mandatory;
                            -webkit-overflow-scrolling: touch;
                            scrollbar-width: thin;
                            scrollbar-color: var(--accent) #f1f5f9;
                        }
                        .dept-scroll-track::-webkit-scrollbar { height: 6px; }
                        .dept-scroll-track::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
                        .dept-scroll-track::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }

                        .dept-chip-label {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            gap: 10px;
                            min-width: 80%;
                            max-width: 80%;
                            padding: 24px 15px;
                            border: 2px solid var(--border);
                            border-radius: 16px;
                            background: #fff;
                            cursor: pointer;
                            scroll-snap-align: center;
                            transition: border-color .2s, background .2s, transform .15s, box-shadow .2s;
                            text-align: center;
                            position: relative;
                            flex-shrink: 0;
                            user-select: none;
                        }
                        .dept-chip-label:hover {
                            border-color: var(--accent);
                            background: #f5f3ff;
                            transform: translateY(-3px);
                            box-shadow: 0 6px 20px rgba(99,102,241,.15);
                        }
                        .dept-chip-label.selected {
                            border-color: var(--accent);
                            background: linear-gradient(135deg, #ede9fe, #f5f3ff);
                            box-shadow: 0 6px 20px rgba(99,102,241,.25);
                        }
                        .dept-chip-label.selected .dept-chip-icon {
                            background: var(--accent);
                            color: #fff;
                            transform: scale(1.1);
                        }
                        .dept-chip-label .dept-chip-icon {
                            width: 54px;
                            height: 54px;
                            border-radius: 50%;
                            background: #f1f5f9;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.4rem;
                            color: var(--accent);
                            transition: all .3s ease;
                            margin-bottom: 5px;
                        }
                        .dept-chip-label .dept-chip-check {
                            position: absolute;
                            top: 12px; right: 15px;
                            width: 24px; height: 24px;
                            border-radius: 50%;
                            background: var(--accent);
                            color: #fff;
                            font-size: .8rem;
                            display: none;
                            align-items: center;
                            justify-content: center;
                            font-weight: 800;
                            box-shadow: 0 2px 8px rgba(99,102,241,.4);
                        }
                        .dept-chip-label.selected .dept-chip-check { display: flex; }
                        .dept-chip-label .dept-chip-name {
                            font-size: 1rem;
                            font-weight: 800;
                            color: var(--text);
                            line-height: 1.3;
                        }
                        .dept-chip-label .dept-chip-code {
                            font-size: .8rem;
                            color: var(--accent);
                            font-weight: 800;
                            letter-spacing: 1px;
                        }
                        .onb-role-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 14px;
                            margin: 18px 0 20px;
                        }
                        .onb-role-card {
                            border: 2px solid var(--border);
                            border-radius: 16px;
                            padding: 24px 14px;
                            background: #fff;
                            cursor: pointer;
                            text-align: center;
                            transition: border-color .2s, background .2s, transform .15s, box-shadow .2s;
                        }
                        .onb-role-card:hover,
                        .onb-role-card.selected {
                            border-color: var(--accent);
                            background: linear-gradient(135deg, #ede9fe, #f8fafc);
                            box-shadow: 0 8px 24px rgba(99,102,241,.18);
                            transform: translateY(-2px);
                        }
                        .onb-role-card .role-icon {
                            width: 54px;
                            height: 54px;
                            margin: 0 auto 12px;
                            border-radius: 50%;
                            background: #eef2ff;
                            color: var(--accent);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.35rem;
                        }
                        .onb-role-card.selected .role-icon {
                            background: var(--accent);
                            color: #fff;
                        }
                        .onb-role-card h4 {
                            font-size: 1rem;
                            font-weight: 800;
                            color: var(--text);
                            margin-bottom: 4px;
                        }
                        .onb-role-card p {
                            font-size: .78rem;
                            color: var(--text-muted);
                            line-height: 1.35;
                        }
                        @media (max-width:640px) {
                            .onb-role-grid { grid-template-columns: 1fr; }
                        }
                    </style>

                    @php
                        $deptIcons = [
                            'Wamama' => ['fa-female', 'WWI'],
                            'Wababa' => ['fa-male', '—'],
                            'Vijana' => ['fa-users', 'CGM'],
                            'Watoto' => ['fa-child', 'CWI'],
                            'Praise' => ['fa-music', 'KUSKI'],
                        ];
                    @endphp

                    <div class="dept-scroll-track" id="onb-dept-track">
                        @foreach($allDepartments as $dept)
                            @php
                                $icon = 'fa-building-columns'; $code = '';
                                foreach($deptIcons as $kw => [$ic, $cd]) {
                                    if(str_contains($dept->name_sw, $kw)) { $icon = $ic; $code = $cd; break; }
                                }
                            @endphp
                            <label class="dept-chip-label" id="chip-onb-{{ $dept->id }}">
                                <div class="dept-chip-check"><i class="fa-solid fa-check"></i></div>
                                <div class="dept-chip-icon"><i class="fa-solid {{ $icon }}"></i></div>
                                <input type="checkbox" name="department_ids[]" value="{{ $dept->id }}" class="onb-dept-check" style="display:none;" onchange="handleChipChange(this, 5, 'onb-dept-check')">
                                <div class="dept-chip-name">{{ Str::limit($dept->name_sw, 22) }}</div>
                                @if($code && $code !== '—') <div class="dept-chip-code">{{ $code }}</div> @endif
                            </label>
                        @endforeach
                    </div>

                    <p style="font-size:.75rem;color:var(--text-muted);margin-top:2px;text-align:center;">Umechagua: <span id="onb_check_count" style="font-weight:700;color:var(--accent);">0</span> / 5</p>
                </div>

                {{-- Kiongozi: single dept + cheo (Hidden by default based on role) --}}
                <div id="onb_leader_section" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">Chagua Idara ya Uongozi</label>
                        <select name="department_id" id="onb_leader_department" class="form-control" required disabled onchange="updateLeadershipRoleOptions('onb_leader_department', 'onb_leader_cheo')">
                            @foreach($allDepartments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name_sw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cheo cha Uongozi</label>
                        <select name="cheo" id="onb_leader_cheo" class="form-control" required disabled>
                            <option value="">-- Chagua Cheo --</option>
                            <option value="Mwenyekiti">Mwenyekiti</option>
                            <option value="Makamu Mwenyekiti">Makamu Mwenyekiti</option>
                            <option value="Katibu">Katibu</option>
                            <option value="Katibu Msaidizi">Katibu Msaidizi</option>
                            <option value="Mhasibu">Mhasibu</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex;gap:12px;margin-top:20px;">
                    <button type="button" class="btn btn-secondary" style="flex:1;padding:13px;font-size:.95rem;display:flex;align-items:center;justify-content:center;gap:8px;" onclick="document.getElementById('onb_step_1').style.display='none'; document.getElementById('onb_step_role').style.display='block';">
                        <i class="fa-solid fa-arrow-left"></i> NYUMA
                    </button>
                    <button type="button" class="btn btn-primary" style="flex:2;padding:13px;font-size:.95rem;display:flex;align-items:center;justify-content:center;gap:8px;" onclick="document.getElementById('onb_step_1').style.display='none'; document.getElementById('onb_step_2').style.display='block';">
                        ENDELEA <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: Photo Upload -->
            <div id="onb_step_2" style="display:none;">
                <div style="margin-top:10px;margin-bottom:20px;padding:30px 14px;border:2px dashed var(--accent);border-radius:16px;text-align:center;background:#f8fafc;">
                    <label class="form-label" style="margin-bottom:15px;font-weight:800;color:var(--text);font-size:1.1rem;">Hatua ya Mwisho: Pakia Picha Yako</label>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                        <div id="onb-photo-preview-wrap" style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:3px solid var(--accent);background:#e2e8f0;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(99,102,241,.2);">
                            @if($user->avatar)
                                <img id="onb-photo-preview" src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <img id="onb-photo-preview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                <i class="fa-solid fa-camera" id="onb-photo-placeholder" style="font-size:2rem;color:#94a3b8;"></i>
                            @endif
                        </div>
                        <label for="onb_avatar_input" class="btn btn-outline btn-sm" style="cursor:pointer;font-size:.85rem;margin-top:4px;padding:8px 16px;">
                            <i class="fa-solid fa-upload"></i> Chagua Picha
                        </label>
                        <input type="file" id="onb_avatar_input" name="avatar" accept="image/*" style="display:none;" onchange="previewOnboardingPhoto(this)">
                        <span style="font-size:.75rem;color:var(--text-muted);">PNG, JPG (max 2MB) — Si lazima</span>
                    </div>
                </div>

                <div style="display:flex;gap:12px;">
                    <button type="button" class="btn btn-secondary" style="flex:1;padding:13px;font-size:.95rem;display:flex;align-items:center;justify-content:center;gap:8px;" onclick="document.getElementById('onb_step_2').style.display='none'; document.getElementById('onb_step_1').style.display='block';">
                        <i class="fa-solid fa-arrow-left"></i> NYUMA
                    </button>
                    <button type="submit" class="btn btn-success" style="flex:2;padding:13px;font-size:.95rem;display:flex;align-items:center;justify-content:center;gap:8px;">
                        MALIZIA <i class="fa-solid fa-check"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewOnboardingPhoto(input) {
            const preview = document.getElementById('onb-photo-preview');
            const placeholder = document.getElementById('onb-photo-placeholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if(placeholder) placeholder.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</div>
@endif

<script>
    // ── Panel switching ──
    const notificationTrigger = document.getElementById('notification-trigger');
    const notificationPanel = document.getElementById('notification-panel');
    const notificationClose = document.getElementById('notification-close');
    const notificationCount = document.querySelector('.notification-count');

    function setNotificationPanel(open) {
        if (!notificationPanel || !notificationTrigger) return;
        notificationPanel.classList.toggle('open', open);
        notificationTrigger.setAttribute('aria-expanded', String(open));
    }

    notificationTrigger?.addEventListener('click', event => {
        event.stopPropagation();
        const willOpen = !notificationPanel.classList.contains('open');
        setNotificationPanel(willOpen);
        if (willOpen && notificationCount) {
            notificationCount.textContent = '0';
            fetch('{{ route('notifications.read') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                },
            });
        }
    });
    notificationClose?.addEventListener('click', () => setNotificationPanel(false));
    document.addEventListener('click', event => {
        if (notificationPanel?.classList.contains('open') && !notificationPanel.contains(event.target) && !notificationTrigger.contains(event.target)) {
            setNotificationPanel(false);
        }
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') setNotificationPanel(false);
    });

    function showPanel(name, el) {
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        const panel = document.getElementById('panel-' + name);
        if (panel) panel.classList.add('active');
        if (el) el.classList.add('active');
        const titles = { 
            overview: 'Overview & Takwimu', 
            members: 'Usimamizi wa Wanachama', 
            committees: 'Kamati & Idara za Kanisa',
            payments: 'Mfumo wa Malipo & Risiti', 
            letters: 'Mfumo wa Barua Rasmi', 
            reports: 'Ripoti za Kanisa', 
            settings: 'Mipangilio, Muhuri & Saini', 
            audit: 'Audit Log & Usalama', 
            announcements: 'Matangazo ya Kanisa', 
            sms: 'Tuma SMS kwa Wanachama', 
            profile: 'Taarifa za Mtumiaji', 
            help: 'Ujumbe wa Msaada',
            'account-settings': @json(__('Mipangilio ya Akaunti'))
        };
        document.getElementById('topbar-title').textContent = titles[name] || name;
    }

    // ── Onboarding role toggle ──
    function toggleOnboardingFields(role) {
        const memberSection = document.getElementById('onb_member_section');
        const leaderSection = document.getElementById('onb_leader_section');
        const isMember = role === 'mshirika';
        const isLeader = role === 'kiongozi';

        if (memberSection) memberSection.style.display = isMember ? 'block' : 'none';
        if (leaderSection) leaderSection.style.display = isLeader ? 'block' : 'none';

        document.querySelectorAll('.onb-dept-check').forEach(input => {
            input.disabled = !isMember;
        });
        document.querySelectorAll('#onb_leader_section select').forEach(input => {
            input.disabled = !isLeader;
            input.required = isLeader;
        });
    }

    function selectOnboardingRole(role) {
        const roleSelect = document.getElementById('onb_role');
        const nextButton = document.getElementById('onb_role_next');

        if (roleSelect) roleSelect.value = role;

        document.querySelectorAll('[data-role-card]').forEach(card => {
            card.classList.toggle('selected', card.dataset.roleCard === role);
        });

        if (nextButton) {
            nextButton.disabled = !role;
            nextButton.style.opacity = role ? '1' : '.55';
        }

        toggleOnboardingFields(role);
    }

    function goToOnboardingDepartments() {
        const role = document.getElementById('onb_role')?.value;
        if (!role) return;

        document.getElementById('onb_step_role').style.display = 'none';
        document.getElementById('onb_step_1').style.display = 'block';
        toggleOnboardingFields(role);
    }

    // ── Dept chip handling ──
    function validateOnboardingSubmit() {
        const role = document.getElementById('onb_role')?.value;

        if (role === 'mshirika') {
            const selectedDepartments = document.querySelectorAll('.onb-dept-check:checked').length;
            if (selectedDepartments < 1) {
                alert('Tafadhali chagua angalau idara moja kabla ya kumaliza.');
                document.getElementById('onb_step_2').style.display = 'none';
                document.getElementById('onb_step_1').style.display = 'block';
                return false;
            }
        }

        if (role === 'kiongozi') {
            const department = document.getElementById('onb_leader_department');
            const cheo = document.getElementById('onb_leader_cheo');
            if (!department?.value || !cheo?.value) {
                alert('Tafadhali chagua idara na cheo cha uongozi kabla ya kumaliza.');
                document.getElementById('onb_step_2').style.display = 'none';
                document.getElementById('onb_step_1').style.display = 'block';
                return false;
            }
        }

        return true;
    }

    function handleChipChange(cb, max, className) {
        let checkedBoxes = document.querySelectorAll('.' + className + ':checked');
        if (checkedBoxes.length > max) {
            cb.checked = false; // Revert change
            checkedBoxes = document.querySelectorAll('.' + className + ':checked');
        }

        // Update styling for all chips
        document.querySelectorAll('.' + className).forEach(box => {
            const label = box.closest('.dept-chip-label');
            if (box.checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
        });

        // Update counter for onboarding
        if (className === 'onb-dept-check') {
            const countEl = document.getElementById('onb_check_count');
            if(countEl) countEl.textContent = checkedBoxes.length;
        }
    }


    // ── SMS counter ──
    function updateSmsCount() {
        const v = document.getElementById('smsText');
        if (v) document.getElementById('smsCount').textContent = v.value.length;
    }

    // ── Letter Preview Modal ──
    function previewLetterModal(data) {
        document.getElementById('vlm_ref').textContent = data.ref || '—';
        document.getElementById('vlm_type').textContent = data.type || 'Barua Rasmi';
        document.getElementById('vlm_sender').textContent = data.sender || '—';
        document.getElementById('vlm_date').textContent = data.date || '—';
        document.getElementById('vlm_recipient').textContent = data.recipient || '—';
        document.getElementById('vlm_subject').textContent = data.subject || '—';
        document.getElementById('vlm_body').textContent = data.body || '—';

        // Status badge
        const statusBadge = document.getElementById('vlm_status_badge');
        if (data.status === 'approved' || data.status === 'generated') {
            statusBadge.className = 'badge-pill badge-kiongozi';
            statusBadge.style.cssText = 'background:#dcfce7;color:#15803d;';
            statusBadge.innerHTML = '✓ Imeidhinishwa';
        } else if (data.status === 'rejected') {
            statusBadge.className = 'badge-pill';
            statusBadge.style.cssText = 'background:#fee2e2;color:#b91c1c;';
            statusBadge.innerHTML = '✗ Imekataliwa';
        } else {
            statusBadge.className = 'badge-pill badge-pending';
            statusBadge.style.cssText = '';
            statusBadge.innerHTML = '⏳ Inasubiri Idhini';
        }

        // Rejection reason
        const rejContainer = document.getElementById('vlm_rejection_container');
        if (data.rejection_reason) {
            rejContainer.style.display = 'block';
            document.getElementById('vlm_rejection_reason').textContent = data.rejection_reason;
        } else {
            rejContainer.style.display = 'none';
        }

        // PDF Buttons
        const viewPdfBtn = document.getElementById('vlm_view_pdf_btn');
        const downloadPdfBtn = document.getElementById('vlm_download_pdf_btn');

        if (data.view_url) {
            viewPdfBtn.style.display = 'inline-flex';
            viewPdfBtn.href = data.view_url;
        } else {
            viewPdfBtn.style.display = 'none';
        }

        if (data.download_url) {
            downloadPdfBtn.style.display = 'inline-flex';
            downloadPdfBtn.href = data.download_url;
        } else {
            downloadPdfBtn.style.display = 'none';
        }

        document.getElementById('viewLetterModal').classList.add('open');
    }

    // ── Auto-open panel from flash ──
    @if(session('success') || session('error'))
        // keep current panel visible — flash auto-shows
    @endif
</script>
<script>
    function toggleMobileSidebar(force) {
        const shouldOpen = typeof force === 'boolean' ? force : !document.body.classList.contains('sidebar-open');
        document.body.classList.toggle('sidebar-open', shouldOpen);
        document.querySelectorAll('.mobile-menu-btn').forEach(btn => {
            btn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
        });
    }

    const adminMemberSearchData = @json($memberSearchData ?? []);

    function formatMoney(value) {
        return 'Tsh ' + Number(value || 0).toLocaleString();
    }

    function renderMemberDocumentary(member) {
        const panel = document.getElementById('memberDocumentary');
        const avatar = document.getElementById('memberDocAvatar');
        const name = document.getElementById('memberDocName');
        const summary = document.getElementById('memberDocSummary');
        const grid = document.getElementById('memberDocGrid');
        const hint = document.getElementById('memberSearchHint');
        if (!panel || !member) return;

        avatar.innerHTML = member.avatar
            ? `<img src="${member.avatar}" alt="${member.name}">`
            : (member.name || '?').charAt(0).toUpperCase();
        name.textContent = member.name || 'Mwanachama';
        summary.textContent = `${member.role || 'Mshirika'} · ${member.status || 'Active'} · ${member.departments?.join(', ') || 'Hana idara'}`;

        const rows = [
            ['Namba ya Mshirika', member.member_id || 'Haijawekwa'],
            ['Email', member.email || 'Haijawekwa'],
            ['Simu', member.phone || 'Haijawekwa'],
            ['Idara', member.departments?.join(', ') || 'Hana idara'],
            ['Cheo', member.cheo || member.role || 'Mshirika'],
            ['Jumla ya Michango', formatMoney(member.total_paid)],
            ['Rekodi za Malipo', `${member.payments_count || 0} ${member.latest_payment ? '· mwisho ' + member.latest_payment : ''}`],
            ['Barua / Maombi', `${member.letters_count || 0} ${member.latest_letter ? '· ' + member.latest_letter : ''}`],
            ['Jinsia', member.gender || 'Haijawekwa'],
            ['Tarehe ya Kuzaliwa', member.dob || 'Haijawekwa'],
            ['Anwani', member.address || 'Haijawekwa'],
        ];

        grid.innerHTML = rows.map(([label, value]) => `
            <div class="documentary-metric">
                <span>${label}</span>
                <strong>${value}</strong>
            </div>
        `).join('');

        panel.classList.add('open');
        if (hint) hint.textContent = 'Taarifa kamili za mwanachama zimepatikana kwenye system.';
    }

    function searchAdminMember(value) {
        const query = (value || '').trim().toLowerCase();
        const panel = document.getElementById('memberDocumentary');
        const hint = document.getElementById('memberSearchHint');

        if (!query) {
            if (panel) panel.classList.remove('open');
            if (hint) hint.textContent = 'Matokeo yataonekana papo hapo ukianza kuandika jina la mtu.';
            return;
        }

        const found = adminMemberSearchData.find(member => {
            const haystack = [
                member.name,
                member.email,
                member.phone,
                member.member_id,
                ...(member.departments || []),
            ].filter(Boolean).join(' ').toLowerCase();
            return haystack.includes(query);
        });

        if (found) {
            renderMemberDocumentary(found);
        } else {
            if (panel) panel.classList.remove('open');
            if (hint) hint.textContent = 'Hakuna mwanachama aliyepatikana kwa jina hilo.';
        }
    }

    function clearAdminMemberSearch() {
        const input = document.getElementById('adminMemberSearch');
        if (input) {
            input.value = '';
            input.focus();
        }
        searchAdminMember('');
    }

    function filterMembersByDepartment(departmentId, label) {
        const cards = document.querySelectorAll('#panel-members .member-card');
        let visible = 0;

        cards.forEach(card => {
            const ids = (card.dataset.departments || '').split(',').filter(Boolean);
            const show = departmentId === 'all' || ids.includes(String(departmentId));
            card.style.display = show ? '' : 'none';
            if (show) visible += 1;
        });

        const labelText = document.querySelector('#panel-members p');
        if (labelText && label) {
            labelText.innerHTML = `${label} · Jumla: <strong>${visible}</strong>`;
        }
    }

    function openChangeLeaderPasswordModal(leaderId, leaderName, leaderEmail, departmentId, leaderCheo) {
        const modal = document.getElementById('changeLeaderPasswordModal');
        const form = document.getElementById('changeLeaderPasswordForm');
        const nameEl = document.getElementById('clp_leader_name');
        const emailEl = document.getElementById('clp_leader_email');
        const cheoEl = document.getElementById('clp_leader_cheo');
        const deptIdInput = document.getElementById('clp_department_id');
        const infoEl = document.getElementById('clp_leader_info');

        if (form) form.action = `/department-leaders/${leaderId}/password`;
        if (nameEl) nameEl.textContent = leaderName;
        if (emailEl) emailEl.textContent = leaderEmail;
        if (cheoEl) cheoEl.textContent = leaderCheo || 'Kiongozi';
        if (infoEl) infoEl.textContent = `${leaderName} (${leaderCheo || 'Kiongozi'})`;
        if (deptIdInput && departmentId) deptIdInput.value = departmentId;

        const pwd = document.getElementById('clp_new_pwd');
        const pwdConfirm = document.getElementById('clp_new_pwd_confirm');
        if (pwd) pwd.value = '';
        if (pwdConfirm) pwdConfirm.value = '';

        if (modal) modal.classList.add('open');
    }

    function generateLeaderPassword(pwdId, confirmId) {
        const words = ['Uongozi', 'Baraka', 'Amani', 'Neema', 'Upendo', 'Tumaini', 'Ushindi'];
        const randomWord = words[Math.floor(Math.random() * words.length)];
        const randomNum = Math.floor(100 + Math.random() * 900);
        const symbols = ['!', '@', '#', '$'];
        const randomSym = symbols[Math.floor(Math.random() * symbols.length)];
        const generated = `${randomWord}${randomNum}${randomSym}`;

        const pwdEl = document.getElementById(pwdId);
        const confirmEl = document.getElementById(confirmId);

        if (pwdEl) {
            pwdEl.value = generated;
            pwdEl.type = 'text';
            const eye = document.getElementById(`${pwdId}_eye`);
            if (eye) eye.className = 'fa-solid fa-eye-slash';
        }
        if (confirmEl) {
            confirmEl.value = generated;
            confirmEl.type = 'text';
            const eye = document.getElementById(`${confirmId}_eye`);
            if (eye) eye.className = 'fa-solid fa-eye-slash';
        }
    }

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(`${inputId}_eye`);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            if (eye) eye.className = 'fa-solid fa-eye-slash';
        } else {
            input.type = 'password';
            if (eye) eye.className = 'fa-solid fa-eye';
        }
    }

    function copyCredentialsText() {
        const block = document.getElementById('credentialsCopyBlock');
        const btnText = document.getElementById('copyCredBtnText');
        if (!block) return;

        let text = block.innerText || block.textContent;
        navigator.clipboard.writeText(text.trim()).then(() => {
            if (btnText) btnText.textContent = 'Imenakiliwa!';
            setTimeout(() => {
                if (btnText) btnText.textContent = 'Nakili Taarifa';
            }, 2500);
        }).catch(err => {
            console.error('Copy failed: ', err);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.sidebar .nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 640) {
                    setTimeout(() => toggleMobileSidebar(false), 80);
                }
            });
        });

        const memberSearch = document.getElementById('adminMemberSearch');
        if (memberSearch) {
            memberSearch.addEventListener('input', e => searchAdminMember(e.target.value));
        }

        const memberFilter = document.getElementById('memberDepartmentFilter');
        if (memberFilter) {
            filterMembersByDepartment(memberFilter.value, memberFilter.options[memberFilter.selectedIndex].text);
        }

        const requestedPanel = new URLSearchParams(window.location.search).get('panel');
        if (requestedPanel) {
            const navItem = document.querySelector(`.nav-item[onclick*="${requestedPanel}"]`);
            showPanel(requestedPanel, navItem);
        }
    });
</script>
</body>
</html>
