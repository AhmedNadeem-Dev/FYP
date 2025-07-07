<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
          <h1 class="auth-title">Forgot Password?</h1>
          <p class="auth-subtitle">No worries! Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="sendResetLink" class="auth-form">
          <!-- Email Field -->
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-wrapper">
              <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <input
                type="email"
                id="email"
                v-model="email"
                class="form-input"
                :class="{ 'error': errorMessage }"
                placeholder="Enter your email address"
                required
                autocomplete="email"
              />
            </div>
            <div v-if="errorMessage" class="error-message">
              {{ errorMessage }}
            </div>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            class="submit-btn"
            :disabled="loading"
            :class="{ 'loading': loading }"
          >
            <div v-if="loading" class="loading-spinner"></div>
            {{ loading ? 'Sending Reset Link...' : 'Send Reset Link' }}
          </button>
        </form>

        <!-- Back to Login -->
        <div class="auth-footer">
          <p class="footer-text">
            Remember your password? 
            <router-link to="/login" class="auth-link">Back to Sign In</router-link>
          </p>
        </div>

        <!-- Help Section -->
        <div class="help-section">
          <div class="help-content">
            <div class="help-item">
              <svg class="help-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="help-text">Check your spam folder if you don't receive the email</p>
            </div>
            <div class="help-item">
              <svg class="help-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="help-text">Reset link expires in 60 minutes for security</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Toast -->
    <div v-if="showSuccessToast" class="toast toast-success">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>Reset link sent successfully! Check your email.</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ForgetPassword',
  data() {
    return {
      email: '',
      loading: false,
      errorMessage: '',
      showSuccessToast: false
    };
  },
  
  methods: {
    validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },
    
    async sendResetLink() {
      this.errorMessage = '';
      
      // Validate email
      if (!this.email) {
        this.errorMessage = 'Email address is required';
        return;
      }
      
      if (!this.validateEmail(this.email)) {
        this.errorMessage = 'Please enter a valid email address';
        return;
      }
      
      this.loading = true;
      
      try {
        const response = await axios.post('/api/password/email', {
          email: this.email
        });
        
        if (response.data.success || response.status === 200) {
          this.showSuccessToast = true;
          this.email = ''; // Clear the form
          
          // Redirect to login after showing success message
          setTimeout(() => {
            this.$router.push('/login');
          }, 3000);
        }
      } catch (error) {
        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message;
        } else if (error.response?.status === 404) {
          this.errorMessage = 'No account found with this email address';
        } else if (error.response?.status === 429) {
          this.errorMessage = 'Too many requests. Please try again later';
        } else {
          this.errorMessage = 'An error occurred. Please try again';
        }
      } finally {
        this.loading = false;
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

.auth-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.auth-container {
  width: 100%;
  max-width: 480px;
}

.auth-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  padding: 40px;
  backdrop-filter: blur(10px);
}

/* Header */
.auth-header {
  text-align: center;
  margin-bottom: 32px;
}

.auth-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 8px;
  letter-spacing: -0.025em;
}

.auth-subtitle {
  color: #6b7280;
  font-size: 1rem;
  line-height: 1.5;
}

/* Form */
.auth-form {
  margin-bottom: 32px;
}

.form-group {
  margin-bottom: 24px;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  color: #9ca3af;
  z-index: 2;
}

.form-input {
  width: 100%;
  padding: 12px 12px 12px 44px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  line-height: 1.5;
  color: #1f2937;
  background-color: white;
  transition: all 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-input.error {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-input::placeholder {
  color: #9ca3af;
}

.error-message {
  margin-top: 8px;
  color: #ef4444;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Submit Button */
.submit-btn {
  width: 100%;
  background: #6366f1;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 12px 16px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 48px;
}

.submit-btn:hover:not(:disabled) {
  background: #5855eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 8px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Footer */
.auth-footer {
  text-align: center;
  margin-bottom: 32px;
}

.footer-text {
  color: #6b7280;
  font-size: 0.875rem;
}

.auth-link {
  color: #6366f1;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.auth-link:hover {
  color: #4f46e5;
}

/* Help Section */
.help-section {
  border-top: 1px solid #e5e7eb;
  padding-top: 24px;
}

.help-content {
  space-y: 16px;
}

.help-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.help-icon {
  color: #6366f1;
  flex-shrink: 0;
  margin-top: 2px;
}

.help-text {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.5;
  margin: 0;
}

/* Toast */
.toast {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #10b981;
  color: white;
  padding: 16px 20px;
  border-radius: 8px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
  z-index: 1000;
  animation: slideIn 0.3s ease-out;
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

/* Responsive */
@media (max-width: 480px) {
  .auth-card {
    padding: 24px;
  }
  
  .auth-title {
    font-size: 1.75rem;
  }
}
</style>
