{{-- resources/views/template/order_card.blade.php --}}
{{-- 訂單狀態計算 --}}
@php
    $payed     = $order->payed ?? ($order['payed'] ?? '0');
    $delivered = $order->delivered ?? ($order['delivered'] ?? '0');
    $recieved  = $order->recieved ?? ($order['recieved'] ?? '0');
    $orderId   = $order->id ?? ($order['id'] ?? '-');
    $orderBill = $order->bill ?? ($order['bill'] ?? '-');
    $orderName = $order->name ?? ($order['name'] ?? '');
    $toShop    = $order->to_shop ?? ($order['to_shop'] ?? '');
    $toAddress = $order->to_address ?? ($order['to_address'] ?? '');
    $shop1addr = $order->shop1_addr2 ?? ($order['shop1_addr2'] ?? 0);
    $createdAt = $order->created_at ?? ($order['created_at'] ?? null);
    $purchased = $order->purchased ?? ($order['purchased'] ?? []);

    if ($recieved == '1') {
        $statusClass = 'status-received';
        $statusText  = '已收貨完成';
        $statusIcon  = '✅';
    } elseif ($delivered == '1') {
        $statusClass = 'status-shipped';
        $statusText  = '已出貨';
        $statusIcon  = '🚚';
    } elseif ($payed == '1') {
        $statusClass = 'status-paid';
        $statusText  = '已付款，備貨中';
        $statusIcon  = '💳';
    } else {
        $statusClass = 'status-pending';
        $statusText  = '待付款';
        $statusIcon  = '⏳';
    }
@endphp

<div class="order-card">
    <div class="order-card-header">
        <div>
            <span class="order-id">訂單編號：#{{ $orderId }}</span>
            @if($createdAt)
                <span class="order-date">&nbsp;·&nbsp;{{ \Carbon\Carbon::parse($createdAt)->format('Y-m-d') }}</span>
            @endif
        </div>
        <span class="status-badge {{ $statusClass }}">
            {{ $statusIcon }} {{ $statusText }}
        </span>
    </div>

    <div class="order-card-body">
        {{-- 配送資訊 --}}
        <div class="order-info-row">
            @if($orderName)
                <span><strong>收件人：</strong>{{ $orderName }}</span>
            @endif
            @if($shop1addr == 1 && $toAddress)
                <span><strong>取貨門市：</strong>{{ $toAddress }}</span>
            @elseif($toShop)
                <span><strong>配送地址：</strong>{{ $toShop }}</span>
            @endif
            @if($orderBill)
                <span><strong>帳單號碼：</strong>{{ $orderBill }}</span>
            @endif
        </div>

        {{-- 訂購商品 --}}
        @if(!empty($purchased) && is_array($purchased) && count($purchased) > 0)
        <table class="product-table">
            <thead>
                <tr>
                    <th>商品名稱</th>
                    <th>數量</th>
                    <th>單價</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchased as $item)
                @php
                    $pName  = $item['product_name'] ?? ($item->product_name ?? '-');
                    $pNum   = $item['number'] ?? ($item['num'] ?? ($item->number ?? 1));
                    $pPrice = $item['price'] ?? ($item->price ?? 0);
                    $pSub   = (int)$pNum * (int)$pPrice;
                @endphp
                <tr>
                    <td>{{ $pName }}</td>
                    <td>{{ $pNum }}</td>
                    <td>$ {{ number_format($pPrice) }}</td>
                    <td>$ {{ number_format($pSub) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- 訂單金額 --}}
        @if($orderBill && $orderBill !== '-')
        <div class="order-bill">訂單金額：$ {{ number_format($orderBill) }}</div>
        @endif
    </div>
</div>
