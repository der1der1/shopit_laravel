<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>訂單查詢</title>
    <link rel="stylesheet" href="{{ asset('order_list.css') }}">
</head>

<body id="top">

    @if(session('success'))
    <script>alert("{{ session('success') }}");</script>
    @endif

    <div id="contener">

        @include('template.header_template')

        <main id="main-content">

            <div class="page-header">
                <h1 class="page-title">訂單查詢</h1>
                @auth
                <p class="page-subtitle">Hi, {{ $user->name }}！以下是您的所有訂單紀錄。</p>
                @else
                <p class="page-subtitle">輸入訂單編號查詢訂單狀態。</p>
                @endauth
            </div>

            {{-- 未登入：顯示查詢表單 --}}
            @guest
            <div class="query-form-card">
                <form method="POST" action="{{ route('order_list_query') }}">
                    @csrf
                    <div class="form-group">
                        <label for="order_id">訂單編號（單號）</label>
                        <div class="form-row">
                            <input
                                type="number"
                                id="order_id"
                                name="order_id"
                                min="1"
                                placeholder="請輸入訂單編號"
                                value="{{ old('order_id') }}"
                                required
                            >
                            <button type="submit" class="btn-search">查詢</button>
                        </div>
                        @error('order_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>

            {{-- 查詢結果 --}}
            @if($query_error)
                <div class="alert-error">{{ $query_error }}</div>
            @endif

            @if($queried_order)
                <div class="orders-section">
                    <h2 class="section-title">查詢結果</h2>
                    @include('template.order_card', ['order' => $queried_order])
                </div>
            @endif
            @endguest

            {{-- 已登入：顯示所有訂單 --}}
            @auth
            <div class="orders-section">
                @if(empty($orders))
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p>目前尚無訂單紀錄。</p>
                    </div>
                @else
                    @foreach($orders as $order)
                        @include('template.order_card', compact('order'))
                    @endforeach
                @endif
            </div>
            @endauth

        </main>

    </div>

    @include('template.footer_template')

    <span id="toTop">
        <a href="#top">
            <img src="{{ asset('img/icon/arrow-up.svg') }}" alt="" title="to top" height="35px" width="35px">
        </a>
    </span>

</body>

<style>
    :root {
        --white: #FFF8DC;
        --background: #FFFFFF;
        --box: #F6F6F6;
        --box2: #FBE0C5;
        --text: #40210F;
        --text2: #2A2A2A;
        --line: #40210F;
        --btnline: #FFFFFF;
        --background2: #FBE0C5;
        --btn: #2A2A2A;
        --btnhover: #D96253;
        --success: #2e7d32;
        --warning: #e65100;
        --info: #0277bd;
        --neutral: #546e7a;
    }

    * { box-sizing: border-box; }

    body {
        background-color: var(--background);
        color: var(--text);
        font-family: 'Noto Sans TC', sans-serif;
        margin: 0;
    }

    a { text-decoration: none; color: var(--text); }

    #contener {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    #main-content {
        max-width: 820px;
        margin: 100px auto 60px;
        padding: 0 16px;
        flex: 1;
    }

    /* Page Header */
    .page-header {
        text-align: center;
        margin-bottom: 32px;
    }
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 8px;
    }
    .page-subtitle {
        color: var(--text2);
        margin: 0;
        font-size: 0.95rem;
    }

    /* Query Form */
    .query-form-card {
        background: var(--box);
        border-radius: 12px;
        padding: 28px 32px;
        margin-bottom: 28px;
        border: 1px solid var(--box2);
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }
    .form-row {
        display: flex;
        gap: 10px;
    }
    .form-row input[type="number"] {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid var(--line);
        border-radius: 8px;
        font-size: 1rem;
        background: var(--background);
        color: var(--text);
        outline: none;
        transition: border-color 0.2s;
    }
    .form-row input[type="number"]:focus {
        border-color: var(--btnhover);
    }
    .btn-search {
        padding: 10px 24px;
        background: var(--btn);
        color: var(--btnline);
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-search:hover { background: var(--btnhover); }
    .form-error {
        color: var(--btnhover);
        font-size: 0.85rem;
        margin: 6px 0 0;
    }

    /* Alert */
    .alert-error {
        background: #fff3f3;
        border: 1px solid #f5c6c6;
        color: #c0392b;
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.92rem;
    }

    /* Orders Section */
    .orders-section { }
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 16px;
        color: var(--text);
    }

    /* Order Card */
    .order-card {
        background: var(--box);
        border-radius: 12px;
        border: 1px solid var(--box2);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .order-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: var(--background2);
        flex-wrap: wrap;
        gap: 8px;
    }
    .order-id {
        font-weight: 700;
        font-size: 1rem;
    }
    .order-date {
        font-size: 0.82rem;
        color: var(--text2);
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .status-pending    { background: #fff8e1; color: #e65100; border: 1px solid #ffe082; }
    .status-paid       { background: #e3f2fd; color: #0277bd; border: 1px solid #90caf9; }
    .status-shipped    { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .status-received   { background: #f3e5f5; color: #6a1b9a; border: 1px solid #ce93d8; }

    /* Order Body */
    .order-card-body {
        padding: 16px 20px;
    }
    .order-info-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 14px;
        font-size: 0.88rem;
        color: var(--text2);
    }
    .order-info-row span { white-space: nowrap; }
    .order-info-row strong { color: var(--text); }

    /* Product Table */
    .product-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
        margin-top: 4px;
    }
    .product-table th {
        background: var(--background2);
        padding: 7px 10px;
        text-align: left;
        font-weight: 600;
        font-size: 0.82rem;
        color: var(--text);
        border-bottom: 1px solid var(--line);
    }
    .product-table td {
        padding: 7px 10px;
        border-bottom: 1px solid var(--box2);
        color: var(--text2);
    }
    .product-table tr:last-child td { border-bottom: none; }

    /* Bill total */
    .order-bill {
        text-align: right;
        font-size: 0.9rem;
        font-weight: 700;
        margin-top: 10px;
        color: var(--text);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 48px 0;
        color: var(--text2);
    }
    .empty-state svg {
        margin-bottom: 12px;
        opacity: 0.4;
    }
    .empty-state p { margin: 0; font-size: 0.95rem; }

    /* To Top */
    #toTop {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 100;
    }

    @media(max-width: 600px) {
        #main-content { margin-top: 90px; }
        .query-form-card { padding: 20px 16px; }
        .form-row { flex-direction: column; }
        .order-card-header { flex-direction: column; align-items: flex-start; }
    }
</style>

</html>
