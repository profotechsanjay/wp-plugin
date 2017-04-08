$ = jQuery;
var modadded = 0;
var dataofform = '';

jQuery(function ($) {
    datatables(); forms(); genfuntions($);    
    chosen_initilize();    
    commonform(); locationvalidation(); smtp_configuration_call();    
});

/*Custom Agency Updated - SMTP Configuration Functions here*/
function smtp_configuration_call() {

    $('[data-toggle="settings-tooltip"]').tooltip();

    /*AJAX Request of SMTP configuration*/
    $("#smtp-details-conf").validate({
        submitHandler: function () {
            $("body").addClass("processing");
            var postdata = $("#smtp-details-conf").serialize() + "&param=smtp_configuration&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                swal({
                    title: '',
                    text: data.msg,
                    timer: 2000,
                    showConfirmButton: false
                });
                location.reload();
            });
        }
    });

    /*Checking Connection with SMTP*/
    $("#mdl-testconnection").validate({
        rules: {
            mdl_email: {required: true, email: true}
        },
        submitHandler: function () {
            $("body").addClass("processing");
            var postdata = $("#mdl-testconnection").serialize() + "&param=test_connection_smtp&action=lg_lib";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                var data = $.parseJSON(response);
                if (data.sts == 1) {
                    $("#testconnection").modal("toggle");
                    swal("Success", data.msg, "success");
                } else {
                    swal("Error", data.msg, "error");
                }
            });
        }
    });

    /*SMTP Status Configuration*/
    $(".chk_smtp").on("click", function () {
        var chkval = $(this).val();
        if (chkval == "disable") {

            var postdata = "param=change_smtp_status&action=lg_lib&status=disable";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                $(".smtp-settings-div").addClass("disableme");
                swal({title: '', text: 'Configuration Disabled', timer: 2000, showConfirmButton: false});
            });


        } else {

            var postdata = "param=change_smtp_status&action=lg_lib&status=enable";
            $("body").addClass("processing");
            $.post(ajaxurl, postdata, function (response) {
                $("body").removeClass("processing");
                $(".smtp-settings-div").removeClass("disableme");
                swal({title: '', text: 'Configuration Enabled', timer: 2000, showConfirmButton: false})
            });

        }

    });
}

function commonform(){
    $(".form_common").validate();    
    $(".sendpwd").on("click",function(){
        show_loader(false);  
        var uid = $("#edit_user_id").val()
        var podata = 'uid='+uid+"&param=pwd_reset_link&action=settings_lib";
        $.post(ajaxurl,podata,function(msg){
            hide_loader();
            var data = $.parseJSON(msg);            
            show_msg(data.sts,data.msg);
            $("#responsive .close").click();
        });
    });
    
}

function remove_parent_user(uid,locid){

    var conf = confirm("Are you sure to remove parent user?");
    if(conf){
        show_loader(false);        
        var podata = 'uid='+uid+"&param=remove_parent_user&action=settings_lib";
        $.post(ajaxurl,podata,function(msg){
            hide_loader();
            var data = $.parseJSON(msg);            
            show_msg(data.sts,data.msg);
            if(data.sts == 1){
                setTimeout(function(){
                   window.location.href = '?parm=ga_connect&location_id='+locid;
                },1000);
            }
        });
    }
}

function datatables(){
        $('#data_location').dataTable
        ({
            // "bJQueryUI": false,
             "bAutoWidth": true,
            "sPaginationType": "full_numbers",
             "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
             "oLanguage": 
             {
                     "sLengthMenu": "<span>Show entries:</span> _MENU_"
             },
             "aaSorting": [[ 3, "desc" ]],
             "aoColumnDefs": [{ "bSortable": false, "aTargets": [4] }]
        });
        
        $('#billing_info_datatable').dataTable
        ({
            // "bJQueryUI": false,
             "bAutoWidth": true,
            "sPaginationType": "full_numbers",
             "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
             "oLanguage": 
             {
                     "sLengthMenu": "<span>Show entries:</span> _MENU_"
             },
             "aaSorting": [[ 0, "asc" ]],
             //"aoColumnDefs": [{ "bSortable": false, "aTargets": [4] }]
        });
        
        $('#purchased_addons_datatable').dataTable
        ({
            // "bJQueryUI": false,
             "bAutoWidth": true,
            "sPaginationType": "full_numbers",
             "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
             "oLanguage": 
             {
                     "sLengthMenu": "<span>Show entries:</span> _MENU_"
             },
             "aaSorting": [[ 0, "asc" ]],
             //"aoColumnDefs": [{ "bSortable": false, "aTargets": [4] }]
        });
        
        $('.commontable').dataTable
        ({
            // "bJQueryUI": false,
             "bAutoWidth": true,
            "sPaginationType": "full_numbers",
             "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
             "oLanguage": 
             {
                     "sLengthMenu": "<span>Show entries:</span> _MENU_"
             }                 
        });
        
        
        $("#pwdresetform").validate({
            submitHandler: function(){
                var podata = $("#pwdresetform").serialize() + "&param=reset_password&action=settings_lib";
                show_loader(false);  
                $.post(ajaxurl,podata,function(msg){
                    hide_loader();                
                    var data = $.parseJSON(msg);
                    alert(data.msg);
                    if(data.sts == 1){
                        setTimeout(function(){
                            window.location.href = jQuery('#baseurl').val();
                        },2000);
                    }
                });
            }
        });
       
        
        
        if( $(".datetimepicker").length > 0 )
            $(".datetimepicker").datetimepicker();
        
}

