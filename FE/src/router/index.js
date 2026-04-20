import { createRouter, createWebHistory } from "vue-router";
import LandingLayout from '../layout/master/LandingLayout.vue';

const routes = [
    {
        path: '/',
        component: LandingLayout,
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('../pages/LandingPage.vue'),
            },
        ]
    },
    // Login Route
    {
        path: '/dang-nhap',
        name: 'login',
        component: () => import('../pages/LoginPage.vue'),
    },
    {
        path: '/admin/dang-nhap',
        name: 'admin-login',
        component: () => import('../pages/AdminLoginPage.vue'),
    },
    // Dashboard Route
    {
        path: '/admin/dashboard',
        name: 'admin-dashboard',
        component: () => import('../pages/Dashboard.vue'),
    },
     {
        path: '/admin/Quan-Ly-Dich-Vu-Dia-Chi',
        name: 'admin-ql-dv-dc',
        component: () => import('../pages/AdminQuanLyDichVuDiaChi.vue'),
    },
      {
        path: '/admin/Quan-Ly-thue-xe',
        name: 'admin-ql-tx',
        component: () => import('../pages/AdminQuanLyThueXe.vue'),
    },
 
    // Routes User 
    {
        path: '/User/trang-Danh-sach-tour',
        name: 'user-ql-tour',
        component: () => import('../pages/UserDanhSachTour.vue'),
    },
    {
        path: '/User/chi-tiet-tour',
        name: 'user-ct-tour',
        component: () => import('../pages/UserChiTietTour.vue'),
    },
    {
        path: '/User/thanh-toan',
        name: 'user-thanh-toan',
        component: () => import('../pages/UserTrangCheckOut.vue'),
    },
        {
        path: '/User/lich-su-thanh-toan',
        name: 'user-lich-su-thanh-toan',
        component: () => import('../pages/UserLichSuHoaDon.vue'),
    },
    {
        path: '/khach-hang/nhom-hanh-trinh',
        name: 'customer-groups',
        component: () => import('../components/KhachHang/NhomHanhTrinh/DanhSachNhom.vue'),
    },

];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});

export default router;
