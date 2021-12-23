import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'target' ]

    onClick() {
        this.targetTarget.classList.toggle('hide')
    }
}
