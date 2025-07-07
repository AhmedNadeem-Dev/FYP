<template>
  <div class="cart-page">
    <div class="container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-content">
          <h1>Shopping Cart</h1>
          <p>Review your items and proceed to checkout</p>
        </div>
      </div>
      
      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading your cart...</p>
      </div>
      
      <!-- Error State -->
      <div v-else-if="error" class="error-state">
        <div class="error-icon">⚠️</div>
        <h3>Something went wrong</h3>
        <p>{{ error }}</p>
        <button @click="fetchCart" class="retry-btn">Try Again</button>
      </div>
      
      <!-- Empty Cart State -->
      <div v-else-if="!cartItems.length" class="empty-state">
        <div class="empty-icon">🛒</div>
        <h3>Your cart is empty</h3>
        <p>Discover amazing scrap items and add them to your cart</p>
        <router-link to="/browse-scrap" class="browse-btn">Browse Items</router-link>
      </div>
      
      <!-- Cart Content -->
      <div v-else class="cart-content">
        <div class="cart-layout">
          <!-- Cart Items -->
          <div class="cart-items-section">
            <div class="section-header">
              <h2>Cart Items</h2>
              <span class="item-count">{{ getTotalItems }} {{ getTotalItems === 1 ? 'item' : 'items' }}</span>
            </div>
            
            <div class="cart-items">
              <div v-for="item in cartItems" :key="item.id" class="cart-item">
                <div class="item-image-container">
                  <img 
                    :src="item.images && item.images.length ? item.images[0] : '/images/main.jpg'" 
                    :alt="item.name" 
                    class="item-image"
                    @click="viewProductDetails(item.id)"
                  />
                </div>
                
                <div class="item-details">
                  <h3 class="item-name" @click="viewProductDetails(item.id)">{{ item.name }}</h3>
                  <p class="item-price">PKR {{ item.price.toLocaleString() }}</p>
                  <button @click="removeFromCart(item.id)" class="remove-btn">
                    <svg class="remove-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Remove
                  </button>
                </div>
                
                <div class="item-actions">
                  <div class="quantity-controls">
                    <button 
                      @click="decrementQuantity(item)" 
                      :disabled="item.quantity <= 1"
                      class="quantity-btn"
                    >
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                      </svg>
                    </button>
                    <span class="quantity">{{ item.quantity }}</span>
                    <button 
                      @click="incrementQuantity(item)" 
                      class="quantity-btn"
                    >
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                      </svg>
                    </button>
                  </div>
                  
                  <div class="item-subtotal">
                    <span class="subtotal-label">Subtotal</span>
                    <span class="subtotal-amount">PKR {{ calculateSubtotal(item).toLocaleString() }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Cart Summary -->
          <div class="cart-summary-section">
            <div class="summary-card">
              <div class="summary-header">
                <h3>Order Summary</h3>
              </div>
              
              <div class="summary-details">
                <div class="summary-row">
                  <span>Items ({{ getTotalItems }})</span>
                  <span>PKR {{ calculateTotal().toLocaleString() }}</span>
                </div>
                <div class="summary-row">
                  <span>Shipping</span>
                  <span class="shipping-note">Calculated at checkout</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-total">
                  <span>Total</span>
                  <span>PKR {{ calculateTotal().toLocaleString() }}</span>
                </div>
              </div>
              
              <div class="summary-actions">
                <button @click="proceedToCheckout" class="checkout-btn">
                  <svg class="checkout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"/>
                  </svg>
                  Proceed to Checkout
                </button>
                <button @click="clearCart" class="clear-btn">Clear Cart</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div v-if="showConfirmModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ confirmModalTitle }}</h3>
        </div>
        <div class="modal-body">
          <p>{{ confirmModalMessage }}</p>
        </div>
        <div class="modal-actions">
          <button @click="confirmAction" class="confirm-btn">Yes, Continue</button>
          <button @click="cancelAction" class="cancel-btn">Cancel</button>
        </div>
      </div>
    </div>
    
    <!-- Login Modal -->
    <div v-if="showLoginModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Login Required</h3>
        </div>
        <div class="modal-body">
          <p>Please log in to view your cart and continue shopping.</p>
        </div>
        <div class="modal-actions">
          <router-link to="/login" class="login-btn">Log In</router-link>
          <button @click="closeLoginModal" class="cancel-btn">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      cartItems: [],
      loading: true,
      error: null,
      showConfirmModal: false,
      confirmModalTitle: '',
      confirmModalMessage: '',
      confirmActionType: null,
      actionItemId: null,
      showLoginModal: false,
      pendingUpdates: {},
      updateTimeout: null
    };
  },
  
  computed: {
    getTotalItems() {
      return this.cartItems.reduce((total, item) => total + item.quantity, 0);
    }
  },
  
  methods: {
    calculateSubtotal(item) {
      return item.price * item.quantity;
    },
    
    calculateTotal() {
      return this.cartItems.reduce((total, item) => {
        return total + (item.price * item.quantity);
      }, 0);
    },
    
    async fetchCart() {
      this.loading = true;
      this.error = null;
      
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.showLoginModal = true;
        this.loading = false;
        return;
      }
      
      try {
        const response = await fetch('http://127.0.0.1:8000/api/cart', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });
        
        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.error || 'Failed to fetch cart');
        }
        
        const data = await response.json();
        this.cartItems = data.items || [];
        // Cart items loaded successfully
        
        // Debug: Check each item's ID structure
        this.cartItems.forEach(item => {
          // Validate item structure
          if (!item.id) {
            console.warn('Cart item missing ID:', item);
          }
        });
      } catch (err) {
        console.error("Error fetching cart:", err);
        this.error = err.message || "Could not load your cart. Please try again.";
      } finally {
        this.loading = false;
      }
    },
    
    viewProductDetails(productId) {
      this.$router.push({ name: 'product-details', params: { id: productId } });
    },
    
    removeFromCart(productId) {
      // Add validation to ensure productId exists
      if (!productId) {
        console.error('Product ID is null or undefined');
        this.error = 'Invalid product ID';
        return;
      }
      
              // Removing product from cart
      this.confirmModalTitle = "Remove Item";
      this.confirmModalMessage = "Are you sure you want to remove this item from your cart?";
      this.confirmActionType = 'remove';
      this.actionItemId = productId;
      this.showConfirmModal = true;
    },
    
    // Client-side quantity increment with debounced API update
    incrementQuantity(item) {
      if (!item.id) {
        console.error('Item ID is null or undefined');
        return;
      }
      
      item.quantity += 1;
      this.debouncedUpdateQuantity(item.id, item.quantity);
    },
    
    // Client-side quantity decrement with debounced API update
    decrementQuantity(item) {
      if (!item.id) {
        console.error('Item ID is null or undefined');
        return;
      }
      
      if (item.quantity > 1) {
        item.quantity -= 1;
        this.debouncedUpdateQuantity(item.id, item.quantity);
      }
    },
    
    // Debounced API update to prevent excessive API calls
    debouncedUpdateQuantity(productId, newQuantity) {
      if (!productId) {
        console.error('Product ID is null or undefined in debouncedUpdateQuantity');
        return;
      }
      
      this.pendingUpdates[productId] = newQuantity;
      
      if (this.updateTimeout) {
        clearTimeout(this.updateTimeout);
      }
      
      this.updateTimeout = setTimeout(() => {
        this.syncPendingUpdates();
      }, 500); // Wait 500ms before sending updates to API
    },
    
    // Sync any pending quantity updates with the server
    async syncPendingUpdates() {
      const token = localStorage.getItem("access_token");
      const updates = { ...this.pendingUpdates };
      this.pendingUpdates = {};
      
      for (const [productId, quantity] of Object.entries(updates)) {
        if (!productId || productId === 'null' || productId === 'undefined') {
          console.error('Invalid product ID in syncPendingUpdates:', productId);
          continue;
        }
        
        try {
          const response = await fetch(`http://127.0.0.1:8000/api/cart/update/${productId}`, {
            method: 'PUT',
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: quantity })
          });
          
          if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Failed to update quantity');
          }
          
          // Updated quantity successfully
        } catch (err) {
          console.error("Error updating quantity:", err);
          // Revert the client-side change if API fails
          const item = this.cartItems.find(item => item.id == productId);
          if (item) {
            // You might want to reload the cart or show an error message
            this.error = "Failed to update quantity. Please refresh the page.";
          }
        }
      }
    },
    
    clearCart() {
      this.confirmModalTitle = "Clear Cart";
      this.confirmModalMessage = "Are you sure you want to remove all items from your cart?";
      this.confirmActionType = 'clear';
      this.showConfirmModal = true;
    },
    
    async confirmAction() {
      const token = localStorage.getItem("access_token");
      
      try {
        if (this.confirmActionType === 'remove') {
          // Validate actionItemId before making the request
          if (!this.actionItemId || this.actionItemId === 'null' || this.actionItemId === 'undefined') {
            throw new Error('Invalid product ID');
          }
          
          // Removing item from cart
          
          const response = await fetch(`http://127.0.0.1:8000/api/cart/remove/${this.actionItemId}`, {
            method: 'DELETE',
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            }
          });
          
          if (response.ok) {
            // Remove the item from local cart state
            this.cartItems = this.cartItems.filter(item => item.id != this.actionItemId);
            // Item removed from cart successfully
          } else {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Failed to remove item');
          }
        } else if (this.confirmActionType === 'clear') {
          const response = await fetch('http://127.0.0.1:8000/api/cart/clear', {
            method: 'DELETE',
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            }
          });
          
          if (response.ok) {
            // Clear the cart
            this.cartItems = [];
            // Cart cleared successfully
          } else {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Failed to clear cart');
          }
        }
      } catch (err) {
        console.error("Error performing cart action:", err);
        this.error = err.message || "Action failed. Please try again.";
      } finally {
        this.showConfirmModal = false;
        this.actionItemId = null;
      }
    },
    
    proceedToCheckout() {
      // First make sure we sync any pending quantity updates
      if (this.updateTimeout) {
        clearTimeout(this.updateTimeout);
        this.syncPendingUpdates();
      }
      
      // Check if cart is empty
      if (this.cartItems.length === 0) {
        this.error = "Your cart is empty. Please add items before checkout.";
        return;
      }
      
      // Navigate to checkout page
      // Proceeding to checkout
      this.$router.push({ name: 'checkout' });
    },
    
    cancelAction() {
      this.showConfirmModal = false;
      this.actionItemId = null;
    },
    
    closeLoginModal() {
      this.showLoginModal = false;
      this.$router.push('/browse-scrap');
    }
  },
  
  mounted() {
    this.fetchCart();
  },
  
  beforeUnmount() {
    // Make sure to sync any pending updates when component is unmounted
    if (this.updateTimeout) {
      clearTimeout(this.updateTimeout);
      this.syncPendingUpdates();
    }
  }
};
</script>
<style scoped>
.cart-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Page Header */
.page-header {
  text-align: center;
  margin-bottom: 3rem;
}

