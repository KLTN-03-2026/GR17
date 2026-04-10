<template>
  <div class="travel-form-container">
    <div class="form-decorative-blob blob-1"></div>
    <div class="form-decorative-blob blob-2"></div>

    <form class="travel-form" @submit.prevent="xuLyGui">
      <div class="form-group">
        <label class="form-label">
          <div class="dot bg-blue"></div>
          ĐIỂM ĐẾN
        </label>
        <div class="form-input-wrapper">
          <div class="icon-box">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <input 
            type="text" 
            placeholder="Bạn muốn đi đâu?" 
            v-model="duLieuBieuMau.destination" 
            required
            class="tf-input"
          />
        </div>
      </div>

      <div class="form-divider"></div>

      <div class="form-group">
        <label class="form-label">
          <div class="dot bg-blue"></div>
          SỐ NGÀY
        </label>
        <div class="form-input-wrapper">
          <div class="icon-box">
            <i class="far fa-calendar-alt"></i>
          </div>
          <div class="duration-wrapper">
            <input 
              type="number" 
              :min="AI_MIN_DAYS"
              :max="AI_MAX_DAYS"
              placeholder="Ví dụ: 5" 
              v-model="duLieuBieuMau.duration" 
              class="tf-input num-field"
            />
            <span class="tf-suffix">ngày</span>
          </div>
        </div>
      </div>

      <div class="form-divider"></div>

      <div class="form-group relative group">
        <label class="form-label">
          <div class="dot bg-blue"></div>
          NGÂN SÁCH
        </label>
        <div class="form-input-wrapper cursor-pointer dropdown-wrapper">
          <div class="icon-box">
            <i class="fas fa-coins"></i>
          </div>
          <span class="tf-select-value">{{ duLieuBieuMau.budget }}</span>
          <i class="fas fa-chevron-down arrow-down"></i>
          <select v-model="duLieuBieuMau.budget" class="tf-select hidden-select">
            <option
              v-for="budgetOption in danhSachNganSach"
              :key="budgetOption"
              :value="budgetOption"
            >
              {{ budgetOption }}
            </option>
          </select>
        </div>
      </div>

      <div class="form-submit-wrapper">
        <button type="submit" class="tf-submit-btn" :disabled="dangTai">
          <template v-if="dangTai">
             <div class="tf-spinner"></div>
          </template>
          <template v-else>
            <div class="tf-btn-icon">
              <i class="fas fa-magic fa-beat-fade"></i>
            </div>
            <span>Lên Kế Hoạch AI</span>
          </template>
        </button>
      </div>
    </form>

    <div class="interests-container">
      <span class="interests-label">
        <i class="fas fa-check text-blue"></i>
        Sở thích của bạn:
      </span>
      <div class="interests-grid">
        <button 
          v-for="interest in danhSachSoThich" 
          :key="interest"
          type="button"
          class="interest-chip"
          :class="{'active': duLieuBieuMau.interests.includes(interest)}"
          @click="chuyenSoThich(interest)"
        >
          <i v-if="duLieuBieuMau.interests.includes(interest)" class="fas fa-check chip-icon"></i>
          {{ interest }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import {
  AI_BUDGET_OPTIONS,
  AI_INTEREST_OPTIONS,
  AI_MAX_DAYS,
  AI_MIN_DAYS,
  DEFAULT_AI_BUDGET,
  DEFAULT_AI_DAYS,
  DEFAULT_AI_INTERESTS,
} from "../../constants/aiPreferences";

export default {
  name: "TravelForm",
  props: {
    dangTai: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      AI_MIN_DAYS,
      AI_MAX_DAYS,
      danhSachSoThich: [...AI_INTEREST_OPTIONS],
      danhSachNganSach: [...AI_BUDGET_OPTIONS],
      duLieuBieuMau: {
        destination: '',
        duration: DEFAULT_AI_DAYS,
        budget: DEFAULT_AI_BUDGET,
        interests: [...DEFAULT_AI_INTERESTS]
      }
    };
  },
  methods: {
    chuanHoaSoNgay(value) {
      const soNgay = Number(value);
      if (!Number.isFinite(soNgay)) return AI_MIN_DAYS;
      return Math.min(AI_MAX_DAYS, Math.max(AI_MIN_DAYS, Math.trunc(soNgay)));
    },
    chuyenSoThich(interest) {
      if (this.duLieuBieuMau.interests.includes(interest)) {
        this.duLieuBieuMau.interests = this.duLieuBieuMau.interests.filter(i => i !== interest);
      } else {
        this.duLieuBieuMau.interests.push(interest);
      }
    },
    xuLyGui() {
      if (!this.duLieuBieuMau.destination) return;
      const duration = this.chuanHoaSoNgay(this.duLieuBieuMau.duration);
      this.duLieuBieuMau.duration = duration;
      this.$emit('gui-ke-hoach', { ...this.duLieuBieuMau, duration });
    }
  }
};
</script>

<style scoped>
.travel-form-container {
  max-width: 1100px;
  margin: 0 auto;
  position: relative;
  z-index: 20;
}

