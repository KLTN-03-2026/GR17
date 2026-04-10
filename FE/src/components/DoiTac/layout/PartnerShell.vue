<template>
  <div class="partner-backoffice-page">
    <section class="partner-backoffice-page__hero">
      <div>
        <p class="partner-backoffice-page__eyebrow">Bảng điều khiển đối tác</p>
        <h1>{{ title }}</h1>
        <p v-if="subtitle" class="partner-backoffice-page__subtitle">{{ subtitle }}</p>
      </div>
      <div class="partner-backoffice-page__actions">
        <slot name="headerActions"></slot>
      </div>
    </section>

    <section v-if="loading" class="partner-backoffice-page__loading">
      <i class="fas fa-spinner fa-spin"></i>
      <span>Đang tải dữ liệu...</span>
    </section>

    <section v-else class="partner-backoffice-page__content">
      <slot></slot>
    </section>
  </div>
</template>

<script>
export default {
  name: "PartnerShell",
  props: {
    title: {
      type: String,
      required: true,
    },
    subtitle: {
      type: String,
      default: "",
    },
    partner: {
      type: Object,
      default: null,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    itineraryPath: {
      type: String,
      default: "/doi-tac/quan-ly-tour",
    },
  },
};
</script>

<style scoped>
.partner-backoffice-page {
  min-height: 100%;
  padding: 1.5rem;
  background: #f8fafc;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.partner-backoffice-page__hero {
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

.partner-backoffice-page__eyebrow {
  margin: 0;
  color: #2563eb;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.partner-backoffice-page h1 {
  margin: 0.5rem 0 0;
  color: #0f172a;
  font-size: 2rem;
  font-weight: 900;
  line-height: 1.2;
}

.partner-backoffice-page__subtitle {
  margin: 0.5rem 0 0;
  color: #64748b;
  font-size: 1rem;
  max-width: 880px;
}

.partner-backoffice-page__actions {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 0.75rem;
}

.partner-backoffice-page__loading {
  min-height: 180px;
  border-radius: 1rem;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: #475569;
  font-weight: 700;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

.partner-backoffice-page__content {
  display: grid;
  gap: 1.5rem;
}

@media (max-width: 1024px) {
  .partner-backoffice-page__hero {
    flex-direction: column;
  }
  .partner-backoffice-page__actions {
    width: 100%;
    justify-content: flex-start;
  }
}
</style>
