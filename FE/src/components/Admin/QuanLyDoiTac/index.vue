<template>
  <div class="partner-page">
    <section class="partner-page__hero">
      <div>
        <p class="partner-page__eyebrow">Bảng Điều Khiển Quản Trị</p>
        <h1>Quản lý đối tác</h1>
        <p class="partner-page__subtitle">
          Xét duyệt đăng ký đối tác mới, quản lý thông tin các nhà cung cấp tour và địa điểm trên nền tảng.
        </p>
      </div>

      <div class="partner-page__hero-actions">
        <button class="ghost-button" type="button" @click="layDanhSach" :disabled="dang_tai">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dang_tai ? "Đang tải..." : "Tải lại dữ liệu" }}</span>
        </button>
      </div>
    </section>

    <section class="stats-grid">
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--blue">
          <i class="fas fa-handshake"></i>
        </div>
        <div>
          <span>Tổng đối tác</span>
          <strong>{{ danh_sach.length.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--amber">
          <i class="fas fa-hourglass-half"></i>
        </div>
        <div>
          <span>Chờ duyệt</span>
          <strong>{{ tong_cho_duyet.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--green">
          <i class="fas fa-check-circle"></i>
        </div>
        <div>
          <span>Đã duyệt (Hoạt động)</span>
          <strong>{{ tong_hoat_dong.toLocaleString() }}</strong>
        </div>
      </article>
      
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--rose">
          <i class="fas fa-ban"></i>
        </div>
        <div>
          <span>Bị từ chối / Khóa</span>
          <strong>{{ tong_tu_choi.toLocaleString() }}</strong>
        </div>
      </article>
    </section>

    <section class="filter-bar">
      <label class="filter-search">
        <i class="fas fa-search"></i>
        <input
          v-model.trim="tu_khoa"
          type="text"
          placeholder="Tìm theo tên công ty, mã, email..."
        >
      </label>
      
      <select v-model="loc_trang_thai" class="filter-select">
        <option value="all">Trạng thái: Tất cả</option>
        <option value="pending">Chờ duyệt</option>
        <option value="approved">Đã duyệt</option>
        <option value="rejected">Từ chối / Bị khóa</option>
      </select>

      <button class="filter-apply" type="button" @click="apDungLoc">
        Áp dụng bộ lọc
      </button>
    </section>

    <div v-if="thong_bao_loi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thong_bao_loi }}</span>
    </div>

    <section class="content-grid">
      <article class="table-card">
        <div class="table-head">
          <div>
            <h2>Danh sách Đối tác</h2>
            <p>{{ danh_sach_da_loc.length }} đối tác phù hợp điều kiện hiện tại.</p>
          </div>
          <span class="table-chip">{{ dang_tai ? "Đồng bộ..." : "Dữ liệu trực tiếp" }}</span>
        </div>

        <div class="partner-table">
          <div class="partner-table__row partner-table__row--head">
            <span>Đối tác</span>
            <span>Mã ĐT</span>
            <span>Trạng thái</span>
            <span>Ngày đăng ký</span>
          </div>

          <div
            v-for="doi_tac in danh_sach_phan_trang"
            :key="doi_tac.ma_doi_tac"
            class="partner-table__row"
            :class="{ 'is-selected': doi_tac_dang_chon && doi_tac_dang_chon.ma_doi_tac === doi_tac.ma_doi_tac }"
            @click="doi_tac_dang_chon = doi_tac"
          >
            <div class="partner-person">
              <div class="partner-person__avatar" :style="{ background: doi_tac.avatarGradient }">
                <i class="fas fa-building"></i>
              </div>
              <div>
                <strong>{{ doi_tac.ten_doi_tac }}</strong>
                <small>{{ doi_tac.email }}</small>
                <small>ĐD: {{ doi_tac.ten_nguoi_dai_dien }}</small>
              </div>
            </div>

            <div class="partner-id">
              <strong>{{ doi_tac.ma_doi_tac }}</strong>
            </div>

            <span :class="['pill', getStatusClass(doi_tac)]">
              {{ getStatusLabel(doi_tac) }}
            </span>

            <span class="muted">{{ doi_tac.createdAtLabel }}</span>
          </div>
          <div v-if="danh_sach_phan_trang.length === 0" style="padding: 2rem; text-align: center; color: #64748b;">
            Không có dữ liệu
          </div>
        </div>

        <footer class="table-footer">
          <span>
            Hiển thị {{ bat_dau_footer }}-{{ ket_thuc_footer }} trên tổng {{ danh_sach_da_loc.length.toLocaleString() }} đối tác
          </span>

          <div class="pagination">
            <button class="page-btn" type="button" :disabled="trang === 1" @click="trang = Math.max(1, trang - 1)">
              <i class="fas fa-angle-left"></i>
            </button>
            <button
              v-for="so_trang in cac_trang_hien_thi"
              :key="so_trang"
              type="button"
              :class="['page-btn', { 'is-active': trang === so_trang }]"
              @click="trang = so_trang"
            >
              {{ so_trang }}
            </button>
            <button
              class="page-btn"
              type="button"
              :disabled="trang === tong_so_trang"
              @click="trang = Math.min(tong_so_trang, trang + 1)"
            >
              <i class="fas fa-angle-right"></i>
            </button>
          </div>
        </footer>
      </article>

      <aside class="profile-card">
        <div v-if="doi_tac_dang_chon">
          <div class="profile-card__header">
            <div class="profile-card__avatar" :style="{ background: doi_tac_dang_chon.avatarGradient }">
              <i class="fas fa-building"></i>
            </div>
            <div>
              <p class="profile-card__eyebrow">Xem nhanh Đối tác</p>
              <h2>{{ doi_tac_dang_chon.ten_doi_tac }}</h2>
              <div class="profile-card__tags">
                <span class="tag tag--slate">{{ doi_tac_dang_chon.ma_doi_tac }}</span>
                <span :class="['tag', getStatusClass(doi_tac_dang_chon).replace('pill', 'tag')]">
                  {{ getStatusLabel(doi_tac_dang_chon) }}
                </span>
              </div>
            </div>
          </div>

          <div class="profile-card__body">
            <div class="profile-row">
              <span>Người đại diện</span>
              <strong>{{ doi_tac_dang_chon.ten_nguoi_dai_dien }}</strong>
            </div>
            <div class="profile-row">
              <span>Email</span>
              <strong>{{ doi_tac_dang_chon.email }}</strong>
            </div>
            <div class="profile-row">
              <span>SĐT</span>
              <strong>{{ doi_tac_dang_chon.so_dien_thoai || '--' }}</strong>
            </div>
            <div class="profile-row">
              <span>Địa chỉ</span>
              <strong>{{ doi_tac_dang_chon.dia_chi || '--' }}</strong>
            </div>
            <div class="profile-row" v-if="doi_tac_dang_chon.ly_do_tu_choi">
              <span style="color: #ef4444;">Lý do từ chối</span>
              <strong style="color: #ef4444;">{{ doi_tac_dang_chon.ly_do_tu_choi }}</strong>
            </div>
            <div class="profile-row">
              <span>Ngày đăng ký</span>
              <strong>{{ doi_tac_dang_chon.createdAtLabel }}</strong>
            </div>
          </div>

          <div class="profile-card__actions" style="flex-direction: column; gap: 0.5rem;" v-if="doi_tac_dang_chon.trang_thai_duyet === 'pending'">
            <button class="action-button action-button--primary" type="button" @click="duyetDoiTac(doi_tac_dang_chon)">
              <i class="fas fa-check"></i> Duyệt hợp tác
            </button>
            <button class="action-button action-button--danger" type="button" @click="moModalTuChoi(doi_tac_dang_chon)">
              <i class="fas fa-xmark"></i> Từ chối
            </button>
          </div>
          
          <div class="profile-card__actions" v-else-if="doi_tac_dang_chon.trang_thai_duyet === 'approved'">
            <button class="action-button action-button--danger" type="button" @click="khoaDoiTac(doi_tac_dang_chon)">
              Khóa tài khoản
            </button>
          </div>
        </div>
        <div v-else class="profile-card profile-card--empty">
          <p>Chọn một đối tác để xem thông tin và thao tác duyệt.</p>
        </div>
      </aside>
    </section>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="modal-overlay" @click.self="closeRejectModal">
      <div class="reject-modal">
        <header class="reject-modal__header">
          <h3>Từ chối Đối tác</h3>
          <button type="button" @click="closeRejectModal"><i class="fas fa-xmark"></i></button>
        </header>
        <div class="reject-modal__body">
          <p>Bạn đang từ chối đối tác <strong>{{ partnerToReject?.ten_doi_tac }}</strong>.</p>
          <label>
            <span>Lý do từ chối (bắt buộc):</span>
            <textarea v-model.trim="rejectReason" rows="3" placeholder="Nhập lý do để đối tác nhận biết..."></textarea>
          </label>
        </div>
        <footer class="reject-modal__footer">
          <button type="button" class="action-button action-button--secondary" @click="closeRejectModal">Hủy</button>
          <button type="button" class="action-button action-button--danger" @click="submitReject" :disabled="!rejectReason">Xác nhận Từ chối</button>
        </footer>
      </div>
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient.js";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position: "top-right" });

export default {
  name: "QuanLyDoiTac",
  data() {
    return {
      dang_tai: false,
      thong_bao_loi: "",
      danh_sach: [],
      tu_khoa: "",
      loc_trang_thai: "all",
      trang: 1,
      so_luong_moi_trang: 8,
      doi_tac_dang_chon: null,
      
      showRejectModal: false,
      partnerToReject: null,
      rejectReason: "",
    };
  },
  computed: {
    danh_sach_da_loc() {
      return this.danh_sach.filter((dt) => {
        const h = [dt.ma_doi_tac, dt.ten_doi_tac, dt.email, dt.so_dien_thoai].join(" ").toLowerCase();
        const khop_tim_kiem = h.includes(this.tu_khoa.toLowerCase());
        
        let status = dt.trang_thai_duyet;
        if (dt.is_block) status = 'rejected'; // Gộp chung lock và reject
        
        let khop_trang_thai = this.loc_trang_thai === "all" || status === this.loc_trang_thai;
        
        return khop_tim_kiem && khop_trang_thai;
      });
    },
    tong_so_trang() { return Math.max(1, Math.ceil(this.danh_sach_da_loc.length / this.so_luong_moi_trang)); },
    chi_so_bat_dau() { return Math.min((this.trang - 1) * this.so_luong_moi_trang, Math.max(this.danh_sach_da_loc.length - 1, 0)); },
    chi_so_ket_thuc() { return Math.min(this.trang * this.so_luong_moi_trang, this.danh_sach_da_loc.length); },
    danh_sach_phan_trang() { return this.danh_sach_da_loc.slice(this.chi_so_bat_dau, this.chi_so_ket_thuc); },
    cac_trang_hien_thi() {
      const cac_trang = [];
      const bat_dau = Math.max(1, this.trang - 1);
      const ket_thuc = Math.min(this.tong_so_trang, bat_dau + 2);
      for (let i = bat_dau; i <= ket_thuc; i += 1) cac_trang.push(i);
      return cac_trang;
    },
    bat_dau_footer() { return this.danh_sach_da_loc.length ? this.chi_so_bat_dau + 1 : 0; },
    ket_thuc_footer() { return this.danh_sach_da_loc.length ? this.chi_so_ket_thuc : 0; },
    tong_cho_duyet() { return this.danh_sach.filter((dt) => dt.trang_thai_duyet === 'pending').length; },
    tong_hoat_dong() { return this.danh_sach.filter((dt) => dt.trang_thai_duyet === 'approved' && !dt.is_block).length; },
    tong_tu_choi() { return this.danh_sach.filter((dt) => dt.trang_thai_duyet === 'rejected' || dt.is_block).length; },
  },
  methods: {
    async layDanhSach() {
      this.dang_tai = true;
      try {
        const [resAll, resPending] = await Promise.all([
           goiApi("/api/admin/doi-tac").catch(() => ({ok: false})),
           goiApi("/api/admin/doi-tac/pending").catch(() => ({ok: false}))
        ]);
        
        let combined = [];
        if (resAll.ok) {
           const d = await resAll.json();
           if (d.data) combined.push(...d.data);
        }
        if (resPending.ok) {
           const d = await resPending.json();
           if (d.data) combined.push(...d.data);
        }
        
        this.danh_sach = combined.map((item, index) => ({
          ...item,
          createdAtLabel: item.created_at ? new Date(item.created_at).toLocaleDateString("vi-VN") : "--",
          avatarGradient: ["linear-gradient(135deg, #6366f1, #4f46e5)", "linear-gradient(135deg, #14b8a6, #0f766e)", "linear-gradient(135deg, #f59e0b, #ea580c)"][index % 3]
        })).sort((a,b) => new Date(b.created_at) - new Date(a.created_at));
        
        if (this.danh_sach.length > 0) this.doi_tac_dang_chon = this.danh_sach[0];
      } catch (err) {
        toaster.error("Lỗi tải danh sách đối tác");
      } finally {
        this.dang_tai = false;
      }
    },
    
    getStatusClass(dt) {
      if (dt.is_block) return 'pill--slate';
      if (dt.trang_thai_duyet === 'pending') return 'pill--amber';
      if (dt.trang_thai_duyet === 'approved') return 'pill--green';
      if (dt.trang_thai_duyet === 'rejected') return 'pill--rose';
      return 'pill--slate';
    },

    getStatusLabel(dt) {
      if (dt.is_block) return 'Bị khóa';
      if (dt.trang_thai_duyet === 'pending') return 'Chờ duyệt';
      if (dt.trang_thai_duyet === 'approved') return 'Hoạt động';
      if (dt.trang_thai_duyet === 'rejected') return 'Từ chối';
      return 'Không xác định';
    },

    apDungLoc() {
      this.trang = 1;
      this.doi_tac_dang_chon = null;
    },

    async duyetDoiTac(dt) {
      if (!confirm(`Xác nhận DUYỆT đối tác: ${dt.ten_doi_tac}?`)) return;
      try {
        const res = await goiApi(`/api/admin/doi-tac/${dt.ma_doi_tac}/approve`, {
          method: 'PATCH'
        });
        if (res.ok) {
          toaster.success("Đã duyệt đối tác thành công!");
          this.layDanhSach();
        } else {
          toaster.error("Lỗi khi duyệt đối tác");
        }
      } catch (err) { toaster.error("Lỗi kết nối"); }
    },

    moModalTuChoi(dt) {
      this.partnerToReject = dt;
      this.rejectReason = "";
      this.showRejectModal = true;
    },

    closeRejectModal() {
      this.showRejectModal = false;
      this.partnerToReject = null;
      this.rejectReason = "";
    },

    async submitReject() {
      if (!this.rejectReason) {
        toaster.error("Vui lòng nhập lý do từ chối");
        return;
      }
      
      try {
        const res = await goiApi(`/api/admin/doi-tac/${this.partnerToReject.ma_doi_tac}/reject`, {
          method: 'PATCH',
          headers: { 
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ ly_do_tu_choi: this.rejectReason })
        });
        if (res.ok) {
          toaster.success("Đã từ chối đối tác!");
          this.closeRejectModal();
          this.layDanhSach();
        } else {
          toaster.error("Lỗi khi từ chối đối tác");
        }
      } catch (err) { toaster.error("Lỗi kết nối"); }
    },
    
    async khoaDoiTac(dt) {
      if (!confirm(`Xác nhận KHÓA tài khoản: ${dt.ten_doi_tac}?`)) return;
      try {
        const res = await goiApi(`/api/admin/doi-tac/${dt.ma_doi_tac}/status`, {
          method: 'PATCH',
          headers: { 
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ is_block: true })
        });
        if (res.ok) {
          toaster.success("Đã khóa đối tác thành công!");
          this.layDanhSach();
        }
      } catch (err) { toaster.error("Lỗi kết nối"); }
    }
  },
  mounted() {
    this.layDanhSach();
  }
};
</script>

<style scoped>
.partner-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24%),
              linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.partner-page__hero { margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; }
.partner-page__eyebrow { color: #2453ff; font-weight: 900; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 0.5rem; }
.partner-page__hero h1 { font-size: 2.5rem; font-weight: 900; color: #111827; margin: 0; }
.partner-page__subtitle { color: #64748b; margin-top: 0.5rem; }
.ghost-button { padding: 0.75rem 1.25rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: white; font-weight: 700; cursor: pointer;}
.ghost-button:hover { background: #f8fafc; }

.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.stats-card { display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: white; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
.stats-card__icon { width: 3.5rem; height: 3.5rem; border-radius: 0.75rem; display: grid; place-items: center; font-size: 1.5rem; }
.stats-card__icon--blue { background: #eff6ff; color: #3b82f6; }
.stats-card__icon--amber { background: #fffbeb; color: #f59e0b; }
.stats-card__icon--green { background: #f0fdf4; color: #10b981; }
.stats-card__icon--rose { background: #fff1f2; color: #e11d48; }

.filter-bar { display: flex; gap: 1rem; padding: 1rem; background: white; border-radius: 1rem; margin-bottom: 1.5rem; }
.filter-search { flex: 1; display: flex; align-items: center; gap: 0.5rem; background: #f1f5f9; padding: 0.5rem 1rem; border-radius: 0.5rem; }
.filter-search input { border: none; background: transparent; width: 100%; outline: none; }
.filter-select { padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; }
.filter-apply { padding: 0.5rem 1.5rem; background: #1e293b; color: white; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 600;}

.content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
.table-card { background: white; padding: 1.5rem; border-radius: 1rem; }
.table-head { display: flex; justify-content: space-between; margin-bottom: 1.5rem; }

.partner-table__row { display: grid; grid-template-columns: 2.5fr 1fr 1fr 1fr; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #f1f5f9; align-items: center; cursor: pointer; }
.partner-table__row--head { font-weight: bold; color: #64748b; font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
.partner-table__row.is-selected { background: #f8fafc; border-radius: 0.5rem; padding: 1rem; margin: 0 -1rem; width: calc(100% + 2rem); }
.partner-person { display: flex; gap: 1rem; align-items: center; }
.partner-person__avatar { width: 3rem; height: 3rem; border-radius: 0.5rem; display: grid; place-items: center; color: white; }
.partner-person small { display: block; color: #64748b; font-size: 0.85rem; }

.pill { padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600; display: inline-block; text-align: center; }
.pill--amber { background: #fef3c7; color: #d97706; }
.pill--green { background: #dcfce7; color: #16a34a; }
.pill--rose { background: #ffe4e6; color: #e11d48; }
.pill--slate { background: #f1f5f9; color: #475569; }

.tag { padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: bold; }
.tag--slate { background: #f1f5f9; color: #475569; }
.tag--amber { background: #fef3c7; color: #d97706; }
.tag--green { background: #dcfce7; color: #16a34a; }
.tag--rose { background: #ffe4e6; color: #e11d48; }

.profile-card { background: white; padding: 1.5rem; border-radius: 1rem; border: 1px solid #e2e8f0; position: sticky; top: 2rem;}
.profile-card__empty { color: #64748b; text-align: center; }
.profile-card__header { display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: center;}
.profile-card__avatar { width: 4rem; height: 4rem; border-radius: 0.5rem; display: grid; place-items: center; color: white; font-size: 1.5rem; }
.profile-card__eyebrow { color: #64748b; font-size: 0.8rem; text-transform: uppercase; margin: 0; }
.profile-card__header h2 { margin: 0 0 0.5rem; font-size: 1.25rem; }
.profile-card__tags { display: flex; gap: 0.5rem; }

.profile-card__body .profile-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.9rem; }
.profile-card__body .profile-row span { color: #64748b; }
.profile-card__body { margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0;}

.profile-card__actions { display: flex; gap: 0.5rem; }
.action-button { flex: 1; padding: 0.75rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 0.5rem;}
.action-button--primary { background: #2563eb; color: white; }
.action-button--primary:hover { background: #1d4ed8; }
.action-button--danger { background: #fee2e2; color: #ef4444; }
.action-button--danger:hover { background: #fecaca; }

.table-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; margin-top: 1rem; border-top: 1px solid #f1f5f9; color: #64748b; }
.pagination { display: flex; gap: 0.25rem; }
.page-btn { padding: 0.5rem 0.75rem; background: white; border: 1px solid #e2e8f0; border-radius: 0.25rem; cursor: pointer; }
.page-btn.is-active { background: #2563eb; color: white; border-color: #2563eb; }

/* Modal CSS */
.modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); display: grid; place-items: center; z-index: 9999; }
.reject-modal { background: white; width: min(400px, 90vw); border-radius: 1rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; }
.reject-modal__header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
.reject-modal__header h3 { margin: 0; color: #0f172a; font-size: 1.25rem; }
.reject-modal__header button { background: none; border: none; font-size: 1.25rem; color: #64748b; cursor: pointer; }
.reject-modal__body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
.reject-modal__body p { margin: 0; color: #475569; line-height: 1.5; }
.reject-modal__body label { display: flex; flex-direction: column; gap: 0.5rem; font-weight: 600; color: #334155; font-size: 0.9rem; }
.reject-modal__body textarea { padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #cbd5e1; resize: vertical; outline: none; font-family: inherit; }
.reject-modal__body textarea:focus { border-color: #2563eb; }
.reject-modal__footer { padding: 1.25rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end; gap: 0.75rem; }
.action-button--secondary { background: white; color: #475569; border: 1px solid #cbd5e1; }
.action-button--secondary:hover { background: #f1f5f9; }
.action-button:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
