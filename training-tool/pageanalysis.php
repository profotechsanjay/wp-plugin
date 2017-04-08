<?php
global $wpdb;
define('PAGE_TITLE_RANGE', '60 - 70');
define('KEYWORD_TITLE_DENSITY', '2 - 3'); // 2% - 3%

define('PAGE_DESC_RANGE', '150 - 160');
define('KEYWORD_DESC_DENSITY', '2 - 3'); //  2% - 3%

define('HEADING_LENGTH', '1 - 50');
define('PAGE_CONTENT_RANGE', '500');
define('KEYWORD_CONTENT_DENSITY', '2 - 3'); // 2% - 3%

define('EXTRANL_LINKS', '1 - 6');
define('AVG_PAGE_SIZE', '1 - 1048576'); // max 1 mb page size
define('AVG_LOADING_TIME', '0 - 2'); // 0 to 2 seconds
define('TITLE_RELEVANCY', 1); // min 1 %
define('DESC_RELEVANCY', 1); // min 1 %
define('TEXT_RATIO', "10 - 20"); // 10 to 20 %    

define('IDCRE', 0);
define('OVERALL_KEY_DENSITY', 3); // greater than equal to 3% - 
define('OVERALL_PRIMARY_DENSITY', 5); // greater than equal to 5%
define('MAX_HEADING_TAGS', 30); // 30 H tags max
define('MAX_H1_TAGS', 1); // 1 H1 tags max

function getLongTailKeywords($str, $len = 3, $min = 2) {
    $keywords = array();
    $str = str_replace("|", " | ", $str);
    //$common = array('i', 'a', 'about', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'com', 'de', 'en', 'for', 'from', 'how', 'in', 'is', 'it', 'la', 'of', 'on', 'or', 'that', 'the', 'this', 'to', 'was', 'what', 'when', 'where', 'who', 'will', 'with', 'und', 'the', 'www');
    $common = array('a','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','e','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','t','take','taken','taking','tell','tends','the','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','www','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero');
    $str = preg_replace('/[^a-z0-9\s-]+/', '', strtolower(strip_tags($str)));
    $str = preg_split('/\s+-\s+|\s+/', $str, -1, PREG_SPLIT_NO_EMPTY);    
    while (0 < $len--)
        for ($i = 0; $i < count($str) - $len; $i++) {
            $word = array_slice($str, $i, $len + 1);
            if (in_array($word[0], $common) || in_array(end($word), $common))
                continue;
            $word = implode(' ', $word);
            if (!isset($keywords[$len][$word]))
                $keywords[$len][$word] = 0;
            $keywords[$len][$word] ++;
        }
    $return = array();
    foreach ($keywords as &$keyword) {
        $keyword = array_filter($keyword, function($v) use($min) {
            return !!($v > $min);
        });        
        arsort($keyword);                
        $return = array_merge($return, $keyword);
    }
    return $return;
}

function cleanString($text) {
    // 1) convert á ô => a o
    $text = preg_replace("/[áàâãªä]/u","a",$text);
    $text = preg_replace("/[ÁÀÂÃÄ]/u","A",$text);
    $text = preg_replace("/[ÍÌÎÏ]/u","I",$text);
    $text = preg_replace("/[íìîï]/u","i",$text);
    $text = preg_replace("/[éèêë]/u","e",$text);
    $text = preg_replace("/[ÉÈÊË]/u","E",$text);
    $text = preg_replace("/[óòôõºö]/u","o",$text);
    $text = preg_replace("/[ÓÒÔÕÖ]/u","O",$text);
    $text = preg_replace("/[úùûü]/u","u",$text);
    $text = preg_replace("/[ÚÙÛÜ]/u","U",$text);
    $text = preg_replace("/[’‘‹›‚]/u","'",$text);
    $text = preg_replace("/[“”«»„]/u",'"',$text);
    $text = str_replace("–","-",$text);
    $text = str_replace(" "," ",$text);
    $text = str_replace("ç","c",$text);
    $text = str_replace("Ç","C",$text);
    $text = str_replace("ñ","n",$text);
    $text = str_replace("Ñ","N",$text);
 
    //2) Translation CP1252. &ndash; => -
    $trans = get_html_translation_table(HTML_ENTITIES); 
    $trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark 
    $trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook 
    $trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark 
    $trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis 
    $trans[chr(134)] = '&dagger;';    // Dagger 
    $trans[chr(135)] = '&Dagger;';    // Double Dagger 
    $trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent 
    $trans[chr(137)] = '&permil;';    // Per Mille Sign 
    $trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron 
    $trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark 
    $trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE 
    $trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark 
    $trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark 
    $trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark 
    $trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark 
    $trans[chr(149)] = '&bull;';    // Bullet 
    $trans[chr(150)] = '&ndash;';    // En Dash 
    $trans[chr(151)] = '&mdash;';    // Em Dash 
    $trans[chr(152)] = '&tilde;';    // Small Tilde 
    $trans[chr(153)] = '&trade;';    // Trade Mark Sign 
    $trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron 
    $trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark 
    $trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE 
    $trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis 
    $trans['euro'] = '&euro;';    // euro currency symbol 
    ksort($trans); 
     
    foreach ($trans as $k => $v) {
        $text = str_replace($v, $k, $text);
    }
 
    // 3) remove <p>, <br/> ...
    $text = strip_tags($text); 
     
    // 4) &amp; => & &quot; => '
    $text = html_entity_decode($text);
     
    // 5) remove Windows-1252 symbols like "TradeMark", "Euro"...
    $text = preg_replace('/[^(\x20-\x7F)]*/','', $text); 
     
    $targets=array('\r\n','\n','\r','\t');
    $results=array(" "," "," ","");
    $text = str_replace($targets,$results,$text);
 
    //XML compatible
    /*
    $text = str_replace("&", "and", $text);
    $text = str_replace("<", ".", $text);
    $text = str_replace(">", ".", $text);
    $text = str_replace("\\", "-", $text);
    $text = str_replace("/", "-", $text);
    */
     
    return ($text);
} 

function create_keywords($str) {
    
    $ar = array();
    $words = explode(" ",$str);
    $num_words = count($words);
    for ($i = 0; $i < $num_words; $i++) {
      for ($j = $i; $j < $num_words; $j++) {
        $st = '';
        for ($k = $i; $k <= $j; $k++) {
           $st .= trim(trim($words[$k]," "),"|") . " ";
        }
        if(trim($st) != ''){
            $ar[] = $st;
        }
      }
    }
    $ar = array_unique($ar);
    return $ar;
}

