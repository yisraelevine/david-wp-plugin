const navigate = (event) => {
    const url = new URL(window.location.href);
    url.searchParams.set('paged', event.target.value);
    window.location.href = url;
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.wrap').removeAttribute('style');

    const inputs = document.querySelectorAll('.pagination input');
    for (const input of inputs) {
        input.addEventListener('input', navigate);
    }
});
