<?php
$page_title = 'Live Chat Support';
include 'includes/header.php';

global $db;

// Get active sessions count
$result = $db->query("SELECT COUNT(*) as count FROM chat_sessions WHERE status = 'active'");
$stats = $result->fetch_assoc();
$active_sessions = $stats['count'];
?>

<div class="container my-5">
    <div class="row">
        <!-- Chat Section -->
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0">
                <!-- Header -->
                <div style="background: linear-gradient(135deg, #001f3f, #003366); color: white; padding: 30px; border-radius: 15px 15px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><i class="fas fa-headset me-2"></i>Customer Support Chat</h2>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-circle me-2" style="color: #4CAF50; font-size: 8px;"></i>
                                <?php echo $active_sessions > 0 ? $active_sessions . ' support agent(s) online' : 'Support team online'; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0" style="display: flex; flex-direction: column; height: 600px;">
                    <!-- Guest Form -->
                    <div id="guest-form-section" style="padding: 30px; display: block; background: #f8f9fa; text-align: center;">
                        <i class="fas fa-comments fa-3x text-primary mb-3" style="display: block;"></i>
                        <h5 class="mb-3">Welcome to Our Support Chat</h5>
                        <p class="text-muted mb-4">Please enter your details to start chatting with our support team. We're here to help!</p>
                        
                        <form id="chat-start-form" onsubmit="event.preventDefault(); initiateChatSession();">
                            <div class="mb-3 text-start">
                                <label class="form-label fw-500">Your Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" id="chat-guest-name" placeholder="Enter your full name" required>
                            </div>
                            <div class="mb-4 text-start">
                                <label class="form-label fw-500">Your Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg" id="chat-guest-email" placeholder="Enter your email address" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-comments me-2"></i>Start Chat
                            </button>
                        </form>

                        <div class="mt-4 pt-3 border-top">
                            <p class="text-muted mb-2">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Your chat session is secure and private. We typically respond within minutes during business hours.</small>
                            </p>
                        </div>
                    </div>

                    <!-- Chat Messages Area -->
                    <div id="chat-messages-section" style="flex: 1; overflow-y: auto; padding: 20px; background: #f8f9fa; display: none;">
                        <div id="page-chat-messages" style="display: flex; flex-direction: column; gap: 15px;"></div>
                        <div id="page-typing-indicator" style="display: none; padding: 15px; text-align: center;">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <small class="text-muted">Support team is typing...</small>
                        </div>
                    </div>

                    <!-- Chat Input Area -->
                    <div id="chat-input-section" style="padding: 20px; border-top: 1px solid #ddd; background: white; border-radius: 0 0 15px 15px; display: none;">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" id="page-message-input" 
                                   placeholder="Type your message here..." style="border-radius: 25px 0 0 25px; padding: 12px 20px;">
                            <button class="btn btn-primary" type="button" onclick="sendPageChatMessage()" 
                                    style="border-radius: 0 25px 25px 0; padding: 12px 20px;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">Press Enter to send message</small>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="row mt-5">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Fast Response</h6>
                        <p class="text-muted small mb-0">Usually within 5 minutes during business hours</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <i class="fas fa-lock fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Secure & Private</h6>
                        <p class="text-muted small mb-0">Your chat is encrypted and confidential</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <i class="fas fa-users fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Expert Support</h6>
                        <p class="text-muted small mb-0">Get help from our trained support team</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Page JavaScript -->
<script>
// Local safe JSON parser for this page (avoids depending on footer global)
function parseJsonSafeInline(response) {
    return response.text().then(function(text) {
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('parseJsonSafeInline: invalid JSON from', response.url, text);
            throw new Error('Invalid JSON response');
        }
    });
}
let pageSessionId = null;
let pageMessageRefreshInterval = null;

