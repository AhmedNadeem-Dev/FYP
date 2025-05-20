<template>
  <div class="home-page">
    <!-- Hero Section with Image -->
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">Welcome to Refurb</h1>
        <p class="hero-subtitle">
          Your go-to marketplace for buying and refurbishing scrap items.
        </p>
        <div class="hero-buttons">
          <button @click="exploreItems" class="hero-button">Explore Items</button>
          <button 
            @click="viewPortfolioOrProjects" 
            class="hero-button portfolio-button"
          >
            {{ isArtist ? 'View Portfolio' : 'View Projects' }}
          </button>
        </div>
      </div>
      <div class="hero-image-wrapper">
        <img src="images/main.jpg" alt="Refurb Marketplace" class="hero-image" />
      </div>
    </section>

    <!-- Recommended Products Section -->
    <section class="recommended-products" v-if="recommendedProducts.length > 0">
      <h2 class="section-title">Recommended For You</h2>
      
      <!-- Personalization Notice for Guest Users -->
      <div class="personalization-notice" v-if="!isLoggedIn">
        <p>
          <i class="info-icon">i</i>
          <span>Log in for personalized recommendations based on your interests!</span>
          <router-link to="/login" class="login-link">Log In</router-link>
        </p>
      </div>
      
      <!-- Loading State -->
      <div class="loading-container" v-if="loading">
        <div class="loading-spinner"></div>
      </div>
      
      <!-- Products Grid -->
      <div class="products-grid" v-else>
        <div 
          v-for="product in recommendedProducts" 
          :key="product.id" 
          class="product-card" 
          @click="viewProductDetails(product)"
        >
          <!-- Product Image -->
          <div class="image-container">
            <img 
              :src="product.images && product.images.length ? product.images[0] : 'images/placeholder.jpg'" 
              alt="Product Image" 
              class="product-image"
            />
            <div class="card-actions">
              <button 
                class="wishlist-btn" 
                :class="{ active: isInWishlist(product.id) }"
                @click.stop="toggleWishlist(product)"
              >
                ♥
              </button>
              <button class="cart-btn" @click.stop="addToCart(product)">
                +
              </button>
            </div>
          </div>

          <!-- Product Info -->
          <div class="product-info">
            <h3>{{ product.name }}</h3>
            <div class="product-meta">
              <span class="category">{{ product.category || 'Uncategorized' }}</span>
              <span class="price">{{ product.price }} PKR</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- View All Button -->
      <div class="view-all-container">
        <button @click="exploreItems" class="view-all-btn">View All Products</button>
      </div>
    </section>
    
    <!-- Login Modal -->
    <div v-if="loginModalVisible" class="modal-overlay">
      <div class="modal-content">
        <h2>Please Log In</h2>
        <p>You need to be logged in to perform this action.</p>
        <div class="modal-actions">
          <router-link to="/login" class="primary-button">Log In</router-link>
          <button @click="closeLoginModal" class="secondary-button">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showCartNotification" class="toast-notification" :class="{ 'show': showCartNotification }">
      <div class="toast-content">✓ {{ cartNotificationMessage }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      recommendedProducts: [],
      loading: false,
      wishlistItems: [],
      loginModalVisible: false,
      showCartNotification: false,
      cartNotificationMessage: "",
    };
  },
  computed: {
    isLoggedIn() {
      // Check if user is logged in using the same logic from App.vue
      return localStorage.getItem("userSession") !== null;
    },
    isArtist() {
      // Check if the logged-in user is an artist
      if (!this.isLoggedIn) return false;
      
      try {
        const userData = JSON.parse(localStorage.getItem("userSession"));
        return userData && userData.role === "artist";
      } catch (e) {
        console.error("Error parsing user session:", e);
        return false;
      }
    }
  },
  methods: {
    exploreItems() {
      this.$router.push('/scrap-items');
    },
    viewPortfolioOrProjects() {
      if (this.isArtist) {
        // If user is an artist, go to their portfolio
        this.$router.push('/portfolio');
      } else {
        // Otherwise, go to public projects
        this.$router.push('/projects/completed'); 
      }
    },
    async fetchRecommendedProducts() {
      this.loading = true;
      try {
        const response = await fetch('http://127.0.0.1:8000/api/recommendations?limit=8');
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        this.recommendedProducts = data;
      } catch (error) {
        console.error('Error fetching recommended products:', error);
      } finally {
        this.loading = false;
      }
    },
    viewProductDetails(product) {
      this.$router.push({ name: 'product-details', params: { id: product.id } });
    },
    // Fetch wishlist items to highlight products that are in the user's wishlist
    async fetchWishlist() {
      const token = localStorage.getItem("access_token");
      if (!token) return;
      
      try {
        const response = await fetch('http://127.0.0.1:8000/api/wishlist', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          this.wishlistItems = data.map(item => item.id);
        }
      } catch (err) {
        console.error("Error fetching wishlist:", err);
      }
    },
    // Check if product is in wishlist
    isInWishlist(productId) {
      return this.wishlistItems.includes(productId);
    },
    // Toggle wishlist status for a product
    async toggleWishlist(product) {
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.loginModalVisible = true;
        return;
      }
      
      try {
        if (this.isInWishlist(product.id)) {
          // Remove from wishlist
          const response = await fetch(`http://127.0.0.1:8000/api/wishlist/remove/${product.id}`, {
            method: 'DELETE',
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          
          if (response.ok) {
            this.wishlistItems = this.wishlistItems.filter(id => id !== product.id);
          }
        } else {
          // Add to wishlist
          const response = await fetch(`http://127.0.0.1:8000/api/wishlist/add/${product.id}`, {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          
          if (response.ok) {
            this.wishlistItems.push(product.id);
          }
        }
      } catch (err) {
        console.error("Error updating wishlist:", err);
      }
    },
    // Add product to cart
    async addToCart(product) {
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.loginModalVisible = true;
        return;
      }
      
      try {
        const response = await fetch(`http://127.0.0.1:8000/api/cart/add/${product.id}`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            quantity: 1
          })
        });
        
        if (response.ok) {
          this.cartNotificationMessage = `Added to cart: ${product.name}`;
          this.showCartNotification = true;
          
          setTimeout(() => {
            this.showCartNotification = false;
          }, 3000);
        } else {
          throw new Error('Failed to add to cart');
        }
      } catch (err) {
        console.error("Error adding to cart:", err);
      }
    },
    // Close the login modal
    closeLoginModal() {
      this.loginModalVisible = false;
    }
  },
  mounted() {
    this.fetchRecommendedProducts();
    this.fetchWishlist();
  }
};
</script>

