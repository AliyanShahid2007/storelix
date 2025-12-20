    </main>
    
    <!-- Footer -->
    <footer class="mt-5 footer" style="color: var(--footer-text);">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3"><i class="fas fa-store"></i> <?php echo SITE_NAME; ?></h5>
                    <p class="mb-0 opacity-75">Your one-stop shop for greeting cards, gift articles, handbags, beauty products, and more!</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo ($is_admin_page || $is_employee_page) ? '../index.php' : 'index.php'; ?>" class="text-decoration-none opacity-75 hover-opacity-100" style="color: var(--footer-text);"><i class="fas fa-chevron-right me-2"></i>Home</a></li>
                        <li class="mb-2"><a href="<?php echo ($is_admin_page || $is_employee_page) ? '../products.php' : 'products.php'; ?>" class="text-decoration-none opacity-75 hover-opacity-100" style="color: var(--footer-text);"><i class="fas fa-chevron-right me-2"></i>Products</a></li>
                        <li class="mb-2"><a href="<?php echo ($is_admin_page || $is_employee_page) ? '../categories.php' : 'categories.php'; ?>" class="text-decoration-none opacity-75 hover-opacity-100" style="color: var(--footer-text);"><i class="fas fa-chevron-right me-2"></i>Categories</a></li>
                        <li class="mb-2"><a href="<?php echo ($is_admin_page || $is_employee_page) ? '../cart.php' : 'cart.php'; ?>" class="text-decoration-none opacity-75 hover-opacity-100" style="color: var(--footer-text);"><i class="fas fa-chevron-right me-2"></i>Cart</a></li>
                        <!-- Chat Support removed -->
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Contact Us</h5>
                    <div class="opacity-75">
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i> <?php echo ADMIN_EMAIL; ?></p>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i> +1 (555) 123-4567</p>
                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> 123 Shopping Street</p>
                    </div>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="text-center">
                <p class="mb-0 opacity-75">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
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

        function toggleSidebar() { const sidebar = document.getElementById('adminSidebar'); if (sidebar) sidebar.classList.toggle('show'); }
        document.addEventListener('click', function(event) { const sidebar = document.getElementById('adminSidebar'); const toggleBtn = document.getElementById('sidebarToggle'); if (sidebar && toggleBtn && window.innerWidth < 768) { if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) { sidebar.classList.remove('show'); } } });
    </script>

