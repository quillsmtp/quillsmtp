<?php

/**
 * FileData
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
 * FileData Class Doc Comment
 *
 * @category Class
 * @description File data that is uploaded
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class FileData implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'FileData';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['url' => 'string', 'id' => 'string', 'name' => 'string', 'authorId' => 'string', 'author' => 'object', 'contactId' => 'int', 'dealId' => 'string', 'companyId' => 'string', 'size' => 'int', 'createdAt' => '\\DateTime', 'updatedAt' => '\\DateTime'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['url' => null, 'id' => null, 'name' => null, 'authorId' => null, 'author' => null, 'contactId' => 'int64', 'dealId' => null, 'companyId' => null, 'size' => 'int64', 'createdAt' => 'date-time', 'updatedAt' => 'date-time'];
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
    protected static $attributeMap = ['url' => 'url', 'id' => 'id', 'name' => 'name', 'authorId' => 'authorId', 'author' => 'author', 'contactId' => 'contactId', 'dealId' => 'dealId', 'companyId' => 'companyId', 'size' => 'size', 'createdAt' => 'createdAt', 'updatedAt' => 'updatedAt'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['url' => 'setUrl', 'id' => 'setId', 'name' => 'setName', 'authorId' => 'setAuthorId', 'author' => 'setAuthor', 'contactId' => 'setContactId', 'dealId' => 'setDealId', 'companyId' => 'setCompanyId', 'size' => 'setSize', 'createdAt' => 'setCreatedAt', 'updatedAt' => 'setUpdatedAt'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['url' => 'getUrl', 'id' => 'getId', 'name' => 'getName', 'authorId' => 'getAuthorId', 'author' => 'getAuthor', 'contactId' => 'getContactId', 'dealId' => 'getDealId', 'companyId' => 'getCompanyId', 'size' => 'getSize', 'createdAt' => 'getCreatedAt', 'updatedAt' => 'getUpdatedAt'];
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
        $this->container['url'] = isset($data['url']) ? $data['url'] : null;
        $this->container['id'] = isset($data['id']) ? $data['id'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['authorId'] = isset($data['authorId']) ? $data['authorId'] : null;
        $this->container['author'] = isset($data['author']) ? $data['author'] : null;
        $this->container['contactId'] = isset($data['contactId']) ? $data['contactId'] : null;
        $this->container['dealId'] = isset($data['dealId']) ? $data['dealId'] : null;
        $this->container['companyId'] = isset($data['companyId']) ? $data['companyId'] : null;
        $this->container['size'] = isset($data['size']) ? $data['size'] : null;
        $this->container['createdAt'] = isset($data['createdAt']) ? $data['createdAt'] : null;
        $this->container['updatedAt'] = isset($data['updatedAt']) ? $data['updatedAt'] : null;
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
     * @param string $url Url of uploaded file
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->container['url'] = $url;
        return $this;
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
     * @param string $id Id of uploaded file
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
     * @param string $name Name of uploaded file
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;
        return $this;
    }
    /**
     * Gets authorId
     *
     * @return string
     */
    public function getAuthorId()
    {
        return $this->container['authorId'];
    }
    /**
     * Sets authorId
     *
     * @param string $authorId Account id of user which created the file
     *
     * @return $this
     */
    public function setAuthorId($authorId)
    {
        $this->container['authorId'] = $authorId;
        return $this;
    }
    /**
     * Gets contactId
     *
     * @return int
     */
    public function getContactId()
    {
        return $this->container['contactId'];
    }
    /**
     * Sets contactId
     *
     * @param int $contactId Contact id of contact on which file is uploaded
     *
     * @return $this
     */
    public function setContactId($contactId)
    {
        $this->container['contactId'] = $contactId;
        return $this;
    }
    /**
     * Gets dealId
     *
     * @return string
     */
    public function getDealId()
    {
        return $this->container['dealId'];
    }
    /**
     * Sets dealId
     *
     * @param string $dealId Deal id linked to a file
     *
     * @return $this
     */
    public function setDealId($dealId)
    {
        $this->container['dealId'] = $dealId;
        return $this;
    }
    /**
     * Gets companyId
     *
     * @return string
     */
    public function getCompanyId()
    {
        return $this->container['companyId'];
    }
    /**
     * Sets companyId
     *
     * @param string $companyId Company id linked to a file
     *
     * @return $this
     */
    public function setCompanyId($companyId)
    {
        $this->container['companyId'] = $companyId;
        return $this;
    }
    /**
     * Gets size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->container['size'];
    }
    /**
     * Sets size
     *
     * @param int $size Size of file in bytes
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->container['size'] = $size;
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
     * @param \DateTime $createdAt File created date/time
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
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->container['updatedAt'];
    }
    /**
     * Sets updatedAt
     *
     * @param \DateTime $updatedAt File updated date/time
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->container['updatedAt'] = $updatedAt;
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
