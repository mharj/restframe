<?php
namespace mharj;

class RestFrameJsonException extends RestFrameException {
	public function getRestOutput($trace=false) {
		$out = array('error'=>'Exception',"msg"=>$this->getMessage() );
		if ( $trace == true) {
			$out['trace'] = $this->getTrace();
		}
		return json_encode($out);
	}
}
