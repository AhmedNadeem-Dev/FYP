<template>
  <div class="order-history">
    <!-- Import Design System -->
    <link rel="stylesheet" href="/css/design-system.css">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h1 class="page-title">Order History</h1>
          <p class="page-subtitle">Track your purchases and order status</p>
        </div>
        <div class="header-stats">
          <div class="stat-item">
            <div class="stat-value">{{ stats.totalOrders }}</div>
            <div class="stat-label">Total Orders</div>
          </div>
          <div class="stat-item">
            <div class="stat-value">PKR {{ stats.totalSpent }}</div>
            <div class="stat-label">Total Spent</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="page-content">
      <!-- Filters -->
      <div class="filters-section">
        <div class="filter-controls">
          <div class="search-group">
            <i class="search-icon">🔍</i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search orders..."
              class="search-input"
              @input="applyFilters"
            />
          </div>
          
          <select v-model="selectedStatus" @change="applyFilters" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
          
          <select v-model="dateRange" @change="applyFilters" class="filter-select">
            <option value="">All Time</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="quarter">Last 3 Months</option>
            <option value="year">This Year</option>
          </select>
          
          <button class="filter-btn" @click="resetFilters">
            <i class="icon">🔄</i>
            Reset
          </button>
        </div>
      </div>

      <!-- Orders List -->
      <div class="orders-section">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading your orders...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredOrders.length === 0" class="empty-state">
          <div class="empty-icon">📦</div>
          <h3 class="empty-title">No orders found</h3>
          <p class="empty-text">
            {{ searchQuery ? 'No orders match your search criteria.' : 'You haven\'t placed any orders yet. Start shopping to see your order history here!' }}
          </p>
          <div class="empty-actions">
            <router-link to="/scrap-items" class="btn btn-primary">
              <i class="icon">🛒</i>
              Browse Products
            </router-link>
            <router-link to="/cart" class="btn btn-secondary">
              <i class="icon">🛍️</i>
              View Cart
            </router-link>
          </div>
        </div>

        <!-- Orders List -->
        <div v-else class="orders-list">
          <div v-for="order in filteredOrders" :key="order.id" class="order-card">
            <!-- Order Header -->
            <div class="order-header">
              <div class="order-info">
                <h3 class="order-id">Order #{{ order.order_id || order.id }}</h3>
                <p class="order-date">Placed on {{ formatDate(order.created_at) }}</p>
              </div>
              <div class="order-status">
                <span class="status-badge" :class="getStatusClass(order.status)">
                  {{ getStatusText(order.status) }}
                </span>
                <div class="order-total">PKR {{ order.total_amount }}</div>
              </div>
            </div>

            <!-- Order Items -->
            <div class="order-items">
              <div v-for="item in order.items" :key="item.id" class="order-item">
                <div class="item-image">
                  <img 
                    :src="getProductImage(item.product)" 
                    :alt="item.product.name"
                    @error="handleImageError"
                  />
                </div>
                <div class="item-details">
                  <h4 class="item-name">{{ item.product.name }}</h4>
                  <p class="item-meta">
                    Quantity: {{ item.quantity }} × PKR {{ item.price }}
                  </p>
                  <p class="item-seller">Sold by {{ item.product.seller?.name }}</p>
                </div>
                <div class="item-actions">
                  <router-link :to="`/product/${item.product.id}`" class="action-btn">
                    <i class="icon">👁️</i>
                    View Product
                  </router-link>
                  <button 
                    v-if="order.status === 'delivered'" 
                    class="action-btn"
                    @click="reviewProduct(item.product)"
                  >
                    <i class="icon">⭐</i>
                    Review
                  </button>
                </div>
              </div>
            </div>

            <!-- Order Progress -->
            <div class="order-progress">
              <div class="progress-steps">
                <div 
                  class="progress-step" 
                  :class="{ active: getStepStatus('pending', order.status) }"
                >
                  <div class="step-icon">📋</div>
                  <div class="step-label">Order Placed</div>
                </div>
                <div 
                  class="progress-step" 
                  :class="{ active: getStepStatus('processing', order.status) }"
                >
                  <div class="step-icon">⚙️</div>
                  <div class="step-label">Processing</div>
                </div>
                <div 
                  class="progress-step" 
                  :class="{ active: getStepStatus('shipped', order.status) }"
                >
                  <div class="step-icon">🚚</div>
                  <div class="step-label">Shipped</div>
                </div>
                <div 
                  class="progress-step" 
                  :class="{ active: getStepStatus('delivered', order.status) }"
                >
                  <div class="step-icon">📦</div>
                  <div class="step-label">Delivered</div>
                </div>
              </div>
            </div>

            <!-- Order Actions -->
            <div class="order-actions">
              <button class="action-btn secondary" @click="viewOrderDetails(order)">
                <i class="icon">📄</i>
                View Details
              </button>
              
              <button 
                v-if="order.status === 'pending'" 
                class="action-btn danger"
                @click="cancelOrder(order)"
              >
                <i class="icon">❌</i>
                Cancel Order
              </button>
              
              <button 
                v-if="order.status === 'delivered'" 
                class="action-btn"
                @click="reorderItems(order)"
              >
                <i class="icon">🔄</i>
                Reorder
              </button>
              
              <button 
                v-if="order.status === 'shipped'" 
                class="action-btn"
                @click="trackOrder(order)"
              >
                <i class="icon">📍</i>
                Track Package
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="showOrderModal" class="modal-overlay" @click="closeOrderModal">
      <div class="modal-content order-modal" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Order Details</h3>
          <button class="modal-close" @click="closeOrderModal">×</button>
        </div>
        
        <div class="modal-body">
          <div v-if="selectedOrder" class="order-details">
            <!-- Order Summary -->
            <div class="detail-section">
              <h4 class="section-title">Order Summary</h4>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="label">Order ID:</span>
                  <span class="value">#{{ selectedOrder.order_id || selectedOrder.id }}</span>
                </div>
                <div class="detail-item">
                  <span class="label">Date:</span>
                  <span class="value">{{ formatDate(selectedOrder.created_at) }}</span>
                </div>
                <div class="detail-item">
                  <span class="label">Status:</span>
                  <span class="value">
                    <span class="status-badge" :class="getStatusClass(selectedOrder.status)">
                      {{ getStatusText(selectedOrder.status) }}
                    </span>
                  </span>
                </div>
                <div class="detail-item">
                  <span class="label">Total:</span>
                  <span class="value">PKR {{ selectedOrder.total_amount }}</span>
                </div>
              </div>
            </div>

            <!-- Shipping Information -->
            <div class="detail-section">
              <h4 class="section-title">Shipping Information</h4>
              <div class="shipping-info">
                <p><strong>Address:</strong> {{ selectedOrder.shipping_address || 'Not specified' }}</p>
                <p><strong>Method:</strong> {{ selectedOrder.shipping_method || 'Standard Shipping' }}</p>
                <p v-if="selectedOrder.tracking_number">
                  <strong>Tracking:</strong> {{ selectedOrder.tracking_number }}
                </p>
              </div>
            </div>

            <!-- Items -->
            <div class="detail-section">
              <h4 class="section-title">Items</h4>
              <div class="modal-items">
                <div v-for="item in selectedOrder.items" :key="item.id" class="modal-item">
                  <div class="item-image">
                    <img 
                      :src="getProductImage(item.product)" 
                      :alt="item.product.name"
                      @error="handleImageError"
                    />
                  </div>
                  <div class="item-info">
                    <h5 class="item-name">{{ item.product.name }}</h5>
                    <p class="item-details">
                      {{ item.quantity }} × PKR {{ item.price }} = PKR {{ (item.quantity * item.price).toFixed(2) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Notification -->
    <div v-if="notification" class="notification" :class="notification.type">
      <span class="notification-text">{{ notification.message }}</span>
      <button class="notification-close" @click="closeNotification">×</button>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div v-if="showCancelModal" class="modal-overlay" @click="closeCancelModal">
      <div class="modal-content cancel-modal" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Cancel Order</h3>
          <button class="modal-close" @click="closeCancelModal">×</button>
        </div>
        
        <div class="modal-body">
          <div v-if="orderToCancel" class="cancel-confirmation">
            <div class="warning-icon">⚠️</div>
            <h4>Are you sure you want to cancel this order?</h4>
            <div class="order-summary">
              <p><strong>Order ID:</strong> #{{ orderToCancel.order_id || orderToCancel.id }}</p>
              <p><strong>Total Amount:</strong> PKR {{ orderToCancel.total_amount }}</p>
              <p><strong>Status:</strong> {{ getStatusText(orderToCancel.status) }}</p>
            </div>
            <p class="warning-text">This action cannot be undone. Your order will be cancelled and you will be refunded according to our refund policy.</p>
          </div>
          
          <div class="modal-actions">
            <button class="action-btn secondary" @click="closeCancelModal">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M18 6L6 18M6 6l12 12"/>
              </svg>
              Keep Order
            </button>
            <button class="action-btn danger" @click="confirmCancelOrder">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
              </svg>
              Cancel Order
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "OrderHistory",
  data() {
    return {
      orders: [],
      filteredOrders: [],
      loading: true,
      searchQuery: "",
      selectedStatus: "",
      dateRange: "",
      stats: {
        totalOrders: 0,
        totalSpent: 0
      },
      showOrderModal: false,
      selectedOrder: null,
      notification: null,
      showCancelModal: false,
      orderToCancel: null
    };
  },
  
  mounted() {
    console.log('🚀 OrderHistory component mounted');
    console.log('🔍 Route params:', this.$route.params);
    console.log('🔍 Route path:', this.$route.path);
    
    // Check authentication but don't redirect immediately
    const hasAuth = this.checkAuthentication();
    
    if (hasAuth) {
      // Load orders after a short delay to ensure component is fully mounted
      setTimeout(() => {
        this.loadOrders();
      }, 100);
    } else {
      // Set loading to false so user can see the page
      this.loading = false;
    }
  },
  
  methods: {
    checkAuthentication() {
      const session = localStorage.getItem("userSession");
      const token = localStorage.getItem("access_token");
      
      console.log('🔐 Auth check - Session:', session ? 'Present' : 'Missing');
      console.log('🔐 Auth check - Token:', token ? 'Present' : 'Missing');
      
      // Don't redirect immediately, just log the issue
      if (!session && !token) {
        console.log('⚠️ No authentication found');
        this.showNotification('Please log in to view your orders', 'error');
        // Give user time to see the page before redirecting
        setTimeout(() => {
          this.$router.push("/login");
        }, 3000);
        return false;
      }
      
      return true;
    },
    
    async loadOrders() {
      this.loading = true;
      try {
        const token = localStorage.getItem('access_token');
        const userSession = localStorage.getItem('userSession');
        
        console.log('📦 Loading orders for user session:', userSession);
        
        // Check if we have a specific order ID in the route
        const orderId = this.$route.params.orderId;
        
        console.log('🔍 Order ID from route:', orderId);
        
        if (orderId) {
          console.log('📋 Loading specific order:', orderId);
          // Fetch specific order details
          await this.loadSpecificOrder(orderId, token);
        } else {
          console.log('📋 Loading all orders');
          // Fetch all orders
          await this.loadAllOrders(token);
        }
      } catch (error) {
        console.error('Error loading orders:', error);
        this.showNotification('Error loading orders. Please try again.', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async loadSpecificOrder(orderId, token) {
      try {
        const response = await fetch(`/api/orders/${orderId}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          const responseData = await response.json();
          // Handle the API response structure - the order data is wrapped in an 'order' key
          const orderData = responseData.order || responseData;
          
          // Transform the order data to match the expected structure
          const transformedOrder = {
            id: orderData.order_id,
            order_id: orderData.order_id,
            created_at: orderData.created_at,
            status: orderData.status,
            total_amount: orderData.total_amount,
            shipping_address: orderData.shipping_address,
            contact_phone: orderData.contact_phone,
            payment_method: orderData.payment_method,
            notes: orderData.notes,
            items: orderData.items.map(item => ({
              id: item.id,
              product_id: item.product_id,
              quantity: item.quantity,
              price: item.price,
              product: {
                id: item.product.id,
                name: item.product.name,
                images: item.product.images || ['/images/main.jpg'],
                image_path: item.product.image_path || '/images/main.jpg',
                seller: { name: 'Seller' }
              }
            }))
          };
          
          this.orders = [transformedOrder]; // Set as array with single order
          this.selectedOrder = transformedOrder; // Also set as selected for modal
          this.showOrderModal = true; // Show the order details modal
          this.calculateStats();
          this.applyFilters();
        } else {
          const errorData = await response.json();
          throw new Error(errorData.error || 'Order not found');
        }
      } catch (error) {
        console.error('Error loading specific order:', error);
        this.showNotification(`Error: ${error.message}. Redirecting to order history.`, 'error');
        // Redirect to general order history after 3 seconds
        setTimeout(() => {
          this.$router.push('/order-history');
        }, 3000);
      }
    },
    
    async loadAllOrders(token) {
      try {
        console.log('📦 Loading all orders with token:', token ? 'Present' : 'Missing');
        
        const response = await fetch('/api/orders', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        console.log('📦 Orders API response status:', response.status);
        
        if (response.ok) {
          const data = await response.json();
          console.log('📦 Orders API response data:', data);
          console.log('📦 Response type:', typeof data, 'Is Array:', Array.isArray(data));
          
          // Handle different response formats
          let orders = [];
          if (Array.isArray(data)) {
            orders = data;
          } else if (data.orders && Array.isArray(data.orders)) {
            orders = data.orders;
          } else if (data.data && Array.isArray(data.data)) {
            orders = data.data;
          } else {
            console.warn('⚠️ Unexpected API response format:', data);
            orders = [];
          }
          
          console.log('📦 Raw orders count:', orders.length);
          
          // Transform orders to ensure consistent structure
          this.orders = orders.map(order => ({
            id: order.order_id,
            order_id: order.order_id,
            created_at: order.created_at,
            status: order.status || 'pending',
            total_amount: order.total_amount || 0,
            shipping_address: order.shipping_address || '',
            contact_phone: order.contact_phone || '',
            payment_method: order.payment_method || '',
            notes: order.notes || '',
            items: order.items || []
          }));
          
          console.log('✅ Processed orders count:', this.orders.length);
          console.log('✅ Processed orders:', this.orders);
          
          this.calculateStats();
          this.applyFilters();
          
          // Show result in notification
          this.showNotification(`Found ${this.orders.length} orders`, this.orders.length > 0 ? 'success' : 'info');
        } else {
          const errorData = await response.json().catch(() => ({}));
          console.error('❌ Orders API error:', response.status, errorData);
          
          if (response.status === 401) {
            this.showNotification('Authentication failed. Please log in again.', 'error');
          } else {
            this.showNotification(`API Error: ${errorData.message || 'Failed to fetch orders'}`, 'error');
          }
          
          throw new Error(errorData.message || 'Failed to fetch orders');
        }
      } catch (error) {
        console.error('❌ Error loading orders:', error);
        this.showNotification('Error loading orders. Please try again.', 'error');
        
        // Set empty orders instead of mock data to avoid confusion
        this.orders = [];
        this.calculateStats();
        this.applyFilters();
      }
    },
    
    calculateStats() {
      this.stats.totalOrders = this.orders.length;
      this.stats.totalSpent = this.orders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
    },
    
    applyFilters() {
      let filtered = [...this.orders];
      
      // Search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(order => 
          order.id.toLowerCase().includes(query) ||
          order.items.some(item => 
            item.product.name.toLowerCase().includes(query)
          )
        );
      }
      
      // Status filter
      if (this.selectedStatus) {
        filtered = filtered.filter(order => order.status === this.selectedStatus);
      }
      
      // Date range filter
      if (this.dateRange) {
        const now = new Date();
        const filterDate = new Date();
        
        switch (this.dateRange) {
          case 'week':
            filterDate.setDate(now.getDate() - 7);
            break;
          case 'month':
            filterDate.setMonth(now.getMonth() - 1);
            break;
          case 'quarter':
            filterDate.setMonth(now.getMonth() - 3);
            break;
          case 'year':
            filterDate.setFullYear(now.getFullYear() - 1);
            break;
        }
        
        filtered = filtered.filter(order => 
          new Date(order.created_at) >= filterDate
        );
      }
      
      // Sort by date (newest first)
      filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      
      this.filteredOrders = filtered;
    },
    
    resetFilters() {
      this.searchQuery = "";
      this.selectedStatus = "";
      this.dateRange = "";
      this.applyFilters();
    },
    
    getProductImage(product) {
      // Handle both image_path and images array structures
      if (product?.images && Array.isArray(product.images) && product.images.length > 0) {
        const image = product.images[0];
        return image.startsWith('/') ? image : `/images/${image}`;
      }
      if (product?.image_path) {
        return product.image_path.startsWith('/') ? product.image_path : `/images/${product.image_path}`;
      }
      return '/images/main.jpg'; // Use main.jpg as fallback instead of placeholder
    },
    
    handleImageError(event) {
      event.target.src = '/images/main.jpg';
    },
    
    getStatusClass(status) {
      const classes = {
        'pending': 'warning',
        'processing': 'primary',
        'shipped': 'info',
        'delivered': 'success',
        'cancelled': 'error'
      };
      return classes[status] || 'neutral';
    },
    
    getStatusText(status) {
      const texts = {
        'pending': 'Pending',
        'processing': 'Processing',
        'shipped': 'Shipped',
        'delivered': 'Delivered',
        'cancelled': 'Cancelled'
      };
      return texts[status] || status;
    },
    
    getStepStatus(stepStatus, currentStatus) {
      const statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
      const stepIndex = statusOrder.indexOf(stepStatus);
      const currentIndex = statusOrder.indexOf(currentStatus);
      return stepIndex <= currentIndex;
    },
    
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    
    viewOrderDetails(order) {
      this.selectedOrder = order;
      this.showOrderModal = true;
    },
    
    closeOrderModal() {
      this.showOrderModal = false;
      this.selectedOrder = null;
    },
    
    async cancelOrder(order) {
      this.orderToCancel = order;
      this.showCancelModal = true;
    },
    
    closeCancelModal() {
      this.showCancelModal = false;
      this.orderToCancel = null;
    },
    
    async confirmCancelOrder() {
      if (!this.orderToCancel) return;
      
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch(`/api/orders/${this.orderToCancel.order_id || this.orderToCancel.id}/cancel`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          this.orderToCancel.status = 'cancelled';
          this.showNotification('Order cancelled successfully', 'success');
        } else {
          const errorData = await response.json();
          this.showNotification(errorData.error || 'Failed to cancel order', 'error');
        }
      } catch (error) {
        console.error('Error cancelling order:', error);
        this.showNotification('An error occurred while cancelling the order', 'error');
      } finally {
        this.closeCancelModal();
      }
    },
    
    reorderItems(order) {
      // Add all items to cart
      this.showNotification('Items added to cart!', 'success');
      // Implement reorder logic
    },
    
    trackOrder(order) {
      // Open tracking page or modal
      this.showNotification('Tracking information will be available soon', 'info');
    },
    
    reviewProduct(product) {
      // Navigate to review page
      this.$router.push(`/product/${product.id}?review=true`);
    },
    
    showNotification(message, type) {
      this.notification = { message, type };
      setTimeout(() => {
        this.notification = null;
      }, 5000);
    },
    
    closeNotification() {
      this.notification = null;
    }
  }
};
</script>

<style scoped>
/* Import design system */
@import url('../../css/design-system.css');

.order-history {
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
  max-width: 1200px;
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

.header-stats {
  display: flex;
  gap: var(--space-6);
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--primary-600);
  margin-bottom: var(--space-1);
}

.stat-label {
  font-size: var(--text-sm);
  color: var(--neutral-600);
  font-weight: var(--font-medium);
}

/* Main Content */
.page-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: var(--space-6);
}

/* Filters */
.filters-section {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-4);
  margin-bottom: var(--space-6);
  box-shadow: var(--shadow-sm);
}

.filter-controls {
  display: flex;
  gap: var(--space-4);
  align-items: center;
  flex-wrap: wrap;
}

.search-group {
  position: relative;
  flex: 1;
  min-width: 250px;
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
  border-color: var(--primary-400);
  box-shadow: 0 0 0 3px var(--primary-100);
}

.filter-select {
  padding: var(--space-3);
  border: 1px solid var(--neutral-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  background: var(--neutral-0);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary-400);
  box-shadow: 0 0 0 3px var(--primary-100);
}

.filter-btn {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3);
  background: var(--neutral-100);
  border: 1px solid var(--neutral-200);
  border-radius: var(--radius-lg);
  color: var(--neutral-700);
  font-size: var(--text-sm);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.filter-btn:hover {
  background: var(--neutral-200);
}

/* Orders Section */
.orders-section {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
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
  border-top: 4px solid var(--primary-600);
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
  padding: var(--space-16) var(--space-8);
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: var(--space-4);
}

.empty-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-2);
}

.empty-text {
  font-size: var(--text-base);
  color: var(--neutral-600);
  margin-bottom: var(--space-6);
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.empty-actions {
  display: flex;
  gap: var(--space-3);
  justify-content: center;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3) var(--space-6);
  border-radius: var(--radius-lg);
  font-size: var(--text-base);
  font-weight: var(--font-medium);
  text-decoration: none;
  transition: all var(--transition-fast);
  border: 1px solid transparent;
}

.btn-primary {
  background: var(--primary-600);
  color: var(--neutral-0);
}

.btn-primary:hover {
  background: var(--primary-700);
  transform: translateY(-1px);
}

.btn-secondary {
  background: var(--neutral-200);
  color: var(--neutral-700);
}

.btn-secondary:hover {
  background: var(--neutral-300);
  transform: translateY(-1px);
}

/* Orders List */
.orders-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
}

.order-card {
  border: 1px solid var(--neutral-200);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  transition: all var(--transition-fast);
}

.order-card:hover {
  box-shadow: var(--shadow-md);
  border-color: var(--primary-300);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: var(--space-4);
  padding-bottom: var(--space-4);
  border-bottom: 1px solid var(--neutral-200);
}

.order-info {
  flex: 1;
}

.order-id {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-1);
}

.order-date {
  font-size: var(--text-sm);
  color: var(--neutral-500);
  margin: 0;
}

.order-status {
  text-align: right;
}

.status-badge {
  display: inline-block;
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-full);
  font-size: var(--text-xs);
  font-weight: var(--font-medium);
  text-transform: uppercase;
  margin-bottom: var(--space-2);
}

.status-badge.success {
  background: var(--success-100);
  color: var(--success-700);
}

.status-badge.warning {
  background: var(--warning-100);
  color: var(--warning-700);
}

.status-badge.primary {
  background: var(--primary-100);
  color: var(--primary-700);
}

.status-badge.info {
  background: var(--info-100);
  color: var(--info-700);
}

.status-badge.error {
  background: var(--error-100);
  color: var(--error-700);
}

.order-total {
  font-size: var(--text-xl);
  font-weight: var(--font-bold);
  color: var(--neutral-800);
}

.order-items {
  margin-bottom: var(--space-6);
}

.order-item {
  display: flex;
  align-items: center;
  gap: var(--space-4);
  padding: var(--space-4);
  background: var(--neutral-50);
  border-radius: var(--radius-lg);
  margin-bottom: var(--space-3);
}

.order-item:last-child {
  margin-bottom: 0;
}

.item-image {
  width: 80px;
  height: 80px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
}

.item-name {
  font-size: var(--text-base);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-1);
}

.item-meta {
  font-size: var(--text-sm);
  color: var(--neutral-600);
  margin-bottom: var(--space-1);
}

.item-seller {
  font-size: var(--text-xs);
  color: var(--neutral-500);
  margin: 0;
}

.item-actions {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.action-btn {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  border: 1px solid var(--neutral-300);
  border-radius: var(--radius-md);
  background: var(--neutral-0);
  color: var(--neutral-700);
  font-size: var(--text-xs);
  text-decoration: none;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.action-btn:hover {
  background: var(--neutral-50);
  border-color: var(--neutral-400);
}

.action-btn.secondary {
  border-color: var(--primary-300);
  color: var(--primary-600);
}

.action-btn.secondary:hover {
  background: var(--primary-50);
}

.action-btn.danger {
  border-color: var(--error-300);
  color: var(--error-600);
}

.action-btn.danger:hover {
  background: var(--error-50);
}

/* Order Progress */
.order-progress {
  margin-bottom: var(--space-6);
}

.progress-steps {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
}

.progress-steps::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 2px;
  background: var(--neutral-200);
  transform: translateY(-50%);
  z-index: 1;
}

.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-2);
  position: relative;
  z-index: 2;
}

.step-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--neutral-200);
  color: var(--neutral-500);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--text-lg);
  transition: all var(--transition-fast);
}

.progress-step.active .step-icon {
  background: var(--primary-600);
  color: var(--neutral-0);
}

.step-label {
  font-size: var(--text-xs);
  color: var(--neutral-500);
  text-align: center;
  font-weight: var(--font-medium);
}

.progress-step.active .step-label {
  color: var(--primary-600);
}

/* Order Actions */
.order-actions {
  display: flex;
  gap: var(--space-3);
  flex-wrap: wrap;
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
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-lg);
}

.order-modal {
  max-width: 800px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--space-6);
  border-bottom: 1px solid var(--neutral-200);
}

.modal-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  font-size: var(--text-2xl);
  cursor: pointer;
  color: var(--neutral-500);
  transition: color var(--transition-fast);
}

.modal-close:hover {
  color: var(--neutral-700);
}

.modal-body {
  padding: var(--space-6);
}

.detail-section {
  margin-bottom: var(--space-6);
}

.detail-section:last-child {
  margin-bottom: 0;
}

.section-title {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-4);
}

.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-3);
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--space-3);
  background: var(--neutral-50);
  border-radius: var(--radius-lg);
}

.detail-item .label {
  font-size: var(--text-sm);
  color: var(--neutral-600);
  font-weight: var(--font-medium);
}

.detail-item .value {
  font-size: var(--text-sm);
  color: var(--neutral-800);
  font-weight: var(--font-semibold);
}

.shipping-info p {
  margin-bottom: var(--space-2);
  color: var(--neutral-600);
}

.modal-items {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.modal-item {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-3);
  background: var(--neutral-50);
  border-radius: var(--radius-lg);
}

.modal-item .item-image {
  width: 60px;
  height: 60px;
}

.modal-item .item-info {
  flex: 1;
}

.modal-item .item-name {
  font-size: var(--text-base);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-1);
}

.modal-item .item-details {
  font-size: var(--text-sm);
  color: var(--neutral-600);
  margin: 0;
}

/* Notification */
.notification {
  position: fixed;
  top: var(--space-6);
  right: var(--space-6);
  padding: var(--space-4) var(--space-6);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: var(--space-3);
  animation: slideIn 0.3s ease-out;
}

.notification.success {
  background: var(--success-100);
  color: var(--success-800);
  border: 1px solid var(--success-200);
}

.notification.error {
  background: var(--error-100);
  color: var(--error-800);
  border: 1px solid var(--error-200);
}

.notification.info {
  background: var(--primary-100);
  color: var(--primary-800);
  border: 1px solid var(--primary-200);
}

.notification-text {
  flex: 1;
  font-weight: var(--font-medium);
}

.notification-close {
  background: none;
  border: none;
  font-size: var(--text-xl);
  cursor: pointer;
  color: inherit;
  opacity: 0.7;
  transition: opacity var(--transition-fast);
}

.notification-close:hover {
  opacity: 1;
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

/* Modal Actions */
.modal-actions {
  display: flex;
  gap: var(--space-3);
  justify-content: flex-end;
  margin-top: var(--space-6);
  padding-top: var(--space-4);
  border-top: 1px solid var(--neutral-200);
}

.modal-actions .action-btn {
  min-width: 120px;
  justify-content: center;
}

.modal-actions .action-btn.danger {
  background: var(--error-600);
  color: var(--neutral-0);
}

.modal-actions .action-btn.danger:hover {
  background: var(--error-700);
}

.modal-actions .action-btn.secondary {
  background: var(--neutral-200);
  color: var(--neutral-700);
}

.modal-actions .action-btn.secondary:hover {
  background: var(--neutral-300);
}

/* Cancel Modal Styles */
.cancel-modal {
  max-width: 500px;
}

.cancel-confirmation {
  text-align: center;
}

.warning-icon {
  font-size: 48px;
  margin-bottom: var(--space-4);
}

.cancel-confirmation h4 {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-4);
}

.order-summary {
  background: var(--neutral-50);
  padding: var(--space-4);
  border-radius: var(--radius-lg);
  margin-bottom: var(--space-4);
  text-align: left;
}

.order-summary p {
  margin-bottom: var(--space-2);
  color: var(--neutral-600);
}

.order-summary p:last-child {
  margin-bottom: 0;
}

.warning-text {
  color: var(--error-600);
  font-size: var(--text-sm);
  margin-bottom: 0;
  line-height: 1.5;
}

.action-btn .icon {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .header-content {
    flex-direction: column;
    gap: var(--space-4);
    align-items: flex-start;
  }
  
  .header-stats {
    order: -1;
  }
  
  .filter-controls {
    flex-direction: column;
    gap: var(--space-3);
  }
  
  .search-group {
    min-width: auto;
  }
  
  .detail-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .page-content {
    padding: var(--space-4);
  }
  
  .order-header {
    flex-direction: column;
    gap: var(--space-3);
    align-items: flex-start;
  }
  
  .order-status {
    text-align: left;
  }
  
  .order-item {
    flex-direction: column;
    text-align: center;
  }
  
  .item-actions {
    flex-direction: row;
    justify-content: center;
  }
  
  .progress-steps {
    flex-direction: column;
    gap: var(--space-4);
  }
  
  .progress-steps::before {
    display: none;
  }
  
  .order-actions {
    flex-direction: column;
  }
  
  .action-btn {
    justify-content: center;
  }
  
  .modal-content {
    width: 95%;
  }
  
  .notification {
    top: var(--space-4);
    right: var(--space-4);
    left: var(--space-4);
  }
}
</style>
