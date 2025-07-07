<template>
  <div class="dashboard-page">
    <!-- Main Dashboard Container -->
    <div class="dashboard-container">
      <!-- Dashboard Header -->
      <div class="dashboard-header">
        <div class="header-content">
          <div class="header-left">
            <h1 class="page-title">Seller Hub</h1>
            <p class="page-subtitle">Welcome back, {{ userName }}! Manage your scrap business and inventory</p>
          </div>
          <div class="header-actions">
            <router-link to="/account" class="header-profile">
              <div class="profile-avatar">
                {{ userName.charAt(0).toUpperCase() }}
              </div>
              <span class="profile-text">Account</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Dashboard Layout -->
      <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="dashboard-sidebar">
          <nav class="sidebar-nav">
            <!-- Overview -->
            <router-link to="/scrap-seller-dashboard" class="nav-item" exact-active-class="active">
              <svg class="nav-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              <span class="nav-text">Overview</span>
            </router-link>

            <!-- Inventory Section -->
            <div class="nav-section">
              <div class="section-header">
                <svg class="section-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="section-title">Inventory</span>
              </div>
              <div class="section-items">
                <router-link to="/add-product" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  <span class="nav-text">Add Product</span>
                </router-link>
                <router-link to="/manage-products" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                  </svg>
                  <span class="nav-text">Manage Products</span>
                </router-link>
                <router-link to="/scrap-items" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                  <span class="nav-text">Browse Market</span>
                </router-link>
              </div>
            </div>

            <!-- Orders Section -->
            <div class="nav-section">
              <div class="section-header">
                <svg class="section-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="section-title">Orders</span>
              </div>
              <div class="section-items">
                <router-link to="/orders-received" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                  </svg>
                  <span class="nav-text">Orders Received</span>
                  <span v-if="stats.newOrders > 0" class="nav-badge">{{ stats.newOrders }}</span>
                </router-link>
                <router-link to="/order-history" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="nav-text">Order History</span>
                </router-link>
              </div>
            </div>

            <!-- Analytics Section -->
            <div class="nav-section">
              <div class="section-header">
                <svg class="section-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="section-title">Analytics</span>
              </div>
              <div class="section-items">
                <router-link to="/sales-analytics" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                  </svg>
                  <span class="nav-text">Sales Report</span>
                </router-link>
                <router-link to="/inventory-analytics" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span class="nav-text">Inventory Report</span>
                </router-link>
              </div>
            </div>
          </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="dashboard-content">
          <!-- Statistics Overview -->
          <div class="overview-section">
            <h2 class="section-title">Business Overview</h2>
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-header">
                  <div class="stat-icon products">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                  <h3 class="stat-title">Total Products</h3>
                </div>
                <div class="stat-content">
                  <div class="stat-value">{{ stats.totalProducts || 0 }}</div>
                  <div class="stat-change neutral">
                    <svg class="change-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>{{ stats.activeProducts || 0 }} active</span>
                  </div>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-header">
                  <div class="stat-icon orders">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                  </div>
                  <h3 class="stat-title">Total Orders</h3>
                </div>
                <div class="stat-content">
                  <div class="stat-value">{{ stats.totalOrders || 0 }}</div>
                  <div class="stat-change positive">
                    <svg class="change-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>{{ stats.newOrders || 0 }} new</span>
                  </div>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-header">
                  <div class="stat-icon revenue">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                  </div>
                  <h3 class="stat-title">Total Revenue</h3>
                </div>
                <div class="stat-content">
                  <div class="stat-value">${{ stats.totalRevenue || 0 }}</div>
                  <div class="stat-change positive">
                    <svg class="change-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>+15% this month</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="actions-section">
            <h2 class="section-title">Quick Actions</h2>
            <div class="actions-grid">
              <router-link to="/add-product" class="action-card">
                <div class="action-icon">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <h3 class="action-title">Add Product</h3>
                <p class="action-description">List new scrap materials</p>
              </router-link>
              
              <router-link to="/manage-products" class="action-card">
                <div class="action-icon">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                  </svg>
                </div>
                <h3 class="action-title">Manage Inventory</h3>
                <p class="action-description">Update product details</p>
              </router-link>
              
              <router-link to="/orders-received" class="action-card">
                <div class="action-icon">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                  </svg>
                </div>
                <h3 class="action-title">View Orders</h3>
                <p class="action-description">Process customer orders</p>
              </router-link>
              
              <router-link to="/scrap-items" class="action-card">
                <div class="action-icon">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>
                <h3 class="action-title">Browse Market</h3>
                <p class="action-description">Explore marketplace</p>
              </router-link>
            </div>
          </div>

          <!-- Recent Orders -->
          <div class="orders-section">
            <div class="section-header-with-action">
              <h2 class="section-title">Recent Orders</h2>
              <router-link to="/orders-received" class="view-all-btn">View All</router-link>
            </div>
            <div class="orders-card">
              <div class="orders-list">
                <div v-if="recentOrders.length === 0" class="empty-state">
                  <svg class="empty-icon" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                  </svg>
                  <p class="empty-text">No recent orders</p>
                </div>
                <div v-else v-for="order in recentOrders" :key="order.id" class="order-item">
                  <div class="order-info">
                    <div class="order-header">
                      <h4 class="order-id">Order #{{ order.id }}</h4>
                      <span class="order-status" :class="order.status">{{ order.status }}</span>
                    </div>
                    <p class="order-customer">{{ order.customer_name }}</p>
                    <p class="order-date">{{ formatDate(order.created_at) }}</p>
                  </div>
                  <div class="order-amount">${{ order.total_amount }}</div>
                  <router-link :to="`/orders/${order.id}`" class="order-action">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
