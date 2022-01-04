import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'target', 'field' ]

    connect() {
        this.update()
    }

    update() {
        let sum = 0, field
        for (field of this.fieldTargets) {
            if (field.querySelector('input').value) {
                sum += parseInt(field.querySelector('input').value)
            }
        }

        this.targetTarget.querySelector('input').value = 100 - sum
    }
}
