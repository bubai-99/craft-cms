// Contact Form Handler
export class ContactForm {
  constructor(formSelector) {
    this.form = document.querySelector(formSelector);
    this.init();
  }

  init() {
    if (!this.form) return;
    
    this.form.addEventListener('submit', this.handleSubmit.bind(this));
    this.addValidation();
  }

  async handleSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this.form);
    const submitBtn = this.form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.textContent = 'Sending...';
    submitBtn.disabled = true;
    
    try {
      // In a real application, you'd send this to your backend
      // For demo purposes, we'll simulate a successful submission
      await this.simulateSubmission(formData);
      
      this.showSuccess();
      this.form.reset();
    } catch (error) {
      this.showError('Sorry, there was an error sending your message. Please try again.');
    } finally {
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  }

  async simulateSubmission(formData) {
    // Simulate API call
    return new Promise((resolve) => {
      setTimeout(() => {
        console.log('Form data:', Object.fromEntries(formData));
        resolve();
      }, 1000);
    });
  }

  addValidation() {
    const fields = this.form.querySelectorAll('input, textarea');
    
    fields.forEach(field => {
      field.addEventListener('blur', () => this.validateField(field));
      field.addEventListener('input', () => this.clearFieldError(field));
    });
  }

  validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';

    if (field.hasAttribute('required') && !value) {
      isValid = false;
      message = 'This field is required.';
    } else if (field.type === 'email' && value && !this.isValidEmail(value)) {
      isValid = false;
      message = 'Please enter a valid email address.';
    }

    if (!isValid) {
      this.showFieldError(field, message);
    } else {
      this.clearFieldError(field);
    }

    return isValid;
  }

  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  showFieldError(field, message) {
    this.clearFieldError(field);
    
    field.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
  }

  clearFieldError(field) {
    field.classList.remove('error');
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
      existingError.remove();
    }
  }

  showSuccess() {
    this.showMessage('Thank you! Your message has been sent successfully.', 'success');
  }

  showError(message) {
    this.showMessage(message, 'error');
  }

  showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.form-message');
    existingMessages.forEach(msg => msg.remove());
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `form-message form-message--${type}`;
    messageDiv.textContent = message;
    
    this.form.insertBefore(messageDiv, this.form.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
      messageDiv.remove();
    }, 5000);
  }
}

// Initialize contact form when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new ContactForm('#contact-form');
});