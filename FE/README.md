# DoAnTotNghiepFE (nhánh `develop`)

Frontend Vue 3 cho hệ thống Smart Travel. Tài liệu này mô tả **đúng theo code FE hiện tại** trong nhánh `develop` (cập nhật ngày 27/03/2026).

## 1) Công nghệ đang dùng

- Vue 3 (Options API)
- Vue Router 4
- Vite 4
- Axios + Fetch API
- Font Awesome
- SB Admin 2 (asset CSS cục bộ)

## 2) Chạy dự án local

Yêu cầu:

- Node.js 18+
- npm
- Backend `DoAnTotNghiepBE` đang chạy ở `http://127.0.0.1:8000`

Cài đặt và chạy:

```bash
npm install
npm run dev
```

Build/preview:

```bash
npm run build
npm run preview
```

## 3) Kiến trúc tổng quan

### 3.1 Layout

- `LandingLayout`: dùng cho public + customer pages (TopNavBar + Footer).
- `MasterLayout`: dùng cho admin (Sidebar + Header + AdminFooter).
- `MasterAuthLayout`: dùng cho đăng nhập/đăng ký.

### 3.2 Điều hướng và phân quyền

`src/router/index.js` có guard:

- `requiresAuth`: bắt buộc đăng nhập.
- `role`: bắt buộc đúng vai trò (`admin` hoặc `customer`).
- `guest`: trang chỉ dành cho khách chưa đăng nhập.

Nếu đăng nhập sai vai trò, router tự chuyển về khu vực tương ứng role hiện tại.

### 3.3 Session và localStorage

FE đang dùng các key chính:

- `token`
- `auth_type`
- `user`
- `preferred_locale`
- `rememberEmail`

## 4) Chức năng FE hiện tại

## 4.1 Public / Landing

### A. Trang chủ `/`

- Hero + form lập kế hoạch du lịch bằng AI UI (`TravelForm`).
- Nhập: điểm đến, số ngày, ngân sách, sở thích.
- Render kết quả hành trình (`ItineraryResult`) dạng markdown đơn giản.
- Hiển thị nguồn tham chiếu (web/maps) theo kiểu accordion nếu có `groundingChunks`.

### B. Khối gợi ý tour AI (`AISuggestions`)

- Tải dữ liệu tour từ API.
- Có fallback dữ liệu mẫu khi API lỗi/rỗng.
- Lọc theo:
  - từ khóa
  - địa điểm
  - mức giá
  - thời lượng
- Phân trang danh sách tour.
- Quick view modal chi tiết tour.
- So sánh tour:
  - chọn tối đa 4 tour
  - modal so sánh khi chọn từ 2 tour trở lên
  - có cảnh báo dialog khi vượt giới hạn.

### C. Khối nội dung landing khác

- Tour nổi bật (lấy API + fallback).
- Điểm đến phổ biến (lấy API + fallback).
- About section (nội dung tĩnh).
- News section (dữ liệu tĩnh).
- Contact section (UI form liên hệ, chưa submit API).
- Newsletter section (UI form, chưa submit API).

### D. TopNav + Footer

- TopNav đổi trạng thái theo đăng nhập/chưa đăng nhập.
- Menu tài khoản khi đã đăng nhập:
  - vào workspace theo role
  - đổi ngôn ngữ VI/EN
  - đăng xuất
- Footer có form đăng ký bản tin mức UI (reset input local).

## 4.2 Auth

### A. Đăng nhập `/dang-nhap`

- Chọn vai trò đăng nhập: `customer` hoặc `admin`.
- Đăng nhập theo endpoint role tương ứng.
- Lưu session (`token`, `auth_type`, `user`).
- `Remember me` lưu email.
- Chuyển hướng theo role sau đăng nhập thành công.
- Hỗ trợ chuyển ngôn ngữ VI/EN.
- Có dialog “Quên mật khẩu” (hướng dẫn liên hệ admin, chưa có flow reset online).

### B. Đăng ký `/dang-ky`

- Validate client:
  - email đúng định dạng
  - password tối thiểu 6 ký tự
  - xác nhận mật khẩu khớp
- Gọi API đăng ký khách hàng.
- Hiển thị lỗi validation từ backend theo field.

## 4.3 Khu vực Khách hàng

### A. Nhóm hành trình

- `/khach-hang/nhom-hanh-trinh`
  - danh sách nhóm của user
  - tìm kiếm
  - tạo nhóm
  - sửa tên nhóm
  - xóa nhóm
  - tự gắn user hiện tại làm thành viên khi tạo nhóm
- `/khach-hang/nhom-hanh-trinh/:id/members`
  - xem thông tin nhóm + thành viên
  - tìm kiếm/lọc thành viên theo vai trò
  - thêm thành viên từ danh sách khách hàng
  - đổi vai trò thành viên
  - xóa thành viên
  - có ràng buộc quyền quản lý theo vai trò nhóm

