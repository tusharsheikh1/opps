<style>
:root{
    --brand-accent: #06b6d4; /* Changed from #f97316 to a water-like blue/cyan */
    --brand-danger: #ef4444;
    --brand-success: #10b981;
    --muted: #6b7280;
    --bg: rgba(255, 255, 255, 0.75);
    --panel-bg: rgba(255, 255, 255, 0.85);
    --shadow: 0 12px 30px rgba(2,6,23,0.08);
    --radius: 16px;
    --max-width: 1200px;
    --transition-fast: 0.18s;
    --glass-border: rgba(255, 255, 255, 0.18);
}

/* Font */
@font-face{
    font-family: 'Muli';
    src: url('{{asset("/")}}assets/frontend/font/Muli/Muli-VariableFont_wght.ttf');
    font-display: swap;
}

/* Reset / base for header - ULTRA MODERN GLASSMORPHIC */
header {
    font-family: 'Muli', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    position: sticky;
    top: 0;
    z-index: 1000;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.7), rgba(249, 250, 251, 0.6));
    backdrop-filter: blur(24px) saturate(180%);
    -webkit-backdrop-filter: blur(24px) saturate(180%);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 0;
    padding: 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* Elevation when scrolled - ENHANCED MODERN GLASS */
header.scrolled {
    box-shadow: 0 10px 40px rgba(2,6,23,0.08), 0 1px 3px rgba(0,0,0,0.1);
    backdrop-filter: blur(30px) saturate(200%);
    -webkit-backdrop-filter: blur(30px) saturate(200%);
    background: linear-gradient(135deg, rgba(255,255,255,0.85), rgba(249, 250, 251, 0.75));
    border-bottom: 1px solid rgba(255, 255, 255, 0.25);
}

/* Top header wrapper */
.top-header {
    background: transparent;
    padding: 12px 0;
}

/* Flex container */
.header-content {
    display: flex;
    align-items: center;
    gap: 32px;
    position: relative;
    width: 100%;
    padding-left: 24px;
    padding-right: 24px;
    box-sizing: border-box;
    margin: 0 auto;
    max-width: var(--max-width);
}

/* Logo - MODERN TREATMENT */
.logo-area {
    flex-shrink: 0;
    transition: transform 0.3s ease;
}
.logo-area:hover {
    transform: scale(1.02);
}
.logo-area img {
    max-height: 48px;
    width: auto;
    display: block;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
}

/* Main Nav - ULTRA MODERN */
.main-nav {
    margin: 0;
    padding: 0;
    flex: 1 1 auto;
}
.main-nav ul {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.main-nav ul li {
    padding: 0;
    margin: 0;
    position: relative;
}
.main-nav ul a {
    font-size: 13px;
    font-weight: 600;
    color: #1f2937;
    text-transform: none;
    text-decoration: none;
    padding: 10px 16px;
    letter-spacing: -0.01em;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
}
.main-nav ul a::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%) scaleX(0);
    width: 80%;
    height: 2px;
    background: linear-gradient(90deg, var(--brand-accent), #0284c7); /* Adjusted secondary color */
    border-radius: 2px;
    transition: transform 0.3s ease;
}
.main-nav ul a:hover::before {
    transform: translateX(-50%) scaleX(1);
}
.main-nav ul a:hover,
.main-nav ul a:focus {
    color: var(--brand-accent);
    background: rgba(6,182,212,0.06); /* Changed from rgba(249,115,22,0.06) */
    transform: translateY(-1px);
}
.main-nav ul a.highlight {
    background: linear-gradient(135deg, rgba(37,99,235,0.1), rgba(6,182,212,0.1)); /* Changed from orange/red */
    color: #111827; /* MODIFIED: Text color set to dark gray/black */
    font-weight: 700;
}
.main-nav ul a.highlight:hover {
    background: linear-gradient(135deg, rgba(37,99,235,0.15), rgba(6,182,212,0.15)); /* Changed from orange/red */
}

