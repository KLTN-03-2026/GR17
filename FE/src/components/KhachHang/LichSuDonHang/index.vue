<template>
  <div class="plan-page">
    <div class="plan-layout">
      <CustomerSidebar />

      <!-- Main Order History Content -->
      <section class="plan-content">
        <div class="order-history-page">
          <header class="page-header">
            <h1>Lịch sử đơn hàng</h1>
            <p>Xem lại các hành trình và dịch vụ bạn đã đặt.</p>
          </header>

          <div class="table-container card">
            <table class="history-table">
              <thead>
                <tr>
                  <th>MÃ ĐƠN</th>
                  <th>NGÀY ĐẶT</th>
                  <th>TÊN TOUR</th>
                  <th>TỔNG TIỀN</th>
                  <th>TRẠNG THÁI</th>
                  <th class="text-center">HÀNH ĐỘNG</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(order, index) in mockOrders" :key="index">
                  <td class="col-id">{{ order.id }}</td>
                  <td class="col-date">{{ order.date }}</td>
                  <td class="col-tour">
                    <strong>{{ order.tourName }}</strong>
                    <span>{{ order.tourDetails }}</span>
                  </td>
                  <td class="col-price">{{ order.total }}</td>
                  <td class="col-status">
                    <span class="status-badge" :class="`badge--${order.statusClass}`">
                      {{ order.statusLabel }}
                    </span>
                  </td>
                  <td class="col-action text-center">
                    <router-link to="#" class="action-btn">
                      Xem chi tiết <i class="far fa-eye"></i>
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Bottom Banner -->
          <div class="promo-banner">
            <div class="promo-text">
              <h3>Lên kế hoạch cho chuyến đi tiếp theo?</h3>
              <p>Khám phá các điểm đến mới nhất đang được ưu đãi trong tháng này.</p>
            </div>
            <router-link to="/khach-hang/tour" class="btn-promo">Khám phá ngay</router-link>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import { getStoredCustomerId, getStoredUser } from "../../Shared/customerSession";
import CustomerSidebar from "../CustomerSidebar.vue";

export default {
  name: "LichSuDonHang",
  components: {
    CustomerSidebar
  },
  data() {
    return {
      // Mock data representing the UI screenshot
      mockOrders: [
        {
          id: "#STP-12345",
          date: "15/06/2024",
          tourName: "Khám phá Vịnh Hạ Long 3N2Đ",
          tourDetails: "Du thuyền 5 sao | Tour trọn gói",
          total: "4.500.000₫",
          statusClass: "completed",
          statusLabel: "Hoàn tất"
        },
        {
          id: "#STP-12346",
          date: "18/06/2024",
          tourName: "Đà Lạt - Thành phố Ngàn Hoa",
          tourDetails: "Khách sạn 4 sao | 4 Ngày 3 Đêm",
          total: "3.200.000₫",
          statusClass: "pending",
          statusLabel: "Đang chờ"
        },
        {
          id: "#STP-12339",
          date: "10/05/2024",
          tourName: "Phú Quốc - Thiên đường Nắng Vàng",
          tourDetails: "Vé máy bay & Resort",
          total: "7.800.000₫",
          statusClass: "cancelled",
          statusLabel: "Đã hủy"
        },
        {
          id: "#STP-12330",
          date: "22/04/2024",
          tourName: "Sapa - Chinh phục Fansipan",
          tourDetails: "Tour leo núi | 2 Ngày 1 Đêm",
          total: "2.150.000₫",
          statusClass: "completed",
          statusLabel: "Hoàn tất"
        }
      ]
    };
  },
  computed: {},
  methods: {}
};
</script>

<style scoped>
/* Inheriting layout rules similarly to HoSoCaNhan */
.plan-page {
  min-height: 100vh;
  width: 100%;
  background: radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%),
              linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
  font-family: 'Inter', 'Segoe UI', sans-serif;
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

/* Main Content Styles */

/* Main Content Styles */
.plan-content {
  padding: 32px 24px 0 0;
  min-width: 0;
}

.order-history-page {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.page-header h1 {
  font-size: 2rem;
  font-weight: 900;
  color: #111827;
  margin: 0 0 12px;
  letter-spacing: -0.02em;
}

.page-header p {
  color: #60728f;
  font-size: 1.05rem;
  margin: 0;
}

.card {
  background: rgba(255, 255, 255, 0.96);
  border-radius: 20px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  border: 1px solid rgba(219, 228, 240, 0.9);
}

/* Table Styles */
.table-container {
  overflow-x: auto;
}

.history-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.history-table th {
  background: #F8FAFC;
  color: #64748B;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 18px 24px;
  border-bottom: 1px solid #E2E8F0;
}

.history-table td {
  padding: 24px;
  border-bottom: 1px solid #F1F5F9;
  vertical-align: middle;
}

.history-table tbody tr {
  transition: background 0.15s;
}

.history-table tbody tr:hover {
  background: #F8FAFC;
}

.history-table tbody tr:last-child td {
  border-bottom: none;
}

.col-id {
  color: #2563EB; /* Matching the blue link color */
  font-weight: 600;
  font-size: 0.95rem;
}

.col-date {
  color: #64748B;
  font-size: 0.95rem;
  font-weight: 500;
}

.col-tour strong {
  display: block;
  color: #1E293B;
  font-size: 1.05rem;
  font-weight: 700;
  margin-bottom: 6px;
}

.col-tour span {
  display: block;
  color: #64748B;
  font-size: 0.85rem;
}

.col-price {
  font-weight: 800;
  color: #0F172A;
  font-size: 1.05rem;
}

/* Badges */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  white-space: nowrap;
}

.badge--completed {
  background: #D1FAE5;
  color: #065F46;
}

.badge--pending {
  background: #FEF3C7;
  color: #9A3412;
}

.badge--cancelled {
  background: #FEE2E2;
  color: #991B1B;
}

/* Action Button */
.text-center {
  text-align: center;
}

.action-btn {
  color: #00609C;
  font-weight: 700;
  font-size: 0.9rem;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 12px;
  transition: background 0.2s;
}

.action-btn i {
  font-size: 1.1rem;
}

.action-btn:hover {
  background: #EFF6FF;
}

/* Promo Banner */
.promo-banner {
  background: #EAF4FD; /* The light blue card from the design */
  border-radius: 20px;
  padding: 36px 48px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  margin-top: 16px;
  border: 1px solid #DCECFB;
}

.promo-text h3 {
  margin: 0 0 12px;
  font-size: 1.4rem;
  font-weight: 800;
  color: #004D7A; /* Darker blue */
  letter-spacing: -0.01em;
}

.promo-text p {
  margin: 0;
  color: #4B5563;
  font-size: 1.05rem;
}

.btn-promo {
  background: #00609C;
  color: white;
  padding: 16px 32px;
  border-radius: 12px;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(0, 96, 156, 0.15);
}

.btn-promo:hover {
  background: #004D7A;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 96, 156, 0.25);
}

@media (max-width: 1024px) {
  .plan-layout {
    grid-template-columns: 1fr;
    padding: 24px;
  }
  
  .profile-panel {
    display: none; 
  }
  
  .plan-content {
    padding: 0;
  }
}

@media (max-width: 768px) {
  .promo-banner {
    flex-direction: column;
    align-items: stretch;
    padding: 32px;
    text-align: center;
  }
  
  .btn-promo {
    text-align: center;
  }
  
  .history-table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
}
</style>