.header-content h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 0.5rem;
}

.header-content p {
  font-size: 1.1rem;
  color: #718096;
}

/* Loading, Error, Empty States */
.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  margin: 2rem 0;
}

.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #6c63ff;
  border-radius: 50%;
  width: 3rem;
  height: 3rem;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-state p {
  color: #718096;
  font-size: 1.1rem;
}

.error-state {
  background: #fef5e7;
  border: 1px solid #f6ad55;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.error-state h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #c53030;
  margin-bottom: 1rem;
}

.error-state p {
  color: #744210;
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
}

.retry-btn {
  background: #6c63ff;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.retry-btn:hover {
  background: #5a52ff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
  color: #cbd5e0;
}

.empty-state h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #4a5568;
  margin-bottom: 1rem;
}

.empty-state p {
  color: #718096;
  font-size: 1.1rem;
  margin-bottom: 2rem;
}

.browse-btn {
  display: inline-block;
  background: #6c63ff;
  color: white;
  padding: 0.75rem 2rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s ease;
}

.browse-btn:hover {
  background: #5a52ff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
}

/* Cart Content */
.cart-content {
  max-width: 1200px;
  margin: 0 auto;
}

.cart-layout {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 2rem;
  align-items: start;
}

/* Cart Items Section */
.cart-items-section {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.section-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0;
}

