<?php
/*
 Plugin Name: Training Tool
 Plugin URI: http://www.rudrainnovatives.com
 Description: Training Tool Plugin is a wordpress plugin.
 Author: Rudra Innnovative Software
 Version: 3.5
 Author URI: http://www.rudrainnovatives.com
*/

$dir = dirname(dirname(dirname(dirname(__FILE__))));
include_once $dir.'/global_config.php';

/* Variables used in Plugin */
if (!defined('TR_DEBUG_MODE'))    define('TR_DEBUG_MODE',  false );
if (!defined('TR_FILE'))       define('TR_FILE',  __FILE__ );
if (!defined('TR_CONTENT_DIR'))      define('TR_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('TR_CONTENT_URL'))      define('TR_CONTENT_URL', site_url() . '/wp-content');
if (!defined('TR_PLUGIN_DIR'))       define('TR_PLUGIN_DIR', TR_CONTENT_DIR . '/plugins');
if (!defined('TR_PLUGIN_URL'))       define('TR_PLUGIN_URL', TR_CONTENT_URL . '/plugins');
if (!defined('TR_PLUGIN_FILENAME'))  define('TR_PLUGIN_FILENAME',  basename( __FILE__ ) );
if (!defined('TR_PLUGIN_DIRNAME'))   define('TR_PLUGIN_DIRNAME',  plugin_basename(dirname(__FILE__)) );
if (!defined('TR_COUNT_PLUGIN_DIR')) define('TR_COUNT_PLUGIN_DIR', TR_PLUGIN_DIR.'/'.TR_PLUGIN_DIRNAME );
if (!defined('TR_COUNT_PLUGIN_URL')) define('TR_COUNT_PLUGIN_URL', site_url().'/wp-content/plugins/'.TR_PLUGIN_DIRNAME );        
if (!defined('PAGE_SLUG')) define('PAGE_SLUG', 'training-tool');
if (!defined('EMAIL_TYPE')) define('EMAIL_TYPE', 'training_tool');
if (!defined('ACCESS_ROLE')) define('ACCESS_ROLE', 'paid_client');
if (!defined('MENTOR_ROLE')) define('MENTOR_ROLE', '');
if (!defined('TT_VERSION')) define('TT_VERSION', '4.2');
if (!defined('CREVER')) define('CREVER', '1.4');

if (!defined('ACME_PLUGIN_SECRET_KEY')) define('ACME_PLUGIN_SECRET_KEY', '57126a1a971369.14426332'); //Rename this constant name so it is specific to your plugin or theme.
if (!defined('ACME_PLUGIN_LICENSE_SERVER_URL')) define('ACME_PLUGIN_LICENSE_SERVER_URL', 'http://112.196.32.246/desktop'); //Rename this constant name so it is specific to your plugin or
if (!defined('ACME_PLUGIN_ITEM_REFERENCE')) define('ACME_PLUGIN_ITEM_REFERENCE', 'Training Tool'); //Rename this constant name so it is specific to your plugin or theme.
if (!defined('TR_SITE_NAME')) define('TR_SITE_NAME', 'The Training Team At Enfusen');

if (!defined('CRE_SLUG')) define('CRE_SLUG', 'content-recommendation-engine');
if (!defined('CRE_DASH')) define('CRE_DASH', 'content-recommendation-dashboard');
if (!defined('CRE_SINGLE_MAX_RUN')) define('CRE_SINGLE_MAX_RUN', 5);

function courses(){
	global $wpdb;
	return $wpdb->prefix . 'courses';
}

function mentorcall(){
	global $wpdb;
	return $wpdb->prefix . 'mentorcall';
}

function modules(){
	global $wpdb;
	return $wpdb->prefix . 'modules';
}

function project_exercise(){
	global $wpdb;
	return $wpdb->prefix . 'project_exercise';
}

function projects(){
	global $wpdb;
	return $wpdb->prefix . 'projects';
}

function lessons(){
	global $wpdb;
	return $wpdb->prefix . 'lessons';
}

function resources(){
	global $wpdb;
	return $wpdb->prefix . 'resource_list';
}

function media(){
	global $wpdb;
	return $wpdb->prefix . 'media';
}

function enrollment(){
	global $wpdb;
	return $wpdb->prefix . 'enrollment';
}

function resource_status(){
	global $wpdb;
	return $wpdb->prefix . 'resource_status';
}

function setting(){
	global $wpdb;
	return $wpdb->prefix . 'setting';
}

function lesson_notes(){
	global $wpdb;
	return $wpdb->prefix . 'lesson_notes';
}

/*function for community_call*/
function community_call(){
    global $wpdb;
    return $wpdb->prefix . 'community_call';
}

function mentor_assign(){
	global $wpdb;
	return $wpdb->prefix . 'mentor_assign';
}


function survey_forms(){
	global $wpdb;
	return $wpdb->prefix . 'survey_forms';
}

function survey_results(){
	global $wpdb;
	return $wpdb->prefix . 'survey_results';
}

function course_mentors(){
	global $wpdb;
	return $wpdb->prefix . 'course_mentors';
}

function email_templates(){
	global $wpdb;
	return $wpdb->prefix . 'email_templates';
}

function TR_install_table()
{
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
        
        // CRE Tables
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "wp_content_recommend"')) == 0){
		$sql = 'CREATE TABLE wp_content_recommend(
		`id` int(11) NOT NULL AUTO_INCREMENT,
                `type` enum("targetpage","allpage") NOT NULL DEFAULT "targetpage",
                `user_id` int(11) DEFAULT NULL,
                `crawl_result` longtext,
                `scannedurls` longtext,
                `urlscanning` int(1) DEFAULT NULL,
                `trigger_report` int(1) DEFAULT "0",
                `auto_trigger` int(1) NOT NULL DEFAULT "1",
                `user_trigger` int(11) DEFAULT NULL,
                `result` longtext,
                `rundate` datetime DEFAULT NULL,
                `created_dt` datetime DEFAULT NULL,
                `updated_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
		
	}
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "cre_algovals"')) == 0){
		$sql = 'CREATE TABLE IF NOT EXISTS `cre_algovals` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `credata` text,                    
                    `created_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;';
		dbDelta($sql);
		
	}
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "cre_history"')) == 0){
		$sql = 'CREATE TABLE IF NOT EXISTS `cre_history` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `type` enum("targetpage","allpage") NOT NULL DEFAULT "targetpage",
                    `user_id` int(11) DEFAULT NULL,
                    `totalurls` int(11) DEFAULT NULL,
                    `totalissues` int(11) DEFAULT NULL,
                    `issues_detail` varchar(500) DEFAULT NULL,
                    `avg_score` varchar(50) DEFAULT NULL,
                    `rundate` datetime DEFAULT NULL,
                    `user_trigger` int(11),
                    `created_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;';
		dbDelta($sql);
		
	}                
              
               
        if (count($wpdb->get_var('SHOW TABLES LIKE "cre_urls"')) == 0){
            $sql = 'CREATE TABLE cre_urls(
            id int UNSIGNED NOT NULL AUTO_INCREMENT,   
            algo_id int(11),
            url varchar(500),
            keyword varchar(1000),
            user_id int,
            is_running int(1) default "0",
            result longtext,
            total_issues int(11),
            rundate datetime DEFAULT NULL,
            `user_trigger` int(11),
            created_dt timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,		
            PRIMARY KEY (id)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
            dbDelta($sql);
        }
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "cre_url_history"')) == 0){
		$sql = 'CREATE TABLE IF NOT EXISTS `cre_url_history` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `algo_id` int(11),
                `page_id` int(11),
                `url` varchar(500) DEFAULT NULL,
                `keyword` varchar(1000) DEFAULT NULL,
                `user_id` int(11) DEFAULT NULL,
                `is_running` int(1) DEFAULT "0",
                `result` longtext,
                `total_issues` int(11),
                `rundate` datetime DEFAULT NULL,
                `user_trigger` int(11),
                `created_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB  DEFAULT CHARSET=latin1';
		dbDelta($sql);
		
	}
        
        // Training Tool tables
        
	if (count($wpdb->get_var('SHOW TABLES LIKE "' . courses() . '"')) == 0){
		$sql = 'CREATE TABLE ' . courses() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
                ord int not null,
		title text NOT NULL,
		description text NOT NULL,
		total_hrs varchar(20),
		total_resources varchar(20),
                imgpath varchar(500),
                link varchar(500),
                mentor_ids text,
                enable_permission int(1) default "0",
                user_ids text,
		created_by int,
		created_dt datetime,
		updated_by int,
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
		
	}
        
	if (count($wpdb->get_var('SHOW TABLES LIKE "' . mentorcall() . '"')) == 0){
		$sql = 'CREATE TABLE ' . mentorcall() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
                guid varchar(100),
                course_id int not null,
                user_id int not null,                
                link varchar(500),
		mentor varchar(200) NOT NULL,
		mentor_call datetime,
                status enum("active","cancelled") not null default "active", 
                mentor_id int,
		created_by int,
                recur_call int default "0",
                parent_id int(1) default "0",
                is_accepted int(1) default "0",
                is_attended enum("pending","yes","no") default "pending",
		created_dt timestamp not null,		
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
		
	}
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . mentor_assign() . '"')) == 0){
		$sql = 'CREATE TABLE ' . mentor_assign() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,   
                course_id int(11) not null,
                user_id int not null,
                mentor_id int not null,		
		created_dt timestamp not null,		
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
		
	}
        
	if (count($wpdb->get_var('SHOW TABLES LIKE "' . modules() . '"')) == 0){
		$sql = 'CREATE TABLE ' . modules() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
                ord int not null,
		course_id int not null,                
		title text NOT NULL,
		description text NOT NULL,
                external_link text NOT NULL,
		total_hrs varchar(20),
		total_resources varchar(20),
		created_by int,
		created_dt datetime,
		updated_by int,
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}
	
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . project_exercise() . '"')) == 0){
		$sql = 'CREATE TABLE ' . project_exercise() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
                type enum("course","module") NOT NULL,
                status int default 1,
                module_id int NOT NULL,
                course_id int NOT NULL,		
		title text NOT NULL,
		description text NOT NULL,		
		total_hrs varchar(20),
		created_by int,
		created_dt datetime,
		updated_by int,
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);				
	}
        
        
	if (count($wpdb->get_var('SHOW TABLES LIKE "' . projects() . '"')) == 0){
		$sql = 'CREATE TABLE ' . projects() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id int not null,
                resource_id int not null,
                exercise_id int not null,
		links text,
		doc_files text,
		docs text,
		created_by int,
		created_dt datetime,
		updated_by int,
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);				
	}

	if (count($wpdb->get_var('SHOW TABLES LIKE "' . lessons() . '"')) == 0){
		$sql = 'CREATE TABLE ' . lessons() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
                ord int not null,
		module_id int not null,                
		title text NOT NULL,
		description text NOT NULL,
                external_link text NOT NULL,
		total_hrs varchar(20),
                total_resources varchar(20),
		created_by int,
		created_dt datetime,
		updated_by int,
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . lesson_notes() . '"')) == 0){
		$sql = 'CREATE TABLE ' . lesson_notes() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		lesson_id int,
                resource_id int,
		note text NOT NULL,                
		created_by int,
		created_dt datetime,
		updated_by int,
		updated_dt timestamp not null,	
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . resources() . '"')) == 0){
		$sql = 'CREATE TABLE ' . resources() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
                ord int not null,
                button_type enum("mark","submit") default "mark",
		course_id int not null,
                module_id int not null,
                lesson_id int not null,
		title text NOT NULL,
		description text NOT NULL,
                external_link text NOT NULL,
		total_hrs varchar(20),		
		created_by int,
		created_dt datetime,
		updated_by int,
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}
	
	if (count($wpdb->get_var('SHOW TABLES LIKE "' . media() . '"')) == 0){
		$sql = 'CREATE TABLE ' . media() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		lesson_id int,     
                resource_id int,
                type enum("document","video","image","link") default "document",		
                source enum("embed","iframe","upload") default "embed",		
		path text,
                extra_info text,
		created_by int,
		created_dt timestamp not null,		
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}

	/* make custom table of community call */
	if (count($wpdb->get_var('SHOW TABLES LIKE "' . community_call() . '"')) == 0){
		$sql = 'CREATE TABLE ' . community_call() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		mentor_call_id int,
		course_id int,
		call_heading text,
        type enum("document","video","image","link") default "document",		
        source enum("embed","iframe","upload") default "embed",		
		path text,
        extra_info text,
        comm_notes text,
        comm_hlp_links text,
        doc_files text,
        doc_file_links text,
		created_by int,
		created_dt timestamp not null,		
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;';
		dbDelta($sql);
				
	}

	if (count($wpdb->get_var('SHOW TABLES LIKE "' . enrollment() . '"')) == 0){
		$sql = 'CREATE TABLE ' . enrollment() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		course_id int not null,
		user_id int,		
		status enum("inprogress","completed","incompleted","closed") default "inprogress",
		created_dt datetime,		
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}                
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . survey_forms() . '"')) == 0){
		$sql = 'CREATE TABLE ' . survey_forms() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		mentor_id int not null,
		course_id int,	
                title varchar(500),
		data longtext,
		created_by int,	
                created_dt datetime,	
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . survey_results() . '"')) == 0){
		$sql = 'CREATE TABLE ' . survey_results() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
                guid varchar(100),
		survey_id int not null,
                mentor_id int not null,
                user_id int not null,
		course_id int,		
		data longtext,
                is_submitted int(1) default "0",
		created_by int,	
                created_dt datetime,	
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}
        
	if (count($wpdb->get_var('SHOW TABLES LIKE "' . resource_status() . '"')) == 0){
		$sql = 'CREATE TABLE ' . resource_status() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		enrollment_id int not null,
                course_id int not null,
		resource_id int not null,
		user_id int,		
		status int(1) default "0",		
		created_dt datetime,		
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
				
	}
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . setting() . '"')) == 0){
		$sql = 'CREATE TABLE ' . setting() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		keyname varchar(250) not null,		
		keyvalue varchar(250) not null,
                type varchar(100) not null,
                is_show int(1) default "1",
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
		                
                $wpdb->query(
                        
			$wpdb->prepare
			(
				"INSERT INTO ". setting() ."(keyname,keyvalue,type)VALUES(%s, %s, %s)",				
				"valid_licence",
				"",
                                ""
			) 
		);
                
                $wpdb->query(
                        
			$wpdb->prepare
			(
				"INSERT INTO ". setting() ."(keyname,keyvalue,type)VALUES(%s, %s, %s)",				
				"Office Hours recording",
				"",
                                "link"
			) 
		);
                
                $wpdb->query(
                        
			$wpdb->prepare
			(
				"INSERT INTO ". setting() ."(keyname,keyvalue,type)VALUES(%s, %s, %s)",				
				"Google+ Community",
				"https://www.google.co.in",
                                "link"
			) 
		);
                
                $wpdb->query(
                        
			$wpdb->prepare
			(
				"INSERT INTO ". setting() ."(keyname,keyvalue,type)VALUES(%s, %s, %s)",				
				"Help",
				"",
                                "link"
			) 
		);
                
                
	}
        
        if (count($wpdb->get_var('SHOW TABLES LIKE "' . email_templates() . '"')) == 0){
		$sql = 'CREATE TABLE ' . email_templates() .'(
		id int UNSIGNED NOT NULL AUTO_INCREMENT,
		template varchar(100) not null,
                subject varchar(500) not null,		
                content text not null,
		created_dt datetime,		
		updated_dt timestamp not null,
		PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci';
		dbDelta($sql);
                
                $now = date("Y-m-d H:i:s");
                     
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "course_permission_granted",
                        "Permisssion granted for {{course_title}} course",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div>The team at Enfusen has granted you access to {{course_title}} course.</div>
                        <div><a href="{{url}}">Click here to view your course</a></div>
                        <div></div>
                        <div>Thanks,
                        {{site_name}}</div>',
                        $now
                    ) 
		);
               /* 
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "course_permission_revoked",
                        "Permisssion revoked for {{course_title}} course",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div>The team at Enfusen has revoked your permissions for {{course_title}} course.</div>
                        <div>For any query, please feel free to contact</div>
                        <div></div>
                        <div>

                        Thanks,
                        {{site_name}}

                        </div>',
                        $now
                    ) 
		);
                
                */
                
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "mentor_added",
                        "Added as mentor to {{course_title}} course",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div>
                        <div>The team at Enfusen has added you as a mentor for {{course_title}} course.</div>
                        <div>Login your account to know more about course.</div>
                        <div></div>
                        Thanks,
                        {{site_name}}

                        </div>',
                        $now
                    ) 
		);
                
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "mentor_removed",
                        "Removed as mentor from {{course_title}} course",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div></div>
                        <div>The team at Enfusen removed you from {{course_title}} course.</div>
                        For any query, please feel free to contact.
                        <div>

                        Thanks,
                        {{site_name}}

                        </div>',
                        $now
                    ) 
		);
                
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "survey_send",
                        "Survey sent regarding your mentor {{mentor_name}}",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div></div>
                        <div>This is the survey regarding your mentor {{mentor_name}}.</div>
                        <div><a href="{{url}}">Please Click Here Fill Survey</a></div>
                        <div></div>
                        <div>
                        <div></div>
                        <div>Thanks,
                        {{site_name}}</div>
                        </div>',
                        $now
                    ) 
		);
                
                
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "survey_result_user",
                        "Thanks for Survey",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div></div>
                        <div>

                        Thanks for submitting survey {{survey_title}} regarding your mentor {{mentor_name}}.
                        <a href="{{url}}">Click Here To Check Your Survey</a>

                        Thanks,
                        {{site_name}}

                        </div>',
                        $now
                    ) 
		);
			
			/*Submitting Servey regarding Course - custom*/
			$wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "survey_send_course",
                        "Survey sent regarding your Course {{course_name}}",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div></div>
                        <div>This is the survey regarding your course {{course_name}}.</div>
                        <div><a href="{{my_url}}">Please Click Here Fill Survey</a></div>
                        <div></div>
                        <div>
                        <div></div>
                        <div>Thanks,
                        {{site_name}}</div>
                        </div>',
                        $now
                    ) 
		);
			/*custom template for sending mail to notify mentor , project submission*/
			$wpdb->query(
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "project_submission",
                        "Work Status Notification",
                        '<div>Hi {{mentor_name}},</div>
						<div></div>
						<div></div>
						<div>

						User {{student_name}} ( {{student_email}} ) has {{status}} 
						Exercise : {{exercise_name}}
						Course : {{course_name}}
						{{work_files}}

						</div>
						<div>
						<div>Thanks,
						{{site_name}}</div>
						</div>',
                        $now
                    ) 
		);
                
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "survey_result_mentor",
                        "New Survey Submitted",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div>User {{survey_user}} has been submitted the survey for {{survey_title}} form.</div>
                        <div>

                        <a href="{{url}}">Click Here To Check Survey</a>

                        Thanks,
                        {{site_name}}

                        </div>',
                        $now
                    ) 
		);
                
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "mentor_call",
                        "Mentor Call For Course {{course_title}}",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div>This is to inform you that your mentor {{mentor_name}} {{scehulde_or_reschedule}} a call on date {{call_date}} for {{course_title}}</div>
                        <div>Link for meeting is: {{meeting_link}}<a href="{{url}}">Click Here To Accept Invitation And Notify Your Mentor</a>

                        Thanks,
                        {{site_name}}

                        </div>',
                        $now
                    ) 
		);
                
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "mentor_call_cancel",
                        "Mentor Call Cancelled For Course {{course_title}}",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div>

                        This is to inform you that your mentor {{mentor_name}} cancelled call for {{course_title}} course on date {{call_date}}

                        Thanks,
                        {{site_name}}

                        </div>',
                        $now
                    ) 
		);
                
                $wpdb->query(
                        
                    $wpdb->prepare
                    (
                        "INSERT INTO ". email_templates() ."(template,subject,content,created_dt)"
                        . "VALUES(%s, %s, %s, '%s')",				
                        "mentor_call_reminder",
                        "Mentor call reminder for {{course_title}} course",
                        '<div>Hi {{username}},</div>
                        <div></div>
                        <div>

                        This is to remind you about mentor call on date {{call_date}}
                        Detail you can find bewlow :
                        <div>Course: {{course_title}}</div>
                        <div>Mentor: {{mentor_name}}</div>
                        <div>Link: {{meeting_link}}</div>
                        <div>Call Date: {{call_date}}</div>
                        <div></div>
                        <div>Thanks,
                        {{site_name}}</div>
                        </div>',
                        $now
                    ) 
		);
                
	}
        
	
}

