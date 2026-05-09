import os

path = r'c:\xampp\htdocs\DoAnTotNghiepFE\src\components\KhachHang\Tour\ChiTiet\index.vue'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Revert chonLich
old_chonLich = """    chonLich(lich) {
      if (!this.maKhachHang) {
        const redirect = `/khach-hang/tour/${this.ma_tour}/thanh-toan?schedule=${encodeURIComponent(lich.id)}`;
        this.$router.push({
          path: "/dang-nhap",
          query: { redirect },
        });
        return;
      }
      this.selectedLich = lich;
      this.showPaymentModal = true;
    }"""

new_chonLich = """    chonLich(lich) {
      if (!this.maKhachHang) {
        const redirect = `/khach-hang/tour/${this.ma_tour}/thanh-toan?schedule=${encodeURIComponent(lich.id)}`;
        this.$router.push({
          path: "/dang-nhap",
          query: { redirect },
        });
        return;
      }
      this.$router.push({
        path: `/khach-hang/tour/${this.ma_tour}/thanh-toan`,
        query: {
          schedule: lich.id,
        },
      });
    }"""
content = content.replace(old_chonLich, new_chonLich)

# 2. Remove Payment QR Modal from template
old_html = """  <!-- Payment QR Modal -->
    <div class="payment-modal-overlay" v-if="showPaymentModal" @click.self="showPaymentModal = false">
      <div class="payment-modal-card">
        <div class="payment-header">
          <h3>Thanh toán Tour</h3>
          <button class="close-btn" @click="showPaymentModal = false">&times;</button>
        </div>
        <div class="payment-body text-center">
          <p class="payment-instruction">Vui lòng quét mã QR dưới đây để thanh toán Tour này.</p>
          <img src="https://api.vietqr.io/image/970436-0987654321-yKkC39Z.jpg?amount=0" alt="QR Code" class="qr-code-img" />
          <h2 class="payment-amount" v-if="tour">{{ tour.gia_hien_thi }}</h2>
          <p class="payment-order" v-if="selectedLich">Lịch khởi hành: {{ selectedLich.ngay_bat_dau_hien_thi }}</p>
        </div>
      </div>
    </div>"""
content = content.replace(old_html, "")

# 3. Remove CSS
old_css = """/* Payment Modal */
.payment-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
}
.payment-modal-card {
  width: 90%;
  max-width: 400px;
  background: #ffffff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
.payment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  border-bottom: 1px solid #f3f4f6;
  padding-bottom: 12px;
}
.payment-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #0f172a;
  font-weight: 700;
}
.close-btn {
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: #64748b;
  line-height: 1;
}
.qr-code-img {
  width: 250px;
  height: 250px;
  margin: 0 auto;
  display: block;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 8px;
}
.text-center { text-align: center; }
.payment-instruction {
  margin-bottom: 20px;
  color: #475569;
  font-size: 0.95rem;
  line-height: 1.5;
}
.payment-amount {
  margin-top: 24px;
  color: #2563eb;
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 8px;
}
.payment-order {
  color: #64748b;
  font-size: 0.9rem;
  margin-top: 0;
}
"""
content = content.replace(old_css, "")

# 4. Clean up data()
old_data = """      maKhachHang: "",
      showPaymentModal: false,
      selectedLich: null,"""
new_data = """      maKhachHang: "","""
content = content.replace(old_data, new_data)

with open(path, 'w', encoding='utf-8', newline='') as f:
    f.write(content)
print("Reverted Tour Detail successfully")
