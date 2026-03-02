@extends('layouts.admin')

@section('title', '訂單管理 - 管理後台')
@section('page-title', '訂單管理')

@section('styles')
<style>
    .filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .filter-bar input,
    .filter-bar select {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .filter-bar input {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-bar select {
        width: 150px;
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
        white-space: nowrap;
    }
    
    .sortable {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 25px !important;
    }
    
    .sortable:hover {
        background: #e9ecef;
    }
    
    .sort-icon {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 10px;
        color: #6c757d;
    }
    
    .sort-icon.active {
        color: #2c3e50;
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
        font-size: 11px;
        font-weight: bold;
        display: inline-block;
        margin: 2px;
    }
    
    .status-paid {
        background: #d4edda;
        color: #155724;
    }
    
    .status-unpaid {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-delivered {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .status-received {
        background: #d4edda;
        color: #155724;
    }
    
    .order-items {
        max-width: 300px;
        font-size: 12px;
        color: #7f8c8d;
    }
    
    .action-btns {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .btn-sm {
        padding: 5px 12px;
        font-size: 12px;
    }
    
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }
    
    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 30px;
        border-radius: 8px;
        width: 80%;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .close:hover {
        color: #000;
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
        <h2 style="color: #2c3e50;">訂單列表</h2>
        <div style="display: flex; gap: 10px;">
            <button onclick="exportOrders()" class="btn btn-success">匯出報表</button>
        </div>
    </div>
    
    <div class="filter-bar">
        <input type="text" id="searchInput" placeholder="搜尋訂單編號或用戶帳號..." onkeyup="searchTable()">
        <select id="filterPaid" onchange="filterTable()">
            <option value="">付款狀態</option>
            <option value="1">已付款</option>
            <option value="0">未付款</option>
        </select>
        <select id="filterDelivered" onchange="filterTable()">
            <option value="">配送狀態</option>
            <option value="1">已配送</option>
            <option value="0">未配送</option>
        </select>
        <select id="filterReceived" onchange="filterTable()">
            <option value="">收貨狀態</option>
            <option value="1">已收貨</option>
            <option value="0">未收貨</option>
        </select>
    </div>
    
    <table class="data-table" id="ordersTable">
        <thead>
            <tr>
                <th class="sortable" data-column="0">訂單編號<span class="sort-icon"></span></th>
                <th class="sortable" data-column="1">訂單金額<span class="sort-icon"></span></th>
                <th class="sortable" data-column="2">用戶帳號<span class="sort-icon"></span></th>
                <th class="sortable" data-column="3">購買商品<span class="sort-icon"></span></th>
                <th class="sortable" data-column="4">付款狀態<span class="sort-icon"></span></th>
                <th class="sortable" data-column="5">配送狀態<span class="sort-icon"></span></th>
                <th class="sortable" data-column="6">收貨狀態<span class="sort-icon"></span></th>
                <th class="sortable" data-column="7">建立時間<span class="sort-icon"></span></th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders ?? [] as $order)
            <tr data-paid="{{ $order->payed }}" data-delivered="{{ $order->delivered }}" data-received="{{ $order->recieved }}">
                <td><strong>#{{ $order->id }}</strong></td>
                <td>$ {{ $order->bill }}</td>
                <td>{{ $order->account }}</td>
                <td>
                    <div class="order-items">
                        {{ Str::limit($order->purchased, 100) }}
                    </div>
                </td>
                <td>
                    <span class="status-badge {{ $order->payed == '1' ? 'status-paid' : 'status-unpaid' }}">
                        {{ $order->payed == '1' ? '已付款' : '未付款' }}
                    </span>
                </td>
                <td>
                    <span class="status-badge {{ $order->delivered == '1' ? 'status-delivered' : 'status-unpaid' }}">
                        {{ $order->delivered == '1' ? '已配送' : '未配送' }}
                    </span>
                </td>
                <td>
                    <span class="status-badge {{ $order->recieved == '1' ? 'status-received' : 'status-unpaid' }}">
                        {{ $order->recieved == '1' ? '已收貨' : '未收貨' }}
                    </span>
                </td>
                <td>{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                    <div class="action-btns">
                        <button onclick="viewOrder({{ $order->id }})" class="btn btn-primary btn-sm">查看</button>
                        <button onclick="updateStatus({{ $order->id }})" class="btn btn-warning btn-sm">更新</button>
                        <button onclick="deleteOrder({{ $order->id }})" class="btn btn-danger btn-sm">刪除</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #7f8c8d; padding: 40px;">
                    暫無訂單資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if(isset($orders) && method_exists($orders, 'links'))
    <div class="pagination">
        @include('template.pagination_simple', ['paginator' => $orders])
    </div>
    @endif
    
    <!-- View Order Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>訂單詳情</h2>
            <div id="orderDetails"></div>
        </div>
    </div>
    
    <!-- Update Status Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStatusModal()">&times;</span>
            <h2>更新訂單狀態</h2>
            <div id="statusForm"></div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('ordersTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < 3; j++) {
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            if (found) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
    
    function filterTable() {
        const paidFilter = document.getElementById('filterPaid').value;
        const deliveredFilter = document.getElementById('filterDelivered').value;
        const receivedFilter = document.getElementById('filterReceived').value;
        const table = document.getElementById('ordersTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            let show = true;
            
            if (paidFilter && tr[i].dataset.paid !== paidFilter) show = false;
            if (deliveredFilter && tr[i].dataset.delivered !== deliveredFilter) show = false;
            if (receivedFilter && tr[i].dataset.received !== receivedFilter) show = false;
            
            tr[i].style.display = show ? '' : 'none';
        }
    }
    
    function viewOrder(id) {
        fetch(`/admin/orders/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('orderDetails').innerHTML = formatOrderDetails(data.order);
                    document.getElementById('orderModal').style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    function formatOrderDetails(order) {
        return `
            <p><strong>訂單編號:</strong> ${order.bill}</p>
            <p><strong>用戶帳號:</strong> ${order.account}</p>
            <p><strong>購買商品:</strong></p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">
                ${order.purchased}
            </div>
            <p><strong>付款狀態:</strong> ${order.payed == '1' ? '已付款' : '未付款'}</p>
            <p><strong>配送狀態:</strong> ${order.delivered == '1' ? '已配送' : '未配送'}</p>
            <p><strong>收貨狀態:</strong> ${order.recieved == '1' ? '已收貨' : '未收貨'}</p>
            <p><strong>建立時間:</strong> ${order.created_at}</p>
        `;
    }
    
    function updateStatus(id) {
        fetch(`/admin/orders/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const order = data.order;
                    document.getElementById('statusForm').innerHTML = `
                        <form onsubmit="submitStatus(event, ${id})">
                            <div style="margin: 15px 0;">
                                <label>
                                    <input type="checkbox" name="payed" value="1" ${order.payed == '1' ? 'checked' : ''}>
                                    已付款
                                </label>
                            </div>
                            <div style="margin: 15px 0;">
                                <label>
                                    <input type="checkbox" name="delivered" value="1" ${order.delivered == '1' ? 'checked' : ''}>
                                    已配送
                                </label>
                            </div>
                            <div style="margin: 15px 0;">
                                <label>
                                    <input type="checkbox" name="recieved" value="1" ${order.recieved == '1' ? 'checked' : ''}>
                                    已收貨
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">更新狀態</button>
                        </form>
                    `;
                    document.getElementById('statusModal').style.display = 'block';
                }
            });
    }
    
    function submitStatus(e, id) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        fetch(`/admin/orders/${id}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('狀態更新成功！');
                location.reload();
            }
        });
    }
    
    function closeModal() {
        document.getElementById('orderModal').style.display = 'none';
    }
    
    function closeStatusModal() {
        document.getElementById('statusModal').style.display = 'none';
    }
    
    function deleteOrder(id) {
        if (confirm('確定要刪除此訂單嗎？此操作無法復原！')) {
            fetch(`/admin/orders/${id}`, {
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
                }
            });
        }
    }
    
    function exportOrders() {
        window.location.href = '/admin/orders/export';
    }
    
    
    // 排序功能
    let currentSortColumn = null;
    let currentSortState = 0; // 0: 原始, 1: 升序, 2: 降序
    let originalOrder = [];
    
    // 儲存原始順序
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('ordersTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = Array.from(tbody.getElementsByTagName('tr'));
        originalOrder = rows.map(row => row.cloneNode(true));
        
        // 為每個可排序的表頭添加點擊事件
        const sortHeaders = document.querySelectorAll('.sortable');
        sortHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const column = parseInt(this.getAttribute('data-column'));
                sortTable(column, this);
            });
        });
    });
    
    function sortTable(columnIndex, headerElement) {
        const table = document.getElementById('ordersTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = Array.from(tbody.getElementsByTagName('tr'));
        
        // 移除所有其他列的排序指示器
        const allHeaders = document.querySelectorAll('.sortable');
        allHeaders.forEach(header => {
            if (header !== headerElement) {
                header.querySelector('.sort-icon').textContent = '';
                header.querySelector('.sort-icon').classList.remove('active');
            }
        });
        
        // 如果點擊的是新列，重置狀態為升序
        if (currentSortColumn !== columnIndex) {
            currentSortColumn = columnIndex;
            currentSortState = 1;
        } else {
            // 相同列，切換狀態
            currentSortState = (currentSortState + 1) % 3;
        }
        
        const sortIcon = headerElement.querySelector('.sort-icon');
        
        // 根據狀態排序
        if (currentSortState === 0) {
            // 恢復原始順序
            tbody.innerHTML = '';
            originalOrder.forEach(row => {
                tbody.appendChild(row.cloneNode(true));
            });
            sortIcon.textContent = '';
            sortIcon.classList.remove('active');
        } else if (currentSortState === 1) {
            // 升序
            rows.sort((a, b) => {
                return compareValues(a, b, columnIndex, true);
            });
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
            sortIcon.textContent = '▲';
            sortIcon.classList.add('active');
        } else {
            // 降序
            rows.sort((a, b) => {
                return compareValues(a, b, columnIndex, false);
            });
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
            sortIcon.textContent = '▼';
            sortIcon.classList.add('active');
        }
    }
    
    function compareValues(rowA, rowB, columnIndex, ascending) {
        const cellA = rowA.getElementsByTagName('td')[columnIndex];
        const cellB = rowB.getElementsByTagName('td')[columnIndex];
        
        if (!cellA || !cellB) return 0;
        
        let valueA = cellA.textContent.trim();
        let valueB = cellB.textContent.trim();
        
        // 移除訂單編號的 # 符號
        if (columnIndex === 0) {
            valueA = valueA.replace('#', '');
            valueB = valueB.replace('#', '');
        }
        
        // 判斷是否為數字
        const numA = parseFloat(valueA);
        const numB = parseFloat(valueB);
        
        let comparison = 0;
        
        if (!isNaN(numA) && !isNaN(numB)) {
            // 數字比較
            comparison = numA - numB;
        } else {
            // 字串比較
            comparison = valueA.localeCompare(valueB, 'zh-TW');
        }
        
        return ascending ? comparison : -comparison;
    }
</script>
@endsection
