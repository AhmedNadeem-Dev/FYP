<template>
  <div class="checkout-page">
    <div class="container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-content">
          <h1>Checkout</h1>
          <p>Complete your purchase securely</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading checkout...</p>
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
        <p>Add items to your cart before checking out</p>
        <router-link to="/browse-scrap" class="browse-btn">Browse Items</router-link>
      </div>

      <!-- Checkout Content -->
      <div v-else class="checkout-content">
        <div class="checkout-layout">
          <!-- Order Summary -->
          <div class="order-summary-section">
            <div class="summary-card">
              <div class="summary-header">
                <h3>Order Summary</h3>
                <span class="item-count">{{ cartItems.length }} {{ cartItems.length === 1 ? 'item' : 'items' }}</span>
              </div>
              
              <div class="summary-items">
                <div v-for="item in cartItems" :key="item.id" class="summary-item">
                  <div class="item-image-container">
                    <img 
                      :src="item.images && item.images.length ? item.images[0] : '/images/main.jpg'" 
                      :alt="item.name" 
                      class="item-image"
                    />
                  </div>
                  <div class="item-info">
                    <h4 class="item-name">{{ item.name }}</h4>
                    <p class="item-details">Quantity: {{ item.quantity }}</p>
                  </div>
                  <div class="item-price">
                    PKR {{ (item.price * item.quantity).toLocaleString() }}
                  </div>
                </div>
              </div>
              
              <div class="summary-totals">
                <div class="total-row">
                  <span>Subtotal</span>
                  <span>PKR {{ calculateSubtotal().toLocaleString() }}</span>
                </div>
                <div class="total-row">
                  <span>Shipping</span>
                  <span>PKR {{ shipping.toLocaleString() }}</span>
                </div>
                <div class="total-divider"></div>
                <div class="total-row grand-total">
                  <span>Total</span>
                  <span>PKR {{ calculateTotal().toLocaleString() }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Checkout Form -->
          <div class="checkout-form-section">
            <div class="form-card">
              <div class="form-header">
                <h3>Delivery Information</h3>
                <p>Please provide your delivery details</p>
              </div>
              
              <!-- Form Validation Errors -->
              <div v-if="formErrors.general" class="form-error">
                <div class="error-icon">⚠️</div>
                <p>{{ formErrors.general }}</p>
              </div>
              
              <form @submit.prevent="placeOrder" class="checkout-form">
                <div class="form-group">
                  <label for="name">Full Name</label>
                  <input 
                    type="text"
                    id="name"
                    v-model="formData.name"
                    :class="['form-input', { 'error': formErrors.name }]"
                    placeholder="Enter your full name"
                    required
                  />
                  <span v-if="formErrors.name" class="field-error">{{ formErrors.name }}</span>
                </div>
                
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <input 
                    type="tel"
                    id="phone"
                    v-model="formData.phone"
                    @input="handlePhoneInput"
                    @keypress="restrictToNumbers"
                    @paste="handlePhonePaste"
                    :class="['form-input', { 'error': formErrors.phone }]"
                    placeholder="Enter your phone number"
                    maxlength="15"
                    required
                  />
                  <span v-if="formErrors.phone" class="field-error">{{ formErrors.phone }}</span>
                </div>
                
                <div class="form-group">
                  <label for="address">Delivery Address</label>
                  <textarea 
                    id="address"
                    v-model="formData.address"
                    :class="['form-textarea', { 'error': formErrors.address }]"
                    placeholder="Enter your complete delivery address"
                    rows="3"
                    required
                  ></textarea>
                  <span v-if="formErrors.address" class="field-error">{{ formErrors.address }}</span>
                </div>
                
                <div class="form-group">
                  <label for="notes">Order Notes (Optional)</label>
                  <textarea 
                    id="notes"
                    v-model="formData.notes"
                    class="form-textarea"
                    placeholder="Any special instructions for delivery"
                    rows="2"
                  ></textarea>
                </div>
                
                <div class="payment-section">
                  <h4>Payment Method</h4>
                  <div class="payment-option">
                    <input
                      type="radio"
                      id="cod"
                      name="payment"
                      value="cod"
                      v-model="formData.paymentMethod"
                      checked
                    />
                    <label for="cod">
                      <div class="payment-info">
                        <span class="payment-name">Cash on Delivery</span>
                        <span class="payment-desc">Pay when you receive your order</span>
                      </div>
                      <div class="payment-icon">💰</div>
                    </label>
                  </div>
                </div>
                
                <button 
                  type="submit" 
                  class="place-order-btn"
                  :disabled="isSubmitting"
                >
                  <span v-if="!isSubmitting" class="btn-content">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"/>
                    </svg>
                    Place Order
                  </span>
                  <span v-else class="btn-content loading">
                    <div class="spinner-small"></div>
                    Processing...
                  </span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Success Modal -->
    <div v-if="showConfirmModal" class="modal-overlay">
      <div class="modal-content success-modal">
        <div class="success-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="modal-header">
          <h3>Order Placed Successfully!</h3>
        </div>
        <div class="modal-body">
          <p>Your order has been placed successfully.</p>
          <div class="order-id">Order ID: <strong>#{{ orderId }}</strong></div>
        </div>
        <div class="modal-actions">
          <router-link :to="{ name: 'order-history-with-id', params: { orderId: orderId } }" class="primary-btn">
            View My Orders
          </router-link>
          <router-link to="/browse-scrap" class="secondary-btn">
            Continue Shopping
          </router-link>
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
      isSubmitting: false,
      showConfirmModal: false,
      orderId: null,
      shipping: 200, // Fixed shipping cost (PKR)
      formData: {
        name: '',
        phone: '',
        address: '',
        notes: '',
        paymentMethod: 'cod'
      },
      formErrors: {
        general: '',
        name: '',
        phone: '',
        address: ''
      }
    };
  },
  
  methods: {
    // Restrict keypress to numbers only
    restrictToNumbers(event) {
      const key = event.key;
      // Allow numbers, backspace, delete, tab, escape, enter
      if (!/[0-9]/.test(key) && 
          !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(key)) {
        event.preventDefault();
      }
    },
    
    // Handle input to remove any non-numeric characters
    handlePhoneInput(event) {
      const input = event.target;
      const value = input.value;
      // Remove any non-numeric characters
      const numericValue = value.replace(/[^0-9]/g, '');
      
      // Update the model value
      this.formData.phone = numericValue;
      
      // Update the input value to ensure consistency
      if (input.value !== numericValue) {
        input.value = numericValue;
      }
    },
    
    // Handle paste events to filter out non-numeric characters
    handlePhonePaste(event) {
      event.preventDefault();
      const paste = (event.clipboardData || window.clipboardData).getData('text');
      const numericPaste = paste.replace(/[^0-9]/g, '');
      
      // Get current cursor position
      const input = event.target;
      const start = input.selectionStart;
      const end = input.selectionEnd;
      
      // Insert the numeric paste at cursor position
      const currentValue = this.formData.phone;
      const newValue = currentValue.substring(0, start) + numericPaste + currentValue.substring(end);
      
      // Respect maxlength
      this.formData.phone = newValue.substring(0, 15);
    },
    
    async fetchCart() {
      this.loading = true;
      this.error = null;
      
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.$router.push('/login');
        return;
      }
      
      try {
        const response = await fetch('http://127.0.0.1:8000/api/cart', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        
        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Failed to fetch cart');
        }
        
        const data = await response.json();
        this.cartItems = data.items || [];
        
        if (this.cartItems.length === 0) {
          // Redirect to cart if cart is empty
          this.$router.push('/cart');
        }
        
        // Pre-fill name from user data if available
        if (data.user && data.user.name) {
          this.formData.name = data.user.name;
        }
      } catch (err) {
        console.error("Error fetching cart:", err);
        this.error = err.message || "Could not load your cart. Please try again.";
      } finally {
        this.loading = false;
      }
    },
    
    calculateSubtotal() {
      return this.cartItems.reduce((total, item) => {
        return total + (item.price * item.quantity);
      }, 0);
    },
    
    calculateTotal() {
      return this.calculateSubtotal() + this.shipping;
    },
    
    clearFormErrors() {
      this.formErrors = {
        general: '',
        name: '',
        phone: '',
        address: ''
      };
    },
    
    async placeOrder() {
      // Clear previous errors
      this.clearFormErrors();
      
      if (!this.validateForm()) {
        return;
      }
      
      const token = localStorage.getItem("access_token");
      if (!token) {
        this.$router.push('/login');
        return;
      }
      
      this.isSubmitting = true;
      
      try {
        const orderData = {
          shipping_address: this.formData.address,
          contact_phone: this.formData.phone,
          payment_method: 'cash_on_delivery', // Use the exact value expected by the backend
          notes: this.formData.notes || ''
        };
        
        // Sending order data to checkout endpoint
        
        const response = await fetch('http://127.0.0.1:8000/api/orders/checkout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(orderData)
        });
        
        // Get response data
        const responseText = await response.text();
        
        // Try to parse the response as JSON
        let responseData;
        try {
          responseData = JSON.parse(responseText);
        } catch (e) {
          console.error("Failed to parse response as JSON:", e);
          throw new Error("Invalid response format from server");
        }
        
        if (!response.ok) {
          console.error("Order API error response:", responseData);
          throw new Error(responseData?.error || responseData?.message || 'Failed to place order');
        }
        
        // Order placed successfully
        this.orderId = responseData.order_id || responseData.id;
        this.showConfirmModal = true;
        
        // Clear cart data from local storage if you're storing it there
        // localStorage.removeItem('cart');
      } catch (err) {
        console.error("Error placing order:", err);
        this.formErrors.general = err.message || "Could not place your order. Please try again.";
        
        // Scroll to error message
        this.$nextTick(() => {
          const errorElement = document.querySelector('.form-error');
          if (errorElement) {
            errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        });
      } finally {
        this.isSubmitting = false;
      }
    },
    
    validateForm() {
      let isValid = true;
      
      // Validate name
      if (!this.formData.name || this.formData.name.trim() === '') {
        this.formErrors.name = "Please enter your full name";
        isValid = false;
      }
      
      // Validate phone
      if (!this.formData.phone || this.formData.phone.trim() === '') {
        this.formErrors.phone = "Please enter your phone number";
        isValid = false;
      } else {
        // Validate phone number format (now only checking for numeric and length)
        const phoneRegex = /^[0-9]{10,15}$/;
        if (!phoneRegex.test(this.formData.phone.trim())) {
          this.formErrors.phone = "Please enter a valid phone number (10-15 digits)";
          isValid = false;
        }
      }
      
      // Validate address
      if (!this.formData.address || this.formData.address.trim() === '') {
        this.formErrors.address = "Please enter your delivery address";
        isValid = false;
      }
      
      // If there are validation errors, scroll to the first error field
      if (!isValid) {
        this.$nextTick(() => {
          const firstErrorField = document.querySelector('.border-red-300');
          if (firstErrorField) {
            firstErrorField.focus();
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        });
      }
      
      return isValid;
    }
  },
  
  mounted() {
    this.fetchCart();
  }
};
</script>
<style scoped>
.checkout-page {
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

.spinner-small {
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid #ffffff;
  border-radius: 50%;
  width: 1rem;
  height: 1rem;
  animation: spin 1s linear infinite;
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

/* Checkout Content */
.checkout-content {
  max-width: 1200px;
  margin: 0 auto;
}

.checkout-layout {
  display: grid;
  grid-template-columns: 1fr 450px;
  gap: 2rem;
  align-items: start;
}

/* Order Summary */
.order-summary-section {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.summary-card {
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.summary-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
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

.item-count {
  color: #718096;
  font-size: 0.9rem;
  background: #e2e8f0;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
}

.summary-items {
  padding: 1rem 0;
  max-height: 400px;
  overflow-y: auto;
}

.summary-item {
  display: grid;
  grid-template-columns: 60px 1fr auto;
  gap: 1rem;
  align-items: center;
  padding: 1rem 2rem;
  border-bottom: 1px solid #f1f5f9;
}

.summary-item:last-child {
  border-bottom: none;
}

.item-image-container {
  width: 60px;
  height: 60px;
  border-radius: 0.5rem;
  overflow: hidden;
}

.item-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-info {
  min-width: 0;
}

.item-name {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 0.25rem 0;
}

.item-details {
  font-size: 0.9rem;
  color: #718096;
  margin: 0;
}

.item-price {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
  text-align: right;
}

.summary-totals {
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.total-row:last-child {
  margin-bottom: 0;
}

.total-row span:first-child {
  color: #4a5568;
  font-size: 1rem;
}

.total-row span:last-child {
  font-weight: 500;
  color: #2d3748;
  font-size: 1rem;
}

.total-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 1rem 0;
}

.grand-total {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
  padding-top: 1rem;
  border-top: 2px solid #e2e8f0;
}

/* Form Section */
.checkout-form-section {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.form-card {
  padding: 0;
}

.form-header {
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.form-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 0.5rem 0;
}

.form-header p {
  color: #718096;
  margin: 0;
}

.form-error {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: #fef5e7;
  border-bottom: 1px solid #f6ad55;
}

.form-error .error-icon {
  font-size: 1.5rem;
  margin: 0;
}

.form-error p {
  color: #744210;
  margin: 0;
  font-size: 0.95rem;
}

.checkout-form {
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #2d3748;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.form-input, .form-textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  background: #f8fafc;
  font-size: 1rem;
  transition: all 0.2s ease;
}

.form-input:focus, .form-textarea:focus {
  outline: none;
  border-color: #6c63ff;
  box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
  background: white;
}

.form-input.error, .form-textarea.error {
  border-color: #e53e3e;
  background: #fef5e7;
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
  font-family: inherit;
}

.field-error {
  display: block;
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 0.5rem;
}

.payment-section {
  margin-bottom: 2rem;
}

.payment-section h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 1rem 0;
}

.payment-option {
  position: relative;
}

.payment-option input[type="radio"] {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.payment-option label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.payment-option input[type="radio"]:checked + label {
  border-color: #6c63ff;
  background: #f8fafc;
}

.payment-info {
  display: flex;
  flex-direction: column;
}

.payment-name {
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 0.25rem;
}

.payment-desc {
  font-size: 0.9rem;
  color: #718096;
}

.payment-icon {
  font-size: 1.5rem;
}

.place-order-btn {
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
}

.place-order-btn:hover:not(:disabled) {
  background: #5a52ff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
}

.place-order-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.btn-content.loading {
  opacity: 0.8;
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
  max-width: 450px;
  width: 90%;
  overflow: hidden;
}

.success-modal .success-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 4rem;
  height: 4rem;
  background: #10b981;
  color: white;
  border-radius: 50%;
  margin: 2rem auto 1rem;
}

.success-icon svg {
  width: 2rem;
  height: 2rem;
}

.modal-header {
  text-align: center;
  padding: 0 2rem;
}

.modal-header h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 1rem 0;
}

.modal-body {
  text-align: center;
  padding: 0 2rem 2rem;
}

.modal-body p {
  color: #4a5568;
  line-height: 1.6;
  margin: 0 0 1rem 0;
}

.order-id {
  background: #f8fafc;
  padding: 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #e2e8f0;
  color: #2d3748;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.primary-btn {
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

.primary-btn:hover {
  background: #5a52ff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
}

.secondary-btn {
  flex: 1;
  background: #f8fafc;
  color: #4a5568;
  border: 1px solid #e2e8f0;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  text-align: center;
}

.secondary-btn:hover {
  background: #e2e8f0;
  color: #2d3748;
}

/* Responsive Design */
@media (max-width: 768px) {
  .checkout-layout {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .order-summary-section {
    order: 2;
  }
  
  .checkout-form-section {
    order: 1;
  }
  
  .summary-card {
    position: static;
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
  
  .checkout-page {
    padding: 1rem 0;
  }
  
  .checkout-form {
    padding: 1.5rem;
  }
  
  .form-header {
    padding: 1rem 1.5rem;
  }
  
  .summary-header {
    padding: 1rem 1.5rem;
  }
  
  .summary-items {
    padding: 0.5rem 0;
  }
  
  .summary-item {
    padding: 0.75rem 1.5rem;
  }
  
  .summary-totals {
    padding: 1rem 1.5rem;
  }
}
</style>