register_activation_hook(__FILE__,'TR_install_table');


// plugin menus
function menus()
{
        global $wpdb;
        $c_id = get_current_user_id();
        $user = new WP_User($c_id);
        $u_role =  $user->roles[0];
        
        if($u_role == 'administrator' || isagencylocation()){            
            
            add_menu_page('Training Tool', 'Training Tool', $u_role, 'triningtool', 'triningtool','');
            add_submenu_page('triningtool', 'triningtool', 'Courses', $u_role, 'triningtool', 'triningtool');
            add_submenu_page('triningtool', 'triningtool', 'New Course', $u_role, 'new_course', 'new_course');
            add_submenu_page('triningtool', 'triningtool', 'Settings', $u_role, 'settings', 'settings');
            add_submenu_page('triningtool', 'triningtool', 'Image By Course', $u_role, 'course_images', 'course_images');            
            add_submenu_page('triningtool', 'triningtool', 'Course Admin', $u_role, 'course_admin', 'course_admin');
            add_submenu_page('triningtool', 'triningtool', 'Mentor Calls', $u_role, 'manage_mentor_calls', 'manage_mentor_calls');
            add_submenu_page('triningtool', 'triningtool', 'Map Mentors', $u_role, 'map_mentors', 'map_mentors');                        
            add_submenu_page('triningtool', 'triningtool', 'Surveys', $u_role, 'surveys', 'surveys');
            add_submenu_page('triningtool', 'triningtool', 'New Survey', $u_role, 'new_survey', 'new_survey');
            add_submenu_page('triningtool', 'triningtool', 'Email Templates', $u_role, 'templates_email', 'templates_email');
            
            add_submenu_page('triningtool', '', '', $u_role, 'edit_course', 'edit_course');
            add_submenu_page('triningtool', '', '', $u_role, 'add_community_call', 'add_community_call');
            add_submenu_page('triningtool', '', '', $u_role, 'all_community_calls', 'all_community_calls');
            add_submenu_page('triningtool', '', '', $u_role, 'course_detail', 'course_detail');
            add_submenu_page('triningtool', '', '', $u_role, 'module_detail', 'module_detail');
            add_submenu_page('triningtool', '', '', $u_role, 'lesson_detail', 'lesson_detail');
            add_submenu_page('triningtool', '', '', $u_role, 'progress_detail', 'progress_detail');
            add_submenu_page('triningtool', '', '', $u_role, 'course_admin_mentors', 'course_admin_mentors');


            add_submenu_page('triningtool', '', '', $u_role, 'add_module', 'add_module');
            add_submenu_page('triningtool', '', '', $u_role, 'edit_module', 'edit_module');

            add_submenu_page('triningtool', '', '', $u_role, 'add_lesson', 'add_lesson');
            add_submenu_page('triningtool', '', '', $u_role, 'edit_lesson', 'edit_lesson');        

            add_submenu_page('triningtool', '', '', $u_role, 'add_exercise', 'add_exercise');
            add_submenu_page('triningtool', '', '', $u_role, 'resource_detail', 'resource_detail');
            add_submenu_page('triningtool', '', '', $u_role, 'manage_survey', 'manage_survey');
            add_submenu_page('triningtool', '', '', $u_role, 'survey_result', 'survey_result');
            add_submenu_page('triningtool', '', '', $u_role, 'call_detail', 'call_detail');
            add_submenu_page('triningtool', '', '', $u_role, 'user_record', 'user_record');
                        
            add_menu_page('Content Recommendation Engine', 'CRE Tool', $u_role, 'cre_options_page', 'cre_options_page','');
            add_submenu_page('cre_options_page', 'cre_options_page', 'CRE', $u_role, 'cre_options_page', 'cre_options_page');
            add_submenu_page('cre_options_page', 'cre_options_page', 'CRE DEMO', $u_role, 'cre_options_new_page', 'cre_options_new_page');
        }
        else{
                        
            add_menu_page('Training Tool', 'Training Tool', $u_role, 'course_admin', 'course_admin','');            
            add_submenu_page('course_admin', 'course_admin', 'Course Admin', $u_role, 'course_admin', 'course_admin');
            add_submenu_page('course_admin', 'course_admin', 'Mentor Calls', $u_role, 'manage_mentor_calls', 'manage_mentor_calls');
            add_submenu_page('course_admin', 'manage_mentor_calls', 'Surveys', $u_role, 'surveys', 'surveys');
            add_submenu_page('course_admin', 'course_admin', 'New Survey', $u_role, 'new_survey', 'new_survey');
            add_submenu_page('course_admin', '', '', $u_role, 'progress_detail', 'progress_detail');
            add_submenu_page('course_admin', '', '', $u_role, 'manage_survey', 'manage_survey');
            add_submenu_page('course_admin', '', '', $u_role, 'survey_result', 'survey_result');
            add_submenu_page('course_admin', '', '', $u_role, 'call_detail', 'call_detail');
            add_submenu_page('course_admin', '', '', $u_role, 'user_record', 'user_record');
        }
	
}
add_action('admin_menu', 'menus');

