<?php

/**
 * GetExternalFeedByUUID
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
 * GetExternalFeedByUUID Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class GetExternalFeedByUUID implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'getExternalFeedByUUID';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['id' => 'string', 'name' => 'string', 'url' => 'string', 'authType' => 'string', 'username' => 'string', 'password' => 'string', 'token' => 'string', 'headers' => '\\QuillSMTP\\Vendor\\Brevo\\Client\\Model\\GetExternalFeedByUUIDHeaders[]', 'maxRetries' => 'int', 'cache' => 'bool', 'createdAt' => '\\DateTime', 'modifiedAt' => '\\DateTime'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['id' => 'uuidv4', 'name' => null, 'url' => 'url', 'authType' => null, 'username' => null, 'password' => null, 'token' => null, 'headers' => null, 'maxRetries' => null, 'cache' => null, 'createdAt' => 'date-time', 'modifiedAt' => 'date-time'];
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
    protected static $attributeMap = ['id' => 'id', 'name' => 'name', 'url' => 'url', 'authType' => 'authType', 'username' => 'username', 'password' => 'password', 'token' => 'token', 'headers' => 'headers', 'maxRetries' => 'maxRetries', 'cache' => 'cache', 'createdAt' => 'createdAt', 'modifiedAt' => 'modifiedAt'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['id' => 'setId', 'name' => 'setName', 'url' => 'setUrl', 'authType' => 'setAuthType', 'username' => 'setUsername', 'password' => 'setPassword', 'token' => 'setToken', 'headers' => 'setHeaders', 'maxRetries' => 'setMaxRetries', 'cache' => 'setCache', 'createdAt' => 'setCreatedAt', 'modifiedAt' => 'setModifiedAt'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['id' => 'getId', 'name' => 'getName', 'url' => 'getUrl', 'authType' => 'getAuthType', 'username' => 'getUsername', 'password' => 'getPassword', 'token' => 'getToken', 'headers' => 'getHeaders', 'maxRetries' => 'getMaxRetries', 'cache' => 'getCache', 'createdAt' => 'getCreatedAt', 'modifiedAt' => 'getModifiedAt'];
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
    const AUTH_TYPE_BASIC = 'basic';
    const AUTH_TYPE_TOKEN = 'token';
    const AUTH_TYPE_NO_AUTH = 'noAuth';
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getAuthTypeAllowableValues()
    {
        return [self::AUTH_TYPE_BASIC, self::AUTH_TYPE_TOKEN, self::AUTH_TYPE_NO_AUTH];
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
        $this->container['id'] = isset($data['id']) ? $data['id'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['url'] = isset($data['url']) ? $data['url'] : null;
        $this->container['authType'] = isset($data['authType']) ? $data['authType'] : null;
        $this->container['username'] = isset($data['username']) ? $data['username'] : null;
        $this->container['password'] = isset($data['password']) ? $data['password'] : null;
        $this->container['token'] = isset($data['token']) ? $data['token'] : null;
        $this->container['headers'] = isset($data['headers']) ? $data['headers'] : null;
        $this->container['maxRetries'] = isset($data['maxRetries']) ? $data['maxRetries'] : null;
        $this->container['cache'] = isset($data['cache']) ? $data['cache'] : null;
        $this->container['createdAt'] = isset($data['createdAt']) ? $data['createdAt'] : null;
        $this->container['modifiedAt'] = isset($data['modifiedAt']) ? $data['modifiedAt'] : null;
    }
    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        if ($this->container['url'] === null) {
            $invalidProperties[] = "'url' can't be null";
        }
        if ($this->container['authType'] === null) {
            $invalidProperties[] = "'authType' can't be null";
        }
        $allowedValues = $this->getAuthTypeAllowableValues();
        if (!\is_null($this->container['authType']) && !\in_array($this->container['authType'], $allowedValues, \true)) {
            $invalidProperties[] = \sprintf("invalid value for 'authType', must be one of '%s'", \implode("', '", $allowedValues));
        }
        if ($this->container['headers'] === null) {
            $invalidProperties[] = "'headers' can't be null";
        }
        if ($this->container['maxRetries'] === null) {
            $invalidProperties[] = "'maxRetries' can't be null";
        }
        if ($this->container['maxRetries'] > 5) {
            $invalidProperties[] = "invalid value for 'maxRetries', must be smaller than or equal to 5.";
        }
        if ($this->container['maxRetries'] < 0) {
            $invalidProperties[] = "invalid value for 'maxRetries', must be bigger than or equal to 0.";
        }
        if ($this->container['cache'] === null) {
            $invalidProperties[] = "'cache' can't be null";
        }
        if ($this->container['createdAt'] === null) {
            $invalidProperties[] = "'createdAt' can't be null";
        }
        if ($this->container['modifiedAt'] === null) {
            $invalidProperties[] = "'modifiedAt' can't be null";
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
     * Gets id
     *
     * @return string
     */
    public function getId()
    {
        return $this->container['id'];
    }
    /**
     * Sets id
     *
     * @param string $id ID of the feed
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->container['id'] = $id;
        return $this;
    }
    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }
    /**
     * Sets name
     *
     * @param string $name Name of the feed
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;
        return $this;
    }
    /**
     * Gets url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->container['url'];
    }
    /**
     * Sets url
     *
     * @param string $url URL of the feed
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->container['url'] = $url;
        return $this;
    }
    /**
     * Gets authType
     *
     * @return string
     */
    public function getAuthType()
    {
        return $this->container['authType'];
    }
    /**
     * Sets authType
     *
     * @param string $authType Auth type of the feed: * `basic` * `token` * `noAuth`
     *
     * @return $this
     */
    public function setAuthType($authType)
    {
        $allowedValues = $this->getAuthTypeAllowableValues();
        if (!\in_array($authType, $allowedValues, \true)) {
            throw new \InvalidArgumentException(\sprintf("Invalid value for 'authType', must be one of '%s'", \implode("', '", $allowedValues)));
        }
        $this->container['authType'] = $authType;
        return $this;
    }
    /**
     * Gets username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->container['username'];
    }
    /**
     * Sets username
     *
     * @param string $username Username for authType `basic`
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->container['username'] = $username;
        return $this;
    }
    /**
     * Gets password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->container['password'];
    }
    /**
     * Sets password
     *
     * @param string $password Password for authType `basic`
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->container['password'] = $password;
        return $this;
    }
    /**
     * Gets token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->container['token'];
    }
    /**
     * Sets token
     *
     * @param string $token Token for authType `token`
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->container['token'] = $token;
        return $this;
    }
    /**
     * Gets headers
     *
     * @return \Brevo\Client\Model\GetExternalFeedByUUIDHeaders[]
     */
    public function getHeaders()
    {
        return $this->container['headers'];
    }
    /**
     * Sets headers
     *
     * @param \Brevo\Client\Model\GetExternalFeedByUUIDHeaders[] $headers Custom headers for the feed
     *
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->container['headers'] = $headers;
        return $this;
    }
    /**
     * Gets maxRetries
     *
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->container['maxRetries'];
    }
    /**
     * Sets maxRetries
     *
     * @param int $maxRetries Maximum number of retries on the feed url
     *
     * @return $this
     */
    public function setMaxRetries($maxRetries)
    {
        if ($maxRetries > 5) {
            throw new \InvalidArgumentException('invalid value for $maxRetries when calling GetExternalFeedByUUID., must be smaller than or equal to 5.');
        }
        if ($maxRetries < 0) {
            throw new \InvalidArgumentException('invalid value for $maxRetries when calling GetExternalFeedByUUID., must be bigger than or equal to 0.');
        }
        $this->container['maxRetries'] = $maxRetries;
        return $this;
    }
    /**
     * Gets cache
     *
     * @return bool
     */
    public function getCache()
    {
        return $this->container['cache'];
    }
    /**
     * Sets cache
     *
     * @param bool $cache Toggle caching of feed url response
     *
     * @return $this
     */
    public function setCache($cache)
    {
        $this->container['cache'] = $cache;
        return $this;
    }
    /**
     * Gets createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->container['createdAt'];
    }
    /**
     * Sets createdAt
     *
     * @param \DateTime $createdAt Datetime on which the feed was created
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->container['createdAt'] = $createdAt;
        return $this;
    }
    /**
     * Gets modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->container['modifiedAt'];
    }
    /**
     * Sets modifiedAt
     *
     * @param \DateTime $modifiedAt Datetime on which the feed was modified
     *
     * @return $this
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->container['modifiedAt'] = $modifiedAt;
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
