<template>
  <section id="lien-he" class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid-2-cols gap-20 items-center">
        <!-- Left Content -->
        <div class="animate-fade-in-left">
          <div class="inline-badge mb-6">
            <i class="fas fa-envelope text-blue"></i>
            <span>Liên hệ với chúng tôi</span>
          </div>
          
          <h2 class="section-title mb-8">
            Sẵn Sàng Cho <span class="text-blue italic">Hành Trình</span> Mới?
          </h2>
          
          <p class="section-desc mb-12">
            Hãy để chúng tôi giúp bạn lên kế hoạch cho chuyến đi mơ ước. Đội ngũ chuyên gia của chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7.
          </p>

          <div class="contact-methods mb-12">
            <div class="contact-item group" v-for="info in contactInfo" :key="info.id">
              <div class="icon-wrap group-hover-bg transition-all duration-500">
                <i :class="info.icon"></i>
              </div>
              <div>
                <h4 class="info-label">{{ info.label }}</h4>
                <p class="info-value">{{ info.value }}</p>
              </div>
            </div>
          </div>

          <div class="social-links">
            <a href="#" class="social-btn" v-for="(icon, idx) in socialIcons" :key="idx">
              <i :class="icon"></i>
            </a>
          </div>
        </div>

        <!-- Right Form -->
        <div class="form-wrapper animate-fade-in-up shadow-2xl">
          <form class="contact-form" @submit.prevent="xuLyGui">
            <div class="form-row grid-2-cols gap-8 mb-8">
              <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" placeholder="Nguyễn Văn A" class="form-input" />
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" placeholder="example@mail.com" class="form-input" />
              </div>
            </div>
            
            <div class="form-group mb-8">
              <label>Chủ đề</label>
              <input type="text" placeholder="Tư vấn tour du lịch" class="form-input" />
            </div>
            
            <div class="form-group mb-8">
              <label>Tin nhắn</label>
              <textarea rows="4" placeholder="Tôi muốn được tư vấn về..." class="form-textarea"></textarea>
            </div>
            
            <button class="submit-btn group shadow-xl">
              Gửi tin nhắn
              <i class="fas fa-paper-plane group-hover-translate transition-transform"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ContactSection',
  data() {
    return {
      contactInfo: [
        { id: 1, label: 'Email', value: 'contact@smarttravel.com', icon: 'fas fa-envelope' },
        { id: 2, label: 'Điện thoại', value: '0123 456 789', icon: 'fas fa-phone' },
        { id: 3, label: 'Địa chỉ', value: '123 Đường du lịch, Quận 1, TP. HCM', icon: 'fas fa-map-marker-alt' },
      ],
      socialIcons: ['fab fa-facebook-f', 'fab fa-instagram', 'fab fa-twitter']
    }
  },
  methods: {
    xuLyGui() {
      // alert('Submitted');
    }
  }
}
</script>

<style scoped>
.py-24 { padding-top: 6rem; padding-bottom: 6rem; }
.relative { position: relative; }
.overflow-hidden { overflow: hidden; }
.max-w-7xl { max-width: 80rem; margin-left: auto; margin-right: auto; }
.px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }

.grid-2-cols {
  display: grid;
  grid-template-columns: 1fr;
  gap: 5rem;
  align-items: center;
}
@media (min-width: 1024px) {
  .grid-2-cols { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
.gap-8 { gap: 2rem; }

.mb-6 { margin-bottom: 1.5rem; }
.mb-8 { margin-bottom: 2rem; }
.mb-12 { margin-bottom: 3rem; }

.inline-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.25rem 0.75rem;
  background-color: #eff6ff;
  border-radius: 9999px;
}
.inline-badge span {
  font-size: 0.625rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #2563eb;
}
.text-blue { color: #2563eb; }
.italic { font-style: italic; }

.section-title {
  font-size: 3rem;
  font-weight: 900;
  color: #0f172a;
  letter-spacing: -0.05em;
  line-height: 0.9;
  margin: 0;
}
@media (min-width: 768px) {
  .section-title { font-size: 3.75rem; }
}

.section-desc {
  color: #64748b;
  font-size: 1.125rem;
  font-weight: 500;
  line-height: 1.625;
  margin: 0;
}

.contact-methods {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.contact-item {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.icon-wrap {
  width: 3.5rem;
  height: 3.5rem;
  background-color: #f8fafc;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2563eb;
  font-size: 1.5rem;
}
.group:hover .group-hover-bg {
  background-color: #2563eb;
  color: #ffffff;
}
.transition-all { transition: all 0.3s ease; }
.duration-500 { transition-duration: 500ms; }

.info-label {
  font-size: 0.625rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #94a3b8;
  margin: 0 0 0.25rem 0;
}
.info-value {
  font-size: 1.125rem;
  font-weight: 900;
  color: #0f172a;
  margin: 0;
}

.social-links {
  display: flex;
  gap: 1rem;
}
.social-btn {
  width: 3rem;
  height: 3rem;
  background-color: #f8fafc;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  text-decoration: none;
  font-size: 1.25rem;
  transition: all 0.3s ease;
}
.social-btn:hover {
  background-color: #2563eb;
  color: #ffffff;
}

.form-wrapper {
  background-color: #ffffff;
  padding: 2.5rem;
  border-radius: 4rem;
  border: 1px solid #f1f5f9;
}
@media (min-width: 768px) {
  .form-wrapper { padding: 4rem; }
}
.shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.form-group label {
  font-size: 0.625rem;
  font-weight: 900;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-left: 0.25rem;
}
.form-input, .form-textarea {
  width: 100%;
  padding: 1rem 1.5rem;
  background-color: #f8fafc;
  border-radius: 1rem;
  border: none;
  font-weight: 700;
  transition: all 0.3s ease;
  color: #0f172a;
  box-sizing: border-box;
}
.form-input:focus, .form-textarea:focus {
  outline: none;
  box-shadow: 0 0 0 4px rgba(37,99,235,0.2);
}
.form-textarea { resize: none; }
.form-input::placeholder, .form-textarea::placeholder {
  color: #cbd5e1;
}

.submit-btn {
  width: 100%;
  padding: 1.25rem 0;
  background-color: #2563eb;
  color: #ffffff;
  border-radius: 1rem;
  font-weight: 900;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  transition: all 0.3s ease;
}
.submit-btn:hover {
  background-color: #1d4ed8;
}
.shadow-xl { box-shadow: 0 20px 25px -5px rgba(37,99,235,0.5); }
.group-hover-translate { transition: transform 0.3s ease; }
.group:hover .group-hover-translate {
  transform: translate(0.25rem, -0.25rem);
}

/* Animations */
.animate-fade-in-left { animation: fadeInLeft 0.8s ease-out forwards; }
.animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
@keyframes fadeInLeft {
  from { opacity: 0; transform: translateX(-50px); }
  to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(50px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
