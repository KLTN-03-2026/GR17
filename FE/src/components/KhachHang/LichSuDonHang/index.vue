<template>
  <div class="plan-page">
    <div class="plan-layout">
      <CustomerSidebar />

      <section class="plan-content">
        <div class="order-history-page">
          <header class="page-header">
            <div>
              <p class="page-header__eyebrow">Hóa đơn và thanh toán</p>
              <h1>Đơn hàng và thanh toán</h1>
              <p>Theo dõi QR thanh toán, mở lại hóa đơn và quay lại đúng màn checkout khi cần xử lý tiếp.</p>
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
            <section v-if="orders.length" class="filter-panel card">
              <button
                v-for="filter in filterOptions"
                :key="filter.key"
                type="button"
                class="filter-chip"
                :class="{ 'filter-chip--active': activeFilter === filter.key }"
                @click="activeFilter = filter.key"
              >
                <span>{{ filter.label }}</span>
                <strong>{{ demTheoTrangThai(filter.key) }}</strong>
              </button>
            </section>

            <div v-if="displayedOrders.length" class="table-container card">
              <table class="history-table">
                <thead>
                  <tr>
                    <th>MA DON</th>
                    <th>NGAY DAT</th>
                    <th>TOUR</th>
                    <th>TONG TIEN</th>
                    <th>TRANG THAI</th>
                    <th class="text-center">HANH DONG</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in displayedOrders" :key="order.ma_hoa_don">
                    <td class="col-id">{{ order.ma_hoa_don }}</td>
                    <td class="col-date">{{ dinhDangNgayGio(order.created_at) }}</td>
                    <td class="col-tour">
                      <strong>{{ order.ten_tour || "Đơn tour đang cập nhật" }}</strong>
                      <span>Lịch khởi hành: {{ order.ngay_khoi_hanh_hien_thi || "--" }}</span>
                    </td>
                    <td class="col-price">{{ dinhDangTien(order.so_tien) }}</td>
                    <td class="col-status">
                      <span class="status-badge" :class="`badge--${order.statusTone}`">
                        {{ order.statusLabel }}
                      </span>
                    </td>
                    <td class="col-action text-center">
                      <button
                        type="button"
                        class="action-btn"
                        :disabled="!order.actionRoute"
                        @click="moHoaDon(order)"
                      >
                        {{ order.actionLabel }}
                        <i class="far fa-eye"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else-if="orders.length" class="empty-card card">
              <div class="empty-card__icon"><i class="fas fa-filter"></i></div>
              <h3>Không có hóa đơn nào trong bộ lọc này</h3>
              <p>Thử chuyển sang trạng thái khác để xem QR đang mở, hóa đơn đã thanh toán hoặc các đơn cần tạo lại mã QR.</p>
              <button type="button" class="btn-promo" @click="activeFilter = 'all'">Xem tất cả</button>
            </div>

            <div v-else class="empty-card card">
              <div class="empty-card__icon"><i class="fas fa-receipt"></i></div>
              <h3>Bạn chưa có hóa đơn nào</h3>
              <p>Hãy đặt một tour để hệ thống tạo mã hóa đơn và mã QR thanh toán cho bạn.</p>
              <router-link to="/khach-hang/tour" class="btn-promo">Khám phá tour</router-link>
            </div>
          </template>

          <div class="promo-banner">
            <div class="promo-text">
              <h3>Tiếp tục lên lịch cho chuyến đi tiếp theo?</h3>
              <p>Mỗi hóa đơn sẽ được giữ lại tại đây để bạn đối chiếu, xem QR và quay lại thanh toán khi cần.</p>
            </div>
            <router-link to="/khach-hang/tour" class="btn-promo">Khám phá ngay</router-link>
          </div>
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
  },
  mounted() {
    this.taiDanhSachHoaDon();
  },
  methods: {
    dinhDangTien(value) {
      const amount = Number(value);
      if (!Number.isFinite(amount) || amount <= 0) return "--";
      return `${amount.toLocaleString("vi-VN")} d`;
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
    radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%),
    linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
  color: #111827;
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
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  align-items: flex-start;
}

