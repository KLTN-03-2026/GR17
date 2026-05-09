<template>
  <teleport to="body">
    <transition name="budget-modal-fade">
      <div v-if="show" class="budget-modal" @click.self="close">
        <section class="budget-modal__panel" role="dialog" aria-modal="true" aria-labelledby="budget-modal-title">
          <header class="budget-modal__header">
            <div>
              <p class="budget-modal__eyebrow">Ước tính kế hoạch</p>
              <h2 id="budget-modal-title">Chi tiết ngân sách</h2>
            </div>
            <button type="button" class="budget-modal__close" aria-label="Đóng" @click="close">
              <i class="fas fa-times"></i>
            </button>
          </header>

          <div class="budget-modal__summary">
            <div class="budget-modal__metric">
              <span>Ngân sách dự kiến</span>
              <strong>{{ details.plannedBudgetLabel }}</strong>
            </div>
            <div class="budget-modal__metric">
              <span>Tổng ước tính</span>
              <strong>{{ details.effectiveTotalLabel }}</strong>
            </div>
            <div class="budget-modal__metric" :class="{ 'budget-modal__metric--danger': details.isOverBudget }">
              <span>{{ details.isOverBudget ? "Vượt ngân sách" : "Còn lại" }}</span>
              <strong>{{ details.remainingLabel }}</strong>
            </div>
          </div>

          <ul class="budget-modal__rows">
            <li v-for="row in details.rows" :key="row.label" :class="row.tone ? `budget-modal__row--${row.tone}` : ''">
              <span>{{ row.label }}</span>
              <strong>{{ row.value }}</strong>
            </li>
          </ul>

          <div class="budget-modal__section-head">
            <h3>Chi phí theo hoạt động</h3>
            <span v-if="details.missingCostCount > 0">
              {{ details.missingCostCount }} hoạt động chưa có chi phí
            </span>
          </div>

          <div v-if="details.activityRows.length === 0" class="budget-modal__empty">
            Chưa có hoạt động để tính chi phí.
          </div>

          <div v-else class="budget-modal__activities">
            <article v-for="activity in details.activityRows" :key="activity.id" class="budget-modal__activity">
              <div class="budget-modal__activity-main">
                <div>
                  <h4>{{ activity.name }}</h4>
                  <p>{{ activity.dayTitle }}<span v-if="activity.time"> • {{ activity.time }}</span></p>
                </div>
                <strong>{{ activity.totalLabel }}</strong>
              </div>

              <p v-if="activity.note" class="budget-modal__note">
                {{ activity.note || "Chưa có chi phí chi tiết cho hoạt động này" }}
              </p>

              <dl v-else class="budget-modal__breakdown">
                <div v-if="activity.baseCost > 0">
                  <dt>Chi phí địa điểm</dt>
                  <dd>{{ activity.baseLabel }}</dd>
                </div>
                <div v-for="service in activity.services" :key="service.id || service.name">
                  <dt>{{ service.name }}</dt>
                  <dd>{{ service.value }}</dd>
                </div>
              </dl>
            </article>
          </div>
        </section>
      </div>
    </transition>
  </teleport>
</template>

<script>
import { buildBudgetDetails } from "../KhachHang/KeHoach/planShared";

export default {
  name: "BudgetDetailModal",
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    plan: {
      type: Object,
      default: () => ({}),
    },
    timeline: {
      type: Array,
      default: () => [],
    },
    budgetRows: {
      type: Object,
      default: null,
    },
  },
  emits: ["update:show"],
  computed: {
    details() {
      return this.budgetRows || buildBudgetDetails(this.plan, this.timeline);
    },
  },
  methods: {
    close() {
      this.$emit("update:show", false);
    },
  },
};
</script>

<style scoped>
.budget-modal {
  position: fixed;
  inset: 0;
  z-index: 5000;
  display: grid;
  place-items: center;
  padding: 24px;
  background: rgba(15, 23, 42, 0.55);
}

