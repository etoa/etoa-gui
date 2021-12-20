import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ "limit" ]

    first() {
        this.limitTarget.value = 0
    }

    previous() {
        this.setLimit(this.element.getAttribute('data-previous'))
    }

    next() {
        this.setLimit(this.element.getAttribute('data-next'))
    }

    last() {
        this.setLimit(this.element.getAttribute('data-last'))
    }

    setLimit(limit) {
        this.limitTarget.value = limit;
        this.limitTarget.dispatchEvent(new Event('change', {
            bubbles: true,
            cancelable: true,
        }));
    }
}
