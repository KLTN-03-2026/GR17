<template>
  <div class="customer-edit-page">
    <section class="editor-hero">
      <router-link class="back-button" to="/admin/customers">
        <i class="fas fa-arrow-left"></i>
      </router-link>

      <div class="editor-hero__copy">
        <p class="editor-hero__eyebrow">Quản trị khách hàng</p>
        <h1>Thêm khách hàng</h1>
        <p>Tạo hồ sơ khách hàng mới vào hệ thống.</p>
      </div>
      <div class="editor-hero__actions">
        <!-- Optional extra actions -->
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

    <section class="editor-grid">
        <div class="field-grid">
          <label class="field--full">
            <span class="field__label">Họ và tên *</span>
            <input v-model.trim="khach_hang.Ho_va_ten" type="text" class="field__control" placeholder="Nhập họ và tên khách hàng">
          </label>
          <label class="field">
            <span class="field__label">Email *</span>
            <input v-model.trim="khach_hang.Email" type="email" class="field__control" placeholder="Ví dụ: email@domain.com">
          </label>
          <label class="field">
            <span class="field__label">Mật khẩu *</span>
            <input v-model="khach_hang.Mat_khau" type="password" class="field__control" placeholder="Tối thiểu 8 ký tự" autocomplete="new-password">
          </label>
          <label class="field">
            <span class="field__label">Số điện thoại *</span>
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
            <span class="field__label">Ngày sinh *</span>
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

      <form class="editor-card" @submit.prevent="themKhachHang">
        <header class="editor-card__head">
          <div class="editor-card__icon">
            <i class="fas fa-user-plus"></i>
          </div>
          <div>
            <h2>Thông tin tài khoản</h2>
            <p>Nhập thông tin bắt buộc để khởi tạo tài khoản mới.</p>
          </div>
        </header>
        <footer class="editor-card__actions">
          <button class="ghost-button" type="button" @click="lamMoiForm" :disabled="dang_luu">
            Làm lại
          </button>
          <button class="primary-button" type="submit" :disabled="dang_luu">
            <i v-if="dang_luu" class="fas fa-spinner fa-spin"></i>
            <span>{{ dang_luu ? "Đang tạo..." : "Tạo khách hàng" }}</span>
          </button>
        </footer>
      </form>

      <aside class="summary-card">
        <div class="summary-card__profile">
          <div class="summary-card__avatar" style="background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%)">
            {{ ten_viet_tat }}
          </div>
          <div>
            <p class="summary-card__eyebrow">Xem trước</p>
            <h2>{{ khach_hang.Ho_va_ten || "Tên khách hàng" }}</h2>
            <div class="summary-card__tags">
              <span class="tag tag--slate">ID Mới</span>
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
        </div>

      </aside>
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
  name: "ThemKhachHang",
  data() {
    return {
      dang_luu: false,
      thong_bao_loi: "",
      thong_bao_ket_qua: {
        type: "success",
        text: "",
      },
      khach_hang: {
        Ho_va_ten: "",
        Email: "",
        Mat_khau: "",
        so_dien_thoai: "",
        Ngay_sinh: "",
        Gioi_tinh: "male",
        is_block: true,
      },
      du_lieu_goc: {
        Ho_va_ten: "",
        Email: "",
        Mat_khau: "",
        so_dien_thoai: "",
        Ngay_sinh: "",
        Gioi_tinh: "male",
        is_block: true,
      }
    };
  },
  computed: {
    nhan_ngay_sinh() {
      if (!this.khach_hang.Ngay_sinh) return "--";
      const date = new Date(this.khach_hang.Ngay_sinh);
      if (Number.isNaN(date.getTime())) return "--";
      return date.toLocaleDateString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    },
    ten_viet_tat() {
      return String(this.khach_hang.Ho_va_ten || "KH")
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() || "")
        .join("");
    }
  },
  methods: {
    lamMoiForm() {
      this.thong_bao_ket_qua = { type: "success", text: "" };
      // cleared thong_bao_loi
      this.khach_hang = JSON.parse(JSON.stringify(this.du_lieu_goc));
    },
    dinhDangNgayGuiAPI(value) {
      if (!value) return "";
      const [year, month, day] = value.split("-");
      if (!year || !month || !day) return "";
      return `${day}/${month}/${year}`;
    },
    async themKhachHang() {
      this.thong_bao_ket_qua = { type: "success", text: "" };
      // cleared thong_bao_loi
      this.dang_luu = true;

      const du_lieu_gui = {
        ...this.khach_hang,
        Ngay_sinh: this.dinhDangNgayGuiAPI(this.khach_hang.Ngay_sinh),
        Gioi_tinh: this.khach_hang.Gioi_tinh === "male",
      };

      try {
        const res = await goiDuLieu("/api/admin/khach-hang", {
          method: "POST",
          body: du_lieu_gui,
        });
        if (res.data.status || res.data.success) {
          toaster.success("Thông báo: " + res.data.message);
          this.thong_bao_ket_qua = {
            type: "success",
            text: "Tạo khách hàng thành công.",
          };
          setTimeout(() => {
            this.$router.push("/admin/customers");
          }, 1500);
        } else {
          toaster.error("Lỗi: " + res.data.message);
          this.thong_bao_ket_qua = {
            type: "error",
            text: res.data.message || "Không thể tạo khách hàng.",
          };
        }
      } catch (err) {
        console.error(err);
        toaster.error("Lỗi hệ thống khi tạo khách hàng!");
        this.thong_bao_ket_qua = {
          type: "error",
          text: "Lỗi hệ thống khi tạo khách hàng!",
        };
      } finally {
        this.dang_luu = false;
      }
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
.summary-card {
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
  text-decoration: none;
}

.ghost-button {
  padding: 0 1rem;
  border: 1px solid #dbe4f0;
  background: #ffffff;
  color: #334155;
}

.primary-button {
  padding: 0 1.25rem;
  border: none;
  background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%);
  color: #ffffff;
}

.back-button:hover,
.ghost-button:hover,
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
.editor-card__head p {
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

.field__error {
  font-size: 0.88rem;
  color: #dc2626;
  font-weight: 700;
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

.summary-list {
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
  font-weight: 800;
}
</style>

