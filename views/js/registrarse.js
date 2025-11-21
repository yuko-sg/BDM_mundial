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