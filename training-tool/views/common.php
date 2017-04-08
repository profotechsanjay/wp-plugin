
<?php $ValueType = isset($_REQUEST['type'])?$_REQUEST['type']:""; if ($ValueType=="iframe") { ?>
    <style>
        #wpcontent #wpadminbar{display:none;}
        #wpwrap #adminmenuwrap{display:none;}
        #wpwrap #adminmenuback{display:none;}
        #wpbody-content #screen-meta{display: none !important;}
        #wpbody-content #contextual-help-wrap{display: none !important;}
        #wpbody-content .update-nag{display: none !important;}
        #wpbody-content #message{display: none !important;}
        body .wp-admin{background:none !important;}
        #wpwrap{background:white;}
        html.wp-toolbar{padding:0;}
        #wpcontent{padding-left:0;}
        html{background:none !important;}
        #wpcontent, #wpfooter{margin-left:0;}
        #wpfooter{display:none;}
    </style>
<?php }else{  ?>

<script type="text/javascript">
     if(window.name=="iframe_type"){
		       $("#wpadminbar").css({"display":"none"});
		       $("#adminmenuwrap").css({"display":"none"});
		       $("#adminmenuback").css({"display":"none"});
		       $("#screen-meta").css({"display":"none"});
		       $("#contextual-help-wrap").css({"display":"none"});
		       $(".update-nag").css({"display":"none"});
		       $("#message").css({"display":"none"});
		       $("body.wp-admin").css({"background-color":"none"});
		       $("#wpwrap").css({"background-color":"white"});
		       $("html.wp-toolbar").css({"padding":"0"});
		       $("#wpcontent").css({"padding-left":"0","margin-left":"0"});
		       $("html").css({"background-color":"none"});
		       $("#wpfooter").css({"display":"none"});
      } 
</script>
<script type="text/javascript">
    jQuery(function(){
        $(".iframeType").css({"display":"none"});
        if(window.name=="iframe_type"){
            $(".webType").css({"display":"none"});$(".iframeType").css({"display":"block"});
        }
    });
</script>
<?php } ?>
<style>
   #wpfooter span#footer-thankyou{display:none;}
</style>
<?php
global $wpdb;
if(is_admin()){
    include_once 'menus.php';
}
$validlicence = $wpdb->get_var
        (
        $wpdb->prepare
                (
                "SELECT keyvalue FROM " . setting()." WHERE keyname = 'valid_licence'", ""
        )
);
$chklicence = 0;

$validlicence = json_decode($validlicence);


