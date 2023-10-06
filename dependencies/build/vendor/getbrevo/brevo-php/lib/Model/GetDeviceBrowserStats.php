<?php

/**
 * GetDeviceBrowserStats
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
 * GetDeviceBrowserStats Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class GetDeviceBrowserStats implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'getDeviceBrowserStats';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['clickers' => 'int', 'uniqueClicks' => 'int', 'viewed' => 'int', 'uniqueViews' => 'int'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['clickers' => 'int64', 'uniqueClicks' => 'int64', 'viewed' => 'int64', 'uniqueViews' => 'int64'];
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
    protected static $attributeMap = ['clickers' => 'clickers', 'uniqueClicks' => 'uniqueClicks', 'viewed' => 'viewed', 'uniqueViews' => 'uniqueViews'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['clickers' => 'setClickers', 'uniqueClicks' => 'setUniqueClicks', 'viewed' => 'setViewed', 'uniqueViews' => 'setUniqueViews'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['clickers' => 'getClickers', 'uniqueClicks' => 'getUniqueClicks', 'viewed' => 'getViewed', 'uniqueViews' => 'getUniqueViews'];
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
        $this->container['clickers'] = isset($data['clickers']) ? $data['clickers'] : null;
        $this->container['uniqueClicks'] = isset($data['uniqueClicks']) ? $data['uniqueClicks'] : null;
        $this->container['viewed'] = isset($data['viewed']) ? $data['viewed'] : null;
        $this->container['uniqueViews'] = isset($data['uniqueViews']) ? $data['uniqueViews'] : null;
    }
    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];
        if ($this->container['clickers'] === null) {
            $invalidProperties[] = "'clickers' can't be null";
        }
        if ($this->container['uniqueClicks'] === null) {
            $invalidProperties[] = "'uniqueClicks' can't be null";
        }
        if ($this->container['viewed'] === null) {
            $invalidProperties[] = "'viewed' can't be null";
        }
        if ($this->container['uniqueViews'] === null) {
            $invalidProperties[] = "'uniqueViews' can't be null";
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
     * Gets clickers
     *
     * @return int
     */
    public function getClickers()
    {
        return $this->container['clickers'];
    }
    /**
     * Sets clickers
     *
     * @param int $clickers Number of total clicks for the campaign using the particular browser
     *
     * @return $this
     */
    public function setClickers($clickers)
    {
        $this->container['clickers'] = $clickers;
        return $this;
    }
    /**
     * Gets uniqueClicks
     *
     * @return int
     */
    public function getUniqueClicks()
    {
        return $this->container['uniqueClicks'];
    }
    /**
     * Sets uniqueClicks
     *
     * @param int $uniqueClicks Number of unique clicks for the campaign using the particular browser
     *
     * @return $this
     */
    public function setUniqueClicks($uniqueClicks)
    {
        $this->container['uniqueClicks'] = $uniqueClicks;
        return $this;
    }
    /**
     * Gets viewed
     *
     * @return int
     */
    public function getViewed()
    {
        return $this->container['viewed'];
    }
    /**
     * Sets viewed
     *
     * @param int $viewed Number of openings for the campaign using the particular browser
     *
     * @return $this
     */
    public function setViewed($viewed)
    {
        $this->container['viewed'] = $viewed;
        return $this;
    }
    /**
     * Gets uniqueViews
     *
     * @return int
     */
    public function getUniqueViews()
    {
        return $this->container['uniqueViews'];
    }
    /**
     * Sets uniqueViews
     *
     * @param int $uniqueViews Number of unique openings for the campaign using the particular browser
     *
     * @return $this
     */
    public function setUniqueViews($uniqueViews)
    {
        $this->container['uniqueViews'] = $uniqueViews;
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
