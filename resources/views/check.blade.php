<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <title>購物車 - Shopping Cart</title>
    <link rel="stylesheet" href="{{ asset('check.css') }}">
</head>

<body>
    <!-- 訊息提示 -->
    @if(session('error'))
    <script>alert("{{ session('error') }}");</script>
    @elseif(session('success'))
    <script>alert("{{ session('success') }}");</script>
    @endif
    
    @include('template.header_template')
    
    <!-- 主要購物車區域 -->
    <div class="cart-page-container">
        
        <!-- 購物車標題 -->
        <div class="cart-header">
            <h1 class="cart-title">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                購物車
            </h1>
            <p class="cart-subtitle">請確認您的商品並前往結帳</p>
        </div>

        <form method="POST" action="{{ route('check_store') }}" enctype="multipart/form-data" id="cartForm">
            @csrf

            <div class="cart-content-wrapper">
                <!-- 購物車商品列表 -->
                <div class="cart-items-section">
                    @if (empty($wanted_product[0]))
                        <!-- 空購物車狀態 -->
                        <div class="empty-cart">
                            <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            <h2>您的購物車是空的</h2>
                            <p>快去挑選喜歡的商品吧！</p>
                            <a href="{{ route('home') }}" class="btn-browse-products">瀏覽商品</a>
                        </div>
                    @else
                        <!-- 購物車表頭 -->
                        <div class="cart-table-header">
                            <div class="header-select">
                                <label class="select-all-label">
                                    <input type="checkbox" id="selectAll" class="select-all-checkbox">
                                    <span class="custom-checkbox-mark"></span>
                                    全選
                                </label>
                            </div>
                            <div class="header-product">商品與規格</div>
                            <div class="header-price">單價</div>
                            <div class="header-quantity">數量</div>
                            <div class="header-total">小計</div>
                        </div>

                        <!-- 購物車商品項目 -->
                        <div class="cart-items-list">
                            @foreach ($wanted_product as $wanted_products)
                                @if (!empty($wanted_products))
                                <div class="cart-item" data-product-id="{{ $wanted_products->id }}">
                                    <!-- 選擇框 -->
                                    <div class="item-select">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" 
                                                   name="selected_items[]" 
                                                   value="{{ $wanted_products->id }}" 
                                                   class="item-checkbox"
                                                   data-quantity-input="quantity-{{ $wanted_products->id }}"
                                                   data-price="{{ $wanted_products->price }}">
                                            <span class="checkbox-mark"></span>
                                        </label>
                                    </div>

                                    <!-- 商品資訊 -->
                                    <div class="item-product">
                                        <div class="product-image">
                                            <img src="{{ asset($wanted_products->pic_dir) }}" 
                                                 alt="{{ $wanted_products->product_name }}">
                                        </div>
                                        <div class="product-details">
                                            <h3 class="product-name">{{ $wanted_products->product_name }}</h3>
                                            <div class="variant">{{ $wanted_products->variant->variant_name ?? null }}</div>
                                            <p class="product-description">{{ Str::limit(strip_tags($wanted_products->description), 80) }}</p>
                                        </div>
                                    </div>

                                    <!-- 單價 -->
                                    <div class="item-price">
                                        <span class="price-currency">NT$</span>
                                        <span class="price-amount" data-price="{{ $wanted_products->variant->price }}">{{ number_format($wanted_products->variant->price) }}</span>                                    </div>

                                    <!-- 數量控制 -->
                                    <div class="item-quantity">
                                        <div class="quantity-control">
                                            <button type="button" class="qty-decrease" onclick="decreaseQty({{ $wanted_products->id }})">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                                </svg>
                                            </button>
                                            <input type="number" 
                                                   class="quantity-input" 
                                                   id="quantity-{{ $wanted_products->id }}" 
                                                   min="1" 
                                                   max="{{ $wanted_products->variant->quantity }}"
                                                   value="1" 
                                                   name="quantity[{{ $wanted_products->id }}]" 
                                                   data-price="{{ $wanted_products->variant->price }}"
                                                   disabled
                                                   onchange="updateItemTotal({{ $wanted_products->id }})"
                                                   oninput="limitMaxQuantity(this)">
                                            <button type="button" class="qty-increase" onclick="increaseQty({{ $wanted_products->id }})">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- 存貨 -->
                                    <div class="item-stock">
                                        <span class="stock-label">庫存:</span>
                                        <span class="stock-quantity">{{ $wanted_products->variant->quantity }}</span>
                                    </div>

                                    <!-- 小計 -->
                                    <div class="item-total">
                                        <span class="total-currency">NT$</span>
                                        <span class="total-amount" id="item-total-{{ $wanted_products->variant->id }}">{{ number_format($wanted_products->variant->price) }}</span>

                                    </div>

                                    <!-- 刪除按鈕 -->
                                    <div class="item-remove">
                                        <button type="button" class="btn-remove" onclick="removeItem({{ $wanted_products->id }})" title="移除商品">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- 結帳摘要側邊欄 -->
                @if (!empty($wanted_product[0]))
                <div class="cart-summary-section">
                    <div class="summary-card">
                        <h2 class="summary-title">訂單摘要</h2>
                        
                        <div class="summary-details">
                            <div class="summary-row">
                                <span class="summary-label">已選商品</span>
                                <span class="summary-value" id="selected-count">0 件</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">商品總計</span>
                                <span class="summary-value">NT$ <span id="subtotal">0</span></span>
                            </div>
                            <div class="summary-row discount-row">
                                <span class="summary-label">優惠折扣</span>
                                <span class="summary-value discount">- NT$ <span id="discount">0</span></span>
                            </div>
                            <div class="summary-row shipping-row">
                                <span class="summary-label">運費</span>
                                <span class="summary-value" id="shipping-cost">NT$ 0</span>
                            </div>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-total">
                            <span class="total-label">總計</span>
                            <div class="total-price">
                                <span class="total-currency">NT$</span>
                                <span class="total-amount" id="grand-total">0</span>
                            </div>
                        </div>

                        <!-- 優惠券區 -->
                        <div class="coupon-section">
                            <!-- 隱藏欄位：已套用的優惠碼，隨表單一起送出 -->
                            <input type="hidden" name="coupon_code" id="appliedCouponCode" value="">

                            <div class="coupon-input-group" id="couponInputGroup">
                                <input type="text" placeholder="請輸入優惠碼" class="coupon-input" id="couponInput"
                                       style="text-transform:uppercase;">
                                <button type="button" class="btn-apply-coupon" id="btnApplyCoupon"
                                        onclick="applyCoupon()">使用</button>
                            </div>

                            <!-- 驗證中提示 -->
                            <p id="couponLoading" style="display:none; color:#888; font-size:0.85rem; margin:6px 0 0;">
                                驗證中...
                            </p>

                            <!-- 套用成功提示 -->
                            <div id="couponSuccess" style="display:none; margin-top:8px; padding:8px 12px;
                                 background:#e8f5e9; border:1px solid #a5d6a7; border-radius:6px;">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <span id="couponSuccessMsg" style="color:#2e7d32; font-size:0.88rem; font-weight:600;"></span>
                                    <button type="button" onclick="removeCoupon()"
                                            style="background:none; border:none; color:#888; cursor:pointer;
                                                   font-size:1.1rem; line-height:1; padding:0 2px;"
                                            title="移除優惠券">✕</button>
                                </div>
                            </div>

                            <!-- 驗證失敗提示 -->
                            <p id="couponError" style="display:none; color:#c0392b; font-size:0.85rem; margin:6px 0 0;"></p>
                        </div>

                        <!-- 結帳按鈕 -->
                        <button type="button" class="btn-checkout" id="checkoutBtn" onclick="handleCheckout()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V4l-8-2-8 2v8c0 6 8 10 8 10z"/>
                            </svg>
                            前往結帳
                        </button>

                        <!-- 繼續購物 -->
                        <a href="{{ route('home') }}" class="btn-continue-shopping">繼續購物</a>
                    </div>

                    <!-- 安全提示 -->
                    <div class="security-badges">
                        <div class="badge-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V4l-8-2-8 2v8c0 6 8 10 8 10z"/>
                            </svg>
                            <span>安全購物保障</span>
                        </div>
                        <div class="badge-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            <span>多元付款方式</span>
                        </div>
                        <div class="badge-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>快速到貨服務</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </form>
    </div>

    @include('template.footer_template')

    <!-- 未登入來賓結帳選擇 Modal -->
    @guest
    <div id="guestCheckoutModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:12px; padding:36px 32px; max-width:400px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18); text-align:center;">
            <h3 style="margin:0 0 8px; font-size:1.3rem; color:#2c3e50;">請選擇結帳方式</h3>
            <p style="color:#666; margin:0 0 28px; font-size:0.95rem;">您目前尚未登入，請選擇繼續的方式</p>
            <!-- 登入帳號 -->
            <button type="button" onclick="guestModalLogin()" style="width:100%; padding:14px; margin-bottom:12px; background:#3498db; color:#fff; border:none; border-radius:8px; font-size:1rem; font-weight:600; cursor:pointer;">
                登入帳號
            </button>
            <!-- 來賓結帳 -->
            <button type="button" onclick="guestModalCheckout()" style="width:100%; padding:14px; margin-bottom:12px; background:#27ae60; color:#fff; border:none; border-radius:8px; font-size:1rem; font-weight:600; cursor:pointer;">
                來賓結帳
            </button>
            <!-- 取消 -->
            <button type="button" onclick="closeGuestModal()" style="width:100%; padding:10px; background:transparent; color:#999; border:1px solid #ddd; border-radius:8px; font-size:0.9rem; cursor:pointer;">
                取消
            </button>
        </div>
    </div>
    @endguest

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // 增加數量
        function increaseQty(productId) {
            const input = document.getElementById(`quantity-${productId}`);
            if (!input.disabled && parseInt(input.value) < 99) {
                input.value = parseInt(input.value) + 1;
                updateItemTotal(productId);
            }
        }

        // 減少數量
        function decreaseQty(productId) {
            const input = document.getElementById(`quantity-${productId}`);
            if (!input.disabled && parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateItemTotal(productId);
            }
        }

        // 更新單項商品小計
        function updateItemTotal(productId) {
            const input = document.getElementById(`quantity-${productId}`);
            const price = parseFloat(input.dataset.price) || 0;
            const quantity = parseInt(input.value) || 0;
            const total = price * quantity;
            
            const totalElement = document.getElementById(`item-total-${productId}`);
            if (totalElement) {
                totalElement.textContent = total.toLocaleString('zh-TW');
            }
            
            updateCartSummary();
        }

        // 移除商品項目
        function removeItem(productId) {
            if (confirm('確定要移除此商品嗎？')) {
                fetch(`/check/remove/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                        if (item) {
                            item.remove();
                            updateCartSummary();

                            const remainingItems = document.querySelectorAll('.cart-item');
                            if (remainingItems.length === 0) {
                                location.reload();
                            }
                        }
                    } else {
                        alert('移除失敗，請重試');
                    }
                })
                .catch(() => alert('發生錯誤，請重試'));
            }
        }

        // 更新購物車摘要
        function updateCartSummary() {
            const checkedItems = document.querySelectorAll('.item-checkbox:checked');
            let subtotal = 0;
            let count = 0;

            checkedItems.forEach(checkbox => {
                const productId = checkbox.value;
                const qtyInput = document.getElementById(`quantity-${productId}`);
                const price = parseFloat(qtyInput.dataset.price) || 0;
                const quantity = parseInt(qtyInput.value) || 0;
                
                subtotal += price * quantity;
                count++;
            });

            // 更新顯示
            document.getElementById('selected-count').textContent = `${count} 件`;
            document.getElementById('subtotal').textContent = subtotal.toLocaleString('zh-TW');
            
            // 計算運費 (滿額免運示例)
            const shipping = subtotal >= 1000 ? 0 : 100;
            document.getElementById('shipping-cost').textContent = shipping === 0 ? '免運費' : `NT$ ${shipping}`;
            
            // 優惠碼折扣（前端僅顯示輸入碼折扣比率，實際金額由後端計算）
            const couponDiscountPct = window._appliedCouponDiscount || 0;
            const discount = Math.round(subtotal * couponDiscountPct / 100);
            document.getElementById('discount').textContent = discount.toLocaleString('zh-TW');
            
            // 總計
            const grandTotal = subtotal + shipping - discount;
            document.getElementById('grand-total').textContent = grandTotal.toLocaleString('zh-TW');
        }

        // 更新全選 checkbox 狀態
        function updateSelectAllState() {
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox && allCheckboxes.length > 0) {
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            }
        }

        // 全選功能
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('.item-checkbox');
            
            checkboxes.forEach(cb => {
                if (cb.checked !== isChecked) {
                    cb.checked = isChecked;
                    
                    // 手動更新相關的數量輸入框和小計
                    const quantityInputId = cb.getAttribute('data-quantity-input');
                    const quantityInput = document.getElementById(quantityInputId);
                    
                    if (quantityInput) {
                        quantityInput.disabled = !isChecked;
                        
                        if (!isChecked) {
                            quantityInput.value = 1;
                            quantityInput.style.backgroundColor = '#e9ecef';
                        } else {
                            quantityInput.style.backgroundColor = 'white';
                            if (quantityInput.value === '' || quantityInput.value < 1) {
                                quantityInput.value = 1;
                            }
                        }
                        
                        // 更新該項目的小計
                        const productId = cb.value;
                        updateItemTotal(productId);
                    }
                }
            });
            
            // 更新購物車摘要
            updateCartSummary();
        });

        // 單選框變更事件
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const quantityInputId = this.getAttribute('data-quantity-input');
                const quantityInput = document.getElementById(quantityInputId);
                
                // 啟用/禁用數量輸入
                if (quantityInput) {
                    quantityInput.disabled = !this.checked;
                    
                    if (!this.checked) {
                        quantityInput.value = 1;
                        quantityInput.style.backgroundColor = '#e9ecef';
                    } else {
                        quantityInput.style.backgroundColor = 'white';
                        if (quantityInput.value === '' || quantityInput.value < 1) {
                            quantityInput.value = 1;
                        }
                    }
                    
                    // 更新該項目的小計
                    const productId = this.value;
                    updateItemTotal(productId);
                }
                
                // 更新全選狀態
                updateSelectAllState();
            });
        });

        function applyCoupon() {
            const code = (document.getElementById('couponInput')?.value || '').trim().toUpperCase();
            if (!code) {
                showCouponError('請輸入優惠碼');
                return;
            }

            // 顯示驗證中
            document.getElementById('couponLoading').style.display = 'block';
            document.getElementById('couponError').style.display = 'none';
            document.getElementById('couponSuccess').style.display = 'none';
            document.getElementById('btnApplyCoupon').disabled = true;

            fetch('{{ route('coupon.validate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ coupon_code: code }),
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('couponLoading').style.display = 'none';
                document.getElementById('btnApplyCoupon').disabled = false;

                if (data.valid) {
                    // 儲存已驗證的優惠碼到隱藏欄位
                    document.getElementById('appliedCouponCode').value = data.code;
                    // 儲存折扣比率供前端摘要計算
                    window._appliedCouponDiscount = data.discount_value;

                    document.getElementById('couponSuccessMsg').textContent =
                        data.title + '｜' + data.message;
                    document.getElementById('couponSuccess').style.display = 'block';
                    document.getElementById('couponInputGroup').style.display = 'none';

                    updateCartSummary();
                } else {
                    showCouponError(data.message);
                }
            })
            .catch(() => {
                document.getElementById('couponLoading').style.display = 'none';
                document.getElementById('btnApplyCoupon').disabled = false;
                showCouponError('驗證失敗，請稍後再試');
            });
        }

        function removeCoupon() {
            document.getElementById('appliedCouponCode').value = '';
            window._appliedCouponDiscount = 0;
            document.getElementById('couponInput').value = '';
            document.getElementById('couponSuccess').style.display = 'none';
            document.getElementById('couponError').style.display = 'none';
            document.getElementById('couponInputGroup').style.display = 'flex';
            updateCartSummary();
        }

        function showCouponError(msg) {
            const el = document.getElementById('couponError');
            el.textContent = msg;
            el.style.display = 'block';
        }

        // 結帳主流程
        function handleCheckout() {
            const checkedItems = document.querySelectorAll('.item-checkbox:checked');

            if (checkedItems.length === 0) {
                alert('請至少選擇一項商品');
                return false;
            }

            let hasInvalidQty = false;
            checkedItems.forEach(checkbox => {
                const qtyInput = document.getElementById(checkbox.getAttribute('data-quantity-input'));
                const qty = parseInt(qtyInput.value) || 0;
                if (qty <= 0) {
                    hasInvalidQty = true;
                }
            });

            if (hasInvalidQty) {
                alert('請確保所選商品的數量都大於 0');
                return false;
            }

            @guest
            // 未登入：顯示來賓結帳選擇 Modal
            const modal = document.getElementById('guestCheckoutModal');
            if (modal) {
                modal.style.display = 'flex';
            }
            @else
            // 已登入：直接送出表單
            document.getElementById('cartForm').submit();
            @endguest
        }

        // 來賓 Modal 功能
        function guestModalLogin() {
            window.location.href = '{{ route('login') }}';
        }

        function guestModalCheckout() {
            closeGuestModal();
            document.getElementById('cartForm').submit();
        }

        function closeGuestModal() {
            const modal = document.getElementById('guestCheckoutModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // 頁面載入時更新摘要
        document.addEventListener('DOMContentLoaded', function() {
            updateCartSummary();
        });
    </script>
    <script>
        // 限制輸入最大值
        function limitMaxQuantity(input) {
            var max = parseInt(input.max);
            var min = parseInt(input.min);
            var val = parseInt(input.value);
            if (val > max) {
                input.value = max;
            } else if (val < min || isNaN(val)) {
                input.value = min;
            }
        }
    </script>

</body>

</html>