function getcontentdata($ar, $KEYWORD_CONTENT_DENSITY, $bodaydata, $no=3){
    
    $newar = array();
    foreach($ar as $key => $a){
        
        $kword = $key; $synonymof = '';
        if($no == 0){
            $kword = isset($a->keyword)?$a->keyword:$a;
            $synonymof = isset($a->synonymof)?$a->synonymof:'';
        }
            
        if($no == 0){
            $total = $no;
        }
        else{
            $total = str_word_count($kword);
        }
        
        if($total == $no){
                                    
            $resocuur = fnd_pos(strtolower($kword),strtolower($bodaydata)); 
            $Nkr = count($resocuur);
            $Tkn = str_word_count(strtolower($bodaydata));        
            $Density = ($Nkr / $Tkn) * 100;

            $rang = explode("-", $KEYWORD_CONTENT_DENSITY);
            $min = isset($rang[0])?trim($rang[0]):2; // %
            $max = isset($rang[1])?trim($rang[1]):3; // %
            
            $add_total_issue = 0; $add_content_issue = 0;            
            if($Density < $min || $Density > $max){
                $add_total_issue = 1; $add_content_issue = 1;
            }            
            $density = round($Density,2).'%';
            
            $occ = $a; $hastargt = 0; $type = 'discovered';
            if($no == 0){
                $occ = $Nkr;
                $hastargt = 1;  
                $type = $a->type;
            }                        
          
            $newar["$kword"] = array('content' => array('occurence' => $occ, 'density' => $density, 'Nkr' => $Nkr, 'Tkn' => $Tkn,
                'add_total_issue' => $add_total_issue, 'add_content_issue' => $add_content_issue, 'hastargt' => $hastargt,
                'type' => $type, 'synonymof' => $synonymof));
        }
    }
    return $newar;
}
function removeCommonWords($string){ 
    
    $stopWords = array('a','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','e','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','t','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','www','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero');
    return preg_replace('/\b('.implode('|',$stopWords).')\b/','',$string);
}

function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}

