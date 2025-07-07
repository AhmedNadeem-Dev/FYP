<template>
  <div class="account-page">
    <!-- Import Design System -->
    <link rel="stylesheet" href="/css/design-system.css">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <h1 class="page-title">Account Settings</h1>
        <p class="page-subtitle">Manage your profile and account preferences</p>
      </div>
    </div>

    <!-- Account Content -->
    <div class="container">
      <div class="account-layout">
        <!-- Sidebar Navigation -->
        <aside class="account-sidebar">
          <nav class="account-nav">
            <button 
              @click="activeTab = 'profile'"
              :class="['nav-item', { active: activeTab === 'profile' }]"
            >
              <i class="icon">👤</i>
              Profile Information
            </button>
            <button 
              @click="activeTab = 'password'"
              :class="['nav-item', { active: activeTab === 'password' }]"
            >
              <i class="icon">🔒</i>
              Change Password
            </button>
            <button 
              @click="activeTab = 'orders'"
              :class="['nav-item', { active: activeTab === 'orders' }]"
            >
              <i class="icon">📦</i>
              Order History
            </button>
            <button 
              v-if="userRole === 'scrapSeller' || userRole === 'artist'"
              @click="activeTab = 'received-orders'"
              :class="['nav-item', { active: activeTab === 'received-orders' }]"
            >
              <i class="icon">📋</i>
              Orders Received
            </button>
          </nav>
        </aside>

        <!-- Main Content -->
        <main class="account-main">
          <!-- Profile Information Tab -->
          <div v-if="activeTab === 'profile'" class="tab-content">
            <div class="card">
              <div class="card-header">
                <h2 class="card-title">Profile Information</h2>
                <p class="card-subtitle">View and manage your personal information</p>
              </div>
              <div class="card-body">
                <!-- Profile Display -->
                <div v-if="!editingProfile" class="profile-display">
                  <div class="profile-avatar">
                    <div class="avatar-circle">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                  </div>
                  
                  <div class="profile-info">
                    <div class="info-group">
                      <label class="info-label">Full Name</label>
                      <div class="info-value">{{ user.name }}</div>
                    </div>
                    
                    <div class="info-group">
                      <label class="info-label">Email Address</label>
                      <div class="info-value">{{ user.email }}</div>
                    </div>
                    
                    <div class="info-group">
                      <label class="info-label">Phone Number</label>
                      <div class="info-value">{{ user.phone || 'Not provided' }}</div>
                    </div>
                    
                    <div class="info-group">
                      <label class="info-label">Account Type</label>
                      <div class="info-value">
                        <span :class="['badge', 'badge-' + getRoleBadgeType(user.role)]">
                          {{ formatRole(user.role) }}
                        </span>
                      </div>
                    </div>
                    
                    <div class="info-group">
                      <label class="info-label">Member Since</label>
                      <div class="info-value">{{ formatDate(user.created_at) }}</div>
                    </div>
                  </div>
                  
                  <div class="profile-actions">
                    <button @click="editingProfile = true" class="btn btn-primary">
                      <i class="icon">✏️</i>
                      Edit Profile
                    </button>
                  </div>
                </div>

                <!-- Profile Edit Form -->
                <div v-else class="profile-edit">
                  <form @submit.prevent="updateProfile">
                    <div class="form-group">
                      <label class="form-label">Full Name</label>
                      <input 
                        type="text" 
                        v-model="profileForm.name" 
                        class="form-input"
                        required
                      />
                    </div>
                    
                    <div class="form-group">
                      <label class="form-label">Email Address</label>
                      <input 
                        type="email" 
                        v-model="profileForm.email" 
                        class="form-input"
                        required
                      />
                    </div>
                    
                    <div class="form-group">
                      <label class="form-label">Phone Number</label>
                      <input 
                        type="tel" 
                        v-model="profileForm.phone" 
                        class="form-input"
                      />
                    </div>
                    
                    <div class="form-actions">
                      <button type="submit" class="btn btn-primary" :disabled="updating">
                        <i class="icon">💾</i>
                        {{ updating ? 'Saving...' : 'Save Changes' }}
                      </button>
                      <button 
                        type="button" 
                        @click="cancelEdit" 
                        class="btn btn-secondary"
                      >
                        Cancel
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Password Change Tab -->
          <div v-if="activeTab === 'password'" class="tab-content">
            <div class="card">
              <div class="card-header">
                <h2 class="card-title">Change Password</h2>
                <p class="card-subtitle">Update your account password</p>
              </div>
              <div class="card-body">
                <form @submit.prevent="changePassword">
                  <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input 
                      type="password" 
                      v-model="passwordForm.currentPassword" 
                      class="form-input"
                      required
                    />
                  </div>
                  
                  <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input 
                      type="password" 
                      v-model="passwordForm.newPassword" 
                      class="form-input"
                      required
                      minlength="8"
                    />
                    <small class="form-help">Password must be at least 8 characters long</small>
                  </div>
                  
                  <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input 
                      type="password" 
                      v-model="passwordForm.confirmPassword" 
                      class="form-input"
                      required
                    />
                  </div>
                  
                  <div v-if="passwordError" class="error-message">
                    {{ passwordError }}
                  </div>
                  
                  <div class="form-actions">
                    <button type="submit" class="btn btn-primary" :disabled="changingPassword">
                      <i class="icon">🔐</i>
                      {{ changingPassword ? 'Changing...' : 'Change Password' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Order History Tab -->
          <div v-if="activeTab === 'orders'" class="tab-content">
            <div class="card">
              <div class="card-header">
                <h2 class="card-title">Order History</h2>
                <p class="card-subtitle">View your past orders and purchases</p>
              </div>
              <div class="card-body">
                <!-- Loading State -->
                <div v-if="loadingOrders" class="loading-container">
                  <div class="loading-spinner"></div>
                  <p class="loading-text">Loading orders...</p>
                </div>
                
                <!-- Orders List -->
                <div v-else-if="orders.length > 0" class="orders-list">
                  <div 
                    v-for="order in orders" 
                    :key="order.order_id"
                    class="order-card"
                  >
                    <div class="order-header">
                      <div class="order-info">
                        <h3 class="order-id">Order #{{ order.order_id }}</h3>
                        <p class="order-date">{{ formatDate(order.created_at) }}</p>
                      </div>
                      <div class="order-status">
                        <span :class="['badge', 'badge-' + getStatusBadgeType(order.status)]">
                          {{ order.status }}
                        </span>
                      </div>
                    </div>
                    
                    <div class="order-items">
                      <div 
                        v-for="item in order.items" 
                        :key="item.id"
                        class="order-item"
                      >
                        <img 
                          :src="item.image_path || '/images/placeholder.jpg'" 
                          :alt="item.name"
                          class="item-image"
                          @error="handleImageError"
                        />
                        <div class="item-details">
                          <h4 class="item-name">{{ item.name }}</h4>
                          <p class="item-quantity">Quantity: {{ item.quantity }}</p>
                        </div>
                        <div class="item-price">${{ item.price }}</div>
                      </div>
                    </div>
                    
                    <div class="order-footer">
                      <div class="order-total">
                        <strong>Total: ${{ order.total_amount }}</strong>
                      </div>
                      <button 
                        @click="viewOrderDetails(order.order_id)"
                        class="btn btn-secondary btn-sm"
                      >
                        View Details
                      </button>
                    </div>
                  </div>
                </div>
                
                <!-- No Orders -->
                <div v-else class="empty-state">
                  <i class="empty-icon">📦</i>
                  <h3 class="empty-title">No orders yet</h3>
                  <p class="empty-description">
                    You haven't placed any orders yet. Start shopping to see your order history here.
                  </p>
                  <router-link to="/scrap-items" class="btn btn-primary">
                    Start Shopping
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Orders Received Tab (for sellers/artists) -->
          <div v-if="activeTab === 'received-orders'" class="tab-content">
            <div class="card">
              <div class="card-header">
                <h2 class="card-title">Orders Received</h2>
                <p class="card-subtitle">Manage orders for your products</p>
              </div>
              <div class="card-body">
                <!-- Loading State -->
                <div v-if="loadingReceivedOrders" class="loading-container">
                  <div class="loading-spinner"></div>
                  <p class="loading-text">Loading received orders...</p>
                </div>
                
                <!-- Received Orders List -->
                <div v-else-if="receivedOrders.length > 0" class="orders-list">
                  <div 
                    v-for="order in receivedOrders" 
                    :key="order.order_id"
                    class="order-card"
                  >
                    <div class="order-header">
                      <div class="order-info">
                        <h3 class="order-id">Order #{{ order.order_id }}</h3>
                        <p class="order-customer">Customer: {{ order.customer_name }}</p>
                        <p class="order-date">{{ formatDate(order.created_at) }}</p>
                      </div>
                      <div class="order-status">
                        <span :class="['badge', 'badge-' + getStatusBadgeType(order.status)]">
                          {{ order.status }}
                        </span>
                      </div>
                    </div>
                    
                    <div class="order-items">
                      <div 
                        v-for="item in order.items" 
                        :key="item.id"
                        class="order-item"
                      >
                        <img 
                          :src="item.image_path || '/images/placeholder.jpg'" 
                          :alt="item.name"
                          class="item-image"
                          @error="handleImageError"
                        />
                        <div class="item-details">
                          <h4 class="item-name">{{ item.name }}</h4>
                          <p class="item-quantity">Quantity: {{ item.quantity }}</p>
                          <p class="item-status">Status: {{ item.status || 'pending' }}</p>
                        </div>
                        <div class="item-price">${{ item.price }}</div>
                      </div>
                    </div>
                    
                    <div class="order-footer">
                      <div class="order-total">
                        <strong>Total: ${{ order.total_amount }}</strong>
                      </div>
                      <div class="order-actions">
                        <button 
                          @click="updateOrderStatus(order.order_id, 'processing')"
                          class="btn btn-primary btn-sm"
                          v-if="order.status === 'pending'"
                        >
                          Accept Order
                        </button>
                        <button 
                          @click="updateOrderStatus(order.order_id, 'shipped')"
                          class="btn btn-success btn-sm"
                          v-if="order.status === 'processing'"
                        >
                          Mark as Shipped
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- No Received Orders -->
                <div v-else class="empty-state">
                  <i class="empty-icon">📋</i>
                  <h3 class="empty-title">No orders received</h3>
                  <p class="empty-description">
                    You haven't received any orders yet. Keep listing great products!
                  </p>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- Success Notifications -->
    <div v-if="showNotification" class="notification notification-success">
      <i class="notification-icon">✅</i>
      <span>{{ notificationMessage }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Account',
  data() {
    return {
      activeTab: 'profile',
      user: {},
      editingProfile: false,
      updating: false,
      changingPassword: false,
      loadingOrders: false,
      loadingReceivedOrders: false,
      orders: [],
      receivedOrders: [],
      
      // Forms
      profileForm: {
        name: '',
        email: '',
        phone: ''
      },
      passwordForm: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      
      // Error handling
      passwordError: '',
      
      // Notifications
      showNotification: false,
      notificationMessage: ''
    };
  },
  
  computed: {
    userRole() {
      return this.user.role || '';
    }
  },
  
  mounted() {
    this.loadUserData();
    this.fetchOrders();
    if (this.userRole === 'scrapSeller' || this.userRole === 'artist') {
      this.fetchReceivedOrders();
    }
  },
  
  methods: {
    loadUserData() {
      const userSession = localStorage.getItem('userSession');
      if (userSession) {
        this.user = JSON.parse(userSession);
        this.profileForm = {
          name: this.user.name || '',
          email: this.user.email || '',
          phone: this.user.phone || ''
        };
      }
    },
    
    async updateProfile() {
      this.updating = true;
      
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/profile/update', {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(this.profileForm)
        });
        
        if (response.ok) {
          const updatedUser = await response.json();
          this.user = { ...this.user, ...updatedUser };
          localStorage.setItem('userSession', JSON.stringify(this.user));
          
          this.editingProfile = false;
          this.showSuccessNotification('Profile updated successfully!');
        }
      } catch (error) {
        console.error('Error updating profile:', error);
      } finally {
        this.updating = false;
      }
    },
    
    cancelEdit() {
      this.editingProfile = false;
      this.profileForm = {
        name: this.user.name || '',
        email: this.user.email || '',
        phone: this.user.phone || ''
      };
    },
    
    async changePassword() {
      this.passwordError = '';
      
      if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
        this.passwordError = 'New passwords do not match';
        return;
      }
      
      this.changingPassword = true;
      
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/change-password', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            currentPassword: this.passwordForm.currentPassword,
            newPassword: this.passwordForm.newPassword,
            confirmPassword: this.passwordForm.confirmPassword
          })
        });
        
        if (response.ok) {
          this.passwordForm = {
            currentPassword: '',
            newPassword: '',
            confirmPassword: ''
          };
          this.showSuccessNotification('Password changed successfully!');
        } else {
          const data = await response.json();
          this.passwordError = data.message || 'Failed to change password';
        }
      } catch (error) {
        console.error('Error changing password:', error);
        this.passwordError = 'An error occurred while changing password';
      } finally {
        this.changingPassword = false;
      }
    },
    
    async fetchOrders() {
      this.loadingOrders = true;
      
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/orders', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          this.orders = await response.json();
        }
      } catch (error) {
        console.error('Error fetching orders:', error);
      } finally {
        this.loadingOrders = false;
      }
    },
    
    async fetchReceivedOrders() {
      this.loadingReceivedOrders = true;
      
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/orders/seller', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          this.receivedOrders = await response.json();
        }
      } catch (error) {
        console.error('Error fetching received orders:', error);
      } finally {
        this.loadingReceivedOrders = false;
      }
    },
    
    async updateOrderStatus(orderId, status) {
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch(`/api/orders/${orderId}/status`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ status })
        });
        
        if (response.ok) {
          this.fetchReceivedOrders();
          this.showSuccessNotification('Order status updated successfully!');
        }
      } catch (error) {
        console.error('Error updating order status:', error);
      }
    },
    
    viewOrderDetails(orderId) {
      this.$router.push(`/order-history/${orderId}`);
    },
    
    formatRole(role) {
      const roleMap = {
        artist: 'Artist',
        scrapSeller: 'Scrap Seller',
        general: 'General User',
        admin: 'Administrator'
      };
      return roleMap[role] || role;
    },
    
    getRoleBadgeType(role) {
      const typeMap = {
        artist: 'primary',
        scrapSeller: 'success',
        general: 'secondary',
        admin: 'warning'
      };
      return typeMap[role] || 'secondary';
    },
    
    getStatusBadgeType(status) {
      const typeMap = {
        pending: 'warning',
        processing: 'primary',
        shipped: 'success',
        delivered: 'success',
        cancelled: 'error'
      };
      return typeMap[status] || 'secondary';
    },
    
    formatDate(dateString) {
      if (!dateString) return 'Unknown';
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    
    handleImageError(event) {
      event.target.src = '/images/placeholder.jpg';
    },
    
    showSuccessNotification(message) {
      this.notificationMessage = message;
      this.showNotification = true;
      
      setTimeout(() => {
        this.showNotification = false;
      }, 3000);
    }
  }
};
</script>

