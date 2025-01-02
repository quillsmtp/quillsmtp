<?php

/**
 * GetAggregatedReport
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
 * GetAggregatedReport Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class GetAggregatedReport implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'getAggregatedReport';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['range' => 'string', 'requests' => 'int', 'delivered' => 'int', 'hardBounces' => 'int', 'softBounces' => 'int', 'clicks' => 'int', 'uniqueClicks' => 'int', 'opens' => 'int', 'uniqueOpens' => 'int', 'spamReports' => 'int', 'blocked' => 'int', 'invalid' => 'int', 'unsubscribed' => 'int'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['range' => null, 'requests' => 'int64', 'delivered' => 'int64', 'hardBounces' => 'int64', 'softBounces' => 'int64', 'clicks' => 'int64', 'uniqueClicks' => 'int64', 'opens' => 'int64', 'uniqueOpens' => 'int64', 'spamReports' => 'int64', 'blocked' => 'int64', 'invalid' => 'int64', 'unsubscribed' => 'int64'];
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
    protected static $attributeMap = ['range' => 'range', 'requests' => 'requests', 'delivered' => 'delivered', 'hardBounces' => 'hardBounces', 'softBounces' => 'softBounces', 'clicks' => 'clicks', 'uniqueClicks' => 'uniqueClicks', 'opens' => 'opens', 'uniqueOpens' => 'uniqueOpens', 'spamReports' => 'spamReports', 'blocked' => 'blocked', 'invalid' => 'invalid', 'unsubscribed' => 'unsubscribed'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['range' => 'setRange', 'requests' => 'setRequests', 'delivered' => 'setDelivered', 'hardBounces' => 'setHardBounces', 'softBounces' => 'setSoftBounces', 'clicks' => 'setClicks', 'uniqueClicks' => 'setUniqueClicks', 'opens' => 'setOpens', 'uniqueOpens' => 'setUniqueOpens', 'spamReports' => 'setSpamReports', 'blocked' => 'setBlocked', 'invalid' => 'setInvalid', 'unsubscribed' => 'setUnsubscribed'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['range' => 'getRange', 'requests' => 'getRequests', 'delivered' => 'getDelivered', 'hardBounces' => 'getHardBounces', 'softBounces' => 'getSoftBounces', 'clicks' => 'getClicks', 'uniqueClicks' => 'getUniqueClicks', 'opens' => 'getOpens', 'uniqueOpens' => 'getUniqueOpens', 'spamReports' => 'getSpamReports', 'blocked' => 'getBlocked', 'invalid' => 'getInvalid', 'unsubscribed' => 'getUnsubscribed'];
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
        $this->container['range'] = isset($data['range']) ? $data['range'] : null;
        $this->container['requests'] = isset($data['requests']) ? $data['requests'] : null;
        $this->container['delivered'] = isset($data['delivered']) ? $data['delivered'] : null;
        $this->container['hardBounces'] = isset($data['hardBounces']) ? $data['hardBounces'] : null;
        $this->container['softBounces'] = isset($data['softBounces']) ? $data['softBounces'] : null;
        $this->container['clicks'] = isset($data['clicks']) ? $data['clicks'] : null;
        $this->container['uniqueClicks'] = isset($data['uniqueClicks']) ? $data['uniqueClicks'] : null;
        $this->container['opens'] = isset($data['opens']) ? $data['opens'] : null;
        $this->container['uniqueOpens'] = isset($data['uniqueOpens']) ? $data['uniqueOpens'] : null;
        $this->container['spamReports'] = isset($data['spamReports']) ? $data['spamReports'] : null;
        $this->container['blocked'] = isset($data['blocked']) ? $data['blocked'] : null;
        $this->container['invalid'] = isset($data['invalid']) ? $data['invalid'] : null;
        $this->container['unsubscribed'] = isset($data['unsubscribed']) ? $data['unsubscribed'] : null;
    }
    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];
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
     * Gets range
     *
     * @return string
     */
    public function getRange()
    {
        return $this->container['range'];
    }
    /**
     * Sets range
     *
     * @param string $range Time frame of the report
     *
     * @return $this
     */
    public function setRange($range)
    {
        $this->container['range'] = $range;
        return $this;
    }
    /**
     * Gets requests
     *
     * @return int
     */
    public function getRequests()
    {
        return $this->container['requests'];
    }
    /**
     * Sets requests
     *
     * @param int $requests Number of requests for the timeframe
     *
     * @return $this
     */
    public function setRequests($requests)
    {
        $this->container['requests'] = $requests;
        return $this;
    }
    /**
     * Gets delivered
     *
     * @return int
     */
    public function getDelivered()
    {
        return $this->container['delivered'];
    }
    /**
     * Sets delivered
     *
     * @param int $delivered Number of delivered emails for the timeframe
     *
     * @return $this
     */
    public function setDelivered($delivered)
    {
        $this->container['delivered'] = $delivered;
        return $this;
    }
    /**
     * Gets hardBounces
     *
     * @return int
     */
    public function getHardBounces()
    {
        return $this->container['hardBounces'];
    }
    /**
     * Sets hardBounces
     *
     * @param int $hardBounces Number of hardbounces for the timeframe
     *
     * @return $this
     */
    public function setHardBounces($hardBounces)
    {
        $this->container['hardBounces'] = $hardBounces;
        return $this;
    }
    /**
     * Gets softBounces
     *
     * @return int
     */
    public function getSoftBounces()
    {
        return $this->container['softBounces'];
    }
    /**
     * Sets softBounces
     *
     * @param int $softBounces Number of softbounces for the timeframe
     *
     * @return $this
     */
    public function setSoftBounces($softBounces)
    {
        $this->container['softBounces'] = $softBounces;
        return $this;
    }
    /**
     * Gets clicks
     *
     * @return int
     */
    public function getClicks()
    {
        return $this->container['clicks'];
    }
    /**
     * Sets clicks
     *
     * @param int $clicks Number of clicks for the timeframe
     *
     * @return $this
     */
    public function setClicks($clicks)
    {
        $this->container['clicks'] = $clicks;
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
     * @param int $uniqueClicks Number of unique clicks for the timeframe
     *
     * @return $this
     */
    public function setUniqueClicks($uniqueClicks)
    {
        $this->container['uniqueClicks'] = $uniqueClicks;
        return $this;
    }
    /**
     * Gets opens
     *
     * @return int
     */
    public function getOpens()
    {
        return $this->container['opens'];
    }
    /**
     * Sets opens
     *
     * @param int $opens Number of openings for the timeframe
     *
     * @return $this
     */
    public function setOpens($opens)
    {
        $this->container['opens'] = $opens;
        return $this;
    }
    /**
     * Gets uniqueOpens
     *
     * @return int
     */
    public function getUniqueOpens()
    {
        return $this->container['uniqueOpens'];
    }
    /**
     * Sets uniqueOpens
     *
     * @param int $uniqueOpens Number of unique openings for the timeframe
     *
     * @return $this
     */
    public function setUniqueOpens($uniqueOpens)
    {
        $this->container['uniqueOpens'] = $uniqueOpens;
        return $this;
    }
    /**
     * Gets spamReports
     *
     * @return int
     */
    public function getSpamReports()
    {
        return $this->container['spamReports'];
    }
    /**
     * Sets spamReports
     *
     * @param int $spamReports Number of complaint (spam report) for the timeframe
     *
     * @return $this
     */
    public function setSpamReports($spamReports)
    {
        $this->container['spamReports'] = $spamReports;
        return $this;
    }
    /**
     * Gets blocked
     *
     * @return int
     */
    public function getBlocked()
    {
        return $this->container['blocked'];
    }
    /**
     * Sets blocked
     *
     * @param int $blocked Number of blocked contact emails for the timeframe
     *
     * @return $this
     */
    public function setBlocked($blocked)
    {
        $this->container['blocked'] = $blocked;
        return $this;
    }
    /**
     * Gets invalid
     *
     * @return int
     */
    public function getInvalid()
    {
        return $this->container['invalid'];
    }
    /**
     * Sets invalid
     *
     * @param int $invalid Number of invalid emails for the timeframe
     *
     * @return $this
     */
    public function setInvalid($invalid)
    {
        $this->container['invalid'] = $invalid;
        return $this;
    }
    /**
     * Gets unsubscribed
     *
     * @return int
     */
    public function getUnsubscribed()
    {
        return $this->container['unsubscribed'];
    }
    /**
     * Sets unsubscribed
     *
     * @param int $unsubscribed Number of unsubscribed emails for the timeframe
     *
     * @return $this
     */
    public function setUnsubscribed($unsubscribed)
    {
        $this->container['unsubscribed'] = $unsubscribed;
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