</body>
</html>
                'hello': 'Hello! Welcome to <?php echo SITE_NAME; ?>! How can I help you today?',
                'hi': 'Hi there! How can I assist you with your shopping experience?',
                'help': 'I\'m here to help! You can ask me about our products, orders, shipping, or anything else.',
                'products': 'We offer a wide range of products including greeting cards, gift articles, handbags, and beauty products. Check out our products page for more details!',
                'shipping': 'We offer fast and reliable shipping worldwide. Standard delivery takes 3-5 business days.',
                'order': 'To place an order, simply browse our products, add items to your cart, and proceed to checkout. Need help with an existing order?',
                'return': 'We accept returns within 30 days of purchase. Please check our return policy for more details.',
                'contact': 'You can reach our support team at <?php echo ADMIN_EMAIL; ?> or call us at +1 (555) 123-4567.',
                'price': 'Our prices are competitive and we often have special offers. Check the current promotions on our homepage!',
                'payment': 'We accept all major credit cards, PayPal, and other secure payment methods.',
                'track': 'You can track your order status in your account dashboard once you\'re logged in.',
                'account': 'Create an account to track orders, save favorites, and get personalized recommendations!',
                'support': 'Our live support is available 24/7. Feel free to ask me anything!',
                'bye': 'Thank you for visiting <?php echo SITE_NAME; ?>! Have a great day!',
                'thank': 'You\'re welcome! Is there anything else I can help you with?',
                'default': 'I\'m here to help! Could you please rephrase your question or ask about our products, orders, or services?'
            };

            // Get bot response based on user message
            function getBotResponse(message) {
                const lowerMessage = message.toLowerCase();

                // Check for exact matches first
                for (const [key, response] of Object.entries(botResponses)) {
                    if (lowerMessage.includes(key)) {
                        return response;
                    }
                }

                // Check for common patterns
                if (lowerMessage.includes('how') && lowerMessage.includes('much')) {
                    return 'Prices vary by product. Please check individual product pages for current pricing and any ongoing promotions.';
                }

                if (lowerMessage.includes('when') && lowerMessage.includes('deliver')) {
                    return 'Delivery times depend on your location and shipping method. Standard shipping takes 3-5 business days.';
                }

                if (lowerMessage.includes('where') && lowerMessage.includes('location')) {
                    return 'We\'re located at 123 Shopping Street. You can also shop online from anywhere!';
                }

                if (lowerMessage.includes('size') || lowerMessage.includes('color')) {
                    return 'Product details including sizes and colors are listed on each product page. Let me know if you need help finding something specific!';
                }

                // Default response
                return botResponses.default;
            }

            // Send bot response
            function sendBotResponse(userMessage) {
                const botMessage = getBotResponse(userMessage);

                // Show typing indicator
                const typingIndicator = document.getElementById('typing-indicator');
                if (typingIndicator) {
                    typingIndicator.style.display = 'block';
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }

                // Simulate typing delay
                setTimeout(() => {
                    if (typingIndicator) {
                        typingIndicator.style.display = 'none';
                    }

                    // Add bot message to chat
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message admin';
                    messageDiv.innerHTML = `
                        <div>${botMessage}</div>
                        <span class="timestamp">${new Date().toLocaleTimeString()}</span>
                    `;
                    chatMessages.appendChild(messageDiv);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    // Save bot message to database
                    const formData = new FormData();
                    formData.append('message', botMessage);
                    formData.append('sender_type', 'admin');
                    if (currentSessionId) {
                        formData.append('session_id', currentSessionId);
                    } else if (isLoggedIn) {
                        formData.append('user_id', userId);
                    }

                    fetch('ajax/send_chat_message.php', {
                        method: 'POST',
                        body: formData
                    }).catch(error => console.error('Error saving bot message:', error));

                }, 1000 + Math.random() * 2000); // Random delay between 1-3 seconds
            }

            // Unified send message function for widget
            window.sendMessage = function() {
                const msg = messageInput.value.trim();
                if (!msg) return;

                // Add user message to chat immediately
                const userMessageDiv = document.createElement('div');
                userMessageDiv.className = 'message user';
                userMessageDiv.innerHTML = `
                    <div>${msg}</div>
                    <span class="timestamp">${new Date().toLocaleTimeString()}</span>
                `;
                chatMessages.appendChild(userMessageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                const formData = new FormData();
                formData.append('message', msg);
                // support both logged-in session and guest session
                const sessionToUse = currentSessionId || guestSessionId;
                if (sessionToUse) {
                    formData.append('session_id', sessionToUse);
                } else {
                    alert('Chat session not started. Please start the chat first.');
                    return;
                }

                fetch('ajax/send_chat_message.php', {
                    method: 'POST',
                    body: formData
                })
                .then(parseJsonSafe)
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        // Send bot response after user message is saved
                        sendBotResponse(msg);
                    } else {
                        alert('Failed to send message. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            };

            // Load messages
            function loadMessages() {
                let url = 'ajax/get_chat_messages.php?';
                if (currentSessionId) {
                    url += `session_id=${currentSessionId}`;
                } else if (isLoggedIn) {
                    url += `user_id=${userId}`;
                } else {
                    return;
                }

                fetch(url)
                .then(parseJsonSafe)
                .then(data => {
                    if (data.success) {
                        displayMessages(data.messages);
                    }
                })
                .catch(error => console.error('Error loading messages:', error));
            }

            // Display messages
            function displayMessages(messages) {
                chatMessages.innerHTML = '';
                messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${message.sender_type === 'user' ? 'user' : 'admin'}`;
                    messageDiv.innerHTML = `
                        <div>${message.message}</div>
                        <span class="timestamp">${new Date(message.created_at).toLocaleTimeString()}</span>
                    `;
                    chatMessages.appendChild(messageDiv);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Emoji picker toggle
            emojiToggleBtn.addEventListener('click', function() {
                emojiPicker.classList.toggle('show');
            });

            // Emoji selection
            emojiPicker.addEventListener('click', function(e) {
                if (e.target.classList.contains('emoji-btn')) {
                    messageInput.value += e.target.textContent;
                    emojiPicker.classList.remove('show');
                    messageInput.focus();
                }
            });

            // File attachment
            attachmentBtn.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function() {
                const file = fileInput.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('file', file);
                    if (currentSessionId) {
                        formData.append('session_id', currentSessionId);
                    } else if (isLoggedIn) {
                        formData.append('user_id', userId);
                    }

                    fetch('ajax/send_chat_message.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(parseJsonSafe)
                    .then(data => {
                        if (data.success) {
                            loadMessages();
                        } else {
                            alert('Failed to upload file. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                }
            });

            // Send message on Enter key
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Send button click
            sendBtn.addEventListener('click', sendMessage);

            // Minimize button
            minimizeBtn.addEventListener('click', minimizeChatWidget);

            // Click outside emoji picker to close
            document.addEventListener('click', function(e) {
                if (!emojiPicker.contains(e.target) && !emojiToggleBtn.contains(e.target)) {
                    emojiPicker.classList.remove('show');
                }
            });

            // Load messages periodically if chat is active
            setInterval(function() {
                if (chatWidget.style.display === 'flex' && (currentSessionId || isLoggedIn)) {
                    loadMessages();
                }
            }, 3000);

            // Initialize for logged-in users: ensure a chat session exists
            if (isLoggedIn) {
                fetch('ajax/start_chat_session.php', {
                    method: 'POST'
                })
                .then(parseJsonSafe)
                .then(data => {
                    if (data.success && data.session_id) {
                        currentSessionId = data.session_id;
                        loadMessages();

                        // Add welcome message from bot for logged-in users
                        setTimeout(() => {
                            const welcomeMessage = `Welcome back, ${userName}! I'm your AI shopping assistant. I can help you with orders, products, shipping, or any questions you have.`;
                            sendBotResponse(welcomeMessage);
                        }, 1000);
                    } else {
                        // fallback: try loading messages without session (will do nothing)
                        console.warn('Could not create chat session for logged-in user');
                    }
                })
                .catch(err => console.error('Error starting session for logged-in user:', err));
            }
        });

        // Guest Chat Functions
        let guestSessionId = null;
        
        function startGuestChat() {
            // Ensure widget is visible when starting chat
            if (typeof openChatWidget === 'function') {
                openChatWidget();
            }
            const guestName = document.getElementById('guest-name').value.trim();
            const guestEmail = document.getElementById('guest-email').value.trim();

            if (!guestName || !guestEmail) {
                alert('Please enter your name and email');
                return;
            }

            const formData = new FormData();
            formData.append('guest_name', guestName);
            formData.append('guest_email', guestEmail);

            fetch('ajax/start_chat_session.php', {
                method: 'POST',
                body: formData
            })
            .then(parseJsonSafe)
            .then(data => {
                if (data.success) {
                    guestSessionId = data.session_id;
                    document.getElementById('guest-form').style.display = 'none';
                    document.getElementById('chat-messages').style.display = 'block';
                    document.getElementById('chat-input').style.display = 'block';
                    loadGuestMessages();
                    startGuestMessageRefresh();
                    // Welcome message
                    const welcomeMsg = `Hi ${guestName}! Welcome to our support. How can we help you today?`;
                    const msgDiv = document.createElement('div');
                    msgDiv.style.cssText = 'background: #e7f3ff; padding: 10px; border-radius: 5px; margin: 5px 0; font-size: 13px;';
                    msgDiv.innerHTML = `<strong>Support:</strong> ${welcomeMsg}`;
                    document.getElementById('chat-messages').appendChild(msgDiv);
                    document.getElementById('message-input').focus();
                } else {
                    alert('Failed to start chat. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error starting chat');
            });
        }

        function loadGuestMessages() {
            if (!guestSessionId) return;

            fetch(`ajax/get_chat_messages.php?session_id=${guestSessionId}`)
            .then(parseJsonSafe)
            .then(data => {
                if (data.success && data.messages) {
                    displayGuestMessages(data.messages);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function displayGuestMessages(messages) {
            const container = document.getElementById('chat-messages');
            
            messages.forEach(message => {
                const isAdmin = message.sender_type === 'admin';
                const msgDiv = document.createElement('div');
                msgDiv.style.cssText = `background: ${isAdmin ? '#e7f3ff' : '#e8f5e9'}; padding: 10px; border-radius: 5px; margin: 5px 0; font-size: 13px;`;
                msgDiv.innerHTML = `<strong>${message.sender_name}:</strong> ${message.message}`;
                
                // Remove duplicates
                if (!container.innerHTML.includes(message.message)) {
                    container.appendChild(msgDiv);
                }
            });
            
            container.scrollTop = container.scrollHeight;
        }

        function startGuestMessageRefresh() {
            setInterval(() => {
                if (guestSessionId && document.getElementById('chat-widget').style.display !== 'none') {
                    loadGuestMessages();
                }
            }, 2000);
        }

        // Allow sending message with Enter key
        document.addEventListener('DOMContentLoaded', function() {
            const messageInput = document.getElementById('message-input');
            if (messageInput) {
                messageInput.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                        sendMessage();
                        event.preventDefault();
                    }
                });
            }
        });
    </script>

</body>
</html>
