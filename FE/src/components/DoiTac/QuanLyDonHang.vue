<template>
  <PartnerShell
    title="Quản lý đơn hàng"
    subtitle="Danh sách khách hàng đã đặt tour của bạn, kèm thông tin thanh toán và đối soát QR."
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

    <section v-if="danhSach.length" class="stats-grid">
      <article class="stat-card">
        <span>Tổng đơn</span>
        <strong>{{ danhSach.length }}</strong>
      </article>
      <article class="stat-card">
        <span>Đang chờ</span>
        <strong>{{ soDonDangCho }}</strong>
      </article>
      <article class="stat-card">
        <span>Đã thanh toán</span>
        <strong>{{ soDonDaThanhToan }}</strong>
      </article>
      <article class="stat-card">
        <span>Doanh thu đã thanh toán</span>
        <strong>{{ formatCurrency(doanhThuPaid) }}</strong>
      </article>
    </section>

    <section class="table-container" style="margin-top: 1rem;">
      <div class="filters-bar">
        <div class="filter-search">
          <i class="fas fa-search"></i>
          <input type="text" v-model="searchQuery" placeholder="Tìm tên tour, tên khách, SĐT, mã đơn..." />
        </div>
        <div class="filter-status">
          <select v-model="filterStatus">
            <option value="all">Tất cả trạng thái</option>
            <option value="pending">Đang chờ</option>
            <option value="paid">Đã thanh toán</option>
            <option value="expired">Đã hết hạn / Hủy</option>
          </select>
        </div>
      </div>

      <div v-if="!filteredDanhSach.length" class="empty-state">
        <i class="fas fa-clipboard-list"></i>
        <p>Không tìm thấy đơn hàng nào phù hợp.</p>
      </div>

      <div v-else class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Tour</th>
              <th>Khách đặt</th>
              <th>Thanh toán</th>
              <th>Tổng tiền</th>
              <th>Ngày đặt</th>
              <th>Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in filteredDanhSach" :key="item.ma_hoa_don">
              <td>
                <strong>{{ item.ma_hoa_don }}</strong>
              </td>
              <td>
                <strong>{{ item.ten_tour || item.ma_tour || "---" }}</strong>
                <small>Mã lịch: {{ item.ma_thoi_gian_tour || "---" }}</small>
              </td>
              <td>
                <div class="customer-block">
                  <strong>{{ item.customer_summary?.ho_ten || "---" }}</strong>
                  <small>{{ item.customer_summary?.email || "---" }}</small>
                  <small>{{ item.customer_summary?.so_dien_thoai || "---" }}</small>
                </div>
              </td>
              <td>
                <strong>{{ paymentMethodLabel(item.payment_method) }}</strong>
                <small>{{ item.payment_reference || item.ma_giao_dich || "---" }}</small>
              </td>
              <td class="money-cell">{{ formatCurrency(item.so_tien) }}</td>
              <td>{{ formatDate(item.ngay_tao) }}</td>
              <td>
                <span :class="['status-badge', `status-${paymentStatus(item)}`]">
                  {{ paymentStatusLabel(item) }}
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
  name: "QuanLyDonHang",
  components: {
    PartnerShell,
  },
  data() {
    return {
      loading: false,
      errorMessage: "",
      danhSach: [],
      searchQuery: "",
      filterStatus: "all",
    };
  },
  computed: {
    filteredDanhSach() {
      return this.danhSach.filter((item) => {
        let matchStatus = true;
        if (this.filterStatus !== 'all') {
            const currentStatus = this.paymentStatus(item);
            if (this.filterStatus === 'expired') {
                matchStatus = currentStatus === 'expired' || currentStatus === 'failed';
            } else {
                matchStatus = currentStatus === this.filterStatus;
            }
        }
        
        let matchSearch = true;
        if (this.searchQuery) {
            const q = this.searchQuery.toLowerCase();
            const maHD = String(item.ma_hoa_don || '').toLowerCase();
            const tenTour = String(item.ten_tour || '').toLowerCase();
            const khach = item.customer_summary || {};
            const tenKhach = String(khach.ho_ten || '').toLowerCase();
            const sdt = String(khach.so_dien_thoai || '').toLowerCase();
            
            matchSearch = maHD.includes(q) || tenTour.includes(q) || tenKhach.includes(q) || sdt.includes(q);
        }
        return matchStatus && matchSearch;
      });
    },
    soDonDangCho() {
      return this.filteredDanhSach.filter((item) => this.paymentStatus(item) === "pending").length;
    },
    soDonDaThanhToan() {
      return this.filteredDanhSach.filter((item) => this.paymentStatus(item) === "paid").length;
    },
    doanhThuPaid() {
      return this.filteredDanhSach
        .filter((item) => this.paymentStatus(item) === "paid")
        .reduce((sum, item) => sum + Number(item.so_tien || 0), 0);
    },
  },
  methods: {
    paymentStatus(item) {
      return item?.payment_status || "pending";
    },
    paymentStatusLabel(item) {
      switch (this.paymentStatus(item)) {
        case "paid":
          return "Đã thanh toán";
        case "expired":
          return "Đã hết hạn";
        case "failed":
          return "Thất bại / hủy";
        default:
          return "Đang chờ thanh toan";
      }
    },
    paymentMethodLabel(method) {
      if (method === "vietqr_bank_transfer") return "QR ngân hàng";
      if (method === "legacy_manual") return "Thủ công / cũ";
      return method || "---";
    },
    formatCurrency(value) {
      const amount = Number(value);
      if (!Number.isFinite(amount)) return "0 đ";
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      }).format(amount);
    },
    formatDate(dateStr) {
      if (!dateStr) return "---";
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) return "---";
      return date.toLocaleString("vi-VN", {
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
        const response = await goiApi("/api/doi-tac/don-hang");
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          throw new Error(payload?.message || "Lỗi tải dữ liệu đơn hàng.");
        }

        this.danhSach = Array.isArray(payload?.data) ? payload.data : [];
      } catch (error) {
        toaster.error(error?.message || "Không thể tải dữ liệu đơn hàng.");
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
  gap: 1rem;
}

