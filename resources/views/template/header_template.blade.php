<header class="col-pc-12 col-mobile-12">
    <nav id="run">
        <marquee direction="left" width="100%" scrollamount="10">
            @foreach ($marqee as $marqees)
            {{ $marqees->texts }}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            @endforeach
        </marquee>
    </nav>
    <nav id="tool">
        <div id="home">
            <a href="{{route('home')}}"><img src="{{ asset('img/icon/house-solid.svg') }}" class="icon" title="home page" alt="go to homepage"></a>
        </div>
        <div id="check_list">
            <a href="{{route('list_show')}}"><img src="{{ asset('img/icon/check-list.png') }}" class="icon" alt="go to check list" width="20px" height="20px" title="訂單清單。管理者登入才會顯示"></a>
        </div>
        <!-- 搜索區 -->
        <div id="searchBar">
            <form action="{{ route('toHome_words_search') }}" method="post" enctype="multipart/form-data">
                @csrf
                <label id="label">搜索Bar:</label>
                <input type="text" id="search" name="search_word" value="{{ request('search') }}" placeholder="我想看看...冰箱?" height="10px" title="搜尋商品名與種類">
                <input type="submit" value="搜索" title="可搜尋商品名與種類">
            </form>
        </div>
        <!-- 購物車 -->
        <div id="cart">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{route('check_show')}}"><img src="{{ asset('img/icon/cart-shopping-solid.svg') }}" class="icon" alt="cart icon" title="購物車"></a>
                @if($user)
                <a href="{{route('member_edit')}}" title="會員資料">&nbsp; Hi! &nbsp; {{ $user->name }}&nbsp;</a>
                <input type="submit" name="logout" value="登出" title="登出">
                @else
                &nbsp; Hi! &nbsp; 來賓 &nbsp;
                <button>
                    <a href="{{route('login')}}">登入</a>
                </button>
                @endif
            </form>
        </div>
    </nav>
</header>