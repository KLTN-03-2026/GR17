const fs = require('fs');

const files = [
  'src/components/Admin/KhachHang/ChinhSuaKhachHang.vue',
  'src/components/Admin/KhachHang/ChiTietKhachHang.vue',
  'src/components/Admin/KhachHang/ThemKhachHang.vue',
  'src/components/Admin/QuanLyTag/index.vue',
  'src/components/Admin/Tour/ChiTiet/index.vue',
  'src/components/Admin/TourKhoiHanh/index.vue',
  'src/components/DoiTac/Dashboard.vue',
  'src/components/DoiTac/DoiSoatDoanhThu.vue',
  'src/components/DoiTac/HanhTrinhTour.vue',
  'src/components/DoiTac/QuanLyDiaDiem.vue',
  'src/components/DoiTac/QuanLyDonHang.vue',
  'src/components/DoiTac/QuanLyTour.vue',
  'src/components/DoiTac/ThongTinTaiKhoan.vue',
  'src/components/KhachHang/ChiTietDiaDiem/index.vue',
  'src/components/KhachHang/DanhSachYeuThich/index.vue',
  'src/components/KhachHang/Dashboard/index.vue',
  'src/components/KhachHang/DiaDiem/index.vue',
  'src/components/KhachHang/HoaDon/ChiTietHoaDon.vue',
  'src/components/KhachHang/LichSuDonHang/index.vue',
  'src/components/KhachHang/Tour/ChiTiet/index.vue',
  'src/components/KhachHang/Tour/index.vue',
  'src/components/KhachHang/Tour/ThanhToan/index.vue',
  'src/components/Tour/ChiTietTour.vue',
  'src/components/Tour/DanhSachTour.vue',
  'src/components/Tour/LichKhoiHanh.vue'
];

files.forEach(file => {
  let content = fs.readFileSync(file, 'utf8');
  let originalContent = content;

  // Add import if not exists
  if (!content.includes('import { createToaster } from') && !content.includes('@meforma/vue-toaster')) {
    const importStr = `import { createToaster } from '@meforma/vue-toaster';\nconst toaster = createToaster({ position: 'top-right' });\n`;
    if (content.includes('export default {')) {
      content = content.replace(/(<script>[\s\S]*?)(export default \{)/, `$1${importStr}\n$2`);
    } else {
      content = content.replace('<script>', `<script>\n${importStr}`);
    }
  }

  // Helper for regex replace
  function replaceState(propName, method) {
    const regex = new RegExp(`this\\.${propName}\\s*=\\s*(.+?);(?!\\s*\\/\\/)`, 'g');
    content = content.replace(regex, (match, val) => {
      val = val.trim();
      if (val === "''" || val === '""' || val === '``' || val === 'null' || val === '"" ') {
        return `// cleared ${propName}`;
      }
      return `toaster.${method}(${val});`;
    });
  }

  replaceState('thong_bao', 'success');
  replaceState('thong_bao_loi', 'error');
  replaceState('successMessage', 'success');
  replaceState('errorMessage', 'error');

  if (content !== originalContent) {
    fs.writeFileSync(file, content);
    console.log('Updated', file);
  }
});
