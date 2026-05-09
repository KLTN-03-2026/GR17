import fs from "node:fs";
import path from "node:path";

const repoRoot = process.cwd();

function read(relativePath) {
  return fs.readFileSync(path.join(repoRoot, relativePath), "utf8");
}

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

const customerAi = read("src/components/KhachHang/KeHoachAI/index.vue");
const adminAi = read("src/components/Admin/CauHinhAI/index.vue");
const router = read("src/router/index.js");
const landingPage = read("src/components/Home/LandingPage.vue");
const aiPreferences = read("src/constants/aiPreferences.js");
const mojibakePattern = /(KhÃ|Ä|LÃ|Thá»|NgÃ|Báº|Há»|Tiáº|Cáº|á»¨)/;

assert(
  !mojibakePattern.test(customerAi),
  "KhachHang/KeHoachAI khong duoc con chuoi mojibake."
);

assert(
  customerAi.includes('goiApi("/khach-hang/ke-hoach-ai"'),
  "KhachHang/KeHoachAI phai goi dung endpoint POST /khach-hang/ke-hoach-ai."
);

assert(
  customerAi.includes("/khach-hang/ke-hoach-ai/de-xuat-dia-diem"),
  "KhachHang/KeHoachAI phai co buoc de xuat dia diem."
);

assert(
  customerAi.includes("selectedLocations") && customerAi.includes("layDeXuatDiaDiem"),
  "KhachHang/KeHoachAI phai co state va flow chon dia diem tu goi y AI."
);

assert(
  customerAi.includes("normalizeSuggestedLocation") &&
    customerAi.includes("this.danhSachDeXuat =") &&
    !customerAi.includes('@click="toggleLocation(loc.ten)"'),
  "KhachHang/KeHoachAI phai chuan hoa du lieu goi y AI va khong duoc phu thuoc truc tiep vao loc.ten khi chon dia diem."
);

assert(
  router.includes('path: "/khach-hang/len-ke-hoach-ai"') && router.includes("layout: CustomerLayout"),
  "Route /khach-hang/len-ke-hoach-ai phai dung CustomerLayout."
);

assert(
  adminAi.includes('"/admin/cau-hinh-ai/api-config"') && adminAi.includes('"/admin/cau-hinh-ai"'),
  "Admin/CauHinhAI phai giu du cac endpoint cau hinh prompt va api-config."
);

assert(
  !mojibakePattern.test(adminAi),
  "Admin/CauHinhAI khong duoc con chuoi mojibake."
);

assert(
  /goiApi\((["'])\/khach-hang\/ke-hoach-ai\1/.test(landingPage) &&
    /goiApi\((["'])\/khach-hang\/ke-hoach-ai\/save\1/.test(landingPage),
  "LandingPage phai giu du generate/save cho AI planner."
);

assert(
  !mojibakePattern.test(landingPage),
  "LandingPage khong duoc con chuoi mojibake trong be mat AI."
);

assert(
  !mojibakePattern.test(aiPreferences),
  "aiPreferences khong duoc con chuoi mojibake."
);

console.log("AI surface smoke checks passed.");
