<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Configre your Cerebras API key inside this file
$config = require 'config.php';

// Set your Cerebras API key
$apiKey = $config['cerebras_api_key'] ?? null;

// Checking if the API key is set in the config file
if (!$apiKey) {
    die('Cerebras API key is not set in the configuration.');
}

// Set the API endpoint and model
$apiEndpoint = 'https://api.cerebras.ai';
$model = 'llama3.1-70b';

// Create a Guzzle client
$client = new Client([
    'base_uri' => $apiEndpoint,
    'headers' => [
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ],
]);

// Function to log messages to a file
function logToFile($message) {
    $logFile = __DIR__ . '/infinity_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Function to generate a unique identity for the user
function generateUserId() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = uniqid();
    }
    return $_SESSION['user_id'];
}

// Function to generate a web page based on user input
function generateWebPage($prompt, $userId) {
    global $client, $model;

    // Create a system message
    $systemMessage = [
        'role' => 'system',
        'content' => 'You will create based on what the user wants a full css inside of html with no commentary,and instead of images create an image shape with CSS text and define the shape. The CSS theme should reflect the category of the site. Make as much content as possible. Do not use lorem ipsum but instead predict the content or speculate.',
    ];

    $maxRetries = 5; // Maximum number of retries
    $retryDelay = 4; // Delay in seconds before retrying
    $attempt = 0;
    $errors = [];

    while ($attempt < $maxRetries) {
        try {
            logToFile("Attempt " . ($attempt + 1) . " to generate webpage for prompt: " . $prompt);

            // Create a new chat completion request
            $response = $client->post('v1/chat/completions', [
                'json' => [
                    'messages' => [
                        $systemMessage,
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'model' => $model,
                ],
            ]);

            $responseBody = $response->getBody()->getContents();

            // Get the generated HTML and CSS
            $generatedContent = json_decode($responseBody, true);

            // Check if the response contains the expected structure
            if (!isset($generatedContent['choices'][0]['message']['content'])) {
                throw new Exception('Unexpected API response format: ' . $responseBody);
            }

            $htmlContent = $generatedContent['choices'][0]['message']['content'];

            // Remove Markdown code block delimiter symbols and comments before and after HTML
            $htmlContent = preg_replace_callback('/^.*?```[\s\S]*?\n([\s\S]*?)\n```[\s\S]*$/ms', function($matches) {
                return trim($matches[1]);
            }, $htmlContent);

            // Create a new file with a unique name based on the current date and time
            $fileName = date('Y-m-d-H-i-s') . '.html';
            $filePath = 'generated-pages/' . $userId . '/' . $fileName;

            // Create the directory for the user if it doesn't exist
            if (!is_dir('generated-pages/' . $userId)) {
                mkdir('generated-pages/' . $userId, 0777, true);
            }

            // Write the generated HTML and CSS to the file
            if (file_put_contents($filePath, $htmlContent) === false) {
                throw new Exception('Failed to write the generated content to the file.');
            }

            logToFile("Successfully generated webpage: " . $filePath);
            // Return the URL of the newly generated page
            return 'generated-pages/' . $userId . '/' . $fileName;

        } catch (RequestException $e) {
            $errorMessage = 'Error generating web page (Attempt ' . ($attempt + 1) . '): ' . $e->getMessage();
            if ($e->hasResponse()) {
                $errorMessage .= ' Response: ' . $e->getResponse()->getBody();
            }
            logToFile($errorMessage);
            $errors[] = $errorMessage;
        } catch (Exception $e) {
            $errorMessage = 'Error generating web page (Attempt ' . ($attempt + 1) . '): ' . $e->getMessage();
            logToFile($errorMessage);
            $errors[] = $errorMessage;
        }

        $attempt++;
        if ($attempt >= $maxRetries) {
            return ['error' => true, 'message' => 'Error generating the page. Please try again later.', 'details' => $errors];
        }
        sleep($retryDelay); // Wait before retrying
    }
}

// Function to delete old web pages
function deleteOldPages() {
    $dir = 'generated-pages/';
    $files = scandir($dir);

    // List of files to exclude from deletion
    $excludedFiles = ['index.html', 'index.php', '.htaccess', 'config.php', 'infinity_log.txt'];

    foreach ($files as $file) {
        if (is_dir($dir . $file)) {
            $userDir = $dir . $file;
            $userFiles = scandir($userDir);

            foreach ($userFiles as $userFile) {
                // Exclude specific files from deletion
                if (!in_array($userFile, $excludedFiles) && is_file($userDir . '/' . $userFile)) {
                    $fileTime = filemtime($userDir . '/' . $userFile);
                    if (time() - $fileTime > 600) { // Delete files older than 10 minutes
                        unlink($userDir . '/' . $userFile);
                    }
                }
            }
        }
    }
}

// Start the session
session_start();

// Generate a unique identity for the user
$userId = generateUserId();

// Rate limiting: Store the last request time in the session
if (!isset($_SESSION['last_request_time'])) {
    $_SESSION['last_request_time'] = time();
}

$current_time = time();
$rate_limit_seconds = 5; // Limit to one request every 5 seconds

// Handle user input
$errorMessage = '';
$errorDetails = [];
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check rate limiting
    if ($current_time - $_SESSION['last_request_time'] < $rate_limit_seconds) {
        $errorMessage = 'Please wait 5 Seconds before making another request.';
    } else {
        // Validate and sanitize user input
        $prompt = isset($_POST['prompt']) ? htmlspecialchars($_POST['prompt'], ENT_QUOTES, 'UTF-8') : '';
        logToFile("Received prompt: " . $prompt);

        try {
            // Generate a new web page based on the user's input
            $result = generateWebPage($prompt, $userId);
            logToFile("generateWebPage result: " . print_r($result, true));

            if (is_array($result) && isset($result['error'])) {
                $errorMessage = $result['message'];
                $errorDetails = $result['details'];
                logToFile("Error occurred: " . $errorMessage);
            } elseif (is_string($result)) {
                // Update the last request time
                $_SESSION['last_request_time'] = $current_time;
                logToFile("Redirecting to: " . $result);

                // Redirect the user to the newly generated page
                header('Location: ' . $result);
                exit;
            } else {
                $errorMessage = 'An unexpected result was returned.';
                logToFile("Unexpected result: " . print_r($result, true));
            }
        } catch (Exception $e) {
            $errorMessage = 'An unexpected error occurred: ' . $e->getMessage();
            logToFile("Exception caught: " . $e->getMessage());
        }
    }
}