<style scoped>
/* Import design system */
@import url('../../css/design-system.css');

.account-page {
  min-height: 100vh;
  background-color: var(--neutral-50);
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 100%);
  color: var(--neutral-0);
  padding: var(--space-12) 0;
  text-align: center;
}

.page-title {
  font-size: var(--text-4xl);
  font-weight: var(--font-bold);
  margin-bottom: var(--space-4);
}

.page-subtitle {
  font-size: var(--text-lg);
  opacity: 0.9;
}

/* Account Layout */
.account-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: var(--space-8);
  padding: var(--space-8) 0;
}

/* Sidebar */
.account-sidebar {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
  height: fit-content;
  position: sticky;
  top: var(--space-8);
}

.account-nav {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.nav-item {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-3) var(--space-4);
  background: transparent;
  border: none;
  border-radius: var(--radius-lg);
  color: var(--neutral-600);
  font-size: var(--text-base);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--transition-fast);
  text-align: left;
  width: 100%;
}

.nav-item:hover {
  background: var(--primary-50);
  color: var(--primary-600);
}

.nav-item.active {
  background: var(--primary-600);
  color: var(--neutral-0);
}

.nav-item .icon {
  font-size: var(--text-lg);
}

/* Main Content */
.account-main {
  min-height: 600px;
}

