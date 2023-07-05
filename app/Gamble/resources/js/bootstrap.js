import axios from 'axios';

window.axios = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
    },
    redirect: false,
});

window.axios.interceptors.response.use(response => response, error => {
    if (error.response && error.response.status === 401) {
        window.location = '/login';
    }

    return Promise.reject(error);
});
