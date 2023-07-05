import Mousetrap from 'mousetrap';

Mousetrap.bind('/',function (e) {
    const el = document.getElementById('search');
    if (el) {
        e.preventDefault();
        el.focus();
    }
});
