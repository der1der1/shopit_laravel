<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- import of Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- import of Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <title>Check</title>
    {{-- 將 CSS & JS 文件連結到 HTML --}}
    <link rel="stylesheet" href="{{ asset('check.css') }}">
</head>

<body>
    <!-- 先跑要給使用者的訊息 -->
    @if(session('error'))
    <script>alert("{{ session('error') }}");</script>
    @elseif(session(key: 'success'))
    <script>alert("{{ session('success') }}");</script>
    @endif
    
    @include('template.header_template')
    
    <main>
        <div id="greatPromotion">
            <!-- Retrieved the div code from Bootstrap -->
            <!-- 廣告圖display -->
            <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel"
                data-aos="zoom-in" data-aos-duration="400">
                <div class="carousel-inner">
                    <div class="carousel-item active" data-bs-interval="4000">
                        <img src="{{ asset('img/pictureTarget/ad1.png') }}" class="d-block w-100"
                            height="255px">
                    </div>
                    <div class="carousel-item" data-bs-interval="4000">
                        <img src="{{ asset('img/pictureTarget/ad2.png') }}" class="d-block w-100"
                            height="255px">
                    </div>
                    <div class="carousel-item" data-bs-interval="4000">
                        <img src="{{ asset('img/pictureTarget/ad3.png') }}" class="d-block w-100"
                            height="255px">
                    </div>
                    <div class="carousel-item" data-bs-interval="4000">
                        <img src="{{ asset('img/pictureTarget/ad4.png') }}" class="d-block w-100"
                            height="255px">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button"
                    data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button"
                    data-bs-target="#carouselExampleInterval" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
            <!-- Bootstrap end -->

        </div>

        <form method="POST" action="{{ route('check_store') }}" enctype="multipart/form-data">
            @csrf

            <div id="items">
                @if (empty($wanted_product[0]))
                {{-- 如果購物車沒東西，傳到前端至少會有第一陣列，但裡面沒東ㄒ，要這樣判斷 --}}
                    <div id="item"><h2>您的購物車內尚無商品！</h2></div>
                @else
                {{-- 如果購物車有東西 --}}
                    @foreach ($wanted_product as $wanted_products)
                    {{-- without that "??empty" shit, view cant run --}}
                        @if (empty($wanted_products))
                        {{-- 如果該id的商品已經下架(即資料庫搜尋不到)則不予顯示 --}}
                        @else
                        <div id="' . $id . '" class="item">
                            <div id="item_pic">
                                <img src="{{ $wanted_products->pic_dir ?? '' }}" alt="{{ $wanted_products->product_name ?? '' }}"  width="140px" height="140px">
                            </div>

                            <div id="item_description">
                                <p id="p">{{ $wanted_products->product_name ?? '' }}</br></br>{{ $wanted_products->description ?? '' }}</p>
                            </div>
                            <div id="item_money">
                                <div id="item_money_total_{{ $wanted_products->id??'' }}" data-price="{{ $wanted_products->price??'' }}class="item_money_total">Price $ = {{ $wanted_products->price ?? '' }}</div>
                                <div id="item_money_top">
                                    <p> number: &nbsp;</p>
                                    <input type="number" id="quantity-{{ $wanted_products->id??'' }}" min="0" value="0" name="quantity[]" style="width:120px;height:40px;backgroung-color: grey;">
                                </div>
                                <div id="item_sum_total_{{ $wanted_products->id??'' }}" class="item_money_total"> SUM $ = _______</div>
                            </div>

                            <div id="item_tradding_item">
                                <label class="custom-checkbox">
                                    <input type="checkbox" 
                                        name="selected_items[]" 
                                        value="{{ $wanted_products->id??'' }}" 
                                        class="item-checkbox"
                                        data-quantity-input="quantity-{{ $wanted_products->id??'' }}">
                                    <span class="checkbox-btn"></span>
                                </label>
                                <p>選擇!</p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>
        
            <div id="trading_bar">
                <button id="onsell_ticket_button">
                    <img src="{{ asset('img/icon/ticket.png') }}" title="gain perfact price" alt="ticketsIcon" height="70px" width="70px">
                    <p id="ticketword">YOUR ONSELL TICKETS HERE !</p>
                </button>

                <button id="check" type="submit" title="pay for my favorits">
                    <img src="{{ asset('img/icon/takeaway.png') }}" alt="takeawayIcon" height="65px" width="65px">
                    <span id="checkword">Purchase !!</span>
                </button>
            </div>

        </form>

            @include('template.footer_template')
    </main>
</body>

</html>

{{-- 引入jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// 確保在 jQuery 載入後才執行
$(document).ready(function() {
    // 選取所有 name 屬性選取所有以 "num_adjust" 開頭的輸入框
    var $inputs = $('input[id^="quantity-"]');
        // 每當輸入框的值發生變化時，都會觸發這個函數
    $inputs.on('input', function() {
        // 從 name 屬性中提取產品 ID；例如 name="num_adjust_123" 會被替換成 "123"
        var productId = $(this).attr('id').replace('quantity-', '');
        // 獲取單價
        // parseFloat 字串轉浮點數；如果轉換失敗，則預設為 0
        // 從 data-price 屬性中讀取單價
        var itemPrice = parseFloat($('#item_money_total_' + productId).data('price')) || 0;
        // 獲取輸入的數量
        var quantity = parseInt($(this).val()) || 0;
        var totalSum = itemPrice * quantity;
        // toFixed(1) 保證顯示一位小數
        // 在總金額前加上 "SUM $ = "
        $('#item_sum_total_' + productId).text('SUM $ = ' + totalSum.toFixed(1));
    });
    // 在頁面載入時立即觸發所有輸入框的 input 事件，上面則是input框更動時跟著更動
    $inputs.trigger('input');
});
</script>

<script>
    // 將選取框框與數量合併傳送到後端
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const quantityInputId = this.getAttribute('data-quantity-input');
        const quantityInput = document.getElementById(quantityInputId);
        
        // 當checkbox被勾選時，數量input啟用；取消勾選時禁用
        quantityInput.disabled = !this.checked;
        
        // 如果取消勾選，重置數量為1
        if (!this.checked) {
            quantityInput.value = 1;
        }
    });
});
//     //防止都沒有勾選就提交
// document.getElementById('check').addEventListener('click', function(e) {
//     const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    
//     if (checkboxes.length === 0) {
//         e.preventDefault();
//         alert('請至少選擇一項商品');
//     }
// });
</script>