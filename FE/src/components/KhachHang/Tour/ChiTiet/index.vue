<template>
  <div class="tour-detail-page">
    <div class="detail-layout">
      <CustomerSidebar />

      <!-- NỘI DUNG CHÍNH -->
      <section class="detail-content">
        <div v-if="thong_bao_loi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thong_bao_loi }}</span>
        </div>

        <div v-if="dang_tai" class="notice notice--info">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang tải thông tin chi tiết tour...</span>
        </div>

        <template v-else-if="tour">
          <!-- HERO BANNER TƯƠNG TỰ CÁC CARD TRONG DANH SÁCH -->
          <header 
            class="tour-hero" 
            :style="{ backgroundImage: `linear-gradient(180deg, rgba(9, 24, 41, 0.2), rgba(9, 24, 41, 0.9)), url(${tour.hinh_anh})` }"
          >
            <div class="tour-hero__badge-row">
              <span class="tour-hero__badge">{{ tour.thoi_gian_hien_thi }}</span>
              <span class="tour-hero__rating">
                <i class="fas fa-user-group"></i> {{ tour.so_nguoi }} người
              </span>
            </div>

            <div class="tour-hero__bottom">
              <div>
                <p class="tour-hero__eyebrow">Trải nghiệm Tour Trọn gói</p>
                <h1>{{ tour.ten }}</h1>
              </div>
              <div class="tour-hero__price">
                <span>Chỉ từ</span>
                <strong>{{ tour.gia_hien_thi }}</strong>
              </div>
            </div>
          </header>

          <div v-if="actionMessage" class="notice notice--success">
            <i class="fas fa-circle-check"></i>
            <span>{{ actionMessage }}</span>
          </div>

          <!-- HAI CỘT THÔNG TIN -->
          <div class="detail-grid">
            <div class="detail-main">
              <section class="detail-card">
                <h2>Giới thiệu Tour</h2>
                <p>{{ tour.mo_ta }}</p>
              </section>

              <section class="detail-card">
                <h2>Lịch trình khái quát</h2>
                <div class="itinerary" v-if="tour.chi_tiet_tours && tour.chi_tiet_tours.length > 0">
                  <div class="itinerary-item" v-for="(ct, index) in tour.chi_tiet_tours" :key="index">
                    <img :src="resolveImageUrl(ct.dia_diem?.hinh_anh)" class="itinerary-image" alt="ĐĐ" v-if="ct.dia_diem" />
                    <div v-if="ct.dia_diem">
                      <h3>{{ ct.dia_diem.ten_dia_diem }} 
                         <span class="visit-time" v-if="ct.dia_diem.thoi_gian_tham_quan"><i class="far fa-clock"></i> {{ ct.dia_diem.thoi_gian_tham_quan }}</span>
                      </h3>
                      <p>{{ ct.dia_diem.mo_ta }}</p>
                    </div>
                  </div>
                </div>
                <div class="itinerary" v-else>
                  <div class="itinerary-item">
                    <div class="itinerary-icon"><i class="fas fa-plane-departure"></i></div>
                    <div>
                      <h3>Khởi hành & Đón khách</h3>
                      <p>Khởi hành theo đúng thời gian dự kiến. Xe và Hướng dẫn viên sẽ hỗ trợ đoàn tận nơi.</p>
                    </div>
                  </div>
                  <div class="itinerary-item">
                    <div class="itinerary-icon"><i class="fas fa-camera"></i></div>
                    <div>
                      <h3>Tham quan & Trải nghiệm</h3>
                      <p>Khám phá các điểm đến nằm trong chương trình. Thời gian tham quan linh hoạt, đảm bảo an toàn.</p>
                    </div>
                  </div>
                  <div class="itinerary-item">
                    <div class="itinerary-icon"><i class="fas fa-plane-arrival"></i></div>
                    <div>
                      <h3>Kết thúc hành trình</h3>
                      <p>Kết thúc chuyến đi, trả khách tại điểm hẹn an toàn. Giải đáp và ghi nhận phản hồi dịch vụ.</p>
                    </div>
                  </div>
                </div>
              </section>

              <section class="detail-card">
                <h2>Quy định chung</h2>
                <div class="rules-list">
                  <div class="rule-box">
                    <i class="fas fa-check-circle"></i>
                    <span>Giá đã bao gồm vé tham quan cơ bản, phương tiện di chuyển trong nội bộ lịch trình.</span>
                  </div>
                  <div class="rule-box">
                    <i class="fas fa-utensils"></i>
                    <span>Ăn uống theo lịch trình tiêu chuẩn (nếu có tùy chọn).</span>
                  </div>
                  <div class="rule-box">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Tùy thuộc vào thời tiết mà lịch trình có thể tinh chỉnh nhằm đảm bảo an toàn.</span>
                  </div>
                </div>
              </section>
            </div>

            <!-- CỘT RIGHT: LỊCH KHỞI HÀNH -->
            <aside class="detail-sidebar">
              <section class="detail-card">
                <h3>Lịch khởi hành</h3>
                <p class="subtitle">Chon lich de dat tour va thanh toan bang QR ngan hang.</p>
                
                <div class="schedules">
                  <template v-if="danh_sach_khoi_hanh.length > 0">
                    <div 
                      v-for="lich in danh_sach_khoi_hanh" 
                      :key="lich.id" 
                      class="schedule-box"
                      :class="{ 'is-active': lich.tinh_trang }"
                    >
                      <div class="schedule-head">
                        <strong><i class="far fa-calendar-alt"></i> Ngày đi: {{ lich.ngay_bat_dau_hien_thi }}</strong>
                      </div>
                      <div class="schedule-info">
                        <span><i class="fas fa-chair"></i> Còn trống: {{ lich.so_cho }} chỗ</span>
                        <span class="status-badge" :class="lich.tinh_trang ? 'active' : 'inactive'">
                          {{ lich.tinh_trang ? "Đang mở" : "Đã chốt sổ" }}
                        </span>
                      </div>
                      <button 
                        class="book-button" 
                        :disabled="!lich.tinh_trang || lich.so_cho < 1"
                        @click="chonLich(lich)"
                      >
                        {{ lich.tinh_trang && lich.so_cho > 0 ? "Đặt Tour / Thanh toán" : "Ngưng tiếp nhận" }}
                      </button>
                    </div>
                  </template>
                  <template v-else>
                    <div class="empty-schedule">
                      <i class="fas fa-calendar-times"></i>
                      <span>Tour hiện tại chưa có lịch khởi hành mới. Vui lòng quay lại sau!</span>
                    </div>
                  </template>
                </div>
              </section>

              <section class="detail-card contact-card">
                <i class="fas fa-headset icon-contact"></i>
                <div>
                  <strong>Cần hỗ trợ?</strong>
                  <p>Hotline: 1900 1234</p>
                  <p>Email: contact@smarttravel.vn</p>
                </div>
              </section>
            </aside>
          </div>
        </template>
      </section>
    </div>
  </div>