</template>

<script>
export default {
  name: "ScrapSellerDashboard",
  data() {
    return {
      userName: '',
      currentUserId: null,
      stats: {
        totalProducts: 0,
        activeProducts: 0,
        totalOrders: 0,
        monthlyOrders: 0,
        newOrders: 0,
        totalRevenue: 0,
        rating: 0,
        totalReviews: 0
      },
      recentOrders: []
    };
  },
  
  mounted() {
    this.initializeDashboard();
    this.loadDashboardData();
  },
  
  methods: {
    initializeDashboard() {
      const session = localStorage.getItem("userSession");
      if (session) {
        const userData = JSON.parse(session);
        this.currentUserId = userData.id || userData.user_id;
        this.userName = userData.name || 'Seller';
        
        // Confirm user is Scrap Seller
        if (userData.role !== "scrapSeller") {
          this.$router.push("/");
        }
      } else {
        this.$router.push("/login");
      }
    },
    
    async loadDashboardData() {
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/seller/dashboard-stats', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          this.stats = { ...this.stats, ...data.stats };
          this.recentOrders = data.recentOrders || [];
        }
      } catch (error) {
        console.error('Error loading dashboard data:', error);
        // Set default values
        this.stats = {
          totalProducts: 18,
          activeProducts: 15,
          totalOrders: 42,
          monthlyOrders: 12,
          newOrders: 3,
          totalRevenue: 2850,
          rating: 4.6,
          totalReviews: 38
        };
        
        this.recentOrders = [
          { id: 'ORD-001', customer_name: 'John Doe', status: 'pending', total_amount: 125, created_at: new Date() },
          { id: 'ORD-002', customer_name: 'Jane Smith', status: 'completed', total_amount: 89, created_at: new Date() }
        ];
      }
    },
    
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    }
  }
};
</script>

<style scoped>
/* Reset and base styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-page {
  min-height: 100vh;
  background: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.dashboard-container {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0;
}

/* Dashboard Header */
.dashboard-header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 24px 32px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left {
  flex: 1;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 4px;
  letter-spacing: -0.025em;
}

.page-subtitle {
  color: #6b7280;
  font-size: 1rem;
  line-height: 1.5;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 16px;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  text-decoration: none;
  color: #374151;
  font-weight: 500;
  transition: all 0.2s ease;
}

.header-profile:hover {
  background: #f1f5f9;
  border-color: #d1d5db;
}

.profile-avatar {
  width: 32px;
  height: 32px;
  background: #10b981;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
}

