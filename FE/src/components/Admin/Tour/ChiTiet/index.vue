<template>
  <div class="admin-tour-detail-page">
    <div v-if="thong_bao_loi" class="tour-detail-alert">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thong_bao_loi }}</span>
    </div>
    <div v-if="thong_bao_thanh_cong" class="tour-detail-alert tour-detail-success">
      <i class="fas fa-circle-check"></i>
      <span>{{ thong_bao_thanh_cong }}</span>
    </div>

    <div v-if="dang_tai" class="tour-detail-empty">
      <i class="fas fa-spinner fa-spin"></i>
      <span>Đang tải chi tiết tour...</span>
    </div>

    <template v-else-if="tour">
      <section class="tour-detail-hero-card">
        <div class="tour-detail-hero-card__image" :style="{ backgroundImage: `url(${tour.hinh_anh})` }"></div>

        <div class="tour-detail-hero-card__content">
          <div class="tour-detail-hero-card__top">
            <div class="tour-identity">
              <h1>{{ tour.ten }}</h1>
              <p><i class="fas fa-location-dot"></i> {{ dia_chi_hien_thi }}</p>
            </div>

            <div class="tour-price-box">
              <span>Giá khởi điểm</span>
              <strong>{{ tour.gia_hien_thi }}</strong>
            </div>
          </div>

          <div class="tour-metric-grid">
            <article class="tour-metric-card">
              <span>Thời gian</span>
              <strong><i class="fas fa-clock"></i> {{ tour.thoi_gian_hien_thi }}</strong>
            </article>
            <article class="tour-metric-card">
              <span>Khởi hành</span>
              <strong><i class="fas fa-calendar-days"></i> {{ khoi_hanh_hien_thi }}</strong>
            </article>
            <article class="tour-metric-card">
              <span>Số chỗ</span>
              <strong><i class="fas fa-users"></i> {{ so_cho_hien_thi }}</strong>
            </article>
          </div>
        </div>
      </section>

      <section class="tour-detail-section-head">
        <div>
          <h2>Lịch trình chi tiết</h2>
          <p>Theo dõi các hoạt động theo từng ngày cho tour này.</p>
        </div>
        <button type="button" class="tour-detail-edit-btn" @click="moFormChinhSua">
          <i class="fas fa-pen"></i>
          Chỉnh sửa tour
        </button>
      </section>

      <section v-if="lich_trinh_hien_thi.length" class="tour-timeline">
        <article v-for="ngay in lich_trinh_hien_thi" :key="ngay.ngay" class="tour-day-card">
          <header class="tour-day-card__header">
            <span>Ngày {{ String(ngay.ngay).padStart(2, '0') }}</span>
            <h3>{{ ngay.tieu_de }}</h3>
          </header>

          <div class="tour-event-list">
            <article v-for="suKien in ngay.su_kien" :key="suKien.id" class="tour-event-card">
              <p class="tour-event-card__period">{{ suKien.nhan }}</p>
              <strong>{{ suKien.tieu_de }}</strong>
              <p>{{ suKien.mo_ta }}</p>
            </article>
          </div>
        </article>
      </section>

      <section v-else class="tour-detail-empty">
        <i class="fas fa-route"></i>
        <span>Chưa có lịch trình cho tour này.</span>
      </section>
    </template>

    <div v-if="hien_form_chinh_sua" class="tour-modal-backdrop" @click.self="dongFormChinhSua">
      <form class="tour-modal" @submit.prevent="luuChinhSuaTour">
        <div class="tour-modal__header">
          <div>
            <h3>Chỉnh sửa tour</h3>
            <p>Cập nhật thông tin cơ bản của tour hiện tại.</p>
          </div>
          <button type="button" class="tour-modal__close" @click="dongFormChinhSua">
            <i class="fas fa-xmark"></i>
          </button>
        </div>

        <div class="tour-modal__grid">
          <label class="tour-modal__field tour-modal__field--full">
            <span>Tên tour</span>
            <input v-model.trim="form_chinh_sua.ten_tour" type="text" required>
          </label>

          <label class="tour-modal__field tour-modal__field--full">
            <span>Mô tả</span>
            <textarea v-model.trim="form_chinh_sua.mo_ta" rows="4"></textarea>
          </label>

          <label class="tour-modal__field">
            <span>Giá (VNĐ)</span>
            <input v-model.number="form_chinh_sua.so_tien" type="number" min="0">
          </label>

          <label class="tour-modal__field">
            <span>Số ngày</span>
            <input v-model.number="form_chinh_sua.so_ngay" type="number" min="1">
          </label>

          <label class="tour-modal__field">
            <span>Số người</span>
            <input v-model.number="form_chinh_sua.so_nguoi" type="number" min="1">
          </label>

          <label class="tour-modal__field">
            <span>Mã tag</span>
            <input v-model.trim="form_chinh_sua.ma_tag" type="text" placeholder="Không bắt buộc">
          </label>

          <label class="tour-modal__field tour-modal__field--full">
            <span>Ảnh tour (URL)</span>
            <input v-model.trim="form_chinh_sua.hinh_anh" type="text" placeholder="https://...">
          </label>
        </div>

        <p v-if="loi_chinh_sua" class="tour-modal__error">{{ loi_chinh_sua }}</p>

        <div class="tour-modal__footer">
          <button type="button" class="tour-modal__btn tour-modal__btn--ghost" :disabled="dang_luu_chinh_sua" @click="dongFormChinhSua">Hủy</button>
          <button type="submit" class="tour-modal__btn tour-modal__btn--primary" :disabled="dang_luu_chinh_sua">
            <i v-if="dang_luu_chinh_sua" class="fas fa-spinner fa-spin"></i>
            <span>{{ dang_luu_chinh_sua ? 'Đang lưu...' : 'Lưu thay đổi' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { goiApi } from '../../../../services/httpClient.js';

const TOUR_API = '/api/tour';
const CHI_TIET_TOUR_API = '/api/chi-tiet-tour';
const TOUR_KHOI_HANH_API = '/api/tour-khoi-hanh';
const DIA_DIEM_API = '/api/dia-diem';
const HINH_MAC_DINH = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=900&q=80';

export default {
  name: 'AdminTourDetailPage',
  data() {
    return {
      dang_tai: false,
      thong_bao_loi: '',
      thong_bao_thanh_cong: '',
      tour: null,
      danh_sach_chi_tiet: [],
      danh_sach_khoi_hanh: [],
      ban_do_dia_diem: {},
      hien_form_chinh_sua: false,
      dang_luu_chinh_sua: false,
      loi_chinh_sua: '',
      form_chinh_sua: {
        ten_tour: '',
        mo_ta: '',
        hinh_anh: '',
        so_tien: null,
        so_ngay: 1,
        so_nguoi: 10,
        ma_tag: '',
      },
    };
  },
  computed: {
    ma_tour() {
      return this.$route.params.id;
    },
    laTrangChinhSua() {
      return this.$route.path.endsWith('/edit');
    },
    dia_chi_hien_thi() {
      const diaChiDau = Object.values(this.ban_do_dia_diem)[0]?.dia_chi;
      return diaChiDau || 'Việt Nam';
    },
    khoi_hanh_hien_thi() {
      const active = this.danh_sach_khoi_hanh.find((item) => item.tinh_trang);
      return active?.ngay_bat_dau_hien_thi || 'Hằng ngày';
    },
    so_cho_hien_thi() {
      const active = this.danh_sach_khoi_hanh.find((item) => item.tinh_trang);
      const soCho = active?.so_cho || this.tour?.so_nguoi || 12;
      return `${Math.max(soCho, 4)} khách`;
    },
    lich_trinh_hien_thi() {
      if (!this.danh_sach_chi_tiet.length) {
        return [
          {
            ngay: 1,
            tieu_de: `Khởi hành ${this.tour?.ten || ''}`,
            su_kien: [
              {
                id: 'default-1',
                nhan: 'Sáng',
                tieu_de: 'Đón khách và bắt đầu hành trình',
                mo_ta: this.tour?.mo_ta || 'Thông tin chi tiết đang được cập nhật.',
              },
            ],
          },
        ];
      }

      return this.danh_sach_chi_tiet.map((item, index) => {
        const diaDiem = this.ban_do_dia_diem[item.ma_dia_diem] || {};
        const ten = diaDiem.ten || `Điểm dừng ${index + 1}`;
        const moTa = diaDiem.mo_ta || this.tour?.mo_ta || 'Thông tin điểm dừng đang được cập nhật.';

        return {
          ngay: index + 1,
          tieu_de: `Khám phá ${ten}`,
          su_kien: [
            {
              id: `${item.id}-morning`,
              nhan: 'Sáng',
              tieu_de: `Di chuyển đến ${ten}`,
              mo_ta: `Bắt đầu hành trình và đến ${ten}. ${moTa}`,
            },
            {
              id: `${item.id}-afternoon`,
              nhan: 'Chiều',
              tieu_de: `Trải nghiệm tại ${ten}`,
              mo_ta: `Khám phá các hoạt động nổi bật và tham quan khu vực ${ten}.`,
            },
            {
              id: `${item.id}-evening`,
              nhan: 'Tối',
              tieu_de: `Kết thúc ngày tại ${ten}`,
              mo_ta: 'Tổng kết hoạt động trong ngày và chuẩn bị cho chặng tiếp theo.',
            },
          ],
        };
      });
    },
  },
  mounted() {
    this.loadChiTietTour();
  },
  methods: {
    async goiDuLieu(url, { method = 'GET', body = null, headers = {} } = {}) {
      const response = await goiApi(url, {
        method,
        headers: {
          Accept: 'application/json',
          ...(body ? { 'Content-Type': 'application/json' } : {}),
          ...headers,
        },
        body: body ? JSON.stringify(body) : undefined,
      });
      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        const error = new Error(data?.message || 'Yeu cau API that bai.');
        error.response = { data, status: response.status };
        throw error;
      }

      return { data };
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
    layGiaTriDauTien(obj, keys, macDinh = '') {
      for (const key of keys) {
        if (obj && obj[key] !== undefined && obj[key] !== null && obj[key] !== '') {
          return obj[key];
        }
      }
      return macDinh;
    },
    chuanHoaBoolean(value, macDinh = false) {
      if (typeof value === 'boolean') return value;
      if (typeof value === 'number') return value !== 0;
      if (typeof value === 'string') {
        const normalized = value.trim().toLowerCase();
        if (['1', 'true', 'yes', 'on'].includes(normalized)) return true;
        if (['0', 'false', 'no', 'off', ''].includes(normalized)) return false;
      }
      return macDinh;
    },
    dinhDangTien(giaTri) {
      const so = Number(giaTri);
      if (!Number.isFinite(so) || so <= 0) return 'Liên hệ';
      return `${so.toLocaleString('vi-VN')} đ`;
    },
    dinhDangNgay(ngay) {
      if (!ngay) return 'Đang cập nhật';
      const date = new Date(ngay);
      if (Number.isNaN(date.getTime())) return ngay;
      return date.toLocaleDateString('vi-VN');
    },
    chuanHoaTour(item = {}) {
      return {
        id: this.layGiaTriDauTien(item, ['ma_tour', 'Ma_tour', 'id', 'Id']),
        ten: this.layGiaTriDauTien(item, ['ten_tour', 'Ten_tour', 'ten', 'name'], 'Tour đang cập nhật'),
        mo_ta: this.layGiaTriDauTien(item, ['mo_ta', 'Mo_ta', 'description'], 'Thông tin tour đang được cập nhật.'),
        hinh_anh: this.layGiaTriDauTien(item, ['hinh_anh', 'Hinh_anh', 'image', 'thumbnail'], HINH_MAC_DINH),
        gia_hien_thi: this.dinhDangTien(this.layGiaTriDauTien(item, ['so_tien', 'So_tien', 'gia_tour', 'gia'], 0)),
        thoi_gian_hien_thi: `${this.layGiaTriDauTien(item, ['so_ngay', 'So_ngay'], 1)} ngày`,
        so_nguoi: Number(this.layGiaTriDauTien(item, ['so_nguoi', 'So_nguoi'], 12)),
        trang_thai: true,
      };
    },
    chuanHoaChiTiet(item = {}, index = 0) {
      return {
        id: this.layGiaTriDauTien(item, ['ma_chi_tiet_tour', 'Ma_chi_tiet_tour', 'id', 'Id'], index + 1),
        ma_tour: this.layGiaTriDauTien(item, ['ma_tour', 'Ma_tour']),
        ma_dia_diem: this.layGiaTriDauTien(item, ['ma_dia_diem', 'Ma_dia_diem']),
      };
    },
    chuanHoaKhoiHanh(item = {}) {
      return {
        id: this.layGiaTriDauTien(item, ['ma_thoi_gian_tour', 'Ma_thoi_gian_tour', 'id', 'Id']),
        ma_tour: this.layGiaTriDauTien(item, ['ma_tour', 'Ma_tour']),
        ngay_bat_dau_hien_thi: this.dinhDangNgay(this.layGiaTriDauTien(item, ['ngay_bat_dau', 'Ngay_bat_dau'])),
        so_cho: Number(this.layGiaTriDauTien(item, ['so_cho', 'So_cho'], 0)),
        tinh_trang: this.chuanHoaBoolean(this.layGiaTriDauTien(item, ['tinh_trang', 'Tinh_trang'], 0)),
      };
    },
    chuanHoaDiaDiem(item = {}) {
      return {
        id: this.layGiaTriDauTien(item, ['ma_dia_diem', 'Ma_dia_diem', 'id', 'Id']),
        ten: this.layGiaTriDauTien(item, ['ten_dia_diem', 'Ten_dia_diem', 'ten', 'name'], 'Điểm đến nổi bật'),
        dia_chi: this.layGiaTriDauTien(item, ['dia_chi', 'Dia_chi'], 'Việt Nam'),
        mo_ta: this.layGiaTriDauTien(item, ['mo_ta', 'Mo_ta', 'description'], 'Thông tin điểm dừng đang được cập nhật.'),
      };
    },
    layThongBaoLoi(error, macDinh) {
      const data = error?.response?.data;
      if (data?.message) return data.message;
      return error?.message || macDinh;
    },
    taoPayloadChinhSua() {
      const parseNum = (value) => {
        if (value === '' || value === null || value === undefined) return null;
        const num = Number(value);
        return Number.isFinite(num) ? num : null;
      };
      return {
        ten_tour: this.form_chinh_sua.ten_tour,
        mo_ta: this.form_chinh_sua.mo_ta || null,
        hinh_anh: this.form_chinh_sua.hinh_anh || null,
        so_tien: parseNum(this.form_chinh_sua.so_tien),
        so_ngay: parseNum(this.form_chinh_sua.so_ngay),
        so_nguoi: parseNum(this.form_chinh_sua.so_nguoi),
        ma_tag: this.form_chinh_sua.ma_tag || null,
      };
    },
    dongFormChinhSua() {
      if (this.dang_luu_chinh_sua) return;
      this.hien_form_chinh_sua = false;
      this.loi_chinh_sua = '';
      if (this.$route.path.endsWith('/edit')) {
        this.$router.replace(`/admin/tour/${this.ma_tour}`).catch(() => {});
      }
    },
    moFormChinhSua() {
      if (!this.tour) return;
      this.loi_chinh_sua = '';
      this.thong_bao_thanh_cong = '';
      this.form_chinh_sua = {
        ten_tour: this.tour.ten || '',
        mo_ta: this.tour.mo_ta || '',
        hinh_anh: this.tour.hinh_anh || '',
        so_tien: Number(this.layGiaTriDauTien(this.tour, ['so_tien'], 0)) || 0,
        so_ngay: Number(this.layGiaTriDauTien(this.tour, ['so_ngay'], 1)) || 1,
        so_nguoi: Number(this.layGiaTriDauTien(this.tour, ['so_nguoi'], 10)) || 10,
        ma_tag: this.layGiaTriDauTien(this.tour, ['ma_tag'], ''),
      };
      this.hien_form_chinh_sua = true;
      if (!this.$route.path.endsWith('/edit')) {
        this.$router.replace(`/admin/tour/${this.ma_tour}/edit`).catch(() => {});
      }
    },
    async luuChinhSuaTour() {
      if (!String(this.form_chinh_sua.ten_tour || '').trim()) {
        this.loi_chinh_sua = 'Tên tour là bắt buộc.';
        return;
      }
      this.dang_luu_chinh_sua = true;
      this.loi_chinh_sua = '';
      this.thong_bao_loi = '';
      this.thong_bao_thanh_cong = '';
      try {
        await this.goiDuLieu(`${TOUR_API}/${this.ma_tour}`, {
          method: 'PUT',
          body: this.taoPayloadChinhSua(),
        });
        this.hien_form_chinh_sua = false;
        await this.loadChiTietTour();
        this.thong_bao_thanh_cong = 'Cập nhật tour thành công.';
        if (this.$route.path.endsWith('/edit')) {
          this.$router.replace(`/admin/tour/${this.ma_tour}`).catch(() => {});
        }
      } catch (error) {
        this.loi_chinh_sua = this.layThongBaoLoi(error, 'Không thể cập nhật tour.');
      } finally {
        this.dang_luu_chinh_sua = false;
      }
    },
    async loadChiTietTour() {
      this.dang_tai = true;
      this.thong_bao_loi = '';

      try {
        const [tourRes, chiTietRes, khoiHanhRes] = await Promise.all([
          this.goiDuLieu(`${TOUR_API}/${this.ma_tour}`),
          this.goiDuLieu(CHI_TIET_TOUR_API),
          this.goiDuLieu(TOUR_KHOI_HANH_API),
        ]);

        const rawTour = this.xuLyItem(tourRes.data);
        this.tour = this.chuanHoaTour(rawTour);
        this.tour.so_tien = this.layGiaTriDauTien(rawTour, ['so_tien', 'So_tien'], 0);
        this.tour.so_ngay = this.layGiaTriDauTien(rawTour, ['so_ngay', 'So_ngay'], 1);
        this.tour.so_nguoi = this.layGiaTriDauTien(rawTour, ['so_nguoi', 'So_nguoi'], 10);
        this.tour.ma_tag = this.layGiaTriDauTien(rawTour, ['ma_tag', 'Ma_tag'], '');
        this.danh_sach_chi_tiet = this.xuLyDanhSach(chiTietRes.data)
          .map((item, index) => this.chuanHoaChiTiet(item, index))
          .filter((item) => item.ma_tour === this.ma_tour);

        this.danh_sach_khoi_hanh = this.xuLyDanhSach(khoiHanhRes.data)
          .map((item) => this.chuanHoaKhoiHanh(item))
          .filter((item) => item.ma_tour === this.ma_tour);

        const danhSachDiaDiem = [...new Set(this.danh_sach_chi_tiet.map((item) => item.ma_dia_diem).filter(Boolean))];
        const ketQuaDiaDiem = await Promise.allSettled(
          danhSachDiaDiem.map((maDiaDiem) => this.goiDuLieu(`${DIA_DIEM_API}/${maDiaDiem}`))
        );

        this.ban_do_dia_diem = {};
        ketQuaDiaDiem.forEach((ketQua, index) => {
          if (ketQua.status === 'fulfilled') {
            const diaDiem = this.chuanHoaDiaDiem(this.xuLyItem(ketQua.value.data));
            this.ban_do_dia_diem[danhSachDiaDiem[index]] = diaDiem;
          }
        });

        this.tour.trang_thai = this.danh_sach_khoi_hanh.some((item) => item.tinh_trang);
        if (this.laTrangChinhSua && !this.hien_form_chinh_sua) {
          this.moFormChinhSua();
        }
      } catch (error) {
        this.thong_bao_loi = this.layThongBaoLoi(error, 'Không thể tải chi tiết tour.');
      } finally {
        this.dang_tai = false;
      }
    },
    moTrangChinhSua() {
      this.$router.push(`/admin/tour/${this.ma_tour}/edit`);
    },
  },
  watch: {
    '$route.params.id'() {
      this.loadChiTietTour();
    },
    '$route.path'(value) {
      if (value.endsWith('/edit')) {
        if (this.tour) this.moFormChinhSua();
      } else {
        this.hien_form_chinh_sua = false;
      }
    },
  },
};
</script>

<style scoped>
.admin-tour-detail-page {
  display: grid;
  gap: 1.25rem;
  padding: 1.25rem 1.5rem 1.5rem;
}

.tour-detail-alert,
.tour-detail-empty {
  min-height: 84px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.2rem;
  border-radius: 1rem;
  border: 1px solid #e2ebf6;
  background: #ffffff;
}

.tour-detail-success {
  color: #047857;
}

.tour-detail-alert {
  color: #b42318;
}

.tour-detail-empty {
  color: #4a6a8d;
}

.tour-detail-hero-card {
  display: grid;
  grid-template-columns: 260px minmax(0, 1fr);
  gap: 1.4rem;
  padding: 1.4rem;
  border-radius: 1.25rem;
  background: #ffffff;
  border: 1px solid #dde9f5;
}

.tour-detail-hero-card__image {
  min-height: 220px;
  border-radius: 1rem;
  background-size: cover;
  background-position: center;
}

.tour-detail-hero-card__content {
  display: grid;
  gap: 1rem;
}

.tour-detail-hero-card__top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.tour-identity h1 {
  margin: 0;
  color: #0f2f58;
  font-size: clamp(1.9rem, 3vw, 2.5rem);
  font-weight: 900;
  line-height: 1.14;
}

.tour-identity p {
  margin: 0.55rem 0 0;
  color: #4f7093;
  font-size: 1rem;
}

.tour-price-box {
  min-width: 182px;
  padding: 0.78rem 1rem;
  border-radius: 0.95rem;
  background: #eff6ff;
  text-align: center;
}

.tour-price-box span {
  display: block;
  color: #0f6b92;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.11em;
}

.tour-price-box strong {
  display: block;
  margin-top: 0.3rem;
  color: #0f3763;
  font-size: 1.55rem;
  font-weight: 900;
}

.tour-metric-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.8rem;
}

.tour-metric-card {
  padding: 0.9rem 1rem;
  border-radius: 0.95rem;
  background: #f3f8ff;
}

.tour-metric-card span {
  display: block;
  color: #5f7d9d;
  font-size: 0.73rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.11em;
}

.tour-metric-card strong {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
  color: #123763;
  font-size: 1.04rem;
  font-weight: 800;
  line-height: 1.4;
}

.tour-detail-section-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.8rem;
  padding: 0 0.15rem;
}

