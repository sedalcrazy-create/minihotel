<?php
require __DIR__ . '/vendor/autoload.php';

echo ".env file exists: " . (file_exists(__DIR__ . '/.env') ? 'YES' : 'NO') . PHP_EOL;
echo ".env file readable: " . (is_readable(__DIR__ . '/.env') ? 'YES' : 'NO') . PHP_EOL;
echo PHP_EOL;

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "Dotenv loaded successfully!" . PHP_EOL;
} catch (Exception $e) {
    echo "Dotenv error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;
echo "ENV variables starting with APP_:" . PHP_EOL;
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'APP_') === 0) {
        echo "$key = " . (strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value) . PHP_EOL;
    }
}

echo PHP_EOL;
echo "APP_KEY from getenv: " . (getenv('APP_KEY') ?: 'NOT FOUND') . PHP_EOL;
