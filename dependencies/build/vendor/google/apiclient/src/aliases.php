<?php

namespace QuillSMTP\Vendor;

if (\class_exists('QuillSMTP\Vendor\Google_Client', \false)) {
    // Prevent error with preloading in PHP 7.4
    // @see https://github.com/googleapis/google-api-php-client/issues/1976
    return;
}
$classMap = ['QuillSMTP\Vendor\Google\Client' => 'Google_Client', 'QuillSMTP\Vendor\Google\Service' => 'Google_Service', 'QuillSMTP\Vendor\Google\AccessToken\Revoke' => 'Google_AccessToken_Revoke', 'QuillSMTP\Vendor\Google\AccessToken\Verify' => 'Google_AccessToken_Verify', 'QuillSMTP\Vendor\Google\Model' => 'Google_Model', 'QuillSMTP\Vendor\Google\Utils\UriTemplate' => 'Google_Utils_UriTemplate', 'QuillSMTP\Vendor\Google\AuthHandler\Guzzle6AuthHandler' => 'Google_AuthHandler_Guzzle6AuthHandler', 'QuillSMTP\Vendor\Google\AuthHandler\Guzzle7AuthHandler' => 'Google_AuthHandler_Guzzle7AuthHandler', 'QuillSMTP\Vendor\Google\AuthHandler\AuthHandlerFactory' => 'Google_AuthHandler_AuthHandlerFactory', 'QuillSMTP\Vendor\Google\Http\Batch' => 'Google_Http_Batch', 'QuillSMTP\Vendor\Google\Http\MediaFileUpload' => 'Google_Http_MediaFileUpload', 'QuillSMTP\Vendor\Google\Http\REST' => 'Google_Http_REST', 'QuillSMTP\Vendor\Google\Task\Retryable' => 'Google_Task_Retryable', 'QuillSMTP\Vendor\Google\Task\Exception' => 'Google_Task_Exception', 'QuillSMTP\Vendor\Google\Task\Runner' => 'Google_Task_Runner', 'QuillSMTP\Vendor\Google\Collection' => 'Google_Collection', 'QuillSMTP\Vendor\Google\Service\Exception' => 'Google_Service_Exception', 'QuillSMTP\Vendor\Google\Service\Resource' => 'Google_Service_Resource', 'QuillSMTP\Vendor\Google\Exception' => 'Google_Exception'];
foreach ($classMap as $class => $alias) {
    \class_alias($class, 'QuillSMTP\\Vendor\\' . $alias);
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
class Google_Task_Composer extends \QuillSMTP\Vendor\Google\Task\Composer
{
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
\class_alias('QuillSMTP\Vendor\Google_Task_Composer', 'Google_Task_Composer', \false);
/** @phpstan-ignore-next-line */
if (\false) {
    class Google_AccessToken_Revoke extends \QuillSMTP\Vendor\Google\AccessToken\Revoke
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_AccessToken_Revoke', 'Google_AccessToken_Revoke', \false);
    class Google_AccessToken_Verify extends \QuillSMTP\Vendor\Google\AccessToken\Verify
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_AccessToken_Verify', 'Google_AccessToken_Verify', \false);
    class Google_AuthHandler_AuthHandlerFactory extends \QuillSMTP\Vendor\Google\AuthHandler\AuthHandlerFactory
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_AuthHandler_AuthHandlerFactory', 'Google_AuthHandler_AuthHandlerFactory', \false);
    class Google_AuthHandler_Guzzle6AuthHandler extends \QuillSMTP\Vendor\Google\AuthHandler\Guzzle6AuthHandler
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_AuthHandler_Guzzle6AuthHandler', 'Google_AuthHandler_Guzzle6AuthHandler', \false);
    class Google_AuthHandler_Guzzle7AuthHandler extends \QuillSMTP\Vendor\Google\AuthHandler\Guzzle7AuthHandler
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_AuthHandler_Guzzle7AuthHandler', 'Google_AuthHandler_Guzzle7AuthHandler', \false);
    class Google_Client extends \QuillSMTP\Vendor\Google\Client
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Client', 'Google_Client', \false);
    class Google_Collection extends \QuillSMTP\Vendor\Google\Collection
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Collection', 'Google_Collection', \false);
    class Google_Exception extends \QuillSMTP\Vendor\Google\Exception
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Exception', 'Google_Exception', \false);
    class Google_Http_Batch extends \QuillSMTP\Vendor\Google\Http\Batch
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Http_Batch', 'Google_Http_Batch', \false);
    class Google_Http_MediaFileUpload extends \QuillSMTP\Vendor\Google\Http\MediaFileUpload
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Http_MediaFileUpload', 'Google_Http_MediaFileUpload', \false);
    class Google_Http_REST extends \QuillSMTP\Vendor\Google\Http\REST
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Http_REST', 'Google_Http_REST', \false);
    class Google_Model extends \QuillSMTP\Vendor\Google\Model
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Model', 'Google_Model', \false);
    class Google_Service extends \QuillSMTP\Vendor\Google\Service
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Service', 'Google_Service', \false);
    class Google_Service_Exception extends \QuillSMTP\Vendor\Google\Service\Exception
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Service_Exception', 'Google_Service_Exception', \false);
    class Google_Service_Resource extends \QuillSMTP\Vendor\Google\Service\Resource
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Service_Resource', 'Google_Service_Resource', \false);
    class Google_Task_Exception extends \QuillSMTP\Vendor\Google\Task\Exception
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Task_Exception', 'Google_Task_Exception', \false);
    interface Google_Task_Retryable extends \QuillSMTP\Vendor\Google\Task\Retryable
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Task_Retryable', 'Google_Task_Retryable', \false);
    class Google_Task_Runner extends \QuillSMTP\Vendor\Google\Task\Runner
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Task_Runner', 'Google_Task_Runner', \false);
    class Google_Utils_UriTemplate extends \QuillSMTP\Vendor\Google\Utils\UriTemplate
    {
    }
    \class_alias('QuillSMTP\Vendor\Google_Utils_UriTemplate', 'Google_Utils_UriTemplate', \false);
}
