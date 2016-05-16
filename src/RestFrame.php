<?php
namespace mharj;

abstract class RestFrame {
	private $ioFactory;
	private static $corsOrigins = array();
	private static $corsMethods = array();
	private static $corsHeaders = array();
	private static $compress = false;
	private $req;
	
	private function __construct(IOFactory $ioFactory) {
		$this->req = HttpRequest::getServerHttpRequest();
		$this->resp = new HttpResponse();
		$this->resp->setHeader('Content-Type',$ioFactory->getContentType());
		$this->ioFactory = $ioFactory;
		$this->setHeaders();
		switch ( filter_input(INPUT_SERVER,"REQUEST_METHOD") ) {
			case "POST":		$this->write( $ioFactory->toString( $this->doPost($this->req,$this->resp) ) ); break;
			case "PUT":		$this->write( $ioFactory->toString( $this->doPut($this->req,$this->resp) ) ); break;
			case "DELETE":		$this->write( $ioFactory->toString( $this->doDelete($this->req,$this->resp) ) ); break;				
			case "OPTIONS":		$data = $this->doOptions($this->req,$this->resp);
						if ( ! is_null($data) ) { // options should not have data to write
							$this->write( $ioFactory->toString( $data ) ); 
						} else {
							$this->buildHeaders();	
						}
						break;
			default:		$this->write( $ioFactory->toString( $this->doGet($this->req,$this->resp) ) );
		}
	}
	private function buildHeaders() {
		foreach ( $this->resp->getHeaderNames() AS $name ) {
			header($name.": ".implode(",",$this->resp->getHeader($name)));
		}
		http_response_code( $this->resp->getStatus() );
	}
	private function write($data) {
		$this->buildHeaders();
		if ( self::$compress == true && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ) {
			ob_start("ob_gzhandler");
			echo $data;
			ob_flush();
		} else {
			echo $data;
		}
        }

	
	private function setHeaders() {
		$headers = getallheaders();
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
	public static function setCompression($status=false) {
		self::$compress = $status;
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
	
	public static function run(IOFactory $ioFactory) {
		new static($ioFactory);
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
