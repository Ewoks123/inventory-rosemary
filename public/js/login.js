// Initialize admins if not exists
if (!localStorage.getItem('adminAccounts')) {
    const defaultAdmins = [
        { username: 'Rosemary', password: 'admin', role: 'Administrator' }
    ];
    localStorage.setItem('adminAccounts', JSON.stringify(defaultAdmins));
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const usernameInput = document.getElementById('username').value;
    const passwordInput = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');
    
    const admins = JSON.parse(localStorage.getItem('adminAccounts'));
    const user = admins.find(a => a.username === usernameInput && a.password === passwordInput);
    
    if (user) {
        // Simpan status login di localStorage
        localStorage.setItem('isLoggedIn', 'true');
        localStorage.setItem('user', user.username);
        localStorage.setItem('currentUser', JSON.stringify(user));
        
        // Redirect ke dashboard
        window.location.href = 'index.html';
    } else {
        // Tampilkan pesan error
        errorMessage.classList.remove('hidden');
        
        // Reset form password
        document.getElementById('password').value = '';
    }
});

// Cek jika sudah login, langsung ke dashboard
window.onload = function() {
    if (localStorage.getItem('isLoggedIn') === 'true') {
        window.location.href = 'index.html';
    }
}
