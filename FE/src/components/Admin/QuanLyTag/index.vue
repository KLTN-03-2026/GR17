<template>
  <div class="admin-tag-page">
    <section class="tag-hero">
      <div class="tag-hero__intro">
        <h1>Quản lý Tag</h1>
        <p class="tag-hero__subtitle">
          Quản lý và tổ chức các danh mục gắn thẻ cho chuyến đi.
        </p>
      </div>

      <button class="tag-create-button" type="button" @click="moModalTaoTag">
        <i class="fas fa-plus"></i>
        <span>Thêm tag mới</span>
      </button>
    </section>

    <section v-if="thong_bao.text" class="alert-banner" :class="`alert-banner--${thong_bao.type}`">
      <i :class="thong_bao.type === 'success' ? 'fas fa-circle-check' : 'fas fa-circle-info'"></i>
      <span>{{ thong_bao.text }}</span>
    </section>

    <section class="tag-stat-grid">
      <article class="tag-stat-card">
        <div>
          <p class="tag-stat-card__label">Tổng số tag</p>
          <strong>{{ tongSoTag }}</strong>
        </div>
        <div class="tag-stat-card__icon tag-stat-card__icon--blue">
          <i class="fas fa-tag"></i>
        </div>
      </article>

      <article class="tag-stat-card">
        <div>
          <p class="tag-stat-card__label">Tag nổi bật</p>
          <strong>{{ tagPhoBienNhat.ten }}</strong>
        </div>
        <div class="tag-stat-card__icon tag-stat-card__icon--violet">
          <i class="fas fa-arrow-trend-up"></i>
        </div>
      </article>

      <article class="tag-stat-card">
        <div>
          <p class="tag-stat-card__label">Mới thêm (30 ngày)</p>
          <strong>+{{ moiThemTrong30Ngay }}</strong>
        </div>
        <div class="tag-stat-card__icon tag-stat-card__icon--sky">
          <i class="fas fa-clock-rotate-left"></i>
        </div>
      </article>
    </section>

    <section class="tag-main-grid">
      <section class="tag-table-card">
      <div class="tag-table-card__header">
        <div>
          <h2>Danh sách chi tiết</h2>
          <p>Theo dõi, chỉnh sửa và sắp xếp tag đang hoạt động trong hệ thống.</p>
        </div>

        <div class="tag-table-card__actions">
          <button type="button" class="icon-button" @click="sapXepTheoTen">
            <i class="fas fa-filter"></i>
          </button>
          <button type="button" class="icon-button" @click="taiLaiDanhSach">
            <i class="fas fa-rotate-right"></i>
          </button>
        </div>
      </div>

      <div v-if="danh_sach_loi.length" class="table-message table-message--error">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ danh_sach_loi.join(" ") }}</span>
      </div>

      <div v-else-if="dang_tai_danh_sach" class="table-message">
        <i class="fas fa-spinner fa-spin"></i>
        <span>Đang tải danh sách tag...</span>
      </div>

      <div v-else-if="!tongSoTag" class="table-message">
        <i class="fas fa-tag"></i>
        <span>Chưa có tag nào trong hệ thống.</span>
      </div>

      <div v-else class="tag-table-wrapper">
        <table class="tag-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên tag</th>
              <th>Mô tả</th>
              <th>Liên kết</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="tag in danhSachTagPhanTrang"
              :key="tag.id"
              :class="{ 'is-selected': tagDangChon && tagDangChon.id === tag.id }"
              @click="ma_tag_dang_chon = tag.id"
            >
              <td>
                <span class="tag-id-chip">#{{ tag.id }}</span>
              </td>
              <td>
                <div class="tag-name-cell">
                  <span class="tag-dot" :style="{ backgroundColor: tag.color }"></span>
                  <strong>{{ tag.ten }}</strong>
                </div>
              </td>
              <td>{{ tag.moTa }}</td>
              <td>{{ tag.luotDung }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="tag-table-card__footer">
        <span>Hiển thị {{ danhSachTagPhanTrang.length }} trên tổng số {{ tongSoTag }} tags</span>

        <div class="tag-pagination">
          <button
            type="button"
            class="page-nav"
            :disabled="trangHienTai === 1"
            @click="denTrang(trangHienTai - 1)"
          >
            <i class="fas fa-chevron-left"></i>
          </button>

          <button
            v-for="page in tongSoTrang"
            :key="page"
            type="button"
            class="page-number"
            :class="{ 'is-active': page === trangHienTai }"
            @click="denTrang(page)"
          >
            {{ page }}
          </button>

          <button
            type="button"
            class="page-nav"
            :disabled="trangHienTai === tongSoTrang"
            @click="denTrang(trangHienTai + 1)"
          >
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
      </section>

      <aside class="tag-quick-card">
        <template v-if="tagDangChon">
          <p class="tag-quick-card__eyebrow">Xem nhanh tag</p>
          <h3>{{ tagDangChon.ten }}</h3>

          <div class="tag-quick-card__meta">
            <span class="tag-dot" :style="{ backgroundColor: tagDangChon.color }"></span>
            <span>#{{ tagDangChon.id }}</span>
          </div>

          <div class="tag-quick-card__row">
            <span>Mô tả</span>
            <strong>{{ tagDangChon.moTa }}</strong>
          </div>
          <div class="tag-quick-card__row">
            <span>Số liên kết</span>
            <strong>{{ tagDangChon.luotDung }}</strong>
          </div>
          <div class="tag-quick-card__row">
            <span>Ngày tạo</span>
            <strong>{{ tagDangChon.ngayTao ? new Date(tagDangChon.ngayTao).toLocaleDateString('vi-VN') : 'Đang cập nhật' }}</strong>
          </div>

          <div class="tag-quick-card__actions">
            <button type="button" class="quick-btn quick-btn--primary" @click="xemTag(tagDangChon)">Xem chi tiết</button>
            <button type="button" class="quick-btn quick-btn--secondary" @click="suaTag(tagDangChon)">Chỉnh sửa</button>
            <button type="button" class="quick-btn quick-btn--danger" @click="xoaTag(tagDangChon)">Xóa</button>
          </div>
        </template>
        <p v-else class="tag-quick-card__empty">Chọn một tag để xem nhanh thông tin.</p>
      </aside>
    </section>

    <section class="tag-insight-grid">
      <article class="tag-insight-card tag-insight-card--guide">
        <p class="tag-insight-card__eyebrow">Tối ưu hóa tìm kiếm</p>
        <h3>Sử dụng tag hiệu quả giúp tăng khả năng tìm kiếm tour của khách hàng lên 40%.</h3>
        <button type="button" class="tag-link-button" @click="taiLaiDanhSach">
          Làm mới dữ liệu
          <i class="fas fa-arrow-right"></i>
        </button>
      </article>

      <article class="tag-insight-card tag-insight-card--suggestions">
        <div class="tag-insight-card__top">
          <div>
            <p class="tag-insight-card__eyebrow">Gợi ý tag mới</p>
            <h3>Dựa trên xu hướng tìm kiếm tuần này.</h3>
          </div>
          <span class="ai-badge">AI hỗ trợ</span>
        </div>

        <div class="suggestion-list">
          <button
            v-for="suggestion in goiYTags"
            :key="suggestion"
            type="button"
            class="suggestion-chip"
            @click="chonGoiY(suggestion)"
          >
            {{ suggestion }}
          </button>
        </div>
      </article>
    </section>

    <div v-if="modal.mo" class="tag-modal-backdrop" @click.self="dongModal">
      <div class="tag-modal">
        <div class="tag-modal__header">
          <div>
            <p class="tag-modal__eyebrow">{{ modal.mode === "view" ? "Chi tiết tag" : tieuDeModal }}</p>
            <h3>{{ tieuDeChinhModal }}</h3>
          </div>
          <button type="button" class="tag-modal__close" @click="dongModal">
            <i class="fas fa-xmark"></i>
          </button>
        </div>

        <div v-if="modal.mode === 'view'" class="tag-modal__content tag-modal__content--detail">
          <div class="tag-detail-item">
            <span>Mã tag</span>
            <strong>{{ formTag.ma_tag || "-" }}</strong>
          </div>
          <div class="tag-detail-item">
            <span>Tên tag</span>
            <strong>{{ formTag.ten_tag || "-" }}</strong>
          </div>
          <div class="tag-detail-item">
            <span>Mô tả</span>
            <p>{{ formTag.mo_ta || "Chưa có mô tả cho tag này." }}</p>
          </div>
          <div class="tag-detail-item">
            <span>Số liên kết</span>
            <strong>{{ formTag.luot_dung ?? 0 }}</strong>
          </div>
        </div>

        <form v-else class="tag-modal__content" @submit.prevent="luuTag">
          <label class="modal-field">
            <span>Mã tag</span>
            <input
              v-model="formTag.ma_tag"
              type="text"
              placeholder="Ví dụ: 006"
              :disabled="modal.mode === 'edit' || dang_luu_tag"
            >
          </label>

          <label class="modal-field">
            <span>Tên tag</span>
            <input
              v-model="formTag.ten_tag"
              type="text"
              placeholder="Nhập tên tag"
              :disabled="dang_luu_tag"
            >
          </label>

          <label class="modal-field modal-field--full">
            <span>Mô tả</span>
            <textarea
              v-model="formTag.mo_ta"
              rows="4"
              placeholder="Nhập mô tả ngắn cho tag"
              :disabled="dang_luu_tag"
            ></textarea>
          </label>

          <div v-if="danh_sach_loi_form.length" class="form-note form-note--error">
            <i class="fas fa-circle-exclamation"></i>
            <span>{{ danh_sach_loi_form.join(" ") }}</span>
          </div>

          <div class="tag-modal__actions">
            <button type="button" class="modal-button modal-button--ghost" @click="dongModal">
              Hủy
            </button>
            <button type="submit" class="modal-button modal-button--primary" :disabled="dang_luu_tag">
              {{ dang_luu_tag ? "Đang lưu..." : "Lưu tag" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient.js";
import { showConfirm } from "../../../services/appDialog";

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "AdminQuanLyTagPage",
  data() {
    return {
      token: "",
      trangHienTai: 1,
      soLuongMoiTrang: 5,
      dang_tai_danh_sach: false,
      dang_tai_chi_tiet: false,
      dang_luu_tag: false,
      dang_xoa_tag: "",
      sap_xep_az: false,
      danhSachTag: [],
      ma_tag_dang_chon: "",
      thong_bao: {
        type: "success",
        text: "",
      },
      danh_sach_loi: [],
      danh_sach_loi_form: [],
      modal: {
        mo: false,
        mode: "create",
      },
      formTag: {
        ma_tag: "",
        ten_tag: "",
        mo_ta: "",
        luot_dung: 0,
      },
      goiYTags: ["#DuLichBenVung", "#GiaDinh", "#SanMay", "#TamLinh"],
    };
  },
  computed: {
    tongSoTag() {
      return this.danhSachTag.length;
    },
    tagPhoBienNhat() {
      return [...this.danhSachTag].sort((a, b) => b.luotDung - a.luotDung)[0] || { ten: "-" };
    },
    moiThemTrong30Ngay() {
      const today = new Date();
      return this.danhSachTag.filter((tag) => {
        if (!tag.ngayTao) return false;
        const diff = today.getTime() - new Date(tag.ngayTao).getTime();
        return diff >= 0 && diff <= 30 * 24 * 60 * 60 * 1000;
      }).length;
    },
    tongSoTrang() {
      return Math.max(1, Math.ceil(this.danhSachTag.length / this.soLuongMoiTrang));
    },
    danhSachTagPhanTrang() {
      const start = (this.trangHienTai - 1) * this.soLuongMoiTrang;
      return this.danhSachTag.slice(start, start + this.soLuongMoiTrang);
    },
    tagDangChon() {
      return this.danhSachTag.find((tag) => tag.id === this.ma_tag_dang_chon) || this.danhSachTagPhanTrang[0] || null;
    },
    tieuDeModal() {
      return this.modal.mode === "edit" ? "Cập nhật tag" : "Tạo tag mới";
    },
    tieuDeChinhModal() {
      if (this.modal.mode === "view") return this.formTag.ten_tag || "Thông tin chi tiết";
      return this.modal.mode === "edit" ? "Chỉnh sửa thông tin tag" : "Thêm tag mới vào hệ thống";
    },
  },
  mounted() {
    this.khoiTaoDuLieuLocal();
    this.taiDanhSachTag();
  },
  methods: {
    async goiDuLieu(url, { method = "GET", body = null, headers = {}, params = undefined } = {}) {
      const query = params
        ? `?${new URLSearchParams(
            Object.entries(params).filter(([, value]) => value !== undefined && value !== null && value !== ""),
          ).toString()}`
        : "";
      const response = await goiApi(`${url}${query}`, {
        method,
        headers: {
          Accept: "application/json",
          ...(body ? { "Content-Type": "application/json" } : {}),
          ...headers,
        },
        body: body ? JSON.stringify(body) : undefined,
      });
      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        const error = new Error(data?.message || "Yeu cau API that bai.");
        error.response = { data, status: response.status };
        throw error;
      }

      return { data };
    },
    khoiTaoDuLieuLocal() {
      this.token = localStorage.getItem("token") || "";
    },

    taoHeader(extra = {}) {
      return {
        Accept: "application/json",
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
        ...extra,
      };
    },

    xuLyPayload(payload) {
      if (!payload) return [];
      if (Array.isArray(payload)) return payload;
      if (Array.isArray(payload.data)) return payload.data;
      if (Array.isArray(payload.result)) return payload.result;
      if (payload.data && Array.isArray(payload.data.data)) return payload.data.data;
      if (payload.result && Array.isArray(payload.result.data)) return payload.result.data;
      if (payload.data && !Array.isArray(payload.data)) return payload.data;
      if (payload.result && !Array.isArray(payload.result)) return payload.result;
      return payload;
    },

    layThongBaoLoi(error, mac_dinh) {
      const data = error?.response?.data;
      if (data?.message) return data.message;
      if (typeof data === "string") return data;
      if (data?.errors) {
        const list = Object.values(data.errors).flat();
        if (list.length) return list.join(" ");
      }
      return error?.message || mac_dinh;
    },

    thongBao(type, text) {
      toaster.success({ type, text });
    },

    taoMauSac(index = 0) {
      const palette = ["#0c6d9a", "#5a50d5", "#15803d", "#c62828", "#0d719d", "#7c3aed"];
      return palette[index % palette.length];
    },

    chuanHoaTag(tag = {}, index = 0) {
      const id = tag?.ma_tag || tag?.Ma_tag || tag?.id || tag?.Id || tag?.ID || "";
      const ten = tag?.ten_tag || tag?.Ten_tag || tag?.name || `Tag ${index + 1}`;
      const moTa = tag?.mo_ta || tag?.Mo_ta || tag?.moTa || "Chưa có mô tả cho tag này.";
      const luotDung =
        Number(tag?.so_dia_diem ?? tag?.so_luong_dia_diem ?? tag?.tong_dia_diem ?? tag?.luot_dung ?? 0) || 0;
      const ngayTao = tag?.created_at || tag?.ngay_tao || tag?.Ngay_tao || "";

      return {
        raw: tag,
        id,
        ten,
        moTa,
        luotDung,
        ngayTao,
        color: tag?.color || this.taoMauSac(index),
      };
    },

    datFormMacDinh() {
      this.formTag = {
        ma_tag: "",
        ten_tag: "",
        mo_ta: "",
        luot_dung: 0,
      };
      this.danh_sach_loi_form = [];
    },

    ganFormTag(tag = {}) {
      this.formTag = {
        ma_tag: tag?.ma_tag || tag?.Ma_tag || tag?.id || tag?.Id || "",
        ten_tag: tag?.ten_tag || tag?.Ten_tag || tag?.name || "",
        mo_ta: tag?.mo_ta || tag?.Mo_ta || tag?.moTa || "",
        luot_dung:
          Number(tag?.so_dia_diem ?? tag?.so_luong_dia_diem ?? tag?.tong_dia_diem ?? tag?.luot_dung ?? 0) || 0,
      };
    },

    kiemTraFormTag() {
      const errors = [];

      if (!this.formTag.ma_tag.trim()) {
        errors.push("Vui lòng nhập mã tag.");
      }

      if (!this.formTag.ten_tag.trim()) {
        errors.push("Vui lòng nhập tên tag.");
      }

      return errors;
    },

    async taiDanhSachTag() {
      this.dang_tai_danh_sach = true;
      this.danh_sach_loi = [];

      try {
        const res = await this.goiDuLieu("/api/tag", {
          headers: this.taoHeader(),
        });

        const payload = this.xuLyPayload(res.data);
        const list = Array.isArray(payload) ? payload : [payload].filter(Boolean);
        this.danhSachTag = list.map((tag, index) => this.chuanHoaTag(tag, index));
        this.trangHienTai = Math.min(this.trangHienTai, this.tongSoTrang);
      } catch (error) {
        this.danhSachTag = [];
        this.danh_sach_loi = [this.layThongBaoLoi(error, "Không thể tải danh sách tag.")];
      } finally {
        this.dang_tai_danh_sach = false;
      }
    },

    async taiChiTietTag(maTag) {
      this.dang_tai_chi_tiet = true;

      try {
        const res = await this.goiDuLieu(`/api/tag/${maTag}`, {
          headers: this.taoHeader(),
        });

        return this.xuLyPayload(res.data);
      } catch (error) {
        this.thongBao("info", this.layThongBaoLoi(error, "Không thể tải chi tiết tag."));
        toaster.error(this.layThongBaoLoi(error, "Không thể tải chi tiết tag."));
        return null;
      } finally {
        this.dang_tai_chi_tiet = false;
      }
    },

    taiLaiDanhSach() {
      this.taiDanhSachTag();
    },

    denTrang(page) {
      if (page < 1 || page > this.tongSoTrang) return;
      this.trangHienTai = page;
    },

    sapXepTheoTen() {
      this.sap_xep_az = !this.sap_xep_az;
      this.danhSachTag = [...this.danhSachTag].sort((a, b) => {
        const value = a.ten.localeCompare(b.ten, "vi");
        return this.sap_xep_az ? value : -value;
      });
      this.trangHienTai = 1;
    },

    moModalTaoTag() {
      this.modal = {
        mo: true,
        mode: "create",
      };
      this.datFormMacDinh();
      this.thong_bao.text = "";
    },

    dongModal() {
      this.modal.mo = false;
      this.danh_sach_loi_form = [];
    },

    async xemTag(tag) {
      const detail = await this.taiChiTietTag(tag.id);
      if (!detail) return;

      this.modal = {
        mo: true,
        mode: "view",
      };
      this.ganFormTag(detail);
    },

    async suaTag(tag) {
      const detail = await this.taiChiTietTag(tag.id);
      if (!detail) return;

      this.modal = {
        mo: true,
        mode: "edit",
      };
      this.ganFormTag(detail);
      this.danh_sach_loi_form = [];
      this.thong_bao.text = "";
    },

    async luuTag() {
      this.danh_sach_loi_form = this.kiemTraFormTag();
      if (this.danh_sach_loi_form.length) {
        return;
      }

      this.dang_luu_tag = true;

      try {
        const payload = {
          ma_tag: this.formTag.ma_tag.trim(),
          ten_tag: this.formTag.ten_tag.trim(),
          ...(this.formTag.mo_ta.trim() ? { mo_ta: this.formTag.mo_ta.trim() } : {}),
        };

        if (this.modal.mode === "edit") {
          await this.goiDuLieu(`/api/tag/${this.formTag.ma_tag}`, {
            method: "PUT",
            body: payload,
            headers: this.taoHeader(),
          });
          this.thongBao("success", "Tag đã được cập nhật thành công.");
          toaster.success(`Tag "${this.formTag.ten_tag}" đã được cập nhật.`);
        } else {
          await this.goiDuLieu("/api/tag", {
            method: "POST",
            body: payload,
            headers: this.taoHeader(),
          });
          this.thongBao("success", "Tag mới đã được tạo thành công.");
          toaster.success(`Tag "${this.formTag.ten_tag}" đã được tạo.`);
        }

        this.dongModal();
        await this.taiDanhSachTag();
      } catch (error) {
        this.danh_sach_loi_form = [this.layThongBaoLoi(error, "Không thể lưu tag.")];
      } finally {
        this.dang_luu_tag = false;
      }
    },    async xoaTag(tag) {
      const xacNhan = await showConfirm({
        title: "Xác nhận xóa tag",
        message: `Bạn có chắc muốn xóa tag "${tag.ten}" không?`,
        tone: "danger",
        confirmText: "Xóa tag",
        cancelText: "Hủy",
      });
      if (!xacNhan) return;

      this.dang_xoa_tag = tag.id;

      try {
        await this.goiDuLieu(`/api/tag/${tag.id}`, {
          method: "DELETE",
          headers: this.taoHeader(),
        });

        this.thongBao("success", `Đã xóa tag "${tag.ten}".`);
        await this.taiDanhSachTag();
        toaster.success(`Đã xóa tag "${tag.ten}".`);
      } catch (error) {
        this.thongBao("info", this.layThongBaoLoi(error, "Không thể xóa tag."));
        toaster.error(this.layThongBaoLoi(error, "Không thể xóa tag."));
      } finally {
        this.dang_xoa_tag = "";
      }
    },

    chonGoiY(suggestion) {
      this.modal = {
        mo: true,
        mode: "create",
      };
      this.datFormMacDinh();
      this.formTag.ten_tag = suggestion.replace(/^#/, "").replace(/^\+/, "");
    },
  },
  watch: {
    danhSachTagPhanTrang: {
      handler(items) {
        if (!items.length) {
          this.ma_tag_dang_chon = "";
          return;
        }
        if (!items.some((item) => item.id === this.ma_tag_dang_chon)) {
          this.ma_tag_dang_chon = items[0].id;
        }
      },
      immediate: true,
    },
  },
};
</script>

<style scoped>
.admin-tag-page {
  min-height: 100%;
  padding: 1.5rem;
  position: relative;
  color: #113761;
  background:
    radial-gradient(circle at top left, rgba(56, 189, 248, 0.08), transparent 28%),
    linear-gradient(180deg, #f8fbff 0%, #f3f7fc 100%);
}

.admin-tag-page::before {
  display: none;
}

.tag-hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
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

.tag-hero::before,
.tag-hero::after {
  content: "";
  position: absolute;
  border-radius: 999px;
  pointer-events: none;
}

.tag-hero::before {
  width: 240px;
  height: 240px;
  top: -90px;
  right: -70px;
  background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, rgba(56, 189, 248, 0) 72%);
}

.tag-hero::after {
  width: 180px;
  height: 180px;
  left: 40%;
  bottom: -90px;
  background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, rgba(14, 165, 233, 0) 72%);
}

.tag-hero__intro,
.tag-create-button {
  position: relative;
  z-index: 1;
}

.tag-hero h1 {
  margin: 0;
  font-size: clamp(1.95rem, 3vw, 2.65rem);
  font-weight: 900;
  color: #102f56;
  line-height: 1.08;
}

.tag-hero__subtitle {
  margin: 0.6rem 0 0;
  font-size: 0.96rem;
  line-height: 1.6;
  color: #5c7b98;
  max-width: 640px;
}

.tag-create-button {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  min-height: 3rem;
  padding: 0 1.1rem;
  border: none;
  border-radius: 1rem;
  background: linear-gradient(135deg, #0f6e99 0%, #116f97 100%);
  color: #ffffff;
  font-size: 0.94rem;
  font-weight: 800;
  box-shadow: 0 14px 28px rgba(16, 111, 152, 0.22);
}

.tag-create-button i {
  width: 1.75rem;
  height: 1.75rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.22);
}

.alert-banner {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.9rem;
  padding: 0.75rem 0.9rem;
  border-radius: 0.9rem;
  font-weight: 700;
  font-size: 0.9rem;
}

.alert-banner--success {
  background: rgba(16, 185, 129, 0.12);
  color: #047857;
  border: 1px solid rgba(16, 185, 129, 0.16);
}

.alert-banner--info {
  background: rgba(37, 99, 235, 0.1);
  color: #1d4ed8;
  border: 1px solid rgba(37, 99, 235, 0.14);
}

.tag-stat-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
  margin-bottom: 1.1rem;
}

