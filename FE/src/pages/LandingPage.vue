<template>
  <div class="landing-page">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>Plan Your Dream Getaway Smarter</h1>
        <p>Discover personalized itineraries and hidden gems with our AI-powered travel planner.</p>
        <div class="search-box">
          <input type="text" placeholder="Where do you want to go?" v-model="destination">
          <input type="date" v-model="date">
          <button class="btn-explore" @click="explore">
            Explore <i class="fas fa-arrow-right"></i>
          </button>
        </div>
      </div>
    </section>

    <!-- Featured Tours Section -->
    <section class="featured-tours">
      <div class="container">
        <div class="section-header">
          <h2>Featured Tours</h2>
          <a href="#" class="view-all">View all <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="tours-grid">
          <div class="tour-card" v-for="tour in tours" :key="tour.id">
            <div class="tour-image" :style="{ backgroundImage: `url(${tour.image})` }">
              <span class="tour-badge">{{ tour.badge }}</span>
            </div>
            <div class="tour-info">
              <h3>{{ tour.title }}</h3>
              <div class="tour-rating">
                <i class="fas fa-star"></i>
                <span>5.0</span>
                <span class="days">{{ tour.days }} Days</span>
              </div>
              <div class="tour-footer">
                <span class="price">${{ tour.price }}</span>
                <button class="btn-book">Book Now</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular Destinations -->
    <section class="popular-destinations">
      <div class="container">
        <div class="section-header">
          <h2>Popular Destinations</h2>
          <p>Explore top-rated locations handpicked by our expert travel community and AI curators.</p>
        </div>
        <div class="destinations-grid">
          <div class="destination-card large" style="background-image: url('https://images.unsplash.com/photo-1540202404-a2f29016b523?w=600&h=400&fit=crop');">
            <div class="destination-overlay">
              <h3>Kyoto, Japan</h3>
              <p>45 Tours available</p>
            </div>
          </div>
          <div class="destination-card" style="background-image: url('https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=400&h=300&fit=crop');">
            <div class="destination-overlay">
              <h3>Paris</h3>
            </div>
          </div>
          <div class="destination-card" style="background-image: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=300&fit=crop');">
            <div class="destination-overlay">
              <h3>Bali</h3>
            </div>
          </div>
          <div class="destination-card" style="background-image: url('https://images.unsplash.com/photo-1514565131-fce0801e5785?w=400&h=300&fit=crop');">
            <div class="destination-overlay">
              <h3>Venice, Italy</h3>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why Travel with Us Section -->
    <section class="why-us">
      <div class="container">
        <div class="why-us-content">
          <div class="why-us-text">
            <h2>Why Travel with Us?</h2>
            <p>We combine advanced technology with human expertise to create the most seamless travel experiences imaginable.</p>
            <button class="btn-learn">Learn More</button>
          </div>
          <div class="why-us-features">
            <div class="feature-item">
              <div class="feature-icon">
                <i class="fas fa-compass"></i>
              </div>
              <h3>AI Itineraries</h3>
              <p>Smart scheduling tailored to your unique interests and travel style automatically.</p>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <i class="fas fa-tag"></i>
              </div>
              <h3>Best Prices</h3>
              <p>Exclusive deals and hidden discounts you won't find anywhere else on the globe.</p>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <i class="fas fa-headset"></i>
              </div>
              <h3>24/7 Support</h3>
              <p>Our dedicated team is here to help you at any time, anywhere around the globe.</p>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <i class="fas fa-lock"></i>
              </div>
              <h3>Secure Booking</h3>
              <p>Your data and payments are protected with industry-leading security protocols.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'LandingPage',
  data() {
    return {
      destination: '',
      date: '',
      tours: [
        {
          id: 1,
          title: 'Swiss Alps Adventure',
          badge: 'Best Seller',
          image: 'https://images.unsplash.com/photo-1531583788116-75f5b4b82b70?w=400&h=300&fit=crop',
          days: 7,
          price: '1,200',
        },
        {
          id: 2,
          title: 'Maldives Relaxation',
          badge: 'Popular',
          image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop',
          days: 5,
          price: '2,500',
        },
        {
          id: 3,
          title: 'Historic Rome Tour',
          badge: 'Cultural',
          image: 'https://images.unsplash.com/photo-1552832860-cfaf79c98e6d?w=400&h=300&fit=crop',
          days: 10,
          price: '1,800',
        },
      ],
    };
  },
  methods: {
    explore() {
      console.log('Exploring:', this.destination, this.date);
    },
  },
};
</script>