/* Modern Mega Panel - ULTRA MODERN GLASSMORPHIC */
.mega-panel {
    position: fixed;
    left: 50%;
    transform: translateX(-50%) translateY(12px);
    top: 72px;
    width: min(1100px, calc(100vw - 80px));
    max-height: calc(100vh - 140px);
    overflow: hidden;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(249, 250, 251, 0.9));
    backdrop-filter: blur(30px) saturate(180%);
    -webkit-backdrop-filter: blur(30px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 20px;
    box-shadow: 0 25px 70px rgba(2,6,23,0.12), 
                0 10px 30px rgba(0,0,0,0.08),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset;
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1100;
}

/* Inner content grid for subcategories - MODERN GRID */
.mega-panel .mega-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 12px;
    padding: 4px;
}

/* Custom scrollbar for mega panel - MODERN MINIMAL */
.mega-panel .mega-grid::-webkit-scrollbar {
    width: 6px;
}
.mega-panel .mega-grid::-webkit-scrollbar-track {
    background: rgba(241, 245, 249, 0.3);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 10px;
    margin: 8px 0;
}
.mega-panel .mega-grid::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, rgba(6,182,212,0.4), rgba(6,182,212,0.6)); /* Changed from orange */
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 10px;
    border: 2px solid rgba(255, 255, 255, 0.2);
}
.mega-panel .mega-grid::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, rgba(6,182,212,0.6), rgba(6,182,212,0.8)); /* Changed from orange */
}

/* Subcategory card column - ULTRA MODERN CARDS */
.mega-col {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.3));
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    padding: 12px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.5);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
.mega-col:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.5));
    border-color: rgba(6, 182, 212, 0.3); /* Changed from orange */
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 12px 24px rgba(6, 182, 212, 0.12); /* Changed from orange */
}
.mega-col .col-title {
    font-weight: 700;
    font-size: 13px;
    color: #111827;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -0.02em;
    padding-bottom: 8px;
    border-bottom: 2px solid rgba(6,182,212,0.15); /* Changed from orange */
}
.mega-col .col-title svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    color: var(--brand-accent);
    transition: transform 0.3s ease;
}
.mega-col:hover .col-title svg {
    transform: translateX(3px);
}
.mega-col .col-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.mega-col .col-list a {
    color: #4b5563;
    font-weight: 500;
    font-size: 12px;
    text-decoration: none;
    padding: 8px 10px;
    border-radius: 8px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    line-height: 1.4;
    position: relative;
    overflow: hidden;
}
.mega-col .col-list a::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 3px;
    background: linear-gradient(180deg, var(--brand-accent), #0284c7); /* Adjusted secondary color */
    transform: scaleY(0);
    transition: transform 0.3s ease;
    border-radius: 0 3px 3px 0;
}
.mega-col .col-list a:hover::before {
    transform: scaleY(1);
}
.mega-col .col-list a:hover,
.mega-col .col-list a:focus {
    color: var(--brand-accent);
    background: rgba(6,182,212,0.1); /* Changed from orange */
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transform: translateX(6px);
    box-shadow: 0 4px 12px rgba(6, 182, 212, 0.15); /* Changed from orange */
    border: 1px solid rgba(6, 182, 212, 0.15); /* Changed from orange */
    padding-left: 14px;
}

