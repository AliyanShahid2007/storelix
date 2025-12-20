<?php
// Include necessary files
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';

    if ($action === 'upload_file') {
        $sessionId = $_POST['session_id'] ?? '';

        if (!$sessionId) {
            echo json_encode(['success' => false, 'message' => 'Session ID is required']);
             exit;
        }

        if (!isset($_FILES['file'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
            exit;
        }

        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        // Check for upload errors
        if ($fileError !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File upload error']);
            exit;
        }

        // Check file size (5MB limit)
        if ($fileSize > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
            exit;
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
        $fileType = $file['type'];

        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'File type not allowed']);
            exit;
        }

        // Create uploads directory if it doesn't exist
        $uploadDir = 'uploads/chat_files/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = uniqid('chat_file_', true) . '.' . $fileExtension;
        $filePath = $uploadDir . $uniqueFileName;

        // Move uploaded file
        if (move_uploaded_file($fileTmpName, $filePath)) {
            echo json_encode(['success' => true, 'file_path' => $filePath]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save file']);
        }
        exit;
    } elseif ($action === 'send_message') {
        $sessionId = $_POST['session_id'] ?? '';
        $message = $_POST['message'] ?? '';
        $filePath = $_POST['file_path'] ?? null;

        if (!$sessionId || (!$message && !$filePath)) {
            echo json_encode(['success' => false, 'message' => 'Session ID and message or file path are required']);
            exit;
        }

        // Get user info
        $userId = null;
        $guestName = null;
        $guestEmail = null;

        if (isLoggedIn()) {
            $userId = $_SESSION['user_id'];
        } else {
            // For guest users, we need to get their info from the session
            // This is a simplified approach - in a real app, you'd store guest info in the session
            $guestName = $_SESSION['guest_name'] ?? 'Guest';
            $guestEmail = $_SESSION['guest_email'] ?? null;
        }

        // Insert message into database
        $stmt = $db->prepare("INSERT INTO chat_messages (session_id, sender_type, sender_id, user_id, guest_name, guest_email, message, file_path, created_at) VALUES (?, 'user', ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiissss", $sessionId, $userId, $userId, $guestName, $guestEmail, $message, $filePath);
        $result = $stmt->execute();

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
        exit;
    }
}

