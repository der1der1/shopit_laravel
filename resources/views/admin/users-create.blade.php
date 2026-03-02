@extends('layouts.admin')

@section('title', '新增用戶 - 管理後台')
@section('page-title', '新增用戶')

@section('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
</style>
@endsection

@section('content')
<div class="form-container">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="account">帳號 *</label>
            <input type="text" id="account" name="account" required>
        </div>
        
        <div class="form-group">
            <label for="password">密碼 *</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="name">姓名</label>
            <input type="text" id="name" name="name">
        </div>
        
        <div class="form-group">
            <label for="prvilige">權限 *</label>
            <select id="prvilige" name="prvilige" required>
                <option value="B" selected>一般用戶 (B)</option>
                <option value="A">管理員 (A)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
        </div>
        
        <div class="form-group">
            <label for="phone">電話</label>
            <input type="text" id="phone" name="phone">
        </div>
        
        <div class="form-group">
            <label for="nickname">暱稱</label>
            <input type="text" id="nickname" name="nickname">
        </div>
        
        <div class="form-group">
            <label for="to_address">配送地址</label>
            <input type="text" id="to_address" name="to_address">
        </div>
        
        <div class="form-group">
            <label for="to_shop">配送店家</label>
            <input type="text" id="to_shop" name="to_shop">
        </div>
        
        <div class="form-group">
            <label for="bank_account">銀行帳號</label>
            <input type="text" id="bank_account" name="bank_account">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">新增用戶</button>
            <a href="{{ route('admin.users') }}" class="btn" style="background: #95a5a6; color: white;">返回列表</a>
        </div>
    </form>
</div>
@endsection
