<?php
namespace mharj;

abstract class RestFrame {
	private static $instance;
	private $ioFactory;
	private static $corsOrigins = array();
	private static $corsMethods = array();
	private static $corsHeaders = array();
	private static $corsExposeHeaders = array();
	private $req;
	
	private function __construct(IOFactory $ioFactory) {
		$this->req = HttpRequest::getServerHttpRequest();
		$this->resp = new HttpResponse();
		$this->resp->setHeader('Content-Type',$ioFactory->getContentType());
		$this->ioFactory = $ioFactory;
		$this->setHeaders();
	}
	
	private function runMethods(IOFactory $ioFactory) {
		$data = null;
		switch ( $this->req->getMethod() ) {
			case "POST":		$data = $this->doPost($this->req,$this->resp); break;
			case "PUT":			$data = $this->doPut($this->req,$this->resp); break;
			case "DELETE":		$data = $this->doDelete($this->req,$this->resp); break;				
			case "OPTIONS":		$data = $this->doOptions($this->req,$this->resp); break;
			default:			$data = $this->doGet($this->req,$this->resp); break;
		}
		$this->buildHeaders();
		if ( $data != null ) {
			$this->resp->setContent($data);
			if ( $this->resp->isRaw() ) {
				$this->write( $data );
			} else {
				$this->write( $ioFactory->toString( $data ) );
			}
		} 
	}
	
	private function buildHeaders() {
		foreach ( $this->resp->getHeaderNames() AS $name ) {
			if ( ! $this->req instanceof CliHttpRequest )  {
				header($name.": ".implode(",",$this->resp->getHeader($name)));
			}
		}
		http_response_code( $this->resp->getStatus() );
	}
	
	private function write($data) {
		if ( $this->resp->getCompression() == true && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ) {
			ob_start("ob_gzhandler");
			echo $data;
			ob_flush();
		} else {
			echo $data;
		}
	}

	private function setHeaders() {
		if ( ! empty(self::$corsOrigins) && $this->req->containsHeader('Origin') ){
			if ( ! in_array($this->req->getHeader('Origin'),self::$corsOrigins) ) { 
				throw new RestFrameCorsException($this->ioFactory,"CORS error",403); // if origin is not in list, throw 403
			}
			$this->resp->setHeader('Access-Control-Allow-Origin',$this->req->getHeader('Origin'));
			if ( ! empty(self::$corsMethods) ) {
				foreach (self::$corsMethods AS $value ) {
					$this->resp->addHeader('Access-Control-Allow-Methods',$value);
				}
			}
			if ( ! empty(self::$corsHeaders) ) {
				foreach (self::$corsHeaders AS $value ) {
					$this->resp->addHeader('Access-Control-Allow-Headers',$value);
				}
			}
			if ( ! empty(self::$corsExposeHeaders) ) {
				foreach (self::$corsExposeHeaders AS $value ) {
					$this->resp->addHeader('Access-Control-Expose-Headers',$value);
				}
			}
		}		
	}
	
	protected function requireAuth(AuthFactory $auth) {
		if ( ! $auth->check() )  {
			throw new RestFrameAuthException($this->ioFactory,"Authentication error",403);
		}
	}
	
	protected function requireGet(array $param) {
		foreach ( $param AS $p) {
			if ( filter_input(INPUT_GET,$p) == null ) {
				throw new RestFrameParameterException($this->ioFactory,"Parameter error",0);
			}
		}
	}
	protected function requirePost(array $param) {
		foreach ( $param AS $p) {
			if ( filter_input(INPUT_POST,$p) == null ) {
				throw new RestFrameParameterException($this->ioFactory,"Parameter error",0);
			}
		}
	}	
	public static function setCorsOrigins(array $origins) {
		self::$corsOrigins = $origins;
	}
	public static function setCorsMethods(array $methods) {
		self::$corsMethods = $methods;
	}
	public static function setCorsHeaders(array $headers) {
		self::$corsHeaders = $headers;
	}	
	public static function setCorsExposeHeaders(array $headers) {
		self::$corsExposeHeaders = $headers;
	}	
	public static function run(IOFactory $ioFactory) {
		self::$instance = new static($ioFactory);
		self::$instance->runMethods($ioFactory);
		return self::$instance->getResponse();
	}
	public static function writeException(RestFrameException $ex) {
		self::$instance->write($ex->getOutput());
	}
	public function getResponse() {
		return $this->resp;
	}
	
	/**
	 * CRUD Read
	 */
	abstract function doGet(HttpRequest $req,HttpResponse $resp);
	/**
	 * CRUD Create
	 */
	abstract function doPost(HttpRequest $req,HttpResponse $resp);
	/**
	 * CRUD Update
	 */
	abstract function doPut(HttpRequest $req,HttpResponse $resp);
	/**
	 * CRUD delete
	 */
	abstract function doDelete(HttpRequest $req,HttpResponse $resp);
	/**
	 * CRUD options (also CORS access-control pre-flight)
	 */
	abstract function doOptions(HttpRequest $req,HttpResponse $resp);
}
