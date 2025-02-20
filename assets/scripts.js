// scripts.js
function showContent(type) {
    const panel = document.querySelector('.dynamic-panel');
    if (type === 'login') {
        panel.innerHTML = `
            <form method="post" action="login.php">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>`;
    } else if (type === 'register') {
        panel.innerHTML = `
            <form>
                <input type="text" placeholder="Username">
                <input type="email" placeholder="Email">
                <input type="password" placeholder="Password">
                <button type="submit">Register</button>
            </form>`;
    } else {
        panel.innerHTML = `<h2>Welcome to Proxy Site</h2><p>Explore the best proxies and manage your connections efficiently!</p>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    showContent(); // Afișăm conținutul implicit "Welcome"
});
function showLogin() {
    const content = document.getElementById('main-content');
    content.innerHTML = `
        <form method="post" action="login.php" class="inline-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    `;
}

