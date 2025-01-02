<?php

/**
 * GetCorporateInvitedUsersListFeatureAccess
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
 * GetCorporateInvitedUsersListFeatureAccess Class Doc Comment
 *
 * @category Class
 * @description Feature accessiblity given to the user.
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class GetCorporateInvitedUsersListFeatureAccess implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'getCorporateInvitedUsersList_feature_access';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['userManagement' => 'string[]', 'apiKeys' => 'string[]', 'myPlan' => 'string[]', 'appsManagement' => 'string[]'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['userManagement' => null, 'apiKeys' => null, 'myPlan' => null, 'appsManagement' => null];
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
    protected static $attributeMap = ['userManagement' => 'user_management', 'apiKeys' => 'api_keys', 'myPlan' => 'my_plan', 'appsManagement' => 'apps_management'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['userManagement' => 'setUserManagement', 'apiKeys' => 'setApiKeys', 'myPlan' => 'setMyPlan', 'appsManagement' => 'setAppsManagement'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['userManagement' => 'getUserManagement', 'apiKeys' => 'getApiKeys', 'myPlan' => 'getMyPlan', 'appsManagement' => 'getAppsManagement'];
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
        $this->container['userManagement'] = isset($data['userManagement']) ? $data['userManagement'] : null;
        $this->container['apiKeys'] = isset($data['apiKeys']) ? $data['apiKeys'] : null;
        $this->container['myPlan'] = isset($data['myPlan']) ? $data['myPlan'] : null;
        $this->container['appsManagement'] = isset($data['appsManagement']) ? $data['appsManagement'] : null;
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
     * Gets userManagement
     *
     * @return string[]
     */
    public function getUserManagement()
    {
        return $this->container['userManagement'];
    }
    /**
     * Sets userManagement
     *
     * @param string[] $userManagement User management accessiblity.
     *
     * @return $this
     */
    public function setUserManagement($userManagement)
    {
        $this->container['userManagement'] = $userManagement;
        return $this;
    }
    /**
     * Gets apiKeys
     *
     * @return string[]
     */
    public function getApiKeys()
    {
        return $this->container['apiKeys'];
    }
    /**
     * Sets apiKeys
     *
     * @param string[] $apiKeys Api keys accessiblity.
     *
     * @return $this
     */
    public function setApiKeys($apiKeys)
    {
        $this->container['apiKeys'] = $apiKeys;
        return $this;
    }
    /**
     * Gets myPlan
     *
     * @return string[]
     */
    public function getMyPlan()
    {
        return $this->container['myPlan'];
    }
    /**
     * Sets myPlan
     *
     * @param string[] $myPlan My plan accessiblity.
     *
     * @return $this
     */
    public function setMyPlan($myPlan)
    {
        $this->container['myPlan'] = $myPlan;
        return $this;
    }
    /**
     * Gets appsManagement
     *
     * @return string[]
     */
    public function getAppsManagement()
    {
        return $this->container['appsManagement'];
    }
    /**
     * Sets appsManagement
     *
     * @param string[] $appsManagement Apps management accessiblity | Not available in ENTv2
     *
     * @return $this
     */
    public function setAppsManagement($appsManagement)
    {
        $this->container['appsManagement'] = $appsManagement;
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