.tab-content {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Profile Display */
.profile-display {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: var(--space-8);
  align-items: start;
}

.profile-avatar {
  display: flex;
  justify-content: center;
}

.avatar-circle {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-500) 0%, var(--secondary-500) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--text-4xl);
  font-weight: var(--font-bold);
  color: var(--neutral-0);
  box-shadow: var(--shadow-lg);
}

.profile-info {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.info-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.info-label {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--neutral-500);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: var(--text-base);
  color: var(--neutral-800);
  font-weight: var(--font-medium);
}

.profile-actions {
  display: flex;
  align-items: flex-start;
}

/* Forms */
.form-group {
  margin-bottom: var(--space-6);
}

.form-help {
  display: block;
  margin-top: var(--space-1);
  font-size: var(--text-sm);
  color: var(--neutral-500);
}

.form-actions {
  display: flex;
  gap: var(--space-3);
  margin-top: var(--space-8);
}

.error-message {
  padding: var(--space-3) var(--space-4);
  background: var(--error-50);
  border: 1px solid var(--error-200);
  border-radius: var(--radius-lg);
  color: var(--error-700);
  font-size: var(--text-sm);
  margin-bottom: var(--space-4);
}

/* Orders */
.orders-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
}

.order-card {
  background: var(--neutral-0);
  border: 1px solid var(--neutral-200);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
  transition: all var(--transition-base);
}