if(!isset($validlicence->sts) || $validlicence->sts == 'n'){
    $chklicence = 1;
    function sample_license_management() {
        
        echo '<div class="wrap">';
        echo '<h2>License Management</h2>';

        /*** License activate button was clicked ***/
        if (isset($_REQUEST['activate_license'])) {
            $license_key = $_REQUEST['sample_license_key'];

            // API query parameters
            $api_params = array(
                'slm_action' => 'slm_activate',
                'secret_key' => ACME_PLUGIN_SECRET_KEY,
                'license_key' => $license_key,
                'registered_domain' => $_SERVER['SERVER_NAME'],
                'item_reference' => urlencode(ACME_PLUGIN_ITEM_REFERENCE),
            );
            
            // Send query to the license manager server
            $query = esc_url_raw(add_query_arg($api_params, ACME_PLUGIN_LICENSE_SERVER_URL));
            $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

            // Check for error in the response
            if (is_wp_error($response)){
                echo "Unexpected Error! The query returned with an error.";
            }

            //var_dump($response);//uncomment it if you want to look at the full response

            // License data.
            $license_data = json_decode(wp_remote_retrieve_body($response));

            // TODO - Do something with it.
            //var_dump($license_data);//uncomment it to look at the data
//print_r($license_data); die;
            if($license_data->result == 'success'){//Success was returned for the license activation

                //Uncomment the followng line to see the message that returned from the license server
                echo '<br />The following message was returned from the server: '.$license_data->message;
                
                global $wpdb;
                $arr = json_encode(array('key'=>$license_key,'sts'=>'y'));
                
                $wpdb->query
                            (
                            $wpdb->prepare
                                    (
                                    "UPDATE " . setting() . " SET keyvalue = %s WHERE keyname = %s",
                                    $arr,
                                    'valid_licence'
                            )
                    );                
                
                //Save the license key in the options table
                update_option('sample_license_key', $license_key); 
                
                //header("Location: ".site_url());
            }
            else{
                //Show error to the user. Probably entered incorrect license key.

                //Uncomment the followng line to see the message that returned from the license server
                echo '<br />The following message was returned from the server: '.$license_data->message;
            }

        }
        /*** End of license activation ***/

        /*** License activate button was clicked ***/
        if (isset($_REQUEST['deactivate_license'])) {
            $license_key = $_REQUEST['sample_license_key'];

            // API query parameters
            $api_params = array(
                'slm_action' => 'slm_deactivate',
                'secret_key' => YOUR_SPECIAL_SECRET_KEY,
                'license_key' => $license_key,
                'registered_domain' => $_SERVER['SERVER_NAME'],
                'item_reference' => urlencode(YOUR_ITEM_REFERENCE),
            );

            // Send query to the license manager server
            $query = esc_url_raw(add_query_arg($api_params, YOUR_LICENSE_SERVER_URL));
            $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

            // Check for error in the response
            if (is_wp_error($response)){
                echo "Unexpected Error! The query returned with an error.";
            }

            //var_dump($response);//uncomment it if you want to look at the full response

            // License data.
            $license_data = json_decode(wp_remote_retrieve_body($response));

            // TODO - Do something with it.
            //var_dump($license_data);//uncomment it to look at the data

            
            
            if($license_data->result == 'success'){//Success was returned for the license activation

                //Uncomment the followng line to see the message that returned from the license server
                echo '<br />The following message was returned from the server: '.$license_data->message;                                                
                
                //Remove the licensse key from the options table. It will need to be activated again.
                update_option('sample_license_key', '');
                
                                
            }
            else{
                //Show error to the user. Probably entered incorrect license key.

                //Uncomment the followng line to see the message that returned from the license server
                echo '<br />The following message was returned from the server: '.$license_data->message;
            }

        }
        
        /*** End of sample license deactivation ***/

        ?>
        <p>Please enter the license key for this product to activate it. You were given a license key when you purchased this item.</p>
        <form action="" method="post">
            <table class="form-table">
                <tr>
                    <th style="width:100px;"><label for="sample_license_key">License Key</label></th>
                    <td ><input class="regular-text" type="text" id="sample_license_key" name="sample_license_key"  value="<?php echo get_option('sample_license_key'); ?>" ></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="activate_license" value="Activate" class="button-primary" />
                <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
            </p>
        </form>
        <?php

        echo '</div>';
    }
    if(is_admin()){
        sample_license_management();
        die;
    }
    else{
        ?>
        
        <div class="col-lg-12">
            <div>No rights to access page</div>
        </div>
        
        <?php
        die;
    }
}

?>
<div class="msg"><div class="messdv"></div></div>

<?php if(is_admin()) { ?>
<div id="reordermodal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title reordertitl">Re-Order </h4>
            </div>
            <div class="modal-body">                
                <form action="#" method="post" id="reorderrows" name="reorderrows" class="form-horizontal">
                    <div class="loadergif">
                        <img src="<?php echo TR_COUNT_PLUGIN_URL; ?>/assets/css/images/loading.gif" />
                    </div>                
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary reordersave" >Save</button>                
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>


<?php
}

function sortByOrder($a, $b) {
    return $a['order'] - $b['order'];
}

function limit_text($text, $limit, $more = true) {
    
    $txt = '<button class="more_ifo moreinfo">more</button>';
    $text = html_entity_decode(stripslashes($text));    
    $text = TruncateHTML::truncateWords($text, $limit, $ellipsis = '...', $txt, $more);    
    if($more == false){
        $text = strip_tags($text);
    }   
      
    return $text;
}


function limit_text1($text, $limit, $more = true) {
    $txt = '';
    $text = html_entity_decode(stripslashes($text));
    
      if (str_word_count($text, 0) > $limit) {
          $words = str_word_count($text, 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]) . '... ';
          if($more == true)
            $txt = '<button class="more_ifo moreinfo">more</button>';
      }
      if($more == true){          
        $text = str_replace(array('<div>', '</div>', '<p>', '</p>'), array('<span>', '</span>', '', ''), $text);        
      }
      else{
          $text = strip_tags($text);
      }      
      return $text .$txt;
    }
    
function full_text($text) {   
      $text = stripslashes($text);
      $txt = '...<button class="more_ifo lessinfo">less</button>';
      $text = str_replace(array('<div>', '</div>'), array('<span>', '</span>'), $text);
      return $text .$txt;
    }
    
    
