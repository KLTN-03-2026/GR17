<template>
  <div class="container-fluid py-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-comments text-primary mr-2"></i> Quản lý Phản Hồi
      </h1>
      <p class="mb-0 text-muted small mt-2 mt-sm-0">Theo dõi và xử lý các ý kiến, sự cố từ người dùng</p>
    </div>

    <!-- Filters & Actions -->
    <div class="card shadow mb-4">
      <div class="card-body py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
        <form class="form-inline w-100 mb-2 mb-md-0">
          <label class="font-weight-bold mr-3">Bộ lọc:</label>
          <div class="input-group mr-3 flex-grow-1" style="max-width: 300px;">
            <div class="input-group-prepend">
              <span class="input-group-text bg-white"><i class="fas fa-filter text-muted"></i></span>
            </div>
            <select v-model="filterStatus" @change="fetchPhanHois(1)" class="custom-select">
              <option value="all">Tất cả trạng thái</option>
              <option value="Mới">Mới</option>
              <option value="Đang xử lý">Đang xử lý</option>
              <option value="Đã giải quyết">Đã giải quyết</option>
            </select>
          </div>
        </form>
        <button @click="fetchPhanHois(1)" class="btn btn-outline-primary shadow-sm w-100" style="max-width: 150px;">
          <i class="fas fa-sync-alt" :class="{'fa-spin': loading}"></i> Làm mới
        </button>
      </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4 position-relative">
      
      <!-- Loading Overlay -->
      <div v-if="loading" class="position-absolute w-100 h-100 d-flex flex-column justify-content-center align-items-center" style="background: rgba(255,255,255,0.8); z-index: 10; top: 0; left: 0; border-radius: 0.35rem;">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <span class="mt-2 text-primary font-weight-bold">Đang tải dữ liệu...</span>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead class="thead-light">
              <tr>
                <th class="text-center" width="80">Mã</th>
                <th>Khách hàng</th>
                <th>Tiêu đề / Nội dung</th>
                <th class="text-center" width="120">Phân loại</th>
                <th width="160">Trạng thái</th>
                <th class="text-center" width="100">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!loading && phanHois.length === 0">
                <td colspan="6" class="text-center py-5">
                  <div class="text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i>
                    <p class="h5">Chưa có phản hồi nào</p>
                    <p class="small">Không tìm thấy dữ liệu phù hợp với bộ lọc hiện tại.</p>
                  </div>
                </td>
              </tr>
              <tr v-for="item in phanHois" :key="item.id">
                <td class="text-center align-middle font-weight-bold text-gray-600">#{{ item.id }}</td>
                <td class="align-middle">
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem; flex-shrink: 0;">
                      {{ item.khach_hang ? item.khach_hang.Ho_va_ten.charAt(0).toUpperCase() : 'V' }}
                    </div>
                    <div>
                      <div class="font-weight-bold text-dark">{{ item.khach_hang ? item.khach_hang.Ho_va_ten : 'Khách vãng lai' }}</div>
                      <div class="small text-muted"><i class="far fa-clock mr-1"></i>{{ formatRelativeTime(item.created_at) }}</div>
                    </div>
                  </div>
                </td>
                <td class="align-middle">
                  <div class="font-weight-bold text-dark text-truncate" style="max-width: 250px;" :title="item.tieu_de">{{ item.tieu_de }}</div>
                  <div class="small text-muted text-truncate" style="max-width: 250px;" :title="item.mo_ta">{{ item.mo_ta }}</div>
                </td>
                <td class="text-center align-middle">
                  <span :class="getTypeBadgeClass(item.loai)" class="badge badge-pill p-2" style="font-size: 0.85rem;">
                    <i :class="getTypeIcon(item.loai)" class="mr-1"></i> {{ item.loai }}
                  </span>
                </td>
                <td class="align-middle">
                  <select v-model="item.trang_thai" @change="updateStatus(item.id, item.trang_thai)" 
                          :class="getStatusSelectClass(item.trang_thai)"
                          class="custom-select custom-select-sm font-weight-bold">
                    <option value="Mới" class="text-dark">Mới</option>
                    <option value="Đang xử lý" class="text-dark">Đang xử lý</option>
                    <option value="Đã giải quyết" class="text-dark">Đã giải quyết</option>
                  </select>
                </td>
                <td class="text-center align-middle">
                  <button @click="viewDetails(item)" class="btn btn-sm btn-info shadow-sm" title="Xem chi tiết">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="d-flex justify-content-between align-items-center mt-3">
          <div class="small text-muted">
            Hiển thị trang <span class="font-weight-bold text-dark">{{ currentPage }}</span> / <span class="font-weight-bold text-dark">{{ totalPages }}</span>
          </div>
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button class="page-link" @click="fetchPhanHois(currentPage - 1)">Trước</button>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <button class="page-link" @click="fetchPhanHois(currentPage + 1)">Sau</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Detail Modal (Bootstrap style) -->
    <div v-if="selectedItem" class="modal fade show" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.6); overflow-y: auto;">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
          
          <div class="modal-header bg-light border-bottom-0">
            <h5 class="modal-title font-weight-bold text-primary">
              <i class="fas fa-ticket-alt mr-2"></i>Chi tiết phản hồi <span class="text-dark">#{{ selectedItem.id }}</span>
            </h5>
            <button type="button" class="close" @click="closeDetails" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <div class="modal-body p-4">
            
            <div class="row mb-4">
              <!-- Left Info Column -->
              <div class="col-md-7 mb-3 mb-md-0">
                <h6 class="font-weight-bold text-uppercase text-muted small mb-3">
                  <i class="fas fa-user mr-1"></i> Thông tin người gửi
                </h6>
                <div class="card bg-light border-0 shadow-sm h-100">
                  <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 50px; height: 50px; font-weight: bold; font-size: 1.5rem; flex-shrink: 0;">
                      {{ selectedItem.khach_hang ? selectedItem.khach_hang.Ho_va_ten.charAt(0).toUpperCase() : 'V' }}
                    </div>
                    <div>
                      <h6 class="font-weight-bold mb-1 text-dark">{{ selectedItem.khach_hang ? selectedItem.khach_hang.Ho_va_ten : 'Khách vãng lai' }}</h6>
                      <div class="small text-muted mb-1">
                        <i class="fas fa-phone fa-fw"></i> {{ selectedItem.khach_hang && selectedItem.khach_hang.So_dien_thoai ? selectedItem.khach_hang.So_dien_thoai : 'Không có SĐT' }}
                      </div>
                      <div class="small text-muted">
                        <i class="far fa-calendar-alt fa-fw"></i> {{ formatDate(selectedItem.created_at) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Status Column -->
              <div class="col-md-5">
                <h6 class="font-weight-bold text-uppercase text-muted small mb-3">
                  <i class="fas fa-tasks mr-1"></i> Trạng thái xử lý
                </h6>
                <div class="card bg-light border-0 shadow-sm h-100">
                  <div class="card-body p-3 d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <span class="small text-muted font-weight-bold">Phân loại:</span>
                      <span :class="getTypeBadgeClass(selectedItem.loai)" class="badge p-2">
                        <i :class="getTypeIcon(selectedItem.loai)" class="mr-1"></i> {{ selectedItem.loai }}
                      </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="small text-muted font-weight-bold">Trạng thái:</span>
                      <span :class="getStatusBadgeClass(selectedItem.trang_thai)" class="badge p-2" style="font-size: 0.9rem;">
                        {{ selectedItem.trang_thai }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Content Area -->
            <div class="card border-left-primary shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-dark">{{ selectedItem.tieu_de }}</h6>
              </div>
              <div class="card-body text-gray-800" style="white-space: pre-line; min-height: 100px; background-color: #f8f9fc;">
                {{ selectedItem.mo_ta }}
              </div>
            </div>

            <!-- Error URL if present -->
            <div v-if="selectedItem.url_trang_loi" class="card border-left-danger shadow-sm">
              <div class="card-body py-3 d-flex align-items-start">
                <i class="fas fa-link text-danger mt-1 mr-3"></i>
                <div style="overflow: hidden;">
                  <h6 class="font-weight-bold text-danger mb-1 small">Đường dẫn xảy ra lỗi</h6>
                  <a :href="selectedItem.url_trang_loi" target="_blank" class="text-danger small" style="word-break: break-all;">
                    {{ selectedItem.url_trang_loi }}
                  </a>
                </div>
              </div>
            </div>

          </div>
          
          <div class="modal-footer border-top-0 bg-light">
            <button type="button" class="btn btn-secondary shadow-sm" @click="closeDetails">Đóng</button>
            <button v-if="selectedItem.trang_thai !== 'Đã giải quyết'" type="button" class="btn btn-success shadow-sm" @click="markAsResolved(selectedItem.id)">
              <i class="fas fa-check mr-1"></i> Đánh dấu đã giải quyết
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { createToaster } from "@meforma/vue-toaster";

export default {
  name: 'AdminPhanHoi',
  setup() {
    const toaster = createToaster({ position: "top-right" });
    const phanHois = ref([]);
    const loading = ref(false);
    const filterStatus = ref('all');
    const currentPage = ref(1);
    const totalPages = ref(1);
    const selectedItem = ref(null);

    const getHeaders = () => {
      const token = localStorage.getItem('admin_token');
      return {
        Authorization: `Bearer ${token}`
      };
    };

    const fetchPhanHois = async (page = 1) => {
      loading.value = true;
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/admin/phan-hoi`, {
          headers: getHeaders(),
          params: {
            page: page,
            trang_thai: filterStatus.value
          }
        });
        
        if (response.data.success) {
          phanHois.value = response.data.data.data;
          currentPage.value = response.data.data.current_page;
          totalPages.value = response.data.data.last_page;
        }
      } catch (error) {
        console.error("Error fetching phan hoi:", error);
        toaster.error("Không thể tải danh sách phản hồi");
      } finally {
        loading.value = false;
      }
    };

    const updateStatus = async (id, newStatus) => {
      try {
        const response = await axios.put(`http://127.0.0.1:8000/api/admin/phan-hoi/${id}/trang-thai`, {
          trang_thai: newStatus
        }, {
          headers: getHeaders()
        });

        if (response.data.success) {
          toaster.success("Cập nhật trạng thái thành công!");
          if (selectedItem.value && selectedItem.value.id === id) {
            selectedItem.value.trang_thai = newStatus;
          }
          const itemInList = phanHois.value.find(item => item.id === id);
          if (itemInList) {
            itemInList.trang_thai = newStatus;
          }
        }
      } catch (error) {
        console.error("Error updating status:", error);
        toaster.error("Cập nhật trạng thái thất bại");
        fetchPhanHois(currentPage.value); 
      }
    };

    const markAsResolved = async (id) => {
      await updateStatus(id, 'Đã giải quyết');
    };

    const viewDetails = (item) => {
      selectedItem.value = { ...item };
    };

    const closeDetails = () => {
      selectedItem.value = null;
    };

    const formatDate = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit'
      }).format(date);
    };

    const formatRelativeTime = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      const now = new Date();
      const diffInSeconds = Math.floor((now - date) / 1000);
      
      if (diffInSeconds < 60) return 'Vừa xong';
      const diffInMinutes = Math.floor(diffInSeconds / 60);
      if (diffInMinutes < 60) return `${diffInMinutes} phút trước`;
      const diffInHours = Math.floor(diffInMinutes / 60);
      if (diffInHours < 24) return `${diffInHours} giờ trước`;
      const diffInDays = Math.floor(diffInHours / 24);
      if (diffInDays < 7) return `${diffInDays} ngày trước`;
      
      return formatDate(dateString);
    };

    const getTypeIcon = (type) => {
      switch (type) {
        case 'Lỗi': return 'fas fa-bug';
        case 'Góp ý': return 'fas fa-lightbulb';
        default: return 'fas fa-comment-dots';
      }
    };

    const getTypeBadgeClass = (type) => {
      switch (type) {
        case 'Lỗi': return 'badge-danger';
        case 'Góp ý': return 'badge-success';
        default: return 'badge-secondary';
      }
    };

    const getStatusBadgeClass = (status) => {
      switch (status) {
        case 'Mới': return 'badge-warning text-dark';
        case 'Đang xử lý': return 'badge-primary';
        case 'Đã giải quyết': return 'badge-success';
        default: return 'badge-secondary';
      }
    };

    const getStatusSelectClass = (status) => {
      switch (status) {
        case 'Mới': return 'text-warning border-warning bg-light';
        case 'Đang xử lý': return 'text-primary border-primary bg-light';
        case 'Đã giải quyết': return 'text-success border-success bg-light';
        default: return 'text-secondary border-secondary bg-light';
      }
    };

    onMounted(() => {
      fetchPhanHois();
    });

    return {
      phanHois,
      loading,
      filterStatus,
      currentPage,
      totalPages,
      selectedItem,
      fetchPhanHois,
      updateStatus,
      markAsResolved,
      viewDetails,
      closeDetails,
      formatDate,
      formatRelativeTime,
      getTypeIcon,
      getTypeBadgeClass,
      getStatusBadgeClass,
      getStatusSelectClass
    };
  }
};
</script>

<style scoped>
/* 
  Custom styles for modal transitions 
  (Bootstrap handles most styling, we just add slight adjustments)
*/
.modal {
  transition: opacity 0.15s linear;
}
.modal.show {
  opacity: 1;
}
.modal-content {
  border-radius: 0.5rem;
}
</style>
