jQuery(function () {
    var $ = jQuery;
    $('#agency-request').DataTable();
    $('#agency-rejected').DataTable();
    $('#agency-created').DataTable();
    $('#password').removeAttr("readonly");
    $('#login').removeAttr("readonly");

    /*Generate Password*/
    $("#gen-pwd").on("click", function () {

        var specials = '!@#$%^&*()_+{}"~';
        var lowercase = 'abcdefghijklmnopqrstuvwxyz';
        var uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var numbers = '0123456789';

        var all = specials + lowercase + uppercase + numbers;

        var password = '';
        password += specials.pick(1);
        password += lowercase.pick(1);
        password += uppercase.pick(1);
        password += all.pick(3, 10);
        password = password.shuffle();
        $("#password,#confirmpassword").val(password);
        //console.log(password);
    });

    /*Show/Hide Password*/
    $("#show-hide-pwd").on("click", function () {
        var attr = $("#password").attr("type");
        var toogleType = '';
        var toogleText = '';
        if (attr == "password") {
            toogleType = "text";
            toogleText = "Hide"
        } else if (attr == "text") {
            toogleType = "password";
            toogleText = "Show"
        }
        $("#password , #confirmpassword").attr("type", toogleType);
        $("#status-toggler").html(toogleText);
    });

    /* Syncing of SS Data */
    $("#sync-ss-data").on("click", function () {
        var podata = "param=sync_ss_data&action=cs_lib";
        $("body").addClass("processing");
        $.post(ajaxurl, podata, function (response) {
             $("body").removeClass("processing");
             var data = $.parseJSON(response);
             swal(data.msg.message);
             if(data.msg.status==1){ setTimeout(function(){ window.location.reload(); },500); }
             
        });
    });

    $('#agency-request').on('click', '.del-request', function () {
        var id = $(this).attr("data-id");
        var type = $(this).attr("data-type");
        if (type == "reject") {
            var podata = "param=reject_setup&action=cs_lib&setupid=" + id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                var data_row = '#row' + id;
                $(data_row).remove();
                $('#agency-request').DataTable();
                swal(data.msg);
                setTimeout(function () {
                    window.location.reload();
                }, 1200);
            });
        } else if (type == "create") {
            var podata = "param=add_setup&action=setup_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, podata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                swal(data.msg);
                setTimeout(function () {
                    window.location.reload();
                }, 1200);
            });
        } else {
            console.log("No Value Found.");
        }

    });

    $('#agency-request').on('click', '.appenddetails', function () {
        $('.modal').click();
        $("#prefix").val($(this).attr("data-prefix"));
        $("#name").val($(this).attr("data-name"));
        $("#email").val($(this).attr("data-email"));
        $("#dbid").val($(this).attr("data-id"));
        $("#emailAddr").val($(this).attr("data-email"));
        $("#email_id").val($(this).attr("data-email"));
        $("#email_id").attr('readonly');
        $('#submit_bulid').attr('data-id', $(this).attr("data-id"));

    });


    $("#mylogindetails").validate({
        rules: {password: {required: true}, confirmpassword: {required: true, equalTo: "#password"}},
        submitHandler: function () {

            /*var password = $("#password").val();
             var confirmPassword = $("#confirmpassword").val();
             console.log("form Submitted");
             return false;*/

            var podata = $("#mylogindetails").serialize() + "&param=add_setup&action=setup_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, podata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    console.log(data);
                    $("#message").html(data.msg);
                    $("#setup_id").val(data.arr);
                    $('#loginDetails').modal('hide');
                    $('#buildsetup').modal('show');
                } else {
                    swal(data.msg);
                }
            });
        }
    });

    $("#mycreatedetails").validate({
        submitHandler: function () {
            var podata = $("#mycreatedetails").serialize() + "&param=create_agency_setup&action=cs_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, podata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                if (data.sts == 1) {

                    swal("Client Created Successfully");

                    setTimeout(function () {
                        window.location.reload();
                    }, 700);

                } else {

                    swal(data.msg);
                }
            });
        }
    });

    $("#mysetupbuild").validate({
        submitHandler: function () {
            var data_id = $('#submit_bulid').attr('data-id');

            var podata = $("#mysetupbuild").serialize() + "&param=setup_creation&action=setup_lib";
            $("body").addClass("processing1");
            $.post(ajaxurl, podata, function (response) {
                $("body").removeClass("processing1");
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    $('#buildsetup').modal('hide');
                    var postdata = "param=create_setup&action=cs_lib&setupid=" + $("#dbid").val();
                    $.post(ajaxurl, postdata, function (response) {
                        var data = $.parseJSON(response);
                        if (data.sts == 1) {
                            $("#buildsetup .close").click();
                            var id = "#row" + data_id;
                            // console.log(id);
                            $(id).remove();
                            swal(data.msg);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1200);
                        }
                    });

                } else {
                    swal("Failed to Create");
                }
            });
        }
    });

    $(document).on("change", ".country_location", function () {

        var country_code = $(this).val();
        var podata = "country_code=" + country_code + "&param=state_list&action=cs_lib";
        $.post(ajaxurl, podata, function (msg) {
            var data = $.parseJSON(msg);
            var html = "<option value='-1'>Choose State</option>";
            $.each(data.msg, function (i, item) {
                html += "<option value='" + item.title + "'>" + item.title + "</option>";
            });
            $(".state_location").html(html);
        });
    });

});

/*Strong Password Generator*/
String.prototype.pick = function (min, max) {
    var n, chars = '';

    if (typeof max === 'undefined') {
        n = min;
    } else {
        n = min + Math.floor(Math.random() * (max - min + 1));
    }

    for (var i = 0; i < n; i++) {
        chars += this.charAt(Math.floor(Math.random() * this.length));
    }

    return chars;
};

String.prototype.shuffle = function () {
    var array = this.split('');
    var tmp, current, top = array.length;

    if (top)
        while (--top) {
            current = Math.floor(Math.random() * (top + 1));
            tmp = array[current];
            array[current] = array[top];
            array[top] = tmp;
        }

    return array.join('');
};

  
