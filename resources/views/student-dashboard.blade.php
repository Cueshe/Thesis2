<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Dashboard - Q2L</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --brand-primary: #4f46e5;
            --brand-primary-dark: #4338ca;
            --page-bg: #f8fafc;
            --card-bg: #ffffff;
            --sidebar-bg: rgba(255, 255, 255, 0.94);
            --surface-border: rgba(148, 163, 184, 0.45);
            --surface-border-strong: rgba(99, 102, 241, 0.18);
            --shadow-soft: 0 24px 55px -25px rgba(15, 23, 42, 0.3);
            --shadow-hover: 0 30px 60px -30px rgba(79, 70, 229, 0.35);
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --text-subtle: #7b8aa8;
            --text-inverse: #ffffff;
            --link-primary: #4f46e5;
            --link-primary-hover: #4338ca;
            --surface-muted: rgba(248, 250, 252, 0.92);
            --surface-muted-strong: rgba(241, 245, 255, 0.9);
            --surface-contrast: rgba(79, 70, 229, 0.16);
            --surface-accent: rgba(79, 70, 229, 0.12);
            --badge-bg: rgba(79, 70, 229, 0.12);
            --badge-text: #4338ca;
            --chip-bg: rgba(79, 70, 229, 0.12);
            --chip-text: #4338ca;
            --input-bg: rgba(255, 255, 255, 0.92);
            --input-bg-focus: #ffffff;
            --input-border: rgba(148, 163, 184, 0.45);
            --input-ring: rgba(79, 70, 229, 0.22);
            --banner-success-bg: rgba(34, 197, 94, 0.1);
            --banner-success-text: #047857;
            --banner-error-bg: rgba(239, 68, 68, 0.1);
            --banner-error-text: #b91c1c;
            --overlay-soft: rgba(15, 23, 42, 0.35);
            --chat-surface: rgba(248, 250, 252, 0.92);
            --chat-message-bg: #ffffff;
            --chat-message-text: #1f2937;
            --chat-user-bg: var(--brand-primary);
            --chat-user-text: #ffffff;
            --chat-avatar-bot: var(--brand-primary);
            --chat-avatar-user: var(--brand-primary);
            --chat-border: rgba(148, 163, 184, 0.25);
            --chat-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
            --chat-header-bg: var(--brand-primary);
            --chat-accent-soft: rgba(79, 70, 229, 0.1);
        }

        .dark {
            color-scheme: dark;
            --brand-primary: #6366f1;
            --brand-primary-dark: #818cf8;
            --page-bg: #0f172a;
            --card-bg: rgba(15, 23, 42, 0.88);
            --sidebar-bg: rgba(15, 23, 42, 0.92);
            --surface-border: rgba(71, 85, 105, 0.55);
            --surface-border-strong: rgba(129, 140, 248, 0.4);
            --shadow-soft: 0 24px 55px -25px rgba(2, 6, 23, 0.7);
            --shadow-hover: 0 30px 70px -30px rgba(99, 102, 241, 0.55);
            --text-primary: #e2e8f0;
            --text-muted: #cbd5f5;
            --text-subtle: #94a3b8;
            --text-inverse: #0f172a;
            --link-primary: #a5b4fc;
            --link-primary-hover: #c7d2fe;
            --surface-muted: rgba(30, 41, 59, 0.78);
            --surface-muted-strong: rgba(30, 41, 59, 0.92);
            --surface-contrast: rgba(129, 140, 248, 0.24);
            --surface-accent: rgba(129, 140, 248, 0.22);
            --badge-bg: rgba(99, 102, 241, 0.22);
            --badge-text: #e0e7ff;
            --chip-bg: rgba(129, 140, 248, 0.22);
            --chip-text: #e0e7ff;
            --input-bg: rgba(15, 23, 42, 0.7);
            --input-bg-focus: rgba(15, 23, 42, 0.85);
            --input-border: rgba(71, 85, 105, 0.65);
            --input-ring: rgba(129, 140, 248, 0.35);
            --banner-success-bg: rgba(34, 197, 94, 0.18);
            --banner-success-text: #bbf7d0;
            --banner-error-bg: rgba(248, 113, 113, 0.18);
            --banner-error-text: #fecaca;
            --overlay-soft: rgba(2, 6, 23, 0.6);
            --chat-surface: rgba(15, 23, 42, 0.85);
            --chat-message-bg: rgba(30, 41, 59, 0.92);
            --chat-message-text: #e2e8f0;
            --chat-user-bg: var(--brand-primary);
            --chat-user-text: #f8fafc;
            --chat-avatar-bot: var(--brand-primary);
            --chat-avatar-user: var(--brand-primary);
            --chat-border: rgba(71, 85, 105, 0.45);
            --chat-shadow: 0 26px 60px rgba(2, 6, 23, 0.6);
            --chat-header-bg: var(--brand-primary);
            --chat-accent-soft: rgba(99, 102, 241, 0.18);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            color: var(--text-primary);
            min-height: 100vh;
            margin: 0;
            transition: background 0.45s ease, color 0.45s ease;
        }

        .layout-shell {
            padding: 0.5rem 0.75rem;
        }
        @media (min-width: 768px) {
            .layout-shell {
                padding: 2rem 1rem;
            }
        }
        @media (min-width: 1024px) {
            .layout-shell {
                padding: 2.5rem 1.5rem;
            }
        }
        .layout-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .layout-grid {
                grid-template-columns: 260px 1fr;
            }
        }
        .dashboard-sidebar {
            background: var(--sidebar-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
            transition: background-color 0.35s ease, border-color 0.35s ease, color 0.35s ease;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--surface-border);
        }
        .sidebar-avatar {
            position: relative;
        }
        .theme-avatar {
            background: var(--brand-primary);
            color: var(--text-inverse);
        }
        .sidebar-avatar .status-dot {
            position: absolute;
            right: -0.35rem;
            bottom: -0.35rem;
            width: 0.95rem;
            height: 0.95rem;
            border-radius: 9999px;
            border: 2px solid #ffffff;
            background: #34d399;
        }
        .sidebar-meta {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .role-label {
            color: var(--text-muted);
            letter-spacing: 0.12em;
        }
        .section-label {
            color: var(--text-muted);
            letter-spacing: 0.18em;
        }
        .border-divider {
            border-color: var(--surface-border) !important;
        }
        .sidebar-meta .name-line {
            font-size: 0.938rem;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: 0.02em;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-subtle);
            transition: all 0.15s ease;
        }
        .nav-link svg {
            width: 1.125rem;
            height: 1.125rem;
            color: var(--text-muted);
            transition: color 0.15s ease;
        }
        .nav-link:hover {
            color: var(--text-primary);
            background: var(--surface-accent);
        }
        .nav-link:hover svg {
            color: var(--brand-primary);
        }
        .nav-link.active {
            color: var(--brand-primary);
            background: var(--surface-contrast);
            font-weight: 600;
        }
        .nav-link.active svg {
            color: var(--brand-primary);
        }
        .main-content {
            padding-bottom: 6rem;
        }
        .dashboard-topbar {
            background: var(--card-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
            transition: background-color 0.35s ease, border-color 0.35s ease, color 0.35s ease;
        }
        .topbar-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            position: relative;
        }
        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }
        .topbar-title {
            color: var(--text-primary);
            transition: color 0.3s ease;
        }
        .search-field {
            position: relative;
            flex: 1;
            max-width: 400px;
        }
        .search-field input {
            width: 100%;
            height: 42px;
            border-radius: 0.75rem;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            padding: 0 1rem 0 2.75rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            transition: all 0.15s ease;
        }
        .search-field input:focus {
            background: var(--input-bg-focus);
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px var(--input-ring);
            outline: none;
        }
        .search-field svg {
            position: absolute;
            top: 50%;
            left: 0.875rem;
            transform: translateY(-50%);
            width: 1.125rem;
            height: 1.125rem;
            color: var(--text-muted);
        }
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }
        .topbar-avatar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .topbar-avatar .name-part {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .topbar-avatar .greeting {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .topbar-avatar .avatar-circle {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-inverse);
            background: var(--brand-primary);
        }
        .dashboard-card {
            background: var(--card-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
            transition: background-color 0.35s ease, border-color 0.35s ease, color 0.35s ease;
        }
        .calendar-card {
            padding: 1.75rem;
        }
        @media (max-width: 767px) {
            .calendar-card {
                padding: 1.25rem;
            }
        }
        .calendar-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .calendar-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .month-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }
        .month-nav button {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            border: 1px solid var(--surface-border);
            background: var(--card-bg);
            color: var(--text-subtle);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .month-nav button:hover {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
            background: rgba(79, 70, 229, 0.12);
        }
        .month-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            min-width: 140px;
            text-align: center;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.5rem;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        .calendar-cell {
            min-height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            color: var(--text-primary);
            background: var(--surface-muted);
            transition: all 0.15s ease;
        }
        .calendar-cell:hover {
            background: var(--surface-contrast);
            color: var(--brand-primary);
        }
        .calendar-cell.muted {
            color: rgba(148, 163, 184, 0.6);
            background: transparent;
        }
        .calendar-cell.today {
            background: var(--brand-primary);
            color: var(--text-inverse);
            font-weight: 600;
        }
        .calendar-cell.has-assignment::after {
            content: '';
            position: absolute;
            bottom: 0.375rem;
            width: 0.375rem;
            height: 0.375rem;
            border-radius: 9999px;
            background: var(--brand-primary);
        }
        .calendar-cell.today.has-assignment::after {
            background: #ffffff;
        }
        .assignments-card {
            padding: 1.75rem;
        }
        @media (max-width: 767px) {
            .assignments-card {
                padding: 1.25rem;
            }
        }
        .assignments-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .assignments-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .link-button {
            color: var(--link-primary);
            transition: color 0.2s ease;
        }
        .link-button:hover {
            color: var(--link-primary-hover);
        }
        .assignment-card {
            background: var(--surface-muted);
            border: 1px solid var(--surface-border);
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            transition: all 0.15s ease;
        }
        .assignment-card:hover {
            background: var(--surface-muted-strong);
            border-color: var(--brand-primary);
            box-shadow: var(--shadow-hover);
        }
        .assignment-card .title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .assignment-card .subject {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-subtle);
        }
        .assignment-card .deadline-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }
        .assignment-card .deadline-date {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--brand-primary);
        }
        .message-banner {
            padding: 1rem 1.2rem;
            border-radius: 1.35rem;
            border: 1px solid var(--surface-border-strong);
            background: var(--banner-success-bg);
            color: var(--banner-success-text);
            box-shadow: 0 20px 45px -32px rgba(34, 197, 94, 0.45);
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        .message-banner.error {
            background: var(--banner-error-bg);
            color: var(--banner-error-text);
            box-shadow: 0 20px 45px -32px rgba(239, 68, 68, 0.45);
        }
        /* Mobile Layout */
        @media (max-width: 1023px) {
            .layout-grid {
                gap: 1rem;
                grid-template-columns: 1fr;
            }
            .dashboard-sidebar {
                display: none;
            }
            .main-content {
                width: 100%;
            }
        }
        @media (max-width: 640px) {
            .layout-shell {
                padding: 0.5rem 0.5rem 0.75rem;
            }
            .dashboard-topbar {
                padding: 1rem 1rem !important;
            }
            .topbar-row {
                flex-direction: column;
                gap: 0.75rem;
                align-items: stretch;
            }
            /* Logo on left, avatar on right - first row */
            .topbar-brand {
                order: 1;
                justify-content: flex-start;
                padding: 0.25rem 0;
                padding-right: 6rem;
                display: flex;
                align-items: center;
                position: relative;
                flex: 1;
                min-width: 0;
            }
            .topbar-brand img {
                height: 2.5rem;
                flex-shrink: 0;
            }
            .topbar-brand span {
                font-size: 1.25rem;
                font-weight: 700;
                letter-spacing: -0.02em;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .topbar-actions {
                order: 2;
                position: absolute;
                right: 0;
                top: 0;
                display: flex;
                align-items: center;
                gap: 0.375rem;
                flex-shrink: 0;
                z-index: 10;
            }
            /* Ensure translation toggle is clickable on mobile */
            .topbar-actions > div {
                pointer-events: auto;
                z-index: 10;
                position: relative;
            }
            .topbar-actions button {
                min-width: 44px;
                min-height: 44px;
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
                white-space: nowrap;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            .topbar-actions button svg {
                width: 1rem;
                height: 1rem;
            }
            /* Ensure dropdown is visible and clickable */
            .topbar-actions [x-data] [x-show] {
                z-index: 100 !important;
            }
            .topbar-avatar .greeting,
            .topbar-avatar .name-part {
                display: none;
            }
            .topbar-avatar .avatar-circle {
                width: 2.75rem;
                height: 2.75rem;
                font-size: 1.125rem;
                border: 2px solid rgba(79, 70, 229, 0.15);
            }
            /* Second row: search field (full width) */
            .search-field {
                order: 3;
                width: 100%;
                max-width: 100%;
                margin-top: 0.25rem;
            }
            .search-field input {
                height: 48px;
                font-size: 0.9375rem;
                border-width: 1.5px;
            }
            .search-field svg {
                width: 1.25rem;
                height: 1.25rem;
            }
            .calendar-card,
            .assignments-card {
                padding: 1rem !important;
            }
            .calendar-title,
            .assignments-title {
                font-size: 1.125rem;
            }
            .calendar-cell {
                min-height: 40px;
                font-size: 0.8125rem;
            }
            .calendar-weekdays {
                font-size: 0.6875rem;
            }
            .month-label {
                font-size: 0.8125rem;
                min-width: 100px;
            }
            .assignment-card {
                padding: 0.875rem 1rem !important;
            }
            .main-content {
                gap: 1rem;
                padding-bottom: 6rem;
            }
            .space-y-7 > * + * {
                margin-top: 1rem !important;
            }
            .nav-link {
                padding: 0.75rem 0.875rem;
                font-size: 0.9375rem;
            }
            .month-nav button {
                width: 2.25rem;
                height: 2.25rem;
            }
        }
        /* Extra small screens (360px and below) */
        @media (max-width: 360px) {
            .dashboard-topbar {
                padding: 0.75rem !important;
            }
            .topbar-brand {
                padding-right: 5.5rem;
            }
            .topbar-brand img {
                height: 1.875rem;
            }
            .topbar-brand span {
                font-size: 1rem;
            }
            .topbar-actions {
                gap: 0.25rem;
                z-index: 10;
            }
            .topbar-actions > div {
                z-index: 10;
            }
            .topbar-actions button {
                padding: 0.5rem 0.625rem;
                font-size: 0.6875rem;
            }
            .topbar-actions button svg {
                width: 0.875rem;
                height: 0.875rem;
            }
            .topbar-avatar .avatar-circle {
                width: 2.25rem;
                height: 2.25rem;
                font-size: 0.875rem;
            }
        }
        /* Very small screens (320px and below) */
        @media (max-width: 320px) {
            .dashboard-topbar {
                padding: 0.625rem !important;
            }
            .topbar-brand {
                padding-right: 5rem;
            }
            .topbar-brand img {
                height: 1.75rem;
            }
            .topbar-brand span {
                font-size: 0.9375rem;
            }
            .topbar-actions {
                gap: 0.125rem;
                z-index: 10;
            }
            .topbar-actions > div {
                z-index: 10;
            }
            .topbar-actions button {
                padding: 0.4375rem 0.5rem;
                font-size: 0.625rem;
            }
            .topbar-actions button svg {
                width: 0.8125rem;
                height: 0.8125rem;
            }
            .topbar-actions button span {
                display: none;
            }
            .topbar-avatar .avatar-circle {
                width: 2rem;
                height: 2rem;
                font-size: 0.8125rem;
            }
        }
        .assignment-card .title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .assignment-card .subject {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-subtle);
        }
        .assignment-card .deadline-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }
        .assignment-card .deadline-date {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--brand-primary);
        }
        .message-banner {
            padding: 1rem 1.2rem;
            border-radius: 1.35rem;
            border: 1px solid var(--surface-border-strong);
            background: var(--banner-success-bg);
            color: var(--banner-success-text);
            box-shadow: 0 20px 45px -32px rgba(34, 197, 94, 0.45);
        }
        .message-banner.error {
            background: var(--banner-error-bg);
            color: var(--banner-error-text);
            box-shadow: 0 20px 45px -32px rgba(239, 68, 68, 0.45);
        }
        /* Mobile Menu Styles */
        .mobile-menu-btn {
            display: none;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            background: var(--brand-primary);
            color: var(--text-inverse);
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .mobile-menu-btn:hover {
            background: var(--brand-primary-dark);
        }
        /* Gamification Styles */
        .gamification-card,
        .leaderboard-card {
            padding: 1.75rem;
        }
        @media (max-width: 767px) {
            .gamification-card,
            .leaderboard-card {
                padding: 1.25rem;
            }
        }
        .gamification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .gamification-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .rank-badge {
            background: var(--brand-primary);
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .gamification-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .level-display {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .level-circle {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background: var(--brand-primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }
        .level-number {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
        }
        .level-label {
            font-size: 0.625rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .points-display {
            flex: 1;
        }
        .points-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--brand-primary);
            line-height: 1;
        }
        .points-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }
        .progress-section {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .progress-text {
            font-weight: 600;
            color: var(--text-primary);
        }
        .progress-bar {
            height: 0.75rem;
            background: var(--surface-muted);
            border-radius: 9999px;
            overflow: hidden;
            position: relative;
        }
        .progress-fill {
            height: 100%;
            background: var(--brand-primary);
            border-radius: 9999px;
            transition: width 0.5s ease;
        }
        .streak-display {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--surface-muted);
            border-radius: 0.75rem;
            border: 1px solid var(--surface-border);
        }
        .streak-icon {
            width: 2.5rem;
            height: 2.5rem;
            color: #f59e0b;
        }
        .streak-days {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .streak-label {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .leaderboard-header {
            margin-bottom: 1.5rem;
        }
        .leaderboard-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .leaderboard-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .leaderboard-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 400px;
            overflow-y: auto;
        }
        .leaderboard-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem;
            background: var(--surface-muted);
            border-radius: 0.75rem;
            border: 1px solid var(--surface-border);
            transition: all 0.2s ease;
        }
        .leaderboard-item.current-user {
            background: var(--surface-accent);
            border-color: var(--brand-primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }
        .leaderboard-rank {
            width: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .rank-medal {
            font-size: 1.5rem;
        }
        .rank-number {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
        }
        .leaderboard-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--brand-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .leaderboard-info {
            flex: 1;
        }
        .leaderboard-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .leaderboard-stats {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .you-badge {
            background: var(--brand-primary);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        @media (max-width: 1023px) {
            .grid.grid-cols-1.lg\:grid-cols-3 {
                grid-template-columns: 1fr;
            }
        }

        .hidden {
            display: none !important;
        }
        @media (max-width: 768px) {
            .pronunciation-header {
                flex-direction: column;
            }
            .practice-item-text {
                font-size: 2rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
    </head>
    <body class="min-h-screen">
    @php($joinedClasses = $joinedClasses ?? [])

    <div class="layout-shell">
        <div class="layout-grid">
            <aside class="dashboard-sidebar p-6 flex flex-col relative" id="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-avatar">
                        <div class="w-14 h-14 rounded-full theme-avatar flex items-center justify-center text-xl font-semibold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="status-dot"></span>
                    </div>
                    <div class="sidebar-meta">
                        <span class="text-xs uppercase tracking-wide role-label">Student</span>
                        <span class="name-line">{{ ucwords(strtolower(auth()->user()->name ?? 'Student')) }}</span>
                    </div>
                </div>

                <nav class="mt-6 space-y-6 pr-1">
                    <div>
                        <p class="text-xs font-semibold section-label uppercase tracking-wide mb-2">Student</p>
                        <ul class="space-y-1.5">
                            <li><a href="{{ route('student.dashboard') }}" class="nav-link active">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75M4.5 10.5V21h15V10.5" />
                                </svg>
                                <span data-translate="dashboard-title">Dashboard</span>
                            </a></li>
                            <li><a href="{{ route('student.calendar') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25m10.5-2.25V5.25M3.75 7.5h16.5M4.5 21.75h15a1.5 1.5 0 001.5-1.5v-12h-18v12a1.5 1.5 0 001.5 1.5z" />
                                </svg>
                                <span data-translate="nav-calendar">Calendar</span>
                            </a></li>
                            <li><a href="{{ route('student.pdf.reader') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span data-translate="nav-pdf-reader">PDF Reader</span>
                            </a></li>
                            <li><a href="{{ route('student.profile') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                                </svg>
                                <span data-translate="nav-profile">My Profile</span>
                            </a></li>
                            <li><a href="{{ route('student.quiz.attempts') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25h13.5M4.5 9.75h15m-9.75 4.5h4.5m-6 4.5h7.5" />
                                </svg>
                                <span data-translate="nav-quiz">My Quiz Attempts</span>
                            </a></li>
                            <li><a href="{{ route('pronunciation.tutor') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3" />
                                </svg>
                                <span data-translate="nav-pronunciation">AI Pronunciation Tutor</span>
                            </a></li>
                            <li>
                                <button type="button" class="nav-link w-full text-left" data-join-class-open>
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span data-translate="nav-join-class">Join a Class</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="text-xs font-semibold section-label uppercase tracking-wide mb-2">Instructor</p>
                        <ul class="space-y-1.5">
                            @php($firstClassId = collect($joinedClasses ?? [])->pluck('id')->filter()->first())
                            <li>
                                <a href="{{ $firstClassId ? route('student.classes.show', $firstClassId) : '#' }}"
                                   class="nav-link {{ $firstClassId ? '' : 'opacity-70 cursor-not-allowed pointer-events-none' }}"
                                   title="{{ $firstClassId ? '' : 'Join a class to see announcements' }}">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5h7.5M6 9.75h12M5.25 15h13.5M8.25 19.5h7.5" />
                                </svg>
                                <span data-translate="nav-announcements">Announcements</span>
                            </a></li>
                            <li><a href="#" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15M6 12h12M8.25 16.5h7.5" />
                                </svg>
                                <span data-translate="nav-assignments">Assignments</span>
                            </a></li>
                        </ul>
                    </div>

                    <div class="pt-2 border-t border-divider space-y-2">
                        <a href="{{ route('student.settings') }}" class="nav-link">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75h4.5M12 5.25v13.5m-6.375 1.5h12.75a1.125 1.125 0 001.125-1.125V6.75a1.125 1.125 0 00-1.125-1.125H5.625A1.125 1.125 0 004.5 6.75v12.375c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            <span data-translate="nav-settings">Settings</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="nav-link w-full justify-start text-red-500 hover:text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-500/10">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21H13.5a2.25 2.25 0 002.25-2.25V15m3.75-3H9.75m9 0l-3 3m3-3l-3-3" />
                                </svg>
                                <span data-translate="nav-logout">Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <main class="space-y-7 main-content">
                @if(session('success'))
                    <div class="message-banner success" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="message-banner error" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <header class="dashboard-topbar px-6 py-4">
                    <div class="topbar-row">
                        <div class="topbar-brand">
                            <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-9 w-auto">
                            <span class="text-xl font-semibold topbar-title">Q2L</span>
                        </div>

                        <div class="search-field">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            <input type="search" name="search" placeholder="Search...">
                        </div>

                        <div class="topbar-actions">
                            <x-translation-toggle />
                            <x-theme-toggle />
                            <div class="topbar-avatar">
                                <div class="text-right hidden sm:block">
                                    <div class="greeting" data-translate="greeting">Hello</div>
                                    <div class="name-part">{{ ucwords(strtolower(auth()->user()->name ?? 'Student')) }}</div>
                                </div>
                                <div class="avatar-circle">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <section class="dashboard-card p-6 space-y-4" id="student-classes-card">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-[color:var(--text-primary)]" data-translate="your-classes-title">Your Classes</h2>
                            <p class="text-sm text-[color:var(--text-muted)]" data-translate="your-classes-desc">Keep track of every portal you’ve unlocked.</p>
                        </div>
                        <button type="button" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2" style="background: var(--brand-primary);" data-join-class-open>
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span data-translate="join-class-button">Join Class</span>
                        </button>
                    </div>

                    @if(empty($joinedClasses))
                        <div class="rounded-2xl border border-dashed border-[color:var(--surface-border)] bg-[color:var(--surface-muted)] p-4 text-sm text-[color:var(--text-muted)]">
                            <p data-translate="your-classes-empty">You haven’t joined a class yet. Enter the code your teacher shares to get started.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($joinedClasses as $class)
    @if(!empty($class['id']))
        <a href="{{ route('student.classes.show', $class['id']) }}" class="block">
    @endif
        <article class="rounded-xl dashboard-card border border-[color:var(--surface-border)] bg-[color:var(--card-bg)] p-5 space-y-4 transition-all relative @if(!empty($class['id'])) cursor-pointer hover:border-[color:var(--brand-primary)] @endif">
    <div class="flex items-center gap-4">
        <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-full bg-[color:var(--surface-muted)]">
            <svg class="w-6 h-6 text-[color:var(--brand-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
        </div>
        <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-[color:var(--text-primary)]">{{ $class['name'] }}</h3>
                <span class="inline-block px-2 py-1 rounded bg-[color:var(--surface-muted)] text-[color:var(--text-primary)] font-mono text-xs">{{ $class['join_code'] }}</span>
            </div>
            <div class="flex items-center text-xs text-[color:var(--text-muted)] gap-2 mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>
                <span>{{ $class['schedule'] ?? 'Schedule to be announced' }}</span>
            </div>
        </div>
    </div>
</article>
    @if(!empty($class['id']))
        </a>
    @endif
                            @endforeach
                        </div>
                    @endif
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Points & Level Card -->
                    <section class="dashboard-card gamification-card">
                        <div class="gamification-header">
                            <h2 class="gamification-title" data-translate="progress-title">Your Progress</h2>
                            <div class="rank-badge">Rank #{{ $userRank ?? 1 }}</div>
                        </div>
                        <div class="gamification-content">
                            <div class="level-display">
                                <div class="level-circle">
                                    <span class="level-number">{{ $user->level ?? 1 }}</span>
                                    <span class="level-label">Level</span>
                                </div>
                                <div class="points-display">
                                    <div class="points-value">{{ number_format($user->points ?? 0) }}</div>
                                    <div class="points-label">Points</div>
                                </div>
                            </div>
                            <div class="progress-section">
                                <div class="progress-header">
                                    <span>Experience</span>
                                    <span class="progress-text">{{ $user->experience ?? 0 }} / {{ $user->getExperienceForNextLevel() ?? 100 }} XP</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $user->getLevelProgress() ?? 0 }}%"></div>
                                </div>
                            </div>
                            <div class="streak-display">
                                <svg class="streak-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 10.5a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
                                </svg>
                                <div>
                                    <div class="streak-days">{{ $user->streak_days ?? 0 }} days</div>
                                    <div class="streak-label">Daily Streak</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Leaderboard Card -->
                    <section class="dashboard-card leaderboard-card">
                        <div class="leaderboard-header">
                            <h2 class="leaderboard-title" data-translate="leaderboard-title">Leaderboard</h2>
                            <span class="leaderboard-subtitle" data-translate="leaderboard-subtitle">Top Students</span>
                        </div>
                        <div class="leaderboard-list">
                            @forelse(($leaderboard ?? []) as $index => $student)
                                <div class="leaderboard-item {{ $student->id === $user->id ? 'current-user' : '' }}">
                                    <div class="leaderboard-rank">{{ $index + 1 }}</div>
                                    <div class="leaderboard-avatar">{{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}</div>
                                    <div class="leaderboard-info">
                                        <div class="leaderboard-name">{{ $student->name ?? 'Student' }}</div>
                                        <div class="leaderboard-meta">
                                            <span>Level {{ $student->level ?? 1 }}</span>
                                            <span>•</span>
                                            <span>{{ number_format($student->points ?? 0) }} pts</span>
                                        </div>
                                    </div>
                                    @if($student->id === $user->id)
                                        <span class="you-badge">You</span>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <p class="text-sm">No leaderboard data available yet.</p>
                                    <p class="text-xs mt-1">Complete activities to appear on the leaderboard!</p>
                                </div>
                            @endforelse
                        </div>
                    </section>

                </div>


                <section class="dashboard-card assignments-card">
                    <div class="assignments-header">
                        <h2 class="assignments-title" data-translate="assignments-title">Assignments</h2>
                        <button type="button" class="text-sm font-semibold link-button" data-translate="view-all">View all</button>
                    </div>

                    <div id="assignmentsList" class="space-y-2.5"></div>
                </section>

                {{-- AI Pronunciation Tutor Card moved to dedicated page --}}
                {{--
                <section class="dashboard-card pronunciation-tutor-card">
                    <div class="pronunciation-header">
                        <div>
                            <h2 class="pronunciation-title" data-translate="pronunciation-title">AI Pronunciation Tutor</h2>
                            <p class="pronunciation-subtitle" data-translate="pronunciation-subtitle">Your AI-powered pronunciation coach. Practice with random words, phrases, and sentences in English and Filipino. Get instant feedback to improve your pronunciation skills!</p>
                        </div>
                        <div class="pronunciation-language-selector">
                            <label class="text-xs font-semibold uppercase tracking-wide mb-2 block" data-translate="pronunciation-select-language">Select Language</label>
                            <select id="pronunciationLanguage" class="language-select">
                                <option value="en-US">English (US)</option>
                                <option value="tl-PH">Filipino</option>
                            </select>
                        </div>
                    </div>

                    <div class="pronunciation-content">
                        <div class="practice-column practice-stack">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)] mb-1">Quest difficulty</p>
                                    <div class="practice-modes">
                                        <button class="practice-mode-btn active" data-mode="word">Word Quest</button>
                                        <button class="practice-mode-btn" data-mode="phrase">Phrase Quest</button>
                                        <button class="practice-mode-btn" data-mode="sentence">Story Quest</button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Current streak</p>
                                    <p class="text-2xl font-black text-[color:var(--brand-primary)]" id="streakCount">0</p>
                                </div>
                            </div>

                            <div class="practice-item-container">
                                <div class="practice-item-display">
                                    <div class="practice-item-text" id="practiceText">Loading...</div>
                                    <div class="practice-item-phonetic" id="practicePhonetic"></div>
                                </div>
                                <div class="flex gap-4 justify-center mt-6">
                                    <div class="glass-pill">
                                        <span class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Language</span>
                                        <span class="font-semibold" id="languageBadge">EN</span>
                                    </div>
                                    <div class="glass-pill">
                                        <span class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Mode</span>
                                        <span class="font-semibold" id="modeBadge">Word</span>
                                    </div>
                                </div>
                                <div class="practice-actions mt-8">
                                    <button id="playReferenceBtn" class="action-btn play-btn">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z" />
                                        </svg>
                                        <span>Listen</span>
                                    </button>
                                    <button id="startRecordingBtn" class="action-btn record-btn">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                        </svg>
                                        <span>Record</span>
                                    </button>
                                    <button id="stopRecordingBtn" class="action-btn stop-btn hidden">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                                        </svg>
                                        <span>Stop</span>
                                    </button>
                                </div>
                            </div>

                            <div id="recordingStatus" class="recording-status hidden">
                                <div class="recording-indicator">
                                    <span class="recording-dot"></span>
                                    <span>Recording...</span>
                                </div>
                            </div>

                            <div id="liveTranscription" class="live-transcription hidden">
                                <div class="live-transcription-label">You're saying:</div>
                                <div class="live-transcription-text" id="liveTranscriptionText">Listening...</div>
                            </div>

                            <div id="feedbackSection" class="feedback-section hidden">
                                <div class="feedback-header">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Pronunciation feedback</p>
                                        <h3 class="text-xl font-semibold">Coach Notes</h3>
                                    </div>
                                    <div class="accuracy-score" id="accuracyScore">0%</div>
                                </div>
                                <div class="feedback-content">
                                    <div class="feedback-item">
                                        <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Your attempt</div>
                                        <div class="text-base font-medium" id="userPronunciation">-</div>
                                    </div>
                                    <div class="feedback-item">
                                        <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Reference</div>
                                        <div class="text-base font-medium" id="correctPronunciation">-</div>
                                    </div>
                                    <div class="feedback-tips mt-4">
                                        <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)] mb-2">Micro tips</div>
                                        <ul id="tipsList" class="list-disc list-inside space-y-1"></ul>
                                    </div>
                                </div>
                            </div>

                        <div class="practice-list-container">
                                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Quest re-roll</p>
                                        <h3 class="text-lg font-bold">Practice Vault</h3>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <button id="nextItemBtn" class="action-btn play-btn" style="padding: 0.5rem 1rem;">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5M4.5 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                            <span>Next Item</span>
                                        </button>
                                        <button id="randomWordBtn" class="action-btn play-btn" style="padding: 0.5rem 1rem;">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            <span>New Quest</span>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-[color:var(--text-muted)] mb-4">Summon a fresh challenge from the AI vault whenever you need something new. Tap any card to practice it instantly.</p>
                                <div id="practiceList" class="practice-list"></div>
                            </div>
                        </div>

                        <aside class="coach-column space-y-6">
                            <div class="coach-card">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.4em] text-[color:var(--text-muted)]">Coach tips</p>
                                        <h3 class="text-lg font-semibold">Power-Ups</h3>
                                    </div>
                                    <div class="glass-pill text-sm">Daily buff active</div>
                                </div>
                                <ul class="space-y-3 text-sm text-[color:var(--text-muted)]">
                                    <li class="flex gap-3">
                                        <span class="text-lg">🎧</span>
                                        <div>
                                            <p class="font-semibold text-[color:var(--text-primary)]">Listen twice, mimic once.</p>
                                            <p>Prime your ear with back-to-back reference plays, then try recording.</p>
                                        </div>
                                    </li>
                                    <li class="flex gap-3">
                                        <span class="text-lg">📝</span>
                                        <div>
                                            <p class="font-semibold text-[color:var(--text-primary)]">Break tricky words.</p>
                                            <p>Clap syllables out loud before speaking to improve rhythm.</p>
                                        </div>
                                    </li>
                                    <li class="flex gap-3">
                                        <span class="text-lg">⚡</span>
                                        <div>
                                            <p class="font-semibold text-[color:var(--text-primary)]">Stack streaks for bonus XP.</p>
                                            <p>Consecutive perfect runs award extra XP and achievements.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="gamified-stats-container">
                                <div class="level-progress-section">
                                    <div class="level-header">
                                        <div class="level-badge">
                                            <div class="level-icon">⭐</div>
                                            <div class="level-info">
                                                <div class="level-label">Level</div>
                                                <div class="level-number" id="userLevel">1</div>
                                            </div>
                                        </div>
                                        <div class="xp-display">
                                            <div class="xp-label">XP</div>
                                            <div class="xp-value" id="userXP">0</div>
                                        </div>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" id="levelProgressBar" style="width: 0%"></div>
                                        </div>
                                        <div class="progress-text">
                                            <span id="currentXP">0</span> / <span id="nextLevelXP">100</span> XP
                                        </div>
                                    </div>
                                </div>

                                <div class="stats-grid">
                                    <div class="stat-card stat-card-primary">
                                        <div class="stat-icon">🎯</div>
                                        <div class="stat-content">
                                            <div class="stat-value" id="totalPracticed">0</div>
                                            <div class="stat-label">Words Practiced</div>
                                        </div>
                                    </div>
                                    <div class="stat-card stat-card-success">
                                        <div class="stat-icon">📊</div>
                                        <div class="stat-content">
                                            <div class="stat-value" id="averageAccuracy">0%</div>
                                            <div class="stat-label">Avg Accuracy</div>
                                        </div>
                                    </div>
                                    <div class="stat-card stat-card-warning">
                                        <div class="stat-icon">🔥</div>
                                        <div class="stat-content">
                                            <div class="stat-value" id="streakCount">0</div>
                                            <div class="stat-label">Day Streak</div>
                                        </div>
                                    </div>
                                    <div class="stat-card stat-card-info">
                                        <div class="stat-icon">🏆</div>
                                        <div class="stat-content">
                                            <div class="stat-value" id="perfectCount">0</div>
                                            <div class="stat-label">Perfect Scores</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </section>
                --}}
            </main>
        </div>
    </div>

    <!-- Q2L AI Assistant Button -->
    <button id="aiAssistantBtn" class="ai-assistant-btn" aria-label="Open Q2L AI Assistant">
        <div class="ai-icon">
            <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="w-7 h-7 object-contain">
        </div>
        <span class="ai-label">Q2L AI Assistant</span>
    </button>

    <!-- AI Chatbot Interface -->
    <div id="aiChatbot" class="ai-chatbot hidden">
        <div class="chatbot-header">
            <div class="flex items-center gap-3">
                <div class="chatbot-avatar">
                    <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="w-full h-full object-contain rounded-full">
                </div>
                <div>
                    <h3 class="chatbot-title">Q2L AI Assistant</h3>
                    <p class="chatbot-status">Online</p>
                </div>
            </div>
            <button id="closeChatbot" class="chatbot-close" aria-label="Close Chatbot">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="chatMessages" class="chatbot-messages">
            <div class="message bot-message">
                <div class="message-avatar">
                    <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="w-full h-full object-contain rounded-full">
                </div>
                <div class="message-content">
                    <p>Hello! I'm your Q2L AI learning assistant. How can I help you today?</p>
                </div>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatInput" placeholder="Type your message..." autocomplete="off">
            <button id="sendMessage" class="send-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="scroll-top-btn hidden" aria-label="Scroll to top">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg>
    </button>

    <!-- Chatbot Backdrop for Mobile -->
    <div id="chatbotBackdrop" class="chatbot-backdrop hidden"></div>

    <!-- Join Class Modal -->
    <div id="joinClassModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 sm:px-6 py-6" data-should-open="{{ $errors->has('join_code') ? 'true' : 'false' }}">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" data-join-class-close></div>
        <div class="relative w-full max-w-md rounded-2xl border border-[color:var(--surface-border)] bg-[color:var(--card-bg)] p-6 shadow-2xl space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-[color:var(--text-soft)]" data-translate="join-class-title">Join a Class</p>
                    <p class="text-sm text-[color:var(--text-muted)]" data-translate="join-class-description">Paste the code from your teacher to unlock their class dashboard.</p>
                </div>
                <button type="button" class="text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)]" data-join-class-close>
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('student.join') }}" class="space-y-4" id="joinClassForm">
                @csrf
                <div class="space-y-2">
                    <label for="join_code" class="text-xs font-semibold tracking-wide text-[color:var(--text-primary)]">Class Code</label>
                    <input type="text" id="join_code" name="join_code" value="{{ old('join_code') }}" required placeholder="e.g. EN10A-XP3" class="w-full rounded-xl border border-[color:var(--surface-border)] bg-transparent px-4 py-3 text-sm text-[color:var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-primary)]" data-translate-placeholder="join-class-placeholder">
                    @error('join_code')
                        <p class="text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full rounded-xl bg-[color:var(--brand-primary)] px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[color:var(--brand-primary)]" data-translate="join-class-button">
                    Join Class
                </button>
            </form>
        </div>
    </div>

    <style>
        .ai-assistant-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 0.875rem 1.5rem;
            border-radius: 3rem;
            background: var(--brand-primary);
            color: white;
            border: none;
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.45), 0 0 0 0 rgba(79, 70, 229, 0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            font-weight: 600;
            font-size: 0.875rem;
            letter-spacing: 0.01em;
            animation: floatBounce 3s ease-in-out infinite;
        }
        .ai-assistant-btn:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 16px 40px rgba(79, 70, 229, 0.5), 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        .ai-assistant-btn:active {
            transform: translateY(-2px) scale(0.98);
        }
        .ai-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 0.375rem;
            backdrop-filter: blur(8px);
            width: 2.5rem;
            height: 2.5rem;
        }
        .ai-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .ai-label {
            white-space: nowrap;
        }
        @keyframes floatBounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }
        @media (max-width: 768px) {
            .ai-assistant-btn {
                bottom: 1.5rem;
                right: 1.5rem;
                padding: 0.75rem 1.25rem;
                font-size: 0.8125rem;
                gap: 0.5rem;
            }
            .ai-icon {
                width: 2rem;
                height: 2rem;
            }
        }
        @media (max-width: 480px) {
            .ai-assistant-btn {
                padding: 0.625rem 1rem;
                font-size: 0.75rem;
                bottom: 1.25rem;
                right: 1rem;
            }
            .ai-label {
                display: none;
            }
            .ai-assistant-btn {
                border-radius: 50%;
                width: 3.25rem;
                height: 3.25rem;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .ai-icon {
                padding: 0;
                background: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .ai-icon svg {
                width: 1.75rem;
                height: 1.75rem;
            }
        }
        /* Chatbot Styles */
        .ai-chatbot {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 380px;
            max-height: 600px;
            background: var(--card-bg);
            border-radius: 1.25rem;
            box-shadow: var(--chat-shadow);
            border: 1px solid var(--chat-border);
            display: flex;
            flex-direction: column;
            z-index: 1001;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: bottom right;
        }
        .ai-chatbot.hidden {
            opacity: 0;
            transform: scale(0.8);
            pointer-events: none;
        }
        .chatbot-header {
            background: var(--chat-header-bg);
            color: var(--text-inverse);
            padding: 1.25rem;
            border-radius: 1.25rem 1.25rem 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chatbot-avatar {
            width: 2.5rem;
            height: 2.5rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            padding: 0.25rem;
            overflow: hidden;
        }
        .chatbot-avatar img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .chatbot-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
        }
        .chatbot-status {
            font-size: 0.75rem;
            opacity: 0.9;
            margin: 0;
        }
        .chatbot-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: var(--text-inverse);
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }
        .chatbot-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background: var(--chat-surface);
        }
        .message {
            display: flex;
            gap: 0.75rem;
            animation: messageSlide 0.3s ease-out;
        }
        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .typing-indicator .message-content {
            background: transparent;
            box-shadow: none;
            color: var(--text-muted);
        }
        .typing-dots {
            display: inline-flex;
            gap: 0.3rem;
            align-items: center;
        }
        .typing-dots span {
            width: 0.4rem;
            height: 0.4rem;
            border-radius: 9999px;
            background: currentColor;
            opacity: 0.25;
            animation: typingBlink 1s ease-in-out infinite;
        }
        .typing-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typingBlink {
            0%, 80%, 100% {
                opacity: 0.25;
                transform: translateY(0);
            }
            40% {
                opacity: 0.9;
                transform: translateY(-2px);
            }
        }
        .message-avatar {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .bot-message .message-avatar {
            background: var(--chat-avatar-bot);
            color: var(--text-inverse);
        }
        .bot-message .message-avatar img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
            padding: 0.25rem;
        }
        .user-message {
            flex-direction: row-reverse;
        }
        .user-message .message-avatar {
            background: var(--chat-avatar-user);
            color: var(--text-inverse);
            font-size: 0.75rem;
            font-weight: 600;
        }
        .message-content {
            background: var(--chat-message-bg);
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            max-width: 75%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            color: var(--chat-message-text);
        }
        .bot-message .message-content {
            background: var(--chat-message-bg);
            color: var(--chat-message-text);
        }
        .user-message .message-content {
            background: var(--chat-user-bg);
            color: var(--chat-user-text);
        }
        .message-content p {
            margin: 0;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .chatbot-input {
            padding: 1rem;
            border-top: 1px solid var(--chat-border);
            display: flex;
            gap: 0.75rem;
            background: var(--card-bg);
            border-radius: 0 0 1.25rem 1.25rem;
        }
        .chatbot-input input {
            flex: 1;
            border: 1px solid var(--input-border);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.2s, background-color 0.2s;
            background: var(--input-bg);
            color: var(--text-primary);
        }
        .chatbot-input input:focus {
            border-color: var(--brand-primary);
            background: var(--input-bg-focus);
            box-shadow: 0 0 0 3px var(--input-ring);
        }
        .send-btn {
            background: var(--chat-user-bg);
            color: var(--chat-user-text);
            border: none;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        }
        .send-btn:active {
            transform: translateY(0);
        }
        @media (max-width: 768px) {
            .ai-chatbot {
                width: calc(100vw - 2rem);
                max-width: 380px;
                bottom: 1.5rem;
                right: 1rem;
            }
        }
        @media (max-width: 480px) {
            .ai-chatbot {
                width: 100vw;
                height: calc(100vh - 3rem);
                bottom: 0;
                right: 0;
                left: 0;
                top: 3rem;
                max-height: calc(100vh - 3rem);
                border-radius: 1rem 1rem 0 0;
                z-index: 1002;
            }
            .chatbot-header {
                border-radius: 1rem 1rem 0 0;
            }
            .chatbot-input {
                border-radius: 0;
            }
        }
        /* Chatbot Backdrop */
        .chatbot-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            transition: opacity 0.3s ease;
        }
        .chatbot-backdrop.hidden {
            opacity: 0;
            pointer-events: none;
        }
        @media (min-width: 481px) {
            .chatbot-backdrop {
                display: none;
            }
        }
        /* Scroll to Top Button */
        .scroll-top-btn {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: #4f46e5;
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 999;
        }
        .scroll-top-btn:hover {
            background: #4338ca;
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.5);
        }
        .scroll-top-btn.hidden {
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px);
        }
        @media (max-width: 768px) {
            .scroll-top-btn {
                bottom: 1.5rem;
                left: 1.5rem;
                width: 2.75rem;
                height: 2.75rem;
            }
        }
        @media (max-width: 480px) {
            .scroll-top-btn {
                bottom: 1.25rem;
                left: 1.25rem;
                width: 2.5rem;
                height: 2.5rem;
            }
            .scroll-top-btn svg {
                width: 1.25rem;
                height: 1.25rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const assignmentsList = document.getElementById('assignmentsList');

            const assignments = [
                {
                    title: 'Assignment 1: Vocabulary Builder',
                    subject: 'English',
                    dueDate: new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() + 5)
                },
                {
                    title: 'Assignment 2: Story Retelling',
                    subject: 'Filipino',
                    dueDate: new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() + 10)
                },
                {
                    title: 'Mid Assignment: Listening Comprehension',
                    subject: 'English',
                    dueDate: new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() + 14)
                }
            ];

            function formatDate(date) {
                return date.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
            }

            function renderAssignments() {
                if (!assignmentsList) return;
                assignmentsList.innerHTML = '';

                assignments
                    .slice()
                    .sort((a, b) => a.dueDate - b.dueDate)
                    .forEach((assignment) => {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'assignment-card';
                        wrapper.innerHTML = `
                            <div>
                                <div class="title">${assignment.title}</div>
                                <div class="subject">${assignment.subject}</div>
                            </div>
                            <div class="text-right">
                                <div class="deadline-label">Deadline</div>
                                <div class="deadline-date">${formatDate(assignment.dueDate)}</div>
                            </div>
                        `;
                        assignmentsList.appendChild(wrapper);
                    });
            }

            renderAssignments();

            // Mobile Menu Toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');

            function toggleMobileMenu() {
                sidebar.classList.toggle('active');
                mobileOverlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            }

            mobileMenuBtn?.addEventListener('click', toggleMobileMenu);
            closeSidebarBtn?.addEventListener('click', toggleMobileMenu);
            mobileOverlay?.addEventListener('click', toggleMobileMenu);

            // Close mobile menu when clicking a nav link
            const navLinks = sidebar?.querySelectorAll('.nav-link');
            navLinks?.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        toggleMobileMenu();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    sidebar?.classList.remove('active');
                    mobileOverlay?.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            // Q2L AI Assistant Chatbot
            const aiAssistantBtn = document.getElementById('aiAssistantBtn');
            const aiChatbot = document.getElementById('aiChatbot');
            const closeChatbot = document.getElementById('closeChatbot');
            const chatInput = document.getElementById('chatInput');
            const sendMessage = document.getElementById('sendMessage');
            const chatMessages = document.getElementById('chatMessages');
            const chatbotBackdrop = document.getElementById('chatbotBackdrop');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const conversation = [];
            let isSending = false;

            const initialBotMessage = chatMessages?.querySelector('.message.bot-message .message-content p')?.textContent?.trim();
            if (initialBotMessage) {
                conversation.push({ role: 'assistant', content: initialBotMessage });
            }

            // Open chatbot
            aiAssistantBtn?.addEventListener('click', () => {
                aiChatbot.classList.remove('hidden');
                chatbotBackdrop.classList.remove('hidden');
                chatInput.focus();
            });

            // Close chatbot
            closeChatbot?.addEventListener('click', () => {
                aiChatbot.classList.add('hidden');
                chatbotBackdrop.classList.add('hidden');
            });

            // Close chatbot when clicking backdrop
            chatbotBackdrop?.addEventListener('click', () => {
                aiChatbot.classList.add('hidden');
                chatbotBackdrop.classList.add('hidden');
            });

            const sanitizeMessage = (value = '') => {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            };

            const formatMessage = (value = '') => {
                const safe = sanitizeMessage(value);
                const paragraphs = safe
                    .split(/\n{2,}/)
                    .map(segment => segment.trim())
                    .filter(Boolean)
                    .map(segment => `<p>${segment.replace(/\n/g, '<br>')}</p>`);

                return paragraphs.length ? paragraphs.join('') : '<p></p>';
            };

            function createMessageElement(text, sender = 'bot') {
                const container = document.createElement('div');
                container.className = `message ${sender === 'user' ? 'user-message' : 'bot-message'}`;

                const avatar = document.createElement('div');
                avatar.className = 'message-avatar';
                if (sender === 'user') {
                    avatar.textContent = '{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}';
                } else {
                    avatar.innerHTML = `
                        <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="w-full h-full object-contain rounded-full">
                    `;
                }

                const content = document.createElement('div');
                content.className = 'message-content';
                content.innerHTML = formatMessage(text);

                container.appendChild(avatar);
                container.appendChild(content);
                return container;
            }

            function createTypingIndicator() {
                const indicator = document.createElement('div');
                indicator.className = 'message bot-message typing-indicator';
                indicator.innerHTML = `
                    <div class="message-avatar">
                        <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="w-full h-full object-contain rounded-full">
                    </div>
                    <div class="message-content">
                        <div class="typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                `;
                return indicator;
            }

            function appendMessageElement(element) {
                chatMessages.appendChild(element);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Send message function
            async function sendUserMessage() {
                if (isSending) {
                    return;
                }

                const message = chatInput.value.trim();
                if (!message) {
                    chatInput.focus();
                    return;
                }

                appendMessageElement(createMessageElement(message, 'user'));
                conversation.push({ role: 'user', content: message });
                chatInput.value = '';

                const typingIndicator = createTypingIndicator();
                appendMessageElement(typingIndicator);

                isSending = true;
                sendMessage.disabled = true;
                chatInput.disabled = true;

                let data;

                try {
                    if (!csrfToken) {
                        throw new Error('Missing CSRF token. Please refresh the page and try again.');
                    }

                    const selectedLanguage = localStorage.getItem('selectedLanguage') || 'en';
                    const payload = {
                        message,
                        conversation: conversation.slice(-10),
                        language: selectedLanguage,
                    };

                    const response = await fetch('{{ route('assistant.chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });

                    data = await response.json().catch(() => ({}));

                    if (typingIndicator.isConnected) {
                        typingIndicator.remove();
                    }

                    if (response.ok && data?.reply) {
                        const reply = data.reply.trim();
                        appendMessageElement(createMessageElement(reply, 'bot'));
                        conversation.push({ role: 'assistant', content: reply });
                    } else {
                        throw new Error(data?.reply || 'The AI assistant is currently unavailable. Please try again shortly.');
                    }
                } catch (error) {
                    if (typingIndicator.isConnected) {
                        typingIndicator.remove();
                    }

                    const fallback = data?.reply
                        ?? (typeof error.message === 'string' && error.message.trim() !== ''
                            ? error.message
                            : "I couldn't connect to our AI service right now. Let's try again soon.");

                    appendMessageElement(createMessageElement(fallback, 'bot'));
                    conversation.push({ role: 'assistant', content: fallback });
                } finally {
                    isSending = false;
                    sendMessage.disabled = false;
                    chatInput.disabled = false;
                    chatInput.focus();
                }
            }

            // Send message on button click
            sendMessage?.addEventListener('click', () => {
                void sendUserMessage();
            });

            // Send message on Enter key
            chatInput?.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    void sendUserMessage();
                }
            });

            // Scroll to Top Button
            const scrollToTopBtn = document.getElementById('scrollToTop');

            // Show/hide button based on scroll position
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.classList.remove('hidden');
                } else {
                    scrollToTopBtn.classList.add('hidden');
                }
            });

            // Scroll to top when clicked
            scrollToTopBtn?.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

        // Join class modal controls
        const joinClassModal = document.getElementById('joinClassModal');
        const joinClassOpeners = document.querySelectorAll('[data-join-class-open]');
        const joinClassClosers = document.querySelectorAll('[data-join-class-close]');

        const openJoinModal = () => {
            if (!joinClassModal) return;
            joinClassModal.classList.remove('hidden');
            joinClassModal.classList.add('flex');
            const input = joinClassModal.querySelector('input[name="join_code"]');
            if (input) {
                setTimeout(() => input.focus(), 100);
            }
        };

        const closeJoinModal = () => {
            if (!joinClassModal) return;
            joinClassModal.classList.add('hidden');
            joinClassModal.classList.remove('flex');
        };

        joinClassOpeners.forEach(btn => btn.addEventListener('click', openJoinModal));
        joinClassClosers.forEach(btn => btn.addEventListener('click', closeJoinModal));

        if (joinClassModal?.dataset.shouldOpen === 'true') {
            openJoinModal();
        }

        joinClassModal?.addEventListener('click', (event) => {
            if (event.target === joinClassModal) {
                closeJoinModal();
            }
        });

        // Live announcements polling

            // Translation functionality
            window.changeLanguage = function(lang) {
                    localStorage.setItem('selectedLanguage', lang);
                    const langText = lang === 'fil' ? 'Filipino' : lang === 'bis' ? 'Bisaya' : 'English';
                    document.querySelectorAll('.translation-current-lang').forEach(el => {
                        el.textContent = langText;
                    });
                    
                    const translations = {
                        'en': {
                            'dashboard-title': 'Dashboard',
                            'nav-profile': 'My Profile',
                            'nav-courses': 'Enrolled Courses',
                            'nav-quiz': 'My Quiz Attempts',
                            'nav-calendar': 'Calendar',
                            'nav-my-courses': 'My Courses',
                            'nav-announcements': 'Announcements',
                            'nav-quiz-attempts': 'Quiz Attempts',
                            'nav-assignments': 'Assignments',
                            'nav-settings': 'Settings',
                            'nav-logout': 'Logout',
                            'nav-join-class': 'Join a Class',
                            'greeting': 'Hello',
                            'calendar-title': 'Calendar',
                            'assignments-title': 'Assignments',
                            'view-all': 'View all',
                            'progress-title': 'Your Progress',
                            'leaderboard-title': 'Leaderboard',
                            'leaderboard-subtitle': 'Top Students',
                            'nav-pronunciation': 'AI Pronunciation Tutor',
                            'pronunciation-title': 'AI Pronunciation Tutor',
                            'pronunciation-subtitle': 'Practice and improve your pronunciation in English and Filipino',
                            'pronunciation-select-language': 'Select Language',
                            'pronunciation-mode-word': 'Word Practice',
                            'pronunciation-mode-phrase': 'Phrase Practice',
                            'pronunciation-mode-sentence': 'Sentence Practice',
                            'pronunciation-listen': 'Listen',
                            'pronunciation-record': 'Record',
                            'pronunciation-stop': 'Stop',
                            'pronunciation-recording': 'Recording...',
                            'pronunciation-feedback': 'Pronunciation Feedback',
                            'pronunciation-your-pronunciation': 'Your Pronunciation:',
                            'pronunciation-correct-pronunciation': 'Correct Pronunciation:',
                            'pronunciation-tips': 'Tips for Improvement:',
                            'pronunciation-practice-items': 'Practice Items',
                            'pronunciation-next': 'Next Item',
                            'pronunciation-total-practiced': 'Total Practiced',
                            'pronunciation-average-accuracy': 'Average Accuracy',
                            'pronunciation-streak': 'Day Streak',
                            'your-classes-title': 'Your Classes',
                            'your-classes-desc': 'Keep track of every portal you’ve unlocked.',
                            'your-classes-empty': 'You haven’t joined a class yet. Enter your teacher’s code to start.',
                            'your-classes-cta': 'Keep this code handy in case a classmate needs it.',
                        'announcements-title': 'Class Announcements',
                        'announcements-desc': 'Stay updated with every ping from your teachers.',
                        'announcements-empty': 'No announcements yet. Once your teacher sends one, you’ll see it here.',
                            'join-class-title': 'Join a Class',
                            'join-class-description': 'Paste the code from your teacher to unlock their class dashboard.',
                            'join-class-placeholder': 'e.g. EN10A-XP3',
                            'join-class-button': 'Join Class'
                        },
                        'fil': {
                            'dashboard-title': 'Dashboard',
                            'nav-profile': 'Aking Profile',
                            'nav-courses': 'Mga In-enroll na Kurso',
                            'nav-quiz': 'Aking Quiz Attempts',
                            'nav-calendar': 'Kalendaryo',
                            'nav-my-courses': 'Aking Mga Kurso',
                            'nav-announcements': 'Mga Anunsyo',
                            'nav-quiz-attempts': 'Quiz Attempts',
                            'nav-assignments': 'Mga Takdang-aralin',
                            'nav-settings': 'Mga Setting',
                            'nav-logout': 'Mag-logout',
                            'nav-join-class': 'Apil sa Klase',
                            'nav-join-class': 'Sumali sa Klase',
                            'greeting': 'Kumusta',
                            'calendar-title': 'Kalendaryo',
                            'assignments-title': 'Mga Takdang-aralin',
                            'view-all': 'Tingnan lahat',
                            'progress-title': 'Iyong Pag-unlad',
                            'leaderboard-title': 'Leaderboard',
                            'leaderboard-subtitle': 'Nangungunang Mag-aaral',
                            'nav-pronunciation': 'AI Pronunciation Tutor',
                            'pronunciation-title': 'AI Pronunciation Tutor',
                            'pronunciation-subtitle': 'Magsanay at pagbutihin ang iyong pagbigkas sa Ingles at Filipino',
                            'pronunciation-select-language': 'Pumili ng Wika',
                            'pronunciation-mode-word': 'Pagsasanay sa Salita',
                            'pronunciation-mode-phrase': 'Pagsasanay sa Parirala',
                            'pronunciation-mode-sentence': 'Pagsasanay sa Pangungusap',
                            'pronunciation-listen': 'Makinig',
                            'pronunciation-record': 'I-record',
                            'pronunciation-stop': 'Itigil',
                            'pronunciation-recording': 'Nagre-record...',
                            'pronunciation-feedback': 'Feedback sa Pagbigkas',
                            'pronunciation-your-pronunciation': 'Iyong Pagbigkas:',
                            'pronunciation-correct-pronunciation': 'Tamang Pagbigkas:',
                            'pronunciation-tips': 'Mga Tip para sa Pagpapabuti:',
                            'pronunciation-practice-items': 'Mga Item sa Pagsasanay',
                            'pronunciation-next': 'Susunod na Item',
                            'pronunciation-total-practiced': 'Kabuuang Nagsanay',
                            'pronunciation-average-accuracy': 'Average na Katumpakan',
                            'pronunciation-streak': 'Araw na Streak',
                            'your-classes-title': 'Iyong Mga Klase',
                            'your-classes-desc': 'Subaybayan ang bawat portal na nabuksan mo.',
                            'your-classes-empty': 'Wala ka pang sinasalihang klase. Ilagay ang code mula sa iyong guro upang magsimula.',
                            'your-classes-cta': 'Itabi ang code sakaling kailanganin ito ng kaklase.',
                        'announcements-title': 'Mga Anunsyo sa Klase',
                        'announcements-desc': 'Maging updated sa bawat ping ng iyong guro.',
                        'announcements-empty': 'Wala pang anunsyo. Makikita mo rito kapag may ipinadala na.',
                            'join-class-title': 'Sumali sa Klase',
                            'join-class-description': 'Ilagay ang code mula sa iyong guro upang mabuksan ang kanilang dashboard.',
                            'join-class-placeholder': 'hal. EN10A-XP3',
                            'join-class-button': 'Sumali sa Klase'
                        },
                        'bis': {
                            'dashboard-title': 'Dashboard',
                            'nav-profile': 'Akong Profile',
                            'nav-courses': 'Mga Na-enroll nga Kurso',
                            'nav-quiz': 'Akong Quiz Attempts',
                            'nav-calendar': 'Kalendaryo',
                            'nav-my-courses': 'Akong Mga Kurso',
                            'nav-announcements': 'Mga Anunsyo',
                            'nav-quiz-attempts': 'Quiz Attempts',
                            'nav-assignments': 'Mga Takdang-aralin',
                            'nav-settings': 'Mga Setting',
                            'nav-logout': 'Mag-logout',
                            'greeting': 'Kumusta',
                            'calendar-title': 'Kalendaryo',
                            'assignments-title': 'Mga Takdang-aralin',
                            'view-all': 'Tan-awa tanan',
                            'progress-title': 'Imong Pag-uswag',
                            'leaderboard-title': 'Leaderboard',
                            'leaderboard-subtitle': 'Nangungunang Estudyante',
                            'nav-pronunciation': 'AI Pronunciation Tutor',
                            'pronunciation-title': 'AI Pronunciation Tutor',
                            'pronunciation-subtitle': 'Magsanay ug pag-ayo sa imong pagbigkas sa English ug Filipino',
                            'pronunciation-select-language': 'Pilia ang Pinulongan',
                            'pronunciation-mode-word': 'Pagsanay sa Pulong',
                            'pronunciation-mode-phrase': 'Pagsanay sa Parirala',
                            'pronunciation-mode-sentence': 'Pagsanay sa Pangungusap',
                            'pronunciation-listen': 'Paminaw',
                            'pronunciation-record': 'I-record',
                            'pronunciation-stop': 'Hunong',
                            'pronunciation-recording': 'Nagre-record...',
                            'pronunciation-feedback': 'Feedback sa Pagbigkas',
                            'pronunciation-your-pronunciation': 'Imong Pagbigkas:',
                            'pronunciation-correct-pronunciation': 'Tama nga Pagbigkas:',
                            'pronunciation-tips': 'Mga Tip para sa Pag-ayo:',
                            'pronunciation-practice-items': 'Mga Item sa Pagsanay',
                            'pronunciation-next': 'Sunod nga Item',
                            'pronunciation-total-practiced': 'Kinatibuk-ang Nagsanay',
                            'pronunciation-average-accuracy': 'Average nga Katukma',
                            'pronunciation-streak': 'Adlaw nga Streak',
                            'your-classes-title': 'Imong mga Klase',
                            'your-classes-desc': 'Bantayi ang tanang portal nga imong naablihan.',
                            'your-classes-empty': 'Wala pa kay nasudlan nga klase. Ibutang ang code gikan sa imong magtutudlo aron magsugod.',
                            'your-classes-cta': 'I-keep ang code kung kinahanglan sa imong kauban.',
                        'announcements-title': 'Mga Anunsyo sa Klase',
                        'announcements-desc': 'Magpabilin nga updated sa tanang ping sa imong magtutudlo.',
                        'announcements-empty': 'Wala pay anunsyo. Makita nimo dinhi kon adunay ipasa.',
                            'join-class-title': 'Apil sa Klase',
                            'join-class-description': 'Ibutang ang code gikan sa imong magtutudlo aron maabli ang ilang dashboard.',
                            'join-class-placeholder': 'pananglitan EN10A-XP3',
                            'join-class-button': 'Apil sa Klase'
                        }
                    };
                    
                    const langData = translations[lang] || translations['en'];
                    document.querySelectorAll('[data-translate]').forEach(el => {
                        const key = el.getAttribute('data-translate');
                        if (langData[key]) {
                            el.textContent = langData[key];
                        }
                    });
                    document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
                        const key = el.getAttribute('data-translate-placeholder');
                        if (langData[key]) {
                            el.setAttribute('placeholder', langData[key]);
                        }
                    });
            }

            // Load saved language on page load
            const savedLang = localStorage.getItem('selectedLanguage') || 'en';
            const langText = savedLang === 'fil' ? 'Filipino' : savedLang === 'bis' ? 'Bisaya' : 'English';
            document.querySelectorAll('.translation-current-lang').forEach(el => {
                el.textContent = langText;
            });
            if (savedLang !== 'en') {
                window.changeLanguage(savedLang);
            }

            // Pronunciation Tutor functionality
            (function() {
                const pronunciationLanguageSelect = document.getElementById('pronunciationLanguage');
                if (!pronunciationLanguageSelect) {
                    return;
                }
                // Practice data for English and Filipino
                const practiceData = {
                    'en-US': {
                        word: [
                            { text: 'Hello', phonetic: '/həˈloʊ/', tips: ['Focus on the "h" sound at the beginning', 'The "o" should be long and clear'] },
                            { text: 'World', phonetic: '/wɜːrld/', tips: ['Pronounce the "r" clearly', 'The "ld" should be soft'] },
                            { text: 'Beautiful', phonetic: '/ˈbjuːtɪfəl/', tips: ['Emphasize the first syllable', 'The "t" is soft, almost like "d"'] },
                            { text: 'Pronunciation', phonetic: '/prəˌnʌnsiˈeɪʃən/', tips: ['Break it into syllables: pro-nun-ci-a-tion', 'Stress the third syllable'] },
                            { text: 'Education', phonetic: '/ˌedʒuˈkeɪʃən/', tips: ['The "e" sounds like "eh"', 'Stress the second syllable'] },
                            { text: 'Computer', phonetic: '/kəmˈpjuːtər/', tips: ['The "u" sounds like "you"', 'Stress the second syllable'] },
                            { text: 'Language', phonetic: '/ˈlæŋɡwɪdʒ/', tips: ['The "ng" is one sound', 'The "g" is soft'] },
                            { text: 'Practice', phonetic: '/ˈpræktɪs/', tips: ['The "a" sounds like "ah"', 'Stress the first syllable'] }
                        ],
                        phrase: [
                            { text: 'How are you?', phonetic: '/haʊ ɑːr juː/', tips: ['Connect "how" and "are" smoothly', 'The "you" should be clear'] },
                            { text: 'Thank you very much', phonetic: '/θæŋk juː ˈveri mʌtʃ/', tips: ['The "th" in "thank" is soft', 'Emphasize "very"'] },
                            { text: 'Nice to meet you', phonetic: '/naɪs tuː miːt juː/', tips: ['The "t" in "to" is soft', 'Connect words smoothly'] },
                            { text: 'I love learning', phonetic: '/aɪ lʌv ˈlɜːrnɪŋ/', tips: ['The "I" sounds like "eye"', 'Stress "learning"'] },
                            { text: 'Good morning', phonetic: '/ɡʊd ˈmɔːrnɪŋ/', tips: ['The "oo" in "good" is short', 'Stress "morning"'] }
                        ],
                        sentence: [
                            { text: 'I am learning English pronunciation.', phonetic: '/aɪ æm ˈlɜːrnɪŋ ˈɪŋɡlɪʃ prəˌnʌnsiˈeɪʃən/', tips: ['Speak slowly and clearly', 'Pause between words if needed'] },
                            { text: 'Practice makes perfect.', phonetic: '/ˈpræktɪs meɪks ˈpɜːrfɪkt/', tips: ['Emphasize "practice" and "perfect"', 'Keep a steady rhythm'] },
                            { text: 'The more you practice, the better you become.', phonetic: '/ðə mɔːr juː ˈpræktɪs ðə ˈbetər juː bɪˈkʌm/', tips: ['Use rising and falling intonation', 'Connect words naturally'] }
                        ]
                    },
                    'fil-PH': {
                        word: [
                            { text: 'Kumusta', phonetic: '/kuːˈmuːstɑː/', tips: ['The "ku" sounds like "coo"', 'Stress the second syllable'] },
                            { text: 'Magandang', phonetic: '/mɑːɡɑːnˈdɑːŋ/', tips: ['The "ng" is one sound', 'Stress the last syllable'] },
                            { text: 'Araw', phonetic: '/ˈɑːrɑːw/', tips: ['The "a" sounds like "ah"', 'The "w" is soft'] },
                            { text: 'Salamat', phonetic: '/sɑːˈlɑːmɑːt/', tips: ['All "a" sounds are long', 'Stress the second syllable'] },
                            { text: 'Paalam', phonetic: '/pɑːˈɑːlɑːm/', tips: ['The "aa" is long', 'Stress the second syllable'] },
                            { text: 'Mahal', phonetic: '/mɑːˈhɑːl/', tips: ['The "h" is pronounced', 'Stress the second syllable'] },
                            { text: 'Kaibigan', phonetic: '/kɑːɪˈbiːɡɑːn/', tips: ['Break into syllables: ka-i-bi-gan', 'Stress the third syllable'] },
                            { text: 'Pag-aaral', phonetic: '/pɑːɡ ɑːˈɑːrɑːl/', tips: ['There is a glottal stop between "pag" and "aaral"', 'Stress "aaral"'] }
                        ],
                        phrase: [
                            { text: 'Kumusta ka?', phonetic: '/kuːˈmuːstɑː kɑː/', tips: ['Connect "kumusta" and "ka" smoothly', 'The "ka" is short'] },
                            { text: 'Magandang umaga', phonetic: '/mɑːɡɑːnˈdɑːŋ uːˈmɑːɡɑː/', tips: ['The "ng" connects to "umaga"', 'Stress both words'] },
                            { text: 'Maraming salamat', phonetic: '/mɑːˈrɑːmɪŋ sɑːˈlɑːmɑːt/', tips: ['The "ng" in "maraming" is clear', 'Both words are stressed'] },
                            { text: 'Ingat ka', phonetic: '/ɪˈŋɑːt kɑː/', tips: ['The "ng" is pronounced', 'Short and clear'] },
                            { text: 'Mahal kita', phonetic: '/mɑːˈhɑːl ˈkiːtɑː/', tips: ['Both words are stressed', 'The "h" in "mahal" is clear'] }
                        ],
                        sentence: [
                            { text: 'Ako ay nag-aaral ng Filipino.', phonetic: '/ˈɑːkoː ɑːj nɑːɡ ɑːˈɑːrɑːl nɑːŋ fiːˈliːpiːnoː/', tips: ['Speak slowly', 'Pause between phrases'] },
                            { text: 'Mahal ko ang aking pamilya.', phonetic: '/mɑːˈhɑːl koː ɑːŋ ˈɑːkɪŋ pɑːˈmiːljɑː/', tips: ['Emphasize "mahal" and "pamilya"', 'Use natural rhythm'] },
                            { text: 'Ang pag-aaral ay mahalaga.', phonetic: '/ɑːŋ pɑːɡ ɑːˈɑːrɑːl ɑːj mɑːhɑːˈlɑːɡɑː/', tips: ['Use rising intonation', 'Connect words smoothly'] }
                        ]
                    }
                };

                // State management
                let currentMode = 'word';
                let currentLanguage = 'en-US';
                let currentIndex = 0;
                let recognition = null;
                let synthesis = window.speechSynthesis;
                let isRecording = false;
                let practiceStats = {
                    totalPracticed: 0,
                    totalAccuracy: 0,
                    attempts: 0,
                    streak: 0,
                    perfectCount: 0,
                    level: 1,
                    xp: 0
                };
                
                // Language code mapping for different APIs
                const languageCodes = {
                    'en-US': {
                        speechRecognition: 'en-US',
                        speechSynthesis: 'en-US',
                        practiceData: 'en-US'
                    },
                    'tl-PH': {
                        speechRecognition: 'tl-PH', // Tagalog for speech recognition
                        speechSynthesis: 'tl-PH',   // Tagalog for speech synthesis
                        practiceData: 'fil-PH'      // Use Filipino practice data
                    }
                };

                // DOM elements
                const modeButtons = document.querySelectorAll('.practice-mode-btn');
                const languageSelect = pronunciationLanguageSelect;
                const practiceText = document.getElementById('practiceText');
                const practicePhonetic = document.getElementById('practicePhonetic');
                const playReferenceBtn = document.getElementById('playReferenceBtn');
                const startRecordingBtn = document.getElementById('startRecordingBtn');
                const stopRecordingBtn = document.getElementById('stopRecordingBtn');
                const recordingStatus = document.getElementById('recordingStatus');
                const feedbackSection = document.getElementById('feedbackSection');
                const userPronunciation = document.getElementById('userPronunciation');
                const correctPronunciation = document.getElementById('correctPronunciation');
                const accuracyScore = document.getElementById('accuracyScore');
                const tipsList = document.getElementById('tipsList');
                const practiceList = document.getElementById('practiceList');
                const nextItemBtn = document.getElementById('nextItemBtn');
                const randomWordBtn = document.getElementById('randomWordBtn');
                const totalPracticedEl = document.getElementById('totalPracticed');
                const averageAccuracyEl = document.getElementById('averageAccuracy');
                const streakCountEl = document.getElementById('streakCount');
                const languageBadge = document.getElementById('languageBadge');
                const modeBadge = document.getElementById('modeBadge');
                const userLevelEl = document.getElementById('userLevel');
                const userXPEl = document.getElementById('userXP');
                const levelProgressBar = document.getElementById('levelProgressBar');
                const currentXPEl = document.getElementById('currentXP');
                const nextLevelXPEl = document.getElementById('nextLevelXP');
                const perfectCountEl = document.getElementById('perfectCount');

                // Initialize Web Speech API
                function initSpeechRecognition() {
                    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                        console.warn('Speech recognition not supported');
                        startRecordingBtn.disabled = true;
                        startRecordingBtn.title = 'Speech recognition not supported in your browser';
                        return null;
                    }

                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                    const recognition = new SpeechRecognition();
                    recognition.continuous = false;
                    recognition.interimResults = false;
                    
                    // Use correct language code for speech recognition
                    const langConfig = languageCodes[currentLanguage] || languageCodes['en-US'];
                    recognition.lang = langConfig.speechRecognition;

                    recognition.onstart = () => {
                        console.log('Recording started');
                        isRecording = true;
                        startRecordingBtn.classList.add('hidden');
                        stopRecordingBtn.classList.remove('hidden');
                        recordingStatus.classList.remove('hidden');
                    };

                    recognition.onresult = (event) => {
                        console.log('Speech recognition result received');
                        if (event.results && event.results.length > 0 && event.results[0].length > 0) {
                            const transcript = event.results[0][0].transcript;
                            console.log('Transcript:', transcript);
                            analyzePronunciation(transcript);
                        } else {
                            console.warn('No transcript in results');
                            stopRecording();
                            alert('No speech detected. Please try again.');
                        }
                    };

                    recognition.onerror = (event) => {
                        console.error('Speech recognition error:', event.error);
                        
                        let errorMessage = 'Speech recognition error. ';
                        let shouldRecreate = false;
                        
                        switch(event.error) {
                            case 'no-speech':
                                errorMessage = 'No speech detected. Please try speaking again.';
                                break;
                            case 'audio-capture':
                                errorMessage = 'No microphone found. Please check your microphone.';
                                break;
                            case 'not-allowed':
                                errorMessage = 'Microphone permission denied. Please allow microphone access in your browser settings.';
                                break;
                            case 'network':
                                errorMessage = 'Network connection error.\n\nThe speech recognition service needs to connect to Google\'s servers. Even with internet, this might fail if:\n\n• Your firewall or antivirus is blocking it\n• Your network administrator has restrictions\n• The service is temporarily unavailable\n\nTry:\n1. Refreshing the page\n2. Checking firewall/antivirus settings\n3. Using Chrome or Edge browser\n4. Trying again in a few moments';
                                shouldRecreate = true;
                                break;
                            case 'aborted':
                                // User stopped recording, don't show error
                                return;
                            case 'service-not-allowed':
                                errorMessage = 'Speech recognition service is not available. Please try a different browser (Chrome or Edge recommended).';
                                shouldRecreate = true;
                                break;
                            default:
                                errorMessage = 'Speech recognition error: ' + event.error + '\n\nPlease try again. If it persists, refresh the page.';
                                shouldRecreate = true;
                        }
                        
                        stopRecording();
                        
                        // Recreate recognition instance if it's a network/service error
                        // Do this after a short delay to ensure cleanup
                        if (shouldRecreate) {
                            setTimeout(() => {
                                console.log('Recreating recognition instance due to error');
                                recognition = initSpeechRecognition();
                            }, 100);
                        }
                        
                        alert(errorMessage);
                    };

                    recognition.onend = () => {
                        console.log('Recording ended');
                        if (isRecording) {
                            // Only stop if we're still in recording state
                            // This prevents double-stopping
                            stopRecording();
                        }
                    };

                    return recognition;
                }

                // Initialize on page load
                recognition = initSpeechRecognition();

                // Load practice stats from server (per user account)
                function loadStats() {
                    fetch('/student/pronunciation-stats', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        practiceStats = {
                            totalPracticed: data.totalPracticed || 0,
                            totalAccuracy: data.totalAccuracy || 0,
                            attempts: data.attempts || 0,
                            streak: data.streak || 0,
                            perfectCount: data.perfectCount || 0,
                            level: data.level || 1,
                            xp: data.xp || 0
                        };
                        updateStatsDisplay();
                    })
                    .catch(error => {
                        console.error('Error loading pronunciation stats:', error);
                        // Fallback to default values
                        practiceStats = {
                            totalPracticed: 0,
                            totalAccuracy: 0,
                            attempts: 0,
                            streak: 0,
                            perfectCount: 0,
                            level: 1,
                            xp: 0
                        };
                        updateStatsDisplay();
                    });
                }

                // Save practice stats to server (per user account)
                function saveStats() {
                    fetch('/student/pronunciation-stats', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            totalPracticed: practiceStats.totalPracticed || 0,
                            totalAccuracy: practiceStats.totalAccuracy || 0,
                            attempts: practiceStats.attempts || 0,
                            streak: practiceStats.streak || 0,
                            perfectCount: practiceStats.perfectCount || 0,
                            level: practiceStats.level || 1,
                            xp: practiceStats.xp || 0
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Pronunciation stats saved successfully');
                        updateStatsDisplay();
                    })
                    .catch(error => {
                        console.error('Error saving pronunciation stats:', error);
                        // Still update display even if save fails
                        updateStatsDisplay();
                    });
                }

                // Update stats display
                function updateStatsDisplay() {
                    totalPracticedEl.textContent = practiceStats.totalPracticed || 0;
                    const avg = practiceStats.attempts > 0 
                        ? Math.round(practiceStats.totalAccuracy / practiceStats.attempts) 
                        : 0;
                    averageAccuracyEl.textContent = avg + '%';
                    streakCountEl.textContent = practiceStats.streak || 0;
                    perfectCountEl.textContent = practiceStats.perfectCount || 0;
                    
                    // Update gamified stats
                    if (userLevelEl) userLevelEl.textContent = practiceStats.level || 1;
                    if (userXPEl) userXPEl.textContent = practiceStats.xp || 0;
                    
                    // Calculate level progress (simplified: 100 XP per level)
                    const xpForNextLevel = (practiceStats.level || 1) * 100;
                    const currentLevelXP = (practiceStats.xp || 0) % 100;
                    const progressPercent = (currentLevelXP / 100) * 100;
                    
                    if (levelProgressBar) levelProgressBar.style.width = progressPercent + '%';
                    if (currentXPEl) currentXPEl.textContent = currentLevelXP;
                    if (nextLevelXPEl) nextLevelXPEl.textContent = xpForNextLevel;
                }

                // Get current practice items
                function getCurrentItems() {
                    const dataKey = languageCodes[currentLanguage]?.practiceData || currentLanguage;
                    return practiceData[dataKey]?.[currentMode] || practiceData['en-US'].word;
                }

                // Load current practice item
                function loadPracticeItem(index) {
                    const items = getCurrentItems();
                    if (items.length === 0) return;

                    currentIndex = index % items.length;
                    const item = items[currentIndex];
                    
                    practiceText.textContent = item.text;
                    practicePhonetic.textContent = item.phonetic;
                    
                    // Update badges
                    if (languageBadge) {
                        languageBadge.textContent = currentLanguage === 'en-US' ? 'EN' : 'FIL';
                    }
                    if (modeBadge) {
                        const modeNames = { word: 'Word', phrase: 'Phrase', sentence: 'Story' };
                        modeBadge.textContent = modeNames[currentMode] || 'Word';
                    }
                    
                    // Update practice list
                    renderPracticeList();
                }

                // Render practice list
                function renderPracticeList() {
                    const items = getCurrentItems();
                    practiceList.innerHTML = '';
                    
                    items.forEach((item, index) => {
                        const div = document.createElement('div');
                        div.className = `practice-item ${index === currentIndex ? 'active' : ''}`;
                        div.textContent = item.text;
                        div.addEventListener('click', () => {
                            loadPracticeItem(index);
                        });
                        practiceList.appendChild(div);
                    });
                }

                // Get appropriate voice for language
                function getVoiceForLanguage(lang) {
                    if (!synthesis) return null;
                    
                    const voices = synthesis.getVoices();
                    if (!voices || voices.length === 0) return null;
                    
                    const langConfig = languageCodes[lang] || languageCodes['en-US'];
                    const targetLang = langConfig.speechSynthesis.toLowerCase();
                    
                    // Try to find a voice that matches the language
                    // For Filipino, prioritize Tagalog (tl) voices specifically
                    // For English, look for 'en-us' or 'en'
                    let exactTagalogVoice = null;      // tl-PH, tl specifically
                    let tagalogVoice = null;            // Any voice with 'tl' in language code
                    let filipinoVoice = null;           // Voice with 'fil' or Filipino in name
                    let fallbackVoice = null;
                    
                    for (let voice of voices) {
                        const voiceLang = voice.lang.toLowerCase();
                        const voiceName = voice.name.toLowerCase();
                        
                        if (targetLang.includes('tl') || targetLang.includes('fil')) {
                            // Priority 1: Exact Tagalog language match (tl-PH, tl)
                            // Tagalog is the primary language code for Filipino
                            if (voiceLang === 'tl-ph' || voiceLang === 'tl') {
                                exactTagalogVoice = voice;
                                // Continue searching to find the best Tagalog voice
                            }
                            
                            // Priority 2: Any Tagalog language code (contains 'tl')
                            if (!tagalogVoice && voiceLang.includes('tl')) {
                                tagalogVoice = voice;
                            }
                            
                            // Priority 3: Filipino language code or name
                            if (!filipinoVoice && (
                                voiceLang.includes('fil') || 
                                voiceLang.includes('ph') ||
                                voiceName.includes('tagalog') ||
                                voiceName.includes('filipino') ||
                                voiceName.includes('philippine')
                            )) {
                                filipinoVoice = voice;
                            }
                            
                            // Fallback
                            if (!fallbackVoice) {
                                fallbackVoice = voice;
                            }
                        } else {
                            // Look for English (US) voices
                            if (voiceLang === 'en-us' || voiceLang === 'en') {
                                if (voiceLang === 'en-us') {
                                    exactTagalogVoice = voice; // Reuse variable for English exact match
                                    break;
                                }
                                if (!tagalogVoice) { // Reuse variable for English preferred
                                    tagalogVoice = voice;
                                }
                            }
                            // Fallback to any English voice
                            if (!fallbackVoice && voiceLang.includes('en')) {
                                fallbackVoice = voice;
                            }
                        }
                    }
                    
                    // For Filipino: Return in priority order - Tagalog voices first
                    if (targetLang.includes('tl') || targetLang.includes('fil')) {
                        return exactTagalogVoice || tagalogVoice || filipinoVoice || fallbackVoice || voices[0];
                    } else {
                        // For English
                        return exactTagalogVoice || tagalogVoice || fallbackVoice || voices[0];
                    }
                }

                // Play reference pronunciation using Google Translate TTS for Filipino
                function playReference() {
                    const text = practiceText.textContent;
                    if (!text) return;

                    const langConfig = languageCodes[currentLanguage] || languageCodes['en-US'];
                    
                    // For Filipino/Tagalog, use Google Translate TTS for native Tagalog pronunciation
                    if (currentLanguage === 'tl-PH') {
                        // Use Google Translate TTS API for fluent Tagalog pronunciation
                        // Split text if it's too long (Google TTS has ~200 char limit per request)
                        const maxLength = 200;
                        if (text.length > maxLength) {
                            // For long texts, split into sentences and play sequentially
                            const sentences = text.match(/[^\.!\?]+[\.!\?]+/g) || [text];
                            playTagalogSequentially(sentences, 0);
                        } else {
                            playTagalogText(text);
                        }
                        console.log('✓ Using Google Translate TTS for fluent Tagalog pronunciation');
                        return;
                    }
                    
                    // For English, use Web Speech API
                    playWithWebSpeech(text, langConfig);
                }
                
                // Store current audio to prevent multiple plays
                let currentAudio = null;
                
                // Play Tagalog text using backend TTS API (avoids CORS issues)
                function playTagalogText(text) {
                    // Stop any currently playing audio
                    if (currentAudio) {
                        currentAudio.pause();
                        currentAudio = null;
                    }
                    
                    console.log('Playing Tagalog text with native voice via backend:', text);
                    
                    // Use backend route to get TTS audio (avoids CORS issues)
                    const ttsUrl = `/api/tts/speak?text=${encodeURIComponent(text)}&lang=tl`;
                    
                    // Create audio element
                    const audio = new Audio(ttsUrl);
                    currentAudio = audio;
                    
                    // Handle successful load
                    audio.addEventListener('loadeddata', () => {
                        console.log('✓ Native Tagalog audio loaded from backend');
                    });
                    
                    audio.addEventListener('play', () => {
                        console.log('✓ Playing with native Tagalog voice');
                    });
                    
                    // Handle errors
                    audio.addEventListener('error', (e) => {
                        console.error('Backend TTS error:', e);
                        console.log('Trying fallback method...');
                        // Fallback to direct Google TTS (might work in some browsers)
                        tryDirectGoogleTTS(text);
                    });
                    
                    // Play the audio
                    audio.play().catch(error => {
                        console.error('Error playing audio:', error);
                        console.log('Trying fallback method...');
                        tryDirectGoogleTTS(text);
                    });
                    
                    // Clean up when done
                    audio.addEventListener('ended', () => {
                        currentAudio = null;
                    });
                }
                
                // Fallback: Try direct Google TTS (for browsers that allow it)
                function tryDirectGoogleTTS(text) {
                    const encodedText = encodeURIComponent(text);
                    const ttsUrl = `https://translate.google.com/translate_tts?ie=UTF-8&tl=tl&client=gtx&q=${encodedText}`;
                    
                    const audio = new Audio(ttsUrl);
                    currentAudio = audio;
                    
                    audio.addEventListener('error', () => {
                        console.error('All TTS methods failed');
                        alert('Unable to play Tagalog pronunciation. Please try again or refresh the page.');
                    });
                    
                    audio.play().catch(() => {
                        alert('Unable to play Tagalog pronunciation. Please try again or refresh the page.');
                    });
                }
                
                
                // Play Tagalog sentences sequentially with native voice using backend
                function playTagalogSequentially(sentences, index) {
                    if (index >= sentences.length) {
                        currentAudio = null;
                        return;
                    }
                    
                    const sentence = sentences[index].trim();
                    if (!sentence) {
                        playTagalogSequentially(sentences, index + 1);
                        return;
                    }
                    
                    // Use backend TTS route for each sentence
                    const ttsUrl = `/api/tts/speak?text=${encodeURIComponent(sentence)}&lang=tl`;
                    
                    const audio = new Audio(ttsUrl);
                    currentAudio = audio;
                    
                    audio.addEventListener('ended', () => {
                        // Play next sentence when current one ends
                        playTagalogSequentially(sentences, index + 1);
                    });
                    
                    audio.addEventListener('error', () => {
                        // If error, skip to next sentence
                        console.warn('Error playing sentence, skipping to next');
                        playTagalogSequentially(sentences, index + 1);
                    });
                    
                    audio.play().catch(() => {
                        // If play fails, skip to next sentence
                        playTagalogSequentially(sentences, index + 1);
                    });
                }
                
                // Fallback function using Web Speech API
                function playWithWebSpeech(text, langConfig) {
                    if (!synthesis) {
                        console.warn('Speech synthesis not available');
                        return;
                    }
                    
                    const utterance = new SpeechSynthesisUtterance(text);
                    
                    // Set language code for speech synthesis
                    utterance.lang = langConfig.speechSynthesis;
                    
                    // Get appropriate voice (preferably native speaker for the language)
                    const voice = getVoiceForLanguage(currentLanguage);
                    if (voice) {
                        utterance.voice = voice;
                        console.log('Using voice:', voice.name, 'Language:', voice.lang, 'for:', langConfig.speechSynthesis);
                    } else {
                        console.warn('No voice found, using default');
                    }
                    
                    // Adjust speech parameters for better clarity
                    utterance.rate = 0.85; // Slightly slower for better clarity
                    utterance.pitch = 1.0;
                    utterance.volume = 1.0;
                    
                    synthesis.speak(utterance);
                }
                
                // Load voices when available (some browsers load voices asynchronously)
                if (synthesis) {
                    // Function to log available voices for debugging
                    function logAvailableVoices() {
                        const voices = synthesis.getVoices();
                        console.log('Total voices available:', voices.length);
                        
                        // Log Tagalog voices specifically (Tagalog is the language code for Filipino)
                        const tagalogVoices = voices.filter(v => {
                            const lang = v.lang.toLowerCase();
                            return lang.includes('tl'); // Tagalog language code
                        });
                        
                        if (tagalogVoices.length > 0) {
                            console.log('✓ Tagalog voices found:', tagalogVoices.map(v => `${v.name} (${v.lang})`));
                        } else {
                            console.warn('⚠ No Tagalog voices found. The system will use available voices.');
                            console.log('Available languages:', [...new Set(voices.map(v => v.lang))].sort());
                            console.log('Note: Tagalog voices may need to be installed on your system.');
                        }
                    }
                    
                    // Try to get voices immediately
                    let voices = synthesis.getVoices();
                    if (voices.length > 0) {
                        logAvailableVoices();
                    } else {
                        // Wait for voices to load
                        synthesis.onvoiceschanged = () => {
                            voices = synthesis.getVoices();
                            if (voices.length > 0) {
                                logAvailableVoices();
                            }
                        };
                    }
                }

                // Start recording
                function startRecording() {
                    console.log('startRecording called');
                    console.log('recognition exists:', !!recognition);
                    console.log('isRecording:', isRecording);
                    
                    // Re-initialize recognition if it doesn't exist (e.g., after error)
                    if (!recognition) {
                        console.log('Recognition not found, initializing...');
                        recognition = initSpeechRecognition();
                        if (!recognition) {
                            alert('Speech recognition is not available in your browser. Please use Chrome, Edge, or another supported browser.');
                            return;
                        }
                    }

                    if (isRecording) {
                        console.log('Already recording');
                        return;
                    }

                    // Hide feedback section when starting new recording
                    feedbackSection.classList.add('hidden');

                    try {
                        const langConfig = languageCodes[currentLanguage] || languageCodes['en-US'];
                        recognition.lang = langConfig.speechRecognition;
                        console.log('Starting recording with language:', recognition.lang);
                        recognition.start();
                        console.log('recognition.start() called successfully');
                    } catch (error) {
                        console.error('Error starting recognition:', error);
                        console.error('Error name:', error.name);
                        console.error('Error message:', error.message);
                        
                        // Try to recreate and retry once
                        try {
                            console.log('Recreating recognition instance and retrying...');
                            recognition = initSpeechRecognition();
                            if (recognition) {
                                const langConfig = languageCodes[currentLanguage] || languageCodes['en-US'];
                                recognition.lang = langConfig.speechRecognition;
                                recognition.start();
                            } else {
                                alert('Could not start recording. Please refresh the page and try again.');
                            }
                        } catch (retryError) {
                            console.error('Retry failed:', retryError);
                            alert('Could not start recording. Please:\n\n1. Check your microphone permissions\n2. Make sure you have internet connection\n3. Try refreshing the page\n4. Use Chrome or Edge browser');
                        }
                    }
                }

                // Stop recording
                function stopRecording() {
                    if (recognition && isRecording) {
                        recognition.stop();
                    }
                    isRecording = false;
                    startRecordingBtn.classList.remove('hidden');
                    stopRecordingBtn.classList.add('hidden');
                    recordingStatus.classList.add('hidden');
                }

                // Normalize text for comparison (remove punctuation, normalize whitespace)
                // This helps with speech recognition variations but maintains strict accuracy
                function normalizeText(text) {
                    return text.toLowerCase()
                        .replace(/[.,!?;:'"()\[\]{}]/g, '') // Remove punctuation
                        .replace(/\s+/g, ' ') // Normalize whitespace
                        .trim();
                }

                // Analyze pronunciation
                function analyzePronunciation(userText) {
                    const currentItem = getCurrentItems()[currentIndex];
                    const expectedText = currentItem.text;
                    
                    // Normalize both texts for comparison
                    const normalizedExpected = normalizeText(expectedText);
                    const normalizedUser = normalizeText(userText);

                    // Calculate similarity using multiple methods
                    let accuracy = 0;
                    
                    // For single words, use character-level similarity
                    if (currentMode === 'word') {
                        accuracy = calculateWordSimilarity(normalizedUser, normalizedExpected);
                    } else {
                        // For phrases and sentences, use word-level similarity
                        accuracy = calculatePhraseSimilarity(normalizedUser, normalizedExpected);
                    }

                    // Display feedback
                    userPronunciation.textContent = userText || '-';
                    correctPronunciation.textContent = expectedText;
                    accuracyScore.textContent = accuracy + '%';
                    
                    // Set color based on accuracy
                    if (accuracy >= 90) {
                        accuracyScore.style.color = '#10b981'; // Green - Excellent
                    } else if (accuracy >= 75) {
                        accuracyScore.style.color = '#22c55e'; // Light green - Good
                    } else if (accuracy >= 60) {
                        accuracyScore.style.color = '#f59e0b'; // Orange - Fair
                    } else if (accuracy >= 40) {
                        accuracyScore.style.color = '#f97316'; // Dark orange - Needs improvement
                    } else {
                        accuracyScore.style.color = '#ef4444'; // Red - Poor
                    }

                    // Display tips
                    tipsList.innerHTML = '';
                    if (currentItem.tips && currentItem.tips.length > 0) {
                        currentItem.tips.forEach(tip => {
                            const li = document.createElement('li');
                            li.textContent = tip;
                            tipsList.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.textContent = 'Keep practicing! Try to match the reference pronunciation.';
                        tipsList.appendChild(li);
                    }

                    feedbackSection.classList.remove('hidden');

                    // Update stats
                    practiceStats.totalPracticed++;
                    practiceStats.attempts++;
                    practiceStats.totalAccuracy += accuracy;
                    
                    // Gamified rewards
                    if (accuracy === 100) {
                        practiceStats.perfectCount = (practiceStats.perfectCount || 0) + 1;
                        practiceStats.xp = (practiceStats.xp || 0) + 20; // Bonus XP for perfect score
                    } else if (accuracy >= 90) {
                        practiceStats.xp = (practiceStats.xp || 0) + 15;
                    } else if (accuracy >= 75) {
                        practiceStats.xp = (practiceStats.xp || 0) + 10;
                    } else {
                        practiceStats.xp = (practiceStats.xp || 0) + 5; // Participation XP
                    }
                    
                    // Level up check (100 XP per level)
                    const newLevel = Math.floor((practiceStats.xp || 0) / 100) + 1;
                    if (newLevel > (practiceStats.level || 1)) {
                        practiceStats.level = newLevel;
                        // Could add level up animation here
                    }
                    
                    saveStats();
                }

                // Calculate similarity for single words (character-level)
                // Returns 100% only for perfect matches, otherwise honest rating
                function calculateWordSimilarity(userWord, expectedWord) {
                    // Perfect match = 100%
                    if (userWord === expectedWord) return 100;
                    
                    if (expectedWord.length === 0) return 0;
                    if (userWord.length === 0) return 0;
                    
                    // Use Levenshtein distance for honest calculation
                    const distance = levenshteinDistance(userWord, expectedWord);
                    const maxLength = Math.max(userWord.length, expectedWord.length);
                    
                    // Calculate base similarity (no bonus points - be honest)
                    const similarity = ((maxLength - distance) / maxLength) * 100;
                    
                    // Penalize for length differences (if user said much more/less)
                    const lengthDiff = Math.abs(userWord.length - expectedWord.length);
                    const lengthPenalty = (lengthDiff / maxLength) * 20; // Up to 20% penalty
                    
                    // Final score: base similarity minus length penalty
                    const finalScore = Math.max(0, similarity - lengthPenalty);
                    
                    return Math.round(finalScore);
                }

                // Calculate similarity for phrases and sentences (word-level)
                // Returns 100% only for perfect matches, otherwise honest rating
                function calculatePhraseSimilarity(userPhrase, expectedPhrase) {
                    // Perfect match = 100%
                    if (userPhrase === expectedPhrase) return 100;
                    
                    const userWords = userPhrase.split(/\s+/).filter(w => w.length > 0);
                    const expectedWords = expectedPhrase.split(/\s+/).filter(w => w.length > 0);
                    
                    if (expectedWords.length === 0) return 0;
                    if (userWords.length === 0) return 0;
                    
                    // Calculate word-by-word similarity (honest, no bonuses)
                    let totalSimilarity = 0;
                    const expectedLength = expectedWords.length;
                    const userLength = userWords.length;
                    const maxLength = Math.max(expectedLength, userLength);
                    
                    // Compare each word position strictly
                    for (let i = 0; i < maxLength; i++) {
                        const userWord = userWords[i] || '';
                        const expectedWord = expectedWords[i] || '';
                        
                        if (userWord && expectedWord) {
                            if (userWord === expectedWord) {
                                // Perfect word match
                                totalSimilarity += 100;
                            } else {
                                // Partial word match - calculate similarity
                                const wordSim = calculateWordSimilarity(userWord, expectedWord);
                                totalSimilarity += wordSim;
                            }
                        } else {
                            // Missing or extra word - significant penalty (0 points for this position)
                            totalSimilarity += 0;
                        }
                    }
                    
                    // Calculate base score based on expected length (not max length)
                    // This ensures missing words are properly penalized
                    const baseScore = totalSimilarity / expectedLength;
                    
                    // Additional penalty for word count mismatch
                    let wordCountPenalty = 0;
                    if (userLength !== expectedLength) {
                        const wordDiff = Math.abs(userLength - expectedLength);
                        wordCountPenalty = (wordDiff / expectedLength) * 10; // Up to 10% penalty
                    }
                    
                    // Final score: base score minus word count penalty
                    const finalScore = Math.max(0, Math.min(100, baseScore - wordCountPenalty));
                    
                    return Math.round(finalScore);
                }

                // Levenshtein distance algorithm
                function levenshteinDistance(str1, str2) {
                    const matrix = [];
                    for (let i = 0; i <= str2.length; i++) {
                        matrix[i] = [i];
                    }
                    for (let j = 0; j <= str1.length; j++) {
                        matrix[0][j] = j;
                    }
                    for (let i = 1; i <= str2.length; i++) {
                        for (let j = 1; j <= str1.length; j++) {
                            if (str2.charAt(i - 1) === str1.charAt(j - 1)) {
                                matrix[i][j] = matrix[i - 1][j - 1];
                            } else {
                                matrix[i][j] = Math.min(
                                    matrix[i - 1][j - 1] + 1,
                                    matrix[i][j - 1] + 1,
                                    matrix[i - 1][j] + 1
                                );
                            }
                        }
                    }
                    return matrix[str2.length][str1.length];
                }

                // Event listeners
                modeButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        modeButtons.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        currentMode = btn.dataset.mode;
                        currentIndex = 0;
                        loadPracticeItem(0);
                        feedbackSection.classList.add('hidden');
                    });
                });

                languageSelect.addEventListener('change', (e) => {
                    currentLanguage = e.target.value;
                    currentIndex = 0;
                    loadPracticeItem(0);
                    feedbackSection.classList.add('hidden');
                    if (recognition) {
                        const langConfig = languageCodes[currentLanguage] || languageCodes['en-US'];
                        recognition.lang = langConfig.speechRecognition;
                    }
                });

                playReferenceBtn.addEventListener('click', playReference);
                startRecordingBtn.addEventListener('click', startRecording);
                stopRecordingBtn.addEventListener('click', stopRecording);

                nextItemBtn.addEventListener('click', () => {
                    loadPracticeItem(currentIndex + 1);
                    feedbackSection.classList.add('hidden');
                });

                if (randomWordBtn) {
                    randomWordBtn.addEventListener('click', () => {
                        const items = getCurrentItems();
                        if (items.length > 0) {
                            const randomIndex = Math.floor(Math.random() * items.length);
                            loadPracticeItem(randomIndex);
                            feedbackSection.classList.add('hidden');
                        }
                    });
                }

                // Initialize
                loadStats();
                loadPracticeItem(0);
            })();
        });
    </script>
</body>
</html>
