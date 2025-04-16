document.addEventListener('DOMContentLoaded', function() {
    const miniCalendar = document.getElementById('mini-calendar');
    const currentMonthElement = document.getElementById('current-month');
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    // Function to get number of days in a month
    function getDaysInMonth(month, year) {
        return new Date(year, month + 1, 0).getDate();
    }

    // Function to get first day of month (0 = Sunday, 1 = Monday, etc.)
    function getFirstDayOfMonth(month, year) {
        return new Date(year, month, 1).getDay();
    }

    // Function to update calendar
    async function updateCalendar() {
        // Get booking counts for the month
        const response = await fetch(`get_monthly_bookings.php?month=${currentMonth + 1}&year=${currentYear}`);
        const bookingCounts = await response.json();

        currentMonthElement.textContent = new Date(currentYear, currentMonth).toLocaleString('default', { month: 'long', year: 'numeric' });

        const daysInMonth = getDaysInMonth(currentMonth, currentYear);
        const firstDay = getFirstDayOfMonth(currentMonth, currentYear);

        let calendarHTML = `
            <div class="calendar-header">
                <div class="weekday">Sun</div>
                <div class="weekday">Mon</div>
                <div class="weekday">Tue</div>
                <div class="weekday">Wed</div>
                <div class="weekday">Thu</div>
                <div class="weekday">Fri</div>
                <div class="weekday">Sat</div>
            </div>
            <div class="calendar-body">
        `;

        // Add empty cells for days before the first day of month
        let dayCount = 0;
        calendarHTML += '<div class="calendar-row">';
        for (let i = 0; i < firstDay; i++) {
            calendarHTML += '<div class="calendar-day empty"></div>';
            dayCount++;
        }

        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const bookingCount = bookingCounts[dateStr] || 0;
            const isToday = day === currentDate.getDate() && 
                           currentMonth === currentDate.getMonth() && 
                           currentYear === currentDate.getFullYear();

            calendarHTML += `
                <div class="calendar-day${isToday ? ' today' : ''}">
                    ${day}
                    ${bookingCount > 0 ? `<span class="booking-count">${bookingCount}</span>` : ''}
                </div>
            `;

            dayCount++;
            if (dayCount % 7 === 0 && day < daysInMonth) {
                calendarHTML += '</div><div class="calendar-row">';
            }
        }

        // Add empty cells for remaining days
        while (dayCount % 7 !== 0) {
            calendarHTML += '<div class="calendar-day empty"></div>';
            dayCount++;
        }

        calendarHTML += '</div></div>';
        miniCalendar.innerHTML = calendarHTML;
    }

    // Event listeners for previous/next month buttons
    prevMonthBtn.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        updateCalendar();
    });

    nextMonthBtn.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        updateCalendar();
    });

    // Initialize calendar
    updateCalendar();
}); 