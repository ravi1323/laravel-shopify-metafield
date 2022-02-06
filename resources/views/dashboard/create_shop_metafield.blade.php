@extends('shopify-app::layouts.default')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="page-title">Create shop metafield</h1>
                </div>
                <div class="col-sm-6" id="show_message">
                        
                </div>
            </div>
        </div>
    </section>
    <form id="create_shop_metafield">
        <input type="hidden" name="token" value="{{ request('token') }}">
        <div class="card card-primary">
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="key">Key</label>
                            <input type="text" name="key" class="form-control" id="key" placeholder="Enter Key">
                            <span id="key-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="namespace">Namespace</label>
                            <input list="namespaces" name="namespace" class="form-control" id="namespace" placeholder="Enter Namespace">
                            <span id="key-namespace"></span>
                            <datalist id="namespaces">
                                @foreach ($namespaces as $namespace)
                                    <option value="{{ $namespace }}">
                                        {{ $namespace }}
                                    </option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="description">Description <b><small>(optional)</small></b></label>
                            <textarea name="description" id="description" rows="3" placeholder="Enter Description" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="value_type">Value Type</label>
                            <select class="form-select" name="value_type" id="value_type" aria-label="Select value type">
                                <option value="#">--SELECT--</option>
                                @foreach ($value_types as $value_type)
                                    <option value="{{ $value_type['type'] }}" data-example="{{ $value_type['example'] }}" data-api="{{ $value_type['api_name'] }}">
                                        {{ $value_type["api_name"] }} | {{$value_type["type"]}}
                                    </option>
                                @endforeach
                                </select>
                                <span id="value_type-error"></span>
                        </div>
                        <div class="form-group" id="value_input_group">
                            <label for="value">Value</label>
                            <input type="text" class="form-control" id="value" placeholder="Enter Value" disabled>
                            <span class="fw-bold" id="show_example">Please select type</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="button-create">Create</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    @parent

    <script>
        actions.TitleBar.create(app, { title: 'Create Shop' });
    </script>
@endsection