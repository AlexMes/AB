export default {
    filters: {},
    options: {},
    show: {},
    search: {},
    multiple: {},

    open(id) { this.show[id] = true; },
    close(id) { this.show[id] = false; },
    isOpen(id) { return this.show[id]; },
    select(id, index, event) {
        if (!this.options[id][index].selected) {
            if (!this.multiple[id]) {
                this.removeAll(id);
            }
            this.options[id][index].selected = true;
            this.options[id][index].element = event.target;

            this.filters[id].push(this.options[id][index].value);
        } else {
            if (!this.multiple[id]) {
                return;
            }
            this.options[id][index].selected = false;

            const el = this.filters[id].findIndex(e => e === this.options[id][index].value);
            if (el !== -1) {
                this.filters[id].splice(el, 1);
            }
        }
    },
    removeAll(id) {
        this.options[id].forEach(option => option.selected = false);
        this.filters[id] = [];
    },
    multiselectInit() {
        const multiselects = document.getElementsByClassName('fn-multiselect');

        for (let i = 0; i < multiselects.length; i++) {
            const id = multiselects[i].getAttribute('id');
            const options = multiselects[i].options;

            this.filters[id] = [];
            this.options[id] = [];
            this.show[id] = false;
            this.search[id] = '';
            this.multiple[id] = multiselects[i].hasAttribute('multiple');

            this.$watch('search.' + id, () => this.filterOptions(id));
            this.$watch('show.' + id, () => {
                if (this.show[id]) {
                    this.$nextTick(() => this.$refs[`search_${id}`].focus());
                } else {
                    this.search[id] = '';
                }
            });

            for (let i = 0; i < options.length; i++) {
                this.options[id].push({
                    value: options[i].value,
                    text: options[i].innerText,
                    selected: options[i].getAttribute('selected') !== null,
                    filtered: false,
                });

                if (this.options[id][i].selected) {
                    this.filters[id].push(this.options[id][i].value);
                }
            }
        }
    },
    selectedValues(id){
        const result = this.options[id].filter(option => option.selected).map(option => option.text);
        return result.length > 0 ? result : ['Все'];
    },
    filterOptions(id) {
        if (this.search[id] === null || this.search[id] === undefined) {
            return;
        }

        this.options[id].forEach(option => {
            option.filtered = !option.text.match(new RegExp(this.search[id], 'i'));
        });
    },
};
