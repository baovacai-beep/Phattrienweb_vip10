<?php
/**
 * Lớp HomeController (controllers/HomeController.php)
 * Điều hướng Trang chủ (MH 01), Banner Carousel và Danh sách Sản phẩm nổi bật
 */

require_once __DIR__ . '/../core/Controller.php';

class HomeController extends Controller {
    private object $productModel;

    public function __construct() {
        $this->productModel = $this->model('ProductModel');
    }

    /**
     * Hiển thị Trang chủ FashionKids
     */
    public function index(): void {
        $featuredProducts = $this->productModel->getFeaturedProducts(4);

        $data = [
            'page_title'             => 'Trang Chủ & BST Mùa Hè 2026',
            'lnkt_featured_products' => $featuredProducts
        ];

        $this->view('home', $data);
    }
}
