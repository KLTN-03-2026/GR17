<template>
  <div class="admin-tour-schedule-page">
    <section class="schedule-hero">
      <div>
        <p class="schedule-hero__eyebrow">Tour management suite</p>
        <h1>Quản lý Lịch khởi hành Tour</h1>
        <p>Theo dõi trạng thái lịch khởi hành, số chỗ còn lại và tình trạng vận hành cho từng tour.</p>
      </div>

      <button type="button" class="schedule-create" @click="themLichKhoiHanh">
        <i class="fas fa-plus-circle"></i>
        Thêm lịch khởi hành
      </button>
    </section>

    <section class="schedule-summary">
      <article class="schedule-summary-card">
        <div class="schedule-summary-card__icon schedule-summary-card__icon--primary"><i class="fas fa-calendar-days"></i></div>
        <div>
          <span>Tổng số lịch khởi hành</span>
          <strong>{{ thong_ke.tong_so }}</strong>
          <small>Danh sách hiện có trong hệ thống</small>
        </div>
      </article>
      <article class="schedule-summary-card">
        <div class="schedule-summary-card__icon schedule-summary-card__icon--info"><i class="fas fa-clock"></i></div>
        <div>
          <span>Sắp khởi hành (7 ngày)</span>
          <strong>{{ thong_ke.sap_khoi_hanh }}</strong>
          <small>Theo mốc ngày hiện tại</small>
        </div>
      </article>
      <article class="schedule-summary-card">
        <div class="schedule-summary-card__icon schedule-summary-card__icon--danger"><i class="fas fa-triangle-exclamation"></i></div>
        <div>
          <span>Đã hết chỗ</span>
          <strong>{{ thong_ke.het_cho }}</strong>
          <small>Cần thêm lịch mới hoặc tăng chỗ</small>
        </div>
      </article>
    </section>

    <section class="schedule-main-grid">
      <section class="schedule-table-card">
      <div class="schedule-table-card__header">
        <div>
          <h2>Danh sách lịch trình</h2>
          <span>Hiện có {{ danh_sach_da_loc.length }} bản ghi</span>
        </div>

        <div class="schedule-table-card__actions">
          <div class="schedule-search">
            <i class="fas fa-magnifying-glass"></i>
            <input v-model.trim="boLoc.tim_kiem" type="text" placeholder="Tìm kiếm lịch trình...">
          </div>
          <button type="button" class="schedule-filter" @click="taiLaiDuLieu"><i class="fas fa-filter"></i> Lọc dữ liệu</button>
        </div>
      </div>

      <div v-if="thong_bao_loi" class="schedule-alert">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ thong_bao_loi }}</span>
      </div>

      <div v-if="dang_tai" class="schedule-empty">
        <i class="fas fa-spinner fa-spin"></i>
        <span>Đang tải lịch khởi hành...</span>
      </div>

      <div v-else-if="!danh_sach_hien_thi.length" class="schedule-empty">
        <i class="fas fa-calendar-xmark"></i>
        <span>Chưa có lịch khởi hành phù hợp với bộ lọc hiện tại.</span>
      </div>

      <div v-else class="schedule-table-wrap">
        <table class="schedule-table">
          <thead>
            <tr>
              <th>Tour</th>
              <th>Ngày khởi hành</th>
              <th>Số chỗ</th>
              <th>Giá</th>
              <th>Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="lich in danh_sach_hien_thi"
              :key="lich.id"
              :class="{ 'is-selected': selected_lich && selected_lich.id === lich.id }"
              @click="selected_lich = lich"
            >
              <td>
                <div class="schedule-tour-cell">
                  <img :src="lich.tour.hinh_anh" :alt="lich.tour.ten">
                  <div>
                    <strong>{{ lich.tour.ten }}</strong>
                    <span>Mã: {{ lich.id }}</span>
                  </div>
                </div>
              </td>
              <td>
                <strong>{{ lich.ngay_bat_dau_hien_thi }}</strong>
                <span>{{ lich.ngay_ket_thuc_hien_thi }}</span>
              </td>
              <td>
                <div class="seat-progress">
                  <div class="seat-progress__bar"><span :style="{ width: `${lich.phan_tram_cho}%` }"></span></div>
                  <strong>{{ lich.so_cho_con_lai }} / {{ lich.tong_so_cho }}</strong>
                </div>
              </td>
              <td>{{ lich.gia_hien_thi }}</td>
              <td class="schedule-status-cell">
                <span class="schedule-status" :class="`schedule-status--${lich.trang_thai_hien_thi.key}`">
                  {{ lich.trang_thai_hien_thi.label }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="schedule-table-card__footer">
        <span>Hiển thị {{ thong_tin_phan_trang.tu }}-{{ thong_tin_phan_trang.den }} trên {{ danh_sach_da_loc.length }} lịch trình</span>
        <div class="pagination">
          <button type="button" :disabled="phan_trang.trang_hien_tai === 1" @click="doiTrang(phan_trang.trang_hien_tai - 1)"><i class="fas fa-chevron-left"></i></button>
          <button v-for="page in tong_so_trang" :key="page" type="button" :class="{ 'is-active': page === phan_trang.trang_hien_tai }" @click="doiTrang(page)">{{ page }}</button>
          <button type="button" :disabled="phan_trang.trang_hien_tai === tong_so_trang" @click="doiTrang(phan_trang.trang_hien_tai + 1)"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
      </section>

      <aside class="schedule-quick-card">
        <template v-if="selected_lich">
          <p class="schedule-quick-card__eyebrow">Xem nhanh lịch khởi hành</p>
          <h3>{{ selected_lich.tour.ten }}</h3>
          <img :src="selected_lich.tour.hinh_anh" :alt="selected_lich.tour.ten">

          <div class="schedule-quick-card__row">
            <span>Mã lịch</span>
            <strong>{{ selected_lich.id }}</strong>
          </div>
          <div class="schedule-quick-card__row">
            <span>Ngày khởi hành</span>
            <strong>{{ selected_lich.ngay_bat_dau_hien_thi }}</strong>
          </div>
          <div class="schedule-quick-card__row">
            <span>Số chỗ</span>
            <strong>{{ selected_lich.so_cho_con_lai }} / {{ selected_lich.tong_so_cho }}</strong>
          </div>
          <div class="schedule-quick-card__row">
            <span>Giá</span>
            <strong>{{ selected_lich.gia_hien_thi }}</strong>
          </div>
          <div class="schedule-quick-card__row">
            <span>Trạng thái</span>
            <strong>{{ selected_lich.trang_thai_hien_thi.label }}</strong>
          </div>

          <div class="schedule-quick-card__actions">
            <button type="button" class="quick-btn quick-btn--primary" @click="xemChiTietLich(selected_lich)">Xem chi tiết</button>
            <button type="button" class="quick-btn quick-btn--secondary" @click="chinhSuaLich(selected_lich)">Chỉnh sửa</button>
            <button type="button" class="quick-btn quick-btn--danger" @click="xoaLich(selected_lich)">Xóa</button>
          </div>
        </template>
        <p v-else class="schedule-quick-card__empty">Chọn một lịch khởi hành để xem nhanh thông tin.</p>
      </aside>
    </section>

    <div v-if="hien_form_khoi_hanh" class="schedule-create-backdrop" @click.self="dongFormKhoiHanh">
      <form class="schedule-create-modal" @submit.prevent="luuLichKhoiHanh">
        <div class="schedule-create-modal__header">
          <div>
            <h3>{{ tieuDeFormKhoiHanh }}</h3>
            <p>{{ moTaFormKhoiHanh }}</p>
          </div>
          <button type="button" @click="dongFormKhoiHanh"><i class="fas fa-xmark"></i></button>
        </div>

        <div class="schedule-create-modal__grid">
          <label>
            <span>Tour</span>
            <select v-model="form_khoi_hanh.ma_tour" :disabled="laCheDoChinhSua" required>
              <option value="" disabled>Chọn tour</option>
              <option v-for="tour in danh_sach_tour_options" :key="tour.id" :value="tour.id">
                {{ tour.ten }} ({{ tour.id }})
              </option>
            </select>
          </label>

          <label>
            <span>Số chỗ</span>
            <input v-model.number="form_khoi_hanh.so_cho" type="number" min="1" required>
          </label>

          <label>
            <span>Ngày bắt đầu</span>
            <input v-model="form_khoi_hanh.ngay_bat_dau" type="date" required>
          </label>

          <label>
            <span>Ngày kết thúc</span>
            <input v-model="form_khoi_hanh.ngay_ket_thuc" type="date">
          </label>
        </div>

        <label class="schedule-create-modal__switch">
          <input v-model="form_khoi_hanh.tinh_trang" type="checkbox">
          <span>Kích hoạt lịch ngay sau khi tạo</span>
        </label>

        <p v-if="loi_tao_khoi_hanh" class="schedule-create-modal__error">{{ loi_tao_khoi_hanh }}</p>

        <div class="schedule-create-modal__footer">
          <button type="button" class="schedule-create-modal__btn schedule-create-modal__btn--ghost" :disabled="dang_tao_khoi_hanh" @click="dongFormKhoiHanh">Hủy</button>
          <button type="submit" class="schedule-create-modal__btn schedule-create-modal__btn--primary" :disabled="dang_tao_khoi_hanh">
            <i v-if="dang_tao_khoi_hanh" class="fas fa-spinner fa-spin"></i>
            <span>{{ dang_tao_khoi_hanh ? 'Đang lưu...' : nhanNutLuuKhoiHanh }}</span>
          </button>
        </div>
      </form>
    </div>

    <div v-if="chi_tiet_dang_xem" class="schedule-modal-backdrop" @click.self="dongChiTiet">
      <div class="schedule-modal">
        <div class="schedule-modal__header">
          <div>
            <h3>Chi tiết lịch khởi hành</h3>
            <p>{{ chi_tiet_dang_xem.id }}</p>
          </div>
          <button type="button" @click="dongChiTiet"><i class="fas fa-xmark"></i></button>
        </div>

        <div class="schedule-modal__grid">
          <div>
            <span>Tour</span>
            <strong>{{ chi_tiet_dang_xem.tour.ten }}</strong>
          </div>
          <div>
            <span>Ngày bắt đầu</span>
            <strong>{{ chi_tiet_dang_xem.ngay_bat_dau_hien_thi }}</strong>
          </div>
          <div>
            <span>Ngày kết thúc</span>
            <strong>{{ chi_tiet_dang_xem.ngay_ket_thuc_hien_thi }}</strong>
          </div>
          <div>
            <span>Số chỗ</span>
            <strong>{{ chi_tiet_dang_xem.so_cho_con_lai }} / {{ chi_tiet_dang_xem.tong_so_cho }}</strong>
          </div>
          <div>
            <span>Giá tham chiếu</span>
            <strong>{{ chi_tiet_dang_xem.gia_hien_thi }}</strong>
          </div>
          <div>
            <span>Tình trạng kích hoạt</span>
            <strong>{{ chi_tiet_dang_xem.tinh_trang ? 'Đang mở bán' : 'Đã tắt' }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import { showAlert, showConfirm } from '../../../services/appDialog';

const TOUR_KHOI_HANH_API = '/api/tour-khoi-hanh';
const TOUR_API = '/api/tour';
const HINH_MAC_DINH = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=900&q=80';

export default {
  name: 'AdminTourKhoiHanhPage',
  data() {
    return {
      dang_tai: false,
      thong_bao_loi: '',
      boLoc: {
        tim_kiem: '',
      },
      danh_sach_lich: [],
      selected_lich: null,
      ban_do_tour: {},
      chi_tiet_dang_xem: null,
      hien_form_khoi_hanh: false,
      lich_dang_sua_id: '',
      dang_tao_khoi_hanh: false,
      loi_tao_khoi_hanh: '',
      form_khoi_hanh: {
        ma_tour: '',
        ngay_bat_dau: '',
        ngay_ket_thuc: '',
        so_cho: 20,
        tinh_trang: true,
      },
      phan_trang: {
        trang_hien_tai: 1,
        moi_trang: 10,
      },
    };
  },
  computed: {
    danh_sach_da_loc() {
      const keyword = this.boLoc.tim_kiem.trim().toLowerCase();
      if (!keyword) return this.danh_sach_lich;
      return this.danh_sach_lich.filter((lich) => `${lich.id} ${lich.tour.ten}`.toLowerCase().includes(keyword));
    },
    tong_so_trang() {
      return Math.max(1, Math.ceil(this.danh_sach_da_loc.length / this.phan_trang.moi_trang));
    },
    danh_sach_hien_thi() {
      const batDau = (this.phan_trang.trang_hien_tai - 1) * this.phan_trang.moi_trang;
      return this.danh_sach_da_loc.slice(batDau, batDau + this.phan_trang.moi_trang);
    },
    thong_tin_phan_trang() {
      if (!this.danh_sach_da_loc.length) return { tu: 0, den: 0 };
      const tu = (this.phan_trang.trang_hien_tai - 1) * this.phan_trang.moi_trang + 1;
      const den = Math.min(this.danh_sach_da_loc.length, this.phan_trang.trang_hien_tai * this.phan_trang.moi_trang);
      return { tu, den };
    },
    thong_ke() {
      const now = new Date();
      const in7Days = new Date();
      in7Days.setDate(now.getDate() + 7);
      return {
        tong_so: this.danh_sach_lich.length,
        sap_khoi_hanh: this.danh_sach_lich.filter((lich) => lich.ngay_bat_dau && lich.ngay_bat_dau >= now && lich.ngay_bat_dau <= in7Days).length,
        het_cho: this.danh_sach_lich.filter((lich) => lich.so_cho_con_lai <= 0).length,
      };
    },
    danh_sach_tour_options() {
      return Object.values(this.ban_do_tour).sort((a, b) => a.ten.localeCompare(b.ten, 'vi'));
    },
    laCheDoChinhSua() {
      return Boolean(this.lich_dang_sua_id);
    },
    tieuDeFormKhoiHanh() {
      return this.laCheDoChinhSua ? 'Chỉnh sửa lịch khởi hành' : 'Thêm lịch khởi hành';
    },
    moTaFormKhoiHanh() {
      return this.laCheDoChinhSua
        ? 'Cập nhật thông tin lịch khởi hành đã tạo.'
        : 'Tạo mới trực tiếp qua API `tour-khoi-hanh`.';
    },
    nhanNutLuuKhoiHanh() {
      return this.laCheDoChinhSua ? 'Lưu thay đổi' : 'Tạo lịch';
    },
  },
  mounted() {
    this.loadDanhSachLich();
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
    taoNgay(ngay) {
      if (!ngay) return null;
      const date = new Date(ngay);
      return Number.isNaN(date.getTime()) ? null : date;
    },
    dinhDangNgay(ngay) {
      const date = this.taoNgay(ngay);
      if (!date) return 'Đang cập nhật';
      return date.toLocaleDateString('vi-VN');
    },
    dinhDangNgayThu(ngay) {
      const date = this.taoNgay(ngay);
      if (!date) return 'Chưa có lịch';
      return date.toLocaleDateString('vi-VN', { weekday: 'long', hour: '2-digit', minute: '2-digit' });
    },
    chuanHoaTour(item = {}) {
      return {
        id: this.layGiaTriDauTien(item, ['ma_tour', 'Ma_tour', 'id', 'Id']),
        ten: this.layGiaTriDauTien(item, ['ten_tour', 'Ten_tour', 'ten'], 'Tour đang cập nhật'),
        hinh_anh: this.layGiaTriDauTien(item, ['hinh_anh', 'Hinh_anh', 'image'], HINH_MAC_DINH),
        tong_so_cho: Number(this.layGiaTriDauTien(item, ['so_nguoi', 'So_nguoi'], 20)),
        gia_hien_thi: this.dinhDangTien(this.layGiaTriDauTien(item, ['so_tien', 'So_tien'], 0)),
      };
    },
    xacDinhTrangThai(ngayBatDau, ngayKetThuc) {
      const now = new Date();
      if (ngayKetThuc && ngayKetThuc < now) return { key: 'ended', label: 'Đã kết thúc' };
      if (ngayBatDau && ngayBatDau <= now && (!ngayKetThuc || ngayKetThuc >= now)) return { key: 'live', label: 'Đang diễn ra' };
      return { key: 'upcoming', label: 'Sắp diễn ra' };
    },
    chuanHoaLich(item = {}) {
      const id = this.layGiaTriDauTien(item, ['ma_thoi_gian_tour', 'Ma_thoi_gian_tour', 'id', 'Id']);
      const maTour = this.layGiaTriDauTien(item, ['ma_tour', 'Ma_tour']);
      const tour = this.ban_do_tour[maTour] || {
        ten: `Tour ${maTour || ''}`.trim(),
        hinh_anh: HINH_MAC_DINH,
        tong_so_cho: 20,
        gia_hien_thi: 'Liên hệ',
      };
      const ngayBatDau = this.taoNgay(this.layGiaTriDauTien(item, ['ngay_bat_dau', 'Ngay_bat_dau']));
      const ngayKetThuc = this.taoNgay(this.layGiaTriDauTien(item, ['ngay_ket_thuc', 'Ngay_ket_thuc']));
      const soChoConLai = Number(this.layGiaTriDauTien(item, ['so_cho', 'So_cho'], 0));
      const tongSoCho = Math.max(tour.tong_so_cho || soChoConLai, soChoConLai, 1);
      const phanTramCho = Math.max(0, Math.min(100, (soChoConLai / tongSoCho) * 100));
      return {
        id,
        ma_tour: maTour,
        tour,
        ngay_bat_dau: ngayBatDau,
        ngay_ket_thuc: ngayKetThuc,
        ngay_bat_dau_hien_thi: this.dinhDangNgay(ngayBatDau),
        ngay_ket_thuc_hien_thi: this.dinhDangNgayThu(ngayBatDau),
        so_cho_con_lai: soChoConLai,
        tong_so_cho: tongSoCho,
        phan_tram_cho: phanTramCho,
        gia_hien_thi: tour.gia_hien_thi,
        tinh_trang: this.chuanHoaBoolean(this.layGiaTriDauTien(item, ['tinh_trang', 'Tinh_trang'], 0)),
        trang_thai_hien_thi: this.xacDinhTrangThai(ngayBatDau, ngayKetThuc),
      };
    },
    layThongBaoLoi(error, macDinh) {
      const data = error?.response?.data;
      if (data?.message) return data.message;
      if (data?.errors) {
        const list = Object.values(data.errors).flat();
        if (list.length) return list.join(' ');
      }
      return error?.message || macDinh;
    },
    taoFormKhoiHanhMacDinh() {
      return {
        ma_tour: this.danh_sach_tour_options[0]?.id || '',
        ngay_bat_dau: '',
        ngay_ket_thuc: '',
        so_cho: 20,
        tinh_trang: true,
      };
    },
    chuanHoaNgayInput(value) {
      if (!value) return '';
      const text = String(value);
      if (/^\d{4}-\d{2}-\d{2}$/.test(text)) return text;
      const date = new Date(text);
      if (Number.isNaN(date.getTime())) return '';
      return date.toISOString().slice(0, 10);
    },
    moFormKhoiHanh() {
      if (!this.danh_sach_tour_options.length) {
        showAlert({
          title: 'Chưa có tour',
          message: 'Chưa có tour để gắn lịch khởi hành. Vui lòng tạo tour trước.',
          tone: 'warning',
        });
        return;
      }
      this.form_khoi_hanh = this.taoFormKhoiHanhMacDinh();
      this.lich_dang_sua_id = '';
      this.loi_tao_khoi_hanh = '';
      this.hien_form_khoi_hanh = true;
    },
    dongFormKhoiHanh() {
      if (this.dang_tao_khoi_hanh) return;
      this.lich_dang_sua_id = '';
      this.hien_form_khoi_hanh = false;
    },
    taoPayloadKhoiHanhMoi() {
      return {
        ma_tour: this.form_khoi_hanh.ma_tour,
        ngay_bat_dau: this.form_khoi_hanh.ngay_bat_dau || null,
        ngay_ket_thuc: this.form_khoi_hanh.ngay_ket_thuc || null,
        so_cho: Number(this.form_khoi_hanh.so_cho) || 0,
        tinh_trang: Boolean(this.form_khoi_hanh.tinh_trang),
      };
    },
    async luuLichKhoiHanh() {
      if (!this.form_khoi_hanh.ma_tour) {
        this.loi_tao_khoi_hanh = 'Vui lòng chọn tour.';
        return;
      }
      if (
        this.form_khoi_hanh.ngay_bat_dau &&
        this.form_khoi_hanh.ngay_ket_thuc &&
        this.form_khoi_hanh.ngay_ket_thuc < this.form_khoi_hanh.ngay_bat_dau
      ) {
        this.loi_tao_khoi_hanh = 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.';
        return;
      }

      this.dang_tao_khoi_hanh = true;
      this.loi_tao_khoi_hanh = '';
      try {
        const dangChinhSua = this.laCheDoChinhSua && this.lich_dang_sua_id;
        const payload = this.taoPayloadKhoiHanhMoi();
        if (dangChinhSua) {
          await this.goiDuLieu(`${TOUR_KHOI_HANH_API}/${this.lich_dang_sua_id}`, {
            method: 'PUT',
            body: payload,
          });
        } else {
          await this.goiDuLieu(TOUR_KHOI_HANH_API, {
            method: 'POST',
            body: payload,
          });
        }
        this.hien_form_khoi_hanh = false;
        this.lich_dang_sua_id = '';
        await this.loadDanhSachLich();
        await showAlert({
          title: dangChinhSua ? 'Cập nhật thành công' : 'Thêm lịch thành công',
          message: dangChinhSua
            ? 'Lịch khởi hành đã được cập nhật.'
            : 'Lịch khởi hành mới đã được thêm vào hệ thống.',
          tone: 'success',
          confirmText: 'Đã hiểu',
        });
      } catch (error) {
        this.loi_tao_khoi_hanh = this.layThongBaoLoi(
          error,
          this.laCheDoChinhSua ? 'Không thể cập nhật lịch khởi hành.' : 'Không thể tạo lịch khởi hành.'
        );
      } finally {
        this.dang_tao_khoi_hanh = false;
      }
    },
    async loadDanhSachLich() {
      this.dang_tai = true;
      this.thong_bao_loi = '';
      try {
        const [lichRes, tourRes] = await Promise.allSettled([
          this.goiDuLieu(TOUR_KHOI_HANH_API),
          this.goiDuLieu(TOUR_API),
        ]);

        if (tourRes.status !== 'fulfilled') {
          throw tourRes.reason;
        }

        this.ban_do_tour = {};
        this.xuLyDanhSach(tourRes.value.data).forEach((item) => {
          const tour = this.chuanHoaTour(item);
          this.ban_do_tour[tour.id] = tour;
        });

        if (lichRes.status === 'fulfilled') {
          this.danh_sach_lich = this.xuLyDanhSach(lichRes.value.data).map((item) => this.chuanHoaLich(item));
        } else {
          this.danh_sach_lich = [];
        }

        this.phan_trang.trang_hien_tai = 1;
      } catch (error) {
        this.danh_sach_lich = [];
        this.thong_bao_loi = this.layThongBaoLoi(error, 'Không thể tải lịch khởi hành tour.');
      } finally {
        this.dang_tai = false;
      }
    },
    doiTrang(page) {
      if (page < 1 || page > this.tong_so_trang) return;
      this.phan_trang.trang_hien_tai = page;
    },
    async xemChiTietLich(lich) {
      try {
        const res = await this.goiDuLieu(`${TOUR_KHOI_HANH_API}/${lich.id}`);
        const item = this.xuLyItem(res.data);
        this.chi_tiet_dang_xem = this.chuanHoaLich(item);
      } catch (error) {
        await showAlert({
          title: 'Không thể tải chi tiết',
          message: this.layThongBaoLoi(error, 'Không thể tải chi tiết lịch khởi hành.'),
          tone: 'danger',
        });
      }
    },
    dongChiTiet() {
      this.chi_tiet_dang_xem = null;
    },
    async chinhSuaLich(lich) {
      try {
        const res = await this.goiDuLieu(`${TOUR_KHOI_HANH_API}/${lich.id}`);
        const item = this.xuLyItem(res.data);
        const maTour = this.layGiaTriDauTien(item, ['ma_tour', 'Ma_tour'], lich.ma_tour);
        if (!maTour) {
          throw new Error('Không xác định được mã tour của lịch khởi hành.');
        }

        this.form_khoi_hanh = {
          ma_tour: maTour,
          ngay_bat_dau: this.chuanHoaNgayInput(this.layGiaTriDauTien(item, ['ngay_bat_dau', 'Ngay_bat_dau'])),
          ngay_ket_thuc: this.chuanHoaNgayInput(this.layGiaTriDauTien(item, ['ngay_ket_thuc', 'Ngay_ket_thuc'])),
          so_cho: Number(this.layGiaTriDauTien(item, ['so_cho', 'So_cho'], lich.so_cho_con_lai || 0)) || 0,
          tinh_trang: this.chuanHoaBoolean(this.layGiaTriDauTien(item, ['tinh_trang', 'Tinh_trang'], lich.tinh_trang)),
        };

        this.lich_dang_sua_id = lich.id;
        this.loi_tao_khoi_hanh = '';
        this.chi_tiet_dang_xem = null;
        this.hien_form_khoi_hanh = true;
      } catch (error) {
        await showAlert({
          title: 'Không thể mở form chỉnh sửa',
          message: this.layThongBaoLoi(error, 'Không thể tải dữ liệu lịch khởi hành để chỉnh sửa.'),
          tone: 'danger',
        });
      }
    },
    async xoaLich(lich) {
      const xacNhan = await showConfirm({
        title: 'Xác nhận xóa lịch',
        message: `Bạn có chắc muốn xóa lịch khởi hành ${lich.id}?`,
        tone: 'danger',
        confirmText: 'Xóa lịch',
        cancelText: 'Hủy',
      });
      if (!xacNhan) return;
      try {
        await this.goiDuLieu(`${TOUR_KHOI_HANH_API}/${lich.id}`, { method: 'DELETE' });
        await this.loadDanhSachLich();
        await showAlert({
          title: 'Xóa thành công',
          message: `Đã xóa lịch khởi hành ${lich.id}.`,
          tone: 'success',
          confirmText: 'Đã hiểu',
        });
      } catch (error) {
        await showAlert({
          title: 'Không thể xóa lịch',
          message: this.layThongBaoLoi(error, 'Không thể xóa lịch khởi hành.'),
          tone: 'danger',
        });
      }
    },
    themLichKhoiHanh() {
      this.moFormKhoiHanh();
    },
    taiLaiDuLieu() {
      this.loadDanhSachLich();
    },
  },
  watch: {
    'boLoc.tim_kiem'() {
      this.phan_trang.trang_hien_tai = 1;
    },
    danh_sach_hien_thi: {
      handler(items) {
        if (!items.length) {
          this.selected_lich = null;
          return;
        }
        const exists = items.find((item) => item.id === this.selected_lich?.id);
        if (!exists) {
          [this.selected_lich] = items;
        }
      },
      immediate: true,
    },
  },
};
</script>

<style scoped>
.admin-tour-schedule-page {
  display: grid;
  gap: 1.5rem;
  padding: 1.5rem;
  position: relative;
  background:
    radial-gradient(circle at top left, rgba(56, 189, 248, 0.08), transparent 28%),
    linear-gradient(180deg, #f8fbff 0%, #f3f7fc 100%);
}

.admin-tour-schedule-page::before {
  display: none;
}

.schedule-hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  position: relative;
  overflow: hidden;
  padding: 1.35rem 1.5rem;
  border-radius: 1.75rem;
  background:
    radial-gradient(circle at top right, rgba(186, 230, 253, 0.45), transparent 24%),
    linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 252, 255, 0.98) 100%);
  border: 1px solid rgba(33, 85, 132, 0.08);
  box-shadow: 0 18px 36px rgba(17, 62, 105, 0.08);
}

