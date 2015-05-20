<?php
include_once('config.php');

//table names
define('DB_SCANNER_SETTING_TABLE','scanner_setting');
define('DB_SCANNER_SETTING_INFO_TABLE', 'setting_info');

//columns
define('DB_COL_CONTINUOUS_SCANNING','continuous_scanning');
define('DB_COL_TAG_ID_STREAMING','tag_id_streaming');
define('DB_COL_PROTOCOL_MODE','protocol_mode');
define('DB_COL_TRANSMIT_FREQUENCY','transmit_frequency');
define('DB_COL_FREQUENCY_HOPPING','frequency_hopping');
define('DB_COL_FREQUENCY_CHANNEL','frequency_channel');
define('DB_COL_OUTPUT_POWER_LEVEL','output_power_level');
define('DB_COL_IQ_RECEIVE_CHANNEL','iq_receive_channel');
define('DB_COL_UPLOAD_TAG_TO_SERVER','upload_tag_to_server');
define('DB_COL_SERVER_ADDRESS','server_address');
define('DB_COL_PROTOCOL_TYPE_OUTPUT','protocol_type_output');
define('DB_COL_READER_ID_OUTPUT','reader_id_output');
define('DB_COL_FREQUENCY_CHANNEL_OUTPUT','frequency_channel_output');
define('DB_COL_PROTOCOL_CONTROL_OUTPUT','protocol_control_output');
define('DB_COL_SET_READER_SESSION','set_reader_session');
define('DB_COL_NUMBER_OF_COLLISION_SLOTS','number_of_collision_slots');
define('DB_COL_NUMBER_OF_COLLISION_ATTEMPTS','number_of_collision_attempts');
define('DB_COL_SET_ACCESS_PASSWORD','set_access_password');
define('DB_COL_SET_KILL_PASSWORD','set_kill_password');

?>