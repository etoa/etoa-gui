import { Controller } from '@hotwired/stimulus';
import Choices from "choices.js/public/assets/scripts/choices.js";

export default class extends Controller {
    static targets = [ 'input' ]
    choices

    inputTargetConnected(target) {
        this.choices = new Choices(target, {
            itemSelectText: '',
            shouldSort: false
        })
    }

    inputTargetDisconnected(target) {
        delete this.choices
    }
}
