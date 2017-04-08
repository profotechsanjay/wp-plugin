<?php

if($dwnldType=='csv'){
	$FilePath = "{$FileName}.csv";
	header('Content-Type: text/csv; charset=utf-8');
	header("Cache-Control: no-store, no-cache");
	header('Content-Disposition: attachment; filename=' . $FilePath);

	$fp = fopen('php://output', "w");

	//fputcsv($fp, $th);
	//ob_flush();

	//print header:
	//$csvArr = array('Client ID','Brand Name','Order ID','Order Date','Sites','Keyword','Title','Date Published','Status','URL','Status Date','DL','SS','SL','Writer Id','Writer Name','Worker Id','Worker Name','Approval User Id','Approval User Name');
	$csvArr = array('Order ID','Date Ordered','Date Status','Go Live Date','Status','Keyword','Title','URL');
	//if(isset($_GET['debug']))
	//	$csvArr = array_merge(array('Order ID'),$csvArr);

	fputcsv($fp, $csvArr);
	ob_flush();

	//print body:
	foreach($all_order as $in => $r_order){
		$csvArr = array();

		if(isset($_GET['debug'])){
			//$csvArr[] = $r_order->order_id;
			//$csvArr[] = empty($r_order->order_date)?'--':$r_order->order_date; //date('d M Y', strtotime($live_date))
			//$csvArr[] = empty($r_order->status_date)?'--':$r_order->status_date; //date('d M Y', strtotime($live_date))
		}
		$csvArr[] = $r_order->order_id;
		$csvArr[] = empty($r_order->order_date)?'--':$r_order->order_date; //date('d M Y', strtotime($live_date))
		$csvArr[] = empty($r_order->status_date)?'--':$r_order->status_date;
		$csvArr[] = empty($r_order->live_date)?'--':$r_order->live_date;

		//$rowTitle = html_entity_decode(orginal_html($r_order->content));
		$rowTitle = getReportRowTitle($r_order,$titlePlacer, 999999);
		/*$BRAND_NAME=get_user_meta($r_order->user_id, "BRAND_NAME", true);

		$csvArr[] = $r_order->user_id;
		$csvArr[] = $BRAND_NAME;
		$csvArr[] = $r_order->order_id;
		$csvArr[] = $r_order->order_date;
		$csvArr[] = $r_order->sites;*/

		$csvArr[] = $r_order->status;
		$csvArr[] = $r_order->keys;
		$csvArr[] = (empty($rowTitle)||$rowTitle==$titlePlacer)?'--':$rowTitle;
		$csvArr[] = empty($r_order->post_url)?'--':$r_order->post_url;

		/*$csvArr[] = ($r_order->status_date != "")?date('d M Y h:i:a', strtotime($r_order->status_date)):'';
		$csvArr[] = ($r_order->dl == '')? 'n/a' : $r_order->dl;
		$csvArr[] = ($r_order->ss == '')? 'n/a' : $r_order->ss;
		$csvArr[] = ($r_order->sl == '')? 'n/a' : $r_order->sl;


		$csvArr[] = $r_order->writer_id;
		$uName = $uEmail = '--';
		if($r_order->writer_id){
			$userInfo = get_userdata($r_order->writer_id);
			$uName = $userInfo->display_name;
			//$uEmail = $userInfo->user_email;
		}
		$csvArr[] = $uName;


		$csvArr[] = $r_order->worker_id;
		$uName = $uEmail = '--';
		if($r_order->worker_id){
			$userInfo = get_userdata($r_order->worker_id);
			$uName = $userInfo->display_name;
			//$uEmail = $userInfo->user_email;
		}
		$csvArr[] = $uName;


		$csvArr[] = $r_order->approval_user_id;
		$uName = $uEmail = '--';
		if($r_order->approval_user_id){
			$userInfo = get_userdata($r_order->approval_user_id);
			$uName = $userInfo->display_name;
			//$uEmail = $userInfo->user_email;
		}
		$csvArr[] = $uName;*/

		//if( empty($csvArr[4]) || $csvArr[4]=='--' || empty($csvArr[7]) || $csvArr[7]=='--' ){
			fputcsv($fp, $csvArr);
			ob_flush();
		//}
	}

	fclose($fp);


}else{
	ob_start();
        echo pdf_header();
	?>
        <div style="clear:both;height:20px;"></div>
        <div style="text-align:center;font-size: 24px;font-weight: bold;"><?php echo brand_name(user_id())?> Content Order Report</div>
        <div style="clear:both;height:20px;"></div>
	<style type="text/css">
		table td{
			vertical-align:top;
			white-space: pre-wrap; /* css-3 */
			white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
			white-space: -pre-wrap; /* Opera 4-6 */
			white-space: -o-pre-wrap; /* Opera 7 */
			word-wrap: break-word; /* Internet Explorer 5.5+ */
		}
	</style>
	<table style="width:100%;font-size:92%" class="display" cellspacing="0">
		<thead style="background-color:#888888;color:#ffffff;">
			<tr>
				<th style="width:18%">Keyword</th>
				<th style="width:22%">Title</th>
				<th style="width:8%">Date Published</th>
				<th style="width:8%">Status</th>
				<th style="width:35%">URL</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if (!empty($all_order)) {
				foreach ($all_order as $in => $r_order) {
					$rowTitle = getReportRowTitle($r_order,$titlePlacer);
					?>
					<tr style="text-align:left;font-size: 13px;">
						<td style="width:18%"><?php echo $r_order->keys; ?></td>
						<td style="width:22%"><?php echo (empty($rowTitle)||$rowTitle==$titlePlacer)?'--':$rowTitle; ?></td>
						<td style="width:8%"><?php echo empty($r_order->live_date)?'--':$r_order->live_date; ?></td>
						<td style="width:8%"><?php echo $r_order->status; ?></td>
						<td style="width:35%"><?php if(empty($r_order->post_url)): ?>--<?php else: ?><a style="text-decoration:none;" target="_blank" href="<?php echo $r_order->post_url; ?>"><?php echo $r_order->post_url; ?></a><?php endif; ?></td>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table><?php
	$html = ob_get_clean();

	//create PDF;
	require_once(ABSPATH."RankreportEmail/dompdf_config.inc.php");
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->set_paper('a4', 'landscape');
	$dompdf->render();
        $user_id = $UserID;
        include(ABSPATH . '/wp-content/themes/twentytwelve/common/pdf-footer.php');
	$pdf = $dompdf->output();
	$dompdf->stream("{$FileName}.pdf", array("Attachment" => true));
}
exit();



?>