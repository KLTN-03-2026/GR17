export const CUSTOMER_MENU_GROUPS = [
  {
    id: "overview",
    label: "Tổng quan",
    items: [
      { to: "/khach-hang/dashboard", icon: "fas fa-grip", text: "Tổng quan", prefixes: ["/khach-hang/dashboard"] },
    ],
  },
  {
    id: "booking",
    label: "Đặt tour và thanh toán",
    items: [
      { to: "/khach-hang/tour", icon: "fas fa-map", text: "Danh sách tour", prefixes: ["/khach-hang/tour"] },
      {
        to: "/khach-hang/lich-su-don-hang",
        icon: "fas fa-receipt",
        text: "Đơn hàng và thanh toán",
        prefixes: ["/khach-hang/lich-su-don-hang", "/khach-hang/hoa-don"],
      },
    ],
  },
  {
    id: "journey",
    label: "Hành trình của tôi",
    items: [
      { to: "/khach-hang/nhom-hanh-trinh", icon: "fas fa-users", text: "Nhóm hành trình", prefixes: ["/khach-hang/nhom-hanh-trinh"] },
      { to: "/khach-hang/ke-hoach", icon: "fas fa-calendar-days", text: "Kế hoạch", prefixes: ["/khach-hang/ke-hoach"] },
      { to: "/khach-hang/len-ke-hoach-ai", icon: "fas fa-magic", text: "Lên kế hoạch AI", prefixes: ["/khach-hang/len-ke-hoach-ai", "/khach-hang/lap-ke-hoach-ai"] },
    ],
  },
  {
    id: "discover",
    label: "Khám phá",
    items: [
      { to: "/khach-hang/dia-diem", icon: "fas fa-map-location-dot", text: "Địa điểm", prefixes: ["/khach-hang/dia-diem"] },
      { to: "/khach-hang/danh-sach-yeu-thich", icon: "fas fa-heart", text: "Yêu thích", prefixes: ["/khach-hang/danh-sach-yeu-thich"] },
      { to: "/khach-hang/danh-gia", icon: "fas fa-star", text: "Đánh giá", prefixes: ["/khach-hang/danh-gia"] },
    ],
  },
  {
    id: "account",
    label: "Tài khoản",
    items: [
      { to: "/khach-hang/ho-so", icon: "fas fa-user-cog", text: "Hồ sơ cá nhân", prefixes: ["/khach-hang/ho-so"] },
    ],
  },
];

export const CUSTOMER_PRIMARY_ACTION = {
  to: "/khach-hang/tour",
  icon: "fas fa-plus",
  text: "Đặt tour ngay",
};
