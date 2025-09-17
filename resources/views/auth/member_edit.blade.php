<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>會員管理</title>
    <link rel="stylesheet" href="{{ asset('member_edit.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">

</head>

<body>

    <!-- sweetalert 套件彈出視窗 for 好看一點-->
    @if(session('success') || $errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if ("{{ session('success') }}") {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}",
                });
            }

            if ("{{ $errors->any() }}") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ $errors->first() }}",
                });
            }
        });
    </script>
    @endif
    <div class="container">

        <!-- Member's Info 區塊 -->
        <form method="POST" action="{{ route('member_edit_save') }}" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <h2>Member’s info</h2>
                <div class="flex-row">
                    <div class="avatar">
                        <img id="avatar-preview" src="{{ $user->img ?? '#' }}" alt="Avatar Preview" style="max-width: 100px; max-height: 100px;">
                        <label for="avatar-upload" class="custom-upload-button">Upload Avatar</label>
                        <input type="file" id="avatar-upload" accept="image/*" style="display:none;">
                    </div>
                    <div class="info-box">
                        <div class="input-group">
                            <label>name</label>
                            <input type="text" name="name" value="{{ $user->name ?? '' }}" placeholder="Enter your name">
                        </div>
                        <div class="input-group">
                            <label>Nick name</label>
                            <input type="text" name="nickname" value="{{ $user->nickname ?? '' }}" placeholder="Enter your nickname">
                        </div>
                    </div>
                </div>

                <div class="input-row">
                    <label>new password</label>
                    <input type="password" name="password" placeholder="Input new password">
                </div>
                <div class="input-row">
                    <label>phone</label>
                    <input type="text" name="phone" value="{{ $user->phone ?? '' }}" placeholder="Enter your phone number">
                </div>
                <div class="input-row">
                    <label>address</label>
                    <input type="text" name="address" value="{{ $user->to_address ?? '' }}" placeholder="Enter your address">
                </div>
                <div class="input-row">
                    <label>email</label>
                    <input type="text" name="email" value="{{ $user->email ?? '' }}" placeholder="Enter your email">
                </div>

                <div class="btn-row">
                    <button class="btn cease" title="停用帳戶">cease</button>
                    <button class="btn delete" title="刪除帳戶">delete</button>
                    <button class="btn google" title="以Google帳戶登入">Google</button>
                </div>

                <div class="btn-row">
                    <button class="btn save" type="submit">save</button>
                </div>

                <div class="btn-row"></div>
            </div>
        </form>

        <!-- 我的最愛 & 訂單查詢 -->
        <div class="card">
            <h3>Products info</h3>
            <button class="btn favorite" title="查詢您儲存喜愛的商品">My favorite</button>
            <form id="order-query-form">
                @csrf
                <div class="query-row">
                    <label>訂單與紀錄</label>
                    <input type="text" name="order_query" placeholder="輸入單號 或 留白以查詢所有">
                    <button class="btn go" type="submit">go</button>
                </div>
            </form>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('#order-query-form').on('submit', function(e) {
                        e.preventDefault(); // Prevent default form submission

                        const query = $('input[name="order_query"]').val();

                        // 清空 .result-table 的內容，避免重複查詢時卡住
                        $('.result-table').html('');

                        $.ajax({
                            url: "{{ route('order_query') }}",
                            method: "GET",
                            data: {
                                order_query: query
                            },
                            success: function(response) {
                                // 檢查是否有錯誤訊息
                                if (response.error) {
                                    $('.result-table').html(`<p style="color: red;">${response.error}</p>`);
                                    return;
                                }

                                // 動態生成表格
                                let html = "<table><tr><th>Order ID</th><th>product/num/$</th><th>bill</th><th>location</th><th>delivered</th><th>recieved</th><th>created_at</th></tr>";
                                response.orders.forEach(order => {
                                    // 格式化 created_at
                                    const createdAt = new Date(order.created_at).toISOString().slice(0, 16).replace('-', '/').replace('-', '/').replace('T', ' ');

                                    html += `<tr>
                                        <td>${order.id}</td>

                                        <td>
                                            ${
                                                order.purchased
                                                    ? order.purchased.map(item => `${item.product_name} / ${item.number} / ${item.price}`).join('<br>')
                                                    : 'N/A'
                                            }
                                        </td>

                                        <td>${order.bill || 'N/A'}</td>
                                        <td>${order.to_shop || order.to_address}</td>
                                        <td>${order.delivered = 1 ? '已出貨' : '未出貨' || 'N/A'}</td>
                                        <td>${order.recieved = 1 ? '已收' : '未收' || 'N/A'}</td>
                                        <td>${createdAt || 'N/A'}</td>

                                    </tr>`;
                                });
                                html += "</table>";

                                // 將生成的 HTML 插入到 .result-table
                                $('.result-table').html(html);
                            },
                            error: function(xhr) {
                                console.error("Error:", xhr.responseText);

                                // 嘗試解析後端返回的 JSON 錯誤訊息
                                let errorMessage = '發生錯誤，請稍後再試。';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error; // 使用後端返回的錯誤訊息
                                }

                                $('.result-table').html(`<p style="color: red;">${errorMessage}</p>`);
                            }
                        });
                    });
                });
            </script>

            <!-- 查詢結果 table -->
            <div class="result-table">
                <!-- table 會被填入 -->
            </div>

            <div class="btn-row">
                <button class="btn home" title="回到首頁" onclick="window.location.href='{{ route('home') }}'">Home</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <input type="submit" class="btn home" name="logout" value="登出" title="登出">
                </form>
            </div>

            <div class="btn-row"></div>

        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const avatarUpload = document.getElementById("avatar-upload");
            const avatarPreview = document.getElementById("avatar-preview");

            avatarUpload.addEventListener("change", function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.display = "block";
                    };
                    reader.readAsDataURL(file);
                }
            });
            const cards = document.querySelectorAll(".card"); // 選取所有卡片
            let isScrolling = false; // 防止多次觸發滾動

            // 監聽滾動事件
            window.addEventListener("wheel", function(event) {
                if (isScrolling) return; // 如果正在滾動，則不執行
                isScrolling = true;

                const currentScroll = window.scrollY; // 當前滾動位置
                const viewportHeight = window.innerHeight; // 視窗高度
                const direction = event.deltaY > 0 ? "down" : "up"; // 判斷滾動方向

                let targetCard = null;

                // 根據滾動方向計算目標卡片
                if (direction === "down") {
                    for (const card of cards) {
                        if (card.offsetTop > currentScroll) {
                            targetCard = card;
                            break;
                        }
                    }
                } else if (direction === "up") {
                    for (let i = cards.length - 1; i >= 0; i--) {
                        if (cards[i].offsetTop < currentScroll) {
                            targetCard = cards[i];
                            break;
                        }
                    }
                }

                // 如果找到目標卡片，滾動到該卡片
                if (targetCard) {
                    window.scrollTo({
                        top: targetCard.offsetTop,
                        behavior: "smooth", // 平滑滾動
                    });
                }

                // 延遲 0.8 秒後允許再次滾動
                setTimeout(() => {
                    isScrolling = false;
                }, 800);
            });
        });
    </script>

</body>

</html>