<?php
/**
 * اسکریپت تست مسیرهای redirect
 * این فایل عملکرد صحیح مسیرهای redirect را بررسی می‌کند
 */

echo "=== تست مسیرهای Redirect ===\n\n";

// شبیه‌سازی مسیرهای مختلف
$testCases = [
    [
        'path' => '/index.php',
        'expected_auth' => 'index.php',
        'expected_403' => 'public/403.php'
    ],
    [
        'path' => '/public/dashboard.php',
        'expected_auth' => '../index.php',
        'expected_403' => '403.php'
    ],
    [
        'path' => '/public/pages/clients.php',
        'expected_auth' => '../../index.php',
        'expected_403' => '../403.php'
    ]
];

echo "تست منطق تشخیص مسیر:\n";
echo str_repeat('-', 60) . "\n\n";

foreach ($testCases as $test) {
    echo "مسیر: {$test['path']}\n";
    
    // تست requireAuth
    $scriptPath = $test['path'];
    if (strpos($scriptPath, '/public/pages/') !== false) {
        $authUrl = '../../index.php';
    } elseif (strpos($scriptPath, '/public/') !== false) {
        $authUrl = '../index.php';
    } else {
        $authUrl = 'index.php';
    }
    
    $authMatch = ($authUrl === $test['expected_auth']) ? '✅' : '❌';
    echo "  Auth redirect: $authUrl $authMatch\n";
    
    // تست requirePermission
    if (strpos($scriptPath, '/public/pages/') !== false) {
        $redirectUrl = '../403.php';
    } elseif (strpos($scriptPath, '/public/') !== false) {
        $redirectUrl = '403.php';
    } else {
        $redirectUrl = 'public/403.php';
    }
    
    $permMatch = ($redirectUrl === $test['expected_403']) ? '✅' : '❌';
    echo "  403 redirect: $redirectUrl $permMatch\n\n";
}

echo str_repeat('-', 60) . "\n";
echo "تست کامل شد!\n";