// If not an AJAX request, show the chat widget
if (!isset($_GET['ajax'])) {
?>
<!DOCTYPE html>
<html>
<head>
<style>
    .chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 350px;
        height: 500px;
        background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,249,250,0.95) 50%, rgba(240,242,245,0.95) 100%);
        border-radius: 28px;
        box-shadow:
            0 25px 80px rgba(0,0,0,0.15),
            0 0 0 1px rgba(255,255,255,0.2),
            inset 0 1px 0 rgba(255,255,255,0.6);
        display: none;
        flex-direction: column;
        z-index: 1000;
        backdrop-filter: blur(25px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.3);
        overflow: hidden;
        animation: widgetAppear 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes widgetAppear {
        from {
            transform: translateY(30px) scale(0.95);
            opacity: 0;
        }
        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    .chat-widget.minimized {
        height: 60px;
        width: 200px;
        border-radius: 30px;
        cursor: pointer;
    }

    .chat-widget.minimized .chat-messages,
    .chat-widget.minimized .chat-input,
    .chat-widget.minimized .guest-form {
        display: none !important;
    }

    .chat-widget.slide-down {
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s ease;
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .chat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        color: white;
        padding: 20px 24px;
        border-radius: 28px 28px 0 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow:
            0 8px 32px rgba(102, 126, 234, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        position: relative;
        overflow: hidden;
    }

    .chat-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    .chat-header .header-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chat-header .header-info span {
        background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 50%, #f0f9ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        font-size: 18px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        letter-spacing: 0.5px;
    }

    .chat-header .status-indicator {
        width: 8px;
        height: 8px;
        background: #28a745;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8f9fa;
        background-image: radial-gradient(circle at 20px 80px, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                          radial-gradient(circle at 80px 20px, rgba(255, 119, 198, 0.1) 0%, transparent 50%),
                          radial-gradient(circle at 40px 40px, rgba(120, 219, 255, 0.1) 0%, transparent 50%);
        background-size: 100px 100px;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .message {
        margin-bottom: 16px;
        padding: 14px 18px;
        border-radius: 20px;
        max-width: 75%;
        word-wrap: break-word;
        position: relative;
        animation: messageSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.2s ease;
    }

    .message:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    @keyframes messageSlideIn {
        from {
            transform: translateY(15px) scale(0.95);
            opacity: 0;
        }
        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    .message.user {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 6px;
        align-self: flex-end;
    }

    .message.user::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: -8px;
        width: 0;
        height: 0;
        border-left: 8px solid #764ba2;
        border-bottom: 8px solid transparent;
        border-top: 8px solid transparent;
    }

    .message.admin {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        color: #2d3748;
        border-bottom-left-radius: 6px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        align-self: flex-start;
    }

    .message.admin::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -8px;
        width: 0;
        height: 0;
        border-right: 8px solid #ffffff;
        border-bottom: 8px solid transparent;
        border-top: 8px solid transparent;
    }

    .message .timestamp {
        font-size: 11px;
        opacity: 0.7;
        margin-top: 4px;
        display: block;
    }

    .chat-input {
        padding: 20px;
        border-top: 1px solid #dee2e6;
        background: white;
        border-radius: 0 0 20px 20px;
    }

    .chat-input .input-group {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-radius: 25px;
        overflow: hidden;
        position: relative;
    }

    .emoji-picker {
        position: absolute;
        bottom: 100%;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        padding: 10px;
        display: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        width: 300px;
    }

    .emoji-picker.show {
        display: block;
    }

    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 5px;
    }

    .emoji-btn {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        padding: 5px;
        border-radius: 5px;
        transition: background-color 0.2s;
    }

    .emoji-btn:hover {
        background-color: #f8f9fa;
    }

    .attachment-btn {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 18px;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.2s;
        margin-right: 5px;
    }

    .attachment-btn:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }

    .emoji-toggle-btn {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 18px;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.2s;
        margin-right: 5px;
    }

    .emoji-toggle-btn:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }

    .chat-input input {
        border: none;
        padding: 12px 20px;
        font-size: 14px;
    }

    .chat-input input:focus {
        box-shadow: none;
    }

    .chat-input button {
        border: none;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 0 25px 25px 0;
        transition: all 0.3s ease;
    }

    .chat-input button:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        transform: scale(1.05);
    }

    .chat-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        z-index: 1001;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: bounceIn 0.8s ease-out;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.1);
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .chat-toggle:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(0,123,255,0.5);
    }

    .chat-toggle.unread {
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .minimize-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .minimize-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .guest-form {
        padding: 20px;
        text-align: center;
    }

    .guest-form h6 {
        color: #333;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .guest-form .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 12px 16px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .guest-form .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }

    .guest-form .btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 14px 32px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .guest-form .btn:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .typing-indicator {
        display: none;
        padding: 12px 20px;
        color: #666;
        font-style: italic;
        animation: typing 1.5s infinite;
    }

    @keyframes typing {
        0%, 60%, 100% { opacity: 1; }
        30% { opacity: 0.5; }
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .chat-widget {
            width: calc(100vw - 16px);
            height: calc(100vh - 32px);
            bottom: 8px;
            right: 8px;
            left: 8px;
            border-radius: 16px;
            max-width: 100vw;
            max-height: 100vh;
        }

        .chat-header {
            padding: 16px 20px;
            border-radius: 16px 16px 0 0;
        }

        .chat-header .header-info span {
            font-size: 16px;
            font-weight: 600;
        }

        .chat-toggle {
            bottom: 16px;
            right: 16px;
            width: 60px;
            height: 60px;
            font-size: 24px;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .chat-messages {
            padding: 16px;
            -webkit-overflow-scrolling: touch;
        }

        .message {
            max-width: 90%;
            font-size: 15px;
            padding: 12px 16px;
            margin-bottom: 12px;
            touch-action: manipulation;
        }

        .message.user {
            margin-left: auto;
            margin-right: 8px;
        }

        .message.admin {
            margin-left: 8px;
            margin-right: auto;
        }

        .chat-input {
            padding: 16px;
        }

        .chat-input input {
            padding: 14px 18px;
            font-size: 16px; /* Prevents zoom on iOS */
            border-radius: 20px;
        }

        .chat-input button {
            padding: 14px 18px;
            border-radius: 0 20px 20px 0;
        }

        .guest-form {
            padding: 20px 16px;
        }

        .guest-form .form-control {
            padding: 14px 16px;
            font-size: 16px;
            border-radius: 12px;
        }

        .guest-form .btn {
            padding: 16px 24px;
            font-size: 16px;
            border-radius: 12px;
        }

        .minimize-btn {
            padding: 10px;
            font-size: 16px;
            min-width: 40px;
            min-height: 40px;
        }
    }

    /* Small mobile devices */
    @media (max-width: 480px) {
        .chat-widget {
            width: calc(100vw - 12px);
            height: calc(100vh - 24px);
            bottom: 6px;
            right: 6px;
            left: 6px;
            border-radius: 12px;
        }

        .chat-toggle {
            bottom: 12px;
            right: 12px;
            width: 56px;
            height: 56px;
            font-size: 22px;
        }

        .chat-messages {
            padding: 12px;
        }

        .message {
            max-width: 95%;
            font-size: 14px;
            padding: 10px 14px;
        }

        .chat-input {
            padding: 12px;
        }

        .chat-input input {
            padding: 12px 16px;
        }

        .chat-input button {
            padding: 12px 16px;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .chat-widget {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            border-color: rgba(255,255,255,0.1);
        }

        .chat-messages {
            background: #2d3748;
        }

        .message.admin {
            background: #4a5568;
            color: white;
            border-color: #718096;
        }

        .chat-input {
            background: #2d3748;
            border-color: #718096;
        }
    }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Local safe JSON parser for this widget script
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
        let sessionId = null;
        let refreshInterval;
        let isMinimized = true;
        let typingTimeout;
        let isTyping = false;

        // Toggle chat widget
        document.getElementById('chat-toggle').addEventListener('click', function() {
            const widget = document.getElementById('chat-widget');
            const toggle = document.getElementById('chat-toggle');

            if (isMinimized) {
                widget.style.display = 'flex';
                toggle.style.display = 'none';
                isMinimized = false;

                // Initialize chat if not already done
                if (!sessionId) {
                    initializeChat();
                }
            }
        });

        // Minimize chat
        document.getElementById('minimize-btn').addEventListener('click', function() {
            const widget = document.getElementById('chat-widget');
            const toggle = document.getElementById('chat-toggle');

            widget.style.display = 'none';
            toggle.style.display = 'flex';
            isMinimized = true;
        });

        // Close chat (now minimizes with slide down)
        document.getElementById('close-btn').addEventListener('click', function() {
            const widget = document.getElementById('chat-widget');
            const toggle = document.getElementById('chat-toggle');
            widget.classList.add('slide-down');
            toggle.style.display = 'flex';
            isMinimized = true;
        });

        function initializeChat() {
            <?php if (isLoggedIn()): ?>
                // User is logged in, start session directly
                startChatSession();
            <?php else: ?>
                // Show guest form
                document.getElementById('guest-form').style.display = 'block';
            <?php endif; ?>
        }

        function startChatSession(guestName = null, guestEmail = null) {
            const formData = new FormData();
            if (guestName) formData.append('guest_name', guestName);
            if (guestEmail) formData.append('guest_email', guestEmail);

            fetch('ajax/start_chat_session.php', {
                method: 'POST',
                body: formData
            })
            .then(parseJsonSafeInline)
            .then(data => {
                if (data.success) {
                    sessionId = data.session_id;
                    showChatInterface();
                    loadMessages();
                    startMessageRefresh();
                } else {
                    alert('Failed to start chat: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error starting chat:', error);
                alert('Error starting chat');
            });
        }

        function showChatInterface() {
            document.getElementById('guest-form').style.display = 'none';
            document.getElementById('chat-messages').style.display = 'block';
            document.getElementById('chat-input').style.display = 'block';
        }

        function loadMessages() {
            if (!sessionId) return;

            fetch(`ajax/get_chat_messages.php?session_id=${sessionId}`)
            .then(parseJsonSafeInline)
            .then(data => {
                if (data.success) {
                    displayMessages(data.messages, data.admin_typing);
                }
            })
            .catch(error => console.error('Error loading messages:', error));
        }

        function displayMessages(messages, showTyping = false) {
            const container = document.getElementById('chat-messages');
            const typingIndicator = document.getElementById('typing-indicator');

            // Check if there are new messages
            const currentMessageCount = container.querySelectorAll('.message').length;
            const hasNewMessages = messages.length > currentMessageCount;

            // Clear existing messages but keep typing indicator
            const messagesHtml = messages.map(message => {
                const isUser = message.sender_type === 'user';
                const senderName = isUser ? 'You' : 'Support';
                const messageClass = isUser ? 'user' : 'admin';
                const timestamp = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                let messageContent = message.message;

                // Handle file attachments
                if (message.file_path) {
                    const fileName = message.file_path.split('/').pop();
                    const fileExtension = fileName.split('.').pop().toLowerCase();
                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);

                    if (isImage) {
                        messageContent += `<br><img src="${message.file_path}" alt="${fileName}" style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 8px;">`;
                    } else {
                        messageContent += `<br><a href="${message.file_path}" target="_blank" style="color: #007bff; text-decoration: none;"><i class="fas fa-file"></i> ${fileName}</a>`;
                    }
                }

                return `
                    <div class="message ${messageClass}">
                        <strong>${senderName}:</strong> ${messageContent}
                        <span class="timestamp">${timestamp}</span>
                    </div>
                `;
            }).join('');

            // Update container content
            container.innerHTML = messagesHtml;
            const existingTyping = container.querySelector('#typing-indicator');
            if (showTyping) {
                if (!existingTyping) {
                    const typingDiv = document.createElement('div');
                    typingDiv.id = 'typing-indicator';
                    typingDiv.className = 'typing-indicator';
                    typingDiv.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Support is typing...';
                    container.appendChild(typingDiv);
                } else {
                    existingTyping.style.display = 'block';
                }
            } else {
                if (existingTyping) {
                    existingTyping.style.display = 'none';
                }
            }

            // Play receive sound for new messages from admin
            if (hasNewMessages && messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                if (lastMessage.sender_type === 'admin') {
                    playSound('receive');
                }
            }

            // Scroll to bottom
            container.scrollTop = container.scrollHeight;
        }

        function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();

            if (!message || !sessionId) return;

            const formData = new FormData();
            formData.append('action', 'send_message');
            formData.append('session_id', sessionId);
            formData.append('message', message);

            fetch('chat-widget.php', {
                method: 'POST',
                body: formData
            })
            .then(parseJsonSafeInline)
            .then(data => {
                if (data.success) {
                    input.value = '';
                    loadMessages();
                } else {
                    alert('Failed to send message: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Error sending message');
            });
        }

        function sendFileMessage(file) {
            const formData = new FormData();
            formData.append('action', 'upload_file');
            formData.append('session_id', sessionId);
            formData.append('file', file);

            fetch('chat-widget.php', {
                method: 'POST',
                body: formData
            })
            .then(parseJsonSafeInline)
            .then(data => {
                if (data.success) {
                    // File uploaded successfully, now send a message with the file path
                    const fileFormData = new FormData();
                    fileFormData.append('action', 'send_message');
                    fileFormData.append('session_id', sessionId);
                    fileFormData.append('message', `Sent file: ${file.name}`);
                    fileFormData.append('file_path', data.file_path);

                    return fetch('chat-widget.php', {
                        method: 'POST',
                        body: fileFormData
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .then(parseJsonSafeInline)
            .then(data => {
                if (data.success) {
                    loadMessages();
                    playSound('send');
                } else {
                    alert('Failed to send file: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error sending file:', error);
                alert('Error sending file: ' + error.message);
            });
        }

        function startMessageRefresh() {
            refreshInterval = setInterval(loadMessages, 3000);
        }

        // Handle guest form submission
        document.getElementById('start-chat-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const guestName = document.getElementById('guest-name').value.trim();
            const guestEmail = document.getElementById('guest-email').value.trim();

            if (!guestName || !guestEmail) {
                alert('Please fill in all fields');
                return;
            }

            startChatSession(guestName, guestEmail);
        });

        // Handle message sending
        document.getElementById('send-btn').addEventListener('click', sendMessage);
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Handle emoji picker toggle
        document.getElementById('emoji-toggle-btn').addEventListener('click', function() {
            const emojiPicker = document.getElementById('emoji-picker');
            emojiPicker.classList.toggle('show');
        });

        // Handle emoji selection
        document.querySelectorAll('.emoji-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const emoji = this.textContent;
                const input = document.getElementById('message-input');
                input.value += emoji;
                input.focus();
                document.getElementById('emoji-picker').classList.remove('show');
            });
        });

        // Handle file attachment
        document.getElementById('attachment-btn').addEventListener('click', function() {
            document.getElementById('file-input').click();
        });

        // Handle file selection
        document.getElementById('file-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (limit to 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showError('File size must be less than 5MB');
                    return;
                }

                // Check file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
                if (!allowedTypes.includes(file.type)) {
                    showError('File type not allowed. Please upload images, PDFs, or documents.');
                    return;
                }

                // Show loading indicator
                showFileUploadProgress();

                // Send file as message
                sendFileMessage(file).finally(() => {
                    hideFileUploadProgress();
                });
            }
        });

        // Close emoji picker when clicking outside
        document.addEventListener('click', function(e) {
            const emojiPicker = document.getElementById('emoji-picker');
            const emojiToggle = document.getElementById('emoji-toggle-btn');
            if (!emojiPicker.contains(e.target) && !emojiToggle.contains(e.target)) {
                emojiPicker.classList.remove('show');
            }
        });

        // Handle typing indicators
        document.getElementById('message-input').addEventListener('input', function(e) {
            handleTyping();
        });

        document.getElementById('message-input').addEventListener('blur', function(e) {
            stopTyping();
        });

        function closeChat() {
            // Close chat session on server
            if (sessionId) {
                fetch('ajax/close_chat_session.php', {
                    method: 'POST',
                    body: new FormData([['session_id', sessionId]])
                })
                .then(parseJsonSafeInline)
                .then(data => {
                    console.log('Chat session closed:', data);
                })
                .catch(error => console.error('Error closing chat:', error));
            }

            // Clear session and stop refresh
            sessionId = null;
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }

            // Reset UI
            document.getElementById('chat-widget').style.display = 'none';
            document.getElementById('chat-toggle').style.display = 'flex';
            document.getElementById('guest-form').style.display = 'none';
            document.getElementById('chat-messages').style.display = 'none';
            document.getElementById('chat-input').style.display = 'none';
            document.getElementById('chat-messages').innerHTML = '<div id="typing-indicator" class="typing-indicator"><i class="fas fa-circle-notch fa-spin me-1"></i> Support is typing...</div>';
            document.getElementById('message-input').value = '';
            isMinimized = true;
        }

        // Handle typing indicators
        function handleTyping() {
            if (!isTyping) {
                isTyping = true;
                // Send typing start signal to server
                fetch('ajax/update_typing_status.php', {
                    method: 'POST',
                    body: new FormData([['session_id', sessionId], ['typing', '1']])
                }).catch(error => console.error('Error sending typing status:', error));
            }

            // Clear existing timeout
            if (typingTimeout) {
                clearTimeout(typingTimeout);
            }

            // Set timeout to stop typing after 2 seconds of inactivity
            typingTimeout = setTimeout(stopTyping, 2000);
        }

        function stopTyping() {
            if (isTyping) {
                isTyping = false;
                // Send typing stop signal to server
                fetch('ajax/update_typing_status.php', {
                    method: 'POST',
                    body: new FormData([['session_id', sessionId], ['typing', '0']])
                }).catch(error => console.error('Error sending typing status:', error));
            }

            if (typingTimeout) {
                clearTimeout(typingTimeout);
                typingTimeout = null;
            }
        }

        // Sound notification function
        function playSound(type) {
            // Use Web Audio API for better cross-browser compatibility
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                if (type === 'receive') {
                    // Pleasant notification sound for received messages
                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                    oscillator.frequency.exponentialRampToValueAtTime(400, audioContext.currentTime + 0.1);
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.3);
                } else if (type === 'send') {
                    // Subtle sound for sent messages
                    oscillator.frequency.setValueAtTime(600, audioContext.currentTime);
                    gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.2);
                }
            } catch (e) {
                // Fallback to HTML5 Audio if Web Audio API fails
                console.log('Web Audio API not supported, using fallback');
            }
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            stopTyping(); // Ensure typing status is cleared
        });
    </script>
</body>
</html>
<?php
}
?>