function forms(){        
    
    $(".form_scripts").on("submit",function(){
        var podata = $(this).serialize()+ "&param=script_save&action=settings_lib";
        show_loader(false);  
        $.post(ajaxurl,podata,function(msg){
                hide_loader();
                var data = $.parseJSON(msg);            
                show_msg(data.sts,data.msg);
            });
        return false;
    });        
    
    $("#whitelabeling").validate({
        submitHandler: function(){            
            var podata = $("#whitelabeling").serialize() + "&param=white_label_settings&action=settings_lib";
            show_loader(false);  
            $.post(ajaxurl,podata,function(msg){
                hide_loader();
                var data = $.parseJSON(msg);                
                if(data.sts == 1){
                    var red = "";                       
                    if($("#slogin").val() == 1){
                       window.location.reload();
                    }
                    else{
                        
                        if($("#urlrewrite").prop('checked') == true){                       
                           red = $("#urlwhitelable").val();
                        }
                        else{
                            red = $("#baseurlcomp").val();                       
                        }

                        red = red.replace(/\/$/, "");
                        red = red  + "/" + $("#locpage").val() +'?parm=company_info';
                        window.location.href = red;
                    }
                    
                }
            });
        }
    });
    
    $("#location_form").validate({
        submitHandler: function(){            
            var podata = $("#location_form").serialize() + "&param=location_saved&action=settings_lib";
            show_loader(true);  
            $.post(ajaxurl,podata,function(msg){
                hide_loader();
                var data = $.parseJSON(msg);
                show_msg(data.sts,data.msg);
                window.location.href = 'parm=locations';
            });
        }
    });
        
    $("#detailcompanyform").validate({
        submitHandler: function(){            
            var podata = $("#detailcompanyform").serialize() + "&param=company_info_update&action=settings_lib";
            show_loader(false);  
            $.post(ajaxurl,podata,function(msg){
                hide_loader();
                var data = $.parseJSON(msg);
                show_msg(data.sts,data.msg);    
                window.location.reload();
            });
        }
    });    
       
    
    $(document).on("click",".btnmentoradd",function(e){
        if($("#mentorform").valid()){            
            var podata = $("#mentorform").serialize()+"&location_id="+$("#location_id").val();            
            show_loader(false);
            $.post(ajaxurl, podata + "&param=add_user&action=settings_lib", function(dat){
                    hide_loader();
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);     
                    $(".msgsml").html(data.msg);
                    if(data.sts == 1){
                      window.location.reload();
                    }               
             });
        }                
        
    });
    
    
    $(document).on("click","#btn_add_location",function(e){                
        var locid = $("#locationname").val(); 
        if(locid == ''){
            alert("Please select location");
            return false;
        }
        show_loader(false);
        $.post(ajaxurl,"locid="+locid + "&param=add_existing_location&action=settings_lib",function(msg){
            console.log(msg);
            hide_loader();
            var data = $.parseJSON(msg);
            show_msg(data.sts,data.msg);    
            window.location.reload();
        });
        
    });
    
    $(document).on("click","#btn_remove_location",function(e){                
        var locid = $("#rmlocationid").val(); 
        if(locid == ''){
            alert("Please select location");
            return false;
        }
        show_loader(false);
        $.post(ajaxurl,"locid="+locid + "&param=remove_existing_location&action=settings_lib",function(msg){
            hide_loader();
            var data = $.parseJSON(msg);
            show_msg(data.sts,data.msg);    
            window.location.reload();
        });
        
    });
    
    $(document).on("click","#btn_assign_location",function(e){                
        var locid = $("#locationname").val(); 
        var locname = $("#locationname option:selected").text();
        if(locid == ''){
            alert("Please select location");
            return false;
        }
        var uid = $("#uid").val();
        show_loader(false);
        $.post(ajaxurl,"locid="+locid + "&uid=" + uid + "&param=assign_location&action=settings_lib",function(msg){
            hide_loader();
            var data = $.parseJSON(msg);
            show_msg(data.sts,data.msg);    
            if(data.sts == 1){
                if($("#BRAND_NAME_"+uid+" .tdlocs").length > 0){
                    if(locid == 'add_all_locations'){
                        var allocs = $("#all_locs_str").val();
                        $("#BRAND_NAME_"+uid+" .tdlocs").html(allocs);
                    }
                    else{
                        $("#BRAND_NAME_"+uid+" .tdlocs").append(", "+locname+" ");
                    }
                }
                else{
                    if(locid == 'add_all_locations'){
                        var allocs = $("#all_locs_str").val();
                        $("#BRAND_NAME_"+uid).html("<div class='tdlocs'>"+allocs+" </div>");
                    }
                    else{
                        $("#BRAND_NAME_"+uid).prepend("<div class='tdlocs'>"+locname+" </div>");
                    }
                }
            }
        });
        
    });
    
    $(document).on("click",".remove_user",function(e){
        
        var conf = confirm("Are you sure to unassign user?");
        if(conf){
            var u_id = $(this).attr("data-id");
            var location_id = $("#location_id").val();            
            var podata = "location_id="+location_id+"&u_id="+u_id;
            show_loader(false);
            $.post(ajaxurl, podata + "&param=remove_user&action=settings_lib", function(dat){
                    hide_loader();
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);     
                    $(".msgsml").html(data.msg);
                    if(data.sts == 1){
                       window.location.reload();
                    }               
             });   
        }
        
    });               
    
    $(document).on("click",".verificode",function(e){      
               
        var podata = "location_id="+$(this).attr('data-location');
        show_loader(false);
        $.post(ajaxurl, podata + "&param=verify_code&action=settings_lib", function(dat){             
                hide_loader();
                var data = jQuery.parseJSON(dat);
                show_msg_time(data.sts,data.msg,15000);
                if(data.sts == 1){
                    $('.verificode').removeClass("btn-red").addClass("btn-green");
                }
                
         }); 
        
    });
    
    $(document).on("click",".copycode",function(e){         
       var rs = copyToClipboard(document.getElementById("copyTarget"));
       if(rs){
           show_msg(1,'Code Copied Successfully');
       }       
    });
    
    $(document).on("change","#tackcodechange",function(e){
       var vl = $(this).val();
       window.location.href = '?parm=tracking-code&location_id='+vl;
    });
    
    $(document).on("change","#gaconnect",function(e){
       var vl = $(this).val();
       window.location.href = '?parm=ga_connect&location_id='+vl;
    });
    
    $(document).on("change","#compurlconnect",function(e){
       var vl = $(this).val();
       window.location.href = '?parm=competitor_url&location_id='+vl;
    });
    
    $(document).on("change","#gconversionurl",function(e){
       var vl = $(this).val();
       window.location.href = '?parm=conversion-urls&location_id='+vl;
    });
    
    $(document).on("change","#locationdd",function(e){
        var vl = $(this).val();
        window.location.href = '?parm=master-user-list&location='+vl;
    });
    
    $(document).on("change","#reportsdd",function(e){
        var vl = $(this).val();
        window.location.href = '?parm=reports&report-type='+vl;
    });
    
    jQuery('.tablleusers').dataTable({
        "order": [[0, "asc"]],
        "iDisplayLength": 25

    });
    
    
    $(document).on("click",".add_exiting_account",function(e){
        $("#existingaccount").modal();
    });
    
    $(document).on("click",".remove_exiting_account",function(e){
        $("#removeaccount").modal();
    });
    
    $(document).on("click",".assignloc a",function(e){
        var uid = $(this).attr("data-uid");
        $("#uid").val(uid);
        $("#locmodal").modal();
    });        
    
    $(document).on("click",".deleteuser",function(e){
        
        var conf = confirm("Are you sure to delete user?");
        if(conf){
            var u_id = $(this).attr("data-id");
            var location_id = $("#location_id").val();
            var podata = "location_id="+location_id+"&u_id="+u_id;
            show_loader(false);
            $.post(ajaxurl, podata + "&param=deleteuser&action=settings_lib", function(dat){
                    hide_loader();
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);     
                    $(".msgsml").html(data.msg);
                    if(data.sts == 1){
                       window.location.reload();
                    }               
             });   
        }
        
    });
    
    
}

