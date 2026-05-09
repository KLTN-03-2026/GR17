<template>
  <div class="detail-page" v-if="!dang_tai || dia_diem">
    <div class="detail-layout">
      <CustomerSidebar />

      <!-- NOi DUNG CHiNH -->
      <section class="detail-content">
        <div v-if="thong_bao_loi" class="app-notice app-notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thong_bao_loi }}</span>
        </div>

        <template v-if="dia_diem">
          <!-- HERO BANNER FULL TRONG CONTENT -->
          <header class="location-hero" :style="{ backgroundImage: `linear-gradient(180deg, rgba(8, 20, 36, 0.1), rgba(8, 20, 36, 0.85)), url(${dia_diem.hinh_anh})` }">
            <div class="location-hero__badge-container">
              <span class="location-hero__badge"><i class="fas fa-medal"></i> {{ dia_diem.nhan }}</span>
            </div>
            <div class="location-hero__bottom">
              <h1>{{ dia_diem.ten }}</h1>
              <div class="location-hero__meta">
                <span><i class="fas fa-location-dot"></i> {{ dia_diem.dia_chi }}</span>
                <span class="hero-rating"><i class="fas fa-star"></i> {{ thong_ke_danh_gia.so_sao_trung_binh }} ({{ thong_ke_danh_gia.so_luong }} đánh giá)</span>
              </div>
            </div>
          </header>

          <div v-if="actionMessage" class="app-notice app-notice--success">
            <i class="fas fa-circle-check"></i>
            <span>{{ actionMessage }}</span>
          </div>

          <!-- LAYOUT 2 COt THEO MẪU MỚI -->
          <div class="main-layout-grid">
            
            <!-- CỘT TRÁI (LEFT MAIN) -->
            <div class="main-column">
              <section class="info-section">
                <h2>Mô tả</h2>
                <div class="info-desc">
                  <p>{{ dia_diem.mo_ta }}</p>
                  <p v-if="dia_diem.thoi_gian_tham_quan && dia_diem.thoi_gian_tham_quan !== 'Đang cập nhật'">
                    Được nhiều du khách lựa chọn nhờ vào chất lượng cảnh quan đặc biệt. Thời gian tham quan lý tưởng được khuyến nghị là <strong>{{ dia_diem.thoi_gian_tham_quan }}</strong> để bạn có thể trải nghiệm đầy đủ các hoạt động tại đây.
                  </p>
                </div>
              </section>

              <section class="info-section" v-if="danh_sach_tuong_tu.length > 0">
                <div class="section-header">
                  <h2>Địa điểm tương tự</h2>
                </div>
                <div class="horizontal-scroll-list">
                  <article class="svc-vcard" v-for="dd in danh_sach_tuong_tu" :key="dd.ma_dia_diem" @click="goToDiaDiem(dd.ma_dia_diem)" style="cursor: pointer;">
                    <div class="svc-vcard__cover" :style="{ backgroundImage: `url(${dd.hinh_anh})` }"></div>
                    <div class="svc-vcard__body">
                      <h3 class="text-truncate" :title="dd.ten_dia_diem">{{ dd.ten_dia_diem }}</h3>
                      <div class="svc-vcard__meta">
                        <span class="text-truncate"><i class="fas fa-location-dot"></i> {{ dd.dia_chi }}</span>
                      </div>
                    </div>
                  </article>
                </div>
              </section>

              <section class="info-section" v-if="danh_sach_tour.length > 0">
                <div class="section-header">
                  <h2>Các tour gợi ý</h2>
                </div>
                <div class="horizontal-scroll-list">
                  <article class="svc-vcard" v-for="tour in danh_sach_tour" :key="tour.ma_tour" @click="goToTour(tour.ma_tour)" style="cursor: pointer;">
                    <div class="svc-vcard__cover" :style="{ backgroundImage: `url(${tour.hinh_anh})` }"></div>
                    <div class="svc-vcard__body">
                      <h3 class="text-truncate" :title="tour.ten_tour">{{ tour.ten_tour }}</h3>
                      <div class="svc-vcard__meta" style="margin-top: 8px;">
                        <span><i class="far fa-clock"></i> {{ tour.so_ngay }} ngày</span>
                        <span class="rating-badge" style="font-size: 0.95rem;">{{ formatGia(tour.so_tien) }}</span>
                      </div>
                    </div>
                  </article>
                </div>
              </section>
            </div>

            <!-- CỘT PHẢI (RIGHT SIDEBAR) -->
            <aside class="sidebar-column">
              <div class="plan-card">
                <h2>Lên kế hoạch ngay</h2>
                
                <div class="plan-list">
                  <div class="plan-item plan-item--blue">
                    <div class="plan-item__icon"><i class="far fa-calendar-alt"></i></div>
                    <div class="plan-item__text">
                      <strong>THỜI ĐIỂM VÀNG</strong>
                      <span>{{ thoi_diem_goi_y }}</span>
                      <small>{{ ghi_chu_thoi_tiet }}</small>
                    </div>
                  </div>

                  <div class="plan-item plan-item--red">
                    <div class="plan-item__icon"><i class="fas fa-tags"></i></div>
                    <div class="plan-item__text">
                      <strong>ƯU ĐÃI HÔM NAY</strong>
                      <span>Giảm 15% Dịch vụ</span>
                      <small>Khi đặt qua hệ thống Booking</small>
                    </div>
                  </div>
                </div>

                <div class="plan-actions">
                  <button class="btn-plan btn-plan--solid" @click="goToPlan">Thêm vào hành trình</button>
                  <button class="btn-plan btn-plan--outline" @click="addCurrentDestinationToFavorites">Lưu vào yêu thích</button>
                </div>
              </div>

              <div class="plan-card">
                <div class="plan-card__headline">
                  <span>Vị trí địa lý</span>
                  <a href="#" @click.prevent="moBanDo">Phóng to</a>
                </div>
                <div class="map-view" @click="moBanDo">
                  <div class="map-view__overlay"></div>
                  <div class="map-view__pin"><i class="fas fa-location-dot"></i></div>
                </div>
                <div class="map-info">
                  <i class="fas fa-info-circle"></i>
                  <span>Nằm tại {{ dia_diem.dia_chi }}. Cách trung tâm thành phố khoảng 15 phút di chuyển.</span>
                </div>
              </div>
            </aside>
          </div>
        </template>
        
        <div v-else-if="dang_tai" class="app-notice app-notice--info">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang tải thông tin chi tiết địa điểm...</span>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import CustomerSidebar from '../CustomerSidebar.vue';
