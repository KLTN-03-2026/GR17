<template>
  <div class="checkout-page">
    <div class="checkout-header">
      <router-link class="back-link" :to="`/khach-hang/tour/${$route.params.id}`">
        <i class="fas fa-arrow-left"></i> Quay lại
      </router-link>
      <h1>Thông tin thanh toán</h1>
    </div>

    <div class="checkout-grid">
      <!-- Left Column: Forms -->
      <div class="checkout-main">
        <!-- Customer Info Section -->
        <section class="checkout-section">
          <div class="section-header">
            <i class="fas fa-user"></i>
            <h2>Thông tin khách hàng</h2>
          </div>
          
          <div class="form-grid">
            <div class="form-group">
              <label>HỌ VÀ TÊN</label>
              <input type="text" placeholder="Nguyễn Văn A" v-model="form.fullName">
            </div>
            <div class="form-group">
              <label>SỐ ĐIỆN THOẠI</label>
              <input type="tel" placeholder="090 123 4567" v-model="form.phone">
            </div>
            <div class="form-group full-width">
              <label>EMAIL NHẬN VÉ ĐIỆN TỬ</label>
              <input type="email" placeholder="example@email.com" v-model="form.email">
            </div>
            <div class="form-group full-width">
              <label>ĐỊA CHỈ THƯỜNG TRÚ</label>
              <input type="text" placeholder="Số nhà, Tên đường, Quận/Huyện, Thành phố" v-model="form.address">
            </div>
          </div>
        </section>

        <!-- Payment Methods Section -->
        <section class="checkout-section">
          <div class="section-header">
            <i class="fas fa-wallet"></i>
            <h2>Phương thức thanh toán</h2>
          </div>

          <div class="payment-methods">
            <!-- Method 1 -->
            <label class="payment-method" :class="{'payment-method--active': form.paymentMethod === 'credit_card'}">
              <div class="method-icon"><i class="fas fa-credit-card"></i></div>
              <div class="method-details">
                <h3>Thẻ tín dụng / Thẻ ghi nợ</h3>
                <p>Visa, Mastercard, JCB, American Express</p>
              </div>
              <div class="method-radio">
                <input type="radio" name="payment" value="credit_card" v-model="form.paymentMethod">
                <span class="custom-radio"></span>
              </div>
            </label>

            <!-- Method 2 -->
            <label class="payment-method" :class="{'payment-method--active': form.paymentMethod === 'bank_transfer'}">
              <div class="method-icon"><i class="fas fa-university"></i></div>
              <div class="method-details">
                <h3>Chuyển khoản ngân hàng</h3>
                <p>Hỗ trợ tất cả ngân hàng nội địa tại Việt Nam</p>
              </div>
              <div class="method-radio">
                <input type="radio" name="payment" value="bank_transfer" v-model="form.paymentMethod">
                <span class="custom-radio"></span>
              </div>
            </label>

            <!-- Method 3 -->
            <label class="payment-method" :class="{'payment-method--active': form.paymentMethod === 'e_wallet'}">
              <div class="method-icon"><i class="fas fa-qrcode"></i></div>
              <div class="method-details">
                <h3>Ví điện tử</h3>
                <p>MoMo, ZaloPay, ShopeePay</p>
              </div>
              <div class="method-radio">
                <input type="radio" name="payment" value="e_wallet" v-model="form.paymentMethod">
                <span class="custom-radio"></span>
              </div>
            </label>
          </div>
        </section>

        <div class="security-badge">
          <i class="fas fa-shield-alt"></i> BẢO MẬT CHUẨN SSL 256-BIT THEO QUY CHUẨN QUỐC TẾ
        </div>
      </div>

      <!-- Right Column: Summary Sidebar -->
      <div class="checkout-sidebar">
        <div class="summary-card">
          <!-- Tour hero image masking -->
          <div class="summary-hero">
            <img :src="mockTour.image" alt="Tour image">
            <div class="hero-overlay">
              <h3>{{ mockTour.title }}</h3>
            </div>
          </div>

          <div class="summary-content">
            <div class="info-row">
              <i class="far fa-calendar-alt"></i>
              <div>
                <span class="label">Ngày khởi hành</span>
                <strong>{{ mockTour.departureDate }}</strong>
              </div>
            </div>
            
            <div class="info-row">
              <i class="fas fa-user-friends"></i>
              <div>
                <span class="label">Số lượng khách</span>
                <strong>{{ mockTour.guests }}</strong>
              </div>
            </div>

            <hr class="divider">

            <div class="price-breakdown">
              <div class="price-item">
                <span>Giá tour (x3)</span>
                <strong>{{ mockTour.basePrice }}</strong>
              </div>
              <div class="price-item">
                <span>Thuế & Phí dịch vụ</span>
                <strong>{{ mockTour.taxesAndFees }}</strong>
              </div>
              <div class="price-item discount">
                <span>Giảm giá thành viên</span>
                <strong>{{ mockTour.discount }}</strong>
              </div>
            </div>

            <hr class="divider">

            <div class="total-row">
              <span class="label">GIÁ TRỌN GÓI<br><span style="font-size: 1.1rem; color: #111827; margin-top: 4px; display: block">Tổng cộng</span></span>
              <strong class="total-price">{{ mockTour.totalPrice }}</strong>
            </div>

            <button class="btn-primary" @click="handlePayment">
              Thanh toán ngay <i class="fas fa-arrow-right" style="margin-left: 8px"></i>
            </button>
            <p class="terms-text">
              Bằng cách nhấp vào "Thanh toán ngay", bạn đồng ý với <a href="#">Điều khoản & Điều kiện</a> của chúng tôi.
            </p>
          </div>
        </div>

        <div class="trust-badges">
          <div class="badge-card">
            <i class="fas fa-bolt"></i> Xác nhận đơn hàng tức thì qua Email
          </div>
          <div class="badge-card">
            <i class="fas fa-headset"></i> Hỗ trợ khách hàng 24/7 trong chuyến đi
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ThuTucThanhToanTour",
  data() {
    return {
      form: {
        fullName: "",
        phone: "",
        email: "",
        address: "",
        paymentMethod: "credit_card"
      },
      // Mock Data based on the UI screenshot
      mockTour: {
        title: "Khám Phá Kỳ Quan Thiên Nhiên Thế Giới: Vịnh Hạ Long 3N2Đ",
        image: "https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&q=80&w=800", // Using a nice Ha Long Bay/Vietnam-like stock photo
        departureDate: "Thứ Bảy, 15 Tháng 06, 2024",
        guests: "2 Người lớn, 1 Trẻ em",
        basePrice: "12.450.000₫",
        taxesAndFees: "550.000₫",
        discount: "-200.000₫",
        totalPrice: "12.800.000₫"
      }
    }
  },
  methods: {
    handlePayment() {
      // In a real app, this would validate the form and call the payment gateway API
      alert(`Đang tiến hành thanh toán qua phương thức: ${this.form.paymentMethod}`);
    }
  }
}
</script>

