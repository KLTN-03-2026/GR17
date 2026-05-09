<template>
  <div class="result-container" v-if="noiDung">
    <div class="result-main-card">
      <div class="card-glow"></div>
      <div class="markdown-body">
        <!-- If we don't have a markdown parser yet, we'll just render text or very basic HTML -->
        <div v-html="noiDungDaDinhDang"></div>
      </div>
    </div>

    <!-- Grounding Chunks Accordion (Nguồn & Địa điểm xác thực) -->
    <div class="sources-accordion" v-if="tatCaLienKet.length > 0">
      <button class="accordion-header" @click="chuyenTrangThaiMoRong">
        <div class="header-left">
          <div class="header-icon">
            <i class="fas fa-search"></i>
          </div>
          <div class="header-copy">
            <h3>Nguồn & Địa điểm xác thực</h3>
            <p>{{ tatCaLienKet.length }} kết quả từ Google Search & Maps</p>
          </div>
        </div>
        <div class="header-arrow" :class="{'is-expanded': dangMoRong}">
          <i class="fas fa-chevron-down"></i>
        </div>
      </button>

      <div class="accordion-body" v-show="dangMoRong">
        <div class="divider"></div>
        <div class="links-grid">
          <a 
            v-for="(link, idx) in tatCaLienKet" 
            :key="idx" 
            :href="link.uri" 
            target="_blank" 
            rel="noopener noreferrer"
            class="link-card"
          >
            <div class="link-left">
              <div class="link-icon" :class="link.type">
                <i v-if="link.type === 'maps'" class="fas fa-map-marker-alt"></i>
                <i v-else class="fas fa-globe"></i>
              </div>
              <div class="link-text">
                <span class="link-title">{{ link.title || (link.type === 'maps' ? 'Xem trên bản đồ' : 'Xem nguồn tin') }}</span>
                <span class="link-type">{{ link.type === 'maps' ? 'Google Maps' : 'Web Reference' }}</span>
              </div>
            </div>
            <div class="link-external">
              <i class="fas fa-external-link-alt"></i>
            </div>
          </a>
        </div>

        <div class="ai-disclaimer">
          <div class="dis-icon">
            <i class="fas fa-info-circle"></i>
          </div>
          <p>
            Các thông tin và địa điểm trên đã được AI đối soát trực tiếp với dữ liệu thực tế từ Google để đảm bảo tính chính xác và tin cậy nhất cho hành trình của bạn.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
function maHoaHtml(value = "") {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function dinhDangChuInDam(value = "") {
  return String(value).replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>");
}

function chuyenMarkdownAnToanSangHtml(value = "") {
  const noiDungDaMaHoa = maHoaHtml(value).replace(/\r\n?/g, "\n");
  const cacDong = noiDungDaMaHoa.split("\n");
  const html = [];
  let dangMoDanhSach = false;

  for (const dongGoc of cacDong) {
    const dong = dongGoc.trimEnd();
    const dongRutGon = dong.trim();

    if (!dongRutGon) {
      if (dangMoDanhSach) {
        html.push("</ul>");
        dangMoDanhSach = false;
      }
      html.push("<br/>");
      continue;
    }

    if (dongRutGon.startsWith("### ")) {
      if (dangMoDanhSach) {
        html.push("</ul>");
        dangMoDanhSach = false;
      }
      html.push(`<h3>${dinhDangChuInDam(dongRutGon.slice(4))}</h3>`);
      continue;
    }

    if (dongRutGon.startsWith("## ")) {
      if (dangMoDanhSach) {
        html.push("</ul>");
        dangMoDanhSach = false;
      }
      html.push(`<h2>${dinhDangChuInDam(dongRutGon.slice(3))}</h2>`);
      continue;
    }

    if (dongRutGon.startsWith("- ")) {
      if (!dangMoDanhSach) {
        html.push("<ul>");
        dangMoDanhSach = true;
      }
      html.push(`<li>${dinhDangChuInDam(dongRutGon.slice(2))}</li>`);
      continue;
    }

    if (dangMoDanhSach) {
      html.push("</ul>");
      dangMoDanhSach = false;
    }

    html.push(`<p>${dinhDangChuInDam(dongRutGon)}</p>`);
  }

  if (dangMoDanhSach) {
    html.push("</ul>");
  }

  return html.join("");
}

export default {
  name: "ItineraryResult",
  props: {
    noiDung: {
      type: String,
      default: ""
    },
    duLieuThamChieu: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      dangMoRong: false
    };
  },
  computed: {
    tatCaLienKet() {
      if (!this.duLieuThamChieu || this.duLieuThamChieu.length === 0) return [];
      
      const mapsLinks = this.duLieuThamChieu
        .filter(chunk => chunk.maps)
        .map(chunk => ({ ...chunk.maps, type: 'maps' }));
        
      const webLinks = this.duLieuThamChieu
        .filter(chunk => chunk.web)
        .map(chunk => ({ ...chunk.web, type: 'web' }));
        
      return [...mapsLinks, ...webLinks];
    },
    noiDungDaDinhDang() {
      return chuyenMarkdownAnToanSangHtml(this.noiDung || "");
    }
  },
  methods: {
    chuyenTrangThaiMoRong() {
      this.dangMoRong = !this.dangMoRong;
    }
  }
};
</script>

