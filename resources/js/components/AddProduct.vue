<template>
  <div class="add-product">
    <!-- Import Design System -->
    <link rel="stylesheet" href="/css/design-system.css">
    
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <button class="back-btn" @click="$router.go(-1)">
          <i class="icon">←</i>
          Back
        </button>
        <div class="header-info">
          <h1 class="page-title">Add New Product</h1>
          <p class="page-subtitle">List your scrap materials for sale</p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="page-content">
      <div class="form-container">
        <!-- Product Form -->
        <form @submit.prevent="addProduct" class="product-form">
          <!-- Basic Information -->
          <div class="form-section">
            <h3 class="section-title">Basic Information</h3>
            
            <div class="form-group">
              <label for="name" class="form-label">Product Name *</label>
              <input
                type="text"
                id="name"
                v-model="product.name"
                class="form-input"
                :class="{ 'error': errors.name }"
                placeholder="Enter product name"
                required
              />
              <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="description" class="form-label">Description *</label>
              <textarea
                id="description"
                v-model="product.description"
                class="form-textarea"
                :class="{ 'error': errors.description }"
                placeholder="Describe your product in detail"
                rows="4"
                required
              ></textarea>
              <span v-if="errors.description" class="error-message">{{ errors.description }}</span>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="category" class="form-label">Category *</label>
                <select
                  id="category"
                  v-model="product.category_id"
                  class="form-select"
                  :class="{ 'error': errors.category_id }"
                  required
                >
                  <option value="">Select category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                <span v-if="errors.category_id" class="error-message">{{ errors.category_id }}</span>
              </div>

              <div class="form-group">
                <label for="price" class="form-label">Price (USD) *</label>
                <div class="price-input">
                  <span class="price-symbol">$</span>
                  <input
                    type="number"
                    id="price"
                    v-model="product.price"
                    class="form-input"
                    :class="{ 'error': errors.price }"
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                    required
                  />
                </div>
                <span v-if="errors.price" class="error-message">{{ errors.price }}</span>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="quantity" class="form-label">Quantity *</label>
                <input
                  type="number"
                  id="quantity"
                  v-model="product.quantity"
                  class="form-input"
                  :class="{ 'error': errors.quantity }"
                  placeholder="Enter quantity"
                  min="1"
                  required
                />
                <span v-if="errors.quantity" class="error-message">{{ errors.quantity }}</span>
              </div>

              <div class="form-group">
                <label for="condition" class="form-label">Condition *</label>
                <select
                  id="condition"
                  v-model="product.condition"
                  class="form-select"
                  :class="{ 'error': errors.condition }"
                  required
                >
                  <option value="">Select condition</option>
                  <option value="excellent">Excellent</option>
                  <option value="good">Good</option>
                  <option value="fair">Fair</option>
                  <option value="poor">Poor</option>
                </select>
                <span v-if="errors.condition" class="error-message">{{ errors.condition }}</span>
              </div>
            </div>
          </div>

          <!-- Product Images -->
          <div class="form-section">
            <h3 class="section-title">Product Images</h3>
            <p class="section-description">Add up to 5 images to showcase your product</p>
            
            <div class="image-upload-section">
              <div class="image-grid">
                <!-- Existing Images -->
                <div v-for="(image, index) in product.images" :key="index" class="image-item">
                  <div class="image-preview">
                    <img :src="getImageUrl(image)" :alt="`Product image ${index + 1}`" />
                    <button type="button" class="remove-image" @click="removeImage(index)">
                      <i class="icon">×</i>
                    </button>
                  </div>
                </div>
                
                <!-- Upload New Image -->
                <div v-if="product.images.length < 5" class="upload-slot">
                  <label for="image-upload" class="upload-label">
                    <div class="upload-content">
                      <i class="upload-icon">📷</i>
                      <span class="upload-text">Add Image</span>
                    </div>
                  </label>
                  <input
                    type="file"
                    id="image-upload"
                    @change="handleImageUpload"
                    accept="image/*"
                    class="upload-input"
                  />
                </div>
              </div>
              
              <div class="upload-info">
                <p class="info-text">
                  <strong>Tips:</strong> Use high-quality images with good lighting. 
                  Show different angles and any defects clearly.
                </p>
              </div>
            </div>
          </div>

          <!-- Additional Details -->
          <div class="form-section">
            <h3 class="section-title">Additional Details</h3>
            
            <div class="form-group">
              <label for="location" class="form-label">Location</label>
              <input
                type="text"
                id="location"
                v-model="product.location"
                class="form-input"
                placeholder="City, State (optional)"
              />
            </div>

            <div class="form-group">
              <label for="tags" class="form-label">Tags</label>
              <input
                type="text"
                id="tags"
                v-model="product.tags"
                class="form-input"
                placeholder="e.g., recyclable, metal, vintage (comma-separated)"
              />
              <span class="help-text">Separate tags with commas to help buyers find your product</span>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input
                  type="checkbox"
                  v-model="product.is_negotiable"
                  class="checkbox-input"
                />
                <span class="checkbox-text">Price is negotiable</span>
              </label>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" @click="$router.go(-1)">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="loading-spinner"></span>
              {{ isSubmitting ? 'Adding Product...' : 'Add Product' }}
            </button>
          </div>
        </form>

        <!-- Preview Card -->
        <div class="preview-section">
          <h3 class="section-title">Preview</h3>
          <div class="product-preview">
            <div class="preview-image">
              <img 
                :src="product.images.length > 0 ? getImageUrl(product.images[0]) : '/images/placeholder.jpg'" 
                :alt="product.name || 'Product preview'"
                @error="handleImageError"
              />
            </div>
            <div class="preview-content">
              <h4 class="preview-title">{{ product.name || 'Product Name' }}</h4>
              <p class="preview-price">${{ product.price || '0.00' }}</p>
              <p class="preview-description">{{ product.description || 'Product description will appear here...' }}</p>
              <div class="preview-details">
                <span class="preview-detail">Qty: {{ product.quantity || 0 }}</span>
                <span class="preview-detail">{{ product.condition || 'Condition' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="modal-overlay" @click="closeSuccessModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Product Added Successfully!</h3>
        </div>
        <div class="modal-body">
          <div class="success-icon">✅</div>
          <p>Your product has been listed successfully and is now available for purchase.</p>
        </div>
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="addAnother">
            Add Another Product
          </button>
          <button class="btn btn-primary" @click="viewProduct">
            View Product
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AddProduct",
  data() {
    return {
      product: {
        name: "",
        description: "",
        category_id: "",
        price: "",
        quantity: "",
        condition: "",
        location: "",
        tags: "",
        is_negotiable: false,
        images: []
      },
      categories: [],
      errors: {},
      isSubmitting: false,
      showSuccessModal: false,
      addedProductId: null
    };
  },
  
  mounted() {
    this.loadCategories();
    this.checkAuthentication();
  },
  
  methods: {
    checkAuthentication() {
      const session = localStorage.getItem("userSession");
      if (!session) {
        this.$router.push("/login");
        return;
      }
      
      const userData = JSON.parse(session);
      if (userData.role !== "scrapSeller") {
        this.$router.push("/");
      }
    },
    
    async loadCategories() {
      try {
        const response = await fetch('/api/categories');
        if (response.ok) {
          this.categories = await response.json();
        }
      } catch (error) {
        console.error('Error loading categories:', error);
        // Fallback categories
        this.categories = [
          { id: 1, name: 'Metals' },
          { id: 2, name: 'Plastics' },
          { id: 3, name: 'Electronics' },
          { id: 4, name: 'Paper' },
          { id: 5, name: 'Glass' },
          { id: 6, name: 'Textiles' },
          { id: 7, name: 'Wood' },
          { id: 8, name: 'Other' }
        ];
      }
    },
    
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (!file) return;
      
      // Validate file type
      const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
      if (!allowedTypes.includes(file.type)) {
        this.showError('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
        return;
      }
      
      // Validate file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        this.showError('Image size must be less than 5MB');
        return;
      }
      
      // Create preview
      const reader = new FileReader();
      reader.onload = (e) => {
        this.product.images.push(e.target.result);
      };
      reader.readAsDataURL(file);
      
      // Reset input
      event.target.value = '';
    },
    
    removeImage(index) {
      this.product.images.splice(index, 1);
    },
    
    getImageUrl(image) {
      if (typeof image === 'string') {
        return image.startsWith('data:') ? image : `/images/${image}`;
      }
      return '/images/placeholder.jpg';
    },
    
    handleImageError(event) {
      event.target.src = '/images/placeholder.jpg';
    },
    
    validateForm() {
      this.errors = {};
      
      if (!this.product.name?.trim()) {
        this.errors.name = 'Product name is required';
      } else if (this.product.name.length < 3) {
        this.errors.name = 'Product name must be at least 3 characters';
      }
      
      if (!this.product.description?.trim()) {
        this.errors.description = 'Description is required';
      } else if (this.product.description.length < 10) {
        this.errors.description = 'Description must be at least 10 characters';
      }
      
      if (!this.product.category_id) {
        this.errors.category_id = 'Category is required';
      }
      
      if (!this.product.price) {
        this.errors.price = 'Price is required';
      } else if (parseFloat(this.product.price) <= 0) {
        this.errors.price = 'Price must be greater than 0';
      }
      
      if (!this.product.quantity) {
        this.errors.quantity = 'Quantity is required';
      } else if (parseInt(this.product.quantity) <= 0) {
        this.errors.quantity = 'Quantity must be greater than 0';
      }
      
      if (!this.product.condition) {
        this.errors.condition = 'Condition is required';
      }
      
      return Object.keys(this.errors).length === 0;
    },
    
    async addProduct() {
      if (!this.validateForm()) {
        this.showError('Please fix the errors in the form');
        return;
      }
      
      this.isSubmitting = true;
      
      try {
        const token = localStorage.getItem('access_token');
        const formData = new FormData();
        
        // Add product data
        Object.keys(this.product).forEach(key => {
          if (key !== 'images') {
            formData.append(key, this.product[key]);
          }
        });
        
        // Add images
        this.product.images.forEach((image, index) => {
          if (image.startsWith('data:')) {
            // Convert base64 to blob
            const byteCharacters = atob(image.split(',')[1]);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
              byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], { type: 'image/jpeg' });
            formData.append(`images[${index}]`, blob, `image_${index}.jpg`);
          }
        });
        
        const response = await fetch('/api/products', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          },
          body: formData
        });
        
        if (response.ok) {
          const data = await response.json();
          this.addedProductId = data.product?.id;
          this.showSuccessModal = true;
        } else {
          const errorData = await response.json();
          this.showError(errorData.message || 'Failed to add product');
        }
      } catch (error) {
        console.error('Error adding product:', error);
        this.showError('An error occurred while adding the product');
      } finally {
        this.isSubmitting = false;
      }
    },
    
    showError(message) {
      // You can implement a toast notification here
      alert(message);
    },
    
    closeSuccessModal() {
      this.showSuccessModal = false;
      this.$router.push('/manage-products');
    },
    
    addAnother() {
      this.showSuccessModal = false;
      this.resetForm();
    },
    
    viewProduct() {
      this.showSuccessModal = false;
      if (this.addedProductId) {
        this.$router.push(`/product/${this.addedProductId}`);
      } else {
        this.$router.push('/manage-products');
      }
    },
    
    resetForm() {
      this.product = {
        name: "",
        description: "",
        category_id: "",
        price: "",
        quantity: "",
        condition: "",
        location: "",
        tags: "",
        is_negotiable: false,
        images: []
      };
      this.errors = {};
    }
  }
};
</script>