<style scoped>
.home-page {
  color: #3C552D;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Hero Section */
.hero {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  padding: 4rem 2rem;
  background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
  border-radius: 12px;
  margin-top: 2rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.hero-content {
  max-width: 50%;
}

.hero-title {
  font-size: 3rem;
  font-weight: 700;
  color: #3B1E54;
  margin-bottom: 1rem;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: #3B1E54;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
}

.hero-button {
  background-color: #D4BEE4;
  color: #3B1E54;
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 30px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: background-color 0.3s ease, transform 0.3s ease;
}

.portfolio-button {
  background-color: #3B1E54;
  color: white;
}

.hero-button:hover {
  background-color: #EEEEEE;
  transform: translateY(-2px);
}

.portfolio-button:hover {
  background-color: #5E3285;
  color: white;
}

.hero-image-wrapper {
  max-width: 45%;
  position: relative;
}

.hero-image {
  width: 100%;
  height: auto;
  border-radius: 12px;
  object-fit: cover;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Recommended Products Section */
.recommended-products {
  margin-top: 4rem;
  padding: 0 1rem;
}

.section-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #3B1E54;
  margin-bottom: 1.5rem;
  text-align: center;
}

.loading-container {
  display: flex;
  justify-content: center;
  padding: 2rem;
}

.loading-spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 4px solid #3B1E54;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.product-card {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.image-container {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.card-actions {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  opacity: 0;
  transform: translateX(10px);
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.product-card:hover .card-actions {
  opacity: 1;
  transform: translateX(0);
}

.wishlist-btn, .cart-btn {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1.2rem;
  background-color: white;
  color: #3B1E54;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: background-color 0.3s, transform 0.3s;
}

.wishlist-btn:hover, .cart-btn:hover {
  transform: scale(1.1);
}

.wishlist-btn.active {
  background-color: #ff6b6b;
  color: white;
}

.product-info {
  padding: 1rem;
}

.product-info h3 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #333;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.5rem;
}

.category {
  font-size: 0.8rem;
  color: #666;
  background-color: #f0f0f0;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
}

.price {
  font-weight: 600;
  color: #3B1E54;
}

.view-all-container {
  display: flex;
  justify-content: center;
  margin-top: 1.5rem;
  margin-bottom: 3rem;
}

.view-all-btn {
  background-color: transparent;
  color: #3B1E54;
  border: 2px solid #3B1E54;
  padding: 0.5rem 1.5rem;
  border-radius: 30px;
  font-weight: 500;
  transition: background-color 0.3s, color 0.3s;
  cursor: pointer;
}

.view-all-btn:hover {
  background-color: #3B1E54;
  color: white;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  text-align: center;
}

.modal-content h2 {
  font-size: 1.5rem;
  color: #3B1E54;
  margin-bottom: 1rem;
}

.modal-content p {
  margin-bottom: 1.5rem;
  color: #666;
}

.modal-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.primary-button, .secondary-button {
  padding: 0.6rem 1.5rem;
  border-radius: 30px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.3s;
  text-decoration: none;
}

.primary-button {
  background-color: #3B1E54;
  color: white;
  border: none;
}

.primary-button:hover {
  background-color: #5E3285;
  transform: translateY(-2px);
}

.secondary-button {
  background-color: transparent;
  color: #3B1E54;
  border: 1px solid #3B1E54;
}

.secondary-button:hover {
  background-color: #f0f0f0;
  transform: translateY(-2px);
}

/* Toast Notification */
.toast-notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #4CAF50;
  color: white;
  padding: 12px 20px;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  transform: translateY(100px);
  opacity: 0;
  transition: transform 0.3s, opacity 0.3s;
  z-index: 1000;
}

