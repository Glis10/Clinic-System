// DOM Elements
const doctorSelect = document.getElementById('doctor_id');
const dateInput = document.getElementById('appointment_date');
const timeSelect = document.getElementById('appointment_time');
const loadingSlots = document.getElementById('loading-slots');
const searchTypeSelect = document.getElementById('search_type');
const searchValueInput = document.getElementById('search_value');
const searchHint = document.getElementById('search_hint');

// AJAX function to fetch available time slots
function fetchAvailableSlots() {
    if (!doctorSelect || !dateInput || !timeSelect) return;
    
    const doctorId = doctorSelect.value;
    const date = dateInput.value;
    
    if (!doctorId || !date) {
        timeSelect.innerHTML = '<option value="">-- Select Date & Doctor First --</option>';
        return;
    }
    
    // Show loading indicator
    if (loadingSlots) loadingSlots.style.display = 'block';
    timeSelect.disabled = true;
    
    // Get current appointment ID if editing
    const currentAppointmentId = document.getElementById('current_appointment_id')?.value || '';
    
    // Build URL with parameters
    let url = `get_slots.php?doctor_id=${doctorId}&date=${date}`;
    if (currentAppointmentId) {
        url += `&exclude_id=${currentAppointmentId}`;
    }
    
    // Fetch available slots
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (loadingSlots) loadingSlots.style.display = 'none';
            timeSelect.disabled = false;
            
            if (data.success && data.slots.length > 0) {
                // Clear existing options
                timeSelect.innerHTML = '<option value="">-- Choose Time Slot --</option>';
                
                // Add available slots
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.value;
                    option.textContent = slot.display;
                    timeSelect.appendChild(option);
                });
                
                // Show info message
                showSlotInfo(`✅ ${data.count} time slot(s) available`, 'success');
            } else {
                timeSelect.innerHTML = '<option value="">No slots available</option>';
                showSlotInfo('⚠️ No time slots available for this date and doctor', 'warning');
            }
        })
        .catch(error => {
            console.error('Error fetching slots:', error);
            if (loadingSlots) loadingSlots.style.display = 'none';
            timeSelect.disabled = false;
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            showSlotInfo('❌ Error loading time slots. Please try again.', 'error');
        });
}

// Show info message
function showSlotInfo(message, type) {
    const slotInfo = document.getElementById('slot-info');
    if (!slotInfo) return;
    
    slotInfo.textContent = message;
    slotInfo.style.display = 'block';
    
    // Set color based on type
    if (type === 'success') {
        slotInfo.style.background = '#d4edda';
        slotInfo.style.color = '#155724';
    } else if (type === 'warning') {
        slotInfo.style.background = '#fff3cd';
        slotInfo.style.color = '#856404';
    } else if (type === 'error') {
        slotInfo.style.background = '#f8d7da';
        slotInfo.style.color = '#721c24';
    }
}

// Event listeners for appointment form
if (doctorSelect && dateInput) {
    doctorSelect.addEventListener('change', fetchAvailableSlots);
    dateInput.addEventListener('change', fetchAvailableSlots);
    
    // Load slots on page load if editing
    if (dateInput.value && doctorSelect.value) {
        fetchAvailableSlots();
    }
}

// Search form dynamic hints
if (searchTypeSelect && searchValueInput && searchHint) {
    searchTypeSelect.addEventListener('change', function() {
        const searchType = this.value;
        
        if (searchType === 'date') {
            searchValueInput.type = 'date';
            searchValueInput.placeholder = '';
            searchHint.textContent = 'Select a date';
        } else if (searchType === 'doctor') {
            searchValueInput.type = 'text';
            searchValueInput.placeholder = 'Enter doctor name...';
            searchHint.textContent = 'Enter doctor name (partial match allowed)';
        } else if (searchType === 'patient') {
            searchValueInput.type = 'text';
            searchValueInput.placeholder = 'Enter patient name...';
            searchHint.textContent = 'Enter patient name (partial match allowed)';
        }
        
        searchValueInput.value = '';
    });
    
    // Trigger on page load
    searchTypeSelect.dispatchEvent(new Event('change'));
}

// Smooth scroll to anchors
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Confirm delete actions
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
});

// Auto-hide alerts after 5 seconds
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.5s';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});

// Form validation
document.querySelectorAll('.data-form, .auth-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required], select[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.style.borderColor = '#dc3545';
            } else {
                input.style.borderColor = '#ddd';
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Please fill in all required fields');
        }
    });
});

// Phone number validation (basic)
const phoneInputs = document.querySelectorAll('input[name="phone"]');
phoneInputs.forEach(input => {
    input.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 15) {
            this.value = this.value.slice(0, 15);
        }
    });
});

// Prevent past dates in appointment booking
if (dateInput) {
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
}

// Password confirmation validation
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm_password');

if (passwordInput && confirmPasswordInput) {
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value !== passwordInput.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
}

console.log('✅ Clinic Management System loaded successfully!');
