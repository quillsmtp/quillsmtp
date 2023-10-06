<?php

/**
 * CreateWhatsAppTemplate
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
 * CreateWhatsAppTemplate Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreateWhatsAppTemplate implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'createWhatsAppTemplate';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['name' => 'string', 'language' => 'string', 'category' => 'string', 'mediaUrl' => 'string', 'bodyText' => 'string', 'headerText' => 'string'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['name' => null, 'language' => null, 'category' => null, 'mediaUrl' => null, 'bodyText' => null, 'headerText' => null];
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
    protected static $attributeMap = ['name' => 'name', 'language' => 'language', 'category' => 'category', 'mediaUrl' => 'mediaUrl', 'bodyText' => 'bodyText', 'headerText' => 'headerText'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['name' => 'setName', 'language' => 'setLanguage', 'category' => 'setCategory', 'mediaUrl' => 'setMediaUrl', 'bodyText' => 'setBodyText', 'headerText' => 'setHeaderText'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['name' => 'getName', 'language' => 'getLanguage', 'category' => 'getCategory', 'mediaUrl' => 'getMediaUrl', 'bodyText' => 'getBodyText', 'headerText' => 'getHeaderText'];
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
    const CATEGORY_MARKETING = 'MARKETING';
    const CATEGORY_TRANSACTIONAL = 'TRANSACTIONAL';
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getCategoryAllowableValues()
    {
        return [self::CATEGORY_MARKETING, self::CATEGORY_TRANSACTIONAL];
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
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['language'] = isset($data['language']) ? $data['language'] : null;
        $this->container['category'] = isset($data['category']) ? $data['category'] : null;
        $this->container['mediaUrl'] = isset($data['mediaUrl']) ? $data['mediaUrl'] : null;
        $this->container['bodyText'] = isset($data['bodyText']) ? $data['bodyText'] : null;
        $this->container['headerText'] = isset($data['headerText']) ? $data['headerText'] : null;
    }
    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];
        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        if ($this->container['language'] === null) {
            $invalidProperties[] = "'language' can't be null";
        }
        if ($this->container['category'] === null) {
            $invalidProperties[] = "'category' can't be null";
        }
        $allowedValues = $this->getCategoryAllowableValues();
        if (!\is_null($this->container['category']) && !\in_array($this->container['category'], $allowedValues, \true)) {
            $invalidProperties[] = \sprintf("invalid value for 'category', must be one of '%s'", \implode("', '", $allowedValues));
        }
        if ($this->container['bodyText'] === null) {
            $invalidProperties[] = "'bodyText' can't be null";
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
     * @param string $name Name of the template
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;
        return $this;
    }
    /**
     * Gets language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->container['language'];
    }
    /**
     * Sets language
     *
     * @param string $language Language of the template. For Example : **en** for English
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->container['language'] = $language;
        return $this;
    }
    /**
     * Gets category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->container['category'];
    }
    /**
     * Sets category
     *
     * @param string $category Category of the template
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $allowedValues = $this->getCategoryAllowableValues();
        if (!\in_array($category, $allowedValues, \true)) {
            throw new \InvalidArgumentException(\sprintf("Invalid value for 'category', must be one of '%s'", \implode("', '", $allowedValues)));
        }
        $this->container['category'] = $category;
        return $this;
    }
    /**
     * Gets mediaUrl
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->container['mediaUrl'];
    }
    /**
     * Sets mediaUrl
     *
     * @param string $mediaUrl Absolute url of the media file **(no local file)** for the header. **Use this field in you want to add media in Template header and headerText is empty.** Allowed extensions for media files are: #### jpeg | png | mp4 | pdf
     *
     * @return $this
     */
    public function setMediaUrl($mediaUrl)
    {
        $this->container['mediaUrl'] = $mediaUrl;
        return $this;
    }
    /**
     * Gets bodyText
     *
     * @return string
     */
    public function getBodyText()
    {
        return $this->container['bodyText'];
    }
    /**
     * Sets bodyText
     *
     * @param string $bodyText Body of the template. **Maximum allowed characters are 1024**
     *
     * @return $this
     */
    public function setBodyText($bodyText)
    {
        $this->container['bodyText'] = $bodyText;
        return $this;
    }
    /**
     * Gets headerText
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->container['headerText'];
    }
    /**
     * Sets headerText
     *
     * @param string $headerText Text content of the header in the template.  **Maximum allowed characters are 45** **Use this field to add text content in template header and if mediaUrl is empty**
     *
     * @return $this
     */
    public function setHeaderText($headerText)
    {
        $this->container['headerText'] = $headerText;
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