function get_project_links($resource_id){
       
    global $wpdb;
    global $current_user;
    $user_id = $current_user->ID;            
    $proj_links = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT links FROM " . projects() . " WHERE resource_id = %d AND user_id = %d", 
                        $resource_id,
                        $user_id
                )
            ); 
    $links = '';
    if(!empty($proj_links)){
    
        $projlinks = explode(",", $proj_links->links);
        if(!empty($projlinks)){
            foreach($projlinks as $link){
                $links .= "<a target='_blank' href='$link'>$link</a> <br/>";
            }
        }
    
    }      
    
    return $links;
    
}

function get_project_links_back($user_id,$resource_id){
       
    global $wpdb;          
    $proj_links = $wpdb->get_row(
                $wpdb->prepare
                        (
                        "SELECT links FROM " . projects() . " WHERE resource_id = %d AND user_id = %d", 
                        $resource_id,
                        $user_id
                )
            ); 
    $links = '';
    if(!empty($proj_links)){
    
        $projlinks = explode(",", $proj_links->links);
        if(!empty($projlinks)){
            foreach($projlinks as $link){
                $links .= "<a target='_blank' href='$link'>$link</a> <br/>";
            }
        }
    
    }      
    
    return $links;
    
}
    
function get_mentor($user_id,$course_id){
    
    global $wpdb;    
    $usertbl = $wpdb->prefix."users";
    $mentor = $wpdb->get_row
    (
        $wpdb->prepare
                (
                "SELECT map.*,m.display_name,m.user_email "
                . "FROM " . mentor_assign()." map INNER JOIN " . $usertbl ." m ON map.mentor_id = m.ID "
                . "WHERE map.user_id = %d AND map.course_id = %d",$user_id,$course_id
        )
    );    
    return $mentor; 
}

function get_next_calldate($user_id,$course_id,$mentor_id){       
    global $wpdb;
    $now = date("Y-m-d H:i:s");           
    $mentorcal = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT id,mentor_call FROM " . mentorcall()." WHERE course_id = %d AND user_id = %d AND mentor_id = %d AND status = 'active' "
                    . "AND mentor_call >= '%s' ORDER BY mentor_call ASC, created_dt DESC",
                    $course_id, $user_id, $mentor_id, $now
            )
    );    
    $next_call_date = "<i>Not Found</i>";     
    if(!empty($mentorcal)){
        $next_call_date = "<a href='admin.php?page=call_detail&mentor_call_id=$mentorcal->id'>".date("D d M Y, h:i a",  strtotime($mentorcal->mentor_call))."</a>";
    }
    return $next_call_date;
}

function get_submissions($user_id,$course_id){
    global $wpdb;
    
    $submits = $wpdb->get_results
    (
        $wpdb->prepare
                (
                "SELECT p.*,r.title as exercise_title,r.course_id,r.module_id,r.lesson_id,r.total_hrs,"
                . "l.title as lesson_title,m.title as mod_title FROM " . projects()." p "
                . " INNER JOIN " . resources() ." r ON p.resource_id = r.id INNER JOIN " . lessons() ." l ON r.lesson_id = l.id"
                . " INNER JOIN " . modules() ." m ON r.module_id = m.id "
                . "WHERE p.user_id = %d AND r.course_id = %d ",$user_id,$course_id
        )
    );
   
    
   return $submits;
}

function get_project($type,$course_id,$chk = ''){
    
    return '';
    die;
    global $wpdb;
    global $current_user;
    $user_id = $current_user->ID;
    $col = 'course_id';
    if($type == 'module')
        $col = 'module_id';
    
    $current_user = wp_get_current_user();
    $exercise = $wpdb->get_row(
        $wpdb->prepare
                (
                "SELECT id,title,description,total_hrs,created_by,created_dt FROM " . project_exercise() . " "
                . "WHERE $col = %d AND type = %s AND status = 1", 
                $course_id,
                $type
        )
    );
    
    if($chk == 'check'){
        if(!empty($exercise)){      
            $proj_links = $wpdb->get_row(
                    $wpdb->prepare
                            (
                            "SELECT links FROM " . projects() . " WHERE exercise_id = %d AND user_id = %d", 
                            $exercise->id,
                            $user_id
                    )
                ); 
            $exercise = (object) array_merge((array) $exercise,(array) $proj_links);
        }
        
    }
    
    return $exercise;
}
    





