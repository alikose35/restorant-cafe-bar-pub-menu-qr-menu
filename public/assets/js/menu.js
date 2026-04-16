(() => {
    const hash = window.location.hash;
    if (!hash.startsWith('#cat-')) return;
    const target = document.querySelector(hash);
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
})();
