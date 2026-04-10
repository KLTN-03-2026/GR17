<template>
  <div class="review-detail-page">
    <section class="detail-hero">
      <button class="back-button" type="button" @click="quayLai">
        <i class="fas fa-arrow-left"></i>
      </button>

      <div class="detail-hero__copy">
        <p class="detail-hero__eyebrow">Quản trị đánh giá</p>
        <h1>Chi tiết đánh giá</h1>
        <p>
          Xem đầy đủ nội dung phản hồi, thông tin khách hàng và địa điểm được đánh giá theo mã
          <strong>{{ reviewIdLabel }}</strong>.
        </p>
      </div>

      <div class="detail-hero__actions">
        <button class="ghost-button" type="button" @click="taiDanhGia" :disabled="dangTai">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dangTai ? "Đang tải..." : "Tải lại" }}</span>
        </button>

        <router-link class="primary-link" :to="`/admin/reviews/${reviewId}/edit`">
          <i class="fas fa-pen"></i>
          <span>Chỉnh sửa</span>
        </router-link>
      </div>
    </section>

    <div v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </div>

    <section v-if="dangTai" class="loading-shell">
      <div class="loading-card"></div>
      <div class="loading-grid">
        <div class="loading-card"></div>
        <div class="loading-card"></div>
      </div>
    </section>

    <template v-else-if="review">
      <section class="review-banner">
        <div class="review-banner__main">
          <div class="review-banner__avatar" :style="{ background: review.avatarGradient }">
            {{ review.customerInitials }}
          </div>

          <div class="review-banner__copy">
            <p class="review-banner__eyebrow">Thông tin đánh giá</p>
            <h2>{{ review.customerName }}</h2>
            <div class="review-banner__meta">
              <span class="tag tag--slate">{{ review.code }}</span>
              <span :class="['tag', xacDinhLopSoSao(review.rating)]">
                {{ review.ratingLabel }}
              </span>
              <span class="tag tag--sky">{{ review.locationName }}</span>
            </div>
          </div>
        </div>

        <div class="review-banner__side">
          <div class="hero-stat">
            <span>Số sao</span>
            <strong>{{ review.stars }}</strong>
          </div>
          <div class="hero-stat">
            <span>Ngày tạo</span>
            <strong>{{ review.createdAtLabel }}</strong>
          </div>
          <div class="hero-stat">
            <span>Cập nhật gần nhất</span>
            <strong>{{ review.updatedAtLabel }}</strong>
          </div>
        </div>
      </section>

      <section class="detail-grid">
        <article class="card card--wide">
          <header class="card__head">
            <div class="card__icon">
              <i class="fas fa-quote-left"></i>
            </div>
            <div>
              <h3>Nội dung đánh giá</h3>
              <p>Phản hồi đầy đủ của khách hàng được lưu trong hệ thống quản trị.</p>
            </div>
          </header>

          <div class="content-panel">
            {{ review.content || "Khách hàng chưa để lại nội dung đánh giá." }}
          </div>
        </article>

        <article class="card">
          <header class="card__head">
            <div class="card__icon card__icon--blue">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h3>Thông tin khách hàng</h3>
              <p>Định danh tài khoản và đầu mối liên hệ đi kèm đánh giá.</p>
            </div>
          </header>

          <div class="info-grid">
            <div class="info-row">
              <span>Mã khách hàng</span>
              <strong>KH {{ review.customerId }}</strong>
            </div>
            <div class="info-row">
              <span>Họ và tên</span>
              <strong>{{ review.customerName }}</strong>
            </div>
            <div class="info-row">
              <span>Email</span>
              <strong>{{ review.customerEmail }}</strong>
            </div>
            <div class="info-row">
              <span>Số điện thoại</span>
              <strong>{{ review.customerPhone }}</strong>
            </div>
          </div>
        </article>

        <article class="card">
          <header class="card__head">
            <div class="card__icon card__icon--amber">
              <i class="fas fa-location-dot"></i>
            </div>
            <div>
              <h3>Thông tin địa điểm</h3>
              <p>Gắn kết đánh giá với địa điểm phát sinh phản hồi của khách hàng.</p>
            </div>
          </header>

          <div class="info-grid">
            <div class="info-row">
              <span>Mã địa điểm</span>
              <strong>{{ review.locationId }}</strong>
            </div>
            <div class="info-row">
              <span>Tên địa điểm</span>
              <strong>{{ review.locationName }}</strong>
            </div>
            <div class="info-row">
              <span>Số sao</span>
              <strong>{{ review.ratingLabel }}</strong>
            </div>
            <div class="info-row">
              <span>Hiển thị sao</span>
              <strong>{{ review.stars }}</strong>
            </div>
          </div>
        </article>

        <article class="card card--wide">
          <header class="card__head">
            <div class="card__icon card__icon--violet">
              <i class="fas fa-lightbulb"></i>
            </div>
            <div>
              <h3>Gợi ý thao tác tiếp theo</h3>
            </div>
          </header>

          <div class="next-steps">
            <router-link class="action-tile action-tile--primary" :to="`/admin/reviews/${review.id}/edit`">
              <i class="fas fa-pen-to-square"></i>
              <div>
                <strong>Chỉnh sửa đánh giá</strong>
                <span>Cập nhật lại số sao hoặc nội dung đánh giá để chuẩn hóa dữ liệu.</span>
              </div>
            </router-link>

            <router-link class="action-tile" to="/admin/reviews">
              <i class="fas fa-table-list"></i>
              <div>
                <strong>Quay về danh sách</strong>
                <span>Tiếp tục rà soát các đánh giá khác trong danh mục quản trị.</span>
              </div>
            </router-link>
          </div>
        </article>
      </section>
    </template>

    <section v-else class="empty-state">
      <i class="fas fa-comment-slash"></i>
      <h2>Không tìm thấy đánh giá</h2>
      <p>Mã đánh giá này hiện không có trong hệ thống hoặc đã được xóa trước đó.</p>
      <router-link class="primary-link" to="/admin/reviews">Quay về danh sách</router-link>
    </section>
  </div>
