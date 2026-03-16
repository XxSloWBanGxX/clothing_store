document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('mouseenter', () => {
            link.style.opacity = '0.9';
        });

        link.addEventListener('mouseleave', () => {
            link.style.opacity = '1';
        });
    });
});