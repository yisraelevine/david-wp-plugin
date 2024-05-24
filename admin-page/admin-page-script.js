const navigate = (event) => {
    const url = new URL(window.location.href);
    url.searchParams.set('paged', event.target.value);
    window.location.href = url;
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.wrap').removeAttribute('style');

    const urls = document.querySelectorAll('input[type=url]');
    for (const url of urls) {
        url.addEventListener('input', (event) => {
            try {
                event.target.value = decodeURIComponent(event.target.value);
            } catch (error) { }
        })
    }

    const inputs = document.querySelectorAll('.pagination input');
    for (const input of inputs) {
        input.addEventListener('input', navigate);
    }
});
