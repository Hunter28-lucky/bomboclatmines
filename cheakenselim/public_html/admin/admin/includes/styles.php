<style>
    :root {
        --main-color: #00ECBE;
        --main-gradient-start: #7AFEC3;
        --main-gradient-end: #02AFB6;
        --main_gradient-color: linear-gradient(180deg, var(--main-gradient-start), var(--main-gradient-end));
        --main_gradient-color2: linear-gradient(90deg, var(--main-gradient-start), var(--main-gradient-end));
        --bg_HomeModule_Stroke: #224BA2;
        --bg_HomeModule_Padding: linear-gradient(180deg, #001C54 0%, #000C33 100%);
        --norm_red-color: #D23838;
        --norm_green-color: #17B15E;
        --norm_secondary-color: #DD9138;
        --norm_Purple-color: #9B48DB;
        --norm_bule-color: #5088D3;
        --button_dis_color: #3D4863;
        --Secondary_red_color: #FD565C;
        --Secondary_green_color: #40AD72;
        --Secondary_Color1: #FFEBEC;
        --Secondary_Color2: #DAFFEB;
        --Secondary_moto_Color9: #239DA1;
        --Secondary_moto_Color8: #163B86;
        --text_color_L1: #E3EFFF;
        --text_color_L2: #92A8E3;
        --text_color_L3: #6F80A4;
        --text_color_L4: #05012B;
        --bg_color_L1: #05012B;
        --bg_color_L2: #011341;
        --bg_color_L3: #001C54;
        --Dividing-line_color: #022C68;
        --sheet_nva_color: #2C5ECA;
        --sheet_detail_bg_color: #000C33;
        --icon1: rgba(12, 195, 159, .6);
        --icon2: rgba(12, 195, 159, .25);
        --tab1: #21D9CC;
        --tab2: #BED921;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    body {
        background: var(--bg_HomeModule_Padding);
        min-height: 100vh;
        color: var(--text_color_L1);
    }
    
    .admin-layout {
        display: flex;
        min-height: 100vh;
    }
    
    /* Sidebar */
    .sidebar {
        width: 280px;
        background: var(--bg_color_L2);
        border-right: 2px solid var(--Dividing-line_color);
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.3);
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        z-index: 1000;
    }
    
    .sidebar-header {
        padding: 25px 20px;
        border-bottom: 2px solid var(--Dividing-line_color);
        text-align: center;
    }
    
    .sidebar-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .sidebar-chicken-icon {
        width: 45px;
        height: 45px;
        background: var(--main_gradient-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-right: 12px;
        box-shadow: 0 4px 15px rgba(0, 236, 190, 0.4);
    }
    
    .sidebar-title {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--text_color_L1);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .sidebar-subtitle {
        font-size: 0.8rem;
        color: var(--text_color_L2);
    }
    
    .admin-info {
        padding: 15px 20px;
        border-bottom: 1px solid var(--Dividing-line_color);
    }
    
    .admin-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--main-color);
        margin-bottom: 5px;
    }
    
    .admin-role {
        font-size: 0.75rem;
        color: var(--text_color_L2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .sidebar-nav {
        padding: 20px 0;
    }
    
    .nav-section {
        margin-bottom: 25px;
    }
    
    .nav-section-title {
        padding: 0 20px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text_color_L3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .nav-item {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: var(--text_color_L2);
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    
    .nav-item:hover {
        background: rgba(0, 236, 190, 0.1);
        color: var(--text_color_L1);
        border-left-color: var(--main-color);
    }
    
    .nav-item.active {
        background: rgba(0, 236, 190, 0.15);
        color: var(--main-color);
        border-left-color: var(--main-color);
    }
    
    .nav-icon {
        width: 20px;
        margin-right: 12px;
        font-size: 1.1rem;
    }
    
    .nav-text {
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .nav-badge {
        margin-left: auto;
        background: var(--Secondary_red_color);
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
    }
    
    /* Main Content */
    .main-content {
        flex: 1;
        margin-left: 280px;
        padding: 30px;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: bold;
        color: var(--text_color_L1);
        margin-bottom: 8px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .page-subtitle {
        color: var(--text_color_L2);
        font-size: 1rem;
    }
    
    /* Statistics Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: var(--bg_color_L2);
        border-radius: 15px;
        padding: 25px;
        border: 2px solid var(--Dividing-line_color);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
    }
    
    .stat-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 15px;
    }
    
    .stat-icon.users { background: rgba(0, 236, 190, 0.2); color: var(--main-color); }
    .stat-icon.transactions { background: rgba(64, 173, 114, 0.2); color: var(--Secondary_green_color); }
    .stat-icon.pending { background: rgba(221, 145, 56, 0.2); color: var(--norm_secondary-color); }
    .stat-icon.bets { background: rgba(155, 72, 219, 0.2); color: var(--norm_Purple-color); }
    .stat-icon.revenue { background: rgba(80, 136, 211, 0.2); color: var(--norm_bule-color); }
    .stat-icon.withdrawals { background: rgba(253, 86, 92, 0.2); color: var(--Secondary_red_color); }
    
    .stat-info h3 {
        font-size: 0.9rem;
        color: var(--text_color_L2);
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: bold;
        color: var(--text_color_L1);
    }
    
    /* Content Sections */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }
    
    .content-card {
        background: var(--bg_color_L2);
        border-radius: 15px;
        padding: 25px;
        border: 2px solid var(--Dividing-line_color);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
    }
    
    .content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--Dividing-line_color);
    }
    
    .content-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text_color_L1);
    }
    
    .content-link {
        color: var(--main-color);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .content-link:hover {
        text-decoration: underline;
    }
    
    .list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(2, 44, 104, 0.3);
    }
    
    .list-item:last-child {
        border-bottom: none;
    }
    
    .list-info h4 {
        font-size: 0.9rem;
        color: var(--text_color_L1);
        margin-bottom: 3px;
    }
    
    .list-info p {
        font-size: 0.8rem;
        color: var(--text_color_L2);
    }
    
    .list-amount {
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .amount-positive { color: var(--Secondary_green_color); }
    .amount-negative { color: var(--Secondary_red_color); }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .status-pending { background: rgba(221, 145, 56, 0.2); color: var(--norm_secondary-color); }
    .status-approved { background: rgba(64, 173, 114, 0.2); color: var(--Secondary_green_color); }
    .status-rejected { background: rgba(253, 86, 92, 0.2); color: var(--Secondary_red_color); }
    
    /* Buttons */
    .btn {
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    
    .btn-primary {
        background: var(--main_gradient-color);
        color: var(--text_color_L4);
        box-shadow: 0 4px 15px rgba(0, 236, 190, 0.4);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 236, 190, 0.6);
    }
    
    .btn-secondary {
        background: var(--bg_color_L3);
        color: var(--text_color_L1);
        border: 2px solid var(--Dividing-line_color);
    }
    
    .btn-secondary:hover {
        background: var(--Dividing-line_color);
    }
    
    .btn-danger {
        background: var(--Secondary_red_color);
        color: white;
    }
    
    .btn-danger:hover {
        background: #e53e3e;
        transform: translateY(-2px);
    }
    
    .btn-success {
        background: var(--Secondary_green_color);
        color: white;
    }
    
    .btn-success:hover {
        background: #38a169;
        transform: translateY(-2px);
    }
    
    /* Forms */
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text_color_L1);
        margin-bottom: 8px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border-radius: 10px;
        background: var(--bg_color_L3);
        border: 2px solid var(--Dividing-line_color);
        color: var(--text_color_L1);
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--main-color);
        box-shadow: 0 0 0 3px rgba(0, 236, 190, 0.3);
    }
    
    /* Tables */
    .table-container {
        background: var(--bg_color_L2);
        border-radius: 15px;
        padding: 25px;
        border: 2px solid var(--Dividing-line_color);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        overflow-x: auto;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th {
        background: var(--bg_color_L3);
        color: var(--text_color_L1);
        font-weight: 600;
        padding: 15px 12px;
        text-align: left;
        border-bottom: 2px solid var(--Dividing-line_color);
    }
    
    .table td {
        padding: 12px;
        border-bottom: 1px solid rgba(2, 44, 104, 0.3);
        color: var(--text_color_L2);
    }
    
    .table tr:hover {
        background: rgba(0, 236, 190, 0.05);
    }
    
    /* Messages */
    .message {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    
    .message.success {
        background: rgba(64, 173, 114, 0.2);
        border: 2px solid rgba(64, 173, 114, 0.3);
        color: var(--Secondary_green_color);
    }
    
    .message.error {
        background: rgba(253, 86, 92, 0.2);
        border: 2px solid rgba(253, 86, 92, 0.3);
        color: var(--Secondary_red_color);
    }
    
    .message.warning {
        background: rgba(221, 145, 56, 0.2);
        border: 2px solid rgba(221, 145, 56, 0.3);
        color: var(--norm_secondary-color);
    }
    
    .message.info {
        background: rgba(80, 136, 211, 0.2);
        border: 2px solid rgba(80, 136, 211, 0.3);
        color: var(--norm_bule-color);
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar.open {
            transform: translateX(0);
        }
        
        .main-content {
            margin-left: 0;
        }
        
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .main-content {
            padding: 20px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
        
        .table-container {
            padding: 15px;
        }
        
        .table th,
        .table td {
            padding: 8px 6px;
            font-size: 0.85rem;
        }
    }
</style> 