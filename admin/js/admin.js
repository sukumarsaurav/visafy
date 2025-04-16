document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const main = document.querySelector('.admin-main');
    
    // Create overlay for mobile sidebar
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    // Desktop sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        });
    }
    
    // Mobile menu toggle
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
    }
    
    // Function to handle window resize
    function handleResize() {
        const isMobile = window.innerWidth < 992;
        
        if (!isMobile) {
            // Reset mobile specific classes when on desktop
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    }
    
    // Run on load and on resize
    handleResize();
    window.addEventListener('resize', handleResize);
    
    // Close sidebar when overlay is clicked
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });
    
    // Close sidebar if ESC key is pressed
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    });
    
    // Select all functionality for checkboxes
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.booking-select');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Status filter highlighting
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const value = this.value;
            const rows = document.querySelectorAll('.booking-table tbody tr');
            
            if (value === '') {
                rows.forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            rows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(6)');
                const statusText = statusCell.textContent.trim().toLowerCase();
                
                if (statusText.includes(value)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Type filter functionality
    const typeFilter = document.getElementById('type-filter');
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            const value = this.value;
            const rows = document.querySelectorAll('.booking-table tbody tr');
            
            if (value === '') {
                rows.forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            rows.forEach(row => {
                const typeCell = row.querySelector('td:nth-child(4)');
                const typeText = typeCell.textContent.trim().toLowerCase();
                
                if (typeText.includes(value)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('booking-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.booking-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Reset filters
    const resetButton = document.querySelector('.btn-reset');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            const filters = document.querySelectorAll('.admin-filters select');
            const dateInputs = document.querySelectorAll('.date-range input');
            const searchInput = document.getElementById('booking-search');
            const rows = document.querySelectorAll('.booking-table tbody tr');
            
            filters.forEach(filter => {
                filter.selectedIndex = 0;
            });
            
            dateInputs.forEach(input => {
                input.value = '';
            });
            
            if (searchInput) {
                searchInput.value = '';
            }
            
            rows.forEach(row => {
                row.style.display = '';
            });
        });
    }
    
    // Calendar navigation
    const calendarNavBtns = document.querySelectorAll('.calendar-nav-btn');
    if (calendarNavBtns.length) {
        // This would be implemented with actual date logic in a real application
        calendarNavBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Calendar navigation would be implemented with real date logic');
            });
        });
    }
    
    // Calendar day click
    const calendarDays = document.querySelectorAll('.calendar-day:not(.empty)');
    if (calendarDays.length) {
        calendarDays.forEach(day => {
            day.addEventListener('click', function() {
                const date = this.textContent.split(' ')[0];
                alert(`You clicked on day: ${date}\nIn a real application, this would show bookings for this day.`);
            });
        });
    }
}); 