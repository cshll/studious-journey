<?php
// PHP Proxy to fetch bus data from BODS API (avoids CORS issues)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Allow requests from your domain

$apiUrl = 'https://data.bus-data.dft.gov.uk/api/v1/gtfsrtdatafeed/';
$apiKey = '1237c7b08622ce2565020c16d29385317f5147d0';

// Function to make API request with different auth methods
function makeApiRequest($url, $apiKey, $authMethod = 'X-API-Key') {
    $ch = curl_init();
    
    $headers = ['Accept: application/x-protobuf, application/octet-stream, */*'];
    
    switch ($authMethod) {
        case 'X-API-Key':
            // Most common: X-API-Key header
            $headers[] = 'X-API-Key: ' . $apiKey;
            $urlWithKey = $url;
            break;
        case 'Authorization-Bearer':
            // Authorization Bearer token
            $headers[] = 'Authorization: Bearer ' . $apiKey;
            $urlWithKey = $url;
            break;
        case 'Authorization-Token':
            // Authorization Token
            $headers[] = 'Authorization: Token ' . $apiKey;
            $urlWithKey = $url;
            break;
        case 'Query-Param':
            // API key as query parameter
            $urlWithKey = $url . '?api_key=' . urlencode($apiKey);
            break;
        default:
            $urlWithKey = $url;
    }
    
    curl_setopt($ch, CURLOPT_URL, $urlWithKey);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'response' => $response,
        'httpCode' => $httpCode,
        'contentType' => $contentType,
        'error' => $error,
        'method' => $authMethod
    ];
}

// Try different authentication methods in order
$authMethods = ['X-API-Key', 'Authorization-Bearer', 'Query-Param', 'Authorization-Token'];
$result = null;

foreach ($authMethods as $method) {
    $result = makeApiRequest($apiUrl, $apiKey, $method);
    
    // If we get 200, success!
    if ($result['httpCode'] == 200) {
        break;
    }
    
    // If we get 401/403, try next method
    // If we get 406, might be format issue but try next anyway
    if ($result['httpCode'] == 401 || $result['httpCode'] == 403 || $result['httpCode'] == 406) {
        continue;
    }
    
    // For other errors, stop trying
    break;
}

$response = $result['response'];
$httpCode = $result['httpCode'];
$contentType = $result['contentType'];
$error = $result['error'];

// Handle errors
if ($error) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'cURL Error: ' . $error
    ]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    // Try to get more details about the error
    $errorDetails = '';
    if ($response) {
        // If response is JSON, decode it
        $decoded = json_decode($response, true);
        if ($decoded) {
            $errorDetails = $decoded;
        } else {
            // Otherwise show first 500 chars
            $errorDetails = substr($response, 0, 500);
        }
    }
    
    echo json_encode([
        'error' => true,
        'message' => 'API returned status code: ' . $httpCode . ' (Tried auth method: ' . ($result['method'] ?? 'unknown') . ')',
        'response' => $errorDetails,
        'contentType' => $contentType,
        'triedMethods' => $authMethods
    ], JSON_PRETTY_PRINT);
    exit;
}

// Return the data
// If it's binary/protobuf, we'll return it as base64 encoded
// If it's text/JSON, return as-is
if ($contentType && strpos($contentType, 'application/json') !== false) {
    echo $response;
} else {
    // For binary or other formats, encode as base64 and return as JSON
    echo json_encode([
        'error' => false,
        'data' => base64_encode($response),
        'format' => 'base64',
        'contentType' => $contentType ? $contentType : 'unknown'
    ]);
}
?>
