var base_url = "https://835e-103-54-21-38.ngrok.io";
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
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    var errors = {
                        key: err.responseJSON.errors.key == undefined ? "" : err.responseJSON.errors.key,
                        namespace: err.responseJSON.errors.namespace == undefined ? "" : err.responseJSON.errors.namespace,
                        description: err.responseJSON.errors.description == undefined ? "" : err.responseJSON.errors.description,
                        value: err.responseJSON.errors.value == undefined ? "" : err.responseJSON.errors.value,
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
                    window.location.replace(`${base_url}/`);
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    console.log(err);
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
            product_id: event.target[4].value, // product
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        console.log(formData);
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
                        product_id:err.responseJSON.errors.product_id == undefined ? "" : err.responseJSON.errors.product_id,
                        product_id:err.responseJSON.errors.type == undefined ? "" : err.responseJSON.errors.type
                    };
                    $('#key-error').html(`${errors.key}`);
                    $('#key-namespace').html(`${errors.namespace}`);
                    $('#show_example').html(`${errors.value}`);
                    $('#product_id-error').html(`${errors.product_id}`);
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
            product_id: event.target[1].value, // product_id
            description: event.target[4].value, // description
            value_type: event.target[5].value, // value_type
            value: (event.target[6].value == "on" || event.target[6].value == "off") && event.target[5].value == "boolean" ? boolean_conf[event.target[6].value] : event.target[6].value, // value
            type: metafield_api_name
        }
        console.log(formData);
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
                    console.log(response);
                    Toast.fire({
                        icon: 'success',
                        title: 'Product metafield updated successfully.'
                    })
                    window.location.replace(`${base_url}/product`);
                }
            },
            error: function (err) {
                if(!err.responseJSON.success)
                {
                    console.log(err);
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
});