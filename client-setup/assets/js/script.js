$ = jQuery;
var modadded = 0;
var dataofform = '';

jQuery(function ($) {
    //Code starts by Rudra
    datatables(); forms(); genfuntions($); cronstatusset();
    //Code ends by Rudra
    chosen_initilize(); step_second_setup();
    if($("#iscompleted").length > 0 && $("#iscompleted").val() == 0){
        setInterval(function(){
            step_second_setup();
        },5000);
    }


    $(document).on("click",".copybcode",function(e){
       var rs = copyClipboard(document.getElementById("copybTarget"));
       if(rs){
           show_msg(1,'Jobs Copied Successfully');
       }
   });

   $(document).on("click",".chkmarkag",function(e){
       if($(this).val() == 1){
           $(".agencilist").prop("checked",true);
       }else{
           $(".agencilist").prop("checked",false);
       }

   });

   $(document).on("click",".chkmarkthm",function(e){
       if($(this).val() == 1){
           $(".thmchkfile").prop("checked",true);
       }else{
           $(".thmchkfile").prop("checked",false);
       }

   });

   $(document).on("click",".chkmarktpl",function(e){
       if($(this).val() == 1){
           $(".plugchk").prop("checked",true);
       }else{
           $(".plugchk").prop("checked",false);
       }

   });


   $(document).on("click",".chkmarktroot",function(e){
       if($(this).val() == 1){
           $(".rootchk").prop("checked",true);
       }else{
           $(".rootchk").prop("checked",false);
       }

   });

   $(document).on("change","#backetime",function(e){
       var val = $(this).val();
       window.location.href = 'admin.php?page=client_setups&deployment&timebefore='+val;
   });

   $(document).on("click",".updateagencyfiles",function(e){

       var conf = confirm("Are you sure?");
       if(conf){

            var agencies = [];

            $('.agencilist').each(function(){
                if($(this).prop("checked")) { agencies.push($(this).val()); }
            });

            var themes = [];
            $('.thmchkfile').each(function(){
                if($(this).prop("checked")) { themes.push($(this).val()); }
            });

            var plugins = [];

            $('.plugchk').each(function(){
                if(!$(this).hasClass("setting_plugin")){
                    if($(this).prop("checked")) { plugins.push($(this).val()); }
                }
            });

            var settingplugin = 0;

            if($('.setting_plugin').prop("checked")) { settingplugin = 1;}

            var rootfiles = [];
            $('.rootchk').each(function(){
                if($(this).prop("checked")) { rootfiles.push($(this).val()); }
            });

            var mainar = {
                'agencies' : agencies,
                'themes' : themes,
                'plugins' : plugins,
                'settingplugin' : settingplugin,
                'rootfiles' : rootfiles
            };

            mainar = JSON.stringify(mainar);

            var podata = $("#formupdatefiles").serialize()+ '&mainar='+ mainar + "&param=updateagencyfiles&action=setup_lib";
            show_loader(false);
            $.post(ajaxurl,podata,function(msg){
                 hide_loader();
                 var data = $.parseJSON(msg);
                 console.log(data);
                 show_msg(data.sts,data.msg);
                 if(data.sts == 1){
                     $("#formupdatefiles").get(0).reset();
                 }
            });
        }

   });


});

function step_second_setup(){
    if($("#iscompleted").length > 0 && $("#iscompleted").val() == 0){
        var podata = "setup_id="+$("#setup_id").val()+"&param=setup_completed&action=setup_lib";
        $.post(ajaxurl,podata,function(msg){
                 var data = $.parseJSON(msg);
                 if(data.sts == 1){
                    $(".progressbar").css("width",data.arr+"%").text(data.arr+"% Completed");
                    $(".backgroundscannings").removeClass("hide");
                 }

            });
    }
}
function datatables(){
    $('#data_announcement').dataTable
    ({
        // "bJQueryUI": false,
         "bAutoWidth": true,
        "sPaginationType": "full_numbers",
         "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
         "oLanguage":
         {
                 "sLengthMenu": "<span>Show entries:</span> _MENU_"
         },
         "aaSorting": [[ 4, "desc" ]],
         "aoColumnDefs": [{ "bSortable": false, "aTargets": [5] }]
    });


    jQuery(".datetimepicker").datetimepicker();

}

