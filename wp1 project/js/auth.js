// Check user authentication status and update navigation bar
document.addEventListener('DOMContentLoaded', function() {
    // Fetch user session status
    fetch('/wp1%20project/wp1%20project/php/check_session.php')
    .then(response => response.json())
    .then(data => {
        if (data.loggedIn) {
            // User is logged in
            document.getElementById('authLinks').style.display = 'none';
            document.getElementById('userLinks').style.display = 'inline';
            document.getElementById('welcomeUser').textContent = 'Welcome, ' + data.userName;
            
            // Check if user is admin and add admin-specific links
            if (data.isAdmin) {
                // Add admin link if it doesn't exist
                const adminLink = document.createElement('a');
                adminLink.href = '/wp1%20project/wp1%20project/Admin.html';
                adminLink.textContent = 'Admin Panel';
                adminLink.id = 'adminPanelLink';
                
                // Only add if it doesn't already exist
                if (!document.getElementById('adminPanelLink')) {
                    document.getElementById('userLinks').appendChild(adminLink);
                }
                
                console.log('Admin user logged in');
            }
            
            // Redirect if on login/signup pages
            const currentPage = window.location.pathname;
            if (currentPage.includes('login.html') || currentPage.includes('sign_up.html')) {
                window.location.href = '/wp1%20project/wp1%20project/main_page.html';
            }
        } else {
            // User is not logged in
            document.getElementById('authLinks').style.display = 'inline';
            document.getElementById('userLinks').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error checking login status:', error);
    });
});
