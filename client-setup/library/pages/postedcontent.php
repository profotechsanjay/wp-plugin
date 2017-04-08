<?php
                    global $wpdb;
                    $keywordDat = get_user_meta($UserID, "Content_keyword_Site", true);
                    $landingpage = $keywordDat["landing_page"];
                    $incriment = 1;
                    foreach ($landingpage as $landingdata) {

                        foreach ($landingdata as $landingfinalvalue) {
                            if (!empty($landingfinalvalue))
                                $landingfinalArr[$incriment] = $landingfinalvalue;
                        }
                        $incriment++;
                    }
                    /* 24-Jul */
                    $path = $_SERVER['DOCUMENT_ROOT'];
                    $msg = '';
                    if (isset($_POST['pcsubmit'])) {

                        $total_post = count($_POST['url']);
                        if (get_user_meta($UserID, "Posted Content", true) != "")
                            $merge_post_content = array_merge_recursive(get_user_meta($UserID, "Posted Content", true), $_POST);
                        else
                            $merge_post_content = $_POST;

                        //echo '<pre>'; print_r($_POST);exit;
                        if (!empty($_POST['url'][0]) && !empty($_POST['keyword1'][0]) && !empty($_POST['dl'][0]) && !empty($_POST['ss'][0]) && !empty($_POST['il'][0])) { //&& !empty($_POST['keyword2'][0]) && !empty($_POST['keyword3'][0]) && !empty($_POST['keyword4'][0]) && !empty($_POST['keyword5'][0]) 
                            if (update_user_meta($UserID, "Posted Content", $merge_post_content)) {
                                foreach ($_POST['ss'] as $new_post_index => $new_post) {
                                    if ($new_post == 'yes') {
                                        $social_url = $_POST['url'][$new_post_index];
                                        $check_url = $wpdb->query("SELECT * FROM `wp_social_adr` where url = '$social_url' and user_id = '$UserID' LIMIT 1");
                                        if (empty($check_url)) {
                                            $insert1 = $wpdb->query('insert into wp_social_adr(url,status,user_id)values("' . $social_url . '","0","' . $UserID . '")');
                                        }
                                    }
                                }

                                // for seonitro setup
                                foreach ($_POST['il'] as $sl_index => $sl_post) {
                                    $seonitro_url = $_POST['url'][$sl_index];
                                    //echo $seonitro_url; exit;
                                    if ($sl_post == 'yes') {
                                        $check_url = $wpdb->query("SELECT * FROM `wp_seo_nitro` where post_url = '$seonitro_url' and user_id = '$UserID' LIMIT 1");
                                        if (empty($check_url)) {
                                            $wpdb->query("INSERT INTO `wp_seo_nitro` (`id`, `user_id`, `post_date`, `post_url`, `src`) VALUES (NULL, '$UserID', '" . date('Y-m-d') . "', '$seonitro_url', 'SEO_NITRO');");
                                        }
                                    }
                                }


                                wp_redirect(site_url() . '/order-content/?type=posted-content-list');
                            }
                        } else {
                            echo '<span style="float:left;margin:0 0 0 347px;color:red">All fields are required</span>';
                        }
                    }
                    if (isset($_POST['csvsubmit'])) {
                        $PcArr = '';
                        if ($_FILES) {
                            $filename = $_FILES['pscsv']['name'];
                            if ($filename == $_FILES['pscsv']['name']) {
                                $destfile = $path . "/csv/" . time() . $filename; //marketingcontrolcenter/csv for localhost path
                                if (move_uploaded_file($_FILES["pscsv"]["tmp_name"], $destfile)) {
                                    $csvfileArr = glob($path . "/csv/" . time() . $filename);
                                    $csvfile = $csvfileArr[0];
                                    $row = 1;
                                    if (($handle = fopen($csvfile, "r")) !== FALSE) {
                                        $linecount = count(file($csvfile));
                                        if ($row <= $linecount) {
                                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                                                $pcUrl = $data[0];
                                                $keyword1 = $data[1];
                                                $keyword2 = $data[2];
                                                $keyword3 = $data[3];
                                                $keyword4 = $data[4];
                                                $keyword5 = $data[5];
                                                $le = $data[6];
                                                $ss = $data[7];
                                                $inl = $data[8];

                                                if ($row > 1) {
                                                    $pcArr1['url'][] = $pcUrl;
                                                    $pcArr1['keyword1'][] = get_meta_key($UserID, $keyword1);
                                                    $pcArr1['keyword2'][] = get_meta_key($UserID, $keyword2);
                                                    $pcArr1['keyword3'][] = get_meta_key($UserID, $keyword3);
                                                    $pcArr1['keyword4'][] = get_meta_key($UserID, $keyword4);
                                                    $pcArr1['keyword5'][] = get_meta_key($UserID, $keyword5);
                                                    $pcArr1['dl'][] = $le;
                                                    $pcArr1['ss'][] = $ss;
                                                    $pcArr1['il'][] = $inl;
                                                }
                                                $row++;
                                            }
                                            foreach ($pcArr1['url'] as $allkeyss => $valss) {
                                                if ($pcArr1['ss'][$allkeyss] == 'yes') {
                                                    $check_url = $wpdb->query("SELECT * FROM `wp_social_adr` where url = '$valss' and user_id = '$UserID' LIMIT 1");
                                                    if (empty($check_url)) {
                                                        $insert = $wpdb->query('insert into wp_social_adr(url,status,user_id)values("' . $valss . '","0","' . $UserID . '")');
                                                    }
                                                }
                                            }

                                            // for seonitro setup
                                            foreach ($pcArr1['url'] as $allkeyss => $valss) {
                                                if ($pcArr1['ss'][$allkeyss] == 'yes') {
                                                    $check_url = $wpdb->query("SELECT * FROM `wp_seo_nitro` where post_url = '$valss' and user_id = '$UserID' LIMIT 1");
                                                    if (empty($check_url)) {
                                                        $wpdb->query("INSERT INTO `wp_seo_nitro` (`id`, `user_id`, `post_date`, `post_url`, `src`) VALUES (NULL, '$UserID', '" . date('Y-m-d') . "', '$valss', 'SEO_NITRO');");
                                                    }
                                                }
                                            }

                                            $PcArr = $pcArr1;
                                            if (get_user_meta($UserID, "Posted Content", true) != "")
                                                $merge_post_content = array_merge_recursive(get_user_meta($UserID, "Posted Content", true), $PcArr);
                                            else
                                                $merge_post_content = $PcArr;
                                            update_user_meta($UserID, "Posted Content", $merge_post_content);
                                            wp_redirect(site_url() . '/order-content/?type=posted-content-list');
                                        } else
                                            $msg = "Empty file. Please upload again";
                                    }
                                } else
                                    $msg = 'File not uploaded';
                            } else
                                $msg = 'File type not supported. Please upload file with csv format only';
                        }
                    }
                    $pcval = '';
                    $pcval = get_user_meta($UserID, "Posted Content", true);
                    /* End */
                    $KeyWordQuery = $wpdb->query('select * from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%"');
                    for ($im = 1; $im <= $KeyWordQuery; $im++) {
                        $PostkeywordsD[] = get_user_meta($UserID, "LE_Repu_Keyword_" . $im . "", true);
                    }
                    ?>
                    <div class="accoSet">
                        <h2 class="fulllist">Posted Content</h2>
                    </div>
                    <div class="item-postedContent">
                        <?php
                        /* 24-Jul */
                        if (!empty($msg))
                            echo "<p style='text-align:center; color: #d14836;'>" . $msg . "</p>";
                        /* End */
                        ?>
                        <form name="postedcoontent" id="postedcoontent_Frm" method="post" enctype="multipart/form-data">
                            <div style="float:left; width:840px;padding: 15px;">
                                <div style="float:left; width:173px;">
                                    URL
                                </div>
                                <div style="float:left; width:129px;">
                                    Keyword 1
                                </div>
                                <div style="float:left; width:128px;">
                                    Keyword 2 
                                </div>
                                <div style="float:left; width:128px;">
                                    Keyword 3
                                </div>
                                <div style="float:left; width:128px;">
                                    Keyword 4
                                </div>
                                <div style="float:left; width:128px;">
                                    Keyword 5
                                </div>
                            </div>
                            <div style="float:left; width:840px;padding:15px">
                                <div id="url_div">
                                    <div class="cl_big">
                                        <input style="border:1px solid #aaa" placeholder="Enter Url" type="text" name="url[]" value="" class="required cl_input_url">
                                    </div>
                                    <div class="cl_lit">
                                        <select placeholder="Keyword 1" type="text" name="keyword1[]" value="" class="required cl_input">
                                            <?php echo $opt; ?>
                                        </select>    
                                    </div>
                                    <div class="cl_lit">
                                        <select placeholder="Keyword 2" type="text" name="keyword2[]" value="" class="cl_input">
                                            <?php echo $opt; ?>
                                        </select>      
                                    </div>
                                    <div class="cl_lit">
                                        <select placeholder="Keyword 3" type="text" name="keyword3[]" value="" class="cl_input">
                                            <?php echo $opt; ?>
                                        </select>   
                                    </div>
                                    <div class="cl_lit">
                                        <select placeholder="Keyword 4" type="text" name="keyword4[]" value="" class="cl_input">
                                            <?php echo $opt; ?>
                                        </select>   
                                    </div>
                                    <div class="cl_lit">
                                        <select placeholder="Keyword 5" type="text" name="keyword5[]" value="" class="cl_input">
                                            <?php echo $opt; ?>
                                        </select>   
                                    </div>
                                    <div style="clear:both;"></div>
                                    <div style="float:left;margin:19px 0 0 0; width:131px;">
                                        <select class="dl_class" name="dl[]">
                                            <option value="yes">(DL)Yes</option>
                                            <option value="no">(DL)No</option>
                                        </select>
                                    </div>
                                    <div style="float:left; width:129px;margin:19px 0 0 0;">
                                        <select class="dl_class" name="ss[]">
                                            <option value="yes">(SS)Yes</option>
                                            <option value="no">(SS)No</option>
                                        </select>
                                    </div>
                                    <div style="float:left; width:129px;margin:19px 0 0 0;">
                                        <select class="dl_class" name="il[]">
                                            <option value="yes">(SL)Yes</option>
                                            <option value="no">(SL)No</option>
                                        </select>
                                    </div>
                                </div>   
                                <div class="clear_both"></div>
                                <div><a href="javascript:void(0);" onclick="add_more()">Add more</a></div>    
                            </div>
                            <?php
                            //}
                            ?>
                            <!-- 24-Jul -->
                            <input type="submit" value="Save" name="pcsubmit" style=" background: none repeat scroll 0 0 #D14836; color: white; float:right; font-weight:bold;" />
                            <!--End-->
                        </form>
                        <!-- 25-Jul -->
                        <form name="postedcontent" method="post" enctype="multipart/form-data" style="margin-left:15px;">
                            <span>Upload CSV :</span>
                            <input type="file" name="pscsv" style="margin:0 0 10px 7px;" /><br />
                            <input type="submit" value="Upload" name="csvsubmit" style=" background: #D14836; color: white; 
                                   float:left; font-weight:bold;" />
                        </form>
                        <!--End-->
                        <br/>
                        <br/>
                        <div style="float:left;margin:0 0 0 12px">Instruction : -  Url must contain a "/" in the end</div>
                        <div style="float:right"><a href="<?php echo site_url() ?>/sample_order.csv">Sample CSV</a></div>
                    </div>	 


                    
                