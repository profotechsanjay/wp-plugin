window.onload = function () {
    if (jQuery(".sneworders").length > 0) {
        var podata = "param=order_counting_change&action=setup_lib";
        jQuery.post(ajaxurl, podata, function (msg) {
            var data = jQuery.parseJSON(msg);  
            var ords = data.arr;
            var neworders = ords.neworders + parseInt(jQuery('.sneworders').text());
            jQuery('.sneworders').text(neworders);
            var delorders = ords.delorders + parseInt(jQuery('.sdeliveredchanges').text());
            jQuery('.sdeliveredchanges').text(delorders);
            var apporders = ords.apporders + parseInt(jQuery('.sapprovedchanges').text());
            jQuery('.sapprovedchanges').text(apporders);
            var reqorders = ords.reqorders + parseInt(jQuery('.sreqchanges').text());
            jQuery('.sreqchanges').text(reqorders);
            var canorders = ords.canorders + parseInt(jQuery('.scancelorders').text());
            jQuery('.scancelorders').text(canorders);
            var allorders = ords.allorders + parseInt(jQuery('.sallorders').text());
            jQuery('.sallorders').text(allorders);            

        });
    }
}