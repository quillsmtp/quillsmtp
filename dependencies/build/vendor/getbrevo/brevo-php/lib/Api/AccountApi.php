<?php

/**
 * AccountApi
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
namespace QuillSMTP\Brevo\Client\Api;

use QuillSMTP\GuzzleHttp\Client;
use QuillSMTP\GuzzleHttp\ClientInterface;
use QuillSMTP\GuzzleHttp\Exception\RequestException;
use QuillSMTP\GuzzleHttp\Psr7\MultipartStream;
use QuillSMTP\GuzzleHttp\Psr7\Request;
use QuillSMTP\GuzzleHttp\RequestOptions;
use QuillSMTP\Brevo\Client\ApiException;
use QuillSMTP\Brevo\Client\Configuration;
use QuillSMTP\Brevo\Client\HeaderSelector;
use QuillSMTP\Brevo\Client\ObjectSerializer;
/**
 * AccountApi Class Doc Comment
 *
 * @category Class
 * @package  Brevo\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class AccountApi
{
    /**
     * @var ClientInterface
     */
    protected $client;
    /**
     * @var Configuration
     */
    protected $config;
    /**
     * @var HeaderSelector
     */
    protected $headerSelector;
    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     */
    public function __construct(ClientInterface $client = null, Configuration $config = null, HeaderSelector $selector = null)
    {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
    }
    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
    /**
     * Operation getAccount
     *
     * Get your account information, plan and credits details
     *
     *
     * @throws \Brevo\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Brevo\Client\Model\GetAccount
     */
    public function getAccount()
    {
        list($response) = $this->getAccountWithHttpInfo();
        return $response;
    }
    /**
     * Operation getAccountWithHttpInfo
     *
     * Get your account information, plan and credits details
     *
     *
     * @throws \Brevo\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Brevo\Client\Model\GetAccount, HTTP status code, HTTP response headers (array of strings)
     */
    public function getAccountWithHttpInfo()
    {
        $returnType = 'QuillSMTP\\Brevo\\Client\\Model\\GetAccount';
        $request = $this->getAccountRequest();
        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException("[{$e->getCode()}] {$e->getMessage()}", $e->getCode(), $e->getResponse() ? $e->getResponse()->getHeaders() : null, $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null);
            }
            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(\sprintf('[%d] Error connecting to the API (%s)', $statusCode, $request->getUri()), $statusCode, $response->getHeaders(), $response->getBody());
            }
            $responseBody = $response->getBody();
            if ($returnType === '\\SplFileObject') {
                $content = $responseBody;
                //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if ($returnType !== 'string') {
                    $content = \json_decode($content);
                }
            }
            return [ObjectSerializer::deserialize($content, $returnType, []), $response->getStatusCode(), $response->getHeaders()];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize($e->getResponseBody(), 'QuillSMTP\\Brevo\\Client\\Model\\GetAccount', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }
    /**
     * Operation getAccountAsync
     *
     * Get your account information, plan and credits details
     *
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getAccountAsync()
    {
        return $this->getAccountAsyncWithHttpInfo()->then(function ($response) {
            return $response[0];
        });
    }
    /**
     * Operation getAccountAsyncWithHttpInfo
     *
     * Get your account information, plan and credits details
     *
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getAccountAsyncWithHttpInfo()
    {
        $returnType = 'QuillSMTP\\Brevo\\Client\\Model\\GetAccount';
        $request = $this->getAccountRequest();
        return $this->client->sendAsync($request, $this->createHttpClientOption())->then(function ($response) use($returnType) {
            $responseBody = $response->getBody();
            if ($returnType === '\\SplFileObject') {
                $content = $responseBody;
                //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if ($returnType !== 'string') {
                    $content = \json_decode($content);
                }
            }
            return [ObjectSerializer::deserialize($content, $returnType, []), $response->getStatusCode(), $response->getHeaders()];
        }, function ($exception) {
            $response = $exception->getResponse();
            $statusCode = $response->getStatusCode();
            throw new ApiException(\sprintf('[%d] Error connecting to the API (%s)', $statusCode, $exception->getRequest()->getUri()), $statusCode, $response->getHeaders(), $response->getBody());
        });
    }
    /**
     * Create request for operation 'getAccount'
     *
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function getAccountRequest()
    {
        $resourcePath = '/account';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = \false;
        // body params
        $_tempBody = null;
        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(['application/json']);
        } else {
            $headers = $this->headerSelector->selectHeaders(['application/json'], ['application/json']);
        }
        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            if ($headers['Content-Type'] === 'application/json') {
                // \stdClass has no __toString(), so we should encode it manually
                if ($httpBody instanceof \stdClass) {
                    $httpBody = \QuillSMTP\GuzzleHttp\json_encode($httpBody);
                }
                // array has no __toString(), so we should encode it manually
                if (\is_array($httpBody)) {
                    $httpBody = \QuillSMTP\GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($httpBody));
                }
            }
        } elseif (\count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = ['name' => $formParamName, 'contents' => $formParamValue];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \QuillSMTP\GuzzleHttp\json_encode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = \QuillSMTP\GuzzleHttp\Psr7\Query::build($formParams);
            }
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $headers['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('partner-key');
        if ($apiKey !== null) {
            $headers['partner-key'] = $apiKey;
        }
        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }
        $headers = \array_merge($defaultHeaders, $headerParams, $headers);
        $query = \QuillSMTP\GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request('GET', $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''), $headers, $httpBody);
    }
    /**
     * Operation getAccountActivity
     *
     * Get user activity logs
     *
     * @param  string $startDate Mandatory if endDate is used. Enter start date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. Additionally, you can retrieve activity logs from the past 12 months from the date of your search. (optional)
     * @param  string $endDate Mandatory if startDate is used. Enter end date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. (optional)
     * @param  int $limit Number of documents per page (optional, default to 10)
     * @param  int $offset Index of the first document in the page. (optional, default to 0)
     *
     * @throws \Brevo\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Brevo\Client\Model\GetAccountActivity
     */
    public function getAccountActivity($startDate = null, $endDate = null, $limit = '10', $offset = '0')
    {
        list($response) = $this->getAccountActivityWithHttpInfo($startDate, $endDate, $limit, $offset);
        return $response;
    }
    /**
     * Operation getAccountActivityWithHttpInfo
     *
     * Get user activity logs
     *
     * @param  string $startDate Mandatory if endDate is used. Enter start date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. Additionally, you can retrieve activity logs from the past 12 months from the date of your search. (optional)
     * @param  string $endDate Mandatory if startDate is used. Enter end date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. (optional)
     * @param  int $limit Number of documents per page (optional, default to 10)
     * @param  int $offset Index of the first document in the page. (optional, default to 0)
     *
     * @throws \Brevo\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Brevo\Client\Model\GetAccountActivity, HTTP status code, HTTP response headers (array of strings)
     */
    public function getAccountActivityWithHttpInfo($startDate = null, $endDate = null, $limit = '10', $offset = '0')
    {
        $returnType = 'QuillSMTP\\Brevo\\Client\\Model\\GetAccountActivity';
        $request = $this->getAccountActivityRequest($startDate, $endDate, $limit, $offset);
        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException("[{$e->getCode()}] {$e->getMessage()}", $e->getCode(), $e->getResponse() ? $e->getResponse()->getHeaders() : null, $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null);
            }
            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(\sprintf('[%d] Error connecting to the API (%s)', $statusCode, $request->getUri()), $statusCode, $response->getHeaders(), $response->getBody());
            }
            $responseBody = $response->getBody();
            if ($returnType === '\\SplFileObject') {
                $content = $responseBody;
                //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if ($returnType !== 'string') {
                    $content = \json_decode($content);
                }
            }
            return [ObjectSerializer::deserialize($content, $returnType, []), $response->getStatusCode(), $response->getHeaders()];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize($e->getResponseBody(), 'QuillSMTP\\Brevo\\Client\\Model\\GetAccountActivity', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize($e->getResponseBody(), 'QuillSMTP\\Brevo\\Client\\Model\\ErrorModel', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }
    /**
     * Operation getAccountActivityAsync
     *
     * Get user activity logs
     *
     * @param  string $startDate Mandatory if endDate is used. Enter start date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. Additionally, you can retrieve activity logs from the past 12 months from the date of your search. (optional)
     * @param  string $endDate Mandatory if startDate is used. Enter end date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. (optional)
     * @param  int $limit Number of documents per page (optional, default to 10)
     * @param  int $offset Index of the first document in the page. (optional, default to 0)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getAccountActivityAsync($startDate = null, $endDate = null, $limit = '10', $offset = '0')
    {
        return $this->getAccountActivityAsyncWithHttpInfo($startDate, $endDate, $limit, $offset)->then(function ($response) {
            return $response[0];
        });
    }
    /**
     * Operation getAccountActivityAsyncWithHttpInfo
     *
     * Get user activity logs
     *
     * @param  string $startDate Mandatory if endDate is used. Enter start date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. Additionally, you can retrieve activity logs from the past 12 months from the date of your search. (optional)
     * @param  string $endDate Mandatory if startDate is used. Enter end date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. (optional)
     * @param  int $limit Number of documents per page (optional, default to 10)
     * @param  int $offset Index of the first document in the page. (optional, default to 0)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getAccountActivityAsyncWithHttpInfo($startDate = null, $endDate = null, $limit = '10', $offset = '0')
    {
        $returnType = 'QuillSMTP\\Brevo\\Client\\Model\\GetAccountActivity';
        $request = $this->getAccountActivityRequest($startDate, $endDate, $limit, $offset);
        return $this->client->sendAsync($request, $this->createHttpClientOption())->then(function ($response) use($returnType) {
            $responseBody = $response->getBody();
            if ($returnType === '\\SplFileObject') {
                $content = $responseBody;
                //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if ($returnType !== 'string') {
                    $content = \json_decode($content);
                }
            }
            return [ObjectSerializer::deserialize($content, $returnType, []), $response->getStatusCode(), $response->getHeaders()];
        }, function ($exception) {
            $response = $exception->getResponse();
            $statusCode = $response->getStatusCode();
            throw new ApiException(\sprintf('[%d] Error connecting to the API (%s)', $statusCode, $exception->getRequest()->getUri()), $statusCode, $response->getHeaders(), $response->getBody());
        });
    }
    /**
     * Create request for operation 'getAccountActivity'
     *
     * @param  string $startDate Mandatory if endDate is used. Enter start date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. Additionally, you can retrieve activity logs from the past 12 months from the date of your search. (optional)
     * @param  string $endDate Mandatory if startDate is used. Enter end date in UTC date (YYYY-MM-DD) format to filter the activity in your account. Maximum time period that can be selected is one month. (optional)
     * @param  int $limit Number of documents per page (optional, default to 10)
     * @param  int $offset Index of the first document in the page. (optional, default to 0)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function getAccountActivityRequest($startDate = null, $endDate = null, $limit = '10', $offset = '0')
    {
        if ($limit !== null && $limit > 100) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling AccountApi.getAccountActivity, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling AccountApi.getAccountActivity, must be bigger than or equal to 1.');
        }
        $resourcePath = '/organization/activities';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = \false;
        // query params
        if ($startDate !== null) {
            $queryParams['startDate'] = ObjectSerializer::toQueryValue($startDate);
        }
        // query params
        if ($endDate !== null) {
            $queryParams['endDate'] = ObjectSerializer::toQueryValue($endDate);
        }
        // query params
        if ($limit !== null) {
            $queryParams['limit'] = ObjectSerializer::toQueryValue($limit);
        }
        // query params
        if ($offset !== null) {
            $queryParams['offset'] = ObjectSerializer::toQueryValue($offset);
        }
        // body params
        $_tempBody = null;
        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(['application/json']);
        } else {
            $headers = $this->headerSelector->selectHeaders(['application/json'], ['application/json']);
        }
        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            if ($headers['Content-Type'] === 'application/json') {
                // \stdClass has no __toString(), so we should encode it manually
                if ($httpBody instanceof \stdClass) {
                    $httpBody = \QuillSMTP\GuzzleHttp\json_encode($httpBody);
                }
                // array has no __toString(), so we should encode it manually
                if (\is_array($httpBody)) {
                    $httpBody = \QuillSMTP\GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($httpBody));
                }
            }
        } elseif (\count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = ['name' => $formParamName, 'contents' => $formParamValue];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \QuillSMTP\GuzzleHttp\json_encode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = \QuillSMTP\GuzzleHttp\Psr7\Query::build($formParams);
            }
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $headers['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('partner-key');
        if ($apiKey !== null) {
            $headers['partner-key'] = $apiKey;
        }
        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }
        $headers = \array_merge($defaultHeaders, $headerParams, $headers);
        $query = \QuillSMTP\GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request('GET', $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''), $headers, $httpBody);
    }
    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = \fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }
        return $options;
    }
}