</template>

<script>
import { API_BASE, mapReview, normalizeRecord, normalizeReviewId } from "./reviewAdminShared";
import { goiApi } from "../../../services/httpClient.js";

export default {
  name: "ChiTietDanhGia",
  data() {
    return {
      dangTai: false,
      thongBaoLoi: "",
      review: null,
    };
  },
  computed: {
    reviewId() {
      return normalizeReviewId(this.$route.params.id);
    },
    reviewIdLabel() {
      return this.reviewId ? `DG ${this.reviewId}` : "--";
    },
  },
  watch: {
    "$route.params.id": {
      immediate: true,
      handler() {
        this.taiDanhGia();
      },
    },
  },
  methods: {
    xacDinhLopSoSao(rating) {
      if (rating >= 5) return "tag--violet";
      if (rating >= 4) return "tag--green";
      if (rating === 3) return "tag--amber";
      return "tag--rose";
    },
    async taiDanhGia() {
      if (!this.reviewId) {
        this.review = null;
        this.thongBaoLoi = "Thiếu mã đánh giá để tải dữ liệu.";
        return;
      }

      this.dangTai = true;
      this.thongBaoLoi = "";

      try {
        const response = await goiApi(`${API_BASE}/danh-gia-ke-hoach/${encodeURIComponent(this.reviewId)}`, {
          headers: {
            Accept: "application/json",
          },
        });
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          throw new Error(payload?.message || "Không thể tải chi tiết đánh giá.");
        }

        const record = normalizeRecord(payload);
        this.review = record ? mapReview(record) : null;
      } catch (error) {
        this.review = null;
        this.thongBaoLoi = error.message || "Không thể tải chi tiết đánh giá.";
      } finally {
        this.dangTai = false;
      }
    },
    quayLai() {
      this.$router.push("/admin/reviews");
    },
  },
};
</script>

<style scoped>
.review-detail-page {
  min-height: calc(100vh - 120px);
  padding: 1.5rem;
  background:
    radial-gradient(circle at top left, rgba(79, 70, 229, 0.08), transparent 28%),
    radial-gradient(circle at top right, rgba(245, 158, 11, 0.08), transparent 24%),
    #f5f7fb;
}

.detail-hero,
.review-banner,
.card,
.empty-state,
.loading-card {
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 22px 55px rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(10px);
}

.detail-hero {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 1rem;
  align-items: center;
  padding: 1.4rem 1.5rem;
  border-radius: 1.75rem;
}

.back-button,
.ghost-button,
.primary-link {
  min-height: 3rem;
  border-radius: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  font-weight: 800;
  text-decoration: none;
  transition: transform 0.2s ease;
}

.back-button {
  width: 3rem;
  border: none;
  background: #eef3ff;
  color: #2952d3;
}

.ghost-button {
  padding: 0 1rem;
  border: 1px solid #dbe4f0;
  background: #ffffff;
  color: #334155;
}

.primary-link {
  padding: 0 1.2rem;
  border: none;
  background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
  color: #ffffff;
}

