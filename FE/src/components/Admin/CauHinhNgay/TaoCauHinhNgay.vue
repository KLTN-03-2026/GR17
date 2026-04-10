<template>
  <div class="config-editor-page">
    <section class="editor-hero">
      <button class="back-button" type="button" @click="quayLai">
        <i class="fas fa-arrow-left"></i>
      </button>

      <div class="editor-hero__copy">
        <p class="editor-hero__eyebrow">Quản trị cấu hình ngày</p>
        <h1>Tạo cấu hình ngày</h1>
        <p>Thiết lập mới một ngày lễ, ngày kỷ niệm hoặc mốc đặc biệt trong hệ thống quản trị.</p>
      </div>

      <div class="editor-hero__actions">
        <router-link class="secondary-link" to="/admin/day-config">
          <i class="fas fa-table-list"></i>
          <span>Danh sách cấu hình</span>
        </router-link>
      </div>
    </section>

    <div v-if="saveMessage.text" :class="['notice', saveMessage.type === 'error' ? 'notice--error' : 'notice--success']">
      <i :class="saveMessage.type === 'error' ? 'fas fa-circle-xmark' : 'fas fa-circle-check'"></i>
      <span>{{ saveMessage.text }}</span>
    </div>

    <section class="editor-grid">
      <form class="editor-card" @submit.prevent="taoCauHinh">
        <header class="editor-card__head">
          <div class="editor-card__icon">
            <i class="fas fa-calendar-plus"></i>
          </div>
          <div>
            <h2>Thông tin cấu hình</h2>
            <p>Dữ liệu gửi lên đúng chuẩn API <code>POST /api/admin/cau-hinh-ngay</code>.</p>
          </div>
        </header>

        <div class="field-grid">
          <label class="field">
            <span class="field__label">Loại ngày</span>
            <select v-model.number="form.type" class="field__control">
              <option v-for="option in typeOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
            <small v-if="fieldErrors.loai_ngay_le" class="field__error">{{ fieldErrors.loai_ngay_le }}</small>
          </label>

          <label class="field">
            <span class="field__label">Ngày áp dụng</span>
            <input v-model="form.date" type="date" class="field__control">
            <small v-if="fieldErrors.ngay" class="field__error">{{ fieldErrors.ngay }}</small>
          </label>

          <label class="field field--full">
            <span class="field__label">Tên ngày</span>
            <input
              v-model.trim="form.name"
              type="text"
              class="field__control"
              placeholder="Ví dụ: Quốc khánh, Phụ nữ Việt Nam, Cuối tuần làm việc"
            >
            <small v-if="fieldErrors.ten_ngay_le" class="field__error">{{ fieldErrors.ten_ngay_le }}</small>
            <small class="field__hint">Có thể để trống nếu hệ thống chỉ cần lưu loại ngày và ngày áp dụng.</small>
          </label>
        </div>

        <div class="form-note">
          <i class="fas fa-circle-info"></i>
          <div>
            <strong>Quy ước loại ngày</strong>
            <p>Hệ thống hiện dùng các giá trị chuẩn 1, 2, 3 tương ứng với ngày lễ chính thức, ngày kỷ niệm và ngày đặc biệt.</p>
          </div>
        </div>

        <footer class="editor-card__actions">
          <button class="ghost-button" type="button" :disabled="dangLuu" @click="lamMoiForm">
            Làm mới biểu mẫu
          </button>
          <button class="primary-button" type="submit" :disabled="dangLuu">
            <i v-if="dangLuu" class="fas fa-spinner fa-spin"></i>
            <span>{{ dangLuu ? "Đang lưu..." : "Tạo cấu hình" }}</span>
          </button>
        </footer>
      </form>

      <aside class="summary-card">
        <div class="summary-card__profile">
          <div class="summary-card__avatar" :class="accentClass(typeMeta.accent)">
            <i class="fas fa-calendar-day"></i>
          </div>
          <div>
            <p class="summary-card__eyebrow">Xem trước cấu hình</p>
            <h2>{{ previewName }}</h2>
            <div class="summary-card__tags">
              <span :class="['tag', accentClass(typeMeta.accent, true)]">
                {{ typeMeta.label }}
              </span>
            </div>
          </div>
        </div>

        <div class="summary-list">
          <div class="summary-row">
            <span>Loại ngày</span>
            <strong>{{ typeMeta.label }}</strong>
          </div>
          <div class="summary-row">
            <span>Ngày áp dụng</span>
            <strong>{{ previewDate }}</strong>
          </div>
          <div class="summary-row">
            <span>Tên ngày</span>
            <strong>{{ previewName }}</strong>
          </div>
        </div>
      </aside>
    </section>
  </div>

