<template>
  <PartnerShell
    title="Hành trình tour"
    :subtitle="`Quản lý điểm đến cho tour ${tour?.ten_tour || maTour}.`"
    :partner="partner"
    :loading="loading"
    :itinerary-path="`/doi-tac/tour/${maTour}/hanh-trinh`"
  >
    <template #headerActions>
      <button class="toolbar-btn toolbar-btn--ghost" type="button" @click="$router.push('/doi-tac/quan-ly-tour')">
        <i class="fas fa-arrow-left"></i>
        Quay lại tour
      </button>
      <button class="toolbar-btn toolbar-btn--ghost" type="button" @click="loadPage">
        <i class="fas fa-rotate"></i>
        Làm mới
      </button>
    </template>

    <section v-if="errorMessage" class="alert-box">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </section>

    <section v-if="successMessage" class="success-box">
      <i class="fas fa-circle-check"></i>
      <span>{{ successMessage }}</span>
    </section>

    <section class="grid">
      <article class="card">
        <h3>Gắn địa điểm có sẵn</h3>
        <div class="field-grid">
          <label class="field">
            <span>Địa điểm *</span>
            <select v-model="attachForm.ma_dia_diem">
              <option value="">Chọn địa điểm</option>
              <option
                v-for="location in locations"
                :key="location.ma_dia_diem"
                :value="location.ma_dia_diem"
              >
                {{ location.ten_dia_diem }} ({{ location.ma_dia_diem }})
              </option>
            </select>
          </label>
          <label class="field">
            <span>Thứ tự hành trình</span>
            <input v-model.number="attachForm.thu_tu_hanh_trinh" type="number" min="1">
          </label>
          <label class="field">
            <span>Ngày hành trình</span>
            <input v-model.number="attachForm.ngay_hanh_trinh" type="number" min="1">
          </label>
          <label class="field field--full">
            <span>Ghi chú</span>
            <input v-model.trim="attachForm.ghi_chu_hanh_trinh" type="text">
          </label>
        </div>
        <button class="action-primary" type="button" :disabled="saving" @click="attachExisting">
          Gắn vào hành trình
        </button>
      </article>

      <article class="card">
        <h3>Tạo địa điểm mới và gắn ngay</h3>
        <div class="field-grid">
          <label class="field">
            <span>Tên địa điểm *</span>
            <input v-model.trim="createForm.ten_dia_diem" type="text">
          </label>
          <label class="field">
            <span>Loại *</span>
            <select v-model.number="createForm.loai">
              <option :value="1">Địa điểm du lịch</option>
              <option :value="2">Khách sạn</option>
              <option :value="3">Nhà hàng</option>
            </select>
          </label>
          <label class="field field--full">
            <span>Địa chỉ *</span>
            <input v-model.trim="createForm.dia_chi" type="text">
          </label>
          <label class="field">
            <span>Kinh độ *</span>
            <input v-model.number="createForm.kinh_do" type="number" step="0.000001">
          </label>
          <label class="field">
            <span>Vĩ độ *</span>
            <input v-model.number="createForm.vi_do" type="number" step="0.000001">
          </label>
          <label class="field">
            <span>Thứ tự hành trình</span>
            <input v-model.number="createForm.thu_tu_hanh_trinh" type="number" min="1">
          </label>
          <label class="field">
            <span>Ngày hành trình</span>
            <input v-model.number="createForm.ngay_hanh_trinh" type="number" min="1">
          </label>
          <label class="field field--full">
            <span>Ghi chú hành trình</span>
            <input v-model.trim="createForm.ghi_chu_hanh_trinh" type="text">
          </label>
        </div>
        <button class="action-primary" type="button" :disabled="saving" @click="createAndAttach">
          Tạo và gắn vào hành trình
        </button>
      </article>
    </section>

    <section class="card">
      <h3>Danh sách hành trình</h3>
      <table v-if="itinerary.length" class="table">
        <thead>
          <tr>
            <th>Ngày</th>
            <th>Thứ tự</th>
            <th>Địa điểm</th>
            <th>Ghi chú</th>
            <th>Cập nhật</th>
            <th>Xóa</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in itinerary" :key="item.ma_chi_tiet_tour">
            <td>
              <input
                v-model.number="editMap[item.ma_chi_tiet_tour].ngay_hanh_trinh"
                type="number"
                min="1"
              >
            </td>
            <td>
              <input
                v-model.number="editMap[item.ma_chi_tiet_tour].thu_tu_hanh_trinh"
                type="number"
                min="1"
              >
            </td>
            <td>
              <strong>{{ item.dia_diem?.ten_dia_diem || item.ma_dia_diem }}</strong>
              <small>{{ item.ma_dia_diem }}</small>
            </td>
            <td>
              <input
                v-model.trim="editMap[item.ma_chi_tiet_tour].ghi_chu_hanh_trinh"
                type="text"
                placeholder="Ghi chú hành trình"
              >
            </td>
            <td>
              <button type="button" :disabled="saving" @click="updateItem(item.ma_chi_tiet_tour)">
                Lưu
              </button>
            </td>
            <td>
              <button type="button" class="danger" :disabled="saving" @click="deleteItem(item.ma_chi_tiet_tour)">
                Xóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="empty-box">Tour này chưa có điểm nào trong hành trình.</div>
    </section>
  </PartnerShell>