.detail-hero__eyebrow,
.review-banner__eyebrow {
  margin: 0 0 0.45rem;
  color: #c86b1a;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.detail-hero__copy h1,
.review-banner__copy h2,
.card__head h3,
.empty-state h2 {
  margin: 0;
  color: #172033;
}

.detail-hero__copy p,
.card__head p,
.empty-state p {
  margin: 0.45rem 0 0;
  color: #5f6c82;
  line-height: 1.7;
}

.detail-hero__actions {
  display: flex;
  gap: 0.8rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.notice {
  margin-top: 1rem;
  padding: 0.95rem 1.1rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  font-weight: 700;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}

.loading-shell {
  margin-top: 1rem;
  display: grid;
  gap: 1rem;
}

.loading-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.loading-card {
  min-height: 220px;
  border-radius: 1.5rem;
  position: relative;
  overflow: hidden;
}

.loading-card::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
  transform: translateX(-100%);
  animation: shimmer 1.4s infinite;
}

.review-banner {
  margin-top: 1rem;
  padding: 1.35rem;
  border-radius: 1.75rem;
  display: grid;
  grid-template-columns: minmax(0, 1.6fr) minmax(280px, 0.9fr);
  gap: 1rem;
}

.review-banner__main {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.review-banner__avatar {
  width: 5.5rem;
  height: 5.5rem;
  border-radius: 1.6rem;
  display: grid;
  place-items: center;
  color: #ffffff;
  font-size: 1.8rem;
  font-weight: 800;
}

.review-banner__meta {
  margin-top: 0.85rem;
  display: flex;
  gap: 0.65rem;
  flex-wrap: wrap;
}

.review-banner__side {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.8rem;
}

.hero-stat,
.info-row,
.action-tile,
.content-panel {
  border-radius: 1.2rem;
  border: 1px solid #e6ebf4;
  background: #fbfcff;
}

.hero-stat {
  padding: 1rem;
}

.hero-stat span,
.info-row span,
.action-tile span {
  display: block;
  color: #64748b;
}

.hero-stat strong,
.info-row strong,
.action-tile strong {
  display: block;
  margin-top: 0.35rem;
  color: #142033;
}

.detail-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.card {
  padding: 1.35rem;
  border-radius: 1.6rem;
}

.card--wide {
  grid-column: 1 / -1;
}

.card__head {
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.card__icon {
  width: 3rem;
  height: 3rem;
  border-radius: 1rem;
  display: grid;
  place-items: center;
  color: #4338ca;
  background: #eef2ff;
}

.card__icon--blue {
  color: #0369a1;
  background: #eaf4ff;
}

.card__icon--amber {
  color: #d97706;
  background: #fff7ed;
}

.card__icon--violet {
  color: #7c3aed;
  background: #f5f3ff;
}

.info-grid {
  display: grid;
  gap: 0.85rem;
}

.info-row {
  padding: 1rem 1.1rem;
}

.content-panel {
  padding: 1.25rem 1.3rem;
  color: #142033;
  line-height: 1.8;
  white-space: pre-wrap;
}

.tag {
  min-height: 2.1rem;
  padding: 0 0.85rem;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 800;
}

.tag--violet { background: #efefff; color: #4f25f4; }
.tag--green { background: #eaf9f1; color: #08986c; }
.tag--amber { background: #fff7e8; color: #d97706; }
.tag--rose { background: #fff1f2; color: #be123c; }
.tag--slate { background: #f1f5f9; color: #475569; }
.tag--sky { background: #eaf4ff; color: #0369a1; }

.next-steps {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.action-tile {
  padding: 1rem 1.1rem;
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
  text-decoration: none;
  color: inherit;
}

.action-tile i {
  width: 2.7rem;
  height: 2.7rem;
  border-radius: 0.95rem;
  display: grid;
  place-items: center;
  background: #eef3ff;
  color: #3452d1;
}

.action-tile--primary {
  background: linear-gradient(135deg, #f5f8ff 0%, #ffffff 100%);
}

.empty-state {
  margin-top: 1rem;
  padding: 3rem 1.5rem;
  border-radius: 1.75rem;
  text-align: center;
}

.empty-state i {
  width: 4.5rem;
  height: 4.5rem;
  margin: 0 auto 1rem;
  border-radius: 1.4rem;
  display: grid;
  place-items: center;
  background: #f4f7fb;
  color: #3452d1;
  font-size: 1.4rem;
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

@media (max-width: 1180px) {
  .detail-grid,
  .review-banner {
    grid-template-columns: 1fr;
  }

  .review-banner__side {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 840px) {
  .review-detail-page {
    padding: 1rem;
  }

  .detail-hero {
    grid-template-columns: 1fr;
    justify-items: flex-start;
  }

  .detail-hero__actions,
  .next-steps,
  .loading-grid,
  .review-banner__side {
    grid-template-columns: 1fr;
  }

  .detail-hero__actions {
    width: 100%;
  }

  .review-banner__main {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>
