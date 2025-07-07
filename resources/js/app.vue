<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <div id="app">
    <header class="clean-header">
      <nav class="clean-navbar">
        <div class="navbar-container">
          <!-- Brand Logo -->
          <div class="navbar-brand">
            <router-link to="/" class="brand-logo">
              Refurb
            </router-link>
          </div>

          <!-- Center Navigation -->
          <div class="navbar-nav">
            <router-link to="/" class="nav-link" active-class="active">
              Home
            </router-link>
            <router-link to="/scrap-items" class="nav-link" active-class="active">
              Explore
            </router-link>
            <router-link to="/projects" class="nav-link" active-class="active">
              Projects
            </router-link>
          </div>

          <!-- Right Actions -->
          <div class="navbar-actions">
            <!-- Cart -->
            <div class="cart-section">
              <router-link v-if="isLoggedIn" to="/cart" class="cart-link">
                <svg class="cart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                Cart
                <span v-if="cartItemCount > 0" class="cart-count">{{ cartItemCount }}</span>
              </router-link>
              <button v-else @click="showCartLoginModal" class="cart-link cart-button">
                <svg class="cart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                Cart
                <span v-if="cartItemCount > 0" class="cart-count">{{ cartItemCount }}</span>
              </button>
            </div>

            <!-- User Profile / Auth -->
            <div class="user-section">
              <!-- Not Logged In -->
              <template v-if="!isLoggedIn">
                <router-link to="/login" class="auth-link login-link">
                  Sign In
                </router-link>
                <router-link to="/signup" class="auth-link signup-link">
                  Sign Up
                </router-link>
              </template>

              <!-- Logged In -->
              <div v-else class="user-profile" @click="toggleDropdown">
                <div class="profile-avatar">
                  {{ userName.charAt(0).toUpperCase() }}
                </div>
                <div class="profile-info">
                  <span class="profile-name">{{ userName }}</span>
                  <span class="profile-role">{{ userRole === 'artist' ? 'Artist' : userRole === 'scrapSeller' ? 'Seller' : userRole === 'admin' ? 'Admin' : 'User' }}</span>
                </div>
                <svg class="dropdown-icon" :class="{ rotated: isDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>

                <!-- User Dropdown -->
                <div class="user-dropdown" :class="{ show: isDropdownOpen }">
                  <div class="dropdown-content">
                    <router-link :to="dashboardRoute" class="dropdown-link" @click="closeDropdown">
                      <span>Dashboard</span>
                    </router-link>
                    <router-link to="/wishlist" class="dropdown-link" @click="closeDropdown">
                      <span>My Wishlist</span>
                    </router-link>
                    <router-link to="/order-history" class="dropdown-link" @click="closeDropdown">
                      <span>Orders</span>
                    </router-link>
                    <div class="dropdown-divider"></div>
                    <button @click="logout" class="dropdown-link logout-link">
                      <span>Logout</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mobile Menu Toggle -->
            <button 
              class="mobile-toggle" 
              :class="{ active: isMobileMenuOpen }"
              @click="toggleMobileMenu"
            >
              <span></span>
              <span></span>
              <span></span>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" :class="{ show: isMobileMenuOpen }">
          <div class="mobile-nav-links">
            <router-link to="/" class="mobile-nav-item" @click="closeMobileMenu">
              <svg class="mobile-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              <span>Home</span>
            </router-link>
            <router-link to="/scrap-items" class="mobile-nav-item" @click="closeMobileMenu">
              <svg class="mobile-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <span>Explore</span>
            </router-link>
            <router-link to="/projects" class="mobile-nav-item" @click="closeMobileMenu">
              <svg class="mobile-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
              <span>Projects</span>
            </router-link>
            <router-link v-if="isLoggedIn" to="/cart" class="mobile-nav-item" @click="closeMobileMenu">
              <svg class="mobile-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
              </svg>
              <span>Cart</span>
              <span v-if="cartItemCount > 0" class="mobile-cart-badge">{{ cartItemCount }}</span>
            </router-link>
            <button v-else @click="showCartLoginModal(); closeMobileMenu();" class="mobile-nav-item mobile-cart-button">
              <svg class="mobile-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
              </svg>
              <span>Cart</span>
              <span v-if="cartItemCount > 0" class="mobile-cart-badge">{{ cartItemCount }}</span>
            </button>
          </div>
          
          <div class="mobile-auth-section">
            <template v-if="!isLoggedIn">
              <router-link to="/login" class="mobile-auth-button" @click="closeMobileMenu">
                <svg class="mobile-auth-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Login
              </router-link>
              <router-link to="/signup" class="mobile-auth-button primary" @click="closeMobileMenu">
                <svg class="mobile-auth-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Sign Up
              </router-link>
            </template>
            <template v-else>
              <div class="mobile-user-info">
                <div class="mobile-avatar">{{ userName.charAt(0).toUpperCase() }}</div>
                <span class="mobile-username">{{ userName }}</span>
              </div>
              <router-link :to="dashboardRoute" class="mobile-nav-item" @click="closeMobileMenu">
                <svg class="mobile-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Dashboard
              </router-link>
              <router-link to="/wishlist" class="mobile-nav-item" @click="closeMobileMenu">
                <svg class="mobile-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                Wishlist
              </router-link>
              <button @click="logout" class="mobile-logout-button">
                <svg class="mobile-logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
              </button>
            </template>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main content area -->
    <main class="main-content">
      <router-view></router-view>
    </main>
    
    <!-- Footer - will appear on all pages -->
    <footer class="site-footer">
      <div class="footer-container">
        <div class="footer-content">
          <!-- Brand Section -->
          <div class="footer-brand">
            <h3 class="footer-brand-title">Refurb</h3>
            <p class="footer-brand-description">
              Connecting artists, scrap sellers, and buyers to create a sustainable creative ecosystem.
            </p>
            <div class="footer-social">
              <a href="#" class="social-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                </svg>
              </a>
              <a href="#" class="social-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                </svg>
              </a>
              <a href="#" class="social-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.219-.359-1.219c0-1.142.662-1.995 1.482-1.995.699 0 1.037.524 1.037 1.155 0 .703-.449 1.754-.68 2.726-.194.819.41 1.487 1.219 1.487 1.463 0 2.587-1.543 2.587-3.771 0-1.972-1.415-3.353-3.437-3.353-2.341 0-3.717 1.757-3.717 3.57 0 .707.272 1.467.613 1.881.067.081.077.152.057.235-.061.254-.196.798-.223.908-.035.146-.116.177-.268.107-1.001-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.287-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.924l-.498 1.902c-.181.695-.669 1.566-.995 2.097A12.013 12.013 0 0 0 12.017 24c6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001.012.001z"/>
                </svg>
              </a>
            </div>
          </div>

          <!-- Quick Links Section -->
          <div class="footer-links-section">
            <h4 class="footer-section-title">Quick Links</h4>
            <div class="footer-links-grid">
              <router-link to="/" class="footer-link">Home</router-link>
              <router-link to="/about" class="footer-link">About Us</router-link>
              <router-link to="/how-it-works" class="footer-link">How It Works</router-link>
              <router-link to="/faq" class="footer-link">FAQ</router-link>
              <router-link to="/contact" class="footer-link">Contact</router-link>
            </div>
          </div>

          <!-- For Artists & Sellers Section -->
          <div class="footer-links-section">
            <h4 class="footer-section-title">For Artists & Sellers</h4>
            <div class="footer-links-grid">
              <router-link to="/signup" class="footer-link">Artist Registration</router-link>
              <router-link to="/seller-guidelines" class="footer-link">Seller Guidelines</router-link>
              <router-link to="/commission-process" class="footer-link">Commission Process</router-link>
              <router-link to="/selling-materials" class="footer-link">Selling Materials</router-link>
              <router-link to="/success-stories" class="footer-link">Success Stories</router-link>
            </div>
          </div>

          <!-- Newsletter Section -->
          <div class="footer-newsletter">
            <h4 class="footer-section-title">Newsletter</h4>
            <p class="newsletter-description">
              Subscribe to get updates on new projects and artists.
            </p>
            <div class="newsletter-form">
              <input 
                type="email" 
                v-model="newsletterEmail" 
                placeholder="Your email" 
                class="newsletter-input"
              />
              <button @click="subscribeNewsletter" class="newsletter-button">
                Subscribe
              </button>
            </div>
          </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
          <div class="footer-bottom-content">
            <p class="copyright">&copy; 2025 Refurb. All rights reserved.</p>
            <div class="footer-bottom-links">
              <router-link to="/privacy-policy" class="footer-bottom-link">Privacy Policy</router-link>
              <router-link to="/terms-of-service" class="footer-bottom-link">Terms of Service</router-link>
              <router-link to="/cookie-policy" class="footer-bottom-link">Cookie Policy</router-link>
            </div>
            <div class="payment-methods">
              <span class="payment-label">Payment Methods:</span>
              <div class="payment-icons">
                <span class="payment-icon">💳</span>
                <span class="payment-icon">🏦</span>
                <span class="payment-icon">💰</span>
                <span class="payment-icon">📱</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
    
    <!-- Modal for cart login -->
    <div v-if="cartLoginModalVisible" class="modal-overlay">
      <div class="modal-content">
        <h2>Please Log In</h2>
        <p>You need to be logged in to view your cart.</p>
        <div class="modal-actions">
          <router-link to="/login" @click="closeCartLoginModal" class="primary-button">Log In</router-link>
          <button @click="closeCartLoginModal" class="secondary-button">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "App",
  data() {
    return {
      isLoggedIn: false,
      userName: "",
      userRole: "",
      isDropdownOpen: false,
      cartItemCount: 0,
      cartLoginModalVisible: false,
      cartUpdateInterval: null,
      newsletterEmail: "",
      isMobileMenuOpen: false
    };
  },
  computed: {
    dashboardRoute() {
      switch (this.userRole) {
        case "artist":
          return "/artist-dashboard";
        case "admin":
          return "/admin-dashboard";
        case "scrapSeller":
          return "/scrap-seller-dashboard";
        case "general":
          return "/general-dashboard";
        default:
          return "/";
      }
    },
  },
  methods: {
    subscribeNewsletter() {
      if (!this.newsletterEmail) {
        alert("Please enter a valid email address");
        return;
      }
      
      // Add your newsletter subscription logic here
      // Subscribing email to newsletter
      // You can make an API call to your backend here
      
      // Reset the input
      this.newsletterEmail = "";
      alert("Thank you for subscribing to our newsletter!");
    },
    
    showCartLoginModal() {
      this.cartLoginModalVisible = true;
    },
    
    closeCartLoginModal() {
      this.cartLoginModalVisible = false;
    },
    
    async fetchCartCount() {
      // Fetching cart count
      
      // For guests, you might want to retrieve from localStorage or sessionStorage
      // if your app supports guest carts
      if (!this.isLoggedIn) {
        const guestCart = localStorage.getItem("guestCart");
        if (guestCart) {
          try {
            const cartItems = JSON.parse(guestCart);
            this.cartItemCount = Array.isArray(cartItems) ? cartItems.length : 0;
          } catch (err) {
            console.error("Error parsing guest cart:", err);
            this.cartItemCount = 0;
          }
          return;
        }
        
        this.cartItemCount = 0;
        return;
      }
      
      // For logged-in users, fetch from API
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.cartItemCount = 0;
        return;
      }
      
      try {
        const response = await fetch('http://127.0.0.1:8000/api/cart', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        
        if (response.ok) {
          const cartData = await response.json();
          // Cart data fetched successfully
          
          // Check the structure of the response to correctly count items
          if (Array.isArray(cartData)) {
            this.cartItemCount = cartData.length;
          } else if (cartData.items && Array.isArray(cartData.items)) {
            this.cartItemCount = cartData.items.length;
          } else if (typeof cartData === 'object' && Object.keys(cartData).length) {
            // If it's an object with keys representing cart items
            this.cartItemCount = Object.keys(cartData).length;
          } else {
            this.cartItemCount = 0;
          }
          
          // Cart count updated
          
          // Broadcast the update to other components
          this.broadcastCartUpdate(this.cartItemCount);
        } else {
          console.error("Error fetching cart:", response.status);
          this.cartItemCount = 0;
        }
      } catch (err) {
        console.error("Error fetching cart:", err);
        this.cartItemCount = 0;
      }
    },
    
    // New method to broadcast cart updates to other components
    broadcastCartUpdate(count) {
      // Using a custom event to notify all components
      const event = new CustomEvent('global-cart-updated', {
        detail: { count: count }
      });
      window.dispatchEvent(event);
    },
    
    checkSession() {
      const session = localStorage.getItem("userSession");
      if (session) {
        try {
          const userData = JSON.parse(session);
          this.isLoggedIn = true;
          this.userName = userData.name;
          this.userRole = userData.role;
        } catch (e) {
          console.error("Error parsing session data:", e);
          this.logout();
        }
      }
    },
    
    logout() {
      // Logout button clicked

      const token = localStorage.getItem('access_token');
      if (!token) {
        console.error("No token found. Logging out anyway.");
        this.handleLogoutCleanup();
        return;
      }

      axios.post('/api/logout', {}, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      })
      .then(response => {
                  // Logout API response received
        if (response.data.success) {
          this.handleLogoutCleanup();
        } else {
          console.error("Logout failed:", response.data.message);
          // Optionally still logout even if API indicates failure
          this.handleLogoutCleanup();
        }
      })
      .catch(error => {
        console.error("Logout API error:", error);
        // Cleanup even if API call fails
        this.handleLogoutCleanup();
      });
    },
    
    handleLogoutCleanup() {
              // Handling logout cleanup
      localStorage.removeItem('access_token');
      localStorage.removeItem('userSession');
      this.isLoggedIn = false;
      this.userName = "";
      this.userRole = "";
      this.cartItemCount = 0;  // Reset cart count on logout
      this.$router.push('/').catch(err => console.error("Redirection error:", err));
      this.closeDropdown();
      
      // Broadcast cart reset
      this.broadcastCartUpdate(0);
    },
    
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen;
    },
    
    closeDropdown() {
      this.isDropdownOpen = false;
    },
    
    handleClickOutside(event) {
      const dropdown = this.$el.querySelector('.user-menu');
      if (dropdown && !dropdown.contains(event.target)) {
        this.closeDropdown();
      }
      
      const mobileMenu = this.$el.querySelector('.mobile-menu');
      const mobileToggle = this.$el.querySelector('.mobile-menu-toggle');
      if (mobileMenu && !mobileMenu.contains(event.target) && !mobileToggle.contains(event.target)) {
        this.closeMobileMenu();
      }
    },
    
    // Setup cart polling
    setupCartPolling() {
      // Clear any existing interval
      if (this.cartUpdateInterval) {
        clearInterval(this.cartUpdateInterval);
      }
      
      // Set up polling to fetch cart count periodically
      this.cartUpdateInterval = setInterval(() => {
        this.fetchCartCount();
      }, 5000); // Check every 5 seconds
    },
    
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
    },
    
    closeMobileMenu() {
      this.isMobileMenuOpen = false;
    }
  },
  
  mounted() {
    this.checkSession();
    document.addEventListener('click', this.handleClickOutside);
    
    // Listen for both custom events
    window.addEventListener('cart-updated', () => {
        // Cart updated event received;
      this.fetchCartCount();
    });
    
    // Listen for storage changes (for cross-tab synchronization)
    window.addEventListener('storage', (event) => {
      if (event.key === 'guestCart' || event.key === 'userSession') {
        this.fetchCartCount();
      }
    });
    
    // Initial cart fetch - make sure this happens AFTER checking the session
    setTimeout(() => {
      this.fetchCartCount();
    }, 100);
    
    // Setup polling for cart updates
    this.setupCartPolling();
    
    // Route change handler
    this.$router.beforeEach((to, from, next) => {
      if (this.cartLoginModalVisible) {
        this.closeCartLoginModal();
      }
      next();
    });
    
    // Fetch cart count when route changes
    this.$router.afterEach(() => {
      this.fetchCartCount();
    });
  },

  watch: {
    // Watch for login status changes to update cart count
    isLoggedIn(newValue) {
      if (newValue) {
        this.fetchCartCount();
        this.setupCartPolling(); // Reset polling on login change
      } else {
        // Clear polling if logged out
        if (this.cartUpdateInterval) {
          clearInterval(this.cartUpdateInterval);
          this.cartUpdateInterval = null;
        }
      }
    }
  },

  beforeDestroy() {
    document.removeEventListener('click', this.handleClickOutside);
    window.removeEventListener('cart-updated', this.fetchCartCount);
    window.removeEventListener('storage', this.fetchCartCount);
    
    // Clear the polling interval
    if (this.cartUpdateInterval) {
      clearInterval(this.cartUpdateInterval);
      this.cartUpdateInterval = null;
    }
  },
};
</script>

