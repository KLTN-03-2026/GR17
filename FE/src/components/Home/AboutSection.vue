<template>
  <section id="gioi-thieu" class="py-24 relative overflow-hidden bg-slate-50">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid-2-cols gap-20 items-center">
        <!-- Left Content -->
        <div class="animate-fade-in-left">
          <div class="inline-badge mb-6">
            <i class="fas fa-shield-alt text-blue"></i>
            <span>Về Smart Travel</span>
          </div>
          
          <h2 class="section-title mb-8">
            Kiến Tạo <span class="text-blue italic">Trải Nghiệm</span> Du Lịch Thông Minh
          </h2>
          
          <p class="section-desc mb-10">
            Smart Travel AI không chỉ là một nền tảng đặt tour, chúng tôi là người bạn đồng hành tin cậy, 
            sử dụng trí tuệ nhân tạo để cá nhân hóa mọi hành trình của bạn. Với hơn 10 năm kinh nghiệm, 
            chúng tôi cam kết mang đến những trải nghiệm độc bản và đẳng cấp nhất.
          </p>
          
          <div class="stats-grid">
            <div class="stat-item" v-for="stat in stats" :key="stat.id">
              <div class="stat-value-wrap mb-2">
                <i :class="stat.icon + ' text-blue'"></i>
                <span class="stat-value">{{ stat.value }}</span>
              </div>
              <span class="stat-label">{{ stat.label }}</span>
            </div>
          </div>
        </div>

        <!-- Right Image -->
        <div class="relative animate-scale-in">
          <div class="image-wrapper shadow-2xl border-8 border-white">
            <img 
              src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&q=80&w=1000" 
              alt="About Smart Travel"
              class="cover-img"
              referrerpolicy="no-referrer"
            />
          </div>
          
          <!-- Floating Card -->
          <div class="floating-card hidden md:block">
            <div class="fc-header mb-4">
              <div class="fc-icon-wrap bg-blue text-white">
                <i class="fas fa-sparkles"></i>
              </div>
              <div>
                <h4 class="fc-title">AI Powered</h4>
                <p class="fc-subtitle">Cá nhân hóa 100%</p>
              </div>
            </div>
            <p class="fc-desc">
              Hệ thống AI của chúng tôi phân tích hàng triệu dữ liệu để đưa ra gợi ý hoàn hảo nhất cho bạn.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'AboutSection',
  data() {
    return {
      stats: [
        { id: 1, label: 'Khách hàng hài lòng', value: '50K+', icon: 'fas fa-users' },
        { id: 2, label: 'Điểm đến toàn cầu', value: '200+', icon: 'fas fa-globe' },
        { id: 3, label: 'Giải thưởng du lịch', value: '15+', icon: 'fas fa-award' },
        { id: 4, label: 'Năm kinh nghiệm', value: '10+', icon: 'fas fa-heart' },
      ]
    }
  }
}
</script>

<style scoped>
.bg-slate-50 {
  background-color: rgba(248, 250, 252, 0.5);
}
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
.bg-blue { background-color: #2563eb; }
.text-white { color: #ffffff; }
.mb-6 { margin-bottom: 1.5rem; }
.mb-8 { margin-bottom: 2rem; }
.mb-10 { margin-bottom: 2.5rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-4 { margin-bottom: 1rem; }

.section-title {
  font-size: 3rem;
  font-weight: 900;
  color: #0f172a;
  letter-spacing: -0.05em;
  line-height: 0.9;
}
@media (min-width: 768px) {
  .section-title { font-size: 3.75rem; }
}
.italic { font-style: italic; }

.section-desc {
  color: #475569;
  font-size: 1.125rem;
  font-weight: 500;
  line-height: 1.625;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 2rem;
}
.stat-item {
  display: flex;
  flex-direction: column;
}
.stat-value-wrap {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.stat-value-wrap i {
  font-size: 1.25rem;
}
.stat-value {
  font-size: 1.875rem;
  font-weight: 900;
  color: #0f172a;
  letter-spacing: -0.05em;
}
.stat-label {
  font-size: 0.625rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #94a3b8;
}

.image-wrapper {
  aspect-ratio: 1 / 1;
  border-radius: 4rem;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(191,219,254,0.5);
}
.border-8 { border-width: 8px; }
.border-white { border-color: #ffffff; border-style: solid; }
.cover-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.floating-card {
  position: absolute;
  bottom: -2.5rem;
  left: -2.5rem;
  background-color: #ffffff;
  padding: 2rem;
  border-radius: 2.5rem;
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
  border: 1px solid #f1f5f9;
  max-width: 280px;
}
.fc-header {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.fc-icon-wrap {
  width: 3rem;
  height: 3rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}
.fc-title {
  font-size: 0.875rem;
  font-weight: 900;
  color: #0f172a;
  margin: 0;
}
.fc-subtitle {
  font-size: 0.625rem;
  color: #94a3b8;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin: 0;
}
.fc-desc {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 500;
  line-height: 1.625;
  margin: 0;
}
@media (max-width: 767px) {
  .hidden { display: none; }
}

/* Animations */
.animate-fade-in-left {
  animation: fadeInLeft 0.8s ease-out forwards;
}
.animate-scale-in {
  animation: scaleIn 0.8s ease-out forwards;
}
@keyframes fadeInLeft {
  from { opacity: 0; transform: translateX(-50px); }
  to { opacity: 1; transform: translateX(0); }
}
@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.8); }
  to { opacity: 1; transform: scale(1); }
}
</style>
