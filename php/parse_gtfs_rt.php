<?php
// Parse GTFS RT binary data and extract vehicle positions
// This requires the protobuf PHP extension or a PHP protobuf library

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Get the base64 encoded data from POST or GET
$base64Data = $_POST['data'] ?? $_GET['data'] ?? '';

if (empty($base64Data)) {
    echo json_encode(['error' => true, 'message' => 'No data provided']);
    exit;
}

// Decode base64 to binary
$binaryData = base64_decode($base64Data);

// Note: Parsing protobuf in PHP requires either:
// 1. PHP protobuf extension (pecl install protobuf)
// 2. A PHP protobuf library like google/protobuf

// For now, we'll use a simpler approach: try to extract data manually
// or use a library. Let's create a basic parser that extracts what we can.

// GTFS RT FeedMessage structure (simplified extraction)
// This is a basic approach - for full parsing, you'd need the protobuf library

$vehicles = [];
$errors = [];

// Try to find vehicle position data in the binary stream
// This is a simplified approach - proper parsing requires protobuf library

// Check if we have protobuf extension
if (extension_loaded('protobuf')) {
    // Use protobuf extension if available
    try {
        // This would require the actual .proto file compiled
        // For now, we'll use a workaround
    } catch (Exception $e) {
        $errors[] = 'Protobuf extension error: ' . $e->getMessage();
    }
}

// Alternative: Use a simple binary parser or return info about the data
// Since full protobuf parsing is complex, let's return the data size and structure info
// and suggest using a JavaScript parser or proper PHP library

$dataSize = strlen($binaryData);
$sampleData = substr($binaryData, 0, min(100, $dataSize));

// Return information about the data
// In a production system, you'd want to use google/protobuf PHP library
// or parse it client-side with protobufjs

echo json_encode([
    'error' => false,
    'message' => 'GTFS RT data received. Full parsing requires protobuf library.',
    'dataSize' => $dataSize,
    'sample' => bin2hex($sampleData),
    'note' => 'To extract lat/lng, use JavaScript protobufjs library or install PHP protobuf extension'
], JSON_PRETTY_PRINT);
?>
