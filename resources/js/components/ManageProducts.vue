<template>
  <div class="manage-products">
    <!-- Import Design System -->
    <link rel="stylesheet" href="/css/design-system.css">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h1 class="page-title">Manage Products</h1>
          <p class="page-subtitle">View and manage your product listings</p>
        </div>
        <div class="header-actions">
          <router-link to="/add-product" class="btn btn-primary">
            <i class="icon">➕</i>
            Add New Product
          </router-link>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="page-content">
      <!-- Filters and Search -->
      <div class="filters-section">
        <div class="search-bar">
          <div class="search-input-group">
            <i class="search-icon">🔍</i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search products..."
              class="search-input"
              @input="handleSearch"
            />
          </div>
        </div>
        
        <div class="filter-controls">
          <select v-model="selectedCategory" @change="applyFilters" class="filter-select">
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          
          <select v-model="selectedStatus" @change="applyFilters" class="filter-select">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="sold">Sold</option>
          </select>
          
          <select v-model="sortBy" @change="applyFilters" class="filter-select">
            <option value="created_at">Newest First</option>
            <option value="name">Name A-Z</option>
            <option value="price">Price Low-High</option>
            <option value="price_desc">Price High-Low</option>
          </select>
        </div>
      </div>

      <!-- Products Stats -->
      <div class="stats-section">
        <div class="stat-card">
          <div class="stat-icon">📦</div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.totalProducts }}</div>
            <div class="stat-label">Total Products</div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.activeProducts }}</div>
            <div class="stat-label">Active</div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon">💰</div>
          <div class="stat-content">
            <div class="stat-value">${{ stats.totalValue }}</div>
            <div class="stat-label">Total Value</div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon">🛒</div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.soldProducts }}</div>
            <div class="stat-label">Sold</div>
          </div>
        </div>
      </div>

      <!-- Products Grid/List -->
      <div class="products-section">
        <div class="section-header">
          <h2 class="section-title">Your Products</h2>
          <div class="view-toggle">
            <button 
              class="view-btn" 
              :class="{ active: viewMode === 'grid' }" 
              @click="viewMode = 'grid'"
            >
              <i class="icon">⊞</i>
              Grid
            </button>
            <button 
              class="view-btn" 
              :class="{ active: viewMode === 'list' }" 
              @click="viewMode = 'list'"
            >
              <i class="icon">☰</i>
              List
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading products...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredProducts.length === 0" class="empty-state">
          <div class="empty-icon">📦</div>
          <h3 class="empty-title">No products found</h3>
          <p class="empty-text">
            {{ searchQuery ? 'No products match your search criteria.' : 'You haven\'t added any products yet.' }}
          </p>
          <router-link to="/add-product" class="btn btn-primary">
            <i class="icon">➕</i>
            Add Your First Product
          </router-link>
        </div>

        <!-- Products Grid View -->
        <div v-else-if="viewMode === 'grid'" class="products-grid">
          <div v-for="product in filteredProducts" :key="product.id" class="product-card">
            <div class="product-image">
              <img 
                :src="getProductImage(product)" 
                :alt="product.name"
                @error="handleImageError"
              />
              <div class="product-status" :class="getStatusClass(product.status)">
                {{ getStatusText(product.status) }}
              </div>
              <div class="product-actions-overlay">
                <button class="action-btn" @click="viewProduct(product.id)" title="View">
                  <i class="icon">👁️</i>
                </button>
                <button class="action-btn" @click="editProduct(product.id)" title="Edit">
                  <i class="icon">✏️</i>
                </button>
                <button class="action-btn danger" @click="deleteProduct(product.id)" title="Delete">
                  <i class="icon">🗑️</i>
                </button>
              </div>
            </div>
            
            <div class="product-info">
              <h3 class="product-name">{{ product.name }}</h3>
              <p class="product-price">${{ product.price }}</p>
              <div class="product-meta">
                <span class="meta-item">Qty: {{ product.quantity }}</span>
                <span class="meta-item">{{ product.category?.name }}</span>
              </div>
              <div class="product-stats">
                <span class="stat-item">
                  <i class="icon">👁️</i>
                  {{ product.views || 0 }} views
                </span>
                <span class="stat-item">
                  <i class="icon">❤️</i>
                  {{ product.likes || 0 }} likes
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Products List View -->
        <div v-else class="products-list">
          <div class="list-header">
            <div class="col-product">Product</div>
            <div class="col-price">Price</div>
            <div class="col-quantity">Quantity</div>
            <div class="col-status">Status</div>
            <div class="col-stats">Stats</div>
            <div class="col-actions">Actions</div>
          </div>
          
          <div v-for="product in filteredProducts" :key="product.id" class="list-item">
            <div class="col-product">
              <div class="product-info-row">
                <div class="product-thumbnail">
                  <img 
                    :src="getProductImage(product)" 
                    :alt="product.name"
                    @error="handleImageError"
                  />
                </div>
                <div class="product-details">
                  <h4 class="product-name">{{ product.name }}</h4>
                  <p class="product-category">{{ product.category?.name }}</p>
                  <p class="product-date">Listed {{ formatDate(product.created_at) }}</p>
                </div>
              </div>
            </div>
            
            <div class="col-price">
              <span class="price-value">${{ product.price }}</span>
            </div>
            
            <div class="col-quantity">
              <span class="quantity-value">{{ product.quantity }}</span>
            </div>
            
            <div class="col-status">
              <span class="status-badge" :class="getStatusClass(product.status)">
                {{ getStatusText(product.status) }}
              </span>
            </div>
            
            <div class="col-stats">
              <div class="stats-group">
                <span class="stat-item">{{ product.views || 0 }} views</span>
                <span class="stat-item">{{ product.likes || 0 }} likes</span>
              </div>
            </div>
            
            <div class="col-actions">
              <div class="actions-group">
                <button class="action-btn" @click="viewProduct(product.id)" title="View">
                  <i class="icon">👁️</i>
                </button>
                <button class="action-btn" @click="editProduct(product.id)" title="Edit">
                  <i class="icon">✏️</i>
                </button>
                <button class="action-btn danger" @click="deleteProduct(product.id)" title="Delete">
                  <i class="icon">🗑️</i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Delete Product</h3>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete "{{ productToDelete?.name }}"?</p>
          <p class="warning-text">This action cannot be undone.</p>
        </div>
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="closeDeleteModal">
            Cancel
          </button>
          <button class="btn btn-danger" @click="confirmDelete">
            Delete Product
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ManageProducts",
  data() {
    return {
      products: [],
      categories: [],
      filteredProducts: [],
      searchQuery: "",
      selectedCategory: "",
      selectedStatus: "",
      sortBy: "created_at",
      viewMode: "grid",
      loading: true,
      stats: {
        totalProducts: 0,
        activeProducts: 0,
        totalValue: 0,
        soldProducts: 0
      },
      showDeleteModal: false,
      productToDelete: null
    };
  },
  
  mounted() {
    this.checkAuthentication();
    this.loadData();
  },
  
  methods: {
    checkAuthentication() {
      const session = localStorage.getItem("userSession");
      if (!session) {
        this.$router.push("/login");
        return;
      }
      
      const userData = JSON.parse(session);
      if (userData.role !== "scrapSeller") {
        this.$router.push("/");
      }
    },
    
    async loadData() {
      this.loading = true;
      try {
        await Promise.all([
          this.loadProducts(),
          this.loadCategories()
        ]);
        this.calculateStats();
        this.applyFilters();
      } catch (error) {
        console.error('Error loading data:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async loadProducts() {
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/user/products', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          this.products = await response.json();
        }
      } catch (error) {
        console.error('Error loading products:', error);
        // Fallback mock data
        this.products = [
          {
            id: 1,
            name: "Aluminum Cans",
            price: 45.00,
            quantity: 100,
            status: "active",
            category: { name: "Metals" },
            image_path: "/images/aluminum_cans_1.jpg",
            views: 234,
            likes: 12,
            created_at: new Date()
          },
          {
            id: 2,
            name: "Copper Wire",
            price: 89.50,
            quantity: 50,
            status: "active",
            category: { name: "Metals" },
            image_path: "/images/copper_wire_1.jpg",
            views: 189,
            likes: 8,
            created_at: new Date()
          }
        ];
      }
    },
    
    async loadCategories() {
      try {
        const response = await fetch('/api/categories');
        if (response.ok) {
          this.categories = await response.json();
        }
      } catch (error) {
        console.error('Error loading categories:', error);
        this.categories = [
          { id: 1, name: 'Metals' },
          { id: 2, name: 'Plastics' },
          { id: 3, name: 'Electronics' }
        ];
      }
    },
    
    calculateStats() {
      this.stats.totalProducts = this.products.length;
      this.stats.activeProducts = this.products.filter(p => p.status === 'active').length;
      this.stats.soldProducts = this.products.filter(p => p.status === 'sold').length;
      this.stats.totalValue = this.products.reduce((sum, p) => sum + (parseFloat(p.price) * p.quantity), 0);
    },
    
    handleSearch() {
      this.applyFilters();
    },
    
    applyFilters() {
      let filtered = [...this.products];
      
      // Search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(product => 
          product.name.toLowerCase().includes(query) ||
          product.description?.toLowerCase().includes(query) ||
          product.category?.name.toLowerCase().includes(query)
        );
      }
      
      // Category filter
      if (this.selectedCategory) {
        filtered = filtered.filter(product => 
          product.category_id == this.selectedCategory
        );
      }
      
      // Status filter
      if (this.selectedStatus) {
        filtered = filtered.filter(product => 
          product.status === this.selectedStatus
        );
      }
      
      // Sort
      filtered.sort((a, b) => {
        switch (this.sortBy) {
          case 'name':
            return a.name.localeCompare(b.name);
          case 'price':
            return parseFloat(a.price) - parseFloat(b.price);
          case 'price_desc':
            return parseFloat(b.price) - parseFloat(a.price);
          case 'created_at':
          default:
            return new Date(b.created_at) - new Date(a.created_at);
        }
      });
      
      this.filteredProducts = filtered;
    },
    
    getProductImage(product) {
      if (product.image_path) {
        return product.image_path.startsWith('/') ? product.image_path : `/images/${product.image_path}`;
      }
      return '/images/placeholder.jpg';
    },
    
    handleImageError(event) {
      event.target.src = '/images/placeholder.jpg';
    },
    
    getStatusClass(status) {
      const classes = {
        'active': 'success',
        'inactive': 'warning',
        'sold': 'info',
        'pending': 'warning'
      };
      return classes[status] || 'neutral';
    },
    
    getStatusText(status) {
      const texts = {
        'active': 'Active',
        'inactive': 'Inactive',
        'sold': 'Sold',
        'pending': 'Pending'
      };
      return texts[status] || status;
    },
    
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    },
    
    viewProduct(productId) {
      this.$router.push(`/product/${productId}`);
    },
    
    editProduct(productId) {
      this.$router.push(`/edit-product/${productId}`);
    },
    
    deleteProduct(productId) {
      this.productToDelete = this.products.find(p => p.id === productId);
      this.showDeleteModal = true;
    },
    
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.productToDelete = null;
    },
    
    async confirmDelete() {
      if (!this.productToDelete) return;
      
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch(`/api/products/${this.productToDelete.id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          this.products = this.products.filter(p => p.id !== this.productToDelete.id);
          this.calculateStats();
          this.applyFilters();
          this.closeDeleteModal();
        } else {
          alert('Failed to delete product');
        }
      } catch (error) {
        console.error('Error deleting product:', error);
        alert('An error occurred while deleting the product');
      }
    }
  }
};
</script>

<style scoped>
/* Import design system */
@import url('../../css/design-system.css');

.manage-products {
  min-height: 100vh;
  background: var(--neutral-50);
}

/* Page Header */
.page-header {
  background: var(--neutral-0);
  border-bottom: 1px solid var(--neutral-200);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
}

.header-info {
  flex: 1;
}

.page-title {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--neutral-800);
  margin: 0 0 var(--space-1) 0;
}

.page-subtitle {
  font-size: var(--text-base);
  color: var(--neutral-600);
  margin: 0;
}

.header-actions {
  display: flex;
  gap: var(--space-3);
}

.btn {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  text-decoration: none;
  cursor: pointer;
  transition: all var(--transition-fast);
  border: 1px solid transparent;
}

.btn-primary {
  background: var(--success-600);
  color: var(--neutral-0);
}

.btn-primary:hover {
  background: var(--success-700);
}

.btn-secondary {
  background: var(--neutral-0);
  color: var(--neutral-700);
  border-color: var(--neutral-300);
}

.btn-secondary:hover {
  background: var(--neutral-50);
}

.btn-danger {
  background: var(--error-600);
  color: var(--neutral-0);
}

.btn-danger:hover {
  background: var(--error-700);
}

/* Main Content */
.page-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: var(--space-6);
}

/* Filters Section */
.filters-section {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  margin-bottom: var(--space-6);
  box-shadow: var(--shadow-sm);
}

.search-bar {
  margin-bottom: var(--space-4);
}

.search-input-group {
  position: relative;
  max-width: 400px;
}

.search-icon {
  position: absolute;
  left: var(--space-3);
  top: 50%;
  transform: translateY(-50%);
  color: var(--neutral-400);
}

.search-input {
  width: 100%;
  padding: var(--space-3) var(--space-3) var(--space-3) var(--space-10);
  border: 1px solid var(--neutral-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-base);
  transition: all var(--transition-fast);
}

.search-input:focus {
  outline: none;
  border-color: var(--success-400);
  box-shadow: 0 0 0 3px var(--success-100);
}

.filter-controls {
  display: flex;
  gap: var(--space-4);
  flex-wrap: wrap;
}

.filter-select {
  padding: var(--space-2) var(--space-3);
  border: 1px solid var(--neutral-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  background: var(--neutral-0);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.filter-select:focus {
  outline: none;
  border-color: var(--success-400);
  box-shadow: 0 0 0 3px var(--success-100);
}

/* Stats Section */
.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: var(--space-4);
  margin-bottom: var(--space-6);
}

.stat-card {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-5);
  display: flex;
  align-items: center;
  gap: var(--space-4);
  box-shadow: var(--shadow-sm);
  transition: all var(--transition-fast);
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.stat-icon {
  font-size: var(--text-3xl);
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--success-100);
  border-radius: var(--radius-lg);
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--neutral-800);
  margin-bottom: var(--space-1);
}

.stat-label {
  font-size: var(--text-sm);
  color: var(--neutral-600);
  font-weight: var(--font-medium);
}

/* Products Section */
.products-section {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-6);
}

.section-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin: 0;
}

.view-toggle {
  display: flex;
  gap: var(--space-1);
  background: var(--neutral-100);
  padding: var(--space-1);
  border-radius: var(--radius-lg);
}

.view-btn {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  background: transparent;
  border: none;
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  color: var(--neutral-600);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.view-btn.active {
  background: var(--neutral-0);
  color: var(--success-600);
  box-shadow: var(--shadow-sm);
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: var(--space-12);
  color: var(--neutral-500);
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--neutral-200);
  border-top: 4px solid var(--success-600);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto var(--space-4);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: var(--space-12);
  color: var(--neutral-500);
}

.empty-icon {
  font-size: var(--text-6xl);
  margin-bottom: var(--space-4);
}

.empty-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--neutral-700);
  margin-bottom: var(--space-2);
}

.empty-text {
  font-size: var(--text-base);
  margin-bottom: var(--space-6);
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: var(--space-6);
}

.product-card {
  border: 1px solid var(--neutral-200);
  border-radius: var(--radius-xl);
  overflow: hidden;
  transition: all var(--transition-base);
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
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

.product-status {
  position: absolute;
  top: var(--space-3);
  right: var(--space-3);
  padding: var(--space-1) var(--space-2);
  border-radius: var(--radius-full);
  font-size: var(--text-xs);
  font-weight: var(--font-medium);
  text-transform: uppercase;
}

.product-status.success {
  background: var(--success-100);
  color: var(--success-700);
}

.product-status.warning {
  background: var(--warning-100);
  color: var(--warning-700);
}

.product-status.info {
  background: var(--primary-100);
  color: var(--primary-700);
}

.product-actions-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-3);
  opacity: 0;
  transition: opacity var(--transition-fast);
}

.product-card:hover .product-actions-overlay {
  opacity: 1;
}

.action-btn {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: var(--neutral-0);
  color: var(--neutral-700);
  cursor: pointer;
  transition: all var(--transition-fast);
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn:hover {
  background: var(--success-600);
  color: var(--neutral-0);
}

.action-btn.danger:hover {
  background: var(--error-600);
}

.product-info {
  padding: var(--space-4);
}

.product-name {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-2);
}

.product-price {
  font-size: var(--text-xl);
  font-weight: var(--font-bold);
  color: var(--success-600);
  margin-bottom: var(--space-3);
}

.product-meta {
  display: flex;
  gap: var(--space-3);
  margin-bottom: var(--space-3);
}

.meta-item {
  font-size: var(--text-sm);
  color: var(--neutral-500);
  background: var(--neutral-100);
  padding: var(--space-1) var(--space-2);
  border-radius: var(--radius-md);
}

.product-stats {
  display: flex;
  gap: var(--space-4);
}

.stat-item {
  display: flex;
  align-items: center;
  gap: var(--space-1);
  font-size: var(--text-sm);
  color: var(--neutral-500);
}

/* Products List */
.products-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.list-header {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;
  gap: var(--space-4);
  padding: var(--space-4);
  background: var(--neutral-50);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  font-weight: var(--font-semibold);
  color: var(--neutral-600);
}

.list-item {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;
  gap: var(--space-4);
  padding: var(--space-4);
  border: 1px solid var(--neutral-200);
  border-radius: var(--radius-lg);
  transition: all var(--transition-fast);
  align-items: center;
}

.list-item:hover {
  background: var(--neutral-50);
  border-color: var(--success-300);
}

.product-info-row {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.product-thumbnail {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  flex-shrink: 0;
}

.product-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-details {
  flex: 1;
}

.product-details .product-name {
  font-size: var(--text-base);
  margin-bottom: var(--space-1);
}

.product-category {
  font-size: var(--text-sm);
  color: var(--neutral-500);
  margin-bottom: var(--space-1);
}

.product-date {
  font-size: var(--text-xs);
  color: var(--neutral-400);
  margin: 0;
}

.price-value {
  font-size: var(--text-lg);
  font-weight: var(--font-bold);
  color: var(--success-600);
}

.quantity-value {
  font-size: var(--text-base);
  color: var(--neutral-700);
}

.status-badge {
  padding: var(--space-1) var(--space-2);
  border-radius: var(--radius-full);
  font-size: var(--text-xs);
  font-weight: var(--font-medium);
  text-transform: uppercase;
}

.stats-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.stats-group .stat-item {
  font-size: var(--text-xs);
}

.actions-group {
  display: flex;
  gap: var(--space-2);
}

.actions-group .action-btn {
  width: 32px;
  height: 32px;
  font-size: var(--text-sm);
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  max-width: 400px;
  width: 90%;
  box-shadow: var(--shadow-lg);
}

.modal-header {
  margin-bottom: var(--space-4);
}

.modal-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin: 0;
}

.modal-body {
  margin-bottom: var(--space-6);
}

.modal-body p {
  margin-bottom: var(--space-2);
  color: var(--neutral-600);
}

.warning-text {
  color: var(--error-600);
  font-weight: var(--font-medium);
}

.modal-actions {
  display: flex;
  gap: var(--space-3);
  justify-content: flex-end;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
  
  .filter-controls {
    flex-direction: column;
    gap: var(--space-3);
  }
  
  .filter-select {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .page-content {
    padding: var(--space-4);
  }
  
  .header-content {
    flex-direction: column;
    gap: var(--space-4);
    align-items: flex-start;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-section {
    grid-template-columns: 1fr;
  }
  
  .products-list {
    display: none; /* Hide list view on mobile */
  }
  
  .view-toggle {
    display: none; /* Hide view toggle on mobile */
  }
  
  .list-header,
  .list-item {
    grid-template-columns: 1fr;
    gap: var(--space-2);
  }
  
  .modal-actions {
    flex-direction: column;
  }
}
</style>
