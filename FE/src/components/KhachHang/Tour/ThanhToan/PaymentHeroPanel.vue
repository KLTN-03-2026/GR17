<template>
  <article class="payment-focus-panel">
    <div class="payment-focus-panel__header">
      <div class="payment-focus-panel__icon">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>

      <div class="payment-focus-panel__title">
        <p class="payment-focus-panel__eyebrow">Ưu tiên thanh toán</p>
        <h2>Theo dõi hóa đơn và thanh toán</h2>
        <p>
          Mã hóa đơn sẽ được đối chiếu tự động qua webhook SePay. Đây là khu vực chính để quét QR và
          theo dõi trạng thái cập nhật.
        </p>
      </div>

      <div class="payment-focus-panel__status">
        <span :class="['payment-status-pill', `payment-status-pill--${paymentStatusTone}`]">
          {{ paymentStatusLabel }}
        </span>
        <span
          v-if="paymentInfo.payment_expires_at && paymentInfo.payment_status === 'pending'"
          class="payment-countdown-pill"
        >
          {{ countdownLabel }}
        </span>
      </div>
    </div>

    <div class="payment-meta-grid">
      <div class="payment-meta-card payment-meta-card--accent">
        <span class="payment-meta-card__label">Trạng thái</span>
        <strong :class="['payment-status-text', `payment-status-text--${paymentStatusTone}`]">
          {{ paymentStatusLabel }}
        </strong>
      </div>
      <div class="payment-meta-card">
        <span class="payment-meta-card__label">Số tiền</span>
        <strong>{{ formattedAmount }}</strong>
      </div>
      <div class="payment-meta-card">
        <span class="payment-meta-card__label">Mã hóa đơn</span>
        <strong>{{ paymentInfo.ma_hoa_don }}</strong>
      </div>
      <div class="payment-meta-card">
        <span class="payment-meta-card__label">Lịch khởi hành</span>
        <strong>{{ scheduleLabel }}</strong>
      </div>
    </div>

    <div class="payment-focus-panel__content">
      <button
        type="button"
        class="payment-qr-card"
        :disabled="!paymentInfo.qr_url"
        @click="openQrPreview"
      >
        <span v-if="paymentInfo.qr_url" class="payment-qr-card__hint">
          <i class="fas fa-up-right-and-down-left-from-center"></i>
          Bấm để phóng to mã QR
        </span>

        <img v-if="paymentInfo.qr_url" :src="paymentInfo.qr_url" alt="Mã QR thanh toán" />

        <div v-else class="payment-qr-card__placeholder">
          <i class="fas fa-qrcode"></i>
          <span>Chưa có dữ liệu mã QR</span>
        </div>
      </button>

      <div class="payment-focus-panel__details">
        <div class="payment-box payment-box--bank">
          <span class="payment-box__label">Tài khoản thụ hưởng</span>
          <dl class="payment-bank-list">
            <div>
              <dt>Ngân hàng</dt>
              <dd>MBBank</dd>
            </div>
            <div>
              <dt>Số tài khoản</dt>
              <dd>0399620355</dd>
            </div>
            <div>
              <dt>Chủ tài khoản</dt>
              <dd>VU HA THAI SON</dd>
            </div>
          </dl>
        </div>

        <div class="payment-box payment-box--code">
          <span class="payment-box__label">Nội dung chuyển khoản</span>
          <div class="payment-copy-row">
            <code>{{ paymentInfo.noi_dung_chuyen_khoan }}</code>
            <button type="button" class="payment-copy-button" @click.stop="$emit('copy-transfer-content')">
              <i class="far fa-copy"></i>
              Sao chép
            </button>
          </div>
        </div>

        <div class="payment-box payment-box--actions">
          <button
            type="button"
            class="payment-check-button"
            :disabled="checkingStatus"
            @click="$emit('check-now')"
          >
            <i class="fas" :class="checkingStatus ? 'fa-spinner fa-spin' : 'fa-arrows-rotate'"></i>
            Kiểm tra ngay
          </button>

          <button
            v-if="paymentInfo.payment_status === 'pending'"
            type="button"
            class="payment-cancel-button"
            :disabled="cancellingPayment"
            @click="$emit('cancel-payment')"
          >
            <i class="fas" :class="cancellingPayment ? 'fa-spinner fa-spin' : 'fa-ban'"></i>
            Hủy thanh toán
          </button>

          <button
            v-if="paymentInfo.payment_status === 'paid'"
            type="button"
            class="payment-view-button"
            @click="$emit('view-invoice')"
          >
            Xem chi tiết hóa đơn
          </button>
        </div>

        <div class="payment-box">
          <span class="payment-box__label">Thông tin thanh toán</span>
          <ul class="payment-instruction-list">
            <li>Quét QR hoặc chuyển khoản đúng số tiền {{ formattedAmount }}.</li>
            <li>Không thay đổi nội dung chuyển khoản để hệ thống đối chiếu đúng hóa đơn.</li>
            <li>Trạng thái được cập nhật tự động. Nếu vừa chuyển khoản xong, bạn có thể bấm “Kiểm tra ngay”.</li>
          </ul>
        </div>

        <div v-if="paymentInfo.payment_expires_at" class="payment-box payment-box--muted">
          <span class="payment-box__label">Hạn QR</span>
          <p>{{ countdownLabel }}</p>
          <small>{{ formattedExpiry }}</small>
        </div>
      </div>
    </div>

    <div
      v-if="qrPreviewOpen && paymentInfo.qr_url"
      class="payment-qr-lightbox"
      role="dialog"
      aria-modal="true"
      @click.self="closeQrPreview"
    >
      <button type="button" class="payment-qr-lightbox__close" @click="closeQrPreview">
        <i class="fas fa-xmark"></i>
      </button>

      <div class="payment-qr-lightbox__content">
        <img :src="paymentInfo.qr_url" alt="Mã QR thanh toán phóng to" />
        <p>{{ paymentInfo.ma_hoa_don }} · {{ formattedAmount }}</p>
      </div>
    </div>
  </article>
