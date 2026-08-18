// =========================================================
// NAVEGACIÓN / MENÚ MÓVIL
// =========================================================
(function () {
    const toggle = document.querySelector('.nav-toggle');
    const links = document.querySelector('.nav-links');
    const header = document.querySelector('.header');

    if (toggle && links) {
        toggle.addEventListener('click', function () {
            const isActive = links.classList.toggle('active');
            toggle.classList.toggle('active', isActive);
            toggle.setAttribute('aria-expanded', isActive ? 'true' : 'false');
        });

        links.addEventListener('click', function (e) {
            if (e.target.tagName === 'A') {
                links.classList.remove('active');
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Sombrear header al hacer scroll
    if (header) {
        const onScroll = function () {
            header.classList.toggle('scrolled', window.scrollY > 10);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }
})();