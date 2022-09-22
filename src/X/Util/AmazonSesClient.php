<?php
/**
 * <code>
 * <?php
 * use \X\Util\AmazonSesClient;
 * $sesClient  = new AmazonSesClient([
 *   'region' => $_ENV['AMAZON_SES_REGION'],
 *   'credentials' => [
 *     'key'    => $_ENV['AMAZON_SES_ACCESS_KEY'],
 *     'secret' => $_ENV['AMAZON_SES_SECRET_KEY']
 *   ],
 *   'configuration' => $_ENV['AMAZON_SES_CONFIGURATION'],
 * ]);
 * $sesClient
 *   ->from('notification@sample.com', 'Sample Notifications')
 *   ->to('who@sample.org')
 *   ->message_from_xml('sample', ['name' => 'Sample'])
 *   ->send();
 * </code>
 *
 * Email body and subject: views/email/sample.xml.
 * <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
 * <mail>
 * <subject>Email subject</subject>
 * <message>
 * To {{ name }}
 * 
 * Email body
 * </message>
 * </mail>
 */
namespace X\Util;
use \X\Util\Logger;
use \X\Util\Template;

class AmazonSesClient {
  private $option = null;
  private $charset = 'UTF-8';
  private $from = null;
  private $from_name = null;
  private $to = null;
  private $bcc = null;
  private $cc = null;
  private $subject = null;
  private $message = null;

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
   * Set charset.
   */
  public function charset(string $charset): AmazonSesClient {
    $this->charset = $charset;
    return $this;
  }

  /**
   * Set the sender.
   */
  public function from(string $from, string $from_name = null): AmazonSesClient {
    $this->from = $from;
    $this->from_name = $from_name;
    return $this;
  }

  /**
   * Set destination.
   */
  public function to($to): AmazonSesClient {
    $this->to = $to;
    return $this;
  }

  /**
   * Set destination.
   */
  public function bcc($bcc): AmazonSesClient {
    $this->bcc = $bcc;
    return $this;
  }

  /**
   * Set destination.
   */
  public function cc($cc): AmazonSesClient {
    $this->cc = $cc;
    return $this;
  }

  /**
   * Set up outgoing subject.
   */
  public function subject(string $subject): AmazonSesClient {
    $this->subject = $subject;
    return $this;
  }

  /**
   * Set up outgoing messages.
   */
  public function message(string $message): AmazonSesClient {
    $this->message = $message;
    return $this;
  }

  /**
   * Set up outgoing messages.
   */
  public function messageFromXml(string $path, array $vars = []): AmazonSesClient {
    static $template;
    if (!isset($template))
      $template = new Template();
    $xml = new \SimpleXMLElement($template->load($path, $vars, 'xml'));
    $this
      ->subject((string) $xml->subject)
      ->message(preg_replace('/^(\r\n|\n|\r)|(\r\n|\n|\r)$/', '', (string) $xml->message));
    return $this;
  }

  /**
   * Send.
   */
  public function send(): \Aws\Result {
    $ci =& get_instance();
    $ci->load->library('form_validation'); 
    $ci->form_validation
      ->set_data([
        // 'to' => $this->to,
        'from' => $this->from
      ])
      // ->set_rules('to', 'To Email', 'required|valid_email')
      ->set_rules('from', 'From Email', 'required|valid_email');
    if (!$ci->form_validation->run())
      throw new \InvalidArgumentException(implode('', $ci->form_validation->error_array()));
    $destination['ToAddresses'] = is_array($this->to) ? $this->to : [$this->to];
    isset($this->cc) && $destination['CcAddresses'] = $this->cc;
    isset($this->bcc) && $destination['BccAddresses'] = $this->bcc;
    $result = $this->client()->sendEmail([
      'Destination' => $destination,
      'ReplyToAddresses' => [$this->from],
      'Source' => isset($this->from_name) ? sprintf('%s <%s>', $this->from_name, $this->from) : $this->from,
      'Message' => [
        'Body' => [
          // 'Html' => [
          //     'Charset' => $this->charset,
          //     'Data' => $this->message,
          // ],
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
    return $result;
  }

  /**
   * Get SES client object.
   */
  private function client(): \Aws\Ses\SesClient {
    static $client;
    if (!isset($client))
      $client = new \Aws\Ses\SesClient([
        'credentials' => $this->option['credentials'],
        'version' => $this->option['version'],
        'region'  => $this->option['region'],
      ]);
    return $client;
  }

  /**
   * Reset option.
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