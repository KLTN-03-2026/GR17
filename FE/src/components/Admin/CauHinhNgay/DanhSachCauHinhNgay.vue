<template>
  <div class="config-page">
    <section class="config-page__hero">
      <div>
        <p class="config-page__eyebrow">Quản trị cấu hình ngày</p>
        <h1>Danh sách cấu hình ngày</h1>
        <p class="config-page__subtitle">
          Theo dõi các ngày lễ, ngày kỷ niệm và mốc đặc biệt đang được cấu hình trong hệ thống quản trị.
        </p>
      </div>

      <div class="config-page__hero-actions">
        <button class="ghost-button" type="button" @click="taiDanhSachCauHinh" :disabled="dangTai">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dangTai ? "Đang tải..." : "Tải lại dữ liệu" }}</span>
        </button>

        <router-link class="primary-link" to="/admin/day-config/create">
          <i class="fas fa-plus"></i>
          <span>Tạo cấu hình</span>
        </router-link>
      </div>
    </section>

    <section class="stats-grid">
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--violet">
          <i class="fas fa-calendar-days"></i>
        </div>
        <div>
          <span>Tổng cấu hình ngày</span>
          <strong>{{ danhSachCauHinh.length.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--blue">
          <i class="fas fa-flag"></i>
        </div>
        <div>
          <span>Ngày lễ chính thức</span>
          <strong>{{ soNgayLeChinhThuc.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--amber">
          <i class="fas fa-cake-candles"></i>
        </div>
        <div>
          <span>Ngày kỷ niệm</span>
          <strong>{{ soNgayKyNiem.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--green">
          <i class="fas fa-sparkles"></i>
        </div>
        <div>
          <span>Ngày đặc biệt</span>
          <strong>{{ soNgayDacBiet.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--sky">
          <i class="fas fa-clock"></i>
        </div>
        <div>
          <span>Mốc gần nhất</span>
          <strong>{{ nhanMocGanNhat }}</strong>
        </div>
      </article>
    </section>

    <section class="filter-bar">
      <label class="filter-search">
        <i class="fas fa-search"></i>
        <input
          v-model.trim="search"
          type="text"
          placeholder="Tìm theo mã cấu hình, tên ngày hoặc ngày tháng..."
        >
      </label>

      <select v-model="boLocLoai" class="filter-select">
        <option value="all">Loại ngày: Tất cả</option>
        <option v-for="option in tuyChonLoai" :key="option.value" :value="String(option.value)">
          {{ option.label }}
        </option>
      </select>

      <button class="filter-apply" type="button" @click="apDungBoLoc">
        Áp dụng bộ lọc
      </button>
    </section>

    <div v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </div>

    <div v-if="thongBaoHanhDong.text" :class="['notice', thongBaoHanhDong.type === 'error' ? 'notice--error' : 'notice--success']">
      <i :class="thongBaoHanhDong.type === 'error' ? 'fas fa-circle-xmark' : 'fas fa-circle-check'"></i>
      <span>{{ thongBaoHanhDong.text }}</span>
    </div>

    <section v-if="dangTai" class="loading-shell">
      <div class="loading-card"></div>
      <div class="loading-card"></div>
    </section>

    <section v-else-if="danhSachCauHinh.length" class="content-grid">
      <article class="table-card">
        <div class="table-head">
          <div>
            <h2>Danh mục cấu hình</h2>
            <p>{{ danhSachCauHinhDaLoc.length }} cấu hình phù hợp điều kiện hiện tại.</p>
          </div>
          <span class="table-chip">{{ dangTai ? "Đồng bộ..." : "Dữ liệu trực tiếp" }}</span>
        </div>

        <div class="config-table">
          <div class="config-table__row config-table__row--head">
            <span>Cấu hình</span>
            <span>Mã CHN</span>
            <span>Loại ngày</span>
            <span>Ngày áp dụng</span>
            <span>Cập nhật</span>
          </div>

          <div
            v-for="config in danhSachCauHinhPhanTrang"
            :key="config.id"
            class="config-table__row"
            :class="{ 'is-selected': cauHinhDangChon && cauHinhDangChon.id === config.id }"
            @click="cauHinhDangChon = config"
          >
            <div class="config-main">
              <strong>{{ config.nameLabel }}</strong>
              <small>{{ config.dateLabel }}</small>
            </div>

            <div class="config-id">
              <span>CHN</span>
              <strong>{{ config.id }}</strong>
            </div>

            <span :class="['pill', layLopNhan(config.typeAccent)]">
              {{ config.typeLabel }}
            </span>

            <span class="muted">{{ config.dateLabel }}</span>

            <span class="muted">{{ config.updatedAtLabel }}</span>

          </div>
        </div>

        <footer class="table-footer">
          <span>
            Hiển thị {{ chiSoHienThiBatDau }}-{{ chiSoHienThiKetThuc }} trên tổng {{ danhSachCauHinhDaLoc.length.toLocaleString() }} cấu hình
          </span>

          <div class="pagination">
            <button class="page-btn" type="button" :disabled="page === 1" @click="page = Math.max(1, page - 1)">
              <i class="fas fa-angle-left"></i>
            </button>
            <button
              v-for="pageNumber in danhSachTrangHienThi"
              :key="pageNumber"
              type="button"
              :class="['page-btn', { 'is-active': page === pageNumber }]"
              @click="page = pageNumber"
            >
              {{ pageNumber }}
            </button>
            <button
              class="page-btn"
              type="button"
              :disabled="page === tongSoTrang"
              @click="page = Math.min(tongSoTrang, page + 1)"
            >
              <i class="fas fa-angle-right"></i>
            </button>
          </div>
        </footer>
      </article>

      <aside class="profile-card">
        <div v-if="cauHinhDangChon">
          <div class="profile-card__header">
            <div class="profile-card__avatar" :class="layLopNhan(cauHinhDangChon.typeAccent)">
              <i class="fas fa-calendar-day"></i>
            </div>
            <div>
              <p class="profile-card__eyebrow">Xem nhanh cấu hình</p>
              <h2>{{ cauHinhDangChon.nameLabel }}</h2>
              <div class="profile-card__tags">
                <span class="tag tag--slate">{{ cauHinhDangChon.code }}</span>
                <span :class="['tag', layLopNhan(cauHinhDangChon.typeAccent, true)]">
                  {{ cauHinhDangChon.typeLabel }}
                </span>
              </div>
            </div>
          </div>

          <div class="profile-card__body">
            <div class="profile-row">
              <span>Ngày áp dụng</span>
              <strong>{{ cauHinhDangChon.dateLabel }}</strong>
            </div>
            <div class="profile-row">
              <span>Loại ngày</span>
              <strong>{{ cauHinhDangChon.typeLabel }}</strong>
            </div>
            <div class="profile-row">
              <span>Tên ngày</span>
              <strong>{{ cauHinhDangChon.nameLabel }}</strong>
            </div>
            <div class="profile-row">
              <span>Ngày tạo</span>
              <strong>{{ cauHinhDangChon.createdAtLabel }}</strong>
            </div>
            <div class="profile-row">
              <span>Cập nhật gần nhất</span>
              <strong>{{ cauHinhDangChon.updatedAtLabel }}</strong>
            </div>
          </div>

          <div class="profile-card__actions">
            <router-link class="profile-link profile-link--primary" :to="`/admin/day-config/${cauHinhDangChon.id}/edit`">
              Chỉnh sửa
            </router-link>
            <router-link class="profile-link profile-link--secondary" to="/admin/day-config/create">
              Tạo mới
            </router-link>
          </div>
        </div>

        <div v-else class="profile-card__empty">
          <i class="fas fa-calendar-xmark"></i>
          <h2>Chưa chọn cấu hình</h2>
          <p>Hãy chọn một dòng trong bảng để xem tóm tắt cấu hình ở khung bên phải.</p>
        </div>
      </aside>
    </section>

    <section v-else class="empty-state">
      <i class="fas fa-calendar-xmark"></i>
      <h2>Chưa có cấu hình ngày nào</h2>
      <p>Hệ thống hiện chưa ghi nhận cấu hình ngày hoặc dữ liệu chưa được đồng bộ.</p>
      <router-link class="primary-link" to="/admin/day-config/create">Tạo cấu hình đầu tiên</router-link>
    </section>
  </div>

</template>

<script>
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
  position: "top-right",
  duration: 3000,
});
import { goiApi } from '../../../services/httpClient.js';
import { API_BASE, DAY_TYPE_OPTIONS, mapDayConfig, normalizeCollection } from "./dayConfigShared";
import { showConfirm } from "../../../services/appDialog";
export default {
  name: "DanhSachCauHinhNgay",
  data() {
    return {
      dangTai: false,
      thongBaoLoi: "",
      thongBaoHanhDong: {
        type: "success",
        text: "",
      },
      maDangXoa: "",
      danhSachCauHinh: [],
      cauHinhDangChon: null,
      search: "",
      boLocLoai: "all",
      tuyChonLoai: DAY_TYPE_OPTIONS,
      page: 1,
      kichThuocTrang: 6,
      thoiDiemApDung: 0,
    };
  },
  computed: {
    danhSachCauHinhDaLoc() {
      const _ = this.thoiDiemApDung;
      return this.danhSachCauHinh.filter((config) => {
        const haystack = [config.id, config.nameLabel, config.dateLabel, config.typeLabel].join(" ").toLowerCase();
        const matchesSearch = haystack.includes(this.search.toLowerCase());
        const matchesType = this.boLocLoai === "all" || config.type === Number(this.boLocLoai);
        return matchesSearch && matchesType;
      });
    },
    tongSoTrang() {
      return Math.max(1, Math.ceil(this.danhSachCauHinhDaLoc.length / this.kichThuocTrang));
    },
    chiSoBatDau() {
      return Math.min((this.page - 1) * this.kichThuocTrang, Math.max(this.danhSachCauHinhDaLoc.length - 1, 0));
    },
    chiSoKetThuc() {
      return Math.min(this.page * this.kichThuocTrang, this.danhSachCauHinhDaLoc.length);
    },
    danhSachCauHinhPhanTrang() {
      return this.danhSachCauHinhDaLoc.slice(this.chiSoBatDau, this.chiSoKetThuc);
    },
    danhSachTrangHienThi() {
      const pages = [];
      const start = Math.max(1, this.page - 1);
      const end = Math.min(this.tongSoTrang, start + 2);
      for (let i = start; i <= end; i += 1) {
        pages.push(i);
      }
      return pages;
    },
    chiSoHienThiBatDau() {
      return this.danhSachCauHinhDaLoc.length ? this.chiSoBatDau + 1 : 0;
    },
    chiSoHienThiKetThuc() {
      return this.danhSachCauHinhDaLoc.length ? this.chiSoKetThuc : 0;
    },
    soNgayLeChinhThuc() {
      return this.danhSachCauHinh.filter((config) => config.type === 1).length;
    },
    soNgayKyNiem() {
      return this.danhSachCauHinh.filter((config) => config.type === 2).length;
    },
    soNgayDacBiet() {
      return this.danhSachCauHinh.filter((config) => config.type === 3).length;
    },
    nhanMocGanNhat() {
      if (!this.danhSachCauHinh.length) return "--";
      const sorted = [...this.danhSachCauHinh].sort((left, right) => left.date.localeCompare(right.date));
      return sorted[0]?.dateLabel || "--";
    },
  },
  watch: {
    tongSoTrang() {
      if (this.page > this.tongSoTrang) {
        this.page = this.tongSoTrang;
      }
    },
    danhSachCauHinhPhanTrang(danhSachCauHinh) {
      if (!danhSachCauHinh.length) {
        this.cauHinhDangChon = null;
        return;
      }

      const stillVisible = danhSachCauHinh.find((item) => item.id === this.cauHinhDangChon?.id);
      if (!stillVisible) {
        [this.cauHinhDangChon] = danhSachCauHinh;
      }
    },
  },
  methods: {
    layLopNhan(accent, tagMode = false) {
      const prefix = tagMode ? "tag--" : "pill--";
      return `${prefix}${accent}`;
    },
    async taiDanhSachCauHinh(options = {}) {
      const { giuThongBaoHanhDong = false } = options;
      this.dangTai = true;
      this.thongBaoLoi = "";
      if (!giuThongBaoHanhDong) {
        this.thongBaoHanhDong = { type: "success", text: "" };
      }

      try {
        const response = await goiApi(`${API_BASE}/admin/cau-hinh-ngay`, {
          headers: {
            Accept: "application/json",
          },
        });
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          if (response.status === 404) {
            this.danhSachCauHinh = [];
            this.cauHinhDangChon = null;
            return;
          }
          throw new Error(payload?.message || "Không thể tải danh sách cấu hình ngày.");
        }

        this.danhSachCauHinh = normalizeCollection(payload).map((item) => mapDayConfig(item));
        this.page = 1;
        this.thoiDiemApDung = Date.now();
        this.cauHinhDangChon = this.danhSachCauHinh[0] || null;
      } catch (error) {
        this.danhSachCauHinh = [];
        this.cauHinhDangChon = null;
        this.thongBaoLoi = error.message || "Không thể tải danh sách cấu hình ngày.";
      } finally {
        this.dangTai = false;
      }
    },
    apDungBoLoc() {
      this.page = 1;
      this.thoiDiemApDung = Date.now();
    },
    async xoaCauHinh(config) {
      const confirmed = await showConfirm({
        title: "Xác nhận xóa cấu hình",
        message: `Xóa cấu hình ngày ${config.code} khỏi hệ thống?`,
        tone: "danger",
        confirmText: "Xóa cấu hình",
        cancelText: "Hủy",
      });
      if (!confirmed) return;

      this.maDangXoa = config.id;
      this.thongBaoLoi = "";
      this.thongBaoHanhDong = { type: "success", text: "" };

      try {
        const response = await goiApi(`${API_BASE}/admin/cau-hinh-ngay/${encodeURIComponent(config.id)}`, {
          method: "DELETE",
          headers: {
            Accept: "application/json",
          },
        });
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          throw new Error(payload?.message || "Không thể xóa cấu hình ngày.");
        }

        await this.taiDanhSachCauHinh({ giuThongBaoHanhDong: true });
        this.thongBaoHanhDong = {
          type: "success",
          text: "Đã xóa cấu hình ngày thành công.",
        };
        toaster.success(`Đã xóa cấu hình ngày ${config.code}.`);
      } catch (error) {
        this.thongBaoHanhDong = {
          type: "error",
          text: error.message || "Không thể xóa cấu hình ngày.",
        };
        toaster.error(error.message || "Không thể xóa cấu hình ngày.");
      } finally {
        this.maDangXoa = "";
      }
    },
  },
  mounted() {
    this.taiDanhSachCauHinh();
  },
};
</script>

<style scoped>
.config-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background:
    radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24%),
    linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.config-page__hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.config-page__eyebrow {
  margin: 0 0 0.4rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.config-page__hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.35rem, 4vw, 3.2rem);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.config-page__subtitle {
  margin: 0.8rem 0 0;
  color: #5f7191;
  font-size: 1.05rem;
  max-width: 760px;
}

.config-page__hero-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.ghost-button,
.primary-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  min-height: 3.2rem;
  padding: 0 1.25rem;
  border-radius: 1rem;
  font-weight: 800;
  text-decoration: none;
}

.ghost-button {
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, 0.88);
  color: #334155;
}

.primary-link {
  border: 0;
  background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%);
  color: #ffffff;
}

