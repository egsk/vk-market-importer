(function () {
    const alerts = document.querySelectorAll('.alert-fixed-message');
    let offset = 0;
    const distance = 20;
    const offsets = [];
    alerts.forEach((el, index) => {
        $(el).fadeIn();
        el.style.transform = `translateY(${offset}px)`;
        offsets[index] = offset;
        el.querySelector('.alert-fixed-message__close')
        el.addEventListener('click', function() {
            $(this).fadeOut();
            for (let i = index + 1; i < alerts.length; i++) {
                offsets[i] -= el.offsetHeight + distance
                alerts[i].style.transition = '.3s';
                alerts[i].style.transform  = `translateY(${offsets[i]}px)`
            }
        });

        offset += el.offsetHeight + distance;
    })
}())