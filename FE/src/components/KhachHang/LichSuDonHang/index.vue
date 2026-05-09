<template>
  <div class="plan-page">
    <div class="plan-layout">
      <CustomerSidebar />

      <section class="plan-content">
        <div class="order-history-page">
          <header class="page-header card">
            <div class="page-header__copy">
              <p class="page-header__eyebrow">Hóa đơn và thanh toán</p>
              <h1>Đơn hàng và thanh toán</h1>
              <p>
                Theo dõi hóa đơn, mở lại mã QR và quay về đúng màn checkout khi bạn cần tiếp tục xử lý.
              </p>
            </div>

            <router-link to="/khach-hang/tour" class="page-header__cta">Đặt thêm tour</router-link>
          </header>

          <div v-if="errorMessage" class="notice notice--error">
            <i class="fas fa-circle-exclamation"></i>
            <span>{{ errorMessage }}</span>
          </div>

          <div v-if="loading" class="notice notice--info">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Đang tải danh sách hóa đơn...</span>
          </div>

          <template v-else>
            <section v-if="orders.length" class="summary-strip">
              <article v-for="item in summaryCards" :key="item.key" class="summary-strip__card">
                <span>{{ item.label }}</span>
                <strong>{{ item.value }}</strong>
              </article>
            </section>

            <section v-if="orders.length" class="orders-shell card">
              <div class="orders-shell__header">
                <div>
                  <h2>Danh sách hóa đơn</h2>
                  <p>{{ displayedOrders.length }} đơn phù hợp với bộ lọc hiện tại.</p>
                </div>
              </div>

              <div class="segmented-filter" role="tablist" aria-label="Bộ lọc trạng thái hóa đơn">
                <button
                  v-for="filter in filterOptions"
                  :key="filter.key"
                  type="button"
                  class="segmented-filter__option"
                  :class="{ 'is-active': activeFilter === filter.key }"
                  :aria-pressed="activeFilter === filter.key ? 'true' : 'false'"
                  @click="activeFilter = filter.key"
                >
                  <span>{{ filter.label }}</span>
                  <strong>{{ demTheoTrangThai(filter.key) }}</strong>
                </button>
              </div>

              <template v-if="displayedOrders.length">
                <div class="table-container">
                  <table class="history-table history-table--desktop">
                    <thead>
                      <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tour</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Hành động</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="order in displayedOrders" :key="order.ma_hoa_don">
                        <td class="col-id">
                          <strong>{{ order.ma_hoa_don }}</strong>
                        </td>
                        <td class="col-date">
                          <span>{{ dinhDangNgayGio(order.created_at) }}</span>
                        </td>
                        <td class="col-tour">
                          <strong>{{ order.ten_tour || "Đơn tour đang cập nhật" }}</strong>
                          <span>Lịch khởi hành: {{ order.ngay_khoi_hanh_hien_thi || "--" }}</span>
                        </td>
                        <td class="col-price">{{ dinhDangTien(order.so_tien) }}</td>
                        <td class="col-status">
                          <span class="status-badge" :class="`badge--${order.statusTone}`">
                            <span class="status-dot"></span>
                            {{ order.statusLabel }}
                          </span>
                        </td>
                        <td class="text-right">
                          <button
                            type="button"
                            class="action-btn action-btn--soft"
                            :disabled="!order.actionRoute"
                            @click="moHoaDon(order)"
                          >
                            <span>{{ order.actionLabel }}</span>
                            <i class="far fa-eye"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="history-card-list">
                  <article v-for="order in displayedOrders" :key="`${order.ma_hoa_don}-card`" class="history-card">
                    <div class="history-card__top">
                      <div>
                        <p class="history-card__label">Mã đơn</p>
                        <strong>{{ order.ma_hoa_don }}</strong>
                      </div>
                      <span class="status-badge" :class="`badge--${order.statusTone}`">
                        <span class="status-dot"></span>
                        {{ order.statusLabel }}
                      </span>
                    </div>

                    <div class="history-card__body">
                      <div>
                        <p class="history-card__label">Tour</p>
                        <strong>{{ order.ten_tour || "Đơn tour đang cập nhật" }}</strong>
                        <span>{{ order.ngay_khoi_hanh_hien_thi || "--" }}</span>
                      </div>

                      <div class="history-card__meta">
                        <div>
                          <p class="history-card__label">Ngày đặt</p>
                          <strong>{{ dinhDangNgayGio(order.created_at) }}</strong>
                        </div>
                        <div>
                          <p class="history-card__label">Tổng tiền</p>
                          <strong>{{ dinhDangTien(order.so_tien) }}</strong>
                        </div>
                      </div>
                    </div>

                    <button
                      type="button"
                      class="action-btn action-btn--soft action-btn--block"
                      :disabled="!order.actionRoute"
                      @click="moHoaDon(order)"
                    >
                      <span>{{ order.actionLabel }}</span>
                      <i class="far fa-eye"></i>
                    </button>
                  </article>
                </div>
              </template>

              <div v-else class="empty-card">
                <div class="empty-card__icon"><i class="fas fa-filter"></i></div>
                <h3>Không có hóa đơn trong bộ lọc này</h3>
                <p>Thử chuyển sang trạng thái khác để xem đơn đang chờ, đã hoàn tất hoặc cần thanh toán lại.</p>
                <button type="button" class="btn-secondary" @click="activeFilter = 'all'">Xem tất cả</button>
              </div>
            </section>

            <div v-else class="empty-card card">
              <div class="empty-card__icon"><i class="fas fa-receipt"></i></div>
              <h3>Bạn chưa có hóa đơn nào</h3>
              <p>Hãy đặt một tour để hệ thống tạo hóa đơn và mã QR thanh toán cho bạn.</p>
              <router-link to="/khach-hang/tour" class="btn-secondary btn-secondary--primary">
                Khám phá tour
              </router-link>
            </div>
          </template>

          <section class="promo-strip">
            <div>
              <p class="promo-strip__eyebrow">Gợi ý tiếp theo</p>
              <h3>Lên kế hoạch cho chuyến đi mới</h3>
              <p>Toàn bộ hóa đơn sẽ luôn được giữ lại tại đây để bạn đối chiếu hoặc mở lại bất cứ lúc nào.</p>
            </div>
            <router-link to="/khach-hang/tour" class="btn-secondary btn-secondary--primary">
              Khám phá tour
            </router-link>
          </section>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient";
