import './bootstrap';
import './react-app';
import nProgress from 'nprogress';
import sort from '@alpinejs/sort'
 

nProgress.configure({ showSpinner: false });

document.addEventListener('livewire:initialized', () => {
    // Reference: https://livewire.laravel.com/docs/javascript#request-hooks
    Livewire.hook('request', ({ respond, fail }) => {
        const timer = setTimeout(() => nProgress.start(), 250)
        respond(({ status, response }) => {
            clearTimeout(timer);
            nProgress.done()
        })
        fail(({ status, content, preventDefault }) => {
            clearTimeout(timer);
            nProgress.done()
        })
    })
});

Alpine.directive('clipboard', (el) => {
    el.addEventListener('click', () => {
        navigator.clipboard.writeText(el.dataset.textCopy)
    })
})

Alpine.plugin(sort)
