async function loadMessages() {
    if (!checkAuth()) {
        window.location.href = 'staff_login.html';
        return;
    }

    const response = await fetch('api/get_messages.php', {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('authToken')}`
        }
    });
    const data = await response.json();

    if (data.success) {
        renderMessages(data.messages);
        renderStudentMessages(data.studentMessages);
    } else {
        alert('Failed to load messages');
    }
}

function renderMessages(messages) {
    const container = document.getElementById('messages-container');
    container.innerHTML = '';

    messages.forEach(msg => {
        const messageElement = document.createElement('div');
        messageElement.className = `message-preview ${msg.is_read ? '' : 'unread'}`;
        messageElement.innerHTML = `
            <div class="message-preview-header">
                <div class="message-preview-sender">${msg.first_name} ${msg.last_name}</div>
                <div class="message-preview-date">${new Date(msg.created_at).toLocaleString()}</div>
            </div>
            <div class="message-preview-content">${msg.subject}</div>
        `;
        messageElement.addEventListener('click', () => showMessageDetail(msg));
        container.appendChild(messageElement);
    });
}