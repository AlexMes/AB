import downloadLink from '../../../../resources/js/utilities/helpers/downloadLink';


require('alpinejs');
require('./socket');
require('./utils');
import axios from 'axios';

import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/flatpickr.js';
import moment from 'moment';

import multiselect from './multiselect';
import {Russian} from 'flatpickr/dist/l10n/ru.js';

window.AssignmentsIndex = function () {
    return {
        isBusy: false,
        filters: {
            status: null,
            office: null,
            period: null,
            manager: null,
            offer: null,
            registration_period: null,
            gender: null,
            smooth_lo: null,
        },
        transferAssignments: [],
        transferOpened: false,
        deletingOpened: false,
        markingLeftoverOpened: false,
        openFilterButton: false,
        exportLeads() {
            this.isBusy = true;
            axios
                .get('/export/assignments', {
                    params: this.filters,
                    responseType: 'blob',
                })
                .then(({ data }) => downloadLink(data, 'assignments.xlsx'))
                .finally(() => (this.isBusy = false));
        },
        init() {
            if (this.$refs.office !== undefined) {
                this.filters.office = this.$refs.office.value;
            }
            if (this.$refs.gender !== undefined) {
                this.filters.gender = this.$refs.gender.value;
            }
            if (this.$refs.smooth_lo !== undefined) {
                this.filters.smooth_lo = this.$refs.smooth_lo.value;
            }

            this.transferAssignments = Array.from(document.querySelectorAll('[name*=assignments]:checked'))
                .map(node => node.value);

            this.filters.period = this.$refs.period.value;
            flatpickr(this.$refs.period, {
                defaultDates: `${moment().format(
                    'YYYY-MM-DD',
                )} to ${moment().format('YYYY-MM-DD')}`,
                minDate: '2019-12-02',
                maxDate: moment().format('YYYY-MM-DD'),
                mode: 'range',
                locale: document.querySelector('html').getAttribute('lang') === 'en' ? 'default' : 'ru',
            });

            this.filters.registration_period = this.$refs.registration_period.value;
            flatpickr(this.$refs.registration_period, {
                defaultDates: `${moment().format(
                    'YYYY-MM-DD',
                )} to ${moment().format('YYYY-MM-DD')}`,
                minDate: '2019-12-02',
                maxDate: moment().format('YYYY-MM-DD'),
                mode: 'range',
            });

            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.Statistics = function () {
    return {
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.ManagerStatistics = function () {
    return {
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.OfficeStatistics = function () {
    return {
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};
