<template>
  <div class="destination-page">
    <div class="destination-layout">
      <CustomerSidebar />

      <section class="destination-content">
        <header class="destination-hero">
          <div>
            <p class="destination-hero__eyebrow">Khám phá điểm đến</p>
            <h1>Danh sách địa điểm du lịch</h1>
            <p class="destination-hero__subtitle">
              Tìm kiếm nhanh, lọc theo khu vực và mở chi tiết từng điểm đến để lên kế hoạch hành trình.
            </p>
          </div>
        </header>

        <section class="stats-row">
          <article class="stats-tile">
            <span>Tổng địa điểm</span>
            <strong>{{ totalDestinations }}</strong>
          </article>
          <article class="stats-tile">
            <span>Đang hiển thị</span>
            <strong>{{ displayedCount }}</strong>
          </article>
          <article class="stats-tile">
            <span>Khu vực đang lọc</span>
            <strong>{{ boLocKhuVuc.find((x) => x.value === khu_vuc_dang_chon)?.label || "Tất cả" }}</strong>
          </article>
        </section>

        <section class="toolbar">
          <form class="toolbar__search" @submit.prevent="timKiemDiaDiem">
            <i class="fas fa-magnifying-glass"></i>
            <input
              v-model.trim="boLoc.tim_kiem"
              type="text"
              placeholder="Bạn muốn đi đâu? (Ví dụ: Hạ Long, Sa Pa...)"
              :disabled="dang_tai"
            >
            <button class="toolbar__button" type="submit" :disabled="dang_tai">
              {{ dang_tai ? "Đang tải..." : "Tìm kiếm" }}
            </button>
          </form>

          <div class="destination-filter-list">
            <button
              v-for="item in boLocKhuVuc"
              :key="item.value"
              type="button"
              class="destination-filter"
              :class="{ 'is-active': khu_vuc_dang_chon === item.value }"
              @click="chonKhuVuc(item.value)"
            >
              {{ item.label }}
            </button>
          </div>
        </section>

        <div v-if="thong_bao_loi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thong_bao_loi }}</span>
        </div>

        <div v-else-if="dang_tai" class="notice notice--info">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang tải danh sách địa điểm...</span>
        </div>

        <div v-else-if="!danh_sach_hien_thi.length" class="empty-state">
          <i class="fas fa-map-location-dot"></i>
          <h2>Chưa tìm thấy địa điểm phù hợp</h2>
          <p>Hãy thử đổi từ khóa hoặc khu vực lọc để xem thêm kết quả.</p>
        </div>

        <template v-else>
          <div class="destination-grid">
            <article
              v-for="diaDiem in danh_sach_hien_thi"
              :key="diaDiem.id"
              class="destination-card"
              :style="{ backgroundImage: `linear-gradient(180deg, rgba(9, 24, 41, 0.12), rgba(9, 24, 41, 0.8)), url(${diaDiem.hinh_anh})` }"
              @click="moChiTietDiaDiem(diaDiem)"
            >
              <div class="destination-card__badge-row">
                <span class="destination-card__badge">{{ diaDiem.loai_hien_thi }}</span>
                <span class="destination-card__rating">
                  <i class="fas fa-star"></i>
                  {{ diaDiem.danh_gia }}
                </span>
              </div>

              <div class="destination-card__content">
                <h3>{{ diaDiem.ten }}</h3>
                <div class="destination-card__bottom-row">
                  <button
                    type="button"
                    class="destination-card__favorite-btn"
                    :class="{ 'is-active': yeuThichMap[diaDiem.id] }"
                    :title="yeuThichMap[diaDiem.id] ? 'Bỏ yêu thích' : 'Thêm vào danh sách yêu thích'"
                    @click.stop="toggleYeuThich(diaDiem)"
                  >
                    <i class="fas fa-heart"></i>
                  </button>
                  <p>
                    <i class="fas fa-location-dot"></i>
                    {{ diaDiem.dia_chi_hien_thi }}
                  </p>
                </div>
              </div>
            </article>
          </div>

          <nav v-if="tong_so_trang > 1" class="listing-pagination" aria-label="Phân trang địa điểm">
            <button
              type="button"
              class="listing-pagination__button"
              :disabled="!pagination.hasPreviousPage"
              @click="diTrang(trang_hien_tai - 1)"
            >
              Trước
            </button>

            <button
              v-for="page in danh_sach_trang"
              :key="page"
              type="button"
              class="listing-pagination__page"
              :class="{ 'is-active': page === trang_hien_tai }"
              @click="diTrang(page)"
            >
              {{ page }}
            </button>

            <button
              type="button"
              class="listing-pagination__button"
              :disabled="!pagination.hasNextPage"
              @click="diTrang(trang_hien_tai + 1)"
            >
              Sau
            </button>
          </nav>
        </template>
      </section>
    </div>
  </div>
