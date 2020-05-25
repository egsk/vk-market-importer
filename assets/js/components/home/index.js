(function () {
    const el = document.getElementById('dashboard-helper');
    if (!el) {
        return;
    }
    if (localStorage.getItem('isDashboardClosed') === '1') {
        el.style.display = 'none';
    } else {
        const closeBtn = document.getElementById('close-dashboard-helper');
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            $(el).slideUp();
            localStorage.setItem('isDashboardClosed', '1');
        })
    }
}())

