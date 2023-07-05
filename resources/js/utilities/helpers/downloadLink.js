export default function downloadLink(data = {}, name = 'file.txt', target = '_blank') {
    const url = window.URL.createObjectURL(new Blob([data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', name);
    link.setAttribute('target', target);
    document.body.appendChild(link);
    link.click();
    link.remove();
}