function page_analysis($data,$browser){
    $credt = getcrealgo(0);    
    if(empty($credt)){
        $PAGE_TITLE_RANGE = PAGE_TITLE_RANGE; //cre->minrangetite - $cre->maxrangetite";
        $KEYWORD_TITLE_DENSITY = KEYWORD_TITLE_DENSITY; //cre->minkeydens - $cre->maxkeydens"; // 5% - min                            
        $PAGE_DESC_RANGE = PAGE_DESC_RANGE; //cre->minrangedesc - $cre->maxrangedesc";
        $KEYWORD_DESC_DENSITY = KEYWORD_DESC_DENSITY; //cre->minkeyedesc - $cre->maxkeyedesc"; // 5% - min

        $MAX_HEADING_TAGS = MAX_HEADING_TAGS; //cre->maxhtags";
        $MAX_H1_TAGS = MAX_H1_TAGS; //cre->maxh1tags";
        $HEADING_LENGTH = HEADING_LENGTH; //cre->minheadlength - $cre->maxheadlength";
        $PAGE_CONTENT_RANGE = PAGE_CONTENT_RANGE; //cre->mincontentrange - $cre->maxcontentrange";
        $KEYWORD_CONTENT_DENSITY = KEYWORD_CONTENT_DENSITY; //cre->mincontentdensity - $cre->maxcontentdensity"; // 5% - min

        $EXTRANL_LINKS = EXTRANL_LINKS; //cre->minextlinks - $cre->maxextlinks";
        $AVG_PAGE_SIZE = AVG_PAGE_SIZE; //cre->minpagesize - $cre->maxpagesize"; // max 1 mb page size
        $AVG_LOADING_TIME = AVG_LOADING_TIME; //cre->minloadtime - $cre->maxloadtime"; // 0 to 5 seconds
        $TITLE_RELEVANCY = TITLE_RELEVANCY; //$cre->titlerelevancy;
        $DESC_RELEVANCY = DESC_RELEVANCY; //$cre->descrelevancy;
        $TEXT_RATIO = TEXT_RATIO; //cre->mintextratio - $cre->maxtextratio";

        $OVERALL_KEY_DENSITY = OVERALL_KEY_DENSITY; //cre->maxoverdens"; // greater than equal to 3% - 
        $OVERALL_PRIMARY_DENSITY = OVERALL_PRIMARY_DENSITY; //cre->maxprimarydens"; // greater than equal to 5%
        $IDCRE = IDCRE;
    }
    else{     
        $IDCRE = $credt->id;
        $cre = json_decode($credt->credata);        
        $PAGE_TITLE_RANGE  = "$cre->minrangetite - $cre->maxrangetite";
        $KEYWORD_TITLE_DENSITY  = "$cre->minkeydens - $cre->maxkeydens"; // 5% - min
        $PAGE_DESC_RANGE  = "$cre->minrangedesc - $cre->maxrangedesc";
        $KEYWORD_DESC_DENSITY  = "$cre->minkeyedesc - $cre->maxkeyedesc"; // 5% - min    
        $MAX_HEADING_TAGS  = "$cre->maxhtags";    
        $MAX_H1_TAGS  = "$cre->maxh1tags";    
        $HEADING_LENGTH  = "$cre->minheadlength - $cre->maxheadlength";
        $PAGE_CONTENT_RANGE  = $cre->mincontentrange;
        $KEYWORD_CONTENT_DENSITY  = "$cre->mincontentdensity - $cre->maxcontentdensity"; // 5% - min
        $EXTRANL_LINKS  = "$cre->minextlinks - $cre->maxextlinks";
        $AVG_PAGE_SIZE  = "$cre->minpagesize - $cre->maxpagesize"; // max 1 mb page size
        $AVG_LOADING_TIME  = "$cre->minloadtime - $cre->maxloadtime"; // 0 to 5 seconds
        $TITLE_RELEVANCY  = $cre->titlerelevancy;
        $DESC_RELEVANCY  = $cre->descrelevancy;    
        $TEXT_RATIO  = "$cre->mintextratio - $cre->maxtextratio";    
        $OVERALL_KEY_DENSITY  = "$cre->maxoverdens"; // greater than equal to 3% - 
        $OVERALL_PRIMARY_DENSITY  = "$cre->maxprimarydens"; // greater than equal to 5%            
    }
    $url = appendhttp($data->url);
    $url_exist = urlexist($url);
    $partsurl = parse_url($url);
    $baseurl = '';
    if(isset($partsurl['scheme']) && isset($partsurl['host'])){
        $baseurl = $partsurl['scheme'].'://'.$partsurl['host'];
    }
    
    $score = 0; $pagespreed = 0; $step = 0; $stepass = 0; $mobile = 0; $text_ratio = 0;
    
    $title_issues = 0; $meta_issues = 0; $content_issues = 0;
    $heading_issues = 0; $link_issues = 0; $image_issues = 0;
    
    /* Start phantom js to check web page 
    $urltocheck = $url;
    $dir_yslow = TR_COUNT_PLUGIN_DIR."/yslow.js";    
    $cmd ="phantomjs --ssl-protocol=any --ignore-ssl-errors=true ".$dir_yslow." --info basic --format json ".$urltocheck;
    exec($cmd, $output, $return_var); $score = 0; $pagespreed = 0;    
    if(isset($output[0])){
        $dt = json_decode($output[0]);
        $score = isset($dt["o"])?$dt["o"]:0;
        $pagespreed = isset($dt["lt"])?$dt["lt"]:0;                
    }
    End phantom js to check web page */        
    //$browser->get($url);
    
    $totalpagesize = 0; $total_issues = 0;
    $keyword = $data->keyword;
    $site_url = $url;    
    $timeload = 0;
    
    $time1 = microtime(true);        
    $html = file_get_contents($url);    
    $time2 = microtime(true);
    
    
    $onlybody = new DOMDocument();
    $mockbody = new DOMDocument;
    $mockbody->preserveWhiteSpace = false;
    
    $onlybody->loadHTML($html);    
    $bodydata = $onlybody->getElementsByTagName('body');
    $bodydata = $bodydata->item(0);
    foreach ($bodydata->childNodes as $child){
        $mockbody->appendChild($mockbody->importNode($child, true));
    }
    
    removeElementsByTagName('script', $mockbody);
    removeElementsByTagName('style', $mockbody);
    removeElementsByTagName('noscript', $mockbody);
    removeElementsByTagName('header', $mockbody);
    removeElementsByTagName('footer', $mockbody);    
            
    $onlybodyhtml = $mockbody->saveHTML();
    $onlybodyhtml = remove_html_comments($onlybodyhtml);
    
    //$bodydata = $bodydata->nodeValue;
    
//    removeElementsByTagName('script', $onlybody);
//    removeElementsByTagName('noscript', $onlybody);
//    removeElementsByTagName('style', $onlybody);
//    removeElementsByTagName('head', $onlybody);  
//    removeElementsByTagName('header', $onlybody);
//    removeElementsByTagName('footer', $onlybody); 
//
//    $onlybodyhtml = $onlybody->saveHtml();
//    pr(htmlspecialchars($onlybodyhtml));  die;   
    
    $totalstrlength = strlen($onlybodyhtml);
    $remintext = strip_html_tags($onlybodyhtml);
    $remintextlength =  strlen($remintext);    
    $text_ratio = round(( $remintextlength / $totalstrlength ) * 100,2);    
    
    $rang = explode("-", $TEXT_RATIO);
    $min = isset($rang[0])?trim($rang[0]):10; // %
    $max = isset($rang[1])?trim($rang[1]):20; // %
    $valid_ratio = 0;
    if($text_ratio >= $min && $text_ratio <= $max){
        $valid_ratio = 1; $stepass++;
    }
    else{
        $total_issues++; $content_issues++;
    }
    $step++;
            
    
    $pagesize = get_remote_size($url);
    $pagestatus = get_remote_status($url);
    $fulltimeload = $time2 - $time1; // in seconds    
    $timeload = $fulltimeload;
    
    $validloadtime = 0;
    if($pagespreed > 0){
        $timeload = $pagespreed;
    }
    
    $lotime = floatval($timeload);
    
    $rang = explode("-", $AVG_LOADING_TIME);
    $min = isset($rang[0])?trim($rang[0]):0; // words
    $max = isset($rang[1])?trim($rang[1]):5; // words
    if($lotime >= $min && $lotime <= $max){
        $validloadtime = 1; $stepass++;
    }
    else{
        $total_issues++; $content_issues++;
    }
    $step++;
                
    $dom = new DOMDocument;
    $dom->loadHTML($html);    
    $dom->preserveWhiteSpace = false;
    $arr = array(
        'keyword' => $keyword,
        'url' => $url,
        'base_url' => $baseurl,
        'validloadtime' => $validloadtime,        
        'text_ratio' => $text_ratio,
        'valid_ratio' => $valid_ratio,
        'pagestatus' => $pagestatus,
        'idrcre' => $IDCRE
    );
    
    //return $arr;
    
    $arphrase = array(); $keywords = array(); $contentdata = array();
    $onlycontentwords = '';
    // get keyword if empty
    if($keyword == ''){
        
        $bodydom = new DOMDocument();
        $bodydom->loadHTML($html);    
        
        removeElementsByTagName('script', $bodydom);
        removeElementsByTagName('noscript', $bodydom);
        removeElementsByTagName('style', $bodydom);
        removeElementsByTagName('head', $bodydom);  
        $fullbodyhtml = $bodydom->saveHtml();          // old $databodyhtml = $bodydom->saveHtml(); 
        
        $databodyhtml = $onlybodyhtml;
         
        
        //$headers = htmlspecialchars($browser->getHeaders());
        $headersar = get_headers($url); $indx = 0; $headerstr = '';
        foreach($headersar as $headear){            
            if (strpos(strtolower($headear), strtolower("charset=")) !== false) {
                $headerstr = $headear;
                break;
            }
            $indx++;
        }
        if($headerstr != ''){
            $headers = explode("charset=", $headerstr);  
            $encoding = isset($headers[1])?trim($headers[1]):'UTF-8';
        }
        else{
            $encoding = 'UTF-8';
        }
                
        $bodaydata = iconv( $encoding, "utf-8", $databodyhtml );
        
        $bodaydata = $onlycontentwords = strip_html_tags( $bodaydata );        
        $bodaydata = html_entity_decode( $bodaydata, ENT_QUOTES, "UTF-8" );         
        
//        $databodyhtml = $bodydom->saveHtml();        
//        $bodaydata = strip_html_tags($databodyhtml);                 
//        $bodaydata = cleanString($databodyhtml);   
               
        $datatargetkeywords = array();
        if($data->tarkeyword != ''){
            $datatargetkeywords = getcontentdata($data->tarkeyword, $KEYWORD_CONTENT_DENSITY, $bodaydata, 0);                                    
        }
        
        $ar = getLongTailKeywords($bodaydata);        
                
        
        $contentdata = getcontentdata($ar, $KEYWORD_CONTENT_DENSITY, $bodaydata, 3); 
        $twowords = getcontentdata($ar, $KEYWORD_CONTENT_DENSITY, $bodaydata, 2);
        $contentdata = array_merge($contentdata, $twowords);
        
        $keywords = $datatargetkeywords;
        foreach($contentdata as $keyw => $contentd){
            if(!array_key_exists(strtolower($keyw), array_change_key_case($datatargetkeywords,CASE_LOWER))){
                $keywords["$keyw"] = $contentd;
            }
        }
               
        //$keywords = array_merge($contentdata, $datatargetkeywords);        
    }   
    
    // check page speed and test    
    $arr['page_speed'] = $timeload; // in micro seconds
            
    $arr['page_size'] = $totalpagesize = $pagesize; // size in bytes        
    
    if(isset($partsurl['query']) && $partsurl['query'] != ''){        
        $arr['seo_friendly'] = 0; $total_issues++; $content_issues++;
    }
    else{
        $arr['seo_friendly'] = 1; $stepass++;
    }
    $step++;
    
    // check doc type
    $arr['doctpye'] = 0;
    $doctype = '<!DOCTYPE';
    if (strpos(strtolower($html), strtolower($doctype)) !== false) {
        $arr['doctpye'] = 1; $stepass++;
    }
    else{
        $total_issues++; $content_issues++;
    }
    $step++;   
        
    // check if body data calcluateed or not
    if(isset($bodaydata) && trim($bodaydata) != ''){
        
        $bodydom = new DOMDocument;
        $bodydom->loadHTML($html);   
        
        removeElementsByTagName('script', $bodydom);
        removeElementsByTagName('noscript', $bodydom);
        removeElementsByTagName('style', $bodydom);
        removeElementsByTagName('head', $bodydom);  
        $fullbodyhtml = $databodyhtml = $bodydom->saveHtml();                  
        //$headers = htmlspecialchars($browser->getHeaders());
      
        $headersar = get_headers($url); $indx = 0; $headerstr = '';
        foreach($headersar as $headear){            
            if (strpos(strtolower($headear), strtolower("charset=")) !== false) {
                $headerstr = $headear;
                break;
            }
            $indx++;
        }
        if($headerstr != ''){
            $headers = explode("charset=", $headerstr);  
            $encoding = isset($headers[1])?trim($headers[1]):'UTF-8';
        }
        else{
            $encoding = 'UTF-8';
        }
        
        
        $bodaydata = iconv( $encoding, "utf-8", $databodyhtml );
        
        $bodaydata = $onlycontentwords = strip_html_tags( $bodaydata );        
        $bodaydata = html_entity_decode( $bodaydata, ENT_QUOTES, "UTF-8" );           
    }
    
    $totalwords = str_word_count($bodaydata);        
    
    $arcontent = array(
        'total_words' => $totalwords
    );    
        
     // check iframe
    $iframe = '<iframe'; $arr['iframe'] = 0;    
    if (strpos(strtolower($fullbodyhtml), strtolower($iframe)) !== false) {
        $arr['iframe'] = 1; $total_issues++; $content_issues++;
    }
    else{
        $stepass++;
    }
    $step++;
    
//    $rang = explode("-", $PAGE_CONTENT_RANGE);
//    $min = isset($rang[0])?trim($rang[0]):500; // words
//    $max = isset($rang[1])?trim($rang[1]):1000; // words
    
    $arcontent['content_valid'] = 0;    
    if($totalwords >= $PAGE_CONTENT_RANGE){
        $arcontent['content_valid'] = 1; $stepass++;
    }
    else{
        $total_issues++; $content_issues++;
    }
    $step++;    
    if($keyword != ''){
        $resocuur = fnd_pos(strtolower($keyword),strtolower($bodaydata));            
        $total_occr = $Nkr = count($resocuur);
        $arcontent['keyword_available_content'] = $total_occr;
    }
    
    // check keywords in content
//    $rang = explode("-", $KEYWORD_CONTENT_DENSITY);
//    $min = isset($rang[0])?trim($rang[0]):5; // words
//    $max = isset($rang[1])?trim($rang[1]):15; // words    
//    $arcontent['keyword_in_content_valid'] = 0;    
//    if($total_occr >= $min && $total_occr <= $max){
//        $arcontent['keyword_in_content_valid'] = 1;
//    }    
    
    if($keyword != ''){
        $Tkn = str_word_count(strtolower($bodaydata));        
        $Density = ($Nkr / $Tkn) * 100;
        
        $rang = explode("-", $KEYWORD_CONTENT_DENSITY);
        $min = isset($rang[0])?trim($rang[0]):2; // %
        $max = isset($rang[1])?trim($rang[1]):3; // %
                
        if($Density < $min || $Density > $max){
            $total_issues++; $content_issues++;
        }
        else{
            $stepass++;
        }
        $step++;
        $arcontent['content_key_density'] = round($Density,2).'%';        
    }
    
    $arr['content'] = $arcontent;
    
    //pr($arr); die;        
    
    // check title tag
    
    $artitle = array();           
    $title = $dom->getElementsByTagName('title'); 
    
    $artitle['title_tag'] = 0;    
    if($title->length > 0){
        $artitle['title_tag'] = 1; 
        $title = trim($title->item(0)->nodeValue);
        $artitle['title'] = $title;
        $rang = explode("-", $PAGE_TITLE_RANGE);
        $min = isset($rang[0])?trim($rang[0]):1;
        $max = isset($rang[1])?trim($rang[1]):70;        
        $titlelen = strlen($title);
        $artitle['title_valid'] = 0;
        $artitle['title_length'] = $titlelen;
        if($titlelen >= $min && $titlelen <= $max){
            $artitle['title_valid'] = 1; $stepass++;
        }
        else{
            $total_issues++; $title_issues++;
        }
        $step++;
        $artitle['title_relevant'] = 0;
        
//        similar_text(strtolower($title), strtolower($onlycontentwords), $percent);
//        $percent = round($percent,2); 
//        pr($percent);
        
        // relevancy new code        
        $titlewords = getLongTailKeywords($title, 1, 0);
        $totaltitlewords = count($titlewords); $matchtitlecont = 0;
        foreach($titlewords as $key => $a){
            $foundtitlekey = fnd_pos(strtolower($key),strtolower($onlycontentwords));
            if(!empty($foundtitlekey)){
                $matchtitlecont++;
            }
        }
        $percent = (($matchtitlecont / $totaltitlewords) * 100);
        $percent = round($percent,2); 
        // relevancy new code                
        
        $artitle['title_relevancy'] = $percent;
        // relevancy fine if grater than 50%
        if($percent >= $TITLE_RELEVANCY){
            $artitle['title_relevant'] = 1; $stepass++;
        }
        else{
            $total_issues++; $title_issues++;
        }
        $step++;
        $artitle['title_key_density'] = "0";
        
        $rang = explode("-", $KEYWORD_TITLE_DENSITY);
        $min = isset($rang[0])?trim($rang[0]):2; // %
        $max = isset($rang[1])?trim($rang[1]):3; // %  

        $Tkn = str_word_count(strtolower($title));   
            
        if (strpos(strtolower($title), strtolower($keyword)) !== false) {            
            $resocuur = fnd_pos(strtolower($keyword),strtolower($title));        
            $total_occr = $Nkr = count($resocuur);
            $artitle['keyword_available_title'] = $total_occr;
            
            // check keywords in title
//            $rang = explode("-", $KEYWORD_TITLE_DENSITY);
//            $min = isset($rang[0])?trim($rang[0]):1; // words
//            $max = isset($rang[1])?trim($rang[1]):2; // words            
//            $artitle['keyword_in_title_valid'] = 0;    
//            if($total_occr >= $min && $total_occr <= $max){
//                $artitle['keyword_in_title_valid'] = 1;
//            }            
            //$Nwp = str_word_count(strtolower($keyword));                        
            
            if($keyword != ''){                
                $Density = ($Nkr / $Tkn) * 100;                
                if($Density < $min || $Density > $max){
                    $total_issues++; $title_issues++;
                }
                else{
                    $stepass++;
                }
                $step++;
                $artitle['title_key_density'] = round($Density,2).'%';
            }            
        }
        
        if($keyword == ''){
            $newar = array();
            foreach($keywords as $key => $keyw){
                $resocuur = fnd_pos(strtolower($key),strtolower($title));
                $Nkr = count($resocuur);
                $Density = ($Nkr / $Tkn) * 100;
                $density = round($Density,2).'%';
                $add_total_issue = 0; $add_content_issue = 0;            
                if($Density < $min || $Density > $max){
                    $add_total_issue = 1; $add_content_issue = 1;
                }
                $keywords["$key"]['title'] = array('occurence' => $Nkr, 'density' => $density,'Nkr' => $Nkr, 'Tkn' => $Tkn,
                'add_total_issue' => $add_total_issue, 'add_content_issue' => $add_content_issue);                   
            }                
        }        
    }
    
    $arr['title'] = $artitle;    
    
    //check meta description
    
    $ardesc = array();
    
    $ardesc['is_meta_desc'] = 0; $description= '';
    $ardesc['robots_meta_tag'] = 0; $duplicatemetadesc = 0;
    $metas = $dom->getElementsByTagName('meta');
    for ($i = 0; $i < $metas->length; $i++){
        $meta = $metas->item($i);
        if($meta->getAttribute('name') == 'description'){
            $ardesc['is_meta_desc'] = 1;            
            $description = $meta->getAttribute('content');        
            $rang = explode("-", $PAGE_DESC_RANGE);
            $min = isset($rang[0])?trim($rang[0]):150;
            $max = isset($rang[1])?trim($rang[1]):160;                         
            $ardesc['desc_valid'] = 0;
            $desclen = strlen($description);
            $ardesc['desc_length'] = $desclen;
            if($desclen >= $min && $desclen <= $max){
                $ardesc['desc_valid'] = 1; $stepass++;
            } 
            else{
                $total_issues++; $meta_issues++;
            }
            $step++;
            $ardesc['desc_relevant'] = 0;
            $percent = 0;
            
            //similar_text(strtolower($description), strtolower($onlycontentwords), $percent);
            //$percent = round($percent,2);
                        
            // desc relevancy new code        
            $descwords = getLongTailKeywords($description, 1, 0);
            $totaldescwords = count($descwords); $matchdesccont = 0;
            foreach($descwords as $key => $a){
                $founddesckey = fnd_pos(strtolower($key),strtolower($onlycontentwords));
                if(!empty($founddesckey)){
                    $matchdesccont++;
                }
            }
            $percent = (($matchdesccont / $totaldescwords) * 100);
            $percent = round($percent,2);            
            // desc relevancy new code
           
            
            $ardesc['desc_relevancy'] = $percent;
            // relevancy fine if grater than 50%
            if($percent >= $DESC_RELEVANCY){
                $ardesc['desc_relevant'] = 1; $stepass++;                
            }
            else{
                $total_issues++; $meta_issues++;
            }
            $step++;
            $ardesc['desc_key_density'] = "0";
            
            $Tkn = str_word_count(strtolower($description));
            $rang = explode("-", $KEYWORD_DESC_DENSITY);
            $min = isset($rang[0])?trim($rang[0]):2; // %
            $max = isset($rang[1])?trim($rang[1]):3; // %
            
            if (strpos(strtolower($description), strtolower($keyword)) !== false) {
                
                $resocuur = fnd_pos(strtolower($keyword),strtolower($description));        
                $total_occr = $Nkr = count($resocuur);    
                $ardesc['keyword_available_desc'] = $total_occr;
                
                 // check keywords in description
//                $rang = explode("-", $KEYWORD_DESC_DENSITY);
//                $min = isset($rang[0])?trim($rang[0]):1; // words
//                $max = isset($rang[1])?trim($rang[1]):2; // words                
//                $ardesc['keyword_in_desc_valid'] = 0;    
//                if($total_occr >= $min && $total_occr <= $max){
//                    $ardesc['keyword_in_desc_valid'] = 1;
//                }                
                //$Nwp = str_word_count(strtolower($keyword));     
                
                if($keyword != ''){                    
                    $Density = ($Nkr / $Tkn) * 100;
                    if($Density < $min || $Density > $max){
                        $total_issues++; $meta_issues++;
                    }
                    else{
                        $stepass++;
                    }
                    $step++;
                    $ardesc['desc_key_density'] = round($Density,2).'%';
                }
            }
            
            if($keyword == ''){
                $newar = array();
                foreach($keywords as $key => $keyw){
                    $resocuur = fnd_pos(strtolower($key),strtolower($description));
                    $Nkr = count($resocuur);
                    $Density = ($Nkr / $Tkn) * 100;
                    $density = round($Density,2).'%';
                    $add_total_issue = 0; $add_content_issue = 0;            
                    if($Density < $min || $Density > $max){
                        $add_total_issue = 1; $add_content_issue = 1;
                    }
                    $keywords["$key"]['desc'] = array('occurence' => $Nkr, 'density' => $density, 'Nkr' => $Nkr, 'Tkn' => $Tkn, 
                    'add_total_issue' => $add_total_issue, 'add_content_issue' => $add_content_issue);                   
                }                
            }
                        
            $duplicatemetadesc++;
        }
        
        // in case description empty
        foreach($keywords as $key => $keyw){
            if($description == ''){
                $keywords["$key"]['desc'] = array('occurence' => 0);   
            }
        }
        
        if($meta->getAttribute('name') == 'robots'){
            $ardesc['robots_meta_tag'] = 1;
        } 
        
        if($meta->getAttribute('name') == 'viewport'){
            $mobile = 1;
        }        
    }
    
    $arr['mobile_friendly'] = 1;
    if($mobile == 0){
        $ardesc['mobile_friendly'] = 0;
        $total_issues++; $content_issues++;
    }
    else{
       $stepass++;
    }
    $step++;
        
    if($duplicatemetadesc >= 2){
        $ardesc['duplicate_meta_desc'] = 1;
        $total_issues++; $meta_issues++;
    }
    else{
        $ardesc['duplicate_meta_desc'] = 0; $stepass++;
    }
    $step++;
    
    if($ardesc['robots_meta_tag'] == 0){
        $total_issues++; $meta_issues++;
    }
    else{
        $stepass++;
    }
    $step++;
    
    $ardesc['meta_desc'] = $description;    
            
    $arr['desc'] = $ardesc;
       
    // check heading tags
    
    $rang = explode("-", $HEADING_LENGTH);
    $min = isset($rang[0])?trim($rang[0]):1;
    $max = isset($rang[1])?trim($rang[1]):120;  
    $keyexistheader = 0;
    $arrheadings = array();
    $h1s = $mockbody->getElementsByTagName('h1');  
    $arrheadings['totalh1'] = $h1s->length;
    
    $newar = array(); $headingocuur = array();
    for ($i = 0; $i < $h1s->length; $i++){
        $htext = $h1s->item($i)->nodeValue; 
        if (strpos(strtolower($htext), strtolower($keyword)) !== false) {
            $arrheadings['h1_keyword'][] = $htext; $keyexistheader++;
        }
        $lenstr = strlen($htext);                
        if($lenstr < $min || $lenstr > $max){
            $arrheadings['h1'][] = $htext;
            $heading_issues++; $total_issues++;
        }
        else{
            $arrheadings['h1f'][] = $htext;
            $stepass++;
        }
        $step++;
        $headingocuur[] = $htext;        
    }
    
    $h2s = $mockbody->getElementsByTagName('h2');
    $arrheadings['totalh2'] = $h2s->length;        
    for ($i = 0; $i < $h2s->length; $i++){
        $htext = $h2s->item($i)->nodeValue; $lenstr = strlen($htext);         
        
        if (strpos(strtolower($htext), strtolower($keyword)) !== false) {
            $arrheadings['h2_keyword'][] = $htext; $keyexistheader++;
        }
        
        if($lenstr < $min || $lenstr > $max){
            $arrheadings['h2'][] = $htext;
            $heading_issues++; $total_issues++;
        }
        else{
            $arrheadings['h2f'][] = $htext;
            $stepass++;
        }
        $step++;

        $headingocuur[] = $htext;
    }
    
    $h3s = $mockbody->getElementsByTagName('h3');
    $arrheadings['totalh3'] = $h3s->length;
    
    for ($i = 0; $i < $h3s->length; $i++){
        $htext = $h3s->item($i)->nodeValue; $lenstr = strlen($htext);
        if (strpos(strtolower($htext), strtolower($keyword)) !== false) {
            $arrheadings['h3_keyword'][] = $htext; $keyexistheader++;
        }
        if($lenstr < $min || $lenstr > $max){
            $arrheadings['h3'][] = $htext;
            $heading_issues++; $total_issues++;
        }
        else{
            $arrheadings['h3f'][] = $htext;
            $stepass++;
        }
        $step++;
        $headingocuur[] = $htext;
    }
    
    
    $h4s = $mockbody->getElementsByTagName('h4');
    $arrheadings['totalh4'] = $h4s->length;
    
    for ($i = 0; $i < $h4s->length; $i++){
        $htext = $h4s->item($i)->nodeValue; $lenstr = strlen($htext);
        if (strpos(strtolower($htext), strtolower($keyword)) !== false) {
            $arrheadings['h4_keyword'][] = $htext; $keyexistheader++;
        }
        if($lenstr < $min || $lenstr > $max){
            $arrheadings['h4'][] = $htext;
            $heading_issues++; $total_issues++;
        }
        else{
            $arrheadings['h4f'][] = $htext;
            $stepass++;
        }
        $step++;
        $headingocuur[] = $htext;
    }
    
    $h5s = $mockbody->getElementsByTagName('h5');
    $arrheadings['totalh5'] = $h5s->length;
    
    for ($i = 0; $i < $h5s->length; $i++){
        $htext = $h5s->item($i)->nodeValue; $lenstr = strlen($htext);
        if (strpos(strtolower($htext), strtolower($keyword)) !== false) {
            $arrheadings['h5_keyword'][] = $htext; $keyexistheader++;
        }
        if($lenstr < $min || $lenstr > $max){
            $arrheadings['h5'][] = $htext;
            $heading_issues++; $total_issues++;
        }
        else{
            $arrheadings['h5f'][] = $htext;
            $stepass++;
        }
        $step++;
        $headingocuur[] = $htext;
    }
    
    $h6s = $mockbody->getElementsByTagName('h6');
    $arrheadings['totalh6'] = $h6s->length;
    
    for ($i = 0; $i < $h6s->length; $i++){
        $htext = $h6s->item($i)->nodeValue; $lenstr = strlen($htext);
        if (strpos(strtolower($htext), strtolower($keyword)) !== false) {
            $arrheadings['h6_keyword'][] = $htext; $keyexistheader++;
        }
        if($lenstr < $min || $lenstr > $max){
            $arrheadings['h6'][] = $htext;
            $heading_issues++; $total_issues++;
        }
        else{
            $arrheadings['h6f'][] = $htext;
            $stepass++;
        }
        $step++;
        $headingocuur[] = $htext;
    }
        
    $step++; 
    if($arrheadings['totalh1'] > $MAX_H1_TAGS){
       $heading_issues++; $total_issues++;
    }
    else{
        $stepass++;
    }    
    
    
    //$MAX_HEADING_TAGS    
    $totalheadtags = $arrheadings['totalh1'] + $arrheadings['totalh2'] + $arrheadings['totalh3'] + $arrheadings['totalh4'] + 
            $arrheadings['totalh5'] + $arrheadings['totalh6'];
    
    $step++; 
    $arrheadings['totalheadtags'] = $totalheadtags;
    if($totalheadtags > $MAX_HEADING_TAGS){
       $heading_issues++; $total_issues++;
    }
    else{
        $stepass++;
    }
    
    if($keyword == ''){
        
        foreach($keywords as $key => $keyw){            
            $totalheadingocuur = 0;
            foreach ($headingocuur as $headi){
            
                $resocuur = fnd_pos(strtolower($key),strtolower($headi));
                                
                $cntNkr = count($resocuur);
                if($cntNkr > 0){
                    $totalheadingocuur++;
                }
            }

            $keywords["$key"]['headings'] = array('occurence' => $totalheadingocuur); 
        }        
    
        /*
         // code to check if keyword not occurred more than 2 places, than it disabled
        foreach($keywords as $key => $keyw){ 
            
            if($keyw['content']['hastargt'] == 0){
                            
                $checkoccur = 0;
                if($keyw['content']['occurence'] > 1){
                    $checkoccur++;
                }

                if($keyw['title']['occurence'] > 1){
                    $checkoccur++;
                }

                if($keyw['desc']['occurence'] > 1){
                    $checkoccur++;
                }

                if($keyw['headings']['occurence'] > 1){
                    $checkoccur++;
                }

                // check if occur less than 2, then it is no keyword
                if($checkoccur < 2){
                    unset($keywords["$key"]);
                }
            }
        }
        */
        
        //$keywords = array_values($keywords);
        $arr['keywords'] = $keywords;            
    }
    else{
        $arrheadings['keyword_in_headings'] = $keyexistheader;
        if($keyexistheader <= 0){
            $total_issues++; $heading_issues++;
        }
        else{
            $stepass++;
        }
        $step++;
    }    
    
    $arr['headings'] = $arrheadings;
    
    // check canonical tag and // Check Favicon Icon // css links 
    $arr['canonical_tag'] = 0; $arr['favicon'] = 0; $arcss = array(); $css_size = 0;
    $canonicaltag = $dom->getElementsByTagName('link'); // rel canonical tag available in only full html
    
    for ($i = 0; $i < $canonicaltag->length; $i++){
        $canonical_tag = $canonicaltag->item($i);        
        if($canonical_tag->getAttribute('rel') == 'canonical'){
            $arr['canonical_tag'] = 1;           
        }
        if($canonical_tag->getAttribute('type') == 'text/css' || $canonical_tag->getAttribute('rel') == 'stylesheet'){
            $csslink = $canonical_tag->getAttribute('href');
            if($baseurl != ''){
                $csslink = reltoabs($csslink, $baseurl);
            }
            $time1 = microtime(true);
            $cssize = get_remote_size($csslink);
            $time2 = microtime(true);
            $fulltimeload = $fulltimeload + ($time2 - $time1);
            
            $css_size = $css_size + $cssize;
            $arcss[] = $csslink;        
        }
        if($canonical_tag->getAttribute('rel') == 'shortcut' || $canonical_tag->getAttribute('rel') == 'shortcut icon' 
                || $canonical_tag->getAttribute('rel') == 'icon'){
            if($arr['favicon'] != 1){
                $arr['favicon'] = 1;                   
                $arr['favicon_img'] = $baseurl != ""?reltoabs($canonical_tag->getAttribute('href'), $baseurl):$canonical_tag->getAttribute('href');
            }
        }
    }
    
    // special case, favicon generated dynamically
    
    if( $arr['favicon'] == 0 ){
        $favicon = get_user_meta($data->user_id, 'webfavicon', TRUE);
        if($favicon != '' && $favicon != 0){
            $arr['favicon'] = 1;
            $arr['favicon_img'] = $favicon;
        }
    }
    
    if($arr['favicon'] == 0){
        $faiconurl = $baseurl.'/favicon.ico';
        $faiconexisst = urlexist($faiconurl);
        if($faiconexisst == 1){
            $arr['favicon'] = 1;
            $arr['favicon_img'] = $faiconurl;
        }
    }
    
    // special case, favicon generated dynamically
    
    if($arr['canonical_tag'] == 0){
        $total_issues++; $content_issues++;
    }
    else{
        $stepass++;
    }
    $step++;
    if($arr['favicon'] == 0){
        $total_issues++; $content_issues++;
    }
    else{
        $stepass++;
    }
    $step++;
    
    // internal css
    $arintenalcss = array();
    $internalcss = $dom->getElementsByTagName('style');
   
    for ($i = 0; $i < $internalcss->length; $i++){
        $internalcsstag = $internalcss->item($i);
        //$arintenalcss[] =  $internalcsstag->nodeValue;
        $cssize = mb_strlen($internalcsstag->nodeValue, '8bit');        
        $css_size = $css_size + $cssize;
    }
     
    $arcss['css_size'] = $css_size;
    $arcss['internal_css'] = $arintenalcss;        
    $totalpagesize = $totalpagesize + $css_size;    
    $arr['arcss'] = $arcss;
    
    
    // check js
    $js = $dom->getElementsByTagName('script'); $arexternaljs = array(); $arinlinejs = array(); $js_size = 0;
    for ($i = 0; $i < $js->length; $i++){
       $js_tag = $js->item($i);               
       if($js_tag->getAttribute('src') != ''){
            $jslink = $js_tag->getAttribute('src');           
            if($baseurl != ''){
                $jslink = reltoabs($jslink, $baseurl);
            }
            $time1 = microtime(true);
            $jsize = get_remote_size($jslink); 
            $time2 = microtime(true);
            $fulltimeload = $fulltimeload + ($time2 - $time1);
            
            $js_size = $js_size + $jsize;           
            $arexternaljs[] =  $jslink; 
       }
       else{  
           //$arinlinejs[] =  $js_tag->nodeValue;
           $jsize = mb_strlen($js_tag->nodeValue, '8bit');
           $js_size = $js_size + $jsize;
       }
    }
    $totalpagesize = $totalpagesize + $js_size;
    $arpagejs = array(
        'external_js' => $arexternaljs,
        'inline_js' => $arinlinejs,
        'js_size' => $js_size
    );
    
    $arr['js'] = $arpagejs; 
    
    // check img alt tag
        
    $imgtag = $mockbody->getElementsByTagName('img'); // images check from only body (excluding header and footer)
    $arimg = array(
        'total_images' => $imgtag->length
    );
    $emptysrc = 0; $img_sz = 0;
    for ($i = 0; $i < $imgtag->length; $i++){
        $img_tag = $imgtag->item($i);                
        if($img_tag->getAttribute('src') != ''){
            $imgsrc = $img_tag->getAttribute('src');
            if($baseurl != ''){
                $imgsrc = reltoabs($imgsrc, $baseurl);
            }
            $time1 = microtime(true);
            $imgsize = get_remote_size($imgsrc);
            $time2 = microtime(true);
            $fulltimeload = $fulltimeload + ($time2 - $time1);
            
            $img_sz = $img_sz + $imgsize;            
            $arimg['all_images'][] = $imgsrc;
            $stepass++;
        }
        else if($img_tag->getAttribute('src') == ''){
            $emptysrc++; $total_issues++; $image_issues++;
        }
        $step++;
        
        if($img_tag->getAttribute('alt') == ''){
            $src = $img_tag->getAttribute('src');
            $arurls1 = parse_url(strtolower($src));                                    
            if(!isset($arurls1['host'])){
                $arurls = parse_url(strtolower($site_url));                
                if($arurls1['path'] != ''){                                        
                    $src = $arurls['host'].$src;
                    $src = appendhttp($src);
                }                   
            }
                        
            $arimg['alt_miss'][] = $src;
            $total_issues++; $image_issues++;
        }
        else{
            $stepass++;
        }
        $step++;
    }
    
    
    // if no image found
    if($arimg['total_images'] == 0){
        $total_issues++; $image_issues++;
    }
    else{
        $stepass++;
    }
    $step++;
    // if no image found
    
    $totalpagesize = $totalpagesize + $img_sz;
    $arimg['empty_src'] = $emptysrc;
    $arimg['img_size'] = $img_sz;    
    $arr['images'] = $arimg;
        
    // check anchors, external links, anchors
    
    $atag = $mockbody->getElementsByTagName('a');
    $ancar = array();
    for ($i = 0; $i < $atag->length; $i++){        
        $anc_ar = $atag->item($i);               
        if($anc_ar->getAttribute('href') == ''){            
            $ancar['empty_links'][] = '';          
        }
        else{            
            // rel and title tag
            
            if($anc_ar->getAttribute('title') == ''){
                $ancar['no_title'][] = $baseurl != ''?reltoabs($anc_ar->getAttribute('href'), $baseurl):$anc_ar->getAttribute('href');
            }
            
            if($anc_ar->getAttribute('rel') == ''){
                $ancar['no_rel'][] = $baseurl != ''?reltoabs($anc_ar->getAttribute('href'), $baseurl):$anc_ar->getAttribute('href');              
            }
            $arurls = parse_url(strtolower($site_url));
            
            $href = $baseurl != ''?reltoabs($anc_ar->getAttribute('href'), $baseurl):$anc_ar->getAttribute('href');
            $hashhref = explode("#", $href);
            if(count($hashhref) > 1){
                $href = $hashhref[0];
            }
            $arurls1 = parse_url(strtolower($href));
            $relativepath = 0;
            if(!isset($arurls1['host'])){
                // if path is relative
                if($arurls1['path'] != ''){
                    $relativepath = 1;                    
                    $arurls1['host'] = $arurls['host'];                    
                }
                   
            }
                   
            if(isset($arurls['host']) && isset($arurls1['host'])){
                                                
                $url1 = str_replace(array("http://","https://"), array("",""), $arurls['host']);
                $url1 = str_replace("www."," ",$url1);
                $url2 = str_replace(array("http://","https://"), array("",""), $arurls1['host']);
                $url2 = str_replace("www."," ",$url2);
                if($url1 != $url2){
                    $ancar['external_links'][] = $baseurl != ''?reltoabs($href, $baseurl):$href;
                }
                else{
                    if($relativepath == 1){
                        
                        $proto = isset($arurls['scheme'])?trim($arurls['scheme']):'';
                        if($proto == ''){
                            $proto = 'http';
                        }
                        $href = $proto.'://'.$arurls['host'].$arurls1['path'];                        
                    }
                    $ancar['internal_links'][] = $href;                                        
                }
                
                //Broken Links Code - Temp disabled
                $sts = get_remote_status($href); //urlbroken($href);
                if($sts == 404){                    
                    $ancar['broken_links'][] = $href;                    
                }
                
            }                                   
        }
    }
    
    $ancar['external_links'] = array_unique($ancar['external_links']);
    $ancar['internal_links'] = array_unique($ancar['internal_links']);
    
    if(isset($ancar['external_links']) && count($ancar['external_links']) > 0){
        //CHECK $EXTRANL_LINKS        
        $rang = explode("-", $EXTRANL_LINKS);
        $min = isset($rang[0])?trim($rang[0]):1;
        $max = isset($rang[1])?trim($rang[1]):6; 
        $total_exlinks = count($ancar['external_links']);
        if($total_exlinks < $min || $total_exlinks > $max){
            $ancar['exceed_external_links'] = 1; $stepass++;
        }
        else{
            $ancar['exceed_external_links'] = 0;    
            $total_issues++; $link_issues++;    
        }
        $step++;
    }
        
    $ancar['no_title'] = array_unique($ancar['no_title']);
    $ancar['no_rel'] = array_unique($ancar['no_rel']);
    $ancar['empty_links'] = array_unique($ancar['empty_links']);
    $ancar['broken_links'] = array_unique($ancar['broken_links']);
    
    $ancar['no_title'] = array_unique($ancar['no_title']);
    if(isset($ancar['no_title']) && !empty($ancar['no_title'])){
        $total_issues++; $link_issues++;
    }
    else{
        $stepass++;
    }
    $step++;
    $ancar['no_rel'] = array_unique($ancar['no_rel']);
    if(isset($ancar['no_rel']) && !empty($ancar['no_rel'])){
        $total_issues++; $link_issues++;
    } else{
        $stepass++;
    }
    $step++;
    if(isset($ancar['empty_links']) && !empty($ancar['empty_links'])){
        $total_issues++; $link_issues++;
    } else{
        $stepass++;
    }
    $step++;
    if(isset($ancar['broken_links']) && !empty($ancar['broken_links'])){
        $total_issues++; $link_issues++;
    } else{
        $stepass++;
    }
    $step++;
    $arr['links'] = $ancar;       
    $arr['totalpagesize'] = $totalpagesize;
    $arr['pagesizevalid'] = 0;
    $rang = explode("-", $AVG_PAGE_SIZE);
    $min = isset($rang[0])?trim($rang[0]):1; // 1 byte
    $max = isset($rang[1])?trim($rang[1]):1048576; // 1 mb
    if($totalpagesize >= $min && $totalpagesize <= $max){
        $arr['pagesizevalid'] = 1; $stepass++;
    }
    else{
        $total_issues++; $content_issues++;
    }
    $step++;
    $arr['total_issues'] = $total_issues;
    $arr['fulltimeload'] = $fulltimeload;
    
    $arrissuecount = array(
        'title_issues' => $title_issues,
        'meta_issues' => $meta_issues,
        'content_issues' => $content_issues,
        'heading_issues' => $heading_issues,
        'link_issues' => $link_issues,
        'image_issues' => $image_issues        
    );
    
    $arr['issues_count'] = $arrissuecount;
    $arr['step'] = $step;
    $arr['stepass'] = $stepass;
    
    if($score == 0){
        $score = round(($stepass / $step) * 100);                
    }
    
    // miniumu score
    if($score < 50){
        $score = 50;
    }
    
    $arr['score'] = $score;    
    return $arr;        
}

