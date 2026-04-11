<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-content">
        <!-- Company Info -->
        <div class="footer-section">
          <h4>
            <i class="fas fa-paper-plane"></i>
            Smart Travel
          </h4>
          <p>Making world exploration accessible and personalized through intelligent planning.</p>
          <div class="social-links">
            <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
          </div>
        </div>

        <!-- Company Links -->
        <div class="footer-section">
          <h5>Company</h5>
          <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Partners</a></li>
            <li><a href="#">Press</a></li>
          </ul>
        </div>

        <!-- Support Links -->
        <div class="footer-section">
          <h5>Support</h5>
          <ul>
            <li><a href="#">Help Center</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div class="footer-section">
          <h5>Newsletter</h5>
          <p>Get travel tips and exclusive deals in your inbox.</p>
          <form @submit.prevent="subscribeNewsletter" class="newsletter-form">
            <input 
              type="email" 
              placeholder="Your email" 
              v-model="email"
              required
            >
            <button type="submit" class="btn-submit">Join</button>
          </form>
        </div>
      </div>

      <!-- Copyright -->
      <div class="footer-bottom">
        <p>&copy; {{ currentYear }} Smart Travel. All rights reserved.</p>
        <div class="footer-icons">
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-linkedin"></i></a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer',
  data() {
    return {
      currentYear: new Date().getFullYear(),
      email: '',
    };
  },
  methods: {
    subscribeNewsletter() {
      console.log('Subscribed:', this.email);
      this.email = '';
    },
  },
};
</script>

<style scoped>
.footer {
  background-color: #1f2937;
  color: #d1d5db;
  padding: 3rem 0;
  margin-top: 4rem;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #374151;
}

.footer-section h4 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: white;
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.footer-section h4 i {
  color: #6366f1;
}

.footer-section h5 {
  color: white;
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 0.95rem;
  font-weight: 600;
}

.footer-section p {
  font-size: 0.9rem;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-section li {
  margin-bottom: 0.5rem;
}

.footer-section a {
  color: #d1d5db;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 0.9rem;
}

.footer-section a:hover {
  color: #6366f1;
}

.social-links {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background-color: #374151;
  color: #d1d5db;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.social-link:hover {
  background-color: #6366f1;
  color: white;
}

.newsletter-form {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.newsletter-form input {
  flex: 1;
  padding: 0.5rem;
  border: none;
  border-radius: 0.35rem;
  background-color: #374151;
  color: white;
  font-size: 0.9rem;
}

.newsletter-form input::placeholder {
  color: #9ca3af;
}

.newsletter-form input:focus {
  outline: none;
  box-shadow: 0 0 0 2px #6366f1;
}

.btn-submit {
  padding: 0.5rem 1rem;
  background-color: #6366f1;
  color: white;
  border: none;
  border-radius: 0.35rem;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.btn-submit:hover {
  background-color: #4f46e5;
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
}

.footer-bottom p {
  margin: 0;
  font-size: 0.85rem;
  color: #9ca3af;
}

.footer-icons {
  display: flex;
  gap: 1rem;
}

.footer-icons a {
  color: #9ca3af;
  font-size: 1.1rem;
  transition: color 0.3s ease;
}

.footer-icons a:hover {
  color: #6366f1;
}

@media (max-width: 768px) {
  .footer {
    padding: 2rem 0;
  }

  .footer-container {
    padding: 0 1rem;
  }

  .footer-content {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .newsletter-form {
    flex-direction: column;
  }
}
</style>

