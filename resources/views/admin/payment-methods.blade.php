@extends('layouts.admin')

@section('title', '付款方式管理 - 管理後台')
@section('page-title', '付款方式管理')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .stat-card h4 {
        color: #7f8c8d;
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    .stat-card .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .data-table th {
        background: #f8f9fa;
        padding: 15px 12px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .data-table tbody tr {
        transition: background 0.2s;
    }
    
    .data-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .data-table tbody tr.sortable-ghost {
        opacity: 0.4;
        background: #e9ecef;
    }
    
    .data-table tbody tr.sortable-chosen {
        background: #e3f2fd;
    }
    
    .data-table tbody tr.sortable-drag {
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .drag-handle {
        cursor: move;
        cursor: grab;
        color: #95a5a6;
        font-size: 18px;
        padding: 0 5px;
        user-select: none;
    }
    
    .drag-handle:active {
        cursor: grabbing;
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-delete {
        background: #e2e3e5;
        color: #383d41;
    }
    
    .action-btns {
        display: flex;
        gap: 5px;
    }
    
    .btn-sm {
        padding: 5px 12px;
        font-size: 12px;
    }
    
    .alert {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .icon-preview {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }
    
    .no-icon {
        width: 40px;
        height: 40px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        color: #95a5a6;
        font-size: 12px;
    }
    
    .key-info {
        font-size: 12px;
        color: #7f8c8d;
    }
    
    .has-keys {
        color: #27ae60;
    }
    
    .no-keys {
        color: #e74c3c;
    }
    
    .drag-info {
        background: #e3f2fd;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        color: #1976d2;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; margin: 0;">付款方式管理</h2>
        <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">+ 新增付款方式</a>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
    @endif
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>總計</h4>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <h4>啟用中</h4>
            <div class="stat-value" style="color: #27ae60;">{{ $stats['active'] }}</div>
        </div>
        <div class="stat-card">
            <h4>停用中</h4>
            <div class="stat-value" style="color: #e74c3c;">{{ $stats['inactive'] }}</div>
        </div>
    </div>
    
    <!-- Payment Methods List -->
    <h3 style="margin-bottom: 15px; color: #2c3e50;">所有付款方式</h3>
    <div class="drag-info">
        ℹ️ 提示：您可以拖拉項目來重新排序，系統會自動儲存新的順序
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">順序</th>
                <th style="width: 60px;">圖標</th>
                <th>付款方式名稱</th>
                <th>描述</th>
                <th>手續費</th>
                <th>設定狀態</th>
                <th>狀態</th>
                <th>建立時間</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sortable-tbody">
            @forelse($paymentMethods as $method)
            @if($method->status !== 'delete')
            <tr data-id="{{ $method->id }}">
                <td>
                    <span class="drag-handle">☰</span>
                </td>
                <td>
                    @if($method->icon)
                        <img src="{{ asset($method->icon) }}" alt="{{ $method->method_name }}" class="icon-preview">
                    @else
                        <div class="no-icon">無</div>
                    @endif
                </td>
                <td><strong>{{ $method->method_name }}</strong></td>
                <td>{{ Str::limit($method->description, 50) ?: '-' }}</td>
                <td>
                    @if($method->fee_percentage > 0 || $method->fee_fixed > 0)
                        <div style="font-size: 13px;">
                            @if($method->fee_percentage > 0)
                                <div>{{ $method->fee_percentage }}%</div>
                            @endif
                            @if($method->fee_fixed > 0)
                                <div>+${{ number_format($method->fee_fixed, 0) }}</div>
                            @endif
                        </div>
                    @else
                        <span style="color: #7f8c8d;">-</span>
                    @endif
                </td>
                <td>
                    <div class="key-info">
                        <div class="{{ $method->api_key || $method->merchant_id ? 'has-keys' : 'no-keys' }}">
                            正式: {{ $method->api_key || $method->merchant_id ? '✓' : '✗' }}
                        </div>
                        <div class="{{ $method->sandbox_api_key || $method->sandbox_merchant_id ? 'has-keys' : 'no-keys' }}">
                            測試: {{ $method->sandbox_api_key || $method->sandbox_merchant_id ? '✓' : '✗' }}
                        </div>
                    </div>
                </td>
                <td>
                    <span class="status-badge status-{{ $method->status }}">
                        @if($method->status == 'active') 啟用
                        @elseif($method->status == 'inactive') 停用
                        @else 已刪除
                        @endif
                    </span>
                </td>
                <td>{{ $method->created_at ? $method->created_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="btn btn-primary btn-sm">編輯</a>
                        <button onclick="deletePaymentMethod({{ $method->id }})" class="btn btn-danger btn-sm">刪除</button>
                    </div>
                </td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="9" style="text-align: center; color: #7f8c8d; padding: 40px;">
                    暫無付款方式
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection

@section('scripts')
<!-- Include SortableJS from CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    function deletePaymentMethod(id) {
        if (!confirm('確定要刪除此付款方式嗎？')) {
            return;
        }
        
        fetch(`/admin/payment-methods/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('刪除失敗：' + data.message);
            }
        })
        .catch(error => {
            console.error('錯誤:', error);
            alert('刪除時發生錯誤');
        });
    }
    
    // Initialize SortableJS for drag and drop
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.getElementById('sortable-tbody');
        
        if (tbody && tbody.children.length > 0) {
            new Sortable(tbody, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    // Get the new order
                    const order = [];
                    const rows = tbody.querySelectorAll('tr[data-id]');
                    rows.forEach(function(row) {
                        const id = row.getAttribute('data-id');
                        if (id) {
                            order.push(id);
                        }
                    });
                    
                    // Send AJAX request to update order
                    fetch('/admin/payment-methods/update-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('順序已更新');
                        } else {
                            console.error('更新失敗:', data.message);
                            alert('更新順序失敗：' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('錯誤:', error);
                        alert('更新順序時發生錯誤');
                    });
                }
            });
        }
    });
</script>
@endsection
