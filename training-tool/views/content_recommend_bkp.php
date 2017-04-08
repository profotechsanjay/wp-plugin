<?php

login_check();
wp_enqueue_style('style.css', TR_COUNT_PLUGIN_URL .'/assets/css/style.css','', TT_VERSION);
wp_enqueue_script('script.js', TR_COUNT_PLUGIN_URL .'/assets/js/script.js?ver=','', TT_VERSION);


?>
<style>
    h4{
        font-size: 18px !important;
        font-weight: 600;
    }
</style>
<input type="hidden" id="hidcontentrec" name="hidcontentrec" value="1" />
<div id="primary" class="site-content" style="min-height: 400px">

    <div id="content" role="main">

        <div class='col-md-12'>
            <h4>Content Recommendation Engine</h4>
            <div class='row'>
                <div class='col-md-12'>
                    <form name='contentform' id='contentform' method="post">
                        <div class='row'>
                            <div class='col-md-5'>
                                <input type="text" name="weburl" id="weburl" class='form-control' />
                            </div>
                            <div class='col-md-5'>
                                <a href='javascript:;' class='getcontentreport btn btn-primary'>Run Report</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div>
                
            </div>
        </div>
    
    </div>
</div>