</template>

<script>
import CustomerSidebar from "../CustomerSidebar.vue";
import { goiApi } from "../../../services/httpClient.js";
import { API_BASE, buildHeaders, getStoredCustomerId } from "../../Shared/customerSession";
import { showConfirm } from "../../../services/appDialog";
import authStorage from "../../../services/authStorage";
import {
  buildPaginatedListing,
  buildPaginationWindow,
  resetListingPage,
} from "../listingPagination.js";
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({ position: "top-right" });

const DIA_DIEM_API = "/api/dia-diem";
const HINH_MAC_DINH = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80";

async function goiDuLieu(url, { method = "GET", body, params, headers } = {}) {
  const phanHoi = await goiApi(url, { method, body, params, headers });
  const duLieu = await phanHoi.json();

  if (!phanHoi.ok) {
    const loi = new Error(duLieu?.message || "Không thể hoàn tất yêu cầu.");
    loi.phanHoi = { data: duLieu, status: phanHoi.status };
    throw loi;
  }

  return { data: duLieu, status: phanHoi.status };
}

export default {
  name: "KhachHangDanhSachDiaDiemPage",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      yeuThichMap: {},
      dang_tai: false,
      thong_bao_loi: "",
      trang_hien_tai: 1,
      moi_trang: 8,
      khu_vuc_dang_chon: "tat-ca",
      maKhachHang: "",
      boLoc: {
        tim_kiem: this.$route.query.search || "",
      },
      danh_sach_dia_diem: [],
      boLocKhuVuc: [
        { label: "Tất cả", value: "tat-ca" },
        { label: "Miền Bắc", value: "mien-bac" },
        { label: "Miền Trung", value: "mien-trung" },
        { label: "Miền Nam", value: "mien-nam" },
        { label: "Tây Nguyên", value: "tay-nguyen" },
        { label: "Biển đảo", value: "bien-dao" },
        { label: "Vùng cao", value: "vung-cao" },
      ],
    };
  },
  computed: {
    danh_sach_da_loc() {
      const keyword = this.boLoc.tim_kiem.trim().toLowerCase();

      return this.danh_sach_dia_diem.filter((diaDiem) => {
        const hopTuKhoa =
          !keyword ||
          `${diaDiem.ten} ${diaDiem.mo_ta_ngan} ${diaDiem.dia_chi_hien_thi}`.toLowerCase().includes(keyword);

        const hopKhuVuc = this.khu_vuc_dang_chon === "tat-ca" || diaDiem.khu_vuc === this.khu_vuc_dang_chon;

        return hopTuKhoa && hopKhuVuc;
      });
    },
    pagination() {
      return buildPaginatedListing(this.danh_sach_da_loc, {
        currentPage: this.trang_hien_tai,
        perPage: this.moi_trang,
      });
    },
    danh_sach_hien_thi() {
      return this.pagination.items;
    },
    danh_sach_trang() {
      return buildPaginationWindow(this.tong_so_trang, this.trang_hien_tai);
    },
    tong_so_trang() {
      return this.pagination.totalPages;
    },
    totalDestinations() {
      return this.danh_sach_dia_diem.length;
    },
    displayedCount() {
      return this.danh_sach_hien_thi.length;
    },
  },
  mounted() {
    this.maKhachHang = getStoredCustomerId();
    this.taiDanhSachDiaDiem();
    this.taiDanhSachYeuThich();
  },
  methods: {
    xuLyDanhSach(duLieuPhanHoi) {
      if (Array.isArray(duLieuPhanHoi)) {
        return duLieuPhanHoi;
      }
      if (Array.isArray(duLieuPhanHoi?.data)) {
        return duLieuPhanHoi.data;
      }
      if (Array.isArray(duLieuPhanHoi?.data?.data)) {
        return duLieuPhanHoi.data.data;
      }
      if (Array.isArray(duLieuPhanHoi?.result)) {
        return duLieuPhanHoi.result;
      }
      if (Array.isArray(duLieuPhanHoi?.result?.data)) {
        return duLieuPhanHoi.result.data;
      }
      return [];
    },
    layGiaTriDauTien(obj, keys, macDinh = "") {
      for (const key of keys) {
        if (obj && obj[key] !== undefined && obj[key] !== null && obj[key] !== "") {
          return obj[key];
        }
      }
      return macDinh;
    },
    xacDinhKhuVuc(diaChi = "", ten = "") {
      const noiDung = `${diaChi} ${ten}`.toLowerCase();

      if (/(ha giang|hà giang|sa pa|sapa|lào cai|lao cai|cao bằng|cao bang|mộc châu|moc chau)/.test(noiDung)) {
        return "vung-cao";
      }
      if (/(phú quốc|phu quoc|côn đảo|con dao|lý sơn|ly son|hạ long|ha long|nha trang|phan thiết|phan thiet)/.test(noiDung)) {
        return "bien-dao";
      }
      if (/(đà lạt|da lat|buôn ma thuột|buon ma thuot|gia lai|kon tum|đắk|dak|pleiku)/.test(noiDung)) {
        return "tay-nguyen";
      }
      if (/(hà nội|ha noi|ninh bình|ninh binh|hải phòng|hai phong|quảng ninh|quang ninh)/.test(noiDung)) {
        return "mien-bac";
      }
      if (/(đà nẵng|da nang|hội an|hoi an|huế|hue|quảng nam|quang nam|quảng bình|quang binh)/.test(noiDung)) {
        return "mien-trung";
      }
      if (/(hồ chí minh|ho chi minh|cần thơ|can tho|an giang|kiên giang|kien giang|vũng tàu|vung tau)/.test(noiDung)) {
        return "mien-nam";
      }
      return "tat-ca";
    },
    tenLoai(loai) {
      if (String(loai) === "2") return "Nghỉ dưỡng";
      if (String(loai) === "3") return "Ẩm thực";
      return "Nổi bật";
    },
    chuanHoaDiaDiem(diaDiem = {}, index = 0) {
      const diaChi = this.layGiaTriDauTien(diaDiem, ["dia_chi", "Dia_chi", "address"], "Việt Nam");
      const ten = this.layGiaTriDauTien(diaDiem, ["ten_dia_diem", "Ten_dia_diem", "ten", "name"], "Địa điểm nổi bật");
      const loai = this.layGiaTriDauTien(diaDiem, ["loai", "Loai"], 1);

      return {
        id: this.layGiaTriDauTien(diaDiem, ["ma_dia_diem", "Ma_dia_diem", "id", "Id"], index + 1),
        ten,
        mo_ta_ngan: this.layGiaTriDauTien(diaDiem, ["mo_ta_ngan", "Mo_ta_ngan", "mo_ta", "description"], "Khám phá thêm nhiều trải nghiệm độc đáo tại địa điểm này."),
        hinh_anh: this.layGiaTriDauTien(diaDiem, ["hinh_anh", "Hinh_anh", "thumbnail", "image"], HINH_MAC_DINH),
        dia_chi_hien_thi: diaChi,
        loai_hien_thi: this.tenLoai(loai),
        danh_gia: this.layGiaTriDauTien(diaDiem, ["danh_gia", "Danh_gia", "rating"], "4.9"),
        khu_vuc: this.xacDinhKhuVuc(diaChi, ten),
      };
    },
    async taiDanhSachDiaDiem() {
      this.dang_tai = true;
      // cleared thong_bao_loi

      try {
        const res = await goiDuLieu(DIA_DIEM_API, {
          params: {
            per_page: 100,
          },
        });
        this.danh_sach_dia_diem = this.xuLyDanhSach(res.data).map((item, index) => this.chuanHoaDiaDiem(item, index));
        this.trang_hien_tai = resetListingPage();
      } catch (error) {
        toaster.error(error?.phanHoi?.data?.message || "Không thể tải danh sách địa điểm.");
      } finally {
        this.dang_tai = false;
      }
    },
    timKiemDiaDiem() {
      this.trang_hien_tai = resetListingPage();
      this.$router.replace({
        path: "/khach-hang/dia-diem",
        query: this.boLoc.tim_kiem.trim() ? { search: this.boLoc.tim_kiem.trim() } : {},
      });
    },
    chonKhuVuc(value) {
      this.khu_vuc_dang_chon = value;
      this.trang_hien_tai = resetListingPage();
    },
    diTrang(page) {
      this.trang_hien_tai = page;
    },
    moChiTietDiaDiem(diaDiem) {
      if (!diaDiem?.id) return;
      this.$router.push(`/khach-hang/dia-diem/${diaDiem.id}`);
    },
    async yeuCauDangNhap(hanhDong) {
      const token = authStorage.getToken("customer") || localStorage.getItem("token");
      if (token) {
        return true;
      }
      const xacNhan = await showConfirm({
        title: "Yêu cầu đăng nhập",
        message: `Bạn cần đăng nhập để ${hanhDong}. Chuyển tới trang đăng nhập ngay bây giờ?`,
        tone: "warning"
      });
      if (xacNhan) {
        this.$router.push("/dang-nhap");
      }
      return false;
    },
    async taiDanhSachYeuThich() {
      if (!this.maKhachHang) return;
      try {
        const res = await goiDuLieu(`${API_BASE}/khach-hang/danh-sach-yeu-thich`, {
          params: { ma_khach_hang: this.maKhachHang },
          headers: buildHeaders(),
        });
        const list = this.xuLyDanhSach(res.data);
        const map = {};
        list.forEach((item) => {
          const mdd = String(item?.ma_dia_diem || item?.Ma_dia_diem || item?.dia_diem?.ma_dia_diem || item?.dia_diem?.Ma_dia_diem || item?.diaDiem?.Ma_dia_diem || "");
          const id = String(item?.ma_danh_sach_ua_thich || item?.Ma_danh_sach_ua_thich || item?.id || item?.Id || "");
          if (mdd && id) {
            map[mdd] = id;
          }
        });
        this.yeuThichMap = map;
      } catch (error) {
        console.error("Lỗi lấy danh sách yêu thích", error);
      }
    },
    async toggleYeuThich(diaDiem) {
      const isLogged = await this.yeuCauDangNhap("thêm địa điểm vào danh sách yêu thích");
      if (!isLogged) {
        return;
      }

      if (!this.maKhachHang || !diaDiem?.id) {
        toaster.warning("Không đủ thông tin để thực hiện thao tác.");
        return;
      }

      const idYeuThich = this.yeuThichMap[diaDiem.id];

      if (idYeuThich) {
        try {
          await goiDuLieu(`${API_BASE}/khach-hang/danh-sach-yeu-thich/${idYeuThich}`, {
            method: "DELETE",
            params: { ma_khach_hang: this.maKhachHang },
            headers: buildHeaders(),
          });

          const newMap = { ...this.yeuThichMap };
          delete newMap[diaDiem.id];
          this.yeuThichMap = newMap;
          toaster.success("Đã xóa địa điểm khỏi danh sách yêu thích.");
        } catch (error) {
          const msg = error?.phanHoi?.data?.message || "Không thể xóa khỏi danh sách yêu thích.";
          toaster.error(msg);
        }
      } else {
        try {
          await goiDuLieu(`${API_BASE}/khach-hang/danh-sach-yeu-thich`, {
            method: "POST",
            body: {
              ma_khach_hang: this.maKhachHang,
              ma_dia_diem: diaDiem.id,
            },
            headers: buildHeaders(true),
          });

          await this.taiDanhSachYeuThich();
          toaster.success("Đã lưu địa điểm vào danh sách yêu thích.");
        } catch (error) {
          const msg = error?.phanHoi?.data?.message || "Không thể lưu địa điểm vào danh sách yêu thích.";
          toaster.error(msg);
        }
      }
    },
  },
  watch: {
    "$route.query.search"(value) {
      this.boLoc.tim_kiem = value || "";
      this.trang_hien_tai = resetListingPage();
    },
    "pagination.currentPage"(value) {
      if (value !== this.trang_hien_tai) {
        this.trang_hien_tai = value;
      }
    },
  },
};
</script>