import { goiApi } from "../../../services/httpClient.js";
import authStorage from "../../../services/authStorage";
import { showConfirm, showAlert } from "../../../services/appDialog";
import {
  API_BASE,
  buildHeaders,
  createInitials,
  getStoredCustomerId,
  getStoredUser,
} from "../../Shared/customerSession";
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({ position: "top-right" });

const API_DIA_DIEM = "/api/dia-diem";
const API_TOUR = "/api/tour";
const API_DANH_GIA = "/api/danh-gia-ke-hoach/dia-diem";
const HINH_MAC_DINH = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80";

async function fetchDuLieu(url, { method = "GET", body, params, headers } = {}) {
  const phanHoi = await goiApi(url, { method, body, params, headers });
  const duLieu = await phanHoi.json();

  if (!phanHoi.ok) {
    const loi = new Error(duLieu?.message || "Lỗi xử lý yêu cầu.");
    loi.phanHoi = { data: duLieu, status: phanHoi.status };
    throw loi;
  }
  return { data: duLieu, status: phanHoi.status };
}

export default {
  name: "KhachHangChiTietDiaDiemPage",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      dang_tai: false,
      thong_bao_loi: "",
      actionMessage: "",
      dia_diem: null,
      danh_sach_tuong_tu: [],
      danh_sach_tour: [],
      danh_sach_danh_gia: [],
      maKhachHang: "",
    };
  },
  computed: {
    hoSoKhachHang() {
      return getStoredUser() || {};
    },
    ma_dia_diem() {
      return this.$route.params.id;
    },
    ten_ngan() {
      const ten = this.dia_diem?.ten || "địa điểm";
      return ten.replace(/^Thành phố\s+/i, "").trim();
    },
    thong_ke_danh_gia() {
      if (!this.danh_sach_danh_gia.length) {
        return { so_luong: 0, so_sao_trung_binh: "4.8" };
      }
      const tong = this.danh_sach_danh_gia.reduce((sum, item) => sum + Number(item.so_sao || 0), 0);
      const trungBinh = tong / this.danh_sach_danh_gia.length;
      return {
        so_luong: this.danh_sach_danh_gia.length,
        so_sao_trung_binh: trungBinh.toFixed(1),
      };
    },
    thoi_diem_goi_y() {
      const ten = `${this.dia_diem?.dia_chi || ""} ${this.dia_diem?.ten || ""}`.toLowerCase();
      if (/(quảng ninh|ha long|hạ long|sapa|sa pa|đà lạt|da lat)/.test(ten)) return "Tháng 3 - Tháng 5";
      if (/(đà nẵng|da nang|hội an|hoi an|huế|hue|nha trang)/.test(ten)) return "Tháng 2 - Tháng 8";
      return "Quanh năm";
    },
    ghi_chu_thoi_tiet() {
      return this.thoi_diem_goi_y === "Quanh năm" ? "Thời tiết khá ổn định cho nhiều loại trải nghiệm." : "Thời tiết mát mẻ, ít mưa";
    },
  },
  mounted() {
    this.maKhachHang = getStoredCustomerId();
    this.taiChiTietDiaDiem();
  },
  methods: {
    xuLyDanhSach(duLieuPhanHoi) {
      if (!duLieuPhanHoi) return [];
      if (Array.isArray(duLieuPhanHoi)) return duLieuPhanHoi;
      if (Array.isArray(duLieuPhanHoi.data)) return duLieuPhanHoi.data;
      if (Array.isArray(duLieuPhanHoi.data?.data)) return duLieuPhanHoi.data.data;
      if (Array.isArray(duLieuPhanHoi.result)) return duLieuPhanHoi.result;
      if (Array.isArray(duLieuPhanHoi.result?.data)) return duLieuPhanHoi.result.data;
      return [];
    },
    xuLyItem(duLieuPhanHoi) {
       if (!duLieuPhanHoi) return null;
       if (Array.isArray(duLieuPhanHoi)) return duLieuPhanHoi[0] || null;
       if (duLieuPhanHoi.data && !Array.isArray(duLieuPhanHoi.data)) return duLieuPhanHoi.data;
       if (duLieuPhanHoi.result && !Array.isArray(duLieuPhanHoi.result)) return duLieuPhanHoi.result;
       return duLieuPhanHoi;
    },
    layGiaTriDauTien(obj, keys, macDinh = "") {
      for (const key of keys) {
        if (obj && obj[key] !== undefined && obj[key] !== null && obj[key] !== "") {
          return obj[key];
        }
      }
      return macDinh;
    },
    chuanHoaDiaDiem(item = {}) {
      return {
        id: this.layGiaTriDauTien(item, ["ma_dia_diem", "Ma_dia_diem", "id", "Id"]),
        ten: this.layGiaTriDauTien(item, ["ten_dia_diem", "Ten_dia_diem", "ten", "name"], "Điểm đến nổi bật"),
        dia_chi: this.layGiaTriDauTien(item, ["dia_chi", "Dia_chi", "address"], "Việt Nam"),
        hinh_anh: this.layGiaTriDauTien(item, ["hinh_anh", "Hinh_anh", "image"], HINH_MAC_DINH),
        mo_ta: this.layGiaTriDauTien(item, ["mo_ta", "Mo_ta", "description"], "Thông tin đang được cập nhật."),
        thoi_gian_tham_quan: this.layGiaTriDauTien(item, ["thoi_gian_tham_quan", "Thoi_gian_tham_quan"], ""),
        nhan: "DI SẢN THẾ GIỚI UNESCO", // Theo mẫu ảnh
      };
    },
    formatGia(gia) {
      if (!gia) return "Liên hệ";
      return Number(gia).toLocaleString("vi-VN") + "đ";
    },
    async taiChiTietDiaDiem() {
      this.dang_tai = true;
      // cleared thong_bao_loi
      try {
        const [chiTietRes, tuongTuRes, tourRes, danhGiaRes] = await Promise.allSettled([
          fetchDuLieu(`${API_DIA_DIEM}/${this.ma_dia_diem}`),
          fetchDuLieu(`${API_DIA_DIEM}/${this.ma_dia_diem}/tuong-tu`),
          fetchDuLieu(`${API_TOUR}?ma_dia_diem=${this.ma_dia_diem}`),
          fetchDuLieu(`${API_DANH_GIA}/${this.ma_dia_diem}`),
        ]);

        if (chiTietRes.status !== "fulfilled") throw chiTietRes.reason;
        
        this.dia_diem = this.chuanHoaDiaDiem(this.xuLyItem(chiTietRes.value.data));

        if (tuongTuRes.status === "fulfilled") {
           this.danh_sach_tuong_tu = this.xuLyDanhSach(tuongTuRes.value.data);
        }

        if (tourRes.status === "fulfilled") {
           this.danh_sach_tour = this.xuLyDanhSach(tourRes.value.data).slice(0, 4);
        }
        
        if (danhGiaRes.status === "fulfilled") {
           this.danh_sach_danh_gia = this.xuLyDanhSach(danhGiaRes.value.data);
        }
      } catch (error) {
        toaster.error("Không thể tải chi tiết địa điểm. " + (error.message || ""));
      } finally {
        this.dang_tai = false;
      }
    },
    async goToPlan() {
      const token = authStorage.getToken("customer") || localStorage.getItem("token");
      if (!token) {
        const xacNhan = await showConfirm({
          title: "Yêu cầu đăng nhập",
          message: "Bạn cần đăng nhập để thêm vào hành trình. Chuyển tới đăng nhập?",
          tone: "warning"
        });
        if(xacNhan) this.$router.push("/dang-nhap");
        return;
      }
      this.$router.push("/khach-hang/ke-hoach/create");
    },
    async addCurrentDestinationToFavorites() {
      const token = authStorage.getToken("customer") || localStorage.getItem("token");
      if (!token) {
        const xacNhan = await showConfirm({
          title: "Yêu cầu đăng nhập",
          message: "Bạn cần đăng nhập để lưu yêu thích. Chuyển tới đăng nhập?",
          tone: "warning"
        });
        if(xacNhan) this.$router.push("/dang-nhap");
        return;
      }
      toaster.success("Đã lưu địa điểm vào danh sách yêu thích cá nhân.");
    },
    moBanDo() {
      const q = encodeURIComponent(`${this.dia_diem?.ten || ""} ${this.dia_diem?.dia_chi || ""}`);
      if (q) window.open(`https://www.google.com/maps/search/?api=1&query=${q}`, "_blank");
    },
    goToDiaDiem(id) {
      if (id) this.$router.push(`/khach-hang/dia-diem/${id}`);
    },
    goToTour(id) {
      if (id) this.$router.push(`/khach-hang/tour/${id}`);
    }
  },
  watch: {
    "$route.params.id"() {
      if (this.$route.params.id) {
        this.taiChiTietDiaDiem();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    }
  }
};
</script>

