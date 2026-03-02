@extends('layouts.admin')

@section('title', '統計儀表板 - 管理後台')
@section('page-title', '統計儀表板')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 25px;
        border-radius: 10px;
        color: white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card.blue {
        background: linear-gradient(135deg, #154666 0%, #184C78 100%);
    }
    
    .stat-card.green {
        background: linear-gradient(135deg, #298073 0%, #438F68 100%);
    }
    
    .stat-card.orange {
        background: linear-gradient(135deg, #184C78 0%, #298073 100%);
    }
    
    .stat-card.purple {
        background: linear-gradient(135deg, #438F68 0%, #5A9C56 100%);
    }
    
    .stat-card.red {
        background: linear-gradient(135deg, #154666 0%, #298073 100%);
    }
    
    .stat-card.teal {
        background: linear-gradient(135deg, #184C78 0%, #5A9C56 100%);
    }
    
    .stat-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }
    
    .stat-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 5px;
    }
    
    .stat-value {
        font-size: 32px;
        font-weight: bold;
    }
    
    .chart-section {
        margin-top: 30px;
    }
    
    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .chart-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .chart-card h3 {
        margin-bottom: 15px;
        color: #154666;
        padding-bottom: 10px;
        border-bottom: 2px solid #298073;
    }
    
    .recent-section {
        margin-top: 30px;
    }
    
    .recent-list {
        list-style: none;
    }
    
    .recent-item {
        padding: 15px;
        border-bottom: 1px solid #ecf0f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .recent-item:last-child {
        border-bottom: none;
    }
    
    .recent-item:hover {
        background: #f0f7f4;
    }
    
    .recent-info {
        flex: 1;
    }
    
    .recent-title {
        font-weight: bold;
        color: #154666;
        margin-bottom: 5px;
    }
    
    .recent-meta {
        font-size: 12px;
        color: #7f8c8d;
    }
    
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-completed {
        background: #d4edda;
        color: #155724;
    }
    
    .status-processing {
        background: #d1ecf1;
        color: #0c5460;
    }
</style>
@endsection

@section('content')
    <h2 style="margin-bottom: 20px; color: #2c3e50;">歡迎回來！這是您的統計概覽</h2>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">👥</div>
            <div class="stat-label">總用戶數</div>
            <div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-icon">📦</div>
            <div class="stat-label">商品總數</div>
            <div class="stat-value">{{ $stats['total_products'] ?? 0 }}</div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-icon">🛒</div>
            <div class="stat-label">訂單總數</div>
            <div class="stat-value">{{ $stats['total_orders'] ?? 0 }}</div>
        </div>
        
        <div class="stat-card purple">
            <div class="stat-icon">💰</div>
            <div class="stat-label">今日訂單</div>
            <div class="stat-value">{{ $stats['today_orders'] ?? 0 }}</div>
        </div>
        
        <div class="stat-card red">
            <div class="stat-icon">📧</div>
            <div class="stat-label">待處理訊息</div>
            <div class="stat-value">{{ $stats['pending_contacts'] ?? 0 }}</div>
        </div>
        
        <div class="stat-card teal">
            <div class="stat-icon">📬</div>
            <div class="stat-label">訂閱用戶</div>
            <div class="stat-value">{{ $stats['mail_subscribers'] ?? 0 }}</div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="chart-section">
        <h2 style="color: #2c3e50; margin-bottom: 20px;">數據分析</h2>
        <div class="chart-grid">
            <div class="chart-card">
                <h3>訂單狀態分布</h3>
                <div style="padding: 20px; text-align: center;">
                    <div style="margin: 10px 0;">
                        <span style="color: #27ae60; font-weight: bold; font-size: 20px;">{{ $stats['paid_orders'] ?? 0 }}</span>
                        <span style="color: #7f8c8d;"> 已付款</span>
                    </div>
                    <div style="margin: 10px 0;">
                        <span style="color: #3498db; font-weight: bold; font-size: 20px;">{{ $stats['delivered_orders'] ?? 0 }}</span>
                        <span style="color: #7f8c8d;"> 已配送</span>
                    </div>
                    <div style="margin: 10px 0;">
                        <span style="color: #e74c3c; font-weight: bold; font-size: 20px;">{{ $stats['pending_orders'] ?? 0 }}</span>
                        <span style="color: #7f8c8d;"> 待處理</span>
                    </div>
                </div>
            </div>
            
            <div class="chart-card">
                <h3>商品分類統計</h3>
                <div style="padding: 20px;">
                    @foreach($stats['products_by_category'] ?? [] as $category => $count)
                    <div style="margin: 15px 0; display: flex; align-items: center; justify-content: space-between;">
                        <span style="color: #2c3e50;">{{ $category }}</span>
                        <div style="flex: 1; margin: 0 15px; height: 8px; background: #ecf0f1; border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; background: #3498db; width: {{ ($count / max($stats['products_by_category'] ?? [1])) * 100 }}%;"></div>
                        </div>
                        <span style="color: #7f8c8d; font-weight: bold;">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="recent-section">
        <h2 style="color: #2c3e50; margin-bottom: 20px;">最近活動</h2>
        <div class="chart-grid">
            <div class="chart-card">
                <h3>最新訂單</h3>
                <ul class="recent-list">
                    @forelse($stats['recent_orders'] ?? [] as $order)
                    <li class="recent-item">
                        <div class="recent-info">
                            <div class="recent-title">訂單 #{{ $order->bill }}</div>
                            <div class="recent-meta">{{ $order->account }} • {{ $order->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <span class="status-badge {{ $order->payed == '1' ? 'status-completed' : 'status-pending' }}">
                            {{ $order->payed == '1' ? '已付款' : '未付款' }}
                        </span>
                    </li>
                    @empty
                    <li class="recent-item">
                        <div class="recent-info">
                            <div class="recent-meta">暫無訂單資料</div>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
            
            <div class="chart-card">
                <h3>最新聯絡訊息</h3>
                <ul class="recent-list">
                    @forelse($stats['recent_contacts'] ?? [] as $contact)
                    <li class="recent-item">
                        <div class="recent-info">
                            <div class="recent-title">{{ $contact->name }}</div>
                            <div class="recent-meta">{{ $contact->email }} • {{ $contact->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <span class="status-badge status-processing">待處理</span>
                    </li>
                    @empty
                    <li class="recent-item">
                        <div class="recent-info">
                            <div class="recent-meta">暫無聯絡訊息</div>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="{{ route('admin.products') }}" class="btn btn-primary">新增商品</a>
        <a href="{{ route('admin.users') }}" class="btn btn-success">管理用戶</a>
        <a href="{{ route('admin.orders') }}" class="btn btn-warning">查看訂單</a>
        <a href="{{ route('admin.contacts') }}" class="btn btn-danger">處理訊息</a>
    </div>
@endsection

