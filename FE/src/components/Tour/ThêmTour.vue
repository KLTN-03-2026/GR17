<template>
  <div class="create-page">
    <section class="create-hero">
      <div>
        <p class="create-hero__eyebrow">Quản lý tour</p>
        <h1>Thêm Tour Mới</h1>
        <p class="create-hero__sub">Tạo nhanh một tour mới từ API `/tour` và chuyển ngay sang màn hình chi tiết để hoàn thiện nội dung.</p>
      </div>
      <router-link class="ghost-link" to="/tour-management">Quay lại danh sách</router-link>
    </section>

    <div v-if="message.text" :class="['notice', message.type === 'error' ? 'notice--error' : 'notice--success']">
      <i :class="message.type === 'error' ? 'fas fa-circle-exclamation' : 'fas fa-circle-check'"></i>
      <span>{{ message.text }}</span>
    </div>

    <section class="create-card">
      <div class="create-grid">
        <label class="field field--full">
          <span>Tên tour</span>
          <input v-model="form.name" type="text" placeholder="Ví dụ: Hành trình khám phá Vịnh Hạ Long">
        </label>

        <label class="field">
          <span>Danh mục</span>
          <select v-model="form.category">
            <option v-for="option in categoryOptions" :key="option" :value="option">{{ option }}</option>
          </select>
        </label>

        <label class="field">
          <span>Mức độ khó</span>
          <select v-model="form.difficulty">
            <option v-for="option in difficultyOptions" :key="option" :value="option">{{ option }}</option>
          </select>
        </label>

        <label class="field field--full">
          <span>Mô tả tour</span>
          <textarea v-model="form.description" rows="6" placeholder="Nhập mô tả ngắn gọn về tour..."></textarea>
        </label>

        <label class="field">
          <span>Giá gốc (VND)</span>
          <input v-model.number="form.basePrice" type="number" min="0">
        </label>

        <label class="field">
          <span>Giá khuyến mãi (VND)</span>
          <input v-model.number="form.salePrice" type="number" min="0">
        </label>

        <label class="field">
          <span>Số khách tối đa</span>
          <input v-model.number="form.maxGuests" type="number" min="1">
        </label>

        <label class="field">
          <span>Từ khóa SEO</span>
          <input v-model="seoInput" type="text" placeholder="vịnh hạ long, du thuyền...">
        </label>
      </div>

      <div class="create-actions">
        <button class="btn-secondary" type="button" @click="resetForm">Làm lại</button>
        <button class="btn-primary" type="button" :disabled="isSaving" @click="createTour">
          <i v-if="isSaving" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSaving ? "Đang tạo..." : "Tạo tour" }}</span>
        </button>
      </div>
    </section>
  </div>
</template>

<script>
import { goiApi } from "../../services/httpClient.js";

