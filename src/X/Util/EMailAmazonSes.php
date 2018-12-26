<?php

use \X\Util\Logger;
use \X\Util\Template;

/**
 * Email Amazon SES util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
class EMailAmazonSes
{
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
  public function __construct(array $option = [])
  {
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
   * @return EMailAmazonSES
   */
  public function charset(string $charset): EMailAmazonSES
  {
    $this->charset = $charset;
    return $this;
  }

  /**
   * 
   * Set the sender
   * 
   * @param  string $from
   * @param  string $from_name
   * @return EMailAmazonSES
   */
  public function from(string $from, string $from_name = null): EMailAmazonSES
  {
    $this->from = $from;
    $this->from_name = $from_name;
    return $this;
  }

  /**
   * 
   * Set destination
   *
   * @param string|array $to
   * @return EMailAmazonSES
   */
  public function to($to): EMailAmazonSES
  {
    $this->to = $to;
    return $this;
  }

  /**
   * 
   * Set destination
   *
   * @param string|array $bcc
   * @return EMailAmazonSES
   */
  public function bcc($bcc): EMailAmazonSES
  {
    $this->bcc = $bcc;
    return $this;
  }

  /**
   * 
   * Set destination
   *
   * @param string|array $cc
   * @return EMailAmazonSES
   */
  public function cc($cc): EMailAmazonSES
  {
    $this->cc = $cc;
    return $this;
  }

  /**
   *
   * Set up outgoing subject
   *
   * @param string $subject
   * @return EMailAmazonSES
   */
  public function subject(string $subject): EMailAmazonSES
  {
    $this->subject = $subject;
    return $this;
  }

  /**
   *
   * Set up outgoing messages
   *
   * @param string $message
   * @return EMailAmazonSES
   */
  public function message(string $message): EMailAmazonSES
  {
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
  public function message_from_xml(string $path, array $vars = []): EMailAmazonSES
  {
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
  public function send()
  {
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
  private function client(): \Aws\Ses\SesClient
  {
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
  private function reset()
  {
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