import CustomerSidebar from "../CustomerSidebar.vue";
import {
  CUSTOMER_ORDER_FILTERS,
  enrichCustomerOrder,
  filterCustomerOrdersByStatus,
  normalizeCustomerPaymentStatus,
  sortCustomerOrdersByCreatedAtDesc,
} from "../customerOrdersShared.js";

async function fetchJson(url, init = {}) {
  const response = await goiApi(url, init);
  const payload = await response.json().catch(() => ({}));

  if (response.status === 404) {
    return { data: [] };
  }

  if (!response.ok) {
    const error = new Error(payload?.message || "Không thể hoàn tất yêu cầu.");
    error.response = { data: payload, status: response.status };
    throw error;
  }

  return payload;
}

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "LichSuDonHang",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      loading: false,
      errorMessage: "",
      orders: [],
      activeFilter: "all",
    };
  },
  computed: {
    filterOptions() {
      return CUSTOMER_ORDER_FILTERS;
    },
    enrichedOrders() {
      return sortCustomerOrdersByCreatedAtDesc(this.orders).map((item) => {
        const order = enrichCustomerOrder(item);
        return {
          ...order,
          ngay_khoi_hanh_hien_thi: this.resolveScheduleLabel(order),
        };
      });
    },
    displayedOrders() {
      return filterCustomerOrdersByStatus(this.enrichedOrders, this.activeFilter);
    },
    summaryCards() {
      return [
        { key: "total", label: "Tổng đơn", value: this.orders.length },
        { key: "pending", label: "Chờ thanh toán", value: this.countByStatus("pending") },
        { key: "paid", label: "Đã thanh toán", value: this.countByStatus("paid") },
        {
          key: "needs-action",
          label: "Cần xử lý",
          value: this.countByStatus("expired") + this.countByStatus("failed"),
        },
      ];
    },
  },
  mounted() {
    this.taiDanhSachHoaDon();
  },
  methods: {
    dinhDangTien(value) {
      const amount = Number(value);
      if (!Number.isFinite(amount) || amount <= 0) return "--";
      return `${amount.toLocaleString("vi-VN")} đ`;
    },
    dinhDangNgayGio(value) {
      if (!value) return "--";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "--";
      return date.toLocaleString("vi-VN", {
        hour: "2-digit",
        minute: "2-digit",
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    },
    countByStatus(status) {
      return this.orders.filter((order) => normalizeCustomerPaymentStatus(order?.payment_status) === status).length;
    },
    demTheoTrangThai(filterKey) {
      return filterCustomerOrdersByStatus(this.enrichedOrders, filterKey).length;
    },
    async taiDanhSachHoaDon() {
      this.loading = true;
      // cleared errorMessage

      try {
        const payload = await fetchJson("/api/khach-hang/hoa-don");
        const danhSach = Array.isArray(payload?.data) ? payload.data : [];
        this.orders = danhSach;
      } catch (error) {
        toaster.error(error?.response?.data?.message || error?.message || "Không thể tải lịch sử hóa đơn.");
      } finally {
        this.loading = false;
      }
    },
    resolveScheduleLabel(item) {
      return item.ma_thoi_gian_tour ? `Mã lịch ${item.ma_thoi_gian_tour}` : "--";
    },
    moHoaDon(order) {
      if (!order.actionRoute) return;
      this.$router.push(order.actionRoute);
    },
  },
};
</script>

<style scoped>
.plan-page {
  min-height: 100vh;
  width: 100%;
  background:
    radial-gradient(circle at top right, rgba(14, 116, 144, 0.08), transparent 24%),
    linear-gradient(180deg, #eef5ff 0%, #f7faff 100%);
  color: #0f172a;
}

.plan-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 24px;
  min-height: calc(100vh - 68px);
}

.plan-content {
  padding: 32px 24px 0 0;
  min-width: 0;
}

.order-history-page {
  display: grid;
  gap: 18px;
}

.card,
.page-header,
.orders-shell,
.empty-card,
.promo-strip {
  background: rgba(255, 255, 255, 0.94);
  border: 1px solid rgba(219, 228, 240, 0.9);
  border-radius: 24px;
  box-shadow: 0 16px 34px rgba(15, 23, 42, 0.04);
}

.page-header {
  padding: 22px 24px;
  display: flex;
  justify-content: space-between;
  gap: 20px;
  align-items: flex-start;
}

.page-header__copy {
  min-width: 0;
}

.page-header__eyebrow,
.promo-strip__eyebrow,
.history-card__label {
  margin: 0 0 8px;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.72rem;
  font-weight: 800;
}

.page-header h1 {
  margin: 0;
  font-size: clamp(1.9rem, 2.8vw, 2.5rem);
  font-weight: 900;
  letter-spacing: -0.045em;
}

.page-header p:not(.page-header__eyebrow) {
  margin: 10px 0 0;
  color: #64748b;
  line-height: 1.65;
  max-width: 700px;
}

.page-header__cta,
.btn-secondary {
  min-height: 44px;
  padding: 0 18px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  border: 1px solid #dbe7f3;
  background: rgba(255, 255, 255, 0.9);
  color: #0f172a;
  font-weight: 800;
  white-space: nowrap;
}

.page-header__cta {
  background: rgba(14, 116, 144, 0.08);
  color: #0b7198;
  border-color: rgba(14, 116, 144, 0.16);
}

.btn-secondary--primary {
  background: linear-gradient(135deg, #0f766e 0%, #0369a1 100%);
  border-color: transparent;
  color: #fff;
  box-shadow: 0 14px 24px rgba(3, 105, 161, 0.14);
}

.notice {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  border-radius: 16px;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}

.notice--info {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.summary-strip {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}

.summary-strip__card {
  padding: 16px 18px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(219, 228, 240, 0.9);
}

.summary-strip__card span {
  display: block;
  color: #64748b;
  font-size: 0.78rem;
  font-weight: 700;
}

.summary-strip__card strong {
  display: block;
  margin-top: 8px;
  font-size: 1.35rem;
  letter-spacing: -0.04em;
}

.orders-shell {
  padding: 18px;
  display: grid;
  gap: 16px;
}

.orders-shell__header {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: flex-end;
}

.orders-shell__header h2 {
  margin: 0;
  font-size: 1.08rem;
  font-weight: 900;
}

.orders-shell__header p {
  margin: 6px 0 0;
  color: #64748b;
  font-size: 0.92rem;
}

.segmented-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  padding: 6px;
  border-radius: 18px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.segmented-filter__option {
  min-height: 40px;
  padding: 0 14px;
  border-radius: 12px;
  border: none;
  background: transparent;
  color: #475569;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.segmented-filter__option strong {
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(148, 163, 184, 0.18);
  color: inherit;
  font-size: 0.72rem;
}

.segmented-filter__option.is-active {
  background: linear-gradient(135deg, #0f766e 0%, #0369a1 100%);
  color: #fff;
  box-shadow: 0 12px 20px rgba(3, 105, 161, 0.14);
}

.segmented-filter__option.is-active strong {
  background: rgba(255, 255, 255, 0.18);
}

.table-container {
  min-width: 0;
}

.history-table {
  width: 100%;
  border-collapse: collapse;
}

.history-table th {
  padding: 14px 16px;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
  color: #64748b;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.history-table td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.history-table tbody tr:last-child td {
  border-bottom: none;
}

.history-table tbody tr:hover {
  background: rgba(248, 250, 252, 0.92);
}

.col-id strong,
.col-price {
  font-weight: 900;
}

.col-id strong {
  color: #0369a1;
}

.col-date span,
.col-tour span {
  color: #64748b;
  font-size: 0.88rem;
}

.col-tour strong {
  display: block;
  margin-bottom: 4px;
  font-weight: 800;
}

.text-right {
  text-align: right;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 7px 12px;
  border-radius: 999px;
  font-size: 0.76rem;
  font-weight: 800;
  white-space: nowrap;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: currentColor;
  flex-shrink: 0;
}

.badge--paid {
  background: rgba(16, 185, 129, 0.12);
  color: #047857;
}

.badge--pending {
  background: rgba(245, 158, 11, 0.14);
  color: #b45309;
}

.badge--failed,
.badge--expired {
  background: rgba(244, 63, 94, 0.12);
  color: #be123c;
}

.action-btn {
  border: none;
  cursor: pointer;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background-color 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
}

.action-btn--soft {
  min-height: 40px;
  padding: 0 14px;
  border-radius: 12px;
  background: #f8fbff;
  color: #0369a1;
  border: 1px solid #dbeafe;
}

.action-btn--block {
  width: 100%;
}

.action-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  background: #eff6ff;
}

.action-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.history-card-list {
  display: none;
}

.history-card {
  padding: 18px;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  background: #fff;
  display: grid;
  gap: 16px;
}

.history-card__top,
.history-card__meta {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: flex-start;
}

.history-card strong {
  display: block;
  color: #0f172a;
}

.history-card span {
  color: #64748b;
  font-size: 0.9rem;
}

.history-card__body {
  display: grid;
  gap: 14px;
}

.empty-card {
  padding: 38px 24px;
  display: grid;
  justify-items: center;
  text-align: center;
  gap: 12px;
}

.empty-card__icon {
  width: 64px;
  height: 64px;
  border-radius: 20px;
  display: grid;
  place-items: center;
  background: #eff6ff;
  color: #0369a1;
  font-size: 1.5rem;
}

.empty-card h3,
.promo-strip h3 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 900;
}

.empty-card p,
.promo-strip p:not(.promo-strip__eyebrow) {
  margin: 0;
  color: #64748b;
  line-height: 1.7;
}

.promo-strip {
  padding: 18px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
}

@media (max-width: 1180px) {
  .plan-layout {
    grid-template-columns: 1fr;
  }

  .plan-content {
    padding: 0 0 32px;
  }

  .summary-strip {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {
  .page-header,
  .promo-strip {
    flex-direction: column;
    align-items: stretch;
  }

  .page-header__cta,
  .btn-secondary {
    width: 100%;
  }

  .summary-strip {
    grid-template-columns: 1fr 1fr;
  }

  .history-table--desktop {
    display: none;
  }

  .history-card-list {
    display: grid;
    gap: 12px;
  }

  .orders-shell {
    padding: 14px;
  }
}

@media (max-width: 640px) {
  .summary-strip,
  .history-card__top,
  .history-card__meta {
    grid-template-columns: 1fr;
    display: grid;
  }
}
</style>
