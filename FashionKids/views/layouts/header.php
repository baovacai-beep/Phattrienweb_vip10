<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . " - FashionKids" : "FashionKids - Thời Trang Trẻ Em An Toàn" ?></title>
    
    <!-- Google Fonts: Quicksand Font Than Thien Tre Em -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 4.6.2 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Bright Pastel Theme (60-30-10) -->
    <link rel="stylesheet" href="/FashionKids/public/css/style-pastel.css">
</head>
<body class="bg-milk-cream">