/* Promo column - ULTRA MODERN CARD */
.mega-promo {
    border-radius: 16px;
    overflow: hidden;
    background: linear-gradient(135deg, 
                rgba(6,182,212,0.12), /* Changed from orange */
                rgba(37,99,235,0.08), /* Changed from red */
                rgba(6,182,212,0.06)); /* Changed from orange */
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 140px;
    padding: 16px;
    box-shadow: 0 10px 40px rgba(6, 182, 212, 0.15); /* Changed from orange */
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
.mega-promo::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1));
    opacity: 0;
    transition: opacity 0.4s ease;
}
.mega-promo:hover::before {
    opacity: 1;
}
.mega-promo:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 50px rgba(6, 182, 212, 0.2); /* Changed from orange */
    border-color: rgba(6, 182, 212, 0.4); /* Changed from orange */
}
.mega-promo .promo-media {
    height: 100px;
    background-position: center;
    background-size: cover;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    transition: transform 0.4s ease;
}
.mega-promo:hover .promo-media {
    transform: scale(1.05);
}
.mega-promo .promo-body {
    padding-top: 12px;
    position: relative;
    z-index: 1;
}
.mega-promo .promo-title {
    font-weight: 800;
    color: #111827;
    font-size: 14px;
    letter-spacing: -0.02em;
    margin-bottom: 4px;
}
.mega-promo .promo-desc {
    font-size: 11px;
    color: #6b7280;
    margin-top: 4px;
    line-height: 1.5;
    font-weight: 500;
}
.mega-promo .promo-cta {
    margin-top: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: linear-gradient(135deg, rgba(6,182,212,0.95), rgba(3,105,161,1)); /* Changed from orange/red */
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: white;
    padding: 10px 16px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    text-decoration: none;
    font-weight: 700;
    font-size: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 16px rgba(6, 182, 212, 0.3); /* Changed from orange */
    width: 100%;
    text-align: center;
}
.mega-promo .promo-cta:hover {
    background: linear-gradient(135deg, rgba(3,105,161,1), rgba(6,182,212,0.95)); /* Changed from orange/red */
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(6,182,212,0.4); /* Changed from orange */
    border-color: rgba(255, 255, 255, 0.6);
}
.mega-promo .promo-cta svg {
    transition: transform 0.3s ease;
}
.mega-promo .promo-cta:hover svg {
    transform: translateX(4px);
}

/* Visible state - IMPROVED: Single dropdown at a time */
.main-nav ul > li.has-dropdown.active > .mega-panel {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
    pointer-events: auto;
    animation: slideInDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

/* Collections dropdown - MODERN GRID LAYOUT */
.main-nav ul > li.collections .mega-panel {
    grid-template-columns: 1fr;
    width: min(700px, calc(100vw - 80px));
}

.main-nav ul > li.collections .simple-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 10px;
    padding: 8px;
}

.main-nav ul > li.collections .simple-list a {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 16px;
    color: #374151;
    text-decoration: none;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, rgba(6,182,212,0.04), rgba(6,182,212,0.02)); /* Changed from orange */
    border: 1px solid rgba(6,182,212,0.1); /* Changed from orange */
    position: relative;
    overflow: hidden;
}
.main-nav ul > li.collections .simple-list a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}
.main-nav ul > li.collections .simple-list a:hover::before {
    left: 100%;
}
.main-nav ul > li.collections .simple-list a:hover {
    color: var(--brand-accent);
    background: rgba(6,182,212,0.15); /* Changed from orange */
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 8px 20px rgba(6,182,212,0.2); /* Changed from orange */
    border: 1px solid rgba(6, 182, 212, 0.3); /* Changed from orange */
}

/* Header actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-shrink: 0;
}
.action-item {
    position: relative;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #374151;
    font-weight: 600;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    background: transparent;
}
.action-item:hover { 
    color: var(--brand-accent); 
    background: rgba(6,182,212,0.1); /* Changed from orange */
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: 0 4px 16px rgba(6, 182, 212, 0.15); /* Changed from orange */
    border: 1px solid rgba(6, 182, 212, 0.15); /* Changed from orange */
    transform: translateY(-2px);
}
.action-item i { font-size: 18px; margin-right: 0; }
.action-item span:not(.badge) { display: none; }

/* Badge - MODERN FLOATING PILL */
.badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: linear-gradient(135deg, var(--brand-danger), #dc2626);
    color: white;
    font-size: 10px;
    font-weight: 800;
    padding: 4px 7px;
    border-radius: 999px;
    min-width: 20px;
    text-align: center;
    line-height: 1;
    box-shadow: 0 4px 12px rgba(239,68,68,0.3), 0 0 0 3px rgba(255,255,255,0.9);
    animation: pulse-badge 2s ease-in-out infinite;
}