.order-card:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: var(--space-4);
  padding-bottom: var(--space-4);
  border-bottom: 1px solid var(--neutral-200);
}

.order-id {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-1);
}

.order-date,
.order-customer {
  font-size: var(--text-sm);
  color: var(--neutral-600);
  margin-bottom: var(--space-1);
}

.order-items {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  margin-bottom: var(--space-4);
}

.order-item {
  display: flex;
  align-items: center;
  gap: var(--space-4);
  padding: var(--space-3);
  background: var(--neutral-50);
  border-radius: var(--radius-lg);
}

.item-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--radius-md);
  flex-shrink: 0;
}

.item-details {
  flex: 1;
}

.item-name {
  font-size: var(--text-base);
  font-weight: var(--font-medium);
  color: var(--neutral-800);
  margin-bottom: var(--space-1);
}

.item-quantity,
.item-status {
  font-size: var(--text-sm);
  color: var(--neutral-600);
}

.item-price {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--primary-600);
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: var(--space-4);
  border-top: 1px solid var(--neutral-200);
}

.order-total {
  font-size: var(--text-lg);
  color: var(--neutral-800);
}

.order-actions {
  display: flex;
  gap: var(--space-2);
}

/* Loading State */
.loading-container {
  text-align: center;
  padding: var(--space-12);
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid var(--neutral-200);
  border-top: 4px solid var(--primary-600);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto var(--space-4);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  font-size: var(--text-lg);
  color: var(--neutral-600);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: var(--space-16);
}

