<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/highcharts/js/highcharts.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/report-theme/assets/global/plugins/highcharts/js/modules/data.js"></script>

<div id="primary" class="site-content" style="min-height: 400px">
    
    <div id="content" role="main">

        <div class='col-md-12'>
            <h4>CRE Page Dashboard                    
                <div class="pull-right">
                    <a href="javascript:;" class="backtodashboard btn btn-danger">Back To Main Dashboard</a>
                </div>
            </h4>            
            
            <div class='row'>
                <div class='col-md-12'>                        
                    <div>
                        <strong>
                            Focus Keyword : <?php echo $analys->keyword; ?>
                        </strong>
                        <div class="pull-right">
                            <a href="" class="btn btn-primary">Download Report</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div>
                        <strong>
                            Target URL : <a target="_blank" href="<?php echo $analys->url; ?>"><?php echo $analys->url; ?></a>
                        </strong>
                        <div class="pull-right">
                            Last Run : <?php echo date('D d M Y, h:i a', strtotime($rcommend->rundate)); ?>
                        </div>
                    </div>                    
                    
                </div>                
            </div>
            
            
            <div class='row'>
                <div class='col-md-12'>           
                    
                    <div class="panel-group margin_top_10">
                        
                        
                        <div class="panel panel-primary">
                          <div class="panel-heading">Page Title</div>
                          <div class="panel-body">
                              
                              <ul class="list-group">
                                 
                                  <li class="list-group-item">
                                      <div class="firstspan">Title : </div> <div class="secondspan"><?php echo $analys->title->title != ''?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>  Title Found</div>
                                      <?php if(isset($analys->title->title) && $analys->title->title != ''){
                                          ?>
                                            <div class="margin_top_10">Title : <?php echo $analys->title->title; ?></div>
                                          <?php
                                      } ?>
                                            
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> The title is an important factor in the on-site search engine optimization. Not only uses the search engines the title for the keywords, the title is also used for display in the SERP (Search Engine Result Page). For best practice make use of your main keywords in the title. 
                                        </div>                                            
                                  </li>                                                                    
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Title Length : </div> <div class="secondspan"><?php echo $analys->title->title_valid == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>  <?php echo $analys->title->title_length; ?> characters</div>
                                      <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> The <?php echo htmlspecialchars('<TITLE>'); ?> element provides a short piece of text describing the document. The title is very important as it shows in the window title bars, bookmarks and search results. Title should be between <?php echo PAGE_TITLE_RANGE; ?> characters long.
                                        </div>
                                  </li>
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Title Relevancy : </div> <div class="secondspan"><?php echo $analys->title->title_relevant == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />  Relevant Title":"<img class='imgoffon' title='please fix this error' src='$ofimg' /> Title Not Relevant"; ?></div>
                                          <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Title relevancy checked on basis of keyword match in title.
                                        </div>
                                </li>
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Keyword Density: </div> <div class="secondspan">
                                          <?php echo intval($analys->title->title_key_density) > $cre->maxkeydens?"<img class='imgoffon' title='This is looking fine' src='$onimg' /> ":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                          
                                          <?php echo $analys->title->title_key_density; ?></div>
                                      <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Keywords density and consistency are notable factors for optimal page SEO. Keyword density of above <?php echo KEYWORD_TITLE_DENSITY; ?>% is a good indication.
                                        </div>
                                  </li>
                              </ul>
                              
                          </div>
                        </div>
                        
                       
                        <div class="panel panel-primary">
                          <div class="panel-heading">Meta Description</div>
                          <div class="panel-body">
                              
                             
                                <ul class="list-group">
                                                                    
                                  <li class="list-group-item">
                                      <div class="firstspan">Description : </div> <div class="secondspan"><?php echo $analys->desc->meta_desc != ''?"<img class='imgoffon' title='This is looking fine' src='$onimg' /> Meta Description Found":"<img class='imgoffon' title='please fix this error' src='$ofimg' /> Meta Description Not Found"; ?>  </div>
                                      <?php if(isset($analys->desc->meta_desc) && $analys->desc->meta_desc != ''){
                                          ?>
                                            <div class="margin_top_10">Description : <?php echo $analys->desc->meta_desc; ?></div>
                                          <?php
                                      } ?>
                                            
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> The meta description is an factor in the on-site search engine optimization. Not only uses the search engines the description for the keywords, the description is used frequently for display in the SERP (Search Engine Result Page). For best practice describe where your webpage is about in the description.
                                        </div>                                            
                                  </li>
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Description Length : </div> <div class="secondspan"><?php echo $analys->desc->desc_valid == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>  <?php echo $analys->desc->desc_length; ?> characters</div>
                                      <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> 
                                            Meta Description should between <?php echo PAGE_DESC_RANGE; ?> characters
                                        </div>
                                  </li>
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Relevancy : </div> <div class="secondspan"><?php echo $analys->desc->desc_relevant == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>
                                      <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Description relevancy checked on basis of keyword match in meta description.
                                        </div>
                                  </li>
                                                                    
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Keyword Density: </div> <div class="secondspan">
                                          <?php echo intval($analys->desc->desc_key_density) > $cre->maxkeydens?"<img class='imgoffon' title='This is looking fine' src='$onimg' /> ":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                          
                                          <?php echo $analys->desc->desc_key_density; ?></div>
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Keywords density and consistency are notable factors for optimal page SEO. Keyword density of above <?php echo KEYWORD_DESC_DENSITY; ?>% is a good indication.
                                        </div>
                                  </li>
                                  
                              </ul>
                                
                           
                              
                          </div>
                        </div>    
                        
                        <div class="panel panel-primary">
                          <div class="panel-heading">Page Content</div>
                          <div class="panel-body">
                              <ul class="list-group">
                                  
                                  <?php
                                  
                                  $cre = get_option('credata');
                                  $cre = json_decode(json_encode($cre));
                                  
                                  if(isset($analys->totalpagesize) > 0){
                                      $total_pagesize = $analys->totalpagesize;
                                  } else{
                                      $total_pagesize = $analys->page_size + $analys->arcss->css_size + $analys->js->js_size + $analys->images->img_size;
                                   }
                                  
                                  ?>                                  
                                  <li class="list-group-item">
                                    <div class="firstspan">Page Size : </div> <div class="secondspan"><?php echo $analys->pagesizevalid == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                    <div><?php echo $total_pagesize; ?> bytes</div>  
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="notebottom alert alert-info">
                                        <strong>Note: </strong> Page size should not more than <?php echo $cre->maxpagesize; ?> bytes (<?php echo bytesformat($cre->maxpagesize); ?>)
                                    </div>
                                </li> 
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Page Breakdown : </div> <div class="secondspan">
                                        <div id="container_graph" style="height:300px; width: 600px;"></div>
                                      </div>
                                  </li> 
                                  <li class="list-group-item">
                                      <div class="firstspan">Total Words : </div> <div class="secondspan"><?php echo $analys->content->total_words; ?>
                                      &nbsp;&nbsp;<div><?php echo $analys->content->content_valid == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>
                                      </div>
                                    <div class="clearfix"></div>
                                    <div class="notebottom alert alert-info">
                                        <strong>Note: </strong> The best amount of content for a article on a webpage is between <?php echo PAGE_CONTENT_RANGE; ?> words.
                                    </div>
                                      
                                  </li>                                                                    
                                  
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Keyword Density: </div> <div class="secondspan">
                                          
                                          <?php echo intval($analys->content->content_key_density) > $cre->maxcontentdensity?"<img class='imgoffon' title='This is looking fine' src='$onimg' /> ":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                          
                                          <?php echo $analys->content->content_key_density; ?></div>
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Keywords density and consistency are notable factors for optimal page SEO. Keyword density of above <?php echo KEYWORD_CONTENT_DENSITY; ?>% is a good indication.
                                        </div>
                                  </li>
                                  <li class="list-group-item">
                                      <div class="firstspan">Load Time : </div> <div class="secondspan">
                                      <?php echo $analys->validloadtime == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                      <div> <?php echo number_format($analys->page_speed, 2); ?> Seconds</div> 
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="notebottom alert alert-info">
                                        <strong>Note: </strong> Content loading time should between <?php echo AVG_LOADING_TIME; ?> seconds
                                    </div>
                                  </li>
                                  <li class="list-group-item">                                      
                                        <div class="firstspan">SEO Friendly URL : </div> <div class="secondspan"><?php echo $analys->seo_friendly == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> SEO Friendly URLs are easy to read and understand for crawlers
                                        </div>
                                  </li>
                                  <li class="list-group-item">
                                      <div class="firstspan">DocType Available : </div> <div class="secondspan"><?php echo $analys->doctpye == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>
                                      
                                      <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> The <?php echo htmlspecialchars("<!DOCTYPE>") ; ?> declaration tells the web browser about what version of HTML the page is written in. It is good practice to always add the <!DOCTYPE> declaration to the HTML documents, so that the browser knows what type of document to expect.
                                        </div>
                                      
                                      
                                  </li>
                                  <li class="list-group-item">
                                        <div class="firstspan">Any Iframe : </div> <div class="secondspan"><?php echo $analys->iframe == 0?"<img class='imgoffon' title='This is looking fine' src='$onimg' /> No iframe found":"<img class='imgoffon' title='please fix this error' src='$ofimg' /> Iframe found"; ?></div>
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> It is not recommended to use frames or iframes because they can cause problems for search engines. It is best to avoid frames and inline frames whenever possible.
                                        </div>
                                  
                                  </li>
                                  <li class="list-group-item">
                                      <div class="firstspan">Canonical Tag : </div> <div class="secondspan"><?php echo $analys->canonical_tag == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?></div>
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Canonical tag help web-masters to prevent duplicate content issues on page.
                                        </div>
                                  </li>
                                  <li class="list-group-item">
                                      <div class="firstspan">Favicon : </div> <div class="secondspan"><?php echo $analys->favicon == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                          <?php if($analys->favicon == 1){
                                              ?>
                                              <img height="20" width="20" src="<?php echo $analys->favicon_img; ?>" />
                                            <?php
                                          } ?>
                                      </div>
                                      <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> The Favicon is a small icon associated with a website. The Favicon is important because it is displayed next to the website's URL in the address bar of the browser as well as in bookmarks and shortcuts.
                                        </div>
                                  </li>
                                  
                                  <li class="list-group-item">
                                        <div class="firstspan">Robots Meta Tag : </div> <div class="secondspan"><?php echo $analys->robots_meta_tag == 1?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>                                         
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Spider robots are not allowed to display a title and description from the Open Directory Project in the search results. 
                                        </div>
                                  </li>
                                  
                              </ul>
                              
                          </div>
                        </div>
                        
                        <div class="panel panel-primary">
                          <div class="panel-heading">Headings</div>
                          <div class="panel-body">
                              <ul class="list-group">              
                                  <li class="list-group-item">
                                      <?php
                                        $in_tag = "";
                                        if(isset($analys->headings->h1_keyword)){
                                              $in_tag .= "<p class='htagp'>". count($analys->headings->h1_keyword) ." time(s) in &lt;H1&gt; </p>";
                                        }

                                        if(isset($analys->headings->h2_keyword)){
                                            $in_tag .= "<p class='htagp'>". count($analys->headings->h2_keyword) ." time(s) in &lt;H2&gt; </p>";
                                        }

                                        if(isset($analys->headings->h3_keyword)){
                                            $in_tag .= "<p class='htagp'>". count($analys->headings->h3_keyword) ." time(s) in &lt;H3&gt; </p>";
                                        }

                                        if(isset($analys->headings->h4_keyword)){
                                            $in_tag .= "<p class='htagp'>". count($analys->headings->h4_keyword) ." time(s) in &lt;H4&gt; </p>";
                                        }

                                        if(isset($analys->headings->h5_keyword)){
                                            $in_tag .= "<p class='htagp'>". count($analys->headings->h5_keyword) ." time(s) in &lt;H5&gt; </p>";
                                        }

                                        if(isset($analys->headings->h6_keyword)){
                                            $in_tag .= "<p class='htagp'>". count($analys->headings->h6_keyword) ." time(s) in &lt;H6&gt; </p>";
                                        }
                                      ?>
                                      <div class="firstspan">Keyword Presence In <?php echo "&lt;H&gt;"; ?> : </div> <div class="secondspan">
                                          
                                          <span><?php echo $analys->headings->keyword_in_headings; ?></span>
                                          
                                      
                                      &nbsp;&nbsp;<div><?php echo $analys->headings->keyword_in_headings > 0 ? "<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                                                            
                                      <?php
                                      if($in_tag != ""){
                                          echo '<br/>'.$in_tag; 
                                      }                                      
                                      ?>
                                        </div>
                                      </div>
                                      
                                      <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Each page should have contained at least one tag (H1 to H6 tags). Heading tags helps to Improve ranking in search engines.
                                            <br/> Heading tag length should range between <?php echo HEADING_LENGTH; ?> characters.                                            
                                        </div>
                                      
                                  </li>
                                  <li class="list-group-item">
                                      <div class="firstspan">Total H1 Tags : </div> <div class="secondspan">
                                          <span><?php echo $analys->headings->totalh1; ?></span>
                                      &nbsp;&nbsp;<div>
                                      <?php
                                      $k = 0;
                                      if(isset($analys->headings->h1)){
                                          echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />"."<br/>";
                                          $totalhead = count($analys->headings->h1);
                                          echo 'Total '.$totalhead.' H1 tags have more length of content.';
                                          foreach($analys->headings->h1 as $tag){
                                              echo "<p class='htagp'>". htmlspecialchars('<h1>'.$tag.'</h1>').'</p>';
                                              $k++;
                                          }
                                      }                                      
                                      ?>                                                                             
                                      </div>
                                      </div>                                                                                                                  
                                      <?php
                                      if($k > 0){
                                          ?>
                                          <div class="clearfix"></div>
                                            <div class="notebottom alert alert-info">
                                                <strong>Note: </strong> Length of above heading tags is not between <?php echo HEADING_LENGTH; ?> characters.
                                            </div>
                                          <?php
                                      }
                                      ?>
                                      
                                  </li>
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Total H2 Tags : </div> <div class="secondspan">
                                          <span><?php echo $analys->headings->totalh2; ?></span>
                                      &nbsp;&nbsp;<div>
                                      <?php
                                      $k = 0;
                                      if(isset($analys->headings->h2)){
                                          echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />"."<br/>";
                                          $totalhead = count($analys->headings->h2);
                                          echo 'Total '.$totalhead.' H2 tags have more length of content.';
                                          foreach($analys->headings->h2 as $tag){
                                              echo "<p class='htagp'>". htmlspecialchars('<h2>'.$tag.'</h2>').'</p>';
                                              $k++;
                                          }
                                      }
                                     
                                      ?>                                                                             
                                      </div>
                                      </div>
                                      
                                      <?php
                                      if($k > 0){
                                          ?>
                                          <div class="clearfix"></div>
                                            <div class="notebottom alert alert-info">
                                                <strong>Note: </strong> Length of above heading tags is not between <?php echo HEADING_LENGTH; ?> characters.
                                            </div>
                                          <?php
                                      }
                                      ?>
                                  </li>
                                  
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Total H3 Tags : </div> <div class="secondspan">
                                          <span><?php echo $analys->headings->totalh3; ?></span>
                                      &nbsp;&nbsp;<div>
                                      <?php
                                      $k = 0;
                                      if(isset($analys->headings->h3)){
                                          echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />"."<br/>";
                                          $totalhead = count($analys->headings->h3);
                                          echo 'Total '.$totalhead.' H3 tags have more length of content.';
                                          foreach($analys->headings->h3 as $tag){
                                              echo "<p class='htagp'>". htmlspecialchars('<h3>'.$tag.'</h3>').'</p>';
                                              $k++;
                                          }
                                      }
                                     
                                      ?>                                                                             
                                      </div>
                                      </div>
                                      
                                      <?php
                                      if($k > 0){
                                          ?>
                                          <div class="clearfix"></div>
                                            <div class="notebottom alert alert-info">
                                                <strong>Note: </strong> Length of above heading tags is not between <?php echo HEADING_LENGTH; ?> characters.
                                            </div>
                                          <?php
                                      }
                                      ?>
                                  </li>
                                  
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Total H4 Tags : </div> <div class="secondspan">
                                          <span><?php echo $analys->headings->totalh4; ?></span>
                                      &nbsp;&nbsp;<div>
                                      <?php
                                      $k = 0;
                                      if(isset($analys->headings->h4)){
                                          echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />"."<br/>";
                                          $totalhead = count($analys->headings->h4);
                                          echo 'Total '.$totalhead.' H4 tags have more length of content.';
                                          foreach($analys->headings->h4 as $tag){
                                              echo "<p class='htagp'>". htmlspecialchars('<h4>'.$tag.'</h4>').'</p>';
                                              $k++;
                                          }
                                      }
                                     
                                      ?>                                                                             
                                      </div>
                                      </div>
                                      
                                      <?php
                                      if($k > 0){
                                          ?>
                                          <div class="clearfix"></div>
                                            <div class="notebottom alert alert-info">
                                                <strong>Note: </strong> Length of above heading tags is not between <?php echo HEADING_LENGTH; ?> characters.
                                            </div>
                                          <?php
                                      }
                                      ?>
                                  </li>
                                  
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Total H5 Tags : </div> <div class="secondspan">
                                          <span><?php echo $analys->headings->totalh5; ?></span>
                                      &nbsp;&nbsp;<div>
                                      <?php
                                      $k = 0;
                                      if(isset($analys->headings->h5)){
                                          echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />"."<br/>";
                                          $totalhead = count($analys->headings->h5);
                                          echo 'Total '.$totalhead.' H5 tags have more length of content.';
                                          foreach($analys->headings->h5 as $tag){
                                              echo "<p class='htagp'>". htmlspecialchars('<h5>'.$tag.'</h5>').'</p>';
                                              $k++;
                                          }
                                      }
                                      
                                      ?>                                                                             
                                      </div>
                                      </div>
                                      
                                      <?php
                                      if($k > 0){
                                          ?>
                                          <div class="clearfix"></div>
                                            <div class="notebottom alert alert-info">
                                                <strong>Note: </strong> Length of above heading tags is not between <?php echo HEADING_LENGTH; ?> characters.
                                            </div>
                                          <?php
                                      }
                                      ?>
                                  </li>
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Total H6 Tags : </div> <div class="secondspan">
                                          <span><?php echo $analys->headings->totalh6; ?></span>
                                      &nbsp;&nbsp;<div>
                                      <?php
                                      $k = 0;
                                      if(isset($analys->headings->h6)){
                                          echo "<img class='imgoffon' title='please fix this error' src='$ofimg' />"."<br/>";
                                          $totalhead = count($analys->headings->h6);
                                          echo 'Total '.$totalhead.' H6 tags have more length of content.';
                                          foreach($analys->headings->h6 as $tag){
                                              echo "<p class='htagp'>". htmlspecialchars('<h6>'.$tag.'</h6>').'</p>';
                                              $k++;
                                          }
                                      }
                                      
                                      ?>                                                                             
                                      </div>
                                      </div>
                                      
                                      <?php
                                      if($k > 0){
                                          ?>
                                          <div class="clearfix"></div>
                                            <div class="notebottom alert alert-info">
                                                <strong>Note: </strong> Length of above heading tags is not between <?php echo HEADING_LENGTH; ?> characters.
                                            </div>
                                          <?php
                                      }
                                      ?>
                                  </li>
                                  
                                  
                                  
                              </ul>
                              
                              
                          </div>
                        </div>
                        
                        <div class="panel panel-primary">
                          <div class="panel-heading">Links</div>
                          <div class="panel-body">
                              
                              <ul class="list-group">                                 
                                  <li class="list-group-item">
                                      <div class="firstspan">Total Internal Links : </div> <div class="secondspan"><?php echo count($analys->links->internal_links); ?>
                                      &nbsp;&nbsp;<div><a href="javascript:;" onclick="jQuery('.modalinternallinks').modal();" class="internallinks">Click to see internal links</a> </div>
                                      </div>
                                  </li>
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Total External Links : </div> <div class="secondspan"><?php echo count($analys->links->external_links); ?>
                                      &nbsp;&nbsp;<div><a href="javascript:;" onclick="jQuery('.modalexternallinks').modal();"  class="externallinks">Click to see external links</a> </div>
                                      
                                     <?php echo $analys->links->exceed_external_links == 0?"<img class='imgoffon' title='This is looking fine' src='$onimg' />":"<img class='imgoffon' title='please fix this error' src='$ofimg' />"; ?>
                                      </div>
                                      
                                    <div class="clearfix"></div>
                                    <div class="notebottom alert alert-info">
                                        <strong>Note: </strong> According to seo, we can not define more than 6 external links
                                    </div>
                                      
                                  </li>
                                  
                                  <li class="list-group-item">
                                      <div class="firstspan">Total Broken Links : </div> <div class="secondspan"><?php echo isset($analys->links->broken_links)?count($analys->links->broken_links):0; ?>
                                      <?php
                                      echo "&nbsp;&nbsp;";
                                      foreach($analys->links->broken_links as $lnk){
                                          echo $lnk.'<br/>'; 
                                      }                                      
                                      ?>                                
                                      </div>
                                        <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> Broken links made bad effect on seo as well as on user experience 
                                        </div>
                                  </li>
                              </ul>
                              
                          </div>
                        </div>
                        
                        
                        <div class="panel panel-primary">
                          <div class="panel-heading">Images</div>
                          <div class="panel-body">
                              
                              <ul class="list-group">                                 
                                  <li class="list-group-item">
                                      <div class="firstspan">Total Images : </div> <div class="secondspan"><?php echo $analys->images->total_images; ?>
                                      
                                          &nbsp;&nbsp; = <div><?php echo $analys->images->total_images - count($analys->images->alt_miss); ?> <img class='imgoffon' src='<?php echo $onimg; ?>' /></div>
                                          &nbsp;&nbsp;<div><?php echo count($analys->images->alt_miss); ?> <img class='imgoffon' src='<?php echo $ofimg; ?>' /></div>
                                      </div>                                      
                                      
                                      <div class="clearfix"></div>
                                        <div class="notebottom alert alert-info">
                                            <strong>Note: </strong> The "alt" attribute provides a text equivalent for the image. If the browser cannot display an image the alt description will be given in its place. Furthermore, some visitors cannot see images as they might be blind in which the alt tag provides a valuable image description. Finally, search engines utilize the alt attribute for image search indexing.
                                        </div>

                                      
                                  </li>
                                  
                                  <li class="list-group-item">
                                    <div class="firstspan">Missing Alt Tag : </div> <div class="secondspan">
                                        <span><?php echo count($analys->images->alt_miss); ?></span>
                                    <div>                                      
                                    <?php if(count($analys->images->alt_miss) > 0) { 
                                        
                                        foreach($analys->images->alt_miss as $miss){
                                            echo "<a href='$miss'>$miss</a>";
                                            echo "<br/>";
                                        }
                                        ?>
                                        
                                        <?php
                                    }
                                    ?>
                                        </div>
                                    </div>
                                     <?php if(count($analys->images->alt_miss) > 0){ ?>
                                    <div class="clearfix"></div>
                                    <div class="notebottom alert alert-info">
                                        <strong>Note: </strong> Above you can find <?php echo count($analys->images->alt_miss); ?> images with missing alt tag
                                    </div>
                                     <?php } ?>
                                  </li>
                                      
                              </ul>
                              
                          </div>
                        </div>
                        
                        
                      </div>
                    
                </div>
            </div>
            
        </div>

    </div>