function isagencylocation(){
    
    // temp solution
    return true;
    
    if(administrator_permission()){
        return true;
    }      
    if (get_user_meta(user_id(), 'USER_LEVEL', true) == 'level_5') {

        return true;
    } else {

        return false;
    }
}

function cre_options_page(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/back_cre.php';
}
function cre_options_new_page(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/backcre_new.php';
}
function triningtool(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/courses-list.php';	
}

/*custom*/
function add_community_call(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/add_community_call.php';
}
function all_community_calls(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/all_community_calls.php';
}
/* ../ends*/

function course_admin(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/course_reports.php';	
}
function course_admin_mentors(){
    global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/course_admin_mentors.php';	
}

function new_course(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/new_course.php';	
}
function edit_course(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/edit_course.php';	
}
function course_detail(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/back_course_detail.php';	
}
function module_detail(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/module_detail.php';	
}
function lesson_detail(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/lesson_detail.php';	
}

function settings(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/settings.php';	
}

function add_module(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/add_module.php';	
}

function edit_module(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/edit_module.php';	
}

function add_lesson(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/add_lesson.php';	
}

function edit_lesson(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/edit_lesson.php';	
}

function add_exercise(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/add_resource.php';	
}

function resource_detail(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/resource_detail.php';	
}
function course_images(){
	global $wpdb;
	include_once TR_COUNT_PLUGIN_DIR . '/views/course_images.php';	
}
function progress_detail(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/progress_detail.php';	
}
function mentor_calls(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/mentor_calls.php';	
}

function manage_mentor_calls(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/manage_mentor_calls.php';	
}

function map_mentors(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/map_mentors.php';	
}

function surveys(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/surveys.php';	
}
function new_survey(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/new_survey.php';	
}
function manage_survey(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/manage_survey.php';	
}
function survey_result(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/survey_result.php';	
}

function call_detail(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/call_detail.php';	
}

function user_record(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/user_record.php';	
}

function templates_email(){
    global $wpdb;
    include_once TR_COUNT_PLUGIN_DIR . '/views/templates_email.php';	
}

function scriptsstyles_function(){
        
	$slug = '';
        if(isset($_REQUEST['page']) && $_REQUEST['page'] != ''){
            $slug = trim($_REQUEST['page']); 
        }
       
        $arr = array("triningtool","new_course","settings","edit_course","course_detail","add_module",
            "edit_module","module_detail","add_lesson","edit_lesson","lesson_detail","add_exercise","resource_detail",
            "course_images","course_admin","progress_detail",'mentor_calls','manage_mentor_calls','map_mentors','surveys',
            'new_survey','manage_survey','survey_result','call_detail','user_record','course_admin_mentors','templates_email','add_community_call');
        
        if(in_array($slug, $arr)) {                
                // register your script location, dependencies and version and enqueue the script
                //wp_enqueue_script('jquery');	                        
                                           
                wp_enqueue_script('vendor.js', TR_COUNT_PLUGIN_URL .'/assets/js/vendor.js?ver=','', TT_VERSION);
                wp_enqueue_script('jquery-ui.min.js', TR_COUNT_PLUGIN_URL .'/assets/js/jquery-ui.min.js');                                
                wp_enqueue_script('formbuilder.js', TR_COUNT_PLUGIN_URL .'/assets/js/formbuilder.js?ver=','', TT_VERSION);
                wp_enqueue_script('jquery.datetimepicker.full.js', TR_COUNT_PLUGIN_URL .'/assets/js/jquery.datetimepicker.full.js');                

                wp_enqueue_script('bootstrap.js', TR_COUNT_PLUGIN_URL .'/assets/js/bootstrap.js');
                wp_enqueue_script('jquery.visible.min.js', TR_COUNT_PLUGIN_URL .'/assets/js/jquery.visible.min.js');
                
                wp_enqueue_script('jquery.validate.js', TR_COUNT_PLUGIN_URL .'/assets/js/jquery.validate.js');
                
                wp_enqueue_script('jquery.dataTables.js', TR_COUNT_PLUGIN_URL .'/assets/js/jquery.dataTables.js');

                
                
                wp_enqueue_script('formrenderer.uncompressed.js', TR_COUNT_PLUGIN_URL .'/assets/js/formrenderer.uncompressed.js?ver=','', TT_VERSION);
               
                wp_enqueue_script('chosen.jquery.js', TR_COUNT_PLUGIN_URL .'/assets/js/chosen.jquery.js?ver=','', TT_VERSION);
                wp_enqueue_script('script.js', TR_COUNT_PLUGIN_URL .'/assets/js/script.js?ver=','', TT_VERSION);


                // style        
                                
                
                wp_enqueue_style('style.css', TR_COUNT_PLUGIN_URL .'/assets/css/style.css','', TT_VERSION);
                wp_enqueue_style('bootstrap.css', TR_COUNT_PLUGIN_URL .'/assets/css/bootstrap.css','', TT_VERSION);               
                wp_enqueue_style('jquery.datetimepicker.css', TR_COUNT_PLUGIN_URL .'/assets/css/jquery.datetimepicker.css');
                wp_enqueue_style('font-awesome.min.css', TR_COUNT_PLUGIN_URL .'/assets/css/font-awesome.min.css');
                wp_enqueue_style('jquery.dataTables.css', TR_COUNT_PLUGIN_URL .'/assets/css/jquery.dataTables.css');  
                
                                
                wp_enqueue_style('vendor.css', TR_COUNT_PLUGIN_URL .'/assets/css/vendor.css');                                  
                wp_enqueue_style('formbuilder.css', TR_COUNT_PLUGIN_URL .'/assets/css/formbuilder.css');  
                wp_enqueue_style('preview.css', TR_COUNT_PLUGIN_URL .'/assets/css/preview.css');  
                
                
                wp_enqueue_style('chosen.css', TR_COUNT_PLUGIN_URL .'/assets/css/chosen.css');  
                wp_enqueue_style('formrenderer.uncompressed.css', TR_COUNT_PLUGIN_URL .'/assets/css/formrenderer.uncompressed.css');                                  
                wp_enqueue_style('components.min.css', site_url() . '/wp-content/themes/twentytwelve/report-theme/assets/global/css/components.min.css');
                
        }
	
}