<style scoped>
/* Global Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* App Layout */
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.main-content {
  flex: 1;
  min-height: calc(100vh - 200px);
}

/* Clean Header Styles */
.clean-header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.clean-navbar {
  width: 100%;
}

/* CSS Reset for navbar to override any conflicting styles */
.clean-navbar * {
  box-sizing: border-box;
}

.clean-navbar ul, .clean-navbar ol {
  list-style: none;
  margin: 0;
  padding: 0;
}

.clean-navbar a {
  text-decoration: none;
}

.clean-navbar .navbar-container {
  max-width: 1200px !important;
  margin: 0 auto !important;
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 0 24px !important;
  height: 70px !important;
  min-width: 0 !important;
  overflow: visible !important;
  width: 100% !important;
}

/* Brand Logo */
.navbar-brand {
  flex-shrink: 0;
}

.brand-logo {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  text-decoration: none;
  letter-spacing: -0.02em;
}

.brand-logo:hover {
  color: #6366f1;
}

/* Center Navigation */
.clean-navbar .navbar-nav {
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  gap: 2rem !important;
  flex: 1 !important;
  justify-content: center !important;
  min-width: 0 !important;
  list-style: none !important;
  margin: 0 !important;
  padding: 0 !important;
}

.clean-navbar .nav-link {
  position: relative !important;
  display: inline-block !important;
  padding: 0.75rem 1rem !important;
  color: #1f2937 !important;
  text-decoration: none !important;
  font-weight: 500 !important;
  font-size: 0.95rem !important;
  border-radius: 8px !important;
  transition: all 0.2s ease !important;
  white-space: nowrap !important;
  border: 1px solid transparent !important;
}

