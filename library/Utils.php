<?php 
function d ($object, $label = '')
{
	Zend_Debug::dump($object, $label);
}

function dLog ($var, $label = '') {
	//$writer = new Zend_Log_Writer_Stream('d:/logs/zendFramework/log.txt');
	//$logger = new Zend_Log($writer);
	//$logger->log(Zend_Debug::dump($var,$label,false),Zend_Log::INFO);
}

function fblog($msg) {
	$dbg = array();
	$hasXdebug = function_exists('xdebug_call_file');
	
	if ($hasXdebug) {
		$str = xdebug_call_file() . 
		':' .
		xdebug_call_line().
		':'.
		xdebug_call_function();
		$dbg['line'] = $str;		
	}
	
	if (count($dbg) == 0) {
		$dbg =  $msg;
	} else {
		$dbg['object'] = $msg;
	}
	if ($hasXdebug) {
		if (is_array($dbg)) {
			$dbg['stacktrace'] = xdebug_get_function_stack();
		}
	}
	
	if (class_exists('FB')) {
		FB::dump('', $dbg);
	} else {
		Zend_Debug::dump($dbg, '');
	}
}