class TruncateHTML {
    
    public static function truncateChars($html, $limit, $ellipsis = '...') {
        
        if($limit <= 0 || $limit >= strlen(strip_tags($html)))
            return $html;
        
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        
        $body = $dom->getElementsByTagName("body")->item(0);
        
        $it = new DOMLettersIterator($body);
        
        foreach($it as $letter) {
            if($it->key() >= $limit) {
                $currentText = $it->currentTextPosition();
                $currentText[0]->nodeValue = substr($currentText[0]->nodeValue, 0, $currentText[1] + 1);
                self::removeProceedingNodes($currentText[0], $body);
                self::insertEllipsis($currentText[0], $ellipsis);
                break;
            }
        }
        
        return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML());
    }
    
    public static function truncateWords($html, $limit, $ellipsis = '...', $txt, $more) {
        
        if($limit <= 0 || $limit >= self::countWords(strip_tags($html)))
            return $html;
        
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        
        $body = $dom->getElementsByTagName("body")->item(0);
        
        $it = new DOMWordsIterator($body);
        
        foreach($it as $word) {            
            if($it->key() >= $limit) {
                $currentWordPosition = $it->currentWordPosition();
                $curNode = $currentWordPosition[0];
                $offset = $currentWordPosition[1];
                $words = $currentWordPosition[2];
                
                $curNode->nodeValue = substr($curNode->nodeValue, 0, $words[$offset][1] + strlen($words[$offset][0]));
                
                self::removeProceedingNodes($curNode, $body);
                self::insertEllipsis($curNode, $ellipsis);
                break;
            }
        }
        if($more == FALSE)
            $txt = '';
        
        return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML()).$txt;
    }
    
    private static function removeProceedingNodes(DOMNode $domNode, DOMNode $topNode) {        
        $nextNode = $domNode->nextSibling;
        
        if($nextNode !== NULL) {
            self::removeProceedingNodes($nextNode, $topNode);
            $domNode->parentNode->removeChild($nextNode);
        } else {
            //scan upwards till we find a sibling
            $curNode = $domNode->parentNode;
            while($curNode !== $topNode) {
                if($curNode->nextSibling !== NULL) {
                    $curNode = $curNode->nextSibling;
                    self::removeProceedingNodes($curNode, $topNode);
                    $curNode->parentNode->removeChild($curNode);
                    break;
                }
                $curNode = $curNode->parentNode;
            }
        }
    }
    
    private static function insertEllipsis(DOMNode $domNode, $ellipsis) {    
        $avoid = array('a', 'strong', 'em', 'h1', 'h2', 'h3', 'h4', 'h5'); //html tags to avoid appending the ellipsis to
        
        if( in_array($domNode->parentNode->nodeName, $avoid) && $domNode->parentNode->parentNode !== NULL) {
            // Append as text node to parent instead
            $textNode = new DOMText($ellipsis);
            
            if($domNode->parentNode->parentNode->nextSibling)
                $domNode->parentNode->parentNode->insertBefore($textNode, $domNode->parentNode->parentNode->nextSibling);
            else
                $domNode->parentNode->parentNode->appendChild($textNode);
        } else {
            // Append to current node
            $domNode->nodeValue = rtrim($domNode->nodeValue).$ellipsis;
        }
    }
    
    private static function countWords($text) {
        $words = preg_split("/[\n\r\t ]+/", $text, -1, PREG_SPLIT_NO_EMPTY);
        return count($words);
    }
    
}

final class DOMWordsIterator implements Iterator {
    
    private $start, $current;
    private $offset, $key, $words;

    /**
     * expects DOMElement or DOMDocument (see DOMDocument::load and DOMDocument::loadHTML)
     */
    function __construct(DOMNode $el)
    {
        if ($el instanceof DOMDocument) $this->start = $el->documentElement;
        else if ($el instanceof DOMElement) $this->start = $el;
        else throw new InvalidArgumentException("Invalid arguments, expected DOMElement or DOMDocument");
    }
    
    /**
     * Returns position in text as DOMText node and character offset.
     * (it's NOT a byte offset, you must use mb_substr() or similar to use this offset properly).
     * node may be NULL if iterator has finished.
     *
     * @return array
     */
    function currentWordPosition()
    {
        return array($this->current, $this->offset, $this->words);
    }

