document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.querySelector('.navbar');

    const currentPath = window.location.pathname;
    const navLinks = navbar.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');

        if (linkPath === currentPath) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        } else {
            link.classList.remove('active');
            link.removeAttribute('aria-current');
        }
    });

});