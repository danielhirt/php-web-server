<?php namespace /Users/daniel/programming/php-web-server;

require_once('Constants.php');

class Response {
  protected static $statusCodes = STATUS_CODES;

  protected $status = null;
  protected $body = '';
  protected $headers = [];

  public function __construct($body, $status = null) {
    if (!is_null($status)) {
      $this->status = $status;
    }
    $this->body = $body;

    // set initial headers
    $this->header('Date', gmdate('D, d M Y H:i:s T'));
    $this->header('Content-Type', 'text/html; charset=utf-8');
    $this->header('Server', 'PHPServer/1.0.0');
  }

  public function header($key, $value) {
    $this->headers[ucfirst($key)] = $value;
  }

  public function buildHeaderString() {
    $lines = [];

    // response status
    $lines[] = "HTTP/1.1 ".$this->status."".static::$statusCodes[$this->status];
    // add the headers
    foreach($this->headers as $key => $value) {
      $lines[] = $key.": ".$value;
    }
    return implode(" \r\n", $lines)."\r\n\r\n";
  }

  public function __toString() {
    return $this->buildHeaderString().$this->body;
  }
}
