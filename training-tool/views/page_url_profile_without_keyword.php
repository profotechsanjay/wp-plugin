<?php// pr($analys);?>
<div class='row'>
    <div class='col-md-12'>           

        <div class="panel-group margin_top_10">

            <div class="panel panel-default">
                <div class="panel-heading">Page Title</div>
                <div class="panel-body">

                    <ul class="list-group">

                        <li class="list-group-item">
                            <div class="col1div">
                                <div class="firstspan">Title : </div> <div class="secondspan"><?php echo $analys->title->title != '' ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>  Title Found</div>
                                <?php if (isset($analys->title->title) && $analys->title->title != '') {
                                    ?>
                                    <div class="margin_top_10 firstlower">Title : <?php echo $analys->title->title; ?></div>
                                    <?php }
                                ?>
                            </div>
                            <div class="col2div"> 
                                <div class="notebottom alert alert-info">
                                    <strong>Note: </strong> The title is an important factor in the on-site search engine optimization. Not only uses the search engines the title for the keywords, the title is also used for display in the SERP (Search Engine Result Page). For best practice make use of your main keywords in the title. 
                                </div>                                            
                            </div>
                        </li>                                                                    

                        <li class="list-group-item">
                            <div class="col1div">
                                <div class="firstspan">Title Length : </div> <div class="secondspan"><?php echo $analys->title->title_valid == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>  <?php echo $analys->title->title_length != ''?$analys->title->title_length:0; ?> Characters</div>
                                
                            </div>
                            <div class="col2div"> 
                            
                                <div class="notebottom alert alert-info">
                                    <strong>Note: </strong> The <?php echo htmlspecialchars('<TITLE>'); ?> element provides a short piece of text describing the document. The title is very important as it shows in the window title bars, bookmarks and search results. Title should be between <?php echo $PAGE_TITLE_RANGE; ?> characters long.
                                </div>
                            </div>
                            
                        </li>

                        <li class="list-group-item">
                            <div class="col1div">
                                <div class="firstspan">Title Relevancy : </div> <div class="secondspan"><?php echo $analys->title->title_relevant == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?> <?php echo $analys->title->title_relevancy != ''?$analys->title->title_relevancy:0; ?> %</div>
                                </div>
                            <div class="col2div"> 
                                <div class="notebottom alert alert-info">
                                    <strong>Note: </strong> The title of page should match with content on webpage. Relevancy should minimum <?php echo $TITLE_RELEVANCY; ?> %.
                                </div>
                                 </div>
                        </li>
                        
                    </ul>

                </div>
            </div>


            <div class="panel panel-default">
                <div class="panel-heading">Meta Description</div>
                <div class="panel-body">


                    <ul class="list-group">

                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Description : </div> <div class="secondspan"><?php echo $analys->desc->meta_desc != '' ? "<img class='imgoffon' title='This is looking fine' src='$onimg' /> Meta Description Found" : "<img class='imgoffon' title='please fix this error' src='$ofimg' /> Meta Description Not Found"; ?>  </div>
                            <?php if (isset($analys->desc->meta_desc) && $analys->desc->meta_desc != '') {
                                ?>
                                <div class="margin_top_10 firstlower">Description : <?php echo $analys->desc->meta_desc; ?></div>
                                <?php }
                            ?>

                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> The meta description is an factor in the on-site search engine optimization. Not only uses the search engines the description for the keywords, the description is used frequently for display in the SERP (Search Engine Result Page). For best practice describe where your webpage is about in the description.
                            </div>                                            
                        </div></li>

                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Description Length : </div> <div class="secondspan"><?php echo $analys->desc->desc_valid == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>  <?php echo $analys->desc->desc_length != ''?$analys->desc->desc_length:0; ?> Characters</div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> 
                                Meta Description should between <?php echo $PAGE_DESC_RANGE; ?> characters
                            </div>
                        </div></li>

                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Relevancy : </div> <div class="secondspan"><?php echo $analys->desc->desc_relevant == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?> <?php echo $analys->desc->desc_relevancy != ''?$analys->desc->desc_relevancy:0; ?> %</div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">                                
                                <strong>Note: </strong> Description of page should match with content on webpage. Relevancy should minimum <?php echo $DESC_RELEVANCY; ?> %.
                            </div>
                        </div></li>

                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Robots Meta Tag : </div> <div class="secondspan"><?php echo $analys->desc->robots_meta_tag == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>                                         
                            </div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> Spider robots are not allowed to display a title and description from the Open Directory Project in the search results. 
                            </div>
                        </div></li>                        
                    </ul>



                </div>
            </div>    

            <div class="panel panel-default">
                <div class="panel-heading">Page Content</div>
                <div class="panel-body">
                    <ul class="list-group">

                        <?php
                        //$cre = get_option('credata');
                        //$cre = json_decode(json_encode($cre));
                        
                        $rang = explode("-", $AVG_PAGE_SIZE);
                        $min = isset($rang[0])?trim($rang[0]):1; // 1 byte
                        $max = isset($rang[1])?trim($rang[1]):1048576; // 1 mb
                        
                        if (isset($analys->totalpagesize) > 0) {
                            $total_pagesize = $analys->totalpagesize;
                        } else {
                            $total_pagesize = $analys->page_size + $analys->arcss->css_size + $analys->js->js_size + $analys->images->img_size;
                        }
                        ?>                                  
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Page Size : </div> <div class="secondspan"><?php echo $analys->pagesizevalid == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                <div><?php echo $total_pagesize; ?> bytes</div>  
                            </div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> Page size should not more than <?php echo $max; ?> bytes (<?php echo bytesformat($max); ?>)
                            </div>
                        </div>
                        </li> 

                        <li class="list-group-item">                            
                            <div class="firstspan">Page Breakdown : </div> <div class="secondspan">
                                <div id="container_graph" style="height:300px; width: 600px;"></div>
                            </div>
                        </li> 
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Total Words : </div> <div class="secondspan"><?php echo $analys->content->content_valid == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?> 
                                &nbsp;&nbsp;<div><?php echo $analys->content->total_words; ?></div>
                            </div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> A webpage must contain at least <?php echo $PAGE_CONTENT_RANGE; ?> words
                            </div>

                        </div></li>                                                                    

                        
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Load Time : </div> <div class="secondspan">
<?php echo $analys->validloadtime == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                <div> <?php echo number_format($analys->page_speed, 2); ?> Seconds</div> 
                            </div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> Content loading time should between <?php echo $AVG_LOADING_TIME; ?> seconds
                            </div>
                        </div></li>
                        <li class="list-group-item">                            <div class="col1div">                                      
                            <div class="firstspan">SEO Friendly URL : </div> <div class="secondspan"><?php echo $analys->seo_friendly == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> SEO Friendly URLs are easy to read and understand for crawlers
                            </div>
                        </div></li>
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">DocType Available : </div> <div class="secondspan"><?php echo $analys->doctpye == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>

                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> The <?php echo htmlspecialchars("<!DOCTYPE>"); ?> declaration tells the web browser about what version of HTML the page is written in. It is good practice to always add the <!DOCTYPE> declaration to the HTML documents, so that the browser knows what type of document to expect.
                            </div>


                        </div></li>
                        
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Text to HTML Ratio </div> <div class="secondspan"><?php echo $analys->valid_ratio == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                <div> <?php echo $analys->text_ratio != ''?$analys->text_ratio:0; ?> %</div> 
                            </div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> Low Text to HTML ratio indicates little content for search engines to index. We consider it to be good practice to have a Text to HTML ratio of between <?php echo $TEXT_RATIO; ?> %
                            </div>
                        </div></li>
                        
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Mobile Friendly : </div> <div class="secondspan"><?php echo $analys->mobile_friendly == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> 
                                <?php
                                if($analys->mobile_friendly == 1){
                                    echo "We found META viewport tag on page. this tag control how a webpage is displayed on a mobile device";
                                }
                                else{
                                    echo "We did not find META viewport tag on page";
                                }
                                ?>
                                
                            </div>
                        </div></li>
                        
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Any Iframe : </div> <div class="secondspan"><?php echo $analys->iframe == 0 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' /> No iframe found" : "<img class='imgoffon' title='please fix this error' src='$ofimg' /> Iframe found"; ?></div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> It is not recommended to use frames or iframes because they can cause problems for search engines. It is best to avoid frames and inline frames whenever possible.
                            </div>

                        </div></li>
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Canonical Tag : </div> <div class="secondspan"><?php echo $analys->canonical_tag == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> Canonical tag help web-masters to prevent duplicate content issues on page.
                            </div>
                        </div></li>
                        <li class="list-group-item">                            
                            <div class="col1div">
                            <div class="firstspan">Favicon : </div> <div class="secondspan"><?php echo $analys->favicon == 1 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                <?php if ($analys->favicon == 1) {
                                    $favimg = $analys->favicon_img;
                                    $totalstrk = substr_count($favimg,$webchk);                                    
                                    if( $totalstrk >= 2 ){
                                        $favimg = explode("$webchk/", $favimg);
                                        $lastpartidx = count($favimg) - 1;
                                        $lastpart = $favimg["$lastpartidx"];
                                        $favimg = appendhttp($website)."/".$lastpart;
                                    }
                                    
                                    ?>
                                <img height="20" width="20" src="<?php echo $favimg; ?>" />
                                <?php }
                                ?>
                            </div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> The Favicon is a small icon associated with a website. The Favicon is important because it is displayed next to the website's URL in the address bar of the browser as well as in bookmarks and shortcuts.
                            </div>
                        </div></li>

                    </ul>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Headings</div>
                <div class="panel-body">
                    <ul class="list-group">              
                        <li class="list-group-item"> 
                            <?php

                                $totlhtags = $analys->headings->totalh1 + $analys->headings->totalh2 + $analys->headings->totalh3 + $analys->headings->totalh4
                                         + $analys->headings->totalh5 + $analys->headings->totalh6;

                                $in_tag = "";
                                if (isset($analys->headings->h1_keyword)) {
                                    $in_tag .= "<p class='htagp'>" . count($analys->headings->h1_keyword) . " time(s) in &lt;H1&gt; </p>";
                                }

                                if (isset($analys->headings->h2_keyword)) {
                                    $in_tag .= "<p class='htagp'>" . count($analys->headings->h2_keyword) . " time(s) in &lt;H2&gt; </p>";
                                }

                                if (isset($analys->headings->h3_keyword)) {
                                    $in_tag .= "<p class='htagp'>" . count($analys->headings->h3_keyword) . " time(s) in &lt;H3&gt; </p>";
                                }

                                if (isset($analys->headings->h4_keyword)) {
                                    $in_tag .= "<p class='htagp'>" . count($analys->headings->h4_keyword) . " time(s) in &lt;H4&gt; </p>";
                                }

                                if (isset($analys->headings->h5_keyword)) {
                                    $in_tag .= "<p class='htagp'>" . count($analys->headings->h5_keyword) . " time(s) in &lt;H5&gt; </p>";
                                }

                                if (isset($analys->headings->h6_keyword)) {
                                    $in_tag .= "<p class='htagp'>" . count($analys->headings->h6_keyword) . " time(s) in &lt;H6&gt; </p>";
                                }
                                ?>                          
                            <div class="col1div">
                            <div class="firstspan">Heading Tags : </div> <div class="secondspan">

                                <span><?php 
                                            if( $totlhtags > 0 && $totlhtags <= $MAX_HEADING_TAGS ){                                        
                                                echo "<img class='imgoffon' title='This is looking fine' src='$onimg' /> ";                                                    
                                            } else { 
                                                echo  "<img class='imgoffon' title='please fix this error' src='$ofimg' />";                                            
                                            }
                                            ?>
                                </span>
                                    
                                   
                                &nbsp;&nbsp;<div> <?php
                                 echo $totlhtags." Heading Tags Found";
                                ?>

                                </div>
                            </div>

                            </div>
                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong>
                                The search engines uses the heading tags for the keywords. Each page should have at least 1 heading tag.
                                For best practice try not to use more than <?php echo $MAX_HEADING_TAGS; ?> heading tags on a webpage.                                
                            </div>

                            </div>
                        </li>
                        <li class="list-group-item">                    
                            <div class="col1div">
                            <div class="firstspan">Total H1 Tags : </div>                            
                                <div class="secondspan">                                    
                                    <span><?php                                     
                                            if( $analys->headings->totalh1 > 0 && $analys->headings->totalh1 <= $MAX_H1_TAGS ){                                        
                                                echo "<img class='imgoffon' title='This is looking fine' src='$onimg' /> ";                                                    
                                            } else { 
                                                echo  "<img class='imgoffon' title='please fix this error' src='$ofimg' />";                                            
                                            }
                                            ?>
                                    
                                    &nbsp;&nbsp;
                                    <?php echo $analys->headings->totalh1; ?></span>                               
                                </div>
                            </div>
                            <div class="col2div divheadingtgs"> 
                                
                                <?php
                                $k = 0; $totalhead = 0;
                                
                                if (isset($analys->headings->h1f) || isset($analys->headings->h1)) {
                                    echo '<div class="notebottom alert alert-info">';    
                                    echo '<span><strong>Note : </strong>Each page should have only '.$MAX_H1_TAGS.' H1 tag.</span>';

                                    if (isset($analys->headings->h1f)) {
                                        
                                        echo '<span class="sphead">Below list of H1 tags</span>';
                                        foreach ($analys->headings->h1f as $tag) {
                                            echo "<div class='htagp'>" . htmlspecialchars('<h1>' . $tag . '</h1>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }
                                    }
                                    if (isset($analys->headings->h1)) {
                                        $totalhead = count($analys->headings->h1);         
                                        echo '<span class="sphead">Below H1 tags are too long. Recommended Length Should between '.$HEADING_LENGTH.' characters</span>';
                                        foreach ($analys->headings->h1 as $tag) {
                                            echo "<div style='color: red;' class='htagp'>" . htmlspecialchars('<h1>' . $tag . '</h1>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }

                                    }                                
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            
                        </li>

                        <li class="list-group-item">                            
                            <div class="col1div">
                            <div class="firstspan">Total H2 Tags : </div> 
                                <div class="secondspan">                                                                                           
                                    <span><?php echo $analys->headings->totalh2; ?></span>                               
                                </div>
                            </div>
                            <div class="col2div divheadingtgs"> 
                                <?php
                                $k = 0; $totalhead = 0;
                                
                                if (isset($analys->headings->h2f) || isset($analys->headings->h2)) {
                                    echo '<div class="notebottom alert alert-info">';    
                                    
                                    if (isset($analys->headings->h2f)) {
                                        
                                        echo '<span class="sphead">Below list of H2 tags</span>';
                                        foreach ($analys->headings->h2f as $tag) {
                                            echo "<div class='htagp'>" . htmlspecialchars('<h2>' . $tag . '</h2>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }
                                    }
                                    if (isset($analys->headings->h2)) {
                                        $totalhead = count($analys->headings->h2);         
                                        echo '<span class="sphead">Below H2 tags are too long. Recommended Length Should between '.$HEADING_LENGTH.' characters</span>';
                                        foreach ($analys->headings->h2 as $tag) {
                                            echo "<div style='color: red;' class='htagp'>" . htmlspecialchars('<h2>' . $tag . '</h2>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }

                                    }                                
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            
                        </li>
                        
                        <li class="list-group-item">                            
                            <div class="col1div">
                            <div class="firstspan">Total H3 Tags : </div> 
                                <div class="secondspan">                                                                                           
                                    <span><?php echo $analys->headings->totalh3; ?></span>                               
                                </div>
                            </div>
                            <div class="col2div divheadingtgs"> 
                                <?php
                                $k = 0; $totalhead = 0;
                                if (isset($analys->headings->h3f) || isset($analys->headings->h3)) {
                                    echo '<div class="notebottom alert alert-info">';    
                                    
                                    if (isset($analys->headings->h3f)) {
                                        
                                        echo '<span class="sphead">Below list of H3 tags</span>';
                                        foreach ($analys->headings->h3f as $tag) {
                                            echo "<div class='htagp'>" . htmlspecialchars('<h3>' . $tag . '</h3>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }
                                    }
                                    if (isset($analys->headings->h3)) {
                                        $totalhead = count($analys->headings->h3);         
                                        echo '<span class="sphead">Below H3 tags are too long. Recommended Length Should between '.$HEADING_LENGTH.' characters</span>';
                                        foreach ($analys->headings->h3 as $tag) {
                                            echo "<div style='color: red;' class='htagp'>" . htmlspecialchars('<h3>' . $tag . '</h3>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }

                                    }                                
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            
                        </li>

                        <li class="list-group-item">                            
                            <div class="col1div">
                            <div class="firstspan">Total H4 Tags : </div> 
                                <div class="secondspan">                                                                                           
                                    <span><?php echo $analys->headings->totalh4; ?></span>                               
                                </div>
                            </div>
                            <div class="col2div divheadingtgs"> 
                                <?php
                                $k = 0; $totalhead = 0;
                                if (isset($analys->headings->h4f) || isset($analys->headings->h4)) {
                                    echo '<div class="notebottom alert alert-info">';    
                                    
                                    if (isset($analys->headings->h4f)) {
                                        
                                        echo '<span class="sphead">Below list of H4 tags</span>';
                                        foreach ($analys->headings->h4f as $tag) {
                                            echo "<div class='htagp'>" . htmlspecialchars('<h4>' . $tag . '</h4>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }
                                    }
                                    if (isset($analys->headings->h4)) {
                                        $totalhead = count($analys->headings->h4);         
                                        echo '<span class="sphead">Below H4 tags are too long. Recommended Length Should between '.$HEADING_LENGTH.' characters</span>';
                                        foreach ($analys->headings->h4 as $tag) {
                                            echo "<div style='color: red;' class='htagp'>" . htmlspecialchars('<h4>' . $tag . '</h4>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }

                                    }                                
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            
                        </li>

                        <li class="list-group-item">                            
                            <div class="col1div">
                            <div class="firstspan">Total H5 Tags : </div> 
                                <div class="secondspan">                                                                                           
                                    <span><?php echo $analys->headings->totalh5; ?></span>                               
                                </div>
                            </div>
                            <div class="col2div divheadingtgs"> 
                                <?php
                                $k = 0; $totalhead = 0;
                                if (isset($analys->headings->h5f) || isset($analys->headings->h5)) {
                                    echo '<div class="notebottom alert alert-info">';    
                                    
                                    if (isset($analys->headings->h5f)) {
                                        
                                        echo '<span class="sphead">Below list of H5 tags</span>';
                                        foreach ($analys->headings->h5f as $tag) {
                                            echo "<div class='htagp'>" . htmlspecialchars('<h5>' . $tag . '</h5>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }
                                    }
                                    if (isset($analys->headings->h5)) {
                                        $totalhead = count($analys->headings->h5);         
                                        echo '<span class="sphead">Below H5 tags are too long. Recommended Length Should between '.$HEADING_LENGTH.' characters</span>';
                                        foreach ($analys->headings->h5 as $tag) {
                                            echo "<div style='color: red;' class='htagp'>" . htmlspecialchars('<h5>' . $tag . '</h5>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }

                                    }                                
                                    echo '</div>';
                                }
                                
                                ?>
                            </div>
                            
                        </li>
                                                
                        <li class="list-group-item">                            
                            <div class="col1div">
                            <div class="firstspan">Total H6 Tags : </div> 
                                <div class="secondspan">                                                                                           
                                    <span><?php echo $analys->headings->totalh6; ?></span>                               
                                </div>
                            </div>
                            <div class="col2div divheadingtgs"> 
                                <?php
                                $k = 0; $totalhead = 0;
                                if (isset($analys->headings->h6f) || isset($analys->headings->h6)) {
                                    echo '<div class="notebottom alert alert-info">';    
                                    
                                    if (isset($analys->headings->h6f)) {
                                        
                                        echo '<span class="sphead">Below list of H6 tags</span>';
                                        foreach ($analys->headings->h6f as $tag) {
                                            echo "<div class='htagp'>" . htmlspecialchars('<h6>' . $tag . '</h6>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }
                                    }
                                    if (isset($analys->headings->h6)) {
                                        $totalhead = count($analys->headings->h6);         
                                        echo '<span class="sphead">Below H6 tags are too long. Recommended Length Should between '.$HEADING_LENGTH.' characters</span>';
                                        foreach ($analys->headings->h6 as $tag) {
                                            echo "<div style='color: red;' class='htagp'>" . htmlspecialchars('<h6>' . $tag . '</h6>') . '  &nbsp;&nbsp;&nbsp; Length : '.  strlen($tag).' characters</div>';
                                            $k++;
                                        }

                                    }                                
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            
                        </li>
                        
                    </ul>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Links</div>
                <div class="panel-body">

                    <ul class="list-group">                                 
                        <li class="list-group-item">                           
                            <div class="firstspan">Total Internal Links : </div> 
                             <div class="secondspan">   <div><a href="javascript:;" onclick="jQuery('.modalinternallinks').modal();" class="internallinks"><?php echo count((array) $analys->links->internal_links); ?> internal links found</a> </div>
                            </div>
                        </li>

                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Total External Links : </div> <div class="secondspan"><?php echo $analys->links->exceed_external_links == 0 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                              &nbsp;&nbsp;
                              
                              <div><a href="javascript:;" onclick="jQuery('.modalexternallinks').modal();"  class="externallinks"><?php echo count((array) $analys->links->external_links); ?> external links found</a> </div>
                            </div>

                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> According to seo, we can not define more than 6 external links
                            </div>

                        </div></li>

                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Total Broken Links : </div> 
                            <div class="secondspan">
                                <?php echo count($analys->links->broken_links) <= 0 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                &nbsp;&nbsp;                                  
                                <div><a href="javascript:;" onclick="jQuery('.modalbrokenlinks').modal();" class="brokenlinks"><?php echo isset($analys->links->broken_links) ? count((array) $analys->links->broken_links) : 0; ?> broken links found</a> </div>
                            </div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> Broken Links may have a negative effect on seo.  Please fix or remove these links 
                            </div>
                        </div></li>
                        
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Links Without Title Attribute : </div> <div class="secondspan">                              
                                &nbsp;&nbsp;<?php echo count((array)($analys->links->no_title)) <= 0 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>                                
                                <div><a href="javascript:;" onclick="jQuery('.modaltitlelinks').modal();" class="titlelinks"><?php echo count((array)($analys->links->no_title)); ?> links found without title attribute</a> </div>                                
                            </div>
                            
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> Title attribute does not have direct impact on ranking. But is can influence click behavior for users, which can indirectly affect SEO
                            </div>                            
                        </div></li>
                        
                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Links Without Rel Attribute : </div> <div class="secondspan">
                                &nbsp;&nbsp;<?php echo count((array)($analys->links->no_rel)) <= 0 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />" : "<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                <div><a href="javascript:;" onclick="jQuery('.modalrellinks').modal();" class="rellinks"><?php echo count((array)($analys->links->no_rel)); ?> links found without rel attribute</a> </div>
                            </div>
                            </div>                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <strong>Note: </strong> The REL no-follow attribute helps with SEO position because it tells search engines not to conflict multiple links coming from the same site with organic linking.
                            </div>
                        </div></li>
                        
                    </ul>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Images</div>
                <div class="panel-body">

                    <ul class="list-group">                                 
                        <li class="list-group-item">                            
                            <div class="col1div">
                                <div class="firstspan">Total Images : </div> <div class="secondspan">
                                <a href="javascript:;" onclick="jQuery('.modalallimages').modal();" class="internallinks"><?php echo $analys->images->total_images; ?> images found</a>
                                <?php
                                    $totalimages = $analys->images->total_images;
                                    if($totalimages > 0){
                                        $finmages = $analys->images->total_images - count((array) $analys->images->alt_miss);
                                        if($finmages > 0){
                                            echo "<br/> <br/><img class='imgoffon' title='This is looking fine' src='$onimg' /> ".$finmages ." images are fine";
                                        }
                                    }
                                    else{
                                        echo "<br/> <br/><img class='imgoffon' title='please fix this error' src='$ofimg' /> ";
                                    }
                                ?>
                                </div>                                      

                            </div> 
                            <div class="col2div"> 
                            <div class="notebottom alert alert-info">
                                <?php if($totalimages > 0){
                                    ?>
                                    <strong>Note: </strong> The "alt" attribute provides a text equivalent for the image. If the browser cannot display an image the alt description will be given in its place. Furthermore, some visitors cannot see images as they might be blind in which the alt tag provides a valuable image description. Finally, search engines utilize the alt attribute for image search indexing.
                                    <?php
                                } else {
                                    ?>
                                    <strong>Note: </strong> No image found on this page. Each page should have at least one image.
                                    <?php
                                } ?>                                
                            </div>


                        </div>
                        </li>

                        <li class="list-group-item">                            <div class="col1div">
                            <div class="firstspan">Missing Alt Tag : </div> 
                            <div class="secondspan">
                                                                
                                 <?php echo count($analys->images->alt_miss) > 0 ? "<img class='imgoffon' title='please fix this error' src='$ofimg' />":"<img class='imgoffon' title='This is looking fine' src='$onimg' />"; ?>
                                 &nbsp;&nbsp;
                                 <span><?php echo count($analys->images->alt_miss); ?> images without alt tag</span>                                
                            </div>
                            
                            </div>                            
                            <div class="col2div"> 
                            <?php if (count($analys->images->alt_miss) > 0) { ?>
                            
                            <div class="notebottom alert alert-info">
                                <strong>Below <?php echo count($analys->images->alt_miss); ?> image(s) found with missing alt tag</strong>
                                    <?php
                                    if (count($analys->images->alt_miss) > 0) {

                                        foreach ($analys->images->alt_miss as $miss) {
                                            echo "<div class='htagp'><a href='$miss'>$miss</a></div>";                                        
                                        }
                                        ?>

                                        <?php
                                        }
                                        ?>                                
                            </div>
                            <?php } ?>
                        </div></li>

                    </ul>

                </div>
            </div>
            <?php
            include_once get_template_directory() . '/analytics/my_functions.php';
            
            $urlchk = trim(str_replace(array("https://","http://","www."), array("","",""), $url),"/");
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">Keywords</div>
                <div class="panel-body">  
                    
                    <table class="table table-bordered">
                        <caption>Primary & Synonym Keywords</caption>
                        <thead>
                            <tr>
                                <th style="width: 250px;">Keyword</th>                               
                                <th style="width: 150px;">Content</th>
                                <th style="width: 150px;">Title</th>
                                <th style="width: 150px;">Meta Description</th>
                                <th style="width: 150px;">Heading Tags</th>
                                <th style="width: 150px;">Overall Density</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ix = 0;
                            
                            $overalldensval = $OVERALL_KEY_DENSITY;
                            $overallprimarysyndensval = $OVERALL_PRIMARY_DENSITY;
                           
                            $synomyms = array();                            
                            foreach($analys->keywords as $key => $keydata){
                                if($keydata->content->type == 'synonym'){
                                    $synomyms["$key"] = $keydata;
                                }
                            }
                            
                            foreach($analys->keywords as $key => $keydata){
                                if($keydata->content->type != 'primary'){
                                    continue;
                                }
                                $ix++;
                                
                                $keyw = strtolower(trim($key));                                
                                $sql = "SELECT * FROM `seo` WHERE TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (RankingURL, 'http://', ''),'https://',''),'www.','')) like '$urlchk' ORDER BY DateOfRank DESC"
                                        . " AND LOWER(Keyword) = '".$keyw."' LIMIT 1 ";
                                $sql = 'SELECT * FROM `seo` WHERE LOWER(Keyword) = "' . $keyw . '" LIMIT 1 ';
                                $get_result = row_array($sql);
                                $CurrentRank = isset($get_result['CurrentRank'])?$get_result['CurrentRank']:'N/A';                               
                                if($CurrentRank != 'N/A'){
                                    if ($CurrentRank <= 0) {
                                        $CurrentRank = '50+';
                                    }
                                }

                                        
                                $alltotal_Nkr = 0; $alltotal_Tkn = 0;
                                $overalldens = 0; $overalltick = "<img class='imgoffon' title='please fix this error' src='$ofimg' />";                          
                                if(isset($keydata->content->Nkr)){
                                    $alltotal_Nkr = $total_Nkr = $keydata->content->Nkr + $keydata->title->Nkr + $keydata->desc->Nkr;
                                    $alltotal_Tkn = $total_Tkn = $keydata->content->Tkn + $keydata->title->Tkn + $keydata->desc->Tkn;
                                    $Density = ($total_Nkr / $total_Tkn) * 100;
                                    $overalldens = round($Density,2).'%';
                                    if($overalldens >= $OVERALL_KEY_DENSITY){
                                        $overalltick = "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                    }
                                }
                                
//                                $valuekey = trim($key); $broad = 'N/A'; $local = 'N/A'; $mobile = 'N/A';
//                                if(isset($keywords_report["$valuekey"]) && !empty($keywords_report["$valuekey"])){
//                                    $RankingData = $keywords_report[$valuekey]['RankingData'];
//                                    $prev_RankingData = $keywords_report[$single_key]['prev_RankingData'];
//                                    $mobile = cre_ranking_data('google-mobile', $RankingData, $prev_RankingData);
//                                }
                                
                                ?>
                                <tr>
                                    <td style="width: 250px;"><?php echo $key; ?> (P)
                                        <div>Current Broad Rank : <?php echo $CurrentRank; ?></div>
                                    </td>                                   
                                    <td style="width: 150px;">
                                        <?php if($keydata->content->occurence > 0){
                                             echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                        } else {
                                            echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                        } ?>
                                        <div>
                                            Occurrence : <?php echo $keydata->content->occurence; ?>
                                        </div>
                                        <div>
                                            Density : <?php echo $keydata->content->density; ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php if($keydata->title->occurence > 0){
                                             echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                        } else {
                                            echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                        } ?>
                                        
                                        <div>
                                            Occurrence : <?php echo $keydata->title->occurence; ?>
                                        </div>
                                        <div>
                                            Density : <?php echo $keydata->title->density; ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php if($keydata->desc->occurence > 0){
                                             echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                        } else {
                                            echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                        } ?>
                                        
                                        <div>
                                            Occurrence : <?php echo $keydata->desc->occurence; ?>
                                        </div>
                                        <div>
                                            Density : <?php echo isset($keydata->desc->density)?$keydata->desc->density:'0%'; ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php if($keydata->headings->occurence > 0){
                                             echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                        } else {
                                            echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                        } ?>
                                         <div>
                                            Occurrence : <?php echo $keydata->headings->occurence; ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php echo $overalltick; ?>
                                        &nbsp;&nbsp;<?php echo $overalldens; ?>                              
                                    </td>
                                </tr>
                                
                                <?php
                                $ik = 0;
                                foreach($synomyms as $k => $synom){
                                    if(trim($synom->content->synonymof) == trim($key)){
                                        
                                                                                
                                        $overalldens = 0; $overalltick = "<img class='imgoffon' title='please fix this error' src='$ofimg' />";                          
                                        if(isset($synom->content->Nkr)){
                                            $total_Nkr = $synom->content->Nkr + $synom->title->Nkr + $synom->desc->Nkr;
                                            $total_Tkn = $synom->content->Tkn + $synom->title->Tkn + $synom->desc->Tkn;
                                            $Density = ($total_Nkr / $total_Tkn) * 100;
                                            $overalldens = round($Density,2).'%';
                                            if($overalldens >= $OVERALL_KEY_DENSITY){
                                                $overalltick = "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                            }
                                            
                                            $alltotal_Nkr = $alltotal_Nkr + $total_Nkr; 
                                            $alltotal_Tkn = $alltotal_Tkn + $total_Tkn;                                            
                                        }
                                        
                                        $keyw = strtolower(trim($k));
                                        
                                        $sql = "SELECT * FROM `seo` WHERE TRIM(BOTH  '/' FROM REPLACE(REPLACE(REPLACE (RankingURL, 'http://', ''),'https://',''),'www.','')) like '$urlchk'"
                                        . " AND LOWER(Keyword) = '".$keyw."' LIMIT 1 ";
                                        $get_result = row_array($sql);                                          
                                        $CurrentRank = isset($get_result['CurrentRank'])?$get_result['CurrentRank']:'N/A';                                        
                                        
                                        if($CurrentRank != 'N/A'){
                                            if ($CurrentRank <= 0) {
                                                $CurrentRank = '50+';
                                            }
                                        }
                                        
                                        $ik++;
                                        ?>                                
                                        <tr>
                                            <td style="width: 250px;"><?php echo $k; ?> (S)
                                            <div>Current Broad Rank : <?php echo $CurrentRank; ?></div>
                                            </td>                                   
                                            <td style="width: 150px;">
                                                <?php if($synom->content->occurence > 0){
                                                     echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                                } else {
                                                    echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                                } ?>
                                                <div>
                                                    Occurrence : <?php echo $synom->content->occurence; ?>
                                                </div>
                                                <div>
                                                    Density : <?php echo $synom->content->density; ?>
                                                </div>
                                            </td>
                                            <td style="width: 150px;">
                                                <?php if($synom->title->occurence > 0){
                                                     echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                                } else {
                                                    echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                                } ?>

                                                <div>
                                                    Occurrence : <?php echo $synom->title->occurence; ?>
                                                </div>
                                                <div>
                                                    Density : <?php echo $synom->title->density; ?>
                                                </div>
                                            </td>
                                            <td style="width: 150px;">
                                                <?php if($synom->desc->occurence > 0){
                                                     echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                                } else {
                                                    echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                                } ?>

                                                <div>
                                                    Occurrence : <?php echo $synom->desc->occurence; ?>
                                                </div>
                                                <div>
                                                    Density : <?php echo isset($synom->desc->density)?$synom->desc->density:'0%'; ?>
                                                </div>
                                            </td>
                                            <td style="width: 150px;">
                                                <?php if($synom->headings->occurence > 0){
                                                     echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                                } else {
                                                    echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                                } ?>
                                                 <div>
                                                    Occurrence : <?php echo $synom->headings->occurence; ?>
                                                </div>
                                            </td>
                                            <td style="width: 150px;">
                                                <?php echo $overalltick; ?>
                                                &nbsp;&nbsp;<?php echo $overalldens; ?>                              
                                            </td>
                                        </tr>
                                
                                        <?php
                                        
                                    }
                                }
                                
                                if($ik > 0){
                                    
                                    $overallptick = "<img class='imgoffon' title='please fix this error' src='$ofimg' />";                                                                                                            
                                    $Densitypval = ($alltotal_Nkr / $alltotal_Tkn) * 100;
                                    $overalldenspval = round($Densitypval,2).'%';
                                    if($overalldenspval >= $OVERALL_PRIMARY_DENSITY){
                                        $overallptick = "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                    }                                    
                                    
                                    ?>
                                        <tr class="rowspecial">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>Overall Density</td>
                                            <td>Primary + Synonym = </td>
                                            <td>
                                                <?php echo $overallptick; ?>
                                                &nbsp;&nbsp;<?php echo $overalldenspval; ?>                              
                                            </td>
                                        <tr/>
                                    <?php
                                }
                                
                                ?>
                                
                                
                                <?php
                            }
                            
                            if($ix == 0){
                                
                                if($age == "NA"){
                                   ?>
                                    <tr>
                                        <td colspan="6">Not A Target Page</td>                                    
                                    </tr>
                                    <?php 
                                }
                                else{
                                    ?>
                                    <tr>
                                        <td colspan="6">No Campaign has been created</td>                                    
                                    </tr>
                                    <?php
                                }
                                
                            }
                            ?>
                        </tbody>
                    </table>
                    
                    
                    <div class="row"><hr/></div>
                    <table class="table table-bordered">
                        <caption>Discovered Keywords</caption>
                        <thead>
                            <tr>
                                <th style="width: 250px;">Keyword</th>                               
                                <th style="width: 150px;">Content</th>
                                <th style="width: 150px;">Title</th>
                                <th style="width: 150px;">Meta Description</th>
                                <th style="width: 150px;">Heading Tags</th>
                                <th style="width: 150px;">Overall Density</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ix = 0;
                                                        
                            $UserID = $user_id;
                            $user_website = get_user_meta($user_id, 'website', true);
                            $fulltrimmedweb = str_replace(array("http://","https://","www."), array("","",""), trim(trim($user_website,"/")));
                            
                            
//                            $keywordDat = get_user_meta($UserID, "Content_keyword_Site", true);
//                            if (!empty($keywordDat)) {
//                                $Synonyms_keyword = $keywordDat["Synonyms_keyword"];
//                                $primarylander = $keywordDat["primarylander"];
//                                $secondarylander = $keywordDat["secondarylander"];
//                                $Additionalsnotes = $keywordDat["Additionalsnotes"];
//
//                                $activation = $keywordDat["activation"];
//                                $target_keyword = $keywordDat["target_keyword"];
//                                $delete = $keywordDat["delete"];
//
//                                $landingpage = $keywordDat["landing_page"];
//                                $livedate = $keywordDat['live_date'];
//                            } else {
//                                $keywordDat['keyword_count'] = 0;
//                            }
//                            
//                            
//                            $sql = "SELECT uh.status, u.meta_key, u.meta_value FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id`WHERE uh.`user_id` = $UserID && `meta_value` != '' ORDER BY `uh`.`update_date`";
//                            $KeyWordQuery = $wpdb->get_results($sql);
//
//                            if (empty($KeyWordQuery)) {
//                                
//                                $sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" && `meta_value` != "" order by `meta_value` ';
//                                $KeyWordQuery = $wpdb->get_results($sql);
//
//                                $sql = 'SELECT meta_key, meta_value FROM wp_usermeta WHERE user_id = ' . $UserID . ' AND meta_key LIKE "LE_Repu_Keyword_%" && `meta_value` = "" ORDER BY `meta_key` ASC';
//                                $null_key_index = $wpdb->get_results($sql);
//                                
//                            }
//                            else{
//                                $sql = "SELECT uh.status, u.meta_key, u.meta_value FROM `wp_keywords_update_history` uh INNER JOIN wp_usermeta u ON u.`meta_key` = uh.`keyword_key` && u.`user_id` = uh.`user_id`WHERE uh.`user_id` = $UserID && `meta_value` = '' ORDER BY `uh`.`update_date` ";
//                                $null_key_index = $wpdb->get_results($sql);
//                            }
//                            
//                            $KeyWordQuery = array_merge($KeyWordQuery, $null_key_index);
//                                                       
//                            foreach ($KeyWordQuery as $KeyWordQ => $KeyWordData) {
//                                $ks = str_replace("LE_Repu_Keyword_", "", $KeyWordData->meta_key);
//                                $j = $ks - 1;
//                                $sykey = trim(strtolower($KeyWordData->meta_value)); 
//                                if($sykey != '')
//                                    $keywordalreadysvaed["$sykey"] = isset($KeyWordData->status)?strtolower(trim($KeyWordData->status)):'active';
//                                
//                                for ($h = 0; $h < 5; $h++) { 
//                                    $sykey = trim(strtolower($Synonyms_keyword[$j][$h]));   
//                                    if($sykey != '')
//                                        $keywordalreadysvaed["$sykey"] = isset($KeyWordData->status)?strtolower(trim($KeyWordData->status)):'active';
//                                }
//                            }
                               
                            
                            $sql = 'SELECT k.keyword FROM wp_keywords k inner join wp_keygroup g '
                                    . 'ON k.group_id = g.id WHERE g.status = 1 and k.location_id = '.$user_id;
                            $allresult = $wpdb->get_results($sql);                              
                            foreach($allresult as $allresul){
                                $keywordalreadysvaed["$allresul->keyword"] = 'active';
                            }
                            
                            foreach($analys->keywords as $key => $keydata){
                                if($keydata->content->type == 'primary' || $keydata->content->type == 'synonym'){
                                    continue;
                                }
                                $ix++;
                                
                                $overalldens = 0; $overalltick = "<img class='imgoffon' title='please fix this error' src='$ofimg' />";                          
                                if(isset($keydata->content->Nkr)){
                                    $alltotal_Nkr = $total_Nkr = $keydata->content->Nkr + $keydata->title->Nkr + $keydata->desc->Nkr;
                                    $alltotal_Tkn = $total_Tkn = $keydata->content->Tkn + $keydata->title->Tkn + $keydata->desc->Tkn;
                                    $Density = ($total_Nkr / $total_Tkn) * 100;
                                    $overalldens = round($Density,2).'%';
                                    if($overalldens >= $OVERALL_KEY_DENSITY){
                                        $overalltick = "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                    }
                                }
                                
                                $keyw = strtolower(trim($key));
                                $sql = 'SELECT rankdetail FROM `wp_keywords` WHERE LOWER(keyword) = "' . $keyw . ' And location_id = '.$user_id.'" LIMIT 1 ';
                                $get_result = $wpdb->get_row($sql);  
                                $CurrentRank = 'N/A';
                                
                                if(!empty($get_result)){
                                    $rankdt = $get_result->rankdetail;
                                    if(!empty($rankdt)){
                                        $rankdt = json_decode($rankdt);                                        
                                        foreach ($rankdt->googleOrganicRankings as $hgorganic) {
                                            if (isset($hgorganic->rankedUrl) && strpos($hgorganic->rankedUrl, $fulltrimmedweb) !== false) {
                                                if($hgorganic->resultType == 'OrganicResult'){
                                                    $CurrentRank = $hgorganic->rank;                                                    
                                                }
                                            }
                                        }
                                        
                                    }
                                }
                                                                
                                if($CurrentRank != 'N/A'){
                                    if ($CurrentRank <= 0) {
                                        $CurrentRank = '50+';
                                    }
                                }
                                ?>
                                <tr>
                                    <td style="width: 250px;">                                                                                                                            
                                        <?php echo $key; ?>          
                                        <div>Current Broad Rank : <?php echo $CurrentRank; ?></div>
                                        <div>
                                            <?php
                                            if(!array_key_exists(trim(strtolower($key)),$keywordalreadysvaed)){
                                                ?>
                                                <a title="Add Keyword" data-key="<?php echo $key; ?>" class="addinkeywordlnk">(Not a keyword) + Add</a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php if($keydata->content->occurence > 0){
                                             echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                        } else {
                                            echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                        } ?>
                                        <div>
                                            Occurrence : <?php echo $keydata->content->occurence; ?>
                                        </div>
                                        <div>
                                            Density : <?php echo $keydata->content->density; ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php if($keydata->title->occurence > 0){
                                             echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                        } else {
                                            echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                        } ?>
                                        
                                        <div>
                                            Occurrence : <?php echo $keydata->title->occurence; ?>
                                        </div>
                                        <div>
                                            Density : <?php echo $keydata->title->density; ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php if($keydata->desc->occurence > 0){
                                             echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                        } else {
                                            echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                        } ?>
                                        
                                        <div>
                                            Occurrence : <?php echo $keydata->desc->occurence; ?>
                                        </div>
                                        <div>
                                            Density : <?php echo isset($keydata->desc->density)?$keydata->desc->density:'0%'; ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php if($keydata->headings->occurence > 0){
                                             echo "<img class='imgoffon' title='This is looking fine' src='$onimg' />";
                                        } else {
                                            echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />";
                                        } ?>
                                         <div>
                                            Occurrence : <?php echo $keydata->headings->occurence; ?>
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <?php echo $overalltick; ?>
                                        &nbsp;&nbsp;<?php echo $overalldens; ?>                              
                                    </td>
                                </tr>
                                <?php
                            }
                            if($ix == 0){
                                ?>
                                <tr>
                                    <td colspan="6">No Keyword Discovered</td>                                    
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
            
            

        </div>

    </div>
</div>


<script>
    jQuery(function () {
        var options_circle = {
            chart: {
                events: {
                    drilldown: function (e) {
                        if (!e.seriesOptions) {
                            var chart = this;
                            // Show the loading label
                            chart.showLoading('Loading ...');
                            setTimeout(function () {
                                chart.hideLoading();
                                chart.addSeriesAsDrilldown(e.point, series);
                            }, 1000);
                        }

                    }
                },
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '',
                style: {
                    display: 'none'
                }
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Keyword Number'
                }
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                series: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    format: '{point.y} bytes',
                    dataLabels: {
                        enabled: true,
                    },
                    tooltip: {
                        pointFormat: '{point.name}: <b>{point.y} bytes</b>'
                    },
                    legend: false
                },
                pie: {
                    dataLabels: {
                        enabled: true,
                        //distance: -20,
                        format: '{point.y} bytes',
                        style: {
                            fontWeight: 'bold',
                            color: '#000',
                        }
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y} bytes</b>'
                    },
                    showInLegend: true
                }
            },
            series: [{
                    name: 'Size',
                    innerSize: '50%',
                    colorByPoint: true,
                    data: [
                        {
                            name: 'HTML',
                            y: <?php echo $analys->page_size; ?>,
                        },
                        {
                            name: 'CSS',
                            y: <?php echo $analys->arcss->css_size; ?>,
                        },
                        {
                            name: 'Javascript',
                            y: <?php echo $analys->js->js_size; ?>,
                        },
                        {
                            name: 'Images',
                            y: <?php echo $analys->images->img_size; ?>,
                        },
                    ]
                }],
        };
        options_circle.chart.renderTo = 'container_graph';
        options_circle.chart.type = 'pie';
        var chart1 = new Highcharts.Chart(options_circle);
        chartfunc = function (chart_type) {
            options_circle.chart.renderTo = 'container_graph';
            options_circle.chart.type = chart_type;
            var chart1 = new Highcharts.Chart(options_circle);
        }
        
        var wid = jQuery('.urldatacontent').width();
        var col1wid = $('.col1div').width() + 70; // 70 outer width of columns
        var col2wid = wid - col1wid;
        jQuery('.col2div').css('width',col2wid);

    });

</script>

