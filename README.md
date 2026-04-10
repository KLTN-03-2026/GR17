# Đồ Án Tốt Nghiệp - Nền Tảng Du Lịch & Lên Kế Hoạch Trực Tuyến (Nhóm GR17)

Chào mừng bạn đến với Repository chính thức của **Đồ Án Tốt Nghiệp Nhóm GR17**. Đây là hệ thống nền tảng du lịch thông minh, kết nối khách du lịch, đối tác cung cấp dịch vụ và quản trị viên, được tích hợp trí tuệ nhân tạo (AI) để gợi ý và tối ưu hóa lịch trình chuyến đi một cách cá nhân hóa.

---

## 🌟 Chức Năng Chính (Core Features)

Hệ thống được chia làm 3 phân hệ chính phục vụ các đối tượng người dùng khác nhau:

### 1. Phân Hệ Khách Hàng (Customer)
- **Lên Tự Động Kế Hoạch Bằng AI:** Tạo lịch trình du lịch thông minh bằng trí tuệ nhân tạo (Gợi ý địa điểm, ước tính chi phí, gợi ý thời gian).
- **Quản Lý Nhóm Du Lịch:** Thêm thành viên, chia sẻ quyền lên kế hoạch và quản lý chi tiêu chung.
- **Tìm Kiếm & Đặt Tour/Địa Điểm:** Mua dịch vụ du lịch, đặt vé, đặt phòng từ các đối tác.
- **Đánh Giá & Phản Hồi:** Xem và viết đánh giá cho các địa điểm, tour tuyến đã trải nghiệm.
- **Quản lý Cá Nhân:** Quản lý lịch sử đơn hàng, địa điểm yêu thích, hồ sơ người dùng.

### 2. Phân Hệ Đối Tác Cung Cấp Dịch Vụ (Partner)
- **Quản Lý Tour & Địa Điểm:** Tạo và chỉnh sửa các Tour, điểm đến, dịch vụ để cung cấp cho khách hàng.
- **Quản Lý Đơn Hàng:** Xét duyệt, theo dõi trạng thái các giao dịch đặt chỗ của hành khách.
- **Đối Soát Doanh Thu:** Quản lý và đối soát các khoản hoa hồng với Admin qua biến động doanh thu.
- **Báo Cáo Thống Kê:** Xem biểu đồ lượng khách đặt, doanh thu dịch vụ theo tháng/quý.

### 3. Phân Hệ Quản Trị Viên (Admin)
- **Kiểm Duyệt Nội Dung:** Duyệt tài khoản đối tác, duyệt thông tin Tour/Địa điểm trước khi hiển thị (Moderation).
- **Phân Quyền Hệ Thống:** Phân quyền và cấp vai trò chi tiết cho các nhân sự ban quản trị.
- **Quản Lý Cấu Hình:** Tinh chỉnh cấu hình AI (prompt, API key), quản lý danh mục, cấu hình ngày nghỉ lễ.
- **Thống Kê Toàn Hệ Thống:** Giám sát dòng tiền, tổng doanh thu, hóa đơn và các chỉ số tăng trưởng.

---

## 🛠️ Công Nghệ Sử Dụng (Tech Stack)

### Backend (Laravel) - Thư mục `/BE`
- **Framework:** Laravel 11.x (PHP 8.2+)
- **Database:** Relational Database (MySQL/PostgreSQL) 
- **Authentication:** Laravel Sanctum (Token-based Auth)
- **AI Integration:** Tích hợp với dịch vụ AI (Gemini/OpenAI) cho việc sinh lịch trình tự động.
- **Tính năng nổi bật:** Queue & Jobs (gửi email ngầm), Route Caching, Seeders/Factories, API RESTful chuẩn mực.

### Frontend (Vue.js) - Thư mục `/FE`
- **Framework:** Vue 3 với Composition API ( `<script setup>` ).
- **Build Tool:** Vite (Hot Module Replacement cực nhanh).
- **Router & State Management:** Vue Router & Quản lý trạng thái nội bộ.
- **Thiết Kế Giao Diện:** Native CSS (Flexbox, Grid), Bootstrap template, Custom components. Giao diện có tính đáp ứng (Responsive) tốt.
- **Tương tác API:** Axios config cho phân quyền, xử lý Token động.

---

## 🚀 Hướng Dẫn Cài Đặt (Installation Guide)

Để khởi chạy dự án tại máy tính cá nhân (Local Environment), vui lòng làm theo các bước dưới đây.

### 1. Cài đặt Backend
1. Di chuyển vào thư mục BE: `cd BE`
2. Cài đặt các thư viện PHP: `composer install`
3. Tạo file cấu hình môi trường: `cp .env.example .env` (Cấu hình lại các thông số kết nối Database bên trong file `.env`)
4. Sinh key mã hóa cho Laravel: `php artisan key:generate`
5. Khởi tạo Database (Migrations & Seeders): `php artisan migrate --seed`
6. Khởi động server API: `php artisan serve` (Chạy ở `http://127.0.0.1:8000`)

### 2. Cài đặt Frontend
1. Mở một terminal mới và di chuyển vào thư mục FE: `cd FE`
2. Cài đặt các thư viện Node: `npm install`
3. Cấu hình file môi trường: Tạo file `.env` từ `.env.example` (Trỏ `VITE_API_BASE_URL` về `http://127.0.0.1:8000/api`)
4. Khởi động server Frontend: `npm run dev`

---

## 📦 Cấu Trúc Repository

```text
├── BE/               - Source code Laravel API phục vụ Back-end
│   ├── app/          - Chứa Controllers, Models, Services, Middleware
│   ├── database/     - Migrations, Seeders
│   ├── routes/       - api.php (Nơi định nghĩa API endpoints)
│   └── tests/        - Các bài Unit test và Feature test 
│
├── FE/               - Source code Vue.js phục vụ Front-end
│   ├── src/          
│   │   ├── components/ - Chứa các file giao diện chia theo Admin/KhachHang/DoiTac
│   │   ├── layout/     - Các Wrapper layouts, Header/Footer
│   │   ├── services/   - Cấu hình Axios gửi api
│   │   └── router/     - Cấu hình các luồng đường dẫn trang
│   ├── vite.config.js
│   └── package.json
│
└── README.md         - Tài liệu tổng quan (Bạn đang đọc cái này)
```

## 🤝 Hướng Dẫn Đóng Góp (Contribution Guidelines)
- Chuyển sang nhánh (branch) riêng khi làm việc hoặc dùng nhánh `develop` làm nhánh base.
- Vui lòng chạy lệnh: `git pull origin develop` để lấy code mới nhất trước khi push phần việc của bạn lên.
- Ghi chú log Commit rõ ràng, giúp dễ theo dõi tiến độ.
