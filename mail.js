document.addEventListener('DOMContentLoaded', () => {
  const recipientsField = document.querySelector('[name="recipients"]');
  const recipientCounter = document.getElementById('recipientCounter');
  const messageField = document.querySelector('[name="message"]');
  const charCount = document.getElementById('charCount');

  recipientsField.addEventListener('input', () => {
    const count = recipientsField.value.split(/[\s,]+/).filter(e => e.trim()).length;
    recipientCounter.textContent = `${count} recipient${count !== 1 ? 's' : ''}`;
  });

  messageField.addEventListener('input', () => {
    const count = messageField.value.length;
    charCount.textContent = `${count} characters`;
    charCount.classList.toggle('warning', count > 1000);
  });
});
