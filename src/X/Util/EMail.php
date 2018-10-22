<?php
/**
 * Email util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
abstract class EMail
{

    /**
     * Send Email
     *
     * @param   bool    $auto_clear = TRUE
     * @return  bool
     */
    public static function send($auto_clear = TRUE):bool
    {
        return call_user_func_array([self::email(), __FUNCTION__], func_get_args());
    }

    /**
     * Set FROM
     *
     * @param   string  $from
     * @param   string  $name
     * @param   string  $return_path = NULL Return-Path
     * @return  string
     */
    public static function from($from, $name = '', $return_path = NULL): string
    {
        call_user_func_array([self::email(), __FUNCTION__], func_get_args());
        return __CLASS__;
    }

    /**
     * Set Recipients
     *
     * @param   string
     * @return  string
     */
    public static function to($to): string
    {
        call_user_func_array([self::email(), __FUNCTION__], func_get_args());
        return __CLASS__;
    }

    /**
     * Set BCC
     *
     * @param   string
     * @param   string
     * @return  string
     */
    public static function bcc($bcc, $limit = ''): string
    {
        call_user_func_array([self::email(), __FUNCTION__], func_get_args());
        return __CLASS__;
    }

    /**
     * Set Email Subject
     *
     * @param   string
     * @return  string
     */
    public static function subject($subject): string
    {
        call_user_func_array([self::email(), __FUNCTION__], func_get_args());
        return __CLASS__;
    }

    /**
     * Set Body
     *
     * @param   string
     * @return  string
     */
    public static function message($body): string
    {
        call_user_func_array([self::email(), __FUNCTION__], func_get_args());
        return __CLASS__;
    }

    /**
     * Set Body
     *
     * @param   string
     * @param   array
     * @return  string
     */
    public static function message_from_template(string $path, array $vars = []): string
    {
        self::message(self::template()->load($path, $vars));
        return __CLASS__;
    }

    /**
     * Set Body
     *
     * @param   string
     * @param   array
     * @return  string
     */
    public static function message_from_xml(string $path, array $vars = []): string
    {
        $xml = new \SimpleXMLElement(self::template()->load($path, $vars, 'xml'));
        self
            ::subject((string) $xml->subject)
            ::message(preg_replace('/^(\r\n|\n|\r)|(\r\n|\n|\r)$/', '', (string) $xml->message));
        return __CLASS__;
    }

    /**
     * Set Mailtype
     *
     * @param   string
     * @return  string
     */
    public static function set_mailtype($type = 'text'): string
    {
        call_user_func_array([self::email(), __FUNCTION__], func_get_args());
        return __CLASS__;
    }

    /**
     * Get CI_Email instance
     *
     * @return \CI_Email
     */
    private static function email(): \CI_Email
    {
        static $email;
        if (!isset($email)) {
            $ci =& \get_instance();
            $ci->load->library('email',[
                'wordwrap' => false,
                // 'mailtype'=> $type,
                'charset' => 'utf-8',
                'crlf' => "\r\n",
                'newline' => "\r\n",
                'priority' => 1,
            ]);
            $email = $ci->email;
        }
        return $email;
    }

    /**
     * Get Template instance
     *
     * @return \X\Util\Template
     */
    private static function template(): \X\Util\Template
    {
        static $template;
        return $template ?? new \X\Util\Template();
    }
}