<template>
  <section id="tin-tuc" class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
      <div class="section-header mb-12">
        <div class="max-w-2xl">
          <div class="inline-badge mb-4">
            <i class="fas fa-book-open text-blue"></i>
            <span>Tin tức du lịch</span>
          </div>
          <h2 class="section-title">
            Cảm Hứng <span class="text-blue italic">Hành Trình</span>
          </h2>
          <p class="section-desc mt-4">
            Khám phá những câu chuyện, kinh nghiệm và bí quyết du lịch từ cộng đồng Smart Travel.
          </p>
        </div>
        
        <button class="view-all-btn group">
          Xem tất cả bài viết
          <i class="fas fa-arrow-right icon-arrow transition-all"></i>
        </button>
      </div>

      <div class="grid-3-cols gap-8">
        <article class="story-card group cursor-pointer" v-for="(story, idx) in stories" :key="story.id" :style="`animation-delay: ${idx * 0.1}s`">
          <div class="image-wrapper shadow-xl mb-6">
            <img 
              :src="story.image" 
              :alt="story.title"
              class="cover-img transition-transform duration-700 group-hover-scale"
              referrerpolicy="no-referrer"
            />
            <div class="category-badge">
              <span>{{ story.category }}</span>
            </div>
          </div>

          <div class="meta-info mb-3">
            <div class="meta-item">
              <i class="far fa-calendar-alt"></i>
              {{ story.date }}
            </div>
            <div class="meta-item">
              <i class="far fa-user"></i>
              {{ story.author }}
            </div>
          </div>

          <h3 class="story-title mb-3 transition-colors">
            {{ story.title }}
          </h3>
          <p class="story-excerpt line-clamp-2">
            {{ story.excerpt }}
          </p>
        </article>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'NewsSection',
  data() {
    return {
      stories: [
        {
          id: 1,
          title: "Khám phá vẻ đẹp tiềm ẩn của Hà Giang",
          excerpt: "Hành trình chinh phục những cung đường đèo hiểm trở và chiêm ngưỡng vẻ đẹp hùng vĩ của cao nguyên đá...",
          author: "Nguyễn Văn A",
          date: "15/03/2026",
          image: "https://images.unsplash.com/photo-1504457047772-27fb18144da9?auto=format&fit=crop&q=80&w=800",
          category: "Adventure"
        },
        {
          id: 2,
          title: "10 món ăn không thể bỏ qua khi đến Hội An",
          excerpt: "Hội An không chỉ có phố cổ mà còn có nền ẩm thực vô cùng phong phú và đặc sắc. Hãy cùng khám phá...",
          author: "Trần Thị B",
          date: "10/03/2026",
          image: "https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=800",
          category: "Food"
        },
        {
          id: 3,
          title: "Kinh nghiệm du lịch Phú Quốc tự túc",
          excerpt: "Chia sẻ chi tiết về lịch trình, chi phí và những địa điểm check-in cực hot tại đảo ngọc Phú Quốc...",
          author: "Lê Văn C",
          date: "05/03/2026",
          image: "https://images.unsplash.com/photo-1589394815804-964ed962eb33?auto=format&fit=crop&q=80&w=800",
          category: "Guide"
        }
      ]
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

.section-header {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
@media (min-width: 768px) {
  .section-header {
    flex-direction: row;
    align-items: flex-end;
    justify-content: space-between;
  }
}
.max-w-2xl { max-width: 42rem; }
.mb-12 { margin-bottom: 3rem; }
.mb-4 { margin-bottom: 1rem; }
.mb-6 { margin-bottom: 1.5rem; }
.mb-3 { margin-bottom: 0.75rem; }
.mt-4 { margin-top: 1rem; }

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
  font-size: 2.25rem;
  font-weight: 900;
  color: #0f172a;
  letter-spacing: -0.05em;
  line-height: 1;
  margin: 0;
}
@media (min-width: 768px) {
  .section-title { font-size: 3rem; }
}

.section-desc {
  color: #64748b;
  font-weight: 500;
  margin: 1rem 0 0 0;
}

.view-all-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #2563eb;
  font-weight: 900;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  background: none;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}
.view-all-btn:hover .icon-arrow {
  transform: translateX(0.5rem);
}
.transition-all { transition: all 0.3s ease; }

.grid-3-cols {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}
@media (min-width: 768px) {
  .grid-3-cols { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

.story-card {
  animation: fadeInUp 0.5s ease-out both;
}
.cursor-pointer { cursor: pointer; }

.image-wrapper {
  position: relative;
  aspect-ratio: 4 / 3;
  border-radius: 2rem;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(226, 232, 240, 0.5);
}
.cover-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.duration-700 { transition-duration: 700ms; }
.group:hover .group-hover-scale { transform: scale(1.1); }

.category-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
}
.category-badge span {
  padding: 0.25rem 0.75rem;
  background-color: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 9999px;
  font-size: 0.625rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #2563eb;
}

.meta-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 0.625rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #94a3b8;
}
.meta-item {
  display: flex;
  align-items: center;
  gap: 0.375rem;
}

.story-title {
  font-size: 1.25rem;
  font-weight: 900;
  color: #0f172a;
  line-height: 1.25;
  margin: 0;
}
.transition-colors { transition-property: color; transition-duration: 300ms; }
.group:hover .story-title { color: #2563eb; }

.story-excerpt {
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 500;
  line-height: 1.625;
  margin: 0;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
