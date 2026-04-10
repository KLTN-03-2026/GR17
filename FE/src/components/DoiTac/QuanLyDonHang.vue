<template>
  <PartnerShell
    title="Quản lý Đơn hàng"
    subtitle="Danh sách các khách hàng đã mua Tour của bạn."
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

    <!-- BẢNG ĐƠN HÀNG -->
    <section class="table-container" style="margin-top: 1rem;">
      <div v-if="!danhSach.length" class="empty-state">
        <i class="fas fa-clipboard-list"></i>
        <p>Chưa có ai đặt Tour của bạn cả. Hãy tạo thêm các Tour hấp dẫn nhé!</p>
      </div>

      <div class="table-wrapper" v-else>
        <table>
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Mã Tour</th>
              <th>Khách Đặt</th>
              <th>Tổng Tiền</th>
              <th>Ngày Đặt</th>
              <th>Trạng Thái</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in danhSach" :key="item.ma_hoa_don">
              <td>
                <strong>{{ item.ma_hoa_don }}</strong>
              </td>
              <td>{{ item.ma_doi_tuong }}</td>
              <td>
                <div class="user-info" v-if="item.nhom">
                  <div class="user-avatar">
                   <i class="fas fa-user"></i>
                  </div>
                  <div>
                    <strong>Cần {{ item.nhom.So_luong_thanh_vien }} chỗ</strong>
                  </div>
                </div>
                <span v-else>Khách lẻ</span>
              </td>
              <td style="color: #10b981; font-weight: 700;">{{ formatCurrency(item.tong_tien) }}</td>
              <td>{{ formatDate(item.ngay_tao) }}</td>
              <td>
                <span :class="['status-badge', item.trang_thai_thanh_toan === 1 ? 'status-paid' : 'status-pending']">
                  {{ item.trang_thai_thanh_toan === 1 ? 'Đã Thanh Toán' : 'Chưa Thanh Toán' }}
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
      this.errorMessage = "";
      try {
        const response = await goiApi("/api/doi-tac/don-hang");
        const payload = await response.json();
        
        if (!response.ok) {
          throw new Error(payload.message || "Lỗi tải dữ liệu đơn hàng.");
        }
        
        this.danhSach = payload.data;
      } catch (error) {
        console.error(error);
        this.errorMessage = error.message;
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

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
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
</style>