add_action('init','scriptsstyles_function');

if(isset($_REQUEST['action'])){    
	switch($_REQUEST['action']){
		case "training_lib":
		add_action( 'admin_init', 'training_lib' );
		function training_lib(){
			global $wpdb;
			include_once TR_COUNT_PLUGIN_DIR . '/library/training-lib.php';
		}
		break;
	}
	
}

function all_courses_shortcode(){
	include_once TR_COUNT_PLUGIN_DIR . '/views/all_courses.php';
}


add_shortcode('all_courses', 'all_courses_shortcode');

function course_detail_shortcode(){
	include_once TR_COUNT_PLUGIN_DIR . '/views/course_detail.php';
}

add_shortcode('course_detail', 'course_detail_shortcode');


function content_recommend_shortcode(){
	include_once TR_COUNT_PLUGIN_DIR . '/views/content_recommend.php';
}

add_shortcode('content_recommend', 'content_recommend_shortcode');

function cre_dashboard_shortcode(){
	include_once TR_COUNT_PLUGIN_DIR . '/views/cre_dashboard.php';
}

add_shortcode('cre_dashboard', 'cre_dashboard_shortcode');

function page_url_profile_shortcode(){
	include_once TR_COUNT_PLUGIN_DIR . '/views/page_url_profile.php';
}

add_shortcode('page_url_profile', 'page_url_profile_shortcode');

function dashboard_content_shortcode(){
	include_once TR_COUNT_PLUGIN_DIR . '/views/dashboard_content.php';
}

add_shortcode('dashboard_content', 'dashboard_content_shortcode');

add_filter( 'page_template', 'training_page_template_tool' );

function training_page_template_tool($page_template){
	global $post;
	$post_slug = $post->post_name;  
	if (the_slug_exists($post_slug) && $post_slug == 'training-tool'){
		$page_template = TR_COUNT_PLUGIN_DIR. '/views/front-trining-template.php';	
	} 
	return $page_template;
}


function the_slug_exists($post_name) {
    global $wpdb;
    $posts = $wpdb->prefix."posts";
    $sql ="SELECT post_name FROM $posts WHERE post_name = '" . $post_name . "'";    
    if($wpdb->get_row($sql, 'ARRAY_A')) {
        return true;
    } else {
        return false;
    }
}

function create_pages(){
    global $wpdb;
    $wp_rewrite = new WP_Rewrite();
    
    $slug = PAGE_SLUG;

    if (!the_slug_exists($slug)){
        $_p = array();
        $_p['post_title']     = "Training Tool";
        $_p['post_content']   = "[course_detail]";
        $_p['post_status']    = 'publish';
        $_p['post_slug']    = $slug;
        $_p['post_type']      = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status']    = 'closed';    
        wp_insert_post($_p);        
    } 
    
    
    $creslug = CRE_SLUG;
    if (!the_slug_exists($creslug)){
        $_p = array();
        $_p['post_title']     = "Content Recommendation Engine";
        $_p['post_content']   = "[content_recommend]";
        $_p['post_status']    = 'publish';
        $_p['post_slug']    = $creslug;
        $_p['post_type']      = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status']    = 'closed';    
        wp_insert_post($_p);        
    }
    
    $credash = CRE_DASH;
    if (!the_slug_exists($credash)){
        $_p = array();
        $_p['post_title']     = "Content Recommendation Dashboard";
        $_p['post_content']   = "[cre_dashboard]";
        $_p['post_status']    = 'publish';
        $_p['post_slug']    = $creslug;
        $_p['post_type']      = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status']    = 'closed';    
        wp_insert_post($_p);        
    }
    
    
    
}

register_activation_hook(__FILE__,'create_pages');

function check_has_target_pages($user_id){
    global $wpdb;    
    $UserID = $user_id;
    $keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);
    if (!empty($keywordDat)) {        

        $activation = $keywordDat["activation"];
        $target_keyword = $keywordDat["target_keyword"];                

    } else {
        $keywordDat['keyword_count'] = 0;
    }
    
    $order_by_con = 'desc';
    $sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" order by `meta_value` desc';
    $KeyWordQuery = $wpdb->get_results($sql);     
    $target_pages = array();
    $ik = 0;
    foreach ($KeyWordQuery as $row_key) {    
        $ks = str_replace("LE_Repu_Keyword_", "", $row_key->meta_key);
        $j = $ks - 1;
        if ($delete[$j] == 0) {   

            if ($activation[$j] != 'inactive') {            
                $ik = 1;
                break;
            }
        }
    }
    if($ik == 0){
        return FALSE;
    }
    return TRUE;
}

function target_pages($user_id){
        
    global $wpdb;    
    $UserID = $user_id;
    $keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);
    if (!empty($keywordDat)) {        

        $activation = $keywordDat["activation"];
        $Synonyms_keyword = $keywordDat["Synonyms_keyword"];
        $target_keyword = $keywordDat["target_keyword"];
        $delete = $keywordDat["delete"];
        $landingpage = $keywordDat["landing_page"];

    } else {
        $keywordDat['keyword_count'] = 0;
    }
    
    $order_by_con = 'desc';
    $sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" order by `meta_value` desc';
    $KeyWordQuery = $wpdb->get_results($sql);     

    
    $target_pages = array();
    $xd = 0;
    foreach ($KeyWordQuery as $row_key) {    
        $ks = str_replace("LE_Repu_Keyword_", "", $row_key->meta_key);
        
        $j = $ks - 1;
        if ($delete[$j] == 0) {   

            if ($activation[$j] != 'inactive') {            
                $page = trim(trim($landingpage[$j][0]),"/");                
                $keyword = trim(get_user_meta($UserID, "LE_Repu_Keyword_" . $ks . "",TRUE));       
                
                if($page != ''){
                    
                    
                    $matched = 0;     
                    foreach($target_pages as $targe){                        
                        
                        if($targe->{0} == $page){
                            $matched = 1;
                            $total = count((array) $targe);
                            $targe->{$total} = array("type" => 'primary','keyword' => $keyword); 
                            
                            // synonyms
                            $tot = $total;                        
                            if(!empty($Synonyms_keyword[$j])){                                
                                for ($h = 0; $h < 5; $h++) {
                                    if (trim($Synonyms_keyword[$j][$h]) != "")  {                                
                                        $tot++;
                                        $targe->{$tot} = array("type" => 'synonym','keyword' => $Synonyms_keyword[$j][$h], 'synonymof' => $keyword); 
                                    }
                                } 
                            }
                            
                            break;
                        }
                    }
                    
                    
                    if($matched == 0){
                        $obj = new stdClass();
                        $obj->{0} = appendhttp($page);                    
                        $obj->{1} = array("type" => 'primary','keyword' => $keyword);;
                        
                        // synonyms
                        $tot = 1;                        
                        if(!empty($Synonyms_keyword[$j])){                                
                            for ($h = 0; $h < 5; $h++) {
                                if (trim($Synonyms_keyword[$j][$h]) != "")  {                                
                                    $tot++;
                                    $obj->{$tot} = array("type" => 'synonym','keyword' => $Synonyms_keyword[$j][$h], 'synonymof' => $keyword);
                                }
                            } 
                        }
                        
                        array_push($target_pages, $obj);
                    }
                    
                    
                    
                    
                }
            }
        }
    }
    
//    $newar = array();
//    foreach($target_pages as $target_pag){
//        if(!in_array($target_pag->{0}, $newar)){
//            
//        }
//    }
    
    
    $outerarr = array();
    $outerarr['robots'] = 1;
    $outerarr['sitemap'] = 1;
    $outerarr['urls'] = $target_pages;
    
    return $outerarr;
    
}

function web_all_pages($user_id){
    global $wpdb;          
    $sql = 'select distinct url from cre_urls where user_id = %d';
    $alldbpages = $wpdb->get_results
    (
        $wpdb->prepare
        (
            $sql,$user_id
        )
    ); 
    
    $allpages = '';    
    foreach($alldbpages as $alldbpage){
        $page = trim(trim(str_replace(array("http://","https://","www."), array("","",""), $alldbpage->url),"/"));
        $allpages .= "'".$page."',";
    }            
    
    if($allpages != ''){
        $allpages = substr($allpages, 0, -1);
    }
    
    return $allpages;
}

function web_target_pages($user_id){
        
    global $wpdb;    
    $UserID = $user_id;
    $keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);
    if (!empty($keywordDat)) {        

        $activation = $keywordDat["activation"];
        $target_keyword = $keywordDat["target_keyword"];
        $delete = $keywordDat["delete"];
        $landingpage = $keywordDat["landing_page"];

    } else {
        $keywordDat['keyword_count'] = 0;
    }
    
    $order_by_con = 'desc';
    $sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" order by `meta_value` desc';
    $KeyWordQuery = $wpdb->get_results($sql);     
    $target_pages = array();
    $tpages = '';
    foreach ($KeyWordQuery as $row_key) {    
        $ks = str_replace("LE_Repu_Keyword_", "", $row_key->meta_key);
        $j = $ks - 1;
        if ($delete[$j] == 0) {   

            if ($activation[$j] != 'inactive') {            
                $page = trim(trim($landingpage[$j][0]),"/");                
                if($page != ''){
                   $page = trim(trim(str_replace(array("http://","https://","www."), array("","",""), $page),"/"));
                   $tpages .= "'".$page."',";
                }
            }
        }
    }
    if($tpages != ''){
        $tpages = substr($tpages, 0, -1);
    }
    
    return $tpages;
    
}

