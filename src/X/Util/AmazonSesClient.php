<?php
namespace X\Util;
use \X\Util\Logger;
use \X\Util\Template;

/**
 * Amazon SES Client.
 * ```php
 * use \X\Util\AmazonSesClient;
 *
 * $ses  = new AmazonSesClient([
 *   'region' => $_ENV['AMS_SES_REGION'],
 *   'credentials' => [
 *     'key' => $_ENV['AMS_SES_ACCESS_KEY'],
 *     'secret' => $_ENV['AMS_SES_SECRET_KEY']
 *   ],
 *   'configuration' => $_ENV['AMS_SES_CONFIGURATION'],
 * ]);
 * $ses
 *   ->from('from@example.com')
 *   ->to('to@example.com')
 *   ->messageFromXml('email/sample', ['name' => 'Alex'])
 *   ->send();
 * ```
 *
 * Email body and subject: application/views/email/sample.xml.
 * ```xml
 * <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
 * <mail>
 * <subject>Test email.</subject>
 * <message>
 * Hi {{ name }}.
 * </message>
 * </mail>
 * ```
 */
class AmazonSesClient {
  /**
   * Options.
   * @var array{credentials: {key: string, secret: string}, configuration: string|null, region: string, version: string}
   */
  private $options = null;

  /**
   * Character code of the email. Default is "UTF-8".
   * @var string
   */
  private $charset = 'UTF-8';

  /**
   * Sender's email address.
   * @var string
   */
  private $from = null;

  /**
   * Sender name.
   * @var string
   */
  private $fromName = null;

  /**
   * Destination email address.
   * @var string
   */
  private $to = null;

  /**
   * BCC email address.
   * @var string
   */
  private $bcc = null;

  /**
   * CC email address.
   * @var string
   */
  private $cc = null;

  /**
   * Subject.
   * @var string
   */
  private $subject = null;

  /**
   * Body.
   * @var string
   */
  private $message = null;

  /**
   * Initialize AmazonSesClient.
   * @param string $options[credentials][key] AWS access key ID.
   * @param string $options[credentials][secret] AWS secret access key.
   * @param string $options[configuration] The name of the configuration set to use when sending the email. Default is null.
   * @param string $options[region] The region to send service requests to.
   * @param string $options[version] Amazon SES Version. Default is "latest".
   */
  public function __construct(array $options=[]) {
    $this->options = array_replace_recursive([
      'credentials' => [
        'key' => null,
        'secret' => null,
      ],
      'configuration' => null,
      'region' => null,
      'version' => 'latest',
    ], $options);
  }

  /**
   * Set charset.
   * @param string $charset Character code of the email.
   * @return AmazonSesClient
   */
  public function charset(string $charset): AmazonSesClient {
    $this->charset = $charset;
    return $this;
  }

  /**
   * Set the sender.
   * @param string $from Sender's email address.
   * @param string $fromName Sender name.
   * @return AmazonSesClient
   */
  public function from(string $from, string $fromName=null): AmazonSesClient {
    $this->from = $from;
    $this->fromName = $fromName;
    return $this;
  }

  /**
   * Set Destination email address.
   * @param string $to Destination email address.
   * @return AmazonSesClient
   */
  public function to($to): AmazonSesClient {
    $this->to = $to;
    return $this;
  }

  /**
   * Set BCC email address.
   * @param string $bcc BCC email address.
   * @return AmazonSesClient
   */
  public function bcc($bcc): AmazonSesClient {
    $this->bcc = $bcc;
    return $this;
  }

  /**
   * Set CC email address.
   * @param string $cc CC email address.
   * @return AmazonSesClient
   */
  public function cc($cc): AmazonSesClient {
    $this->cc = $cc;
    return $this;
  }

  /**
   * Set Subject.
   * @param string $subject Subject.
   * @return AmazonSesClient
   */
  public function subject(string $subject): AmazonSesClient {
    $this->subject = $subject;
    return $this;
  }

  /**
   * Set Body.
   * @param string $body Body.
   * @return AmazonSesClient
   */
  public function message(string $message): AmazonSesClient {
    $this->message = $message;
    return $this;
  }

  /**
   * Set the mail body based on XML.
   * @param string $xmlPath Path of the XML file. Relative path from `application/views/`.
   * @param array $params (optional) Embedded variables for subject and body text.
   * @return AmazonSesClient
   */
  public function messageFromXml(string $xmlPath, array $params=[]): AmazonSesClient {
    static $template;
    if (!isset($template))
      $template = new Template();
    $xml = new \SimpleXMLElement($template->load($xmlPath, $params, 'xml'));
    $this
      ->subject((string) $xml->subject)
      ->message(preg_replace('/^(\r\n|\n|\r)|(\r\n|\n|\r)$/', '', (string) $xml->message));
    return $this;
  }

  /**
   * Send.
   * @return array{MessageId: string} Result of email transmission.
   */
  public function send(): \Aws\Result {
    $CI =& get_instance();
    $CI->load->library('form_validation'); 
    $CI->form_validation
      ->reset_validation()
      ->set_data([
        // 'to' => $this->to,
        'from' => $this->from
      ])
      // ->set_rules('to', 'To Email', 'required|valid_email')
      ->set_rules('from', 'From Email', 'required|valid_email');
    if (!$CI->form_validation->run())
      throw new \InvalidArgumentException(implode('', $CI->form_validation->error_array()));
    $destination['ToAddresses'] = is_array($this->to) ? $this->to : [$this->to];
    isset($this->cc) && $destination['CcAddresses'] = $this->cc;
    isset($this->bcc) && $destination['BccAddresses'] = $this->bcc;
    $res = $this->client()->sendEmail([
      'Destination' => $destination,
      'ReplyToAddresses' => [$this->from],
      'Source' => isset($this->fromName) ? sprintf('%s <%s>', $this->fromName, $this->from) : $this->from,
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
      'ConfigurationSetName' => $this->options['configuration'],
    ]);
    $this->reset();
    return $res;
  }

  /**
   * Get SES client instance.
   * @return \Aws\Ses\SesClient SES client instance.
   */
  private function client(): \Aws\Ses\SesClient {
    static $client;
    if (!isset($client))
      $client = new \Aws\Ses\SesClient([
        'credentials' => $this->options['credentials'],
        'version' => $this->options['version'],
        'region' => $this->options['region'],
      ]);
    return $client;
  }

  /**
   * Reset options.
   * @return void
   */
  private function reset(): void {
    $this->charset = 'UTF-8';
    $this->from = null;
    $this->fromName = null;
    $this->to = null;
    $this->bcc = null;
    $this->cc = null;
    $this->subject = null;
    $this->message = null;
  }
}