</template>

<script>
import PartnerShell from "./layout/PartnerShell.vue";
import {
  attachExistingLocationToTour,
  createAndAttachLocationToTour,
  fetchPartnerLocations,
  fetchPartnerSession,
  fetchPartnerTour,
  fetchTourItinerary,
  removeTourItineraryItem,
  updateTourItineraryItem,
} from "./shared/partnerApi";
import { showConfirm } from "../../services/appDialog.js";

function attachFormDefault() {
  return {
    ma_dia_diem: "",
    thu_tu_hanh_trinh: null,
    ngay_hanh_trinh: 1,
    ghi_chu_hanh_trinh: "",
  };
}

function createFormDefault() {
  return {
    ten_dia_diem: "",
    loai: 1,
    dia_chi: "",
    sdt: "",
    kinh_do: 106.7,
    vi_do: 10.77,
    gio_mo_cua: null,
    gio_dong_cua: null,
    gia_giao_dong: null,
    hinh_anh: null,
    mo_ta: null,
    thoi_gian_tham_quan: null,
    thu_tu_hanh_trinh: null,
    ngay_hanh_trinh: 1,
    ghi_chu_hanh_trinh: "",
  };
}

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "PartnerHanhTrinhTour",
  components: {
    PartnerShell,
  },
  data() {
    return {
      loading: false,
      saving: false,
      partner: null,
      tour: null,
      itinerary: [],
      locations: [],
      editMap: {},
      attachForm: attachFormDefault(),
      createForm: createFormDefault(),
      errorMessage: "",
      successMessage: "",
    };
  },
  computed: {
    maTour() {
      return this.$route.params.ma_tour;
    },
  },
  methods: {
    mapEditRows() {
      const next = {};
      this.itinerary.forEach((item) => {
        next[item.ma_chi_tiet_tour] = {
          ngay_hanh_trinh: Number(item.ngay_hanh_trinh || 1),
          thu_tu_hanh_trinh: Number(item.thu_tu_hanh_trinh || 1),
          ghi_chu_hanh_trinh: item.ghi_chu_hanh_trinh || "",
        };
      });
      this.editMap = next;
    },
    async loadPage() {
      this.loading = true;
      this.errorMessage = "";
      // cleared successMessage

      try {
        const [partner, tour, itinerary, locations] = await Promise.all([
          fetchPartnerSession(),
          fetchPartnerTour(this.maTour),
          fetchTourItinerary(this.maTour),
          fetchPartnerLocations(),
        ]);

        this.partner = partner;
        this.tour = tour;
        this.itinerary = itinerary;
        this.locations = locations;
        this.mapEditRows();
      } catch (error) {
        toaster.error(error?.message || "Không thể tải dữ liệu hành trình.");
      } finally {
        this.loading = false;
      }
    },
    async attachExisting() {
      if (!this.attachForm.ma_dia_diem) {
        toaster.error("Vui lòng chọn địa điểm cần gắn.");
        return;
      }

      this.saving = true;
      this.errorMessage = "";
      // cleared successMessage

      try {
        await attachExistingLocationToTour(this.maTour, this.attachForm);
        toaster.success("Đã gắn địa điểm vào hành trình.");
        this.attachForm = attachFormDefault();
        this.itinerary = await fetchTourItinerary(this.maTour);
        this.mapEditRows();
      } catch (error) {
        toaster.error(error?.message || "Không thể gắn địa điểm vào hành trình.");
      } finally {
        this.saving = false;
      }
    },
    async createAndAttach() {
      if (!this.createForm.ten_dia_diem || !this.createForm.dia_chi) {
        toaster.error("Vui lòng nhập tên và địa chỉ địa điểm.");
        return;
      }

      this.saving = true;
      this.errorMessage = "";
      // cleared successMessage

      try {
        await createAndAttachLocationToTour(this.maTour, this.createForm);
        toaster.success("Đã tạo địa điểm mới và gắn vào hành trình.");
        this.createForm = createFormDefault();
        const [itinerary, locations] = await Promise.all([
          fetchTourItinerary(this.maTour),
          fetchPartnerLocations(),
        ]);
        this.itinerary = itinerary;
        this.locations = locations;
        this.mapEditRows();
      } catch (error) {
        toaster.error(error?.message || "Không thể tạo địa điểm mới cho hành trình.");
      } finally {
        this.saving = false;
      }
    },
    async updateItem(maChiTietTour) {
      const data = this.editMap[maChiTietTour];
      if (!data) return;

      this.saving = true;
      this.errorMessage = "";
      // cleared successMessage

      try {
        await updateTourItineraryItem(this.maTour, maChiTietTour, data);
        toaster.success("Đã cập nhật hành trình.");
        this.itinerary = await fetchTourItinerary(this.maTour);
        this.mapEditRows();
      } catch (error) {
        toaster.error(error?.message || "Không thể cập nhật hành trình.");
      } finally {
        this.saving = false;
      }
    },
    async deleteItem(maChiTietTour) {
      const confirmed = await showConfirm({
        title: "Xác nhận xóa",
        message: "Bạn có chắc muốn xóa điểm này khỏi hành trình?",
        tone: "danger",
        confirmText: "Xóa",
      });
      if (!confirmed) return;

      this.saving = true;
      this.errorMessage = "";
      // cleared successMessage

      try {
        await removeTourItineraryItem(this.maTour, maChiTietTour);
        toaster.success("Đã xóa địa điểm khỏi hành trình.");
        this.itinerary = await fetchTourItinerary(this.maTour);
        this.mapEditRows();
      } catch (error) {
        toaster.error(error?.message || "Không thể xóa điểm khỏi hành trình.");
      } finally {
        this.saving = false;
      }
    },
  },
  mounted() {
    this.loadPage();
  },
};
</script>

