<template>
  <div class="customer-dashboard-page">
    <div class="customer-dashboard-layout">
      <CustomerSidebar />

      <section class="customer-dashboard-content">
        <header class="hero-section">
          <div class="hero-copy">
            <p class="hero-copy__eyebrow">Không gian khách hàng</p>
            <h1>Đặt tour, theo dõi thanh toán và quay lại hành trình đang mở</h1>
            <p class="hero-copy__summary">
              Đây là điểm vào nhanh cho các thao tác quan trọng nhất: tìm tour, tiếp tục thanh toán QR và mở lại các mục bạn dùng thường xuyên.
            </p>

            <div class="hero-actions">
              <router-link class="hero-button hero-button--primary" to="/khach-hang/tour">
                <i class="fas fa-map"></i>
                <span>Đặt tour ngay</span>
              </router-link>
              <router-link
                v-if="resumeRoute"
                class="hero-button hero-button--secondary"
                :to="resumeRoute"
              >
                <i class="fas fa-qrcode"></i>
                <span>Tiếp tục thanh toán</span>
              </router-link>
            </div>
          </div>

          <div class="hero-spotlight">
            <span class="hero-spotlight__label">Trạng thái ưu tiên</span>
            <template v-if="resumeOrder">
              <strong>{{ resumeOrder.ma_hoa_don }}</strong>
              <p>{{ resumeStatusText }}</p>
              <dl class="hero-spotlight__meta">
                <div>
                  <dt>Tour</dt>
                  <dd>{{ resumeOrder.ten_tour || "Đang cập nhật" }}</dd>
                </div>
                <div>
                  <dt>Khởi hành</dt>
                  <dd>{{ resumeOrder.ma_thoi_gian_tour || "--" }}</dd>
                </div>
              </dl>
            </template>
            <template v-else>
              <strong>Không có đơn cần xử lý</strong>
              <p>Bạn đã thanh toán xong hoặc chưa tạo hóa đơn nào. Có thể bắt đầu từ danh sách tour.</p>
            </template>
          </div>
        </header>

        <div v-if="errorMessage" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ errorMessage }}</span>
        </div>

        <div v-if="warningMessage" class="notice notice--info">
          <i class="fas fa-circle-info"></i>
          <span>{{ warningMessage }}</span>
        </div>

        <div v-if="loading" class="notice notice--loading">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang đồng bộ thông tin tổng quan...</span>
        </div>

        <section class="stats-strip">
          <article class="stat-item">
            <span>Đơn chờ thanh toán</span>
            <strong>{{ summary.stats.pendingOrders }}</strong>
          </article>
          <article class="stat-item">
            <span>Đơn đã thanh toán</span>
            <strong>{{ summary.stats.paidOrders }}</strong>
          </article>
          <article class="stat-item">
            <span>Kế hoạch</span>
            <strong>{{ summary.stats.planCount }}</strong>
          </article>
          <article class="stat-item">
            <span>Nhóm hành trình</span>
            <strong>{{ summary.stats.groupCount }}</strong>
          </article>
        </section>

        <section class="workspace-grid">
          <article class="workspace-block">
            <div class="workspace-block__header">
              <p class="workspace-block__eyebrow">Lối tắt</p>
              <h2>Đi đến đúng màn hình cần thao tác</h2>
            </div>

            <div class="shortcut-grid">
              <router-link class="shortcut-tile" to="/khach-hang/tour">
                <i class="fas fa-map"></i>
                <strong>Danh sách tour</strong>
                <span>Bắt đầu đặt tour từ danh sách đang mở.</span>
              </router-link>
              <router-link class="shortcut-tile" to="/khach-hang/lich-su-don-hang">
                <i class="fas fa-receipt"></i>
                <strong>Đơn hàng và thanh toán</strong>
                <span>Theo dõi QR, trạng thái thanh toán và tạo lại mã QR khi cần.</span>
              </router-link>
              <router-link class="shortcut-tile" to="/khach-hang/ke-hoach">
                <i class="fas fa-calendar-days"></i>
                <strong>Kế hoạch của tôi</strong>
                <span>Xem các kế hoạch đã tạo và tiếp tục lập lịch trình.</span>
              </router-link>
            </div>
          </article>

          <article class="workspace-block workspace-block--orders">
            <div class="workspace-block__header">
              <p class="workspace-block__eyebrow">Hóa đơn gần đây</p>
              <h2>Theo dõi các đơn cần quay lại</h2>
            </div>

            <div v-if="recentOrders.length" class="order-list">
              <router-link
                v-for="order in recentOrders"
                :key="order.ma_hoa_don"
                class="order-row"
                :to="order.actionRoute || '/khach-hang/lich-su-don-hang'"
              >
                <div>
                  <strong>{{ order.ma_hoa_don }}</strong>
                  <span>{{ order.ten_tour || "Đơn tour đang cập nhật" }}</span>
                </div>
                <div class="order-row__meta">
                  <span class="order-row__status" :class="`order-row__status--${order.statusTone}`">
                    {{ order.statusLabel }}
                  </span>
                  <small>{{ order.actionLabel }}</small>
                </div>
              </router-link>
            </div>
            <div v-else class="workspace-empty">
              <i class="fas fa-receipt"></i>
              <p>Chưa có hóa đơn nào. Sau khi đặt tour, mã QR thanh toán sẽ xuất hiện tại đây.</p>
            </div>
          </article>
        </section>
      </section>
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient.js";
import CustomerSidebar from "../CustomerSidebar.vue";
import { API_BASE, chuanHoaDanhSach, getStoredCustomerId } from "../../Shared/customerSession";
import {
  buildCustomerDashboardSummary,
  buildCustomerPaymentRoute,
  enrichCustomerOrder,
  getCustomerOrderStatusMeta,
  sortCustomerOrdersByCreatedAtDesc,
} from "../customerOrdersShared.js";

