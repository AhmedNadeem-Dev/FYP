<template>
  <div class="auth-page">
    <div class="auth-container">
      <!-- Left Side - Form -->
      <div class="auth-form-section">
        <div class="auth-card">
          <!-- Header -->
          <div class="auth-header">
            <h1 class="auth-title">Create Your Account</h1>
            <p class="auth-subtitle">Join our community of artists, sellers, and buyers to transform scrap into art.</p>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSignup" class="auth-form">
            <!-- Name Field -->
            <div class="form-group">
              <label for="name" class="form-label">Full Name</label>
              <div class="input-wrapper">
                <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="form-input"
                  :class="{ 'error': fieldErrors.name }"
                  placeholder="Enter your full name"
                  required
                  autocomplete="name"
                />
              </div>
              <div v-if="fieldErrors.name" class="error-message">
                {{ fieldErrors.name }}
              </div>
            </div>

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
                  v-model="form.email"
                  class="form-input"
                  :class="{ 'error': fieldErrors.email }"
                  placeholder="Enter your email address"
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
              <label for="password" class="form-label">Password</label>
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
                  placeholder="Create a strong password"
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
              <div v-if="fieldErrors.password" class="error-message">
                {{ fieldErrors.password }}
              </div>
              <div class="password-requirements">
                <p class="requirement-text">Password must be at least 8 characters with letters, numbers, and special characters.</p>
              </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="form-group">
              <label for="password_confirmation" class="form-label">Confirm Password</label>
              <div class="input-wrapper">
                <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input
                  :type="confirmPasswordFieldType"
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  class="form-input"
                  :class="{ 'error': fieldErrors.password_confirmation }"
                  placeholder="Confirm your password"
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
              <div v-if="fieldErrors.password_confirmation" class="error-message">
                {{ fieldErrors.password_confirmation }}
              </div>
            </div>

            <!-- Role Selection -->
            <div class="form-group">
              <label class="form-label">I am joining as a:</label>
              <div class="role-selection">
                <div 
                  class="role-option"
                  :class="{ 'selected': form.role === 'artist' }"
                  @click="form.role = 'artist'"
                >
                  <div class="role-content">
                    <svg class="role-icon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <div class="role-text">
                      <h4 class="role-title">Artist</h4>
                    </div>
                  </div>
                  <div class="role-radio">
                    <input type="radio" value="artist" v-model="form.role" />
                  </div>
                </div>
                
                <div 
                  class="role-option"
                  :class="{ 'selected': form.role === 'scrapSeller' }"
                  @click="form.role = 'scrapSeller'"
                >
                  <div class="role-content">
                    <svg class="role-icon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <div class="role-text">
                      <h4 class="role-title">Scrap Seller</h4>
                    </div>
                  </div>
                  <div class="role-radio">
                    <input type="radio" value="scrapSeller" v-model="form.role" />
                  </div>
                </div>

                <div 
                  class="role-option"
                  :class="{ 'selected': form.role === 'buyer' }"
                  @click="form.role = 'buyer'"
                >
                  <div class="role-content">
                    <svg class="role-icon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <div class="role-text">
                      <h4 class="role-title">Buyer</h4>
                    </div>
                  </div>
                  <div class="role-radio">
                    <input type="radio" value="buyer" v-model="form.role" />
                  </div>
                </div>
              </div>
              <div v-if="fieldErrors.role" class="error-message">
                {{ fieldErrors.role }}
              </div>
            </div>

            <!-- Terms Agreement -->
            <div class="form-group">
              <label class="checkbox-wrapper">
                <input type="checkbox" v-model="agreedToTerms" class="checkbox">
                <span class="checkbox-checkmark"></span>
                <span class="checkbox-label">
                  I agree to the 
                  <a href="#" class="terms-link">Terms of Service</a> 
                  and 
                  <a href="#" class="terms-link">Privacy Policy</a>
                </span>
              </label>
              <div v-if="fieldErrors.terms" class="error-message">
                {{ fieldErrors.terms }}
              </div>
            </div>

            <!-- General Error Message -->
            <div v-if="errorMessage" class="general-error">
              {{ errorMessage }}
            </div>

            <!-- Submit Button -->
            <button 
              type="submit" 
              class="submit-btn"
              :disabled="isLoading || !agreedToTerms"
              :class="{ 'loading': isLoading }"
            >
              <div v-if="isLoading" class="loading-spinner"></div>
              {{ isLoading ? 'Creating Account...' : 'Create Account' }}
            </button>
          </form>

          <!-- Sign In Link -->
          <div class="auth-footer">
            <p class="footer-text">
              Already have an account? 
              <router-link to="/login" class="auth-link">Sign In</router-link>
            </p>
          </div>
        </div>
      </div>

      <!-- Right Side - Benefits -->
      <div class="auth-benefits-section">
        <div class="benefits-content">
          <h2 class="benefits-title">Join our creative community</h2>
          <p class="benefits-subtitle">Connect with artists, sell materials, or find unique sustainable art pieces.</p>
          
          <div class="benefits-list">
            <div class="benefit-item">
              <div class="benefit-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <span class="benefit-text">Access to a network of 1,200+ artists</span>
            </div>
            
            <div class="benefit-item">
              <div class="benefit-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
              </div>
              <span class="benefit-text">List materials or find what you need</span>
            </div>
            
            <div class="benefit-item">
              <div class="benefit-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
              </div>
              <span class="benefit-text">Commission or sell unique art pieces</span>
            </div>
          </div>

          <div class="community-stats">
            <div class="profile-images">
              <img src="/images/people1.jpeg" alt="Community member" class="profile-img">
              <img src="/images/people2.jpg" alt="Community member" class="profile-img">
              <img src="/images/people3.jpg" alt="Community member" class="profile-img">
            </div>
            <p class="stats-text">Join 5,000+ members already on Refurb</p>
          </div>

          <div class="rating-section">
            <div class="stars">
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" opacity="0.5">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
            </div>
            <p class="rating-text">4.8/5 average rating from our community</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Toast -->
    <div v-if="showSuccessToast" class="toast toast-success">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>Account created successfully! Redirecting...</span>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Signup",
  data() {
    return {
      form: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: ""
      },
      agreedToTerms: false,
      errorMessage: "",
      fieldErrors: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: "",
        terms: ""
      },
      passwordFieldType: "password",
      confirmPasswordFieldType: "password",
      isLoading: false,
      showSuccessToast: false
    };
  },
  
  methods: {
    togglePasswordVisibility() {
      this.passwordFieldType = this.passwordFieldType === "password" ? "text" : "password";
    },

    toggleConfirmPasswordVisibility() {
      this.confirmPasswordFieldType = this.confirmPasswordFieldType === "password" ? "text" : "password";
    },

    validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },

    validatePassword(password) {
      // At least 8 characters, one letter, one number, one special character
      const re = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;
      return re.test(password);
    },

    clearFieldErrors() {
      this.fieldErrors.name = "";
      this.fieldErrors.email = "";
      this.fieldErrors.password = "";
      this.fieldErrors.password_confirmation = "";
      this.fieldErrors.role = "";
      this.fieldErrors.terms = "";
    },

    async handleSignup() {
      this.clearFieldErrors();
      this.errorMessage = "";
      
      // Validation
      if (!this.form.name) {
        this.fieldErrors.name = "Full name is required";
        return;
      }
      
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
      
      if (!this.validatePassword(this.form.password)) {
        this.fieldErrors.password = "Password must be at least 8 characters with letters, numbers, and special characters";
        return;
      }
      
      if (!this.form.password_confirmation) {
        this.fieldErrors.password_confirmation = "Please confirm your password";
        return;
      }
      
      if (this.form.password !== this.form.password_confirmation) {
        this.fieldErrors.password_confirmation = "Passwords do not match";
        return;
      }
      
      if (!this.form.role) {
        this.fieldErrors.role = "Please select your role";
        return;
      }
      
      if (!this.agreedToTerms) {
        this.fieldErrors.terms = "You must agree to the terms and privacy policy";
        return;
      }

      this.isLoading = true;

      try {
        const response = await axios.post('/api/register', this.form);

        if (response.data.success) {
          this.showSuccessToast = true;

          // Redirect after short delay
          setTimeout(() => {
            this.$router.push('/login');
          }, 1500);
        }
      } catch (error) {
        console.error('Signup error:', error);
        
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          if (errors.name) this.fieldErrors.name = errors.name[0];
          if (errors.email) this.fieldErrors.email = errors.email[0];
          if (errors.password) this.fieldErrors.password = errors.password[0];
          if (errors.role) this.fieldErrors.role = errors.role[0];
        } else {
          this.errorMessage = error.response?.data?.message || "Registration failed. Please try again.";
        }
      } finally {
        this.isLoading = false;
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
  max-width: 1200px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
  min-height: 600px;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Left Side - Form */
.auth-form-section {
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

.auth-card {
  width: 100%;
  max-width: 500px;
}

/* Header */
.auth-header {
  margin-bottom: 32px;
}

.auth-title {
  font-size: 1.875rem;
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
  space-y: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
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
  padding: 10px 12px 10px 44px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
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
  margin-top: 6px;
  color: #ef4444;
  font-size: 0.75rem;
  font-weight: 500;
}

.password-requirements {
  margin-top: 6px;
}

.requirement-text {
  color: #6b7280;
  font-size: 0.75rem;
  line-height: 1.4;
}

/* Role Selection */
.role-selection {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 12px;
  margin-top: 8px;
}

.role-option {
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 16px 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
}

.role-option:hover {
  border-color: #6366f1;
  background: #f8fafc;
}

.role-option.selected {
  border-color: #6366f1;
  background: #eff6ff;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.role-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.role-icon {
  color: #6366f1;
}

.role-text {
  text-align: center;
}

.role-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin: 0;
}

.role-radio {
  position: absolute;
  top: 8px;
  right: 8px;
}

.role-radio input[type="radio"] {
  width: 16px;
  height: 16px;
  margin: 0;
}

/* Checkbox */
.checkbox-wrapper {
  display: flex;
  align-items: flex-start;
  cursor: pointer;
  user-select: none;
  gap: 12px;
}

.checkbox {
  display: none;
}

.checkbox-checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid #d1d5db;
  border-radius: 4px;
  position: relative;
  background: white;
  transition: all 0.2s ease;
  flex-shrink: 0;
  margin-top: 2px;
}

