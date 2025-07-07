<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
          <h1 class="auth-title">Create New Password</h1>
          <p class="auth-subtitle">Enter your new password to complete the reset process</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="resetPassword" class="auth-form">
          <!-- New Password Field -->
          <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <div class="input-wrapper">
              <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <input
                :type="passwordFieldType"
                id="password"
                v-model="password"
                class="form-input"
                :class="{ 'error': errors.password }"
                placeholder="Enter your new password"
                required
                autocomplete="new-password"
              />
              <button
                type="button"
                class="password-toggle"
                @click="togglePasswordVisibility"
              >
                <svg v-if="passwordFieldType === 'password'" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                </svg>
              </button>
            </div>
            <div v-if="errors.password" class="error-message">
              {{ errors.password }}
            </div>
          </div>

          <!-- Confirm Password Field -->
          <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <div class="input-wrapper">
              <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <input
                :type="confirmPasswordFieldType"
                id="password_confirmation"
                v-model="password_confirmation"
                class="form-input"
                :class="{ 'error': errors.password_confirmation }"
                placeholder="Confirm your new password"
                required
                autocomplete="new-password"
              />
              <button
                type="button"
                class="password-toggle"
                @click="toggleConfirmPasswordVisibility"
              >
                <svg v-if="confirmPasswordFieldType === 'password'" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                </svg>
              </button>
            </div>
            <div v-if="errors.password_confirmation" class="error-message">
              {{ errors.password_confirmation }}
            </div>
          </div>

          <!-- Password Strength Indicator -->
          <div v-if="password" class="password-strength">
            <div class="strength-header">
              <span class="strength-label">Password Strength:</span>
              <span class="strength-text" :class="passwordStrength.class">
                {{ passwordStrength.text }}
              </span>
            </div>
            <div class="strength-bar">
              <div 
                class="strength-fill"
                :class="passwordStrength.class"
                :style="{ width: passwordStrength.percentage + '%' }"
              ></div>
            </div>
          </div>

          <!-- General Error Message -->
          <div v-if="generalError" class="general-error">
            {{ generalError }}
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            class="submit-btn"
            :disabled="loading"
            :class="{ 'loading': loading }"
          >
            <div v-if="loading" class="loading-spinner"></div>
            {{ loading ? 'Resetting Password...' : 'Reset Password' }}
          </button>
        </form>

        <!-- Back to Login -->
        <div class="auth-footer">
          <p class="footer-text">
            Remember your password? 
            <router-link to="/login" class="auth-link">Back to Sign In</router-link>
          </p>
        </div>

        <!-- Password Requirements -->
        <div class="password-requirements">
          <h4 class="requirements-title">Password must contain:</h4>
          <ul class="requirements-list">
            <li class="requirement-item" :class="{ 'valid': hasMinLength }">
              <svg class="requirement-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              At least 8 characters
            </li>
            <li class="requirement-item" :class="{ 'valid': hasUppercase }">
              <svg class="requirement-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              One uppercase letter
            </li>
            <li class="requirement-item" :class="{ 'valid': hasLowercase }">
              <svg class="requirement-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              One lowercase letter
            </li>
            <li class="requirement-item" :class="{ 'valid': hasNumber }">
              <svg class="requirement-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              One number
            </li>
            <li class="requirement-item" :class="{ 'valid': hasSpecialChar }">
              <svg class="requirement-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              One special character
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Success Toast -->
    <div v-if="showSuccessToast" class="toast toast-success">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>Password reset successfully! Redirecting to login...</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ResetPassword',
  data() {
    return {
      password: '',
      password_confirmation: '',
      passwordFieldType: 'password',
      confirmPasswordFieldType: 'password',
      loading: false,
      errors: {},
      generalError: '',
      showSuccessToast: false,
      token: ''
    };
  },
  
  computed: {
    hasMinLength() {
      return this.password.length >= 8;
    },
    hasUppercase() {
      return /[A-Z]/.test(this.password);
    },
    hasLowercase() {
      return /[a-z]/.test(this.password);
    },
    hasNumber() {
      return /\d/.test(this.password);
    },
    hasSpecialChar() {
      return /[@$!%*#?&]/.test(this.password);
    },
    passwordStrength() {
      const password = this.password;
      if (!password) return { percentage: 0, text: '', class: '' };
      
      let score = 0;
      
      // Length check
      if (password.length >= 8) score += 20;
      if (password.length >= 12) score += 10;
      
      // Character diversity
      if (/[a-z]/.test(password)) score += 20;
      if (/[A-Z]/.test(password)) score += 20;
      if (/\d/.test(password)) score += 20;
      if (/[@$!%*#?&]/.test(password)) score += 20;
      
      // Determine strength level
      if (score < 40) {
        return { percentage: score, text: 'Weak', class: 'weak' };
      } else if (score < 70) {
        return { percentage: score, text: 'Fair', class: 'fair' };
      } else if (score < 90) {
        return { percentage: score, text: 'Good', class: 'good' };
      } else {
        return { percentage: score, text: 'Strong', class: 'strong' };
      }
    }
  },
  
  mounted() {
    // Get token from URL
    this.token = this.$route.query.token || '';
  },
  
  methods: {
    togglePasswordVisibility() {
      this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
    },
    
    toggleConfirmPasswordVisibility() {
      this.confirmPasswordFieldType = this.confirmPasswordFieldType === 'password' ? 'text' : 'password';
    },
    
    validatePasswords() {
      this.errors = {};
      
      if (!this.password) {
        this.errors.password = 'Password is required';
        return false;
      }
      
      if (this.password.length < 8) {
        this.errors.password = 'Password must be at least 8 characters';
        return false;
      }
      
      if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])/.test(this.password)) {
        this.errors.password = 'Password must contain uppercase, lowercase, number, and special character';
        return false;
      }
      
      if (!this.password_confirmation) {
        this.errors.password_confirmation = 'Please confirm your password';
        return false;
      }
      
      if (this.password !== this.password_confirmation) {
        this.errors.password_confirmation = 'Passwords do not match';
        return false;
      }
      
      return true;
    },
    
    async resetPassword() {
      this.generalError = '';
      
      if (!this.validatePasswords()) {
        return;
      }
      
      this.loading = true;
      
      try {
        const response = await axios.post('/api/password/reset', {
          token: this.token,
          email: this.$route.query.email || '',
          password: this.password,
          password_confirmation: this.password_confirmation
        });
        
        if (response.data.success) {
          this.showSuccessToast = true;
          
          // Redirect to login after success
          setTimeout(() => {
            this.$router.push('/login');
          }, 2000);
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.generalError = error.response?.data?.message || 'Password reset failed. Please try again.';
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
  max-width: 520px;
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

.password-toggle {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s ease;
}

.password-toggle:hover {
  color: #6b7280;
}

.error-message {
  margin-top: 8px;
  color: #ef4444;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Password Strength */
.password-strength {
  margin-top: 16px;
  margin-bottom: 8px;
}

.strength-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.strength-label {
  font-size: 0.875rem;
  color: #374151;
  font-weight: 500;
}

.strength-text {
  font-size: 0.875rem;
  font-weight: 600;
}

.strength-text.weak {
  color: #ef4444;
}

.strength-text.fair {
  color: #f59e0b;
}

.strength-text.good {
  color: #3b82f6;
}

.strength-text.strong {
  color: #10b981;
}

.strength-bar {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.strength-fill {
  height: 100%;
  transition: all 0.3s ease;
  border-radius: 4px;
}

.strength-fill.weak {
  background: #ef4444;
}

.strength-fill.fair {
  background: #f59e0b;
}

.strength-fill.good {
  background: #3b82f6;
}

.strength-fill.strong {
  background: #10b981;
}

/* General Error */
.general-error {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 12px 16px;
  border-radius: 8px;
  font-size: 0.875rem;
  margin-bottom: 24px;
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

/* Password Requirements */
.password-requirements {
  border-top: 1px solid #e5e7eb;
  padding-top: 24px;
}

.requirements-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 12px;
}

.requirements-list {
  list-style: none;
  space-y: 8px;
}

.requirement-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 8px;
  transition: color 0.2s ease;
}

.requirement-item.valid {
  color: #10b981;
}

.requirement-icon {
  color: #d1d5db;
  transition: color 0.2s ease;
}

.requirement-item.valid .requirement-icon {
  color: #10b981;
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