async function fetchJson(url, { allowEmpty404 = false } = {}) {
  const response = await goiApi(url);
  const payload = await response.json().catch(() => ({}));

  if (allowEmpty404 && response.status === 404) {
    return { data: [] };
  }

  if (!response.ok) {
    const error = new Error(payload?.message || "Không thể hoàn tất yêu cầu.");
    error.response = { data: payload, status: response.status };
    throw error;
  }

  return payload;
}

function uniqueGroupsFromMembership(rows = []) {
  const byId = new Map();

  rows.forEach((row) => {
    const id = String(row?.Ma_nhom || row?.ma_nhom || row?.nhom?.Ma_nhom || "").trim();
    if (!id || byId.has(id)) return;

    byId.set(id, {
      id,
      name: row?.nhom?.ten_nhom || row?.ten_nhom || "Nhóm chưa xác định",
    });
  });

  return Array.from(byId.values());
}

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "KhachHangDashboard",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      loading: false,
      errorMessage: "",
      warningMessage: "",
      orders: [],
      plans: [],
      groups: [],
    };
  },
  computed: {
    summary() {
      return buildCustomerDashboardSummary({
        orders: this.orders,
        plans: this.plans,
        groups: this.groups,
      });
    },
    resumeOrder() {
      return this.summary.resumeOrder;
    },
    resumeRoute() {
      return buildCustomerPaymentRoute(this.resumeOrder);
    },
    resumeStatusText() {
      if (!this.resumeOrder) return "";
      const meta = getCustomerOrderStatusMeta(this.resumeOrder.payment_status);
      return `${meta.label}. Mở lại màn thanh toán để xem QR hoặc tiếp tục xử lý.`;
    },
    recentOrders() {
      return sortCustomerOrdersByCreatedAtDesc(this.orders)
        .slice(0, 3)
        .map((order) => enrichCustomerOrder(order));
    },
  },
  mounted() {
    this.taiTongQuan();
  },
  methods: {
    async taiTongQuan() {
      const maKhachHang = getStoredCustomerId();
      if (!maKhachHang) {
        this.$router.replace({
          path: "/dang-nhap",
          query: {
            redirect: "/khach-hang/dashboard",
          },
        });
        return;
      }

      this.loading = true;
      // cleared errorMessage
      this.warningMessage = "";

      const [ordersResult, plansResult, groupsResult] = await Promise.allSettled([
        fetchJson(`${API_BASE}/khach-hang/hoa-don`, { allowEmpty404: true }),
        fetchJson(`${API_BASE}/ke-hoach?ma_khach_hang=${encodeURIComponent(maKhachHang)}`, { allowEmpty404: true }),
        fetchJson(`${API_BASE}/thanh-vien-nhom/search?Ma_khach_hang=${encodeURIComponent(maKhachHang)}`, { allowEmpty404: true }),
      ]);

      try {
        if (ordersResult.status === "fulfilled") {
          this.orders = chuanHoaDanhSach(ordersResult.value?.data ?? ordersResult.value);
        } else {
          throw ordersResult.reason;
        }

        if (plansResult.status === "fulfilled") {
          this.plans = chuanHoaDanhSach(plansResult.value?.data ?? plansResult.value);
        }

        if (groupsResult.status === "fulfilled") {
          const memberships = chuanHoaDanhSach(groupsResult.value?.data ?? groupsResult.value);
          this.groups = uniqueGroupsFromMembership(memberships);
        }

        const softFailures = [plansResult, groupsResult].filter((result) => result.status === "rejected");
        if (softFailures.length) {
          this.warningMessage = "Một số số liệu phụ trợ chưa đồng bộ. Bạn vẫn có thể đặt tour và tiếp tục thanh toán.";
        }
      } catch (error) {
        toaster.error(error?.response?.data?.message || error?.message || "Không thể tải tổng quan khách hàng.");
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.customer-dashboard-page {
  min-height: 100vh;
  background:
    radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%),
    linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
}

.customer-dashboard-layout {
  width: min(100%, 1840px);
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 352px minmax(0, 1fr);
  gap: 24px;
  min-height: calc(100vh - 68px);
}

.customer-dashboard-content {
  padding: 12px 12px 0 0;
  display: grid;
  gap: 22px;
}

.hero-section,
.stats-strip,
.workspace-block,
.notice {
  border-radius: 28px;
  border: 1px solid #e2eaf5;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 16px 34px rgba(31, 65, 114, 0.06);
}

.hero-section {
  padding: 28px;
  display: grid;
  grid-template-columns: minmax(0, 1.32fr) 380px;
  gap: 28px;
  align-items: stretch;
}

.hero-copy__eyebrow,
.workspace-block__eyebrow {
  margin: 0 0 10px;
  color: #4f25f4;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.hero-copy h1,
.workspace-block h2 {
  margin: 0;
  color: #111827;
  font-size: clamp(2rem, 3.1vw, 2.8rem);
  line-height: 1.06;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.workspace-block h2 {
  font-size: 1.45rem;
}

.hero-copy__summary {
  margin: 12px 0 0;
  color: #5f6f89;
  max-width: 760px;
  font-size: 1rem;
  line-height: 1.7;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 24px;
}

.hero-button {
  min-height: 50px;
  padding: 0 20px;
  border-radius: 18px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  font-weight: 800;
}

.hero-button--primary {
  color: #ffffff;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  box-shadow: 0 16px 28px rgba(11, 99, 160, 0.2);
}

.hero-button--secondary {
  color: #0f2b4a;
  border: 1px solid #d5e3f4;
  background: #f8fbff;
}

.hero-spotlight {
  padding: 22px;
  border-radius: 24px;
  background:
    linear-gradient(160deg, rgba(11, 113, 152, 0.92), rgba(10, 49, 89, 0.96));
  color: #ffffff;
  display: grid;
  align-content: start;
  gap: 12px;
}

.hero-spotlight__label {
  font-size: 0.76rem;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: rgba(226, 240, 255, 0.84);
}

.hero-spotlight strong {
  font-size: 1.4rem;
  line-height: 1.2;
}

.hero-spotlight p,
.hero-spotlight dd,
.hero-spotlight dt {
  margin: 0;
}

.hero-spotlight p {
  color: rgba(240, 247, 255, 0.92);
  line-height: 1.7;
}

.hero-spotlight__meta {
  display: grid;
  gap: 10px;
}

.hero-spotlight__meta div {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding-top: 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.16);
}

.hero-spotlight__meta dt {
  color: rgba(215, 232, 255, 0.8);
}

.hero-spotlight__meta dd {
  text-align: right;
  font-weight: 700;
}

.notice {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.notice--info,
.notice--loading {
  background: #eff6ff;
  color: #1d4ed8;
}

.stats-strip {
  padding: 14px;
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.stat-item {
  padding: 18px;
  border-radius: 20px;
  background: #fbfdff;
  border: 1px solid #edf2f8;
}

.stat-item span {
  display: block;
  color: #617891;
  font-weight: 700;
}

.stat-item strong {
  display: block;
  margin-top: 10px;
  color: #0d2f57;
  font-size: 2rem;
  line-height: 1;
  font-weight: 900;
}

.workspace-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.18fr) minmax(360px, 0.9fr);
  gap: 22px;
}

.workspace-block {
  padding: 24px;
  display: grid;
  gap: 20px;
}

.shortcut-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
}

.shortcut-tile,
.order-row {
  text-decoration: none;
}

.shortcut-tile {
  min-height: 176px;
  padding: 18px;
  border-radius: 20px;
  border: 1px solid #e6edf8;
  background: #fbfdff;
  color: #24415d;
  display: grid;
  align-content: start;
  gap: 12px;
}

.shortcut-tile i {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  color: #ffffff;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
}

.shortcut-tile strong,
.order-row strong {
  color: #102f56;
}

.shortcut-tile span {
  color: #617891;
  line-height: 1.7;
}

.order-list {
  display: grid;
  gap: 12px;
}

.order-row {
  padding: 16px 18px;
  border-radius: 18px;
  border: 1px solid #e6edf8;
  background: #fbfdff;
  display: flex;
  justify-content: space-between;
  gap: 16px;
  align-items: center;
}

.order-row span {
  display: block;
  margin-top: 4px;
  color: #617891;
}

.order-row__meta {
  display: grid;
  justify-items: end;
  gap: 6px;
}

.order-row__meta small {
  color: #617891;
  font-weight: 700;
}

.order-row__status {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 0.78rem;
  font-weight: 800;
}

.order-row__status--paid {
  background: #dcfce7;
  color: #047857;
}

.order-row__status--pending {
  background: #fef3c7;
  color: #92400e;
}

.order-row__status--expired,
.order-row__status--failed {
  background: #fee2e2;
  color: #b91c1c;
}

.workspace-empty {
  min-height: 200px;
  border-radius: 20px;
  border: 1px dashed #c8d6e8;
  display: grid;
  place-items: center;
  text-align: center;
  gap: 12px;
  padding: 24px;
  color: #617891;
}

.workspace-empty i {
  font-size: 1.6rem;
  color: #0b63a0;
}

@media (max-width: 1200px) {
  .customer-dashboard-layout {
    grid-template-columns: 1fr;
  }

  .hero-section,
  .workspace-grid,
  .shortcut-grid,
  .stats-strip {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 860px) {
  .customer-dashboard-layout {
    width: calc(100% - 20px);
  }

  .hero-section,
  .workspace-block {
    padding: 20px;
  }

  .hero-actions {
    flex-direction: column;
  }

  .hero-button {
    width: 100%;
    justify-content: center;
  }

  .order-row {
    grid-template-columns: 1fr;
    display: grid;
  }

  .order-row__meta {
    justify-items: start;
  }
}
</style>
