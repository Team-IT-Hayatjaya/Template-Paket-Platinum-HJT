<style>
/* ==========================================================================
   TEMA RESMI PANEL ADMIN PAKET PLATINUM — INSTANSI PEMERINTAH
   Warna Utama: Putih (White), Emas (Gold), Biru (Navy/Royal Blue), Hijau (Emerald)
   ========================================================================== */

/* 1. Global SVG & Icon Safety Bounds */
svg.fi-icon {
    display: inline-block !important;
    vertical-align: middle !important;
    flex-shrink: 0 !important;
}

svg.fi-icon.fi-size-xs { width: 0.875rem !important; height: 0.875rem !important; max-width: 0.875rem !important; }
svg.fi-icon.fi-size-sm { width: 1rem !important; height: 1rem !important; max-width: 1rem !important; }
svg.fi-icon.fi-size-md { width: 1.25rem !important; height: 1.25rem !important; max-width: 1.25rem !important; }
svg.fi-icon.fi-size-lg { width: 1.5rem !important; height: 1.5rem !important; max-width: 1.5rem !important; }
svg.fi-icon.fi-size-xl { width: 1.75rem !important; height: 1.75rem !important; max-width: 1.75rem !important; }

/* 2. Skip to Content Accessibility Fix */
.fi-skip-link.fi-sr-only,
.fi-sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border-width: 0 !important;
}

.fi-skip-link.fi-sr-only:focus {
    position: fixed !important;
    top: 1rem !important;
    left: 1rem !important;
    width: auto !important;
    height: auto !important;
    padding: 0.5rem 1rem !important;
    background: #14304F !important;
    color: #ffffff !important;
    z-index: 9999 !important;
    border-radius: 0.5rem !important;
    border: 1px solid #C9A227 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
    clip: auto !important;
}

/* 3. Latar Belakang & Layout Halaman Login: PUTIH Bersih dengan Aksen Emas & Biru */
body.fi-panel-admin .fi-simple-layout {
    min-height: 100vh;
    background: linear-gradient(135deg, #F0F4F8 0%, #FFFFFF 40%, #FFFDF8 70%, #EDF4FB 100%) !important;
    position: relative;
}

body.fi-panel-admin .fi-simple-layout::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: 
        radial-gradient(circle at 50% 10%, rgba(201, 162, 39, 0.08) 0%, transparent 45%),
        radial-gradient(circle at 90% 85%, rgba(29, 78, 216, 0.05) 0%, transparent 40%),
        radial-gradient(circle at 10% 90%, rgba(16, 185, 129, 0.04) 0%, transparent 40%);
    pointer-events: none;
}

body.fi-panel-admin .fi-simple-main-ctn {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 2.5rem 1rem;
}

/* 4. Kartu Form Login: PUTIH Murni, Bingkai EMAS, Bayangan Biru & Emas */
body.fi-panel-admin .fi-simple-main {
    width: 100% !important;
    max-width: 28rem !important;
    background: #FFFFFF !important;
    border: 2px solid rgba(201, 162, 39, 0.45) !important;
    border-radius: 1.5rem !important;
    box-shadow: 0 20px 45px -10px rgba(20, 48, 79, 0.12), 0 0 25px -3px rgba(201, 162, 39, 0.18) !important;
    padding: 2.5rem 2rem !important;
    color: #1e293b !important;
}

/* 5. Form Controls: Label BIRU, Input Putih, Fokus EMAS & HIJAU */
body.fi-panel-admin .fi-simple-main label {
    color: #14304F !important;
    font-size: 0.8125rem !important;
    font-weight: 700 !important;
}

body.fi-panel-admin .fi-simple-main input[type="email"],
body.fi-panel-admin .fi-simple-main input[type="password"],
body.fi-panel-admin .fi-simple-main input[type="text"] {
    background-color: #FFFFFF !important;
    border: 1.5px solid #CBD5E1 !important;
    color: #0F172A !important;
    border-radius: 0.75rem !important;
    padding: 0.65rem 0.875rem !important;
    font-size: 0.875rem !important;
    transition: all 0.2s ease;
}