<style scoped>
/* Import design system */
@import url('../../css/design-system.css');

.add-product {
  min-height: 100vh;
  background: var(--neutral-50);
}

/* Page Header */
.page-header {
  background: var(--neutral-0);
  border-bottom: 1px solid var(--neutral-200);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
}

.header-content {
  display: flex;
  align-items: center;
  gap: var(--space-4);
  max-width: 1200px;
  margin: 0 auto;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-4);
  background: var(--neutral-100);
  border: 1px solid var(--neutral-200);
  border-radius: var(--radius-lg);
  color: var(--neutral-700);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.back-btn:hover {
  background: var(--neutral-200);
  border-color: var(--neutral-300);
}

.header-info {
  flex: 1;
}

.page-title {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--neutral-800);
  margin: 0 0 var(--space-1) 0;
}

.page-subtitle {
  font-size: var(--text-base);
  color: var(--neutral-600);
  margin: 0;
}

/* Main Content */
.page-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: var(--space-6);
}

.form-container {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: var(--space-8);
}

/* Product Form */
.product-form {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
}

.form-section {
  margin-bottom: var(--space-8);
}

.form-section:last-child {
  margin-bottom: 0;
}

.section-title {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-4);
  padding-bottom: var(--space-2);
  border-bottom: 2px solid var(--success-200);
}

