# Master Layout - Hướng Dẫn Sử Dụng

## Cấu Trúc Thư Mục

```
src/
├── layout/
│   ├── master/
│   │   └── MasterLayout.vue      # Bố cục chính
│   └── components/
│       ├── Sidebar.vue           # Menu bên trái
│       ├── Header.vue            # Thanh công cụ
│       └── Footer.vue            # Chân trang
├── pages/
│   └── Dashboard.vue             # Trang test
├── assets/
│   └── layout.css                # CSS chung
└── router/
    └── index.js                  # Cấu hình routing
```

## Các Thành Phần Đã Tách

### 1. **MasterLayout.vue** - Bố cục chính
- Chứa toàn bộ cấu trúc layout
- Import Sidebar, Header, Footer
- Sử dụng `<router-view>` cho nội dung động

### 2. **Sidebar.vue** - Menu bên trái
- Menu chính có thể cấu hình
- Hỗ trợ collapse menu con
- Sử dụng router-link cho điều hướng
- Dễ dàng thêm/xóa menu items

### 3. **Header.vue** - Thanh công cụ trên
- Tìm kiếm
- Thông báo (Alerts)
- Tin nhắn (Messages)
- Tài khoản người dùng
- Tất cả các dropdown hoàn toàn chức năng

### 4. **Footer.vue** - Chân trang
- Thông tin bản quyền
- Dễ dàng tùy chỉnh

### 5. **layout.css** - Tất cả CSS chung
- Toàn bộ styling cho layout
- Responsive design (mobile, tablet, desktop)
- Bootstrap utilities
- Dễ dàng tùy chỉnh màu sắc và kích thước

## Cách Sử Dụng

### Tạo Trang Mới Trong Master Layout

```vue
<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <h1>Trang Mới Của Tôi</h1>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MyPage',
};
</script>

<style scoped>
.container-fluid {
  padding: 2rem;
}
</style>
```

### Thêm Route Mới

Trong `src/router/index.js`:

```javascript
{
    path: '',
    name: 'dashboard',
    component: () => import('../pages/Dashboard.vue'),
},
{
    path: 'my-page',
    name: 'myPage',
    component: () => import('../pages/MyPage.vue'),
}
```

### Thêm Menu Item

Trong `src/layout/components/Sidebar.vue`, thêm vào phần menu:

```vue
<li class="nav-item">
    <router-link class="nav-link" to="/my-page">
        <i class="fas fa-fw fa-star"></i>
        <span>Trang Mới</span>
    </router-link>
</li>
```

## Tùy Chỉnh Giao Diện

### Thay Đổi Màu Chính

Trong `src/assets/layout.css`, tìm và thay đổi:

```css
/* Màu xanh chính */
--primary-color: #224abe;

/* Hoặc thay trực tiếp */
background: linear-gradient(180deg, #224abe 10%, #00b4d8 100%);
```

### Thay Đổi Logo/Brand

Trong `Sidebar.vue`:

```vue
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="#/">
    <div class="sidebar-brand-icon rotate-n-15">
        <!-- Thay icon ở đây -->
        <i class="fas fa-laugh-wink"></i>
    </div>
    <div class="sidebar-brand-text mx-3">
        <!-- Thay text ở đây -->
        SB Admin <sup>2</sup>
    </div>
</a>
```

### Thay Đổi Thông Tin Người Dùng

Trong `Header.vue`:

```vue
<span class="mr-2 d-none d-lg-inline text-gray-600 small">
    Tên Người Dùng Của Bạn
</span>
```

## Tính Năng

✅ **Layout Responsive** - Hoạt động tốt trên mobile, tablet, desktop
✅ **Component Tái Sử Dụng** - Dễ dàng thay đổi và cấu hình
✅ **Menu Có Collapse** - Menu con có thể thu gọn
✅ **Tìm Kiếm** - Thanh tìm kiếm chức năng
✅ **Thông Báo** - Hệ thống alerts
✅ **Tin Nhắn** - Hệ thống messages
✅ **Tài Khoản** - Dropdown tài khoản người dùng
✅ **Router Integration** - Tích hợp sẵn với Vue Router
✅ **Dễ Tùy Chỉnh** - CSS modular, dễ thay đổi

## Cấu Trúc Router

```javascript
// Routes bên trong MasterLayout
{
    path: '/',
    component: MasterLayout,
    children: [
        {
            path: '',
            name: 'dashboard',
            component: () => import('../pages/Dashboard.vue'),
        },
        // Thêm các routes con ở đây
    ]
}

// Routes ngoài MasterLayout (nếu cần)
{
    path: '/login',
    name: 'login',
    component: () => import('../pages/LoginPage.vue'),
}
```

## Kiểm Tra

1. Chạy dev server: `npm run dev`
2. Truy cập http://localhost:5173
3. Kiểm tra:
   - ✓ Sidebar hiển thị
   - ✓ Header hiển thị
   - ✓ Footer hiển thị
   - ✓ Menu items click được
   - ✓ Responsive trên mobile

## Liên Kết Hữu Ích

- [SB Admin 2](https://startbootstrap.com/theme/sb-admin-2)
- [Vue 3 Docs](https://vuejs.org)
- [Vue Router](https://router.vuejs.org)