    /**
     * Returns DOMElement that is currently being iterated or NULL if iterator has finished.
     *
     * @return DOMElement
     */
    function currentElement()
    {
        return $this->current ? $this->current->parentNode : NULL;
    }
    
    // Implementation of Iterator interface
    function key()
    {
        return $this->key;
    }
    
    function next()
    {
        if (!$this->current) return;

        if ($this->current->nodeType == XML_TEXT_NODE || $this->current->nodeType == XML_CDATA_SECTION_NODE)
        {
            if ($this->offset == -1)
            {
                $this->words = preg_split("/[\n\r\t ]+/", $this->current->textContent, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
            }
            $this->offset++;
            
            if ($this->offset < count($this->words)) { 
                $this->key++;
                return;
            }
            $this->offset = -1;
        }

        while($this->current->nodeType == XML_ELEMENT_NODE && $this->current->firstChild)
        {
            $this->current = $this->current->firstChild;
            if ($this->current->nodeType == XML_TEXT_NODE || $this->current->nodeType == XML_CDATA_SECTION_NODE) return $this->next();
        }

        while(!$this->current->nextSibling && $this->current->parentNode)
        {
            $this->current = $this->current->parentNode;
            if ($this->current === $this->start) {$this->current = NULL; return;}
        }

        $this->current = $this->current->nextSibling;

        return $this->next();
    }

    function current()
    {
        if ($this->current) return $this->words[$this->offset][0];
        return NULL;
    }

    function valid()
    {
        return !!$this->current;
    }

    function rewind()
    {
        $this->offset = -1; $this->words = array();
        $this->current = $this->start;
        $this->next();
    }
}

final class DOMLettersIterator implements Iterator
{
    private $start, $current;
    private $offset, $key, $letters;

    /**
     * expects DOMElement or DOMDocument (see DOMDocument::load and DOMDocument::loadHTML)
     */
    function __construct(DOMNode $el)
    {
        if ($el instanceof DOMDocument) $this->start = $el->documentElement;
        else if ($el instanceof DOMElement) $this->start = $el;
        else throw new InvalidArgumentException("Invalid arguments, expected DOMElement or DOMDocument");
    }

    /**
     * Returns position in text as DOMText node and character offset.
     * (it's NOT a byte offset, you must use mb_substr() or similar to use this offset properly).
     * node may be NULL if iterator has finished.
     *
     * @return array
     */
    function currentTextPosition()
    {
        return array($this->current, $this->offset);
    }

    /**
     * Returns DOMElement that is currently being iterated or NULL if iterator has finished.
     *
     * @return DOMElement
     */
    function currentElement()
    {
        return $this->current ? $this->current->parentNode : NULL;
    }

    // Implementation of Iterator interface
    function key()
    {
        return $this->key;
    }

    function next()
    {
        if (!$this->current) return;

        if ($this->current->nodeType == XML_TEXT_NODE || $this->current->nodeType == XML_CDATA_SECTION_NODE)
        {
            if ($this->offset == -1)
            {
                // fastest way to get individual Unicode chars and does not require mb_* functions
                preg_match_all('/./us',$this->current->textContent,$m); $this->letters = $m[0];
            }
            $this->offset++; $this->key++;
            if ($this->offset < count($this->letters)) return;
            $this->offset = -1;
        }

        while($this->current->nodeType == XML_ELEMENT_NODE && $this->current->firstChild)
        {
            $this->current = $this->current->firstChild;
            if ($this->current->nodeType == XML_TEXT_NODE || $this->current->nodeType == XML_CDATA_SECTION_NODE) return $this->next();
        }

        while(!$this->current->nextSibling && $this->current->parentNode)
        {
            $this->current = $this->current->parentNode;
            if ($this->current === $this->start) {$this->current = NULL; return;}
        }

        $this->current = $this->current->nextSibling;

        return $this->next();
    }

    function current()
    {
        if ($this->current) return $this->letters[$this->offset];
        return NULL;
    }

    function valid()
    {
        return !!$this->current;
    }

    function rewind()
    {
        $this->offset = -1; $this->letters = array();
        $this->current = $this->start;
        $this->next();
    }
}



if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){
    ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
}
else{

        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo str_replace(array('https','HTTPS'), array('http','HTTP'), admin_url('admin-ajax.php')); ?>';
        </script>
        <?php

}

?>
<style>
    .insert-media.add_media{
        display: none; visibility: hidden;
    }
</style>


