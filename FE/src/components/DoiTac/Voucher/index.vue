<template>
  <div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-ticket-alt text-primary mr-2"></i> Quản lý Mã Giảm Giá Của Bạn
      </h1>
      <button @click="openModal()" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tạo mã mới
      </button>
    </div>

    <div class="card shadow mb-4">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead class="thead-light">
              <tr>
                <th>Mã Voucher</th>
                <th>Tên chương trình</th>
                <th>Mức giảm</th>
                <th>Tối thiểu</th>
                <th>Đã dùng / Tổng</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th class="text-center">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading" class="text-center">
                <td colspan="8">Đang tải dữ liệu...</td>
              </tr>
              <tr v-else-if="vouchers.length === 0" class="text-center">
                <td colspan="8">Chưa có mã giảm giá nào.</td>
              </tr>
              <tr v-for="item in vouchers" :key="item.id">
                <td class="font-weight-bold text-primary">{{ item.ma_voucher }}</td>
                <td>{{ item.ten_voucher }}</td>
                <td>
                  <span v-if="item.loai_giam_gia === 'percent'">{{ item.gia_tri_giam }}%</span>
                  <span v-else>{{ formatCurrency(item.gia_tri_giam) }}</span>
                  <div class="small text-muted" v-if="item.loai_giam_gia === 'percent' && item.giam_toi_da">
                    (Tối đa: {{ formatCurrency(item.giam_toi_da) }})
                  </div>
                </td>
                <td>{{ formatCurrency(item.don_toi_thieu) }}</td>
                <td>{{ item.da_su_dung }} / {{ item.so_luong }}</td>
                <td class="small">
                  Từ: {{ formatDate(item.ngay_bat_dau) }} <br>
                  Đến: {{ formatDate(item.ngay_ket_thuc) }}
                </td>
                <td>
                  <span v-if="item.trang_thai" class="badge badge-success" style="cursor: pointer;" @click="toggleStatus(item.id)" title="Nhấn để Khóa">Kích hoạt</span>
                  <span v-else class="badge badge-secondary" style="cursor: pointer;" @click="toggleStatus(item.id)" title="Nhấn để Kích hoạt">Đã khóa</span>
                </td>
                <td class="text-center">
                  <button @click="openModal(item)" class="btn btn-sm btn-info shadow-sm mr-2" title="Sửa">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="deleteVoucher(item.id)" class="btn btn-sm btn-danger shadow-sm" title="Xóa" :disabled="item.da_su_dung > 0">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Cập nhật mã giảm giá' : 'Tạo mã giảm giá mới' }}</h5>
            <button type="button" class="close" @click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveVoucher">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Mã Voucher (Code) <span class="text-danger">*</span></label>
                  <input v-model="form.ma_voucher" type="text" class="form-control" required :disabled="isEditing" style="text-transform: uppercase;">
                  <small class="text-muted">Viết liền không dấu, VD: KHUYENMAI20</small>
                </div>
                <div class="col-md-6 form-group">
                  <label>Tên chương trình <span class="text-danger">*</span></label>
                  <input v-model="form.ten_voucher" type="text" class="form-control" required>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-4 form-group">
                  <label>Loại giảm giá <span class="text-danger">*</span></label>
                  <select v-model="form.loai_giam_gia" class="form-control" required>
                    <option value="fixed">Số tiền cố định (VNĐ)</option>
                    <option value="percent">Phần trăm (%)</option>
                  </select>
                </div>
                <div class="col-md-4 form-group">
                  <label>Giá trị giảm <span class="text-danger">*</span></label>
                  <input v-model="form.gia_tri_giam" type="number" class="form-control" required min="0">
                </div>
                <div class="col-md-4 form-group" v-if="form.loai_giam_gia === 'percent'">
                  <label>Giảm tối đa (VNĐ)</label>
                  <input v-model="form.giam_toi_da" type="number" class="form-control" min="0">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Đơn tối thiểu (VNĐ) <span class="text-danger">*</span></label>
                  <input v-model="form.don_toi_thieu" type="number" class="form-control" required min="0">
                </div>
                <div class="col-md-6 form-group">
                  <label>Số lượng phát hành <span class="text-danger">*</span></label>
                  <input v-model="form.so_luong" type="number" class="form-control" required min="1">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Ngày bắt đầu <span class="text-danger">*</span></label>
                  <input v-model="form.ngay_bat_dau" type="datetime-local" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Ngày kết thúc <span class="text-danger">*</span></label>
                  <input v-model="form.ngay_ket_thuc" type="datetime-local" class="form-control" required>
                </div>
              </div>

              <div class="form-group form-check">
                <input v-model="form.trang_thai" type="checkbox" class="form-check-input" id="trangThaiCheck">
                <label class="form-check-label" for="trangThaiCheck">Kích hoạt ngay</label>
              </div>

              <div class="text-right mt-4">
                <button type="button" class="btn btn-secondary mr-2" @click="closeModal">Hủy</button>
                <button type="submit" class="btn btn-primary" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm mr-1"></span>
                  Lưu lại
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { goiApi } from "../../../services/httpClient.js";
import { createToaster } from "@meforma/vue-toaster";
import { showConfirm, showAlert } from "../../../services/appDialog.js";

