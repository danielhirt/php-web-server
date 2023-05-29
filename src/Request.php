<?php namespace /Users/daniel/programming/php-web-server;

class Request {
  protected $method = null;
  protected $uri = null;
  protected $parameters = [];
  protected $headers = [];

  public function __construct($method, $uri, $headers) {
    $this->headers = $headers;
    $this->method = strtoupper($method);

    // split uri and parameters string
    @list($this->uri, $params) = explode('?', $uri);
    parse_str($params, $this->parameters);
  }

  public static function withHeaderString($header) {
    // explode the string into lines
    $lines = explode("\n", $header);
    // extract the method and uri
    list($method, $uri) = explode(' ', array_shift($lines));
    $headers = [];

    foreach($lines as $line) {
      // clean the line 
      $line = trim($line);

      if (strpos($line, ': ') !== false) {
        list($key, $value) = explode(': ', $line);
        $headers[$key] = $value;
      }
    }
    return new static($method, $uri, $headers);
  }

  public function method() {
    return $this->method;
  }

  public function uri() {
    return $this->uri;
  }

  public function header($key, $default = null) {
    if (!isset($this->headers[$key])) {
      return $default;
    }
    return $this->headers[$key];
  }

  public function param($key, $default = null) {
    if (!isset($this->parameters[$key])) {
      return $default;
    }
    return $this->parameters[$key];
  }
}
