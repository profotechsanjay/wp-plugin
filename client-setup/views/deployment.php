<?php

include_once 'common.php';
global $wpdb;



$setup = $wpdb->get_row
(
	$wpdb->prepare
	(
		"SELECT * FROM ". setup_table() . " WHERE id = %d",
		73
	)
);


$src = ST_ANALYTIC_DIR;
$dir = "/var/www/enfusen.com/ttestanalytics"; //$setup->analytic_dir;
$client_db = $setup->db_name;
$user = $setup->db_username;
$pass = $setup->db_password;
$analytic_db = $setup->analytic_db;
$igonre_folders = array(ST_ANALYTIC_DIR.'/app',ST_ANALYTIC_DIR.'/public',ST_ANALYTIC_DIR.'/vendor',ST_ANALYTIC_DIR.'/cgi-bin',
        ST_ANALYTIC_DIR.'/.htpasswds',ST_ANALYTIC_DIR.'/ecdemo',ST_ANALYTIC_DIR.'/crm',ST_ANALYTIC_DIR.'/nbproject',
    ST_ANALYTIC_DIR.'/bootstrap');

rrcopy($src, $dir, $client_db, $user, $pass, $analytic_db, $igonre_folders, $setup);
error_reporting(E_ALL);
function rrcopy($src, $dest, $client_db, $user, $pass, $analytic_db, $filters, $setup) {
           
    $dbname = DB_NAME;
    $dbhost = DB_HOST;
    $dbuser = DB_USER;
    $dbpass = DB_PASSWORD;
       
    // If source is not a directory stop processing
    if (!is_dir($src))
        return false;

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
        if ($f->isFile()) {   
                        
            $file_name = $f->getFilename();            
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if($ext != 'zip' && $ext != 'tar' && $ext != 'rar' && $ext != 'xz' && $ext != 'tar.xz'){                            
                $dest_file = "$dest/" . $file_name;
                copy($f->getRealPath(), $dest_file);
                if($file_name == 'global_config.php' || $file_name == 'analytics_config.php'){                                        
                    
                    /* Main MCC DB string replaces */
                    
                    $str = file_get_contents(ST_COUNT_PLUGIN_DIR.'/dbs/global_config.php');
                    $str = str_replace("{{mcc_url}}", 'http://'.$setup->url, $str);
                    $str = str_replace("{{analytical_url}}", 'http://'.$setup->analytic_url, $str);                    
                    $str = str_replace("{{mcc_host}}", DB_HOST,$str);
                    $str = str_replace("{{mcc_user}}", $user,$str);
                    $str = str_replace("{{mcc_pwd}}", $pass,$str);
                    
                    $str = str_replace("{{mcc_db}}", $client_db,$str);
                    $str = str_replace("{{analytical_db}}", $setup->analytic_db,$str);
                    $str = str_replace("{{grader_db}}", $setup->grader_db,$str);                    
                                        
                    /* Main MCC DB string replaces */
                    
                    /* OTHER String replaces */
                    
                    $str = str_replace("{{admin_name}}", $setup->name, $str);	
                    $str = str_replace("{{setting_cron_key}}", md5($setup->db_name), $str);
                    $sitename = 'http://'.$setup->url;
                    if($setup->white_lbl != '')
                        $sitename = $setup->white_lbl;
                    
                    $str = str_replace("{{site_name}}", $sitename, $str);
                    $str = str_replace("{{blog_name}}", ucfirst($setup->prefix), $str);
                    $str = str_replace("{{no_replay_email}}", $setup->email, $str);
                    $str = str_replace("{{training_team_name}}", "Training Team at ".$sitename, $str);
                    $str = str_replace("{{announcements_url}}", $sitename."/announcement", $str);
                    $str = str_replace("{{parent_url}}", admin_url('admin-ajax.php'), $str); 
                    file_put_contents($dest_file, $str);
                    
                    /* Other string replaces                                         
                    $message = PHP_EOL."<?php define('DISALLOW_FILE_EDIT', true); ?>".PHP_EOL;
                    file_put_contents($dest_file, $message, FILE_APPEND); */
                }                
                else if($file_name == 'top-menu.php'){
                    
                    $str = file_get_contents(ST_COUNT_PLUGIN_DIR.'/dbs/top_menu.php');
                    file_put_contents($dest_file, $str);
                }            
                else if($file_name == '.htaccess'){
                    $str = '# BEGIN WordPress
                            <IfModule mod_rewrite.c>
                            RewriteEngine On
                            RewriteBase /
                            RewriteRule ^index\.php$ - [L]
                            RewriteCond %{REQUEST_FILENAME} !-f
                            RewriteCond %{REQUEST_FILENAME} !-d
                            RewriteRule . index.php [L]
                            </IfModule>
                            # END WordPress';                               

                    file_put_contents($dest_file, $str);
                } 
            }
        }
        else if (!$f->isDot() && $f->isDir()) {
            $file_path = $f->getRealPath();
            if(count($filters) > 0){                                 
//                pr($filters);
                if (!in_array($file_path, $filters)) {                        
                        rrcopy($file_path, "$dest/$f", $client_db, $user, $pass, $analytic_db, $filters, $setup);
                } else {   
                    // create folder only, not to copy inner content
                    $oldmask = umask(0);
                    mkdir("$dest/$f", 0777);
                    umask($oldmask);
                }                
            } 
            else {
                rrcopy($file_path, "$dest/$f", $client_db, $user, $pass, $analytic_db, $filters, $setup);
            }            
        }
    }
}