</template>

<script>
import CustomerSidebar from '../../CustomerSidebar.vue';
import { goiApi } from "../../../../services/httpClient.js";
import { getStoredCustomerId, getStoredUser } from "../../../Shared/customerSession";

const TOUR_API = "/api/tour";
const TOUR_KHOI_HANH_API = "/api/tour-khoi-hanh";
const HINH_MAC_DINH = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80";

async function fetchDuLieu(url) {
  const phanHoi = await goiApi(url);
  const duLieu = await phanHoi.json();
  if (!phanHoi.ok) {
    const loi = new Error(duLieu?.message || "Không thể hoàn tất yêu cầu.");
    loi.phanHoi = { data: duLieu, status: phanHoi.status };
    throw loi;
  }
  return { data: duLieu, status: phanHoi.status };
}

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "KhachHangChiTietTourPage",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      dang_tai: false,
      thong_bao_loi: "",
      actionMessage: "",
      tour: null,
      danh_sach_khoi_hanh: [],
      maKhachHang: "",
    };
  },
  computed: {
    hoSoKhachHang() {
      return getStoredUser() || {};
    },
    ma_tour() {
      return this.$route.params.id;
    },
  },
  mounted() {
    this.maKhachHang = getStoredCustomerId();
    this.docThongBaoRoute();
    this.taiChiTietTour();
  },
  methods: {
    docThongBaoRoute() {
      const notice = String(this.$route.query.notice || "").trim();
      const noticeType = String(this.$route.query.notice_type || "").trim();

      if (!notice) return;

      if (noticeType === "error") {
        toaster.error(notice);
        this.actionMessage = "";
        return;
      }

      this.actionMessage = notice;
    },
    xuLyDanhSach(payload) {
      if (!payload) return [];
      if (Array.isArray(payload)) return payload;
      if (Array.isArray(payload.data)) return payload.data;
      if (Array.isArray(payload.data?.data)) return payload.data.data;
      if (Array.isArray(payload.result)) return payload.result;
      if (Array.isArray(payload.result?.data)) return payload.result.data;
      return [];
    },
    xuLyItem(payload) {
      if (!payload) return null;
      if (Array.isArray(payload)) return payload[0] || null;
      if (payload.data && !Array.isArray(payload.data)) return payload.data;
      if (payload.result && !Array.isArray(payload.result)) return payload.result;
      return payload;
    },
    layGiaTriDauTien(obj, keys, macDinh = "") {
      for (const key of keys) {
        if (obj && obj[key] !== undefined && obj[key] !== null && obj[key] !== "") {
          return obj[key];
        }
      }
      return macDinh;
    },
    dinhDangTien(giaTri) {
      const so = Number(giaTri);
      if (!Number.isFinite(so) || so <= 0) return "Liên hệ";
      return `${so.toLocaleString("vi-VN")} đ`;
    },
    dinhDangNgay(ngay) {
      if (!ngay) return "";
      const date = new Date(ngay);
      if (Number.isNaN(date.getTime())) return ngay;
      return date.toLocaleDateString("vi-VN");
    },
    chuanHoaBoolean(value, macDinh = false) {
      if (typeof value === "boolean") return value;
      if (typeof value === "number") return value !== 0;
      if (typeof value === "string") {
        const normalized = value.trim().toLowerCase();
        if (["1", "true", "yes", "on"].includes(normalized)) return true;
        if (["0", "false", "no", "off", ""].includes(normalized)) return false;
      }
      return macDinh;
    },
    chuanHoaTour(item = {}) {
      return {
        id: this.layGiaTriDauTien(item, ["ma_tour", "Ma_tour", "id", "Id"]),
        ten: this.layGiaTriDauTien(item, ["ten_tour", "Ten_tour", "ten", "name"], "Tour nổi bật"),
        mo_ta: this.layGiaTriDauTien(item, ["mo_ta", "Mo_ta", "description"], "Thông tin mô tả tour đang được cập nhật."),
        hinh_anh: this.layGiaTriDauTien(item, ["hinh_anh", "Hinh_anh", "image", "thumbnail"], HINH_MAC_DINH),
        gia_hien_thi: this.dinhDangTien(this.layGiaTriDauTien(item, ["so_tien", "So_tien", "gia_tour", "gia"], 0)),
        thoi_gian_hien_thi: `${this.layGiaTriDauTien(item, ["so_ngay", "So_ngay"], 1)} ngày`,
        so_nguoi: this.layGiaTriDauTien(item, ["so_nguoi", "So_nguoi"], 10),
        chi_tiet_tours: item.chi_tiet_tours || [],
      };
    },
    resolveImageUrl(rawUrl) {
      if (!rawUrl) return HINH_MAC_DINH;
      if (rawUrl.startsWith('http') || rawUrl.startsWith('data:')) return rawUrl;
      return `http://127.0.0.1:8000/${rawUrl.replace(/^\/+/, '')}`;
    },
    chuanHoaLichKhoiHanh(item = {}) {
      const ngayBD = this.layGiaTriDauTien(item, ["ngay_bat_dau", "Ngay_bat_dau"]);
      const tTrang = this.chuanHoaBoolean(this.layGiaTriDauTien(item, ["tinh_trang", "Tinh_trang"], false));
      // Tinh trang tuong doi voi hien tai
      return {
        id: this.layGiaTriDauTien(item, ["ma_thoi_gian_tour", "Ma_thoi_gian_tour", "id", "Id"]),
        ma_tour: String(this.layGiaTriDauTien(item, ["ma_tour", "Ma_tour"])),
        ngay_bat_dau: ngayBD,
        ngay_bat_dau_hien_thi: ngayBD ? this.dinhDangNgay(ngayBD) : "Đang cập nhật",
        so_cho: Number(this.layGiaTriDauTien(item, ["so_cho", "So_cho"], 0)),
        tinh_trang: tTrang,
      };
    },
    layThongBaoLoi(error, macDinh) {
      const data = error?.phanHoi?.data;
      if (data?.message) return data.message;
      return error?.message || macDinh;
    },
    async taiChiTietTour() {
      this.dang_tai = true;
      // cleared thong_bao_loi
      this.actionMessage = "";

      try {
        const [tourRes, lichRes] = await Promise.allSettled([
          fetchDuLieu(`${TOUR_API}/${this.ma_tour}`),
          fetchDuLieu(TOUR_KHOI_HANH_API)
        ]);

        if (tourRes.status !== "fulfilled") {
          throw tourRes.reason;
        }

        this.tour = this.chuanHoaTour(this.xuLyItem(tourRes.value.data));

        if (lichRes.status === "fulfilled") {
          const allLich = this.xuLyDanhSach(lichRes.value.data);
          // Lọc ra lịch khởi hành của riêng tour này
          this.danh_sach_khoi_hanh = allLich
            .map(x => this.chuanHoaLichKhoiHanh(x))
            .filter(x => x.ma_tour === String(this.ma_tour))
            // Ưu tiên hiển thị lịch đang mở trước
            .sort((a, b) => (b.tinh_trang ? 1 : 0) - (a.tinh_trang ? 1 : 0));
        }
      } catch (error) {
        toaster.error(this.layThongBaoLoi(error, "Không thể lấy thông tin tour."));
      } finally {
        this.dang_tai = false;
      }
    },
    chonLich(lich) {
      if (!this.maKhachHang) {
        const redirect = `/khach-hang/tour/${this.ma_tour}/thanh-toan?schedule=${encodeURIComponent(lich.id)}`;
        this.$router.push({
          path: "/dang-nhap",
          query: { redirect },
        });
        return;
      }
      this.$router.push({
        path: `/khach-hang/tour/${this.ma_tour}/thanh-toan`,
        query: {
          schedule: lich.id,
        },
      });
    }
  },
};
</script>

