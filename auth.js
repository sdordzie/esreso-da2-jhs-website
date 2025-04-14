async function login(staffId, password) {
    const response = await fetch('api/staff_login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ staffId, password })
    });
    return await response.json();
}

async function register(staffData) {
    const response = await fetch('api/staff_register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(staffData)
    });
    return await response.json();
}

function checkAuth() {
    return localStorage.getItem('authToken') !== null;
}

function logout() {
    localStorage.removeItem('authToken');
    window.location.href = 'staff_login.html';
}