</div>


<div class="modal fade modalinternallinks" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
        <h4 class="modal-title">Internal Links</h4>
      </div>
      <div class="modal-body">
        <?php
        if(!isset($analys->links->internal_links) || count($analys->links->internal_links) == 0){
            echo "<div>No Link Found</div>";
        }
        else{
            echo "<ul class='list-group'>";
            foreach($analys->links->internal_links as $link){
                echo "<li class='list-group-item'><a target='_blank' href='$link'>$link</a></li>";
            }
            echo "</ul>";
        }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade modalexternallinks" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><div aria-hidden="true">&times;</div></button>
        <h4 class="modal-title">External Links</h4>
      </div>
      <div class="modal-body">
        <?php
        if(!isset($analys->links->internal_links) || count($analys->links->internal_links) == 0){
            echo "<div>No Link Found</div>";
        }
        else{
            echo "<ul class='list-group'>";
            foreach($analys->links->external_links as $link){
                echo "<li class='list-group-item'><a target='_blank' href='$link'>$link</a></li>";
            }
            echo "</ul>";
        }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>      
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    jQuery(function() {
        var options_circle = {
            chart: {
                events: {
                    drilldown: function(e) {
                        if (!e.seriesOptions) {
                            var chart = this;
                            // Show the loading label
                            chart.showLoading('Loading ...');
                            setTimeout(function() {
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
        chartfunc = function(chart_type) {
            options_circle.chart.renderTo = 'container_graph';
            options_circle.chart.type = chart_type;
            var chart1 = new Highcharts.Chart(options_circle);
        }

    });

</script>