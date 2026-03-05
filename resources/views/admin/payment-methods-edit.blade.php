@extends('layouts.admin')

@section('title', '編輯付款方式 - 管理後台')
@section('page-title', '編輯付款方式')

@section('styles')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .form-section h3 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-size: 18px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 15px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 600;
        font-size: 14px;
    }
    
    .form-group label .required {
        color: #e74c3c;
    }
    
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3498db;
    }
    
    .form-help {
        font-size: 12px;
        color: #7f8c8d;
        margin-top: 5px;
    }
    
    .alert {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
    
    .icon-preview {
        max-width: 100px;
        max-height: 100px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }
    
    .info-box {
        background: #e3f2fd;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        color: #1976d2;
        font-size: 13px;
    }
    
    .current-icon {
        max-width: 80px;
        max-height: 80px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }
</style>
@endsection

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.payment-methods') }}" class="btn" style="background: #95a5a6; color: white;">← 返回列表</a>
    </div>
    
    @if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
    @endif
    
    @if ($errors->any())
    <div class="alert alert-error">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="form-container">
        <h2 style="color: #2c3e50; margin-bottom: 30px;">編輯付款方式</h2>
        
        <form action="{{ route('admin.payment-methods.update', $paymentMethod->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3>基本資訊</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="method_name">付款方式名稱 <span class="required">*</span></label>
                        <input type="text" id="method_name" name="method_name" value="{{ old('method_name', $paymentMethod->method_name) }}" required maxlength="20">
                        <div class="form-help">最多20個字元，例如：信用卡、LINE Pay、街口支付</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">狀態</label>
                        <select id="status" name="status">
                            <option value="active" {{ old('status', $paymentMethod->status) == 'active' ? 'selected' : '' }}>啟用</option>
                            <option value="inactive" {{ old('status', $paymentMethod->status) == 'inactive' ? 'selected' : '' }}>停用</option>
                            <option value="delete" {{ old('status', $paymentMethod->status) == 'delete' ? 'selected' : '' }}>已刪除</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="display_order">顯示順序</label>
                        <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $paymentMethod->display_order) }}" min="0">
                        <div class="form-help">數字越小越靠前</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">圖標</label>
                        <input type="file" id="icon" name="icon" accept="image/*" onchange="previewIcon(event)">
                        <div class="form-help">支援 JPG, PNG, GIF 格式，留空則保持原圖標</div>
                        @if($paymentMethod->icon)
                            <div style="margin-top: 10px;">
                                <strong style="font-size: 12px; color: #7f8c8d;">目前圖標：</strong>
                                <img src="{{ asset($paymentMethod->icon) }}" alt="Current Icon" class="current-icon">
                            </div>
                        @endif
                        <img id="icon-preview" class="icon-preview" style="display: none;">
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="description">描述</label>
                    <textarea id="description" name="description">{{ old('description', $paymentMethod->description) }}</textarea>
                    <div class="form-help">付款方式的詳細說明</div>
                </div>
                
                <div class="form-group full-width">
                    <label for="api_endpoint">API 端點</label>
                    <input type="text" id="api_endpoint" name="api_endpoint" value="{{ old('api_endpoint', $paymentMethod->api_endpoint) }}" maxlength="255">
                    <div class="form-help">金流服務的 API URL</div>
                </div>
            </div>
            
            <!-- Production Environment -->
            <div class="form-section">
                <h3>正式環境設定</h3>
                <div class="info-box">
                    ℹ️ 正式環境金鑰用於實際交易，請妥善保管
                </div>
                
                <div class="form-group">
                    <label for="merchant_id">商戶 ID</label>
                    <input type="text" id="merchant_id" name="merchant_id" value="{{ old('merchant_id', $paymentMethod->merchant_id) }}">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="api_key">API 金鑰</label>
                        <input type="text" id="api_key" name="api_key" value="{{ old('api_key', $paymentMethod->api_key) }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="api_secret">API 密鑰</label>
                        <input type="password" id="api_secret" name="api_secret" value="{{ old('api_secret', $paymentMethod->api_secret) }}" placeholder="留空則保持不變">
                    </div>
                </div>
            </div>
            
            <!-- Test Environment -->
            <div class="form-section">
                <h3>測試環境設定</h3>
                <div class="info-box">
                    ℹ️ 測試環境金鑰用於開發和測試，不會產生實際交易
                </div>
                
                <div class="form-group">
                    <label for="sandbox_merchant_id">測試商戶 ID</label>
                    <input type="text" id="sandbox_merchant_id" name="sandbox_merchant_id" value="{{ old('sandbox_merchant_id', $paymentMethod->sandbox_merchant_id) }}">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="sandbox_api_key">測試 API 金鑰</label>
                        <input type="text" id="sandbox_api_key" name="sandbox_api_key" value="{{ old('sandbox_api_key', $paymentMethod->sandbox_api_key) }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="sandbox_api_secret">測試 API 密鑰</label>
                        <input type="password" id="sandbox_api_secret" name="sandbox_api_secret" value="{{ old('sandbox_api_secret', $paymentMethod->sandbox_api_secret) }}" placeholder="留空則保持不變">
                    </div>
                </div>
            </div>
            
            <!-- Fee Settings -->
            <div class="form-section">
                <h3>手續費設定</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="fee_percentage">手續費百分比 (%)</label>
                        <input type="number" id="fee_percentage" name="fee_percentage" value="{{ old('fee_percentage', $paymentMethod->fee_percentage) }}" min="0" max="100" step="0.01">
                        <div class="form-help">例如：2.5 代表 2.5%</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="fee_fixed">固定手續費 (元)</label>
                        <input type="number" id="fee_fixed" name="fee_fixed" value="{{ old('fee_fixed', $paymentMethod->fee_fixed) }}" min="0" step="0.01">
                        <div class="form-help">固定金額，例如：10 元</div>
                    </div>
                </div>
                
                <div class="info-box">
                    ℹ️ 實際手續費 = (交易金額 × 百分比) + 固定金額
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">更新付款方式</button>
                <a href="{{ route('admin.payment-methods') }}" class="btn" style="background: #95a5a6; color: white;">取消</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    function previewIcon(event) {
        const preview = document.getElementById('icon-preview');
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endsection
