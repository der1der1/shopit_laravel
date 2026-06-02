@extends('layouts.admin')

@section('title', '折扣碼設定 - 管理後台')
@section('page-title', '折扣碼設定')

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

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn-add {
        padding: 10px 20px;
        background: #ff9800;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-add:hover { background: #e65100; }

    /* Coupon code cards */
    .code-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .code-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .code-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: #fff8f0;
        border-bottom: 1px solid #ffe0b2;
        cursor: pointer;
        user-select: none;
    }

    .code-card-header .title {
        font-size: 15px;
        font-weight: 700;
        color: #bf360c;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .code-card-header .meta {
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
        color: #ff9800;
        font-size: 16px;
    }
    .chevron.open { transform: rotate(180deg); }

    .code-card-body {
        padding: 22px 20px;
        display: none;
    }
    .code-card-body.open { display: block; }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

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
    .field-group input[type="number"],
    .field-group input[type="date"] {
        padding: 9px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
    }

    .field-group input:focus { border-color: #ff9800; }
    .field-group input.is-invalid { border-color: #e74c3c; }

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
    .toggle-switch input:checked + .toggle-slider { background: #ff9800; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }
    .toggle-status { font-size: 12px; }
    .toggle-status.on { color: #27ae60; font-weight: 600; }
    .toggle-status.off { color: #e74c3c; }

    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #fff3e0;
    }

    .btn-save-sm {
        padding: 8px 20px;
        background: #ff9800;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save-sm:hover { background: #e65100; }

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

    /* Code display chip */
    .code-chip {
        display: inline-block;
        padding: 2px 10px;
        background: #fff3e0;
        color: #bf360c;
        border: 1px dashed #ff9800;
        border-radius: 5px;
        font-family: monospace;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* New coupon modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: #fff;
        border-radius: 12px;
        padding: 28px 32px;
        width: 560px;
        max-width: 95vw;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    }
    .modal-title {
        font-size: 17px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 22px;
    }
    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        justify-content: flex-end;
    }
    .btn-cancel {
        padding: 9px 20px;
        background: #f5f5f5;
        color: #555;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-cancel:hover { background: #eee; }

    /* Validation error list */
    .error-list {
        background: #fdecea;
        border: 1px solid #ef9a9a;
        border-radius: 6px;
        padding: 10px 14px;
        margin-bottom: 16px;
        font-size: 13px;
        color: #c62828;
    }
    .error-list ul { margin: 0; padding-left: 18px; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
        font-size: 15px;
    }
</style>
@endsection

@section('content')
<a href="{{ route('admin.coupons') }}" class="back-link">← 返回優惠券管理</a>

<h2 style="color:#2c3e50; margin-bottom:20px;">折扣碼設定</h2>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

{{-- Validation errors (from store/update) --}}
@if($errors->any())
    <div class="error-list">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="toolbar">
    <span style="font-size:14px; color:#7f8c8d;">共 <strong>{{ $coupons->count() }}</strong> 筆折扣碼</span>
    <button class="btn-add" onclick="openNewModal()">+ 新增折扣碼</button>
</div>

<div class="code-list">
    @forelse($coupons as $coupon)
    <div class="code-card" id="card-{{ $coupon->id }}">
        <div class="code-card-header" onclick="toggleCard('cbody-{{ $coupon->id }}', 'cchevron-{{ $coupon->id }}')">
            <div class="title">
                🎟️ {{ $coupon->title }}
                <span class="status-badge {{ $coupon->is_active ? 'badge-on' : 'badge-off' }}" id="badge-{{ $coupon->id }}">
                    {{ $coupon->is_active ? '啟用中' : '已停用' }}
                </span>
            </div>
            <div class="meta">
                <span>代碼：<span class="code-chip">{{ $coupon->code }}</span></span>
                <span>折扣：{{ 100 - $coupon->discount_value }} 折（{{ $coupon->discount_value }}% off）</span>
                <span>{{ $coupon->start_date->format('Y/m/d') }} – {{ $coupon->end_date->format('Y/m/d') }}</span>
                <span class="chevron" id="cchevron-{{ $coupon->id }}">▼</span>
            </div>
        </div>
        <div class="code-card-body" id="cbody-{{ $coupon->id }}">
            <form method="POST" action="{{ route('admin.coupons.code.update', $coupon->id) }}">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="field-group">
                        <label>標題</label>
                        <input type="text" name="title" value="{{ $coupon->title }}" required maxlength="100">
                    </div>
                    <div class="field-group">
                        <label>折扣代碼</label>
                        <input type="text" name="code" value="{{ $coupon->code }}" required maxlength="50"
                               style="text-transform:uppercase"
                               oninput="this.value=this.value.toUpperCase()">
                        <span class="hint">顧客結帳時輸入此代碼即可享有折扣。</span>
                    </div>
                    <div class="field-group">
                        <label>折扣比例（%）</label>
                        <input type="number" name="discount_value" value="{{ $coupon->discount_value }}" min="1" max="99" required>
                        <span class="hint">例如 15 = 八五折</span>
                    </div>
                    <div class="field-group">
                        <label>折扣有效期間</label>
                        <div class="date-range">
                            <input type="date" name="start_date" value="{{ $coupon->start_date->format('Y-m-d') }}"
                                   onchange="validateDateRange(this.closest('.date-range').querySelector('input[name=end_date]'))">
                            <span>至</span>
                            <input type="date" name="end_date" value="{{ $coupon->end_date->format('Y-m-d') }}"
                                   onchange="validateDateRange(this)">
                        </div>
                        <div class="date-error">結束日期不能早於開始日期，請重新選擇。</div>
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
                            onclick="deleteCode({{ $coupon->id }}, '{{ $coupon->title }}')">刪除</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="empty-state">目前尚無折扣碼，點擊「+ 新增折扣碼」建立第一筆。</div>
    @endforelse
</div>

{{-- ===== 新增折扣碼 Modal ===== --}}
<div class="modal-overlay" id="newModal">
    <div class="modal-box">
        <div class="modal-title">新增折扣碼</div>
        <form method="POST" action="{{ route('admin.coupons.code.store') }}" id="newCouponForm">
            @csrf
            <div class="form-grid">
                <div class="field-group">
                    <label>標題 <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="title" placeholder="請輸入折扣碼標題" maxlength="100" required>
                </div>
                <div class="field-group">
                    <label>折扣代碼 <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="code" placeholder="例如：SALE10" maxlength="50"
                           style="text-transform:uppercase"
                           oninput="this.value=this.value.toUpperCase()" required>
                    <span class="hint">只能使用英數字，系統會自動轉為大寫。</span>
                </div>
                <div class="field-group">
                    <label>折扣比例（%）<span style="color:#e74c3c">*</span></label>
                    <input type="number" name="discount_value" value="10" min="1" max="99" required>
                    <span class="hint">例如 10 = 九折</span>
                </div>
                <div class="field-group">
                    <label>折扣有效期間 <span style="color:#e74c3c">*</span></label>
                    <div class="date-range">
                        <input type="date" name="start_date" id="new_start_date" required
                               onchange="validateDateRange(document.getElementById('new_end_date'))">
                        <span>至</span>
                        <input type="date" name="end_date" id="new_end_date" required
                               onchange="validateDateRange(this)">
                    </div>
                    <div class="date-error" id="new_date_error">結束日期不能早於開始日期，請重新選擇。</div>
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
                <button type="button" class="btn-cancel" onclick="closeNewModal()">取消</button>
                <button type="submit" class="btn-save-sm">建立折扣碼</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== Delete hidden form ===== --}}
<form id="deleteForm" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    // ── card toggle ──────────────────────────────
    function toggleCard(bodyId, chevronId) {
        const body    = document.getElementById(bodyId);
        const chevron = document.getElementById(chevronId);
        body.classList.toggle('open');
        chevron.classList.toggle('open');
    }

    // ── toggle switch label ──────────────────────
    function updateToggle(checkbox) {
        const statusEl = checkbox.closest('.toggle-row').querySelector('.toggle-status');
        statusEl.textContent = checkbox.checked ? '已啟用' : '已停用';
        statusEl.className   = 'toggle-status ' + (checkbox.checked ? 'on' : 'off');
    }

    // ── date range validation ────────────────────
    function validateDateRange(endInput) {
        const dateRange  = endInput.closest('.date-range');
        const startInput = dateRange.querySelector('input[type="date"]:first-of-type');
        const errorEl    = dateRange.closest('.field-group').querySelector('.date-error');

        if (!startInput.value || !endInput.value) return true;

        const invalid = endInput.value < startInput.value;
        endInput.classList.toggle('is-invalid', invalid);
        if (errorEl) errorEl.classList.toggle('show', invalid);
        return !invalid;
    }

    // ── delete coupon ────────────────────────────
    function deleteCode(id, title) {
        if (!confirm('確定要刪除折扣碼「' + title + '」嗎？此操作無法復原。')) return;

        const form = document.getElementById('deleteForm');
        form.action = '/admin/coupons/code/' + id;
        form.submit();
    }

    // ── new modal ────────────────────────────────
    function openNewModal() {
        // set default dates
        const today     = new Date().toISOString().slice(0, 10);
        const nextMonth = new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10);
        document.getElementById('new_start_date').value = today;
        document.getElementById('new_end_date').value   = nextMonth;

        document.getElementById('newModal').classList.add('show');
    }

    function closeNewModal() {
        document.getElementById('newModal').classList.remove('show');
    }

    // close modal when clicking outside
    document.getElementById('newModal').addEventListener('click', function(e) {
        if (e.target === this) closeNewModal();
    });

    // ── re-open modal on validation error ──
    // (errors already shown at top of page via blade errors block)
</script>
@endsection
