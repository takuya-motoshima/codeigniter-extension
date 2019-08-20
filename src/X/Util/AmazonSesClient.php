<?php

use \X\Util\Logger;
use \X\Util\Template;

/**
 * Email Amazon SES util class
 * 
 * e.g.:
 * ## PHP sample code:
 * use \X\Util\AmazonSesClient;
 * $sesClient  = new AmazonSesClient([
 *   'credentials' => [
 *     'key'    => getenv('AMAZON_SES_KEY'),
 *     'secret' => getenv('AMAZON_SES_SECRET')
 *   ],
 *   'configuration' => getenv('AMAZON_SES_CONFIGURATION'),
 * ]);
 * $sesClient
 *   ->from('notification@sample.com', 'Sample Notifications')
 *   ->to('who@sample.org')
 *   ->message_from_xml('sample', ['name' => 'Sample'])
 *   ->send();
 * 
 * 
 * ## Email body and subject: views/email/sample.xml
 * <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
 * <mail>
 * <subject>Email subject</subject>
 * <message>
 * To {{ name }}
 * 
 * Email body
 * </message>
 * </mail>
 * 
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
class AmazonSesClient {
  /** @var array */
  private $option = null;

  /** @var string */
  private $charset = 'UTF-8';

  /** @var string */
  private $from = null;

  /** @var string */
  private $from_name = null;

  /** @var string|array */
  private $to = null;

  /** @var string|array */
  private $bcc = null;

  /** @var string|array */
  private $cc = null;

  /** @var string */
  private $subject = null;

  /** @var string */
  private $message = null;

  /**
   * 
   * construct
   *
   * @param array $option
   */
  public function __construct(array $option = []) {
    $this->option = array_replace_recursive([
      'credentials' => [
        'key' => null,
        'secret' => null,
      ],
      'configuration' => null,
      'region' => null,
      'version' => 'latest',
    ], $option);
  }

  /**
   * 
   * Set charset
   * 
   * @param  string $charset
   * @return AmazonSesClient
   */
  public function charset(string $charset): AmazonSesClient {
    $this->charset = $charset;
    return $this;
  }

  /**
   * 
   * Set the sender
   * 
   * @param  string $from
   * @param  string $from_name
   * @return AmazonSesClient
   */
  public function from(string $from, string $from_name = null): AmazonSesClient {
    $this->from = $from;
    $this->from_name = $from_name;
    return $this;
  }

  /**
   * 
   * Set destination
   *
   * @param string|array $to
   * @return AmazonSesClient
   */
  public function to($to): AmazonSesClient {
    $this->to = $to;
    return $this;
  }

  /**
   * 
   * Set destination
   *
   * @param string|array $bcc
   * @return AmazonSesClient
   */
  public function bcc($bcc): AmazonSesClient {
    $this->bcc = $bcc;
    return $this;
  }

  /**
   * 
   * Set destination
   *
   * @param string|array $cc
   * @return AmazonSesClient
   */
  public function cc($cc): AmazonSesClient {
    $this->cc = $cc;
    return $this;
  }

  /**
   *
   * Set up outgoing subject
   *
   * @param string $subject
   * @return AmazonSesClient
   */
  public function subject(string $subject): AmazonSesClient {
    $this->subject = $subject;
    return $this;
  }

  /**
   *
   * Set up outgoing messages
   *
   * @param string $message
   * @return AmazonSesClient
   */
  public function message(string $message): AmazonSesClient {
    $this->message = $message;
    return $this;
  }

  /**
   * Set up outgoing messages
   *
   * @param   string
   * @param   array
   * @return  string
   */
  public function messageFromXml(string $path, array $vars = []): AmazonSesClient {
    static $template;
    if (!isset($template)) {
      $template = new Template();
    }
    $xml = new \SimpleXMLElement($template->load($path, $vars, 'xml'));
    $this
      ->subject((string) $xml->subject)
      ->message(preg_replace('/^(\r\n|\n|\r)|(\r\n|\n|\r)$/', '', (string) $xml->message));
    return $this;
  }

  /**
   *
   * Send
   *
   * @return void
   */
  public function send() {
    try {
      
      $ci =& get_instance();
      $ci->load->library('form_validation'); 
      $ci->form_validation
        ->set_data(['to' => $this->to, 'from' => $this->from,])
        ->set_rules('to', 'To Email', 'required|valid_email')
        ->set_rules('from', 'From Email', 'required|valid_email');
      if (!$ci->form_validation->run()) {
        throw new InvalidArgumentException(implode('', $ci->form_validation->error_array()));
      }
      $destination['ToAddresses'] = [$this->to];
      isset($this->cc) && $destination['CcAddresses'] = $this->cc;
      isset($this->bcc) && $destination['BccAddresses'] = $this->bcc;
      $this->client()->sendEmail([
        'Destination' => $destination,
        'ReplyToAddresses' => [$this->from],
        'Source' => isset($this->from_name) ? sprintf('%s <%s>', $this->from_name, $this->from) : $from,
        'Message' => [
          'Body' => [
            'Html' => [
                'Charset' => $this->charset,
                'Data' => $this->message,
            ],
            'Text' => [
              'Charset' => $this->charset,
              'Data' => $this->message,
            ],
          ],
          'Subject' => [
            'Charset' => $this->charset,
            'Data' => $this->subject,
          ],
        ],
        'ConfigurationSetName' => $this->option['configuration'],
      ]);
      $this->reset();
    } catch (\Throwable $e) {
      throw $e;
    }
  }

  /**
   * 
   * Get SES client object
   *
   * @return \Aws\Ses\SesClient
   */
  private function client(): \Aws\Ses\SesClient {
    static $client;
    if (!isset($client)) {
      $client = new \Aws\Ses\SesClient([
        'credentials' => $this->option['credentials'],
        'version' => $this->option['version'],
        'region'  => $this->option['region'],
      ]);
    }
    return $client;
  }

  /**
   * 
   * Reset option
   *
   * @return void
   */
  private function reset() {
    $this->charset = 'UTF-8';
    $this->from = null;
    $this->from_name = null;
    $this->to = null;
    $this->bcc = null;
    $this->cc = null;
    $this->subject = null;
    $this->message = null;
  }
}