body.fi-panel-admin .fi-simple-main input[type="email"]:focus,
body.fi-panel-admin .fi-simple-main input[type="password"]:focus,
body.fi-panel-admin .fi-simple-main input[type="text"]:focus {
    border-color: #C9A227 !important;
    box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.25), 0 0 0 1px #1E40AF !important;
    outline: none !important;
    background-color: #FFFFFF !important;
}

/* Checkbox Ingat Saya: Aksen HIJAU (Green) */
body.fi-panel-admin .fi-simple-main input[type="checkbox"] {
    accent-color: #059669 !important;
    border-radius: 0.25rem !important;
}

/* 6. Tombol Masuk: Gradien BIRU (Navy & Royal Blue) dengan List EMAS */
body.fi-panel-admin .fi-simple-main button[type="submit"] {
    background: linear-gradient(135deg, #14304F 0%, #1E40AF 100%) !important;
    border: 1.5px solid #C9A227 !important;
    border-radius: 0.75rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.025em !important;
    padding: 0.75rem 1.25rem !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 14px rgba(20, 48, 79, 0.25), 0 0 10px rgba(201, 162, 39, 0.2) !important;
    transition: all 0.2s ease;
}

body.fi-panel-admin .fi-simple-main button[type="submit"]:hover {
    background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%) !important;
    box-shadow: 0 6px 20px rgba(30, 64, 175, 0.35), 0 0 15px rgba(201, 162, 39, 0.4) !important;
    border-color: #F59E0B !important;
    transform: translateY(-1px);
}

/* 7. Topbar & Header Paket Platinum */
.fi-topbar {
    background: #FFFFFF !important;
    border-bottom: 2px solid rgba(201, 162, 39, 0.3) !important;
    box-shadow: 0 2px 8px rgba(20, 48, 79, 0.05) !important;
}

/* 8. User Avatar Styling: Bingkai EMAS */
.fi-user-avatar {
    border: 2px solid #C9A227 !important;
    box-shadow: 0 0 8px rgba(201, 162, 39, 0.3) !important;
}

/* 9. Sidebar Panel Admin: Elegan, Rapi, & Indikator Aktif Emas */
.fi-sidebar {
    border-right: 1px solid rgba(201, 162, 39, 0.2) !important;
}

.fi-sidebar-group-label {
    color: #14304F !important;
    font-size: 0.72rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    padding-top: 0.75rem !important;
}

.fi-sidebar-item-active > .fi-sidebar-item-btn {
    background: linear-gradient(90deg, rgba(20, 48, 79, 0.08) 0%, rgba(201, 162, 39, 0.06) 100%) !important;
    border-left: 3.5px solid #C9A227 !important;
    color: #14304F !important;
    font-weight: 700 !important;
    border-radius: 0 0.5rem 0.5rem 0 !important;
}

.fi-sidebar-item-active .fi-sidebar-item-icon {
    color: #C9A227 !important;
}

/* 10. Dashboard Stats Overview Cards */
.fi-wi-stats-overview-stat {
    border: 1px solid rgba(201, 162, 39, 0.25) !important;
    border-radius: 1rem !important;
    background: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(20, 48, 79, 0.04) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.fi-wi-stats-overview-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(20, 48, 79, 0.08) !important;
    border-color: rgba(201, 162, 39, 0.45) !important;
}

/* 11. Tables & Records Card Styling */
.fi-ta-ctn {
    border: 1px solid rgba(201, 162, 39, 0.2) !important;
    border-radius: 1rem !important;
    box-shadow: 0 4px 14px rgba(20, 48, 79, 0.04) !important;
    overflow: hidden !important;
}

.fi-ta-header-ctn {
    background: #FAFCFE !important;
    border-bottom: 1px solid rgba(201, 162, 39, 0.15) !important;
}
</style>
