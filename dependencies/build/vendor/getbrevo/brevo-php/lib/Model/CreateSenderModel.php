<?php

/**
 * CreateSenderModel
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
namespace QuillSMTP\Brevo\Client\Model;

use ArrayAccess;
use QuillSMTP\Brevo\Client\ObjectSerializer;
/**
 * CreateSenderModel Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreateSenderModel implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'createSenderModel';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['id' => 'int', 'spfError' => 'bool', 'dkimError' => 'bool'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['id' => 'int64', 'spfError' => null, 'dkimError' => null];
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
    protected static $attributeMap = ['id' => 'id', 'spfError' => 'spfError', 'dkimError' => 'dkimError'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['id' => 'setId', 'spfError' => 'setSpfError', 'dkimError' => 'setDkimError'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['id' => 'getId', 'spfError' => 'getSpfError', 'dkimError' => 'getDkimError'];
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
        $this->container['spfError'] = isset($data['spfError']) ? $data['spfError'] : null;
        $this->container['dkimError'] = isset($data['dkimError']) ? $data['dkimError'] : null;
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
     * @return int
     */
    public function getId()
    {
        return $this->container['id'];
    }
    /**
     * Sets id
     *
     * @param int $id ID of the Sender created
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->container['id'] = $id;
        return $this;
    }
    /**
     * Gets spfError
     *
     * @return bool
     */
    public function getSpfError()
    {
        return $this->container['spfError'];
    }
    /**
     * Sets spfError
     *
     * @param bool $spfError Status of SPF configuration for the sender (true = SPF not well configured, false = SPF well configured)
     *
     * @return $this
     */
    public function setSpfError($spfError)
    {
        $this->container['spfError'] = $spfError;
        return $this;
    }
    /**
     * Gets dkimError
     *
     * @return bool
     */
    public function getDkimError()
    {
        return $this->container['dkimError'];
    }
    /**
     * Sets dkimError
     *
     * @param bool $dkimError Status of DKIM configuration for the sender (true = DKIM not well configured, false = DKIM well configured)
     *
     * @return $this
     */
    public function setDkimError($dkimError)
    {
        $this->container['dkimError'] = $dkimError;
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
