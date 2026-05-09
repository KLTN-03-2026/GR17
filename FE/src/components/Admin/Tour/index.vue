<template>
  <div class="admin-tour-page">
    <section class="tour-page__hero">
      <div>
        <p class="tour-page__eyebrow">Bảng điều khiển quản trị</p>
        <h1>Quản lý tour</h1>
        <p>Admin chỉ xem chi tiết tour và duyệt tour đối tác đang chờ duyệt theo đúng luồng backend.</p>
      </div>
      <button class="tour-page__refresh" type="button" @click="loadDanhSachTour" :disabled="dangTai">
        <i class="fas fa-rotate"></i>
        <span>{{ dangTai ? "Đang tải..." : "Làm mới" }}</span>
      </button>
    </section>

    <section class="tour-summary">
      <article class="summary-card">
        <span>Tổng tour hiển thị</span>
        <strong>{{ thongKe.tongTourHienThi }}</strong>
      </article>
      <article class="summary-card summary-card--pending">
        <span>Tour đối tác chờ duyệt</span>
        <strong>{{ thongKe.tongTourChoDuyet }}</strong>
      </article>
      <article class="summary-card">
        <span>Tour đối tác đã duyệt</span>
        <strong>{{ thongKe.tongTourDoiTacDaDuyet }}</strong>
      </article>
    </section>

    <section class="tour-main-grid">
      <section class="tour-table-card">
        <div class="tour-table-card__header">
          <h2>Danh sách gần đây</h2>
          <div class="tour-toolbar">
            <div class="tour-search">
              <i class="fas fa-magnifying-glass"></i>
              <input v-model.trim="boLoc.timKiem" type="text" placeholder="Tìm tên tour hoặc mã tour...">
            </div>
            <select v-model="boLoc.trangThaiDuyet" class="tour-select">
              <option value="tat_ca">Tất cả trạng thái duyệt</option>
              <option value="pending_approval">Chờ duyệt</option>
              <option value="approved">Đã duyệt</option>
            </select>
            <select v-model="boLoc.nguonTao" class="tour-select">
              <option value="tat_ca">Tất cả nguồn tạo</option>
              <option value="doi_tac">Đối tác</option>
              <option value="he_thong">Hệ thống</option>
            </select>
            <select v-model="boLoc.sapXep" class="tour-select">
              <option value="uu_tien_duyet_moi_nhat">Ưu tiên chờ duyệt (mới nhất)</option>
              <option value="moi_nhat">Mới cập nhật trước</option>
              <option value="cu_nhat">Cũ cập nhật trước</option>
              <option value="gia_tang_dan">Giá tăng dần</option>
              <option value="gia_giam_dan">Giá giảm dần</option>
              <option value="ten_a_z">Tên A - Z</option>
              <option value="ten_z_a">Tên Z - A</option>
            </select>
          </div>
        </div>

        <div v-if="thongBaoLoi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thongBaoLoi }}</span>
        </div>

        <div v-if="dangTai" class="tour-empty">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang tải dữ liệu tour...</span>
        </div>

        <div v-else-if="!danhSachHienThi.length" class="tour-empty">
          <i class="fas fa-compass"></i>
          <span>Không có tour phù hợp với bộ lọc hiện tại.</span>
        </div>

        <div v-else class="tour-table-wrap">
          <table class="tour-table">
            <thead>
              <tr>
                <th>Tên tour</th>
                <th>Giá</th>
                <th>Thời gian</th>
                <th>Nguồn tạo</th>
                <th>Trạng thái duyệt</th>
                <th>Trạng thái hiển thị</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="tour in danhSachHienThi"
                :key="tour.id"
                :class="{ 'is-selected': selectedTour && selectedTour.id === tour.id }"
                @click="selectedTour = tour"
              >
                <td>
                  <div class="tour-row__identity">
                    <img :src="tour.hinhAnh" :alt="tour.ten">
                    <div>
                      <strong>{{ tour.ten }}</strong>
                      <span>ID: {{ tour.id }}</span>
                      <small v-if="tour.lyDoTuChoi" class="tour-row__reject-reason">
                        Lý do từ chối: {{ tour.lyDoTuChoi }}
                      </small>
                    </div>
                  </div>
                </td>
                <td>{{ tour.giaHienThi }}</td>
                <td>{{ tour.thoiGianHienThi }}</td>
                <td>{{ tour.nguonHienThi }}</td>
                <td>
                  <span class="tour-review-status" :class="`tour-review-status--${layToneTrangThaiDuyet(tour.trangThaiDuyet)}`">
                    {{ hienThiTrangThaiDuyet(tour.trangThaiDuyet) }}
                  </span>
                </td>
                <td>
                  <span class="tour-display-status" :class="tour.trangThaiHienThi ? 'tour-display-status--visible' : 'tour-display-status--hidden'">
                    {{ tour.trangThaiHienThi ? "Đang hiển thị" : "Đang ẩn" }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="tour-table-card__footer">
          <span>Hiển thị {{ phanTrangTu }}-{{ phanTrangDen }} trên {{ danhSachDaLoc.length }} tour</span>
          <div class="pagination">
            <button type="button" :disabled="phanTrang.trangHienTai === 1" @click="doiTrang(phanTrang.trangHienTai - 1)">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button
              v-for="page in tongSoTrang"
              :key="page"
              type="button"
              :class="{ 'is-active': page === phanTrang.trangHienTai }"
              @click="doiTrang(page)"
            >
              {{ page }}
            </button>
            <button type="button" :disabled="phanTrang.trangHienTai === tongSoTrang" @click="doiTrang(phanTrang.trangHienTai + 1)">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </section>

      <aside class="tour-quick-card">
        <template v-if="selectedTour">
          <p class="tour-quick-card__eyebrow">Xem nhanh tour</p>
          <h3>{{ selectedTour.ten }}</h3>
          <img :src="selectedTour.hinhAnh" :alt="selectedTour.ten">

          <div class="tour-quick-card__row">
            <span>Mã tour</span>
            <strong>{{ selectedTour.id }}</strong>
          </div>
          <div class="tour-quick-card__row">
            <span>Nguồn tạo</span>
            <strong>{{ selectedTour.nguonHienThi }}</strong>
          </div>
          <div class="tour-quick-card__row">
            <span>Trạng thái duyệt</span>
            <strong>{{ hienThiTrangThaiDuyet(selectedTour.trangThaiDuyet) }}</strong>
          </div>
          <div class="tour-quick-card__row">
            <span>Hiển thị</span>
            <strong>{{ selectedTour.trangThaiHienThi ? "Đang hiển thị" : "Đang ẩn" }}</strong>
          </div>

          <div class="tour-quick-card__actions">
            <button type="button" class="tour-quick-card__btn tour-quick-card__btn--detail" @click="xemChiTietTour(selectedTour)">
              Xem chi tiết tour
            </button>
            <button
              type="button"
              class="tour-quick-card__btn tour-quick-card__btn--approve"
              :disabled="dangXuLyDuyet === selectedTour.id || !coTheDuyetTourDoiTac(selectedTour)"
              @click="duyetTourDoiTac(selectedTour)"
            >
              Duyệt tour
            </button>
            <button
              type="button"
              class="tour-quick-card__btn tour-quick-card__btn--reject"
              :disabled="dangXuLyDuyet === selectedTour.id || !coTheDuyetTourDoiTac(selectedTour)"
              @click="tuChoiTourDoiTac(selectedTour)"
            >
              Từ chối
            </button>
          </div>
        </template>
        <template v-else>
          <p class="tour-quick-card__empty">Chọn một tour trong bảng để xem nhanh chi tiết.</p>
        </template>
      </aside>
    </section>
  </div>
</template>

<script>
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
  position: "top-right",
  duration: 3000,
});
import { goiApi } from "../../../services/httpClient.js";
import { showConfirm } from "../../../services/appDialog";