<style scoped>
.destination-page {
  background: radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%), linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
}

.destination-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 18px;
  min-height: calc(100vh - 68px);
}

.destination-content {
  padding: 28px 6px 0 0;
}

.destination-hero {
  margin-bottom: 20px;
}

.destination-hero__eyebrow {
  margin: 0 0 10px;
  color: #4f25f4;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.destination-hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.1rem, 3.3vw, 3rem);
  line-height: 1.06;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.destination-hero__subtitle {
  margin: 10px 0 0;
  color: #5f6f89;
  max-width: 720px;
  font-size: 1rem;
  line-height: 1.7;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
  margin-bottom: 20px;
}

.stats-tile,
.toolbar,
.notice,
.empty-state,
.destination-card,
.listing-pagination {
  border-radius: 24px;
  border: 1px solid #e2eaf5;
  background: rgba(255, 255, 255, 0.9);
  box-shadow: 0 16px 34px rgba(31, 65, 114, 0.06);
}

.stats-tile {
  padding: 22px;
}

.stats-tile span {
  color: #617891;
  font-weight: 700;
}

.stats-tile strong {
  display: block;
  margin-top: 10px;
  color: #0d2f57;
  font-size: 1.95rem;
  line-height: 1;
  font-weight: 900;
}

.toolbar {
  padding: 18px;
  margin-bottom: 18px;
}

.toolbar__search {
  display: grid;
  grid-template-columns: 20px minmax(0, 1fr) auto;
  align-items: center;
  gap: 12px;
  min-height: 56px;
  border-radius: 18px;
  border: 1px solid #dfe7f3;
  background: #f8fbff;
  padding: 0 16px;
}

.toolbar__search input {
  border: 0;
  background: transparent;
  outline: none;
  color: #24415d;
}

.toolbar__button {
  min-height: 40px;
  border: 0;
  border-radius: 12px;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  color: #fff;
  font-weight: 800;
  padding: 0 14px;
}

.destination-filter-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 14px;
}

