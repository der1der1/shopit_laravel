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
            <a href="{{route('home')}}"><img src="{{ asset('img/logo1.png') }}" class="logo" title="home page" alt="go to homepage"></a>
        </div>
        <div id="check_list">
            <a href="{{route('list_show')}}"><img src="{{ asset('img/icon/check-list.png') }}" class="icon" alt="go to check list" width="20px" height="20px" title="訂單清單。管理者登入才會顯示"></a>
        </div>
        <!-- 搜索區 -->
        <div id="searchBar">
            <form action="{{ route('toHome_words_search') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="text" id="search" name="search_word" value="{{ request('search') }}" placeholder="我想看看...冰箱?" height="10px" title="搜尋商品名與種類">
                <button type="submit" title="可搜尋商品名與種類" style="border: none; background: none; padding: 0; cursor: pointer;">
                    <img src="{{ asset('img/search.png') }}"
                        alt="search icon"
                        width="20px"
                        height="20px"
                        style="margin-top: -3px"
                        cursor="pointer">
                </button>
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
}

* {
  box-sizing: border-box;
}

a {
  text-decoration: none;
  color: var(--text);
}

a:hover {
  text-decoration: none;
}

header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 80px;
  padding: 7px;
  color: var(--text);
  background-color: var(--background);
  overflow: hidden;
  z-index: 9999;
}

#tool {
  padding: 14px 3% 0px 3%;
  height: 100%;
  display: flex;
  justify-content: space-around;
}

#searchBar {
  display: flex;
  flex-direction: row;
  justify-content: center;
  grid-gap: 7px;
  margin-top: -8px;
}

.logo {
  height: 40px;
  width: 115px;
  margin-top: -10px;
}

.icon {
  height: 20px;
  width: 20px;
}

.icon:hover {
  height: 22px;
  width: 22px;
}

button {
  border: none;
}

@media(max-width:700px) {
  header {
    font-size: 12px;
    height: 80px;
  }

  #tool {
    padding: 14px 0% 0px 0%;
    height: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
  }

  .logo {
    height: 35px;
    width: 85px;
    margin-top: -10px;
  }

  .icon {
    height: 15px;
    width: 15px;
  }

  .icon:hover,
  .icon:focus {
    height: 16px;
    width: 16px;
  }

  #searchBar {
    margin-top: 0;
  }

  #search {
    width: 70px;
    font-size: x-small;
  }
}

  @media(max-width:321px) {
    .logo {
      margin-left: -10px;
    }
  }

  @media only screen and (min-width: 0px) {
    .col-mobile-12 {
      width: 100.0%
    }
  }

</style>