function initiateChatSession() {
    const guestName = document.getElementById('chat-guest-name').value.trim();
    const guestEmail = document.getElementById('chat-guest-email').value.trim();

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
    .then(parseJsonSafeInline)
    .then(data => {
        if (data.success) {
            pageSessionId = data.session_id;
            document.getElementById('guest-form-section').style.display = 'none';
            document.getElementById('chat-messages-section').style.display = 'block';
            document.getElementById('chat-input-section').style.display = 'block';
            
            // Load initial messages
            loadPageChatMessages();
            startPageMessageRefresh();
            
            // Welcome message
            addSystemMessage(`Welcome ${guestName}! How can we assist you today?`);
            document.getElementById('page-message-input').focus();
        } else {
            alert('Failed to start chat: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error starting chat session');
    });
}

function loadPageChatMessages() {
    if (!pageSessionId) return;

    fetch(`ajax/get_chat_messages.php?session_id=${pageSessionId}`)
    .then(parseJsonSafeInline)
    .then(data => {
        if (data.success && data.messages) {
            displayPageChatMessages(data.messages);
        }
    })
    .catch(error => console.error('Error loading messages:', error));
}

function displayPageChatMessages(messages) {
    const container = document.getElementById('page-chat-messages');
    
    // Clear existing messages but keep system messages
    const existingMessages = container.querySelectorAll('.message-item');
    const lastTimestamp = existingMessages.length > 0 ? existingMessages[existingMessages.length - 1].dataset.timestamp : 0;

    messages.forEach(message => {
        if (message.timestamp <= lastTimestamp) return; // Skip already displayed messages
        
        const isAdmin = message.sender_type === 'admin';
        const msgDiv = document.createElement('div');
        msgDiv.className = 'message-item';
        msgDiv.dataset.timestamp = message.timestamp || 0;
        msgDiv.style.cssText = `
            background: ${isAdmin ? '#e3f2fd' : '#f1f3f5'};
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 10px;
            border-left: 4px solid ${isAdmin ? '#1976d2' : '#6c757d'};
        `;
        
        const senderColor = isAdmin ? '#1976d2' : '#6c757d';
        msgDiv.innerHTML = `
            <strong style="color: ${senderColor}; font-size: 13px;">${message.sender_name}</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px; color: #333;">${escapeHtml(message.message)}</p>
            <small style="color: #999; font-size: 12px;">${new Date(message.created_at).toLocaleTimeString()}</small>
        `;
        
        container.appendChild(msgDiv);
    });
    
    // Scroll to bottom
    const messagesSection = document.getElementById('chat-messages-section');
    messagesSection.scrollTop = messagesSection.scrollHeight;
}

function addSystemMessage(message) {
    const container = document.getElementById('page-chat-messages');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'message-item system';
    msgDiv.style.cssText = `
        background: #fff3cd;
        padding: 12px;
        border-radius: 8px;
        text-align: center;
        color: #856404;
        font-size: 13px;
        margin-bottom: 10px;
        border: 1px solid #ffeaa7;
    `;
    msgDiv.innerHTML = `<i class="fas fa-info-circle me-2"></i>${message}`;
    container.appendChild(msgDiv);
    
    const messagesSection = document.getElementById('chat-messages-section');
    messagesSection.scrollTop = messagesSection.scrollHeight;
}

function sendPageChatMessage() {
    const messageInput = document.getElementById('page-message-input');
    const message = messageInput.value.trim();

    if (!message || !pageSessionId) return;

    const formData = new FormData();
    formData.append('session_id', pageSessionId);
    formData.append('message', message);
    formData.append('sender_type', 'guest');

    fetch('ajax/send_chat_message.php', {
        method: 'POST',
        body: formData
    })
    .then(parseJsonSafeInline)
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            messageInput.focus();
            
            // Show user message immediately
            const container = document.getElementById('page-chat-messages');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'message-item';
            msgDiv.style.cssText = `
                background: #f1f3f5;
                padding: 15px;
                border-radius: 12px;
                margin-bottom: 10px;
                border-left: 4px solid #6c757d;
            `;
            msgDiv.innerHTML = `
                <strong style="color: #6c757d; font-size: 13px;">You</strong>
                <p style="margin: 8px 0 0 0; font-size: 14px; color: #333;">${escapeHtml(message)}</p>
                <small style="color: #999; font-size: 12px;">${new Date().toLocaleTimeString()}</small>
            `;
            container.appendChild(msgDiv);
            
            const messagesSection = document.getElementById('chat-messages-section');
            messagesSection.scrollTop = messagesSection.scrollHeight;
        } else {
            alert('Failed to send message. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message');
    });
}

function startPageMessageRefresh() {
    pageMessageRefreshInterval = setInterval(() => {
        if (pageSessionId) {
            loadPageChatMessages();
        }
    }, 2000); // Refresh every 2 seconds
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Allow Enter key to send message
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('page-message-input');
    if (messageInput) {
        messageInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendPageChatMessage();
            }
        });
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (pageMessageRefreshInterval) {
        clearInterval(pageMessageRefreshInterval);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