### B. Kế hoạch du lịch

- `/khach-hang/ke-hoach`
  - danh sách kế hoạch
  - tìm kiếm + lọc trạng thái
  - phân trang
  - thẻ tóm tắt thống kê
- `/khach-hang/ke-hoach/create`
  - tạo kế hoạch mới
  - liên kết nhóm nếu có
- `/khach-hang/ke-hoach/:id`
  - xem chi tiết kế hoạch
- `/khach-hang/ke-hoach/:id/edit`
  - cập nhật nội dung kế hoạch
  - đổi trạng thái kế hoạch
  - xóa kế hoạch (có xác nhận)

### C. Đánh giá

- `/khach-hang/danh-gia`
  - danh sách đánh giá của user
  - tìm kiếm + lọc theo số sao
  - tạo đánh giá
  - sửa đánh giá
  - xóa đánh giá

### D. Địa điểm và yêu thích

- `/khach-hang/dia-diem`
  - danh sách địa điểm
  - tìm kiếm + lọc loại địa điểm
- `/khach-hang/dia-diem/:id`
  - chi tiết địa điểm
  - tải thêm dịch vụ + đánh giá liên quan
  - mở Google Maps theo địa điểm
  - thêm vào danh sách yêu thích
  - điều hướng nhanh qua tạo kế hoạch
- `/khach-hang/danh-sach-yeu-thich`
  - danh sách yêu thích của user
  - tìm kiếm
  - thêm địa điểm yêu thích
  - sửa mục yêu thích
  - xóa mục yêu thích

## 4.4 Khu vực Admin

### A. Dashboard `/dashboard`

- Tổng hợp số liệu từ nhiều API (`hoa-don`, `admins`, `dia-diem`, `tour`, `ke-hoach`).
- Các card KPI + biểu đồ xu hướng doanh thu + tỷ lệ lấp đầy tour.
- Fallback dữ liệu khi một phần API lỗi.

### B. Quản lý khách hàng

- `/admin/customers`
  - thống kê nhanh
  - tìm kiếm
  - lọc giới tính/trạng thái
  - phân trang
  - xem nhanh profile và xóa
- `/admin/customers/create`
  - thêm khách hàng
- `/admin/customers/:id`
  - chi tiết khách hàng
- `/admin/customers/:id/edit`
  - chỉnh sửa khách hàng

### C. Quản lý đánh giá

- `/admin/reviews`
  - thống kê + tìm kiếm + lọc sao + phân trang
  - xóa đánh giá (dialog xác nhận)
- `/admin/reviews/:id`
  - chi tiết đánh giá
- `/admin/reviews/:id/edit`
  - cập nhật đánh giá
  - xóa đánh giá

### D. Quản lý cấu hình ngày

- `/admin/day-config`
  - thống kê + tìm kiếm + lọc loại ngày + phân trang
  - xóa cấu hình (dialog xác nhận)
- `/admin/day-config/create`
  - tạo cấu hình ngày
- `/admin/day-config/:id/edit`
  - chỉnh sửa
  - xóa

### E. Quản lý tour và lịch khởi hành

- `/admin/tour`
  - danh sách tour
  - tìm kiếm
  - phân trang
  - tạo tour mới (modal)
  - cập nhật trạng thái hoạt động tour
  - xóa tour
- `/admin/tour/:id`
  - chi tiết/chỉnh sửa tour
- `/admin/tour/:id/edit`
  - route edit dùng cùng component chi tiết
- `/admin/tour-khoi-hanh`
  - danh sách lịch khởi hành
  - tìm kiếm + phân trang
  - tạo/sửa/xóa lịch khởi hành
  - xem chi tiết từng lịch

### F. Quản lý địa điểm và tag

- `/admin/quan-ly-dia-diem`
  - danh sách địa điểm
  - tìm kiếm + lọc
  - tạo/sửa/xóa địa điểm
- `/admin/quan-ly-tag`
  - danh sách tag
  - tìm kiếm + phân trang
  - xem chi tiết tag
  - tạo/sửa/xóa tag

### G. Hóa đơn, phân quyền, tài khoản admin

- `/admin/hoa-don`
  - danh sách hóa đơn
  - tìm kiếm + lọc trạng thái thanh toán
  - cập nhật trạng thái thanh toán (PATCH)
- `/admin/phan-quyen`
  - quản lý chức vụ (CRUD + đổi trạng thái)
  - quản lý chức năng (CRUD)
  - quản lý mapping phân quyền admin (CRUD + cấp nhanh)
- `/admin/tai-khoan`
  - danh sách admin
  - tìm kiếm
  - tạo/sửa admin
  - khóa/mở khóa admin
  - xóa admin
