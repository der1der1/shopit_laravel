@extends('layouts.admin')

@section('title', '全站折扣 - 管理後台')
@section('page-title', '全站折扣設定')

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
        max-width: 680px;
    }

    .form-card h3 {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e3f2fd;
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
    .form-row input[type="date"],
    .form-row input[type="text"] {
        padding: 10px 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        width: 100%;
        outline: none;
        transition: border-color 0.2s;
    }

    .form-row input:focus {
        border-color: #3498db;
    }

    .form-row input.is-invalid {
        border-color: #e74c3c;
    }

    .invalid-feedback {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 4px;
    }

    .date-range {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .date-range span {
        color: #7f8c8d;
        font-size: 13px;
        white-space: nowrap;
    }

    .date-range input {
        flex: 1;
    }

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

    .date-error.show {
        display: block;
    }

    /* Toggle Switch */
    .toggle-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }

    .toggle-label {
        font-size: 14px;
        font-weight: 600;
        color: #555;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 28px;
        transition: 0.3s;
    }

    .toggle-slider:before {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
    }

    .toggle-switch input:checked + .toggle-slider {
        background: #2196f3;
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    .toggle-status {
        font-size: 13px;
        color: #7f8c8d;
    }

    .toggle-status.on { color: #27ae60; font-weight: 600; }
    .toggle-status.off { color: #e74c3c; }

    .hint {
        font-size: 12px;
        color: #95a5a6;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .btn-save {
        padding: 11px 28px;
        background: #2196f3;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-save:hover { background: #1976d2; }

    .btn-cancel {
        padding: 11px 22px;
        background: #f5f5f5;
        color: #555;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background 0.2s;
    }

    .btn-cancel:hover { background: #ebebeb; color: #333; text-decoration: none; }

    .alert {
        padding: 12px 16px;
        border-radius: 7px;
        font-size: 14px;
        margin-bottom: 20px;
        max-width: 680px;
    }
    .alert-success {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }
    .alert-danger {
        background: #fdecea;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }
</style>
@endsection

@section('content')
<a href="{{ route('admin.coupons') }}" class="back-link">← 返回優惠券管理</a>

<div style="display:flex; align-items:center; margin-bottom:20px;">
    <h2 style="color:#2c3e50; margin:0;">全站折扣設定</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.coupons.sitewide.update') }}">
    @csrf
    <div class="form-card">
        <h3>🌐 全站折扣設定</h3>

        <!-- Toggle -->
        <div class="toggle-row">
            <span class="toggle-label">啟用全站折扣</span>
            <label class="toggle-switch">
                <input type="checkbox" name="is_active" id="sitewide-toggle"
                    {{ old('is_active', $discount->is_active) ? 'checked' : '' }}
                    onchange="updateStatus(this)">
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-status {{ old('is_active', $discount->is_active) ? 'on' : 'off' }}"
                  id="sitewide-status">
                {{ old('is_active', $discount->is_active) ? '已啟用' : '已停用' }}
            </span>
        </div>

        <!-- 折扣數字 -->
        <div class="form-row">
            <label for="discount-value">折扣比例（%）</label>
            <input type="number"
                   id="discount-value"
                   name="discount_value"
                   min="1" max="99"
                   value="{{ old('discount_value', $discount->discount_value) }}"
                   placeholder="例如：10 表示九折"
                   class="{{ $errors->has('discount_value') ? 'is-invalid' : '' }}">
            @error('discount_value')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <span class="hint">輸入 1–99 的整數，例如輸入 20 表示打八折（八折 = 80% 原價）。</span>
        </div>

        <!-- 日期區間 -->
        <div class="form-row">
            <label>折扣有效期間</label>
            <div class="date-range">
                <input type="date"
                       id="start-date"
                       name="start_date"
                       value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d') : date('Y-m-d')) }}"
                       class="{{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                       onchange="validateDateRange()">
                <span>至</span>
                <input type="date"
                       id="end-date"
                       name="end_date"
                       value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d') : date('Y-m-d', strtotime('+7 days'))) }}"
                       class="{{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                       onchange="validateDateRange()">
            </div>
            <div class="date-error" id="date-range-error">
                結束日期不能早於開始日期，請重新選擇。
            </div>
            @if($errors->has('start_date') || $errors->has('end_date'))
                <span class="invalid-feedback" style="display:block;">
                    {{ $errors->first('start_date') ?: $errors->first('end_date') }}
                </span>
            @endif
            <span class="hint">選擇折扣活動的開始日期與結束日期，區間以天為單位。</span>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">儲存設定</button>
            <a href="{{ route('admin.coupons') }}" class="btn-cancel">取消</a>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function updateStatus(checkbox) {
        const status = document.getElementById('sitewide-status');
        if (checkbox.checked) {
            status.textContent = '已啟用';
            status.className = 'toggle-status on';
        } else {
            status.textContent = '已停用';
            status.className = 'toggle-status off';
        }
    }

    function validateDateRange() {
        const startInput = document.getElementById('start-date');
        const endInput   = document.getElementById('end-date');
        const errorBox   = document.getElementById('date-range-error');

        if (!startInput.value || !endInput.value) return true;

        const invalid = endInput.value < startInput.value;

        endInput.classList.toggle('is-invalid', invalid);
        errorBox.classList.toggle('show', invalid);

        return !invalid;
    }

    // 表單送出前攔截
    document.querySelector('form').addEventListener('submit', function (e) {
        if (!validateDateRange()) {
            e.preventDefault();
            document.getElementById('end-date').focus();
        }
    });
</script>
@endsection
