<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> | Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Summernote Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --sidebar-width: 280px;
            --sidebar-bg: #0f172a;
            --content-bg: #f8fafc;
            --accent-color: #6366f1;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        /* Prevent horizontal scroll at the HTML level only — avoids killing vertical scroll on body */
        html {
            overflow-x: hidden;
        }

        body { 
            font-family: 'Outfit', sans-serif; 
            background: var(--content-bg);
            color: #1e293b;
            overflow: visible; /* allow natural vertical scroll */
        }

        /* Sidebar Styles */
        .sidebar { 
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg); 
            color: white; 
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1050;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;          /* OUR CSS — no Bootstrap interference */
            flex-direction: column;
            overflow: hidden;       /* contain children; scroll in .sidebar-body */
        }

        /* Hide sidebar on mobile via OUR media query (no Bootstrap d-none needed) */
        @media (max-width: 991.98px) {
            .sidebar {
                display: none;  /* hide on mobile; offcanvas is used instead */
            }
        }

        /* Pinned brand area at top of sidebar */
        .sidebar-header {
            padding: 2rem 1.5rem 1rem 1.5rem;
            flex-shrink: 0;
        }

        /* Scrollable nav body */
        .sidebar-body {
            flex: 1 1 0;   /* grow AND shrink from 0 — critical for overflow to work */
            min-height: 0; /* MUST have this or flex won't constrain the height */
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0 1.5rem 2rem 1.5rem;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        /* Thin custom scrollbar for the sidebar nav */
        .sidebar-body::-webkit-scrollbar { width: 4px; }
        .sidebar-body::-webkit-scrollbar-track { background: transparent; }
        .sidebar-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; }
        .sidebar-body::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }

        .sidebar-brand {
            font-size: 1.6rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem; /* reduced from 3rem — was wasting vertical space */
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .sidebar-nav-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.3);
            margin: 2rem 0 1rem 1rem;
            font-weight: 700;
        }

        .sidebar a { 
            color: #94a3b8; 
            text-decoration: none; 
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0.9rem 1.2rem; 
            border-radius: 14px; 
            margin-bottom: 0.4rem; 
            transition: all 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar a i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .sidebar a:hover { 
            background: rgba(255, 255, 255, 0.06); 
            color: white; 
            transform: translateX(4px);
        }

        .sidebar a:hover i { transform: scale(1.1); }

        .sidebar a.active { 
            background: var(--primary-gradient);
            color: white; 
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            font-weight: 600;
        }

        .sidebar hr {
            border-color: rgba(255, 255, 255, 0.08);
            margin: 1.5rem 0;
        }

        /* Main Content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            /* Ensure this wrapper allows normal vertical document flow */
            overflow: visible;
        }

        .main-content { 
            padding: 2rem 3rem 4rem 3rem; 
            flex: 1;
            overflow: visible; /* do NOT clip content — let the browser page scroll */
        }

        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 1.2rem 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }

        /* Cards and UI Elements */
        .card { 
            border: none; 
            border-radius: 26px; 
            box-shadow: var(--card-shadow);
            background: white;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 62px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            font-size: 1.4rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.9rem 2.2rem;
            border-radius: 18px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
        }

        .btn-gradient:hover {
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
            transform: translateY(-3px);
            color: white;
        }

        .form-control, .form-select {
            padding: 0.9rem 1.4rem;
            border-radius: 18px;
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: white;
            border-color: #6366f1;
            box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.1);
        }

        /* Badge Styling */
        .badge-premium {
            padding: 0.6rem 1.2rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in { animation: fadeInUp 0.6s ease-out forwards; }

        /* ── Admin Mobile Polish ── */
        @media (max-width: 991.98px) {
            .main-wrapper { margin-left: 0; }
            .main-content { padding: 1.5rem 1rem 3rem 1rem !important; }
            .top-navbar { padding: 1rem 1.25rem; }
            
            /* Sidebar inside offcanvas needs specific width/reset */
            .offcanvas .sidebar { 
                display: flex !important; 
                width: 100% !important; 
                height: 100% !important; 
                position: relative !important; 
                background: transparent !important;
                border: none !important;
            }
            .offcanvas .sidebar-body { padding: 1rem !important; }
            .offcanvas .sidebar a { padding: 1rem 1.25rem !important; margin-bottom: 0.5rem !important; }
        }

        @media (max-width: 767.98px) {
            /* Prevent card hover lift from causing scroll jank */
            .card:hover { transform: none; }

            /* Better button sizing on touch */
            .btn { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; }

            /* Stack action buttons in headers */
            .d-flex.gap-2.justify-content-end,
            .top-navbar .d-flex.gap-3 { 
                flex-wrap: wrap; 
                gap: 8px !important; 
            }

            /* Responsive tables with horizontal scroll */
            .table-responsive { 
                border-radius: 12px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin-bottom: 1rem;
            }
            .table th, .table td { 
                padding: 1rem 0.75rem !important; 
                font-size: 0.85rem; 
                white-space: nowrap;
            }

            /* Image previews in tables */
            .table img { width: 40px !important; height: 40px !important; }
            
            /* Breadcrumb smaller */
            .breadcrumb { font-size: 0.75rem; }
            h2.fw-bold { font-size: 1.6rem !important; }
        }

        @media (max-width: 575.98px) {
            /* Full-width buttons on small screens */
            .btn-gradient, .btn-dark, .btn-white { 
                width: 100% !important; 
                margin-top: 5px;
            }

            /* Stack grid columns */
            .row.g-4 > [class*="col-"] {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            /* Dashboard icons smaller on mobile */
            .stat-icon { width: 48px; height: 48px; border-radius: 14px; font-size: 1.1rem; }
            .stat-value { font-size: 1.5rem !important; }
        }

        /* Summernote UI Fixes */
        .note-modal-backdrop { z-index: 1060 !important; }
        .note-modal { z-index: 1070 !important; margin-top: 5vh; }
        @media (max-width: 767.98px) {
            .note-editable { padding: 25px !important; margin: 15px auto !important; }
            .note-toolbar { padding: 10px 15px !important; }
        }
        .note-editor .note-editing-area { background: #f1f5f9; }
        .note-editable { 
            background: white !important; 
            max-width: 900px !important; 
            margin: 40px auto !important; 
            padding: 60px !important; 
            min-height: 600px !important; 
            border-radius: 4px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            font-size: 1.1rem !important;
            line-height: 1.8 !important;
        }
        .note-editor.note-frame { border: none !important; border-radius: 12px; overflow: hidden; background: #f1f5f9; }
        .note-toolbar { 
            background: white !important; 
            border-bottom: 1px solid #eef2f6 !important; 
            padding: 15px 30px !important; 
            position: sticky; 
            top: 0; 
            z-index: 500;
        }
        
        /* Blocks Styling inside Editor */
        .note-editable .row { border: 1px dashed #e2e8f0; padding: 10px; border-radius: 8px; position: relative; }
        .note-editable .row::before { content: 'ROW CONTAINER'; position: absolute; top: -10px; left: 10px; background: #4f46e5; color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: bold; }
        
        /* Blocks Dropdown Styling */
        .wp-blocks-dropdown { min-width: 250px !important; padding: 10px !important; border-radius: 15px !important; border: none !important; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; background: white !important; }
        .wp-blocks-dropdown .note-dropdown-item { padding: 10px 15px !important; border-radius: 8px !important; transition: all 0.2s !important; display: flex !important; align-items: center !important; cursor: pointer !important; }
        .wp-blocks-dropdown .note-dropdown-item:hover { background: #f1f5f9 !important; transform: translateX(5px); }
        .wp-blocks-dropdown i { width: 25px; font-size: 1.1rem; }
    </style>
</head>
<body>
    <!-- Mobile Toggle Button (Only on Small Screens) -->
    <div class="d-lg-none p-3 bg-white border-bottom sticky-top d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold">Boost Stride</h5>
        <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
            <i class="fa fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar for Desktop (classes removed: Bootstrap d-lg-block was overriding display:flex) -->
    <nav class="sidebar">
        <!-- Pinned brand header -->
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="bg-white p-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="fas fa-rocket text-primary" style="font-size: 1.4rem;"></i>
                </div>
                <span>Boost Stride</span>
            </div>
        </div>

        <!-- Scrollable nav body -->
        <div class="sidebar-body">
            <div class="sidebar-nav-label">Main Dashboard</div>
            <a href="?page=dashboard" class="<?php echo ($page ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-grid-2"></i> Dashboard Overview
            </a>

            <div class="sidebar-nav-label">Website Content</div>
            <a href="?page=custom_pages" class="<?php echo ($page ?? '') === 'custom_pages' ? 'active' : ''; ?>">
                <i class="fas fa-layer-group"></i> Manage Pages
            </a>
            <a href="?page=menu" class="<?php echo ($page ?? '') === 'menu' ? 'active' : ''; ?>">
                <i class="fas fa-sitemap"></i> Navigation Menu
            </a>
            <a href="?page=footer" class="<?php echo ($page ?? '') === 'footer' ? 'active' : ''; ?>">
                <i class="fas fa-shoe-prints"></i> Footer Builder
            </a>

            <div class="sidebar-nav-label">Module Editors</div>
            <a href="?page=hero" class="<?php echo ($page ?? '') === 'hero' ? 'active' : ''; ?>">
                <i class="fas fa-window-restore"></i> Hero Section
            </a>
            <a href="?page=services" class="<?php echo ($page ?? '') === 'services' ? 'active' : ''; ?>">
                <i class="fas fa-tools"></i> Services Catalog
            </a>
            <a href="?page=testimonials" class="<?php echo ($page ?? '') === 'testimonials' ? 'active' : ''; ?>">
                <i class="fas fa-comment-dots"></i> Client Reviews
            </a>
            <a href="?page=team" class="<?php echo ($page ?? '') === 'team' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Staff & Team
            </a>
            <a href="?page=pages" class="<?php echo ($page ?? '') === 'pages' ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice"></i> Dynamic Sections
            </a>

            <div class="sidebar-nav-label">Settings & SEO</div>
            <a href="?page=leads" class="<?php echo ($page ?? '') === 'leads' ? 'active' : ''; ?>">
                <i class="fas fa-envelopes-bulk"></i> Customer Leads
                <?php 
                    try {
                        // Safe check for table existence to prevent crash if not imported yet
                        $table_check = $db->query("SHOW TABLES LIKE 'leads'")->fetch();
                        if ($table_check) {
                            $new_leads_count = $db->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn();
                            if($new_leads_count > 0): 
                ?>
                            <span class="badge bg-danger rounded-pill ms-auto" style="font-size: 0.6rem;"><?php echo $new_leads_count; ?></span>
                <?php 
                            endif;
                        }
                    } catch (Exception $e) {} 
                ?>
            </a>
            <a href="?page=pages" class="<?php echo ($page ?? '') === 'pages' ? 'active' : ''; ?>">
                <i class="fas fa-palette"></i> Appearance
            </a>
            <a href="?page=seo" class="<?php echo ($page ?? '') === 'seo' ? 'active' : ''; ?>">
                <i class="fas fa-search-plus"></i> Search Engine (SEO)
            </a>
            <a href="?page=settings" class="<?php echo ($page ?? '') === 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-sliders-h"></i> System Config
            </a>

            <hr style="border-color: rgba(255,255,255,0.08); margin-top: 1.5rem;">
            <a href="logout.php" class="text-danger" style="margin-top: 0.5rem;">
                <i class="fas fa-power-off"></i> Sign Out
            </a>
        </div>
    </nav>

    <div class="main-wrapper">
    <!-- Top Navbar -->
    <div class="top-navbar d-none d-lg-flex">
        <div class="d-flex align-items-center gap-3">
            <div class="search-box position-relative d-none d-xl-block">
                <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" class="form-control ps-5 border-0 bg-light" placeholder="Search modules..." style="width: 300px; border-radius: 14px;">
            </div>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="d-flex align-items-center gap-2">
                <div class="badge-premium bg-success bg-opacity-10 text-success d-flex align-items-center gap-2">
                    <span class="pulse-green"></span>
                    Systems Operational
                </div>
            </div>
            <div class="vr bg-gray-300 mx-2" style="height: 30px; width: 1px;"></div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="fw-bold small lh-1">Master Administrator</div>
                    <small class="text-muted extra-small">Full Access Control</small>
                </div>
                <div class="avatar-box">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff&bold=true" class="rounded-circle shadow-sm" width="45" alt="Avatar">
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Offcanvas for Mobile -->
    <div class="offcanvas offcanvas-start text-white" tabindex="-1" id="sidebarOffcanvas" style="background: var(--sidebar-bg); width: 280px;">
        <div class="offcanvas-header pt-4 px-4 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
            <div class="sidebar-brand mb-0">
                <div class="bg-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width:38px; height:38px;">
                    <i class="fas fa-rocket text-primary" style="font-size:1.1rem;"></i>
                </div>
                <span>Boost Stride</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body px-3 pb-4" style="overflow-y: auto;">
            <div class="sidebar p-0 position-relative w-100" style="height: auto;">
                <div class="sidebar-nav-label">Main Dashboard</div>
                <a href="?page=dashboard" class="<?php echo ($page ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>

                <div class="sidebar-nav-label">Website Content</div>
                <a href="?page=custom_pages" class="<?php echo ($page ?? '') === 'custom_pages' ? 'active' : ''; ?>">
                    <i class="fas fa-layer-group"></i> Manage Pages
                </a>
                <a href="?page=menu" class="<?php echo ($page ?? '') === 'menu' ? 'active' : ''; ?>">
                    <i class="fas fa-sitemap"></i> Navigation Menu
                </a>
                <a href="?page=footer" class="<?php echo ($page ?? '') === 'footer' ? 'active' : ''; ?>">
                    <i class="fas fa-shoe-prints"></i> Footer Builder
                </a>

                <div class="sidebar-nav-label">Module Editors</div>
                <a href="?page=hero" class="<?php echo ($page ?? '') === 'hero' ? 'active' : ''; ?>">
                    <i class="fas fa-window-restore"></i> Hero Section
                </a>
                <a href="?page=services" class="<?php echo ($page ?? '') === 'services' ? 'active' : ''; ?>">
                    <i class="fas fa-tools"></i> Services Catalog
                </a>
                <a href="?page=testimonials" class="<?php echo ($page ?? '') === 'testimonials' ? 'active' : ''; ?>">
                    <i class="fas fa-comment-dots"></i> Client Reviews
                </a>
                <a href="?page=team" class="<?php echo ($page ?? '') === 'team' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Staff &amp; Team
                </a>
                <a href="?page=pages" class="<?php echo ($page ?? '') === 'pages' ? 'active' : ''; ?>">
                    <i class="fas fa-file-invoice"></i> Dynamic Sections
                </a>

                <div class="sidebar-nav-label">Settings &amp; SEO</div>
                <a href="?page=seo" class="<?php echo ($page ?? '') === 'seo' ? 'active' : ''; ?>">
                    <i class="fas fa-search-plus"></i> Search Engine (SEO)
                </a>
                <a href="?page=settings" class="<?php echo ($page ?? '') === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-sliders-h"></i> System Config
                </a>

                <hr style="border-color: rgba(255,255,255,0.08);">
                <a href="logout.php" class="text-danger">
                    <i class="fas fa-power-off"></i> Sign Out
                </a>
            </div>
        </div>
    </div>


    <!-- Main Content Slot -->
    <main class="main-content animate-fade-in">
        <!-- Page Header for Mobile -->
        <div class="mb-4 d-lg-none">
            <h3 class="fw-bold"><?php echo ucfirst($page ?? 'dashboard'); ?></h3>
            <p class="text-muted small">Welcome back to your command center.</p>
        </div>
        
        <!-- Dashboard Header for Desktop -->
        <div class="d-none d-lg-flex justify-content-between align-items-end mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 extra-small text-uppercase fw-bold letter-spacing-1">
                        <li class="breadcrumb-item"><a href="?page=dashboard" class="text-decoration-none text-muted">Admin</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page"><?php echo $page ?? 'Dashboard'; ?></li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-0" style="letter-spacing: -1px;">
                    <?php 
                        $titles = [
                            'dashboard' => 'Network Overview',
                            'hero' => 'Visual Identity',
                            'services' => 'Service Pipeline',
                            'testimonials' => 'Social Reputation',
                            'team' => 'Expert Collective',
                            'settings' => 'System Infrastructure',
                            'seo' => 'Growth & Reach',
                            'footer' => 'Brand Conclusion',
                            'menu' => 'Architecture',
                            'pages' => 'Dynamic Assets',
                            'custom_pages' => 'Unified Directory'
                        ];
                        echo $titles[$page ?? 'dashboard'] ?? ucfirst($page);
                    ?>
                </h2>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-white border px-4 py-2 shadow-sm rounded-pill small fw-bold"><i class="fas fa-redo me-2"></i>Refresh</button>
                <button class="btn btn-dark px-4 py-2 shadow-sm rounded-pill small fw-bold"><i class="fas fa-plus me-2"></i>New Page</button>
            </div>
        </div>

        <style>
            .extra-small { font-size: 0.65rem; }
            .letter-spacing-1 { letter-spacing: 1.5px; }
            .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; vertical-align: middle; }
            .pulse-green {
                width: 8px;
                height: 8px;
                background: #22c55e;
                border-radius: 50%;
                display: inline-block;
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
            }
            .btn-white { background: white; color: #1e293b; }
            .btn-white:hover { background: #f8fafc; color: #6366f1; }
        </style>
