/*Custom JS Code Here !*/
//Code starts by rudra 02-03-2017
$(document).ready(function () {
    var mcc_userid = $("#mccuserid").val();
    var integrate_id = localStorage.getItem("integrate-id");
    if (integrate_id == 4) {

        $('#create_campaign').click();
        $("li.active").removeClass('active');
        $("#home").removeClass('in');
        $("#home").removeClass('active');
        $("#campaign-tab3").addClass('active');
        $('.nav-tabs a[href="#menu2"]').trigger('click');
        $("#menu2").addClass('in');
        $("#menu2").addClass('active');
        localStorage.removeItem("integrate-id");
        $("#campaign-tab3").removeClass('disabledTab');
    }
    $("#test").on("click", function () {
         
        window.analytics=false;
        window.close();
	   $("#ga_connect_data").trigger('click');
           var mcc_user_id = $(this).attr('data-id');

    
       
           
      });

$("#tabmenu2").on("click", function () {


setTimeout(function()
{ 
   var podata = "&param=connect_ga&action=lg_cre_lib";
 $.post(ajaxurl, podata, function (data) {
      var data = $.parseJSON(data);
    $('#menu2').empty();
     $('#menu2').html(data.arr);
     
});
}, 2000);
});
$(document).on("click", ".btn-disconnect", function () {
var mcc_user_id = $(this).attr('data-id');
 var podata = "&param=disconnect_ga&action=lg_cre_lib&id="+mcc_user_id+"&post_Action=DisconnectAnalytics";
$("body").addClass("processing");
 $.post(ajaxurl, podata, function (data) {
$("body").removeClass("processing");
  var data = $.parseJSON(data);
    $('#menu2').empty();
     $('#menu2').html(data.arr);
});

});
$(document).on("click", ".btn-connect", function () {
Stopme=false;
var stopCall = setInterval(function(){ ajax_call() }, 5000);

});
 
$(document).on("change", ".btn-select", function () {
var serial_data = $("#AuthDemoFormFormID").serialize();
 var podata = "&param=select_ga&action=lg_cre_lib&data="+serial_data;
$("body").addClass("processing");
 $.post(ajaxurl, podata, function (data) {
$("body").removeClass("processing");
  var data = $.parseJSON(data);
   $('#menu2').empty();
   $('#menu2').html(data.arr);

});
});
$(document).on("change", ".btn-select1", function () {
var serial_data = $("#AuthDemoFormFormID").serialize();
$("body").addClass("processing");
 var podata = "&param=select_ga1&action=lg_cre_lib&data="+serial_data;

 $.post(ajaxurl, podata, function (data) {
$("body").removeClass("processing");
  var data = $.parseJSON(data);
   $('#menu2').empty();
   $('#menu2').html(data.arr);

});
});
$(document).on("change", ".btn-select2", function () {
var serial_data = $("#AuthDemoFormFormID").serialize();
 var podata = "&param=select_ga2&action=lg_cre_lib&data="+serial_data;
$("body").addClass("processing");
 $.post(ajaxurl, podata, function (data) {
$("body").removeClass("processing");
 //var data = $.parseJSON(data);
   //$('#menu2').empty();
  // $('#menu2').html(data.arr);

});
});
    // Code ends for CRE RUN's
$(document).on("click", "#tab-item3", function () {

        $("body").addClass("processing");
        var mcc_user_id = $(this).attr('data-id');
        var data_website = $(this).attr('data-website');
        var podata = "&param=run_cre&action=lg_cre_lib&type=allpage&data_website=" + data_website + "&mccuserid=" + mcc_user_id;
        $.post(ajaxurl, podata, function (data) {
            $("body").removeClass("processing");
            $("li.active").removeClass('active');
            $("#menu2").removeClass('in');
            $("#menu2").removeClass('active');
            $("#campaign-tab4").removeClass('disabledTab');
            $("#campaign-tab4").addClass('active');
            $("#menu3").addClass('in');
            $("#menu3").addClass('active');
            $('.nav-tabs a[href="#menu3"]').trigger('click');

        });

    });

    $("#camp-srch").on("keyup", function () {
        var srchkeyword = $(this).val();
        var podata = "param=search_campaign&action=lg_cre_lib&srch=" + srchkeyword;
        $.post(ajaxurl, podata, function (response) {
            console.log(response);
        });
    });
    // Code ends for CRE RUN's

});
//Code ends by rudra 02-03-2017

var Stopme=false;
function ajax_call(){
 var podata = "&param=connect_gaa&action=lg_cre_lib";
 
if(Stopme==false)
{
$.post(ajaxurl, podata, function (data) {
var data = $.parseJSON(data);
console.log(data);
if(data.arr.AnalyticsAccountName!="")
{

 var podata1 = "&param=connect_ga&action=lg_cre_lib";
	$.post(ajaxurl, podata1, function (data1) {
      var data1 = $.parseJSON(data1);
          $('#menu2').empty();
          $('#menu2').html(data1.arr);
          Stopme=true;      
      
	});
}
});
}
}
