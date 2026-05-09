<template>
  <section class="feature-shell">
    <div class="feature-shell__inner">
      <header class="feature-shell__hero">
        <p class="feature-shell__eyebrow">Khung Giao Diện</p>
        <h1>{{ title }}</h1>
        <p class="feature-shell__description">{{ description }}</p>
      </header>

      <div class="feature-shell__grid">
        <article class="feature-card">
          <h2>API liên quan</h2>
          <ul v-if="endpoints.length" class="feature-list">
            <li v-for="endpoint in endpoints" :key="endpoint">{{ endpoint }}</li>
          </ul>
          <p v-else class="feature-card__empty">Chưa có endpoint được khai báo.</p>
        </article>

        <article class="feature-card">
          <h2>Gợi ý tiếp theo</h2>
          <ul v-if="notes.length" class="feature-list">
            <li v-for="note in notes" :key="note">{{ note }}</li>
          </ul>
          <p v-else class="feature-card__empty">Chưa có ghi chú bổ sung.</p>
        </article>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "FeaturePlaceholder",
  props: {
    title: {
      type: String,
      required: true,
    },
    description: {
      type: String,
      default: "",
    },
    endpoints: {
      type: Array,
      default: () => [],
    },
    notes: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style scoped>
.feature-shell {
  min-height: calc(100vh - 120px);
  padding: 32px 20px 48px;
}

.feature-shell__inner {
  width: min(1120px, 100%);
  margin: 0 auto;
}

.feature-shell__hero {
  padding: 28px;
  border-radius: 24px;
  background: linear-gradient(135deg, #fff8ec 0%, #ffffff 55%, #eef6ff 100%);
  border: 1px solid #e7ebf3;
  box-shadow: 0 22px 55px rgba(15, 23, 42, 0.08);
}

.feature-shell__eyebrow {
  margin: 0 0 10px;
  color: #c26a1b;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.feature-shell__hero h1 {
  margin: 0;
  color: #172033;
  font-size: clamp(2rem, 3vw, 3rem);
  font-weight: 800;
  line-height: 1.05;
}

.feature-shell__description {
  margin: 14px 0 0;
  color: #5b6478;
  max-width: 760px;
  line-height: 1.7;
}

.feature-shell__grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
  margin-top: 22px;
}

.feature-card {
  padding: 24px;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid #e7ebf3;
  box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
}

.feature-card h2 {
  margin: 0 0 14px;
  color: #182133;
  font-size: 1.05rem;
  font-weight: 700;
}

.feature-list {
  margin: 0;
  padding-left: 18px;
  color: #4b5568;
  line-height: 1.7;
}

.feature-card__empty {
  margin: 0;
  color: #7a8497;
}

@media (max-width: 768px) {
  .feature-shell {
    padding: 20px 14px 36px;
  }

  .feature-shell__hero,
  .feature-card {
    padding: 20px;
  }

  .feature-shell__grid {
    grid-template-columns: 1fr;
  }
}
</style>