.stat-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  padding: 1rem 1.2rem;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
  display: grid;
  gap: 0.35rem;
}

.stat-card span {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 700;
}

.stat-card strong {
  color: #111827;
  font-size: 1.25rem;
  font-weight: 900;
}

.table-wrapper {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

.filters-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

.filter-search {
  flex: 1;
  min-width: 250px;
  position: relative;
}

.filter-search i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}

.filter-search input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border-radius: 0.75rem;
  border: 1px solid #cbd5e1;
  outline: none;
  font-family: inherit;
  font-size: 0.95rem;
  transition: all 0.2s;
}

.filter-search input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-status select {
  padding: 0.75rem 2.5rem 0.75rem 1rem;
  border-radius: 0.75rem;
  border: 1px solid #cbd5e1;
  outline: none;
  font-family: inherit;
  font-size: 0.95rem;
  background-color: #fff;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 1rem;
  transition: all 0.2s;
}

.filter-status select:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem 1.25rem;
  text-align: left;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: top;
}

th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.82rem;
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

.customer-block {
  display: grid;
  gap: 0.25rem;
}

.customer-block strong {
  color: #111827;
}

.customer-block small,
td small {
  display: block;
  margin-top: 0.3rem;
  color: #64748b;
}

.money-cell {
  color: #059669;
  font-weight: 800;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2rem;
  padding: 0 0.85rem;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 800;
}

.status-pending {
  background: #fef3c7;
  color: #b45309;
}

.status-paid {
  background: #dcfce7;
  color: #15803d;
}

.status-expired,
.status-failed {
  background: #fee2e2;
  color: #b91c1c;
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

@media (max-width: 1180px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 820px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .table-wrapper {
    overflow-x: auto;
  }

  table {
    min-width: 960px;
  }
}
</style>
