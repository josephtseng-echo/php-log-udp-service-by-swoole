<?php
/**
 *
 * @author JosephZeng
 *
 */
class LogBehavior extends SwooleBehavior {
	
	public $taskCnt = 0;
	private $udpPackage;

	public function onReceive($server, $fd, $from_id, $packet_buff) {		
		
	}

	public function onPacket($server, $data, $client_info) {
		$ret = $this->udpPackage->ReadPackageBuffer($data);
		if (!$ret) {
			return;
		}
		switch ($this->udpPackage->cmdType) {
			case 0x09:
				$this->_admin($server);
				return;
			case 0x01:
				$this->_log($server);
				return;
		}
	}

	private function _admin($server) {
		$key = $this->udpPackage->ReadString();
		if ($key != ADMIN_KEY) {
			return;
		}
		$type = $this->udpPackage->ReadInt();
		switch ($type) {
			case 1:
				$server->reload();
				break;
			case 2:
				$server->shutdown();
				break;
		}
	}

	private function _log($server) {
		$message = $this->udpPackage->ReadString();
		$arr = json_decode($message, true);
		$fileName = date('YmdH').'.log';
		if(json_last_error() == JSON_ERROR_NONE){
			if(isset($arr['filename']) && !empty($arr['filename'])) $fileName = trim($arr['filename']);
		}
		$fileContent = $message;
		$task_id = crc32($fileName) % $this->taskCnt;
		$task_data = array("filename" => $fileName, "content" => $fileContent);		
		$server->task($task_data, $task_id);
	}

	public function onTask($serv, $task_id, $from_id, $data) {
		$fname = $data["filename"];
		$content = $data["content"];
		$filename = LOGROOT.$fname;
		LogClient::log($content, $filename);
	}

	public function onWorkerStart($serv, $worker_id) {
		ini_set('memory_limit', '128M');
		set_time_limit(0);
		$this->taskCnt = $serv->setting['task_worker_num'];
		$this->udpPackage = new ReadPackage();
	}

}
