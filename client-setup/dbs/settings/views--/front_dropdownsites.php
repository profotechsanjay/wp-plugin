<style>
    #lc-search {
        color: #d6d6d6; 
    }
    .locationddtop {
        width: 135px;
        margin: 12px 3px 0px 6px;
        border: 0;
        padding: 3px;            
        background: whitesmoke;
    }
    .ui-autocomplete {
        min-width: 320px;       
    }  
    
    #lc-search::-webkit-input-placeholder {
        color: #d6d6d6; 
    }

    #lc-search:-moz-placeholder { /* Firefox 18- */
        color: #d6d6d6; 
    }

    #lc-search::-moz-placeholder {  /* Firefox 19+ */
    color: #d6d6d6;   
    }

    #lc-search:-ms-input-placeholder {  
        color: #d6d6d6;   
    }
    
</style>
<?php
global $wpdb;
$user_id = get_current_user_id();
$user = new WP_User($user_id);
$u_role =  $user->roles[0];    

$exiistingloc = isset($_SESSION['location'])?intval($_SESSION['location']):0;
if($exiistingloc > 0){    
    $sql = "SELECT id FROM " . client_location()." WHERE id = $exiistingloc";
    $haslocation = $wpdb->get_row($sql); 
    if(empty($haslocation)){
        unset($_SESSION['location']);
    }
}

if($u_role == 'administrator' || administrator_permission()){
        
    $sql = "SELECT * FROM " . client_location()." WHERE status = 1 ORDER By created_dt ASC";
    $locations = $wpdb->get_results($sql);    
}
else{    
    $locations = $wpdb->get_results
    (
        $wpdb->prepare
        (
            "SELECT * FROM " . client_location()." WHERE id IN(SELECT location_id FROM ".location_mapping()." WHERE"
                . " user_id  = %d) AND status = 1 ORDER By created_dt ASC", $user_id
        )
    );    

}

global $post;
$slugpost = trim($post->post_name);
$slug = trim(ST_LOC_PAGE);
$islocadded = 0;

if($slugpost == $slug){
   $islocadded = 1;
}

$vr = 0;
$urltogo = site_url().'/'.$slug.'?parm=locations';
if ($islocadded == 0) {   
    
    if (empty($locations)) {        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['msgaddlocation'] = 'Please add location to use agency setup properly.';        
        $vr = 1;
        wp_redirect($urltogo);
        exit();
    }
}

if (empty($locations)) {
    if($vr == 0){
        $_SESSION['msgaddlocation'] = 'Please add location to use agency setup properly.';        
    }
}

if (!empty($locations)) {
    unset($_SESSION['msgaddlocation']);
}

function getLocations($locations){    
    
    global $wpdb;
    $suggestions = array();
    $CURENT_ID = $_SESSION["Current_user_live"]; $locex = 0;
    foreach ($locations as $location){
        $user_id = $location->MCCUserId;
        $sel = '';
        if(isset($_SESSION["Current_user_live"]) && $_SESSION["Current_user_live"] == $user_id){
            continue;
        }        
        
        $BRand_name = get_user_meta($user_id, "BRAND_NAME",TRUE);
        $level = get_user_meta($user_id, "USER_LEVEL", true);
        $USER_LEVELS = ($level == '' ? '1' : str_replace("level_", "", $level));
        $webanem = get_user_meta($user_id, "website",TRUE);
        $suggestions[] = array(
               'label' => $BRand_name,
               'level' => 'Level - ' . $USER_LEVELS,           
               'uid' => $user_id,           
                'role' => $webanem
               //'role' => ($USER_LEVELS == 4 ? 'Assessment' : 'Client')
           );

    }    
    return $suggestions;
}


if(!empty($locations)){
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if(!isset($_SESSION['location'])){
        //set location session    
        $_SESSION['location'] = $locations[0]->id;           
        $_SESSION["Current_user_live"] = $locations[0]->MCCUserId;
        header("Refresh:0"); die;
    }
}



$CURENT_ID = $_SESSION["Current_user_live"];

