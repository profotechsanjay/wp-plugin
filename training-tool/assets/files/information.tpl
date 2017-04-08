<?php 
global $wpdb;
if(!isset($print)) $print=false;
session_start();
 $sql = "SELECT dui.user_info, du.*
	  FROM {$wpdb->prefix}dvc_user AS du
	  LEFT JOIN {$wpdb->prefix}dvc_user_info AS dui
	  ON du.user_id = dui.user_id
	  WHERE du.user_id = '%d'";
	$sql = $wpdb->prepare($sql, $_SESSION['user_id']);
	$userinfo=$wpdb->get_row($sql);
	$currenuserinfo =  json_decode($userinfo->user_info);
?>
<div class="userinfowrap buyerinfo col-md-12 well">
	<h3 class="head_title">User Information</h3>
	<div class="buyer1 col-md-4">
		<span class="inline"><b>First Name:</b><?php echo $currenuserinfo->first_name; ?></span>
		<span class="inline"><b>Last Name:</b><?php echo $currenuserinfo->last_name; ?></span>
		<span class="inline"><b>Phone1:</b><?php echo $currenuserinfo->phone1; ?></span>
		<span class="inline"><b>Phone2:</b><?php echo $currenuserinfo->phone2; ?></span>
		<span class="inline"><b>Email:</b><?php echo $userinfo->email; ?></span>
	</div>
	<div class="buyer2 col-md-4">
		<span class="inline"><b>First Name:</b><?php echo $currenuserinfo->first_name2; ?></span>
		<span class="inline"><b>Last Name:</b><?php echo $currenuserinfo->last_name2; ?></span>
		<span class="inline"><b>Phone1:</b><?php echo $currenuserinfo->phone3; ?></span>
		<span class="inline"><b>Phone2:</b><?php echo $currenuserinfo->phone4; ?></span>
	</div>	
	<div class="common col-md-4">
		<span class="inline"><b>Address:</b><?php echo $currenuserinfo->address; ?></span>
		<span class="inline"><b>City: </b><?php echo $currenuserinfo->city; ?></span>
		<span class="inline"><b>State: </b><?php echo $buyer->state; ?></span>
		<span class="inline"><b>Zip: </b><?php echo $currenuserinfo->zip; ?></span>
		<span class="inline"><b>Country: </b><?php echo $currenuserinfo->Country; ?></span>
	</div>