export default {
  name: 'DoiTacVoucher',
  setup() {
    const toaster = createToaster({ position: "top-right" });
    const vouchers = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const showModal = ref(false);
    const isEditing = ref(false);
    const editingId = ref(null);

    const defaultForm = {
      ma_voucher: '',
      ten_voucher: '',
      loai_giam_gia: 'fixed',
      gia_tri_giam: 0,
      giam_toi_da: null,
      don_toi_thieu: 0,
      so_luong: 100,
      ngay_bat_dau: '',
      ngay_ket_thuc: '',
      trang_thai: true
    };
    const form = ref({ ...defaultForm });

    const fetchVouchers = async () => {
      loading.value = true;
      try {
        const response = await goiApi('/api/doi-tac/vouchers');
        const data = await response.json();
        if (data.success) {
          vouchers.value = data.data.data || data.data;
        }
      } catch (error) {
        toaster.error("Không thể tải dữ liệu voucher");
      } finally {
        loading.value = false;
      }
    };

    const formatDateForInput = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toISOString().slice(0, 16);
    };

    const openModal = (item = null) => {
      if (item) {
        isEditing.value = true;
        editingId.value = item.id;
        form.value = {
          ...item,
          ngay_bat_dau: formatDateForInput(item.ngay_bat_dau),
          ngay_ket_thuc: formatDateForInput(item.ngay_ket_thuc),
          trang_thai: Boolean(item.trang_thai)
        };
      } else {
        isEditing.value = false;
        editingId.value = null;
        form.value = { ...defaultForm };
        const now = new Date();
        const nextWeek = new Date(now);
        nextWeek.setDate(now.getDate() + 7);
        form.value.ngay_bat_dau = now.toISOString().slice(0, 16);
        form.value.ngay_ket_thuc = nextWeek.toISOString().slice(0, 16);
      }
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
    };

    const saveVoucher = async () => {
      saving.value = true;
      try {
        const url = isEditing.value 
          ? `/api/doi-tac/vouchers/${editingId.value}`
          : '/api/doi-tac/vouchers';
        
        const method = isEditing.value ? 'PUT' : 'POST';
        
        const payload = { ...form.value };
        if (payload.loai_giam_gia === 'fixed') {
          payload.giam_toi_da = null;
        }

        const response = await goiApi(url, {
          method,
          body: payload
        });
        const data = await response.json();

        if (data.success) {
          toaster.success(data.message || "Thao tác thành công");
          closeModal();
          fetchVouchers();
        }
      } catch (error) {
        toaster.error(error.response?.data?.message || error.message || "Có lỗi xảy ra khi lưu");
      } finally {
        saving.value = false;
      }
    };

    const deleteVoucher = async (id) => {
      const isConfirmed = await showConfirm({
        title: 'Xác nhận xóa',
        message: 'Bạn có chắc chắn muốn xóa mã giảm giá này không?',
        tone: 'danger',
        confirmText: 'Xóa',
        cancelText: 'Hủy'
      });

      if (!isConfirmed) return;

      try {
        const response = await goiApi(`/api/doi-tac/vouchers/${id}`, {
          method: 'DELETE'
        });
        const data = await response.json();
        if (data.success) {
          showAlert({
            title: 'Đã xóa!',
            message: 'Mã giảm giá đã được xóa thành công.',
            tone: 'success'
          });
          fetchVouchers();
        }
      } catch (error) {
        toaster.error(error.response?.data?.message || "Không thể xóa mã này");
      }
    };

    const toggleStatus = async (id) => {
      try {
        const response = await goiApi(`/api/doi-tac/vouchers/${id}/status`, {
          method: 'PATCH'
        });
        const data = await response.json();
        if (data.success) {
          toaster.success(data.message || "Đổi trạng thái thành công");
          fetchVouchers();
        } else {
          toaster.error(data.message || "Không thể đổi trạng thái");
        }
      } catch (error) {
        toaster.error("Lỗi khi đổi trạng thái");
      }
    };

    const formatCurrency = (value) => {
      if (!value) return "0 đ";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    };

    const formatDate = (dateString) => {
      if (!dateString) return '';
      return new Date(dateString).toLocaleString('vi-VN');
    };

    onMounted(() => {
      fetchVouchers();
    });

    return {
      vouchers,
      loading,
      saving,
      showModal,
      isEditing,
      form,
      openModal,
      closeModal,
      saveVoucher,
      deleteVoucher,
      toggleStatus,
      formatCurrency,
      formatDate
    };
  }
};
</script>