- `/admin/ho-so`
  - cập nhật hồ sơ admin hiện tại
  - đổi mật khẩu

### H. Header/Sidebar admin

- Sidebar điều hướng theo module quản trị.
- Header có:
  - trung tâm thông báo (global notification)
  - menu profile
  - đăng xuất

## 4.5 Hệ thống dialog/notification dùng chung

- `AppDialogHost` mount global trong `App.vue`.
- `showAlert()` và `showConfirm()` dùng xuyên suốt cho cảnh báo/xác nhận.
- `globalNotifications` + `window.showAdminNotification(...)` cho thông báo header admin.

## 5) Bản đồ route hiện có

### 5.1 Public

- `/`
- `/dang-nhap`
- `/dang-ky`

### 5.2 Admin (`requiresAuth: true`, `role: admin`)

- `/dashboard`
- `/admin/phan-quyen`
- `/admin/tai-khoan`
- `/admin/ho-so`
- `/admin/hoa-don`
- `/admin/customers`
- `/admin/customers/create`
- `/admin/customers/:id`
- `/admin/customers/:id/edit`
- `/admin/reviews`
- `/admin/reviews/:id`
- `/admin/reviews/:id/edit`
- `/admin/day-config`
- `/admin/day-config/create`
- `/admin/day-config/:id/edit`
- `/admin/quan-ly-dia-diem`
- `/admin/quan-ly-tag`
- `/admin/tour`
- `/admin/tour/:id`
- `/admin/tour/:id/edit`
- `/admin/tour-khoi-hanh`

### 5.3 Customer (`requiresAuth: true`, `role: customer`)

- `/khach-hang/nhom-hanh-trinh`
- `/khach-hang/nhom-hanh-trinh/:id/members`
- `/khach-hang/ke-hoach`
- `/khach-hang/ke-hoach/create`
- `/khach-hang/ke-hoach/:id`
- `/khach-hang/ke-hoach/:id/edit`
- `/khach-hang/danh-gia`
- `/khach-hang/dia-diem`
- `/khach-hang/dia-diem/:id`
- `/khach-hang/danh-sach-yeu-thich`

### 5.4 Redirect/alias tương thích cũ

- `/tour-management` -> `/admin/tour`
- `/tour-management/create` -> `/admin/tour`
- `/tour-management/:id/edit` -> `/admin/tour/:id/edit`
- `/tour-management/:id/departures` -> `/admin/tour-khoi-hanh`
- `/khach-hang/nhom-hanh-trinh/create` -> `/khach-hang/nhom-hanh-trinh`
- `/khach-hang/nhom-hanh-trinh/:id` -> `/khach-hang/nhom-hanh-trinh/:id/members`
- `/khach-hang/nhom-hanh-trinh/:id/edit` -> `/khach-hang/nhom-hanh-trinh`
- `/khach-hang/danh-gia/create` -> `/khach-hang/danh-gia`
- `/khach-hang/danh-gia/:id/edit` -> `/khach-hang/danh-gia`

## 6) Các nhóm API FE đang gọi

- Auth: `/khach-hang/login`, `/admin/login`, `/khach-hang/register`
- Tour + lịch: `/tour`, `/tour-khoi-hanh`, `/chi-tiet-tour`
- Địa điểm + tag: `/dia-diem`, `/tag`, `/dich-vu-dia-diem/location/*`
- Khách hàng: `/khach-hang`, `/khach-hang/profile/*`, `/admin/khach-hang/*`
- Kế hoạch: `/ke-hoach/*`
- Nhóm/thành viên: `/nhom/*`, `/thanh-vien-nhom/*`
- Đánh giá: `/danh-gia-ke-hoach/*`
- Yêu thích: `/khach-hang/danh-sach-yeu-thich/*`
- Cấu hình ngày: `/admin/cau-hinh-ngay/*`
- Hóa đơn: `/admin/hoa-don*`
- Phân quyền: `/chuc-vu*`, `/chuc-nang*`, `/phan-quyen-admin*`
- Admin account: `/admins`, `/admin/*`

## 7) Ghi chú kỹ thuật quan trọng

- `API_BASE` đang hard-code ở nhiều component (`127.0.0.1:8000`/`localhost:8000`), chưa đồng nhất theo biến môi trường.
- Một số khối landing (Contact, Newsletter, News) hiện là dữ liệu/UI tĩnh, chưa nối API thật.
- Một số file trong `src/components/Tour/*` là mã legacy và hiện **không được gắn route trực tiếp** trong `router/index.js` (đang dùng module mới ở `src/components/Admin/Tour/*`).

## 8) Script hỗ trợ trong repo

- `scripts/audit_user_text.py`: quét nhanh text/encoding (mojibake, cụm từ cấm, không dấu) trong FE và một phần BE.

Chạy thủ công:

```bash
python scripts/audit_user_text.py
```