.budget-modal__panel {
  width: min(760px, 100%);
  max-height: min(86vh, 760px);
  overflow: auto;
  border-radius: 18px;
  background: #ffffff;
  box-shadow: 0 26px 80px rgba(15, 23, 42, 0.3);
}

.budget-modal__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  padding: 28px 30px 18px;
  border-bottom: 1px solid #e2e8f0;
}

.budget-modal__eyebrow {
  margin: 0 0 6px;
  color: #64748b;
  font-size: 0.76rem;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.budget-modal__header h2 {
  margin: 0;
  color: #0f172a;
  font-size: 1.55rem;
  line-height: 1.2;
}

.budget-modal__close {
  display: inline-grid;
  width: 40px;
  height: 40px;
  place-items: center;
  border: 0;
  border-radius: 999px;
  background: #f1f5f9;
  color: #475569;
  cursor: pointer;
}

.budget-modal__summary {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
  padding: 22px 30px 14px;
}

.budget-modal__metric {
  padding: 16px;
  border: 1px solid #dbeafe;
  border-radius: 14px;
  background: #f8fbff;
}

.budget-modal__metric span,
.budget-modal__rows span,
.budget-modal__section-head span {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 700;
}

.budget-modal__metric strong {
  display: block;
  margin-top: 8px;
  color: #0f4f73;
  font-size: 1.2rem;
}

.budget-modal__metric--danger strong,
.budget-modal__row--danger strong {
  color: #dc2626;
}

.budget-modal__row--success strong {
  color: #15803d;
}

.budget-modal__rows {
  display: grid;
  gap: 10px;
  margin: 0;
  padding: 0 30px 22px;
  list-style: none;
}

.budget-modal__rows li {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 12px 0;
  border-bottom: 1px solid #e2e8f0;
}

.budget-modal__rows strong {
  color: #0f172a;
}

.budget-modal__section-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 0 30px 12px;
}

.budget-modal__section-head h3 {
  margin: 0;
  color: #0f172a;
  font-size: 1.05rem;
}

.budget-modal__empty {
  margin: 0 30px 30px;
  padding: 18px;
  border: 1px dashed #cbd5e1;
  border-radius: 12px;
  color: #64748b;
  font-weight: 700;
}

.budget-modal__activities {
  display: grid;
  gap: 12px;
  padding: 0 30px 30px;
}

.budget-modal__activity {
  padding: 16px;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  background: #ffffff;
}

.budget-modal__activity-main {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
}

.budget-modal__activity h4 {
  margin: 0 0 5px;
  color: #0f172a;
  font-size: 0.98rem;
}

.budget-modal__activity p {
  margin: 0;
  color: #64748b;
  font-size: 0.86rem;
}

.budget-modal__activity-main > strong {
  color: #0f4f73;
  white-space: nowrap;
}

.budget-modal__note {
  margin-top: 12px !important;
  padding: 10px 12px;
  border-radius: 10px;
  background: #f8fafc;
  font-weight: 700;
}

.budget-modal__breakdown {
  display: grid;
  gap: 8px;
  margin: 14px 0 0;
}

.budget-modal__breakdown div {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  color: #475569;
  font-size: 0.88rem;
}

.budget-modal__breakdown dt,
.budget-modal__breakdown dd {
  margin: 0;
}

.budget-modal__breakdown dd {
  color: #0f172a;
  font-weight: 800;
}

.budget-modal-fade-enter-active,
.budget-modal-fade-leave-active {
  transition: opacity 0.18s ease;
}

.budget-modal-fade-enter-from,
.budget-modal-fade-leave-to {
  opacity: 0;
}

@media (max-width: 720px) {
  .budget-modal {
    padding: 12px;
  }

  .budget-modal__summary {
    grid-template-columns: 1fr;
  }

  .budget-modal__header,
  .budget-modal__summary,
  .budget-modal__rows,
  .budget-modal__section-head,
  .budget-modal__activities {
    padding-left: 18px;
    padding-right: 18px;
  }

  .budget-modal__activity-main {
    display: grid;
  }
}
</style>
