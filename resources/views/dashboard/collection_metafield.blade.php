@extends('shopify-app::layouts.default')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="page-title">Collection Metafields</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <a href="{{ route('product_customer_create') }}" class="btn btn-success">Create</a>
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
                                <th>Collection ID</th>
                                <th>Collection Title</th>
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
                                <th>Collection ID</th>
                                <th>Collection Title</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($collections_metafields as $collection_id => $collections_metafield)
                                @foreach ($collections_metafield as $collection_metafield)
                                    <tr>
                                        <td class="action-col">
                                            <a href="{{ route('edit_customer_metafield', ['id'=>$collection_metafield["id"], 'collection_id'=>$collection_id]) }}" class="ml-1 action-button bg-green">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button class="ml-2 action-button bg-red delete_customer_metafield" id="{{ $collection_metafield["id"] }}" data-customer="{{ $collection_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>{{ $collection_metafield["id"] }}</td>
                                        <td>{{ $collection_metafield["namespace"] }}</td>
                                        <td>{{ $collection_metafield["key"] }}</td>
                                        <td>
                                            @if ($collection_metafield["value_type"] == "boolean")
                                                @if ($collection_metafield["value"])
                                                    True
                                                @else
                                                    False
                                                @endif
                                            @else
                                                {{ $collection_metafield["value"] }}
                                            @endif
                                        </td>
                                        <td>{{ $collection_metafield["value_type"] }} | {{ $collection_metafield["type"] }}</td>
                                        <td>{{ $collection_metafield["description"] }}</td>
                                        <td>{{ $collection_metafield["created_at"] }}</td>
                                        
                                        <td>{{ $collections_metafield["collection_id"] }}</td>
                                        <td>{{ $collections_metafield["collection_title"] }}</td>
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
        actions.TitleBar.create(app, { title: 'Collection' });
    </script>
@endsection