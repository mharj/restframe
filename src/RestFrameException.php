<?php
namespace mharj;

abstract class RestFrameException extends \Exception {
	private $ioFactory;
	public function __construct(IOFactory $ioFactory,$msg,$code) {
		$this->ioFactory = $ioFactory;
		parent::__construct($msg,$code,null);
	}
	public function getOutput() {
		return $this->ioFactory->toString(array("error"=>"Exception","msg"=>$this->getMessage()));
	}
}
