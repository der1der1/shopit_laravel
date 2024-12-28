<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editing page</title>
    <!-- 將 CSS 文件連結到 HTML -->
    <link rel="stylesheet" href="{{ asset('edit.css') }}">
</head>

<body id="top">
    <div id="contener">
        @include('template.header_template')

        <main>
            <div id="middbody" class="ROW">
                <aside id="aside" class="col-pc-2 col-mobile-12">
                    <h3>editing items:</h3>
                    <!-- 此設計三個頁面，點擊後id="contents"隨之改變，以JS控制 -->
                    <button class="edit_btn"><h4>ads' pictures</h4></button>
                    <button class="edit_btn"><h4>ads' words</h4></button>
                    <button class="edit_btn"><h4>products</h4></button>
                </aside>

                <div id="contents" class="col-pc-10 col-mobile-12">
                    <!-- 下分三區，以上面的btn選擇顯示 -->
                    <div id="ad_pic"   onclick="ad_pic()"></div>

                    <div id="ad_words" onclick="ad_words()"></div>

                    <div id="product"  onclick="product()">

                    
                        產品列表--修該與刪除
                    <table>
                        <tr><th>id</th><th>pic_name</th><th>pic_dir</th><th>productname</th><th>description</th><th>price</th><th>ori_price</th><th>category</th></tr>
                            @foreach ($products as $product)
                            <form method="POST" action="{{ route('edit_product_store') }}" enctype="multipart/form-data">
                            @csrf
                            <tr>
                                <th>{{ $product->id ??''}}</th>
                                <input type="text" name="id" value="{{ $product->id ??''}}" style="display:none;">
                                <th><input type="text" name="pic_name"    value="{{ $product->pic_name ??''}}">   </th>
                                <th>
                                    {{-- 顯示現有圖片 --}}
                                    <img src="{{ asset( $product->pic_dir) }}" width="100">

                                    <label for="image">選擇新圖片</label>
                                    <input type="file" id="image" name="pic_dir" accept="image/*">
                                </th>
                                <th><input type="text" name="product_name"value="{{ $product->product_name ??''}}"></th>
                                <th><input type="text" name="description" value="{{ $product->description ??''}}"></th>
                                <th><input type="text" name="price"       value="{{ $product->price ??''}}">      </th>
                                <th><input type="text" name="ori_price"   value="{{ $product->ori_price ??''}}">  </th>
                                <th><input type="text" name="category"   value="{{ $product->category ??''}}">  </th>
                                <th>
                                    <input type="checkbox" name="delete" value="1">刪除
                                    <input type="submit" id="submit" value="送出">
                                </th>
                            </tr>
                            </form>
                            @endforeach
                        </table>
                        
                        <form method="POST" action="{{ route('edit_product_add') }}" enctype="multipart/form-data">
                            @csrf
                                <div id="add">
                                    新增商品
                                    <div id="row1">
                                        <input type="text" name="product_name" placeholder="商品名稱">
                                        <input type="submit" id="submit" value="送出新增">
                                    </div>
                                    <div id="row2">
                                        <div id="col1">
                                            <img src="" width="100">
                                            <label for="image">選擇新圖片</label>
                                            <input type="file" id="" name="pic_dir" accept="image/*">
                                        </div>
                                        <div id="col2">
                                            <input type="text" name="pic_name" placeholder="圖片名稱">
                                            <input type="text" name="price" placeholder="售價">
                                            <input type="text" name="ori_price" placeholder="原始價格">
                                            <input type="text" name="category" placeholder="分類">
                                        </div>
                                        <div id="col3">
                                            <textarea name="description" id="" placeholder="商品描述"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    
    @include('template.footer_template')

    <script>
        AOS.init({
            duration: 500,
        });

        
        // 控制要前往哪個頁面
        // function ad_pic() {
        //     document.getElementById("ad_pic").className = "panel_log";
        //     document.getElementById("ad_words").className = "none";
        //     document.getElementById("product").className = "none";
        // }
        // function ad_words() {
        //     document.getElementById("ad_pic").className = "none";
        //     document.getElementById("ad_words").className = "ad_words";
        //     document.getElementById("product").className = "none";
        // }
        // function product() {
        //     document.getElementById("ad_pic").className = "none";
        //     document.getElementById("ad_words").className = "none";
        //     document.getElementById("product").className = "product";
        // }
    </script>

</body>
<span id="toTop"> <a href="#top"><img src="{{ asset('img/icon/arrow-up.svg') }}" alt="" title="to top" height="35px" width="35px"></a></span>
</html>