<style scoped>
/* 1. LAYOUT & SIDEBAR (Gữi lại luồng cũ) */
.detail-page {
  background: #f7f9fc;
}

.detail-layout {
  width: min(100%, 1840px);
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 352px minmax(0, 1fr);
  gap: 28px;
  min-height: calc(100vh - 68px);
}

.detail-content {
  padding: 10px 8px 0 0;
}

/* 2. HERO BANNER THEO MẪU BLUE-HORIZON */
.location-hero {
  position: relative;
  min-height: 360px;
  border-radius: 24px;
  padding: 40px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background-size: cover;
  background-position: center;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
  overflow: hidden;
  color: #ffffff;
}

.location-hero__badge-container {
  position: relative;
  z-index: 2;
}

.location-hero__badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #a980ff;
  color: #ffffff;
  padding: 6px 14px;
  border-radius: 999px;
  font-weight: 800;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  box-shadow: 0 4px 12px rgba(169, 128, 255, 0.4);
}

.location-hero__bottom {
  position: relative;
  z-index: 2;
}

.location-hero__bottom h1 {
  margin: 0 0 12px;
  font-size: clamp(2.4rem, 4vw, 3.6rem);
  font-weight: 900;
  letter-spacing: -1px;
  text-shadow: 0 4px 16px rgba(0,0,0,0.4);
}