</template>

<script>
export default {
  name: "PaymentHeroPanel",
  props: {
    paymentInfo: {
      type: Object,
      required: true,
    },
    paymentStatusLabel: {
      type: String,
      required: true,
    },
    paymentStatusTone: {
      type: String,
      required: true,
    },
    formattedAmount: {
      type: String,
      required: true,
    },
    scheduleLabel: {
      type: String,
      default: "--",
    },
    countdownLabel: {
      type: String,
      default: "--",
    },
    formattedExpiry: {
      type: String,
      default: "--",
    },
    checkingStatus: {
      type: Boolean,
      default: false,
    },
    cancellingPayment: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["copy-transfer-content", "check-now", "cancel-payment", "view-invoice"],
  data() {
    return {
      qrPreviewOpen: false,
    };
  },
  methods: {
    openQrPreview() {
      if (!this.paymentInfo?.qr_url) return;
      this.qrPreviewOpen = true;
    },
    closeQrPreview() {
      this.qrPreviewOpen = false;
    },
  },
};
</script>

<style scoped>
.payment-focus-panel {
  position: relative;
  overflow: hidden;
  padding: 30px;
  border-radius: 32px;
  border: 1px solid rgba(125, 211, 252, 0.6);
  background:
    radial-gradient(circle at top right, rgba(14, 165, 233, 0.18), transparent 26%),
    linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(239, 246, 255, 0.98));
  box-shadow:
    0 30px 60px rgba(2, 132, 199, 0.14),
    inset 0 1px 0 rgba(255, 255, 255, 0.85);
}

.payment-focus-panel__header {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) auto;
  gap: 18px;
  align-items: start;
  margin-bottom: 24px;
}