.empty-icon {
  font-size: var(--text-6xl);
  margin-bottom: var(--space-4);
}

.empty-title {
  font-size: var(--text-2xl);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-3);
}

.empty-description {
  font-size: var(--text-base);
  color: var(--neutral-600);
  margin-bottom: var(--space-6);
  line-height: var(--leading-relaxed);
}

/* Notifications */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-4) var(--space-6);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  z-index: var(--z-toast);
  animation: slideIn 0.3s ease-out;
}

.notification-success {
  background: var(--success-50);
  border: 1px solid var(--success-200);
  color: var(--success-700);
}

.notification-icon {
  font-size: var(--text-lg);
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
@media (max-width: 1024px) {
  .account-layout {
    grid-template-columns: 1fr;
    gap: var(--space-6);
  }
  
  .account-sidebar {
    position: static;
  }
  
  .account-nav {
    flex-direction: row;
    overflow-x: auto;
    padding-bottom: var(--space-2);
  }
  
  .nav-item {
    white-space: nowrap;
    min-width: fit-content;
  }
}

@media (max-width: 768px) {
  .page-title {
    font-size: var(--text-3xl);
  }
  
  .profile-display {
    grid-template-columns: 1fr;
    text-align: center;
    gap: var(--space-6);
  }
  
  .avatar-circle {
    width: 100px;
    height: 100px;
    font-size: var(--text-3xl);
  }
  
  .order-header {
    flex-direction: column;
    gap: var(--space-3);
    align-items: flex-start;
  }
  
  .order-footer {
    flex-direction: column;
    gap: var(--space-3);
    align-items: flex-start;
  }
  
  .order-item {
    flex-direction: column;
    text-align: center;
    gap: var(--space-3);
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .form-actions .btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .account-nav {
    flex-direction: column;
  }
  
  .nav-item {
    width: 100%;
  }
  
  .container {
    padding: 0 var(--space-3);
  }
}
</style> 
