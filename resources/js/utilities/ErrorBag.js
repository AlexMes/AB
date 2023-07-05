export default class ErrorBag {
    /**
     * Create new ErrorBag instance
     */
    constructor() {
        this.message = null;
        this.errors = {};
    }

    /**
     * Determine if an error exists for a given field
     * @param {string} field
     */
    has(field) {
        return this.errors.hasOwnProperty(field);
    }

    /**
     * Determine if we have any errors.
     */
    any() {
        return Object.keys(this.errors).length > 0;
    }

    /**
     * Retrieve the error message for a field.
     *
     * @param {string} field
     */
    get(field) {
        if (this.errors[field]) {
            return this.errors[field][0];
        }
    }

    /**
     * Record the new errors.
     *
     * @param {object} errors
     */
    record(errors) {
        this.errors = errors;
    }
    /**
     * Record errors from axios response
     *
     * @param {Object} response
     */
    fromResponse({ response }) {
        this.message = response.data.message;
        if (response.data.hasOwnProperty('errors')) {
            this.record(response.data.errors);
        }
        return this;
    }
    /**
     * Clear one or all error fields.
     *
     * @param {string|null} field
     */
    clear(field) {
        if (field) {
            delete this.errors[field];

            return;
        }
        this.errors = {};
        this.clearMessage();
    }

    /**
     * Determine is there any message available
     */
    hasMessage() {
        return this.message !== null;
    }

    /**
     * Clear main message
     */
    clearMessage() {
        this.message = null;
    }

    /**
     * Get main message
     */
    getMessage() {
        return this.message;
    }
}
