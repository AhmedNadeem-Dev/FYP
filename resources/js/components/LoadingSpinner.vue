<template>
  <div class="loading-container" :class="{ 'overlay': overlay }">
    <div class="loading-content">
      <div class="spinner" :class="size"></div>
      <p v-if="message" class="loading-message">{{ message }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    message: {
      type: String,
      default: 'Loading...'
    },
    size: {
      type: String,
      default: 'medium',
      validator: value => ['small', 'medium', 'large'].includes(value)
    },
    overlay: {
      type: Boolean,
      default: false
    }
  }
};
</script>

<style scoped>
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.loading-container.overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.8);
  z-index: 9999;
}

.loading-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.spinner {
  border-radius: 50%;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #3B1E54;
  animation: spin 1s linear infinite;
}

.spinner.small {
  width: 20px;
  height: 20px;
  border-width: 2px;
}

.spinner.medium {
  width: 40px;
  height: 40px;
  border-width: 3px;
}

.spinner.large {
  width: 60px;
  height: 60px;
  border-width: 4px;
}

.loading-message {
  color: #6c757d;
  font-size: 1rem;
  margin: 0;
  text-align: center;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 
