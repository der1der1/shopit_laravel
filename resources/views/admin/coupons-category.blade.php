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

    /* ── 卡片 ── */
    .discount-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 28px 30px;
        margin-bottom: 24px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        max-width: 760px;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e8f5e9;
    }

    .card-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .btn-delete-card {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        background: #fdecea;
        color: #c62828;
        border: 1px solid #ef9a9a;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-delete-card:hover { background: #ef9a9a; color: #fff; }

    /* ── 表單欄位 ── */
    .form-row {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 20px;
    }

    .form-row > label {
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
    .form-row input.is-invalid { border-color: #e74c3c; }

    .date-range {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .date-range span { color: #7f8c8d; font-size: 13px; white-space: nowrap; }
    .date-range input { flex: 1; }

    .date-error {
        display: none;
        color: #e74c3c;
        font-size: 12px;
        margin-top: 6px;
        padding: 6px 10px;
        background: #fdecea;
        border: 1px solid #ef9a9a;
        border-radius: 5px;
    }
    .date-error.show { display: block; }

    /* ── Toggle ── */
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

    /* ── 分類格線 ── */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 8px;
    }

    .category-item {
        position: relative;
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

    /* ── 已被其他折扣單佔用 ── */
    .category-item.taken {
        background: #f5f5f5;
        border-color: #ddd;
        color: #bbb;
        cursor: not-allowed;
        opacity: 0.7;
    }
    .category-item.taken:hover {
        border-color: #ddd;
        background: #f5f5f5;
    }
    .category-item.taken input[type="checkbox"] {
        cursor: not-allowed;
        pointer-events: none;
    }

    /* tooltip */
    .category-item.taken::after {
        content: '其他折扣單已選擇';
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.72);
        color: #fff;
        font-size: 11px;
        font-weight: 400;
        white-space: nowrap;
        padding: 4px 8px;
        border-radius: 4px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s;
        z-index: 10;
    }
    .category-item.taken:hover::after { opacity: 1; }

    /* ── 輔助 ── */
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

    /* ── 新增按鈕 ── */
    .btn-add-card {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 11px 24px;
        background: #fff;
        color: #4caf50;
        border: 2px dashed #4caf50;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        max-width: 760px;
        width: 100%;
        justify-content: center;
        margin-bottom: 32px;
    }
    .btn-add-card:hover { background: #f1f8e9; }

    /* ── 空狀態 ── */
    .empty-state {
        max-width: 760px;
        text-align: center;
        padding: 48px 24px;
        color: #95a5a6;
        font-size: 15px;
        background: #fff;
        border: 1px dashed #ddd;
        border-radius: 10px;
        margin-bottom: 24px;
    }
</style>
@endsection

@section('content')
<a href="{{ route('admin.coupons') }}" class="back-link">← 返回優惠券管理</a>

<div style="display:flex; align-items:center; margin-bottom:20px;">
    <h2 style="color:#2c3e50; margin:0;">分類商品折扣設定</h2>
</div>

{{-- 成功訊息 --}}
@if(session('success'))
<div style="max-width:760px; margin-bottom:16px; padding:12px 16px; background:#e8f5e9; border:1px solid #a5d6a7; border-radius:7px; color:#2e7d32; font-size:14px;">
    {{ session('success') }}
</div>
@endif

{{-- 錯誤訊息 --}}
@if(session('error'))
<div style="max-width:760px; margin-bottom:16px; padding:12px 16px; background:#fdecea; border:1px solid #ef9a9a; border-radius:7px; color:#c62828; font-size:14px;">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div style="max-width:760px; margin-bottom:16px; padding:12px 16px; background:#fdecea; border:1px solid #ef9a9a; border-radius:7px; color:#c62828; font-size:14px;">
    <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ── 新增折扣單 ── --}}
<form method="POST" action="{{ route('admin.coupons.category.store') }}">
    @csrf
    <button type="submit" class="btn-add-card">＋ 新增折扣單</button>
</form>

{{-- ── 空狀態 ── --}}
@if($discounts->isEmpty())
<div class="empty-state">
    目前尚無任何分類折扣單，點擊上方「新增折扣單」開始建立。
</div>
@endif

{{-- ── 折扣單卡片列表 ── --}}
@foreach($discounts as $discount)
@php
    $otherUsed   = \App\Models\CategoryDiscountModel::categoriesUsedByOthers($discount->id);
    $isActive    = $discount->is_active;
    $savedCats   = (array) ($discount->categories ?? []);
    $cardId      = $discount->id;
@endphp

<div class="discount-card" id="card-{{ $cardId }}">
    <div class="card-header">
        <h3>🏷️ 折扣單 #{{ $cardId }}</h3>
        {{-- 刪除按鈕 --}}
        <form method="POST" action="{{ route('admin.coupons.category.delete', $cardId) }}"
              onsubmit="return confirm('確定要刪除折扣單 #{{ $cardId }} 嗎？')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete-card">✕ 刪除此折扣單</button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.coupons.category.update', $cardId) }}">
        @csrf

        {{-- Toggle --}}
        <div class="toggle-row">
            <span class="toggle-label">啟用此折扣</span>
            <label class="toggle-switch">
                <input type="checkbox" id="toggle-{{ $cardId }}" name="is_active" value="1"
                       onchange="updateStatus(this, {{ $cardId }})"
                       {{ $isActive ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-status {{ $isActive ? 'on' : 'off' }}" id="status-{{ $cardId }}">
                {{ $isActive ? '已啟用' : '已停用' }}
            </span>
        </div>

        {{-- 商品分類勾選 --}}
        <div class="form-row">
            <label>套用折扣的商品分類</label>
            <div class="select-actions">
                <button type="button" class="btn-text" onclick="selectAll({{ $cardId }})">全選可用</button>
                <span style="color:#ccc;">|</span>
                <button type="button" class="btn-text" onclick="clearAll({{ $cardId }})">清除全選</button>
            </div>
            <div class="category-grid" id="grid-{{ $cardId }}">
                @foreach($allCategories as $idx => $cat)
                @php
                    $checked = in_array($cat, $savedCats);
                    $taken   = in_array($cat, $otherUsed);
                    $labelId = 'lbl-'.$cardId.'-'.$idx;
                @endphp
                <label class="category-item {{ $checked ? 'checked' : '' }} {{ $taken ? 'taken' : '' }}"
                       id="{{ $labelId }}">
                    <input type="checkbox" name="categories[]" value="{{ $cat }}"
                           onchange="toggleCategoryStyle(this, '{{ $labelId }}')"
                           {{ $checked ? 'checked' : '' }}
                           {{ $taken  ? 'disabled' : '' }}>
                    {{ $cat }}
                </label>
                @endforeach
            </div>
            <span class="hint">可複選多個分類。灰色項目已被其他折扣單選用，無法重複套用。</span>
        </div>

        {{-- 折扣比例 --}}
        <div class="form-row">
            <label for="discount-{{ $cardId }}">折扣比例（%）</label>
            <input type="number" id="discount-{{ $cardId }}" name="discount_value" min="1" max="99"
                   value="{{ old('discount_value', $discount->discount_value) }}"
                   placeholder="例如：15 表示八五折"
                   class="{{ $errors->has('discount_value') ? 'is-invalid' : '' }}">
            <span class="hint">輸入 1–99 的整數，例如輸入 30 代表打七折。</span>
            @error('discount_value')
                <span style="color:#e74c3c; font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        {{-- 日期區間 --}}
        <div class="form-row">
            <label>折扣有效期間</label>
            <div class="date-range">
                <input type="date" id="start-{{ $cardId }}" name="start_date"
                       value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d') : date('Y-m-d')) }}"
                       onchange="validateDateRange({{ $cardId }})"
                       class="{{ $errors->has('start_date') ? 'is-invalid' : '' }}">
                <span>至</span>
                <input type="date" id="end-{{ $cardId }}" name="end_date"
                       value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d') : date('Y-m-d', strtotime('+7 days'))) }}"
                       onchange="validateDateRange({{ $cardId }})"
                       class="{{ $errors->has('end_date') ? 'is-invalid' : '' }}">
            </div>
            <div class="date-error" id="date-error-{{ $cardId }}">
                結束日期不能早於開始日期，請重新選擇。
            </div>
            @error('start_date')
                <span style="color:#e74c3c; font-size:12px;">{{ $message }}</span>
            @enderror
            @error('end_date')
                <span style="color:#e74c3c; font-size:12px;">{{ $message }}</span>
            @enderror
            <span class="hint">選擇折扣活動的開始日期與結束日期。</span>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">儲存</button>
            <a href="{{ route('admin.coupons') }}" class="btn-cancel">取消</a>
        </div>
    </form>
</div>
@endforeach

@endsection

@section('scripts')
<script>
    function updateStatus(checkbox, id) {
        const status = document.getElementById('status-' + id);
        status.textContent = checkbox.checked ? '已啟用' : '已停用';
        status.className   = 'toggle-status ' + (checkbox.checked ? 'on' : 'off');
    }

    function validateDateRange(id) {
        const start    = document.getElementById('start-' + id);
        const end      = document.getElementById('end-' + id);
        const errorBox = document.getElementById('date-error-' + id);
        if (!start.value || !end.value) return true;
        const invalid = end.value < start.value;
        end.classList.toggle('is-invalid', invalid);
        errorBox.classList.toggle('show', invalid);
        return !invalid;
    }

    function toggleCategoryStyle(checkbox, labelId) {
        document.getElementById(labelId).classList.toggle('checked', checkbox.checked);
    }

    function selectAll(id) {
        document.querySelectorAll('#grid-' + id + ' input[type="checkbox"]:not([disabled])').forEach(function(cb) {
            cb.checked = true;
            cb.closest('label').classList.add('checked');
        });
    }

    function clearAll(id) {
        document.querySelectorAll('#grid-' + id + ' input[type="checkbox"]:not([disabled])').forEach(function(cb) {
            cb.checked = false;
            cb.closest('label').classList.remove('checked');
        });
    }
</script>
@endsection
