@extends('layouts.admin')

@section('title', '郵件列表 - 管理後台')
@section('page-title', '郵件列表管理')

@section('styles')
<style>
    .search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .search-bar input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
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
    
    .data-table tr:hover {
        background: #f8f9fa;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    
    .action-btns {
        display: flex;
        gap: 5px;
    }
    
    .btn-sm {
        padding: 5px 12px;
        font-size: 12px;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .stat-box {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
    
    .stat-label {
        color: #7f8c8d;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .stat-value {
        color: #2c3e50;
        font-size: 28px;
        font-weight: bold;
    }
    
    /* Toggle Switch 樣式 */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 24px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: #3498db;
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }
</style>
@endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50;">訂閱用戶列表</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.maillist.compose') }}" class="btn btn-primary">📧 發送郵件</a>
            <button onclick="exportList()" class="btn btn-success">匯出列表</button>
        </div>
    </div>
    
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-label">總訂閱數</div>
            <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">啟用中</div>
            <div class="stat-value" style="color: #27ae60;">{{ $stats['active'] ?? 0 }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">已停用</div>
            <div class="stat-value" style="color: #e74c3c;">{{ $stats['inactive'] ?? 0 }}</div>
        </div>
    </div>
    
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="搜尋姓名、Email或標題..." onkeyup="searchTable()">
        <select id="filterStatus" class="btn" style="width: 150px;" onchange="filterByStatus()">
            <option value="">所有狀態</option>
            <option value="1">啟用</option>
            <option value="0">停用</option>
        </select>
    </div>
    
    <table class="data-table" id="mailTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>姓名</th>
                <th>Email</th>
                <th>標題/備註</th>
                <th>狀態</th>
                <th>數量不足通知</th>
                <th>訂閱時間</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mailList ?? [] as $mail)
            <tr data-status="{{ $mail->onoff }}">
                <td>{{ $mail->id }}</td>
                <td><strong>{{ $mail->name }}</strong></td>
                <td>{{ $mail->email }}</td>
                <td>{{ $mail->title }}</td>
                <td>
                    <span class="status-badge {{ $mail->onoff ? 'status-active' : 'status-inactive' }}">
                        {{ $mail->onoff ? '啟用' : '停用' }}
                    </span>
                </td>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" 
                               onchange="toggleStockNotification({{ $mail->id }}, this.checked)"
                               {{ $mail->stock_notification ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </td>
                <td>{{ $mail->created_at ? $mail->created_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                    <div class="action-btns">
                        <button onclick="toggleStatus({{ $mail->id }}, {{ $mail->onoff }})" 
                                class="btn {{ $mail->onoff ? 'btn-warning' : 'btn-success' }} btn-sm">
                            {{ $mail->onoff ? '停用' : '啟用' }}
                        </button>
                        <button onclick="sendEmail('{{ $mail->email }}')" class="btn btn-primary btn-sm">發信</button>
                        <button onclick="deleteMail({{ $mail->id }})" class="btn btn-danger btn-sm">刪除</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #7f8c8d; padding: 40px;">
                    暫無訂閱用戶
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if(isset($mailList) && method_exists($mailList, 'links'))
    <div class="pagination">
        @include('template.pagination_simple', ['paginator' => $mailList])
    </div>
    @endif
@endsection

@section('scripts')
<script>
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('mailTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            tr[i].style.display = found ? '' : 'none';
        }
    }
    
    function filterByStatus() {
        const select = document.getElementById('filterStatus');
        const filter = select.value;
        const table = document.getElementById('mailTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            if (filter === '' || tr[i].dataset.status === filter) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
    
    function toggleStatus(id, currentStatus) {
        const newStatus = currentStatus ? 0 : 1;
        const action = newStatus ? '啟用' : '停用';
        
        if (confirm(`確定要${action}此訂閱嗎？`)) {
            fetch(`/admin/maillist/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`${action}成功！`);
                    location.reload();
                } else {
                    alert(`${action}失敗：` + (data.message || '未知錯誤'));
                }
            })
            .catch(error => {
                alert(`${action}失敗：` + error.message);
            });
        }
    }
    
    function sendEmail(email) {
        window.location.href = `mailto:${email}`;
    }
    
    function deleteMail(id) {
        if (confirm('確定要刪除此訂閱嗎？此操作無法復原！')) {
            fetch(`/admin/maillist/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('刪除成功！');
                    location.reload();
                } else {
                    alert('刪除失敗：' + (data.message || '未知錯誤'));
                }
            })
            .catch(error => {
                alert('刪除失敗：' + error.message);
            });
        }
    }
    
    function exportList() {
        window.location.href = '/admin/maillist/export';
    }
    
    function toggleStockNotification(id, isChecked) {
        fetch(`/admin/maillist/${id}/toggle-stock-notification`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ stock_notification: isChecked ? 1 : 0 })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Optionally show a success message
                console.log('數量不足通知設定已更新');
            } else {
                alert('更新失敗：' + (data.message || '未知錯誤'));
                // Revert the toggle
                location.reload();
            }
        })
        .catch(error => {
            alert('更新失敗：' + error.message);
            // Revert the toggle
            location.reload();
        });
    }
</script>
@endsection
