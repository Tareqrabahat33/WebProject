// Navigation toggle function
function toggleNav() {
    var navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('active');
}

// Check URL parameters for success/error messages
document.addEventListener('DOMContentLoaded', function() {
    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    // Show registration success message if applicable
    if (urlParams.get('registered') === 'true') {
        document.getElementById('registrationSuccess').style.display = 'block';
    }
    
    // Show login error message if applicable
    if (urlParams.get('error') === 'login') {
        document.getElementById('loginError').style.display = 'block';
    }
});