.payment-focus-panel__icon {
  width: 60px;
  height: 60px;
  border-radius: 20px;
  display: grid;
  place-items: center;
  color: #fff;
  font-size: 1.2rem;
  background: linear-gradient(135deg, #0369a1 0%, #0f766e 100%);
  box-shadow: 0 18px 32px rgba(3, 105, 161, 0.25);
}

.payment-focus-panel__eyebrow {
  margin: 0 0 10px;
  font-size: 0.78rem;
  font-weight: 900;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: #0284c7;
}

.payment-focus-panel__title h2 {
  margin: 0;
  font-size: clamp(1.7rem, 2.3vw, 2.4rem);
  font-weight: 900;
  letter-spacing: -0.03em;
}

.payment-focus-panel__title p:last-child {
  margin: 10px 0 0;
  color: #475569;
  line-height: 1.7;
}

.payment-focus-panel__status {
  display: grid;
  gap: 10px;
  justify-items: end;
}

.payment-status-pill,
.payment-countdown-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 16px;
  border-radius: 999px;
  font-size: 0.85rem;
  font-weight: 900;
  letter-spacing: 0.04em;
}

.payment-status-pill--pending {
  background: rgba(251, 191, 36, 0.18);
  color: #b45309;
}

.payment-status-pill--paid {
  background: rgba(16, 185, 129, 0.16);
  color: #047857;
}

.payment-status-pill--failed {
  background: rgba(244, 63, 94, 0.14);
  color: #be123c;
}

.payment-countdown-pill {
  background: #0f172a;
  color: #f8fafc;
}

.payment-meta-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
  margin-bottom: 24px;
}

.payment-meta-card {
  padding: 18px;
  border-radius: 22px;
  border: 1px solid #dbeafe;
  background: rgba(255, 255, 255, 0.86);
  display: grid;
  gap: 8px;
}

.payment-meta-card--accent {
  background: linear-gradient(135deg, rgba(224, 242, 254, 0.92), rgba(236, 253, 245, 0.92));
  border-color: #7dd3fc;
}

.payment-meta-card__label {
  font-size: 0.76rem;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #64748b;
}

.payment-meta-card strong {
  font-size: 1.1rem;
}

.payment-status-text--paid {
  color: #047857;
}

.payment-status-text--pending {
  color: #b45309;
}

.payment-status-text--failed {
  color: #be123c;
}

.payment-focus-panel__content {
  display: grid;
  grid-template-columns: 460px minmax(0, 1fr);
  gap: 24px;
}

.payment-qr-card {
  position: relative;
  width: 100%;
  min-height: 520px;
  padding: 28px;
  border: 1px solid #bae6fd;
  border-radius: 28px;
  background: linear-gradient(180deg, #ffffff, #f8fbff);
  display: grid;
  place-items: center;
  cursor: zoom-in;
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.payment-qr-card:not(:disabled):hover {
  transform: translateY(-2px);
  border-color: #38bdf8;
  box-shadow: 0 24px 40px rgba(14, 165, 233, 0.14);
}

.payment-qr-card:disabled {
  cursor: not-allowed;
}

.payment-qr-card img {
  width: 100%;
  max-width: 430px;
  object-fit: contain;
}

.payment-qr-card__hint {
  position: absolute;
  top: 20px;
  left: 20px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.88);
  color: #f8fafc;
  font-size: 0.82rem;
  font-weight: 800;
}

.payment-qr-card__placeholder {
  display: grid;
  gap: 12px;
  text-align: center;
  color: #94a3b8;
}

.payment-qr-card__placeholder i {
  font-size: 3rem;
}

.payment-focus-panel__details {
  display: grid;
  gap: 14px;
}

.payment-box {
  padding: 18px 20px;
  border-radius: 22px;
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, 0.9);
}

.payment-box--bank {
  background: linear-gradient(135deg, rgba(239, 246, 255, 0.96), rgba(236, 253, 245, 0.95));
  border-color: #93c5fd;
}

.payment-box--code {
  border-color: #c7d2fe;
}

.payment-box--muted {
  background: #f8fafc;
}

.payment-box__label {
  display: block;
  margin-bottom: 10px;
  font-size: 0.78rem;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #64748b;
}

