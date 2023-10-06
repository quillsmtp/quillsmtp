<?php

/**
 * SubAccountAppsToggleRequest
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
 * SubAccountAppsToggleRequest Class Doc Comment
 *
 * @category Class
 * @description List of enable/disable applications on the sub-account
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class SubAccountAppsToggleRequest implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $swaggerModelName = 'subAccountAppsToggleRequest';
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerTypes = ['inbox' => 'bool', 'whatsapp' => 'bool', 'automation' => 'bool', 'emailCampaigns' => 'bool', 'smsCampaigns' => 'bool', 'landingPages' => 'bool', 'transactionalEmails' => 'bool', 'transactionalSms' => 'bool', 'facebookAds' => 'bool', 'webPush' => 'bool', 'meetings' => 'bool', 'conversations' => 'bool', 'crm' => 'bool'];
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $swaggerFormats = ['inbox' => null, 'whatsapp' => null, 'automation' => null, 'emailCampaigns' => null, 'smsCampaigns' => null, 'landingPages' => null, 'transactionalEmails' => null, 'transactionalSms' => null, 'facebookAds' => null, 'webPush' => null, 'meetings' => null, 'conversations' => null, 'crm' => null];
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
    protected static $attributeMap = ['inbox' => 'inbox', 'whatsapp' => 'whatsapp', 'automation' => 'automation', 'emailCampaigns' => 'email-campaigns', 'smsCampaigns' => 'sms-campaigns', 'landingPages' => 'landing-pages', 'transactionalEmails' => 'transactional-emails', 'transactionalSms' => 'transactional-sms', 'facebookAds' => 'facebook-ads', 'webPush' => 'web-push', 'meetings' => 'meetings', 'conversations' => 'conversations', 'crm' => 'crm'];
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = ['inbox' => 'setInbox', 'whatsapp' => 'setWhatsapp', 'automation' => 'setAutomation', 'emailCampaigns' => 'setEmailCampaigns', 'smsCampaigns' => 'setSmsCampaigns', 'landingPages' => 'setLandingPages', 'transactionalEmails' => 'setTransactionalEmails', 'transactionalSms' => 'setTransactionalSms', 'facebookAds' => 'setFacebookAds', 'webPush' => 'setWebPush', 'meetings' => 'setMeetings', 'conversations' => 'setConversations', 'crm' => 'setCrm'];
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = ['inbox' => 'getInbox', 'whatsapp' => 'getWhatsapp', 'automation' => 'getAutomation', 'emailCampaigns' => 'getEmailCampaigns', 'smsCampaigns' => 'getSmsCampaigns', 'landingPages' => 'getLandingPages', 'transactionalEmails' => 'getTransactionalEmails', 'transactionalSms' => 'getTransactionalSms', 'facebookAds' => 'getFacebookAds', 'webPush' => 'getWebPush', 'meetings' => 'getMeetings', 'conversations' => 'getConversations', 'crm' => 'getCrm'];
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
        $this->container['inbox'] = isset($data['inbox']) ? $data['inbox'] : null;
        $this->container['whatsapp'] = isset($data['whatsapp']) ? $data['whatsapp'] : null;
        $this->container['automation'] = isset($data['automation']) ? $data['automation'] : null;
        $this->container['emailCampaigns'] = isset($data['emailCampaigns']) ? $data['emailCampaigns'] : null;
        $this->container['smsCampaigns'] = isset($data['smsCampaigns']) ? $data['smsCampaigns'] : null;
        $this->container['landingPages'] = isset($data['landingPages']) ? $data['landingPages'] : null;
        $this->container['transactionalEmails'] = isset($data['transactionalEmails']) ? $data['transactionalEmails'] : null;
        $this->container['transactionalSms'] = isset($data['transactionalSms']) ? $data['transactionalSms'] : null;
        $this->container['facebookAds'] = isset($data['facebookAds']) ? $data['facebookAds'] : null;
        $this->container['webPush'] = isset($data['webPush']) ? $data['webPush'] : null;
        $this->container['meetings'] = isset($data['meetings']) ? $data['meetings'] : null;
        $this->container['conversations'] = isset($data['conversations']) ? $data['conversations'] : null;
        $this->container['crm'] = isset($data['crm']) ? $data['crm'] : null;
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
     * Gets inbox
     *
     * @return bool
     */
    public function getInbox()
    {
        return $this->container['inbox'];
    }
    /**
     * Sets inbox
     *
     * @param bool $inbox Set this field to enable or disable Inbox on the sub-account
     *
     * @return $this
     */
    public function setInbox($inbox)
    {
        $this->container['inbox'] = $inbox;
        return $this;
    }
    /**
     * Gets whatsapp
     *
     * @return bool
     */
    public function getWhatsapp()
    {
        return $this->container['whatsapp'];
    }
    /**
     * Sets whatsapp
     *
     * @param bool $whatsapp Set this field to enable or disable Whatsapp campaigns on the sub-account
     *
     * @return $this
     */
    public function setWhatsapp($whatsapp)
    {
        $this->container['whatsapp'] = $whatsapp;
        return $this;
    }
    /**
     * Gets automation
     *
     * @return bool
     */
    public function getAutomation()
    {
        return $this->container['automation'];
    }
    /**
     * Sets automation
     *
     * @param bool $automation Set this field to enable or disable Automation on the sub-account
     *
     * @return $this
     */
    public function setAutomation($automation)
    {
        $this->container['automation'] = $automation;
        return $this;
    }
    /**
     * Gets emailCampaigns
     *
     * @return bool
     */
    public function getEmailCampaigns()
    {
        return $this->container['emailCampaigns'];
    }
    /**
     * Sets emailCampaigns
     *
     * @param bool $emailCampaigns Set this field to enable or disable Email Campaigns on the sub-account
     *
     * @return $this
     */
    public function setEmailCampaigns($emailCampaigns)
    {
        $this->container['emailCampaigns'] = $emailCampaigns;
        return $this;
    }
    /**
     * Gets smsCampaigns
     *
     * @return bool
     */
    public function getSmsCampaigns()
    {
        return $this->container['smsCampaigns'];
    }
    /**
     * Sets smsCampaigns
     *
     * @param bool $smsCampaigns Set this field to enable or disable SMS Marketing on the sub-account
     *
     * @return $this
     */
    public function setSmsCampaigns($smsCampaigns)
    {
        $this->container['smsCampaigns'] = $smsCampaigns;
        return $this;
    }
    /**
     * Gets landingPages
     *
     * @return bool
     */
    public function getLandingPages()
    {
        return $this->container['landingPages'];
    }
    /**
     * Sets landingPages
     *
     * @param bool $landingPages Set this field to enable or disable Landing pages on the sub-account
     *
     * @return $this
     */
    public function setLandingPages($landingPages)
    {
        $this->container['landingPages'] = $landingPages;
        return $this;
    }
    /**
     * Gets transactionalEmails
     *
     * @return bool
     */
    public function getTransactionalEmails()
    {
        return $this->container['transactionalEmails'];
    }
    /**
     * Sets transactionalEmails
     *
     * @param bool $transactionalEmails Set this field to enable or disable Transactional Email on the sub-account
     *
     * @return $this
     */
    public function setTransactionalEmails($transactionalEmails)
    {
        $this->container['transactionalEmails'] = $transactionalEmails;
        return $this;
    }
    /**
     * Gets transactionalSms
     *
     * @return bool
     */
    public function getTransactionalSms()
    {
        return $this->container['transactionalSms'];
    }
    /**
     * Sets transactionalSms
     *
     * @param bool $transactionalSms Set this field to enable or disable Transactional SMS on the sub-account
     *
     * @return $this
     */
    public function setTransactionalSms($transactionalSms)
    {
        $this->container['transactionalSms'] = $transactionalSms;
        return $this;
    }
    /**
     * Gets facebookAds
     *
     * @return bool
     */
    public function getFacebookAds()
    {
        return $this->container['facebookAds'];
    }
    /**
     * Sets facebookAds
     *
     * @param bool $facebookAds Set this field to enable or disable Facebook ads on the sub-account
     *
     * @return $this
     */
    public function setFacebookAds($facebookAds)
    {
        $this->container['facebookAds'] = $facebookAds;
        return $this;
    }
    /**
     * Gets webPush
     *
     * @return bool
     */
    public function getWebPush()
    {
        return $this->container['webPush'];
    }
    /**
     * Sets webPush
     *
     * @param bool $webPush Set this field to enable or disable Web Push on the sub-account
     *
     * @return $this
     */
    public function setWebPush($webPush)
    {
        $this->container['webPush'] = $webPush;
        return $this;
    }
    /**
     * Gets meetings
     *
     * @return bool
     */
    public function getMeetings()
    {
        return $this->container['meetings'];
    }
    /**
     * Sets meetings
     *
     * @param bool $meetings Set this field to enable or disable Meetings on the sub-account
     *
     * @return $this
     */
    public function setMeetings($meetings)
    {
        $this->container['meetings'] = $meetings;
        return $this;
    }
    /**
     * Gets conversations
     *
     * @return bool
     */
    public function getConversations()
    {
        return $this->container['conversations'];
    }
    /**
     * Sets conversations
     *
     * @param bool $conversations Set this field to enable or disable Conversations on the sub-account
     *
     * @return $this
     */
    public function setConversations($conversations)
    {
        $this->container['conversations'] = $conversations;
        return $this;
    }
    /**
     * Gets crm
     *
     * @return bool
     */
    public function getCrm()
    {
        return $this->container['crm'];
    }
    /**
     * Sets crm
     *
     * @param bool $crm Set this field to enable or disable Sales CRM on the sub-account
     *
     * @return $this
     */
    public function setCrm($crm)
    {
        $this->container['crm'] = $crm;
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
