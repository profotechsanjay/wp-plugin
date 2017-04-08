<?php
session_start();

global $wpdb;
echo "SELECT * FROM ".database_name.".clients_table WHERE MCCUserId = %d", $_COOKIE['mccuserid']
?>