<style scoped>
.toolbar-btn {
  min-height: 40px;
  border-radius: 10px;
  background: #fff;
  color: #215b95;
  border: 1px solid #c8dcf2;
  font-weight: 700;
  padding: 0 12px;
  display: inline-flex;
  align-items: center;
  gap: 0.46rem;
}

.alert-box,
.success-box {
  border-radius: 12px;
  padding: 10px 12px;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.alert-box {
  border: 1px solid #ffd1cb;
  background: #fff2f0;
  color: #b42318;
}

.success-box {
  border: 1px solid #bae6c8;
  background: #effdf3;
  color: #147d45;
}

.grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.card {
  border: 1px solid #d0e0f2;
  border-radius: 14px;
  background: #fff;
  padding: 14px;
}

.card h3 {
  margin: 0 0 10px;
}

.field-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.field {
  display: grid;
  gap: 6px;
}

.field--full {
  grid-column: 1 / -1;
}

.field span {
  font-weight: 700;
  color: #2c4d70;
}

.field input,
.field select {
  min-height: 40px;
  border-radius: 10px;
  border: 1px solid #cfdeef;
  background: #fff;
  padding: 8px 10px;
}

.action-primary {
  margin-top: 10px;
  min-height: 34px;
  border: 0;
  border-radius: 9px;
  color: #fff;
  background: linear-gradient(135deg, #0f73cd, #149adf);
  padding: 0 12px;
  font-weight: 700;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 11px 8px;
  border-bottom: 1px solid #e6eef8;
  text-align: left;
  vertical-align: middle;
}

.table th {
  color: #5c7794;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.table input {
  min-height: 34px;
  border-radius: 8px;
  border: 1px solid #cbdced;
  padding: 6px 8px;
}

.table button {
  min-height: 34px;
  border: 1px solid #c8daee;
  border-radius: 8px;
  background: #fff;
  color: #235d98;
  font-weight: 700;
  padding: 0 10px;
}

.table .danger {
  border-color: #fecdd3;
  color: #a51e2d;
  background: #fff1f2;
}

.table strong,
.table small {
  display: block;
}

.empty-box {
  min-height: 100px;
  display: grid;
  place-items: center;
  color: #54708d;
}

@media (max-width: 1040px) {
  .grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 720px) {
  .field-grid {
    grid-template-columns: 1fr;
  }

  .table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
}
</style>