.payment-bank-list {
  margin: 0;
  display: grid;
  gap: 12px;
}

.payment-bank-list div {
  display: flex;
  justify-content: space-between;
  gap: 12px;
}

.payment-bank-list dt {
  color: #64748b;
  font-weight: 700;
}

.payment-bank-list dd {
  margin: 0;
  text-align: right;
  font-weight: 900;
  color: #0f172a;
}

.payment-copy-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.payment-copy-row code {
  padding: 12px 14px;
  border-radius: 16px;
  background: #0f172a;
  color: #f8fafc;
  font-weight: 800;
  letter-spacing: 0.08em;
}

.payment-copy-button,
.payment-check-button,
.payment-cancel-button,
.payment-view-button {
  border: none;
  cursor: pointer;
  font-weight: 800;
  transition: transform 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease;
}

.payment-copy-button {
  padding: 11px 15px;
  border-radius: 14px;
  background: #e0f2fe;
  color: #075985;
}

.payment-check-button,
.payment-cancel-button,
.payment-view-button {
  width: 100%;
  padding: 14px 16px;
  border-radius: 18px;
  font-size: 0.98rem;
}

.payment-check-button {
  background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
  color: #fff;
  box-shadow: 0 18px 28px rgba(2, 132, 199, 0.2);
}

.payment-cancel-button {
  background: rgba(244, 63, 94, 0.12);
  color: #be123c;
  border: 1px solid rgba(244, 63, 94, 0.24);
}

.payment-view-button {
  background: transparent;
  color: #0f172a;
  border: 1px solid #cbd5e1;
}

.payment-copy-button:hover,
.payment-check-button:hover,
.payment-cancel-button:hover,
.payment-view-button:hover {
  transform: translateY(-1px);
}

.payment-copy-button:disabled,
.payment-check-button:disabled,
.payment-cancel-button:disabled,
.payment-view-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.payment-box--actions {
  display: grid;
  gap: 12px;
}

.payment-instruction-list {
  margin: 0;
  padding-left: 18px;
  color: #334155;
  line-height: 1.75;
}

.payment-box p {
  margin: 0;
  color: #0f172a;
  font-weight: 800;
}

.payment-box small {
  display: block;
  margin-top: 8px;
  color: #64748b;
}

.payment-qr-lightbox {
  position: fixed;
  inset: 0;
  z-index: 40;
  padding: 28px;
  background: rgba(15, 23, 42, 0.72);
  backdrop-filter: blur(6px);
  display: grid;
  place-items: center;
}

.payment-qr-lightbox__close {
  position: absolute;
  top: 22px;
  right: 22px;
  width: 46px;
  height: 46px;
  border: none;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.14);
  color: #fff;
  cursor: pointer;
}

.payment-qr-lightbox__content {
  width: min(92vw, 720px);
  padding: 28px;
  border-radius: 28px;
  background: #fff;
  box-shadow: 0 36px 72px rgba(15, 23, 42, 0.28);
  text-align: center;
}

.payment-qr-lightbox__content img {
  width: 100%;
  max-width: 620px;
  object-fit: contain;
}

.payment-qr-lightbox__content p {
  margin: 18px 0 0;
  color: #334155;
  font-weight: 800;
}

@media (max-width: 1180px) {
  .payment-focus-panel__content {
    grid-template-columns: 1fr;
  }

  .payment-qr-card {
    min-height: 460px;
  }
}

@media (max-width: 860px) {
  .payment-focus-panel__header {
    grid-template-columns: 1fr;
  }

  .payment-focus-panel__status {
    justify-items: start;
  }

  .payment-meta-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  .payment-focus-panel {
    padding: 22px;
  }

  .payment-meta-grid {
    grid-template-columns: 1fr;
  }

  .payment-copy-row,
  .payment-bank-list div {
    flex-direction: column;
    align-items: stretch;
  }

  .payment-bank-list dd {
    text-align: left;
  }

  .payment-qr-card {
    min-height: 360px;
    padding: 20px;
  }
}
</style>
