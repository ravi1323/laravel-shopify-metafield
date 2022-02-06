@extends('shopify-app::layouts.default')

@section('content')
    <!-- You are: (shop domain name) -->
    <p>Collection Metafield</p>
@endsection

@section('scripts')
    @parent
    <script>
        actions.TitleBar.create(app, { title: 'Collection' });
    </script>
@endsection