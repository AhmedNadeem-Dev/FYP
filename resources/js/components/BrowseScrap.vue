<template>
  <div class="browse-page">
    <!-- New Products Slider -->
    <div class="new-products-section">
      <div class="container">
        <div class="section-header">
          <h2>New Arrivals</h2>
          <div class="slider-controls">
            <button @click="prevSlide" :disabled="currentSlide === 0" class="slider-btn">‹</button>
            <button @click="nextSlide" :disabled="currentSlide >= maxSlide" class="slider-btn">›</button>
          </div>
        </div>
        
        <div class="slider-container">
          <div class="slider-track" :style="{ transform: `translateX(-${currentSlide * 25}%)` }">
            <div v-for="product in newProducts" :key="product.id" class="slider-card" @click="viewProductDetails(product)">
              <div class="card-image">
                <img :src="getImageUrl(product)" :alt="product.name" @error="handleImageError" />
                <div class="new-badge">New</div>
              </div>
              <div class="card-content">
                <h4>{{ product.name }}</h4>
                <p class="category">{{ product.category }}</p>
                <div class="price">${{ product.price }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Browse Section -->
    <div class="browse-section">
      <div class="container">
        <!-- Search & Filter Bar -->
        <div class="filter-bar">
          <div class="search-group">
            <input 
              v-model="searchQuery" 
              @input="filterProducts"
              type="text" 
              placeholder="Search products..." 
              class="search-input"
            />
          </div>
          
          <div class="filter-group">
            <select v-model="selectedCategory" @change="filterProducts" class="category-select">
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>
          
          <div class="results-info">
            {{ filteredProducts.length }} products found
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading products...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          <p>{{ error }}</p>
          <button @click="fetchProducts" class="retry-btn">Try Again</button>
        </div>

        <!-- Products Grid -->
        <div v-else-if="paginatedProducts.length > 0" class="products-grid">
          <div 
            v-for="product in paginatedProducts" 
            :key="product.id"
            class="product-card"
            @click="viewProductDetails(product)"
          >
            <div class="product-image">
              <img :src="getImageUrl(product)" :alt="product.name" @error="handleImageError" />
              <div class="product-actions">
                <button 
                  @click.stop="toggleWishlist(product)"
                  class="action-btn"
                  :class="{ active: isInWishlist(product.id) }"
                >
                  ♡
                </button>
                <button @click.stop="addToCart(product)" class="action-btn cart">
                  +
                </button>
              </div>
            </div>
            
            <div class="product-info">
              <h3>{{ product.name }}</h3>
              <p class="category">{{ product.category || 'Uncategorized' }}</p>
              <div class="price">${{ product.price }}</div>
            </div>
          </div>
        </div>

        <!-- No Products -->
        <div v-else class="no-products">
          <h3>No products found</h3>
          <p>Try adjusting your search or filters</p>
          <button @click="resetFilters" class="clear-btn">Clear Filters</button>
        </div>

        <!-- Simple Pagination -->
        <div v-if="totalPages > 1" class="pagination">
          <button @click="prevPage" :disabled="currentPage === 1" class="page-btn">Previous</button>
          <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage === totalPages" class="page-btn">Next</button>
        </div>
      </div>
    </div>

    <!-- Simple Login Modal -->
    <div v-if="loginModalVisible" class="modal-overlay" @click="closeLoginModal">
      <div class="modal" @click.stop>
        <h3>Login Required</h3>
        <p>Please log in to add items to your cart or wishlist.</p>
        <div class="modal-buttons">
          <router-link to="/login" class="btn primary">Login</router-link>
          <button @click="closeLoginModal" class="btn secondary">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="showCartNotification" class="toast" :class="{ show: showCartNotification }">
      ✓ {{ cartNotificationMessage }}
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      products: [],
      filteredProducts: [],
      newProducts: [],
      loading: false,
      error: null,
      wishlistItems: [],
      loginModalVisible: false,
      showCartNotification: false,
      cartNotificationMessage: "",
      
      // Search and filter
      searchQuery: "",
      selectedCategory: "",
      categories: [],
      
      // Pagination
      currentPage: 1,
      productsPerPage: 12,
      
      // Slider
      currentSlide: 0
    };
  },
  computed: {
    hasActiveFilters() {
      return this.searchQuery || this.selectedCategory;
    },
    totalPages() {
      return Math.ceil(this.filteredProducts.length / this.productsPerPage);
    },
    paginatedProducts() {
      const startIndex = (this.currentPage - 1) * this.productsPerPage;
      const endIndex = startIndex + this.productsPerPage;
      return this.filteredProducts.slice(startIndex, endIndex);
    },
    visiblePages() {
      const pages = [];
      for (let i = 1; i <= this.totalPages; i++) {
        pages.push(i);
      }
      return pages;
    },
    maxSlide() {
      return Math.max(0, this.newProducts.length - 4);
    }
  },

  methods: {
    // Recommendation navigation methods
    nextRecommendation() {
      if (this.currentRecommendationIndex < this.recommendations.length - this.visibleRecommendations) {
        this.currentRecommendationIndex++;
      }
    },
    
    prevRecommendation() {
      if (this.currentRecommendationIndex > 0) {
        this.currentRecommendationIndex--;
      }
    },

    async fetchRecommendations() {
      const token = localStorage.getItem("access_token");
      
      if (!token) {
        // No token found, showing fallback recommendations
        this.generateFallbackRecommendations();
        return;
      }
      
      this.recommendationsLoading = true;
      
      try {
        // Fetching personalized recommendations
        
        const response = await fetch('http://127.0.0.1:8000/api/v1/recommendations', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });
        
                  // Check response status
        
        if (response.ok) {
          const data = await response.json();
                      // Recommendations data received
          
          // Check if it's the "no recommendations yet" message
          if (data.message && data.recommendations) {
            this.recommendationTitle = "Popular Products";
            this.recommendations = data.recommendations;
            // Using popular products as recommendations
          } else if (Array.isArray(data)) {
            // We have real recommendations
            this.recommendations = data;
            this.recommendationTitle = "Recommended for You";
            // Using personalized recommendations
          } else {
            // Unexpected data format, using fallback
            this.generateFallbackRecommendations();
          }
          
          // Set recommendation reasons from backend
          this.recommendations.forEach(product => {
            if (product.recommendation_reason) {
              this.recommendationReasons[product.product_id || product.id] = product.recommendation_reason;
            }
          });
          
          // Final recommendations processed
          
        } else {
          const errorText = await response.text();
          console.error("HTTP Error:", response.status, errorText);
          
          if (response.status === 401) {
            // Unauthorized - removing token
            localStorage.removeItem("access_token");
            this.generateFallbackRecommendations();
          } else if (response.status === 404) {
            console.error("Recommendations endpoint not found - check your routes");
            this.generateFallbackRecommendations();
          } else {
            console.error("Failed to fetch recommendations - HTTP", response.status);
            this.generateFallbackRecommendations();
          }
        }
      } catch (err) {
        console.error("Network error fetching recommendations:", err);
        console.error("Error details:", err.message);
        this.generateFallbackRecommendations();
      } finally {
        this.recommendationsLoading = false;
      }
    },

    // Updated generateFallbackRecommendations with better logging
    generateFallbackRecommendations() {
      if (!this.products || this.products.length === 0) {
        // No products available for fallback recommendations
        return;
      }

      // Generating fallback recommendations from available products
      
      // Generate random recommendations when no user data is available
      const shuffled = [...this.products].sort(() => 0.5 - Math.random());
      this.recommendations = shuffled.slice(0, 8);
      
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.recommendationTitle = "Featured Products";
      } else {
        this.recommendationTitle = "Popular Products";
      }
      
      // Set default reasons for fallback recommendations
      this.recommendations.forEach(product => {
        this.recommendationReasons[product.product_id || product.id] = "Featured";
      });
      
      // Fallback recommendations set
    },

    getRecommendationReason(product) {
      const productId = product.product_id || product.id;
      return this.recommendationReasons[productId] || "Recommended";
    },

    // Updated to handle both product_id and id fields
    viewProductDetails(product) {
      const productId = product.product_id || product.id;
      this.$router.push({ name: 'product-details', params: { id: productId } });
    },

    // FIXED: Single addToCart method that handles both product_id and id fields
    async addToCart(product) {
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.loginModalVisible = true;
        return;
      }

      const productId = product.product_id || product.id;
      
      try {
        const response = await fetch(`http://127.0.0.1:8000/api/v1/cart/add/${productId}`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            quantity: 1
          })
        });
        
        const data = await response.json();
        
        if (response.ok) {
          this.fetchCartCount();
          this.cartNotificationMessage = `Added to cart: ${product.name}`;
          this.showCartNotification = true;
          
          setTimeout(() => {
            this.showCartNotification = false;
          }, 3000);
        } else {
          console.error('Cart error:', data);
          this.error = data.error || data.message || 'Failed to add to cart';
          
          if (response.status === 401) {
            this.loginModalVisible = true;
          }
        }
      } catch (err) {
        console.error("Error adding to cart:", err);
        this.error = "Network error. Please check your connection and try again.";
      }
    },
    
    async fetchCartCount() {
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.cartCount = 0;
        return;
      }
      
      try {
        const response = await fetch('http://127.0.0.1:8000/api/v1/cart', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          if (Array.isArray(data)) {
            this.cartCount = data.reduce((total, item) => total + (item.quantity || 0), 0);
          } else if (data.items && Array.isArray(data.items)) {
            this.cartCount = data.items.reduce((total, item) => total + (item.quantity || 0), 0);
          } else {
            this.cartCount = 0;
          }
        } else if (response.status === 401) {
          this.cartCount = 0;
          localStorage.removeItem("access_token");
        } else {
          console.error("Failed to fetch cart count");
          this.cartCount = 0;
        }
      } catch (err) {
        console.error("Error fetching cart:", err);
        this.cartCount = 0;
      }
    },

    // Updated to handle both product_id and id fields
    async toggleWishlist(product) {
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.loginModalVisible = true;
        return;
      }

      const productId = product.product_id || product.id;
      
      try {
        if (this.isInWishlist(productId)) {
          const response = await fetch(`http://127.0.0.1:8000/api/wishlist/remove/${productId}`, {
            method: 'DELETE',
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          });
          
          if (response.ok) {
            this.wishlistItems = this.wishlistItems.filter(id => id !== productId);
          } else if (response.status === 401) {
            this.loginModalVisible = true;
          }
        } else {
          const response = await fetch(`http://127.0.0.1:8000/api/wishlist/add/${productId}`, {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          });
          
          if (response.ok) {
            this.wishlistItems.push(productId);
          } else if (response.status === 401) {
            this.loginModalVisible = true;
          }
        }
      } catch (err) {
        console.error("Error updating wishlist:", err);
      }
    },

    // Debug method to test recommendations
    async testRecommendations() {
      const token = localStorage.getItem("access_token");
      if (!token) {
        console.log("No token for testing");
        return;
      }

      try {
        // Test the debug endpoint
        const response = await fetch('http://127.0.0.1:8000/api/recommendations/debug', {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        if (response.ok) {
          const data = await response.json();
          console.log("Debug data:", data);
        } else {
          console.error("Debug endpoint failed:", response.status);
        }
      } catch (err) {
        console.error("Error testing recommendations:", err);
      }
    },

    // Pagination methods
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    
    async fetchProducts() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('Fetching products...');
        const response = await fetch('http://127.0.0.1:8000/api/products/all');
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Products loaded:', data.length);
        
        this.products = data;
        this.filteredProducts = [...data];
        this.extractCategories();
        this.newProducts = data.slice(0, 8);
        
        // Load recommendations after products
        this.fetchRecommendations();
        
      } catch (error) {
        console.error('Fetch error:', error);
        this.error = 'Unable to load products. Please refresh the page.';
      } finally {
        this.loading = false;
      }
    },
    
    extractCategories() {
      const categorySet = new Set();
      this.products.forEach(product => {
        if (product.category) {
          categorySet.add(product.category);
        }
      });
      this.categories = Array.from(categorySet).sort();
    },
    
    filterProducts() {
      this.filteredProducts = this.products.filter(product => {
        const matchesSearch = !this.searchQuery || 
          product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (product.description && product.description.toLowerCase().includes(this.searchQuery.toLowerCase()));
        
        const matchesCategory = !this.selectedCategory || 
          product.category === this.selectedCategory;
        
        const matchesPrice = !this.minPrice || product.price >= this.minPrice;
        const matchesMaxPrice = !this.maxPrice || product.price <= this.maxPrice;
        
        return matchesSearch && matchesCategory && matchesPrice && matchesMaxPrice;
      });
      
      this.currentPage = 1;
    },
    
    resetFilters() {
      this.searchQuery = "";
      this.selectedCategory = "";
      this.minPrice = null;
      this.maxPrice = null;
      this.filterProducts();
    },
    
    clearSearch() {
      this.searchQuery = "";
      this.filterProducts();
    },
    
    clearCategory() {
      this.selectedCategory = "";
      this.filterProducts();
    },
    
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
    
    isInWishlist(productId) {
      return this.wishlistItems.includes(productId);
    },
    
    closeLoginModal() {
      this.loginModalVisible = false;
    },

    handleImageError(event) {
      event.target.src = '/images/placeholder.jpg';
    },
    
    getImageUrl(product) {
      if (product.images && product.images.length > 0) {
        const imagePath = product.images[0];
        // Handle different image path formats
        if (imagePath.startsWith('http')) {
          return imagePath;
        } else if (imagePath.startsWith('/')) {
          return imagePath;
        } else {
          return `/images/${imagePath}`;
        }
      }
      // Use a default placeholder image
      return '/images/main.jpg';
    },

    async testAPI() {
      try {
        console.log('Testing API endpoint...');
        const response = await fetch('http://127.0.0.1:8000/api/products/all');
        console.log('Test response status:', response.status);
        console.log('Test response headers:', response.headers);
        const text = await response.text();
        console.log('Test response text:', text);
        
        if (text) {
          try {
            const json = JSON.parse(text);
            console.log('Test parsed JSON:', json);
          } catch (e) {
            console.error('Failed to parse JSON:', e);
          }
        }
      } catch (err) {
        console.error('Test API error:', err);
      }
    },

    selectCategory(category) {
      this.selectedCategory = category;
      this.filterProducts();
    },

    getCategoryCount(category) {
      return this.products.filter(product => product.category === category).length;
    },

    goToPage(page) {
      this.currentPage = page;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    // Slider methods
    nextSlide() {
      if (this.currentSlide < this.maxSlide) {
        this.currentSlide++;
      }
    },
    
    prevSlide() {
      if (this.currentSlide > 0) {
        this.currentSlide--;
      }
    }
  },
  mounted() {
    // this.testAPI();
    this.fetchProducts();
    this.fetchWishlist();
    this.fetchCartCount();
    
    // Uncomment this line to test the debug endpoint
    // this.testRecommendations();
  },
};
</script>
<style scoped>
/* Clean & Simple Browse Page */
.browse-page {
  background: #f8f9fa;
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* New Products Slider */
.new-products-section {
  background: white;
  padding: 2rem 0;
  border-bottom: 1px solid #e9ecef;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.slider-controls {
  display: flex;
  gap: 0.5rem;
}

.slider-btn {
  width: 40px;
  height: 40px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.slider-btn:hover:not(:disabled) {
  background: #6c63ff;
  color: white;
  border-color: #6c63ff;
}

.slider-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.slider-container {
  overflow: hidden;
  border-radius: 12px;
}

.slider-track {
  display: flex;
  transition: transform 0.3s ease;
  gap: 1rem;
}

.slider-card {
  flex: 0 0 calc(25% - 0.75rem);
  background: white;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s ease;
  border: 1px solid #e9ecef;
}

.slider-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.card-image {
  position: relative;
  height: 160px;
  overflow: hidden;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.new-badge {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: #ff6b6b;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.card-content {
  padding: 1rem;
}

.card-content h4 {
  margin: 0 0 0.5rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: #2c3e50;
}

.card-content .category {
  margin: 0 0 0.5rem;
  font-size: 0.8rem;
  color: #6c757d;
}

.card-content .price {
  font-weight: 700;
  color: #6c63ff;
  font-size: 1rem;
}

/* Browse Section */
.browse-section {
  padding: 2rem 0;
}

/* Filter Bar */
.filter-bar {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.search-group {
  flex: 1;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
}

.search-input:focus {
  outline: none;
  border-color: #6c63ff;
  box-shadow: 0 0 0 2px rgba(108, 99, 255, 0.1);
}

.category-select {
  padding: 0.75rem 1rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: white;
  font-size: 1rem;
  min-width: 150px;
}

.results-info {
  color: #6c757d;
  font-weight: 500;
  white-space: nowrap;
}

/* Loading & Error States */
.loading-state, .error-state {
  text-align: center;
  padding: 3rem 1rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #6c63ff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.retry-btn {
  padding: 0.75rem 1.5rem;
  background: #6c63ff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 1rem;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.product-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid #e9ecef;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.product-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-actions {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.product-card:hover .product-actions {
  opacity: 1;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: white;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  transition: all 0.2s ease;
}

.action-btn:hover {
  transform: scale(1.1);
}

.action-btn.active {
  background: #ff6b6b;
  color: white;
}

.action-btn.cart {
  background: #6c63ff;
  color: white;
}

.product-info {
  padding: 1.25rem;
}

.product-info h3 {
  margin: 0 0 0.5rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
}

.product-info .category {
  margin: 0 0 0.75rem;
  color: #6c757d;
  font-size: 0.9rem;
}

.product-info .price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #6c63ff;
}

/* No Products */
.no-products {
  text-align: center;
  padding: 3rem 1rem;
}

.no-products h3 {
  margin: 0 0 0.5rem;
  color: #2c3e50;
}

.no-products p {
  margin: 0 0 1.5rem;
  color: #6c757d;
}

.clear-btn {
  padding: 0.75rem 1.5rem;
  background: #6c63ff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  padding: 2rem 0;
}

.page-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  background: white;
  border-radius: 6px;
  cursor: pointer;
}

.page-btn:hover:not(:disabled) {
  background: #6c63ff;
  color: white;
  border-color: #6c63ff;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: #6c757d;
  font-weight: 500;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  max-width: 400px;
  width: 90%;
  text-align: center;
}

.modal h3 {
  margin: 0 0 1rem;
  color: #2c3e50;
}

.modal p {
  margin: 0 0 1.5rem;
  color: #6c757d;
}

.modal-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  text-decoration: none;
  font-weight: 500;
}

.btn.primary {
  background: #6c63ff;
  color: white;
}

.btn.secondary {
  background: #e9ecef;
  color: #495057;
}

/* Toast */
.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #28a745;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 6px;
  transform: translateY(100px);
  opacity: 0;
  transition: all 0.3s ease;
  z-index: 1000;
}

.toast.show {
  transform: translateY(0);
  opacity: 1;
}

/* Responsive */
@media (max-width: 768px) {
  .filter-bar {
    flex-direction: column;
    align-items: stretch;
  }
  
  .slider-card {
    flex: 0 0 calc(50% - 0.5rem);
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1rem;
  }
  
  .pagination {
    flex-direction: column;
    gap: 0.5rem;
  }
}

@media (max-width: 480px) {
  .slider-card {
    flex: 0 0 calc(100% - 0rem);
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
}
</style>

