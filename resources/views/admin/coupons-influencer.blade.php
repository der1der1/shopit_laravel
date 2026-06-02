@extends('layouts.admin')

@section('title', '網紅折扣碼 - 管理後台')
@section('page-title', '網紅折扣碼管理')

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

    /* Flash messages */
    .alert {
        padding: 12px 18px;
        border-radius: 7px;
        margin-bottom: 18px;
        font-size: 14px;
        font-weight: 500;
    }
    .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .alert-error   { background: #fdecea; color: #c62828; border: 1px solid #ef9a9a; }

    /* Header toolbar */
    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn-add {
        padding: 10px 20px;
        background: #9c27b0;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-add:hover { background: #7b1fa2; }

    /* Influencer cards list */
    .influencer-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .influencer-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .influencer-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: #f9f0ff;
        border-bottom: 1px solid #e1bee7;
        cursor: pointer;
        user-select: none;
    }

    .influencer-card-header .name {
        font-size: 15px;
        font-weight: 700;
        color: #6a1b9a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .influencer-card-header .meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: #7f8c8d;
    }

    .status-badge {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-on  { background: #e8f5e9; color: #2e7d32; }
    .badge-off { background: #fce4ec; color: #c62828; }

    .chevron {
        transition: transform 0.3s;
        color: #9c27b0;
        font-size: 16px;
    }
    .chevron.open { transform: rotate(180deg); }

    .influencer-card-body {
        padding: 22px 20px;
        display: none;
    }
    .influencer-card-body.open { display: block; }

    /* Form grid inside card */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-grid.full { grid-template-columns: 1fr; }

    .field-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .field-group label {
        font-size: 13px;
        font-weight: 600;
        color: #555;
    }

    .field-group input[type="text"],
    .field-group input[type="email"],
    .field-group input[type="number"],
    .field-group input[type="date"] {
        padding: 9px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
    }

    .field-group input:focus { border-color: #9c27b0; }
    .field-group input.is-invalid { border-color: #e74c3c; }

    .field-error {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 3px;
    }

    .date-range {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .date-range span { color: #7f8c8d; font-size: 13px; white-space: nowrap; }
    .date-range input { flex: 1; }

    .date-error {
        display: none;
        color: #e74c3c;
        font-size: 12px;
        margin-top: 6px;
        padding: 5px 9px;
        background: #fdecea;
        border: 1px solid #ef9a9a;
        border-radius: 5px;
    }
    .date-error.show { display: block; }

    .toggle-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
    }
    .toggle-label { font-size: 13px; font-weight: 600; color: #555; }
    .toggle-switch { position: relative; display: inline-block; width: 48px; height: 26px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; cursor: pointer; inset: 0;
        background: #ccc; border-radius: 26px; transition: 0.3s;
    }
    .toggle-slider:before {
        content: ''; position: absolute;
        width: 20px; height: 20px; left: 3px; bottom: 3px;
        background: white; border-radius: 50%; transition: 0.3s;
    }
    .toggle-switch input:checked + .toggle-slider { background: #9c27b0; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }
    .toggle-status { font-size: 12px; }
    .toggle-status.on { color: #27ae60; font-weight: 600; }
    .toggle-status.off { color: #e74c3c; }

    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f0e6f6;
    }

    .btn-save-sm {
        padding: 8px 20px;
        background: #9c27b0;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save-sm:hover { background: #7b1fa2; }

    .btn-delete-sm {
        padding: 8px 16px;
        background: #fff;
        color: #e74c3c;
        border: 1px solid #e74c3c;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete-sm:hover { background: #fce4ec; }

    .hint { font-size: 12px; color: #95a5a6; margin-top: 3px; }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #95a5a6;
        background: #fafafa;
        border: 2px dashed #e0e0e0;
        border-radius: 10px;
    }

    /* 新增表單 modal overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.open { display: flex; }

    .modal-box {
        background: #fff;
        border-radius: 12px;
        padding: 28px 32px;
        width: 620px;
        max-width: 95vw;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 8px 40px rgba(0,0,0,0.18);
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #6a1b9a;
        margin-bottom: 22px;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #f0e6f6;
        justify-content: flex-end;
    }

    .btn-cancel {
        padding: 9px 20px;
        background: #fff;
        color: #555;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-cancel:hover { background: #f5f5f5; }
</style>
@endsection

@section('content')
<a href="{{ route('admin.coupons') }}" class="back-link">← 返回優惠券管理</a>

<div style="display:flex; align-items:center; margin-bottom:20px;">
    <h2 style="color:#2c3e50; margin:0;">網紅折扣碼管理</h2>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="toolbar">
    <span style="font-size:14px; color:#7f8c8d;">共 <strong id="total-count">{{ $coupons->count() }}</strong> 筆網紅折扣碼</span>
    <button class="btn-add" onclick="openAddModal()">+ 新增網紅折扣碼</button>
</div>

<div class="influencer-list" id="influencer-list">

    @forelse($coupons as $coupon)
    <div class="influencer-card" id="inf-{{ $coupon->id }}">
        <div class="influencer-card-header" onclick="toggleCard('body-{{ $coupon->id }}', 'chevron-{{ $coupon->id }}')">
            <div class="name">
                ⭐ {{ $coupon->name }}
                <span class="status-badge {{ $coupon->is_active ? 'badge-on' : 'badge-off' }}" id="badge-{{ $coupon->id }}">
                    {{ $coupon->is_active ? '啟用中' : '已停用' }}
                </span>
            </div>
            <div class="meta">
                <span>代碼：{{ $coupon->code }}</span>
                <span>折扣：{{ 100 - $coupon->discount_value }} 折</span>
                <span>{{ $coupon->start_date->format('Y/m/d') }} – {{ $coupon->end_date->format('Y/m/d') }}</span>
                <span class="chevron" id="chevron-{{ $coupon->id }}">▼</span>
            </div>
        </div>
        <div class="influencer-card-body" id="body-{{ $coupon->id }}">
            <form method="POST" action="{{ route('admin.coupons.influencer.update', $coupon->id) }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field-group">
                        <label>姓名 / 帳號名稱 <span style="color:#e74c3c">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $coupon->name) }}" required>
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label>社群連結</label>
                        <input type="text" name="social_link" value="{{ old('social_link', $coupon->social_link) }}" placeholder="IG / YouTube / TikTok 等連結">
                        @error('social_link')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $coupon->email) }}">
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label>折扣比例（%）<span style="color:#e74c3c">*</span></label>
                        <input type="number" name="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" min="1" max="99" required>
                        <span class="hint">例如 10 = 九折</span>
                        @error('discount_value')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label>折扣代碼 <span style="color:#e74c3c">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $coupon->code) }}" style="text-transform:uppercase" required>
                        <span class="hint">僅限英數字，儲存後自動轉大寫</span>
                        @error('code')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label>折扣有效期間 <span style="color:#e74c3c">*</span></label>
                        <div class="date-range">
                            <input type="date" name="start_date" value="{{ old('start_date', $coupon->start_date->format('Y-m-d')) }}"
                                onchange="validateCardDateRange(this.closest('.date-range').querySelector('input[name=end_date]'))" required>
                            <span>至</span>
                            <input type="date" name="end_date" value="{{ old('end_date', $coupon->end_date->format('Y-m-d')) }}"
                                onchange="validateCardDateRange(this)" required>
                        </div>
                        <div class="date-error">結束日期不能早於開始日期，請重新選擇。</div>
                        @error('start_date')<span class="field-error">{{ $message }}</span>@enderror
                        @error('end_date')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="toggle-row">
                    <span class="toggle-label">啟用此折扣碼</span>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }}
                            onchange="updateToggle(this)">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-status {{ $coupon->is_active ? 'on' : 'off' }}">
                        {{ $coupon->is_active ? '已啟用' : '已停用' }}
                    </span>
                </div>

                <div class="card-actions">
                    <button type="submit" class="btn-save-sm">儲存</button>
                    <button type="button" class="btn-delete-sm"
                        onclick="deleteInfluencer({{ $coupon->id }}, '{{ addslashes($coupon->name) }}')">刪除</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="empty-state" id="empty-state">
        <p style="font-size:32px; margin:0 0 10px;">🎯</p>
        <p style="margin:0; font-size:15px;">尚無網紅折扣碼，點擊右上角「+ 新增」開始建立</p>
    </div>
    @endforelse

</div>

{{-- 新增網紅折扣碼 Modal --}}
<div class="modal-overlay" id="add-modal" onclick="closeModalOnOverlay(event)">
    <div class="modal-box">
        <div class="modal-title">+ 新增網紅折扣碼</div>
        <form method="POST" action="{{ route('admin.coupons.influencer.store') }}" id="add-form">
            @csrf
            <div class="form-grid">
                <div class="field-group">
                    <label>姓名 / 帳號名稱 <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="name" placeholder="請輸入姓名或帳號名稱" required>
                </div>
                <div class="field-group">
                    <label>社群連結</label>
                    <input type="text" name="social_link" placeholder="IG / YouTube / TikTok 等連結">
                </div>
                <div class="field-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="example@email.com">
                </div>
                <div class="field-group">
                    <label>折扣比例（%）<span style="color:#e74c3c">*</span></label>
                    <input type="number" name="discount_value" value="10" min="1" max="99" required>
                    <span class="hint">例如 10 = 九折</span>
                </div>
                <div class="field-group">
                    <label>折扣代碼 <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="code" placeholder="英數字，例如 MING10" style="text-transform:uppercase" required>
                    <span class="hint">儲存後自動轉大寫</span>
                </div>
                <div class="field-group">
                    <label>折扣有效期間 <span style="color:#e74c3c">*</span></label>
                    <div class="date-range">
                        <input type="date" name="start_date" id="add-start-date"
                            onchange="validateCardDateRange(document.getElementById('add-end-date'))" required>
                        <span>至</span>
                        <input type="date" name="end_date" id="add-end-date"
                            onchange="validateCardDateRange(this)" required>
                    </div>
                    <div class="date-error">結束日期不能早於開始日期，請重新選擇。</div>
                </div>
            </div>
            <div class="toggle-row">
                <span class="toggle-label">啟用此折扣碼</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" value="1" onchange="updateToggle(this)">
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-status off">已停用</span>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeAddModal()">取消</button>
                <button type="submit" class="btn-save-sm">新增</button>
            </div>
        </form>
    </div>
</div>

{{-- 隱藏的刪除表單 --}}
<form method="POST" id="delete-form" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    /* ── 展開 / 收合卡片 ── */
    function toggleCard(bodyId, chevronId) {
        const body = document.getElementById(bodyId);
        const chevron = document.getElementById(chevronId);
        body.classList.toggle('open');
        chevron.classList.toggle('open');
    }

    /* ── Toggle 開關文字更新 ── */
    function updateToggle(checkbox) {
        const statusEl = checkbox.closest('.toggle-row').querySelector('.toggle-status');
        statusEl.textContent = checkbox.checked ? '已啟用' : '已停用';
        statusEl.className = 'toggle-status ' + (checkbox.checked ? 'on' : 'off');
    }

    /* ── 日期區間驗證：傳入結束日期 input ── */
    function validateCardDateRange(endInput) {
        const dateRange  = endInput.closest('.date-range');
        const startInput = dateRange.querySelector('input[type="date"]');
        const errorEl    = dateRange.closest('.field-group').querySelector('.date-error');

        if (!startInput.value || !endInput.value) return true;

        const invalid = endInput.value < startInput.value;
        endInput.classList.toggle('is-invalid', invalid);
        if (errorEl) errorEl.classList.toggle('show', invalid);
        return !invalid;
    }

    /* ── 刪除（送出隱藏表單到 DELETE 路由）── */
    function deleteInfluencer(id, name) {
        if (!confirm('確定要刪除「' + name + '」的折扣碼嗎？此操作無法復原。')) return;

        const form = document.getElementById('delete-form');
        form.action = '/admin/coupons/influencer/' + id;
        form.submit();
    }

    /* ── 新增 Modal ── */
    function openAddModal() {
        const today     = new Date().toISOString().slice(0, 10);
        const nextMonth = new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10);
        document.getElementById('add-start-date').value = today;
        document.getElementById('add-end-date').value   = nextMonth;
        document.getElementById('add-modal').classList.add('open');
    }

    function closeAddModal() {
        document.getElementById('add-modal').classList.remove('open');
        document.getElementById('add-form').reset();
    }

    function closeModalOnOverlay(event) {
        if (event.target === document.getElementById('add-modal')) {
            closeAddModal();
        }
    }

    /* ── 鍵盤 Esc 關閉 Modal ── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAddModal();
    });
</script>
@endsection