$base_url = site_url();
$results = $wpdb->get_results
(
        $wpdb->prepare
        (
                "SELECT * FROM ". setup_table() . " ORDER BY created_dt DESC ",""
        )
);
$defaulttime = -1;
if(isset($_REQUEST['timebefore']) && $_REQUEST['timebefore'] < 0 ){
    $defaulttime = $_REQUEST['timebefore'];
}

$datebk = date('Y-m-d H:i:s', strtotime(trim($defaulttime).' hours'));

?>
<div class="contaninerinner deplpage">     
    <h4>Deployment of Files     
        <div class="pull-right">
                <a class="btn btn-danger margin-bottom-10" href="admin.php?page=client_setups">Back To Setups</a>            
        </div>
    </h4>
    <div class="clearfix"></div>
    <form class="form-horizontal" name="formupdatefiles" id="formupdatefiles">
    
        <div class="panel panel-primary">

            <div class="panel-heading">Agencies</div>
            <div class="panel-body">

                <ul class="list-group">
                    <li class="list-group-item"> 
                            <div class="row">
                                <div class="col-lg-6">
                                    <label> <input type="radio" value="1" name="checkagenc" class="chkmarg chkmarkag" /> Check All </label>
                                </div>
                                <div class="col-lg-6">
                                    <label> <input type="radio" value="0" name="checkagenc" class="chkmarg chkmarkag" /> Uncheck All </label>
                                </div>
                            </div>

                    </li>
                </ul>
                <ul class="list-group">

                <?php

                foreach($results as $result){
                    $url = PROTOCOL.$result->url;
                    if(trim($result->white_lbl) != ''){
                        $url = $result->white_lbl;
                    }

                    ?>
                    <li class="list-group-item"> 
                        <label> <input type="checkbox" value="<?php echo $result->dir; ?>" name="agencchk[]" class="chkmarg agencilist" />
                         <?php                            
                            echo $result->name.' [ '.$url.' ]';
                        ?> 
                        </label>
                    </li>
                    <?php


                }            
                ?>                        
                 </ul>

            </div>

        </div>
        <div class="clearfix"></div>

        <div class="rowsel">
            <div >
                <div class="col-lg-7">
                        <h4>Select hours ( how many hours before files to be shown in panel) </h4>                    
                    </div>
                    <div class="col-lg-5">                   
                        <select name="backetime" id="backetime" >                                                
                            <?php
                            for($flg = 1; $flg <= 24; $flg++){
                                $sel = '';
                                $vl = "-$flg";
                                if( trim($defaulttime) == $vl ){
                                    $sel = 'selected="selected"';
                                }
                                ?>
                                <option <?php echo $sel; ?> value="-<?php echo $flg; ?>">-<?php echo $flg; ?> hour(s)</option>;
                                <?php
                            }
                            ?>
                        </select>
                    </div>  
            </div>              
            </div>
        <div class="clearfix"></div>
        <div class="panel panel-primary">

            <div class="panel-heading">Theme Files</div>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item"> 
                            <div class="row">
                                <div class="col-lg-6">
                                    <label> <input type="radio" value="1" name="chkmarkthm" class="chkmarg chkmarkthm" /> Check All </label>
                                </div>
                                <div class="col-lg-6">
                                    <label> <input type="radio" value="0" name="chkmarkthm" class="chkmarg chkmarkthm" /> Uncheck All </label>
                                </div>
                            </div>

                    </li>
                </ul>
                <ul class="list-group">
                    <?php
                    $path = ABSPATH.'wp-content/themes/twentytwelve';
                    $iterator = new RecursiveIteratorIterator(
                         new RecursiveDirectoryIterator($path)
                     );

                     $files = [];

                     foreach ($iterator as $fileInfo) {                
                         if ($fileInfo->isFile()) {
                             $filename =  $fileInfo->getFilename();                    
                             if (substr($filename, -1) == '~') {
                                 continue;
                             }
                             $filetime = date('Y-m-d H:i:s', $fileInfo->getMTime());
                             if($filetime >= $datebk){                                                  
                                 ?>
                                 <li class="list-group-item"> 
                                     <label> <input type="checkbox" value="<?php echo $fileInfo->getPathname(); ?>" name="themechk[]" class="chkmarg thmchkfile" />
                                      <?php                            
                                         echo $fileInfo->getPathname().' [ Modified date: '.$filetime.' ]';
                                     ?> 
                                     </label>
                                 </li>
                                 <?php

                             }
                         }
                     }

                    ?>
                </ul>
            </div>

        </div>
        <div class="clearfix"></div>
        <div class="panel panel-primary">

            <div class="panel-heading">Plugin Files</div>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item"> 
                            <div class="row">
                                <div class="col-lg-6">
                                    <label> <input type="radio" value="1" name="chkmarktpl" class="chkmarg chkmarktpl" /> Check All </label>
                                </div>
                                <div class="col-lg-6">
                                    <label> <input type="radio" value="0" name="chkmarktpl" class="chkmarg chkmarktpl" /> Uncheck All </label>
                                </div>
                            </div>

                    </li>
                </ul>
                <ul class="list-group">

                    <li class="list-group-item"> 
                        <label> <input type="checkbox" value="settingplugin" name="setting_plugin" class="chkmarg plugchk setting_plugin" />
                         Update Location Settings Plugin
                        </label>
                    </li>

                    <?php
                    $path = ABSPATH.'wp-content/plugins';
                    $iterator = new RecursiveIteratorIterator(
                         new RecursiveDirectoryIterator($path)
                     );

                     $files = [];
                     $plugins_not = array('client-setup/assets','client-setup/library','client-setup/nbproject',
                         'client-setup/views','client-setup/client-setup.php','client-setup/plugin_icon.png','client-setup/dbs/settings',
                         'client-setup/dbs/global_config.php','client-setup/dbs/mysql.php','client-setup/dbs/index_script.sql',
                         'client-setup/dbs/logo-2.png','settings','nbproject');
                     foreach ($iterator as $fileInfo) {                
                         if ($fileInfo->isFile()) {
                             $filename =  $fileInfo->getFilename();                    
                             if (substr($filename, -1) == '~') {
                                 continue;
                             }
                             $filetime = date('Y-m-d H:i:s', $fileInfo->getMTime());
                             if($filetime >= $datebk){
                                $filepth = $fileInfo->getPathname(); $fk = 0;
                                foreach($plugins_not as $plugin){                                
                                    if (strpos($filepth, $plugin) !== false) {
                                        $fk = 1;
                                    }
                                }
                                if($fk == 1){
                                    continue;
                                }

                                ?>
                                <li class="list-group-item"> 
                                    <label> <input type="checkbox" value="<?php echo $fileInfo->getPathname(); ?>" name="plugchk[]" class="chkmarg plugchk" />
                                     <?php                            
                                        echo $filepth.' [ Modified date: '.$filetime.' ]';
                                    ?> 
                                    </label>
                                </li>
                                <?php

                             }
                         }
                     }

                    ?>
                </ul>

            </div>

        </div>
        <div class="panel panel-primary">

            <div class="panel-heading">Root Files</div>
            <div class="panel-body">

                <ul class="list-group">
                    <li class="list-group-item"> 
                            <div class="row">
                                <div class="col-lg-6">
                                    <label> <input type="radio" value="1" name="chkmarktroot" class="chkmarg chkmarktroot" /> Check All </label>
                                </div>
                                <div class="col-lg-6">
                                    <label> <input type="radio" value="0" name="chkmarktroot" class="chkmarg chkmarktroot" /> Uncheck All </label>
                                </div>
                            </div>

                    </li>
                </ul>
                <ul class="list-group">

                    <?php
                    $path = ABSPATH;
                    $iterator = new RecursiveIteratorIterator(
                         new RecursiveDirectoryIterator($path), RecursiveDirectoryIterator::SKIP_DOTS
                     );

                     $files = [];                 
                     foreach ($iterator as $fileInfo) {                
                         if ($fileInfo->isFile()) {
                             $filename =  $fileInfo->getFilename();                    
                             if (substr($filename, -1) == '~' || $filename == 'global_config.php' || $filename == 'wp-config.php') {
                                 continue;
                             }
                             $filetime = date('Y-m-d H:i:s', $fileInfo->getMTime());
                             if($filetime >= $datebk){
                                $filepth = $fileInfo->getPathname(); $fk = 0;                            

                                ?>
                                <li class="list-group-item"> 
                                    <label> <input type="checkbox" value="<?php echo $fileInfo->getPathname(); ?>" name="rootchk[]" class="chkmarg rootchk" />
                                     <?php                            
                                        echo $filepth.' [ Modified date: '.$filetime.' ]';
                                    ?> 
                                    </label>
                                </li>
                                <?php

                             }
                         }
                     }

                    $path = ABSPATH.'cron';
                    $iterator = new RecursiveIteratorIterator(
                         new RecursiveDirectoryIterator($path)
                     );

                     $files = [];                 
                     foreach ($iterator as $fileInfo) {                
                         if ($fileInfo->isFile()) {
                             $filename =  $fileInfo->getFilename();                    
                             if (substr($filename, -1) == '~') {
                                 continue;
                             }
                             $filetime = date('Y-m-d H:i:s', $fileInfo->getMTime());
                             if($filetime >= $datebk){
                                $filepth = $fileInfo->getPathname(); $fk = 0;                            

                                ?>
                                <li class="list-group-item"> 
                                    <label> <input type="checkbox" value="<?php echo $fileInfo->getPathname(); ?>" name="rootchk[]" class="chkmarg rootchk" />
                                     <?php                            
                                        echo $filepth.' [ Modified date: '.$filetime.' ]';
                                    ?> 
                                    </label>
                                </li>
                                <?php

                             }
                         }
                     }


                    ?>
                </ul>

            </div>        
        </div>

    </form>
    
    <div class="pull-right">
        <a href="javascript:;" class="updateagencyfiles btn btn-primary">Update</a>
    </div>
</div>
