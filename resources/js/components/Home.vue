<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <div class="hero-content">
          <div class="hero-text">
            <h1 class="hero-title">Transform Scrap into Art</h1>
            <p class="hero-subtitle">
              Connect with artists, sell your scrap materials, or find unique refurbished pieces. Join our community today!
            </p>
            <div class="hero-buttons">
              <button @click="getStarted" class="btn btn-primary">Get Started</button>
              <button @click="learnMore" class="btn btn-outline">Learn More</button>
            </div>
          </div>
          <div class="hero-image">
            <img src="/images/main.jpg" alt="Transform Scrap into Art" class="hero-img" />
          </div>
        </div>
      </div>
    </section>

    <!-- How Refurb Works Section -->
    <section class="how-it-works">
      <div class="container">
        <h2 class="section-title">How Refurb Works</h2>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon artist-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z" fill="currentColor"/>
              </svg>
            </div>
            <h3 class="feature-title">For Artists</h3>
            <p class="feature-description">
              Find materials, collaborate on projects, and showcase your portfolio to a community that values sustainable art.
            </p>
            <a href="#" @click.prevent="learnMore" class="feature-link">Learn more →</a>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon seller-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 7.5V9M15 10.5V9L9 9.5V10.5H3V15.5H9V14.5L15 15V13.5L21 14V12L15 12.5V10.5Z" fill="currentColor"/>
              </svg>
            </div>
            <h3 class="feature-title">For Scrap Sellers</h3>
            <p class="feature-description">
              List your scrap materials, connect with artists, and contribute to sustainable creativity and environmental conservation.
            </p>
            <a href="#" @click.prevent="learnMore" class="feature-link">Learn more →</a>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon buyer-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z" fill="currentColor"/>
              </svg>
            </div>
            <h3 class="feature-title">For Buyers</h3>
            <p class="feature-description">
              Discover unique, sustainable art pieces and products. Commission custom projects and support eco-friendly creativity.
            </p>
            <a href="#" @click.prevent="learnMore" class="feature-link">Learn more →</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Projects Section -->
    <section class="featured-projects">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Featured Projects</h2>
          <a href="/projects" class="view-all-link">View all projects →</a>
        </div>
        
        <div v-if="projectsLoading" class="loading-container">
          <div class="loading-spinner"></div>
        </div>
        
        <div v-else class="projects-grid">
          <div 
            v-for="project in featuredProjects" 
            :key="project.id"
            class="project-card"
            @click="viewProject(project.id)"
          >
            <div class="project-image">
              <img :src="getProjectImage(project)" :alt="project.title" />
            </div>
            <div class="project-content">
              <div class="project-header">
                <span :class="['project-status', project.status]">{{ formatStatus(project.status) }}</span>
                <span class="project-date">{{ formatDate(project.updated_at || project.created_at) }}</span>
              </div>
              <h3 class="project-title">{{ project.title }}</h3>
              <p class="project-description">{{ project.description }}</p>
              <div class="project-footer">
                <div class="project-owner">
                  <img :src="getAvatarUrl(project.owner)" :alt="project.owner?.name" class="owner-avatar" />
                  <span class="owner-name">{{ project.owner?.name }}</span>
                </div>
                <button class="bookmark-btn">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 3H7C5.9 3 5 3.9 5 5V21L12 18L19 21V5C19 3.9 18.1 3 17 3Z" stroke="currentColor" stroke-width="2" fill="none"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Featured Products</h2>
          <a href="/scrap-items" class="view-all-link">View all products →</a>
        </div>
        
        <div v-if="productsLoading" class="loading-container">
          <div class="loading-spinner"></div>
        </div>
        
        <div v-else class="products-grid">
          <div 
            v-for="product in featuredProducts" 
            :key="product.product_id || product.id"
            class="product-card"
            @click="viewProduct(product)"
          >
            <div class="product-image">
              <img :src="getProductImage(product)" :alt="product.name" />
            </div>
            <div class="product-content">
              <h3 class="product-title">{{ product.name }}</h3>
              <p class="product-description">{{ product.description }}</p>
              <div class="product-footer">
                <span class="product-price">${{ product.price }}</span>
                <button @click.stop="addToCart(product)" class="add-to-cart-btn">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7Z" fill="currentColor"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Recommended Products Section -->
    <section class="recommended-products">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Recommended For You</h2>
          <router-link to="/scrap-items" class="view-all-link">View all products →</router-link>
        </div>
        
        <div v-if="recommendationsLoading" class="loading-container">
          <div class="loading-spinner"></div>
        </div>
        
        <div v-else-if="recommendedProducts.length > 0" class="products-grid">
          <div 
            v-for="product in recommendedProducts" 
            :key="product.id"
            class="product-card"
            @click="viewProduct(product.id)"
          >
            <div class="product-image">
              <img 
                v-if="product.images && product.images.length > 0" 
                :src="getImageUrl(product.images[0])" 
                :alt="product.name" 
              />
              <img v-else src="/images/placeholder.jpg" :alt="product.name" />
            </div>
            <div class="product-content">
              <h3 class="product-title">{{ product.name }}</h3>
              <p class="product-description">{{ product.description }}</p>
              <div class="product-footer">
                <span class="product-price">${{ parseFloat(product.price).toFixed(2) }}</span>
                <button 
                  @click.stop="addToCart(product)"
                  class="add-to-cart-btn"
                  :disabled="!isLoggedIn"
                >
                  <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="no-recommendations">
          <p>No recommendations available at the moment. Browse our <router-link to="/scrap-items">product catalog</router-link> to discover amazing items!</p>
        </div>
      </div>
    </section>

    <!-- Artist Spotlight Section -->
    <section class="artist-spotlight">
      <div class="container">
        <h2 class="section-title">Artist Spotlight</h2>
        
        <div v-if="artistsLoading" class="loading-container">
          <div class="loading-spinner"></div>
        </div>
        
        <div v-else class="artists-grid">
          <div 
            v-for="artist in featuredArtists" 
            :key="artist.id"
            class="artist-card"
          >
            <div class="artist-image">
              <img :src="getAvatarUrl(artist)" :alt="artist.name" />
            </div>
            <div class="artist-content">
              <h3 class="artist-name">{{ artist.name }}</h3>
              <p class="artist-bio">{{ artist.bio || getDefaultBio(artist) }}</p>
              <blockquote class="artist-quote">{{ artist.quote || getDefaultQuote(artist) }}</blockquote>
              <div class="artist-actions">
                <button @click="viewPortfolio(artist.id)" class="btn btn-outline">View Portfolio</button>
                <button @click="commissionWork(artist.id)" class="btn btn-primary">Commission Work</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Community Testimonials Section -->
    <section class="testimonials">
      <div class="container">
        <h2 class="section-title">What Our Community Says</h2>
        <div class="testimonials-grid">
          <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p class="testimonial-text">
              "As an artist, finding quality materials at affordable prices has always been a challenge. 
              Refurb has connected me with scrap sellers who provide exactly what I need for my metal sculptures."
            </p>
            <div class="testimonial-author">
              <img src="/images/people1.jpeg" alt="Rebecca Martinez" class="author-avatar" />
              <div class="author-info">
                <span class="author-name">Rebecca Martinez</span>
                <span class="author-role">Metal Artist</span>
              </div>
            </div>
          </div>
          
          <div class="testimonial-card">
            <div class="stars">★★★★☆</div>
            <p class="testimonial-text">
              "I used to throw away scrap materials from my construction business. Now I list them on Refurb 
              and not only make extra income but feel good knowing they're being transformed into art."
            </p>
            <div class="testimonial-author">
              <img src="/images/people2.jpg" alt="James Wilson" class="author-avatar" />
              <div class="author-info">
                <span class="author-name">James Wilson</span>
                <span class="author-role">Construction Business Owner</span>
              </div>
            </div>
          </div>
          
          <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p class="testimonial-text">
              "The custom lamp I commissioned through Refurb is now the centerpiece of my living room. 
              It's not just a light fixture—it's a conversation starter with a sustainable story behind it."
            </p>
            <div class="testimonial-author">
              <img src="/images/people3.jpg" alt="Sophia Chen" class="author-avatar" />
              <div class="author-info">
                <span class="author-name">Sophia Chen</span>
                <span class="author-role">Interior Designer</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="statistics">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number">{{ statistics.artists }}+</div>
            <div class="stat-label">Active Artists</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">{{ statistics.sellers }}+</div>
            <div class="stat-label">Scrap Sellers</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">{{ statistics.projects }}+</div>
            <div class="stat-label">Completed Projects</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">{{ statistics.products }}+</div>
            <div class="stat-label">Products Sold</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2 class="cta-title">Ready to Join Our Community?</h2>
          <p class="cta-description">
            Whether you're an artist, scrap seller, or looking for unique sustainable products, 
            Refurb has something for you.
          </p>
          <div class="cta-buttons">
            <button @click="signUp" class="btn btn-primary">Sign Up Now</button>
            <button @click="browseProjects" class="btn btn-outline">Browse Projects</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Success Notifications -->
    <div v-if="showNotification" class="notification">
      <span class="notification-icon">✅</span>
      <span>{{ notificationMessage }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      featuredProjects: [],
      featuredProducts: [],
      recommendedProducts: [],
      featuredArtists: [],
      statistics: {
        artists: 1200,
        sellers: 850,
        projects: 3500,
        products: 15000
      },
      projectsLoading: false,
      productsLoading: false,
      recommendationsLoading: false,
      artistsLoading: false,
      showNotification: false,
      notificationMessage: ''
    };
  },
  
  methods: {
    async fetchFeaturedProjects() {
      this.projectsLoading = true;
      try {
        const response = await fetch('/api/projects/completed?per_page=3');
        if (response.ok) {
          const data = await response.json();
          this.featuredProjects = data.data || data;
        }
      } catch (error) {
        console.error('Error fetching projects:', error);
      } finally {
        this.projectsLoading = false;
      }
    },
    
    async fetchFeaturedProducts() {
      this.productsLoading = true;
      try {
        const response = await fetch('/api/products/all?limit=4');
        if (response.ok) {
          const data = await response.json();
          this.featuredProducts = Array.isArray(data) ? data.slice(0, 4) : [];
        }
      } catch (error) {
        console.error('Error fetching products:', error);
      } finally {
        this.productsLoading = false;
      }
    },
    
    async fetchRecommendedProducts() {
      this.recommendationsLoading = true;
      try {
        // Try to get personalized recommendations first
        if (this.isLoggedIn) {
          const token = localStorage.getItem('access_token');
          const response = await fetch('/api/v1/recommendations', {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json'
            }
          });
          
          if (response.ok) {
            const data = await response.json();
            // The API returns either an array of recommendations or an object with recommendations field
            const recommendations = Array.isArray(data) ? data : (data.recommendations || []);
            this.recommendedProducts = recommendations.slice(0, 4);
            return;
          }
        }
        
        // Fallback to random products if no recommendations or not logged in
        const response = await fetch('/api/products/all?limit=4');
        if (response.ok) {
          const data = await response.json();
          this.recommendedProducts = Array.isArray(data) ? data.slice(0, 4) : [];
        }
      } catch (error) {
        console.error('Error fetching recommended products:', error);
        // Fallback to empty array
        this.recommendedProducts = [];
      } finally {
        this.recommendationsLoading = false;
      }
    },
    
    async fetchFeaturedArtists() {
      this.artistsLoading = true;
      try {
        const response = await fetch('/api/v1/users/artists?limit=2');
        if (response.ok) {
          const data = await response.json();
          this.featuredArtists = Array.isArray(data) ? data.slice(0, 2) : [];
        } else {
          // Fallback - get users with role 'artist'
          const fallbackResponse = await fetch('/api/v1/users?role=artist&limit=2');
          if (fallbackResponse.ok) {
            const fallbackData = await fallbackResponse.json();
            this.featuredArtists = Array.isArray(fallbackData) ? fallbackData.slice(0, 2) : [];
          }
        }
      } catch (error) {
        console.error('Error fetching artists:', error);
        // Set fallback data
        this.featuredArtists = [
          {
            id: 1,
            name: 'Sarah Johnson',
            bio: 'Metal sculptor specializing in transforming industrial scrap into elegant home decor and functional art pieces.',
            quote: 'I believe in giving discarded materials a second life. Every piece of metal has a story, and my work is about continuing that narrative in a beautiful, meaningful way.'
          },
          {
            id: 2,
            name: 'David Torres',
            bio: 'Woodworker focused on creating sustainable furniture from reclaimed materials and promoting eco-friendly design.',
            quote: 'Each piece of wood has character and history. I strive to honor that history by creating furniture that will be cherished for generations, continuing the sustainability cycle.'
          }
        ];
      } finally {
        this.artistsLoading = false;
      }
    },
    
    async fetchStatistics() {
      try {
        // Fetch real statistics from database
        const responses = await Promise.all([
          fetch('/api/v1/statistics/artists').catch(() => null),
          fetch('/api/v1/statistics/sellers').catch(() => null),
          fetch('/api/v1/statistics/projects').catch(() => null),
          fetch('/api/v1/statistics/products').catch(() => null)
        ]);
        
        // Update with real data if available, otherwise keep defaults
        responses.forEach(async (response, index) => {
          if (response && response.ok) {
            const data = await response.json();
            const keys = ['artists', 'sellers', 'projects', 'products'];
            this.statistics[keys[index]] = data.count || this.statistics[keys[index]];
          }
        });
      } catch (error) {
        console.error('Error fetching statistics:', error);
      }
    },
    
    getProjectImage(project) {
      if (project.image) return project.image;
      // Return different placeholder based on project type
      const placeholders = [
        '/images/item1.jpeg',
        '/images/item2.jpeg', 
        '/images/item3.jpg'
      ];
      return placeholders[project.id % placeholders.length];
    },
    
    getProductImage(product) {
      if (product.images && product.images.length > 0) {
        const imagePath = product.images[0];
        if (typeof imagePath === 'string') {
          return imagePath.startsWith('http') ? imagePath : `/images/${imagePath}`;
        }
        return imagePath.image_path ? `/images/${imagePath.image_path}` : '/images/placeholder.jpg';
      }
      return '/images/placeholder.jpg';
    },
    
    getImageUrl(imagePath) {
      if (typeof imagePath === 'string') {
        return imagePath.startsWith('http') ? imagePath : `/images/${imagePath}`;
      }
      return imagePath.image_path ? `/images/${imagePath.image_path}` : '/images/placeholder.jpg';
    },
    
    getAvatarUrl(user) {
      if (user && user.avatar) return user.avatar;
      const avatars = ['/images/people1.jpeg', '/images/people2.jpg', '/images/people3.jpg'];
      return avatars[(user?.id || 0) % avatars.length];
    },
    
    getDefaultBio(artist) {
      const bios = [
        'Metal sculptor specializing in transforming industrial scrap into elegant home decor and functional art pieces.',
        'Woodworker focused on creating sustainable furniture from reclaimed materials and promoting eco-friendly design.',
        'Mixed media artist who creates unique pieces by combining various recycled materials in innovative ways.'
      ];
      return bios[artist.id % bios.length];
    },
    
    getDefaultQuote(artist) {
      const quotes = [
        'I believe in giving discarded materials a second life. Every piece of metal has a story, and my work is about continuing that narrative in a beautiful, meaningful way.',
        'Each piece of wood has character and history. I strive to honor that history by creating furniture that will be cherished for generations.',
        'Art has the power to transform not just materials, but perspectives. Through my work, I hope to inspire others to see beauty in the unexpected.'
      ];
      return quotes[artist.id % quotes.length];
    },
    
    formatStatus(status) {
      const statusMap = {
        'active': 'Collaborative',
        'completed': 'Completed',
        'in_progress': 'In Progress'
      };
      return statusMap[status] || status;
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
    },
    
    viewProject(projectId) {
      this.$router.push(`/projects/${projectId}`);
    },
    
    viewProduct(product) {
      const productId = product.product_id || product.id;
      this.$router.push({ name: 'product-details', params: { id: productId } });
    },
    
    viewPortfolio(artistId) {
      this.$router.push(`/portfolio/${artistId}`);
    },
    
    commissionWork(artistId) {
      this.$router.push('/custom-request');
    },
    
    async addToCart(product) {
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.$router.push('/login');
        return;
      }

      const productId = product.product_id || product.id;
      
      try {
        const response = await fetch(`/api/cart/add/${productId}`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ quantity: 1 })
        });
        
        if (response.ok) {
          this.showNotificationMessage(`Added ${product.name} to cart!`);
        }
      } catch (err) {
        console.error("Error adding to cart:", err);
      }
    },
    
    showNotificationMessage(message) {
      this.notificationMessage = message;
      this.showNotification = true;
      setTimeout(() => {
        this.showNotification = false;
      }, 3000);
    },
    
    getStarted() {
      this.$router.push('/signup');
    },
    
    learnMore() {
      this.$router.push('/about');
    },
    
    signUp() {
      this.$router.push('/signup');
    },
    
    browseProjects() {
      this.$router.push('/projects');
    }
  },
  
  computed: {
    isLoggedIn() {
      return !!localStorage.getItem('access_token');
    }
  },
  
  mounted() {
    this.fetchFeaturedProjects();
    this.fetchFeaturedProducts();
    this.fetchRecommendedProducts();
    this.fetchFeaturedArtists();
    this.fetchStatistics();
  }
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.home-page {
  min-height: 100vh;
  background-color: #f8fafc;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Hero Section */
.hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 80px 0;
  color: white;
}

