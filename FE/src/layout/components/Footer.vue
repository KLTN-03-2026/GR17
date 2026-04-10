<template>
  <footer class="site-footer">
    <div class="site-footer__inner">
      <div class="site-footer__top">
        <div class="site-footer__brand">
          <div class="site-footer__logo-line">
            <span class="site-footer__mark">
              <i class="fas fa-map"></i>
            </span>
            <strong>Smart Travel</strong>
          </div>
          <p>
            Mang thế giới đến gần bạn hơn bằng cách lập kế hoạch du lịch thông minh, cá nhân hóa.
          </p>
        </div>

        <div class="site-footer__links">
          <div class="site-footer__column">
            <h5>Công ty</h5>
            <a href="#">Về Chúng Tôi</a>
            <a href="#">Tuyển Dụng</a>
            <a href="#">Đối Tác</a>
            <a href="#">Truyền Thông</a>
          </div>

          <div class="site-footer__column">
            <h5>Hỗ trợ</h5>
            <a href="#">Trung Tâm Trợ Giúp</a>
            <a href="#">Liên Hệ Hỗ Trợ</a>
            <a href="#">Chính Sách Bảo Mật</a>
            <a href="#">Điều Khoản Dịch Vụ</a>
          </div>

          <div class="site-footer__column site-footer__column--newsletter">
            <h5>Bản tin</h5>
            <p>Nhận mẹo du lịch và ưu đãi độc quyền qua email của bạn.</p>
            <form class="site-footer__newsletter" @submit.prevent="subscribeNewsletter">
              <input v-model="email" type="email" placeholder="Email của bạn" required>
              <button type="submit">Đăng ký</button>
            </form>
          </div>
        </div>
      </div>

      <div class="site-footer__bottom">
        <p>&copy; 2026 Smart Travel. Mọi quyền được bảo lưu.</p>
        <div class="site-footer__socials">
          <a href="#" aria-label="Toàn cầu"><i class="fas fa-globe"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Chia sẻ"><i class="fas fa-share-alt"></i></a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "Footer",
  data() {
    return {
      email: "",
    };
  },
  methods: {
    subscribeNewsletter() {
      console.log("Đã đăng ký:", this.email);
      this.email = "";
    },
  },
};
</script>

<style scoped>
.site-footer {
  margin-top: 0;
  background: #f4f7fb;
  border-top: 1px solid #e7edf4;
}

.site-footer__inner {
  width: min(1240px, calc(100% - 40px));
  margin: 0 auto;
  padding: 32px 0 24px;
}

.site-footer__top {
  display: grid;
  grid-template-columns: 1.15fr 2fr;
  gap: 44px;
  padding-bottom: 22px;
}

.site-footer__logo-line {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
  color: #0f172a;
}

.site-footer__mark {
  width: 20px;
  height: 20px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #4f2cff 0%, #643dff 100%);
  color: #fff;
  font-size: 0.68rem;
}

.site-footer__brand p,
.site-footer__column p {
  margin: 0;
  color: #7b8699;
  font-size: 0.84rem;
  line-height: 1.8;
}

.site-footer__links {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 32px;
}

.site-footer__column {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.site-footer__column h5 {
  margin: 0 0 8px;
  color: #111827;
  font-size: 0.92rem;
}

.site-footer__column a {
  color: #6b7280;
  text-decoration: none;
  font-size: 0.84rem;
}

.site-footer__newsletter {
  display: flex;
  gap: 8px;
  margin-top: 10px;
}

.site-footer__newsletter input {
  flex: 1;
  min-width: 0;
  height: 40px;
  border: 1px solid #dbe3ee;
  border-radius: 10px;
  background: #fff;
  padding: 0 14px;
  outline: none;
}

.site-footer__newsletter button {
  min-width: 64px;
  border: 0;
  border-radius: 10px;
  background: linear-gradient(135deg, #4a24f5 0%, #5a34ff 100%);
  color: #fff;
  font-weight: 700;
  cursor: pointer;
}

.site-footer__bottom {
  border-top: 1px solid #e3e9f2;
  padding-top: 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.site-footer__bottom p {
  margin: 0;
  color: #98a2b3;
  font-size: 0.76rem;
}

.site-footer__socials {
  display: flex;
  gap: 14px;
}

.site-footer__socials a {
  color: #7c8798;
  text-decoration: none;
  font-size: 0.95rem;
}

@media (max-width: 900px) {
  .site-footer__top {
    grid-template-columns: 1fr;
  }

  .site-footer__links {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .site-footer__inner {
    width: calc(100% - 24px);
  }

  .site-footer__newsletter {
    flex-direction: column;
  }

  .site-footer__bottom {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
