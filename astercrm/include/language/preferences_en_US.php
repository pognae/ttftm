<?php
$db_dbtype		= 'database type';
$db_dbhost		= 'database server address';
$db_dbname		= 'database name';
$db_username	= 'database username';
$db_password	= 'database password';

$as_server		= 'Default Asterisk server address';
$as_port		= 'Default Asterisk AMI port, it\'s 5038 by default';
$as_username	= 'Default Asterisk AMI username';
$as_secret		= 'Default Asterisk AMI password';
$as_monitorpath		= 'the path recording file will store, the fils will store on Asterisk server not asterCRM server';
$as_monitorformat	= 'format of the record file';

$sys_log_enabled	= 'if enable astercrm log';
$sys_log_file_path	= 'file path of the log file';
$sys_outcontext		= 'when agent dial external number in asterCRM, which context in Asterisk it would use';
$sys_incontext		= 'when agent dial internal number in asterCRM, which context in Asterisk it would use';
$sys_predialer_context		= 'when using predictive dialer, if connected use which context to handel the call';
$sys_predialer_extension	= 'when using predictive dialer, if connected use which extension in the context to handel the call';
$sys_phone_number_length	= 'asterCRM wouldnot pop-up unless the length of callerid is greater than this number';
$sys_trim_prefix			= 'if asterCRM trim will remove prefix, use gamma to sperate,leave it blank if no prefix need to be removed';
$sys_allow_dropcall			= 'if asterCRM will generate a .call file to originate a call, select 0 if your asterCRM is not on the same machine with Asterisk then asterCRM will originate a call via AMI';
$sys_portal_display_type	= 'which information will be displayed in agent\'s inteface, if "customer", it would display all customer information the agent added, if "note" it would only display the customer who has a note record added by this agent';
$sys_enable_contact			= 'if asterCRM enable contact in agent interface';
$sys_pop_up_when_dial_out	= 'if asterCRM pop up when agent dial out';
$sys_pop_up_when_dial_in	= 'if asterCRM pop up when there\'s incoming call';
$sys_allow_same_data		= 'if allow same customer name';
$sys_browser_maximize_when_pop_up	= 'if browser will maximize when pop up';
$sys_firstring				= 'caller ring first or callee ring first';
$sys_enable_external_crm	= 'if asterCRM use external CRM software';
$sys_open_new_window	= 'if enable_external_crm enabled,how to show the popup. internal means the popup in the iframe, external means the popup not in the iframe and open a new window, both means the popup in the iframe and open a new window';

$sys_external_crm_default_url = 'when using external CRM, the default page to be displayed';
$sys_external_crm_url		= 'when asterCRM need to pop up, which url would recevie the event,  %callerid: %calleeid:  %method	dialout or dialin';
$sys_upload_file_path		= 'the upload directory, such as "./upload/", it need a writable permission, ';
$save_success				= 'Save success';
$save_failed				= 'Save config file failed, please check permission';
$db_connect_failed			= 'database connection test failed, please check the parameters';
$db_connect_success			= 'database connection test passed';
$AMI_connect_failed			= 'AMI connection test failed, please check the parameters';
$AMI_connect_success		= 'AMI connection test passed';
$permission_error			= 'directory permission error';
$sys_eventtype				= 'where astercrm get asterisk call events, set to curcdr when using astercc';
$sys_stop_work_verify		= 'if need to enter accountcode/password of admin or groupadmin when agent stop work';
$astercc_path			= 'path of astercc daemon';
$update_licence_success	= 'licence has been updated,you must reboot server to enable new licence';
$astercc_conf_non		= 'astercc.conf is not exists in specified directory:';
$astercc_non		= 'astercc daemon is not exists in specified directory:';
$status_check_interval	= 'time intervals of update event in pages';
$smart_match_remove	= 'how many digits end of callerid remove when incoming call smart matching, disabled if set it to 0';
$enable_surveynote = "if need a note after survey option";
$close_popup_after_survey = "if need close all popups after survey saved";
$popup_diallist = "if popup customer infomation in diallist";
$sys_agent_pannel_setting = "if display these pannels in agent interface";
$if_auto_popup_note_info = 'if auto popup note info';
$if_share_note_default = 'if share note default';
$if_enable_code = 'if enable code';
$the_smaller_the_value_the_more_accurate = "the smaller the value the more accurate";
$require_reason_when_pause = 'if set to yes,will popup a tip to record the reasion when the agent pause the queueif set to no,will not popup a tip';
$create_ticket = 'if set default,will create the ticket: systemadmin for all user; groupadmin for all group user; agent for self.if set system,allow a ticket to be assigned to any user regardless of the group they belong to.if set group,allow a ticket to be assigned to any user who belongs to same group';
$enable_socket = "if check socket to yes,when the call dialin it will notic the agent by socket";
$fix_port = "fix port";
$socket_url = "socket string";
$export_customer_fields_in_dialedlist = "export customer field when export dialedlist";
$allow_popup_when_already_popup = "whether to popup the customer window when it had existed";
$enable_formadd_popup = "whether to pop up the add record popup";
$if_popup_the_highest_priority_note_info = "if popup the highest priority note info";
$if_popup_the_lastest_priority_note_info = "if popup the lastest priority note info";
?>