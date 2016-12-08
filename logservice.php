<?php
/**
 * swoole udp log server
 */
date_default_timezone_set('Asia/Shanghai');
error_reporting(E_WARNING | E_ERROR);
define('ROOT', dirname(__FILE__) . '/');
define('LOGROOT', ROOT.'log/');
define('ADMIN_KEY', 'admin@localhost');
include ROOT.'config.inc.php';
include ROOT.'lib/swoole_service.php';
include ROOT.'lib/exception.php';
include ROOT.'lib/package.php';
include ROOT.'lib/swoole_behavior.php';
include ROOT.'lib/log_behavior.php';
include ROOT.'lib/log_client.php';
//get script params
$shortopts = 'h:';
$longopts = array(
	'host:',
	'port:',
	'worker:',
	'task:'
);
$options = getopt($shortopts, $longopts);
if(empty($options)){
echo <<< EOT
    请先安装php swoole扩展  
    example:
    /usr/local/php7/bin/php logservice.php --host 127.0.0.1 --port 12150 --worker 1 --task 2
	or
	./start.sh
	===============================================================
    --host listen ip
    --port listen port
    --worker swoole worker num
    --task swoole task worker num
EOT;
die();
}

//init
$_ip = (isset($options['host']) && !empty($options['host'])) ? trim($options['host']) : '0.0.0.0';
$_port = (isset($options['port']) && !empty($options['port'])) ? intval($options['port']) : 12150;
$_worker_num = (isset($options['worker']) && !empty($options['worker'])) ? intval($options['worker']) : 2;
$_task_worker_num = (isset($options['task']) && !empty($options['task'])) ? intval($options['task']) : 4;
$_config['Host'] = $_ip;
$_config['Set']['message_queue_key'] += $_port * 10;
$_config['Set']['worker_num'] = $_worker_num;
$_config['Set']['task_worker_num'] = $_task_worker_num;
$_config['MainProcessName'] = implode(" ", $argv);
$_config['Port'] = $_port;
$_config['SocketType'] = SWOOLE_SOCK_UDP;
$_config['Behavior'] = "LogBehavior";
$swoole_service = new SwooleService($_config, SWOOLE_BASE);
$swoole_service->start();