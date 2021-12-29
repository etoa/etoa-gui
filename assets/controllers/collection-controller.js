import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'prototype', 'container']

    addEntry() {
        const entry = this.prototypeTarget.cloneNode(true);
        entry.querySelector('.hide')?.classList.remove('hide')
        for (let i = 0; i < entry.attributes.length; i++) {
            let attrib = entry.attributes[i];
            if (attrib.specified && attrib.name.includes('-target')) {
                entry.removeAttribute(attrib.name);
            }
        }

        this.containerTarget.append(entry);
    }

    removeEntry(event) {
        let element = event.target,
            parentElement;

        while (parentElement = element.parentElement) {
            if (parentElement === this.containerTarget) {
                element.remove();
            }

            element = parentElement;
        }
    }
}
