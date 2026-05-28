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

    .preview-badge {
        display: inline-block; padding: 4px 12px; background: #fff3e0;
        color: #e65100; border-radius: 20px; font-size: 12px; font-weight: 600;
        border: 1px solid #ffcc80; margin-left: 10px; vertical-align: middle;
    }

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

    .date-range {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .date-range span { color: #7f8c8d; font-size: 13px; white-space: nowrap; }
    .date-range input { flex: 1; }

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
</style>
@endsection

@section('content')
<a href="{{ route('admin.coupons') }}" class="back-link">← 返回優惠券管理</a>

<div style="display:flex; align-items:center; margin-bottom:20px;">
    <h2 style="color:#2c3e50; margin:0;">折扣碼設定</h2>
    <span class="preview-badge">僅供展示</span>
</div>

<div class="toolbar">
    <span style="font-size:14px; color:#7f8c8d;">共 <strong id="total-count">3</strong> 筆折扣碼</span>
    <button class="btn-add" onclick="addCode()">+ 新增折扣碼</button>
</div>

<div class="code-list" id="code-list">

    <!-- 示範資料 1 -->
    <div class="code-card" id="code-1">
        <div class="code-card-header" onclick="toggleCard('cbody-1', 'cchevron-1')">
            <div class="title">
                🎟️ 夏季大促銷
                <span class="status-badge badge-on">啟用中</span>
            </div>
            <div class="meta">
                <span>代碼：<span class="code-chip">SUMMER15</span></span>
                <span>折扣：8.5 折</span>
                <span>2025/06/01 – 2025/08/31</span>
                <span class="chevron open" id="cchevron-1">▼</span>
            </div>
        </div>
        <div class="code-card-body open" id="cbody-1">
            <div class="form-grid">
                <div class="field-group">
                    <label>標題</label>
                    <input type="text" value="夏季大促銷">
                </div>
                <div class="field-group">
                    <label>折扣代碼</label>
                    <input type="text" value="SUMMER15">
                    <span class="hint">顧客結帳時輸入此代碼即可享有折扣。</span>
                </div>
                <div class="field-group">
                    <label>折扣比例（%）</label>
                    <input type="number" value="15" min="1" max="99">
                    <span class="hint">例如 15 = 八五折</span>
                </div>
                <div class="field-group">
                    <label>折扣有效期間</label>
                    <div class="date-range">
                        <input type="date" value="2025-06-01">
                        <span>至</span>
                        <input type="date" value="2025-08-31">
                    </div>
                </div>
            </div>
            <div class="toggle-row">
                <span class="toggle-label">啟用此折扣碼</span>
                <label class="toggle-switch">
                    <input type="checkbox" checked onchange="updateToggle(this)">
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-status on">已啟用</span>
            </div>
            <div class="card-actions">
                <button class="btn-save-sm" onclick="alert('（展示用）已儲存')">儲存</button>
                <button class="btn-delete-sm" onclick="deleteCard('code-1')">刪除</button>
            </div>
        </div>
    </div>

    <!-- 示範資料 2 -->
    <div class="code-card" id="code-2">
        <div class="code-card-header" onclick="toggleCard('cbody-2', 'cchevron-2')">
            <div class="title">
                🎟️ 新會員首購
                <span class="status-badge badge-on">啟用中</span>
            </div>
            <div class="meta">
                <span>代碼：<span class="code-chip">NEWBIE20</span></span>
                <span>折扣：8 折</span>
                <span>長期有效</span>
                <span class="chevron" id="cchevron-2">▼</span>
            </div>
        </div>
        <div class="code-card-body" id="cbody-2">
            <div class="form-grid">
                <div class="field-group">
                    <label>標題</label>
                    <input type="text" value="新會員首購">
                </div>
                <div class="field-group">
                    <label>折扣代碼</label>
                    <input type="text" value="NEWBIE20">
                    <span class="hint">顧客結帳時輸入此代碼即可享有折扣。</span>
                </div>
                <div class="field-group">
                    <label>折扣比例（%）</label>
                    <input type="number" value="20" min="1" max="99">
                    <span class="hint">例如 20 = 八折</span>
                </div>
                <div class="field-group">
                    <label>折扣有效期間</label>
                    <div class="date-range">
                        <input type="date" value="2025-01-01">
                        <span>至</span>
                        <input type="date" value="2099-12-31">
                    </div>
                </div>
            </div>
            <div class="toggle-row">
                <span class="toggle-label">啟用此折扣碼</span>
                <label class="toggle-switch">
                    <input type="checkbox" checked onchange="updateToggle(this)">
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-status on">已啟用</span>
            </div>
            <div class="card-actions">
                <button class="btn-save-sm" onclick="alert('（展示用）已儲存')">儲存</button>
                <button class="btn-delete-sm" onclick="deleteCard('code-2')">刪除</button>
            </div>
        </div>
    </div>

    <!-- 示範資料 3 -->
    <div class="code-card" id="code-3">
        <div class="code-card-header" onclick="toggleCard('cbody-3', 'cchevron-3')">
            <div class="title">
                🎟️ 週年慶特賣
                <span class="status-badge badge-off">已停用</span>
            </div>
            <div class="meta">
                <span>代碼：<span class="code-chip">ANNIV30</span></span>
                <span>折扣：7 折</span>
                <span>2024/10/01 – 2024/10/31</span>
                <span class="chevron" id="cchevron-3">▼</span>
            </div>
        </div>
        <div class="code-card-body" id="cbody-3">
            <div class="form-grid">
                <div class="field-group">
                    <label>標題</label>
                    <input type="text" value="週年慶特賣">
                </div>
                <div class="field-group">
                    <label>折扣代碼</label>
                    <input type="text" value="ANNIV30">
                    <span class="hint">顧客結帳時輸入此代碼即可享有折扣。</span>
                </div>
                <div class="field-group">
                    <label>折扣比例（%）</label>
                    <input type="number" value="30" min="1" max="99">
                    <span class="hint">例如 30 = 七折</span>
                </div>
                <div class="field-group">
                    <label>折扣有效期間</label>
                    <div class="date-range">
                        <input type="date" value="2024-10-01">
                        <span>至</span>
                        <input type="date" value="2024-10-31">
                    </div>
                </div>
            </div>
            <div class="toggle-row">
                <span class="toggle-label">啟用此折扣碼</span>
                <label class="toggle-switch">
                    <input type="checkbox" onchange="updateToggle(this)">
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-status off">已停用</span>
            </div>
            <div class="card-actions">
                <button class="btn-save-sm" onclick="alert('（展示用）已儲存')">儲存</button>
                <button class="btn-delete-sm" onclick="deleteCard('code-3')">刪除</button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    let counter = 4;

    function toggleCard(bodyId, chevronId) {
        const body = document.getElementById(bodyId);
        const chevron = document.getElementById(chevronId);
        body.classList.toggle('open');
        chevron.classList.toggle('open');
    }

    function updateToggle(checkbox) {
        const statusEl = checkbox.closest('.toggle-row').querySelector('.toggle-status');
        statusEl.textContent = checkbox.checked ? '已啟用' : '已停用';
        statusEl.className = 'toggle-status ' + (checkbox.checked ? 'on' : 'off');
    }

    function deleteCard(id) {
        if (!confirm('確定要刪除此折扣碼嗎？')) return;
        document.getElementById(id).remove();
        updateCount();
    }

    function updateCount() {
        const count = document.querySelectorAll('#code-list .code-card').length;
        document.getElementById('total-count').textContent = count;
    }

    function addCode() {
        const id = 'code-' + counter;
        const bodyId = 'cbody-' + counter;
        const chevronId = 'cchevron-' + counter;
        counter++;

        const today = new Date().toISOString().slice(0, 10);
        const nextMonth = new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10);

        const html = `
        <div class="code-card" id="${id}">
            <div class="code-card-header" onclick="toggleCard('${bodyId}', '${chevronId}')">
                <div class="title">🎟️ 新折扣碼 <span class="status-badge badge-off">已停用</span></div>
                <div class="meta"><span>代碼：待填寫</span><span class="chevron open" id="${chevronId}">▼</span></div>
            </div>
            <div class="code-card-body open" id="${bodyId}">
                <div class="form-grid">
                    <div class="field-group">
                        <label>標題</label>
                        <input type="text" placeholder="請輸入折扣碼標題">
                    </div>
                    <div class="field-group">
                        <label>折扣代碼</label>
                        <input type="text" placeholder="例如：SALE10">
                        <span class="hint">顧客結帳時輸入此代碼即可享有折扣。</span>
                    </div>
                    <div class="field-group">
                        <label>折扣比例（%）</label>
                        <input type="number" value="10" min="1" max="99">
                        <span class="hint">例如 10 = 九折</span>
                    </div>
                    <div class="field-group">
                        <label>折扣有效期間</label>
                        <div class="date-range">
                            <input type="date" value="${today}">
                            <span>至</span>
                            <input type="date" value="${nextMonth}">
                        </div>
                    </div>
                </div>
                <div class="toggle-row">
                    <span class="toggle-label">啟用此折扣碼</span>
                    <label class="toggle-switch">
                        <input type="checkbox" onchange="updateToggle(this)">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-status off">已停用</span>
                </div>
                <div class="card-actions">
                    <button class="btn-save-sm" onclick="alert('（展示用）已儲存')">儲存</button>
                    <button class="btn-delete-sm" onclick="deleteCard('${id}')">刪除</button>
                </div>
            </div>
        </div>`;

        document.getElementById('code-list').insertAdjacentHTML('beforeend', html);
        updateCount();
    }
</script>
@endsection
