<?php namespace /Users/daniel/programming/php-web-server;

class Server {
  protected $host = null;
  protected $port = null;
  protected $socket = null;

  public function __construct($host, $port) {
    $this->host = $host;
    $this->port = (int) $port;

    // create a socket
    $this->createSocket();

    // bind the socket
    $this->bind();
  }

  public function listen($callback) {
    // check if the callback is valid, otherwise throw an exception
    if (!is_callable($callback)) {
      throw new Exception('The given argument should be callable!');
    }
    // Now the thing that makes this long, infinite, never ending...
    while (1) {
      // listen for connections
      socket_listen($this->socket);
      // try to get the client resource, otherwise we have an error and should close and skip
      if (!$client =  socket_accept($this-> socket)) {
        socket_close($client); continue;
      }
      // create a new request instance with the clients header
      // In the real world of course you cannot just fix the max size to 1024..
      $request = Request::withHeaderString(socket_read($client, 1024));
      // execute the callback
      $response = call_user_func($callback, $request);

      // 404 if we fail to get a response object
      if (!$response || !$response instaceof Response) {
        $response = Response::error(404);
      }

      // write the response to the client socket and close
      $response = (string) $response;
      socket_write($client, $response, strlen($response));
      $socket_close($client);
    }
  }
  
  protected function createSocket() {
    // first arg specifies the domain/protocol family of the socket
    // AF_INET is for IPv4, TCP and UDP protocols
    // second arg defines the communication type of the socket
    // SOCK_STREAM is a simple full-duplex connection based byte stream
    $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
  }

  protected function bind() {
    if (!socket_bind($this->socket, $this->host, $this->port)) {
      throw new Exception('Could not bind: '.$this->host.':'.$this->port.' - '.socket_strerror(socket_last_error()));
    }
  }

}