function browsername($user_agent){
    
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    
    return 'Other';
}

function notifyuserrecom($UserID,$recommend){
   // send notification
    if($recommend->auto_trigger == 0){
        global $wpdb;
        $user_id = $UserID;                               
        $uiid = isset($recommend->user_trigger)?intval($recommend->user_trigger):0;
        $user = get_userdata($uiid);
        
        if(!empty($user)){
            $email = $user->user_email;
            $display_name = $user->display_name;

            $brand = brand_name($UserID);

            $date = date("Y-m-d H:i:s");            
            $admin_email = get_option('admin_email');
            $headers = 'From: ' . $admin_email . "\r\n" .
                    'Reply-To: ' . $admin_email . "\r\n" .
                    'MIME-Version: 1.0' . "\r\n" .
                    'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

            $subj = $brand." - Campaign result of Content Recommendation Engine";        
            $msg = "<div>Hi $display_name, </div> <br/><br/>"
                    . "<div> Campaign for recommendation engine has been completed. <br/>"
                    . "Login to your account to check recommendations. <br/>"
                    . "<a href='".site_url()."/".CRE_SLUG."'>".site_url()."/".CRE_SLUG."</a>"                
                    . "</div>";
            
            //$email = 'parambir.rudra@gmail.com'; // temp email added
            @mail($email, $subj, $msg, $headers);    
            
        }
                
    }
}

?>