</div>
<?php /*   user questioin part   */ ?>
<?php 
global $wpdb;
?>
<div class="col-md-12 well">
	<?php if(isset($_SESSION['role']) && $_SESSION['role']==1): ?>
		<?php 
			$sql = "SELECT *
			FROM {$wpdb->prefix}dvc_listings AS dl
			LEFT JOIN {$wpdb->prefix}dvc_listings_status AS dls
			ON dl.status_id = dls.status_id
			LEFT JOIN {$wpdb->prefix}dvc_resort AS dr
			ON dl.resort_id = dr.resort_id
			WHERE dl.seller_id = '".$_SESSION['user_id']."'";
			$listing = $wpdb->get_row($sql);
		?>
		<ul>
		  <li class="current_dues">
			<label>Are you current with the payments for this loan?</label>
			<?php if($print): ?>
				<?php echo $listing->current_dues === "1" ? 'Yes' : 'no'; ?>
			<?php else: ?>
				<div class="yesno">
					<label>Yes <input disabled name="current_dues" type="radio" value="1" <?php echo $listing->current_dues === "1" ? "checked" : ""; ?>></label>
					<label>No <input disabled name="current_dues" type="radio" value="0" <?php echo $listing->current_dues === "0" ? "checked" : ""; ?>></label>
				</div>
		    <?php endif; ?>	
		  </li>
		  <li class="mortage_li">
			<label>Are you currently involved in a Bankruptcy?</label>
			<?php if($print): ?>
				<?php echo $listing->mortage === "1" ? 'Yes' : 'no'; ?>
			<?php else: ?>
			<div class="yesno">
				<label>Yes<input disabled name="mortage" type="radio" class="mortage" value="1" <?php echo $listing->mortage === "1" ? "checked" : ""; ?> <?php echo in_array($release_status, array(2, 3)) ? 'disabled' : ''; ?>></label>
				<label>No<input disabled name="mortage" type="radio" class="mortage" value="0" <?php echo $listing->mortage === "0" ? "checked" : ""; ?> <?php echo in_array($release_status, array(2, 3)) ? 'disabled' : ''; ?>></label>
			</div>
			<?php endif; ?>	
		  </li>
		  <li class="monthly_payments_li" <?php echo $listing->mortage == "1" ? 'style="display: inline-block"' : ''; ?>>
			<label>Are you currently divorced or seperated?</label>
			<?php if($print): ?>
				<?php echo $listing->monthly_payments === "1" ? 'Yes' : 'no'; ?>
			<?php else: ?>
				<div class="yesno">
					<label>Yes <input disabled name="monthly_payments" type="radio" class="monthly_payments" value="1" <?php echo $listing->monthly_payments === "1" ? "checked" : "" ?> <?php echo in_array($release_status, array(2, 3)) ? 'disabled' : ''; ?>></label>
					<label>No <input disabled name="monthly_payments" type="radio" class="monthly_payments" value="0" <?php echo $listing->monthly_payments === "0" ? "checked" : ""; ?> <?php echo in_array($release_status, array(2, 3)) ? 'disabled' : ''; ?>></label>
				</div>
			<?php endif; ?>	
		  </li>
		  <!--<li class="foreclosure_li" <?php echo $listing->monthly_payments == "0" ? 'style="display: inline-block"' : ''; ?>>
			<label>Is this contract in the foreclosure process?</label>
			<div class="yesno">
				<label>Yes <input name="foreclosure" type="radio" class="foreclosure" value="1" <?php echo $listing->foreclosure === "1" ? "checked" : ""; ?> <?php echo in_array($release_status, array(2, 3)) ? 'disabled' : ''; ?>></label>
				<label>No <input name="foreclosure" type="radio" class="foreclosure" value="0" <?php echo $listing->foreclosure === "0" ? "checked" : ""; ?> <?php echo in_array($release_status, array(2, 3)) ? 'disabled' : ''; ?>></label>
			</div>
		  </li>-->
		  <li class="sign_closing">
			<label>Are all the signers of the origional contract available to sign the new closing documents?</label>
			<?php if($print): ?>
				<?php echo $listing->sign_closing === "1" ? 'Yes' : 'no'; ?>
			<?php else: ?>
				<div class="yesno">
					<label>Yes <input disabled name="sign_closing" type="radio" value="1" <?php echo $listing->sign_closing === "1" ? "checked" : ""; ?>></label>
					<label>No <input disabled name="sign_closing" type="radio" value="0" <?php echo $listing->sign_closing === "0" ? "checked" : ""; ?>></label>
				</div>
			<?php endif;  ?>	
		  </li>
		  <li class="reservations_li">
			<label>Do you have any pending reservations with these points after the estimated closing date?</label>
			<?php if($print): ?>
				<?php echo $listing->reservations === "1" ? 'Yes' : 'no'; ?>
			<?php else: ?>
				<div class="yesno">
					<label>Yes <input disabled name="reservations" type="radio" class="reservations" value="1" <?php echo $listing->reservations === "1" ? "checked" : ""; ?> <?php echo in_array($release_status, array(1, 3)) ? 'disabled' : ''; ?>></label>
					<label>No <input disabled name="reservations" type="radio" class="reservations" value="0" <?php echo $listing->reservations === "0" ? "checked" : "" ; ?> <?php echo in_array($release_status, array(1, 3)) ? 'disabled' : ''; ?>></label>
				</div>
			<?php endif; ?>	
		  </li>
		  <?php if($listing->reservations == "1"): ?>
			  <li class="stays info_reservations_li" <?php //echo $listing->reservations == "1" ? 'style="display: block"' : 'style="display: none"'; ?>>
			  COMPLETE ALL STAYS: All stays must be completed prior to the closing date of the sale. Reservations for stays after the sale of the Ownership Interest will be cancelled. Unused Vacation Points remaining in the account for the Ownership Interest that is sold will be transferred to the buyer’s account after the closing is completed
			  </li>
			 <?php endif; ?> 
		</ul>
	<?php elseif(isset($_SESSION['role']) && $_SESSION['role']==0): ?>
		<?php $sql= "SELECT *
				FROM {$wpdb->prefix}dvc_user_info where user_id='{$_SESSION['user_id']}'";   
				$listing = $wpdb->get_row($sql); 
				$userdata=json_decode($listing->user_info);
		?>
		<ul>
		  <li class="mortage_li">
			<label>Do you need financing?</label>
			<div class="yesno">
				<?php if($print): ?>
					<?php echo $userdata->financing === "1" ? 'Yes' : 'no'; ?>
				<?php else: ?>
				<label>Yes<input  type="radio" class="mortage" value="1" <?php echo $userdata->financing === "1" ? "checked" : ""; ?> disabled></label>
				<label>No<input  type="radio" class="mortage" value="0" <?php echo $userdata->financing === "0" ? "checked" : ""; ?> disabled></label>
				<?php endif; ?>
			</div>
		  </li>
		  <li class="reservations_li">
			<label>Are you currently a DVC Member?</label>
			<?php if($print): ?>
				<?php echo $userdata->isdvcmember === "1" ? 'Yes' : 'no'; ?>
			<?php else: ?>
			<div class="yesno">
				
				<label>Yes <input name="reservations" type="radio" class="reservations" value="1" <?php echo $userdata->isdvcmember === "1" ? "checked" : ""; ?> disabled></label>
				<label>No <input name="reservations" type="radio" class="reservations" value="0" <?php echo $userdata->isdvcmember === "0" ? "checked" : "" ; ?> disabled></label>
			</div>
			<?php endif; ?>
		  </li>
		  <?php if($userdata->isdvcmember==1):  ?>
			  <li class="reservations_li">
				<label>What is you Disney Membership Number?</label>
				<div class="yesno">
					<b><?php echo $userdata->membershipid; ?></b>
				</div>
			  </li>
		  <?php endif; ?>
		</ul>
	<?php endif; ?>	
