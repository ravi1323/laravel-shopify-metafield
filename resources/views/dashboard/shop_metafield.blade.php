@extends('shopify-app::layouts.default')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="page-title">Shop Metafields</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <a href="{{ route('shop_metafield_create') }}" class="btn btn-success">Create</a>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shop_metafields as $shop_metafield)    
                                <tr>
                                    <td class="action-col">
                                        <a href="{{ route('edit_shop_metafield', ['id'=>$shop_metafield['id']]) }}" class="ml-1 action-button bg-green">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button class="ml-2 action-button bg-red delete_shop_metafield" id="{{ $shop_metafield["id"] }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>{{ $shop_metafield["id"] }}</td>
                                    <td>{{ $shop_metafield["namespace"] }}</td>
                                    <td>{{ $shop_metafield["key"] }}</td>
                                    <td>
                                        @if ($shop_metafield["value_type"] == "boolean")
                                            @if ($shop_metafield["value"])
                                                True
                                            @else
                                                False
                                            @endif
                                        @else
                                            {{ $shop_metafield["value"] }}
                                        @endif
                                    </td>
                                    <td>{{ $shop_metafield["value_type"] }}</td>
                                    <td>{{ $shop_metafield["description"] }}</td>
                                    <td>{{ $shop_metafield["created_at"] }}</td>
                                    
                                </tr>
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
        actions.TitleBar.create(app, { title: 'Shop' });
    </script>
@endsection