<style scoped>
.checkout-page {
  width: min(1200px, calc(100% - 40px));
  margin: 0 auto;
  padding: 32px 0 80px;
  font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #111827;
}

/* Header */
.checkout-header {
  margin-bottom: 40px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #2563EB;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.95rem;
  margin-bottom: 16px;
}
.back-link:hover {
  text-decoration: underline;
}

.checkout-header h1 {
  font-size: 2rem;
  font-weight: 800;
  color: #0F172A;
  margin: 0;
}

/* Layout */
.checkout-grid {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 40px;
  align-items: start;
}

/* --- Left Column --- */
.checkout-main {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.checkout-section {
  background: white;
  border-radius: 24px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.03);
  border: 1px solid #F1F5F9;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  color: #1E3A8A;
}

.section-header i {
  font-size: 1.25rem;
}

.section-header h2 {
  font-size: 1.25rem;
  font-weight: 800;
  color: #0F172A;
  margin: 0;
}

/* Forms */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 0.75rem;
  font-weight: 700;
  color: #64748B;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.form-group input {
  padding: 14px 16px;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  background: #F8FAFC;
  font-size: 1rem;
  color: #1E293B;
  transition: all 0.2s;
}

.form-group input:focus {
  outline: none;
  border-color: #3B82F6;
  background: white;
  box-shadow: 0 0 0 4px #EFF6FF;
}

