<?php

/**
 * MasterDetailsResponsePlanInfo
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
 * MasterDetailsResponsePlanInfo Class Doc Comment
 *
 * @category Class
 * @description Plan details
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class MasterDetailsResponsePlanInfo implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'masterDetailsResponse_planInfo';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['currencyCode' => 'string', 'nextBillingAt' => 'int', 'price' => 'float', 'planPeriod' => 'string', 'subAccounts' => 'int', 'features' => '\\QuillSMTP\\Vendor\\Brevo\\Client\\Model\\MasterDetailsResponsePlanInfoFeatures[]'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['currencyCode' => null, 'nextBillingAt' => 'int64', 'price' => null, 'planPeriod' => null, 'subAccounts' => null, 'features' => null];
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
    protected static $attributeMap = ['currencyCode' => 'currencyCode', 'nextBillingAt' => 'nextBillingAt', 'price' => 'price', 'planPeriod' => 'planPeriod', 'subAccounts' => 'subAccounts', 'features' => 'features'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['currencyCode' => 'setCurrencyCode', 'nextBillingAt' => 'setNextBillingAt', 'price' => 'setPrice', 'planPeriod' => 'setPlanPeriod', 'subAccounts' => 'setSubAccounts', 'features' => 'setFeatures'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['currencyCode' => 'getCurrencyCode', 'nextBillingAt' => 'getNextBillingAt', 'price' => 'getPrice', 'planPeriod' => 'getPlanPeriod', 'subAccounts' => 'getSubAccounts', 'features' => 'getFeatures'];
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
    const PLAN_PERIOD_MONTH = 'month';
    const PLAN_PERIOD_YEAR = 'year';
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPlanPeriodAllowableValues()
    {
        return [self::PLAN_PERIOD_MONTH, self::PLAN_PERIOD_YEAR];
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
        $this->container['currencyCode'] = isset($data['currencyCode']) ? $data['currencyCode'] : null;
        $this->container['nextBillingAt'] = isset($data['nextBillingAt']) ? $data['nextBillingAt'] : null;
        $this->container['price'] = isset($data['price']) ? $data['price'] : null;
        $this->container['planPeriod'] = isset($data['planPeriod']) ? $data['planPeriod'] : null;
        $this->container['subAccounts'] = isset($data['subAccounts']) ? $data['subAccounts'] : null;
        $this->container['features'] = isset($data['features']) ? $data['features'] : null;
    }
    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];
        $allowedValues = $this->getPlanPeriodAllowableValues();
        if (!\is_null($this->container['planPeriod']) && !\in_array($this->container['planPeriod'], $allowedValues, \true)) {
            $invalidProperties[] = \sprintf("invalid value for 'planPeriod', must be one of '%s'", \implode("', '", $allowedValues));
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
     * Gets currencyCode
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->container['currencyCode'];
    }
    /**
     * Sets currencyCode
     *
     * @param string $currencyCode Plan currency
     *
     * @return $this
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->container['currencyCode'] = $currencyCode;
        return $this;
    }
    /**
     * Gets nextBillingAt
     *
     * @return int
     */
    public function getNextBillingAt()
    {
        return $this->container['nextBillingAt'];
    }
    /**
     * Sets nextBillingAt
     *
     * @param int $nextBillingAt Timestamp of next billing date
     *
     * @return $this
     */
    public function setNextBillingAt($nextBillingAt)
    {
        $this->container['nextBillingAt'] = $nextBillingAt;
        return $this;
    }
    /**
     * Gets price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->container['price'];
    }
    /**
     * Sets price
     *
     * @param float $price Plan amount
     *
     * @return $this
     */
    public function setPrice($price)
    {
        $this->container['price'] = $price;
        return $this;
    }
    /**
     * Gets planPeriod
     *
     * @return string
     */
    public function getPlanPeriod()
    {
        return $this->container['planPeriod'];
    }
    /**
     * Sets planPeriod
     *
     * @param string $planPeriod Plan period type
     *
     * @return $this
     */
    public function setPlanPeriod($planPeriod)
    {
        $allowedValues = $this->getPlanPeriodAllowableValues();
        if (!\is_null($planPeriod) && !\in_array($planPeriod, $allowedValues, \true)) {
            throw new \InvalidArgumentException(\sprintf("Invalid value for 'planPeriod', must be one of '%s'", \implode("', '", $allowedValues)));
        }
        $this->container['planPeriod'] = $planPeriod;
        return $this;
    }
    /**
     * Gets subAccounts
     *
     * @return int
     */
    public function getSubAccounts()
    {
        return $this->container['subAccounts'];
    }
    /**
     * Sets subAccounts
     *
     * @param int $subAccounts Number of sub-accounts
     *
     * @return $this
     */
    public function setSubAccounts($subAccounts)
    {
        $this->container['subAccounts'] = $subAccounts;
        return $this;
    }
    /**
     * Gets features
     *
     * @return \Brevo\Client\Model\MasterDetailsResponsePlanInfoFeatures[]
     */
    public function getFeatures()
    {
        return $this->container['features'];
    }
    /**
     * Sets features
     *
     * @param \Brevo\Client\Model\MasterDetailsResponsePlanInfoFeatures[] $features List of provided features in the plan
     *
     * @return $this
     */
    public function setFeatures($features)
    {
        $this->container['features'] = $features;
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
