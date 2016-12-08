<?php
error_reporting(E_WARNING | E_ERROR);
include dirname(__FILE__) . '/../lib/package.php';

function admin($ip, $port, $cmd = 1) {	
	$w = new WritePackage();
	$w->WriteBegin(0x09);
	$w->WriteString("admin@localhost");
	$w->WriteInt($cmd);
	$data = $w->WriteEnd();
	send_udp($data, $ip, $port);
}


function send_udp($content, $ip, $port) {
	$socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR,1);
	var_dump(@socket_sendto($socket, $content, strlen($content), 0, $ip, $port));
}

function log_test($ip, $port, $data) {
	$w = new WritePackage();
	$w->WriteBegin(0x01);
	$w->WriteString($data);
	$data = $w->WriteEnd();
	send_udp($data, $ip, $port);
}

//admin("127.0.0.1", 12150, 1);
//保存文件名为 a.log 保存内容为 json
log_test("127.0.0.1", 12150, json_encode(array('filename'=>"a.log", 'message' => array("msg" => "abc", "time" => time())), true));
//保存文件名为 如：2016120811.log 内容为 "1231232" or json
log_test("127.0.0.1", 12150, "1231232");
log_test("127.0.0.1", 12150, json_encode(array('message' => array("msg" => "abc", "time" => time())), true));
