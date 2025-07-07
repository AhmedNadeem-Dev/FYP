<template>
  <div class="dashboard-page">
    <!-- Main Dashboard Container -->
    <div class="dashboard-container">
      <!-- Dashboard Header -->
      <div class="dashboard-header">
        <div class="header-content">
          <div class="header-left">
            <h1 class="page-title">Artist Studio</h1>
            <p class="page-subtitle">Welcome back, {{ userName }}! Manage your creative projects and business</p>
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
            <router-link to="/artist-dashboard" class="nav-item" exact-active-class="active">
              <svg class="nav-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              <span class="nav-text">Overview</span>
            </router-link>

            <!-- Projects Section -->
            <div class="nav-section">
              <div class="section-header">
                <svg class="section-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span class="section-title">Projects</span>
              </div>
              <div class="section-items">
                <router-link to="/projects/create" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  <span class="nav-text">Create Project</span>
                </router-link>
                <router-link to="/projects" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  <span class="nav-text">My Projects</span>
                </router-link>
                <router-link to="/completed-projects" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="nav-text">Completed</span>
                </router-link>
              </div>
            </div>

            <!-- Products Section -->
            <div class="nav-section">
              <div class="section-header">
                <svg class="section-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="section-title">Products</span>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  <span class="nav-text">Manage Products</span>
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

            <!-- Collaboration Section -->
            <div class="nav-section">
              <div class="section-header">
                <svg class="section-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="section-title">Collaboration</span>
              </div>
              <div class="section-items">
                <router-link to="/my-sent-invitations" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                  </svg>
                  <span class="nav-text">Sent Invites</span>
                </router-link>
                <router-link to="/my-invitations" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  <span class="nav-text">Received Invites</span>
                  <span v-if="stats.newInvitations > 0" class="nav-badge">{{ stats.newInvitations }}</span>
                </router-link>
              </div>
            </div>

            <!-- Custom Requests Section -->
            <div class="nav-section">
              <div class="section-header">
                <svg class="section-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span class="section-title">Custom Requests</span>
              </div>
              <div class="section-items">
                <router-link to="/artist-view-custom-requests" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <span class="nav-text">View All</span>
                  <span v-if="stats.newRequests > 0" class="nav-badge">{{ stats.newRequests }}</span>
                </router-link>
                <router-link to="/view-accepted-requests" class="nav-item sub-item">
                  <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="nav-text">Accepted</span>
                </router-link>
              </div>
            </div>
          </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="dashboard-content">
          <!-- Statistics Overview -->
          <div class="overview-section">
            <h2 class="section-title">Your Studio Overview</h2>
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-header">
                  <div class="stat-icon projects">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                  </div>
                  <h3 class="stat-title">Total Projects</h3>
                </div>
                <div class="stat-content">
                  <div class="stat-value">{{ stats.totalProjects || 0 }}</div>
                  <div class="stat-change positive">
                    <svg class="change-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>{{ stats.activeProjects || 0 }} active</span>
                  </div>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-header">
                  <div class="stat-icon products">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                  <h3 class="stat-title">Products Listed</h3>
                </div>
                <div class="stat-content">
                  <div class="stat-value">{{ stats.totalProducts || 0 }}</div>
                  <div class="stat-change neutral">
                    <svg class="change-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>{{ stats.totalOrders || 0 }} orders</span>
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
                    <span>{{ stats.rating || 0 }}/5 rating</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="actions-section">
            <h2 class="section-title">Quick Actions</h2>
            <div class="actions-grid">
              <router-link to="/projects/create" class="action-card">
                <div class="action-icon">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                  </svg>
                </div>
                <h3 class="action-title">New Project</h3>
                <p class="action-description">Start a creative project</p>
              </router-link>
              
              <router-link to="/add-product" class="action-card">
                <div class="action-icon">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <h3 class="action-title">Add Product</h3>
                <p class="action-description">List a new product</p>
              </router-link>
              
              <router-link to="/portfolio" class="action-card">
                <div class="action-icon">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <h3 class="action-title">Portfolio</h3>
                <p class="action-description">Showcase your work</p>
              </router-link>
              
              <router-link to="/artist-view-custom-requests" class="action-card">
                <div class="action-icon">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                  </svg>
                </div>
                <h3 class="action-title">View Requests</h3>
                <p class="action-description">Check custom requests</p>
              </router-link>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="activity-section">
            <h2 class="section-title">Recent Activity</h2>
            <div class="activity-card">
              <div class="activity-list">
                <div v-if="recentActivity.length === 0" class="empty-state">
                  <svg class="empty-icon" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                  <p class="empty-text">No recent activity</p>
                </div>
                <div v-else v-for="activity in recentActivity" :key="activity.id" class="activity-item">
                  <div class="activity-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div class="activity-content">
                    <p class="activity-text">{{ activity.message || activity.description }}</p>
                    <span class="activity-time">{{ formatTime(activity.created_at || activity.timestamp) }}</span>
                  </div>
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
  name: "ArtistDashboard",
  data() {
    return {
      userName: '',
      currentUserId: null,
      stats: {
        totalProjects: 0,
        totalProducts: 0,
        totalOrders: 0,
        newOrders: 0,
        newInvitations: 0,
        newRequests: 0,
        totalRevenue: 0,
        activeProjects: 0,
        rating: 0,
        totalReviews: 0
      },
      recentActivity: []
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
        this.userName = userData.name || 'Artist';
        
        // Confirm user is Artist
        if (userData.role !== "artist") {
          this.$router.push("/");
        }
      } else {
        this.$router.push("/login");
      }
    },
    
    async loadDashboardData() {
      try {
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/artist/dashboard-stats', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          this.stats = { ...this.stats, ...data.stats };
          this.recentActivity = data.recentActivity || [];
        }
      } catch (error) {
        console.error('Error loading dashboard data:', error);
        // Set default values
        this.stats = {
          totalProjects: 5,
          totalProducts: 12,
          totalOrders: 8,
          newOrders: 2,
          newInvitations: 1,
          newRequests: 3,
          totalRevenue: 1250,
          activeProjects: 3,
          rating: 4.8,
          totalReviews: 24
        };
      }
    },
    
    formatTime(timestamp) {
      if (!timestamp) return '';
      const date = new Date(timestamp);
      const now = new Date();
      const diffMs = now - date;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMins / 60);
      const diffDays = Math.floor(diffHours / 24);
      
      if (diffMins < 60) {
        return `${diffMins} minutes ago`;
      } else if (diffHours < 24) {
        return `${diffHours} hours ago`;
      } else {
        return `${diffDays} days ago`;
      }
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
  background: #6366f1;
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
  background: #eff6ff;
  color: #2563eb;
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

.stat-icon.projects {
  background: #3b82f6;
}

.stat-icon.products {
  background: #10b981;
}

.stat-icon.revenue {
  background: #f59e0b;
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
  border-color: #6366f1;
}

.action-icon {
  width: 48px;
  height: 48px;
  background: #eff6ff;
  color: #2563eb;
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

/* Activity Section */
.activity-section {
  margin-bottom: 32px;
}

.activity-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 24px;
}

.activity-list {
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

.activity-item {
  display: flex;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f3f4f6;
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-icon {
  width: 32px;
  height: 32px;
  background: #f3f4f6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  flex-shrink: 0;
}

.activity-content {
  flex: 1;
}

.activity-text {
  color: #374151;
  font-size: 0.875rem;
  margin: 0 0 4px 0;
}

.activity-time {
  color: #9ca3af;
  font-size: 0.75rem;
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
}
</style>
