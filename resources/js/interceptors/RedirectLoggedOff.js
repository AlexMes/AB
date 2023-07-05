
export function RedirectLoggedOff(error){
    if(error.response && error.response.status===401){
        window.location = '/login';
    }

    return Promise.reject(error);
}
