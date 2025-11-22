// Initialize AOS
AOS.init({
    duration: 1000,
    once: true,
    offset: 100
});

// Mobile Menu Toggle
const menuToggle = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');

menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        navLinks.classList.remove('active');
    });
});

// Smooth Scrolling
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

// Function to show message
function showMessage(message, type) {
    const messageDiv = document.getElementById('form-message');
    messageDiv.textContent = message;
    messageDiv.className = `form-message ${type}`;
    messageDiv.style.display = 'block';

    // Auto hide after 5 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Contact Form Submission
const contactForm = document.querySelector('.contact-form');

if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;

        // Get form data
        const formData = new FormData();
        formData.append('name', contactForm.querySelector('input[name="name"]').value);
        formData.append('phone', contactForm.querySelector('input[name="phone"]').value);
        formData.append('message', contactForm.querySelector('textarea[name="message"]').value);

        // Disable button and show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        try {
            const response = await fetch('send-email.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                showMessage('✅ ' + result.message, 'success');
                // Reset form
                contactForm.reset();
            } else {
                // Show error message
                if (result.errors) {
                    showMessage('❌ ' + result.errors.join(', '), 'error');
                } else {
                    showMessage('❌ ' + result.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('❌ An error occurred. Please try again later.', 'error');
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        }
    });
}