.hero-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
  min-height: 500px;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 20px;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 1.25rem;
  margin-bottom: 40px;
  opacity: 0.9;
  line-height: 1.6;
}

.hero-buttons {
  display: flex;
  gap: 20px;
}

.hero-image {
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero-img {
  width: 100%;
  max-width: 500px;
  height: auto;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

/* Buttons */
.btn {
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  font-size: 1rem;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background-color: #6366f1;
  color: white;
}

.btn-primary:hover {
  background-color: #5855eb;
  transform: translateY(-2px);
}

.btn-outline {
  background-color: transparent;
  color: white;
  border: 2px solid white;
}

.btn-outline:hover {
  background-color: white;
  color: #6366f1;
}

/* How It Works Section */
.how-it-works {
  padding: 80px 0;
  background-color: #f8fafc;
}

.section-title {
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 30px;
  color: #1f2937;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 40px;
}

.feature-card {
  background: white;
  padding: 40px 30px;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  color: white;
}

.artist-icon { background-color: #8b5cf6; }
.seller-icon { background-color: #06b6d4; }
.buyer-icon { background-color: #10b981; }

.feature-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 15px;
  color: #1f2937;
}

.feature-description {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 20px;
}

.feature-link {
  color: #6366f1;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.feature-link:hover {
  color: #5855eb;
}

/* Section Headers */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.view-all-link {
  color: #6366f1;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.view-all-link:hover {
  color: #5855eb;
}

/* Featured Projects Section */
.featured-projects {
  padding: 50px 0;
  background-color: white;
}

.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.project-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.project-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.project-image {
  width: 100%;
  height: 160px;
  overflow: hidden;
}

.project-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.project-content {
  padding: 16px;
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.project-status {
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.project-status.active { background-color: #dbeafe; color: #1d4ed8; }
.project-status.completed { background-color: #d1fae5; color: #065f46; }
.project-status.in_progress { background-color: #fef3c7; color: #92400e; }

.project-date {
  color: #6b7280;
  font-size: 0.75rem;
}

.project-title {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 6px;
  color: #1f2937;
}

.project-description {
  color: #6b7280;
  line-height: 1.4;
  margin-bottom: 12px;
  font-size: 0.875rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.project-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.project-owner {
  display: flex;
  align-items: center;
  gap: 10px;
}

.owner-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.owner-name {
  font-weight: 500;
  color: #374151;
}

.bookmark-btn {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.bookmark-btn:hover {
  background-color: #f3f4f6;
  color: #6366f1;
}

/* Featured Products Section */
.featured-products {
  padding: 50px 0;
  background-color: #f8fafc;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.product-image {
  width: 100%;
  height: 160px;
  overflow: hidden;
  background-color: #f3f4f6;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-content {
  padding: 16px;
}

.product-title {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 6px;
  color: #1f2937;
}

.product-description {
  color: #6b7280;
  font-size: 0.8rem;
  line-height: 1.3;
  margin-bottom: 12px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
}

.product-price {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1f2937;
}

.add-to-cart-btn {
  background-color: #6366f1;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  font-size: 0.875rem;
}

.add-to-cart-btn:hover {
  background-color: #5855eb;
}

.add-to-cart-btn:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

/* Recommended Products Section */
.recommended-products {
  padding: 50px 0;
  background-color: white;
}

.no-recommendations {
  text-align: center;
  padding: 40px 20px;
  color: #6b7280;
  font-size: 1rem;
}

.no-recommendations a {
  color: #6366f1;
  text-decoration: none;
  font-weight: 600;
}

.no-recommendations a:hover {
  color: #5855eb;
  text-decoration: underline;
}

/* Artist Spotlight Section */
.artist-spotlight {
  padding: 80px 0;
  background-color: white;
}

.artists-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 60px;
}

.artist-card {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 30px;
  align-items: start;
}

.artist-image {
  width: 200px;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
}

.artist-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.artist-name {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 15px;
  color: #1f2937;
}

.artist-bio {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 20px;
}

.artist-quote {
  font-style: italic;
  color: #374151;
  line-height: 1.6;
  margin-bottom: 25px;
  padding-left: 20px;
  border-left: 3px solid #6366f1;
}

.artist-actions {
  display: flex;
  gap: 15px;
}

/* Testimonials Section */
.testimonials {
  padding: 80px 0;
  background-color: #f8fafc;
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 30px;
}

.testimonial-card {
  background: white;
  padding: 30px;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.stars {
  color: #fbbf24;
  font-size: 1.25rem;
  margin-bottom: 15px;
}

.testimonial-text {
  color: #374151;
  line-height: 1.6;
  margin-bottom: 20px;
  font-style: italic;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 15px;
}

.author-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.author-name {
  font-weight: 600;
  color: #1f2937;
  display: block;
}

.author-role {
  color: #6b7280;
  font-size: 0.875rem;
}

/* Statistics Section */
.statistics {
  padding: 60px 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 40px;
  text-align: center;
}

.stat-number {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 10px;
}

.stat-label {
  font-size: 1.125rem;
  opacity: 0.9;
}

/* CTA Section */
.cta-section {
  padding: 80px 0;
  background-color: white;
}

.cta-content {
  text-align: center;
  max-width: 600px;
  margin: 0 auto;
}

.cta-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: #1f2937;
}

.cta-description {
  font-size: 1.125rem;
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 40px;
}

.cta-buttons {
  display: flex;
  gap: 20px;
  justify-content: center;
}

/* Loading and Notifications */
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #6366f1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background-color: #10b981;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: 10px;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-content {
    grid-template-columns: 1fr;
    text-align: center;
    gap: 40px;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .features-grid,
  .projects-grid,
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .artists-grid {
    grid-template-columns: 1fr;
  }
  
  .artist-card {
    grid-template-columns: 1fr;
    text-align: center;
  }
  
  .artist-image {
    margin: 0 auto;
  }
  
  .section-header {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .cta-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .hero-buttons {
    flex-direction: column;
    align-items: center;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 15px;
  }
  
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .cta-title {
    font-size: 2rem;
  }
  
  .stat-number {
    font-size: 2rem;
  }
}
</style>