// Delete old web pages
deleteOldPages();

// Display the prompt entry box and submit button
?>
<!DOCTYPE html>
<html>
<head>
    <title>Infinity Internet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: space-between;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .search-box {
            width: 60%;
            height: 25px;
            padding: 10px;
            font-size: 18px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .search-box:focus {
            border-color: #aaa;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .search-button {
            width: 100px;
            height: 40px;
            padding: 10px;
            font-size: 18px;
            background-color: #808080;
            color: #fff;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .search-button:hover {
            background-color: #C0C0C0;
        }

        .loading-indicator {
            display: none;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .loading-indicator span {
            animation: loading 1s infinite;
        }

        @keyframes loading {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f2f2f2;
            border-top: 1px solid #ccc;
        }

        .error-message {
            color: red;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">Infinity Internet 
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
            <?php if (!empty($errorDetails)): ?>
                <div class="error-details">
                    <h3>Error Details:</h3>
                    <ul>
                        <?php foreach ($errorDetails as $detail): ?>
                            <li><?php echo htmlspecialchars($detail); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <div class="footer">
        <form action="" method="post">
            <input type="text" name="prompt" class="search-box" placeholder="Describe your website..." required>
            <button type="submit" class="search-button">To Infinity</button>
            
        </form>
    </div>

    <script>
        const form = document.querySelector('form');
        const loadingIndicator = document.getElementById('loading-indicator');

        form.addEventListener('submit', () => {
            loadingIndicator.style.display = 'block';
        });
    </script>
</body>
</html>
