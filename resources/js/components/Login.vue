<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
          <h1 class="auth-title">Sign In to Refurb</h1>
          <p class="auth-subtitle">Welcome back! Please enter your details</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="auth-form">
          <!-- Email Field -->
          <div class="form-group">
            <label for="email" class="form-label">Email or Username</label>
            <div class="input-wrapper">
              <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <input
                type="email"
                id="email"
                v-model="form.email"
                class="form-input"
                :class="{ 'error': fieldErrors.email }"
                placeholder="Enter your email or username"
                required
                autocomplete="email"
              />
            </div>
            <div v-if="fieldErrors.email" class="error-message">
              {{ fieldErrors.email }}
            </div>
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <div class="password-header">
              <label for="password" class="form-label">Password</label>
              <router-link to="/forget-password" class="forgot-link">Forgot Password?</router-link>
            </div>
            <div class="input-wrapper">
              <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <input
                :type="passwordFieldType"
                id="password"
                v-model="form.password"
                class="form-input"
                :class="{ 'error': fieldErrors.password }"
                placeholder="Enter your password"
                required
                autocomplete="current-password"
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
            <div v-if="fieldErrors.password" class="error-message">
              {{ fieldErrors.password }}
            </div>
          </div>

          <!-- Remember Me -->
          <div class="form-options">
            <label class="checkbox-wrapper">
              <input type="checkbox" v-model="rememberMe" class="checkbox">
              <span class="checkbox-checkmark"></span>
              <span class="checkbox-label">Remember me for 30 days</span>
            </label>
          </div>

          <!-- General Error Message -->
          <div v-if="errorMessage" class="general-error">
            {{ errorMessage }}
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            class="submit-btn"
            :disabled="isLoading"
            :class="{ 'loading': isLoading }"
          >
            <div v-if="isLoading" class="loading-spinner"></div>
            {{ isLoading ? 'Signing In...' : 'Sign In' }}
          </button>
        </form>

        <!-- Divider -->
        <div class="divider">
          <span class="divider-text">or</span>
        </div>

        <!-- Social Login -->
        <div class="social-login">
          <button class="social-btn google-btn" @click="loginWithGoogle">
            <svg width="20" height="20" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google
          </button>
          <button class="social-btn facebook-btn" @click="loginWithFacebook">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Facebook
          </button>
        </div>

        <!-- Sign Up Link -->
        <div class="auth-footer">
          <p class="footer-text">
            Don't have an account? 
            <router-link to="/signup" class="auth-link">Sign Up</router-link>
          </p>
        </div>
      </div>
    </div>

    <!-- Success Toast -->
    <div v-if="showSuccessToast" class="toast toast-success">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>Login successful! Redirecting...</span>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Login",
  data() {
    return {
      form: {
        email: "",
        password: ""
      },
      rememberMe: false,
      errorMessage: "",
      fieldErrors: {
        email: "",
        password: ""
      },
      passwordFieldType: "password",
      isLoading: false,
      showSuccessToast: false
    };
  },
  
  methods: {
    togglePasswordVisibility() {
      this.passwordFieldType = this.passwordFieldType === "password" ? "text" : "password";
    },

    validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },

    clearFieldErrors() {
      this.fieldErrors.email = "";
      this.fieldErrors.password = "";
    },

    async handleLogin() {
      this.clearFieldErrors();
      this.errorMessage = "";
      
      // Validation
      if (!this.form.email) {
        this.fieldErrors.email = "Email is required";
        return;
      }
      
      if (!this.validateEmail(this.form.email)) {
        this.fieldErrors.email = "Please enter a valid email address";
        return;
      }
      
      if (!this.form.password) {
        this.fieldErrors.password = "Password is required";
        return;
      }

      this.isLoading = true;

      try {
        const response = await axios.post('/api/login', {
          email: this.form.email,
          password: this.form.password,
          remember: this.rememberMe
        });

        if (response.data.success) {
          // Store authentication data
          localStorage.setItem('access_token', response.data.access_token);
          localStorage.setItem('userSession', JSON.stringify({
            name: response.data.user.name,
            email: response.data.user.email,
            role: response.data.user.role
          }));

          this.showSuccessToast = true;

          // Redirect after short delay
          setTimeout(() => {
            this.$router.push('/');
          }, 1500);
        }
      } catch (error) {
        console.error('Login error:', error);
        
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          if (errors.email) this.fieldErrors.email = errors.email[0];
          if (errors.password) this.fieldErrors.password = errors.password[0];
        } else {
          this.errorMessage = error.response?.data?.message || "Login failed. Please try again.";
        }
      } finally {
        this.isLoading = false;
      }
    },

    loginWithGoogle() {
      // Implement Google OAuth
      console.log('Google login clicked');
    },

    loginWithFacebook() {
      // Implement Facebook OAuth
      console.log('Facebook login clicked');
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
  space-y: 24px;
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

.password-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.forgot-link {
  color: #6366f1;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: color 0.2s ease;
}

.forgot-link:hover {
  color: #4f46e5;
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

/* Form Options */
.form-options {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin-bottom: 24px;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  cursor: pointer;
  user-select: none;
}

.checkbox {
  display: none;
}

.checkbox-checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid #d1d5db;
  border-radius: 4px;
  margin-right: 12px;
  position: relative;
  background: white;
  transition: all 0.2s ease;
}

.checkbox:checked + .checkbox-checkmark {
  background: #6366f1;
  border-color: #6366f1;
}

.checkbox:checked + .checkbox-checkmark::after {
  content: '';
  position: absolute;
  left: 6px;
  top: 2px;
  width: 6px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.checkbox-label {
  font-size: 0.875rem;
  color: #374151;
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

/* Divider */
.divider {
  margin: 32px 0;
  text-align: center;
  position: relative;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e5e7eb;
}

.divider-text {
  background: white;
  color: #6b7280;
  padding: 0 16px;
  font-size: 0.875rem;
  position: relative;
}

/* Social Login */
.social-login {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 32px;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.social-btn:hover {
  background: #f9fafb;
  border-color: #9ca3af;
  transform: translateY(-1px);
}

/* Footer */
.auth-footer {
  text-align: center;
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
  
  .social-login {
    grid-template-columns: 1fr;
  }
}
</style>