.section-description {
  font-size: var(--text-sm);
  color: var(--neutral-600);
  margin-bottom: var(--space-4);
}

.form-group {
  margin-bottom: var(--space-4);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-4);
}

.form-label {
  display: block;
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--neutral-700);
  margin-bottom: var(--space-2);
}

.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: var(--space-3);
  border: 1px solid var(--neutral-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-base);
  transition: all var(--transition-fast);
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: var(--success-400);
  box-shadow: 0 0 0 3px var(--success-100);
}

.form-input.error,
.form-textarea.error,
.form-select.error {
  border-color: var(--error-400);
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.price-input {
  position: relative;
}

.price-symbol {
  position: absolute;
  left: var(--space-3);
  top: 50%;
  transform: translateY(-50%);
  color: var(--neutral-500);
  font-weight: var(--font-medium);
}

.price-input .form-input {
  padding-left: var(--space-6);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  cursor: pointer;
}

.checkbox-input {
  width: 18px;
  height: 18px;
  accent-color: var(--success-600);
}

.checkbox-text {
  font-size: var(--text-base);
  color: var(--neutral-700);
}

.error-message {
  display: block;
  color: var(--error-600);
  font-size: var(--text-sm);
  margin-top: var(--space-1);
}

.help-text {
  display: block;
  color: var(--neutral-500);
  font-size: var(--text-sm);
  margin-top: var(--space-1);
}

/* Image Upload */
.image-upload-section {
  margin-bottom: var(--space-4);
}

.image-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: var(--space-3);
  margin-bottom: var(--space-4);
}