.clean-navbar .nav-link:hover {
  color: #1f2937 !important;
  background-color: #f8fafc !important;
}

.clean-navbar .nav-link.active {
  color: #6366f1 !important;
  background-color: #eff6ff !important;
}

/* Additional specificity for horizontal layout */
.clean-navbar .navbar-nav router-link,
.clean-navbar .navbar-nav a {
  display: inline-block !important;
  float: none !important;
  clear: none !important;
}

.clean-navbar .navbar-nav > * {
  display: inline-block !important;
}

/* Right Actions */
.navbar-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-shrink: 0;
}

/* Cart Section */
.cart-section {
  position: relative;
}

.cart-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  color: #374151;
  text-decoration: none;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: white;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
  position: relative;
}

.cart-link:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.cart-button {
  background: transparent;
  border: 1px solid #d1d5db;
}

.cart-icon {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

.cart-count {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #ef4444;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
  line-height: 1;
}

/* User Section */
.user-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

/* Authentication Links */
.auth-link {
  padding: 0.5rem 1rem;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.875rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.login-link {
  color: #374151;
  border: 1px solid transparent;
}

.login-link:hover {
  color: #1f2937;
  background-color: #f8fafc;
}

.signup-link {
  color: white;
  background: #6366f1;
  border: 1px solid #6366f1;
}

.signup-link:hover {
  background: #5855eb;
  border-color: #5855eb;
}

/* User Profile */
.user-profile {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 1rem;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.user-profile:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.profile-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
  color: white;
}

.profile-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.125rem;
}

.profile-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
  line-height: 1;
}