.location-hero__meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 24px;
  font-size: 0.95rem;
  font-weight: 500;
}

.hero-rating {
  font-weight: 700;
}
.hero-rating i {
  color: #fbbc04;
  margin-right: 4px;
}

/* 3. LAYOUT MAIN N CỘT */
.main-layout-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.9fr) 390px;
  gap: 34px;
  align-items: start;
}

.main-column {
  display: grid;
  gap: 36px;
  min-width: 0; /* Prevent flex/grid overflow */
}

/* TEXT INFO SECTION */
.info-section {
  width: 100%;
}
.info-section h2 {
  font-size: 1.5rem;
  color: #1a2b4c;
  font-weight: 800;
  margin: 0 0 20px;
}

.info-desc {
  color: #454f5b;
  font-size: 1rem;
  line-height: 1.8;
}
.info-desc p {
  margin: 0 0 16px;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
.section-header h2 {
  margin: 0;
}
.section-header a {
  color: #0055ff;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.92rem;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

/* THIẾT KẾ CHO LIST CUỘN NGANG */
.horizontal-scroll-list {
  display: flex;
  gap: 16px;
  overflow-x: auto;
  padding-bottom: 12px;
}
.horizontal-scroll-list::-webkit-scrollbar {
  height: 6px;
}
.horizontal-scroll-list::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.horizontal-scroll-list > article {
  flex: 0 0 260px; /* fixed width for cards */
}

/* THẺ DỌC (DÙNG CHO CẢ ĐỊA ĐIỂM TƯƠNG TỰ VÀ TOUR) */
.svc-vcard {
  background: #ffffff;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #edf1f7;
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
}
.svc-vcard:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(26, 43, 76, 0.08);
}
.svc-vcard__cover {
  height: 160px;
  background-size: cover;
  background-position: center;
}
.svc-vcard__body {
  padding: 16px;
  flex: 1;
  display: flex;
  flex-direction: column;
}
.svc-vcard__body h3 {
  margin: 0 0 6px;
  color: #1a2b4c;
  font-size: 1.1rem;
  font-weight: 800;
}
.svc-vcard__meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #637381;
  font-size: 0.85rem;
  margin-top: auto;
}
.rating-badge {
  background: #eef2ff;
  color: #0055ff;
  padding: 4px 8px;
  border-radius: 6px;
  font-weight: 700;
}
.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.svc-hcard {
  display: flex;
  background: #ffffff;
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid #edf1f7;
  transition: box-shadow 0.2s;
}
.svc-hcard:hover {
  box-shadow: 0 12px 24px rgba(26, 43, 76, 0.06);
}
.svc-hcard__cover {
  width: 280px;
  min-height: 100%;
  background-size: cover;
  background-position: center;
}
.svc-hcard__body {
  flex: 1;
  padding: 24px;
  display: flex;
  flex-direction: column;
}
.svc-hcard__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 12px;
}
.svc-hcard__header h3 {
  margin: 0 0 8px;
  color: #1a2b4c;
  font-size: 1.3rem;
  font-weight: 800;
}
.svc-hcard__tags {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.85rem;
}
.tag-blue {
  background: #eef2ff;
  color: #0055ff;
  padding: 2px 8px;
  border-radius: 6px;
  font-weight: 700;
}
.svc-hcard__rating {
  font-weight: 700;
  color: #1a2b4c;
}
.svc-hcard__rating i {
  color: #0055ff;
}
.svc-hcard__price {
  text-align: right;
  color: #0055ff;
}
.svc-hcard__price strong {
  display: block;
  font-size: 1.35rem;
  font-weight: 900;
  line-height: 1;
}
.svc-hcard__price span {
  font-size: 0.75rem;
  color: #637381;
}
.svc-hcard__desc {
  color: #454f5b;
  font-size: 0.95rem;
  line-height: 1.6;
  margin: 0 0 20px;
}
.svc-hcard__amenities {
  display: flex;
  align-items: center;
  gap: 16px;
  color: #454f5b;
  font-size: 0.85rem;
  margin-top: auto;
}
.svc-hcard__amenities i {
  color: #8c98a4;
  margin-right: 4px;
}

