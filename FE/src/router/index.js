import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/aaa",
    component: () => import("../layout/components/test.vue"),
  },
  {
    path: "/khach-hang/nhom-hanh-trinh",
    component: () => import("../components/KhachHang/NhomHanhTrinh/DanhSachNhom.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