.tour-detail-section-head h2 {
  margin: 0;
  color: #102f56;
  font-size: 1.55rem;
  font-weight: 900;
}

.tour-detail-section-head p {
  margin: 0;
  color: #5f7d9d;
  font-size: 0.96rem;
}

.tour-detail-edit-btn {
  min-height: 2.75rem;
  padding: 0 1rem;
  border: 1px solid #c6dcf1;
  border-radius: 0.85rem;
  background: #ffffff;
  color: #0f6b92;
  font-size: 0.92rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
}

.tour-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: grid;
  place-items: center;
  z-index: 80;
  padding: 0.9rem;
}

.tour-modal {
  width: min(760px, 100%);
  padding: 1.25rem;
  border-radius: 1.2rem;
  background: #ffffff;
  box-shadow: 0 22px 50px rgba(15, 23, 42, 0.28);
}

.tour-modal__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.tour-modal__header h3 {
  margin: 0;
  color: #102f56;
  font-size: 1.4rem;
  font-weight: 900;
}

.tour-modal__header p {
  margin: 0.35rem 0 0;
  color: #647a96;
}

.tour-modal__close {
  width: 2.4rem;
  height: 2.4rem;
  border: none;
  border-radius: 50%;
  background: #f4f8ff;
  color: #0f6b92;
}

