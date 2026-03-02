@extends('layouts.admin')

@section('title', '商品管理 - 管理後台')
@section('page-title', '商品管理')

@section('styles')
<style>
    .search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .search-bar input,
    .search-bar select {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .search-bar input {
        flex: 1;
    }
    
    .search-bar select {
        width: 200px;
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
    
    .product-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .price-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    
    .current-price {
        color: #e74c3c;
        font-weight: bold;
        font-size: 16px;
    }
    
    .original-price {
        color: #95a5a6;
        text-decoration: line-through;
        font-size: 12px;
    }
    
    .category-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
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
</style>
@endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50;">商品列表</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ 新增商品</a>
    </div>
    
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="搜尋商品名稱或描述..." onkeyup="searchTable()">
        <select id="filterCategory" onchange="filterByCategory()">
            <option value="">所有分類</option>
            @foreach($categories ?? [] as $category)
            <option value="{{ $category }}">{{ $category }}</option>
            @endforeach
        </select>
    </div>
    
    <table class="data-table" id="productsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>圖片</th>
                <th>商品名稱</th>
                <th>描述</th>
                <th>分類</th>
                <th>價格</th>
                <th>精選</th>
                <th>建立時間</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products ?? [] as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>
                    @if($product->pic_dir)
                    <img src="{{ asset($product->pic_dir) }}" alt="{{ $product->product_name }}" class="product-img">
                    @else
                    <div class="product-img" style="background: #ecf0f1; display: flex; align-items: center; justify-content: center; color: #95a5a6;">無圖</div>
                    @endif
                </td>
                <td><strong>{{ $product->product_name }}</strong></td>
                <td>{{ Str::limit($product->description, 50) }}</td>
                <td><span class="category-badge">{{ $product->category }}</span></td>
                <td>
                    <div class="price-info">
                        <span class="current-price">NT$ {{ number_format($product->price) }}</span>
                        @if($product->ori_price && $product->ori_price != $product->price)
                        <span class="original-price">NT$ {{ number_format($product->ori_price) }}</span>
                        @endif
                    </div>
                </td>
                <td>{{ $product->selected == '1' ? '✓' : '-' }}</td>
                <td>{{ $product->created_at ? $product->created_at->format('Y-m-d') : '-' }}</td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">編輯</a>
                        <button onclick="deleteProduct({{ $product->id }})" class="btn btn-danger btn-sm">刪除</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; color: #7f8c8d; padding: 40px;">
                    暫無商品資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if(isset($products) && method_exists($products, 'links'))
    <div class="pagination">
        @include('template.pagination_simple', ['paginator' => $products])
    </div>
    @endif
@endsection

@section('scripts')
<script>
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('productsTable');
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
    
    function filterByCategory() {
        const select = document.getElementById('filterCategory');
        const filter = select.value.toUpperCase();
        const table = document.getElementById('productsTable');
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td')[4];
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
    
    function deleteProduct(id) {
        if (confirm('確定要刪除此商品嗎？此操作無法復原！')) {
            fetch(`/admin/products/${id}`, {
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