<style scoped>
.result-container {
  display: flex;
  flex-direction: column;
  gap: 32px;
  animation: slide-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

/* MAIN CARD */
.result-main-card {
  background: #ffffff;
  padding: 48px;
  border-radius: 40px;
  box-shadow: 0 25px 50px -12px rgba(241, 245, 249, 0.8);
  border: 1px solid #f8fafc;
  position: relative;
  overflow: hidden;
}

.card-glow {
  position: absolute;
  top: 0;
  right: 0;
  width: 256px;
  height: 256px;
  background-color: rgba(239, 246, 255, 0.8);
  border-radius: 50%;
  filter: blur(80px);
  transform: translate(30%, -30%);
  transition: transform 1s ease;
}

.result-main-card:hover .card-glow {
  transform: translate(20%, -20%) scale(1.1);
}

.markdown-body {
  position: relative;
  z-index: 10;
  color: #334155;
  line-height: 1.8;
  font-size: 1.05rem;
}

.markdown-body :deep(h2) {
  font-size: 1.8rem;
  font-weight: 900;
  color: #0f172a;
  margin-top: 2rem;
  margin-bottom: 1rem;
}
.markdown-body :deep(h2:first-child) {
  margin-top: 0;
}

.markdown-body :deep(h3) {
  font-size: 1.3rem;
  font-weight: 800;
  color: #1e293b;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}

.markdown-body :deep(strong) {
  color: #0f172a;
  font-weight: 700;
}

.markdown-body :deep(li) {
  margin-left: 1.5rem;
  list-style-type: disc;
  margin-bottom: 0.5rem;
}

/* ACCORDION */
.sources-accordion {
  background: #0f172a;
  color: white;
  border-radius: 40px;
  box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.2);
  border: 1px solid #1e293b;
  overflow: hidden;
}

.accordion-header {
  width: 100%;
  padding: 32px 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: transparent;
  border: none;
  color: white;
  cursor: pointer;
  text-align: left;
  transition: background-color 0.3s ease;
}

.accordion-header:hover {
  background: rgba(255, 255, 255, 0.05);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-icon {
  width: 48px;
  height: 48px;
  background: #2563eb;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  box-shadow: 0 10px 20px rgba(30, 58, 138, 0.4);
}

.header-copy h3 {
  font-size: 1.25rem;
  font-weight: 900;
  letter-spacing: -0.5px;
  margin: 0 0 4px 0;
}

.header-copy p {
  font-size: 0.65rem;
  color: #94a3b8;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin: 0;
}

.header-arrow {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  transition: transform 0.4s ease;
}
.header-arrow.is-expanded {
  transform: rotate(180deg);
}

.accordion-body {
  padding: 0 40px 40px;
}

.divider {
  height: 1px;
  background: rgba(255,255,255,0.1);
  margin-bottom: 32px;
}

.links-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

@media (max-width: 768px) {
  .links-grid {
    grid-template-columns: 1fr;
  }
}

.link-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.05);
  text-decoration: none;
  transition: all 0.3s ease;
}

.link-card:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(59, 130, 246, 0.3);
}

.link-left {
  display: flex;
  align-items: center;
  gap: 16px;
  overflow: hidden;
}

.link-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.link-icon.maps {
  background: rgba(34, 197, 94, 0.1);
  color: #4ade80;
}
.link-icon.web {
  background: rgba(59, 130, 246, 0.1);
  color: #60a5fa;
}

.link-text {
  min-width: 0;
}
.link-title {
  display: block;
  font-weight: 700;
  font-size: 0.9rem;
  color: #e2e8f0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 2px;
  transition: color 0.3s ease;
}
.link-card:hover .link-title {
  color: #ffffff;
}

.link-type {
  display: block;
  font-size: 0.65rem;
  color: #64748b;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.link-external {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #475569;
  transition: color 0.3s ease;
}
.link-card:hover .link-external {
  color: #60a5fa;
}

/* AI DISCLAIMER */
.ai-disclaimer {
  margin-top: 32px;
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 24px;
  background: rgba(37, 99, 235, 0.1);
  border-radius: 24px;
  border: 1px solid rgba(37, 99, 235, 0.2);
}

.dis-icon {
  width: 32px;
  height: 32px;
  background: rgba(37, 99, 235, 0.2);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #60a5fa;
  flex-shrink: 0;
}

.ai-disclaimer p {
  color: rgba(191, 219, 254, 0.8);
  font-size: 0.8rem;
  font-weight: 500;
  line-height: 1.6;
  margin: 0;
}
</style>