.tag-main-grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(320px, 0.95fr);
  gap: 1rem;
  align-items: start;
}

.tag-stat-card {
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.2rem 1.3rem;
  border-radius: 1.4rem;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(33, 85, 132, 0.08);
  box-shadow: 0 18px 36px rgba(17, 62, 105, 0.08);
}

.tag-stat-card::after {
  content: "";
  position: absolute;
  width: 110px;
  height: 110px;
  top: -25px;
  right: -25px;
  border-radius: 50%;
  background: rgba(15, 107, 146, 0.06);
}

.tag-stat-card__label {
  margin: 0 0 0.4rem;
  color: #1e5487;
  font-size: 0.82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.tag-stat-card strong {
  font-size: clamp(1.6rem, 2.4vw, 2.1rem);
  line-height: 1;
  color: #0d2d55;
}

.tag-stat-card__icon {
  width: 4rem;
  height: 4rem;
  display: grid;
  place-items: center;
  border-radius: 1rem;
  font-size: 1.35rem;
  flex-shrink: 0;
}

.tag-stat-card__icon--blue { background: #d9efff; color: #0f6b92; }
.tag-stat-card__icon--violet { background: #eaf4ff; color: #1d4ed8; }
.tag-stat-card__icon--sky { background: #eef6ff; color: #0f6ba4; }

.tag-table-card,
.tag-insight-card,
.tag-modal {
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(33, 85, 132, 0.08);
  box-shadow: 0 18px 36px rgba(17, 62, 105, 0.08);
  backdrop-filter: blur(14px);
}

.tag-table-card {
  border-radius: 1.6rem;
  overflow: hidden;
}

.tag-table-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.2rem 1.35rem 1rem;
}

.tag-table-card__header h2 {
  margin: 0;
  font-size: 1.45rem;
  font-weight: 900;
  color: #0d2d55;
}

.tag-table-card__header p {
  margin: 0.25rem 0 0;
  color: #53769c;
  font-size: 0.9rem;
}

.tag-table-card__actions {
  display: flex;
  align-items: center;
  gap: 0.55rem;
}

.icon-button {
  width: 2.4rem;
  height: 2.4rem;
  border: none;
  border-radius: 0.85rem;
  background: #f4f8ff;
  color: #125f8b;
}

.table-message {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0 1.35rem 0.95rem;
  color: #285b85;
  font-weight: 700;
  font-size: 0.9rem;
}

.table-message--error {
  color: #b42318;
}

.tag-table-wrapper {
  overflow-x: auto;
}

.tag-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 760px;
}

.tag-table thead th {
  padding: 0.9rem 1.35rem;
  color: #355d88;
  font-size: 0.82rem;
  font-weight: 900;
  text-align: left;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  border-top: 1px solid rgba(33, 85, 132, 0.08);
  border-bottom: 1px solid rgba(33, 85, 132, 0.08);
}

.tag-table thead th:last-child,
.tag-table tbody td:last-child {
  width: 168px;
  text-align: center;
}

.tag-table tbody td {
  padding: 1rem 1.35rem;
  border-top: 1px solid rgba(33, 85, 132, 0.08);
  color: #17385f;
  font-size: 0.94rem;
  vertical-align: middle;
}

.tag-table tbody tr {
  cursor: pointer;
}

.tag-table tbody tr.is-selected {
  background: linear-gradient(90deg, rgba(15, 107, 146, 0.08), rgba(15, 107, 146, 0.02));
}

.tag-table__actions-cell {
  padding-left: 1rem;
  padding-right: 1rem;
}

.tag-id-chip {
  display: inline-flex;
  align-items: center;
  padding: 0.32rem 0.55rem;
  border-radius: 0.45rem;
  background: #f1f8ff;
  color: #116799;
  font-size: 0.8rem;
  font-weight: 800;
}

.tag-name-cell {
  display: flex;
  align-items: center;
  gap: 0.7rem;
}

.tag-name-cell strong {
  color: #112f58;
  font-size: 0.98rem;
}

.tag-dot {
  width: 0.6rem;
  height: 0.6rem;
  border-radius: 50%;
  flex-shrink: 0;
}

.tag-row-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  width: 100%;
}

.tag-icon-action {
  width: 2.5rem;
  height: 2.5rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 0.9rem;
  background: #f4f8ff;
  color: #0f6b92;
  flex: 0 0 auto;
}

.tag-icon-action--danger {
  color: #b91c1c;
}

.tag-icon-action:hover {
  background: #eaf2ff;
}

.tag-icon-action--danger:hover {
  background: #fff1f2;
}

.tag-icon-action:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.tag-table-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.95rem 1.1rem 0.9rem;
  border-top: 1px solid #d7e6f7;
  color: #325f88;
  font-size: 0.9rem;
}

