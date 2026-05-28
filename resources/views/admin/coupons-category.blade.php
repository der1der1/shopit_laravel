@extends('layouts.admin')

@section('title', '分類商品折扣 - 管理後台')
@section('page-title', '分類商品折扣設定')

@section('styles')
<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #3498db;
        font-size: 14px;
        text-decoration: none;
        margin-bottom: 20px;
    }
    .back-link:hover { text-decoration: underline; }

    .form-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 28px 30px;
        margin-bottom: 24px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        max-width: 720px;
    }

    .form-card h3 {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e8f5e9;
    }

    .form-row {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 20px;
    }

    .form-row label {
        font-size: 14px;
        font-weight: 600;
        color: #555;
    }

    .form-row input[type="number"],
    .form-row input[type="date"] {
        padding: 10px 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        width: 100%;
        outline: none;
        transition: border-color 0.2s;
    }

    .form-row input:focus { border-color: #4caf50; }

    .date-range {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .date-range span { color: #7f8c8d; font-size: 13px; white-space: nowrap; }
    .date-range input { flex: 1; }

    /* Toggle */
    .toggle-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }
    .toggle-label { font-size: 14px; font-weight: 600; color: #555; }
    .toggle-switch { position: relative; display: inline-block; width: 52px; height: 28px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; cursor: pointer; inset: 0;
        background: #ccc; border-radius: 28px; transition: 0.3s;
    }
    .toggle-slider:before {
        content: ''; position: absolute;
        width: 22px; height: 22px; left: 3px; bottom: 3px;
        background: white; border-radius: 50%; transition: 0.3s;
    }
    .toggle-switch input:checked + .toggle-slider { background: #4caf50; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(24px); }
    .toggle-status { font-size: 13px; color: #7f8c8d; }
    .toggle-status.on { color: #27ae60; font-weight: 600; }
    .toggle-status.off { color: #e74c3c; }

    /* Category checkboxes */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 10px;
        margin-top: 8px;
    }

    .category-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 7px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        color: #444;
        background: #fafafa;
        user-select: none;
    }

    .category-item:hover { border-color: #4caf50; background: #f1f8e9; }

    .category-item input[type="checkbox"] {
        accent-color: #4caf50;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        cursor: pointer;
    }

    .category-item.checked {
        border-color: #4caf50;
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 600;
    }

    .hint { font-size: 12px; color: #95a5a6; margin-top: 4px; }

    .select-actions {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }

    .btn-text {
        background: none;
        border: none;
        color: #4caf50;
        font-size: 13px;
        cursor: pointer;
        padding: 0;
        text-decoration: underline;
    }

    .btn-text:hover { color: #2e7d32; }

    .form-actions { display: flex; gap: 12px; margin-top: 8px; }

    .btn-save {
        padding: 11px 28px; background: #4caf50; color: #fff;
        border: none; border-radius: 7px; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: background 0.2s;
    }
    .btn-save:hover { background: #388e3c; }

    .btn-cancel {
        padding: 11px 22px; background: #f5f5f5; color: #555;
        border: 1px solid #ddd; border-radius: 7px; font-size: 14px;
        font-weight: 600; cursor: pointer; text-decoration: none;
        display: inline-block; transition: background 0.2s;
    }
    .btn-cancel:hover { background: #ebebeb; color: #333; text-decoration: none; }

    .preview-badge {
        display: inline-block; padding: 4px 12px; background: #fff3e0;
        color: #e65100; border-radius: 20px; font-size: 12px; font-weight: 600;
        border: 1px solid #ffcc80; margin-left: 10px; vertical-align: middle;
    }
</style>
@endsection

@section('content')
<a href="{{ route('admin.coupons') }}" class="back-link">← 返回優惠券管理</a>

<div style="display:flex; align-items:center; margin-bottom:20px;">
    <h2 style="color:#2c3e50; margin:0;">分類商品折扣設定</h2>
    <span class="preview-badge">僅供展示</span>
</div>

<div class="form-card">
    <h3>🏷️ 分類商品折扣設定</h3>

    <!-- Toggle -->
    <div class="toggle-row">
        <span class="toggle-label">啟用分類折扣</span>
        <label class="toggle-switch">
            <input type="checkbox" id="cat-toggle" onchange="updateStatus(this)">
            <span class="toggle-slider"></span>
        </label>
        <span class="toggle-status off" id="cat-status">已停用</span>
    </div>

    <!-- 商品分類勾選 -->
    <div class="form-row">
        <label>套用折扣的商品分類</label>
        <div class="select-actions">
            <button class="btn-text" onclick="selectAll()">全選</button>
            <span style="color:#ccc;">|</span>
            <button class="btn-text" onclick="clearAll()">清除全選</button>
        </div>
        <div class="category-grid" id="category-grid">
            @php
                $categories = ['上衣', '褲子', '裙子', '外套', '配件', '鞋子', '包包', '帽子', '首飾', '特賣品'];
            @endphp
            @foreach($categories as $cat)
            <label class="category-item" id="label-{{ $loop->index }}">
                <input type="checkbox" name="categories[]" value="{{ $cat }}"
                    onchange="toggleCategoryStyle(this, 'label-{{ $loop->index }}')">
                {{ $cat }}
            </label>
            @endforeach
        </div>
        <span class="hint">可複選多個分類，折扣將套用至所選分類下的所有商品。</span>
    </div>

    <!-- 折扣數字 -->
    <div class="form-row">
        <label for="cat-discount">折扣比例（%）</label>
        <input type="number" id="cat-discount" name="discount_value" min="1" max="99" value="15" placeholder="例如：15 表示八五折">
        <span class="hint">輸入 1–99 的整數，例如輸入 30 代表打七折。</span>
    </div>

    <!-- 日期區間 -->
    <div class="form-row">
        <label>折扣有效期間</label>
        <div class="date-range">
            <input type="date" id="cat-start" name="start_date" value="{{ date('Y-m-d') }}">
            <span>至</span>
            <input type="date" id="cat-end" name="end_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
        </div>
        <span class="hint">選擇折扣活動的開始日期與結束日期。</span>
    </div>

    <div class="form-actions">
        <button class="btn-save" onclick="alert('（展示用）設定已儲存')">儲存設定</button>
        <a href="{{ route('admin.coupons') }}" class="btn-cancel">取消</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateStatus(checkbox) {
        const status = document.getElementById('cat-status');
        status.textContent = checkbox.checked ? '已啟用' : '已停用';
        status.className = 'toggle-status ' + (checkbox.checked ? 'on' : 'off');
    }

    function toggleCategoryStyle(checkbox, labelId) {
        const label = document.getElementById(labelId);
        label.classList.toggle('checked', checkbox.checked);
    }

    function selectAll() {
        document.querySelectorAll('#category-grid input[type="checkbox"]').forEach(function(cb, i) {
            cb.checked = true;
            cb.closest('label').classList.add('checked');
        });
    }

    function clearAll() {
        document.querySelectorAll('#category-grid input[type="checkbox"]').forEach(function(cb) {
            cb.checked = false;
            cb.closest('label').classList.remove('checked');
        });
    }
</script>
@endsection