.tour-modal__grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.8rem;
}

.tour-modal__field {
  display: grid;
  gap: 0.4rem;
}

.tour-modal__field--full {
  grid-column: 1 / -1;
}

.tour-modal__field span {
  color: #355d88;
  font-size: 0.82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.tour-modal__field input,
.tour-modal__field textarea {
  width: 100%;
  min-height: 2.8rem;
  border: 1px solid #d7e4f4;
  border-radius: 0.8rem;
  padding: 0.7rem 0.85rem;
  color: #17385f;
}

.tour-modal__field textarea {
  min-height: 6rem;
  resize: vertical;
}

.tour-modal__error {
  margin: 0.9rem 0 0;
  color: #b42318;
  font-weight: 700;
}

.tour-modal__footer {
  margin-top: 1rem;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.7rem;
}

.tour-modal__btn {
  min-height: 2.8rem;
  padding: 0 1rem;
  border: none;
  border-radius: 0.8rem;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.tour-modal__btn--ghost {
  background: #eff6ff;
  color: #355d88;
}

.tour-modal__btn--primary {
  background: linear-gradient(135deg, #0b7399 0%, #106f98 100%);
  color: #ffffff;
}

.tour-timeline {
  display: grid;
  gap: 0.95rem;
}

.tour-day-card {
  display: grid;
  gap: 0.85rem;
  padding: 1.1rem 1.15rem;
  border-radius: 1.1rem;
  border: 1px solid #dde9f5;
  background: #ffffff;
}

.tour-day-card__header {
  display: flex;
  align-items: baseline;
  flex-wrap: wrap;
  gap: 0.65rem;
  border-bottom: 1px dashed #d7e6f4;
  padding-bottom: 0.7rem;
}

.tour-day-card__header span {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 1.8rem;
  padding: 0 0.7rem;
  border-radius: 999px;
  background: #0f6b92;
  color: #ffffff;
  font-size: 0.74rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.tour-day-card__header h3 {
  margin: 0;
  color: #102f56;
  font-size: 1.24rem;
  font-weight: 800;
}

.tour-event-list {
  display: grid;
  gap: 0.72rem;
}

.tour-event-card {
  padding: 0.85rem 0.95rem;
  border-radius: 0.85rem;
  background: #f7fbff;
  border: 1px solid #deebf8;
}

.tour-event-card__period {
  margin: 0;
  color: #0f6b92;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.tour-event-card strong {
  display: block;
  margin-top: 0.25rem;
  color: #102f56;
  font-size: 1rem;
  font-weight: 800;
}

.tour-event-card p {
  margin: 0.45rem 0 0;
  color: #5c7c9c;
  line-height: 1.55;
}

@media (max-width: 1100px) {
  .tour-detail-hero-card {
    grid-template-columns: 1fr;
  }

  .tour-detail-hero-card__image {
    min-height: 240px;
  }

  .tour-metric-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .admin-tour-detail-page {
    padding: 1rem;
  }

  .tour-detail-hero-card__top {
    flex-direction: column;
  }

  .tour-detail-section-head {
    flex-direction: column;
    align-items: flex-start;
  }

  .tour-metric-grid {
    grid-template-columns: 1fr;
  }

  .tour-day-card {
    padding: 1rem;
  }

  .tour-modal__grid {
    grid-template-columns: 1fr;
  }
}
</style>

