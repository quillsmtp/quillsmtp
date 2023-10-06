<?php

/**
 * Order
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
 * Order Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Order implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'order';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['id' => 'string', 'createdAt' => 'string', 'updatedAt' => 'string', 'status' => 'string', 'amount' => 'float', 'products' => '\\Brevo\\Client\\Model\\OrderProducts[]', 'email' => 'string', 'billing' => 'QuillSMTP\\Vendor\\Brevo\\Client\\Model\\OrderBilling', 'coupons' => 'string[]'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['id' => null, 'createdAt' => null, 'updatedAt' => null, 'status' => null, 'amount' => null, 'products' => null, 'email' => null, 'billing' => null, 'coupons' => null];
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
    protected static $attributeMap = ['id' => 'id', 'createdAt' => 'createdAt', 'updatedAt' => 'updatedAt', 'status' => 'status', 'amount' => 'amount', 'products' => 'products', 'email' => 'email', 'billing' => 'billing', 'coupons' => 'coupons'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['id' => 'setId', 'createdAt' => 'setCreatedAt', 'updatedAt' => 'setUpdatedAt', 'status' => 'setStatus', 'amount' => 'setAmount', 'products' => 'setProducts', 'email' => 'setEmail', 'billing' => 'setBilling', 'coupons' => 'setCoupons'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['id' => 'getId', 'createdAt' => 'getCreatedAt', 'updatedAt' => 'getUpdatedAt', 'status' => 'getStatus', 'amount' => 'getAmount', 'products' => 'getProducts', 'email' => 'getEmail', 'billing' => 'getBilling', 'coupons' => 'getCoupons'];
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
        $this->container['createdAt'] = isset($data['createdAt']) ? $data['createdAt'] : null;
        $this->container['updatedAt'] = isset($data['updatedAt']) ? $data['updatedAt'] : null;
        $this->container['status'] = isset($data['status']) ? $data['status'] : null;
        $this->container['amount'] = isset($data['amount']) ? $data['amount'] : null;
        $this->container['products'] = isset($data['products']) ? $data['products'] : null;
        $this->container['email'] = isset($data['email']) ? $data['email'] : null;
        $this->container['billing'] = isset($data['billing']) ? $data['billing'] : null;
        $this->container['coupons'] = isset($data['coupons']) ? $data['coupons'] : null;
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
        if ($this->container['createdAt'] === null) {
            $invalidProperties[] = "'createdAt' can't be null";
        }
        if ($this->container['updatedAt'] === null) {
            $invalidProperties[] = "'updatedAt' can't be null";
        }
        if ($this->container['status'] === null) {
            $invalidProperties[] = "'status' can't be null";
        }
        if ($this->container['amount'] === null) {
            $invalidProperties[] = "'amount' can't be null";
        }
        if ($this->container['products'] === null) {
            $invalidProperties[] = "'products' can't be null";
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
     * @param string $id Unique ID of the order.
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->container['id'] = $id;
        return $this;
    }
    /**
     * Gets createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->container['createdAt'];
    }
    /**
     * Sets createdAt
     *
     * @param string $createdAt Event occurrence UTC date-time (YYYY-MM-DDTHH:mm:ssZ), when order is actually created.
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->container['createdAt'] = $createdAt;
        return $this;
    }
    /**
     * Gets updatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->container['updatedAt'];
    }
    /**
     * Sets updatedAt
     *
     * @param string $updatedAt Event updated UTC date-time (YYYY-MM-DDTHH:mm:ssZ), when the status of the order is actually changed/updated.
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->container['updatedAt'] = $updatedAt;
        return $this;
    }
    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->container['status'];
    }
    /**
     * Sets status
     *
     * @param string $status State of the order.
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->container['status'] = $status;
        return $this;
    }
    /**
     * Gets amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->container['amount'];
    }
    /**
     * Sets amount
     *
     * @param float $amount Total amount of the order, including all shipping expenses, tax and the price of items.
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->container['amount'] = $amount;
        return $this;
    }
    /**
     * Gets products
     *
     * @return \Brevo\Client\Model\OrderProducts[]
     */
    public function getProducts()
    {
        return $this->container['products'];
    }
    /**
     * Sets products
     *
     * @param \Brevo\Client\Model\OrderProducts[] $products products
     *
     * @return $this
     */
    public function setProducts($products)
    {
        $this->container['products'] = $products;
        return $this;
    }
    /**
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->container['email'];
    }
    /**
     * Sets email
     *
     * @param string $email Email of the contact, Mandatory if \"phone\" field is not passed in \"billing\" parameter.
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;
        return $this;
    }
    /**
     * Gets billing
     *
     * @return \Brevo\Client\Model\OrderBilling
     */
    public function getBilling()
    {
        return $this->container['billing'];
    }
    /**
     * Sets billing
     *
     * @param \Brevo\Client\Model\OrderBilling $billing billing
     *
     * @return $this
     */
    public function setBilling($billing)
    {
        $this->container['billing'] = $billing;
        return $this;
    }
    /**
     * Gets coupons
     *
     * @return string[]
     */
    public function getCoupons()
    {
        return $this->container['coupons'];
    }
    /**
     * Sets coupons
     *
     * @param string[] $coupons Coupons applied to the order. Stored case insensitive.
     *
     * @return $this
     */
    public function setCoupons($coupons)
    {
        $this->container['coupons'] = $coupons;
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
