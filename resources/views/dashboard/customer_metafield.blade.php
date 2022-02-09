@extends('shopify-app::layouts.default')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="page-title">Customer Metafields</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <a href="{{ route('create_customer_metafield') }}" class="btn btn-success">Create</a>
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
                                <th>Customer ID</th>
                                <th>Customer Email</th>
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
                                <th>Customer ID</th>
                                <th>Customer Email</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($customers_metafields as $customer_id => $customers_metafield)
                                @foreach ($customers_metafield as $customer_metafield)
                                    <tr>
                                        <td class="action-col">
                                            <a href="{{ route('edit_customer_metafield', ['id'=>$customer_metafield["id"], 'customer_id'=>$customer_id]) }}" class="ml-1 action-button bg-green">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button class="ml-2 action-button bg-red delete_customer_metafield" id="{{ $customer_metafield["id"] }}" data-customer="{{ $customer_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>{{ $customer_metafield["id"] }}</td>
                                        <td>{{ $customer_metafield["namespace"] }}</td>
                                        <td>{{ $customer_metafield["key"] }}</td>
                                        <td>
                                            @if ($customer_metafield["value_type"] == "boolean")
                                                @if ($customer_metafield["value"])
                                                    True
                                                @else
                                                    False
                                                @endif
                                            @else
                                                {{ $customer_metafield["value"] }}
                                            @endif
                                        </td>
                                        <td>{{ $customer_metafield["value_type"] }} | {{ $customer_metafield["type"] }}</td>
                                        <td>{{ $customer_metafield["description"] }}</td>
                                        <td>{{ $customer_metafield["created_at"] }}</td>
                                        
                                        <td>{{ $customers_metafield["customer_id"] }}</td>
                                        <td>{{ $customers_metafield["customer_email"] }}</td>
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
        actions.TitleBar.create(app, { title: 'Customer' });
    </script>
@endsection