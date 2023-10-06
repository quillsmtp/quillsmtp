<?php

/**
 * ErrorModel
 *
 * PHP version 5
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
/**
 * Brevo API
 *
 * Brevo provide a RESTFul API that can be used with any languages. With this API, you will be able to :   - Manage your campaigns and get the statistics   - Manage your contacts   - Send transactional Emails and SMS   - and much more...  You can download our wrappers at https://github.com/orgs/brevo  **Possible responses**   | Code | Message |   | :-------------: | ------------- |   | 200  | OK. Successful Request  |   | 201  | OK. Successful Creation |   | 202  | OK. Request accepted |   | 204  | OK. Successful Update/Deletion  |   | 400  | Error. Bad Request  |   | 401  | Error. Authentication Needed  |   | 402  | Error. Not enough credit, plan upgrade needed  |   | 403  | Error. Permission denied  |   | 404  | Error. Object does not exist |   | 405  | Error. Method not allowed  |   | 406  | Error. Not Acceptable  |
 *
 * OpenAPI spec version: 3.0.0
 * Contact: contact@brevo.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.29
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */
namespace QuillSMTP\Vendor\Brevo\Client\Model;

use ArrayAccess;
use QuillSMTP\Vendor\Brevo\Client\ObjectSerializer;
/**
 * ErrorModel Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class ErrorModel implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'errorModel';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['code' => 'string', 'message' => 'string'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['code' => null, 'message' => null];
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }
    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = ['code' => 'code', 'message' => 'message'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['code' => 'setCode', 'message' => 'setMessage'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['code' => 'getCode', 'message' => 'getMessage'];
    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }
    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }
    const CODE_INVALID_PARAMETER = 'invalid_parameter';
    const CODE_MISSING_PARAMETER = 'missing_parameter';
    const CODE_OUT_OF_RANGE = 'out_of_range';
    const CODE_CAMPAIGN_PROCESSING = 'campaign_processing';
    const CODE_CAMPAIGN_SENT = 'campaign_sent';
    const CODE_DOCUMENT_NOT_FOUND = 'document_not_found';
    const CODE_RESELLER_PERMISSION_DENIED = 'reseller_permission_denied';
    const CODE_NOT_ENOUGH_CREDITS = 'not_enough_credits';
    const CODE_PERMISSION_DENIED = 'permission_denied';
    const CODE_DUPLICATE_PARAMETER = 'duplicate_parameter';
    const CODE_DUPLICATE_REQUEST = 'duplicate_request';
    const CODE_METHOD_NOT_ALLOWED = 'method_not_allowed';
    const CODE_UNAUTHORIZED = 'unauthorized';
    const CODE_ACCOUNT_UNDER_VALIDATION = 'account_under_validation';
    const CODE_NOT_ACCEPTABLE = 'not_acceptable';
    const CODE_BAD_REQUEST = 'bad_request';
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getCodeAllowableValues()
    {
        return [self::CODE_INVALID_PARAMETER, self::CODE_MISSING_PARAMETER, self::CODE_OUT_OF_RANGE, self::CODE_CAMPAIGN_PROCESSING, self::CODE_CAMPAIGN_SENT, self::CODE_DOCUMENT_NOT_FOUND, self::CODE_RESELLER_PERMISSION_DENIED, self::CODE_NOT_ENOUGH_CREDITS, self::CODE_PERMISSION_DENIED, self::CODE_DUPLICATE_PARAMETER, self::CODE_DUPLICATE_REQUEST, self::CODE_METHOD_NOT_ALLOWED, self::CODE_UNAUTHORIZED, self::CODE_ACCOUNT_UNDER_VALIDATION, self::CODE_NOT_ACCEPTABLE, self::CODE_BAD_REQUEST];
    }
    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['code'] = isset($data['code']) ? $data['code'] : null;
        $this->container['message'] = isset($data['message']) ? $data['message'] : null;
    }
    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];
        if ($this->container['code'] === null) {
            $invalidProperties[] = "'code' can't be null";
        }
        $allowedValues = $this->getCodeAllowableValues();
        if (!\is_null($this->container['code']) && !\in_array($this->container['code'], $allowedValues, \true)) {
            $invalidProperties[] = \sprintf("invalid value for 'code', must be one of '%s'", \implode("', '", $allowedValues));
        }
        if ($this->container['message'] === null) {
            $invalidProperties[] = "'message' can't be null";
        }
        return $invalidProperties;
    }
    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return \count($this->listInvalidProperties()) === 0;
    }
    /**
     * Gets code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->container['code'];
    }
    /**
     * Sets code
     *
     * @param string $code Error code displayed in case of a failure
     *
     * @return $this
     */
    public function setCode($code)
    {
        $allowedValues = $this->getCodeAllowableValues();
        if (!\in_array($code, $allowedValues, \true)) {
            throw new \InvalidArgumentException(\sprintf("Invalid value for 'code', must be one of '%s'", \implode("', '", $allowedValues)));
        }
        $this->container['code'] = $code;
        return $this;
    }
    /**
     * Gets message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->container['message'];
    }
    /**
     * Sets message
     *
     * @param string $message Readable message associated to the failure
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->container['message'] = $message;
        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }
    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (\is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }
    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (\defined('JSON_PRETTY_PRINT')) {
            // use JSON pretty print
            return \json_encode(ObjectSerializer::sanitizeForSerialization($this), \JSON_PRETTY_PRINT);
        }
        return \json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}
