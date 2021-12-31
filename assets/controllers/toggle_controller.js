import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'target' ]

    onClick(event) {
        const target = event.target.getAttribute('href');
        if (target.startsWith('#')) {
            let targetElement = document.querySelector(target);
            if (targetElement) {
                targetElement.classList.toggle('hide');
                return;
            }

        }

        this.targetTarget.classList.toggle('hide')
    }
}