function target_page_keywords($url, $user_id){
        
    global $wpdb;    
    $url = trim(str_replace(array("http://","https://","www."), array("","",""), $url),"/");
    $UserID = $user_id;
    $keywordDat = get_user_meta($UserID, "Content_keyword_Site",TRUE);
    if (!empty($keywordDat)) {        

        $activation = $keywordDat["activation"];
        $Synonyms_keyword = $keywordDat["Synonyms_keyword"];
        $target_keyword = $keywordDat["target_keyword"];
        $delete = $keywordDat["delete"];
        $landingpage = $keywordDat["landing_page"];

    } else {
        $keywordDat['keyword_count'] = 0;
    }
    
    $order_by_con = 'desc';
    $sql = 'select meta_key, meta_value from wp_usermeta where user_id = ' . $UserID . ' and meta_key like "LE_Repu_Keyword_%" order by `meta_value` desc';
    $KeyWordQuery = $wpdb->get_results($sql);     
    $target_pages = array();
    $keywordstarg = array();
    foreach ($KeyWordQuery as $row_key) {    
        $ks = str_replace("LE_Repu_Keyword_", "", $row_key->meta_key);
        $j = $ks - 1;
        if ($delete[$j] == 0) {   

            if ($activation[$j] != 'inactive') {            
                $page = trim(trim($landingpage[$j][0]),"/");  
                $pag = trim(str_replace(array("http://","https://","www."), array("","",""), $page),"/");                
                
                if($pag == $url){
                    $keyword = trim(get_user_meta($UserID, "LE_Repu_Keyword_" . $ks . "",TRUE));  
                    $keywordstarg[] = array("type" => 'primary', 'keyword' => $keyword);
                    
                    // synonyms                          
                    if(!empty($Synonyms_keyword[$j])){                                
                        for ($h = 0; $h < 5; $h++) {
                            if (trim($Synonyms_keyword[$j][$h]) != "")  {                                
                                $keywordstarg[] = array("type" => 'synonym','keyword' => $Synonyms_keyword[$j][$h], 'synonymof' => $keyword);
                            }
                        } 
                    }
                    
                }                
            }
        }
    }
   
    return $keywordstarg;    
}


// Content Recomm Code Started

function crawl_page($user_id, $url){
    
    global $wpdb;
    // target pages    
    $ar = array();    
    $url = trim(trim($url,"/"));    
    
    $withhttps = "https://".str_replace(array("https://","http://"), array("",""), $url);
    $sts = get_remote_status($withhttps);
    if($sts == 200){
        $site_url = $url = $withhttps; 
    }
    else{
        $site_url = appendhttp($url); 
    }
    
    // start check if url is moved or not
    $urlheaders = get_headers($site_url, 1);
    $st = isset($urlheaders[0])?$urlheaders[0]:'';
    if($st != ''){
        $st = explode("301", $st);
        if(count($st) >= 2){
            if(isset($urlheaders['Location']) && $urlheaders['Location'] != ''){
                $site_url = $urlheaders['Location'];
            }
        }
    }
    // end check if url is moved or not
       
    $roboturl = $site_url.'/robots.txt';     
    $sitemapline = 0;
    $sitemapurl = '';
    $hascontent = '';        
    $ar['robots'] = 1;    
    if (!homeurlcheck($roboturl)){
        $ar['robots'] = 0;
    }
        
    if($sitemapline == 0){
        // case forSEO yoast plugin
        $site_map_url = $site_url.'/sitemap_index.xml';       
        if (homeurlcheck($site_map_url)){
            $sitemapurl = $site_map_url;
            $ar['yoast'] = 1;
            $sitemapline = 1;
        }        
    }
    
    if($sitemapline == 0){
        $site_map_url = $site_url.'/sitemap.xml';        
        if (homeurlcheck($site_map_url)){
            $sitemapurl = $site_map_url;
            $sitemapline = 1;
        }                
    }
    
        
    if($sitemapline == 0){
        $site_map_url = $site_url.'/sitemap';        
        if (homeurlcheck($site_map_url)){
            $sitemapurl = $site_map_url;
            $sitemapline = 1;
        }                
    }
        
    $robotfile = file_get_contents($roboturl);    
    $file = explode("\n",$robotfile);
    if($sitemapline == 0){
        foreach($file as $f){
            if (strpos($f, 'sitemap.xml') !== false) {       
                preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $f, $match);
                $url = isset($match[0][0])?$match[0][0]:'';
                if($url == ''){
                    continue;
                }
                $name = trim(strtolower(basename($url)));
                if($name == 'sitemap.xml'){                
                    $sitemapurl = $url;
                    $sitemapline = 1;
                    break;
                }
            }
        }
    }
    
    if($sitemapline == 0){
        foreach($file as $f){        
            if (strpos(strtolower($f), 'sitemap:') !== false) {       
                preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $f, $match);
                $url = isset($match[0][0])?$match[0][0]:'';            
                if($url == ''){
                    continue;
                }
                $name = trim(strtolower(basename($url)));                
                if($name == 'sitemap'){
                    $sitemapurl = $url;
                    $sitemapline = 1;
                    break;
                }
            }
        }
    }   
    
    // disallow urls
    $disallow = array();
    if(!empty($file)){
        foreach($file as $f){        
            if (strpos(strtolower($f), 'disallow:') !== false) {  
                $str = explode("disallow:", strtolower($f));
                $disall = trim(trim($str[1],"/"));
                if($disall != ""){ 
                    $disallow[] = $disall;
                }
            }
        }
    }       
    
    if(!isset($ar['yoast'])){
        $ar['yoast'] = 0;
    }
    $urls = '';
    
    if($sitemapline == 1){
        $ar['sitemap'] = 1;
        $ar['sitemapurl'] = $sitemapurl;
        if($ar['yoast'] == 1){
            $urls = getmapurls($sitemapurl, $user_id, $disallow, 1);
        }
        else{
            $urls = getmapurls($sitemapurl, $user_id, $disallow, 0);              
            if(empty($urls)){
                $urls = getmapurls($sitemapurl, $user_id, $disallow, 1);
            }            
        }                               
    }
    else{
        $ar['sitemap'] = 0;
    }
    $ar['sitemap_corrupted'] = 0;
    if(empty($urls)){       
        if($ar['sitemap'] == 1){
            $ar['sitemap_corrupted'] = 1;
        }
        // check if URLs present in site audit tables
        $siteaudittbl = "wp_site_audit_error_page_list_".$user_id;        
        $sql = "SELECT u.source_url FROM $siteaudittbl u INNER JOIN wp_site_audit s ON u.snapshot_id = s.snapshot_id WHERE s.user_id = $user_id";
        $siteauditdata = $wpdb->get_results($sql);
        if(!empty($siteauditdata)){
            $urls = filtersitauditurls($site_url, $siteauditdata, $user_id, $disallow);
        }
        else{                
            $urls = pageinternallinks($site_url, $user_id, $disallow);
        }
    }
    
    $ar['urls'] = $urls;    
    return $ar;
    
}

function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}

function getmapurls($sitemapurl, $user_id, $disallow, $is_parent){
            
    $target_pages = target_pages($user_id);
    
    $target_urls = $target_pages['urls'];
    
    $sitemapurl = appendhttp($sitemapurl); 
    
    $urls = array();
    $arext = array('jpg','jpeg','png','gif','bmp','bat','exif','tiff','svg','bat','bpg','axd');
    
    if($is_parent == 1){
        // case of SEO yoast        
        $xml = trim(file_get_contents($sitemapurl));        
        
        $pdata = simplexml_load_string($xml);           
        foreach($pdata as $dt){
            if(isset($dt->loc) && $dt->loc != ''){

                $dat = trim(file_get_contents($dt->loc));
                $innerdata = simplexml_load_string($dat); 
                foreach($innerdata as $innerdt){
                    
                    $ext = isset($innerdt->loc->{0})?strtolower(trim(pathinfo($innerdt->loc->{0}, PATHINFO_EXTENSION))):'';
                    $ext = explode("?", $ext);
                    $ext = $ext[0];
                    if(!in_array($ext, $arext)){
                        $fg = 0;
                        foreach($disallow as $dis){
                            if(strpos($innerdt->loc, $dis) !== false){
                                $fg = 1;                                
                            }  
                        }                              
                        if($fg == 0){
                                                        
                            $ob = json_decode(json_encode($innerdt->loc));
                            if(is_object($ob))
                                $ur = $ob->{0};
                            else
                                $ur = $ob[0];
                            
                            $urhash = explode("#", $ur);
                            if(count($urhash) > 1){
                               $ur = $urhash[0]; 
                            }
                            
                            $istarget = 0;
                            $uur = trim(str_replace(array("https://","http://","www."), array("","",""), $ur),"/");
                            
                            
                            $obj = new stdClass();
                            $obj->{0} = $ur;
                            foreach($target_urls as $key => $targe){
                                $matchurl = trim(str_replace(array("https://","http://","www."), array("","",""), $targe->{0}),"/");
                               
                                if($uur == $matchurl){
                                    $istarget = 1;
                                    foreach($targe as $k => $ta){
                                        if($k > 0){
                                            $obj->{$k} = $ta;
                                        }                                        
                                    }  
                                    unset($target_urls["$key"]);
                                    $target_urls = array_values($target_urls);
                                    break;
                                }
                            }
                            if($istarget == 1)
                                array_unshift($urls, $obj);
                            else
                                array_push($urls, $obj);
                        }                        
                    }
                }        
            }
        }
    }
    else{        
                
        $xml = trim(file_get_contents($sitemapurl));        
        $data = simplexml_load_string($xml);                
        foreach($data as $dat){
            
            $ext = isset($dat->loc->{0})?strtolower(trim(pathinfo($dat->loc->{0}, PATHINFO_EXTENSION))):'';   
            $ext = explode("?", $ext);
            $ext = $ext[0];                
            if(trim(strtolower($ext)) == 'xml'){
                continue;
            }         
            if(!in_array($ext, $arext)){      
                $fg = 0;
                foreach($disallow as $dis){                    
                    if(strpos($dat->loc, $dis) !== false){
                        $fg = 1;                      
                    }  
                }
                
                if($fg == 0){
                                        
                    $ob = json_decode(json_encode($dat->loc));
                    if(is_object($ob))
                        $ur = $ob->{0};
                    else
                        $ur = $ob[0];
                    
                    $urhash = explode("#", $ur);
                    if(count($urhash) > 1){
                       $ur = $urhash[0]; 
                    }
                    
                    $istarget = 0;
                    $uur = trim(str_replace(array("https://","http://","www."), array("","",""), $ur),"/");
                    
                    $obj = new stdClass();
                    $obj->{0} = $ur;
                    
                    foreach($target_urls as $key => $targe){
                        $matchurl = trim(str_replace(array("https://","http://","www."), array("","",""), $targe->{0}),"/");
                        if($uur == $matchurl){
                            $istarget = 1;
                            foreach($targe as $k => $ta){
                               if($k > 0){
                                    $obj->{$k} = $ta;
                                }
                            }  
                            unset($target_urls["$key"]);
                            $target_urls = array_values($target_urls);
                            break;
                        }
                    }
                    
                    if($istarget == 1)
                        array_unshift($urls, $obj);
                    else
                        array_push($urls, $obj);                    
                }
                
            }
        }    
    }    
    if(!empty($urls)){
        foreach($target_urls as $target){
            array_unshift($urls, $target);
        }
    }
        
    return $urls;
}

