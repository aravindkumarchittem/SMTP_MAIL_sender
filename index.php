<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Mail Composer</title>
    
</head>
<body>
    <div class="mail-container">
        <div class="header">
            <h2>✉ Compose Mail</h2>
            <p>Send your message with style</p>
        </div>
        
        <form action="send_mail.php" method="POST" id="mailForm" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Recipients</label>
                <textarea 
                    name="recipients" 
                    class="form-textarea" 
                    placeholder="Enter email addresses separated by commas or new lines..."
                    required
                    id="recipients"
                ></textarea>
                <div class="recipients-counter" id="recipientCounter">0 recipients</div>
            </div>

            <div class="form-group">
                <label class="form-label">Subject</label>
                <input 
                    type="text" 
                    name="subject" 
                    class="form-input" 
                    placeholder="What's this email about?"
                    required
                    id="subject"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Message</label>
                <textarea 
                    name="message" 
                    class="form-textarea message-field" 
                    placeholder="Write your message here..."
                    required
                    id="message"
                ></textarea>
                <div class="character-count" id="charCount">0 characters</div>
            </div>

            <div class="form-group">
    <label class="form-label" for="attachments">Attachments (optional)</label>
    <input type="file" name="attachments[]" id="attachments" multiple 
           class="form-input" 
           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx">
    <small style="color:#888;">Allowed types: jpg, jpeg, png, pdf, doc, docx, txt, xls, xlsx, ppt, pptx. Max size: 5MB each.</small>
</div>


            <button type="submit" class="send-button">
                🚀 Send Message
            </button>
        </form>
    </div>

    <script>
        // Recipients counter
        const recipientsField = document.getElementById('recipients');
        const recipientCounter = document.getElementById('recipientCounter');

        function updateRecipientCount() {
            const text = recipientsField.value.trim();
            if (!text) {
                recipientCounter.classList.remove('show');
                return;
            }

            const emails = text.split(/[,\n]/).filter(email => email.trim());
            const count = emails.length;
            
            recipientCounter.textContent = ${count} recipient${count !== 1 ? 's' : ''};
            recipientCounter.classList.add('show');
        }

        recipientsField.addEventListener('input', updateRecipientCount);

        // Character counter for message
        const messageField = document.getElementById('message');
        const charCount = document.getElementById('charCount');

        function updateCharCount() {
            const count = messageField.value.length;
            charCount.textContent = ${count} characters;
            
            if (count > 1000) {
                charCount.classList.add('warning');
            } else {
                charCount.classList.remove('warning');
            }
        }

        messageField.addEventListener('input', updateCharCount);

        // Form submission with loading state
        const form = document.getElementById('mailForm');
        const sendButton = form.querySelector('.send-button');
        const originalButtonText = sendButton.innerHTML;

        form.addEventListener('submit', function(e) {
            sendButton.innerHTML = '📤 Sending...';
            sendButton.style.opacity = '0.8';
            sendButton.style.cursor = 'not-allowed';
            
            // Reset button after 3 seconds (for demo purposes)
            setTimeout(() => {
                sendButton.innerHTML = originalButtonText;
                sendButton.style.opacity = '1';
                sendButton.style.cursor = 'pointer';
            }, 3000);
        });

        // Auto-resize textareas
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        [recipientsField, messageField].forEach(field => {
            field.addEventListener('input', () => autoResize(field));
        });

        // Add subtle parallax effect
        document.addEventListener('mousemove', (e) => {
            const container = document.querySelector('.mail-container');
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            const moveX = (x / rect.width) * 10;
            const moveY = (y / rect.height) * 10;
            
            container.style.transform = perspective(1000px) rotateX(${moveY * 0.1}deg) rotateY(${moveX * 0.1}deg);
        });

        document.addEventListener('mouseleave', () => {
            const container = document.querySelector('.mail-container');
            container.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
        });
    </script>
</body>
</html>