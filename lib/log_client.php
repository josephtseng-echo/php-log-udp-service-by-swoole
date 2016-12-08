<?php
/**
 *
 * @author JosephZeng
 *
 */
class LogClient {

	public static $sizeList = array();

	public static function log($content, $filename) {
		try {
			clearstatcache();
			if (!file_exists($filename)) {
				$dir = dirname($filename);
				if (!is_dir($dir)) {
					@mkdir($dir, 0775, true);
				}
			}
			file_put_contents($filename, date('Y-m-d H:i:s').':'.floor(microtime() * 1000).' '.$content.PHP_EOL, FILE_APPEND);
		} catch (Exception $ex) {
			
		} catch (Error $ex) {
			
		}
	}
}