function urlbroken($url) {
    try{
        $exists = false;                
        if (!$exists && in_array('curl', get_loaded_extensions())) {

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_exec($ch);
            $erocurl = curl_errno($ch);           
            $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);            
            if ($response == 200 || $response == 301 || $response == 302 || $response == 999){
                $exists = true;
            }
            curl_close($ch);
        }

        return $exists;
    }
    catch (Exception $e){
        $message = $e->getMessage();
        //mail('parambir@rudrainnovatives.com','URL Check Error. Function urlbroken',$message);
        return true;
    }
}

function remove_html_comments($content = '') {
	return preg_replace('/<!--(.|\s)*?-->/', '', $content);
}

function checkurlcaseerror($url) {
    try{
        $exists = false;

        if (!$exists && in_array('curl', get_loaded_extensions())) {

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_exec($ch);

            $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);            
            if ($response === 200 || $response === 301 || $response === 302){
                $exists = true;
            }

            curl_close($ch);
        }

        return $exists;
    }
    catch (Exception $e){
        $message = $e->getMessage();
        //mail('parambir@rudrainnovatives.com','URL Check Error. Function urlexist',$message);
        return true;
    }
}
function homeurlcheck($url) {
    try{
        $exists = false;
         
        if (!$exists && in_array('curl', get_loaded_extensions())) {

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_exec($ch);            
            $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
            if ($response === 200  || $response === 300  || $response === 301  || $response === 302 
                    || $response === 304 || $response === 307){
                $exists = true;
            }

            curl_close($ch);
        }

        return $exists;
    }
    catch (Exception $e){
        $message = $e->getMessage();
        //mail('parambir@rudrainnovatives.com','URL Check Error. Function urlexist',$message);
        return true;
    }
}

function urlexistnofollow($url) {
    try{
        $exists = false;
         
        if (!$exists && in_array('curl', get_loaded_extensions())) {

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_exec($ch);            
            $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
            if ($response === 200){
                $exists = true;
            }

            curl_close($ch);
        }

        return $exists;
    }
    catch (Exception $e){
        $message = $e->getMessage();
        //mail('parambir@rudrainnovatives.com','URL Check Error. Function urlexist',$message);
        return true;
    }
}

function urlexist($url) {
    try{
        $exists = false;

        if (!$exists && in_array('curl', get_loaded_extensions())) {

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_exec($ch);

            $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);               
            if ($response != 404){
                $exists = true;
            }

            curl_close($ch);
        }

        return $exists;
    }
    catch (Exception $e){
        $message = $e->getMessage();
        //mail('parambir@rudrainnovatives.com','URL Check Error. Function urlexist',$message);
        return true;
    }
}

function removeElementsByTagName($tagName, $document) {
  $nodeList = $document->getElementsByTagName($tagName);
  for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0; ) {
    $node = $nodeList->item($nodeIdx);
    $node->parentNode->removeChild($node);
  }
}

include_once 'pageanalysis.php';

function get_remote_size($url) {
    //return mt_rand(1000, 100000); // temp
    $url = appendhttp($url);           
    $c = curl_init();
    curl_setopt_array($c, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_HTTPHEADER => array('User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3'),
        ));
    curl_exec($c);
    $siz = curl_getinfo($c, CURLINFO_SIZE_DOWNLOAD);
    curl_close($ch);
    return $siz;
}

function get_remote_status($url) {
    //return mt_rand(1000, 100000); // temp
    $url = appendhttp($url);        
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_exec($ch);
    $sts = curl_getinfo($ch, CURLINFO_HTTP_CODE);          
    return $sts;
}

function reltoabs($rel, $base)
{   
    //check if https
    $basehttp = explode("https", $base);
    $ishttps = 0;
    if(count($basehttp) > 1){
        $ishttps = 1;
    }
    if (strpos(strtolower($rel), strtolower("www.")) !== false) {
        if (parse_url($rel, PHP_URL_SCHEME) == '') {
            $rel = appendhttp($rel);
        }
        return $rel;
    }    
    /* return if already absolute URL */
    
    if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
    
    /* queries and anchors */
    if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;
    
    /* parse base URL and convert to local variables:
       $scheme, $host, $path */
    $path = parse_url($base);
    if($ishttps == 1)
        $scheme = isset($path['scheme'])?$path['scheme']:'http';
    else
        $scheme = isset($path['scheme'])?$path['scheme']:'https';
    
    $rel = trim($rel,"/");
    /* absolute URL is ready! */
    return $scheme.'://'.$path['host']."/$rel";
}

function fnd_pos($needle, $haystack) {
  $fnd = array();
  $pos = 0;

  while ($pos <= strlen($haystack)) {
    $pos = strpos($haystack, $needle, $pos);
    if ($pos > -1) {
      $fnd[] = $pos++;
      continue;
    }
    break;
  }
  return $fnd;
}

function silent_post($params,$remote_url = ""){     
    $url = trim($remote_url);
    if($url == ""){
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){
            $url = admin_url('admin-ajax.php');
        }
        else{
            $url = str_replace(array('https','HTTPS'), array('http','HTTP'), admin_url('admin-ajax.php'));
        }
    }    
    
    $params['action'] = 'training_lib';        
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }      
    
    $post_string = implode('&', $post_params);        
    $is_https = 0;
    $httpscnt = explode("https", strtolower(trim($url)));
    if(count($httpscnt) > 1){
        $is_https = 1;
    }
    
    $parts=parse_url($url);    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_string);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);    
    
    if($is_https == 1 || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 250);
    $res = curl_exec($ch);     
    curl_close ($ch);    
}

function post_cre_data($params,$remote_url = ""){     
    $url = trim($remote_url);
    if($url == ""){
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){
            $url = admin_url('admin-ajax.php');
        }
        else{
            $url = str_replace(array('https','HTTPS'), array('http','HTTP'), admin_url('admin-ajax.php'));
        }
    }    
    
    $httpscnt = explode("https", strtolower(trim($url)));
    if(count($httpscnt) > 1){
        $is_https = 1;
    }
    
    $params['action'] = 'training_lib';        
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    
    $post_string = implode('&', $post_params);        
    $parts=parse_url($url);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    if($is_https == 1 || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }
    
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_string);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
    curl_exec($ch);    
    curl_close ($ch);    
}


function bytesformat($bytes){
    
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' kB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function tr_unsubscribed($email) {
    global $wpdb;
    $tbl_unsbs = $wpdb->prefix . "all_email_subscription";
    $unnsubs = $wpdb->get_row
            (
            $wpdb->prepare
                    (
                    "SELECT setting FROM " . $tbl_unsbs . " WHERE email = %s", $email
            )
    );
    if (empty($unnsubs)) {
        return FALSE;
    }
    $unnsubs = $unnsubs->setting;
    $unnsubs = unserialize($unnsubs);
    $i = 0;
    foreach ($unnsubs as $key => $unnsub) {
        if ($key == EMAIL_TYPE) {
            $i = 1;
            break;
        }
    }

    if ($i == 1) {
        return TRUE;
    }
    return FALSE;
}

function anconn(){
    
    $servername = database_host;
    $db_name = database_name;
    $db_user = database_user;
    $db_password = database_password;
    $conn = new mysqli($servername, $db_user, $db_password, $db_name);
    if ($conn->connect_error) {
       return '';
    } 
    return $conn;
}

function formatpercent($Num, $NDecimals = 2, $DivBy = 100.0) {       
    return number_format(convertfloat($Num) / $DivBy, $NDecimals) . '%';
}
function convertfloat($Num) {

    return !empty($Num) ? floatval($Num) : 0.0;
}
function formatsecondsToMinSec2($Seconds) {

    $sec = intval($Seconds % 60);

    if (strlen($sec) == 1) {

        $sec = '0' . $sec;
    }

    return strval(intval($Seconds / 60)) . ':' . $sec;
}
function appendhttp($url) {    
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {        
        $url = trim($url,"/");        
        $url = "http://" . $url;        
    }    
    return $url;
}

function arrtoobj($arr){
    $obj = json_decode(json_encode($arr));
    return $obj;
}


function arsearch($obj, $url){
    
    $url = trim(str_replace(array("http://","http"), array("",""), $url),"/"); $jk = 0;    
    foreach ($obj as $key => $ob){
        $ur = $ob->url;
        $ur = trim(str_replace(array("http://","http"), array("",""), $ur),"/");        
        if($ur == $url){
            $jk++;
            break;
        }
        
    }
    if($jk > 0){
        $arr = array('ob'=>$ob,'index' =>$key);
        return $arr;
    }
    return '';
}

