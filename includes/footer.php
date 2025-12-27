    </main>
    
    <!-- Footer -->
    <footer class="mt-auto footer" style="color: var(--footer-text); background: var(--footer-bg); margin-top: auto;">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-store text-light me-2"></i><?php echo SITE_NAME; ?></h5>
                    <p class="mb-0 opacity-75 lh-lg">Your one-stop shop for greeting cards, gift articles, handbags, beauty products, and more!</p>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-link text-light me-2"></i>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo ($is_admin_page || $is_employee_page) ? '../index.php' : 'index.php'; ?>" class="text-decoration-none opacity-75 transition-opacity" style="color: var(--footer-text);" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.75'"><i class="fas fa-chevron-right me-2"></i>Home</a></li>
                        <li class="mb-2"><a href="<?php echo ($is_admin_page || $is_employee_page) ? '../products.php' : 'products.php'; ?>" class="text-decoration-none opacity-75 transition-opacity" style="color: var(--footer-text);" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.75'"><i class="fas fa-chevron-right me-2"></i>Products</a></li>
                        <li class="mb-2"><a href="<?php echo ($is_admin_page || $is_employee_page) ? '../categories.php' : 'categories.php'; ?>" class="text-decoration-none opacity-75 transition-opacity" style="color: var(--footer-text);" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.75'"><i class="fas fa-chevron-right me-2"></i>Categories</a></li>
                        <li class="mb-2"><a href="<?php echo ($is_admin_page || $is_employee_page) ? '../cart.php' : 'cart.php'; ?>" class="text-decoration-none opacity-75 transition-opacity" style="color: var(--footer-text);" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.75'"><i class="fas fa-chevron-right me-2"></i>Cart</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-phone text-light me-2"></i>Contact Us</h5>
                    <div class="opacity-75 lh-lg">
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i><a href="mailto:<?php echo ADMIN_EMAIL; ?>" style="color: var(--footer-text); text-decoration: none;"><?php echo ADMIN_EMAIL; ?></a></p>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i><a href="tel:+15551234567" style="color: var(--footer-text); text-decoration: none;">+1 (555) 123-4567</a></p>
                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>123 Shopping Street</p>
                    </div>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 opacity-75"><small>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</small></p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <div class="footer-social-links">
                        <a href="#" class="text-decoration-none opacity-75 me-3" style="color: var(--footer-text);" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-decoration-none opacity-75 me-3" style="color: var(--footer-text);" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-decoration-none opacity-75 me-3" style="color: var(--footer-text);" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-decoration-none opacity-75" style="color: var(--footer-text);" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript (chat removed) -->
    <script>
        // Safe JSON parser
        function parseJsonSafe(response) {
            return response.text().then(function(text) {
                try { return JSON.parse(text); } catch (e) { console.error('parseJsonSafe invalid JSON', response.url, text); throw e; }
            });
        }

        var isAdminPage = <?php echo $is_admin_page ? 'true' : 'false'; ?>;

        // Special Offer Countdown Timer
        window.initializeCountdown = function() {
            const countdownElement = document.getElementById('countdown-timer');
            if (!countdownElement) return;
            let endTimeStr = countdownElement.dataset.endtime || '<?php echo isset($special_offer) && $special_offer ? date('c', strtotime($special_offer['end_time'])) : ''; ?>';
            if (!endTimeStr) return;
            const parsed = Date.parse(endTimeStr);
            const endTime = isNaN(parsed) ? Date.parse(endTimeStr.replace(' ', 'T')) : parsed;
            if (isNaN(endTime)) return;
            if (window.countdownInterval) { clearInterval(window.countdownInterval); window.countdownInterval = null; }
            function updateCountdown() {
                const now = Date.now();
                const distance = endTime - now;
                const daysEl = document.getElementById('days');
                const hoursEl = document.getElementById('hours');
                const minutesEl = document.getElementById('minutes');
                const secondsEl = document.getElementById('seconds');
                if (!daysEl || !hoursEl || !minutesEl || !secondsEl) return;
                if (distance <= 0) { daysEl.innerText='00'; hoursEl.innerText='00'; minutesEl.innerText='00'; secondsEl.innerText='00'; clearInterval(window.countdownInterval); return; }
                const days = Math.floor(distance / (1000*60*60*24));
                const hours = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));
                const minutes = Math.floor((distance % (1000*60*60)) / (1000*60));
                const seconds = Math.floor((distance % (1000*60)) / 1000);
                daysEl.innerText = String(days).padStart(2,'0'); hoursEl.innerText = String(hours).padStart(2,'0'); minutesEl.innerText = String(minutes).padStart(2,'0'); secondsEl.innerText = String(seconds).padStart(2,'0');
            }
            updateCountdown(); window.countdownInterval = setInterval(updateCountdown, 1000);
        };

        document.addEventListener('DOMContentLoaded', function() { initializeCountdown(); });

        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => { if (alert) { const bsAlert = new bootstrap.Alert(alert); bsAlert.close(); } });
        }, 3000);

        function confirmDelete(message) { return confirm(message || 'Are you sure you want to delete this item?'); }

        function addToCartAnimation(button) { const icon = button.querySelector('i'); if (icon) { icon.classList.add('fa-spin'); setTimeout(()=>icon.classList.remove('fa-spin'),500); } }

        function markAsRead(notificationId, event) {
            if (event) event.preventDefault();
            var ajaxPath = isAdminPage ? '../ajax/mark_notification_read.php' : 'ajax/mark_notification_read.php';
            fetch(ajaxPath, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'notification_id='+notificationId })
            .then(parseJsonSafe)
            .then(data => { if (data.success) { var notificationsPath = isAdminPage ? '../notifications.php' : 'notifications.php'; window.location.href = notificationsPath; } })
            .catch(error => console.error('Error:', error));
        }

        // Theme Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle'); if (!themeToggle) return;
            const body = document.body; const icon = themeToggle.querySelector('i');
            const savedTheme = localStorage.getItem('theme') || 'light'; if (savedTheme === 'dark') { body.setAttribute('data-theme','dark'); icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); }
            themeToggle.addEventListener('click', function() { const currentTheme = body.getAttribute('data-theme'); if (currentTheme === 'dark') { body.removeAttribute('data-theme'); localStorage.setItem('theme','light'); icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); } else { body.setAttribute('data-theme','dark'); localStorage.setItem('theme','dark'); icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); } });
        });

        function toggleSidebar() {
            // Try adminSidebar first (for admin/employee pages)
            const adminSidebar = document.getElementById('adminSidebar');
            if (adminSidebar) {
                adminSidebar.classList.toggle('show');
                return;
            }

            // Try dashboard sidebar (for dashboard page)
            const dashboardSidebar = document.getElementById('sidebar');
            if (dashboardSidebar) {
                dashboardSidebar.classList.toggle('show');
                // Update main content margin for dashboard
                const main = document.querySelector('main');
                if (main && window.innerWidth <= 768) {
                    const isVisible = dashboardSidebar.classList.contains('show');
                    main.style.marginLeft = isVisible ? '250px' : '0';
                }
            }
        }

        // Handle admin sidebar toggle button
        const adminToggle = document.getElementById('adminSidebarToggle');
        if (adminToggle) {
            adminToggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebar();
                // Adjust footer margin when sidebar is shown
                const footer = document.querySelector('footer');
                const sidebar = document.getElementById('sidebar') || document.getElementById('adminSidebar');
                if (footer && sidebar) {
                    const isVisible = sidebar.classList.contains('show');
                    footer.style.marginLeft = isVisible ? '250px' : '0';
                }
            });
        }

        // Handle mobile sidebar toggle button
        const mobileToggle = document.getElementById('mobileSidebarToggle');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebar();
            });
        }

        document.addEventListener('click', function(event) { const sidebar = document.getElementById('adminSidebar'); const toggleBtn = document.getElementById('adminSidebarToggle'); const mobileBtn = document.getElementById('mobileSidebarToggle'); if (sidebar && (toggleBtn || mobileBtn) && window.innerWidth < 768) { if (!sidebar.contains(event.target) && (!toggleBtn || !toggleBtn.contains(event.target)) && (!mobileBtn || !mobileBtn.contains(event.target))) { sidebar.classList.remove('show'); } } });
    </script>
</body>
</html>
    

