document.addEventListener('DOMContentLoaded', function() {
    // Save status change
    const saveStatusBtn = document.getElementById('save-status');
    if (saveStatusBtn) {
        saveStatusBtn.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');
            const newStatus = document.getElementById('booking-status').value;
            const newPaymentStatus = document.getElementById('payment-status').value;
            
            // Send AJAX request to update status
            fetch('update_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `booking_id=${bookingId}&status=${newStatus}&payment_status=${newPaymentStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status updated successfully!');
                    
                    // Update status badges in the UI
                    document.querySelectorAll('.status-badge.status-display').forEach(badge => {
                        badge.className = 'status-badge status-display status-' + newStatus.toLowerCase();
                        badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    });
                    
                    document.querySelector('.payment-badge').className = 'status-badge payment-badge status-' + newPaymentStatus.toLowerCase();
                    document.querySelector('.payment-badge').textContent = newPaymentStatus.charAt(0).toUpperCase() + newPaymentStatus.slice(1);
                    
                    // Close modal
                    document.getElementById('status-modal').style.display = 'none';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }
}); 