.schedule-hero::before,
.schedule-hero::after {
  content: '';
  position: absolute;
  border-radius: 999px;
  pointer-events: none;
}

.schedule-hero::before {
  width: 240px;
  height: 240px;
  top: -90px;
  right: -70px;
  background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, rgba(56, 189, 248, 0) 72%);
}

.schedule-hero::after {
  width: 180px;
  height: 180px;
  left: 38%;
  bottom: -90px;
  background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, rgba(14, 165, 233, 0) 72%);
}

.schedule-hero > div,
.schedule-create {
  position: relative;
  z-index: 1;
}

.schedule-hero__eyebrow {
  margin: 0 0 0.45rem;
  color: #2f6f95;
  font-size: 0.82rem;
  font-weight: 800;
  letter-spacing: 0.16em;
  text-transform: uppercase;
}

.schedule-hero h1 {
  margin: 0;
  color: #102f56;
  font-size: clamp(1.95rem, 3vw, 2.65rem);
  font-weight: 900;
  line-height: 1.08;
}

.schedule-hero p {
  max-width: 680px;
  margin: 0.6rem 0 0;
  color: #5c7b98;
  font-size: 0.96rem;
  line-height: 1.6;
}

.schedule-create {
  min-height: 3rem;
  padding: 0 1.1rem;
  border: none;
  border-radius: 1rem;
  background: linear-gradient(135deg, #0b7399 0%, #106f98 100%);
  color: #ffffff;
  font-size: 0.94rem;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 0.7rem;
  box-shadow: 0 14px 28px rgba(16, 111, 152, 0.26);
}

.schedule-summary {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.schedule-main-grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(320px, 0.95fr);
  gap: 1rem;
  align-items: start;
}

.schedule-summary-card {
  position: relative;
  overflow: hidden;
  padding: 1.4rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-radius: 1.4rem;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(33, 85, 132, 0.08);
  box-shadow: 0 18px 36px rgba(17, 62, 105, 0.08);
}

.schedule-summary-card::after {
  content: '';
  position: absolute;
  width: 110px;
  height: 110px;
  top: -25px;
  right: -25px;
  border-radius: 50%;
  background: rgba(15, 107, 146, 0.06);
}

.schedule-summary-card__icon {
  width: 3.8rem;
  height: 3.8rem;
  display: grid;
  place-items: center;
  border-radius: 1rem;
  font-size: 1.35rem;
}

.schedule-summary-card__icon--primary { background: #e1f0ff; color: #0f6b92; }
.schedule-summary-card__icon--info { background: #dbeafe; color: #1d4ed8; }
.schedule-summary-card__icon--danger { background: #fee2e2; color: #b91c1c; }

.schedule-summary-card span,
.schedule-modal__grid span {
  display: block;
  color: #5d7d9e;
  font-size: 0.82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.schedule-summary-card strong,
.schedule-modal__grid strong {
  display: block;
  margin-top: 0.35rem;
  color: #102f56;
  font-size: 2rem;
  font-weight: 900;
}

.schedule-summary-card small {
  display: block;
  margin-top: 0.4rem;
  color: #6783a3;
}

.schedule-table-card {
  border-radius: 1.6rem;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(33, 85, 132, 0.08);
  box-shadow: 0 18px 36px rgba(17, 62, 105, 0.08);
  overflow: hidden;
  backdrop-filter: blur(14px);
}

.schedule-table-card__header,
.schedule-table-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.25rem 1.4rem;
}

.schedule-table-card__header h2 {
  margin: 0;
  color: #102f56;
  font-size: 1.5rem;
  font-weight: 900;
}

.schedule-table-card__header span,
.schedule-table-card__footer span {
  margin-top: 0.4rem;
  color: #6783a3;
  font-size: 0.92rem;
}

.schedule-table-card__actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.schedule-search {
  min-width: 300px;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 0 1rem;
  border-radius: 999px;
  background: #f5f9ff;
  border: 1px solid #d7e4f4;
}

.schedule-search input {
  width: 100%;
  min-height: 2.9rem;
  border: none;
  background: transparent;
  color: #17385f;
}

.schedule-search input:focus { outline: none; }

.schedule-filter,
.schedule-actions button,
.pagination button,
.schedule-modal__header button {
  border: none;
}

.schedule-filter {
  min-height: 2.9rem;
  padding: 0 1rem;
  border-radius: 999px;
  background: #edf5ff;
  color: #0f6b92;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
}

.schedule-alert,
.schedule-empty {
  min-height: 90px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.3rem;
}

.schedule-alert { color: #b42318; }
.schedule-empty { color: #4a6a8d; }

.schedule-table-wrap { overflow-x: auto; }
.schedule-table { width: 100%; border-collapse: collapse; }

.schedule-table thead th {
  padding: 1rem 1.4rem;
  text-align: left;
  color: #355d88;
  font-size: 0.82rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  border-top: 1px solid rgba(33, 85, 132, 0.08);
  border-bottom: 1px solid rgba(33, 85, 132, 0.08);
}

.schedule-table thead th:last-child,
.schedule-table tbody td:last-child {
  width: 168px;
  text-align: center;
}

.schedule-table thead th:nth-child(5),
.schedule-table tbody td:nth-child(5) {
  width: 190px;
  text-align: center;
}

.schedule-table tbody td {
  padding: 1.15rem 1.4rem;
  color: #17385f;
  border-bottom: 1px solid rgba(33, 85, 132, 0.08);
  vertical-align: middle;
}

.schedule-table tbody tr {
  cursor: pointer;
}

.schedule-table tbody tr.is-selected {
  background: linear-gradient(90deg, rgba(15, 107, 146, 0.08), rgba(15, 107, 146, 0.02));
}

.schedule-tour-cell {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.schedule-tour-cell img {
  width: 4rem;
  height: 4rem;
  object-fit: cover;
  border-radius: 1rem;
}

.schedule-tour-cell strong { display: block; font-size: 1.12rem; color: #102f56; }
.schedule-tour-cell span,
.schedule-table td > span { display: block; margin-top: 0.3rem; color: #6783a3; }

.seat-progress {
  min-width: 130px;
}

.seat-progress__bar {
  height: 0.5rem;
  margin-bottom: 0.5rem;
  border-radius: 999px;
  background: #d7e4f4;
  overflow: hidden;
}

.seat-progress__bar span {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(135deg, #0b7399 0%, #22c55e 100%);
}

.schedule-status {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2rem;
  min-width: 130px;
  padding: 0 0.85rem;
  border-radius: 999px;
  font-size: 0.86rem;
  font-weight: 800;
  white-space: nowrap;
}

.schedule-status-cell {
  text-align: center;
}

.schedule-status--upcoming { background: #dbeafe; color: #1d4ed8; }
.schedule-status--live { background: #dcfce7; color: #15803d; }
.schedule-status--ended { background: #e2e8f0; color: #64748b; }

.schedule-actions-cell {
  padding-left: 1rem;
  padding-right: 1rem;
}

.schedule-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  width: 100%;
}

.schedule-actions button {
  width: 2.45rem;
  height: 2.45rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 0.85rem;
  background: #f4f8ff;
  color: #0f6b92;
}

.schedule-actions button i {
  width: 1rem;
  text-align: center;
  line-height: 1;
}

.schedule-actions .is-danger { color: #b91c1c; }

.pagination {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.pagination button {
  width: 2.6rem;
  height: 2.6rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  border-radius: 50%;
  background: #f4f8ff;
  color: #0f6b92;
  font-weight: 800;
  font-size: 0.95rem;
  font-variant-numeric: tabular-nums;
}

.pagination .is-active { background: #0f6b92; color: #ffffff; }

.pagination button i {
  font-size: 0.85rem;
  line-height: 1;
}

.schedule-quick-card {
  border-radius: 1.6rem;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(33, 85, 132, 0.08);
  box-shadow: 0 18px 36px rgba(17, 62, 105, 0.08);
  backdrop-filter: blur(14px);
  padding: 1.2rem;
  position: sticky;
  top: 1rem;
  display: grid;
  gap: 0.85rem;
}

.schedule-quick-card__eyebrow {
  margin: 0;
  color: #2f6f95;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.schedule-quick-card h3 {
  margin: 0;
  color: #0d2d55;
  font-size: 1.35rem;
  font-weight: 900;
}

.schedule-quick-card img {
  width: 100%;
  height: 170px;
  object-fit: cover;
  border-radius: 1rem;
}

.schedule-quick-card__row {
  display: grid;
  gap: 0.3rem;
  padding-bottom: 0.6rem;
  border-bottom: 1px solid rgba(33, 85, 132, 0.08);
}

.schedule-quick-card__row span {
  color: #5f7d9b;
}

.schedule-quick-card__row strong {
  color: #17385f;
  font-size: 0.95rem;
}

.schedule-quick-card__actions {
  margin-top: 0.3rem;
  display: grid;
  gap: 0.55rem;
}

.quick-btn {
  min-height: 2.6rem;
  border: none;
  border-radius: 0.85rem;
  font-size: 0.92rem;
  font-weight: 800;
}

.quick-btn--primary {
  color: #fff;
  background: linear-gradient(135deg, #3b3ef6, #4422d4);
}

.quick-btn--secondary {
  background: #edf3fb;
  color: #23486d;
}

.quick-btn--danger {
  background: #fef2f2;
  color: #b91c1c;
}

.schedule-quick-card__empty {
  margin: 0;
  color: #6783a3;
  min-height: 220px;
  display: grid;
  place-items: center;
  text-align: center;
}

.schedule-create-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: grid;
  place-items: center;
  z-index: 75;
  padding: 0.9rem;
}

.schedule-create-modal {
  width: min(700px, 100%);
  background: #ffffff;
  border-radius: 1.2rem;
  box-shadow: 0 24px 55px rgba(15, 23, 42, 0.28);
  padding: 1.2rem;
}

.schedule-create-modal__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.schedule-create-modal__header h3 {
  margin: 0;
  color: #102f56;
  font-size: 1.35rem;
  font-weight: 900;
}

.schedule-create-modal__header p {
  margin: 0.35rem 0 0;
  color: #647a96;
}

.schedule-create-modal__header button {
  width: 2.4rem;
  height: 2.4rem;
  border: none;
  border-radius: 50%;
  background: #f4f8ff;
  color: #0f6b92;
}

.schedule-create-modal__grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.8rem;
}

.schedule-create-modal__grid label {
  display: grid;
  gap: 0.4rem;
}

.schedule-create-modal__grid label span {
  color: #355d88;
  font-size: 0.8rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.09em;
}

.schedule-create-modal__grid input,
.schedule-create-modal__grid select {
  min-height: 2.7rem;
  border: 1px solid #d7e4f4;
  border-radius: 0.8rem;
  padding: 0.65rem 0.8rem;
  color: #17385f;
  background: #ffffff;
}

.schedule-create-modal__grid input:focus,
.schedule-create-modal__grid select:focus {
  outline: none;
  border-color: #0f6b92;
  box-shadow: 0 0 0 3px rgba(15, 107, 146, 0.15);
}

.schedule-create-modal__switch {
  margin-top: 0.9rem;
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  color: #355d88;
  font-weight: 700;
}

.schedule-create-modal__error {
  margin: 0.9rem 0 0;
  color: #b42318;
  font-weight: 700;
}

.schedule-create-modal__footer {
  margin-top: 1rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.7rem;
}

.schedule-create-modal__btn {
  min-height: 2.8rem;
  padding: 0 1rem;
  border: none;
  border-radius: 0.8rem;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
}

.schedule-create-modal__btn--ghost {
  background: #eff6ff;
  color: #355d88;
}

.schedule-create-modal__btn--primary {
  background: linear-gradient(135deg, #0b7399 0%, #106f98 100%);
  color: #ffffff;
}

.schedule-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.4);
  display: grid;
  place-items: center;
  z-index: 70;
}

.schedule-modal {
  width: min(720px, calc(100vw - 24px));
  padding: 1.4rem;
  border-radius: 1.5rem;
  background: #ffffff;
  box-shadow: 0 28px 60px rgba(15, 23, 42, 0.24);
}

.schedule-modal__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.schedule-modal__header h3 { margin: 0; color: #102f56; font-size: 1.45rem; font-weight: 900; }
.schedule-modal__header p { margin: 0.35rem 0 0; color: #6c87a5; }
.schedule-modal__header button {
  width: 2.7rem;
  height: 2.7rem;
  border-radius: 50%;
  background: #f4f8ff;
  color: #0f6b92;
}

.schedule-modal__grid {
  margin-top: 1.2rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

@media (max-width: 1100px) {
  .schedule-hero,
  .schedule-table-card__header,
  .schedule-table-card__footer {
    grid-template-columns: 1fr;
    flex-direction: column;
    align-items: stretch;
  }

  .schedule-summary {
    grid-template-columns: 1fr;
  }

  .schedule-main-grid {
    grid-template-columns: 1fr;
  }

  .schedule-quick-card {
    position: static;
  }

  .schedule-search {
    min-width: 0;
    width: 100%;
  }
}

@media (max-width: 768px) {
  .admin-tour-schedule-page {
    padding: 1rem;
  }

  .schedule-hero {
    padding: 1.35rem;
  }

  .schedule-modal__grid {
    grid-template-columns: 1fr;
  }

  .schedule-create-modal__grid {
    grid-template-columns: 1fr;
  }
}
</style>