export default {
  name: "ThêmTour",
  data() {
    return {
      isSaving: false,
      seoInput: "",
      message: { type: "success", text: "" },
      categoryOptions: ["Nghỉ dưỡng & Biển", "Khám phá thiên nhiên", "Văn hóa & Di sản", "Phiêu lưu", "Gia đình"],
      difficultyOptions: ["Dễ", "Trung bình", "Trung bình (Người trẻ)", "Nâng cao"],
      form: this.createDefaultForm(),
    };
  },
  methods: {
    createDefaultForm() {
      return {
        name: "",
        category: "Nghỉ dưỡng & Biển",
        difficulty: "Trung bình",
        description: "",
        basePrice: 0,
        salePrice: 0,
        maxGuests: 10,
      };
    },
    buildHeaders() {
      const token = localStorage.getItem("token");
      const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
      };
      if (token) headers.Authorization = `Bearer ${token}`;
      return headers;
    },
    normalizeArray(value) {
      if (Array.isArray(value)) return value;
      if (typeof value === "string") return value.split(/\r?\n|,/).map((item) => item.trim()).filter(Boolean);
      return [];
    },
    resetForm() {
      this.form = this.createDefaultForm();
      this.seoInput = "";
      this.message = { type: "success", text: "" };
    },
    async createTour() {
      this.isSaving = true;
      this.message = { type: "success", text: "" };
      try {
        const payload = {
          ten_tour: this.form.name,
          danh_muc: this.form.category,
          muc_do_kho: this.form.difficulty,
          mo_ta: this.form.description,
          gia_goc: Number(this.form.basePrice) || 0,
          gia_khuyen_mai: Number(this.form.salePrice) || 0,
          so_khach_toi_da: Number(this.form.maxGuests) || 0,
          the_seo: this.normalizeArray(this.seoInput),
        };

        const response = await goiApi("/api/tour", {
          method: "POST",
          headers: this.buildHeaders(),
          body: JSON.stringify(payload),
        });
        const data = await response.json().catch(() => null);

        if (!response.ok) {
          throw new Error(data?.message || "Không thể tạo tour mới.");
        }

        const created = data?.data?.data || data?.data || data;
        const createdId = created?.id || created?.ID || created?.ma_tour || created?.Ma_tour;

        this.message = {
          type: "success",
          text: createdId
            ? "Tạo tour thành công. Đang chuyển sang trang chi tiết..."
            : "Tạo tour thành công.",
        };

        if (createdId) {
          setTimeout(() => {
            this.$router.push(`/tour-management/${createdId}/edit`);
          }, 700);
        } else {
          this.resetForm();
        }
      } catch (error) {
        this.message = {
          type: "error",
          text: `${error.message} Nếu backend chưa hỗ trợ POST, bạn vẫn có thể dùng giao diện này để hoàn thiện form.`,
        };
      } finally {
        this.isSaving = false;
      }
    },
  },
};
</script>

<style scoped>
.create-page{min-height:100%;padding:2rem;background:linear-gradient(180deg,#f7f8fd 0%,#eef2fb 100%)}
.create-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1.5rem}.create-hero__eyebrow{margin:0 0 .4rem;color:#4f46e5;text-transform:uppercase;letter-spacing:.16em;font-size:.8rem;font-weight:800}.create-hero h1{margin:0;font-size:2.8rem;line-height:1;font-weight:900;color:#111827}.create-hero__sub{margin:.55rem 0 0;color:#64748b;max-width:700px}
.ghost-link{display:inline-flex;align-items:center;justify-content:center;min-height:3rem;padding:0 1rem;border-radius:1rem;background:#fff;color:#374151;text-decoration:none;border:1px solid #dbe2f0;font-weight:700}
.notice{display:flex;align-items:center;gap:.75rem;padding:1rem;border-radius:1rem;margin-bottom:1rem}.notice--error{background:#fff1f2;color:#be123c}.notice--success{background:#ecfdf5;color:#047857}
.create-card{background:#fff;border:1px solid #e6eaf6;border-radius:1.6rem;padding:1.6rem;box-shadow:0 16px 32px rgba(31,41,55,.05)}.create-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.field{display:grid;gap:.55rem}.field--full{grid-column:1/-1}.field span{font-weight:700;color:#374151}.field input,.field select,.field textarea{min-height:3.25rem;border:1px solid #dbe2f0;border-radius:1rem;padding:.9rem 1rem;background:#fff;outline:none}.field textarea{min-height:10rem;resize:vertical}.field input:focus,.field select:focus,.field textarea:focus{border-color:#4f46e5;box-shadow:0 0 0 4px rgba(79,70,229,.08)}
.create-actions{display:flex;align-items:center;justify-content:flex-end;gap:1rem;margin-top:1.5rem}.btn-secondary,.btn-primary{min-height:3.2rem;border:0;border-radius:1rem;padding:.9rem 1.3rem;font-weight:800}.btn-secondary{background:#eef2ff;color:#4338ca}.btn-primary{display:inline-flex;align-items:center;gap:.65rem;background:linear-gradient(135deg,#4f46e5 0%,#2563eb 100%);color:#fff}
@media (max-width:768px){.create-page{padding:1rem}.create-hero,.create-grid{grid-template-columns:1fr;display:grid}.create-hero h1{font-size:2.2rem}.create-actions{justify-content:stretch;flex-direction:column}.btn-secondary,.btn-primary{width:100%;justify-content:center}}
</style>