.ghost-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

.stats-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.45rem;
  border-radius: 1.6rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
}

.stats-card__icon {
  width: 4.25rem;
  height: 4.25rem;
  border-radius: 1.25rem;
  display: grid;
  place-items: center;
  font-size: 1.45rem;
}

.stats-card__icon--violet { background: #efefff; color: #4f25f4; }
.stats-card__icon--blue { background: #eaf4ff; color: #0369a1; }
.stats-card__icon--amber { background: #fff7e8; color: #d97706; }
.stats-card__icon--green { background: #eaf9f1; color: #08986c; }
.stats-card__icon--sky { background: #eef2ff; color: #4338ca; }

.stats-card span {
  display: block;
  color: #60708d;
  font-weight: 700;
}

.stats-card strong {
  display: block;
  margin-top: 0.35rem;
  color: #111b39;
  font-size: 1.75rem;
  line-height: 1;
  font-weight: 900;
}

.filter-bar {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(220px, 0.8fr) auto;
  gap: 1rem;
  padding: 1.15rem;
  border-radius: 1.6rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
  margin-bottom: 1.5rem;
}

.filter-search,
.filter-select {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  min-height: 3.45rem;
  padding: 0 1rem;
  border-radius: 1rem;
  background: #f7f8fc;
  border: 1px solid #eef1f7;
  color: #324053;
}

.filter-search i {
  color: #8d99af;
}

.filter-search input,
.filter-select {
  border: 0;
  outline: none;
  font-size: 1rem;
}

.filter-search input {
  width: 100%;
  background: transparent;
}

.filter-apply {
  min-height: 3.45rem;
  padding: 0 1.45rem;
  border: 0;
  border-radius: 1rem;
  background: #131b35;
  color: #ffffff;
  font-weight: 800;
}

.notice {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.1rem;
  border-radius: 1rem;
  margin-bottom: 1rem;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.notice--success {
  background: #ecfdf3;
  color: #15803d;
}

.loading-shell {
  display: grid;
  grid-template-columns: 1.6fr 1fr;
  gap: 1rem;
}

.loading-card,
.table-card,
.profile-card,
.empty-state {
  border-radius: 1.75rem;
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
}

.loading-card {
  min-height: 280px;
  position: relative;
  overflow: hidden;
}

.loading-card::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.85), transparent);
  transform: translateX(-100%);
  animation: shimmer 1.4s infinite;
}

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.9fr) minmax(320px, 0.9fr);
  gap: 1.5rem;
}

.table-card,
.profile-card {
  padding: 1.35rem 1.4rem 1rem;
}

.table-card {
  overflow: hidden;
}

.table-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.table-head h2 {
  margin: 0;
  color: #111827;
  font-size: 1.35rem;
  font-weight: 900;
}

.table-head p {
  margin: 0.4rem 0 0;
  color: #64748b;
}

.table-chip {
  padding: 0.7rem 1rem;
  border-radius: 0.95rem;
  background: #eef2ff;
  color: #4338ca;
  font-weight: 800;
}

.config-table {
  display: grid;
  width: 100%;
}

.config-table__row {
  display: grid;
  grid-template-columns: minmax(190px, 1.45fr) 92px minmax(150px, 0.95fr) 120px 132px;
  gap: 1rem;
  align-items: center;
  padding: 1.2rem 0;
  border-top: 1px solid #eef2f7;
}

.config-table__row:not(.config-table__row--head) {
  cursor: pointer;
}

.config-table__row.is-selected {
  background: #f8fbff;
}

.config-table__row--head {
  border-top: 0;
  padding-top: 0.1rem;
  color: #637594;
  font-size: 0.82rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.config-main strong {
  display: block;
  color: #111b39;
  overflow-wrap: anywhere;
}

.config-main small {
  display: block;
  margin-top: 0.35rem;
  color: #66768f;
}

.config-main {
  min-width: 0;
}

.config-id {
  display: inline-flex;
  gap: 0.35rem;
  min-width: 4.25rem;
  padding: 0.45rem 0.8rem;
  border-radius: 0.75rem;
  background: #f3f5fa;
  color: #60708d;
  font-size: 0.9rem;
  font-weight: 700;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
}

.pill,
.tag {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: fit-content;
  font-weight: 800;
}

.pill {
  min-height: 1.95rem;
  padding: 0 0.95rem;
  border-radius: 999px;
}

.pill--blue,
.tag--blue {
  background: #eaf4ff;
  color: #0369a1;
}

.pill--amber,
.tag--amber {
  background: #fff7e8;
  color: #d97706;
}

.pill--violet,
.tag--violet {
  background: #efefff;
  color: #4f25f4;
}

.muted {
  color: #66768f;
}

.actions {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.icon-btn {
  width: 2.2rem;
  height: 2.2rem;
  border: 0;
  border-radius: 0.8rem;
  background: transparent;
  color: #8b97ae;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.icon-btn:hover {
  background: #f4f6fb;
  color: #344054;
}

.icon-btn--danger:hover {
  background: #fff1f2;
  color: #be123c;
}

.icon-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.table-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding-top: 1.2rem;
}

.table-footer span {
  color: #64748b;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.page-btn {
  width: 3rem;
  height: 3rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  border: 1px solid #eceff6;
  border-radius: 1rem;
  background: #ffffff;
  color: #55657f;
  font-weight: 800;
  font-size: 0.95rem;
  font-variant-numeric: tabular-nums;
}

.page-btn.is-active {
  border-color: transparent;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
  box-shadow: 0 12px 24px rgba(79, 37, 244, 0.22);
}

.page-btn i {
  font-size: 0.9rem;
  line-height: 1;
}

.profile-card {
  align-self: start;
  position: sticky;
  top: 1.5rem;
}

.profile-card__header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #eef2f7;
}

.profile-card__avatar {
  width: 4.2rem;
  height: 4.2rem;
  border-radius: 1.3rem;
  display: grid;
  place-items: center;
  font-size: 1.15rem;
}

.profile-card__eyebrow {
  margin: 0 0 0.4rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  font-size: 0.72rem;
  font-weight: 900;
}

.profile-card__header h2,
.profile-card__empty h2,
.empty-state h2 {
  margin: 0 0 0.55rem;
  color: #111827;
  font-size: 1.45rem;
  font-weight: 900;
}

.profile-card__tags {
  display: flex;
  gap: 0.55rem;
  flex-wrap: wrap;
}

.tag {
  min-height: 2rem;
  padding: 0 0.85rem;
  border-radius: 999px;
  font-size: 0.9rem;
}

.tag--slate {
  background: #f1f5f9;
  color: #475569;
}

.profile-card__body {
  display: grid;
  gap: 0.95rem;
  padding: 1.3rem 0;
}

.profile-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.profile-row span {
  color: #64748b;
}

.profile-row strong {
  color: #0f172a;
  text-align: right;
  max-width: 60%;
}

.profile-card__actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.8rem;
}