function filtersitauditurls($url, $siteauditdata, $user_id, $disallow){
    $urls = $siteauditdata;    
    $arurls1 = str_replace(array("http://","https://"), array("",""), $url);
    $arurls1 = trim(trim($arurls1,"/"));
    $ret_urls = array();
    
    foreach($urls as $urr){
        $ur = $urr->source_url;
        $ext = pathinfo(basename($ur), PATHINFO_EXTENSION);
        if(strtolower($ext) == 'xml' || strtolower($ext) == 'txt'){
            continue;
        }
        $arurls2 = parse_url(strtolower($ur));    

        if(isset($arurls2['host'])){                
            $url2 = str_replace(array("http://","https://"), array("",""), $arurls2['host']);                
            $url2 = trim(trim($url2,"/")); 
            $url2 = str_replace("www."," ",$url2);
            $arurls1 = str_replace("www."," ",$arurls1);            
            if($arurls1 == $url2){
                $fg = 0;
                $ur = reltoabs($ur, $url);
                foreach($disallow as $dis){
                    if(strpos($ur, $dis) !== false){     
                        $fg = 1;                        
                    }  
                } 
                if($fg == 0){
                    
                    $urhash = explode("#", $ur);
                    if(count($urhash) > 1){
                       $ur = $urhash[0]; 
                    }
                    
                    $ob = new stdClass();
                    $ob->{0} = $ur;                    
                    $uur = trim(trim(str_replace(array("https://","http://","www."), array("","",""), $ur),"/"));
                    $mat = 0;
                    $istarget = 0;
                    foreach($target_urls as $key => $targe){
                        $matchurl = trim(trim(str_replace(array("https://","http://","www."), array("","",""), $targe->{0}),"/"));
                        
                        if($uur == $matchurl){
                            $mat = 1;
                            $istarget = 1;
                            foreach($targe as $k => $ta){
                                if($k > 0){
                                    $ob->{$k} = $ta;
                                }
                            }    
                            unset($target_urls["$key"]);
                            $target_urls = array_values($target_urls);
                            break;
                        }                        

                    }
                    
                    if($istarget == 1)
                        array_unshift($ret_urls, $ob);
                    else
                        array_push($ret_urls, $ob);
                }                
            }
        }            
    }
    
    foreach($target_urls as $target){
        array_unshift($ret_urls, $target);
    }
    
    return $ret_urls;
    
}

function pageinternallinks($url, $user_id, $disallow = array()){
    $urls = array();    
    $target_pages = target_pages($user_id);
    $target_urls = $target_pages['urls'];
    require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
    $browser = &new SimpleBrowser(); 
    $browser->get($url);
    $urls = $browser->getUrls();           
    $options = '';

    $arurls1 = str_replace(array("http://","https://"), array("",""), $url);
    $arurls1 = trim(trim($arurls1,"/"));
    $ret_urls = array();
    foreach($urls as $ur){
        $arurls2 = parse_url(strtolower($ur));    

        if(isset($arurls2['host'])){                
            $url2 = str_replace(array("http://","https://"), array("",""), $arurls2['host']);                
            $url2 = trim(trim($url2,"/")); 
            $url2 = str_replace("www."," ",$url2);
            $arurls1 = str_replace("www."," ",$arurls1);            
            if($arurls1 == $url2){
                $fg = 0;
                $ur = reltoabs($ur, $url);
                foreach($disallow as $dis){
                    if(strpos($ur, $dis) !== false){     
                        $fg = 1;                        
                    }  
                } 
                if($fg == 0){
                    
                    $urhash = explode("#", $ur);
                    if(count($urhash) > 1){
                       $ur = $urhash[0]; 
                    }
                    
                    $ob = new stdClass();
                    $ob->{0} = $ur;                    
                    $uur = trim(trim(str_replace(array("https://","http://","www."), array("","",""), $ur),"/"));
                    $mat = 0;
                    $istarget = 0;
                    foreach($target_urls as $key => $targe){
                        $matchurl = trim(trim(str_replace(array("https://","http://","www."), array("","",""), $targe->{0}),"/"));
                        
                        if($uur == $matchurl){
                            $mat = 1;
                            $istarget = 1;
                            foreach($targe as $k => $ta){
                                if($k > 0){
                                    $ob->{$k} = $ta;
                                }
                            }    
                            unset($target_urls["$key"]);
                            $target_urls = array_values($target_urls);
                            break;
                        }                        

                    }
                    
                    if($istarget == 1)
                        array_unshift($ret_urls, $ob);
                    else
                        array_push($ret_urls, $ob);
                }                
            }
        }            
    }
    
    foreach($target_urls as $target){
        array_unshift($ret_urls, $target);
    }
    
    return $ret_urls;
}

function analysyscall($url,$user_id){
    global $wpdb;    
    $browser = '';
    $data = array(                
            'url' => $url,
            'keyword' => ''
        );    
    $data = arrtoobj($data);   
    //@mail("parambir.rudra@gmail.com","Analysis started for $url", "Analysis has been started for $url at ".date('Y-m-d H:i:s') );
    $analysis = page_analysis($data,$browser);  
    //@mail("parambir.rudra@gmail.com","Analysis end for $url", json_encode($analysis));                

    $result = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT crawl_result, result FROM wp_content_recommend WHERE user_id = %d", $user_id
        )
    );
    $dataset = json_decode($result->result);
    foreach($dataset as $key => $dat){
        if(trim($dat->url) == trim($url)){
            $dat->analysis = $analysis;
            $dat->total_issues = $analysis['total_issues'];
            $dat->is_running = 0;
        }          
    }

    $final_result = json_encode($dataset);

    $result->result = $final_result;

    $wpdb->query
    (
        $wpdb->prepare
        (
        "UPDATE wp_content_recommend SET result = %s WHERE user_id = %d", 
         $final_result, $user_id
        )
    );

    // checking last result        

    $result = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT crawl_result, result FROM wp_content_recommend WHERE user_id = %d", $user_id
        )
    );

    $rsrec = json_decode($result->result);;
    $crawlrs = json_decode($result->crawl_result);
    $totalurls = count($crawlrs->urls);

    $total_title_issues = 0; $total_meta_issues = 0; $total_content_issues = 0;
    $total_heading_issues = 0; $total_link_issues = 0; $total_image_issues = 0;

    $completed_urls = 0;
    foreach($rsrec as $key => $rsre){
        if(isset($rsre->analysis) && $rsre->analysis != ''){
            $completed_urls++;
            $analysis = $rsre->analysis;
            $total_title_issues = $total_title_issues + $analysis->issues_count->title_issues;
            $total_meta_issues = $total_meta_issues + $analysis->issues_count->meta_issues;
            $total_content_issues = $total_content_issues + $analysis->issues_count->content_issues;
            $total_heading_issues = $total_heading_issues + $analysis->issues_count->heading_issues;
            $total_link_issues = $total_link_issues + $analysis->issues_count->link_issues;
            $total_image_issues = $total_image_issues + $analysis->issues_count->image_issues;                
        }                        
    }

    if($completed_urls == $totalurls){

        $rsrec = (array) $rsrec;
        $arnew = array(
            'total_title_issues' => $total_title_issues,
            'total_meta_issues' => $total_meta_issues,
            'total_content_issues' => $total_content_issues,
            'total_heading_issues' => $total_heading_issues,
            'total_link_issues' => $total_link_issues,
            'total_image_issues' => $total_image_issues
        );

        $rsrec = array_merge($rsrec, $arnew);            
        $final_result = json_encode($rsrec);

        $wpdb->query
        (
            $wpdb->prepare
            (
            "UPDATE wp_content_recommend SET trigger_report = 0, result = %s WHERE user_id = %d", 
             $final_result, $user_id
            )
        );
        // send mail to user
        //@mail("parambir.rudra@gmail.com","Analysis completed for all urls", "Analysis done for all pages");
    }
    
}

function newsinglepagecall($pageindex, $url, $user_id, $queue_id = 0){
    
    global $wpdb;    
    require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
    $browser = &new SimpleBrowser();
    
    $creres = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT id, keyword, is_running FROM cre_urls WHERE id = %d", $pageindex
        )
    );
    
    if(isset($creres->is_running) && $creres->is_running == 0){
        removeurlfrmprocessedqueue($pageindex, $queue_id, $url);   
        exit();
    }
    
    $driver = array();
    $data = array();
    $data['url'] = $url;
    $data['user_id'] = $user_id;
    
    if(isset($creres->keyword) && $creres->keyword != ''){
        $data['tarkeyword'] = json_decode($creres->keyword); 
    }
    else{
        $data['tarkeyword'] = '';
    }
    $data['keyword'] = "";    
    
    $newdt = json_decode(json_encode($data));   
    //@mail("parambir@rudrainnovatives.com","Analysis start for $url", json_encode($data));
    $res = page_analysis($newdt,$browser);     
    //@mail("parambir@rudrainnovatives.com","Analysis Done for $url", json_encode($res));    
    $total_issues = $res['total_issues'];       
    $idrcre = isset($res['idrcre'])?$res['idrcre']:0;
    $res = json_encode($res);
    
    if(!empty($creres)){   
        
        //enter_page_history_data($user_id, $pageindex);        
        $wpdb->query
        (
            $wpdb->prepare
            (
                "UPDATE cre_urls SET algo_id = %d, result = %s, total_issues = %d, is_running = %d WHERE id = %d", 
                $idrcre, $res, $total_issues, 0, $pageindex
            )
        );
    }
    else{
        $wpdb->query
        (
            $wpdb->prepare
            (
                "INSERT INTO cre_urls(algo_id, url, user_id, result, total_issues, is_running, rundate) VALUES(%d, %s, %d, %s, %s, %d, '%s')", 
                $idrcre, $url, $user_id, $res, $total_issues, 0, date("Y-m-d H:i:s")
            )
        );
    }
    
    // if function call from remote url (agency), update queue that analysys complete for url
    if($queue_id > 0){
        removeurlfrmprocessedqueue($pageindex, $queue_id, $url);   
        //@mail("parambir.rudra@gmail.com","Analysed Url : $url", "Id: $pageindex, Queue_id: $queue_id, Url: $url");
    }
    
}


