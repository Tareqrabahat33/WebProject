// Navigation toggle function
function toggleNav() {
    var navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('active');
}

// Terms and conditions confirmation
function showconfirm(){
    let res = confirm("we will be able to access your personal info, sure you want to procced?");
    if (res){
        console.log("confirmed")
    } else{
        console.log("canceled")
    }
}

// Client-side form validation
document.addEventListener('DOMContentLoaded', function() {
    const signupForm = document.getElementById('signupForm');
    
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            let isValid = true;
            const errorDiv = document.getElementById('signupError');
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            // Reset previous error messages
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            document.getElementById('nameError').textContent = '';
            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            document.getElementById('confirmError').textContent = '';
            
            // Validate name
            if (name.trim() === '') {
                document.getElementById('nameError').textContent = 'Name is required';
                isValid = false;
            }
            
            // Validate email
            if (email.trim() === '') {
                document.getElementById('emailError').textContent = 'Email is required';
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('emailError').textContent = 'Enter a valid email address';
                isValid = false;
            }
            
            // Validate password
            if (password.trim() === '') {
                document.getElementById('passwordError').textContent = 'Password is required';
                isValid = false;
            } else if (password.length < 6) {
                document.getElementById('passwordError').textContent = 'Password must be at least 6 characters';
                isValid = false;
            }
            
            // Validate password confirmation
            if (confirmPassword !== password) {
                document.getElementById('confirmError').textContent = 'Passwords do not match';
                isValid = false;
            }
            
            // Check if terms are accepted
            if (!document.getElementById('terms').checked) {
                errorDiv.textContent = 'You must agree to the terms and conditions';
                errorDiv.style.display = 'block';
                isValid = false;
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});
