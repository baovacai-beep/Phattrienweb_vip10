<?php
$page_title = "Trải Nghiệm Hộp Thoại Tư Vấn Trực Tuyến (MH 08)";
require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
?>

<div class="container my-5 text-center">
    <div class="card card-pastel p-5 shadow-sm border-0 bg-white mx-auto w-75">
        <span class="badge badge-pill badge-mint px-3 py-2 mb-3 mx-auto">MH 08: Live Chat Widget Realtime (<code>tgb_tin_nhan_tu_van</code>)</span>
        <h3 class="font-weight-bold text-slate mb-2">Hộp Thoại Tư Vấn Khách Hàng Nổi Góc Phải</h3>
        <p class="text-muted-custom mb-4">
            Widget chat trực tuyến đã được tích hợp toàn cục ở góc dưới bên phải mọi trang web và đồng bộ trực tiếp với bàn làm việc CSKH (MH 11).
        </p>
        <div>
            <button type="button" onclick="$('#customerChatWidget').collapse('show');" class="btn btn-pastel-primary px-4 py-2 font-weight-bold mr-2">
                <i class="fa-solid fa-comments mr-1"></i>Mở Hộp Thoại Chat Ngay
            </button>
            <a href="/FashionKids/views/pages/staff-chat.php" class="btn btn-pastel-sky px-4 py-2 font-weight-bold">
                <i class="fa-solid fa-headset mr-1"></i>Mở Bàn Làm Việc CSKH (MH 11)
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
