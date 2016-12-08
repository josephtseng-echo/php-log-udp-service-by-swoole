<?php
/**
 * 
 * @author JosephZeng
 *
 */
class SwooleService {
	
	private $_config;
	
	private $_behavior;
	
	public $_swoole;

	public function __construct($config, $model = SWOOLE_PROCESS) {

		$this->_config = $config;
		$this->_swoole = new swoole_server($this->_config['Host'], $this->_config['Port'], $model, $this->_config['SocketType']);
	}

	private function _logPrint($ex = null, $server = null, $fd = 0) {
		//print_r($ex, $server);
	}

	public function onWorkerStart($serv, $worker_id) {
		if (function_exists('apc_clear_cache')) {
			apc_clear_cache();
		}
		if (function_exists('opcache_reset')) {
			opcache_reset();
		}
		try {
			$arr = $this->_config;
			$behavior = $arr['Behavior'];
			$this->_behavior = new $behavior();
			$pid = $serv->master_pid;
			if (isset($arr['MainProcessName'])) {
				if ($serv->taskworker) {
					swoole_set_process_name($arr['MainProcessName'] . ' task_pid_' . $pid);
				} else {
					swoole_set_process_name($arr['MainProcessName'] . ' worker_pid_' . $pid);
				}
			}
			$this->_behavior->onWorkerStart($serv, $worker_id);
		} catch (Throwable $ex) {
			$this->_logPrint($ex, $serv);
		}
	}

	public function onReceive($server, $fd, $from_id, $data) {
		try {
			$this->_behavior->onReceive($server, $fd, $from_id, $data);
		} catch (Throwable $ex) {
			$this->_logPrint($ex, $server);
		}
	}

	public function onTask($server, $task_id, $from_id, $data) {
		try {
			$this->_behavior->onTask($server, $task_id, $from_id, $data);
		} catch (Throwable $ex) {
			$this->_logPrint($ex, $server);
		}
	}

	public function onFinish($serv, $task_id, $data) {
		try {
			$this->_behavior->onFinish($serv, $task_id, $data);
		} catch (Throwable $ex) {
			$this->_logPrint($ex, $server);
		}
	}

	public function onWorkerError($serv, $worker_id, $worker_pid, $exit_code) {
	}

	public function onWorkerStop($server, $worker_id) {
		try {
			$this->_behavior->onWorkerStop($server, $worker_id);
		} catch (Throwable $ex) {
			$this->_logPrint($ex, $server);
		}
	}

	public function onPacket($server, $data, $client_info) {
		try {
			$this->_behavior->onPacket($server, $data, $client_info);
		} catch (Throwable $ex) {
			$this->_logPrint($ex, $server);
		}
	}

	public function onPipeMessage($server, $from_worker_id, $message) {
		try {
			$this->_behavior->onPipeMessage($server, $from_worker_id, $message);
		} catch (Throwable $ex) {
			$this->_logPrint($ex, $server);
		}
	}

	public function onClose($server, $fd, $from_id) {
		try {
			$this->_behavior->onClose($server, $fd, $from_id);
		} catch (Throwable $ex) {
			$this->_logPrint($ex, $server);
		}
	}

	public function onStart($server) {

	}

	public function start($clearMsg = true) {
		if ($clearMsg) {
			if (isset($this->_config['Set']['message_queue_key'])) {
				$messagekey = sprintf("0x%08x", intval($this->_config['Set']['message_queue_key']));
				system('ipcrm -Q ' . $messagekey);
			}
		}
		$this->_swoole->set($this->_config['Set']);
		$events = array(
				"onStart",
				"onShutdown",
				"onWorkerStart",
				"onWorkerStop",
				"onTimer",
				"onConnect",
				"onReceive",
				"onClose",
				"onTask",
				"onFinish",
				"onPipeMessage",
				"onWorkerError",
				"onManagerStart",
				"onManagerStop",
				"onPacket"
		);
		foreach ($events as $event_name) {
			if (method_exists($this, $event_name)) {
				$this->_swoole->on(substr($event_name, 2), array($this, $event_name));
			}
		}
		$this->_swoole->start();
	}
}
