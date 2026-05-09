import { defineAsyncComponent } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import authStorage, { Role } from "../services/authStorage";

const LandingLayout = defineAsyncComponent(() => import("../layout/wrapper/LandingLayout.vue"));
const AdminLayout = defineAsyncComponent(() => import("../layout/wrapper/AdminLayout.vue"));
const PartnerLayout = defineAsyncComponent(() => import("../layout/wrapper/PartnerLayout.vue"));
const MasterAuthLayout = defineAsyncComponent(() => import("../layout/wrapper/MasterAuthLayout.vue"));
const CustomerLayout = defineAsyncComponent(() => import("../layout/wrapper/CustomerLayout.vue"));

const routes = [
  // Public
  {
    path: "/",
    component: () => import("../components/Home/LandingPage.vue"),
    meta: {
      layout: LandingLayout,
      title: "Trang chủ",
    },
  },
  {
    path: "/dang-nhap",
    component: () => import("../components/KhachHang/DangNhap.vue"),
    meta: {
      layout: MasterAuthLayout,
      title: "Đăng nhập",
      guest: true,
    },
  },
  {
    path: "/dang-ky",
    component: () => import("../components/KhachHang/DangKi.vue"),
    meta: {
      layout: MasterAuthLayout,
      title: "Đăng ký",
      guest: true,
    },
  },
  {
    path: "/admin/dang-nhap",
    component: () => import("../components/Admin/DangNhapAdmin.vue"),
    meta: {
      layout: MasterAuthLayout,
      title: "Đăng nhập Admin",
      guest: true,
    },
  },
  {
    path: "/doi-tac/dang-nhap",
    component: () => import("../components/DoiTac/DangNhap.vue"),
    meta: {
      layout: MasterAuthLayout,
      title: "Đăng nhập đối tác",
      guest: true,
    },
  },
  {
    path: "/doi-tac/dang-ky",
    component: () => import("../components/DoiTac/DangKy.vue"),
    meta: {
      layout: MasterAuthLayout,
      title: "Đăng ký đối tác",
      guest: true,
    },
  },
  {
    path: "/doi-tac/dashboard",
    component: () => import("../components/DoiTac/Dashboard.vue"),
    meta: {
      layout: PartnerLayout,
      title: "Tổng quan đối tác",
      requiresAuth: true,
      role: "partner",
      keepAlive: true,
    },
  },
  {
    path: "/doi-tac/quan-ly-tour",
    component: () => import("../components/DoiTac/QuanLyTour.vue"),
    meta: {
      layout: PartnerLayout,
      title: "Quản lý tour đối tác",
      requiresAuth: true,
      role: "partner",
      keepAlive: true,
    },
  },
  {
    path: "/doi-tac/quan-ly-dia-diem",
    component: () => import("../components/DoiTac/QuanLyDiaDiem.vue"),
    meta: {
      layout: PartnerLayout,
      title: "Quản lý địa điểm đối tác",
      requiresAuth: true,
      role: "partner",
      keepAlive: true,
    },
  },
  {
    path: "/doi-tac/doanh-thu",
    component: () => import("../components/DoiTac/DoiSoatDoanhThu.vue"),
    meta: {
      layout: PartnerLayout,
      title: "Theo dõi doanh thu",
      requiresAuth: true,
      role: "partner",
      keepAlive: true,
    },
  },
  {
    path: "/doi-tac/voucher",
    component: () => import("../components/DoiTac/Voucher/index.vue"),
    meta: {
      layout: PartnerLayout,
      title: "Quản lý Mã Giảm Giá",
      requiresAuth: true,
      role: "partner",
    },
  },
  {
    path: "/doi-tac/don-hang",
    component: () => import("../components/DoiTac/QuanLyDonHang.vue"),
    meta: {
      layout: PartnerLayout,
      title: "Quản lý đơn hàng",
      requiresAuth: true,
      role: "partner",
      keepAlive: true,
    },
  },
  {
    path: "/doi-tac/tour/:ma_tour/hanh-trinh",
    redirect: "/doi-tac/quan-ly-tour",
    meta: {
      layout: PartnerLayout,
      title: "Hành trình tour đối tác",
      requiresAuth: true,
      role: "partner",
    },
  },

  {
    path: "/doi-tac/thong-tin-tai-khoan",
    component: () => import("../components/DoiTac/ThongTinTaiKhoan.vue"),
    meta: {
      layout: PartnerLayout,
      title: "Thông tin tài khoản đối tác",
      requiresAuth: true,
      role: "partner",
    },
  },

  // Admin
  {
    path: "/dashboard",
    component: () => import("../components/Dashboard/Dashboard.vue"),
    meta: {
      layout: AdminLayout,
      title: "Tổng quan",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/thong-ke",
    component: () => import("../components/Admin/ThongKe/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý thống kê",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/phan-hoi",
    component: () => import("../components/Admin/PhanHoi/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý Báo Lỗi & Phản hồi",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/voucher",
    component: () => import("../components/Admin/Voucher/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý Mã Giảm Giá",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/phan-quyen",
    component: () => import("../components/Admin/PhanQuyen/QuanLyPhanQuyen.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý Phân quyền",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/tai-khoan",
    component: () => import("../components/Admin/TaiKhoanAdmin/DanhSachTaiKhoanAdmin.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý tài khoản admin",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/ho-so",
    component: () => import("../components/Admin/TaiKhoanAdmin/HoSoAdmin.vue"),
    meta: {
      layout: AdminLayout,
      title: "Hồ sơ admin",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/hoa-don",
    component: () => import("../components/Admin/HoaDon/DanhSachHoaDon.vue"),
    meta: {
      layout: AdminLayout,
      title: "Danh sách hóa đơn",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/hoa-don/:id",
    component: () => import("../components/Admin/HoaDon/ChiTietHoaDon.vue"),
    meta: {
      layout: AdminLayout,
      title: "Chi tiết hóa đơn",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/customers",
    component: () => import("../components/Admin/KhachHang/DanhSachKhachHang.vue"),
    meta: {
      layout: AdminLayout,
      title: "Danh sách khách hàng",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/doi-tac",
    component: () => import("../components/Admin/QuanLyDoiTac/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý đối tác",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/doi-soat",
    component: () => import("../components/Admin/DoiSoat/DoiSoatDoanhThu.vue"),
    meta: {
      layout: AdminLayout,
      title: "Đối soát doanh thu",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/customers/create",
    component: () => import("../components/Admin/KhachHang/ThemKhachHang.vue"),
    meta: {
      layout: AdminLayout,
      title: "Thêm khách hàng",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/customers/:id",
    component: () => import("../components/Admin/KhachHang/ChiTietKhachHang.vue"),
    meta: {
      layout: AdminLayout,
      title: "Chi tiết khách hàng",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/customers/:id/edit",
    component: () => import("../components/Admin/KhachHang/ChinhSuaKhachHang.vue"),
    meta: {
      layout: AdminLayout,
      title: "Chỉnh sửa khách hàng",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/reviews",
    component: () => import("../components/Admin/DanhGia/DanhSachDanhGia.vue"),
    meta: {
      layout: AdminLayout,
      title: "Danh sách đánh giá",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/reviews/:id",
    component: () => import("../components/Admin/DanhGia/ChiTietDanhGia.vue"),
    meta: {
      layout: AdminLayout,
      title: "Chi tiết đánh giá",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/reviews/:id/edit",
    component: () => import("../components/Admin/DanhGia/ChinhSuaDanhGia.vue"),
    meta: {
      layout: AdminLayout,
      title: "Chỉnh sửa đánh giá",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/day-config",
    component: () => import("../components/Admin/CauHinhNgay/DanhSachCauHinhNgay.vue"),
    meta: {
      layout: AdminLayout,
      title: "Danh sách cấu hình ngày",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/day-config/create",
    component: () => import("../components/Admin/CauHinhNgay/TaoCauHinhNgay.vue"),
    meta: {
      layout: AdminLayout,
      title: "Tạo cấu hình ngày",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/day-config/:id/edit",
    component: () => import("../components/Admin/CauHinhNgay/ChinhSuaCauHinhNgay.vue"),
    meta: {
      layout: AdminLayout,
      title: "Chỉnh sửa cấu hình ngày",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/quan-ly-dia-diem",
    component: () => import("../components/Admin/QuanLyDiaDiem/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý địa điểm",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/quan-ly-tag",
    component: () => import("../components/Admin/QuanLyTag/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý tag",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  // {
  //   path: "/admin/cau-hinh-ai",
  //   component: () => import("../components/Admin/CauHinhAI/index.vue"),
  //   meta: {
  //     layout: MasterLayout,
  //     title: "Cấu hình AI",
  //     requiresAuth: true,
  //     role: "admin",
  //   },
  // },
  {
    path: "/admin/tour",
    component: () => import("../components/Admin/Tour/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Quản lý tour",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },
  {
    path: "/admin/tour/:id",
    component: () => import("../components/Admin/Tour/ChiTiet/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Chi tiết tour",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/tour/:id/edit",
    component: () => import("../components/Admin/Tour/ChiTiet/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Chỉnh sửa tour",
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/admin/tour-khoi-hanh",
    component: () => import("../components/Admin/TourKhoiHanh/index.vue"),
    meta: {
      layout: AdminLayout,
      title: "Tour khởi hành",
      requiresAuth: true,
      role: "admin",
      keepAlive: true,
    },
  },

  // Backward-compatible aliases for old routes
  {
    path: "/tour-management",
    redirect: "/admin/tour",
  },
  {
    path: "/tour-management/create",
    redirect: "/admin/tour",
  },
  {
    path: "/tour-management/:id/edit",
    redirect: (to) => `/admin/tour/${to.params.id}/edit`,
  },
  {
    path: "/tour-management/:id/departures",
    redirect: "/admin/tour-khoi-hanh",
  },

  // Customer
  {
    path: "/khach-hang/dashboard",
    component: () => import("../components/KhachHang/Dashboard/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Tổng quan khách hàng",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
  {
    path: "/khach-hang/nhom-hanh-trinh",
    component: () => import("../components/KhachHang/NhomHanhTrinh/DanhSachNhom.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Danh sách nhóm",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
  {
    path: "/khach-hang/nhom-hanh-trinh/create",
    redirect: "/khach-hang/nhom-hanh-trinh",
  },
  {
    path: "/khach-hang/nhom-hanh-trinh/:id",
    redirect: (to) => `/khach-hang/nhom-hanh-trinh/${to.params.id}/members`,
  },
  {
    path: "/khach-hang/nhom-hanh-trinh/:id/edit",
    redirect: "/khach-hang/nhom-hanh-trinh",
  },
  {
    path: "/khach-hang/nhom-hanh-trinh/:id/members",
    component: () => import("../components/KhachHang/NhomHanhTrinh/QuanLyThanhVienNhom.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Quản lý thành viên nhóm",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/ke-hoach",
    component: () => import("../components/KhachHang/KeHoach/DanhSachKeHoach.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Danh sách kế hoạch",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
  {
    path: "/khach-hang/ho-so",
    component: () => import("../components/KhachHang/HoSoCaNhan.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Hồ sơ cá nhân",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/ke-hoach/create",
    component: () => import("../components/KhachHang/KeHoach/TaoKeHoach.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Tạo kế hoạch",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/ke-hoach/:id",
    component: () => import("../components/KhachHang/KeHoach/ChiTietKeHoach.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Chi tiết kế hoạch",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/ke-hoach/:id/edit",
    component: () => import("../components/KhachHang/KeHoach/ChinhSuaKeHoach.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Chỉnh sửa kế hoạch",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/danh-gia",
    component: () => import("../components/KhachHang/DanhGia/BangDanhGia.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Bảng đánh giá",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
  {
    path: "/khach-hang/danh-gia/create",
    redirect: "/khach-hang/danh-gia",
  },
  {
    path: "/khach-hang/danh-gia/:id/edit",
    redirect: "/khach-hang/danh-gia",
  },
  {
    path: "/khach-hang/len-ke-hoach-ai",
    component: () => import("../components/KhachHang/KeHoachAI/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Lên kế hoạch AI",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
  {
    path: "/khach-hang/lap-ke-hoach-ai",
    redirect: "/khach-hang/len-ke-hoach-ai",
  },
  {
    path: "/khach-hang/lich-su-don-hang",
    component: () => import("../components/KhachHang/LichSuDonHang/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Đơn hàng và thanh toán",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
  {
    path: "/khach-hang/dia-diem",
    component: () => import("../components/KhachHang/DiaDiem/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Danh sách địa điểm",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
  {
    path: "/khach-hang/tour",
    component: () => import("../components/KhachHang/Tour/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Danh sách tour",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
  {
    path: "/khach-hang/tour/:id",
    component: () => import("../components/KhachHang/Tour/ChiTiet/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Chi tiết tour",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/tour/:id/thanh-toan",
    component: () => import("../components/KhachHang/Tour/ThanhToan/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Thanh toán tour",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/hoa-don/:ma_hoa_don",
    component: () => import("../components/KhachHang/HoaDon/ChiTietHoaDon.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Chi tiết hóa đơn",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/dia-diem/:id",
    component: () => import("../components/KhachHang/ChiTietDiaDiem/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Chi tiết địa điểm",
      requiresAuth: true,
      role: "customer",
    },
  },
  {
    path: "/khach-hang/danh-sach-yeu-thich",
    component: () => import("../components/KhachHang/DanhSachYeuThich/index.vue"),
    meta: {
      layout: CustomerLayout,
      title: "Danh sách yêu thích",
      requiresAuth: true,
      role: "customer",
      keepAlive: true,
    },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition;
    if (to.hash) {
      return {
        el: to.hash,
        behavior: "smooth",
        top: 80,
      };
    }
    return { top: 0 };
  },
});

const daTaiTruoc = new Set();
let daLenLichTaiTruoc = false;

const danhSachTaiTruocTheoQuyen = {
  admin: ["/admin/quan-ly-dia-diem", "/admin/tour", "/admin/doi-tac", "/admin/hoa-don", "/admin/customers", "/admin/dang-nhap", "/admin/doi-soat"],
  customer: ["/khach-hang/dashboard", "/khach-hang/tour", "/khach-hang/lich-su-don-hang", "/khach-hang/ke-hoach", "/khach-hang/dia-diem", "/khach-hang/len-ke-hoach-ai", "/dang-nhap"],
  partner: [
    "/doi-tac/dashboard",
    "/doi-tac/quan-ly-tour",
    "/doi-tac/quan-ly-dia-diem",
    "/doi-tac/don-hang",
    "/doi-tac/doanh-thu",
    "/doi-tac/thong-tin-tai-khoan",
    "/doi-tac/dang-nhap",
  ],
  guest: ["/dang-nhap", "/dang-ky", "/admin/dang-nhap"],
};

function chayKhiRanh(callback) {
  if (typeof window === "undefined") return;
  if (typeof window.requestIdleCallback === "function") {
    window.requestIdleCallback(() => callback(), { timeout: 1500 });
    return;
  }
  window.setTimeout(callback, 300);
}

function taiTruocRoute(path) {
  if (!path || daTaiTruoc.has(path)) return;

  const mucTieu = router.resolve(path);
  if (!mucTieu?.matched?.length) return;

  daTaiTruoc.add(path);

  mucTieu.matched.forEach((record) => {
    const component = record.components?.default || record.component;
    if (typeof component === "function") {
      component().catch(() => { });
    }
  });
}

function lenLichTaiTruoc(authType = "") {
  if (daLenLichTaiTruoc) return;
  daLenLichTaiTruoc = true;

  const key = authType === "admin" ? "admin" : authType === "customer" ? "customer" : "guest";
  const roleKey = authType === "partner" ? "partner" : key;
  const danhSachCanTai = danhSachTaiTruocTheoQuyen[roleKey] || [];
  if (!danhSachCanTai.length) return;

  chayKhiRanh(() => {
    danhSachCanTai.forEach((path) => taiTruocRoute(path));
  });
}

router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} - Smart Travel` : "Smart Travel";

  // Determine which role context this route belongs to
  const targetRole = authStorage.getCurrentRoleFromPath(to.path);

  // Get token and auth type for the relevant role
  let token = authStorage.getToken(targetRole);
  let authType = localStorage.getItem(`${targetRole}_auth_type`) || targetRole;
  let user = authStorage.getUser(targetRole);

  // Fallback for transition/compatibility with existing session
  if (!token) {
    token = localStorage.getItem("token");
    authType = (localStorage.getItem("auth_type") || "").toLowerCase();
    user = authStorage.getUser(Role.CUSTOMER); // Assume generic 'user' was for customer
  }

  const isAuthenticated = Boolean(token);

  lenLichTaiTruoc(authType);

  if (to.meta.requiresAuth && !isAuthenticated) {
    const isAdminRoute = to.path.startsWith("/admin") || to.meta.role === "admin" || to.path === "/dashboard";
    const isPartnerRoute = to.path.startsWith("/doi-tac") || to.meta.role === "partner";
    return next({
      path: isAdminRoute ? "/admin/dang-nhap" : isPartnerRoute ? "/doi-tac/dang-nhap" : "/dang-nhap",
      query: { redirect: to.fullPath },
    });
  }

  if (to.meta.requiresAuth && to.meta.role && authType !== to.meta.role) {
    if (authType === "admin") {
      return next("/dashboard");
    }
    if (authType === "customer") {
      return next("/khach-hang/dashboard");
    }
    if (authType === "partner") {
      return next("/doi-tac/dashboard");
    }
    return next("/");
  }

  if (to.meta.guest && isAuthenticated) {
    if (authType === "admin") {
      return next("/dashboard");
    }
    if (authType === "partner") {
      return next("/doi-tac/dashboard");
    }
    return next("/khach-hang/dashboard");
  }

  next();
});

export default router;