function singlepagecall($pageindex, $url, $user_id, $queue_id = 0){
    
    global $wpdb;    
    
    $rcommend = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT * FROM wp_content_recommend WHERE user_id = %d", $user_id
        )
    );
    
    require_once TR_COUNT_PLUGIN_DIR.'/simpletest/browser.php';
    $browser = &new SimpleBrowser();
    
    $driver = array();
    $data = array();
    $data['url'] = $url;
    $data['keyword'] = "";    
    $newdt = json_decode(json_encode($data));    
    
    $res = page_analysis($newdt,$browser);                
    
    if(!empty($rcommend)){
        if(isset($rcommend->result)){
            $rs = json_decode(trim($rcommend->result));
            $rstotal = count((array)$rs);            
            if($pageindex < 0){
                $pageindex = $rstotal + 1;
                $newdt->analysis = $res;
                $newdt->rundate = date("Y-m-d H:i:s");
                $newdt->total_issues = $res['total_issues'];
                $newdt->is_running = 0;                
                if(is_object($rs))
                    $rs->{$pageindex} = $newdt;
                else
                    $rs["$pageindex"] = $newdt;
                
                $rsdt = json_encode($rs);
                
                $crawl_res = json_decode($rcommend->crawl_result);
                $arurls = (array) $crawl_res->urls;
                $arurls[] = $url;
                $crawl_res->urls = $arurls;
                $crawl_res = json_encode($crawl_res);
                $vl = $wpdb->query
                (
                    $wpdb->prepare
                    (
                    "UPDATE wp_content_recommend SET crawl_result = %s, result = %s WHERE user_id = %d", 
                     $crawl_res, $rsdt, $user_id
                    )
                ); 
            }
            else{
                
                $newdt->analysis = $res;
                $newdt->rundate = date("Y-m-d H:i:s");
                $newdt->total_issues = $res['total_issues'];
                $newdt->is_running = 0;
                if(is_object($rs))
                    $rs->{$pageindex} = $newdt;
                else
                    $rs["$pageindex"] = $newdt;
                
                //pr($rs); die;
                $rsdt = json_encode($rs);

                $vl = $wpdb->query
                (
                    $wpdb->prepare
                    (
                    "UPDATE wp_content_recommend SET result = %s WHERE user_id = %d", 
                     $rsdt, $user_id
                    )
                );
                
               // @mail("parambir.rudra@gmail.com","Analysis Updated Done for $url", $res);  
            }
            
            // if function call from remote url (agency), update queue that analysys complete for url
            if($queue_id > 0){
                removeurlfrmprocessedqueue($queue_id, $url);
            }

        }
    }
    
}

function codetostatus($code){
    $http_codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );
    
    $msg = 'No Status Message Found';
    if(isset($http_codes["$code"]) && $http_codes["$code"] != ''){
        $msg = $http_codes["$code"];
    }
    
    return $msg;
}

function curlres($url,$post_string){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_string);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
    $output = curl_exec($ch);      
    curl_close ($ch); 
    return $output;
}
function add_global_queue($agency_url,$urls,$user_id){
    if(defined("SET_PARENT_URL")){
        $url = SET_PARENT_URL;
    }
    else{
        $url = site_url().'/wp-admin/admin-ajax.php';
    }    
    $params['agency_url'] = $agency_url;
    $params['urls'] = json_encode($urls);
    $params['user_id'] = $user_id;    
    $params['db'] = DB_NAME;
    $params['key'] = md5(DB_NAME);
    $params['action'] = 'training_lib';
    $params['param'] = 'global_queue';    
    foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
          $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);
    $output = curlres($url,$post_string);
    $output = json_decode($output);  
     
    if($output->sts == 1){
        return 1;
    }
    return 0;
}
function removeurlfrmprocessedqueue($pageindex, $queue_id, $queueurl){
    if(defined("SET_PARENT_URL")){
        $url = SET_PARENT_URL;
    }
    else{
        $url = site_url().'/wp-admin/admin-ajax.php';
    }    
    
    $params['queueurl'] = $queueurl;
    $params['queue_id'] = $queue_id;
    $params['pageindex'] = $pageindex;
    $params['db'] = DB_NAME;
    $params['key'] = md5(DB_NAME);
    $params['action'] = 'training_lib';
    $params['param'] = 'processed_queue_update';
    
    foreach ($params as $key => &$val) {
    if (is_array($val)) $val = implode(',', $val);
      $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);
    $output = curlres($url,$post_string);    
    $output = json_decode($output);         
    if($output->sts == 1){
        return 1;
    }
    return 0;
    
}

function enter_page_history_data($user_id, $pageindex){
    global $wpdb;
    
    if($pageindex > 0){
        
        $creres = $wpdb->get_row
        (
            $wpdb->prepare
            (
                "SELECT * FROM cre_urls WHERE id = %d AND user_id = %d", $pageindex, $user_id
            )
        );
        if(isset($creres->result) && $creres->result != ''){            
            $wpdb->query
            (
                $wpdb->prepare
                (
                    "INSERT INTO cre_url_history(algo_id, page_id, url, user_id, result, total_issues, rundate, user_trigger) VALUES(%d, %d, %s, %d, %s, %d, '%s', %d)", 
                    $creres->algo_id, $creres->id, $creres->url, $creres->user_id, $creres->result, $creres->total_issues, $creres->rundate, $creres->user_trigger
                )
            );
        }
    }
}

function enter_history_data($user_id){
    global $wpdb;   
        
    $rcommend = $wpdb->get_row
    (
        $wpdb->prepare
        (
            "SELECT rundate FROM wp_content_recommend WHERE user_id = %d", $user_id
        )
    );

    $rs = $wpdb->get_results
    (
        $wpdb->prepare
        (
            "SELECT * FROM cre_urls WHERE user_id = %d ORDER BY total_issues DESC", $user_id
        )
    );

    $totalurls = 0; $totalurlissue = 0; $total_score_val = 0;
    $title = 0; $meta = 0; $content = 0; $heading = 0; $link = 0; $image = 0;
    $rundate = date("Y-m-d H:i:s"); $user_trigger = 1;
    if(!empty($rcommend)){
        $rundate = $rcommend->rundate;
        $user_trigger = $rcommend->user_trigger;
    }


    foreach($rs as $rowvaldata) {     

        $key = $rowvaldata->id;
        $row = json_decode($rowvaldata->result);
        $title = $title + $row->issues_count->title_issues;
        $meta = $meta + $row->issues_count->meta_issues;
        $content = $content + $row->issues_count->content_issues;
        $heading = $heading + $row->issues_count->heading_issues;
        $link = $link + $row->issues_count->link_issues;
        $image = $image + $row->issues_count->image_issues;


        $totalurlissue += $row->issues_count->title_issues + $row->issues_count->meta_issues + 
                $row->issues_count->content_issues + $row->issues_count->heading_issues + 
                $row->issues_count->link_issues + $row->issues_count->image_issues;


        $total_score_val =  $total_score_val + $row->score; 
        $totalurls++;

    }

    $issues = array();
    $issues['title'] = $title;
    $issues['meta'] = $meta;
    $issues['content'] = $content;
    $issues['heading'] = $heading;
    $issues['link']= $link;
    $issues['image'] = $image;      
    $issues = json_encode($issues);
    
    $avgscore = round(($total_score_val / $totalurls),2);
//    mail('parambir@rudrainnovatives.com','History Added'," avgscore : $avgscore , "
//            . "total_score_val : $total_score_val , totalurls : $totalurls");
    $wpdb->query
    (
        $wpdb->prepare
        (
            "INSERT INTO cre_history(type, user_id, totalurls, totalissues, issues_detail, avg_score, rundate, user_trigger) "
            . "VALUES(%s, %d, %d, %d, %s, %s, '%s', %d)", 
            $rcommend->type, $user_id, $totalurls, $totalurlissue, $issues, $avgscore, $rundate, $user_trigger
        )
    );        
         
}


function cre_ranking_data($search_type, $RankingData, $prev_RankingData) {

    $key_index = array_search($search_type, $RankingData);
    $ranking_url_index = $key_index + 1;
    $rank_index = $key_index + 2;
    $google_places_CurrentRank = $google_places_CurrentRank_text = $RankingData[$rank_index];
    $rank_result['RankingURL'] = $RankingData[$ranking_url_index];

    $key_index = array_search($search_type, $prev_RankingData);
    $rank_index = $key_index + 2;
    $google_places_prev_CurrentRank = $google_places_prev_CurrentRank_text = $prev_RankingData[$rank_index];

    if ($google_places_CurrentRank == 0 || $google_places_CurrentRank == 50) {
        $google_places_CurrentRank_text = '50+';
        $google_places_CurrentRank = 50;
    }
    if ($google_places_prev_CurrentRank == 0 || $google_places_prev_CurrentRank == 50) {
        $google_places_prev_CurrentRank_text = '50+';
        $google_places_prev_CurrentRank = 50;
    }
    $google_places_rank_change = $google_places_prev_CurrentRank - $google_places_CurrentRank;
    $rank_result['CurrentRank'] = $google_places_CurrentRank_text;
    $rank_result['prev_CurrentRank'] = $google_places_prev_CurrentRank_text;
    $rank_result['rank_change'] = $google_places_rank_change;
   

    $rank_change_func = rank_change_func($google_places_rank_change);
    $rank_result['arrow_class'] = $rank_change_func['arrow_class'];
    $rank_result['rank_change_text'] = $rank_change_func['rank_change_text'];

    return $rank_result;
}

function getcrealgo($id = 0){
        
    if(defined("SET_PARENT_URL")){
        $url = SET_PARENT_URL;
    }
    else{
        $url = site_url().'/wp-admin/admin-ajax.php';
    }    
    
    $params['id'] = $id;    
    $params['action'] = 'training_lib';
    $params['param'] = 'fetchcredata';
    
    foreach ($params as $key => &$val) {
    if (is_array($val)) $val = implode(',', $val);
      $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);    
    $output = curlres($url,$post_string);    
    $output = json_decode($output);      
    if($output->sts == 1){ 
        $ar = $output->arr;        
        $ar->curver = $output->msg;
        return $ar;
    }
    return '';
}

function is_duplicate_array($input, $status) {
    $duplicates=array();
    $processed=array();
    foreach($input as $key => $i) {
        if($status["$key"] == 'active'){
            if(trim($i[0]) == ''){
                continue;
            }
            if(in_array($i,$processed)) {
                $duplicates[]=$i;
            } else {
                $processed[]=$i;
            }
        }
    }
    return $duplicates;
}

function checkcrever(){
    
    include_once 'views/newcrever.php';
}

function url_get_content($URL){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, $URL);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
}


add_action('init','checkcrever');