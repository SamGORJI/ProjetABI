<?php
/**
 * اسکریپت تست login و session
 */

require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'includes/auth.php';

echo "=== تست Login و Session ===\n\n";

// تست 1: اتصال به دیتابیس
echo "1. تست اتصال به دیتابیس...\n";
try {
    $db = Database::getInstance()->getConnection();
    echo "   ✅ اتصال موفق\n\n";
} catch (Exception $e) {
    echo "   ❌ خطا: " . $e->getMessage() . "\n\n";
    exit(1);
}

// تست 2: بررسی کاربر admin
echo "2. بررسی کاربر admin...\n";
$userModel = new User();
$user = $userModel->login('admin@abi.fr', 'admin123');

if ($user) {
    echo "   ✅ کاربر پیدا شد\n";
    echo "   - ID: {$user['id']}\n";
    echo "   - نام: {$user['prenom']} {$user['nom']}\n";
    echo "   - نقش: {$user['role']}\n\n";
} else {
    echo "   ❌ کاربر پیدا نشد یا رمز اشتباه است\n\n";
    exit(1);
}

// تست 3: شبیه‌سازی login
echo "3. شبیه‌سازی login...\n";
connecterUtilisateur($user);
echo "   ✅ Session تنظیم شد\n";
echo "   - Session ID: " . session_id() . "\n";
echo "   - Utilisateur ID: " . $_SESSION['utilisateur_id'] . "\n";
echo "   - Role: " . $_SESSION['utilisateur_role'] . "\n\n";

// تست 4: بررسی estConnecte
echo "4. بررسی estConnecte()...\n";
if (estConnecte()) {
    echo "   ✅ کاربر متصل است\n\n";
} else {
    echo "   ❌ کاربر متصل نیست\n\n";
}

// تست 5: بررسی Permission
echo "5. بررسی Permission...\n";
require_once 'classes/Permission.php';
$perm = Permission::current();
if ($perm) {
    echo "   ✅ Permission ایجاد شد\n";
    echo "   - می‌تواند clients ببیند: " . ($perm->canView('clients') ? 'بله' : 'خیر') . "\n";
    echo "   - می‌تواند clients ایجاد کند: " . ($perm->canCreate('clients') ? 'بله' : 'خیر') . "\n\n";
} else {
    echo "   ❌ Permission ایجاد نشد\n\n";
}

echo "=== تست کامل شد ===\n";