.item-count {
  color: #718096;
  font-size: 0.9rem;
  background: #e2e8f0;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
}

.cart-items {
  padding: 1rem 0;
}

.cart-item {
  display: grid;
  grid-template-columns: 80px 1fr auto;
  gap: 1rem;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f1f5f9;
  transition: background-color 0.2s ease;
}

.cart-item:hover {
  background: #f8fafc;
}

.cart-item:last-child {
  border-bottom: none;
}

.item-image-container {
  width: 80px;
  height: 80px;
  border-radius: 0.5rem;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.item-image-container:hover {
  transform: scale(1.05);
}

.item-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  min-width: 0;
}

.item-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 0.5rem 0;
  cursor: pointer;
  transition: color 0.2s ease;
}

.item-name:hover {
  color: #6c63ff;
}

.item-price {
  font-size: 1rem;
  color: #4a5568;
  margin: 0 0 0.75rem 0;
  font-weight: 500;
}

.remove-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: none;
  border: none;
  color: #e53e3e;
  font-size: 0.9rem;
  cursor: pointer;
  transition: color 0.2s ease;
  padding: 0;
}

.remove-btn:hover {
  color: #c53030;
}

.remove-icon {
  width: 1rem;
  height: 1rem;
}

.item-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 1rem;
}

.quantity-controls {
  display: flex;
  align-items: center;
  background: #f8fafc;
  border-radius: 0.5rem;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.quantity-btn {
  width: 2rem;
  height: 2rem;
  background: none;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #4a5568;
}

.quantity-btn:hover:not(:disabled) {
  background: #e2e8f0;
  color: #2d3748;
}

.quantity-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity-btn svg {
  width: 1rem;
  height: 1rem;
}

.quantity {
  min-width: 3rem;
  text-align: center;
  font-weight: 600;
  color: #2d3748;
  padding: 0.5rem;
  background: white;
  border-left: 1px solid #e2e8f0;
  border-right: 1px solid #e2e8f0;
}

.item-subtotal {
  text-align: right;
}

.subtotal-label {
  display: block;
  font-size: 0.8rem;
  color: #718096;
  margin-bottom: 0.25rem;
}

.subtotal-amount {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
}

/* Cart Summary */
.cart-summary-section {
  position: sticky;
  top: 2rem;
}

.summary-card {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.summary-header {
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.summary-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0;
}

.summary-details {
  padding: 1.5rem 2rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 1rem;
}

.summary-row:last-child {
  margin-bottom: 0;
}

.summary-row span:first-child {
  color: #4a5568;
}

.summary-row span:last-child {
  font-weight: 500;
  color: #2d3748;
}

.shipping-note {
  font-size: 0.9rem;
  color: #718096;
  font-style: italic;
}

.summary-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 1.5rem 0;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
  padding-top: 1rem;
  border-top: 2px solid #e2e8f0;
}

.summary-actions {
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.checkout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  background: #6c63ff;
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 0.75rem;
}

.checkout-btn:hover {
  background: #5a52ff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
}

.checkout-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.clear-btn {
  width: 100%;
  background: #f8fafc;
  color: #718096;
  border: 1px solid #e2e8f0;
  padding: 0.75rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-btn:hover {
  background: #e2e8f0;
  color: #4a5568;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 90%;
  overflow: hidden;
}

.modal-header {
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0;
}

.modal-body {
  padding: 1.5rem 2rem;
}

.modal-body p {
  color: #4a5568;
  line-height: 1.6;
  margin: 0;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.confirm-btn, .login-btn {
  flex: 1;
  background: #6c63ff;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  text-align: center;
}

.confirm-btn:hover, .login-btn:hover {
  background: #5a52ff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
}

.cancel-btn {
  flex: 1;
  background: #f8fafc;
  color: #4a5568;
  border: 1px solid #e2e8f0;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.cancel-btn:hover {
  background: #e2e8f0;
  color: #2d3748;
}

/* Responsive Design */
@media (max-width: 768px) {
  .cart-layout {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .cart-item {
    grid-template-columns: 60px 1fr;
    gap: 1rem;
    padding: 1rem;
  }
  
  .item-actions {
    grid-column: span 2;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
  }
  
  .item-subtotal {
    text-align: left;
  }
  
  .subtotal-label {
    display: inline;
    margin-right: 0.5rem;
  }
  
  .subtotal-amount {
    display: inline;
  }
  
  .header-content h1 {
    font-size: 2rem;
  }
  
  .modal-actions {
    flex-direction: column;
    gap: 0.75rem;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 0.5rem;
  }
  
  .cart-page {
    padding: 1rem 0;
  }
  
  .section-header {
    padding: 1rem;
  }
  
  .cart-item {
    padding: 1rem;
  }
  
  .summary-card {
    margin: 0 -0.5rem;
  }
}
</style>