$webanem = get_user_meta($CURENT_ID, "website",TRUE);
$BRand_name_User = get_user_meta($CURENT_ID, "BRAND_NAME",TRUE);
$level_User = get_user_meta($CURENT_ID, "USER_LEVEL", true);
$USER_LEVELS_User = ($level == '' ? '1' : str_replace("level_", "", $level));

$linkcll = site_url() . '/user?usider=';

wp_localize_script('mcc-custom', 'mccLocationJs', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'link' => $linkcll,
    'result' => is_user_logged_in() ? getLocations($locations) : array(),
    'label' => $BRand_name_User,
    'uid' => $CURENT_ID,
    'level' => $USER_LEVELS_User,
    'role' => $webanem
    //'role' => ($USER_LEVELS_User == 4 ? 'Assessment' : 'Client')
));

?>
    <script type="text/javascript">        
        var ajaxurl = "<?php echo admin_url("admin-ajax.php"); ?>";
            
    jQuery(document).ready(function($){
        
        
        /*Custom Code By Ruda - SearchBox*/
        $("#tour-clntSrch").validate({
            submitHandler: function () {
                if ($("#lc-search").val() == "") {
                    return false;
                }else{
                       // $("#tour-clntSrch").attr("action","<?php echo site_url(); ?>/user/");
                        flag=0;
			$.each(mccLocationJs.result,function(i,item){
                           console.log("Label Value : "+item.label+" and search Value : "+$("#lc-search").val());
			   if(item.label.toLowerCase()==$("#lc-search").val().toLowerCase()){
			     flag=1;
                             $("#lc-search").val(item.label);
			   }else{}
			});
			if(flag){
			    //window.location.href="<?php echo site_url(); ?>/user/?cname="+$("#lc-search").val();
			}else{
			   $("#lc-search").val("");$('#lc-search').focus();return false;
			}
                }
            }
        });/*Code Ends - Rudra*/
	 


        var autoComplet = $("#lc-search").on('click focus',function(){

            $(this).autocomplete('search', $(this).val());

        }).autocomplete({

            minLength: 0,

            select: function( event, ui ) {

                $( "#lc-search" ).val( ui.item.label );                
                window.location.href = "<?php echo $linkcll; ?>"+ui.item.uid+"&us=qw&search=1";                
                return false;

            },

            source: mccLocationJs.result


        }).autocomplete("instance");

        autoComplet._renderItem = function( ul, item ){

            return $( "<li>" )

                .append( "<a href=\""+mccLocationJs.link+item.uid+"&us=qw&search=1\">" + item.label + "<br><small>- "+item.role+ "</small></a>" )

                .appendTo( ul );

        };

        autoComplet._resizeMenu = function(){

            this.menu.element.outerWidth( 250 );

        };

        autoComplet._create = function(){

            this._super();

            this.widget().menu( "option", "items", "> :not(.mCat1,.mCat2)" );

        };

        autoComplet._renderMenu = function( ul, items ){

            var that = this;

            $.each( items, function( index, item ){

                if( index==0 ){

                    ul.append( "<li class='ui-autocomplete-category mCat1'>&mdash;&mdash; Current Location</li>" );

                    that._renderItemData( ul, mccLocationJs );

                    ul.append( "<li class='ui-autocomplete-category mCat2'>&mdash;&mdash; Other Locations</li>" );

                }

                that._renderItemData( ul, item );

            });

            $(ul).find("li:odd").addClass("odd");

            $(ul).children('li').eq(1).addClass("mHd");

        };



        $('#en-submit').click(function(e){

            e.preventDefault();

            return false;

        });
        
        if(jQuery("#hidnewlocpage").length == 0){
            var hssess = "<?php echo isset($_SESSION['msgaddlocation'])?$_SESSION['msgaddlocation']:''; ?>";
            hssess = jQuery.trim(hssess);
            if(hssess != ''){
                show_msg_time(0,hssess,10000);
            }
        }
      
    });            
        
        
    </script>
