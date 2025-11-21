// Dark mode toggle functionality
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;

// Check for saved dark mode preference
const darkMode = localStorage.getItem('darkMode');

if (darkMode === 'enabled') {
    body.classList.add('dark-mode');
}

// Toggle dark mode
darkModeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    
    // Save preference
    if (body.classList.contains('dark-mode')) {
        localStorage.setItem('darkMode', 'enabled');
    } else {
        localStorage.setItem('darkMode', 'disabled');
    }
});

// Character counter for textarea
const textarea = document.getElementById('contenido');
const charCount = document.getElementById('charCount');

textarea.addEventListener('input', () => {
    const count = textarea.value.length;
    charCount.textContent = count;
    
    // Change color when approaching limit
    if (count > 270) {
        charCount.style.color = '#e53e3e';
    } else if (count > 240) {
        charCount.style.color = '#f59e0b';
    } else {
        charCount.style.color = '#555';
    }
});