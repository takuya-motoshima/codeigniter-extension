<?php
namespace X\Util;

/**
 * Email Utility.
 */
abstract class EMail {
  /**
   * Email default settings.
   * @var array{wordwrap: bool, mailtype: 'text'|'html', charset: string, priority: int, crlf: string, newline: string}
   */
  private static $defaultConfig = [
    // 'useragent' => 'CodeIgniter',
    // 'protocol' => 'mail',
    // 'mailpath' => '/usr/sbin/sendmail',
    // 'smtp_host' => null,
    // 'smtp_user' => null,
    // 'smtp_pass' => null,
    // 'smtp_port' => 25,
    // 'smtp_timeout' => 5,
    // 'smtp_keepalive' => false,
    // 'smtp_crypto' => null,
    'wordwrap' => false,
    // 'wrapchars' => 76,
    'mailtype' => 'text',
    'charset' => 'utf-8',
    // 'validate' => false,
    'priority' => 1,
    'crlf' => "\r\n",
    'newline' => "\r\n",
    // 'bcc_batch_mode' => false,
    // 'bcc_batch_size' => 200,
    // 'dsn' => false,
  ];

  /**
   * Initialize EMail.
   * @param array $config EMail settings.
   * @return string This class name.
   */
  public static function initialize(array $config=array()): string {
    self::email()->initialize(array_merge(self::$defaultConfig, $config));
    return __CLASS__;
  }

  /**
   * Send.
   * @param bool $autoClear (optional) Whether to clear the destination and other transmission information after transmission. Default is true.
   * @return void
   */
  public static function send($autoClear=true) {
    return call_user_func_array([self::email(), __FUNCTION__], func_get_args());
  }

  /**
   * Set the sender.
   * @param string $from Sender's email address.
   * @param string $fromName (optional) Sender name.
   * @param string $returnPath (optional) Return-Path.
   */
  public static function from($from, $fromName='', $returnPath=null): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set Destination email address.
   * @param string $to Destination email address.
   * @return string This class name.
   */
  public static function to($to): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set BCC email address.
   * @param string $bcc BCC email address.
   * @param string $limit (optional) BCC Batch max number size. Default is blank.
   */
  public static function bcc($bcc, $limit=''): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set Subject.
   * @param string $subject Subject.
   * @return string This class name.
   */
  public static function subject($subject): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set Body.
   * @param string $body Body.
   * @return string This class name.
   */
  public static function message($body): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set the mail body based on Template.
   * @param string $templatePath Path of the Template file. Relative path from `application/views/`.
   * @param array $params (optional) Embedded variables for subject and body text.
   * @return string This class name.
   */
  public static function messageFromTemplate(string $templatePath, array $params=[]): string {
    self::message(self::template()->load($templatePath, $params));
    return __CLASS__;
  }

  /**
   * Set the mail body based on XML.
   * @param string $xmlPath Path of the XML file. Relative path from `application/views/`.
   * @param array $params (optional) Embedded variables for subject and body text.
   * @return string This class name.
   */
  public static function messageFromXml(string $xmlPath, array $params=[]): string {
    $xml = new \SimpleXMLElement(self::template()->load($xmlPath, $params, 'xml'));
    self
      ::subject((string) $xml->subject)
      ::message(preg_replace('/^(\r\n|\n|\r)|(\r\n|\n|\r)$/', '', (string) $xml->message));
    return __CLASS__;
  }

  /**
   * Set mail type.
   * @param 'text'|'html' $type (optional) Mail type. Default is "text".
   * @return string This class name.
   */
  public static function setMailType($type='text'): string {
    call_user_func_array([self::email(), 'set_mailtype'], func_get_args());
    return __CLASS__;
  }

  /**
   * Assign file attachments.
   * @param string $file File name.
   * @param string $disposition (optional) "disposition" of the attachment.
   * @param string $newname (optional) Custom file name to use in the e-mail.
   * @param string $mime (optional) MIME type to use (useful for buffered data).
   * @return string This class name.
   */
  public static function attach($file, $disposition='', $newname=null, $mime='') {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set and return attachment Content-ID. Useful for attached inline pictures.
   * @param string $filename Existing attachment filename.
   * @return string Attachment Content-ID or FALSE if not found.
   */
  public static function attachmentCid($filename) {
    return call_user_func_array([self::email(), 'attachment_cid'], func_get_args());
  }

  /**
   * Get CI_Email instance.
   * @return CI_Email CI_Email instance.
   */
  private static function email() {
    static $instance;
    if (!isset($instance)) {
      $CI =& \get_instance();
      $CI->load->library('email', self::$defaultConfig);
      $instance = $CI->email;
    }
    return $instance;
  }

  /**
   * Get Template instance.
   * @return \X\Util\Template Template instance.
   */
  private static function template(): \X\Util\Template {
    static $template;
    return $template ?? new \X\Util\Template();
  }
}