var timout;
function show_msg(sts,msg){ 
    
    clearTimeout(timout);
    if(jQuery(".messdv").length == 0){
        jQuery(".msg").html('<div class="messdv"></div>').show();
    }
    
    if(sts == 0){
        jQuery(".messdv").removeClass('alert-success').addClass("alert alert-danger").html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> \n\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n\
            ' + msg).slideDown('slow');
        
    }
    else if(sts == 1){
        jQuery(".messdv").removeClass('alert-danger').addClass("alert alert-success").html(' <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> \n\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n\
            ' + msg).slideDown('slow');
    }
    
    timout = setTimeout(function(){
        jQuery(".msg").slideUp('slow').html('').show();
    },8000);
}

function show_msg_time(sts,msg,time){ 
    
    clearTimeout(timout);
    if(jQuery(".messdv").length == 0){
        jQuery(".msg").html('<div class="messdv"></div>').show();
    }
    
    if(sts == 0){
        jQuery(".messdv").removeClass('alert-success').addClass("alert alert-danger").html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> \n\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n\
            ' + msg).slideDown('slow');
        
    }
    else if(sts == 1){
        jQuery(".messdv").removeClass('alert-danger').addClass("alert alert-success").html(' <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> \n\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n\
            ' + msg).slideDown('slow');
    }
    
    timout = setTimeout(function(){
        jQuery(".msg").slideUp('slow').html('').show();
    },time);
}

