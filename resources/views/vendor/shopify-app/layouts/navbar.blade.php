<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars" style="color: aliceblue"></i>
      </button>
      <a class="navbar-brand" href="#">
        <img src="https://img.icons8.com/external-tal-revivo-tritone-tal-revivo/64/000000/external-m-romania-an-automotive-industry-in-romania-automotive-tritone-tal-revivo.png" style="width: 40px;" />
      </a>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="navbar-nav mr-auto">
          <a class="nav-link @if(Request::path() == '/') active @endif" aria-current="page" href="{{ route('home') }}">Shop</a>
          <a class="nav-link @if(Request::path() == 'product') active @endif" href="{{ route('product') }}">Product</a>
          <a class="nav-link @if(Request::path() == 'customer') active @endif" href="{{ route('customer') }}">Customer</a>
          <a class="nav-link @if(Request::path() == 'collection') active @endif" href="{{ route('collection') }}">Collection</a>
          <a class="nav-link @if(Request::path() == 'variant') active @endif" href="{{ route('variant') }}">Variant</a>
        </div>
      </div>
    </div>
  </nav>