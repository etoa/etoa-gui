import { Controller } from '@hotwired/stimulus';
import debounce from 'debounce';

export default class extends Controller {
    form
    elements = []

    connect() {
        this.form = this.element.getElementsByTagName('form')[0];
        for (let element of [...this.form.getElementsByTagName('input'), ...this.form.getElementsByTagName('select')]) {
            let debounceChange = debounce(() => this.triggerChange(element), 400);

            this.elements.push(element);
            element.addEventListener('change', this.updateUrl);
            element.addEventListener('input', debounceChange);
        }
    }

    triggerChange(element) {
        element.dispatchEvent(new Event('change', { bubbles: true }));
    }

    updateUrl() {
        const formData = new FormData(this.form);
        let formProps = {};
        for (const [key, value] of Object.entries(Object.fromEntries(formData))) {
            let queryKey = key.replace(this.form.getAttribute('name') + '[', '').replace(']', '');
            if (value && queryKey !== '_token') {
                formProps[queryKey] = value;
            }
        }

        window.history.pushState({}, null, window.location.pathname + '?' + Object.keys(formProps).map(key => key + '=' + formProps[key]).join('&'));
    }

    disconnect() {
        for (let element of this.elements) {
            element.addEventListener.removeEventListeners();
        }
    }
}
