<?php
namespace X\Util;
// use \X\Util\Logger;

class RestClient {
  public $response;
  public $responseRaw;
  public $responseHeaders;
  public $info;
  public $error;
  public $status;
  public $options;
  public $requestUrl;
  
  public function __construct(array $options = []) {
    // Set options.
    $this->options = array_merge([
      'headers' => [],
      'parameters' => [],
      'curl_option' => [],
      'user_agent' => 'PHP RestClient',
      'base_url' => null,
      'username' => null,
      'password' => null,
      // 'debug' => false,
      'ssl' => null,
    ], $options);

    // Set SSL option.
    if (!empty($options['ssl']))
      $this->options['ssl'] = array_merge([
        'cert_file' => '',// CURLOPT_SSLCERT
        'ca_file' => '',// CURLOPT_CAINFO
        'secret_key_file' => '',// CURLOPT_SSLKEY
        'secret_key_passphrase' => '',// CURLOPT_SSLKEYPASSWD
      ], $options['ssl']);
  }

  /**
   * Send GET request.
   */
  public function get(string $requestUrl, $params = [], array $headers = []): RestClient {
    return $this->send($requestUrl, 'GET', $params, $headers);
  }

  /**
   * Send POST request.
   */
  public function post(string $requestUrl, $params = [], array $headers = []): RestClient {
    return $this->send($requestUrl, 'POST', $params, $headers);
  }

  /**
   * Send PUT request.
   */
  public function put(string $requestUrl, $params = [], array $headers = []): RestClient {
    return $this->send($requestUrl, 'PUT', $params, $headers);
  }

  /**
   * Send DELETE request.
   */
  public function delete(string $requestUrl, $params = [], array $headers = []): RestClient {
    return $this->send($requestUrl, 'DELETE', $params, $headers);
  }

  /**
   * Common Requests.
   */
  private function send(string $requestUrl, string $method, $params = [], array $headers = []): RestClient {
    $client = clone $this;
    $client->requestUrl = $requestUrl;
    $curl = curl_init();

    // curl option.
    $options = [
      CURLOPT_HEADER => TRUE,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_USERAGENT => $client->options['user_agent']
    ];

    // User name and password for basic authentication.
    if ($client->options['username']
        && $client->options['password'])
      $options[CURLOPT_USERPWD] = sprintf("%s:%s", $client->options['username'], $client->options['password']);


    // Set request header.
    if (count($client->options['headers']) || count($headers)){
      $options[CURLOPT_HTTPHEADER] = [];
      $headers = array_merge($client->options['headers'], $headers);
      foreach($headers as $key => $values) {
        foreach(is_array($values)? $values : [$values] as $value)
          $options[CURLOPT_HTTPHEADER][] = sprintf("%s:%s", $key, $value);
      }
    }

    // Set request parameters.
    if (is_array($params)){
      $params = array_merge($client->options['parameters'], $params);
      $params = http_build_query($params);
      $params = preg_replace("/%5B[0-9]+%5D=/simU", "%5B%5D=", $params);
    } else
      $params = (string) $params;

    // Per HTTP method.
    if ($method == 'POST') {
      $options[CURLOPT_POST] = TRUE;
      $options[CURLOPT_POSTFIELDS] = $params;
    } else if ($method != 'GET') {
      $options[CURLOPT_CUSTOMREQUEST] = $method;
      $options[CURLOPT_POSTFIELDS] = $params;
    } else if ($params) {
      $client->requestUrl .= strpos($client->requestUrl, '?')? '&' : '?';
      $client->requestUrl .= $params;
    }

    // Request URL.
    if ($client->options['base_url']){
      if ($client->requestUrl[0] != '/'
          && substr($client->options['base_url'], -1) != '/')
        $client->requestUrl = '/' . $client->requestUrl;
      $client->requestUrl = $client->options['base_url'] . $client->requestUrl;
    }
    $options[CURLOPT_URL] = $client->requestUrl;

    // SSL client authentication.
    if (!empty($client->options['ssl'])) {
      $options[CURLOPT_SSL_VERIFYPEER] = FALSE;
      $options[CURLOPT_SSL_VERIFYHOST] = FALSE;
      $options[CURLOPT_SSLCERT] = $client->options['ssl']['cert_file'];
      $options[CURLOPT_CAINFO] = $client->options['ssl']['ca_file'];
      $options[CURLOPT_SSLKEY] = $client->options['ssl']['secret_key_file'];
      $options[CURLOPT_SSLKEYPASSWD] = $client->options['ssl']['secret_key_passphrase'];
    }

    // Set other customized curl options.
    if ($client->options['curl_option']) {
      foreach($client->options['curl_option'] as $key => $value)
        $options[$key] = $value;
    }

    // Send request.
    curl_setopt_array($curl, $options);
    $client->parse(curl_exec($curl));

    // Get response.
    $client->info = (object) curl_getinfo($curl);
    $client->error = curl_error($curl);
    curl_close($curl);
    if (!($client->status >= 200 && $client->status < 400))
      throw new \X\Exception\RestClientException(sprintf('Request failed. status=%s, url=%s %s, error=%s', $client->status, $method, $client->requestUrl, $client->error));
    // if ($client->options['debug'])
    //   Logger::debug(sprintf('status=%s, url=%s %s', $client->status, $method, $client->requestUrl));
    return $client;
  }

  /**
   * Parse response.
   */
  private function parse(string $response) {
    $this->response = null;
    $this->responseRaw = null;
    $this->responseHeaders = [];
    $this->status = null;
    $line = strtok($response, "\n");
    do {
      if (strlen(trim($line)) == 0) {
        if (count($this->responseHeaders) > 0)
          break;
      } else if (strpos($line, 'HTTP') === 0){
        $this->status = (int) preg_replace('/.+(\d{3}).+/', '$1', $line);
      } else {
        list($key, $value) = explode(':', $line, 2);
        $key = trim(strtolower(str_replace('-', '_', $key)));
        $value = trim($value);
        if (empty($this->responseHeaders[$key]))
          $this->responseHeaders[$key] = $value;
        else if (is_array($this->responseHeaders[$key]))
          $this->responseHeaders[$key][] = $value;
        else
          $this->responseHeaders[$key] = [$this->responseHeaders[$key], $value];
      }
    } while($line = strtok("\n"));
    $this->responseHeaders = (object) $this->responseHeaders;
    $this->responseRaw = strtok("");
    $this->response = json_decode($this->responseRaw, true);
  }
}