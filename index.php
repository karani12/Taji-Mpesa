<?php

$logfile = 'log.txt';  

/**
 * Log data to the terminal and to a file
 */
function logData($data) {
    global $logfile;
    echo $data . "\n";
    file_put_contents($logfile, $data . "\n", FILE_APPEND);
}

/**
 * Handle incoming request
 */
function handleRequest() {
    // Get the raw POST data
    $rawData = file_get_contents("php://input");
    $json = json_decode($rawData, true);
    
    // Check if JSON is valid
    if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo "Invalid JSON";
        logData("Invalid JSON received: " . $rawData);
        return;
    }

    // Process the request based on the path
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($uri === '/validation' || $uri === '/confirmation') {
        logData("Received on {$uri}: " . $rawData);
        echo "Data logged as {$uri}";
    } else {
        http_response_code(404);
        echo "Not Found";
        logData("404 Not Found: " . $uri);
    }
}

handleRequest();