@keyframes pulse-badge {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Mobile toggles - MODERN ANIMATED */
.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    justify-content: space-between;
    cursor: pointer;
    padding: 10px;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: transparent;
    border: none;
    z-index: 1001;
    height: 40px;
    width: 40px;
}
.mobile-menu-toggle:hover { 
    background: rgba(6,182,212,0.1); /* Changed from orange */
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: 0 4px 16px rgba(6, 182, 212, 0.15); /* Changed from orange */
    transform: scale(1.05);
}
.mobile-menu-toggle span {
    width: 24px;
    height: 2.5px;
    background: linear-gradient(90deg, #374151, #1f2937);
    margin: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 3px;
}
.mobile-menu-toggle:hover span {
    background: linear-gradient(90deg, var(--brand-accent), #0284c7); /* Adjusted secondary color */
}
/* Animated X when open */
.mobile-menu-panel.active ~ .header-content .mobile-menu-toggle span:nth-child(1) {
    transform: rotate(45deg) translateY(8px);
}
.mobile-menu-panel.active ~ .header-content .mobile-menu-toggle span:nth-child(2) {
    opacity: 0;
    transform: translateX(-20px);
}
.mobile-menu-panel.active ~ .header-content .mobile-menu-toggle span:nth-child(3) {
    transform: rotate(-45deg) translateY(-8px);
}

/* Mobile overlay/panel */
.mobile-menu-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 9998;
    opacity: 0;
    visibility: hidden;
    transition: all 0.28s ease;
}
.mobile-menu-overlay.active { opacity: 1; visibility: visible; }
.mobile-menu-panel {
    position: fixed;
    top: 0;
    left: -100%;
    width: 320px;
    height: 100vh;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-right: 1px solid rgba(255, 255, 255, 0.3);
    z-index: 9999;
    transition: all 0.28s ease;
    box-shadow: 4px 0 30px rgba(2,6,23,0.1);
    overflow-y: auto;
    padding-top: 0;
}
.mobile-menu-panel.active { left: 0; }

/* Mobile menu styles */
.mobile-menu-header { 
    display:flex; 
    justify-content:space-between; 
    align-items:center; 
    padding:18px; 
    border-bottom: 1px solid rgba(255, 255, 255, 0.3); 
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    position: sticky;
    top: 0;
    z-index: 10;
}
.mobile-menu-close { 
    background: none; 
    border: none; 
    font-size: 22px; 
    color: var(--muted); 
    cursor: pointer; 
    padding: 0; 
}

.mobile-menu-content {
    padding: 0;
    padding-bottom: 20px;
}

.mobile-menu-section {
    border-bottom: 1px solid rgba(243, 244, 246, 0.5);
    padding: 8px 0;
}

/* Auth buttons container at bottom - Positioned after menu items */
.mobile-auth-buttons {
    position: relative;
    padding: 20px;
    background: transparent;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 16px;
}

.mobile-auth-btn {
    padding: 12px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
}

/* Sign In Button - Outlined Style (matching UI image) */
.mobile-auth-btn.signin {
    background: rgba(255, 255, 255, 0.8);
    color: var(--brand-accent);
    border: 2px solid var(--brand-accent);
    box-shadow: 0 2px 8px rgba(6, 182, 212, 0.15);
}

.mobile-auth-btn.signin:hover {
    background: rgba(6, 182, 212, 0.08);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(6,182,212,0.25);
    border-color: var(--brand-accent);
}

/* Sign Up Button - Solid Filled Style (matching UI image) */
.mobile-auth-btn.signup {
    background: linear-gradient(135deg, var(--brand-accent), #0284c7);
    color: white;
    border: 2px solid transparent;
    box-shadow: 0 4px 12px rgba(6,182,212,0.35);
}

.mobile-auth-btn.signup:hover {
    background: linear-gradient(135deg, #0891b2, var(--brand-accent));
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(6,182,212,0.45);
}

.mobile-auth-btn i {
    font-size: 16px;
}

.mobile-menu-item {
    display: flex;
    align-items: center;
    padding: 14px 20px;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    position: relative;
}

.mobile-menu-item:hover {
    background: rgba(6,182,212,0.08); /* Changed from orange */
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: var(--brand-accent);
    box-shadow: 0 2px 8px rgba(6, 182, 212, 0.1); /* Changed from orange */
}

.mobile-menu-item i {
    margin-right: 12px;
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.mobile-menu-item .badge {
    position: relative;
    top: auto;
    right: auto;
    margin-left: auto;
}

/* Expandable Category Items */
.mobile-category-item {
    display: block;
    width: 100%;
    background: none;
    border: none;
    text-align: left;
    padding: 14px 20px;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.mobile-category-item:hover {
    background: rgba(6,182,212,0.08); /* Changed from orange */
    color: var(--brand-accent);
}

.mobile-category-item i.category-icon {
    margin-right: 12px;
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.mobile-category-item .expand-icon {
    transition: transform 0.3s ease;
    font-size: 14px;
    color: #9ca3af;
}

.mobile-category-item.active .expand-icon {
    transform: rotate(180deg);
    color: var(--brand-accent);
}

.mobile-category-item.active {
    background: rgba(6,182,212,0.1); /* Changed from orange */
    color: #111827; /* MODIFIED: Active text color set to dark gray/black */
    font-weight: 700;
}

/* Subcategory Lists */
.mobile-subcategory-list {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(249, 250, 251, 0.3);
}

.mobile-subcategory-list.active {
    max-height: 2000px;
}

.mobile-subcategory-item {
    display: block;
    width: 100%;
    background: none;
    border: none;
    text-align: left;
    padding: 12px 20px 12px 52px;
    color: #4b5563;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-left: 3px solid transparent;
}

.mobile-subcategory-item::before {
    content: '';
    position: absolute;
    left: 32px;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #cbd5e1;
    transition: all 0.2s ease;
}

.mobile-subcategory-item:hover {
    background: rgba(6,182,212,0.06); /* Changed from orange */
    color: var(--brand-accent);
    border-left-color: var(--brand-accent);
}

.mobile-subcategory-item:hover::before {
    background: var(--brand-accent);
    transform: translateY(-50%) scale(1.3);
}

.mobile-subcategory-item.active {
    background: rgba(6,182,212,0.08); /* Changed from orange */
    color: var(--brand-accent);
    border-left-color: var(--brand-accent);
    font-weight: 600;
}

.mobile-subcategory-item.active::before {
    background: var(--brand-accent);
}

.mobile-subcategory-item .expand-icon {
    font-size: 12px;
    color: #9ca3af;
    transition: transform 0.3s ease;
}

.mobile-subcategory-item.active .expand-icon {
    transform: rotate(180deg);
    color: var(--brand-accent);
}

/* Mini Category Lists */
.mobile-mini-category-list {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(241, 245, 249, 0.4);
}

.mobile-mini-category-list.active {
    max-height: 1500px;
}

.mobile-mini-category-item {
    display: block;
    width: 100%;
    background: none;
    border: none;
    text-align: left;
    padding: 10px 20px 10px 68px;
    color: #6b7280;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.mobile-mini-category-item::before {
    content: '→';
    position: absolute;
    left: 52px;
    color: #cbd5e1;
    font-size: 10px;
    transition: all 0.2s ease;
}

.mobile-mini-category-item:hover {
    background: rgba(6,182,212,0.05); /* Changed from orange */
    color: var(--brand-accent);
    padding-left: 72px;
}

.mobile-mini-category-item:hover::before {
    color: var(--brand-accent);
    left: 56px;
}

.mobile-mini-category-item.active {
    background: rgba(6,182,212,0.07); /* Changed from orange */
    color: var(--brand-accent);
    font-weight: 600;
}

.mobile-mini-category-item .expand-icon {
    font-size: 11px;
    color: #9ca3af;
    transition: transform 0.3s ease;
}

.mobile-mini-category-item.active .expand-icon {
    transform: rotate(180deg);
    color: var(--brand-accent);
}

/* Extra Mini Category Lists */
.mobile-extra-category-list {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(237, 242, 247, 0.5);
}

.mobile-extra-category-list.active {
    max-height: 1000px;
}

.mobile-extra-category-link {
    display: block;
    padding: 9px 20px 9px 84px;
    color: #6b7280;
    font-size: 11px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    position: relative;
}

.mobile-extra-category-link::before {
    content: '•';
    position: absolute;
    left: 72px;
    color: #cbd5e1;
    font-size: 14px;
    transition: all 0.2s ease;
}

.mobile-extra-category-link:hover {
    background: rgba(6,182,212,0.04); /* Changed from orange */
    color: var(--brand-accent);
    padding-left: 88px;
}

.mobile-extra-category-link:hover::before {
    color: var(--brand-accent);
    left: 76px;
}

/* Direct category links (when no subcategories) */
.mobile-category-link {
    display: block;
    padding: 14px 20px;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.mobile-category-link i {
    margin-right: 12px;
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.mobile-category-link:hover {
    background: rgba(6,182,212,0.08); /* Changed from orange */
    color: var(--brand-accent);
}

/* Mobile search container */
.mobile-search-bar-container {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    padding: 14px;
    box-shadow: 0 8px 24px rgba(2,6,23,0.1);
    z-index: 999;
    transform: translateY(-12px);
    opacity: 0;
    visibility: hidden;
    transition: transform 0.22s ease, opacity 0.22s ease, visibility 0.22s ease;
    padding-left: 18px;
    padding-right: 18px;
    box-sizing: border-box;
}
.mobile-search-bar-container.active {
    display: block;
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
}
.search-input-group {
    display:flex;
    background: rgba(249, 250, 251, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(230, 233, 238, 0.6);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.search-input { 
    flex:1; 
    border:none; 
    padding:12px 14px; 
    font-size:14px; 
    outline:none; 
    background:transparent; 
    color:#111827; 
}
.search-btn { 
    background:var(--brand-accent); 
    border:none; 
    padding:10px 14px; 
    color:#fff; 
    cursor:pointer;
    transition: all 0.2s ease;
}
.search-btn:hover {
    background: #0284c7; /* Changed from #ea580c to a darker blue */
}

/* Responsive */
@media (max-width: 991px) {
    .mobile-menu-toggle { display:flex; order:1; }
    .logo-area { order:2; position: absolute; left: 50%; transform: translateX(-50%); }
    .header-actions { order:3; margin-left:auto; gap:10px; }
    .main-nav { flex: 0 0 auto; }
    .mega-panel { display: none !important; }
    .main-nav.d-none.d-lg-block { display: none !important; }
}

@media (max-width: 767px) {
    .top-header { padding: 8px 0; }
    .header-content { gap: 8px; padding-left: 12px; padding-right: 12px; }
    .logo-area img { max-height: 36px; }
    .header-actions { gap: 8px; }
}

/* Accessibility improvements */
.mega-panel:focus-within {
    outline: 2px solid var(--brand-accent);
    outline-offset: 2px;
}

/* Smooth transitions for active states */
.main-nav ul > li.has-dropdown.active > a {
    color: var(--brand-accent);
}

/* Glassmorphic shine animation */
@keyframes shine {
    0% {
        background-position: -200% center;
    }
    100% {
        background-position: 200% center;
    }
}

.mega-panel::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    transition: left 0.5s;
}

.mega-panel:hover::before {
    left: 100%;
}

/* Enhanced scrollbar for glassmorphic look */
.mega-panel .mega-grid::-webkit-scrollbar-track {
    background: rgba(241, 245, 249, 0.4);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 3px;
}

.mega-panel .mega-grid::-webkit-scrollbar-thumb {
    background: rgba(203, 213, 225, 0.6);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 3px;
}

.mega-panel .mega-grid::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.8);
}
</style>