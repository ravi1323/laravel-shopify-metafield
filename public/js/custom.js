var base_url = "https://0efd-103-54-21-38.ngrok.io";
$(document).ready(function () {
    var table = $('#table').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "responsive": true,
    });
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000
    });
    var metafield_api_name = $("option:selected", $('#value_type')).attr("data-api");
    $("#value_type").on("change", function () {
        var example = $("option:selected", this).attr("data-example");
        metafield_api_name = $("option:selected", this).attr('data-api');
        if (example !== undefined) {
            $('#value').removeAttr("disabled");
            if($(this).val() == "boolean")
            {
                $('#value_input_group').html(`<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" name="value" class="custom-control-input" id="customSwitch3"><label class="custom-control-label" for="customSwitch3">Value <small>(True / False)</small></label><span id="show_example"></span></div>`);
            } else if($(this).val() == "integer")
            {
                $('#value_input_group').html(`<label for="value">Value</label><input type="number" name="value" class="form-control" id="value" placeholder="Enter integer Value."><span id="show_example"></span>`);
            } else
            {
                $('#value_input_group').html(`<label for="value">Value</label><input type="text" name="value" class="form-control" id="value" placeholder="Enter Value."><span id="show_example"></span>`);
            }
            $("#show_example").html(
                `Example : ${example}`
            );
        } else {
            $('#value').attr("disabled", true);
            $('#value').val("");
            $("#show_example").html("Please select value type.");
        }
    });
    $('#create_shop_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var boolean_conf = {on:true,off:false};
        var formData = {
            _token: event.target[0].value, // _token
            key: event.target[1].value, // Key
            namespace: event.target[2].value, // namespace
            description: event.target[3].value, // description
            value_type: event.target[4].value, // value_type
            value: (event.target[5].value == "on" || event.target[5].value == "off") && event.target[4].value == "boolean" ? boolean_conf[event.target[5].value] : event.target[5].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/store-shop-metafield",
            type: 'POST',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    form.each(function() {
                        this.reset();
                    });
                    $('#value').attr("disabled", true);
                    $('#show_example').html('Please select type.');
                    Toast.fire({
                        icon: 'success',
                        title: 'Shop metafield created successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                    $('#value_type-error').html('');
                    window.location.replace(`${base_url}/`);
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please try again with all fields.'
                    });
                    var errors = {
                        key: err.responseJSON.errors.key == undefined ? "" : err.responseJSON.errors.key,
                        namespace: err.responseJSON.errors.namespace == undefined ? "" : err.responseJSON.errors.namespace,
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        type:err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type
                    };
                    $('#key-error').html(`${errors.key}`);
                    $('#key-namespace').html(`${errors.namespace}`);
                    $('#show_example').html(`${errors.value}`);
                    $('#value_type-error').html(`${errors.type}`);
                }
            }
        }); // Key
    });
    $('.delete_shop_metafield').on('click', function() {
        var metafield_id = $(this).attr('id');
        var delete_button = $(this);
        let user_permission = confirm(`Are you sure deleting metafield with id : ${metafield_id} ?`);
        if(user_permission) {
            delete_button.parents("tr").remove();
            var delete_data = {
                id:metafield_id
            }
            $.ajax({
                url: base_url + "/delete-shop-metafield",
                type: 'DELETE',
                /* send the csrf-token and the input to the controller */
                data: delete_data,
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (response) {
                    if(!response.errors)
                    {
                        Toast.fire({
                            icon: 'success',
                            title: 'Shop metafield deleted successfully.'
                        })
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        } else {
            return;
        }
    });
    $('#update_shop_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var metafield_id = form.attr('data-id');
        var boolean_conf = {on:true,off:false};
        var formData = {
            id:metafield_id,
            _method:"PUT",
            _token: event.target[0].value, // _token
            description: event.target[3].value, // description
            value_type: event.target[4].value, // value_type
            value: (event.target[5].value == "on" || event.target[5].value == "off") && event.target[4].value == "boolean" ? boolean_conf[event.target[5].value] : event.target[5].value, // value
            type: metafield_api_name
        }
        console.log(formData);
        $.ajax({
            url: base_url + "/update_shop_metafield",
            type: 'PUT',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    Toast.fire({
                        icon: 'success',
                        title: 'Shop metafield updated successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    console.log(err.responseJSON);
                    Toast.fire({
                        icon: 'error',
                        title: 'Please try again with all fields.'
                    });
                    var errors = {
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                    };
                    $('#key-error').html(`${errors.key}`);
                    $('#key-namespace').html(`${errors.namespace}`);
                    $('#show_example').html(`${errors.value}`);
                }
            }
        });
    });
    $('#create_product_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var boolean_conf = {on:true,off:false};
        var formData = {
            _token: event.target[0].value, // _token
            key: event.target[1].value, // Key
            namespace: event.target[2].value, // namespace
            description: event.target[3].value, // description
            product: event.target[4].value, // product
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/store-product-metafield",
            type: 'POST',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    form.each(function() {
                        this.reset();
                    });
                    $('#value').attr("disabled", true);
                    $('#show_example').html('Please select type.');
                    Toast.fire({
                        icon: 'success',
                        title: 'Product metafield created successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                    $('#product_id-error').html('');
                    $('#value_type-error').html('');
                }
            },
            error: function (err) {
                console.log(err);
                if(!err.responseJSON.success)
                {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please try again with all fields.'
                    });
                    var errors = {
                        key: err.responseJSON.errors.key == undefined ? "" : err.responseJSON.errors.key,
                        namespace: err.responseJSON.errors.namespace == undefined ? "" : err.responseJSON.errors.namespace,
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        product:err.responseJSON.errors.product == undefined ? "" : err.responseJSON.errors.product,
                        type:err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type,
                    };
                    $('#key-error').html(`${errors.key}`);
                    $('#key-namespace').html(`${errors.namespace}`);
                    $('#show_example').html(`${errors.value}`);
                    $('#product_id-error').html(`${errors.product}`);
                    $('#value_type-error').html(`${errors.type}`);
                }
            }
        }); // Key
    });
    $('#update_product_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var metafield_id = form.attr('data-id');
        var boolean_conf = {on:true,off:false};
        var formData = {
            id:metafield_id,
            _method:"PUT",
            _token: event.target[0].value, // _token
            product: event.target[1].value, // product_id
            description: event.target[4].value, // description
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/update_product_metafield",
            type: 'PUT',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    Toast.fire({
                        icon: 'success',
                        title: 'Product metafield updated successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                    $('#product_id-error').html('');
                    $('#value_type-error').html('');
                    window.location.replace(`${base_url}/product`);
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    var errors = {
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        product: err.responseJSON.errors.product == undefined ? "" : err.responseJSON.errors.product,
                        type: err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type
                    };
                    if(errors.type != undefined)
                    {
                        Toast.fire({
                            icon: 'error',
                            title: 'You can\'t change type of those metafield that created within shopify dashboard.'
                        })
                    }
                    $('#show_example').html(`${errors.value}`);
                    $('#value_type-error').html(`${errors.type}`);
                }
            }
        });
    });
    $('.delete_product_metafield').on('click', function() {
        var metafield_id = $(this).attr('id');
        var product_id = $(this).attr('data-product');
        var delete_button = $(this);
        let user_permission = confirm(`Are you sure deleting metafield with id : ${metafield_id} ?`);
        if(user_permission) {
            delete_button.parents("tr").remove();
            var delete_data = {
                id:metafield_id,
                product_id:product_id
            }
            $.ajax({
                url: base_url + "/delete-product-metafield",
                type: 'DELETE',
                /* send the csrf-token and the input to the controller */
                data: delete_data,
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (response) {
                    if(!response.errors)
                    {
                        Toast.fire({
                            icon: 'success',
                            title: 'Product metafield deleted successfully.'
                        })
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        } else {
            return;
        }
    });

    $('#create_customer_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var boolean_conf = {on:true,off:false};
        var formData = {
            _token: event.target[0].value, // _token
            key: event.target[1].value, // Key
            namespace: event.target[2].value, // namespace
            description: event.target[3].value, // description
            customer: event.target[4].value, // customer
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/store-customer-metafield",
            type: 'POST',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    form.each(function() {
                        this.reset();
                    });
                    $('#value').attr("disabled", true);
                    $('#show_example').html('Please select type.');
                    Toast.fire({
                        icon: 'success',
                        title: 'Customer metafield created successfully.'
                    })
                }
            },
            error: function (err) {
                console.log(err);
                if(!err.responseJSON.success)
                {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please try again with all fields.'
                    });
                    var errors = {
                        key: err.responseJSON.errors.key == undefined ? "" : err.responseJSON.errors.key,
                        namespace: err.responseJSON.errors.namespace == undefined ? "" : err.responseJSON.errors.namespace,
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        customer:err.responseJSON.errors.customer == undefined ? "" : err.responseJSON.errors.customer,
                        type:err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type
                    };
                    $('#key-error').html(`${errors.key}`);
                    $('#key-namespace').html(`${errors.namespace}`);
                    $('#show_example').html(`${errors.value}`);
                    $('#customer-error').html(`${errors.customer}`);
                    $('#value_type-error').html(`${errors.type}`);
                }
            }
        }); // Key
    });
    $('#update_customer_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var metafield_id = form.attr('data-id');
        var boolean_conf = {on:true,off:false};
        var formData = {
            id:metafield_id,
            _method:"PUT",
            _token: event.target[0].value, // _token
            customer: event.target[1].value, // product_id
            description: event.target[4].value, // description
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/update_customer_metafield",
            type: 'PUT',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    console.log(response);
                    Toast.fire({
                        icon: 'success',
                        title: 'Customer metafield updated successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                    $('#product_id-error').html('');
                    $('#value_type-error').html('');
                    window.location.replace(`${base_url}/customer`);
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    console.log(err.responseJSON);
                    var errors = {
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        type: err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type
                    };
                    if(errors.type != undefined)
                    {
                        Toast.fire({
                            icon: 'error',
                            title: 'You can\'t change type of those metafield that created within shopify dashboard.'
                        })
                    }
                    $('#show_example').html(`${errors.value}`);
                    $('#description-error').html(`${errors.description}`);
                    $('#value_type-error').html(`${errors.type}`);
                }
            }
        });
    });
    $('.delete_customer_metafield').on('click', function() {
        var metafield_id = $(this).attr('id');
        var customer_id = $(this).attr('data-customer');
        var delete_button = $(this);
        let user_permission = confirm(`Are you sure deleting metafield with id : ${metafield_id} ?`);
        if(user_permission) {
            delete_button.parents("tr").remove();
            var delete_data = {
                id:metafield_id,
                customer_id:customer_id
            }
            $.ajax({
                url: base_url + "/delete-customer-metafield",
                type: 'DELETE',
                /* send the csrf-token and the input to the controller */
                data: delete_data,
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (response) {
                    if(!response.errors)
                    {
                        Toast.fire({
                            icon: 'success',
                            title: 'Product metafield deleted successfully.'
                        })
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        } else {
            return;
        }
    });
    $('#create_collection_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var boolean_conf = {on:true,off:false};
        var formData = {
            _token: event.target[0].value, // _token
            key: event.target[1].value, // Key
            namespace: event.target[2].value, // namespace
            description: event.target[3].value, // description
            collection: event.target[4].value, // collection
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/store-collection-metafield",
            type: 'POST',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    form.each(function() {
                        this.reset();
                    });
                    $('#value').attr("disabled", true);
                    $('#show_example').html('Please select type.');
                    Toast.fire({
                        icon: 'success',
                        title: 'Collection metafield created successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                    $('#product_id-error').html('');
                    $('#value_type-error').html('');
                }
            },
            error: function (err) {
                console.log(err);
                if(!err.responseJSON.success)
                {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please try again with all fields.'
                    });
                    var errors = {
                        key: err.responseJSON.errors.key == undefined ? "" : err.responseJSON.errors.key,
                        namespace: err.responseJSON.errors.namespace == undefined ? "" : err.responseJSON.errors.namespace,
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        product:err.responseJSON.errors.product == undefined ? "" : err.responseJSON.errors.product,
                        type:err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type,
                    };
                    $('#key-error').html(`${errors.key}`);
                    $('#key-namespace').html(`${errors.namespace}`);
                    $('#show_example').html(`${errors.value}`);
                    $('#product_id-error').html(`${errors.product}`);
                    $('#value_type-error').html(`${errors.type}`);
                }
            }
        }); // Key
    });
    $('#update_collection_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var metafield_id = form.attr('data-id');
        var boolean_conf = {on:true,off:false};
        var formData = {
            id:metafield_id,
            _method:"PUT",
            _token: event.target[0].value, // _token
            collection: event.target[1].value, // product_id
            description: event.target[4].value, // description
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/update_collection_metafield",
            type: 'PUT',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    Toast.fire({
                        icon: 'success',
                        title: 'Collection metafield updated successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                    $('#product_id-error').html('');
                    $('#value_type-error').html('');
                    window.location.replace(`${base_url}/collection`);
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    var errors = {
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        product: err.responseJSON.errors.product == undefined ? "" : err.responseJSON.errors.product,
                        type: err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type
                    };
                    if(errors.type != undefined)
                    {
                        Toast.fire({
                            icon: 'error',
                            title: 'You can\'t change type of those metafield that created within shopify dashboard.'
                        })
                    }
                    $('#show_example').html(`${errors.value}`);
                    $('#value_type-error').html(`${errors.type}`);
                }
            }
        });
    });
    $('.delete_collection_metafield').on('click', function() {
        var metafield_id = $(this).attr('id');
        var collection_id = $(this).attr('data-collection');
        var delete_button = $(this);
        let user_permission = confirm(`Are you sure deleting metafield with id : ${metafield_id} ?`);
        if(user_permission) {
            delete_button.parents("tr").remove();
            var delete_data = {
                id:metafield_id,
                collection_id:collection_id
            }
            $.ajax({
                url: base_url + "/delete-collection-metafield",
                type: 'DELETE',
                /* send the csrf-token and the input to the controller */
                data: delete_data,
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (response) {
                    if(!response.errors)
                    {
                        Toast.fire({
                            icon: 'success',
                            title: 'Collection metafield deleted successfully.'
                        })
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        } else {
            return;
        }
    });
    $('.product_select').on('change', function() {
        var product_id = $(this).val();
        var request_data = {
            product_id
        }
        if(/^\d+$/.test(request_data.product_id))
        {
            console.log("digit value.");
            $('#variant_id').removeAttr('disabled');
            $("product_id-error").html('');
            $.ajax({
                url: base_url + "/get_product_variants",
                type: 'GET',
                /* send the csrf-token and the input to the controller */
                data: request_data,
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (response) {
                    if(!response.errors)
                    {
                        var variant_options = `<option value="">--SELECT--</option>`;
                        response.shopify_response.forEach(variant => {
                            variant_options += `<option value="${variant.id}">${variant.title}</option>`;
                        });
                        $('.select-variant').html(variant_options);
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        } else {
            console.log("not digit value.");
            $('#variant_id').attr('disabled', true);
            $("product_id-error").html('Product is required field.');
        }
    });
    $('#create_variant_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var boolean_conf = {on:true,off:false};
        var formData = {
            _token: event.target[0].value, // _token
            key: event.target[1].value, // Key
            namespace: event.target[2].value, // namespace
            description: event.target[3].value, // description
            product: event.target[4].value, // collection
            variant: event.target[5].value, // collection
            value_type: event.target[6].value, // value_type
            value: (event.target[7].value == "on" || event.target[7].value == "off") && event.target[6].value == "boolean" ? boolean_conf[event.target[7].value] : event.target[7].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/store-variant-metafield",
            type: 'POST',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    form.each(function() {
                        this.reset();
                    });
                    $('#value').attr("disabled", true);
                    $('#show_example').html('Please select type.');
                    Toast.fire({
                        icon: 'success',
                        title: 'Variant metafield created successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                    $('#product_id-error').html('');
                    $('#value_type-error').html('');
                }
            },
            error: function (err) {
                console.log(err);
                if(!err.responseJSON.success)
                {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please try again with all fields.'
                    });
                    var errors = {
                        key: err.responseJSON.errors.key == undefined ? "" : err.responseJSON.errors.key,
                        namespace: err.responseJSON.errors.namespace == undefined ? "" : err.responseJSON.errors.namespace,
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        product:err.responseJSON.errors.product == undefined ? "" : err.responseJSON.errors.product,
                        variant:err.responseJSON.errors.variant == undefined ? "" : err.responseJSON.errors.variant,
                        type:err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type,
                    };
                    $('#key-error').html(`${errors.key}`);
                    $('#key-namespace').html(`${errors.namespace}`);
                    $('#show_example').html(`${errors.value}`);
                    $('#product_id-error').html(`${errors.product}`);
                    $('#value_type-error').html(`${errors.type}`);
                    $('#variant_id-error').html(`${errors.variant}`);
                }
            }
        }); // Key
    });
    $('#update_variant_metafield').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        $('.button-update').toggleClass('loading');
        var metafield_id = form.attr('data-id');
        var boolean_conf = {on:true,off:false};
        var formData = {
            id:metafield_id,
            _method:"PUT",
            _token: event.target[0].value, // _token
            variant: event.target[1].value, // product_id
            description: event.target[4].value, // description
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        $.ajax({
            url: base_url + "/update_variant_metafield",
            type: 'PUT',
            /* send the csrf-token and the input to the controller */
            data: formData,
            dataType: 'JSON',
            /* remind that 'data' is the response of the AjaxController */
            success: function (response) {
                if (response.success)
                {
                    Toast.fire({
                        icon: 'success',
                        title: 'Variant metafield updated successfully.'
                    })
                    $('#key-error').html('');
                    $('#key-namespace').html('');
                    $('#show_example').html('');
                    $('#product_id-error').html('');
                    $('#value_type-error').html('');
                    $('.button-update').toggleClass('loading');
                    window.location.replace(`${base_url}/variant`);
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    console.log(err.responseJSON);
                    var errors = {
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
                        product: err.responseJSON.errors.product == undefined ? "" : err.responseJSON.errors.product,
                        type: err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type
                    };
                    if(errors.type != undefined)
                    {
                        Toast.fire({
                            icon: 'error',
                            title: 'You can\'t change type of those metafield that created within shopify dashboard.'
                        })
                    }
                    $('#show_example').html(`${errors.value}`);
                    $('#value_type-error').html(`${errors.type}`);
                }
            }
        });
    });
    $('.delete_variant_metafield').on('click', function() {
        
        var metafield_id = $(this).attr('id');
        var variant_id = $(this).attr('data-variant');
        var delete_button = $(this);
        let user_permission = confirm(`Are you sure deleting metafield with id : ${metafield_id} ?`);
        if(user_permission) {
            delete_button.parents("tr").remove();
            var delete_data = {
                id:metafield_id,
                variant_id:variant_id
            }
            $.ajax({
                url: base_url + "/delete-variant-metafield",
                type: 'DELETE',
                /* send the csrf-token and the input to the controller */
                data: delete_data,
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (response) {
                    if(!response.errors)
                    {
                        Toast.fire({
                            icon: 'success',
                            title: 'Variant metafield deleted successfully.'
                        })
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        } else {
            return;
        }
    });
});