.toast-notification.show {
  transform: translateY(0);
  opacity: 1;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .hero {
    flex-direction: column;
    align-items: center;
  }

  .hero-content {
    max-width: 100%;
    text-align: center;
  }
  
  .hero-buttons {
    justify-content: center;
  }

  .hero-image-wrapper {
    max-width: 100%;
    margin-top: 2rem;
  }

  .hero-image {
    height: auto;
    width: 100%;
  }
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }

  .hero-subtitle {
    font-size: 1.1rem;
  }

  .hero-button {
    padding: 0.6rem 1.2rem;
    font-size: 0.95rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }
}

@media (max-width: 480px) {
  .hero {
    padding: 2rem 1rem;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-subtitle {
    font-size: 1rem;
  }
  
  .hero-buttons {
    flex-direction: column;
  }

  .hero-button {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
}

/* Personalization Notice */
.personalization-notice {
  background-color: #f8f1ff;
  border: 1px solid #e2d1f9;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.personalization-notice p {
  display: flex;
  align-items: center;
  margin: 0;
  font-size: 0.9rem;
  color: #3B1E54;
}

.info-icon {
  background-color: #3B1E54;
  color: white;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-style: normal;
  font-weight: bold;
  font-size: 12px;
  margin-right: 8px;
}

.login-link {
  color: #3B1E54;
  font-weight: 600;
  text-decoration: none;
  margin-left: 8px;
  padding: 0.25rem 0.75rem;
  border-radius: 30px;
  background-color: #D4BEE4;
  transition: background-color 0.3s, transform 0.3s;
}

.login-link:hover {
  background-color: #3B1E54;
  color: white;
  transform: translateY(-2px);
}
</style>