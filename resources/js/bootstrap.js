import Echo from "laravel-echo";
import io from "socket.io-client";
import { RedirectLoggedOff } from "./interceptors/RedirectLoggedOff";

import { library } from "@fortawesome/fontawesome-svg-core";

import {
    faPlay,
    faPause,
    faStop,
    faEdit,
    faSpinner,
    faSync,
    faExclamationCircle,
    faEye,
    faCopy,
    faPencilAlt,
    faTimesCircle,
    faCheckCircle,
    faDollarSign,
    faCheck,
    faBell,
    faBars,
    faAngleUp,
    faFilter,
    faPlus,
    faSortUp,
    faSortDown,
    faAngleDown,
    faAd,
    faBookmark,
    faExchange,
    faFile,
    faFileCheck,
    faBallotCheck,
    faHandsUsd,
    faCommentDots,
    faDownload,
    faUpload,
} from "@fortawesome/pro-regular-svg-icons";
library.add(
    faPlay,
    faPause,
    faStop,
    faEdit,
    faSpinner,
    faSync,
    faExclamationCircle,
    faEye,
    faCopy,
    faPencilAlt,
    faTimesCircle,
    faCheckCircle,
    faDollarSign,
    faCheck,
    faBell,
    faBars,
    faAngleUp,
    faFilter,
    faPlus,
    faSortUp,
    faSortDown,
    faAngleDown,
    faAd,
    faBookmark,
    faExchange,
    faFile,
    faFileCheck,
    faBallotCheck,
    faHandsUsd,
    faCommentDots,
    faDownload,
    faUpload,
);

window.axios = require("axios");
window.axios.defaults.headers.common = {
    "X-Requested-With": "XMLHttpRequest",
    "Content-Type": "application/json",
    "X-CSRF-TOKEN": document.head.querySelector('meta[name="csrf-token"]')
        .content
};

window.axios.interceptors.response.use(response => response, RedirectLoggedOff);

window.io = io;
window.Echo = new Echo({
    namespace: null,
    broadcaster: "socket.io",
    host: window.location.hostname
});
