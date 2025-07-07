<template>
  <div class="error-boundary">
    <div v-if="hasError" class="error-container">
      <div class="error-icon">⚠️</div>
      <h2 class="error-title">Something went wrong</h2>
      <p class="error-message">{{ errorMessage }}</p>
      <div class="error-actions">
        <button @click="retry" class="retry-button">Try Again</button>
        <button @click="goHome" class="home-button">Go Home</button>
      </div>
      <details v-if="showDetails" class="error-details">
        <summary>Error Details</summary>
        <pre>{{ errorDetails }}</pre>
      </details>
    </div>
    <slot v-else></slot>
  </div>
</template>

<script>
export default {
  name: 'ErrorBoundary',
  data() {
    return {
      hasError: false,
      errorMessage: 'An unexpected error occurred.',
      errorDetails: '',
      showDetails: false
    };
  },
  errorCaptured(err, instance, info) {
    this.hasError = true;
    this.errorMessage = err.message || 'An unexpected error occurred.';
    this.errorDetails = `${err.stack}\n\nComponent: ${info}`;
    this.showDetails = process.env.NODE_ENV === 'development';
    
    // Log error for debugging
    console.error('Error caught by ErrorBoundary:', err);
    console.error('Component info:', info);
    
    // Prevent error from propagating
    return false;
  },
  methods: {
    retry() {
      this.hasError = false;
      this.errorMessage = 'An unexpected error occurred.';
      this.errorDetails = '';
      
      // Force re-render
      this.$forceUpdate();
    },
    goHome() {
      this.$router.push('/');
    }
  }
};
</script>

<style scoped>
.error-boundary {
  width: 100%;
  height: 100%;
}

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  padding: 2rem;
  text-align: center;
  background-color: #f8f9fa;
  border-radius: 8px;
  margin: 2rem;
}

.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.error-title {
  font-size: 2rem;
  font-weight: 600;
  color: #dc3545;
  margin-bottom: 1rem;
}

.error-message {
  font-size: 1.1rem;
  color: #6c757d;
  margin-bottom: 2rem;
  max-width: 600px;
}

.error-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.retry-button,
.home-button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.retry-button {
  background-color: #007bff;
  color: white;
}

.retry-button:hover {
  background-color: #0056b3;
}

.home-button {
  background-color: #6c757d;
  color: white;
}

.home-button:hover {
  background-color: #545b62;
}

.error-details {
  max-width: 800px;
  text-align: left;
}

.error-details summary {
  cursor: pointer;
  font-weight: 600;
  margin-bottom: 1rem;
}

.error-details pre {
  background-color: #f1f3f4;
  padding: 1rem;
  border-radius: 4px;
  overflow-x: auto;
  font-size: 0.875rem;
  white-space: pre-wrap;
}

@media (max-width: 768px) {
  .error-container {
    margin: 1rem;
    padding: 1rem;
  }
  
  .error-title {
    font-size: 1.5rem;
  }
  
  .error-actions {
    flex-direction: column;
    width: 100%;
    max-width: 300px;
  }
}
</style> 