.profile-link {
  min-height: 3rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
  text-decoration: none;
  font-weight: 800;
}

.profile-link--primary {
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
}

.profile-link--secondary {
  background: #f7f8fc;
  border: 1px solid #e6ebf4;
  color: #334155;
}

.profile-card__empty,
.empty-state {
  display: grid;
  place-items: center;
  text-align: center;
  color: #64748b;
}

.profile-card__empty {
  min-height: 420px;
}

.profile-card__empty i,
.empty-state i {
  width: 5rem;
  height: 5rem;
  display: grid;
  place-items: center;
  border-radius: 1.5rem;
  margin: 0 auto 1rem;
  background: #f4f7fb;
  color: #2453ff;
  font-size: 1.5rem;
}

.empty-state {
  min-height: 420px;
  padding: 2rem;
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

@media (max-width: 1360px) {
  .content-grid,
  .loading-shell {
    grid-template-columns: 1fr;
  }

  .profile-card {
    position: static;
  }
}

@media (max-width: 1280px) {
  .stats-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .filter-bar {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .config-page {
    padding: 1rem;
  }

  .config-page__hero {
    flex-direction: column;
    align-items: flex-start;
  }

  .config-table__row,
  .config-table__row--head {
    grid-template-columns: minmax(190px, 1.45fr) 92px minmax(150px, 0.95fr) 120px 96px;
  }

  .config-table__row > :nth-child(5),
  .config-table__row--head > :nth-child(5) {
    display: none;
  }
}

@media (max-width: 768px) {
  .stats-grid,
  .filter-bar,
  .profile-card__actions {
    grid-template-columns: 1fr;
  }

  .config-table__row--head {
    display: none;
  }

  .config-table__row {
    grid-template-columns: 1fr;
    gap: 0.8rem;
  }

  .table-footer {
    flex-direction: column;
    align-items: flex-start;
  }

  .profile-row {
    flex-direction: column;
    gap: 0.35rem;
  }

  .profile-row strong {
    max-width: 100%;
    text-align: left;
  }
}
</style>





