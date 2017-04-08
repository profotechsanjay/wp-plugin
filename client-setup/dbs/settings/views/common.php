
<?php
global $wpdb;
function loc_stylesheets(){
    wp_enqueue_script('jquery');	                        
    wp_enqueue_script('jquery-ui.min.js', SET_COUNT_PLUGIN_URL .'/assets/js/jquery-ui.min.js');

    wp_enqueue_script('jquery.datetimepicker.full.js', SET_COUNT_PLUGIN_URL .'/assets/js/jquery.datetimepicker.full.js');                

    wp_enqueue_script('bootstrap.js', SET_COUNT_PLUGIN_URL .'/assets/js/bootstrap.js');
    wp_enqueue_script('jquery.visible.min.js', SET_COUNT_PLUGIN_URL .'/assets/js/jquery.visible.min.js');

    wp_enqueue_script('jquery.validate.js', SET_COUNT_PLUGIN_URL .'/assets/js/jquery.validate.js');

    wp_enqueue_script('jquery.dataTables.js', SET_COUNT_PLUGIN_URL .'/assets/js/jquery.dataTables.js');

    wp_enqueue_script('chosen.jquery.js', SET_COUNT_PLUGIN_URL .'/assets/js/chosen.jquery.js?ver=','', SET_VERSION);
    wp_enqueue_script('script.js', SET_COUNT_PLUGIN_URL .'/assets/js/script.js?ver=','', SET_VERSION);


    // style        


    wp_enqueue_style('style.css', SET_COUNT_PLUGIN_URL .'/assets/css/style.css','', SET_VERSION);    
    wp_enqueue_style('jquery.datetimepicker.css', SET_COUNT_PLUGIN_URL .'/assets/css/jquery.datetimepicker.css');    
    wp_enqueue_style('jquery.dataTables.css', SET_COUNT_PLUGIN_URL .'/assets/css/jquery.dataTables.css');                  
    wp_enqueue_style('chosen.css', SET_COUNT_PLUGIN_URL .'/assets/css/chosen.css');                      
}

loc_stylesheets();
include_once 'menus.php';
include_once ABSPATH . "wp-content/plugins/settings/get_location_package_prices.php";
function time_elapsed_string($ptime)
{
    $ptime = strtotime($ptime);
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return '0 seconds';
    }

    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    $a_plural = array( 'year'   => 'years',
                       'month'  => 'months',
                       'day'    => 'days',
                       'hour'   => 'hours',
                       'minute' => 'minutes',
                       'second' => 'seconds'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}

function user_location_add_email($location_id,$user_id){
    global $wpdb;
    $usertabl = $wpdb->prefix."users";    
    $date = date("D d M Y, h:i a");
    $location = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT * FROM ". client_location() . " WHERE id = %d",
            $location_id
        )
    );
    
    $location_name = get_user_meta($location->MCCUserId,'website',true);
    $site_name = get_option( 'blogname' );  
    $admin_email = get_option( 'admin_email' );
    $headers = 'From: ' . $admin_email . "\r\n" .
                'Reply-To: ' . $admin_email . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();        
        
    /* Email for permissions granted */
    if($user_id > 0){
               
        $uuser = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT display_name,user_email FROM ". $usertabl . " WHERE id = %d",$user_id
            )
        );
        if(!empty($uuser)){
            $email = $uuser->user_email;
            
            $template = '<div>Hi {{username}},</div>
                        <div></div>
                        <div>
                        <div>You have added in new location {{location_name}}.</div>
                        <div>Login your account to know more.</div>
                        <div></div>
                        Thanks,
                        {{site_name}}

                        </div>';            
            $subj = 'New Location assigned';             
                  
            $msg = $template; 
            $msg = str_replace(array('{{username}}','{{location_name}}','{{site_name}}'),
                    array($uuser->display_name,$location_name,$site_name), $msg);
                 
            /****  Code commented ***/
            //custom_mail($email,$subj,$msg,EMAIL_TYPE,"");
        }
    }
}

function custom_mail($user_email,$setup_sub,$body,$email_type,$reason){        
    $email_template_body = email_template_body($body, $user_email, $email_type);
    @mail($user_email, $setup_sub, $email_template_body, custom_mail_header(), mail_additional_parameters());
    insert_email_historical_report(user_id(), $email_type, $setup_sub, $user_email, $reason, current_id());    
}

function get_client_account_info(){
    global $wpdb;
    $url = SET_PARENT_URL;
    $db_name = DB_NAME;
    $token = md5($db_name.time().$db_name);
    $wpdb->query
    (
        $wpdb->prepare
        (
                "INSERT INTO super_tokens (token) "
                . "VALUES (%s)", 
                $token
        )
    );    
    $params = array();
    $params['param'] = 'get_client_info';
    $params['db_name'] = $db_name;
    $params['token'] = $token;
    $res = parent_api_call($url,$params);
    delete_token($token);
    $res = json_decode($res);    
    if($res->sts == 1){
        $res = $res->arr;
        return $res;
    }
    else{        
        ?>
        <div class="update-nag"> Status Code : <?php echo $res->sts; ?> <br/> Message : <?php echo $res->msg; ?></div>
        <?php
        die;
    }
    
}

function delete_token($token){
    global $wpdb;
    $wpdb->query
    (
        $wpdb->prepare
        (
                "DELETE FROM super_tokens WHERE token = %s", 
                $token
        )
    );
}

function parent_api_call($url,$params = array()){
    
    $params['action'] = 'rudra_api';
    
    foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
          $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_string);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec ($ch);
    curl_close ($ch);
    return $output;
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

function full_text($text) {   
      $text = stripslashes($text);
      $txt = '...<button class="more_ifo lessinfo">less</button>';
      $text = str_replace(array('<div>', '</div>'), array('<span>', '</span>'), $text);
      return $text .$txt;
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

//pr($locations_package_prices);
?>
<div class="patientmsg" style="display: none;">Setup is under processing. Please be patient.....</div>
<div class="msg"><div class="messdv"></div></div>