.profile-role {
  font-size: 0.75rem;
  color: #6b7280;
  line-height: 1;
}

.dropdown-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: transform 0.2s ease;
}

.dropdown-icon.rotated {
  transform: rotate(180deg);
}

/* User Dropdown */
.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 200px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px);
  transition: all 0.2s ease;
  z-index: 1000;
  margin-top: 0.25rem;
}

.user-dropdown.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-content {
  padding: 0.5rem 0;
}

.dropdown-link {
  display: block;
  padding: 0.75rem 1rem;
  color: #374151;
  text-decoration: none;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}

.dropdown-link:hover {
  background: #f9fafb;
  color: #1f2937;
}

.dropdown-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 0.5rem 0;
}

.logout-link {
  color: #ef4444;
}

.logout-link:hover {
  background: #fef2f2;
  color: #dc2626;
}

/* Mobile Toggle */
.mobile-toggle {
  display: none;
  flex-direction: column;
  gap: 3px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 4px;
  transition: background 0.2s ease;
}

.mobile-toggle:hover {
  background: #f9fafb;
}

.mobile-toggle span {
  width: 20px;
  height: 2px;
  background: #374151;
  border-radius: 1px;
  transition: all 0.3s ease;
}

.mobile-toggle.active span:nth-child(1) {
  transform: rotate(45deg) translate(4px, 4px);
}

