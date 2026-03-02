@extends('layouts.admin')

@section('title', '跑馬燈管理 - 管理後台')
@section('page-title', '跑馬燈管理')

@section('styles')
<style>
    .add-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .data-table th {
        background: #f8f9fa;
        padding: 12px;
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
    
    .marquee-preview {
        background: #2c3e50;
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
        overflow: hidden;
        white-space: nowrap;
    }
    
    .marquee-text {
        display: inline-block;
        padding-left: 100%;
        animation: marquee 25s linear infinite;
    }
    
    @keyframes marquee {
        0% {
            transform: translate(0, 0);
        }
        100% {
            transform: translate(-100%, 0);
        }
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
    <h2 style="color: #2c3e50; margin-bottom: 20px;">跑馬燈訊息管理</h2>
    
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
    
    <!-- Add New Marquee Form -->
    <div class="add-form">
        <h3 style="margin-bottom: 15px; color: #2c3e50;">新增跑馬燈訊息</h3>
        <form action="{{ route('admin.marquee.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="texts">訊息內容</label>
                <textarea id="texts" name="texts" required placeholder="輸入跑馬燈要顯示的訊息..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">+ 新增訊息</button>
        </form>
    </div>
    
    <!-- Preview Section -->
    <div style="margin-bottom: 25px;">
        <h3 style="margin-bottom: 10px; color: #2c3e50;">跑馬燈預覽</h3>
        <div class="marquee-preview">
            <div class="marquee-text">
                @forelse($marquees ?? [] as $marquee)
                    {{ $marquee->texts }} &nbsp;&nbsp;&nbsp; ★ &nbsp;&nbsp;&nbsp;
                @empty
                    歡迎來到 SHOPIT 購物網站！
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Marquee List -->
    <h3 style="margin-bottom: 15px; color: #2c3e50;">所有跑馬燈訊息</h3>
    <div class="drag-info">
        ℹ️ 提示：您可以拖拉訊息來重新排序，系統會自動儲存新的順序
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">順序</th>
                <!-- <th>ID</th> -->
                <th>訊息內容</th>
                <th>建立時間</th>
                <th>最後更新</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sortable-tbody">
            @forelse($marquees ?? [] as $marquee)
            <tr data-id="{{ $marquee->id }}">
                <td>
                    <span class="drag-handle">☰</span>
                </td>
                <!-- <td>{{ $marquee->id }}</td> -->
                <td>{{ $marquee->texts }}</td>
                <td>{{ $marquee->created_at ? $marquee->created_at->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $marquee->updated_at ? $marquee->updated_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                    <div class="action-btns">
                        <button onclick="editMarquee({{ $marquee->id }}, '{{ addslashes($marquee->texts) }}')" 
                                class="btn btn-primary btn-sm">編輯</button>
                        <form action="{{ route('admin.marquee.destroy', $marquee->id) }}" 
                              method="POST" 
                              style="display: inline;"
                              onsubmit="return confirm('確定要刪除此訊息嗎？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">刪除</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 40px;">
                    暫無跑馬燈訊息
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Edit Modal -->
    <div id="editModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div style="background-color: white; margin: 10% auto; padding: 30px; border-radius: 8px; width: 80%; max-width: 600px;">
            <h3 style="margin-bottom: 20px; color: #2c3e50;">編輯跑馬燈訊息</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit_texts">訊息內容</label>
                    <textarea id="edit_texts" name="texts" required></textarea>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <button type="button" onclick="closeEditModal()" class="btn" style="background: #95a5a6; color: white;">取消</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<!-- Include SortableJS from CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    function editMarquee(id, texts) {
        document.getElementById('edit_texts').value = texts;
        document.getElementById('editForm').action = `/admin/marquee/${id}`;
        document.getElementById('editModal').style.display = 'block';
    }
    
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
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
                    fetch('/admin/marquee/update-order', {
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
                            // You can show a success message here if needed
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