.form-decorative-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(40px);
  animation: pulse-glow 4s infinite alternate;
  z-index: -1;
}

.blob-1 {
  top: -40px;
  left: -40px;
  width: 120px;
  height: 120px;
  background-color: rgba(37, 99, 235, 0.2); 
}

.blob-2 {
  bottom: -40px;
  right: -40px;
  width: 150px;
  height: 150px;
  background-color: rgba(79, 70, 229, 0.2);
}

.travel-form {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: row;
  align-items: stretch;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(24px);
  border-radius: 40px;
  box-shadow: 0 32px 64px -16px rgba(0,0,0,0.1);
  border: 1px solid rgba(255,255,255,1);
  overflow: hidden;
  margin-bottom: 40px;
  transition: box-shadow 0.5s ease;
}

.travel-form:hover {
  box-shadow: 0 48px 80px -24px rgba(37, 99, 235, 0.15);
}

@media (max-width: 1024px) {
  .travel-form {
    flex-direction: column;
    border-radius: 24px;
  }
}

.form-group {
  flex: 1;
  padding: 32px;
  transition: background-color 0.3s ease;
  min-width: 0;
}

.form-group:hover {
  background-color: rgba(239, 246, 255, 0.4);
}

.form-divider {
  width: 1px;
  background-color: rgba(226, 232, 240, 0.5);
  margin: 20px 0;
}

@media (max-width: 1024px) {
  .form-divider {
    height: 1px;
    width: auto;
    margin: 0 20px;
  }
}

.form-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.65rem;
  font-weight: 900;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  margin-bottom: 12px;
}

.dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.bg-blue {
  background-color: #2563eb;
}
.text-blue {
  color: #2563eb;
}

.form-input-wrapper {
  display: flex;
  align-items: center;
  gap: 16px;
  width: 100%;
}

.icon-box {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background-color: #eff6ff;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.tf-input {
  width: 100%;
  background: transparent;
  border: none;
  outline: none;
  font-size: 1.125rem;
  font-weight: 800;
  color: #0f172a;
  min-width: 0;
}
.tf-input::placeholder {
  color: #cbd5e1;
  font-weight: 700;
}

.duration-wrapper {
  display: flex;
  align-items: center;
  flex: 1;
  gap: 8px;
}

.num-field {
  flex: none;
  width: 50px;
  min-width: 0;
  padding-right: 0;
}

.tf-suffix {
  font-size: 1.125rem;
  font-weight: 800;
  color: #0f172a;
  white-space: nowrap;
  flex: none;
}

.dropdown-wrapper {
  position: relative;
}

.tf-select-value {
  flex: 1;
  font-size: 1.125rem;
  font-weight: 800;
  color: #0f172a;
}
.arrow-down {
  color: #94a3b8;
  transition: color 0.3s;
}
.form-group:hover .arrow-down {
  color: #2563eb;
}

.hidden-select {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.form-submit-wrapper {
  padding: 24px;
  display: flex;
  align-items: center;
  background: rgba(248, 250, 252, 0.3);
}

@media (max-width: 1024px) {
  .form-submit-wrapper {
    background: transparent;
  }
}

.tf-submit-btn {
  width: 100%;
  height: 80px;
  padding: 0 40px;
  background: linear-gradient(135deg, #0b1d46 0%, #0f172a 55%, #1d4ed8 100%);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 24px;
  font-weight: 900;
  font-size: 1.05rem;
  letter-spacing: 0.01em;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  cursor: pointer;
  box-shadow: 0 22px 38px rgba(15, 23, 42, 0.28);
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  white-space: nowrap;
}

.tf-submit-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #0a1c43 0%, #1e3a8a 60%, #2563eb 100%);
  transform: translateY(-3px);
  box-shadow: 0 24px 44px rgba(37, 99, 235, 0.35);
}

.tf-submit-btn:disabled {
  opacity: 0.74;
  cursor: not-allowed;
}

.tf-btn-icon {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #f8fafc;
  transition: background 0.3s ease;
}
.tf-submit-btn:hover .tf-btn-icon {
  background: rgba(255, 255, 255, 0.28);
}

.tf-spinner {
  width: 28px;
  height: 28px;
  border: 3px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* INTERESTS */
.interests-container {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
  justify-content: center;
  background: rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(12px);
  padding: 24px;
  border-radius: 32px;
  border: 1px solid rgba(255, 255, 255, 0.6);
  box-shadow: 0 10px 30px rgba(0,0,0,0.03);
}

.interests-label {
  font-size: 0.65rem;
  font-weight: 900;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-right: 12px;
}

.interests-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
}

.interest-chip {
  padding: 10px 20px;
  border-radius: 16px;
  background: white;
  color: #64748b;
  border: 1px solid #f1f5f9;
  font-size: 0.8rem;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.interest-chip:hover {
  border-color: #bfdbfe;
  color: #2563eb;
}

.interest-chip.active {
  background: #2563eb;
  color: white;
  border-color: #2563eb;
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
  transform: scale(1.05);
}

.chip-icon {
  font-size: 0.75rem;
}
</style>
