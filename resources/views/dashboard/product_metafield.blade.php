@extends('shopify-app::layouts.default')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="page-title">Product Metafields</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <a href="{{ route('product_metafield_create') }}" class="btn btn-success">Create</a>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>ID</th>
                                <th>Namespace</th>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Value Type</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Product ID</th>
                                <th>Product Title</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Actions</th>
                                <th>ID</th>
                                <th>Namespace</th>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Value Type</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Product ID</th>
                                <th>Product Title</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($products_metafields as $product_id => $products_metafield)
                                @foreach ($products_metafield as $product_metafield)
                                    <tr>
                                        <td class="action-col">
                                            <a href="{{ route('edit_product_metafield', ['id'=>$product_metafield['id'], 'product_id'=>$product_id]) }}" class="ml-1 action-button bg-green">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button class="ml-2 action-button bg-red delete_product_metafield" id="{{ $product_metafield["id"] }}" data-product="{{ $product_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>{{ $product_metafield["id"] }}</td>
                                        <td>{{ $product_metafield["namespace"] }}</td>
                                        <td>{{ $product_metafield["key"] }}</td>
                                        <td>
                                            @if ($product_metafield["value_type"] == "boolean")
                                                @if ($product_metafield["value"])
                                                    True
                                                @else
                                                    False
                                                @endif
                                            @else
                                                {{ $product_metafield["value"] }}
                                            @endif
                                        </td>
                                        <td>{{ $product_metafield["value_type"] }} | {{ $product_metafield["type"] }}</td>
                                        <td>{{ $product_metafield["description"] }}</td>
                                        <td>{{ $product_metafield["created_at"] }}</td>
                                        
                                        <td>{{ $products_metafield["product_id"] }}</td>
                                        <td>{{ $products_metafield["product_title"] }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent

    <script>
        actions.TitleBar.create(app, { title: 'Products' });
    </script>
@endsection