.page-header__eyebrow {
  margin: 0 0 10px;
  color: #4f25f4;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.page-header h1 {
  margin: 0 0 10px;
  font-size: clamp(2rem, 3vw, 2.8rem);
  font-weight: 900;
  letter-spacing: -0.05em;
}

.page-header p {
  margin: 0;
  color: #60728f;
  font-size: 1.02rem;
  line-height: 1.7;
  max-width: 760px;
}

.page-header__cta,
.btn-promo {
  min-height: 48px;
  padding: 0 22px;
  border-radius: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  font-weight: 800;
  border: 0;
}

.page-header__cta,
.btn-promo {
  color: #ffffff;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  box-shadow: 0 16px 28px rgba(11, 99, 160, 0.16);
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

.card {
  background: rgba(255, 255, 255, 0.96);
  border-radius: 24px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  border: 1px solid rgba(219, 228, 240, 0.9);
}

.filter-panel {
  padding: 14px;
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.filter-chip {
  min-height: 44px;
  padding: 0 16px;
  border-radius: 999px;
  border: 1px solid #d7e3f0;
  background: #f8fbff;
  color: #24415d;
  font-weight: 800;
  display: inline-flex;
  gap: 10px;
  align-items: center;
}

.filter-chip strong {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: rgba(15, 23, 42, 0.08);
  color: inherit;
  font-size: 0.8rem;
}

.filter-chip--active {
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  border-color: transparent;
  color: #ffffff;
}

.filter-chip--active strong {
  background: rgba(255, 255, 255, 0.16);
  color: #ffffff;
}

.table-container {
  overflow-x: auto;
}

.history-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.history-table th {
  background: #f8fafc;
  color: #64748b;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 18px 24px;
  border-bottom: 1px solid #e2e8f0;
}

.history-table td {
  padding: 22px 24px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.history-table tbody tr:last-child td {
  border-bottom: none;
}

.history-table tbody tr:hover {
  background: #f8fbff;
}

.col-id {
  color: #0369a1;
  font-weight: 700;
}

.col-date {
  color: #64748b;
  font-weight: 500;
}

.col-tour strong {
  display: block;
  margin-bottom: 6px;
}

.col-tour span {
  color: #64748b;
  font-size: 0.88rem;
}

.col-price {
  font-weight: 800;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 7px 14px;
  border-radius: 999px;
  font-size: 0.78rem;
  font-weight: 800;
  white-space: nowrap;
}

.badge--paid {
  background: #d1fae5;
  color: #065f46;
}

.badge--pending {
  background: #fef3c7;
  color: #9a3412;
}

.badge--failed,
.badge--expired {
  background: #fee2e2;
  color: #991b1b;
}

.text-center {
  text-align: center;
}

.action-btn {
  border: none;
  background: #eff6ff;
  color: #0369a1;
  padding: 10px 16px;
  border-radius: 14px;
  font-weight: 800;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.empty-card {
  padding: 48px 24px;
  display: grid;
  justify-items: center;
  text-align: center;
  gap: 12px;
}

.empty-card__icon {
  width: 72px;
  height: 72px;
  border-radius: 24px;
  display: grid;
  place-items: center;
  background: #eff6ff;
  color: #0369a1;
  font-size: 1.8rem;
}

.empty-card h3,
.promo-text h3 {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 900;
}

.empty-card p,
.promo-text p {
  margin: 0;
  color: #60728f;
  line-height: 1.7;
}

.promo-banner {
  background: #eaf4fd;
  border-radius: 24px;
  padding: 32px 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  border: 1px solid #dcecfb;
}

@media (max-width: 1024px) {
  .plan-layout {
    grid-template-columns: 1fr;
  }

  .plan-content {
    padding: 0 0 32px;
  }
}

@media (max-width: 860px) {
  .page-header,
  .promo-banner {
    flex-direction: column;
    align-items: stretch;
  }

  .page-header__cta,
  .btn-promo {
    width: 100%;
  }

  .history-table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
}
</style>
