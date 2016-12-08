<?php
$_config = array(
	"Host" => "0.0.0.0",
	"Set" => array(
		'worker_num' => 2,
		'dispatch_mode' => 2,
		'max_request' => 0,
		'task_worker_num' => 4,
		'task_ipc_mode' => 2,
		'message_queue_key' => 65535,
	)
);
return $_config;