const TOUR_PUBLIC_API = "/api/tour";
const DOI_TAC_PENDING_API = "/api/admin/doi-tac/tour/pending";
const DOI_TAC_TOUR_MODERATION_API = "/api/admin/doi-tac/tour";
const HINH_MAC_DINH = "https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=900&q=80";

export default {
  name: "AdminTourListPage",
  data() {
    return {
      dangTai: false,
      dangXuLyDuyet: "",
      thongBaoLoi: "",
      boLoc: {
        timKiem: "",
        trangThaiDuyet: "tat_ca",
        nguonTao: "tat_ca",
        sapXep: "uu_tien_duyet_moi_nhat",
      },
      danhSachTour: [],
      selectedTour: null,
      phanTrang: {
        trangHienTai: 1,
        moiTrang: 8,
      },
    };
  },
  computed: {
    danhSachDaLoc() {
      const keyword = this.boLoc.timKiem.trim().toLowerCase();
      let danhSach = [...this.danhSachTour];

      if (keyword) {
        danhSach = danhSach.filter((tour) => `${tour.ten} ${tour.id}`.toLowerCase().includes(keyword));
      }

      if (this.boLoc.trangThaiDuyet !== "tat_ca") {
        danhSach = danhSach.filter((tour) => tour.trangThaiDuyet === this.boLoc.trangThaiDuyet);
      }

      if (this.boLoc.nguonTao !== "tat_ca") {
        const laTourDoiTac = this.boLoc.nguonTao === "doi_tac";
        danhSach = danhSach.filter((tour) => tour.laTourDoiTac === laTourDoiTac);
      }

      return this.sapXepTheoBoLoc(danhSach);
    },
    tongSoTrang() {
      return Math.max(1, Math.ceil(this.danhSachDaLoc.length / this.phanTrang.moiTrang));
    },
    danhSachHienThi() {
      const batDau = (this.phanTrang.trangHienTai - 1) * this.phanTrang.moiTrang;
      return this.danhSachDaLoc.slice(batDau, batDau + this.phanTrang.moiTrang);
    },
    phanTrangTu() {
      if (!this.danhSachDaLoc.length) return 0;
      return (this.phanTrang.trangHienTai - 1) * this.phanTrang.moiTrang + 1;
    },
    phanTrangDen() {
      if (!this.danhSachDaLoc.length) return 0;
      return Math.min(this.phanTrangTu + this.phanTrang.moiTrang - 1, this.danhSachDaLoc.length);
    },
    thongKe() {
      const tourChoDuyet = this.danhSachTour.filter((tour) => this.coTheDuyetTourDoiTac(tour)).length;
      const tourDoiTacDaDuyet = this.danhSachTour.filter(
        (tour) => tour.laTourDoiTac && String(tour.trangThaiDuyet) === "approved"
      ).length;
      const tourHienThi = this.danhSachTour.filter((tour) => tour.trangThaiHienThi).length;
      return {
        tongTourHienThi: tourHienThi,
        tongTourChoDuyet: tourChoDuyet,
        tongTourDoiTacDaDuyet: tourDoiTacDaDuyet,
      };
    },
  },
  mounted() {
    this.loadDanhSachTour();
  },
  methods: {
    async goiDuLieu(url, { method = "GET", body = null, allowNotFound = false } = {}) {
      const response = await goiApi(url, {
        method,
        headers: {
          Accept: "application/json",
          ...(body ? { "Content-Type": "application/json" } : {}),
        },
        body: body ? JSON.stringify(body) : undefined,
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        if (allowNotFound && response.status === 404) {
          return { data: { data: [] }, notFound: true };
        }
        const error = new Error(data?.message || "Yêu cầu API thất bại.");
        error.response = { data, status: response.status };
        throw error;
      }

      return { data, notFound: false };
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
    layGiaTriDauTien(obj, keys, macDinh = "") {
      for (const key of keys) {
        if (obj && obj[key] !== undefined && obj[key] !== null && obj[key] !== "") {
          return obj[key];
        }
      }
      return macDinh;
    },
    layThongBaoLoi(error, macDinh) {
      const data = error?.response?.data;
      if (data?.message) return data.message;
      if (data?.errors) {
        const list = Object.values(data.errors).flat();
        if (list.length) return list.join(" ");
      }
      return error?.message || macDinh;
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
    dinhDangTien(giaTri) {
      const so = Number(giaTri);
      if (!Number.isFinite(so) || so <= 0) return "Liên hệ";
      return `${so.toLocaleString("vi-VN")} đ`;
    },
    chuanHoaTour(item = {}, index = 0, options = {}) {
      const id = this.layGiaTriDauTien(item, ["ma_tour", "Ma_tour", "id", "Id"], `T${index + 1}`);
      const ten = this.layGiaTriDauTien(item, ["ten_tour", "Ten_tour", "ten", "name"], "Tour đang cập nhật");
      const nguonTao = String(this.layGiaTriDauTien(item, ["nguon_tao", "Nguon_tao"], options.nguonMacDinh || "he_thong")).toLowerCase();
      const trangThaiDuyet = String(
        this.layGiaTriDauTien(item, ["trang_thai_duyet", "Trang_thai_duyet"], options.trangThaiDuyetMacDinh || "approved")
      ).toLowerCase();
      const soNgay = Number(this.layGiaTriDauTien(item, ["so_ngay", "So_ngay"], 0));
      const trangThaiHienThi = this.chuanHoaBoolean(
        this.layGiaTriDauTien(item, ["trang_thai_hien_thi", "Trang_thai_hien_thi"], options.trangThaiHienThiMacDinh ?? true),
        options.trangThaiHienThiMacDinh ?? true
      );

      return {
        id: String(id),
        ten,
        hinhAnh: this.layGiaTriDauTien(item, ["hinh_anh", "Hinh_anh", "image", "thumbnail"], HINH_MAC_DINH),
        giaHienThi: this.dinhDangTien(this.layGiaTriDauTien(item, ["so_tien", "So_tien", "gia_tour", "gia"], 0)),
        giaSo: Number(this.layGiaTriDauTien(item, ["so_tien", "So_tien", "gia_tour", "gia"], 0)) || 0,
        thoiGianHienThi: soNgay > 0 ? `${soNgay} ngày` : "Chưa cập nhật",
        nguonHienThi: nguonTao === "doi_tac" ? "Đối tác" : "Hệ thống",
        laTourDoiTac: nguonTao === "doi_tac",
        trangThaiDuyet,
        trangThaiHienThi,
        lyDoTuChoi: this.layGiaTriDauTien(item, ["ly_do_tu_choi", "Ly_do_tu_choi"], ""),
        updatedAt: this.layGiaTriDauTien(item, ["updated_at", "Updated_at", "created_at", "Created_at"], ""),
        updatedAtTimestamp: new Date(this.layGiaTriDauTien(item, ["updated_at", "Updated_at", "created_at", "Created_at"], 0)).getTime() || 0,
      };
    },
    hienThiTrangThaiDuyet(status) {
      const key = String(status || "").toLowerCase();
      if (key === "approved") return "Đã duyệt";
      if (key === "pending_approval") return "Chờ duyệt";
      if (key === "rejected") return "Chưa duyệt";
      if (key === "draft") return "Bản nháp";
      return "Không xác định";
    },
    layToneTrangThaiDuyet(status) {
      const key = String(status || "").toLowerCase();
      if (key === "approved") return "approved";
      if (key === "pending_approval") return "pending";
      if (key === "rejected") return "rejected";
      return "draft";
    },
    coTheDuyetTourDoiTac(tour) {
      return Boolean(tour?.laTourDoiTac) && String(tour?.trangThaiDuyet || "").toLowerCase() === "pending_approval";
    },
    sapXepUuTienChoDuyet(danhSach) {
      return [...danhSach].sort((a, b) => {
        if (this.coTheDuyetTourDoiTac(a) && !this.coTheDuyetTourDoiTac(b)) return -1;
        if (!this.coTheDuyetTourDoiTac(a) && this.coTheDuyetTourDoiTac(b)) return 1;
        return b.updatedAtTimestamp - a.updatedAtTimestamp;
      });
    },
    sapXepTheoBoLoc(danhSach) {
      const type = this.boLoc.sapXep;
      if (type === "moi_nhat") return [...danhSach].sort((a, b) => b.updatedAtTimestamp - a.updatedAtTimestamp);
      if (type === "cu_nhat") return [...danhSach].sort((a, b) => a.updatedAtTimestamp - b.updatedAtTimestamp);
      if (type === "gia_tang_dan") return [...danhSach].sort((a, b) => a.giaSo - b.giaSo);
      if (type === "gia_giam_dan") return [...danhSach].sort((a, b) => b.giaSo - a.giaSo);
      if (type === "ten_a_z") return [...danhSach].sort((a, b) => a.ten.localeCompare(b.ten, "vi"));
      if (type === "ten_z_a") return [...danhSach].sort((a, b) => b.ten.localeCompare(a.ten, "vi"));
      return this.sapXepUuTienChoDuyet(danhSach);
    },
    async loadDanhSachTour() {
      this.dangTai = true;
      this.thongBaoLoi = "";

      try {
        const [publicRes, pendingRes] = await Promise.all([
          this.goiDuLieu(TOUR_PUBLIC_API, { allowNotFound: true }),
          this.goiDuLieu(DOI_TAC_PENDING_API, { allowNotFound: true }),
        ]);

        const publicTours = this.xuLyDanhSach(publicRes.data).map((item, index) =>
          this.chuanHoaTour(item, index, {
            nguonMacDinh: "he_thong",
            trangThaiDuyetMacDinh: "approved",
            trangThaiHienThiMacDinh: true,
          })
        );

        const pendingTours = this.xuLyDanhSach(pendingRes.data).map((item, index) =>
          this.chuanHoaTour(item, index, {
            nguonMacDinh: "doi_tac",
            trangThaiDuyetMacDinh: "pending_approval",
            trangThaiHienThiMacDinh: false,
          })
        );

        const merged = new Map();
        [...publicTours, ...pendingTours].forEach((tour) => {
          merged.set(tour.id, tour);
        });

        this.danhSachTour = Array.from(merged.values());
        this.phanTrang.trangHienTai = 1;

        if (this.danhSachTour.length) {
          const selected = this.danhSachTour.find((item) => item.id === this.selectedTour?.id);
          this.selectedTour = selected || this.danhSachTour[0];
        } else {
          this.selectedTour = null;
        }
      } catch (error) {
        this.danhSachTour = [];
        this.selectedTour = null;
        this.thongBaoLoi = this.layThongBaoLoi(error, "Không thể tải danh sách tour.");
      } finally {
        this.dangTai = false;
      }
    },
    doiTrang(page) {
      if (page < 1 || page > this.tongSoTrang) return;
      this.phanTrang.trangHienTai = page;
    },
    xemChiTietTour(tour) {
      this.$router.push(`/admin/tour/${tour.id}`);
    },
    async duyetTourDoiTac(tour) {
      if (!this.coTheDuyetTourDoiTac(tour)) return;
      const xacNhan = await showConfirm({
        title: "Xác nhận duyệt tour đối tác",
        message: `Bạn có chắc muốn duyệt tour ${tour.ten}?`,
        tone: "success",
        confirmText: "Duyệt tour",
        cancelText: "Hủy",
      });
      if (!xacNhan) return;

      this.dangXuLyDuyet = tour.id;
      try {
        await this.goiDuLieu(`${DOI_TAC_TOUR_MODERATION_API}/${tour.id}/approve`, { method: "PATCH" });
        await this.loadDanhSachTour();
        toaster.success(`Tour ${tour.ten} đã được duyệt.`);
      } catch (error) {
        toaster.error(this.layThongBaoLoi(error, "Không thể duyệt tour đối tác."));
      } finally {
        this.dangXuLyDuyet = "";
      }
    },
    async tuChoiTourDoiTac(tour) {
      if (!this.coTheDuyetTourDoiTac(tour)) return;

      const lyDoNhap = window.prompt(
        `Nhập lý do chưa duyệt cho tour "${tour.ten}"`,
        "Nội dung tour chưa đạt yêu cầu kiểm duyệt."
      );
      if (lyDoNhap === null) return;

      const lyDo = String(lyDoNhap || "").trim();
      if (lyDo.length < 5) {
        toaster.warning("Lý do từ chối phải có ít nhất 5 ký tự.");
        return;
      }

      const xacNhan = await showConfirm({
        title: "Xác nhận từ chối tour",
        message: `Bạn có chắc muốn chuyển tour ${tour.ten} sang trạng thái chưa duyệt?`,
        tone: "warning",
        confirmText: "Từ chối",
        cancelText: "Hủy",
      });
      if (!xacNhan) return;

      this.dangXuLyDuyet = tour.id;
      try {
        await this.goiDuLieu(`${DOI_TAC_TOUR_MODERATION_API}/${tour.id}/reject`, {
          method: "PATCH",
          body: { ly_do_tu_choi: lyDo },
        });
        await this.loadDanhSachTour();
        toaster.success(`Tour ${tour.ten} đã được chuyển sang trạng thái chưa duyệt.`);
      } catch (error) {
        toaster.error(this.layThongBaoLoi(error, "Không thể từ chối tour đối tác."));
      } finally {
        this.dangXuLyDuyet = "";
      }
    },
  },
  watch: {
    boLoc: {
      handler() {
        this.phanTrang.trangHienTai = 1;
      },
      deep: true,
    },
    danhSachHienThi: {
      handler(items) {
        if (!items.length) {
          this.selectedTour = null;
          return;
        }
        const exists = items.find((item) => item.id === this.selectedTour?.id);
        if (!exists) {
          [this.selectedTour] = items;
        }
      },
      immediate: true,
    },
  },
};
</script>

<style scoped>
.admin-tour-page {
  min-height: 100%;
  padding: 1.5rem;
  background: #f8fafc;
  display: grid;
  gap: 1.5rem;
}

.tour-page__hero {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1.5rem;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

.tour-page__eyebrow {
  margin: 0;
  color: #2563eb;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.tour-page__hero h1 {
  margin: 0.5rem 0 0;
  color: #0f172a;
  font-size: 2rem;
  font-weight: 900;
  line-height: 1.2;
}

.tour-page__hero p {
  margin: 0.5rem 0 0;
  color: #64748b;
  font-size: 1rem;
}

.tour-page__refresh {
  min-height: 2.75rem;
  padding: 0 1rem;
  border-radius: 0.75rem;
  border: 1px solid #bfdbfe;
  color: #1d4ed8;
  background: #eff6ff;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tour-page__refresh:hover:not(:disabled) {
  background: #dbeafe;
  transform: translateY(-1px);
}

.tour-page__refresh:active:not(:disabled) {
  transform: scale(0.97);
}

.tour-page__refresh:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.tour-summary {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.summary-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
  transition: box-shadow 0.2s ease;
}

.summary-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.summary-card span {
  color: #64748b;
  font-weight: 700;
  font-size: 0.875rem;
}

.summary-card strong {
  display: block;
  margin-top: 0.5rem;
  color: #0f172a;
  font-size: 2rem;
  font-weight: 900;
  line-height: 1;
}

.summary-card--pending {
  border-color: #bfdbfe;
  background: #f8fbff;
}

.tour-main-grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(320px, 0.8fr);
  gap: 1.5rem;
}

.tour-table-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

.tour-table-card__header,
.tour-table-card__footer {
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.tour-table-card__footer {
  border-top: 1px solid #e2e8f0;
  border-bottom: 0;
  padding: 1.25rem 1.5rem;
  color: #64748b;
  font-size: 0.875rem;
}

.tour-table-card__header h2 {
  margin: 0;
  color: #0f172a;
  font-size: 1.25rem;
  font-weight: 900;
}

.tour-toolbar {
  width: 100%;
  display: grid;
  grid-template-columns: minmax(280px, 1.5fr) repeat(3, minmax(180px, 1fr));
  gap: 0.75rem;
  align-items: center;
}

.tour-search {
  min-width: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border: 1px solid #dbeafe;
  border-radius: 999px;
  background: #f8fbff;
  padding: 0 1rem;
  min-height: 2.75rem;
  transition: border-color 0.2s;
}

.tour-search:focus-within {
  border-color: #2563eb;
  outline: 2px solid rgba(37, 99, 235, 0.2);
}

.tour-search input {
  width: 100%;
  border: none;
  background: transparent;
  color: #1e293b;
  font-size: 0.875rem;
}

.tour-search i {
  color: #64748b;
}

.tour-search input:focus {
  outline: none;
}

.tour-select {
  min-height: 2.75rem;
  border: 1px solid #dbeafe;
  border-radius: 0.75rem;
  background: #f8fbff;
  color: #1e293b;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 0 1rem;
  cursor: pointer;
}

.tour-select:focus {
  outline: 2px solid rgba(37, 99, 235, 0.2);
  border-color: #2563eb;
}

.notice {
  margin: 1rem 1.5rem 0;
  padding: 1rem 1.5rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

.notice--error {
  border: 1px solid #fecaca;
  color: #b91c1c;
  background: #fef2f2;
}

.tour-empty {
  min-height: 240px;
  display: grid;
  place-items: center;
  color: #64748b;
  gap: 0.5rem;
  text-align: center;
  padding: 2rem;
}

.tour-table-wrap {
  overflow: auto;
}

.tour-table {
  width: 100%;
  border-collapse: collapse;
}

.tour-table th,
.tour-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  text-align: left;
  vertical-align: middle;
}

.tour-table th {
  color: #475569;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  background: #f8fafc;
}

.tour-table tbody tr {
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.tour-table tbody tr:hover {
  background: #f8fafc;
}

.tour-table tbody tr.is-selected {
  background: #eff6ff;
}

.tour-row__identity {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.tour-row__identity img {
  width: 72px;
  height: 56px;
  border-radius: 0.75rem;
  object-fit: cover;
  box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.05);
}

.tour-row__identity strong {
  display: block;
  color: #0f172a;
  font-size: 0.95rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.tour-row__identity span {
  display: block;
  color: #64748b;
  font-size: 0.8rem;
}

.tour-row__reject-reason {
  display: block;
  margin-top: 0.25rem;
  color: #ef4444;
  font-size: 0.75rem;
  font-weight: 700;
}

.tour-review-status,
.tour-display-status {
  min-height: 2rem;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0 1rem;
  font-size: 0.75rem;
  font-weight: 800;
}

.tour-review-status--approved {
  color: #166534;
  background: #dcfce7;
}

.tour-review-status--pending {
  color: #1d4ed8;
  background: #dbeafe;
}

.tour-review-status--rejected {
  color: #b91c1c;
  background: #fee2e2;
}

.tour-review-status--draft {
  color: #475569;
  background: #f1f5f9;
}

.tour-display-status--visible {
  color: #166534;
  background: #dcfce7;
}

.tour-display-status--hidden {
  color: #475569;
  background: #f1f5f9;
}

.pagination {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.pagination button {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.5rem;
  border: 1px solid #e2e8f0;
  color: #334155;
  background: #ffffff;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination button:hover:not(:disabled) {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.pagination button.is-active {
  color: #ffffff;
  background: #1d4ed8;
  border-color: #1d4ed8;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.tour-quick-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1.5rem;
  display: grid;
  gap: 0.75rem;
  align-self: start;
  position: sticky;
  top: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

.tour-quick-card__eyebrow {
  margin: 0;
  color: #2563eb;
  font-size: 0.75rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  font-weight: 800;
}

.tour-quick-card h3 {
  margin: 0 0 0.5rem 0;
  color: #0f172a;
  font-size: 1.25rem;
  line-height: 1.3;
}

.tour-quick-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 0.75rem;
  margin-bottom: 0.5rem;
}

.tour-quick-card__row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 0.75rem;
  margin-bottom: 0.25rem;
}

.tour-quick-card__row span {
  color: #64748b;
  font-size: 0.875rem;
}

.tour-quick-card__row strong {
  color: #0f172a;
  font-size: 0.95rem;
  text-align: right;
}

.tour-quick-card__actions {
  margin-top: 0.75rem;
  display: grid;
  gap: 0.5rem;
}

.tour-quick-card__btn {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  border: 1px solid transparent;
  font-weight: 800;
  font-size: 0.875rem;
  padding: 0 1rem;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.tour-quick-card__btn--detail {
  color: #1d4ed8;
  border-color: #bfdbfe;
  background: #eff6ff;
}

.tour-quick-card__btn--detail:hover:not(:disabled) {
  background: #dbeafe;
}

.tour-quick-card__btn--approve {
  color: #166534;
  border-color: #86efac;
  background: #f0fdf4;
}

.tour-quick-card__btn--approve:hover:not(:disabled) {
  background: #dcfce7;
}

.tour-quick-card__btn--reject {
  color: #b91c1c;
  border-color: #fca5a5;
  background: #fef2f2;
}

.tour-quick-card__btn--reject:hover:not(:disabled) {
  background: #fee2e2;
}

.tour-quick-card__btn:active:not(:disabled) {
  transform: scale(0.97);
}

.tour-quick-card__btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.tour-quick-card__empty {
  margin: 0;
  color: #64748b;
  min-height: 240px;
  display: grid;
  place-items: center;
  text-align: center;
  padding: 2rem;
}

@media (max-width: 1180px) {
  .tour-main-grid {
    grid-template-columns: 1fr;
  }

  .tour-quick-card {
    position: static;
  }
}

@media (max-width: 860px) {
  .tour-page__hero,
  .tour-table-card__header,
  .tour-table-card__footer {
    flex-direction: column;
    align-items: stretch;
  }

  .tour-summary {
    grid-template-columns: 1fr;
  }

  .tour-toolbar {
    grid-template-columns: 1fr;
  }

  .tour-search {
    width: 100%;
  }

  .tour-table-card__footer {
    text-align: center;
  }

  .pagination {
    justify-content: center;
  }
}
</style>
