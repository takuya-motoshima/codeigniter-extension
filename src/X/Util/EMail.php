<?php
namespace X\Util;

abstract class EMail {
  private static $default = [
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
   * Initialize preferences.
   */
  public static function initialize(array $config = array()): string {
    self::email()->initialize(array_merge(self::$default, $config));
    return __CLASS__;
  }

  /**
   * Send Email.
   */
  public static function send($auto_clear = TRUE) {
    return call_user_func_array([self::email(), __FUNCTION__], func_get_args());
  }

  /**
   * Set FROM.
   */
  public static function from($from, $name = '', $return_path = NULL): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set Recipients.
   */
  public static function to($to): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set BCC.
   */
  public static function bcc($bcc, $limit = ''): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set Email Subject.
   */
  public static function subject($subject): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set Body.
   */
  public static function message($body): string {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set Body.
   */
  public static function messageFromTemplate(string $templatePath, array $templateVars = []): string {
    self::message(self::template()->load($templatePath, $templateVars));
    return __CLASS__;
  }

  /**
   * Set Body.
   */
  public static function messageFromXml(string $xmlPath, array $xmlVars = []): string {
    $xml = new \SimpleXMLElement(self::template()->load($xmlPath, $xmlVars, 'xml'));
    self
      ::subject((string) $xml->subject)
      ::message(preg_replace('/^(\r\n|\n|\r)|(\r\n|\n|\r)$/', '', (string) $xml->message));
    return __CLASS__;
  }

  /**
   * Set mail type.
   */
  public static function setMailType($type = 'text'): string {
    call_user_func_array([self::email(), 'set_mailtype'], func_get_args());
    return __CLASS__;
  }

  /**
   * Assign file attachments.
   *
   * @param string  $file Can be local path, URL or buffered content
   * @param string  $disposition = 'attachment'
   * @param string  $newname = NULL
   * @param string  $mime = ''
   * @return  EMail
   */
  public static function attach($file, $disposition = '', $newname = NULL, $mime = '') {
    call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    return __CLASS__;
  }

  /**
   * Set and return attachment Content-ID.
   * Useful for attached inline pictures.
   */
  public static function attachmentCid($fileName) {
    return call_user_func_array([self::email(), 'attachment_cid'], func_get_args());
  }

  /**
   * Get CI_Email instance.
   */
  private static function email() {
    static $instance;
    if (!isset($$instance)) {
      $ci =& \get_instance();
      $ci->load->library('email', self::$default);
      $$instance = $ci->email;
    }
    return $$instance;
  }

  /**
   * Get Template instance.
   */
  private static function template(): \X\Util\Template {
    static $template;
    return $template ?? new \X\Util\Template();
  }

  // ----------------------------------------------------------------
  // Deprecated
  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  public static function message_from_template(string $templatePath, array $templateVars = []): string {
    return self::messageFromTemplate($templatePath, $templateVars);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  public static function message_from_xml(string $xmlPath, array $xmlVars = []): string {
    return self::messageFromXml($xmlPath, $xmlVars);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  public static function set_mailtype($type = 'text'): string {
    return self::mailType($type);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  public static function attachment_cid($fileName) {
    return self::attachmentCid($fileName);
  }
}