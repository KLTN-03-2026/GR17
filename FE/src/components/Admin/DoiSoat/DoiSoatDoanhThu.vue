<template>
  <div class="doi-soat-container">
    <div class="page-header">
      <div class="title-group">
        <h2>Đối soát Doanh Thu Đối Tác</h2>
        <p>Quản lý dòng tiền hoa hồng và lịch sử thanh toán giữa Nền tảng và Đối tác.</p>
      </div>
      <button class="action-btn" @click="fetchData">
        <i class="fas fa-rotate"></i> Làm mới
      </button>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon"><i class="fas fa-wallet"></i></div>
        <div class="stat-info">
          <h3>Tổng Doanh Thu (Hệ thống)</h3>
          <p class="stat-value">{{ formatCurrency(stats.tong_doanh_thu) }}</p>
        </div>
      </div>
      <div class="stat-card success">
        <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
        <div class="stat-info">
          <h3>Tiền Hoa Hồng Cắt Phế (10%)</h3>
          <p class="stat-value">{{ formatCurrency(stats.tong_hoa_hong_admin) }}</p>
        </div>
      </div>
      <div class="stat-card warning">
        <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="stat-info">
          <h3>Đang nợ Đối tác</h3>
          <p class="stat-value">{{ formatCurrency(stats.tong_cho_thanh_toan) }}</p>
        </div>
      </div>
    </div>

    <!-- Bảng danh sách đối soát -->
    <div class="table-card" v-if="!loading">
      <table v-if="list.length" class="table">
        <thead>
          <tr>
            <th>Mã HĐ</th>
            <th>Mã Đối tác</th>
            <th>Loại G.Dịch</th>
            <th>Nội dung</th>
            <th>Hoa hồng (Admin)</th>
            <th>Thực nhận (Đối tác)</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in list" :key="item.ma_doi_soat">
            <td><strong>#{{ item.ma_hoa_don }}</strong></td>
            <td>{{ item.ma_doi_tac }}</td>
            <td>
              <span class="badge" :class="item.loai_giao_dich === 'tour' ? 'badge--info' : 'badge--draft'">
                {{ item.loai_giao_dich.toUpperCase() }}
              </span>
            </td>
            <td>{{ item.mo_ta }}</td>
            <td class="text-success fw-bold">{{ formatCurrency(item.tien_hoa_hong_admin) }}</td>
            <td class="text-warning fw-bold">{{ formatCurrency(item.tien_doi_tac_thuc_nhan) }}</td>
            <td>
               <span 
                 class="badge" 
                 :class="item.trang_thai_thanh_toan === 'da_chuyen_khoan' ? 'badge--approved' : 'badge--pending'"
               >
                 {{ item.trang_thai_thanh_toan === 'da_chuyen_khoan' ? 'Đã thanh toán' : 'Chưa đối soát' }}
               </span>
            </td>
            <td>
              <button 
                v-if="item.trang_thai_thanh_toan === 'chua_doi_soat'" 
                class="action-btn-sm warning"
                @click="markAsPaid(item)"
              >
                Đánh dấu đã CK
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="empty-state">
        <i class="fas fa-box-open"></i>
        <p>Chưa có dữ liệu đối soát doanh thu.</p>
      </div>
    </div>
    <div v-else class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient";
import { showConfirm, showAlert } from "../../../services/appDialog";

export default {
  name: "DoiSoatDoanhThu",
  data() {
    return {
      loading: true,
      stats: {
        tong_doanh_thu: 0,
        tong_hoa_hong_admin: 0,
        tong_cho_thanh_toan: 0,
      },
      list: [],
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    formatCurrency(val) {
      if (!val) return "0 VNĐ";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
    },
    async fetchData() {
      this.loading = true;
      try {
        const res = await goiApi("/admin/doi-soat");
        if (res.data?.success) {
          this.stats = res.data.data.thong_ke;
          this.list = res.data.data.danh_sach;
        }
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    async markAsPaid(item) {
      const ok = await showConfirm({
        title: "Xác nhận Thanh toán",
        message: `Xác nhận bạn đã chuyển khoản số tiền ${this.formatCurrency(item.tien_doi_tac_thuc_nhan)} cho đối tác ${item.ma_doi_tac}?`,
        tone: "success",
        confirmText: "Đã chuyển khoản",
      });
      if (!ok) return;

      try {
        await goiApi(`/admin/doi-soat/${item.ma_doi_soat}/pay`, { method: "PATCH" });
        await this.fetchData();
        showAlert({ title: "Thành công", message: "Đã đánh dấu thanh toán hoàn tất.", tone: "success" });
      } catch (err) {
        showAlert({ title: "Lỗi", message: err.response?.data?.message || err.message, tone: "danger" });
      }
    }
  }
}
</script>

<style scoped>
.doi-soat-container {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}
.title-group h2 {
  margin: 0 0 0.5rem 0;
  color: #0f172a;
  font-size: 1.5rem;
  font-weight: 800;
}
.title-group p {
  margin: 0;
  color: #64748b;
}

.action-btn {
  padding: 0.6rem 1.25rem;
  background: #fff;
  border: 1px solid #cbd5e1;
  border-radius: 0.5rem;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s;
}
.action-btn:hover {
  background: #f8fafc;
  color: #0f172a;
}
.action-btn-sm {
  padding: 0.4rem 0.75rem;
  font-size: 0.8rem;
  border: none;
  font-weight: 600;
  border-radius: 0.4rem;
  cursor: pointer;
}
.action-btn-sm.warning {
  background: #fef08a;
  color: #854d0e;
}
.action-btn-sm.warning:hover {
  background: #fde047;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}
.stat-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.5rem;
  background: #fff;
  border-radius: 1rem;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.stat-icon {
  width: 3.5rem;
  height: 3.5rem;
  display: grid;
  place-items: center;
  border-radius: 1rem;
  font-size: 1.5rem;
}
.stat-card.primary .stat-icon { background: #dbeafe; color: #1d4ed8; }
.stat-card.success .stat-icon { background: #dcfce7; color: #15803d; }
.stat-card.warning .stat-icon { background: #ffedd5; color: #ea580c; }

.stat-info h3 {
  margin: 0 0 0.25rem 0;
  font-size: 0.875rem;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.stat-value {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 800;
  color: #0f172a;
}

.table-card {
  background: #fff;
  border-radius: 1rem;
  border: 1px solid #e2e8f0;
  overflow: auto;
}
.table {
  width: 100%;
  border-collapse: collapse;
}
.table th, .table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #f1f5f9;
}
.table th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
}
.table tbody tr:hover {
  background: #f8fbff;
}
.text-success { color: #15803d; }
.text-warning { color: #ea580c; }
.fw-bold { font-weight: 700; }

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
}
.badge--info { background: #e0f2fe; color: #0369a1; }
.badge--draft { background: #f1f5f9; color: #475569; }
.badge--approved { background: #dcfce7; color: #15803d; }
.badge--pending { background: #ffedd5; color: #ea580c; }

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #64748b;
}
.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #cbd5e1;
}
.loading-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
  font-size: 1.125rem;
}
</style>
