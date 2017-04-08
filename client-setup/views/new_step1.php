<div class="contaninerinner">

    <h4>New Setup</h4>
    <div class="bread_crumb">
        <ul>
            <li title="Client Setups">
                <a href="admin.php?page=client_setups">Client Setups</a> >>
            </li>
            <li title="New Setup">
                New Setup
            </li>
        </ul>
    </div>

    <div class="panel panel-primary">
        <div class="pull-right"><a href="admin.php?page=client_setups" class="btn btn-danger"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back</a></div>
        <div class="panel-heading">New Setup</div>
        <div class="panel-body">
            
            <form action="#" method="post" id="add_new_setup" name="add_new_setup" class="form-horizontal">

                <div class="form-group">
                    <label for="name" class="col-lg-2 control-label">Name* :</label>
                    <div class="col-lg-8">
                        <input type="text" required class="form-control" id="name" name="name" placeholder="Client Name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="name" class="col-lg-2 control-label">Email* :</label>
                    <div class="col-lg-8">
                        <input type="text" email='true' required class="form-control" id="email" name="email" placeholder="Client Email">
                    </div>
                </div>                
                <div class="form-group">
                    <label for="login" class="col-lg-2 control-label">Login * :</label>
                    <div class="col-lg-8">
                        <input type="text" required class="form-control" id="login" name="login" placeholder="Login ID">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="col-lg-2 control-label">Password * :</label>
                    <div class="col-lg-8">
                        <input type="password" required class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="cpassword" class="col-lg-2 control-label">Confirm Password * :</label>
                    <div class="col-lg-8">
                        <input type="password" required equalTo='#password' class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password">
                    </div>
                </div>
                
                 <div class="form-group">
                    <label for="prefix" class="col-lg-2 control-label">URL * :</label>
                    <div class="col-lg-2">
                        <input type="text" required class="form-control" style="text-transform: lowercase;" id="prefix" name="prefix" placeholder="URL Prefix">
                    </div>
                    <div class="col-lg-2 domainname">
                        .<?php echo ST_DOMAIN; ?>
                     </div>
                    <div class="col-lg-3 availability">
                        <a href="javascript:;" class="check_availablity">Check Availability</a>
                    </div>
                </div>
                                    
                <div class="form-group">
                    <label for="add_btn" class="col-lg-2 control-label"></label>
                    <div class="col-lg-8">
                        <input type="submit" value="Next >>" class="btn btn-primary"/>           
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>