.checkbox:checked + .checkbox-checkmark {
  background: #6366f1;
  border-color: #6366f1;
}

.checkbox:checked + .checkbox-checkmark::after {
  content: '';
  position: absolute;
  left: 5px;
  top: 1px;
  width: 5px;
  height: 9px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.checkbox-label {
  font-size: 0.875rem;
  color: #374151;
  line-height: 1.5;
}

.terms-link {
  color: #6366f1;
  text-decoration: none;
  font-weight: 500;
}

.terms-link:hover {
  text-decoration: underline;
}

/* General Error */
.general-error {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 12px 16px;
  border-radius: 8px;
  font-size: 0.875rem;
  margin-bottom: 20px;
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
  margin-top: 24px;
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
  margin-top: 24px;
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

/* Right Side - Benefits */
.auth-benefits-section {
  background: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
  color: white;
}

.benefits-content {
  width: 100%;
  max-width: 400px;
}

.benefits-title {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 12px;
  line-height: 1.2;
}

.benefits-subtitle {
  font-size: 1rem;
  opacity: 0.9;
  margin-bottom: 32px;
  line-height: 1.5;
}

.benefits-list {
  margin-bottom: 32px;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.benefit-icon {
  width: 24px;
  height: 24px;
  color: white;
  flex-shrink: 0;
}

.benefit-text {
  font-size: 0.875rem;
  line-height: 1.5;
}

.community-stats {
  margin-bottom: 24px;
}

.profile-images {
  display: flex;
  margin-bottom: 12px;
}

.profile-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid white;
  margin-right: -8px;
  object-fit: cover;
}

.stats-text {
  font-size: 0.875rem;
  opacity: 0.9;
}

.rating-section {
  margin-bottom: 24px;
}

.stars {
  display: flex;
  gap: 4px;
  margin-bottom: 8px;
  color: #fbbf24;
}

.rating-text {
  font-size: 0.875rem;
  opacity: 0.9;
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
@media (max-width: 1024px) {
  .auth-container {
    grid-template-columns: 1fr;
    max-width: 500px;
  }
  
  .auth-benefits-section {
    display: none;
  }
}

@media (max-width: 768px) {
  .auth-form-section {
    padding: 24px;
  }
  
  .auth-title {
    font-size: 1.5rem;
  }
  
  .role-selection {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .auth-page {
    padding: 12px;
  }
  
  .auth-form-section {
    padding: 16px;
  }
}
</style>
