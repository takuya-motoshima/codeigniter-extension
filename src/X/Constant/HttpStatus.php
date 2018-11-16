<?php
/**
 * Http status constant
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Constant;

// [Informational 1xx]
/**
 * @var int HTTP_CONTINUE HTTP continue
 */
const HTTP_CONTINUE = 100;

/**
 * @var int HTTP_SWITCHING_PROTOCOLS HTTP switching protocols
 */
const HTTP_SWITCHING_PROTOCOLS = 101;

// [Successful 2xx]
/**
 * @var int HTTP_OK HTTP ok
 */
const HTTP_OK = 200;

/**
 * @var int HTTP_CREATED HTTP created
 */
const HTTP_CREATED = 201;

/**
 * @var int HTTP_ACCEPTED HTTP accepted
 */
const HTTP_ACCEPTED = 202;

/**
 * @var int HTTP_NONAUTHORITATIVE_INFORMATION HTTP nonauthoritative information
 */
const HTTP_NONAUTHORITATIVE_INFORMATION = 203;

/**
 * @var int HTTP_NO_CONTENT HTTP no content
 */
const HTTP_NO_CONTENT = 204;

/**
 * @var int HTTP_RESET_CONTENT HTTP reset content
 */
const HTTP_RESET_CONTENT = 205;

/**
 * @var int HTTP_PARTIAL_CONTENT HTTP partial content
 */
const HTTP_PARTIAL_CONTENT = 206;

// [Redirection 3xx]

/**
 * @var int HTTP_MULTIPLE_CHOICES HTTP multiple choices
 */
const HTTP_MULTIPLE_CHOICES = 300;

/**
 * @var int HTTP_MOVED_PERMANENTLY HTTP moved permanently
 */
const HTTP_MOVED_PERMANENTLY = 301;

/**
 * @var int HTTP_FOUND HTTP found
 */
const HTTP_FOUND = 302;

/**
 * @var int HTTP_SEE_OTHER HTTP see other
 */
const HTTP_SEE_OTHER = 303;

/**
 * @var int HTTP_NOT_MODIFIED HTTP not modified
 */
const HTTP_NOT_MODIFIED = 304;

/**
 * @var int HTTP_USE_PROXY HTTP use proxy
 */
const HTTP_USE_PROXY = 305;

/**
 * @var int HTTP_UNUSED HTTP unused
 */
const HTTP_UNUSED= 306;

/**
 * @var int HTTP_TEMPORARY_REDIRECT HTTP temporary redirect
 */
const HTTP_TEMPORARY_REDIRECT = 307;

// [Client Error 4xx]
/**
 * @var int HTTP_BAD_REQUEST HTTP bad request
 */
const HTTP_BAD_REQUEST = 400;

/**
 * @var int HTTP_UNAUTHORIZED HTTP unauthorized
 */
const HTTP_UNAUTHORIZED  = 401;

/**
 * @var int HTTP_PAYMENT_REQUIRED HTTP payment required
 */
const HTTP_PAYMENT_REQUIRED = 402;

/**
 * @var int HTTP_FORBIDDEN HTTP forbidden
 */
const HTTP_FORBIDDEN = 403;

/**
 * @var int HTTP_NOT_FOUND HTTP not found
 */
const HTTP_NOT_FOUND = 404;

/**
 * @var int HTTP_METHOD_NOT_ALLOWED HTTP method not allowed
 */
const HTTP_METHOD_NOT_ALLOWED = 405;

/**
 * @var int HTTP_NOT_ACCEPTABLE HTTP not acceptable
 */
const HTTP_NOT_ACCEPTABLE = 406;

/**
 * @var int HTTP_PROXY_AUTHENTICATION_REQUIRED HTTP proxy authentication required
 */
const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;

/**
 * @var int HTTP_REQUEST_TIMEOUT HTTP request timeout
 */
const HTTP_REQUEST_TIMEOUT = 408;

/**
 * @var int HTTP_CONFLICT HTTP conflict
 */
const HTTP_CONFLICT = 409;

/**
 * @var int HTTP_GONE HTTP gone
 */
const HTTP_GONE = 410;

/**
 * @var int HTTP_LENGTH_REQUIRED HTTP length required
 */
const HTTP_LENGTH_REQUIRED = 411;

/**
 * @var int HTTP_PRECONDITION_FAILED HTTP precondition failed
 */
const HTTP_PRECONDITION_FAILED = 412;

/**
 * @var int HTTP_REQUEST_ENTITY_TOO_LARGE HTTP request entity too large
 */
const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;

/**
 * @var int HTTP_REQUEST_URI_TOO_LONG HTTP request uri too long
 */
const HTTP_REQUEST_URI_TOO_LONG = 414;

/**
 * @var int HTTP_UNSUPPORTED_MEDIA_TYPE HTTP unsupported media type
 */
const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;

/**
 * @var int HTTP_REQUESTED_RANGE_NOT_SATISFIABLE HTTP requested range not satisfiable
 */
const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

/**
 * @var int HTTP_EXPECTATION_FAILED HTTP expectation failed
 */
const HTTP_EXPECTATION_FAILED = 417;

// [Server Error 5xx]

/**
 * @var int HTTP_INTERNAL_SERVER_ERROR HTTP internal server error
 */
const HTTP_INTERNAL_SERVER_ERROR = 500;

/**
 * @var int HTTP_NOT_IMPLEMENTED HTTP not implemented
 */
const HTTP_NOT_IMPLEMENTED = 501;

/**
 * @var int HTTP_BAD_GATEWAY HTTP bad gateway
 */
const HTTP_BAD_GATEWAY = 502;

/**
 * @var int HTTP_SERVICE_UNAVAILABLE HTTP service unavailable
 */
const HTTP_SERVICE_UNAVAILABLE = 503;

/**
 * @var int HTTP_GATEWAY_TIMEOUT HTTP gateway timeout
 */
const HTTP_GATEWAY_TIMEOUT = 504;

/**
 * @var int HTTP_VERSION_NOT_SUPPORTED HTTP version not supported
 */
const HTTP_VERSION_NOT_SUPPORTED = 505;