.mobile-toggle.active span:nth-child(2) {
  opacity: 0;
}

.mobile-toggle.active span:nth-child(3) {
  transform: rotate(-45deg) translate(4px, -4px);
}

/* Mobile Menu */
.mobile-menu {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  left: auto;
  min-width: 280px;
  background: white;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border-radius: 0 0 8px 8px;
  overflow: hidden;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s ease;
  border: 1px solid #e5e7eb;
  border-top: none;
  z-index: 999;
}

.mobile-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.mobile-nav-links {
  padding: 1rem 0;
}

.mobile-nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  color: #374151;
  text-decoration: none;
  transition: background 0.2s ease;
  font-weight: 500;
  position: relative;
}

.mobile-nav-item:hover {
  background: #f9fafb;
  color: #1f2937;
}

.mobile-cart-button {
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.mobile-cart-badge {
  position: absolute;
  top: 0.5rem;
  right: 1rem;
  background: #ef4444;
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.mobile-nav-icon {
  width: 20px;
  height: 20px;
  stroke-width: 2;
  flex-shrink: 0;
}

.mobile-auth-section {
  border-top: 1px solid #f3f4f6;
  padding: 1rem 1.5rem;
}

.mobile-auth-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem;
  margin-bottom: 0.5rem;
  text-align: center;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.mobile-auth-icon {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

.mobile-auth-button:not(.primary) {
  color: #374151;
  background: white;
  border: 1px solid #d1d5db;
}

.mobile-auth-button:not(.primary):hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.mobile-auth-button.primary {
  color: white;
  background: #3b82f6;
  border: 1px solid #3b82f6;
}

.mobile-auth-button.primary:hover {
  background: #2563eb;
  border-color: #2563eb;
}

.mobile-user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.mobile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: white;
}

.mobile-username {
  font-weight: 600;
  color: #1f2937;
}

.mobile-logout-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
  margin-top: 1rem;
}

.mobile-logout-button:hover {
  background: #dc2626;
}

.mobile-logout-icon {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .clean-navbar .navbar-nav {
    display: none !important;
  }
  
  .clean-navbar .user-section {
    display: none !important;
  }
  
  .clean-navbar .cart-section {
    display: none !important;
  }
  
  .clean-navbar .mobile-toggle {
    display: flex !important;
  }
  
  .clean-navbar .mobile-menu {
    display: block !important;
  }
  
  .clean-navbar .navbar-container {
    padding: 0 16px !important;
  }
}

/* Ensure navbar is visible and horizontal on larger screens */
@media (min-width: 769px) {
  .clean-navbar .navbar-nav {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: center !important;
  }
  
  .clean-navbar .user-section {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
  }
  
  .clean-navbar .cart-section {
    display: block !important;
  }
  
  .clean-navbar .mobile-toggle {
    display: none !important;
  }
  
  .clean-navbar .mobile-menu {
    display: none !important;
  }
}

/* Footer Styles */
.site-footer {
  background: #374151;
  color: white;
  margin-top: auto;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 20px 0;
}

.footer-content {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr;
  gap: 40px;
  margin-bottom: 40px;
}

/* Footer Brand Section */
.footer-brand {
  max-width: 280px;
}

.footer-brand-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0 0 15px 0;
  color: white;
}

