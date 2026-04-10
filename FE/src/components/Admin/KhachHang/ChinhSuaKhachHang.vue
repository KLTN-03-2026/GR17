<template>
  <div class="customer-edit-page">
    <section class="editor-hero">
      <button class="back-button" type="button" @click="denTrangChiTiet">
        <i class="fas fa-arrow-left"></i>
      </button>

      <div class="editor-hero__copy">
        <p class="editor-hero__eyebrow">Quản trị khách hàng</p>
        <h1>Chỉnh sửa khách hàng</h1>
        <p>Cập nhật thông tin hồ sơ khách hàng trong hệ thống quản trị.</p>
        
      </div>

      <div class="editor-hero__actions">
        <button class="ghost-button" type="button" @click="layThongTinKhachHang" :disabled="dang_tai || dang_cap_nhat">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dang_tai ? "Đang tải..." : "Tải lại" }}</span>
        </button>

        <router-link class="secondary-link" :to="`/admin/customers/${ma_khach_hang}`">
          <i class="fas fa-eye"></i>
          <span>Xem chi tiết</span>
        </router-link>
      </div>
    </section>

    <div v-if="thong_bao_loi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thong_bao_loi }}</span>
    </div>

    <div v-if="thong_bao_ket_qua.text" :class="['notice', thong_bao_ket_qua.type === 'error' ? 'notice--error' : 'notice--success']">
      <i :class="thong_bao_ket_qua.type === 'error' ? 'fas fa-circle-xmark' : 'fas fa-circle-check'"></i>
      <span>{{ thong_bao_ket_qua.text }}</span>
    </div>

    <section v-if="dang_tai" class="loading-shell">
      <div class="loading-card"></div>
      <div class="loading-card"></div>
    </section>

    <section v-else-if="thong_tin_khach_hang" class="editor-grid">
      <form class="editor-card" @submit.prevent="capNhatKhachHang">
        <header class="editor-card__head">
          <div class="editor-card__icon">
            <i class="fas fa-user-pen"></i>
          </div>
          <div>
            <h2>Thông tin có thể chỉnh sửa</h2>
            <p>Dữ liệu gửi lên đúng chuẩn API `PUT /api/khach-hang/profile/{maKhachHang}`.</p>
          </div>
        </header>

        <div class="field-grid">
          <label class="field--full">
            <span class="field__label">Họ và tên</span>
            <input v-model.trim="khach_hang.Ho_va_ten" type="text" class="field__control" placeholder="Nhập họ và tên khách hàng">
          </label>

          <label class="field">
            <span class="field__label">Mã khách hàng</span>
            <input :value="nhan_ma_khach_hang" type="text" class="field__control field__control--readonly" readonly>
          </label>

          <label class="field">
            <span class="field__label">Email</span>
            <input v-model.trim="khach_hang.Email" type="email" class="field__control" placeholder="Ví dụ: hotro@domain.com">
          </label>

          <label class="field">
            <span class="field__label">Số điện thoại</span>
            <input
              v-model.trim="khach_hang.so_dien_thoai"
              type="text"
              inputmode="numeric"
              maxlength="10"
              class="field__control"
              placeholder="Ví dụ: 0987654321"
            >
          </label>

          <label class="field">
            <span class="field__label">Ngày sinh</span>
            <input v-model="khach_hang.Ngay_sinh" type="date" class="field__control">
          </label>

          <label class="field">
            <span class="field__label">Giới tính</span>
            <select v-model="khach_hang.Gioi_tinh" class="field__control">
              <option value="male">Nam</option>
              <option value="female">Nữ</option>
            </select>
          </label>

          <label class="field">
            <span class="field__label">Trạng thái</span>
            <select v-model="khach_hang.is_block" class="field__control">
              <option :value="true">Hoạt động</option>
              <option :value="false">Bị khóa</option>
            </select>
          </label>
        </div>

        <div class="red-tips">
          <div class="red-tip">
            <span class="red-tip__label">Điểm cần kiểm tra</span>
            <strong>Số điện thoại có đúng chưa</strong>
          </div>
          <div class="red-tip">
            <span class="red-tip__label">Điểm cần kiểm tra</span>
            <strong>Ngày sinh đã khớp hồ sơ chưa</strong>
          </div>
          <div class="red-tip">
            <span class="red-tip__label">Điểm cần kiểm tra</span>
            <strong>Tài khoản có cần chỉnh sửa thêm không</strong>
          </div>
        </div>

        <footer class="editor-card__actions">
          <button class="ghost-button" type="button" @click="khoiPhucForm" :disabled="dang_cap_nhat || !du_lieu_goc">
            Khôi phục
          </button>
          <button class="primary-button" type="submit" :disabled="dang_cap_nhat">
            <i v-if="dang_cap_nhat" class="fas fa-spinner fa-spin"></i>
            <span>{{ dang_cap_nhat ? "Đang lưu..." : "Lưu thay đổi" }}</span>
          </button>
        </footer>
      </form>

      <aside class="summary-card">
        <div class="summary-card__profile">
          <div class="summary-card__avatar" :style="{ background: thong_tin_khach_hang.avatarGradient }">
            {{ thong_tin_khach_hang.initials }}
          </div>
          <div>
            <p class="summary-card__eyebrow">Xem trước hồ sơ</p>
            <h2>{{ khach_hang.Ho_va_ten || thong_tin_khach_hang.name }}</h2>
            <div class="summary-card__tags">
              <span class="tag tag--slate">{{ nhan_ma_khach_hang }}</span>
              <span :class="['tag', khach_hang.is_block ? 'tag--green' : 'tag--rose']">
                {{ khach_hang.is_block ? "Hoạt động" : "Bị khóa" }}
              </span>
            </div>
          </div>
        </div>

        <div class="summary-list">
          <div class="summary-row">
            <span>Email</span>
            <strong>{{ khach_hang.Email || "--" }}</strong>
          </div>
          <div class="summary-row">
            <span>Số điện thoại</span>
            <strong>{{ khach_hang.so_dien_thoai || "--" }}</strong>
          </div>
          <div class="summary-row">
            <span>Giới tính</span>
            <strong>{{ khach_hang.Gioi_tinh === "male" ? "Nam" : "Nữ" }}</strong>
          </div>
          <div class="summary-row">
            <span>Ngày sinh</span>
            <strong>{{ nhan_ngay_sinh }}</strong>
          </div>
          <div class="summary-row">
            <span>Ngày cập nhật hệ thống</span>
            <strong>{{ thong_tin_khach_hang.updatedAtLabel }}</strong>
          </div>
        </div>

      </aside>
    </section>

    <section v-else class="empty-state">
      <i class="fas fa-user-slash"></i>
      <h2>Không tìm thấy khách hàng</h2>
      <p>Mã khách hàng này hiện không có trong hệ thống hoặc đã bị thay đổi.</p>
      <router-link class="secondary-link" to="/admin/customers">Quay về danh sách</router-link>
    </section>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient.js";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position: "top-right" });

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
  name: "ChinhSuaKhachHang",
  data() {
    return {
      dang_tai: false,
      dang_cap_nhat: false,
      thong_bao_loi: "",
      thong_bao_ket_qua: {
        type: "success",
        text: "",
      },
      thong_tin_khach_hang: null,
      du_lieu_goc: null,
      khach_hang: {
        Ho_va_ten: "",
        Email: "",
        so_dien_thoai: "",
        Ngay_sinh: "",
        Gioi_tinh: "male",
        is_block: true,
      },
    };
  },
  computed: {
    ma_khach_hang() {
      return this.chuanHoaMaKhachHang(this.$route.params.id);
    },
    nhan_ma_khach_hang() {
      return this.ma_khach_hang ? `KH ${this.ma_khach_hang}` : "--";
    },
    nhan_ngay_sinh() {
      return this.khach_hang.Ngay_sinh ? this.dinhDangNgayHienThi(this.khach_hang.Ngay_sinh) : "--";
    },
  },
  watch: {
    "$route.params.id": {
      immediate: true,
      handler() {
        this.layThongTinKhachHang();
      },
    },
  },
  methods: {
    chuanHoaMaKhachHang(value) {
      return String(value || "")
        .trim()
        .replace(/^KH[-\s]*/i, "");
    },
    taoTenVietTat(name) {
      return String(name || "KH")
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() || "")
        .join("");
    },
    layMauBacGiao(id) {
      const gradients = [
        "linear-gradient(135deg, #4338ca 0%, #2563eb 100%)",
        "linear-gradient(135deg, #0891b2 0%, #0f766e 100%)",
        "linear-gradient(135deg, #d97706 0%, #ea580c 100%)",
        "linear-gradient(135deg, #db2777 0%, #9333ea 100%)",
      ];
      const numericId = Number.parseInt(id, 10);
      const index = Number.isFinite(numericId) ? numericId % gradients.length : 0;
      return gradients[index];
    },
    dinhDangNgayHienThi(value) {
      if (!value) return "--";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "--";
      return date.toLocaleDateString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    },
    dinhDangNgayChoInput(value) {
      if (!value) return "";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "";
      const year = date.getFullYear();
      const month = `${date.getMonth() + 1}`.padStart(2, "0");
      const day = `${date.getDate()}`.padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    dinhDangNgayGuiAPI(value) {
      if (!value) return "";
      const [year, month, day] = value.split("-");
      if (!year || !month || !day) return "";
      return `${day}/${month}/${year}`;
    },
    chuanHoaBoolean(value, fallback = false) {
      if (typeof value === "boolean") return value;
      if (typeof value === "number") return value !== 0;
      if (typeof value === "string") {
        const normalized = value.trim().toLowerCase();
        if (["1", "true", "yes", "on"].includes(normalized)) return true;
        if (["0", "false", "no", "off", ""].includes(normalized)) return false;
      }
      return fallback;
    },
    layDuLieuKhachHang(payload) {
      return payload?.data?.data || payload?.data || payload;
    },
    mappingKhachHang(item) {
      const id = String(item?.Ma_khach_hang ?? item?.id ?? "");
      const is_block = this.chuanHoaBoolean(item?.is_block);
      const genderKey = item?.Gioi_tinh ? "male" : "female";

      return {
        id,
        name: item?.Ho_va_ten || "Khách hàng",
        email: item?.Email || "",
        phone: item?.so_dien_thoai || "",
        birthDate: this.dinhDangNgayChoInput(item?.Ngay_sinh),
        gender: genderKey,
        is_block,
        statusLabel: is_block ? "Hoạt động" : "Bị khóa",
        updatedAtLabel: this.dinhDangNgayHienThi(item?.updated_at),
        initials: this.taoTenVietTat(item?.Ho_va_ten),
        avatarGradient: this.layMauBacGiao(id),
      };
    },
    duaDuLieuVaoForm(customer) {
      this.khach_hang = {
        Ho_va_ten: customer.name,
        Email: customer.email,
        so_dien_thoai: customer.phone,
        Ngay_sinh: customer.birthDate,
        Gioi_tinh: customer.gender,
        is_block: customer.is_block,
      };
      this.du_lieu_goc = JSON.parse(JSON.stringify(this.khach_hang));
    },
    khoiPhucForm() {
      if (!this.du_lieu_goc) return;
      this.thong_bao_ket_qua = { type: "success", text: "" };
      this.khach_hang = JSON.parse(JSON.stringify(this.du_lieu_goc));
    },
    async layThongTinKhachHang() {
      if (!this.ma_khach_hang) {
        this.thong_tin_khach_hang = null;
        this.thong_bao_loi = "Thiếu mã khách hàng để tải dữ liệu.";
        return;
      }

      this.dang_tai = true;
      this.thong_bao_loi = "";
      this.thong_bao_ket_qua = { type: "success", text: "" };

      try {
        const res = await goiDuLieu(`/api/khach-hang/profile/${encodeURIComponent(this.ma_khach_hang)}`);
        const record = this.layDuLieuKhachHang(res);
        this.thong_tin_khach_hang = this.mappingKhachHang(record);
        this.duaDuLieuVaoForm(this.thong_tin_khach_hang);
      } catch (err) {
        console.error(err);
        this.thong_tin_khach_hang = null;
        this.du_lieu_goc = null;
        this.thong_bao_loi = err.response?.data?.message || "Không thể tải dữ liệu khách hàng.";
      } finally {
        this.dang_tai = false;
      }
    },
    async capNhatKhachHang() {
      this.thong_bao_ket_qua = { type: "success", text: "" };
      this.thong_bao_loi = "";
      this.dang_cap_nhat = true;

      const body = {
        Ho_va_ten: this.khach_hang.Ho_va_ten,
        Email: this.khach_hang.Email,
        Gioi_tinh: this.khach_hang.Gioi_tinh === "male",
        is_block: this.khach_hang.is_block,
      };

      if (this.khach_hang.so_dien_thoai) {
        body.so_dien_thoai = this.khach_hang.so_dien_thoai;
      }

      if (this.khach_hang.Ngay_sinh) {
        body.Ngay_sinh = this.dinhDangNgayGuiAPI(this.khach_hang.Ngay_sinh);
      }

      try {
        await goiDuLieu(`/api/khach-hang/profile/${encodeURIComponent(this.ma_khach_hang)}`, {
          method: "PUT",
          body,
          headers: {
            Authorization: "Bearer " + localStorage.getItem("chia_khoa_16"),
          },
        });
        toaster.success("Cập nhật thành công!");
        this.layThongTinKhachHang();
        this.thong_bao_ket_qua = {
          type: "success",
          text: "Cập nhật khách hàng thành công.",
        };
      } catch (err) {
        console.error(err);
        toaster.error("Lỗi: " + (err.response?.data?.message || "Không thể lưu thay đổi."));
        this.thong_bao_ket_qua = {
          type: "error",
          text: err.response?.data?.message || "Không thể lưu thay đổi.",
        };
      } finally {
        this.dang_cap_nhat = false;
      }
    },
    denTrangChiTiet() {
      this.$router.push(`/admin/customers/${this.ma_khach_hang}`);
    },
  },
};
</script>