.destination-filter {
  min-height: 40px;
  padding: 0 14px;
  border-radius: 999px;
  border: 1px solid transparent;
  background: #ebf2fc;
  color: #4b6788;
  font-weight: 700;
}

.destination-filter.is-active {
  color: #fff;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
}

.notice {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  margin-bottom: 18px;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.notice--info {
  background: #eff6ff;
  color: #1d4ed8;
}

.destination-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.destination-card {
  position: relative;
  min-height: 255px;
  padding: 0;
  display: block;
  overflow: hidden;
  background-size: cover;
  background-position: center;
  cursor: pointer;
}

.destination-card__badge-row {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem;
}

.destination-card__badge {
  padding: 0.32rem 0.65rem;
  border-radius: 999px;
  background: rgba(168, 139, 250, 0.92);
  color: #ffffff;
  font-size: 0.72rem;
  font-weight: 800;
}

.destination-card__rating {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  color: #ffffff;
  font-size: 0.78rem;
  font-weight: 700;
}

.destination-card__rating i {
  color: #fbbf24;
}

.destination-card__content {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  padding: 1.05rem;
  color: #ffffff;
}

.destination-card__content h3 {
  margin: 0;
  color: #ffffff;
  font-size: 1.08rem;
  font-weight: 900;
}

.destination-card__content p {
  margin: 0;
  color: rgba(255, 255, 255, 0.86);
  font-size: 0.88rem;
  font-weight: 600;
  line-height: 1.6;
}

.destination-card__bottom-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 0.65rem;
}