.footer-brand-description {
  margin: 0 0 20px 0;
  color: #9ca3af;
  font-size: 0.875rem;
  line-height: 1.5;
}

.footer-social {
  display: flex;
  gap: 12px;
}

.social-link {
  width: 36px;
  height: 36px;
  background: #4b5563;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #d1d5db;
  transition: all 0.3s ease;
  text-decoration: none;
}

.social-link:hover {
  background: #6366f1;
  color: white;
  transform: translateY(-2px);
}

/* Footer Links Sections */
.footer-links-section {
  display: flex;
  flex-direction: column;
}

.footer-section-title {
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 20px 0;
  color: white;
}

.footer-links-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-link {
  color: #9ca3af;
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.3s ease;
  line-height: 1.5;
}

.footer-link:hover {
  color: white;
}

/* Newsletter Section */
.footer-newsletter {
  display: flex;
  flex-direction: column;
}

.newsletter-description {
  color: #9ca3af;
  font-size: 0.875rem;
  line-height: 1.5;
  margin: 0 0 20px 0;
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.newsletter-input {
  padding: 12px 16px;
  border: 1px solid #4b5563;
  border-radius: 6px;
  background: #374151;
  color: white;
  font-size: 0.875rem;
  transition: border-color 0.3s ease;
}

.newsletter-input::placeholder {
  color: #9ca3af;
}

.newsletter-input:focus {
  outline: none;
  border-color: #6366f1;
  background: #4b5563;
}

.newsletter-button {
  padding: 12px 24px;
  background: #6366f1;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background 0.3s ease;
}

.newsletter-button:hover {
  background: #5855eb;
}

/* Footer Bottom */
.footer-bottom {
  border-top: 1px solid #4b5563;
  padding: 20px 0;
}

.footer-bottom-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.copyright {
  margin: 0;
  color: #9ca3af;
  font-size: 0.875rem;
}

.footer-bottom-links {
  display: flex;
  gap: 20px;
}

.footer-bottom-link {
  color: #9ca3af;
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.3s ease;
}

.footer-bottom-link:hover {
  color: white;
}

.payment-methods {
  display: flex;
  align-items: center;
  gap: 10px;
}

.payment-label {
  color: #9ca3af;
  font-size: 0.875rem;
}

.payment-icons {
  display: flex;
  gap: 8px;
}

.payment-icon {
  font-size: 1.125rem;
  opacity: 0.7;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  width: 90%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-content h2 {
  margin-bottom: 1rem;
  color: #333;
}

.modal-content p {
  margin-bottom: 1.5rem;
  color: #666;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.primary-button {
  background: #667eea;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
}

.primary-button:hover {
  background: #5a6fd8;
  transform: translateY(-1px);
}

.secondary-button {
  background: #f8f9fa;
  color: #333;
  border: 1px solid #e9ecef;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.secondary-button:hover {
  background: #e9ecef;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .footer-content {
    grid-template-columns: 1fr 1fr;
    gap: 30px;
  }
  
  .footer-newsletter {
    grid-column: span 2;
  }
}

@media (max-width: 768px) {
  .nav-container {
    padding: 1rem;
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  .nav-links {
    display: none;
  }

  .mobile-menu-toggle {
    display: flex;
    margin-left: auto;
  }

  .mobile-menu {
    display: block;
  }

  .auth-section {
    display: none;
  }

  .cart-container {
    display: none;
  }

  .brand-section {
    min-width: auto;
    flex: 0 0 auto;
  }

  .nav-actions {
    min-width: auto;
    display: flex;
    justify-content: flex-end;
    flex: 0 0 auto;
    margin-left: auto;
  }

  /* Hide all nav-actions content except hamburger on mobile */
  .nav-actions > *:not(.mobile-menu-toggle) {
    display: none;
  }

  .logo {
    font-size: 1.5rem;
  }

  .footer-content {
    grid-template-columns: 1fr;
    gap: 30px;
    text-align: center;
  }

  .footer-brand {
    max-width: none;
  }

  .footer-bottom-content {
    flex-direction: column;
    align-items: center;
    gap: 15px;
  }

  .footer-bottom-links {
    gap: 15px;
  }

  .payment-methods {
    flex-direction: column;
    gap: 8px;
  }
}

@media (max-width: 480px) {
  .nav-container {
    padding: 0.75rem;
  }

  .logo {
    font-size: 1.3rem;
  }

  .footer-container {
    padding: 1.5rem 1rem;
  }

  .footer-links {
    flex-direction: column;
    gap: 0.5rem;
  }

  .modal-content {
    padding: 1.5rem;
    margin: 1rem;
  }
}
</style>