.tag-pagination {
  display: flex;
  align-items: center;
  gap: 0.45rem;
}

.tag-quick-card {
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

.tag-quick-card__eyebrow {
  margin: 0;
  color: #2f6f95;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.tag-quick-card h3 {
  margin: 0;
  color: #0d2d55;
  font-size: 1.4rem;
  font-weight: 900;
}

.tag-quick-card__meta {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  font-weight: 800;
  color: #24527e;
}

.tag-quick-card__row {
  display: grid;
  gap: 0.35rem;
  padding-bottom: 0.6rem;
  border-bottom: 1px solid rgba(33, 85, 132, 0.08);
}

.tag-quick-card__row span {
  color: #5f7d9b;
}

.tag-quick-card__row strong {
  color: #17385f;
  font-size: 0.95rem;
}

.tag-quick-card__actions {
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

.tag-quick-card__empty {
  margin: 0;
  color: #6783a3;
  min-height: 220px;
  display: grid;
  place-items: center;
  text-align: center;
}

.page-nav,
.page-number {
  width: 2.15rem;
  height: 2.15rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  border-radius: 0.65rem;
  border: 1px solid #d4e2f3;
  background: #ffffff;
  color: #285b85;
  font-weight: 700;
  font-size: 0.85rem;
  font-variant-numeric: tabular-nums;
}

.page-number.is-active {
  background: #0f6b92;
  border-color: #0f6b92;
  color: #ffffff;
}

.page-nav:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.tag-pagination i {
  font-size: 0.8rem;
  line-height: 1;
}

.tag-insight-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-top: 1.1rem;
}

.tag-insight-card {
  min-height: 180px;
  padding: 1.35rem;
  border-radius: 1.5rem;
}

.tag-insight-card--guide {
  background:
    radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.12), transparent 22%),
    rgba(255, 255, 255, 0.96);
}

