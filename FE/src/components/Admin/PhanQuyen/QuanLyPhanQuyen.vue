<template>
  <div class="permission-page">
    <section class="hero">
      <div>
        <p class="eyebrow">Bảng điều khiển quản trị</p>
        <h1>Quản lý Chức vụ &amp; Phân quyền</h1>
        <p class="subtitle">Quản lý chức vụ, kho chức năng và gán quyền chi tiết cho từng nhóm admin.</p>
      </div>
      <button class="btn btn-ghost" type="button" @click="taiTatCaDuLieu" :disabled="dangTai">
        <i class="fas fa-rotate-right" :class="{ 'fa-spin': dangTai }"></i>
        <span>{{ dangTai ? "Đang đồng bộ..." : "Tải lại dữ liệu" }}</span>
      </button>
    </section>

    <section class="stats-grid">
      <article class="stats-card">
        <i class="fas fa-user-tie"></i>
        <div><span>Tổng số chức vụ</span><strong>{{ chucVus.length }}</strong></div>
      </article>
      <article class="stats-card">
        <i class="fas fa-user-check"></i>
        <div><span>Chức vụ hoạt động</span><strong>{{ tongChucVuHoatDong }}</strong></div>
      </article>
      <article class="stats-card">
        <i class="fas fa-bolt"></i>
        <div><span>Chức năng hệ thống</span><strong>{{ chucNangs.length }}</strong></div>
      </article>
      <article class="stats-card">
        <i class="fas fa-link"></i>
        <div><span>Liên kết phân quyền</span><strong>{{ phanQuyens.length }}</strong></div>
      </article>
    </section>

    <section class="content-grid">
      <article class="card">
        <div class="card-head">
          <div>
            <h2>Danh sách chức vụ</h2>
            <p>Hiển thị cả chức vụ đang hoạt động và đã khóa.</p>
          </div>
          <button class="btn btn-primary" type="button" @click="moModalChucVu()">
            <i class="fas fa-plus"></i><span>Thêm chức vụ</span>
          </button>
        </div>

        <div class="toolbar">
          <label class="search-box">
            <i class="fas fa-search"></i>
            <input v-model.trim="tuKhoaChucVu" type="text" placeholder="Tìm theo mã hoặc tên chức vụ">
          </label>
        </div>

        <div v-if="danhSachChucVuDaLoc.length" class="role-list">
          <button
            v-for="role in danhSachChucVuDaLoc"
            :key="role.ma_chuc_vu"
            type="button"
            class="role-item"
            :class="{ 'is-selected': soSanhMa(maChucVuDangChon, role.ma_chuc_vu) }"
            @click="chonChucVu(role.ma_chuc_vu)"
          >
            <div class="role-item__top">
              <div>
                <small class="muted">{{ role.ma_chuc_vu }}</small>
                <strong>{{ role.ten_chuc_vu }}</strong>
              </div>
              <span :class="['pill', role.tinh_trang === 1 ? 'pill-success' : 'pill-muted']">
                {{ role.tinh_trang === 1 ? 'Hoạt động' : 'Đã khóa' }}
              </span>
            </div>
            <div class="role-item__bottom">
              <span>{{ demSoQuyen(role.ma_chuc_vu) }} quyền</span>
              <div class="actions-inline">
                <button class="icon-btn" type="button" @click.stop="moModalChucVu(role)" title="Chỉnh sửa chức vụ">
                  <i class="fas fa-pen"></i>
                </button>
                <button
                  class="icon-btn"
                  type="button"
                  @click.stop="chuyenTrangThaiChucVu(role)"
                  :disabled="laChucVuDangXuLy(role.ma_chuc_vu)"
                  :title="role.tinh_trang === 1 ? 'Khóa chức vụ' : 'Mở khóa chức vụ'"
                >
                  <i class="fas" :class="role.tinh_trang === 1 ? 'fa-lock' : 'fa-lock-open'"></i>
                </button>
                <button
                  class="icon-btn icon-btn-danger"
                  type="button"
                  @click.stop="xoaChucVu(role)"
                  :disabled="laChucVuDangXuLy(role.ma_chuc_vu)"
                  title="Xóa chức vụ"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </button>
        </div>
        <div v-else class="empty-state">
          <i class="fas fa-user-slash"></i>
          <p>Không tìm thấy chức vụ phù hợp.</p>
        </div>
      </article>

      <aside class="card">
        <div class="card-head card-head-stack">
          <div v-if="chucVuDangChon">
            <p class="eyebrow">Gán quyền theo chức vụ</p>
            <h2>{{ chucVuDangChon.ten_chuc_vu }}</h2>
            <p>{{ demSoQuyen(chucVuDangChon.ma_chuc_vu) }} quyền đang được gán cho chức vụ này.</p>
          </div>
          <div v-else>
            <p class="eyebrow">Gán quyền theo chức vụ</p>
            <h2>Chưa chọn chức vụ</h2>
            <p>Chọn một chức vụ ở cột bên trái để bắt đầu cấu hình quyền.</p>
          </div>
        </div>

        <div v-if="chucVuDangChon" class="toolbar toolbar-stack">
          <label class="search-box">
            <i class="fas fa-search"></i>
            <input v-model.trim="tuKhoaChucNang" type="text" placeholder="Lọc chức năng theo mã, tên hoặc mô tả">
          </label>
          <button class="btn btn-secondary" type="button" @click="moModalChucNang()">
            <i class="fas fa-plus"></i><span>Thêm chức năng</span>
          </button>
        </div>

        <div v-if="chucVuDangChon && danhSachChucNangDaLoc.length" class="permission-list">
          <article v-for="func in danhSachChucNangDaLoc" :key="func.ma_chuc_nang" class="permission-item">
            <div class="permission-item__copy">
              <small class="muted">{{ func.ma_chuc_nang }}</small>
              <strong>{{ func.ten_chuc_nang }}</strong>
              <p>{{ func.mo_ta || 'Chưa có mô tả cho chức năng này.' }}</p>
            </div>
            <div class="permission-item__aside">
              <span>{{ demSoChucVuDuocGan(func.ma_chuc_nang) }} chức vụ</span>
              <label class="switch" :class="{ 'is-disabled': laQuyenDangXuLy(chucVuDangChon.ma_chuc_vu, func.ma_chuc_nang) }">
                <input
                  type="checkbox"
                  :checked="coQuyen(chucVuDangChon.ma_chuc_vu, func.ma_chuc_nang)"
                  :disabled="laQuyenDangXuLy(chucVuDangChon.ma_chuc_vu, func.ma_chuc_nang)"
                  @change="chuyenQuyen(chucVuDangChon.ma_chuc_vu, func.ma_chuc_nang, $event.target.checked, $event)"
                >
                <span class="slider"></span>
              </label>
              <div v-if="timPhanQuyen(chucVuDangChon.ma_chuc_vu, func.ma_chuc_nang)" class="mapping-actions">
                <button
                  class="icon-btn"
                  type="button"
                  title="Sửa phân quyền"
                  @click="moModalPhanQuyen(timPhanQuyen(chucVuDangChon.ma_chuc_vu, func.ma_chuc_nang))"
                >
                  <i class="fas fa-pen"></i>
                </button>
                <button
                  class="icon-btn icon-btn-danger"
                  type="button"
                  title="Xóa phân quyền"
                  @click="xoaPhanQuyen(timPhanQuyen(chucVuDangChon.ma_chuc_vu, func.ma_chuc_nang))"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </article>
        </div>
        <div v-else-if="chucVuDangChon" class="empty-state empty-state-small">
          <i class="fas fa-key"></i>
          <p>Không có chức năng nào khớp với bộ lọc hiện tại.</p>
        </div>
        <div v-else class="empty-state">
          <i class="fas fa-fingerprint"></i>
          <p>Hãy chọn một chức vụ để xem và gán quyền.</p>
        </div>
      </aside>
    </section>

    <section class="card">
      <div class="card-head">
        <div>
          <h2>Kho chức năng</h2>
          <p>Quản lý danh sách chức năng được phép gán cho các nhóm admin.</p>
        </div>
        <button class="btn btn-primary" type="button" @click="moModalChucNang()">
          <i class="fas fa-plus"></i><span>Thêm chức năng</span>
        </button>
      </div>

      <div class="toolbar">
        <label class="search-box">
          <i class="fas fa-search"></i>
          <input v-model.trim="tuKhoaChucNang" type="text" placeholder="Tìm kiếm chức năng">
        </label>
      </div>

      <div v-if="danhSachChucNangDaLoc.length" class="function-grid">
        <article v-for="func in danhSachChucNangDaLoc" :key="func.ma_chuc_nang" class="function-card">
          <div class="function-card__top">
            <div>
              <small class="muted">{{ func.ma_chuc_nang }}</small>
              <strong>{{ func.ten_chuc_nang }}</strong>
            </div>
            <span class="pill pill-info">{{ demSoChucVuDuocGan(func.ma_chuc_nang) }} chức vụ</span>
          </div>
          <p>{{ func.mo_ta || 'Chưa có mô tả cho chức năng này.' }}</p>
          <div class="function-card__actions">
            <button class="btn btn-secondary btn-small" type="button" @click="moModalChucNang(func)">
              <i class="fas fa-pen"></i><span>Chỉnh sửa</span>
            </button>
            <button class="btn btn-danger btn-small" type="button" @click="xoaChucNang(func)" :disabled="laChucNangDangXuLy(func.ma_chuc_nang)">
              <i class="fas fa-trash"></i><span>Xóa</span>
            </button>
          </div>
        </article>
      </div>
      <div v-else class="empty-state empty-state-small">
        <i class="fas fa-bolt"></i>
        <p>Chưa có chức năng nào trong hệ thống.</p>
      </div>
    </section>

    <div v-if="hienThiModalChucVu" class="modal-overlay" @click.self="dongModalChucVu">
      <article class="modal-card">
        <div class="card-head">
          <div>
            <p class="eyebrow">Chức vụ quản trị</p>
            <h2>{{ formChucVu.ma_chuc_vu ? 'Cập nhật chức vụ' : 'Tạo chức vụ mới' }}</h2>
          </div>
          <button class="icon-btn" type="button" @click="dongModalChucVu"><i class="fas fa-times"></i></button>
        </div>
        <div class="form-grid">
          <label class="field">
            <span>Tên chức vụ</span>
            <input v-model.trim="formChucVu.ten_chuc_vu" type="text" placeholder="Ví dụ: Trưởng phòng vận hành">
          </label>
          <label v-if="formChucVu.ma_chuc_vu" class="field">
            <span>Trạng thái</span>
            <select v-model.number="formChucVu.tinh_trang">
              <option :value="1">Hoạt động</option>
              <option :value="0">Đã khóa</option>
            </select>
          </label>
        </div>
        <div class="modal-actions">
          <button class="btn btn-ghost btn-small" type="button" @click="dongModalChucVu">Hủy</button>
          <button class="btn btn-primary btn-small" type="button" @click="luuChucVu" :disabled="dangLuuChucVu || !formChucVu.ten_chuc_vu">
            <i v-if="dangLuuChucVu" class="fas fa-spinner fa-spin"></i>
            <span>{{ dangLuuChucVu ? 'Đang lưu...' : 'Lưu chức vụ' }}</span>
          </button>
        </div>
      </article>
    </div>

    <div v-if="hienThiModalChucNang" class="modal-overlay" @click.self="dongModalChucNang">
      <article class="modal-card modal-card-wide">
        <div class="card-head">
          <div>
            <p class="eyebrow">Kho chức năng</p>
            <h2>{{ formChucNang.ma_chuc_nang ? 'Cập nhật chức năng' : 'Tạo chức năng mới' }}</h2>
          </div>
          <button class="icon-btn" type="button" @click="dongModalChucNang"><i class="fas fa-times"></i></button>
        </div>
        <div class="form-grid">
          <label class="field">
            <span>Tên chức năng</span>
            <input v-model.trim="formChucNang.ten_chuc_nang" type="text" placeholder="Ví dụ: Quản lý hóa đơn">
          </label>
          <label class="field field-full">
            <span>Mô tả</span>
            <textarea v-model.trim="formChucNang.mo_ta" rows="4" placeholder="Mô tả ngắn gọn chức năng này dùng để làm gì"></textarea>
          </label>
        </div>
        <div class="modal-actions">
          <button class="btn btn-ghost btn-small" type="button" @click="dongModalChucNang">Hủy</button>
          <button class="btn btn-primary btn-small" type="button" @click="luuChucNang" :disabled="dangLuuChucNang || !formChucNang.ten_chuc_nang">
            <i v-if="dangLuuChucNang" class="fas fa-spinner fa-spin"></i>
            <span>{{ dangLuuChucNang ? 'Đang lưu...' : 'Lưu chức năng' }}</span>
          </button>
        </div>
      </article>
    </div>

    <div v-if="hienThiModalPhanQuyen" class="modal-overlay" @click.self="dongModalPhanQuyen">
      <article class="modal-card">
        <div class="card-head">
          <div>
            <p class="eyebrow">Phân quyền admin</p>
            <h2>Cập nhật phân quyền</h2>
          </div>
          <button class="icon-btn" type="button" @click="dongModalPhanQuyen"><i class="fas fa-times"></i></button>
        </div>
        <div class="form-grid">
          <label class="field">
            <span>Chức vụ</span>
            <select v-model="formPhanQuyen.ma_chuc_vu">
              <option value="" disabled>Chọn chức vụ</option>
              <option v-for="role in chucVus" :key="`mapping-role-${role.ma_chuc_vu}`" :value="role.ma_chuc_vu">
                {{ role.ten_chuc_vu }} ({{ role.ma_chuc_vu }})
              </option>
            </select>
          </label>
          <label class="field">
            <span>Chức năng</span>
            <select v-model="formPhanQuyen.ma_chuc_nang">
              <option value="" disabled>Chọn chức năng</option>
              <option v-for="func in chucNangs" :key="`mapping-func-${func.ma_chuc_nang}`" :value="func.ma_chuc_nang">
                {{ func.ten_chuc_nang }} ({{ func.ma_chuc_nang }})
              </option>
            </select>
          </label>
        </div>
        <div class="modal-actions">
          <button class="btn btn-ghost btn-small" type="button" @click="dongModalPhanQuyen">Hủy</button>
          <button
            class="btn btn-primary btn-small"
            type="button"
            @click="luuPhanQuyen"
            :disabled="dangLuuPhanQuyen || !formPhanQuyen.ma_chuc_vu || !formPhanQuyen.ma_chuc_nang"
          >
            <i v-if="dangLuuPhanQuyen" class="fas fa-spinner fa-spin"></i>
            <span>{{ dangLuuPhanQuyen ? 'Đang lưu...' : 'Lưu phân quyền' }}</span>
          </button>
        </div>
      </article>
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient.js";
import { showConfirm } from "../../../services/appDialog";
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
  position: "top-right",
  duration: 3000,
});