<style scoped>
.customer-edit-page {
  min-height: calc(100vh - 120px);
  padding: 1.5rem;
  background:
    radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 28%),
    radial-gradient(circle at top right, rgba(14, 165, 233, 0.08), transparent 22%),
    #f5f7fb;
}

.editor-hero,
.editor-card,
.summary-card,
.loading-card {
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.94);
  box-shadow: 0 22px 55px rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(10px);
}

.editor-hero {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 1rem;
  align-items: center;
  padding: 1.35rem 1.5rem;
  border-radius: 1.75rem;
}

.back-button,
.ghost-button,
.secondary-link,
.primary-button {
  min-height: 3rem;
  border-radius: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  font-weight: 800;
  transition: transform 0.2s ease;
}

.back-button {
  width: 3rem;
  border: none;
  background: #eef3ff;
  color: #2952d3;
}

.ghost-button,
.secondary-link {
  padding: 0 1rem;
  border: 1px solid #dbe4f0;
  background: #ffffff;
  color: #334155;
  text-decoration: none;
}

.primary-button {
  padding: 0 1.25rem;
  border: none;
  background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%);
  color: #ffffff;
}

.back-button:hover,
.ghost-button:hover,
.secondary-link:hover,
.primary-button:hover {
  transform: translateY(-1px);
}