.tag-insight-card--suggestions {
  background:
    radial-gradient(circle at top right, rgba(14, 165, 233, 0.1), transparent 24%),
    rgba(255, 255, 255, 0.96);
}

.tag-insight-card__eyebrow {
  margin: 0 0 0.5rem;
  color: #0f6d9c;
  font-size: 0.84rem;
  font-weight: 800;
}

.tag-insight-card h3 {
  margin: 0;
  max-width: 440px;
  color: #12355e;
  font-size: 1rem;
  line-height: 1.5;
}

.tag-link-button {
  margin-top: 1.2rem;
  padding: 0;
  border: none;
  background: transparent;
  color: #0d6d9b;
  font-size: 0.92rem;
  font-weight: 800;
}

.tag-insight-card__top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.ai-badge {
  display: inline-flex;
  align-items: center;
  min-height: 1.7rem;
  padding: 0 0.65rem;
  border-radius: 999px;
  background: #eaf4ff;
  color: #0f6b92;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
}

.suggestion-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  margin-top: 1.25rem;
}

.suggestion-chip {
  min-height: 2.25rem;
  padding: 0 0.85rem;
  border-radius: 999px;
  border: 1px solid #cfddf0;
  background: #ffffff;
  color: #234f7b;
  font-size: 0.9rem;
  font-weight: 700;
}

