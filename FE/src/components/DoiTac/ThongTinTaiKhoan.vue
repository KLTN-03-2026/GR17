<template>
  <PartnerShell
    title="Thông tin tài khoản"
    subtitle="Theo dõi thông tin tài khoản đối tác đang đăng nhập."
    :partner="partner"
    :loading="loading"
  >
    <template #headerActions>
      <button class="toolbar-btn toolbar-btn--ghost" type="button" @click="loadProfile">
        <i class="fas fa-rotate"></i>
        Làm mới
      </button>
    </template>

    <section v-if="errorMessage" class="alert-box">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </section>

    <section class="profile-card">
      <div class="profile-card__head">
        <div class="profile-avatar">
          <i class="fas fa-user-tie"></i>
        </div>
        <div>
          <h3>{{ displayName }}</h3>
          <p>{{ displaySubtitle }}</p>
        </div>
      </div>

      <div class="profile-grid">
        <article v-for="field in displayFields" :key="field.label" class="profile-item">
          <span>{{ field.label }}</span>
          <strong>{{ field.value }}</strong>
        </article>
      </div>
    </section>
  </PartnerShell>
</template>

<script>
import PartnerShell from "./layout/PartnerShell.vue";
import { fetchPartnerSession, formatDateTime } from "./shared/partnerApi";

function displayValue(value, fallback = "-") {
  const normalized = String(value ?? "").trim();
  return normalized || fallback;
}

export default {
  name: "PartnerThongTinTaiKhoan",
  components: {
    PartnerShell,
  },
  data() {
    return {
      loading: false,
      errorMessage: "",
      partner: null,
    };
  },
  computed: {
    displayName() {
      return (
        this.partner?.ten_doi_tac
        || this.partner?.ten_nguoi_dai_dien
        || "Tài khoản đối tác"
      );
    },
    displaySubtitle() {
      const maDoiTac = this.partner?.ma_doi_tac;
      if (maDoiTac) return `Mã đối tác: ${maDoiTac}`;
      return "Đối tác";
    },
    displayFields() {
      const partner = this.partner || {};
      return [
        { label: "Mã đối tác", value: displayValue(partner.ma_doi_tac) },
        { label: "Tên đối tác", value: displayValue(partner.ten_doi_tac) },
        { label: "Người đại diện", value: displayValue(partner.ten_nguoi_dai_dien) },
        { label: "Email", value: displayValue(partner.email) },
        { label: "Số điện thoại", value: displayValue(partner.so_dien_thoai || partner.sdt) },
        { label: "Địa chỉ", value: displayValue(partner.dia_chi) },
        { label: "Mã số thuế", value: displayValue(partner.ma_so_thue) },
        {
          label: "Ngày tạo tài khoản",
          value: partner.created_at ? formatDateTime(partner.created_at) : "-",
        },
      ];
    },
  },
  methods: {
    async loadProfile() {
      this.loading = true;
      this.errorMessage = "";
      try {
        this.partner = await fetchPartnerSession();
      } catch (error) {
        this.errorMessage = error?.message || "Không thể tải thông tin tài khoản đối tác.";
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.loadProfile();
  },
};
</script>

<style scoped>
.toolbar-btn {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  font-weight: 700;
  padding: 0 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.toolbar-btn:hover {
  background: #dbeafe;
  transform: translateY(-1px);
}

.alert-box {
  margin: 0.5rem 0;
  padding: 1rem 1.5rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #b91c1c;
  font-weight: 500;
}

.profile-card {
  border-radius: 1rem;
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
  padding: 1.2rem;
}

.profile-card__head {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e6ebf4;
}

.profile-avatar {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #93c5fd 0%, #3b82f6 100%);
  color: #ffffff;
  font-size: 1.3rem;
}

.profile-card__head h3 {
  margin: 0;
  color: #0f172a;
  font-size: 1.25rem;
  font-weight: 800;
}

.profile-card__head p {
  margin: 4px 0 0;
  color: #64748b;
  font-size: 0.9rem;
}

.profile-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.profile-item {
  min-height: 78px;
  border-radius: 0.85rem;
  border: 1px solid #e7edf7;
  background: #f8faff;
  padding: 0.75rem 0.9rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.profile-item span {
  color: #64748b;
  font-size: 0.8rem;
  font-weight: 700;
}

.profile-item strong {
  margin-top: 0.28rem;
  color: #0f172a;
  font-size: 0.96rem;
  font-weight: 800;
  line-height: 1.3;
}

@media (max-width: 900px) {
  .profile-grid {
    grid-template-columns: 1fr;
  }
}
</style>
