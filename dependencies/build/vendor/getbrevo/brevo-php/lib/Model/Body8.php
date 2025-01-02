<?php

/**
 * Body8
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
 * Body8 Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Body8 implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'body_8';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['name' => 'string', 'duration' => 'int', 'taskTypeId' => 'string', 'date' => '\\DateTime', 'notes' => 'string', 'done' => 'bool', 'assignToId' => 'string', 'contactsIds' => 'int[]', 'dealsIds' => 'string[]', 'companiesIds' => 'string[]', 'reminder' => 'QuillSMTP\\Vendor\\Brevo\\Client\\Model\\TaskReminder'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['name' => null, 'duration' => 'int64', 'taskTypeId' => null, 'date' => 'date-time', 'notes' => null, 'done' => null, 'assignToId' => null, 'contactsIds' => null, 'dealsIds' => null, 'companiesIds' => null, 'reminder' => null];
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
    protected static $attributeMap = ['name' => 'name', 'duration' => 'duration', 'taskTypeId' => 'taskTypeId', 'date' => 'date', 'notes' => 'notes', 'done' => 'done', 'assignToId' => 'assignToId', 'contactsIds' => 'contactsIds', 'dealsIds' => 'dealsIds', 'companiesIds' => 'companiesIds', 'reminder' => 'reminder'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['name' => 'setName', 'duration' => 'setDuration', 'taskTypeId' => 'setTaskTypeId', 'date' => 'setDate', 'notes' => 'setNotes', 'done' => 'setDone', 'assignToId' => 'setAssignToId', 'contactsIds' => 'setContactsIds', 'dealsIds' => 'setDealsIds', 'companiesIds' => 'setCompaniesIds', 'reminder' => 'setReminder'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['name' => 'getName', 'duration' => 'getDuration', 'taskTypeId' => 'getTaskTypeId', 'date' => 'getDate', 'notes' => 'getNotes', 'done' => 'getDone', 'assignToId' => 'getAssignToId', 'contactsIds' => 'getContactsIds', 'dealsIds' => 'getDealsIds', 'companiesIds' => 'getCompaniesIds', 'reminder' => 'getReminder'];
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
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['duration'] = isset($data['duration']) ? $data['duration'] : null;
        $this->container['taskTypeId'] = isset($data['taskTypeId']) ? $data['taskTypeId'] : null;
        $this->container['date'] = isset($data['date']) ? $data['date'] : null;
        $this->container['notes'] = isset($data['notes']) ? $data['notes'] : null;
        $this->container['done'] = isset($data['done']) ? $data['done'] : null;
        $this->container['assignToId'] = isset($data['assignToId']) ? $data['assignToId'] : null;
        $this->container['contactsIds'] = isset($data['contactsIds']) ? $data['contactsIds'] : null;
        $this->container['dealsIds'] = isset($data['dealsIds']) ? $data['dealsIds'] : null;
        $this->container['companiesIds'] = isset($data['companiesIds']) ? $data['companiesIds'] : null;
        $this->container['reminder'] = isset($data['reminder']) ? $data['reminder'] : null;
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
        if (!\is_null($this->container['duration']) && $this->container['duration'] < 0) {
            $invalidProperties[] = "invalid value for 'duration', must be bigger than or equal to 0.";
        }
        if ($this->container['taskTypeId'] === null) {
            $invalidProperties[] = "'taskTypeId' can't be null";
        }
        if ($this->container['date'] === null) {
            $invalidProperties[] = "'date' can't be null";
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
     * @param string $name Name of task
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;
        return $this;
    }
    /**
     * Gets duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->container['duration'];
    }
    /**
     * Sets duration
     *
     * @param int $duration Duration of task in milliseconds [1 minute = 60000 ms]
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        if (!\is_null($duration) && $duration < 0) {
            throw new \InvalidArgumentException('invalid value for $duration when calling Body8., must be bigger than or equal to 0.');
        }
        $this->container['duration'] = $duration;
        return $this;
    }
    /**
     * Gets taskTypeId
     *
     * @return string
     */
    public function getTaskTypeId()
    {
        return $this->container['taskTypeId'];
    }
    /**
     * Sets taskTypeId
     *
     * @param string $taskTypeId Id for type of task e.g Call / Email / Meeting etc.
     *
     * @return $this
     */
    public function setTaskTypeId($taskTypeId)
    {
        $this->container['taskTypeId'] = $taskTypeId;
        return $this;
    }
    /**
     * Gets date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->container['date'];
    }
    /**
     * Sets date
     *
     * @param \DateTime $date Task due date and time
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->container['date'] = $date;
        return $this;
    }
    /**
     * Gets notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->container['notes'];
    }
    /**
     * Sets notes
     *
     * @param string $notes Notes added to a task
     *
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->container['notes'] = $notes;
        return $this;
    }
    /**
     * Gets done
     *
     * @return bool
     */
    public function getDone()
    {
        return $this->container['done'];
    }
    /**
     * Sets done
     *
     * @param bool $done Task marked as done
     *
     * @return $this
     */
    public function setDone($done)
    {
        $this->container['done'] = $done;
        return $this;
    }
    /**
     * Gets assignToId
     *
     * @return string
     */
    public function getAssignToId()
    {
        return $this->container['assignToId'];
    }
    /**
     * Sets assignToId
     *
     * @param string $assignToId User id to whom task is assigned
     *
     * @return $this
     */
    public function setAssignToId($assignToId)
    {
        $this->container['assignToId'] = $assignToId;
        return $this;
    }
    /**
     * Gets contactsIds
     *
     * @return int[]
     */
    public function getContactsIds()
    {
        return $this->container['contactsIds'];
    }
    /**
     * Sets contactsIds
     *
     * @param int[] $contactsIds Contact ids for contacts linked to this task
     *
     * @return $this
     */
    public function setContactsIds($contactsIds)
    {
        $this->container['contactsIds'] = $contactsIds;
        return $this;
    }
    /**
     * Gets dealsIds
     *
     * @return string[]
     */
    public function getDealsIds()
    {
        return $this->container['dealsIds'];
    }
    /**
     * Sets dealsIds
     *
     * @param string[] $dealsIds Deal ids for deals a task is linked to
     *
     * @return $this
     */
    public function setDealsIds($dealsIds)
    {
        $this->container['dealsIds'] = $dealsIds;
        return $this;
    }
    /**
     * Gets companiesIds
     *
     * @return string[]
     */
    public function getCompaniesIds()
    {
        return $this->container['companiesIds'];
    }
    /**
     * Sets companiesIds
     *
     * @param string[] $companiesIds Companies ids for companies a task is linked to
     *
     * @return $this
     */
    public function setCompaniesIds($companiesIds)
    {
        $this->container['companiesIds'] = $companiesIds;
        return $this;
    }
    /**
     * Gets reminder
     *
     * @return \Brevo\Client\Model\TaskReminder
     */
    public function getReminder()
    {
        return $this->container['reminder'];
    }
    /**
     * Sets reminder
     *
     * @param \Brevo\Client\Model\TaskReminder $reminder reminder
     *
     * @return $this
     */
    public function setReminder($reminder)
    {
        $this->container['reminder'] = $reminder;
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
