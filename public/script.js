// script.js  Frontend JavaScript for car dealership website
// This file contains all client-side functionality

// Wait for DOM to load before executing scripts
document.addEventListener('DOMContentLoaded', function() {
    console.log('Car dealership website loaded');
    
    // Initialize the page based on current URL
    initPage();
    
    // Setup event listeners for forms
    setupFormValidation();
});

// Initialize page-specific functionality
function initPage() {
    const path = window.location.pathname;
    
    if (path.includes('index.html') || path === '/') {
        loadFeaturedCars();
    } else if (path.includes('cars.php')) {
            // loadAllCars() was referenced but not implemented; avoid calling it
            setupSearchFilter();
    } else if (path.includes('register.php')) {
        setupRegistrationForm();
    } else if (path.includes('login.php')) {
        setupLoginForm();
    }
}

// Load featured cars on homepage
async function loadFeaturedCars() {
    const container = document.getElementById('featuredCars');
    if (!container) return;
    
    try {
        // In a real application, this would fetch from an API
        // For demo, we'll use mock data
        const mockCars = [
            { id: 1, make: 'Toyota', model: 'Camry', year: 2022, price: 25000, image: 'default_car.jpg' },
            { id: 2, make: 'Honda', model: 'Civic', year: 2021, price: 22000, image: 'default_car.jpg' },
            { id: 3, make: 'Ford', model: 'Mustang', year: 2020, price: 35000, image: 'default_car.jpg' }
        ];
        
        container.innerHTML = '';
        
        mockCars.forEach(car => {
            const carCard = createCarCard(car);
            container.appendChild(carCard);
        });
        
    } catch (error) {
        console.error('Error loading featured cars:', error);
        container.innerHTML = '<p>Unable to load featured cars. Please try again later.</p>';
    }
}

// Create HTML card for a car
function createCarCard(car) {
    const card = document.createElement('div');
    card.className = 'car-card';
    
    card.innerHTML = `
        <div class="car-image">
            <i class="fas fa-car" style="font-size: 60px; color: #666;"></i>
        </div>
        <div class="car-details">
            <h3>${car.year} ${car.make} ${car.model}</h3>
           <div class="car-price">TZS ${car.price.toLocaleString()}</div>

            <p><i class="fas fa-gas-pump"></i> Petrol | <i class="fas fa-cog"></i> Automatic</p>
            <a href="cars.php?id=${car.id}" class="btn" style="display: block; text-align: center; margin-top: 10px;">
                View Details
            </a>
        </div>
    `;
    
    return card;
}

// Setup form validation
function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault(); // Stop form submission if validation fails
            }
        });
    });
}

// Generic form validation
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    // Clear previous errors
    clearErrors(form);
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showError(input, 'This field is required');
            isValid = false;
        }
        
        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                showError(input, 'Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Password validation (for registration)
        if (input.type === 'password' && input.value) {
            if (input.value.length < 6) {
                showError(input, 'Password must be at least 6 characters');
                isValid = false;
            }
        }
    });
    
    // Confirm password validation
    const password = form.querySelector('#password');
    const confirmPassword = form.querySelector('#confirm_password');
    if (password && confirmPassword && password.value !== confirmPassword.value) {
        showError(confirmPassword, 'Passwords do not match');
        isValid = false;
    }
    
    return isValid;
}

// Show error message for form field
function showError(input, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error';
    errorDiv.textContent = message;
    input.parentNode.appendChild(errorDiv);
    input.style.borderColor = '#e74c3c';
}

// Clear all error messages
function clearErrors(form) {
    const errors = form.querySelectorAll('.error');
    errors.forEach(error => error.remove());
    
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => input.style.borderColor = '#ddd');
}

// Setup search and filter for cars page
function setupSearchFilter() {
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterCars(this.value.toLowerCase());
        });
    }
    
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });
    }
}

// Filter cars based on search input
function filterCars(searchTerm) {
    const carCards = document.querySelectorAll('.car-card');
    
    carCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Apply advanced filters
function applyFilters() {
    const minPrice = document.getElementById('minPrice').value;
    const maxPrice = document.getElementById('maxPrice').value;
    const make = document.getElementById('makeFilter').value;
        // Submit the form so server-side filtering/search handles results
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            // If a submit handler prevented default previously, attempt a normal submit
            filterForm.onsubmit = null;
            filterForm.submit();
        }
}