<style scoped>
.tour-detail-page {
  background: radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%), linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
}

.detail-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 18px;
  min-height: calc(100vh - 68px);
}

.detail-content {
  padding: 28px 6px 0 0;
}

/* HERO SECTION LỚN */
.tour-hero {
  position: relative;
  border-radius: 28px;
  min-height: 380px;
  padding: 28px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  margin-bottom: 24px;
  background-size: cover;
  background-position: center;
  box-shadow: 0 16px 34px rgba(31, 65, 114, 0.15);
  overflow: hidden;
  color: #fff;
}

.tour-hero__badge-row {
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
  z-index: 2;
}

.tour-hero__badge {
  padding: 6px 14px;
  border-radius: 999px;
  background: rgba(168, 139, 250, 0.95);
  color: #ffffff;
  font-size: 0.85rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.tour-hero__rating {
  padding: 6px 14px;
  border-radius: 999px;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(8px);
  color: #fff;
  font-size: 0.85rem;
  font-weight: 700;
}

.tour-hero__rating i {
  color: #fbbf24;
}

.tour-hero__bottom {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
}

.tour-hero__eyebrow {
  margin: 0 0 8px;
  color: #a8bcf8;
  font-size: 0.95rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.tour-hero h1 {
  margin: 0;
  color: #ffffff;
  font-size: clamp(2rem, 3.5vw, 3.2rem);
  font-weight: 900;
  line-height: 1.1;
  text-shadow: 2px 2px 14px rgba(0, 0, 0, 0.2);
}

.tour-hero__price {
  text-align: right;
  min-width: 180px;
}

.tour-hero__price span {
  display: block;
  font-size: 0.9rem;
  color: #bfdbfe;
  font-weight: 600;
  margin-bottom: 4px;
}

.tour-hero__price strong {
  display: block;
  font-size: 2.2rem;
  font-weight: 900;
  color: #fff;
  line-height: 1;
  text-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.notice {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  margin-bottom: 18px;
  border-radius: 12px;
  background: #fff;
}
.notice--error {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #ffe4e6;
}
.notice--info {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #dbeafe;
}
.notice--success {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #d1fae5;
}

.detail-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.6fr) 340px;
  gap: 20px;
  align-items: start;
}

.detail-main {
  display: grid;
  gap: 20px;
}

.detail-card {
  padding: 24px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid #e2eaf5;
  box-shadow: 0 12px 24px rgba(31, 65, 114, 0.04);
}

.detail-card h2,
.detail-card h3 {
  margin: 0;
  color: #133864;
  font-size: 1.35rem;
  font-weight: 900;
  margin-bottom: 16px;
}

.detail-card p {
  color: #4a6c90;
  line-height: 1.8;
  margin: 0;
}

.detail-card .subtitle {
  color: #63809f;
  font-size: 0.9rem;
  margin-top: -12px;
  margin-bottom: 16px;
}

/* Itinerary */
.itinerary {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.itinerary-item {
  display: flex;
  gap: 1rem;
}
.itinerary-icon {
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  border-radius: 12px;
  background: #eef5ff;
  color: #0b6b93;
  display: grid;
  place-items: center;
  font-size: 1.25rem;
}
.itinerary-image {
  width: 64px;
  height: 64px;
  flex-shrink: 0;
  border-radius: 12px;
  object-fit: cover;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.itinerary-item h3 {
  font-size: 1.1rem;
  margin-bottom: 6px;
  margin-top: 0;
}
.visit-time {
  font-size: 0.85rem;
  font-weight: normal;
  color: #63809f;
  margin-left: 8px;
  background: #EAF4FD;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-block;
  vertical-align: middle;
}

/* Rules list */
.rules-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.rule-box {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px 16px;
  background: #f8fbff;
  border-radius: 12px;
  color: #2b4566;
}
.rule-box i {
  color: #28a745;
  margin-top: 3px;
}

/* Detail Sidebar */
.detail-sidebar {
  display: grid;
  gap: 20px;
}

/* Schedules */
.schedules {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.schedule-box {
  border: 1px solid #e4ecf8;
  border-radius: 16px;
  padding: 16px;
  background: #ffffff;
  transition: all 0.2s ease;
}

.schedule-box.is-active {
  border-color: #a3cdf1;
  background: #fcfdff;
  box-shadow: 0 6px 16px rgba(11, 107, 147, 0.08);
}

.schedule-head {
  color: #102f56;
  font-size: 1.05rem;
  margin-bottom: 12px;
}

.schedule-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  font-size: 0.9rem;
  color: #4a6c90;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 0.8rem;
}
.status-badge.active { background: #dcfce7; color: #065f46; }
.status-badge.inactive { background: #fee2e2; color: #991b1b; }

.book-button {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #0b7198 0%, #085676 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  transition: opacity 0.2s;
}

.book-button:hover {
  opacity: 0.9;
}

.book-button:disabled {
  background: #d1d5db;
  color: #6b7280;
  cursor: not-allowed;
}

.empty-schedule {
  text-align: center;
  padding: 24px 10px;
  color: #6b7280;
}
.empty-schedule i {
  font-size: 2rem;
  color: #d1d5db;
  margin-bottom: 10px;
}

/* Contact */
.contact-card {
  display: flex;
  align-items: center;
  gap: 16px;
}
.icon-contact {
  font-size: 2.2rem;
  color: #0b7198;
}
.contact-card strong {
  color: #111827;
  display: block;
  margin-bottom: 4px;
}

@media (max-width: 1200px) {
  .detail-layout {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 860px) {
  .detail-grid {
    grid-template-columns: 1fr;
  }
  .tour-hero {
    min-height: auto;
    gap: 40px;
  }
  .tour-hero__bottom {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  .tour-hero__price {
    text-align: left;
  }
}
/* ... other styles remain ... */

</style>
