@extends('layouts.admin')

@section('title', '用戶管理 - 管理後台')
@section('page-title', '用戶管理')

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
    
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .badge-admin {
        background: #e74c3c;
        color: white;
    }
    
    .badge-user {
        background: #3498db;
        color: white;
    }
    
    .action-btns {
        display: flex;
        gap: 5px;
    }
    
    .btn-sm {
        padding: 5px 12px;
        font-size: 12px;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 20px;
        list-style: none;
        padding: 0;
    }
    
    .pagination li {
        display: inline-block;
    }
    
    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        text-decoration: none;
        color: #2c3e50;
        transition: all 0.3s;
        display: inline-block;
    }
    
    .pagination a:hover {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }
    
    .pagination .active span {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }
    
    .pagination .disabled span {
        color: #c0c0c0;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50;">用戶列表</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ 新增用戶</a>
    </div>
    
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="搜尋用戶名稱、帳號或email..." onkeyup="searchTable()">
        <select id="filterRole" class="btn" style="width: 200px;" onchange="filterByRole()">
            <option value="">所有權限</option>
            <option value="A">管理員</option>
            <option value="B">一般用戶</option>
        </select>
    </div>
    
    <table class="data-table" id="usersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>帳號</th>
                <th>姓名</th>
                <th>權限</th>
                <th>電話</th>
                <th>配送地址</th>
                <th>註冊時間</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users ?? [] as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->account }}</td>
                <td>{{ $user->name ?? '-' }}</td>
                <td>
                    <span class="badge {{ $user->prvilige == 'A' ? 'badge-admin' : 'badge-user' }}">
                        {{ $user->prvilige == 'A' ? '管理員' : '一般用戶' }}
                    </span>
                </td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>{{ $user->to_address ?? '-' }}</td>
                <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}</td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">編輯</a>
                        <button onclick="deleteUser({{ $user->id }})" class="btn btn-danger btn-sm">刪除</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #7f8c8d; padding: 40px;">
                    暫無用戶資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if(isset($users) && method_exists($users, 'links'))
    <div class="pagination">
        @include('template.pagination_simple', ['paginator' => $users])
    </div>
    @endif
@endsection

@section('scripts')
<script>
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('usersTable');
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
    
    function filterByRole() {
        const select = document.getElementById('filterRole');
        const filter = select.value.toUpperCase();
        const table = document.getElementById('usersTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td')[3];
            if (td) {
                const txtValue = td.textContent || td.innerText;
                if (filter === '' || txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
    }
    
    function deleteUser(id) {
        if (confirm('確定要刪除此用戶嗎？此操作無法復原！')) {
            fetch(`/admin/users/${id}`, {
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
</script>
@endsection