<?php /* ?>	
<ul>
	<li class="current_dues">
        <label>Are you current on your <?php echo date("Y"); ?> Annual Dues?</label>
        <div class="yesno">
            <label><input disabled='disabled' name="current_dues" type="radio" value="1" <?php echo $listing->current_dues == "1" ? "checked" : ""; ?>> Yes</label>
            <label><input disabled='disabled' name="current_dues" type="radio" value="0" <?php echo $listing->current_dues == "0" ? "checked" : ""; ?>> No</label>
        </div>
      </li>
      <li class="mortage_li">
        <label>Is there a mortage on this contract?</label>
        <div class="yesno">
            <label><input disabled='disabled' name="mortage" type="radio" class="mortage" value="1" <?php echo $listing->mortage == "1" ? "checked" : "" ; ?>> Yes</label>
            <label><input disabled='disabled' name="mortage" type="radio" class="mortage" value="0" <?php echo $listing->mortage == "0" ? "checked" : ""; ?> > No</label>
        </div>
      </li>
      <li class="monthly_payments_li" <?php echo $listing->mortage == "1" ? 'style="display: inline-block"' : ''; ?>>
        <label>Are you current on your monthly payments?</label>
        <div class="yesno">
            <label><input disabled='disabled' name="monthly_payments" type="radio" class="monthly_payments" value="1" <?php echo $listing->monthly_payments == "1" ? "checked" : ""; ?>> Yes</label>
            <label><input disabled='disabled' name="monthly_payments" type="radio" class="monthly_payments" value="0" <?php echo $listing->monthly_payments == "0" ? "checked" : ""; ?>> No</label>
        </div>
      </li>
      <li class="sign_closing">
        <label>Are all the signers of the original contract available to sign the new closing documents?</label>
        <div class="yesno">
            <label><input disabled='disabled' name="sign_closing" type="radio" value="1" <?php echo $listing->sign_closing == "1" ? "checked" : ""; ?>> Yes</label>
            <label><input disabled='disabled' name="sign_closing" type="radio" value="0" <?php echo $listing->sign_closing == "0" ? "checked" : ""; ?>> No</label>
        </div>
      </li>
      <li class="reservations_li">
        <label>Do you have any pending reservations with these points after the estimated closing date?</label>
        <div class="yesno">
            <label><input disabled='disabled' name="reservations" type="radio" class="reservations" value="1" <?php echo $listing->reservations == "1" ? "checked" : ""; ?>> Yes</label>
            <label><input disabled='disabled' name="reservations" type="radio" class="reservations" value="0" <?php echo $listing->reservations == "0" ? "checked" : ""; ?>> No</label>
        </div>
        <?php  if($listing->reservations == "1"): ?>
			<div class="info_reservations_li">
				<strong>COMPLETE ALL STAYS</strong>
				<p>
				All stays must be completed prior to the closing date of the sale.Reservation for stays after the sale of the sale of the ownership interest will be calcelled. Unused vacation points remaining in the account for the ownership interest that is sold will be transferred to the buyer's account after the closing is completed.
				</p>
			</div>	
        <?php endif; ?>
      </li>
    </ul>
    * <?php */ ?>
</div> 
<?php /* questions part end */  ?>