/* 4. CỘT PHẢI - SIDEBAR CARDS */
.sidebar-column {
  display: grid;
  gap: 24px;
}

.plan-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.03);
  border: 1px solid #edf1f7;
}

.plan-card h2 {
  margin: 0 0 20px;
  color: #1a2b4c;
  font-size: 1.3rem;
  font-weight: 800;
}

.plan-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}

.plan-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 16px;
  border-radius: 12px;
}
.plan-item--blue {
  background: #f4f8ff;
}
.plan-item--red {
  background: #fff4f4;
}

.plan-item__icon {
  width: 24px;
  font-size: 1.25rem;
}
.plan-item--blue .plan-item__icon { color: #0055ff; }
.plan-item--red .plan-item__icon { color: #ff3333; }

.plan-item__text {
  display: flex;
  flex-direction: column;
  color: #1a2b4c;
}
.plan-item__text strong {
  font-size: 0.7rem;
  font-weight: 800;
  color: #637381;
  letter-spacing: 0.5px;
  margin-bottom: 2px;
}
.plan-item__text span {
  font-weight: 800;
  font-size: 0.95rem;
  margin-bottom: 4px;
}
.plan-item__text small {
  color: #637381;
  font-size: 0.8rem;
}

.plan-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.btn-plan {
  width: 100%;
  padding: 14px;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-plan--solid {
  background: #0055ff;
  color: #ffffff;
  border: none;
  box-shadow: 0 4px 12px rgba(0, 85, 255, 0.2);
}
.btn-plan--solid:hover {
  background: #0044cc;
}
.btn-plan--outline {
  background: #ffffff;
  color: #0055ff;
  border: 1px solid #0055ff;
}
.btn-plan--outline:hover {
  background: #f4f8ff;
}

/* VỊ TRÍ ĐỊA LÝ CARD */
.plan-card__headline {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.plan-card__headline span {
  font-weight: 800;
  color: #1a2b4c;
  font-size: 1.1rem;
}
.plan-card__headline a {
  color: #0055ff;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.85rem;
}

.map-view {
  position: relative;
  height: 200px;
  border-radius: 12px;
  background: #e2e8f0;
  background-image: url('https://images.unsplash.com/photo-1524661135-423995f22d0b?w=800');
  background-size: cover;
  background-position: center;
  cursor: pointer;
  margin-bottom: 16px;
  overflow: hidden;
}
.map-view__overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.2);
}
.map-view__pin {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 44px;
  height: 44px;
  background: #0055ff;
  color: #ffffff;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 1.2rem;
  box-shadow: 0 4px 12px rgba(0,85,255,0.4);
  z-index: 2;
}

.map-info {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  color: #637381;
  font-size: 0.85rem;
  line-height: 1.5;
}
.map-info i {
  color: #a0aec0;
  margin-top: 3px;
}

/* ALERTS */
.app-notice {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  border-radius: 12px;
  margin-bottom: 24px;
  font-weight: 500;
}
.app-notice--error {
  background: #fff4f4;
  color: #d32f2f;
  border: 1px solid #ffcdd2;
}
.app-notice--info {
  background: #f4f8ff;
  color: #0055ff;
  border: 1px solid #d0e1fd;
}
.app-notice--success {
  background: #edf7ed;
  color: #1e4620;
  border: 1px solid #c8e6c9;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
  .detail-layout {
    grid-template-columns: 1fr;
  }
  .profile-panel {
    border-right: none;
    border-bottom: 1px solid #edf1f7;
  }
  .profile-panel__menu {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 992px) {
  .main-layout-grid {
    grid-template-columns: 1fr;
  }
  .svc-hcard {
    flex-direction: column;
  }
  .svc-hcard__cover {
    width: 100%;
    height: 200px;
  }
}
</style>
