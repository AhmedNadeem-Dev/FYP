<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
          <h1 class="auth-title">Verify Your Email</h1>
          <p class="auth-subtitle">We've sent a verification code to your email address. Please enter it below to continue.</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="verifyCode" class="auth-form">
          <!-- Verification Code Field -->
          <div class="form-group">
            <label for="code" class="form-label">Verification Code</label>
            <div class="code-input-container">
              <input
                type="text"
                id="code"
                v-model="verificationCode"
                class="code-input"
                :class="{ 'error': errorMessage }"
                placeholder="Enter 6-digit code"
                maxlength="6"
                pattern="[0-9]{6}"
                required
                autocomplete="one-time-code"
                @input="formatCode"
              />
            </div>
            <div class="code-hint">
              <svg class="hint-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Check your email for the 6-digit verification code</span>
            </div>
            <div v-if="errorMessage" class="error-message">
              {{ errorMessage }}
            </div>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            class="submit-btn"
            :disabled="loading || verificationCode.length !== 6"
            :class="{ 'loading': loading }"
          >
            <div v-if="loading" class="loading-spinner"></div>
            {{ loading ? 'Verifying...' : 'Verify Code' }}
          </button>
        </form>

        <!-- Resend Section -->
        <div class="resend-section">
          <p class="resend-text">Didn't receive the code?</p>
          <button 
            @click="resendCode" 
            class="resend-btn"
            :disabled="resendLoading || countdown > 0"
          >
            <div v-if="resendLoading" class="loading-spinner-small"></div>
            <span v-if="!resendLoading && countdown === 0">Resend Code</span>
            <span v-else-if="!resendLoading">Resend in {{ countdown }}s</span>
            <span v-else>Sending...</span>
          </button>
        </div>

        <!-- Back to Login -->
        <div class="auth-footer">
          <p class="footer-text">
            Want to try a different email? 
            <router-link to="/login" class="auth-link">Back to Sign In</router-link>
          </p>
        </div>

        <!-- Security Tips -->
        <div class="security-tips">
          <h4 class="tips-title">Security Tips</h4>
          <div class="tips-content">
            <div class="tip-item">
              <svg class="tip-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span class="tip-text">Check your spam/junk folder</span>
            </div>
            <div class="tip-item">
              <svg class="tip-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span class="tip-text">Code expires in 10 minutes</span>
            </div>
            <div class="tip-item">
              <svg class="tip-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <span class="tip-text">Keep your code private</span>
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
      <span>Email verified successfully! Redirecting...</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'VerifyCode',
  data() {
    return {
      verificationCode: '',
      loading: false,
      resendLoading: false,
      errorMessage: '',
      showSuccessToast: false,
      countdown: 0,
      countdownInterval: null
    };
  },
  
  methods: {
    formatCode(event) {
      // Only allow numbers
      const value = event.target.value.replace(/[^0-9]/g, '');
      this.verificationCode = value;
      
      // Clear error when user starts typing
      if (this.errorMessage) {
        this.errorMessage = '';
      }
    },
    
    async verifyCode() {
      this.errorMessage = '';
      
      if (!this.verificationCode) {
        this.errorMessage = 'Verification code is required';
        return;
      }
      
      if (this.verificationCode.length !== 6) {
        this.errorMessage = 'Please enter a 6-digit code';
        return;
      }
      
      this.loading = true;
      
      try {
        const response = await axios.post('/api/verify-email', {
          code: this.verificationCode,
          email: this.$route.query.email
        });
        
        if (response.data.success) {
          this.showSuccessToast = true;
          
          // Redirect to login after success
          setTimeout(() => {
            this.$router.push('/login');
          }, 2000);
        }
      } catch (error) {
        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message;
        } else if (error.response?.status === 422) {
          this.errorMessage = 'Invalid verification code';
        } else if (error.response?.status === 410) {
          this.errorMessage = 'Verification code has expired';
        } else {
          this.errorMessage = 'Verification failed. Please try again';
        }
      } finally {
        this.loading = false;
      }
    },
    
    async resendCode() {
      this.resendLoading = true;
      this.errorMessage = '';
      
      try {
        const response = await axios.post('/api/resend-verification', {
          email: this.$route.query.email
        });
        
        if (response.data.success) {
          // Start countdown
          this.startCountdown(60);
        }
      } catch (error) {
        this.errorMessage = error.response?.data?.message || 'Failed to resend code';
      } finally {
        this.resendLoading = false;
      }
    },
    
    startCountdown(seconds) {
      this.countdown = seconds;
      this.countdownInterval = setInterval(() => {
        this.countdown--;
        if (this.countdown <= 0) {
          clearInterval(this.countdownInterval);
        }
      }, 1000);
    }
  },
  
  beforeUnmount() {
    if (this.countdownInterval) {
      clearInterval(this.countdownInterval);
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
  text-align: center;
}

.code-input-container {
  display: flex;
  justify-content: center;
  margin-bottom: 12px;
}

.code-input {
  width: 200px;
  padding: 16px 20px;
  border: 2px solid #d1d5db;
  border-radius: 12px;
  font-size: 1.5rem;
  font-weight: 600;
  text-align: center;
  letter-spacing: 0.5em;
  color: #1f2937;
  background-color: white;
  transition: all 0.2s ease;
}

.code-input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.code-input.error {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.code-input::placeholder {
  color: #9ca3af;
  letter-spacing: 0.2em;
  font-size: 1rem;
}

.code-hint {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 8px;
}

.hint-icon {
  color: #6366f1;
  flex-shrink: 0;
}

.error-message {
  text-align: center;
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
  opacity: 0.6;
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

/* Resend Section */
.resend-section {
  text-align: center;
  margin-bottom: 32px;
  padding: 20px 0;
  border-bottom: 1px solid #e5e7eb;
}

.resend-text {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 12px;
}

.resend-btn {
  background: none;
  border: 1px solid #6366f1;
  color: #6366f1;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 36px;
}

.resend-btn:hover:not(:disabled) {
  background: #6366f1;
  color: white;
}

.resend-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-spinner-small {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 6px;
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

/* Security Tips */
.security-tips {
  border-top: 1px solid #e5e7eb;
  padding-top: 24px;
}

.tips-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 16px;
  text-align: center;
}

.tips-content {
  space-y: 12px;
}

.tip-item {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}

.tip-icon {
  color: #6366f1;
  flex-shrink: 0;
}

.tip-text {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.5;
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
  
  .code-input {
    width: 100%;
    max-width: 240px;
  }
}
</style>