.editor-hero__eyebrow,
.summary-card__eyebrow {
  margin: 0 0 0.45rem;
  color: #c86b1a;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.editor-hero__copy h1,
.editor-card__head h2,
.summary-card__profile h2,
.editor-hero__copy p,
.editor-card__head p,
.summary-panel ul {
  margin: 0.45rem 0 0;
  color: #5f6c82;
  line-height: 1.7;
}

.editor-hero__actions {
  display: flex;
  gap: 0.8rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.notice {
  margin-top: 1rem;
  padding: 0.95rem 1.1rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  font-weight: 700;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}

.notice--success {
  background: #ecfdf3;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.loading-shell {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: 1.4fr 0.9fr;
  gap: 1rem;
}

.loading-card {
  min-height: 280px;
  border-radius: 1.6rem;
  position: relative;
  overflow: hidden;
}

.loading-card::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
  transform: translateX(-100%);
  animation: shimmer 1.4s infinite;
}

.editor-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.95fr);
  gap: 1rem;
}

.editor-card,
.summary-card {
  border-radius: 1.75rem;
}

.editor-card {
  padding: 1.4rem;
}

.editor-card__head {
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.editor-card__icon {
  width: 3rem;
  height: 3rem;
  border-radius: 1rem;
  display: grid;
  place-items: center;
  background: #eef2ff;
  color: #4338ca;
}

.field-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.field {
  display: grid;
  gap: 0.45rem;
}

.field--full {
  grid-column: 1 / -1;
}

.field__label {
  color: #344256;
  font-weight: 800;
}

.field__control {
  min-height: 3.2rem;
  padding: 0.85rem 1rem;
  border-radius: 1rem;
  border: 1px solid #d7dfeb;
  background: #ffffff;
  color: #172033;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.field__control:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
}

.field__control--readonly {
  background: #f7f9fc;
  color: #64748b;
}

.field__hint,
.field__error {
  font-size: 0.88rem;
}

.field__hint {
  color: #64748b;
}

.field__error {
  color: #dc2626;
  font-weight: 700;
}

.form-note {
  margin-top: 1rem;
  padding: 0.95rem 1rem;
  border-radius: 1rem;
  display: flex;
  align-items: flex-start;
  gap: 0.7rem;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.red-tips {
  margin-top: 0.95rem;
  display: grid;
  gap: 0.75rem;
}

.red-tip {
  padding: 0.9rem 1rem;
  border-radius: 1rem;
  border: 1px solid #fecaca;
  background: linear-gradient(135deg, #fff1f2 0%, #ffffff 100%);
}

.red-tip__label {
  display: block;
  color: #ef4444;
  font-size: 0.84rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.red-tip strong {
  display: block;
  margin-top: 0.35rem;
  color: #7f1d1d;
}

.editor-card__actions {
  margin-top: 1.2rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.8rem;
  flex-wrap: wrap;
}

.summary-card {
  padding: 1.35rem;
  display: grid;
  gap: 1rem;
  align-content: start;
}

.summary-card__profile {
  display: flex;
  gap: 0.9rem;
  align-items: center;
}

.summary-card__avatar {
  width: 4.75rem;
  height: 4.75rem;
  border-radius: 1.4rem;
  display: grid;
  place-items: center;
  color: #ffffff;
  font-size: 1.5rem;
  font-weight: 800;
}

.summary-card__tags {
  margin-top: 0.75rem;
  display: flex;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.tag {
  min-height: 2.1rem;
  padding: 0 0.85rem;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 800;
}

.tag--green {
  background: #e9fbef;
  color: #0f9f57;
}

.tag--rose {
  background: #fff1f2;
  color: #e11d48;
}

.tag--slate {
  background: #f1f5f9;
  color: #475569;
}

.summary-list,
.summary-panel {
  border-radius: 1.2rem;
  border: 1px solid #e6ebf4;
  background: #fbfcff;
}

.summary-row {
  padding: 0.95rem 1rem;
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  border-bottom: 1px solid #edf2f7;
}

.summary-row:last-child {
  border-bottom: none;
}

.summary-row span {
  color: #64748b;
}

.summary-row strong {
  color: #172033;
  text-align: right;
}

.empty-state {
  margin-top: 1rem;
  padding: 3rem 1.5rem;
  border-radius: 1.75rem;
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.94);
  box-shadow: 0 22px 55px rgba(15, 23, 42, 0.08);
  text-align: center;
}

.empty-state i {
  width: 4.5rem;
  height: 4.5rem;
  margin: 0 auto 1rem;
  border-radius: 1.4rem;
  display: grid;
  place-items: center;
  background: #f4f7fb;
  color: #3452d1;
  font-size: 1.4rem;
}

.empty-state h2 {
  margin: 0;
  color: #172033;
}

.empty-state p {
  margin: 0.65rem 0 1.2rem;
  color: #5f6c82;
  line-height: 1.7;
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

@media (max-width: 1180px) {
  .editor-grid,
  .loading-shell {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 860px) {
  .customer-edit-page {
    padding: 1rem;
  }

  .editor-hero {
    grid-template-columns: 1fr;
    justify-items: flex-start;
  }

  .editor-hero__actions,
  .field-grid,
  .summary-row {
    grid-template-columns: 1fr;
  }

  .summary-row {
    display: grid;
  }

  .summary-row strong {
    text-align: left;
  }

  .editor-card__actions {
    justify-content: stretch;
  }

  .editor-card__actions .ghost-button,
  .editor-card__actions .primary-button {
    width: 100%;
  }
}
</style>

