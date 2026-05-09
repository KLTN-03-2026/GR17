<template>
  <PartnerShell
    title="Đối soát & Doanh thu"
    subtitle="Theo dõi doanh thu từ các Tour đã bán và tiền hoa hồng thực nhận."
    :loading="loading"
  >
    <template #headerActions>
      <button class="refresh-btn" type="button" @click="fetchData">
        <i class="fas fa-rotate"></i>
        Làm mới
      </button>
    </template>

    <div v-if="errorMessage" class="alert-box">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </div>

    <!-- THỐNG KÊ TỔNG QUAN -->
    <section class="stats-grid" v-if="thongKe">
      <article class="stat-card">
        <p>Tổng doanh thu (Bán được)</p>
        <strong>{{ formatCurrency(thongKe.tong_doanh_thu_ban_duoc) }}</strong>
        <small>Tiền khách đã thanh toán</small>
      </article>
      <article class="stat-card stat-card--warning">
        <p>Phí nền tảng (10%)</p>
        <strong>{{ formatCurrency(thongKe.phi_hoa_hong_nen_tang) }}</strong>
        <small>Phân bổ cho hệ thống</small>
      </article>
      <article class="stat-card stat-card--accent">
        <p>Thực nhận của bạn</p>
        <strong>{{ formatCurrency(thongKe.tong_tien_da_nhan) }}</strong>
        <small>Đã được Admin thanh toán</small>
      </article>
      <article class="stat-card">
        <p>Tiền đang chờ duyệt</p>
        <strong>{{ formatCurrency(thongKe.tong_tien_dang_cho) }}</strong>
        <small>Admin chưa chuyển khoản</small>
      </article>
    </section>

    <!-- BẢNG LỊCH SỬ CHUYỂN KHOẢN / ĐỐI SOÁT -->
    <section class="table-container" style="margin-top: 2rem;">
      <h3 style="margin-bottom: 1rem; color: #0f172a; font-weight: 800;">Lịch sử giao dịch</h3>
      <div v-if="!danhSach.length" class="empty-state">
        <i class="fas fa-receipt"></i>
        <p>Chưa có giao dịch đối soát nào được ghi nhận.</p>
      </div>

      <div class="table-wrapper" v-else>
        <table>
          <thead>
            <tr>
              <th>Mã hóa đơn</th>
              <th>Tổng tiền khách mua</th>
              <th>Hoa hồng nền tảng</th>
              <th>Thực nhận của bạn</th>
              <th>Ngày tạo</th>
              <th>Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in danhSach" :key="item.ma_doi_soat">
              <td>
                <strong>{{ item.ma_hoa_don }}</strong>
              </td>
              <td>{{ formatCurrency(item.tong_tien_giao_dich) }}</td>
              <td style="color: #ef4444">-{{ formatCurrency(item.tien_hoa_hong_admin) }}</td>
              <td style="color: #10b981; font-weight: 700;">+{{ formatCurrency(item.tien_doi_tac_thuc_nhan) }}</td>
              <td>{{ formatDate(item.created_at) }}</td>
              <td>
                <span :class="['status-badge', item.trang_thai_thanh_toan === 'da_chuyen_khoan' ? 'status-paid' : 'status-pending']">
                  {{ item.trang_thai_thanh_toan === 'da_chuyen_khoan' ? 'Đã Thanh Toán' : 'Đang Chờ' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </PartnerShell>
</template>

<script>
import PartnerShell from "./layout/PartnerShell.vue";
import { goiApi } from "../../services/httpClient";

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "DoiSoatDoanhThu",
  components: {
    PartnerShell,
  },
  data() {
    return {
      loading: false,
      errorMessage: "",
      thongKe: null,
      danhSach: [],
    };
  },
  methods: {
    formatCurrency(value) {
      if (!value) return "0 ₫";
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      }).format(value);
    },
    formatDate(dateStr) {
      if (!dateStr) return "";
      const d = new Date(dateStr);
      return d.toLocaleDateString("vi-VN", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    async fetchData() {
      this.loading = true;
      // cleared errorMessage
      try {
        const response = await goiApi("/api/doi-tac/doanh-thu");
        const payload = await response.json();
        
        if (!response.ok) {
          throw new Error(payload.message || "Lỗi tải dữ liệu doanh thu.");
        }
        
        this.thongKe = payload.data.thong_ke;
        this.danhSach = payload.data.danh_sach;
      } catch (error) {
        console.error(error);
        toaster.error(error.message);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>

<style scoped>
.refresh-btn {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  border: 1px solid #bfdbfe;
  background: #eff6ff;
  color: #1d4ed8;
  font-weight: 700;
  padding: 0 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.refresh-btn:hover {
  background: #dbeafe;
  transform: translateY(-1px);
}

.alert-box {
  border-radius: 1rem;
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #b91c1c;
  padding: 1rem 1.5rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 1.5rem;
}

.stat-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
  transition: box-shadow 0.2s ease;
}

.stat-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.stat-card p {
  margin: 0;
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.stat-card strong {
  margin: 0.5rem 0;
  display: block;
  font-size: 1.5rem;
  color: #0f172a;
  font-weight: 900;
  line-height: 1.2;
}

.stat-card small {
  color: #64748b;
  font-size: 0.875rem;
}

.stat-card--warning {
  border-color: #fce7f3;
  background: linear-gradient(135deg, #fdf2f8, #fbcfe8);
}

.stat-card--accent {
  border-color: #dcfce7;
  background: linear-gradient(135deg, #f0fdf4, #bbf7d0);
}

/* TABLE STYES */
.table-wrapper {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem 1.5rem;
  text-align: left;
  border-bottom: 1px solid #f1f5f9;
}

th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

td {
  color: #334155;
  font-size: 0.95rem;
}

tr:last-child td {
  border-bottom: none;
}

tr:hover td {
  background: #f8fafc;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 700;
}

.status-pending {
  background: #fef3c7;
  color: #d97706;
}

.status-paid {
  background: #dcfce7;
  color: #15803d;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: #64748b;
  background: #f8fafc;
  border-radius: 1rem;
  border: 1px dashed #cbd5e1;
}

.empty-state i {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: #94a3b8;
}

@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
