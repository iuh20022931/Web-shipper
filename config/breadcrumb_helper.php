<?php
/**
 * Breadcrumb Helper - Tạo breadcrumb navigation cho admin pages
 */

function getBreadcrumb($current_file) {
    // Lấy tên file hiện tại
    $page = basename($current_file, '.php');
    
    // Định nghĩa breadcrumb cho từng trang
    $breadcrumbs = [
        'admin_stats' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php']
        ],
        'orders_manage' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Quản lý', 'url' => '#'],
            ['name' => 'Đơn hàng', 'url' => '']
        ],
        'users_manage' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Quản lý', 'url' => '#'],
            ['name' => 'Người dùng', 'url' => '']
        ],
        'services_manage' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Quản lý', 'url' => '#'],
            ['name' => 'Dịch vụ', 'url' => '']
        ],
        'testimonials_manage' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Nội dung', 'url' => '#'],
            ['name' => 'Đánh giá', 'url' => '']
        ],
        'faq_manage' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Nội dung', 'url' => '#'],
            ['name' => 'FAQ', 'url' => '']
        ],
        'contact_manage' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Nội dung', 'url' => '#'],
            ['name' => 'Liên hệ', 'url' => '']
        ],
        'admin_settings' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Cài đặt', 'url' => '']
        ],
        'profile' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Tài khoản', 'url' => '']
        ],
        'order_detail' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Quản lý', 'url' => '#'],
            ['name' => 'Đơn hàng', 'url' => 'orders_manage.php'],
            ['name' => 'Chi tiết', 'url' => '']
        ],
        'user_form' => [
            ['name' => 'Dashboard', 'url' => 'admin_stats.php'],
            ['name' => 'Quản lý', 'url' => '#'],
            ['name' => 'Người dùng', 'url' => 'users_manage.php'],
            ['name' => 'Chỉnh sửa', 'url' => '']
        ]
    ];
    
    return $breadcrumbs[$page] ?? [['name' => 'Dashboard', 'url' => 'admin_stats.php']];
}

function renderBreadcrumb($current_file) {
    $items = getBreadcrumb($current_file);
    $html = '<nav class="breadcrumb" aria-label="breadcrumb"><ol class="breadcrumb-list">';
    
    $total = count($items);
    foreach ($items as $index => $item) {
        $isLast = ($index === $total - 1);
        
        if ($isLast || empty($item['url'])) {
            $html .= '<li class="breadcrumb-item active">' . htmlspecialchars($item['name']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['name']) . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    return $html;
}
?>