/* Payment Methods */
.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.payment-method {
  display: flex;
  align-items: center;
  padding: 20px;
  border: 2px solid #F1F5F9;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.2s;
}

.payment-method:hover {
  border-color: #CBD5E1;
}

.payment-method--active {
  border-color: #1E3A8A;
  background: #F8FAFC;
}

.method-icon {
  width: 48px;
  height: 48px;
  background: #EFF6FF;
  color: #1E3A8A;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin-right: 16px;
}

.method-details {
  flex: 1;
}

.method-details h3 {
  margin: 0 0 4px;
  font-size: 1rem;
  font-weight: 700;
  color: #1E293B;
}

.method-details p {
  margin: 0;
  font-size: 0.85rem;
  color: #64748B;
}

/* Custom Radio */
.method-radio {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.method-radio input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.custom-radio {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  border: 2px solid #CBD5E1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.method-radio input:checked ~ .custom-radio {
  border-color: #1E3A8A;
}

.method-radio input:checked ~ .custom-radio::after {
  content: "";
  width: 12px;
  height: 12px;
  background: #1E3A8A;
  border-radius: 50%;
}

.security-badge {
  background: #F8FAFC;
  color: #64748B;
  padding: 16px;
  border-radius: 12px;
  text-align: center;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.security-badge i {
  color: #10B981; /* Green lock is good for trust */
  font-size: 1.1rem;
}

/* --- Right Column Sidebar --- */
.checkout-sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.summary-card {
  background: white;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 12px 32px rgba(0,0,0,0.06);
  border: 1px solid #E2E8F0;
}

.summary-hero {
  position: relative;
  height: 220px;
}

.summary-hero img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 40px 24px 20px; /* Gradient from bottom */
  background: linear-gradient(0deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 100%);
}

.hero-overlay h3 {
  color: white;
  margin: 0;
  font-size: 1.15rem;
  font-weight: 800;
  line-height: 1.5;
}

.summary-content {
  padding: 24px;
}

.info-row {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 20px;
}

.info-row i {
  color: #3B82F6;
  font-size: 1.2rem;
  margin-top: 2px;
}

.info-row .label {
  display: block;
  font-size: 0.8rem;
  color: #64748B;
  margin-bottom: 4px;
  font-weight: 600;
}

.info-row strong {
  display: block;
  font-size: 0.95rem;
  color: #0F172A;
  font-weight: 700;
}

.divider {
  border: 0;
  border-top: 1px dashed #CBD5E1;
  margin: 24px 0;
}

/* Price Breakdown */
.price-breakdown {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.price-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #475569;
  font-size: 0.95rem;
}

.price-item strong {
  font-weight: 700;
  color: #1E293B;
}

.price-item.discount {
  color: #059669;
}
.price-item.discount strong {
  color: #059669;
}

/* Total */
.total-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 24px;
}

.total-row .label {
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.05em;
  color: #64748B;
}

.total-price {
  font-size: 1.8rem;
  font-weight: 900;
  color: #00609C; /* The blue from the image right-side summary */
}

.btn-primary {
  width: 100%;
  background: #00609C; 
  color: white;
  padding: 16px;
  border: none;
  border-radius: 14px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: #004D7A;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 96, 156, 0.25);
}

.terms-text {
  margin: 16px 0 0;
  text-align: center;
  font-size: 0.8rem;
  color: #64748B;
  line-height: 1.5;
}

.terms-text a {
  color: #2563EB;
  text-decoration: none;
}
.terms-text a:hover {
  text-decoration: underline;
}

.trust-badges {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.badge-card {
  background: #F8FAFC;
  padding: 16px 20px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.9rem;
  font-weight: 700;
  color: #1E293B;
  border: 1px solid #F1F5F9;
}

.badge-card i {
  color: #3B82F6; /* Use standard blue for badges */
  font-size: 1.2rem;
}


@media (max-width: 1024px) {
  .checkout-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }
  
  .checkout-sidebar {
    order: -1; /* Sidebar summary comes first on mobile */
  }
}

@media (max-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .checkout-section {
    padding: 24px;
  }
}
</style>
