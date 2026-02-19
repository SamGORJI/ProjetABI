<?php
/**
 * اسکریپت بررسی جامع مسیرها
 * این اسکریپت تمام فایل‌های PHP را بررسی می‌کند
 */

echo "=== بررسی جامع مسیرهای پروژه ===\n\n";

$errors = [];
$warnings = [];

// 1. بررسی index.php
echo "1. بررسی index.php...\n";
if (file_exists('index.php')) {
    $content = file_get_contents('index.php');
    if (strpos($content, "header('Location: public/dashboard.php')") !== false) {
        echo "   ✅ Redirect به public/dashboard.php صحیح است\n";
    } else {
        $errors[] = "index.php: redirect به dashboard اشتباه است";
        echo "   ❌ Redirect اشتباه است\n";
    }
} else {
    $errors[] = "index.php پیدا نشد";
}

// 2. بررسی public/dashboard.php
echo "\n2. بررسی public/dashboard.php...\n";
if (file_exists('public/dashboard.php')) {
    $content = file_get_contents('public/dashboard.php');
    
    // بررسی require ها
    $requiredPaths = [
        "'../config/config.php'",
        "'../classes/Database.php'",
        "'../classes/User.php'",
        "'../includes/auth.php'"
    ];
    
    $allCorrect = true;
    foreach ($requiredPaths as $path) {
        if (strpos($content, $path) !== false) {
            echo "   ✅ require $path موجود است\n";
        } else {
            $errors[] = "public/dashboard.php: require $path پیدا نشد";
            echo "   ❌ require $path پیدا نشد\n";
            $allCorrect = false;
        }
    }
    
    // بررسی CSS
    if (strpos($content, 'href="../assets/css/style.css"') !== false) {
        echo "   ✅ مسیر CSS صحیح است\n";
    } else {
        $errors[] = "public/dashboard.php: مسیر CSS اشتباه است";
        echo "   ❌ مسیر CSS اشتباه است\n";
    }
} else {
    $errors[] = "public/dashboard.php پیدا نشد";
}

// 3. بررسی public/pages/*.php
echo "\n3. بررسی فایل‌های public/pages/...\n";
$pagesFiles = ['clients.php', 'produits.php', 'commandes.php', 'projets.php'];

foreach ($pagesFiles as $file) {
    $path = "public/pages/$file";
    echo "\n   بررسی $file:\n";
    
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // باید ../../ داشته باشد
        if (strpos($content, "'../../config/config.php'") !== false) {
            echo "      ✅ مسیر config صحیح است (../../)\n";
        } else if (strpos($content, "'../config/config.php'") !== false) {
            $errors[] = "$file: مسیر config باید ../../ باشد نه ../";
            echo "      ❌ مسیر config اشتباه است (../ به جای ../../)\n";
        } else {
            $errors[] = "$file: مسیر config پیدا نشد";
            echo "      ❌ مسیر config پیدا نشد\n";
        }
        
        // بررسی CSS
        if (strpos($content, 'href="../../assets/css/style.css"') !== false) {
            echo "      ✅ مسیر CSS صحیح است (../../)\n";
        } else if (strpos($content, 'href="../assets/css/style.css"') !== false) {
            $errors[] = "$file: مسیر CSS باید ../../ باشد نه ../";
            echo "      ❌ مسیر CSS اشتباه است (../ به جای ../../)\n";
        } else {
            $warnings[] = "$file: مسیر CSS پیدا نشد";
            echo "      ⚠️  مسیر CSS پیدا نشد\n";
        }
    } else {
        $errors[] = "$path پیدا نشد";
        echo "      ❌ فایل پیدا نشد\n";
    }
}

// 4. بررسی public/logout.php
echo "\n4. بررسی public/logout.php...\n";
if (file_exists('public/logout.php')) {
    $content = file_get_contents('public/logout.php');
    if (strpos($content, "'../config/config.php'") !== false) {
        echo "   ✅ مسیر config صحیح است\n";
    } else {
        $errors[] = "public/logout.php: مسیر config اشتباه است";
    }
    
    if (strpos($content, "header('Location: ../index.php')") !== false) {
        echo "   ✅ redirect به ../index.php صحیح است\n";
    } else {
        $errors[] = "public/logout.php: redirect اشتباه است";
    }
} else {
    $errors[] = "public/logout.php پیدا نشد";
}

// 5. بررسی public/403.php
echo "\n5. بررسی public/403.php...\n";
if (file_exists('public/403.php')) {
    $content = file_get_contents('public/403.php');
    if (strpos($content, 'href="../assets/css/style.css"') !== false) {
        echo "   ✅ مسیر CSS صحیح است\n";
    } else {
        $errors[] = "public/403.php: مسیر CSS اشتباه است";
    }
} else {
    $errors[] = "public/403.php پیدا نشد";
}

// خلاصه
echo "\n" . str_repeat('=', 60) . "\n";
echo "خلاصه:\n";
echo "تعداد خطاها: " . count($errors) . "\n";
echo "تعداد هشدارها: " . count($warnings) . "\n\n";

if (count($errors) > 0) {
    echo "❌ خطاها:\n";
    foreach ($errors as $error) {
        echo "   - $error\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "⚠️  هشدارها:\n";
    foreach ($warnings as $warning) {
        echo "   - $warning\n";
    }
    echo "\n";
}

if (count($errors) === 0 && count($warnings) === 0) {
    echo "✅ همه مسیرها صحیح هستند!\n";
    exit(0);
} else {
    exit(1);
}
