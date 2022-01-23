import { Controller } from '@hotwired/stimulus';
import Choices from "choices.js/public/assets/scripts/choices.js";

export default class extends Controller {
    static targets = [ 'input' ]
    choices

    inputTargetConnected(target) {
        this.choices = new Choices(target, {
            itemSelectText: '',
            shouldSort: false,
            choices: [{label: 'Schnellsuche'}],
            noChoicesText: 'Kein Suchbegriff eingegeben!',
            noResultsText: 'Keine Spieler, Allianzen oder Planeten gefunden',
            searchResultLimit: 30,
        })

        target.addEventListener('search', async (e) => {
            let data = await this.search(e.detail.value)
                .then(res => {
                        return res.json();
                    });

            this.choices.setChoices(data, 'value', 'label', true);
        });

        target.addEventListener('choice', (e) => {
            if (e.detail.choice.value) {
                window.location = e.detail.choice.customProperties.link;
            }
        });
    }

    search(query) {
        return fetch('/admin/overview/search?query=' + query);
    }

    inputTargetDisconnected(target) {
        delete this.choices
    }
}