const API_BASE = "/api";

async function goiDuLieu(url, { method = "GET", body, params, headers } = {}) {
  const phanHoi = await goiApi(url, { method, body, params, headers });
  const duLieu = await phanHoi.json();

  if (!phanHoi.ok) {
    const loi = new Error(duLieu?.message || "Không thể hoàn tất yêu cầu.");
    loi.response = { data: duLieu, status: phanHoi.status };
    throw loi;
  }

  return { data: duLieu, status: phanHoi.status };
}

export default {
  name: "QuanLyPhanQuyen",
  data() {
    return {
      chucVus: [],
      chucNangs: [],
      phanQuyens: [],
      dangTai: false,
      tuKhoaChucVu: "",
      tuKhoaChucNang: "",
      maChucVuDangChon: "",
      hienThiModalChucVu: false,
      hienThiModalChucNang: false,
      hienThiModalPhanQuyen: false,
      dangLuuChucVu: false,
      dangLuuChucNang: false,
      dangLuuPhanQuyen: false,
      danhSachMaChucVuDangXuLy: [],
      danhSachMaChucNangDangXuLy: [],
      danhSachKhoaQuyenDangXuLy: [],
      formChucVu: { ma_chuc_vu: "", ten_chuc_vu: "", tinh_trang: 1 },
      formChucNang: { ma_chuc_nang: "", ten_chuc_nang: "", mo_ta: "" },
      formPhanQuyen: { ma_phan_quyen: "", ma_chuc_vu: "", ma_chuc_nang: "" },
    };
  },
  computed: {
    tongChucVuHoatDong() {
      return this.chucVus.filter((role) => Number(role.tinh_trang) === 1).length;
    },
    danhSachChucVuDaLoc() {
      const keyword = this.tuKhoaChucVu.trim().toLowerCase();
      if (!keyword) return this.chucVus;
      return this.chucVus.filter((role) => [role.ma_chuc_vu, role.ten_chuc_vu]
        .map((value) => String(value || "").toLowerCase())
        .some((value) => value.includes(keyword)));
    },
    danhSachChucNangDaLoc() {
      const keyword = this.tuKhoaChucNang.trim().toLowerCase();
      if (!keyword) return this.chucNangs;
      return this.chucNangs.filter((func) => [func.ma_chuc_nang, func.ten_chuc_nang, func.mo_ta]
        .map((value) => String(value || "").toLowerCase())
        .some((value) => value.includes(keyword)));
    },
    chucVuDangChon() {
      return this.chucVus.find((role) => this.soSanhMa(role.ma_chuc_vu, this.maChucVuDangChon)) || null;
    },
  },
  mounted() {
    this.taiTatCaDuLieu();
  },
  methods: {
    soSanhMa(left, right) {
      return String(left ?? "") === String(right ?? "");
    },
    trichXuatDanhSach(payload) {
      if (Array.isArray(payload)) return payload;
      if (Array.isArray(payload?.data)) return payload.data;
      if (Array.isArray(payload?.data?.data)) return payload.data.data;
      return [];
    },
    chuanHoaChucVu(role) {
      return {
        ma_chuc_vu: String(role?.ma_chuc_vu ?? ""),
        ten_chuc_vu: String(role?.ten_chuc_vu ?? ""),
        tinh_trang: Number(role?.tinh_trang) === 1 ? 1 : 0,
      };
    },
    chuanHoaChucNang(func) {
      return {
        ma_chuc_nang: String(func?.ma_chuc_nang ?? ""),
        ten_chuc_nang: String(func?.ten_chuc_nang ?? ""),
        mo_ta: String(func?.mo_ta ?? "").trim(),
      };
    },
    chuanHoaPhanQuyen(mapping) {
      return {
        ma_phan_quyen: mapping?.ma_phan_quyen,
        ma_chuc_vu: String(mapping?.ma_chuc_vu ?? ""),
        ma_chuc_nang: String(mapping?.ma_chuc_nang ?? ""),
      };
    },
    chonChucVu(roleId) {
      this.maChucVuDangChon = String(roleId || "");
    },
    taoKhoaQuyen(roleId, functionId) {
      return `${String(roleId)}::${String(functionId)}`;
    },
    laQuyenDangXuLy(roleId, functionId) {
      return this.danhSachKhoaQuyenDangXuLy.includes(this.taoKhoaQuyen(roleId, functionId));
    },
    laChucVuDangXuLy(roleId) {
      return this.danhSachMaChucVuDangXuLy.includes(String(roleId));
    },
    laChucNangDangXuLy(functionId) {
      return this.danhSachMaChucNangDangXuLy.includes(String(functionId));
    },
    dinhDangThongBaoLoi(error, fallback) {
      const payload = error?.response?.data;
      if (payload?.message) return payload.message;
      if (payload?.errors && typeof payload.errors === "object") {
        const firstKey = Object.keys(payload.errors)[0];
        const firstValue = payload.errors[firstKey];
        if (Array.isArray(firstValue) && firstValue.length) return firstValue[0];
      }
      return fallback;
    },
    demSoQuyen(roleId) {
      return this.phanQuyens.filter((mapping) => this.soSanhMa(mapping.ma_chuc_vu, roleId)).length;
    },
    demSoChucVuDuocGan(functionId) {
      return this.phanQuyens.filter((mapping) => this.soSanhMa(mapping.ma_chuc_nang, functionId)).length;
    },
    coQuyen(roleId, functionId) {
      return this.phanQuyens.some((mapping) => this.soSanhMa(mapping.ma_chuc_vu, roleId) && this.soSanhMa(mapping.ma_chuc_nang, functionId));
    },
    timPhanQuyen(roleId, functionId) {
      return this.phanQuyens.find((mapping) => this.soSanhMa(mapping.ma_chuc_vu, roleId) && this.soSanhMa(mapping.ma_chuc_nang, functionId)) || null;
    },
    themChucVuDangXuLy(roleId) {
      const key = String(roleId);
      if (!this.danhSachMaChucVuDangXuLy.includes(key)) this.danhSachMaChucVuDangXuLy.push(key);
    },
    xoaChucVuDangXuLy(roleId) {
      const key = String(roleId);
      this.danhSachMaChucVuDangXuLy = this.danhSachMaChucVuDangXuLy.filter((item) => item !== key);
    },
    themChucNangDangXuLy(functionId) {
      const key = String(functionId);
      if (!this.danhSachMaChucNangDangXuLy.includes(key)) this.danhSachMaChucNangDangXuLy.push(key);
    },
    xoaChucNangDangXuLy(functionId) {
      const key = String(functionId);
      this.danhSachMaChucNangDangXuLy = this.danhSachMaChucNangDangXuLy.filter((item) => item !== key);
    },
    themKhoaQuyenDangXuLy(roleId, functionId) {
      const key = this.taoKhoaQuyen(roleId, functionId);
      if (!this.danhSachKhoaQuyenDangXuLy.includes(key)) this.danhSachKhoaQuyenDangXuLy.push(key);
    },
    xoaKhoaQuyenDangXuLy(roleId, functionId) {
      const key = this.taoKhoaQuyen(roleId, functionId);
      this.danhSachKhoaQuyenDangXuLy = this.danhSachKhoaQuyenDangXuLy.filter((item) => item !== key);
    },
    async taiDanhSachChucVu() {
      try {
        const response = await goiDuLieu(`${API_BASE}/chuc-vu/all`, { params: { per_page: 200 } });
        return this.trichXuatDanhSach(response.data).map((role) => this.chuanHoaChucVu(role));
      } catch (error) {
        const fallback = await goiDuLieu(`${API_BASE}/chuc-vu`);
        return this.trichXuatDanhSach(fallback.data).map((role) => this.chuanHoaChucVu(role));
      }
    },
    async taiTatCaDuLieu() {
      this.dangTai = true;
      const previousRoleId = this.maChucVuDangChon;
      try {
        const [chucVus, chucNangs, phanQuyensRes] = await Promise.all([
          this.taiDanhSachChucVu(),
          goiDuLieu(`${API_BASE}/chuc-nang`),
          goiDuLieu(`${API_BASE}/phan-quyen-admin`),
        ]);
        this.chucVus = chucVus;
        this.chucNangs = this.trichXuatDanhSach(chucNangs.data).map((func) => this.chuanHoaChucNang(func));
        this.phanQuyens = this.trichXuatDanhSach(phanQuyensRes.data).map((mapping) => this.chuanHoaPhanQuyen(mapping));
        this.maChucVuDangChon = this.chucVus.some((role) => this.soSanhMa(role.ma_chuc_vu, previousRoleId))
          ? previousRoleId
          : (this.chucVus[0]?.ma_chuc_vu || "");
      } catch (error) {
        toaster.error(this.dinhDangThongBaoLoi(error, "Không thể tải dữ liệu chức vụ, chức năng và phân quyền."));
      } finally {
        this.dangTai = false;
      }
    },
    moModalChucVu(role = null) {
      this.formChucVu = role
        ? { ma_chuc_vu: role.ma_chuc_vu, ten_chuc_vu: role.ten_chuc_vu, tinh_trang: Number(role.tinh_trang) === 1 ? 1 : 0 }
        : { ma_chuc_vu: "", ten_chuc_vu: "", tinh_trang: 1 };
      this.hienThiModalChucVu = true;
    },
    dongModalChucVu() {
      if (!this.dangLuuChucVu) this.hienThiModalChucVu = false;
    },
    async luuChucVu() {
      const payload = { ten_chuc_vu: this.formChucVu.ten_chuc_vu.trim() };
      if (!payload.ten_chuc_vu) return;
      if (this.formChucVu.ma_chuc_vu) payload.tinh_trang = Number(this.formChucVu.tinh_trang) === 1 ? 1 : 0;
      this.dangLuuChucVu = true;
      try {
        if (this.formChucVu.ma_chuc_vu) {
          const response = await goiDuLieu(`${API_BASE}/chuc-vu/${this.formChucVu.ma_chuc_vu}`, { method: "PUT", body: payload });
          this.maChucVuDangChon = response?.data?.data?.ma_chuc_vu || this.formChucVu.ma_chuc_vu;
          toaster.success(response?.data?.message || "Thông tin chức vụ đã được cập nhật.");
        } else {
          const response = await goiDuLieu(`${API_BASE}/chuc-vu`, { method: "POST", body: payload });
          this.maChucVuDangChon = response?.data?.data?.ma_chuc_vu || this.maChucVuDangChon;
          toaster.success(response?.data?.message || "Chức vụ mới đã được tạo thành công.");
        }
        this.hienThiModalChucVu = false;
        await this.taiTatCaDuLieu();
      } catch (error) {
        toaster.error(this.dinhDangThongBaoLoi(error, "Không thể lưu thông tin chức vụ."));
      } finally {
        this.dangLuuChucVu = false;
      }
    },
    async chuyenTrangThaiChucVu(role) {
      const willActivate = Number(role.tinh_trang) !== 1;
      const confirmed = await showConfirm({
        title: willActivate ? "Mở khóa chức vụ" : "Khóa chức vụ",
        message: willActivate ? `Bạn có chắc muốn mở khóa chức vụ "${role.ten_chuc_vu}"?` : `Bạn có chắc muốn khóa chức vụ "${role.ten_chuc_vu}"?`,
        tone: willActivate ? "info" : "warning",
        confirmText: willActivate ? "Mở khóa" : "Khóa chức vụ",
      });
      if (!confirmed) return;
      this.themChucVuDangXuLy(role.ma_chuc_vu);
      try {
        const response = await goiDuLieu(`${API_BASE}/chuc-vu/${role.ma_chuc_vu}/status`, { method: "PATCH" });
        await this.taiTatCaDuLieu();
        toaster.success(response?.data?.message || "Trạng thái chức vụ đã được cập nhật.");
      } catch (error) {
        toaster.error(this.dinhDangThongBaoLoi(error, "Đã xảy ra lỗi khi thay đổi trạng thái chức vụ."));
      } finally {
        this.xoaChucVuDangXuLy(role.ma_chuc_vu);
      }
    },
    async xoaChucVu(role) {
      const confirmed = await showConfirm({
        title: "Xóa chức vụ",
        message: `Bạn có chắc muốn xóa chức vụ "${role.ten_chuc_vu}"? Hành động này có thể thất bại nếu chức vụ đang được sử dụng bởi admin hoặc phân quyền.`,
        tone: "danger",
        confirmText: "Xóa chức vụ",
      });
      if (!confirmed) return;
      this.themChucVuDangXuLy(role.ma_chuc_vu);
      try {
        const response = await goiDuLieu(`${API_BASE}/chuc-vu/${role.ma_chuc_vu}`, { method: "DELETE" });
        if (this.soSanhMa(this.maChucVuDangChon, role.ma_chuc_vu)) this.maChucVuDangChon = "";
        await this.taiTatCaDuLieu();
        toaster.success(response?.data?.message || "Chức vụ đã được xóa thành công.");
      } catch (error) {
        toaster.error(this.dinhDangThongBaoLoi(error, "Không thể xóa chức vụ này."));
      } finally {
        this.xoaChucVuDangXuLy(role.ma_chuc_vu);
      }
    },
    moModalChucNang(func = null) {
      this.formChucNang = func
        ? { ma_chuc_nang: func.ma_chuc_nang, ten_chuc_nang: func.ten_chuc_nang, mo_ta: func.mo_ta || "" }
        : { ma_chuc_nang: "", ten_chuc_nang: "", mo_ta: "" };
      this.hienThiModalChucNang = true;
    },
    dongModalChucNang() {
      if (!this.dangLuuChucNang) this.hienThiModalChucNang = false;
    },
    async luuChucNang() {
      const payload = { ten_chuc_nang: this.formChucNang.ten_chuc_nang.trim(), mo_ta: this.formChucNang.mo_ta.trim() || null };
      if (!payload.ten_chuc_nang) return;
      this.dangLuuChucNang = true;
      try {
        if (this.formChucNang.ma_chuc_nang) {
          const response = await goiDuLieu(`${API_BASE}/chuc-nang/${this.formChucNang.ma_chuc_nang}`, { method: "PUT", body: payload });
          toaster.success(response?.data?.message || "Thông tin chức năng đã được cập nhật.");
        } else {
          const response = await goiDuLieu(`${API_BASE}/chuc-nang`, { method: "POST", body: payload });
          toaster.success(response?.data?.message || "Chức năng mới đã được tạo thành công.");
        }
        this.hienThiModalChucNang = false;
        await this.taiTatCaDuLieu();
      } catch (error) {
        toaster.error(this.dinhDangThongBaoLoi(error, "Không thể lưu thông tin chức năng."));
      } finally {
        this.dangLuuChucNang = false;
      }
    },
    async xoaChucNang(func) {
      const assignedRoles = this.demSoChucVuDuocGan(func.ma_chuc_nang);
      const confirmed = await showConfirm({
        title: "Xóa chức năng",
        message: assignedRoles > 0 ? `Chức năng "${func.ten_chuc_nang}" hiện đang gán cho ${assignedRoles} chức vụ. Bạn vẫn muốn xóa?` : `Bạn có chắc muốn xóa chức năng "${func.ten_chuc_nang}"?`,
        tone: "danger",
        confirmText: "Xóa chức năng",
      });
      if (!confirmed) return;
      this.themChucNangDangXuLy(func.ma_chuc_nang);
      try {
        const response = await goiDuLieu(`${API_BASE}/chuc-nang/${func.ma_chuc_nang}`, { method: "DELETE" });
        await this.taiTatCaDuLieu();
        toaster.success(response?.data?.message || "Chức năng đã được xóa thành công.");
      } catch (error) {
        toaster.error(this.dinhDangThongBaoLoi(error, "Không thể xóa chức năng này."));
      } finally {
        this.xoaChucNangDangXuLy(func.ma_chuc_nang);
      }
    },
    moModalPhanQuyen(mapping) {
      if (!mapping) return;
      this.formPhanQuyen = {
        ma_phan_quyen: String(mapping.ma_phan_quyen ?? ""),
        ma_chuc_vu: String(mapping.ma_chuc_vu ?? ""),
        ma_chuc_nang: String(mapping.ma_chuc_nang ?? ""),
      };
      this.hienThiModalPhanQuyen = true;
    },
    dongModalPhanQuyen() {
      if (!this.dangLuuPhanQuyen) this.hienThiModalPhanQuyen = false;
    },
    async luuPhanQuyen() {
      const payload = {
        ma_chuc_vu: String(this.formPhanQuyen.ma_chuc_vu || "").trim(),
        ma_chuc_nang: String(this.formPhanQuyen.ma_chuc_nang || "").trim(),
      };
      if (!payload.ma_chuc_vu || !payload.ma_chuc_nang || !this.formPhanQuyen.ma_phan_quyen) return;

      const duplicated = this.phanQuyens.find((mapping) =>
        !this.soSanhMa(mapping.ma_phan_quyen, this.formPhanQuyen.ma_phan_quyen) &&
        this.soSanhMa(mapping.ma_chuc_vu, payload.ma_chuc_vu) &&
        this.soSanhMa(mapping.ma_chuc_nang, payload.ma_chuc_nang),
      );

      if (duplicated) {
        toaster.warning("Liên kết chức vụ và chức năng này đã tồn tại.");
        return;
      }

      this.dangLuuPhanQuyen = true;
      try {
        const response = await goiDuLieu(`${API_BASE}/phan-quyen-admin/${this.formPhanQuyen.ma_phan_quyen}`, { method: "PUT", body: payload });
        this.maChucVuDangChon = payload.ma_chuc_vu;
        this.hienThiModalPhanQuyen = false;
        await this.taiTatCaDuLieu();
        toaster.success(response?.data?.message || "Phân quyền đã được cập nhật thành công.");
      } catch (error) {
        toaster.error(this.dinhDangThongBaoLoi(error, "Không thể cập nhật phân quyền này."));
      } finally {
        this.dangLuuPhanQuyen = false;
      }
    },
    async xoaPhanQuyen(mapping) {
      if (!mapping?.ma_phan_quyen) return;
      const role = this.chucVus.find((item) => this.soSanhMa(item.ma_chuc_vu, mapping.ma_chuc_vu));
      const func = this.chucNangs.find((item) => this.soSanhMa(item.ma_chuc_nang, mapping.ma_chuc_nang));
      const confirmed = await showConfirm({
        title: "Xóa phân quyền",
        message: `Bạn có chắc muốn xóa quyền "${func?.ten_chuc_nang || mapping.ma_chuc_nang}" khỏi chức vụ "${role?.ten_chuc_vu || mapping.ma_chuc_vu}"?`,
        tone: "danger",
        confirmText: "Xóa phân quyền",
      });
      if (!confirmed) return;

      this.themKhoaQuyenDangXuLy(mapping.ma_chuc_vu, mapping.ma_chuc_nang);
      try {
        const response = await goiDuLieu(`${API_BASE}/phan-quyen-admin/${mapping.ma_phan_quyen}`, { method: "DELETE" });
        this.phanQuyens = this.phanQuyens.filter((item) => !this.soSanhMa(item.ma_phan_quyen, mapping.ma_phan_quyen));
        toaster.success(response?.data?.message || "Phân quyền đã được xóa thành công.");
      } catch (error) {
        await this.taiTatCaDuLieu();
        toaster.error(this.dinhDangThongBaoLoi(error, "Không thể xóa phân quyền này."));
      } finally {
        this.xoaKhoaQuyenDangXuLy(mapping.ma_chuc_vu, mapping.ma_chuc_nang);
      }
    },
    async chuyenQuyen(roleId, functionId, isChecked, event) {
      const role = this.chucVus.find((item) => this.soSanhMa(item.ma_chuc_vu, roleId));
      const func = this.chucNangs.find((item) => this.soSanhMa(item.ma_chuc_nang, functionId));
      const currentMapping = this.timPhanQuyen(roleId, functionId);
      const granting = isChecked;
      const confirmed = await showConfirm({
        title: granting ? "Cấp quyền" : "Gỡ quyền",
        message: granting ? `Cấp chức năng "${func?.ten_chuc_nang || functionId}" cho chức vụ "${role?.ten_chuc_vu || roleId}"?` : `Gỡ chức năng "${func?.ten_chuc_nang || functionId}" khỏi chức vụ "${role?.ten_chuc_vu || roleId}"?`,
        tone: granting ? "info" : "warning",
        confirmText: granting ? "Cấp quyền" : "Gỡ quyền",
      });
      if (!confirmed) {
        if (event?.target) event.target.checked = !isChecked;
        return;
      }
      this.themKhoaQuyenDangXuLy(roleId, functionId);
      try {
        if (granting && !currentMapping) {
          const response = await goiDuLieu(`${API_BASE}/phan-quyen-admin`, {
            method: "POST",
            body: { ma_chuc_vu: String(roleId), ma_chuc_nang: String(functionId) },
          });
          if (response?.data?.data) this.phanQuyens.push(this.chuanHoaPhanQuyen(response.data.data));
          toaster.success(response?.data?.message || "Phân quyền đã được tạo thành công.");
        }
        if (!granting && currentMapping) {
          const response = await goiDuLieu(`${API_BASE}/phan-quyen-admin/${currentMapping.ma_phan_quyen}`, { method: "DELETE" });
          this.phanQuyens = this.phanQuyens.filter((mapping) => !this.soSanhMa(mapping.ma_phan_quyen, currentMapping.ma_phan_quyen));
          toaster.success(response?.data?.message || "Phân quyền đã được xóa thành công.");
        }
      } catch (error) {
        if (event?.target) event.target.checked = !isChecked;
        await this.taiTatCaDuLieu();
        toaster.error(this.dinhDangThongBaoLoi(error, "Không thể cập nhật phân quyền cho chức vụ này."));
      } finally {
        this.xoaKhoaQuyenDangXuLy(roleId, functionId);
      }
    },
  },
};
</script><style scoped>
.permission-page { min-height: 100%; padding: 2rem 2.25rem 2.5rem; background: radial-gradient(circle at top right, rgba(37,99,235,.08), transparent 24%), linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%); }
.hero, .card-head, .toolbar, .permission-item, .permission-item__aside, .function-card__top, .function-card__actions, .modal-actions { display: flex; justify-content: space-between; gap: 1rem; align-items: center; }
.hero { margin-bottom: 1.5rem; align-items: flex-start; }
.eyebrow { margin: 0 0 .45rem; color: #2453ff; text-transform: uppercase; letter-spacing: .14em; font-size: .76rem; font-weight: 900; }
.hero h1, .card-head h2 { margin: 0; color: #111827; font-weight: 900; letter-spacing: -.04em; }
.hero h1 { font-size: clamp(2.2rem, 4vw, 3rem); line-height: 1; }
.subtitle, .card-head p { margin: .7rem 0 0; color: #60728f; font-size: 1rem; }
.stats-grid, .content-grid, .function-grid, .form-grid { display: grid; gap: 1rem; }
.stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 1.5rem; }
.content-grid { grid-template-columns: minmax(320px, .95fr) minmax(420px, 1.35fr); margin-bottom: 1.5rem; }
.function-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.form-grid { margin: 1rem 0 1.25rem; }
.stats-card, .card, .modal-card { background: rgba(255,255,255,.94); border: 1px solid rgba(219,228,240,.9); border-radius: 1.25rem; box-shadow: 0 24px 50px rgba(15,23,42,.08); }
.stats-card { padding: 1.15rem 1.25rem; display: flex; gap: .9rem; align-items: center; }
.stats-card i { width: 3rem; height: 3rem; border-radius: 1rem; display: grid; place-items: center; color: #fff; font-size: 1.1rem; background: linear-gradient(135deg, #2563eb, #1d4ed8); }
.stats-card:nth-child(2) i { background: linear-gradient(135deg, #16a34a, #15803d); }
.stats-card:nth-child(3) i { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
.stats-card:nth-child(4) i { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stats-card span, .muted, .permission-item__aside span { color: #64748b; font-size: .88rem; }
.stats-card strong { display: block; margin-top: .2rem; color: #0f172a; font-size: 1.4rem; }
.card, .modal-card { padding: 1.2rem; }
.card-head-stack { align-items: flex-start; }
.toolbar { margin: 1rem 0 1.15rem; }
.toolbar-stack { align-items: stretch; }
.search-box { flex: 1; display: flex; align-items: center; gap: .75rem; min-height: 3rem; padding: 0 1rem; border: 1px solid #dbe4f0; border-radius: .95rem; background: #fff; color: #64748b; }
.search-box input { width: 100%; border: none; outline: none; background: transparent; color: #0f172a; font-size: .96rem; }
.btn, .icon-btn { border: none; cursor: pointer; transition: .2s ease; }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: .65rem; min-height: 3rem; padding: 0 1.1rem; border-radius: .95rem; font-weight: 800; }
.btn-small { min-height: 2.75rem; }
.btn-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; box-shadow: 0 12px 24px rgba(37,99,235,.22); }
.btn-secondary { background: rgba(37,99,235,.09); color: #1d4ed8; }
.btn-ghost { border: 1px solid #dbe4f0; background: rgba(255,255,255,.88); color: #334155; }
.btn-danger { background: rgba(239,68,68,.1); color: #b91c1c; }
.btn:disabled, .icon-btn:disabled { opacity: .65; cursor: not-allowed; box-shadow: none; }
.role-list, .permission-list { display: grid; gap: .85rem; }
.role-item, .permission-item, .function-card { width: 100%; border: 1px solid #e2e8f0; border-radius: 1rem; background: #fff; }
.role-item { padding: 1rem; text-align: left; }
.role-item.is-selected { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
.role-item__top,
.role-item__bottom {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  align-items: center;
  column-gap: 1rem;
}
.role-item__bottom { margin-top: .8rem; }
.role-item__top > :last-child,
.role-item__bottom > :last-child { justify-self: end; }
.actions-inline { display: flex; width: 100%; gap: .45rem; justify-content: center; align-items: center; flex-wrap: nowrap; }
.icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: .85rem; background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; }
.icon-btn-danger { color: #b91c1c; background: rgba(254,242,242,.96); border-color: rgba(252,165,165,.55); }
.pill { display: inline-flex; align-items: center; justify-content: center; min-height: 1.9rem; padding: 0 .8rem; border-radius: 999px; font-size: .78rem; font-weight: 800; }
.pill-success { background: rgba(34,197,94,.14); color: #15803d; }
.pill-muted { background: rgba(148,163,184,.14); color: #475569; }
.pill-info { background: rgba(37,99,235,.12); color: #1d4ed8; }
.permission-item, .function-card { padding: 1rem; }
.permission-item__copy strong, .function-card__top strong, .role-item strong { display: block; color: #0f172a; font-size: 1rem; }
.permission-item__copy p, .function-card p { margin: .4rem 0 0; color: #64748b; font-size: .92rem; line-height: 1.5; }
.permission-item__aside { align-items: flex-end; flex-wrap: wrap; }
.mapping-actions { display: inline-flex; align-items: center; gap: .45rem; }
.switch { position: relative; display: inline-flex; width: 54px; height: 30px; }
.switch input { opacity: 0; width: 0; height: 0; }
.switch.is-disabled { opacity: .65; }
.slider { position: absolute; inset: 0; border-radius: 999px; background: #cbd5e1; transition: background-color .2s ease; }
.slider::before { content: ""; position: absolute; width: 22px; height: 22px; left: 4px; top: 4px; border-radius: 50%; background: #fff; box-shadow: 0 4px 10px rgba(15,23,42,.16); transition: transform .2s ease; }
.switch input:checked + .slider { background: #2563eb; }
.switch input:checked + .slider::before { transform: translateX(24px); }
.empty-state { min-height: 220px; display: grid; place-items: center; gap: .65rem; text-align: center; border: 1px dashed #dbe4f0; border-radius: 1rem; color: #64748b; }
.empty-state-small { min-height: 170px; }
.empty-state i { font-size: 1.4rem; color: #94a3b8; }
.modal-overlay { position: fixed; inset: 0; z-index: 9999; padding: 1rem; background: rgba(15,23,42,.52); display: grid; place-items: center; }
.modal-card { width: min(100%, 460px); }
.modal-card-wide { width: min(100%, 680px); }
.field { display: grid; gap: .5rem; }
.field-full { grid-column: 1 / -1; }
.field span { color: #334155; font-weight: 700; }
.field input, .field select, .field textarea { width: 100%; min-height: 3rem; padding: .85rem 1rem; border-radius: .95rem; border: 1px solid #dbe4f0; background: #fff; color: #0f172a; outline: none; }
.field textarea { min-height: 7rem; resize: vertical; }
@media (max-width: 1200px) { .stats-grid, .function-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .content-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) {
  .permission-page { padding: 1rem; }
  .hero, .card-head, .toolbar, .permission-item, .permission-item__aside, .function-card__top, .function-card__actions, .modal-actions { flex-direction: column; align-items: stretch; }
  .role-item__top, .role-item__bottom { grid-template-columns: 1fr; row-gap: .75rem; }
  .role-item__top > :last-child, .role-item__bottom > :last-child { justify-self: start; }
  .stats-grid, .function-grid, .form-grid { grid-template-columns: 1fr; }
  .actions-inline { width: 100%; justify-content: flex-end; }
}
</style>