<style scoped>
.landing-page {
  width: 100%;
}

/* Hero Section */
.hero {
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
    url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&h=600&fit=crop');
  background-size: cover;
  background-position: center;
  padding: 8rem 2rem;
  text-align: center;
  color: white;
  min-height: 500px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-content h1 {
  font-size: 3.5rem;
  margin-bottom: 1rem;
  font-weight: 700;
  line-height: 1.2;
}

.hero-content p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.search-box {
  display: flex;
  gap: 0.5rem;
  max-width: 600px;
  margin: 0 auto;
  background-color: white;
  padding: 0.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.search-box input {
  flex: 1;
  border: none;
  padding: 0.75rem;
  outline: none;
  font-size: 0.95rem;
  border-radius: 0.35rem;
}

.search-box input::placeholder {
  color: #9ca3af;
}

.btn-explore {
  padding: 0.75rem 2rem;
  background-color: #6366f1;
  color: white;
  border: none;
  border-radius: 0.35rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.3s ease;
}

.btn-explore:hover {
  background-color: #4f46e5;
}

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Section Headers */
.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-header h2 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.section-header p {
  color: #6b7280;
  font-size: 1rem;
}

.view-all {
  color: #6366f1;
  text-decoration: none;
  font-weight: 600;
  display: inline-block;
  margin-top: 1rem;
  transition: color 0.3s ease;
}

.view-all:hover {
  color: #4f46e5;
}

/* Featured Tours */
.featured-tours {
  padding: 4rem 0;
  background-color: #f9fafb;
}

.tours-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.tour-card {
  background-color: white;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.tour-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 16px rgba(0, 0, 0, 0.15);
}

.tour-image {
  height: 200px;
  background-size: cover;
  background-position: center;
  position: relative;
}

.tour-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background-color: #10b981;
  color: white;
  padding: 0.35rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.tour-info {
  padding: 1.5rem;
}

.tour-info h3 {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.tour-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.tour-rating i {
  color: #f59e0b;
}

.days {
  margin-left: auto;
}

.tour-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price {
  font-weight: 700;
  color: #6366f1;
  font-size: 1.2rem;
}

.btn-book {
  padding: 0.5rem 1rem;
  background-color: transparent;
  color: #1f2937;
  border: 1px solid #e5e7eb;
  border-radius: 0.35rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-book:hover {
  background-color: #1f2937;
  color: white;
}

/* Popular Destinations */
.popular-destinations {
  padding: 4rem 0;
}

.destinations-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.destination-card {
  height: 250px;
  background-size: cover;
  background-position: center;
  border-radius: 0.75rem;
  overflow: hidden;
  cursor: pointer;
  position: relative;
  transition: transform 0.3s ease;
}

.destination-card:hover {
  transform: scale(1.02);
}

.destination-card.large {
  grid-row: span 2;
  height: auto;
}

.destination-overlay {
  background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.7) 100%);
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 1.5rem;
  color: white;
}

.destination-overlay h3 {
  font-size: 1.5rem;
  margin-bottom: 0.25rem;
  font-weight: 700;
}

.destination-overlay p {
  font-size: 0.9rem;
  opacity: 0.8;
  margin: 0;
}

/* Why Us Section */
.why-us {
  padding: 4rem 0;
  background-color: #f9fafb;
}

.why-us-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
}

.why-us-text h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #1f2937;
}

.why-us-text p {
  font-size: 1rem;
  color: #6b7280;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.btn-learn {
  padding: 0.75rem 2rem;
  background-color: #6366f1;
  color: white;
  border: none;
  border-radius: 0.35rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-learn:hover {
  background-color: #4f46e5;
}

.why-us-features {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.feature-item {
  text-align: center;
}

.feature-icon {
  width: 60px;
  height: 60px;
  background-color: #e0e7ff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  font-size: 1.5rem;
  color: #6366f1;
}

.feature-item h3 {
  font-size: 1rem;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.feature-item p {
  font-size: 0.9rem;
  color: #6b7280;
  line-height: 1.5;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .hero {
    padding: 4rem 1rem;
  }

  .hero-content h1 {
    font-size: 2rem;
  }

  .hero-content p {
    font-size: 1rem;
  }

  .search-box {
    flex-direction: column;
  }

  .section-header h2 {
    font-size: 1.75rem;
  }

  .destinations-grid {
    grid-template-columns: 1fr;
  }

  .destination-card.large {
    grid-row: span 1;
  }

  .why-us-content {
    grid-template-columns: 1fr;
  }

  .why-us-features {
    grid-template-columns: 1fr;
  }
}
</style>
