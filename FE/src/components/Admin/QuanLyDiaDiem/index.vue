<template>
  <div class="admin-partner-location-page">
    <section class="page-hero">
      <div>
        <p class="page-hero__eyebrow">Bảng điều khiển quản trị</p>
        <h1>Quản lý địa điểm đối tác</h1>
        <p>Admin chỉ xem chi tiết và duyệt/từ chối địa điểm do đối tác tạo.</p>
      </div>
      <button class="refresh-btn" type="button" :disabled="dangTai" @click="taiDanhSachDiaDiem">
        <i class="fas fa-rotate"></i>
        <span>{{ dangTai ? "Đang tải..." : "Làm mới" }}</span>
      </button>
    </section>

    <section class="summary-grid">
      <article class="summary-card">
        <span>Tổng địa điểm đối tác</span>
        <strong>{{ thongKe.tong }}</strong>
      </article>
      <article class="summary-card summary-card--pending">
        <span>Đang chờ duyệt</span>
        <strong>{{ thongKe.pending }}</strong>
      </article>
      <article class="summary-card">
        <span>Đã duyệt</span>
        <strong>{{ thongKe.approved }}</strong>
      </article>
      <article class="summary-card summary-card--rejected">
        <span>Chưa duyệt</span>
        <strong>{{ thongKe.rejected }}</strong>
      </article>
    </section>

    <section class="toolbar-card">
      <div class="toolbar">
        <label class="toolbar-search">
          <i class="fas fa-magnifying-glass"></i>
          <input
            v-model.trim="boLoc.search"
            type="text"
            placeholder="Tìm theo mã hoặc tên địa điểm..."
            @keyup.enter="taiDanhSachDiaDiem(1)"
          >
        </label>

        <label class="toolbar-select">
          <span>Trạng thái duyệt</span>
          <select v-model="boLoc.trang_thai_duyet" @change="taiDanhSachDiaDiem(1)">
            <option value="">Tất cả</option>
            <option value="pending_approval">Chờ duyệt</option>
            <option value="approved">Đã duyệt</option>
            <option value="rejected">Chưa duyệt</option>
          </select>
        </label>

        <label class="toolbar-select">
          <span>Loại địa điểm</span>
          <select v-model="boLoc.loai" @change="taiDanhSachDiaDiem(1)">
            <option value="">Tất cả</option>
            <option value="1">Du lịch</option>
            <option value="2">Khách sạn</option>
            <option value="3">Nhà hàng</option>
          </select>
        </label>

        <label class="toolbar-select">
          <span>Sắp xếp</span>
          <select v-model="boLoc.sap_xep">
            <option value="moi_nhat">Mới cập nhật trước</option>
            <option value="cu_nhat">Cũ cập nhật trước</option>
            <option value="ten_a_z">Tên A - Z</option>
            <option value="ten_z_a">Tên Z - A</option>
          </select>
        </label>

        <button class="search-btn" type="button" :disabled="dangTai" @click="taiDanhSachDiaDiem(1)">
          Lọc dữ liệu
        </button>
      </div>
    </section>

    <section v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </section>

    <section class="content-grid">
      <section class="table-card">
        <div v-if="dangTai" class="empty-box">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang tải danh sách địa điểm đối tác...</span>
        </div>

        <div v-else-if="!danhSachDiaDiemHienThi.length" class="empty-box">
          <i class="fas fa-map-location-dot"></i>
          <span>Không có địa điểm đối tác phù hợp bộ lọc.</span>
        </div>

        <div v-else class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Địa điểm</th>
                <th>Loại</th>
                <th>Mã đối tác</th>
                <th>Trạng thái duyệt</th>
                <th>Trạng thái hiển thị</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="diaDiem in danhSachDiaDiemHienThi"
                :key="diaDiem.id"
                :class="{ 'is-selected': diaDiemDangChon && diaDiemDangChon.id === diaDiem.id }"
                @click="maDiaDiemDangChon = diaDiem.id"
              >
                <td>
                  <div class="location-cell">
                    <img :src="diaDiem.hinhAnh" :alt="diaDiem.ten" @error="loiHinhAnh">
                    <div>
                      <strong>{{ diaDiem.ten }}</strong>
                      <small>ID: {{ diaDiem.id }}</small>
                      <small>{{ diaDiem.diaChi }}</small>
                      <small v-if="diaDiem.lyDoTuChoi" class="reject-reason">Lý do từ chối: {{ diaDiem.lyDoTuChoi }}</small>
                    </div>
                  </div>
                </td>
                <td>{{ tenLoai(diaDiem.loai) }}</td>
                <td>
                  <div class="partner-cell">
                    <strong>{{ diaDiem.maDoiTacTao || "-" }}</strong>
                    <small>{{ diaDiem.tenDoiTac || "Đối tác" }}</small>
                  </div>
                </td>
                <td>
                  <span class="status-chip" :class="`status-chip--${layToneTrangThaiDuyet(diaDiem.trangThaiDuyet)}`">
                    {{ hienThiTrangThaiDuyet(diaDiem.trangThaiDuyet) }}
                  </span>
                </td>
                <td>
                  <span class="display-chip" :class="diaDiem.trangThaiHienThi ? 'display-chip--visible' : 'display-chip--hidden'">
                    {{ diaDiem.trangThaiHienThi ? "Đang hiển thị" : "Đang ẩn" }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="table-footer">
          <span>Hiển thị {{ phamViHienThi.tu }}-{{ phamViHienThi.den }} trên {{ phanTrang.tongMuc }} địa điểm</span>
          <div class="pagination">
            <button type="button" :disabled="phanTrang.trangHienTai <= 1" @click="taiDanhSachDiaDiem(phanTrang.trangHienTai - 1)">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button
              v-for="page in danhSachTrang"
              :key="page"
              type="button"
              :class="{ 'is-active': page === phanTrang.trangHienTai }"
              @click="taiDanhSachDiaDiem(page)"
            >
              {{ page }}
            </button>
            <button type="button" :disabled="phanTrang.trangHienTai >= phanTrang.trangCuoi" @click="taiDanhSachDiaDiem(phanTrang.trangHienTai + 1)">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </section>

      <aside class="quick-card">
        <template v-if="diaDiemDangChon">
          <p class="quick-card__eyebrow">Xem nhanh địa điểm</p>
          <h3>{{ diaDiemDangChon.ten }}</h3>
          <img :src="diaDiemDangChon.hinhAnh" :alt="diaDiemDangChon.ten" @error="loiHinhAnh">

          <div class="quick-row">
            <span>Mã địa điểm</span>
            <strong>{{ diaDiemDangChon.id }}</strong>
          </div>
          <div class="quick-row">
            <span>Mã đối tác</span>
            <strong>{{ diaDiemDangChon.maDoiTacTao || "-" }}</strong>
          </div>
          <div class="quick-row">
            <span>Đối tác</span>
            <strong>{{ diaDiemDangChon.tenDoiTac || "-" }}</strong>
          </div>
          <div class="quick-row">
            <span>Trạng thái duyệt</span>
            <strong>{{ hienThiTrangThaiDuyet(diaDiemDangChon.trangThaiDuyet) }}</strong>
          </div>
          <div class="quick-row">
            <span>Trạng thái hiển thị</span>
            <strong>{{ diaDiemDangChon.trangThaiHienThi ? "Đang hiển thị" : "Đang ẩn" }}</strong>
          </div>
          <div class="quick-row">
            <span>Lý do từ chối</span>
            <strong>{{ diaDiemDangChon.lyDoTuChoi || "-" }}</strong>
          </div>
          <div class="quick-row">
            <span>Địa chỉ</span>
            <strong>{{ diaDiemDangChon.diaChi }}</strong>
          </div>

          <div class="quick-actions">
            <button type="button" class="action-btn action-btn--detail" @click="chonVaXemChiTiet(diaDiemDangChon)">
              Xem chi tiết địa điểm
            </button>
            <button
              type="button"
              class="action-btn action-btn--approve"
              :disabled="dangXuLyDuyet === diaDiemDangChon.id || !coTheDuyet(diaDiemDangChon)"
              @click="duyetDiaDiem(diaDiemDangChon)"
            >
              Duyệt địa điểm
            </button>
            <button
              type="button"
              class="action-btn action-btn--reject"
              :disabled="dangXuLyDuyet === diaDiemDangChon.id || !coTheDuyet(diaDiemDangChon)"
              @click="tuChoiDiaDiem(diaDiemDangChon)"
            >
              Từ chối
            </button>
          </div>
        </template>

        <p v-else class="quick-empty">Chọn một địa điểm để xem thông tin chi tiết nhanh.</p>
      </aside>
    </section>

    <div v-if="modal.mo" class="modal-backdrop" @click.self="dongModal">
      <div class="modal-card">
        <header class="modal-card__header">
          <div>
            <p class="modal-card__eyebrow">Chi tiết địa điểm đối tác</p>
            <h3>{{ modalDiaDiem.ten }}</h3>
          </div>
          <button type="button" class="modal-close" @click="dongModal">
            <i class="fas fa-xmark"></i>
          </button>
        </header>

        <section class="modal-grid">
          <article class="modal-item">
            <span>Mã địa điểm</span>
            <strong>{{ modalDiaDiem.id }}</strong>
          </article>
          <article class="modal-item">
            <span>Mã đối tác tạo</span>
            <strong>{{ modalDiaDiem.maDoiTacTao || "-" }}</strong>
          </article>
          <article class="modal-item">
            <span>Đối tác</span>
            <strong>{{ modalDiaDiem.tenDoiTac || "-" }}</strong>
          </article>
          <article class="modal-item">
            <span>Loại</span>
            <strong>{{ tenLoai(modalDiaDiem.loai) }}</strong>
          </article>
          <article class="modal-item">
            <span>Trạng thái duyệt</span>
            <strong>{{ hienThiTrangThaiDuyet(modalDiaDiem.trangThaiDuyet) }}</strong>
          </article>
          <article class="modal-item">
            <span>Trạng thái hiển thị</span>
            <strong>{{ modalDiaDiem.trangThaiHienThi ? "Đang hiển thị" : "Đang ẩn" }}</strong>
          </article>
          <article class="modal-item modal-item--full">
            <span>Địa chỉ</span>
            <strong>{{ modalDiaDiem.diaChi }}</strong>
          </article>
          <article class="modal-item modal-item--full">
            <span>Mô tả</span>
            <strong>{{ modalDiaDiem.moTa || "-" }}</strong>
          </article>
          <article class="modal-item modal-item--full">
            <span>Lý do từ chối</span>
            <strong>{{ modalDiaDiem.lyDoTuChoi || "-" }}</strong>
          </article>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient.js";
import { showAlert, showConfirm } from "../../../services/appDialog";
import { fixImageUrl, HINH_ANH_MAC_DINH } from "../../../services/imageService.js";

const DOI_TAC_DIA_DIEM_API = "/api/admin/doi-tac/dia-diem";

export default {
  name: "AdminQuanLyDiaDiemPage",
  data() {
    return {
      dangTai: false,
      dangXuLyDuyet: "",
      thongBaoLoi: "",
      boLoc: {
        search: "",
        trang_thai_duyet: "",
        loai: "",
        sap_xep: "moi_nhat",
      },
      danhSachDiaDiem: [],
      maDiaDiemDangChon: "",
      phanTrang: {
        trangHienTai: 1,
        moiTrang: 10,
        tongMuc: 0,
        trangCuoi: 1,
      },
      modal: {
        mo: false,
      },
      modalDiaDiem: {},
    };
  },
  computed: {
    diaDiemDangChon() {
      if (!this.danhSachDiaDiem.length) return null;
      return this.danhSachDiaDiemHienThi.find((item) => item.id === this.maDiaDiemDangChon) || this.danhSachDiaDiemHienThi[0];
    },
    danhSachDiaDiemHienThi() {
      const list = [...this.danhSachDiaDiem];
      if (this.boLoc.sap_xep === "cu_nhat") {
        return list.sort((a, b) => a.capNhatLucTimestamp - b.capNhatLucTimestamp);
      }
      if (this.boLoc.sap_xep === "ten_a_z") {
        return list.sort((a, b) => a.ten.localeCompare(b.ten, "vi"));
      }
      if (this.boLoc.sap_xep === "ten_z_a") {
        return list.sort((a, b) => b.ten.localeCompare(a.ten, "vi"));
      }
      return list.sort((a, b) => b.capNhatLucTimestamp - a.capNhatLucTimestamp);
    },
    thongKe() {
      const pending = this.danhSachDiaDiem.filter((item) => item.trangThaiDuyet === "pending_approval").length;
      const approved = this.danhSachDiaDiem.filter((item) => item.trangThaiDuyet === "approved").length;
      const rejected = this.danhSachDiaDiem.filter((item) => item.trangThaiDuyet === "rejected").length;
      return {
        tong: this.phanTrang.tongMuc,
        pending,
        approved,
        rejected,
      };
    },
    danhSachTrang() {
      const pages = [];
      const start = Math.max(1, this.phanTrang.trangHienTai - 1);
      const end = Math.min(this.phanTrang.trangCuoi, start + 2);
      for (let page = start; page <= end; page += 1) {
        pages.push(page);
      }
      return pages;
    },
    phamViHienThi() {
      if (!this.phanTrang.tongMuc) {
        return { tu: 0, den: 0 };
      }
      const tu = (this.phanTrang.trangHienTai - 1) * this.phanTrang.moiTrang + 1;
      const den = Math.min(this.phanTrang.trangHienTai * this.phanTrang.moiTrang, this.phanTrang.tongMuc);
      return { tu, den };
    },
  },
  mounted() {
    this.taiDanhSachDiaDiem(1);
  },
  methods: {
    async goiDuLieu(url, { method = "GET", body = null, params = undefined } = {}) {
      const query = params
        ? `?${new URLSearchParams(
          Object.entries(params).filter(([, value]) => value !== undefined && value !== null && value !== "")
        ).toString()}`
        : "";

      const response = await goiApi(`${url}${query}`, {
        method,
        headers: {
          Accept: "application/json",
          ...(body ? { "Content-Type": "application/json" } : {}),
        },
        body: body ? JSON.stringify(body) : undefined,
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok) {
        const error = new Error(data?.message || "Yêu cầu API thất bại.");
        error.response = { data, status: response.status };
        throw error;
      }

      return data;
    },
    layThongBaoLoi(error, fallback) {
      const data = error?.response?.data;
      if (data?.message) return data.message;
      if (data?.errors) {
        const list = Object.values(data.errors).flat();
        if (list.length) return list.join(" ");
      }
      return error?.message || fallback;
    },
    tenLoai(loai) {
      if (Number(loai) === 1) return "Du lịch";
      if (Number(loai) === 2) return "Khách sạn";
      if (Number(loai) === 3) return "Nhà hàng";
      return "Không xác định";
    },
    hienThiTrangThaiDuyet(status) {
      const key = String(status || "").toLowerCase();
      if (key === "pending_approval") return "Chờ duyệt";
      if (key === "approved") return "Đã duyệt";
      if (key === "rejected") return "Chưa duyệt";
      return "Không xác định";
    },
    layToneTrangThaiDuyet(status) {
      const key = String(status || "").toLowerCase();
      if (key === "pending_approval") return "pending";
      if (key === "approved") return "approved";
      if (key === "rejected") return "rejected";
      return "draft";
    },
    coTheDuyet(diaDiem) {
      return String(diaDiem?.trangThaiDuyet || "").toLowerCase() === "pending_approval";
    },
    chuanHoaDiaDiem(item = {}) {
      const doiTac = item?.doi_tac || item?.doiTac || {};
      const trangThaiDuyet = String(item?.trang_thai_duyet || "pending_approval").toLowerCase();

      return {
        id: String(item?.ma_dia_diem || ""),
        ten: item?.ten_dia_diem || "Địa điểm chưa đặt tên",
        diaChi: item?.dia_chi || "-",
        moTa: item?.mo_ta || "",
        loai: Number(item?.loai || 1),
        hinhAnh: fixImageUrl(item?.hinh_anh || ""),
        maDoiTacTao: item?.ma_doi_tac_tao || "",
        tenDoiTac: doiTac?.ten_doi_tac || "",
        trangThaiDuyet,
        trangThaiHienThi: trangThaiDuyet === "approved",
        lyDoTuChoi: item?.ly_do_tu_choi || "",
        capNhatLucTimestamp: new Date(item?.updated_at || item?.created_at || 0).getTime() || 0,
      };
    },
    async taiDanhSachDiaDiem(page = 1) {
      this.dangTai = true;
      this.thongBaoLoi = "";

      try {
        const payload = await this.goiDuLieu(DOI_TAC_DIA_DIEM_API, {
          params: {
            page,
            per_page: this.phanTrang.moiTrang,
            search: this.boLoc.search || undefined,
            trang_thai_duyet: this.boLoc.trang_thai_duyet || undefined,
            loai: this.boLoc.loai || undefined,
          },
        });

        const pageData = payload?.data || {};
        const records = Array.isArray(pageData?.data) ? pageData.data : [];

        this.danhSachDiaDiem = records.map((item) => this.chuanHoaDiaDiem(item));
        this.phanTrang.trangHienTai = Number(pageData?.current_page || page);
        this.phanTrang.tongMuc = Number(pageData?.total || this.danhSachDiaDiem.length);
        this.phanTrang.trangCuoi = Number(pageData?.last_page || 1);

        if (this.danhSachDiaDiem.length) {
          const selected = this.danhSachDiaDiem.find((item) => item.id === this.maDiaDiemDangChon);
          this.maDiaDiemDangChon = selected ? selected.id : this.danhSachDiaDiem[0].id;
        } else {
          this.maDiaDiemDangChon = "";
        }
      } catch (error) {
        this.danhSachDiaDiem = [];
        this.maDiaDiemDangChon = "";
        this.phanTrang.trangHienTai = 1;
        this.phanTrang.tongMuc = 0;
        this.phanTrang.trangCuoi = 1;
        this.thongBaoLoi = this.layThongBaoLoi(error, "Không thể tải danh sách địa điểm đối tác.");
      } finally {
        this.dangTai = false;
      }
    },
    chonVaXemChiTiet(diaDiem) {
      this.maDiaDiemDangChon = diaDiem.id;
      this.modalDiaDiem = { ...diaDiem };
      this.modal.mo = true;
    },
    dongModal() {
      this.modal.mo = false;
      this.modalDiaDiem = {};
    },
    async duyetDiaDiem(diaDiem) {
      if (!this.coTheDuyet(diaDiem)) return;

      const xacNhan = await showConfirm({
        title: "Xác nhận duyệt địa điểm",
        message: `Bạn có chắc muốn duyệt địa điểm ${diaDiem.ten}?`,
        tone: "success",
        confirmText: "Duyệt địa điểm",
        cancelText: "Hủy",
      });
      if (!xacNhan) return;

      this.dangXuLyDuyet = diaDiem.id;
      try {
        await this.goiDuLieu(`${DOI_TAC_DIA_DIEM_API}/${diaDiem.id}/approve`, { method: "PATCH" });
        await this.taiDanhSachDiaDiem(this.phanTrang.trangHienTai);
        await showAlert({
          title: "Duyệt thành công",
          message: `Địa điểm ${diaDiem.ten} đã được duyệt.`,
          tone: "success",
          confirmText: "Đã hiểu",
        });
      } catch (error) {
        await showAlert({
          title: "Không thể duyệt địa điểm",
          message: this.layThongBaoLoi(error, "Không thể duyệt địa điểm đối tác."),
          tone: "danger",
        });
      } finally {
        this.dangXuLyDuyet = "";
      }
    },
    async tuChoiDiaDiem(diaDiem) {
      if (!this.coTheDuyet(diaDiem)) return;

      const lyDoNhap = window.prompt(
        `Nhập lý do chưa duyệt cho địa điểm "${diaDiem.ten}"`,
        "Địa điểm chưa đủ thông tin theo tiêu chí kiểm duyệt."
      );
      if (lyDoNhap === null) return;

      const lyDo = String(lyDoNhap || "").trim();
      if (lyDo.length < 5) {
        await showAlert({
          title: "Lý do chưa hợp lệ",
          message: "Lý do từ chối phải có ít nhất 5 ký tự.",
          tone: "warning",
        });
        return;
      }

      const xacNhan = await showConfirm({
        title: "Xác nhận từ chối địa điểm",
        message: `Bạn có chắc muốn chuyển địa điểm ${diaDiem.ten} sang trạng thái chưa duyệt?`,
        tone: "warning",
        confirmText: "Từ chối",
        cancelText: "Hủy",
      });
      if (!xacNhan) return;

      this.dangXuLyDuyet = diaDiem.id;
      try {
        await this.goiDuLieu(`${DOI_TAC_DIA_DIEM_API}/${diaDiem.id}/reject`, {
          method: "PATCH",
          body: { ly_do_tu_choi: lyDo },
        });
        await this.taiDanhSachDiaDiem(this.phanTrang.trangHienTai);
        await showAlert({
          title: "Đã cập nhật chưa duyệt",
          message: `Địa điểm ${diaDiem.ten} đã được chuyển sang trạng thái chưa duyệt.`,
          tone: "success",
          confirmText: "Đã hiểu",
        });
      } catch (error) {
        await showAlert({
          title: "Không thể cập nhật trạng thái",
          message: this.layThongBaoLoi(error, "Không thể từ chối địa điểm đối tác."),
          tone: "danger",
        });
      } finally {
        this.dangXuLyDuyet = "";
      }
    },
    loiHinhAnh(event) {
      event.target.src = HINH_ANH_MAC_DINH;
    },
  },
  watch: {
    danhSachDiaDiem: {
      handler(items) {
        if (!items.length) {
          this.maDiaDiemDangChon = "";
          return;
        }
        if (!items.some((item) => item.id === this.maDiaDiemDangChon)) {
          this.maDiaDiemDangChon = items[0].id;
        }
      },
      immediate: true,
    },
    "boLoc.sap_xep"() {
      if (!this.danhSachDiaDiemHienThi.length) {
        this.maDiaDiemDangChon = "";
        return;
      }
      if (!this.danhSachDiaDiemHienThi.some((item) => item.id === this.maDiaDiemDangChon)) {
        this.maDiaDiemDangChon = this.danhSachDiaDiemHienThi[0].id;
      }
    },
  },
};
</script>

<style scoped>
.admin-partner-location-page {
  min-height: 100%;
  padding: 1.5rem;
  background: #f8fafc;
  display: grid;
  gap: 1.5rem;
}

.page-hero {
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

.page-hero__eyebrow {
  margin: 0;
  color: #2563eb;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-size: 0.75rem;
  font-weight: 800;
}

.page-hero h1 {
  margin: 0.5rem 0 0;
  color: #0f172a;
  font-size: 2rem;
  font-weight: 900;
  line-height: 1.2;
}

.page-hero p {
  margin: 0.5rem 0 0;
  color: #64748b;
  font-size: 1rem;
}

.refresh-btn {
  min-height: 2.75rem;
  padding: 0 1rem;
  border-radius: 0.75rem;
  border: 1px solid #bfdbfe;
  background: #eff6ff;
  color: #1d4ed8;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.refresh-btn:hover:not(:disabled) {
  background: #dbeafe;
  transform: translateY(-1px);
}

.refresh-btn:active:not(:disabled) {
  transform: scale(0.97);
}

.refresh-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
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

.summary-card--rejected {
  border-color: #fecaca;
  background: #fff7f7;
}

.toolbar-card,
.table-card,
.quick-card,
.modal-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

.toolbar-card {
  padding: 1rem 1.5rem;
}

.toolbar {
  display: grid;
  grid-template-columns: minmax(280px, 1.5fr) repeat(3, minmax(180px, 1fr)) auto;
  gap: 1rem;
  align-items: end;
}

.toolbar-search {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-height: 2.75rem;
  border: 1px solid #dbeafe;
  border-radius: 999px;
  padding: 0 1rem;
  background: #f8fbff;
  transition: border-color 0.2s;
}

.toolbar-search:focus-within {
  border-color: #2563eb;
  outline: 2px solid rgba(37, 99, 235, 0.2);
}

.toolbar-search input {
  width: 100%;
  border: none;
  background: transparent;
  color: #1e293b;
  font-size: 0.875rem;
}

.toolbar-search input:focus {
  outline: none;
}

.toolbar-search i {
  color: #64748b;
}

.toolbar-select {
  display: grid;
  gap: 0.25rem;
}

.toolbar-select span {
  color: #475569;
  font-size: 0.75rem;
  font-weight: 700;
}

.toolbar-select select {
  min-height: 2.75rem;
  border: 1px solid #dbeafe;
  border-radius: 0.75rem;
  background: #f8fbff;
  color: #1e293b;
  padding: 0 1rem;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
}

.toolbar-select select:focus {
  outline: 2px solid rgba(37, 99, 235, 0.2);
  border-color: #2563eb;
}

.search-btn {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  border: none;
  background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
  color: #ffffff;
  font-weight: 800;
  padding: 0 1.5rem;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.search-btn:hover:not(:disabled) {
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  transform: translateY(-1px);
}

.search-btn:active:not(:disabled) {
  transform: scale(0.95);
}

.search-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.notice {
  border-radius: 1rem;
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

.notice--error {
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #b91c1c;
}

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(320px, 0.8fr);
  gap: 1.5rem;
}

.table-wrap {
  overflow: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th,
.data-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  text-align: left;
}

.data-table th {
  color: #475569;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  background: #f8fafc;
}

.data-table tbody tr {
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.data-table tbody tr:hover {
  background: #f8fafc;
}

.data-table tbody tr.is-selected {
  background: #eff6ff;
}

.location-cell {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.location-cell img {
  width: 72px;
  height: 56px;
  border-radius: 0.75rem;
  object-fit: cover;
  box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.05);
}

.location-cell strong,
.partner-cell strong {
  display: block;
  color: #0f172a;
  font-size: 0.95rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.location-cell small,
.partner-cell small {
  display: block;
  color: #64748b;
  font-size: 0.8rem;
}

.reject-reason {
  margin-top: 0.25rem;
  color: #ef4444 !important;
  font-weight: 700;
}

.status-chip,
.display-chip {
  min-height: 2rem;
  border-radius: 999px;
  padding: 0 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 800;
}

.status-chip--pending {
  color: #1d4ed8;
  background: #dbeafe;
}

.status-chip--approved {
  color: #166534;
  background: #dcfce7;
}

.status-chip--rejected {
  color: #b91c1c;
  background: #fee2e2;
}

.status-chip--draft {
  color: #475569;
  background: #f1f5f9;
}

.display-chip--visible {
  color: #166534;
  background: #dcfce7;
}

.display-chip--hidden {
  color: #475569;
  background: #f1f5f9;
}

.empty-box {
  min-height: 240px;
  display: grid;
  place-items: center;
  color: #64748b;
  gap: 0.5rem;
  text-align: center;
  padding: 2rem;
}

.table-footer {
  padding: 1.25rem 1.5rem;
  border-top: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  color: #64748b;
  font-size: 0.875rem;
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
  background: #ffffff;
  color: #334155;
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
  background: #1d4ed8;
  border-color: #1d4ed8;
  color: #ffffff;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quick-card {
  padding: 1.5rem;
  display: grid;
  gap: 0.75rem;
  align-self: start;
  position: sticky;
  top: 1.5rem;
}

.quick-card__eyebrow {
  margin: 0;
  color: #2563eb;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-size: 0.75rem;
  font-weight: 800;
}

.quick-card h3 {
  margin: 0 0 0.5rem 0;
  color: #0f172a;
  font-size: 1.25rem;
  line-height: 1.3;
}

.quick-card img {
  width: 100%;
  height: 200px;
  border-radius: 0.75rem;
  object-fit: cover;
  margin-bottom: 0.5rem;
}

.quick-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 0.75rem;
  margin-bottom: 0.25rem;
}

.quick-row span {
  color: #64748b;
  font-size: 0.875rem;
}

.quick-row strong {
  text-align: right;
  color: #0f172a;
  font-size: 0.95rem;
}

.quick-actions {
  margin-top: 0.75rem;
  display: grid;
  gap: 0.5rem;
}

.action-btn {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  border: 1px solid transparent;
  font-size: 0.875rem;
  font-weight: 800;
  padding: 0 1rem;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.action-btn--detail {
  color: #1d4ed8;
  border-color: #bfdbfe;
  background: #eff6ff;
}

.action-btn--detail:hover:not(:disabled) {
  background: #dbeafe;
}

.action-btn--approve {
  color: #166534;
  border-color: #86efac;
  background: #f0fdf4;
}

.action-btn--approve:hover:not(:disabled) {
  background: #dcfce7;
}

.action-btn--reject {
  color: #b91c1c;
  border-color: #fca5a5;
  background: #fef2f2;
}

.action-btn--reject:hover:not(:disabled) {
  background: #fee2e2;
}

.action-btn:active:not(:disabled) {
  transform: scale(0.97);
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quick-empty {
  margin: 0;
  min-height: 240px;
  display: grid;
  place-items: center;
  color: #64748b;
  text-align: center;
  padding: 2rem;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  z-index: 100;
  display: grid;
  place-items: center;
  padding: 1.5rem;
  backdrop-filter: blur(4px);
}

.modal-card {
  width: min(800px, 100%);
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  border: none;
}

.modal-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.modal-card__eyebrow {
  margin: 0;
  color: #2563eb;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-weight: 800;
}

.modal-card__header h3 {
  margin: 0.5rem 0 0;
  color: #0f172a;
  font-size: 1.5rem;
}

.modal-close {
  width: 2.5rem;
  height: 2.5rem;
  border: none;
  border-radius: 0.5rem;
  background: #e2e8f0;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s;
  display: grid;
  place-items: center;
}

.modal-close:hover {
  background: #cbd5e1;
  color: #0f172a;
}

.modal-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
  padding: 1.5rem;
  max-height: 70vh;
  overflow-y: auto;
}

.modal-item {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  background: #f8fafc;
  padding: 1rem;
  display: grid;
  gap: 0.5rem;
}

.modal-item--full {
  grid-column: 1 / -1;
}

.modal-item span {
  color: #64748b;
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.modal-item strong {
  color: #0f172a;
  font-size: 1rem;
  line-height: 1.5;
}

@media (max-width: 1200px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .quick-card {
    position: static;
  }
}

@media (max-width: 900px) {
  .summary-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .toolbar {
    grid-template-columns: 1fr;
  }

  .page-hero {
    flex-direction: column;
    align-items: stretch;
  }

  .table-footer {
    flex-direction: column;
    align-items: stretch;
    text-align: center;
  }

  .pagination {
    justify-content: center;
  }
}

@media (max-width: 680px) {
  .summary-grid,
  .modal-grid {
    grid-template-columns: 1fr;
  }
  .admin-partner-location-page {
    padding: 1rem;
  }
}
</style>
