# OpenAPI\Client\DefaultApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**messagesGet()**](DefaultApi.md#messagesGet) | **GET** /messages | Get Messages |
| [**messagesSendGet()**](DefaultApi.md#messagesSendGet) | **GET** /messages/send | Send a Message |


## `messagesGet()`

```php
messagesGet($status): \OpenAPI\Client\Model\MessagesGet200Response
```

Get Messages

Retrieves a list of messages.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$status = 'status_example'; // string | Filter messages by status

try {
    $result = $apiInstance->messagesGet($status);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->messagesGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **status** | **string**| Filter messages by status | [optional] |

### Return type

[**\OpenAPI\Client\Model\MessagesGet200Response**](../Model/MessagesGet200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `messagesSendGet()`

```php
messagesSendGet($text)
```

Send a Message

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$text = 'text_example'; // string | The text you want to send

try {
    $apiInstance->messagesSendGet($text);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->messagesSendGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **text** | **string**| The text you want to send | |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
