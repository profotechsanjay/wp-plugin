<?php
echo $stle; 
include_once get_template_directory() . '/common/report-function.php';
include_once get_template_directory() . '/analytics/my_functions.php';
include_once SET_COUNT_PLUGIN_DIR . '/library/report_functions.php';

$db_report_name = ST_COMPETIROR_REPORT;

if (isset($_POST['btn_download-report'])) {
        
    ob_end_clean();
    
    if ($_POST['dwnld_type'] == 'pdf') {
                
        $str = '<style>.padding_full{padding:10px 3px; font-size: 15px; border: 1px solid #ddd;}
                .keyword_width{width:20%;} .text-center{S;} 
                .ranking_width{width:33%;} .bg-green-jungle{     padding: 5px; color: #fff; background: #26C281!important;} .bg-blue{     padding: 5px; color: #fff; background: #3598dc!important;}
                .bg-red-thunderbird {    padding: 5px; background: #D91E18 !important; color: #fff;}
                td.text-center { text-align: center; padding: 10px 5px; }
                </style>';
        
        $str .= $stle;
        $str .= rd_pdf_header();
        $str .= '<h3 style="text-align:center;">' . bloginfo('name') . ' Competitor Report</h3><br/>';
        $str .='<table cellspacing="10" class="c2" style="margin-top:10px; text-align: center; font-size:15px; border-radius: 3px 3px 3px 3px; width: 100%;">';
          
        $ht = 70;
        foreach ($locations as $location) {
            
            $ht = $ht + 70;
            $location_id = $location->id;
            $user_id = $UserID = $location->MCCUserId;    
            $str .='<tr><td style="border: 1px solid #cecece; padding: 15px 10px;">'; 
            
            $client_website = $website = get_user_meta($UserID, 'website', TRUE);    
            $client_name = get_user_meta($UserID, 'BRAND_NAME', TRUE); 
            $str .='<div><h4>'.$client_name.' ( '.$website.' )</h4></div>';
            $str .= '<table style="margin:10px 0; text-align: center; font-size:15px; text-align: center; width: 100%;">';
                        
            $competitor_url = get_user_meta($UserID, "competitor_url", true);   
            
            if(!empty($competitor_url)){ 
                include 'competitor_common.php';
                $ii = 0;
                $colors_array = array("#55BF3B", "#DF5353", "#7798BF", "#ff0066", "#8d4654", "#f45b5b", "#8085e9", "#7798BF", "#aaeeee", "#aaeeee", "#eeaaee");
                $gh_competitor[] = $client_website;
                $str .= '<tr><td>';                
                $styletop = isset($colors_array[$ii]) ? 'color:' . $colors_array[$ii] . '; background:' . $colors_array[$ii] : '';
                $str .= '<table cellspacing="0" border="1" class="table table-bordered white-table" style="border-color: #e0e0e0; width: 100%;">
                    <thead>
                        <tr>
                            <th class=" bg-blue" width="25%">Domains</th>
                            <th colspan="2" class="text-center bg-blue" width="15%">Total Rankings</th>
                            <th colspan="2" class="text-center bg-blue"  width="15%">Avg. Pos.</th>
                            <th colspan="2" class="text-center bg-blue"  width="15%">First Place</th>
                            <th colspan="2" class="text-center bg-blue"  width="15%">Top 3</th>
                            <th colspan="2" class="text-center bg-blue"  width="15%">Page 1</th>
                        </tr>                       
                        
                        <tr>
                            <td class="text-center"><i class="fa fa-bar-chart fa-1x" style="'.$styletop.'"></i>
                                '.$client_website.'                                
                            </td>
                            <td class="text-center">'.$comp_data_arr[$client_website]['total_rank'].'</td>
                            <td class="text-center" width="10%;">';
                                
                                $ii++;
                                $new_total = $comp_data_arr[$client_website]['total_rank'];
                                $old_total = $comp_data_arr[$client_website]['old_total_rank'];
                                $rank_ch = $new_total - $old_total;
                                $sign = "";
                                if ($rank_ch > 0) {
                                    $ch_class = $label_arr['green'];
                                    $sign = "+";
                                } else if ($rank_ch == 0) {
                                    $ch_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $ch_class = $label_arr['red'];
                                    $sign = "-";
                                }
                                
                        $str .= '<span class="label label-sm label-hf-block '.$ch_class.'">'.$sign . abs($rank_ch).'</span>
                            </td>
                            <td class="text-center">';                                                                
                                if ($comp_data_arr[$client_website]['total_avg_pos'] == 0) {
                                    $str .= '50+';
                                } else {
                                    $str .=  number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                                }
                               
                            $str .= '</td><td class="text-center"  width="10%;">';
                                
                                $new_avg = number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                                $old_avg = number_format($comp_data_arr[$client_website]['old_total_avg_pos'] / $total_keywords, 2);
                                $avg_ch = $new_avg - $old_avg;
                                if ($avg_ch == 51 || $avg_ch == -51) {
                                    $avg_ch = 0;
                                }

                                if ($avg_ch > 0) {
                                    $avg_class = $label_arr['red'];
                                    $sign = "-";
                                } else if ($avg_ch == 0) {
                                    $avg_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $avg_class = $label_arr['green'];
                                    $sign = "+";
                                }
                              
                            $str .= '<span class="label label-sm label-hf-block '.$avg_class.'">'.$sign . abs($avg_ch).'</span>
                            </td>
                            <td class="text-center">'.$comp_data_arr[$client_website]['first_palce'].'</td>
                            <td class="text-center"  width="10%;">';
                                
                                $new_first_place = $comp_data_arr[$client_website]['first_palce'];
                                $old_first_place = $comp_data_arr[$client_website]['old_first_palce'];

                                $first_place_ch = $new_first_place - $old_first_place;
                                if ($first_place_ch > 0) {
                                    $first_place_class = $label_arr['green'];
                                    $sign = "+";
                                } else if ($first_place_ch == 0) {
                                    $first_place_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $first_place_class = $label_arr['red'];
                                    $sign = "-";
                                }
                                
                            $str .= '<span class="label label-sm label-hf-block '.$first_place_class.'">'.$sign . abs($first_place_ch).'</span>
                            </td>
                            <td class="text-center">'.$comp_data_arr[$client_website]['top3'].'</td>
                            <td class="text-center"  width="10%;">';
                                
                                $new_top3 = $comp_data_arr[$client_website]['top3'];
                                $old_top3 = $comp_data_arr[$client_website]['old_top3'];

                                $top3_ch = $new_top3 - $old_top3;
                                if ($top3_ch > 0) {
                                    $top3_class = $label_arr['green'];
                                    $sign = "+";
                                } else if ($top3_ch == 0) {
                                    $top3_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $top3_class = $label_arr['red'];
                                    $sign = "-";
                                }
                                
                                $str .= '<span class="label label-sm label-hf-block '.$top3_class.'">'.$sign . abs($top3_ch).'</span>
                            </td>
                            <td class="text-center">'.$comp_data_arr[$client_website]['page1'].'</td>
                            <td class="text-center" width="10%;">';
                                
                                $new_page1 = $comp_data_arr[$client_website]['page1'];
                                $old_page1 = $comp_data_arr[$client_website]['old_page1'];

                                $page1_ch = $new_page1 - $old_page1;
                                if ($page1_ch > 0) {
                                    $page1_class = $label_arr['green'];
                                    $sign = "+";
                                } else if ($page1_ch == 0) {
                                    $page1_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $page1_class = $label_arr['red'];
                                    $sign = "-";
                                }
                                
                            $str .= '<span class="label label-sm label-hf-block '.$page1_class.'">'.$sign . abs($page1_ch).'</span> 
                            </td>
                        </tr>';
                                                    
                        for ($com_url = 0; $com_url < $count_competitor_url; $com_url++) { 
                           
                            $style = isset($colors_array[$ii]) ? 'color:' . $colors_array[$ii] . '; background:' . $colors_array[$ii] : '';
                        
                            $str .=  '<tr>
                                <td class="text-center"><i class="fa fa-bar-chart fa-1x" style="'.$style .'"></i>';
                                    
                                    $ii++;
                                    if (!empty($competitor_url)) {
                                        $str .= $competitor_url[$com_url];
                                        $gh_competitor[] = $competitor_url[$com_url];
                                    }
                                   
                                $str .= '</td>
                                <td class="text-center">'.$comp_data_arr[$competitor_url[$com_url]]['total_rank'].'</td>
                                <td class="text-center">';
                                    
                                    $new_total = $comp_data_arr[$competitor_url[$com_url]]['total_rank'];
                                    $old_total = $comp_data_arr[$competitor_url[$com_url]]['old_total_rank'];
                                    $rank_ch = $new_total - $old_total;
                                    if ($rank_ch > 0) {
                                        $ch_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($rank_ch == 0) {
                                        $ch_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $ch_class = $label_arr['red'];
                                        $sign = "-";
                                    }
                                    
                                $str .= '<span class="label label-sm label-hf-block '.$ch_class.'">'.$sign . abs($rank_ch).'</span>
                                </td>
                                <td class="text-center">';
                                    
                                    if ($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] == 0) {
                                        $str .= '50+';
                                    } else {
                                        $str .= number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                                    }
                                
                                $str .= '</td>
                                <td class="text-center">';
                                    
                                    $new_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                                    $old_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['old_total_avg_pos'] / $total_keywords, 2);
                                    $avg_ch = $new_avg - $old_avg;

                                    if ($avg_ch == 51 || $avg_ch == -51) {
                                        $avg_ch = 0;
                                    }

                                    if ($avg_ch > 0) {
                                        $avg_class = $label_arr['red'];
                                        $sign = "-";
                                    } else if ($avg_ch == 0) {
                                        $avg_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $avg_class = $label_arr['green'];
                                        $sign = "+";
                                    }
                                   
                                $str .= '<span class="label label-sm label-hf-block '.$avg_class.'">
                                    '.$sign . abs($avg_ch).'</span>
                                </td>
                                <td class="text-center">'.$comp_data_arr[$competitor_url[$com_url]]['first_palce'].'</td>
                                <td class="text-center">';
                                    
                                    $new_first_place = $comp_data_arr[$competitor_url[$com_url]]['first_palce'];
                                    $old_first_place = $comp_data_arr[$competitor_url[$com_url]]['old_first_palce'];

                                    $first_place_ch = $new_first_place - $old_first_place;
                                    if ($first_place_ch > 0) {
                                        $first_place_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($first_place_ch == 0) {
                                        $first_place_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $first_place_class = $label_arr['red'];
                                        $sign = "-";
                                    }
                                   
                                $str .= '<span class="label label-sm label-hf-block '.$first_place_class.'">'.$sign . abs($first_place_ch).'</span>
                                </td>
                                <td class="text-center">'.$comp_data_arr[$competitor_url[$com_url]]['top3'].'</td>
                                <td class="text-center">';
                                    
                                    $new_top3 = $comp_data_arr[$competitor_url[$com_url]]['top3'];
                                    $old_top3 = $comp_data_arr[$competitor_url[$com_url]]['old_top3'];

                                    $top3_ch = $new_top3 - $old_top3;
                                    if ($top3_ch > 0) {
                                        $top3_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($top3_ch == 0) {
                                        $top3_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $top3_class = $label_arr['red'];
                                        $sign = "-";
                                    }
                                   
                                $str .= '<span class="label label-sm label-hf-block '.$top3_class.'">'.$sign . abs($top3_ch).'</span>
                                </td>
                                <td class="text-center">'.$comp_data_arr[$competitor_url[$com_url]]['page1'].'</td>
                                <td class="text-center">';
                                    
                                    $new_page1 = $comp_data_arr[$competitor_url[$com_url]]['page1'];
                                    $old_page1 = $comp_data_arr[$competitor_url[$com_url]]['old_page1'];

                                    $page1_ch = $new_page1 - $old_page1;
                                    if ($page1_ch > 0) {
                                        $page1_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($page1_ch == 0) {
                                        $page1_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $page1_class = $label_arr['red'];
                                        $sign = "-";
                                    }
                                    
                                $str .= '<span class="label label-sm label-hf-block '.$page1_class.'">
                                    '.$sign . abs($page1_ch).'</span> 
                                </td>
                            </tr>';
                         } 
                    $str .= '</thead></table>';
                     $str .='</td></tr>';
                
            }
            else{
                $str .= '<tr><td style="color: #e73d4a; padding: 10px; background-color: #fbe1e3; border-color: #fbe1e3; ">No competitor added for this location</td></tr>';
            }
            $str .=  '</table>';            
            $str .='</td></tr>';
                                    
        }
                
        $str .='</table>';
        
        $str .='<div style="clear:both;height:20px;"></div>';                       
        require_once(ABSPATH . "RankreportEmail/dompdf_config.inc.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($str); 
        $widpdf = 1350;
        $customPaper = array(0,0,$widpdf,$ht);
        $dompdf->set_paper($customPaper);        
        $dompdf->render();
        $user_id = $UserID;
        
        include ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php';
        $pdf = $dompdf->output();        
        $dompdf->stream("competitor_report.pdf", array("Attachment" => true));
        exit;        
        
    }
    else if ($_POST['dwnld_type'] == 'csv') {
                
        $header_table = array('', 'DOMAINS', 'TOTAL RANKINGS', 'AVG. POS.', 'FIRST PLACE', 'TOP 3', 'PAGE 1');
        $header_empty = array('', '', '', '', '', '', '');
        
        $FilePath =  "competitor_report.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename=' . $FilePath);    
        ob_clean();
        $fp = fopen('php://output', "w");                
                
        foreach ($locations as $location) {
            $location_id = $location->id;
            $user_id = $UserID = $location->MCCUserId;        
            $client_website = $website = get_user_meta($UserID, 'website', TRUE);    
            $client_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);    
            $totalkeywords =  countlocation_keywords($UserID);
            if($totalkeywords == 'N/A'){
                $totalkeywords = 'NA';
            }
            
            $header_key_report = array('Account Name - '.$client_name, '', '', '', '', '', '');
            $header_lower = array('Url - '.$website, '', '', '', '', '', '');
                                    
            fputcsv($fp, $header_key_report);
            fputcsv($fp, $header_lower);
            fputcsv($fp, $header_empty);
                        
            $competitor_url = get_user_meta($UserID, "competitor_url", true);
            
            if(!empty($competitor_url)){
                
                fputcsv($fp, $header_table);
                $table_values = array('', '', '', '', '', '', '');
                include 'competitor_common.php';
                $ii = 0;                
                $gh_competitor[] = $client_website;
                                                
                
                $ii++;
                $new_total = $comp_data_arr[$client_website]['total_rank'];
                $old_total = $comp_data_arr[$client_website]['old_total_rank'];
                $rank_ch = $new_total - $old_total;
                $sign = "";
                if ($rank_ch > 0) {
                    $ch_class = $label_arr['green'];
                    $sign = "+";
                } else if ($rank_ch == 0) {
                    $ch_class = $label_arr['blue'];
                    $sign = "";
                } else {
                    $ch_class = $label_arr['red'];
                    $sign = "-";
                }

                if ($comp_data_arr[$client_website]['total_avg_pos'] == 0) {
                    $vll = '50+';
                } else {
                    $vll =  number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                }                                                                                                                   

                $new_avg = number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                $old_avg = number_format($comp_data_arr[$client_website]['old_total_avg_pos'] / $total_keywords, 2);
                $avg_ch = $new_avg - $old_avg;
                if ($avg_ch == 51 || $avg_ch == -51) {
                    $avg_ch = 0;
                }

                if ($avg_ch > 0) {
                    $avg_class = $label_arr['red'];
                    $sign = "-";
                } else if ($avg_ch == 0) {
                    $avg_class = $label_arr['blue'];
                    $sign = "";
                } else {
                    $avg_class = $label_arr['green'];
                    $sign = "+";
                }                                                        

                $new_first_place = $comp_data_arr[$client_website]['first_palce'];
                $old_first_place = $comp_data_arr[$client_website]['old_first_palce'];

                $first_place_ch = $new_first_place - $old_first_place;
                if ($first_place_ch > 0) {
                    $first_place_class = $label_arr['green'];
                    $sign = "+";
                } else if ($first_place_ch == 0) {
                    $first_place_class = $label_arr['blue'];
                    $sign = "";
                } else {
                    $first_place_class = $label_arr['red'];
                    $sign = "-";
                }

                $new_top3 = $comp_data_arr[$client_website]['top3'];
                $old_top3 = $comp_data_arr[$client_website]['old_top3'];

                $top3_ch = $new_top3 - $old_top3;
                if ($top3_ch > 0) {
                    $top3_class = $label_arr['green'];
                    $sign = "+";
                } else if ($top3_ch == 0) {
                    $top3_class = $label_arr['blue'];
                    $sign = "";
                } else {
                    $top3_class = $label_arr['red'];
                    $sign = "-";
                }


                $new_page1 = $comp_data_arr[$client_website]['page1'];
                $old_page1 = $comp_data_arr[$client_website]['old_page1'];

                $page1_ch = $new_page1 - $old_page1;
                if ($page1_ch > 0) {
                    $page1_class = $label_arr['green'];
                    $sign = "+";
                } else if ($page1_ch == 0) {
                    $page1_class = $label_arr['blue'];
                    $sign = "";
                } else {
                    $page1_class = $label_arr['red'];
                    $sign = "-";
                }


                $table_values = array('', $client_website,$comp_data_arr[$client_website]['total_rank'].' | '.$sign . abs($rank_ch),
                        $vll.' | '.$sign . abs($avg_ch), $comp_data_arr[$client_website]['first_palce'].' | '.$sign . abs($first_place_ch),
                        $comp_data_arr[$client_website]['top3'].' | '.$sign . abs($top3_ch), $comp_data_arr[$client_website]['page1'].' | '.$sign . abs($page1_ch)); 
                        
                        
                fputcsv($fp, $table_values);
                
                for ($com_url = 0; $com_url < $count_competitor_url; $com_url++) {

                                                           
                        $ii++; $urlcompt = '';
                        if (!empty($competitor_url)) {
                            $urlcompt = $competitor_url[$com_url];
                            $gh_competitor[] = $competitor_url[$com_url];
                        }

                                                                           

                        $new_total = $comp_data_arr[$competitor_url[$com_url]]['total_rank'];
                        $old_total = $comp_data_arr[$competitor_url[$com_url]]['old_total_rank'];
                        $rank_ch = $new_total - $old_total;
                        if ($rank_ch > 0) {
                            $ch_class = $label_arr['green'];
                            $sign = "+";
                        } else if ($rank_ch == 0) {
                            $ch_class = $label_arr['blue'];
                            $sign = "";
                        } else {
                            $ch_class = $label_arr['red'];
                            $sign = "-";
                        }

                        if ($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] == 0) {
                            $vll = '50+';
                        } else {
                            $vll = number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                        }
                                                                           
                        $new_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                        $old_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['old_total_avg_pos'] / $total_keywords, 2);
                        $avg_ch = $new_avg - $old_avg;

                        if ($avg_ch == 51 || $avg_ch == -51) {
                            $avg_ch = 0;
                        }

                        if ($avg_ch > 0) {
                            $avg_class = $label_arr['red'];
                            $sign = "-";
                        } else if ($avg_ch == 0) {
                            $avg_class = $label_arr['blue'];
                            $sign = "";
                        } else {
                            $avg_class = $label_arr['green'];
                            $sign = "+";
                        }
                       

                        $new_first_place = $comp_data_arr[$competitor_url[$com_url]]['first_palce'];
                        $old_first_place = $comp_data_arr[$competitor_url[$com_url]]['old_first_palce'];

                        $first_place_ch = $new_first_place - $old_first_place;
                        if ($first_place_ch > 0) {
                            $first_place_class = $label_arr['green'];
                            $sign = "+";
                        } else if ($first_place_ch == 0) {
                            $first_place_class = $label_arr['blue'];
                            $sign = "";
                        } else {
                            $first_place_class = $label_arr['red'];
                            $sign = "-";
                        }

                        $new_top3 = $comp_data_arr[$competitor_url[$com_url]]['top3'];
                        $old_top3 = $comp_data_arr[$competitor_url[$com_url]]['old_top3'];

                        $top3_ch = $new_top3 - $old_top3;
                        if ($top3_ch > 0) {
                            $top3_class = $label_arr['green'];
                            $sign = "+";
                        } else if ($top3_ch == 0) {
                            $top3_class = $label_arr['blue'];
                            $sign = "";
                        } else {
                            $top3_class = $label_arr['red'];
                            $sign = "-";
                        }                    

                        $new_page1 = $comp_data_arr[$competitor_url[$com_url]]['page1'];
                        $old_page1 = $comp_data_arr[$competitor_url[$com_url]]['old_page1'];

                        $page1_ch = $new_page1 - $old_page1;
                        if ($page1_ch > 0) {
                            $page1_class = $label_arr['green'];
                            $sign = "+";
                        } else if ($page1_ch == 0) {
                            $page1_class = $label_arr['blue'];
                            $sign = "";
                        } else {
                            $page1_class = $label_arr['red'];
                            $sign = "-";
                        }
                        
                        
                        $table_values = array('', $urlcompt,$comp_data_arr[$competitor_url[$com_url]]['total_rank'].' | '.$sign . abs($rank_ch),
                            $vll.' | '.$sign . abs($avg_ch), $comp_data_arr[$competitor_url[$com_url]]['first_palce'].' | '.$sign . abs($first_place_ch),
                            $comp_data_arr[$competitor_url[$com_url]]['top3'].' | '.$sign . abs($top3_ch), $comp_data_arr[$competitor_url[$com_url]]['page1'].' | '.$sign . abs($page1_ch)); 
                        
                        fputcsv($fp, $table_values);
                        
                }               
                
            }
            else{                
                
                $no_competitor_table = array('', 'No Competitor added for this Location', '', '', '', '', '');
                fputcsv($fp, $no_competitor_table);
                
            }
                        
            
            fputcsv($fp, $header_empty);
            fputcsv($fp, $header_empty);
        }
        
        ob_flush(); 
        fclose($fp);
        exit;
    }    
    
   
}
else if (isset($_POST['btn_schedule-executive-report'])) {
    //pr($_POST); die;    
    $shEmails = array_filter($_POST['sh-email'], function($a) { //IF PHP >=3.5 ELSE create_function('$a','!empty($a["to"]) && empty($a["id"]);')
        //if 'id'-key is set, then do not remove/filter this item from the array. The 'id'-key items will be handled differently to delete or edit;
        return !empty($a["to"]) || !empty($a["id"]);
    });
    if (empty($shEmails)) {
        $popupErrMsg = 'Please type your email';        
        
    } else {
        
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $sql = "SELECT sch_id FROM {$wpdb->prefix}mcc_sch_settings WHERE sch_type='$db_report_name'";
        $settingId = $wpdb->get_var($sql);
        $sch_status = empty($_POST['sh-send-report-via-email']) ? 0 : 1;
        $schData = array(
            'sch_frequency' => $_POST['sh-how-often'],
            'sch_reportVolume' => $_POST['sh-volume'],
            'report_type' => $_POST['report_type'],           
            'sch_status' => $sch_status,		            
            'sch_type' => $db_report_name,
            'sch_uId' => $user_id,
        );
        
        if (empty($settingId)) {
            $queryStatus = $wpdb->insert(
            	"{$wpdb->prefix}mcc_sch_settings",
				$schData,
				array('%s', '%s', '%s', '%d', '%s', '%d')
            );

            if ($queryStatus === false) {
                $popupErrMsg = 'Invalid Query! Please try again.';

            } else {
                $settingId = $wpdb->insert_id;
                $popupSucsMsg = 'Email scheduler is successfully added';
                unset($_POST);

            }
        } else {
                                    
            $tbl = $wpdb->prefix."mcc_sch_settings";           
            $queryStatus = $wpdb->query
            (
                $wpdb->prepare
                (
                        "UPDATE " . $tbl . " SET sch_frequency = %s, report_type = %s, sch_reportVolume = %s, sch_status = %s "
                        . "WHERE sch_id = %d", 
                        $_POST['sh-how-often'], $_POST['report_type'], $_POST['sh-volume'], $sch_status, $settingId
                )
            );
                            
            if ($queryStatus === false) {
                $popupErrMsg = 'Invalid Query! Please try again';

            } else {
                $popupSucsMsg = 'Email scheduler is successfully updated';
                unset($_POST);

            }
        }
        
         //Finally process emails:
        if (!empty($settingId)) {
            $queryStatus2 = array();
            foreach ($shEmails as $em) {
                if (empty($em['id'])) {
                    $queryStatus2[] = $wpdb->insert(
                        "{$wpdb->prefix}mcc_sch_emails",
						array(
							'em_sch_id' => $settingId,
							'em_emailTo' => $em['to'],
							'em_status' => $em['st']
                        ),
						array('%d', '%s', '%d')
                    );

                } else {
                    if (empty($em['to'])) {
                        $queryStatus2[] = $wpdb->delete(
                        	"{$wpdb->prefix}mcc_sch_emails",
							array('em_id' => $em['id']),
							array('%d')
                        );

                    } else {
                        $queryStatus2[] = $wpdb->update(
                            "{$wpdb->prefix}mcc_sch_emails",
							array(
								//'em_sch_id'		=> $settingId,
								'em_emailTo' => $em['to'],
								'em_status' => $em['st']
                            ),
							array('em_id' => $em['id']),
							array('%s', '%d'),
							array('%d')
                        );
						
                    }
                }
            }
			//pr($queryStatus2,'======$queryStatus2====='); die();
        }
        
        
    }
}

$i = 0;
foreach ($locations as $location) {
    $location_id = $location->id;
    $user_id = $UserID = $location->MCCUserId;        
    $client_website = $website = get_user_meta($UserID, 'website', TRUE);    
    $client_name = get_user_meta($UserID, 'BRAND_NAME', TRUE);
        ?>

        <div class="reportdiv <?php if($i == 0) echo 'margin_top_minus20'; ?>">
            <h5>
                <div>Account Name - <?php echo $client_name; ?> </div>
                <div>URL: <?php echo $website; ?>  </div>  
                <div class="pull-right comptreport"><a href="?parm=execution&function=competitor_full_report&location_id=<?php echo $location_id; ?>" target="_blank" class="btn btn-primary ">Location Full Report</a></div>
            </h5>

            <div class="clearfix"></div>
            <div class="portlet light competitor_report<?php echo $UserID; ?>">
                <?php
                
                $competitor_url = get_user_meta($UserID, "competitor_url", true);   
                if(!empty($competitor_url)){ 
                    include 'competitor_common.php';
                ?>
                <table class="table table-bordered white-table">
                    <thead>
                        <tr>
                            <th class=" bg-blue" width="25%">Domains</th>
                            <th colspan="2" class="text-center bg-blue" width="15%">Total Rankings</th>
                            <th colspan="2" class="text-center bg-blue"  width="15%">Avg. Pos.</th>
                            <th colspan="2" class="text-center bg-blue"  width="15%">First Place</th>
                            <th colspan="2" class="text-center bg-blue"  width="15%">Top 3</th>
                            <th colspan="2" class="text-center bg-blue"  width="15%">Page 1</th>
                        </tr>
                        <?php
                        $colors_array = array("#55BF3B", "#DF5353", "#7798BF", "#ff0066", "#8d4654", "#f45b5b", "#8085e9", "#7798BF", "#aaeeee", "#aaeeee", "#eeaaee");
                        $ii = 0;
                        ?>
                        <tr>
                            <td><i class="fa fa-bar-chart fa-1x" style=" <?php echo isset($colors_array[$ii]) ? 'color:' . $colors_array[$ii] . '; background:' . $colors_array[$ii] : ''; ?>"></i><?php
                                echo $client_website;
                                $gh_competitor[] = $client_website;
                                ?>
                            </td>
                            <td class="text-center"><?php echo $comp_data_arr[$client_website]['total_rank']; ?></td>
                            <td class="text-center" width="10%;">
                                <?php
                                $ii++;
                                $new_total = $comp_data_arr[$client_website]['total_rank'];
                                $old_total = $comp_data_arr[$client_website]['old_total_rank'];
                                $rank_ch = $new_total - $old_total;
                                $sign = "";
                                if ($rank_ch > 0) {
                                    $ch_class = $label_arr['green'];
                                    $sign = "+";
                                } else if ($rank_ch == 0) {
                                    $ch_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $ch_class = $label_arr['red'];
                                    $sign = "-";
                                }
                                ?>
                                <span class="label label-sm label-hf-block <?php echo $ch_class; ?>"><?php echo $sign . abs($rank_ch); ?></span>
                            </td>
                            <td class="text-center">
                                <?php                                
                                if ($comp_data_arr[$client_website]['total_avg_pos'] == 0) {
                                    echo "50+";
                                } else {
                                    echo number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                                }
                                ?>
                            </td>
                            <td class="text-center"  width="10%;">
                                <?php
                                $new_avg = number_format($comp_data_arr[$client_website]['total_avg_pos'] / $total_keywords, 2);
                                $old_avg = number_format($comp_data_arr[$client_website]['old_total_avg_pos'] / $total_keywords, 2);
                                $avg_ch = $new_avg - $old_avg;
                                if ($avg_ch == 51 || $avg_ch == -51) {
                                    $avg_ch = 0;
                                }

                                if ($avg_ch > 0) {
                                    $avg_class = $label_arr['red'];
                                    $sign = "-";
                                } else if ($avg_ch == 0) {
                                    $avg_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $avg_class = $label_arr['green'];
                                    $sign = "+";
                                }
                                ?>
                                <span class="label label-sm label-hf-block <?php echo $avg_class; ?>"><?php echo $sign . abs($avg_ch); ?></span>
                            </td>
                            <td class="text-center"><?php echo $comp_data_arr[$client_website]['first_palce']; ?></td>
                            <td class="text-center"  width="10%;">
                                <?php
                                $new_first_place = $comp_data_arr[$client_website]['first_palce'];
                                $old_first_place = $comp_data_arr[$client_website]['old_first_palce'];

                                $first_place_ch = $new_first_place - $old_first_place;
                                if ($first_place_ch > 0) {
                                    $first_place_class = $label_arr['green'];
                                    $sign = "+";
                                } else if ($first_place_ch == 0) {
                                    $first_place_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $first_place_class = $label_arr['red'];
                                    $sign = "-";
                                }
                                ?>
                                <span class="label label-sm label-hf-block <?php echo $first_place_class; ?>"><?php echo $sign . abs($first_place_ch); ?></span>
                            </td>
                            <td class="text-center"><?php echo $comp_data_arr[$client_website]['top3']; ?></td>
                            <td class="text-center"  width="10%;">
                                <?php
                                $new_top3 = $comp_data_arr[$client_website]['top3'];
                                $old_top3 = $comp_data_arr[$client_website]['old_top3'];

                                $top3_ch = $new_top3 - $old_top3;
                                if ($top3_ch > 0) {
                                    $top3_class = $label_arr['green'];
                                    $sign = "+";
                                } else if ($top3_ch == 0) {
                                    $top3_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $top3_class = $label_arr['red'];
                                    $sign = "-";
                                }
                                ?>
                                <span class="label label-sm label-hf-block <?php echo $top3_class; ?>"><?php echo $sign . abs($top3_ch); ?></span>
                            </td>
                            <td class="text-center"><?php echo $comp_data_arr[$client_website]['page1']; ?></td>
                            <td class="text-center" width="10%;">
                                <?php
                                $new_page1 = $comp_data_arr[$client_website]['page1'];
                                $old_page1 = $comp_data_arr[$client_website]['old_page1'];

                                $page1_ch = $new_page1 - $old_page1;
                                if ($page1_ch > 0) {
                                    $page1_class = $label_arr['green'];
                                    $sign = "+";
                                } else if ($page1_ch == 0) {
                                    $page1_class = $label_arr['blue'];
                                    $sign = "";
                                } else {
                                    $page1_class = $label_arr['red'];
                                    $sign = "-";
                                }
                                ?>
                                <span class="label label-sm label-hf-block <?php echo $page1_class; ?>"><?php echo $sign . abs($page1_ch); ?></span> 
                            </td>
                        </tr>
                        <?php
                        
                        for ($com_url = 0; $com_url < $count_competitor_url; $com_url++) { ?>
                            <tr>
                                <td><i class="fa fa-bar-chart fa-1x" style=" <?php echo isset($colors_array[$ii]) ? 'color:' . $colors_array[$ii] . '; background:' . $colors_array[$ii] : ''; ?>"></i>
                                    <?php
                                    $ii++;
                                    if (!empty($competitor_url)) {
                                        echo $competitor_url[$com_url];
                                        $gh_competitor[] = $competitor_url[$com_url];
                                    }
                                    ?>
                                </td>
                                <td class="text-center"><?php echo $comp_data_arr[$competitor_url[$com_url]]['total_rank']; ?></td>
                                <td class="text-center">
                                    <?php
                                    $new_total = $comp_data_arr[$competitor_url[$com_url]]['total_rank'];
                                    $old_total = $comp_data_arr[$competitor_url[$com_url]]['old_total_rank'];
                                    $rank_ch = $new_total - $old_total;
                                    if ($rank_ch > 0) {
                                        $ch_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($rank_ch == 0) {
                                        $ch_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $ch_class = $label_arr['red'];
                                        $sign = "-";
                                    }
                                    ?>
                                    <span class="label label-sm label-hf-block <?php echo $ch_class; ?>"><?php echo $sign . abs($rank_ch); ?></span>
                                </td>
                                <td class="text-center">

                                    <?php
                                    if ($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] == 0) {
                                        echo "50+";
                                    } else {
                                        echo number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                                    }
                                    ?></td>
                                <td class="text-center">
                                    <?php
                                    $new_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['total_avg_pos'] / $total_keywords, 2);
                                    $old_avg = number_format($comp_data_arr[$competitor_url[$com_url]]['old_total_avg_pos'] / $total_keywords, 2);
                                    $avg_ch = $new_avg - $old_avg;

                                    if ($avg_ch == 51 || $avg_ch == -51) {
                                        $avg_ch = 0;
                                    }

                                    if ($avg_ch > 0) {
                                        $avg_class = $label_arr['red'];
                                        $sign = "-";
                                    } else if ($avg_ch == 0) {
                                        $avg_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $avg_class = $label_arr['green'];
                                        $sign = "+";
                                    }
                                    ?>
                                    <span class="label label-sm label-hf-block <?php echo $avg_class; ?>"><?php echo $sign . abs($avg_ch); ?></span>
                                </td>
                                <td class="text-center"><?php echo $comp_data_arr[$competitor_url[$com_url]]['first_palce']; ?></td>
                                <td class="text-center">
                                    <?php
                                    $new_first_place = $comp_data_arr[$competitor_url[$com_url]]['first_palce'];
                                    $old_first_place = $comp_data_arr[$competitor_url[$com_url]]['old_first_palce'];

                                    $first_place_ch = $new_first_place - $old_first_place;
                                    if ($first_place_ch > 0) {
                                        $first_place_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($first_place_ch == 0) {
                                        $first_place_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $first_place_class = $label_arr['red'];
                                        $sign = "-";
                                    }
                                    ?>
                                    <span class="label label-sm label-hf-block <?php echo $first_place_class; ?>"><?php echo $sign . abs($first_place_ch); ?></span>
                                </td>
                                <td class="text-center"><?php echo $comp_data_arr[$competitor_url[$com_url]]['top3']; ?></td>
                                <td class="text-center">
                                    <?php
                                    $new_top3 = $comp_data_arr[$competitor_url[$com_url]]['top3'];
                                    $old_top3 = $comp_data_arr[$competitor_url[$com_url]]['old_top3'];

                                    $top3_ch = $new_top3 - $old_top3;
                                    if ($top3_ch > 0) {
                                        $top3_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($top3_ch == 0) {
                                        $top3_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $top3_class = $label_arr['red'];
                                        $sign = "-";
                                    }
                                    ?>
                                    <span class="label label-sm label-hf-block <?php echo $top3_class; ?>"><?php echo $sign . abs($top3_ch); ?></span>
                                </td>
                                <td class="text-center"><?php echo $comp_data_arr[$competitor_url[$com_url]]['page1']; ?></td>
                                <td class="text-center">
                                    <?php
                                    $new_page1 = $comp_data_arr[$competitor_url[$com_url]]['page1'];
                                    $old_page1 = $comp_data_arr[$competitor_url[$com_url]]['old_page1'];

                                    $page1_ch = $new_page1 - $old_page1;
                                    if ($page1_ch > 0) {
                                        $page1_class = $label_arr['green'];
                                        $sign = "+";
                                    } else if ($page1_ch == 0) {
                                        $page1_class = $label_arr['blue'];
                                        $sign = "";
                                    } else {
                                        $page1_class = $label_arr['red'];
                                        $sign = "-";
                                    }
                                    ?>
                                    <span class="label label-sm label-hf-block <?php echo $page1_class; ?>"><?php echo $sign . abs($page1_ch); ?></span> 
                                </td>
                            </tr>
                        <?php } ?>
                    </thead>

                </table>
                <?php                      
            }
            else{
                    ?>
                    <div class="alert alert-danger">No competitor added for this location</div>
                    <?php
            }
            ?>
            </div>                        
            
        </div>
    <?php
}
include_once 'download_report.php';
?>
