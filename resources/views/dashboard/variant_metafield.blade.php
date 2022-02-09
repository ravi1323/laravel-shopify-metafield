@extends('shopify-app::layouts.default')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="page-title">Variant Metafields</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <a href="{{ route('create_variant_metafield') }}" class="btn btn-success">Create</a>
              </ol>
            </div>
          </div>
        </div>
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
                                <th>Variant ID</th>
                                <th>Variant Title</th>
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
                                <th>Variant ID</th>
                                <th>Variant Title</th>
                                <th>Product ID</th>
                                <th>Product Title</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($variants_metafields as $variant_id => $variants_metafield)
                                @foreach ($variants_metafield as $variant_metafield)
                                    <tr>
                                        <td class="action-col">
                                            <a href="{{ route('edit_variant_metafield', ['id'=>$variant_metafield["id"], 'variant_id'=>$variant_id]) }}" class="ml-1 action-button bg-green">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button class="ml-2 action-button bg-red delete_variant_metafield" id="{{ $variant_metafield["id"] }}" data-variant="{{ $variant_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>{{ $variant_metafield["id"] }}</td>
                                        <td>{{ $variant_metafield["namespace"] }}</td>
                                        <td>{{ $variant_metafield["key"] }}</td>
                                        <td>
                                            @if ($variant_metafield["value_type"] == "boolean")
                                                @if ($variant_metafield["value"])
                                                    True
                                                @else
                                                    False
                                                @endif
                                            @else
                                                {{ $variant_metafield["value"] }}
                                            @endif
                                        </td>
                                        <td>{{ $variant_metafield["value_type"] }} | {{ $variant_metafield["type"] }}</td>
                                        <td>{{ $variant_metafield["description"] }}</td>
                                        <td>{{ $variant_metafield["created_at"] }}</td>
                                        
                                        <td>{{ $variants_metafield["variant_id"] }}</td>
                                        <td>{{ $variants_metafield["variant_title"] }}</td>

                                        <td>{{ $variants_metafield["product_id"] }}</td>
                                        <td>{{ $variants_metafield["product_title"] }}</td>
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
        actions.TitleBar.create(app, { title: 'Variants' });
    </script>
@endsection