.tag-modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: grid;
  place-items: center;
  padding: 1rem;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(3px);
}

.tag-modal {
  width: min(100%, 560px);
  border-radius: 1.5rem;
  overflow: hidden;
}

.tag-modal__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.1rem 1.1rem 0.8rem;
  border-bottom: 1px solid #dbe7f5;
}

.tag-modal__eyebrow {
  margin: 0 0 0.35rem;
  color: #0f6d9c;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.tag-modal__header h3 {
  margin: 0;
  color: #0d2d55;
  font-size: 1.2rem;
  font-weight: 900;
}

.tag-modal__close {
  width: 2.1rem;
  height: 2.1rem;
  border: none;
  border-radius: 0.7rem;
  background: #eef5ff;
  color: #124f78;
}

.tag-modal__content {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.85rem;
  padding: 1.1rem;
}

.tag-modal__content--detail {
  grid-template-columns: 1fr;
}

.modal-field {
  display: grid;
  gap: 0.4rem;
}

.modal-field--full,
.form-note,
.tag-modal__actions {
  grid-column: 1 / -1;
}

.modal-field span,
.tag-detail-item span {
  color: #18466d;
  font-size: 0.84rem;
  font-weight: 800;
}

.modal-field input,
.modal-field textarea {
  width: 100%;
  border: 1px solid #d6e5f7;
  border-radius: 0.75rem;
  background: #ffffff;
  color: #17385f;
  font-size: 0.94rem;
  padding: 0.72rem 0.85rem;
}

