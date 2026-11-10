<?php
if (ob_get_level() === 0) {
    ob_start();
}
if (session_status() === PHP_SESSION_NONE) {
    $savePath = session_save_path();
    $canWrite = false;
    if (!empty($savePath) && is_dir($savePath)) {
        $probe = @tempnam($savePath, 'fk_');
        if ($probe !== false) {
            @unlink($probe);
            $canWrite = true;
        }
    }
    if (!$canWrite) {
        $temp = sys_get_temp_dir();
        if (is_dir($temp)) {
            @session_save_path($temp);
        }
    }
    @session_start();
}
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../../core/Rbac.php';
Rbac::syncSession();
$fk_rbac_access_granted = Rbac::enforceCurrentViewAccess();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>">
    <meta name="fk-endpoint" content="<?= htmlspecialchars(APP_ENDPOINT) ?>">
    <meta name="fk-user-role" content="<?= htmlspecialchars(Rbac::currentRole()) ?>">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . " - FashionKids" : "FashionKids - Thời Trang Trẻ Em An Toàn" ?></title>
    
    <!-- Favicon Logo Nhom 10 FashionKids (Hien thi tren Tab & Thanh tim kiem trinh duyet) -->
    <link rel="icon" type="image/jpeg" href="/FashionKids/images/logo-fashionkids.jpg">
    <link rel="shortcut icon" type="image/jpeg" href="/FashionKids/images/logo-fashionkids.jpg">
    <link rel="apple-touch-icon" href="/FashionKids/images/logo-fashionkids.jpg">
    
    <!-- Google Fonts: Quicksand Font Than Thien Tre Em -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 4.6.2 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Bright Pastel Theme (60-30-10) -->
    <?php $cssVer = @filemtime(__DIR__ . '/../../public/css/style-pastel.css') ?: '20261110'; ?>
    <link rel="stylesheet" href="/FashionKids/public/css/style-pastel.css?v=<?= $cssVer ?>">
</head>
<body class="bg-milk-cream" data-user-role="<?= htmlspecialchars(Rbac::currentRole()) ?>">
