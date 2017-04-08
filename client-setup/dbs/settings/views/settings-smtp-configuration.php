<?php
include_once 'common.php';
global $wpdb;
global $current_user;

$find_smtp_status = get_option("smtp_conf_status");
$enabled = false;
if (!empty($find_smtp_status) && $find_smtp_status == "enable") {
    $enabled = true;
}
?>

<style>
    #smtp-details-conf label.error,#mdl-testconnection label.error{color:red;}
    #smtp-details-conf .smtp-settings-div{padding: 20px;box-shadow: 0px 0px 6px 1px;}
    .disableme{ pointer-events:none ;opacity:0.3; }
    #smtp-details-conf label.question-mark{text-align:left;}
    #smtp-details-conf .resizeme-quot{}
    #smtp-details-conf .tooltip {
        color: white !important;
        font:11px "Open Sans",sans-serif;
        width: 
    }
    #smtp-details-conf .tooltip{ border-radius: 3px !important;width:100% !important;}
</style>

<div class="contaninerinner">  
    <div class="panel panel-primary">

        <div class="panel-heading">Configuration - Proxy Server Settings</div>
        <div class="panel-body">

            <form class="form-horizontal" id="smtp-details-conf">
                <div class="form-group">
                    <label class="control-label col-sm-2">SMTP Settings:</label>
                    <div class="col-sm-10">
                        <input type="radio" value="enable" class="chk_smtp" name="chk_smtp" <?php
                        if ($enabled) {
                            echo "checked";
                        }
                        ?> /> Enable &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" value="disable" class="chk_smtp" <?php
                               if (!$enabled) {
                                   echo "checked";
                               }
                               ?> name="chk_smtp"/> Disable
                    </div>
                </div>
                <div class="smtp-settings-div <?php
                if (!$enabled) {
                    echo 'disableme';
                }
                ?>">
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="smtp">SMTP Server <i class="fa fa-question-circle resizeme-quot" data-toggle="settings-tooltip" title="You need the SMTP server settings if you want to send email from your SMTP server account through an email software program.Example for Server Gmail, SMTP Server is smtp.gmail.com" aria-hidden="true"></i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="smtp" id="smtp" value="<?php echo get_option('user_smtp'); ?>" required="" placeholder="SMTP Server">
                        </div>
                       
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="txtUsername">Username <i class="fa fa-question-circle resizeme-quot" data-toggle="settings-tooltip" title="SMTP username, Example Your Gmail address (e.g. example@gmail.com)" aria-hidden="true"></i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="txtUsername" value="<?php echo get_option('user_smtp_username'); ?>" id="txtUsername" required="" placeholder="Username">
                        </div>
                        
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="txtPassword">Password <i class="fa fa-question-circle resizeme-quot" data-toggle="settings-tooltip" title="SMTP password, Example Your Gmail password" aria-hidden="true"></i></label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="txtPassword" name="txtPassword" value="<?php echo get_option('user_smtp_password'); ?>" required="" placeholder="Password">
                        </div>
                        
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="txtEmailFrom">Email From <i class="fa fa-question-circle resizeme-quot" data-toggle="settings-tooltip" title="Email address used to send mail. By default you should give your SMTP Username" aria-hidden="true"></i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="txtEmailFrom" value="<?php echo get_option('user_smtp_email_from'); ?>" name="txtEmailFrom" required="" placeholder="Email From">
                        </div>
                       
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="txtFromName">From Name <i class="fa fa-question-circle resizeme-quot" data-toggle="settings-tooltip" title="Reciever will get mail from Which name. You should specify the name of SMTP Server Owner. For example in case of Gmail, Gmail account name." aria-hidden="true"></i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="txtFromName" value="<?php echo get_option('user_smtp_from_name'); ?>" name="txtFromName" required="" placeholder="From Name">
                        </div>
                        
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="txtPortnumber">Port Number <i class="fa fa-question-circle resizeme-quot" data-toggle="settings-tooltip" title="SMTP Port, Example in case of GMAIL (TLS : 587 ,SSL : 465). Mail is going from TLS by default, so you should use 587 if using GMAIL SMTP Account." aria-hidden="true"></i></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="txtPortnumber" value="<?php echo get_option('user_smtp_port'); ?>" name="txtPortnumber" required="" placeholder="SMTP Port No">
                        </div>
                        
                    </div>
                    <div class="form-group"> 
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Submit</button> &nbsp;&nbsp;&nbsp;<!--button type="button" class="btn btn-success" id="test-smtp-connection" data-toggle="modal" data-target="#testconnection">Test Connection</button-->
                        </div>
                    </div>
                </div>
            </form>

        </div>

    </div>

</div>
<!-- Modal Used to Test Connection -->
<div id="testconnection" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">SMTP - Test Connection</h4>
            </div>
            <div class="modal-body">
                <form id="mdl-testconnection">
                    <div class="form-group">
                        <label for="email">Email address(Mail To):</label>
                        <input type="text" class="form-control" placeholder="Email Address ( To )" required id="email" name="mdl_email" id="mdl_email">
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>

    </div>
</div>

