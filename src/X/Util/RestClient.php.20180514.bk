<?php
/**
 * Rest client class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Util;
class RestClient
{

  public $option;

  // Response body.
  public $response;

  // Response source body.
  public $response_source;

  // Response header.
  public $headers;

  // Response info object.
  public $info;

  // Response error string.
  public $error;

  // indexed array of raw HTTP response status lines.
  public $status;

  /**
   * Construct
   *
   * @param array $option
   * @return void
   */
  public function __construct(array $option = [])
  {
    $defaultOption = [
      'headers' => [],
      'parameters' => [],
      'curl_option' => [],
      'user_agent' => "PHP RestClient",
      'base_url' => null,
      'username' => null,
      'password' => null,
      'debug' => false,
      'ssl' => null,
    ];
    $this->option = array_merge($defaultOption, $option);
    if (!empty($option['ssl'])) {
      $sslDefaultOption = [
        'cert_file' => '',// CURLOPT_SSLCERT
        'ca_file' => '',// CURLOPT_CAINFO
        'secret_key_file' => '',// CURLOPT_SSLKEY
        'secret_key_passphrase' => '',// CURLOPT_SSLKEYPASSWD
      ];
      $this->option['ssl'] = array_merge($sslDefaultOption, $option['ssl']);
    }
  }

  /**
   * Get request
   *
   * @param  string $url
   * @param  array  $parameters
   * @param  array  $headers
   * @return RestClient
   */
  public function get(string $url, $parameters = [], array $headers = []): RestClient
  {
    return $this->execute($url, 'GET', $parameters, $headers);
  }

  /**
   * Get request
   *
   * @param  string $url
   * @param  array  $parameters
   * @param  array  $headers
   * @return RestClient
   */
  public function post(string $url, $parameters = [], array $headers = []): RestClient
  {
    return $this->execute($url, 'POST', $parameters, $headers);
  }

  /**
   * Put request
   *
   * @param  string $url
   * @param  array  $parameters
   * @param  array  $headers
   * @return RestClient
   */
  public function put(string $url, $parameters = [], array $headers = []): RestClient
  {
    return $this->execute($url, 'PUT', $parameters, $headers);
  }

  /**
   * Delete request
   *
   * @param  string $url
   * @param  array  $parameters
   * @param  array  $headers
   * @return RestClient
   */
  public function delete(string $url, $parameters = [], array $headers = []): RestClient
  {
    return $this->execute($url, 'DELETE', $parameters, $headers);
  }

  /**
   * Request
   *
   * @param  string $url
   * @param  array  $parameters
   * @param  array  $headers
   * @return RestClient
   */
  private function execute(string $url, string $method, $parameters = [], array $headers = []): RestClient
  {
    $client = clone $this;
    $client->url = $url;
    $curl = curl_init();
    $option = [
      CURLOPT_HEADER => TRUE,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_USERAGENT => $client->option['user_agent']
    ];
    if ($client->option['username'] && $client->option['password']) {
      $option[CURLOPT_USERPWD] = sprintf("%s:%s", $client->option['username'], $client->option['password']);
    }
    if (count($client->option['headers']) || count($headers)){
      $option[CURLOPT_HTTPHEADER] = [];
      $headers = array_merge($client->option['headers'], $headers);
      foreach($headers as $key => $values) {
        foreach(is_array($values)? $values : [$values] as $value) {
          $option[CURLOPT_HTTPHEADER][] = sprintf("%s:%s", $key, $value);
        }
      }
    }

    // Allow passing parameters as a pre-encoded string (or something that
    // allows casting to a string). Parameters passed as strings will not be
    // merged with parameters specified in the default option.
    if (is_array($parameters)){
      $parameters = array_merge($client->option['parameters'], $parameters);
      $parameters = http_build_query($parameters);
      // http_build_query automatically adds an array index to repeated
      // parameters which is not desirable on most systems. This hack
      // reverts "key[0]=foo&key[1]=bar" to "key[]=foo&key[]=bar"
      $parameters = preg_replace("/%5B[0-9]+%5D=/simU", "%5B%5D=", $parameters);
    } else {
      $parameters = (string) $parameters;
    }
    if ($method == 'POST') {
      $option[CURLOPT_POST] = TRUE;
      $option[CURLOPT_POSTFIELDS] = $parameters;
    } else if ($method != 'GET') {
      $option[CURLOPT_CUSTOMREQUEST] = $method;
      $option[CURLOPT_POSTFIELDS] = $parameters;
    } else if ($parameters) {
      $client->url .= strpos($client->url, '?')? '&' : '?';
      $client->url .= $parameters;
    }
    if ($client->option['base_url']){
      if ($client->url[0] != '/' && substr($client->option['base_url'], -1) != '/') {
        $client->url = '/' . $client->url;
      }
      $client->url = $client->option['base_url'] . $client->url;
    }
    $option[CURLOPT_URL] = $client->url;
    if (!empty($client->option['ssl'])) {
      $option[CURLOPT_SSL_VERIFYPEER] = FALSE;
      $option[CURLOPT_SSL_VERIFYHOST] = FALSE;
      $option[CURLOPT_SSLCERT] = $client->option['ssl']['cert_file'];
      $option[CURLOPT_CAINFO] = $client->option['ssl']['ca_file'];
      $option[CURLOPT_SSLKEY] = $client->option['ssl']['secret_key_file'];
      $option[CURLOPT_SSLKEYPASSWD] = $client->option['ssl']['secret_key_passphrase'];
    }
    if ($client->option['curl_option']) {
      // array_merge would reset our numeric keys.
      foreach($client->option['curl_option'] as $key => $value) {
        $option[$key] = $value;
      }
    }
    curl_setopt_array($curl, $option);
    $client->parse(curl_exec($curl));
    $client->info = (object) curl_getinfo($curl);
    $client->error = curl_error($curl);
    curl_close($curl);
    if (!($client->status >= 200 && $client->status < 400)) {
      Logger::e(sprintf('Request failed. status=%s, url=%s %s, response=%s, error=%s', $client->status, $method, $client->url, $client->response_source, $client->error));
      throw new \X\Exception\RestClientException(sprintf('Request failed. status=%s, url=%s %s, response=%s, error=%s', $client->status, $method, $client->url, $client->response_source, $client->error));
    }
    if ($client->option['debug']) {
      Logger::d(sprintf('status=%s, url=%s %s, response=%s', $client->status, $method, $client->url, $client->response_source));
    } else {
      Logger::i(sprintf('status=%s, url=%s %s', $client->status, $method, $client->url));
    }
    return $client;
  }

  /**
   * Parse response
   *
   * @param  string $response
   * @return void
   */
  private function parse(string $response)
  {
    $this->response = null;
    $this->response_source = null;
    $this->headers = [];
    $this->status = null;
    // $this->status = [];
    $line = strtok($response, "\n");
    do {
      if (strlen(trim($line)) == 0) {
        // Since we tokenize on \n, use the remaining \r to detect empty lines.
        if (count($this->headers) > 0) {
          // Must be the newline after headers, move on to response body
          break;
        }
      } else if (strpos($line, 'HTTP') === 0){
        // One or more HTTP status lines
        $this->status = (int) preg_replace('/.+(\d{3}).+/', '$1', $line);
        // $this->status[] = (int) preg_replace('/.+(\d{3}).+/', '$1', $line);
      } else {
        // Has to be a header
        list($key, $value) = explode(':', $line, 2);
        $key = trim(strtolower(str_replace('-', '_', $key)));
        $value = trim($value);
        if (empty($this->headers[$key])) {
          $this->headers[$key] = $value;
        } else if (is_array($this->headers[$key])) {
          $this->headers[$key][] = $value;
        } else {
          $this->headers[$key] = [$this->headers[$key], $value];
        }
      }
    } while($line = strtok("\n"));
    $this->headers = (object) $this->headers;
    $this->response_source = strtok("");
    $this->response = json_decode($this->response_source, true);
  }
}