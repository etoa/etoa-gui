import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'planets', 'metal', 'crystal', 'plastic' ]

    input(event) {
        if (!['metal', 'crystal', 'plastic'].includes(event.target.getAttribute('data-target'))) {
            this.calculate();
        }
    }

    calculate() {
        const metal = this.metalTarget.value;
        const crystal = this.crystalTarget.value;
        const plastic = this.plasticTarget.value;

        for (let element of this.planetsTarget.children) {
            const percentage = element.querySelector('[data-target=percentage]').value;
            element.querySelector('[data-target=metal]').value = Math.ceil(percentage * metal / 100);
            element.querySelector('[data-target=crystal]').value = Math.ceil(percentage * crystal / 100);
            element.querySelector('[data-target=plastic]').value = Math.ceil(percentage * plastic / 100);
        }
    }
}