</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import { API_BASE, DAY_TYPE_OPTIONS, formatDateDisplay, getDayTypeMeta } from "./dayConfigShared";

function createDefaultForm() {
  return {
    type: 1,
    name: "",
    date: "",
  };
}
export default {
  name: "TaoCauHinhNgay",
  data() {
    return {
      dangLuu: false,
      saveMessage: {
        type: "success",
        text: "",
      },
      fieldErrors: {},
      typeOptions: DAY_TYPE_OPTIONS,
      form: createDefaultForm(),
    };
  },
  computed: {
    typeMeta() {
      return getDayTypeMeta(this.form.type);
    },
    previewName() {
      return this.form.name || "Chưa đặt tên ngày";
    },
    previewDate() {
      return this.form.date ? formatDateDisplay(this.form.date) : "--";
    },
  },
  methods: {
    accentClass(accent, tagMode = false) {
      const prefix = tagMode ? "tag--" : "pill--";
      return `${prefix}${accent}`;
    },
    lamMoiForm() {
      this.form = createDefaultForm();
      this.fieldErrors = {};
      this.saveMessage = { type: "success", text: "" };
    },
    kiemTraForm() {
      const errors = {};

      if (![1, 2, 3].includes(Number(this.form.type))) {
        errors.loai_ngay_le = "Loại ngày phải thuộc một trong các giá trị 1, 2 hoặc 3.";
      }

      if (!this.form.date) {
        errors.ngay = "Vui lòng chọn ngày áp dụng.";
      }

      if (this.form.name && this.form.name.length > 255) {
        errors.ten_ngay_le = "Tên ngày không được vượt quá 255 ký tự.";
      }

      this.fieldErrors = errors;
      return Object.keys(errors).length === 0;
    },
    async taoCauHinh() {
      this.saveMessage = { type: "success", text: "" };

      if (!this.kiemTraForm()) {
        this.saveMessage = {
          type: "error",
          text: "Biểu mẫu còn thiếu hoặc sai dữ liệu. Vui lòng kiểm tra lại trước khi lưu.",
        };
        return;
      }

      this.dangLuu = true;

      try {
        const response = await goiApi(`${API_BASE}/admin/cau-hinh-ngay`, {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            loai_ngay_le: this.form.type,
            ten_ngay_le: this.form.name || null,
            ngay: this.form.date,
          }),
        });
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          const backendErrors = payload?.errors || {};
          this.fieldErrors = Object.fromEntries(
            Object.entries(backendErrors).map(([key, value]) => [key, Array.isArray(value) ? value[0] : value]),
          );
          throw new Error(payload?.message || "Không thể tạo cấu hình ngày.");
        }

        const newId = payload?.data?.ma_cau_hinh_ngay;
        this.saveMessage = {
          type: "success",
          text: "Tạo cấu hình ngày thành công.",
        };

        if (newId) {
          this.$router.push(`/admin/day-config/${newId}/edit`);
          return;
        }

        this.lamMoiForm();
      } catch (error) {
        this.saveMessage = {
          type: "error",
          text: error.message || "Không thể tạo cấu hình ngày.",
        };
      } finally {
        this.dangLuu = false;
      }
    },
    quayLai() {
      this.$router.push("/admin/day-config");
    },
  },
};
</script>

<style scoped>
.config-editor-page {
  min-height: calc(100vh - 120px);
  padding: 1.5rem;
  background:
    radial-gradient(circle at top left, rgba(37, 99, 235, 0.08), transparent 28%),
    radial-gradient(circle at top right, rgba(168, 85, 247, 0.08), transparent 22%),
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
.summary-card__profile h2 {
  margin: 0;
  color: #172033;
}

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
  grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.95fr);
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
  padding: 1rem 1.05rem;
  border-radius: 1rem;
  display: flex;
  align-items: flex-start;
  gap: 0.8rem;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.form-note strong {
  display: block;
}

.form-note p {
  margin: 0.25rem 0 0;
  line-height: 1.6;
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
  font-size: 1.5rem;
}

.summary-card__tags {
  margin-top: 0.75rem;
  display: flex;
  gap: 0.6rem;
  flex-wrap: wrap;
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

@media (max-width: 1180px) {
  .editor-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 860px) {
  .config-editor-page {
    padding: 1rem;
  }

  .editor-hero {
    grid-template-columns: 1fr;
    justify-items: flex-start;
  }

  .editor-hero__actions,
  .field-grid {
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