.image-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.image-preview {
  width: 100%;
  height: 100%;
  position: relative;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-image {
  position: absolute;
  top: var(--space-2);
  right: var(--space-2);
  width: 24px;
  height: 24px;
  background: var(--error-600);
  color: var(--neutral-0);
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--text-sm);
  transition: all var(--transition-fast);
}

.remove-image:hover {
  background: var(--error-700);
}

.upload-slot {
  aspect-ratio: 1;
}

.upload-label {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  border: 2px dashed var(--neutral-300);
  border-radius: var(--radius-lg);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.upload-label:hover {
  border-color: var(--success-400);
  background: var(--success-50);
}

.upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-2);
  color: var(--neutral-500);
}

.upload-icon {
  font-size: var(--text-2xl);
}

.upload-text {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
}

.upload-input {
  display: none;
}

.upload-info {
  padding: var(--space-3);
  background: var(--success-50);
  border-radius: var(--radius-lg);
  border: 1px solid var(--success-200);
}

.info-text {
  font-size: var(--text-sm);
  color: var(--success-700);
  margin: 0;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: var(--space-4);
  justify-content: flex-end;
  padding-top: var(--space-6);
  border-top: 1px solid var(--neutral-200);
}

.btn {
  padding: var(--space-3) var(--space-6);
  border-radius: var(--radius-lg);
  font-size: var(--text-base);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--transition-fast);
  display: flex;
  align-items: center;
  gap: var(--space-2);
  text-decoration: none;
  border: 1px solid transparent;
}

.btn-primary {
  background: var(--success-600);
  color: var(--neutral-0);
}

.btn-primary:hover:not(:disabled) {
  background: var(--success-700);
}

.btn-primary:disabled {
  background: var(--neutral-300);
  cursor: not-allowed;
}

.btn-secondary {
  background: var(--neutral-0);
  color: var(--neutral-700);
  border-color: var(--neutral-300);
}

.btn-secondary:hover {
  background: var(--neutral-50);
  border-color: var(--neutral-400);
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid var(--neutral-0);
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Preview Section */
.preview-section {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
  height: fit-content;
  position: sticky;
  top: var(--space-6);
}

.product-preview {
  border: 1px solid var(--neutral-200);
  border-radius: var(--radius-lg);
  overflow: hidden;
  transition: all var(--transition-fast);
}

.product-preview:hover {
  box-shadow: var(--shadow-md);
}

.preview-image {
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.preview-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.preview-content {
  padding: var(--space-4);
}

.preview-title {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--space-2);
}

.preview-price {
  font-size: var(--text-xl);
  font-weight: var(--font-bold);
  color: var(--success-600);
  margin-bottom: var(--space-3);
}

.preview-description {
  font-size: var(--text-sm);
  color: var(--neutral-600);
  margin-bottom: var(--space-3);
  line-height: 1.5;
}

.preview-details {
  display: flex;
  gap: var(--space-3);
}

.preview-detail {
  font-size: var(--text-xs);
  color: var(--neutral-500);
  background: var(--neutral-100);
  padding: var(--space-1) var(--space-2);
  border-radius: var(--radius-md);
}

/* Success Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--neutral-0);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  max-width: 400px;
  width: 90%;
  box-shadow: var(--shadow-lg);
}

.modal-header {
  text-align: center;
  margin-bottom: var(--space-4);
}

.modal-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--neutral-800);
  margin: 0;
}

.modal-body {
  text-align: center;
  margin-bottom: var(--space-6);
}

.success-icon {
  font-size: var(--text-4xl);
  margin-bottom: var(--space-3);
}

.modal-body p {
  color: var(--neutral-600);
  margin: 0;
}

.modal-actions {
  display: flex;
  gap: var(--space-3);
  justify-content: center;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .form-container {
    grid-template-columns: 1fr;
    gap: var(--space-6);
  }
  
  .preview-section {
    position: static;
  }
}

@media (max-width: 768px) {
  .page-content {
    padding: var(--space-4);
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--space-3);
  }
  
  .image-grid {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  }
  
  .modal-actions {
    flex-direction: column;
  }
}
</style>
