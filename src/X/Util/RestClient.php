<?php
namespace X\Util;

/**
 * REST API client.
 * ```php
 * use \X\Util\RestClient;
 * 
 * $client = new RestClient([
 *   'base_url' => 'https://example.com/',
 *   'headers' => [
 *     'X-API-KEY' => 'rrjhueveu3zcywqy4ry2m5qd',
 *   ],
 * ]);
 * 
 * // Request GET https://example.com/users.
 * $res = $this->backend->get('users');
 * var_dump($res->response);
 * ```
 */
class RestClient {
  /**
   * Rest Client option.
   * @var {
   *   headers?: string[],
   *   parameters?: mixed[],
   *   curl_option?: mixed[],
   *   user_agent? string,
   *   base_url?: string,
   *   username?: string,
   *   password?: string,
   *   ssl: {cert_file?: string, ca_file?: string, secret_key_file?: string, secret_key_passphrase?: string}
   * }
   */
  public $options;

  /**
   * Request URL.
   * @var string
   */
  public $requestUrl;

  /**
   * Response data.
   * @var array
   */
  public $response;

  /**
   * Raw response data.
   * @var string
   */
  public $responseRaw;

  /**
   * Response header.
   * @var array
   */
  public $responseHeaders;

  /**
   * Result of curl_getinfo().
   * @var object
   */
  public $info;

  /**
   * Response Error.
   * @var string
   */
  public $error;

  /**
   * HTTP Status.
   * @var int
   */
  public $status;

  /**
   * Initialize RestClient.
   * @param string[] $options[headers] (optional) HTTP request header to be applied to all transmissions.
   * @param mixed[] $options[parameters] (optional) Request parameters that apply to all submissions. For POST and PUT, this is the request body, and for GET, it is the query parameter.
   * @param mixed[] $options[curl_option] (optional) CURL options that apply to all submissions.
   * @param string $options[user_agent] (optional) User agent to be applied to all submissions. Defaults to "PHP RestClient".
   * @param string $options[base_url] (optional) Base URL of the request.
   * @param string $options[username] (optional) User name to be applied to `CURLOPT_USERPWD`.
   * @param string $options[password] (optional) Password to be applied to `CURLOPT_USERPWD`.
   * @param string $options[ssl][cert_file] (optional) Pem file path required for SSL authentication, set to `CURLOPT_SSLCERT`.
   * @param string $options[ssl][ca_file] (optional) CA certificate path required for SSL authentication. Set to `CURLOPT_SSLCERT`.
   * @param string $options[ssl][secret_key_file] (optional) The private key path required for SSL authentication, set to `CURLOPT_SSLKEY`.
   * @param string $options[ssl][secret_key_passphrase] (optional) Password required to use the SSL private key, set to `CURLOPT_SSLKEYPASSWD`.
   */  
  public function __construct(array $options=[]) {
    $this->options = array_merge([
      'headers' => [],
      'parameters' => [],
      'curl_option' => [],
      'user_agent' => 'PHP RestClient',
      'base_url' => null,
      'username' => null,
      'password' => null,
      'ssl' => null,
    ], $options);
    if (!empty($options['ssl']))
      $this->options['ssl'] = array_merge([
        'cert_file' => '',// CURLOPT_SSLCERT
        'ca_file' => '',// CURLOPT_CAINFO
        'secret_key_file' => '',// CURLOPT_SSLKEY
        'secret_key_passphrase' => '',// CURLOPT_SSLKEYPASSWD
      ], $options['ssl']);
  }

  /**
   * GET Request.
   * @param string $url Request URL.
   * @param array $params (optional) Query Parameters.
   * @param array $headers (optional) Request headers.
   * @return RestClient
   */
  public function get(string $url, $params=[], array $headers=[]): RestClient {
    return $this->send($url, 'GET', $params, $headers);
  }

  /**
   * Send POST request.
   * @param string $url Request URL.
   * @param array $params (optional) Request Body.
   * @param array $headers (optional) Request headers.
   * @return RestClient
   */
  public function post(string $url, $params=[], array $headers=[]): RestClient {
    return $this->send($url, 'POST', $params, $headers);
  }

  /**
   * Send PUT request.
   * @param string $url Request URL.
   * @param array $params (optional) Request Body.
   * @param array $headers (optional) Request headers.
   * @return RestClient
   */
  public function put(string $url, $params=[], array $headers=[]): RestClient {
    return $this->send($url, 'PUT', $params, $headers);
  }

  /**
   * Send DELETE request.
   * @param string $url Request URL.
   * @param array $params (optional) Request Body.
   * @param array $headers (optional) Request headers.
   * @return RestClient
   */
  public function delete(string $url, $params=[], array $headers=[]): RestClient {
    return $this->send($url, 'DELETE', $params, $headers);
  }

  /**
   * Common Requests.
   * @param string $url Request URL.
   * @param string $method HTTP Method.
   * @param array $params (optional) Query parameter in case of GET, request body otherwise.
   * @param array $headers (optional) Request headers.
   * @return RestClient
   */
  private function send(string $url, string $method, $params=[], array $headers=[]): RestClient {
    $client = clone $this;
    $client->requestUrl = $url;
    $ch = curl_init();

    // curl option.
    $options = [
      CURLOPT_HEADER => true,
      CURLOPT_RETURNTRANSFER => true,
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
      $options[CURLOPT_POST] = true;
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
      if ($client->requestUrl[0] != '/' && substr($client->options['base_url'], -1) != '/')
        $client->requestUrl = '/' . $client->requestUrl;
      $client->requestUrl = $client->options['base_url'] . $client->requestUrl;
    }
    $options[CURLOPT_URL] = $client->requestUrl;

    // SSL client authentication.
    if (!empty($client->options['ssl'])) {
      $options[CURLOPT_SSL_VERIFYPEER] = false;
      $options[CURLOPT_SSL_VERIFYHOST] = false;
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
    curl_setopt_array($ch, $options);
    $client->parse(curl_exec($ch));

    // Get response.
    $client->info = (object) curl_getinfo($ch);
    $client->error = curl_error($ch);
    curl_close($ch);
    if (!($client->status >= 200 && $client->status < 400))
      throw new \X\Exception\RestClientException(sprintf('Request failed. status=%s, url=%s %s, error=%s', $client->status, $method, $client->requestUrl, $client->error));
    return $client;
  }

  /**
   * Set response data (responseHeaders,responseRaw,response).
   * @param string $res Response data (curl_exec($ch)).
   * @return void
   */
  private function parse(string $res): void {
    $this->response = null;
    $this->responseRaw = null;
    $this->responseHeaders = [];
    $this->status = null;
    $line = strtok($res, "\n");
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