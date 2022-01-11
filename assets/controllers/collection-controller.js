import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'prototype', 'container']
    counter

    connect() {
        this.counter = this.containerTarget.children.length;
    }

    addEntry() {
        const counter = ++this.counter;
        const entry = this.prototypeTarget.cloneNode(true);
        entry.querySelector('.hide')?.classList.remove('hide')
        for (let i = 0; i < entry.attributes.length; i++) {
            let attrib = entry.attributes[i];
            if (attrib.specified && attrib.name.includes('-target')) {
                entry.removeAttribute(attrib.name);
            }
        }

        // Revert search choice fields in prototype
        for (let choiceList of entry.querySelectorAll('[data-controller=searchable-choice]')) {
            let elements = [];
            let optGroups = {};
            let currentOptGroup = null;
            for (let option of choiceList.querySelectorAll('[role=listbox] > div')) {
                // Regular options re marked as role=option, optgroup are market as role=group and their items as role=treeitem
                if (option.getAttribute('role') === 'option') {
                    elements.push('<option value="' + option.getAttribute('data-value') + '">' + option.innerHTML + "</option>");
                } else if (option.getAttribute('role') === 'group') {
                    optGroups[currentOptGroup = option.textContent] = [];
                } else if (option.getAttribute('role') === 'treeitem') {
                    optGroups[currentOptGroup].push('<option value="' + option.getAttribute('data-value') + '">' + option.innerHTML + "</option>");
                }
            }

            for (let optGroup in optGroups) {
                elements.push('<optgroup label="' + optGroup + '">' + optGroups[optGroup].join('') + '</optgroup>');
            }

            if (elements.length) {
                choiceList.innerHTML = '<select name="' + choiceList.querySelector('select').getAttribute('name') + '" data-searchable-choice-target="input">' + elements.join('') + '</select>';
            }
        }

        entry.innerHTML = entry.innerHTML.replaceAll('[0]', '[' + counter + ']').replaceAll('_0_', '_' + counter + '_');
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
