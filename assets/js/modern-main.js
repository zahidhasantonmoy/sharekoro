// assets/js/modern-main.js - Modern JavaScript with enhanced animations and functionality

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all animations
    initAnimations();
    
    // Initialize all interactive elements
    initInteractiveElements();
    
    // Initialize form enhancements
    initFormEnhancements();
    
    // Initialize share functionality
    initShareFunctionality();
});

// Initialize animations
function initAnimations() {
    // Fade in elements when they come into view
    const fadeElements = document.querySelectorAll('.feature-card, .hero-content, .stat-card, .share-item');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    fadeElements.forEach(element => {
        element.style.opacity = 0;
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
    
    // Add hover effects to share options with enhanced animations
    const shareOptions = document.querySelectorAll('.share-option');
    
    shareOptions.forEach(option => {
        option.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.05)';
            this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });
        
        option.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });
    });
    
    // Add animation to buttons
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.transition = 'all 0.3s ease';
        });
        
        // Add ripple effect
        button.addEventListener('click', function(e) {
            createRipple(e, this);
        });
    });
}

// Create ripple effect for buttons
function createRipple(event, element) {
    const circle = document.createElement("span");
    circle.classList.add("ripple");
    
    const diameter = Math.max(element.clientWidth, element.clientHeight);
    const radius = diameter / 2;
    
    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = `${event.clientX - element.getBoundingClientRect().left - radius}px`;
    circle.style.top = `${event.clientY - element.getBoundingClientRect().top - radius}px`;
    circle.classList.add("ripple");
    
    const ripple = element.getElementsByClassName("ripple")[0];
    if (ripple) {
        ripple.remove();
    }
    
    element.appendChild(circle);
    
    // Remove ripple after animation
    setTimeout(() => {
        if (circle.parentNode) {
            circle.parentNode.removeChild(circle);
        }
    }, 600);
}

// Initialize interactive elements
function initInteractiveElements() {
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add animation to modal open/close
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });
    
    // Close button functionality
    const closeButtons = document.querySelectorAll('.close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                closeModal(modal);
            }
        });
    });
}

// Initialize form enhancements
function initFormEnhancements() {
    // Toggle visibility options
    const visibilitySelect = document.getElementById('visibility');
    if (visibilitySelect) {
        visibilitySelect.addEventListener('change', toggleVisibilityOptions);
        toggleVisibilityOptions(); // Initialize on load
    }
    
    // Password visibility toggle
    const passwordToggles = document.querySelectorAll('[onclick*="togglePasswordVisibility"]');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const inputId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            const iconId = this.querySelector('i').id;
            togglePasswordVisibility(inputId, iconId);
        });
    });
    
    // Access code generator
    const accessCodeGenerator = document.querySelector('[onclick*="generateAccessCode"]');
    if (accessCodeGenerator) {
        accessCodeGenerator.addEventListener('click', generateAccessCode);
    }
}

// Initialize share functionality
function initShareFunctionality() {
    // Initialize social sharing
    initSocialSharing();
}

// Toggle visibility options based on selection
function toggleVisibilityOptions() {
    const visibility = document.getElementById('visibility')?.value;
    const privateOption = document.querySelector('.private-option');
    const protectedOption = document.querySelector('.protected-option');
    const isPublicCheckbox = document.getElementById('is_public');
    
    // Hide all options first
    if (privateOption) privateOption.style.display = 'none';
    if (protectedOption) protectedOption.style.display = 'none';
    
    // Show relevant option
    if (visibility === 'private') {
        if (privateOption) privateOption.style.display = 'block';
        if (isPublicCheckbox) {
            isPublicCheckbox.checked = false;
            isPublicCheckbox.disabled = true;
        }
    } else if (visibility === 'protected') {
        if (protectedOption) protectedOption.style.display = 'block';
        if (isPublicCheckbox) {
            isPublicCheckbox.checked = false;
            isPublicCheckbox.disabled = true;
        }
        generateAccessCode();
    } else {
        if (isPublicCheckbox) {
            isPublicCheckbox.checked = true;
            isPublicCheckbox.disabled = false;
        }
    }
}

// Generate random 4-character access code
function generateAccessCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 4; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    const accessCodeInput = document.getElementById('access_code');
    if (accessCodeInput) {
        accessCodeInput.value = code;
    }
    showNotification('Access code generated: ' + code);
}

// Function to toggle password visibility
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

// Function to copy text to clipboard with enhanced feedback
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copied to clipboard!');
        }).catch(err => {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

// Fallback for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showNotification('Copied to clipboard!');
        } else {
            showNotification('Failed to copy. Please try again.');
        }
    } catch (err) {
        showNotification('Failed to copy. Please try again.');
    }
    
    document.body.removeChild(textArea);
}

// Function to show notification with enhanced styling
function showNotification(message) {
    // Remove existing notification if any
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
        <span class="close-notification" onclick="this.parentElement.remove()">&times;</span>
    `;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after 4 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }
    }, 4000);
}

// Function to close modal with animation
function closeModal(modal) {
    modal.style.opacity = '0';
    modal.style.transform = 'scale(0.9)';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.opacity = '1';
        modal.style.transform = 'scale(1)';
    }, 300);
}

// Social sharing functionality
function initSocialSharing() {
    // Facebook sharing
    const facebookShareBtns = document.querySelectorAll('.btn-facebook');
    facebookShareBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&t=${title}`, '_blank', 'width=600,height=400');
        });
    });
    
    // Twitter sharing
    const twitterShareBtns = document.querySelectorAll('.btn-twitter');
    twitterShareBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent(document.title);
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
        });
    });
    
    // LinkedIn sharing
    const linkedinShareBtns = document.querySelectorAll('.btn-linkedin');
    linkedinShareBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}`, '_blank', 'width=600,height=400');
        });
    });
}

// Enhanced print functionality
function enhancedPrint() {
    // Add print-specific styles
    const printStyle = document.createElement('style');
    printStyle.innerHTML = `
        @media print {
            .header, .nav, .footer, .share-actions, .btn {
                display: none !important;
            }
            .share-content {
                border: none !important;
                box-shadow: none !important;
                background: white !important;
                color: black !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
        }
    `;
    document.head.appendChild(printStyle);
    
    // Print after a short delay to ensure styles are applied
    setTimeout(() => {
        window.print();
        // Remove print styles after printing
        setTimeout(() => {
            document.head.removeChild(printStyle);
        }, 1000);
    }, 100);
}

// Enhanced download functionality
function enhancedDownload(url, filename) {
    showNotification('Preparing download...');
    
    // Create a temporary link
    const link = document.createElement('a');
    link.href = url;
    link.download = filename || '';
    link.style.display = 'none';
    
    // Add to document and trigger click
    document.body.appendChild(link);
    link.click();
    
    // Remove link after a short delay
    setTimeout(() => {
        document.body.removeChild(link);
        showNotification('Download started!');
    }, 100);
}