function open_modal(id){
    jQuery("#"+id).modal();
}
function close_modal(){
    jQuery(".modal").modal('hide');
}

function reset_form(){
    
    jQuery("form").each(function(){
        jQuery(this).get(0).reset();
    });
    
    if($(".btnupdt").length > 0){
        $(".btnupdt").text("Submit");
    }
    if($("#fileList").length > 0){
        $("#fileList").empty();
    }
    
    jQuery("#helpnkid").val('');
    jQuery("#noteid").val('');
    jQuery("#addmodules #id").val('');
    jQuery("#addlesson #lessid").val('');
    jQuery("#addlesson #lessid").val('');
    jQuery("#addresource #resid").val('');
    
}


function show_loader(text){
    if(text == true){
       $(".patientmsg").show(); 
    }
    $(".contaninerinner").addClass("processing");
}
function hide_loader(){    
    $(".contaninerinner").removeClass("processing")
    $(".patientmsg").hide(); 
}

function locationvalidation(){
    $("#form_url").validate();
}

function genfuntions($){
        
    $(document).on("click",".del_client_loc",function(){
       var conf = confirm("This will delete location permanently. Are you sure to delete this location?");
       if(conf){
            var location_id = $(this).attr("data-id");       
            var podata = "location_id=" + location_id + "&param=delete_client_location&action=settings_lib";
            show_loader(false); 
            $.post(ajaxurl, podata,function(msg){
                hide_loader(); 
                var data = $.parseJSON(msg);
                show_msg(data.sts,data.msg);   
                window.location.reload();
            });
        }
        
    });
            
}

function chosen_initilize(){
    if($('.chosen').length > 0){
        $('.chosen').chosen({
            width: '100%', 
            no_results_text:'Oops, nothing found!'
        });
    }
}

function chosen_reinitilize(){
    if($('.chosen').length > 0){
        $('.chosen').trigger("chosen:updated");
    }
}

function DoubleScroll(element) {
    var scrollbar= document.createElement('div');
    scrollbar.appendChild(document.createElement('div'));
    scrollbar.style.overflow= 'auto';
    scrollbar.style.overflowY= 'hidden';

    scrollbar.firstChild.style.width= element.scrollWidth+'px';
    scrollbar.firstChild.style.paddingTop= '1px';
    scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
    scrollbar.onscroll= function() {
        element.scrollLeft= scrollbar.scrollLeft;
    };
    element.onscroll= function() {
        scrollbar.scrollLeft= element.scrollLeft;
    };
    element.parentNode.insertBefore(scrollbar, element);
}

function copyToClipboard(elem) {
	  // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);
    
    // copy the selection
    var succeed;
    try {
    	  succeed = document.execCommand("copy");
    } catch(e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }
    
    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
}

function analyticfromadmin(locid){
    var podata = 'locid='+locid+"&param=set_analytic_url_session&action=settings_lib";
    $.get(ajaxurl,podata); 
}