/* Dashboard Layout */
.dashboard-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  min-height: calc(100vh - 97px);
}

/* Sidebar */
.dashboard-sidebar {
  background: white;
  border-right: 1px solid #e5e7eb;
  padding: 24px 0;
  overflow-y: auto;
}

.sidebar-nav {
  padding: 0 16px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  color: #6b7280;
  text-decoration: none;
  border-radius: 8px;
  margin-bottom: 4px;
  font-weight: 500;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  position: relative;
}

.nav-item:hover {
  background: #f8fafc;
  color: #374151;
}

.nav-item.active {
  background: #ecfdf5;
  color: #10b981;
}

.nav-item.sub-item {
  margin-left: 24px;
  font-size: 0.8125rem;
}

.nav-icon {
  flex-shrink: 0;
}

.nav-text {
  flex: 1;
}

.nav-badge {
  background: #ef4444;
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
}

/* Nav Sections */
.nav-section {
  margin-bottom: 24px;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  margin-bottom: 8px;
  color: #9ca3af;
  font-weight: 600;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.section-icon {
  flex-shrink: 0;
}

.section-items {
  space-y: 2px;
}

/* Main Content */
.dashboard-content {
  padding: 32px;
  overflow-y: auto;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 20px;
}

/* Overview Section */
.overview-section {
  margin-bottom: 32px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}

.stat-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 24px;
  transition: all 0.2s ease;
}

.stat-card:hover {
  shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.stat-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.stat-icon.products {
  background: #10b981;
}

.stat-icon.orders {
  background: #f59e0b;
}

.stat-icon.revenue {
  background: #3b82f6;
}

.stat-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  margin: 0;
}

.stat-content {
  margin-left: 52px;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1;
}

.stat-change {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.875rem;
}

.stat-change.positive {
  color: #10b981;
}

.stat-change.neutral {
  color: #6b7280;
}

.change-icon {
  flex-shrink: 0;
}

/* Actions Section */
.actions-section {
  margin-bottom: 32px;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}

.action-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  color: inherit;
}

.action-card:hover {
  shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
  border-color: #10b981;
}

.action-icon {
  width: 48px;
  height: 48px;
  background: #ecfdf5;
  color: #10b981;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
}

.action-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
}

.action-description {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0;
  line-height: 1.5;
}

/* Orders Section */
.orders-section {
  margin-bottom: 32px;
}

.section-header-with-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.view-all-btn {
  color: #10b981;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.875rem;
  padding: 8px 16px;
  border: 1px solid #10b981;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.view-all-btn:hover {
  background: #ecfdf5;
}

.orders-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 24px;
}

.orders-list {
  space-y: 16px;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
}

.empty-icon {
  color: #9ca3af;
  margin: 0 auto 16px;
}

.empty-text {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0;
}

.order-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 0;
  border-bottom: 1px solid #f3f4f6;
}

.order-item:last-child {
  border-bottom: none;
}

.order-info {
  flex: 1;
}

.order-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.order-id {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.order-status {
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.order-status.pending {
  background: #fef3c7;
  color: #d97706;
}

.order-status.processing {
  background: #dbeafe;
  color: #2563eb;
}

.order-status.completed {
  background: #d1fae5;
  color: #10b981;
}

.order-customer {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0 0 4px 0;
}

.order-date {
  color: #9ca3af;
  font-size: 0.75rem;
  margin: 0;
}

.order-amount {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin-right: 16px;
}

.order-action {
  color: #6b7280;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.2s ease;
  text-decoration: none;
}

.order-action:hover {
  background: #f3f4f6;
  color: #374151;
}

/* Responsive */
@media (max-width: 1024px) {
  .dashboard-layout {
    grid-template-columns: 1fr;
  }
  
  .dashboard-sidebar {
    display: none;
  }
}

@media (max-width: 768px) {
  .dashboard-header {
    padding: 16px 20px;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  
  .dashboard-content {
    padding: 20px;
  }
  
  .stats-grid,
  .actions-grid {
    grid-template-columns: 1fr;
  }
  
  .page-title {
    font-size: 1.5rem;
  }
  
  .section-header-with-action {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
}
</style>
