document.addEventListener('DOMContentLoaded', function() {
    // ACTIVE MENU ITEM
    const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');
    
    allSideMenu.forEach(item => {
        const li = item.parentElement;
        
        item.addEventListener('click', function () {
            allSideMenu.forEach(i => {
                i.parentElement.classList.remove('active');
            });
            li.classList.add('active');
        });
        
        // Set active class based on current URL
        if (window.location.href.includes(item.getAttribute('href'))) {
            allSideMenu.forEach(i => {
                i.parentElement.classList.remove('active');
            });
            li.classList.add('active');
        }
    });
    
    // TOGGLE SIDEBAR
    const menuBar = document.querySelector('#content nav .bx.bx-menu');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    
    // Create overlay element for mobile
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    document.body.appendChild(overlay);
    
    function toggleSidebar() {
        sidebar.classList.toggle('hide');
        
        if (window.innerWidth <= 576) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        } else {
            content.classList.toggle('sidebar-open');
        }
    }
    
    menuBar.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
    
    // SEARCH FORM TOGGLE
    const searchButton = document.querySelector('#content nav form .form-input button');
    const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
    const searchForm = document.querySelector('#content nav form');
    
    searchButton.addEventListener('click', function (e) {
        if (window.innerWidth < 576) {
            e.preventDefault();
            searchForm.classList.toggle('show');
            if (searchForm.classList.contains('show')) {
                searchButtonIcon.classList.replace('bx-search', 'bx-x');
            } else {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
            }
        }
    });
    
    // TOGGLE DARK MODE
    const switchMode = document.getElementById('switch-mode');
    
    switchMode.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            document.body.classList.remove('dark');
            localStorage.setItem('darkMode', 'disabled');
        }
    });
    
    // Check for saved dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        switchMode.checked = true;
        document.body.classList.add('dark');
    }
    
    // RESPONSIVE BEHAVIOR
    function handleWindowResize() {
        if (window.innerWidth < 768) {
            sidebar.classList.add('hide');
        }
        
        if (window.innerWidth > 576) {
            searchButtonIcon.classList.replace('bx-x', 'bx-search');
            searchForm.classList.remove('show');
            
            if (sidebar.classList.contains('show')) {
                content.classList.add('sidebar-open');
            } else {
                content.classList.remove('sidebar-open');
            }
        } else {
            content.classList.remove('sidebar-open');
            
            // Reset sidebar state on mobile
            if (!sidebar.classList.contains('show')) {
                overlay.classList.remove('show');
            }
        }
    }
    
    // Initial setup
    handleWindowResize();
    
    // Handle window resize
    window.addEventListener('resize', handleWindowResize);
    
    // DROPDOWN MENUS (if any)
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');
    
    dropdownMenus.forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        const menu = dropdown.querySelector('.dropdown-content');
        
        if (trigger && menu) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                menu.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        }
    });
    
    // NOTIFICATION BADGES
    // Auto-update notification badges (example)
    function updateNotifications() {
        const notificationBadges = document.querySelectorAll('.notification .num');
        
        notificationBadges.forEach(badge => {
            // This would typically be an API call to get actual notification count
            // For demo purposes, we're just using a random number
            const count = Math.floor(Math.random() * 10);
            if (count > 0) {
                badge.style.display = 'flex';
                badge.textContent = count;
            } else {
                badge.style.display = 'none';
            }
        });
    }
    
    // Uncomment to enable periodic notification updates
    // setInterval(updateNotifications, 60000); // Update every minute
    
    // FORM VALIDATION
    const forms = document.querySelectorAll('form.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    });
    
    // TOOLTIPS
    const tooltips = document.querySelectorAll('[data-tooltip]');
    
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseenter', function() {
            const text = this.getAttribute('data-tooltip');
            
            if (!text) return;
            
            const tooltipEl = document.createElement('div');
            tooltipEl.className = 'tooltip';
            tooltipEl.textContent = text;
            
            document.body.appendChild(tooltipEl);
            
            const rect = this.getBoundingClientRect();
            tooltipEl.style.top = rect.top - tooltipEl.offsetHeight - 10 + 'px';
            tooltipEl.style.left = rect.left + (rect.width / 2) - (tooltipEl.offsetWidth / 2) + 'px';
            tooltipEl.style.opacity = '1';
            
            this.addEventListener('mouseleave', function() {
                tooltipEl.remove();
            }, { once: true });
        });
    });
});