.modal-field input:focus,
.modal-field textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
}

.tag-detail-item {
  display: grid;
  gap: 0.35rem;
  padding: 0.85rem 0.9rem;
  border-radius: 0.85rem;
  background: #f6fbff;
  border: 1px solid #dbe7f5;
}

.tag-detail-item strong,
.tag-detail-item p {
  margin: 0;
  color: #14345e;
  font-size: 0.94rem;
}

.form-note {
  display: flex;
  align-items: flex-start;
  gap: 0.65rem;
  padding: 0.75rem 0.85rem;
  border-radius: 0.85rem;
  font-size: 0.84rem;
}

.form-note--error {
  background: rgba(239, 68, 68, 0.08);
  color: #c2410c;
  border: 1px solid rgba(239, 68, 68, 0.14);
}

.tag-modal__actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.65rem;
}

.modal-button {
  min-width: 6.25rem;
  min-height: 2.65rem;
  padding: 0 0.95rem;
  border-radius: 0.75rem;
  font-weight: 800;
  font-size: 0.88rem;
}

.modal-button--ghost {
  border: 1px solid #cfe0f3;
  background: #ffffff;
  color: #255782;
}

.modal-button--primary {
  border: none;
  background: linear-gradient(135deg, #0f6e99 0%, #116f97 100%);
  color: #ffffff;
}

@media (max-width: 1200px) {
  .tag-main-grid,
  .tag-stat-grid,
  .tag-insight-grid {
    grid-template-columns: 1fr;
  }

  .tag-quick-card {
    position: static;
  }
}

@media (max-width: 992px) {
  .tag-hero,
  .tag-table-card__footer {
    flex-direction: column;
    align-items: stretch;
  }

  .tag-create-button {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .admin-tag-page {
    padding: 1rem;
  }

  .tag-hero {
    padding: 1.35rem;
  }

  .tag-stat-grid {
    gap: 0.85rem;
  }

  .tag-stat-card,
  .tag-table-card__header,
  .tag-insight-card {
    padding: 1.05rem;
  }

  .tag-table thead th,
  .tag-table tbody td {
    padding: 0.85rem 1rem;
  }

  .tag-pagination,
  .tag-row-actions,
  .tag-modal__actions,
  .tag-modal__content {
    flex-wrap: wrap;
    grid-template-columns: 1fr;
  }
}
</style>




