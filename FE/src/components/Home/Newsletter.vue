<template>
  <section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
      <div class="newsletter-wrapper">
        <!-- Decorative Elements -->
        <div class="dec-elem dec-top-right"></div>
        <div class="dec-elem dec-bottom-left"></div>
        
        <div class="newsletter-content">
          <div class="newsletter-icon animate-scale-in">
            <i class="fas fa-envelope-open-text"></i>
          </div>
          
          <h2 class="section-title">
            Nhận Ưu Đãi <span class="text-light italic">Đặc Biệt</span>
          </h2>
          <p class="section-desc">
            Đăng ký nhận bản tin để không bỏ lỡ những hành trình mới nhất và các ưu đãi độc quyền dành riêng cho bạn.
          </p>
          
          <form class="newsletter-form" @submit.prevent>
            <div class="input-wrap">
              <input 
                type="email" 
                placeholder="Địa chỉ email của bạn"
                class="form-input"
              />
              <i class="fas fa-sparkles abs-icon"></i>
            </div>
            <button type="submit" class="submit-btn group shadow-xl">
              Đăng ký ngay
              <i class="fas fa-paper-plane group-hover-translate transition-transform"></i>
            </button>
          </form>
          
          <p class="privacy-note">
            Chúng tôi cam kết bảo mật thông tin của bạn.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'Newsletter'
}
</script>

<style scoped>
.py-24 { padding-top: 6rem; padding-bottom: 6rem; }
.relative { position: relative; }
.overflow-hidden { overflow: hidden; }
.max-w-7xl { max-width: 80rem; margin-left: auto; margin-right: auto; }
.px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }

.newsletter-wrapper {
  position: relative;
  background-color: #2563eb;
  border-radius: 4rem;
  padding: 3rem;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(191,219,254,1);
}
@media (min-width: 768px) {
  .newsletter-wrapper { padding: 6rem; }
}

.dec-elem {
  position: absolute;
  border-radius: 50%;
}
.dec-top-right {
  top: 0; right: 0;
  width: 24rem; height: 24rem;
  background-color: rgba(255,255,255,0.1);
  filter: blur(100px);
  margin-right: -12rem; margin-top: -12rem;
}
.dec-bottom-left {
  bottom: 0; left: 0;
  width: 16rem; height: 16rem;
  background-color: rgba(96,165,250,0.2);
  filter: blur(80px);
  margin-left: -8rem; margin-bottom: -8rem;
}

.newsletter-content {
  position: relative;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  max-width: 48rem;
  margin: 0 auto;
}

.newsletter-icon {
  width: 4rem; height: 4rem;
  background-color: rgba(255,255,255,0.2);
  backdrop-filter: blur(12px);
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 2rem;
  margin-bottom: 2rem;
}

.section-title {
  font-size: 2.25rem;
  font-weight: 900;
  color: #ffffff;
  letter-spacing: -0.05em;
  line-height: 0.9;
  margin: 0 0 1.5rem 0;
}
@media (min-width: 768px) {
  .section-title { font-size: 3.75rem; }
}
.text-light { color: #bfdbfe; }
.italic { font-style: italic; }

.section-desc {
  color: #dbeafe;
  font-size: 1.125rem;
  font-weight: 500;
  line-height: 1.625;
  margin: 0 0 3rem 0;
}
@media (min-width: 768px) {
  .section-desc { font-size: 1.25rem; }
}

.newsletter-form {
  width: 100%;
  max-width: 32rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
@media (min-width: 640px) {
  .newsletter-form { flex-direction: row; }
}

.input-wrap {
  flex: 1;
  position: relative;
}
.form-input {
  width: 100%;
  padding: 1.25rem 2rem;
  background-color: #ffffff;
  border-radius: 1rem;
  color: #0f172a;
  font-weight: 700;
  border: none;
  outline: none;
  transition: all 0.3s ease;
  box-sizing: border-box;
}
.form-input::placeholder { color: #94a3b8; }
.form-input:focus { box-shadow: 0 0 0 4px rgba(255,255,255,0.2); }

.abs-icon {
  position: absolute;
  right: 1.5rem;
  top: 50%;
  transform: translateY(-50%);
  color: #2563eb;
  opacity: 0.2;
}

.submit-btn {
  padding: 1.25rem 2.5rem;
  background-color: #0f172a;
  color: #ffffff;
  font-weight: 900;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  border-radius: 1rem;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  transition: all 0.3s ease;
}
.submit-btn:hover {
  background-color: #1e293b;
  transform: scale(1.05);
}
.shadow-xl { box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); }
.group-hover-translate { transition: transform 0.3s ease; }
.group:hover .group-hover-translate { transform: translate(0.25rem, -0.25rem); }

.privacy-note {
  margin-top: 2rem;
  color: rgba(191,219,254,0.6);
  font-size: 0.625rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.2em;
}

.animate-scale-in { animation: scaleIn 0.8s ease-out forwards; }
@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.8); }
  to { opacity: 1; transform: scale(1); }
}
</style>