function forms(){

    $("#add_new_setup").validate({
        submitHandler: function(){
            var podata = $("#add_new_setup").serialize() + "&param=add_setup&action=setup_lib";
            show_loader(false);
            $.post(ajaxurl,podata,function(msg){
                hide_loader();
                var data = $.parseJSON(msg);
                show_msg(data.sts,data.msg);
                if(data.sts == 1){
                    window.location.href = "admin.php?page=new_setup&setup_id="+data.arr;
                }
            });
        }
    });

    $("#confiure_setup").validate({
        submitHandler: function(){
            if($("#setup_update").val() != 0){
                var conf = confirm("Are you sure to re-build setup?");
                if(conf == false){
                    return false;
                }
            }
            var podata = $("#confiure_setup").serialize() + "&param=setup_creation&action=setup_lib";
            show_loader(true);
            $.post(ajaxurl,podata,function(msg){
                hide_loader();
                var data = $.parseJSON(msg);
                show_msg(data.sts,data.msg);
                window.location.reload();
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

function genfuntions($){

    $(document).on("click",".check_availablity",function(){
       var prefix = $.trim($("#add_new_setup #prefix").val());
       if($("#add_new_setup #url").val() == ''){
           alert("Please enter value Client prefix");
           return false;
       }
       var podata = "prefix=" + prefix + "&param=check_availablity&action=setup_lib";
       show_loader(false);
       $.post(ajaxurl, podata,function(msg){
           hide_loader();
           var data = $.parseJSON(msg);
           show_msg(data.sts,data.msg);
       });

    });


    $(document).on("click",".rebuilconfig",function(){
        var conf = confirm("Are you sure to reset configuration file?");
        if(conf == false){
            return false;
        }

        show_loader(false);
        $("#global_config_auto").submit();

    });

    $(document).on("click",".statussetup",function(){
        var status = $.trim($(this).attr('data-sts'));
        var obj = $(this);
        var conf = confirm("Are you sure "+status+" this setup?");
        if(conf){
            var setup_id = $.trim($(this).attr('data-id'));
            var status = $.trim($(this).attr('data-attr'));

            var podata = "setup_id=" + setup_id + "&status=" + status + "&param=setup_status&action=setup_lib";
            show_loader(false);
            $.post(ajaxurl, podata,function(msg){
                hide_loader();
                var data = $.parseJSON(msg);
                show_msg(data.sts,data.msg);
                if($(".setuppageinner").length > 0){
                    window.location.reload();
                } else {
                    if(status == 1){
                        $(".statustd[data-id="+setup_id+"]").html('Enabled');
                        obj.text('Disable').addClass("btn-warning").removeClass('btn-success').attr("data-sts","Disable").attr("data-attr",0);
                    }
                    else{
                        $(".statustd[data-id="+setup_id+"]").html('Disabled');
                        obj.text('Enable').addClass("btn-success").removeClass('btn-warning').attr("data-sts","Enable").attr("data-attr",1);
                    }
                }
            });
        }

    });

    $(document).on("click",".updateconfig", function(){
        show_loader(false);
        $("#global_config_form").submit();

    });

    $(document).on("click",".deletesetup",function(){
        var conf = confirm("This will also delete database, users, files. Are you sure delete this setup?");
        if(conf){
            var setup_id = $.trim($(this).attr('data-id'));
            var podata = "setup_id=" + setup_id + "&param=setup_delete&action=setup_lib";
            show_loader(true);
            $.post(ajaxurl, podata,function(msg){
                console.log(msg);
                hide_loader();
                var data = $.parseJSON(msg);
                show_msg(data.sts,data.msg);
                if(data.sts == 1){
                    setTimeout(function(){
                        window.location.href = 'admin.php?page=client_setups';
                    },2000);
                }

            });
        }
    });

    $(document).on("click",".rebuildhtaccess",function(){
        var conf = confirm("This will re-create htaccess file at root. Are you sure?");
        if(conf){
            var podata = "param=htaccess_recreate&action=setup_lib";
            show_loader(true);
            $.post(ajaxurl, podata,function(msg){
                console.log(msg);
                hide_loader();
                var data = $.parseJSON(msg);
                show_msg(data.sts,data.msg);
                if(data.sts == 1){
                    window.location.reload();
                }

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


function copyClipboard(elem) {
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
//Code starts by Rudra
function cronstatusset(){

  jQuery(document).on('click', '.disablecron', function () {
    var obj = jQuery(this);
    var conf = confirm('Are you sure for this Step?');
    if (conf) {
        var data_id = jQuery(this).attr('data-id');
        var data_status = jQuery(this).attr('data-status');

        var podata = "param=callfunction&action=setup_lib&data_id="+data_id+"&data_status="+data_status;
        show_loader(true);
        $.post(ajaxurl,podata,function(response){
          hide_loader();
          var data = $.parseJSON(response);
          show_msg(data.sts,data.msg);
          if(data_status == 1){
            $(obj).attr('data-status','0').removeClass('btn-success').addClass('btn-warning').val('Disable Crons');
          } else {
            $(obj).attr('data-status','1').removeClass('btn-warning').addClass('btn-success').val('Enable Crons');
          }
        });
      }
  });

}
//Code ends by Rudra