jQuery(function () {
    var $ = jQuery;

    /*Dashboard ICONS tooltips*/
    $('[data-toggle="tooltip"]').tooltip();

    /*Lost Password*/
    $("#lostpwd").validate({
        rules: {lostemail: {required: true, email: true}},
        submitHandler: function () {
            var postdata = $("#lostpwd").serialize() + "&param=lost_password&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                $("#lostpassword").modal("toggle");
                //console.log(response); /*sanjay@rudrainnovatives.com*/
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    swal({
                        title: data.msg,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    swal("Error", data.msg, "error");
                }
            });
        }
    });

    /*Change Password*/
    $("#lostpwdPanel").validate({
        rules: {
            cpwd: {
                required: true,
                minlength: 5
            },

            confpwd: {
                required: true,
                minlength: 5,
                equalTo: "#cpwd"
            }
        },
        submitHandler: function () {
            var postdata = $("#lostpwdPanel").serialize() + "&param=change_password&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                //console.log(response); /*sanjay@rudrainnovatives.com*/
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    swal({
                        title: data.msg,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(function () {
                        window.location.href = data.arr;
                    }, 1000);
                } else {
                    swal("Error", data.msg, "error");
                }
            });
        }
    });


    $("#agencylogin").validate({
        submitHandler: function () {
            var postdata = $("#agencylogin").serialize() + "&param=custom_agency_login&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    window.location.href = data.arr;
                } else {
                    swal("Error", data.arr, "error");
                }
            });
        }
    });
    /*Email Confirmation*/

    $("#change-email").validate({
        submitHandler: function () {
            var postdata = $("#change-email").serialize() + "&param=change_email&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    swal(data.msg);
                    window.location.reload();
                } else {
                    swal("Error", data.msg, "error");
                }
            });
        }
    });


    $("#camp-del-succ").validate({
        submitHandler: function () {
            var confirm = $("#txtConfirm").val();
            var location_id = $("#lc_id").val();
            var del_id = $("#delete_id").val();
            if (confirm.toUpperCase() == "DELETE") {
                $("#" + del_id).parents(".client-column").css("display", "none");
                var podata = "location_id=" + location_id + "&param=del_usermeta&action=lg_cre_lib";
                $.post(ajaxurl, podata, function (msg) {
                    var data = $.parseJSON(msg);
                    if (data.sts == 1) {
                        $("#delete-campaign").modal('toggle');
                    } else if (data.sts == 0) {
                        swal("Error", data.msg, "error");
                    }

                });
            }
        }
    });

    $(document).on("click", ".del_usermeta2", function () {
        $("#lc_id").val($(this).attr("data-id"));
        $("#delete_id").val($(this).attr("id"));
    });


    $("#confirm-email").validate({
        submitHandler: function () {
            var postdata = $("#confirm-email").serialize() + "&param=resend_mail&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    $("body").removeClass("processing");
                    swal({
                        title: data.msg,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    //$("#confirm").css("display", "none");
                    //$("#confirmed").css("display", "block");
                } else {
                    swal("Error", data.msg, "error");
                }
            });
        }
    });

    $("#email-confirmed").validate({
        submitHandler: function () {
            $("body").addClass("processing");
            setTimeout(function () {
                $("body").removeClass("processing");
                $("#confirmed").css("display", "block");
                $("#confirmed").addClass("disabledbutton");
                $("#profile-div").removeClass("disabledbutton");
            }, 1200);
        }
    });
    /* Email Confirmation Ends */

    $("#phone").intlTelInput();

    /*Adding Profile Information*/
    $("#show-info-div").validate({
        submitHandler: function () {
            $("body").addClass("processing");
            setTimeout(function () {
                $("body").removeClass("processing");
                $("#profile-div-form").css("display", "block");
                $("#profile-div").css("display", "none");
            }, 1200);
        }
    });

    $("#show-info").validate({
        submitHandler: function () {
            var postdata = $("#show-info").serialize() + "&param=agency_profile&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                $("#phone_citation").val($("#phone").val());
                //console.log(response);
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    $("body").removeClass("processing");
                    $('#profile-modal-new').modal('toggle');
                    $("#profile-div").css("display", "none");
                    $(".show-me-at").css("display", "block");
                    $("#u_name").html($("#name").val());
                    $("#campaign-div-ref").css("display", "block");
                    $("#campaign-div").css("display", "none");
                    //window.location.reload();
                } else {
                    swal("Error", data.msg, "error");
                }
            });

        }
    });

    /*Adding Profile Ends*/

    /*Campaign Starts*/
    $("#campaign-home").validate({
        submitHandler: function () {
            //console.log("First Campaign Form Called");
        }
    });
    /*Adding First Campaign - Tab#1*/
    $(document).on("click", "#tab-item1", function () {
        var cname = $("#cname").val();
        var website = $("#website").val();
        var country = $("#country_location").val();
        var userid = $("#usid").val();
        var useremail = $("#useremail").val();
        var geolocation = $("#geolocation").val();
        var ctype = $("#ctype").val();
        var uname = $("#uname").val();
        var uphone = $("#userphone").val();

        var postdata = "param=create_campaign&action=lg_lib&userid=" + userid + "&useremail=" + useremail + "&ctype=" + ctype + "&country=" + country;
        postdata += "&geo_location=" + $("#city_location2").val() + "&curl=" + website + "&cname=" + cname + "&uname=" + uname + "&uphone=" + uphone + "&state=" + $("#state_location").val() + "&city=" + $("#city_location2").val() + "&street=" + $("#street_general").val() + "&zipcode=" + $("#zipcode_general").val();
        //console.log($("#city_location2").val());
        $("body").addClass("processing");
        $.post(ajaxurl, postdata, function (response) {
            $("body").removeClass("processing");

            var data = $.parseJSON(response);
            if (data.sts == 1) {

                if (data.msg.length > 0) {
                    $("#key-msg").css({display: "none"});
                    var htmldata = '';
                    if (data.msg.length > 0) {
                        $.each(data.msg, function (index, item) {
                            htmldata += "<option value='" + item.keyword + "'>" + item.keyword + "</option>";
                        });
                    }
                    $("#choosekeywords").html(htmldata);


                } else {
                    $("#key-msg").css({display: "block"});
                    $("#choosekeywords").css({display: "none"});
                    $("#key-msg").html("No Keywords found with location url : <span>" + website+"</span>");
                }

                $("#bus_name").val(website);
                $("#geolocation_citation").val($("#city_location2").val());
                $("#rprt_name").val($("#cname").val());
                $("#country_citation").val(country);
                $("#state_citation").val($("#state_location").val());
                $("#city_addr").val($("#city_location2").val());
                $("#street_addr").val($("#street_general").val());
                $("#zipcode_addr").val($("#zipcode_general").val());

                $("#mccuserid").val(data.arr);
                document.cookie = "mccuserid=" + data.arr;
                $("li.active").removeClass('active');
                $("#home").removeClass('in');
                $("#home").removeClass('active');
                $("#campaign-tab2").addClass('active');
                $('.nav-tabs a[href="#menu1"]').trigger('click');
                $("#menu1").addClass('in');
                $("#menu1").addClass('active');
                $("#campaign-tab2").removeClass('disabledTab');
                //   $('#tabmenu2').trigger('click');
                var podata1 = "&param=connect_ga&action=lg_cre_lib";
                $.post(ajaxurl, podata1, function (data1) {
                    var data1 = $.parseJSON(data1);
                    $('#menu2').empty();
                    $('#menu2').html(data1.arr);

                });
            } else {
                alert(data.msg);
            }
        });
    });


    /*Validate.min.js on Keyword Campaign*/
    $("#campaign-keywords").validate({
        submitHandler: function () {
            if ($(this).hasClass("disabled")) {
                e.preventDefault();
                return false;
            }
            var postdata = $("#campaign-keywords").serialize() + "&param=campaign_keywords&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                //console.log(response);
                $("body").removeClass("processing");
                var data_redirect = $(this).attr('data-redirect');
                $("li.active").removeClass('active');
                $("#menu1").removeClass('in');
                $("#menu1").removeClass('active');
                $("#campaign-tab3").addClass('active');
                $('.nav-tabs a[href="#menu2"]').trigger('click');
                $("#menu2").addClass('in');
                $("#menu2").addClass('active');
                $("#campaign-tab3").removeClass('disabledTab');
            });
        }
    });

    $(document).on("click", ".remove_last_session", function () {
        var data_id = $(this).attr('data-id');
        var postdata = "&param=unset_session&action=lg_lib&current_userID=" + data_id;

        $.post(ajaxurl, postdata, function (response) {
            //$("#create-campaign-modal").modal('toggle');
        });
    });


    /*Phone No Validation*/
    $("#uphoneNo").keydown(function (e) {
	    var charCode = (e.which) ? e.which : e.keyCode;
	    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	    }
    });

    /*Campaign Citation*/
    $("#campaign-citation").validate({
        rules: {
            bus_name: {required: true, url: true}
        },
        submitHandler: function () {
            $("body").addClass("processing");
            var mcc_userid = $("#mccuserid").val();
            var postdata = $("#campaign-citation").serialize() + "&param=run_citation&action=lg_lib&mccuserid=" + mcc_userid;

            $.post(ajaxurl, postdata, function (response) {

                var data = $.parseJSON(response);
                $("body").removeClass("processing");
                console.log(response);
                if (data.sts == 1) {

                    $("li.active").removeClass('active');
                    $("#menu3").removeClass('in');
                    $("#menu3").removeClass('active');
                    $("#campaign-tab5").removeClass('disabledTab');
                    $("#campaign-tab4").addClass('active');
                    $('.nav-tabs a[href="#menu4"]').trigger('click');
                    $("#menu4").addClass('in');
                    $("#menu4").addClass('active');
                } else if (data.sts == 0) {

                }

            });
        }
    });

    $(document).on("click", "#esc-tab-item4", function () {
        $("li.active").removeClass('active');
        $("#menu3").removeClass('in');
        $("#menu3").removeClass('active');
        $("#campaign-tab5").removeClass('disabledTab');
        $("#campaign-tab4").addClass('active');
        $('.nav-tabs a[href="#menu4"]').trigger('click');
        $("#menu4").addClass('in');
        $("#menu4").addClass('active');

    });

    $(document).on("click", "#tab-item5", function () {
        var compurl_1 = $("#compurl_1").val();
        var compurl_2 = $("#compurl_2").val();
        var compurl_3 = $("#compurl_3").val();
        var mcc_userid = $("#mccuserid").val();
        var data_id = $(this).attr('data-id');
        var postdata = "param=run_competitor&action=lg_lib&mccuserid=" + mcc_userid + "&url1=" + compurl_1 + "&url2=" + compurl_2 + "&url3=" + compurl_3;
        $("body").addClass("processing");

        $.post(ajaxurl, postdata, function (response) {
            $("body").removeClass("processing");
            var data = $.parseJSON(response);
            if (data.sts == 1) {
                $("#create-campaign-modal").modal('toggle');
                var postdata = "&param=unset_session&action=lg_lib&current_userID=" + data_id;
                $.post(ajaxurl, postdata, function (response) {}); //location.reload();
                window.location.href = data.arr;
            } else {
                swal("Error", data.msg, "error");
            }
        });
    });

    $(document).on("click", "#esc-tab-5", function () {
        var data_id = $(this).attr('data-id');
        var postdata = "param=unset_session&action=lg_lib&current_userID=" + data_id;
        $("body").addClass("processing");
        $.post(ajaxurl, postdata, function (response) {
            $("#create-campaign-modal").modal('toggle');
            $("body").removeClass("processing");
            location.reload();
            //window.location.reload();
        });
    });

    /*Campaign Ends*/

    /* Campaign Edit */

    $(document).on("click", ".c-edit", function () {
        var cname = $(this).attr("data-cname");
        var id = $(this).attr("data-id");
        var url = $(this).attr("data-web");
        var country = $(this).attr("data-country");
        var ctype = $(this).attr("data-ctype");
        var geo = $(this).attr("data-geo");

        $("#cname_edit").val(cname);
        $("#website_ed").val(url);
        $("#country_location_edit").val(country);
        $("#geolocation_edit").val(geo);
        $("#ctype_edit").val(ctype);
        $("#mccuser_edit").val(id);

    });


    $("#campaign-edit").validate({
        submitHandler: function () {

            var postdata = $("#campaign-edit").serialize() + "&param=campaign_edit&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    $("body").removeClass("processing");
                    swal(data.msg);
                    setTimeout(function () {
                        location.reload();
                        //window.location.reload();
                    }, 1000);

                } else {
                    swal("Error", data.msg, "error");
                }
            });

        }
    });

    $(document).on("click", ".del_usermeta", function () {
        var conf = confirm("This will delete location permanently. Are you sure to delete this Campaign?");
        if (conf) {
            var location_id = $(this).attr("data-id");
            var podata = "location_id=" + location_id + "&param=del_usermeta&action=lg_lib";
            $.post(ajaxurl, podata, function (msg) {
                var data = $.parseJSON(msg);
                if (data.sts == 1) {
                    setTimeout(function () {
                        swal(data.msg);
                    }, 1000);
                } else if (data.sts == 0) {
                    swal("Error", data.msg, "error");
                }

            });
        }

    });

    $(document).on("change", ".country_location", function () {

        var country_code = $(this).val();
        var podata = "country_code=" + country_code + "&param=state_list&action=lg_lib";
        $.post(ajaxurl, podata, function (msg) {
            var data = $.parseJSON(msg);
            var html = "<option value='-1'>Choose State</option>";
            $.each(data.msg, function (i, item) {
                html += "<option value='" + item.title + "'>" + item.title + "</option>";
            });
            $(".state_location").html(html);
        });
    });

    $(document).on("change", ".state_location", function () {
        var state_code = $(this).val();
        var podata = "state_code=" + state_code + "&param=city_list&action=lg_lib";
        $.post(ajaxurl, podata, function (msg) {
            var data = $.parseJSON(msg);
            var html = "<option value='-1'>Choose City</option>";
            $.each(data.msg, function (i, item) {
                html += "<option value='" + item.id + "'>" + item.title + "</option>";
            });
            $(".city_location").html(html);
        });
    });

    $("#add_profile_info").validate({
        submitHandler: function () {
            $("#confirm").addClass("disabledbutton");
            $("#add_profile_info").addClass("disabledbutton");
            $("#campaign-div").removeClass("disabledbutton");
            $("#profile-div-form").addClass("disabledbutton");
        }
    });

    $("#campaign-form").validate({
        submitHandler: function () {
            $("body").addClass("processing");
            setTimeout(function () {
                $("body").removeClass("processing");
                $("#pullKeywords").modal({backdrop: 'static', keyboard: false});
            }, 1200);
            // window.location.href=$("#redirectUri").val();
        }
    });

    $("#slctKeywords").validate({
        submitHandler: function () {
            $("body").addClass("processing");
            setTimeout(function () {
                $("body").removeClass("processing");
                window.location.href = $("#redirectUri").val();
            }, 1500);
        }
    });

    $("#runcomp").validate({
        rules: {required: true, url: true},
        submitHandler: function () {
            $("body").addClass("processing");
            var postdata = $("#runcomp").serialize() + "&param=agency_comp&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                // console.log(response);
                return false;
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    window.location.href = data.arr;
                } else {
                    swal("Error", data.arr, "error");
                }
            });
        }
    });
});

function isUrl(s) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
    return regexp.test(s);
}

