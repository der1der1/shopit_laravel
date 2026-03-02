<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '管理後台')</title>
    <link rel="stylesheet" href="{{ asset('admin.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Microsoft JhengHei', Arial, sans-serif;
            background: #f5f5f5;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            background: #1a252f;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 12px;
            color: #95a5a6;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-section {
            margin-bottom: 20px;
        }
        
        .menu-section-title {
            padding: 10px 20px;
            font-size: 12px;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .menu-item {
            display: block;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .menu-item:hover {
            background: #34495e;
            border-left-color: #3498db;
        }
        
        .menu-item.active {
            background: #34495e;
            border-left-color: #3498db;
            color: #3498db;
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            display: inline-block;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
            width: calc(100% - 250px);
        }
        
        .top-bar {
            background: white;
            padding: 15px 25px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .top-bar h1 {
            font-size: 24px;
            color: #2c3e50;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info span {
            color: #7f8c8d;
        }
        
        .content-area {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background: #e67e22;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>SHOPIT</h2>
                <p>管理後台系統</p>
            </div>
            
            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">主要功能</div>
                    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <img src="{{ asset('img/icon/dashboard.png') }}" alt="統計儀表板" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 統計儀表板
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">資料管理</div>
                    <a href="{{ route('admin.users') }}" class="menu-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <img src="{{ asset('img/icon/users.png') }}" alt="用戶管理" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 用戶管理
                    </a>
                    <a href="{{ route('admin.products') }}" class="menu-item {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                        <img src="{{ asset('img/icon/products.png') }}" alt="商品管理" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 商品管理
                    </a>
                    <a href="{{ route('admin.orders') }}" class="menu-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                        <img src="{{ asset('img/icon/order.png') }}" alt="訂單管理" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 訂單管理
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">客戶服務</div>
                    <a href="{{ route('admin.contacts') }}" class="menu-item {{ request()->routeIs('admin.contacts') ? 'active' : '' }}">
                        <img src="{{ asset('img/icon/contact.png') }}" alt="聯絡訊息" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 聯絡訊息
                    </a>
                    <a href="{{ route('admin.maillist') }}" class="menu-item {{ request()->routeIs('admin.maillist') ? 'active' : '' }}">
                        <img src="{{ asset('img/icon/email.png') }}" alt="郵件列表" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 郵件列表
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">網站設定</div>
                    <a href="{{ route('admin.marquee') }}" class="menu-item {{ request()->routeIs('admin.marquee') ? 'active' : '' }}">
                        <img src="{{ asset('img/icon/marqee.png') }}" alt="跑馬燈管理" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 跑馬燈管理
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">系統</div>
                    <a href="/" class="menu-item" target="_blank">
                        <img src="{{ asset('img/icon/home.png') }}" alt="前往網站" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 前往網站
                    </a>
                    <a href="{{ route('logout') }}" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <img src="{{ asset('img/icon/logout.png') }}" alt="登出系統" style="width:22px;height:22px;margin-right:10px;vertical-align:middle;"> 登出系統
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <h1>@yield('page-title', '管理後台')</h1>
                <div class="user-info">
                    <span>管理員: {{ session('user_account', 'Admin') }}</span>
                    <span>{{ date('Y-m-d H:i') }}</span>
                </div>
            </div>
            
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    @yield('scripts')
</body>
</html>