.destination-card__favorite-btn {
  background: rgba(255, 255, 255, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.5);
  color: #fff;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.destination-card__favorite-btn:hover {
  background: #ff4d4f;
  border-color: #ff4d4f;
  transform: scale(1.1);
}

.destination-card__favorite-btn.is-active {
  background: #ff4d4f;
  border-color: #ff4d4f;
  color: #fff;
}

.empty-state {
  min-height: 260px;
  display: grid;
  place-items: center;
  text-align: center;
  gap: 10px;
  padding: 28px;
}

.empty-state i {
  width: 72px;
  height: 72px;
  border-radius: 22px;
  display: grid;
  place-items: center;
  background: #e7f2ff;
  color: #0b63a0;
  font-size: 1.5rem;
}

.empty-state p {
  max-width: 560px;
  margin: 0;
  color: #617891;
  line-height: 1.7;
}

.listing-pagination {
  margin-top: 20px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.listing-pagination__button,
.listing-pagination__page {
  min-width: 44px;
  min-height: 44px;
  border-radius: 14px;
  border: 1px solid #d7e2f0;
  background: #ffffff;
  color: #24415d;
  font-weight: 800;
  transition: all 0.2s ease;
}

.listing-pagination__button:disabled,
.listing-pagination__page:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.listing-pagination__page.is-active {
  border-color: transparent;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  color: #ffffff;
  box-shadow: 0 12px 22px rgba(11, 99, 160, 0.2);
}

@media (max-width: 1200px) {
  .destination-layout {
    grid-template-columns: 1fr;
  }

  .destination-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {
  .destination-layout {
    width: calc(100% - 20px);
  }

  .stats-row,
  .destination-grid {
    grid-template-columns: 1fr;
  }

  .toolbar__search {
    grid-template-columns: 20px minmax(0, 1fr);
    padding-top: 10px;
    padding-bottom: 10px;
  }

  .toolbar__button {
    grid-column: 1 / -1;
  }

  .listing-pagination {
    flex-wrap: wrap;
  }
}
</style>
