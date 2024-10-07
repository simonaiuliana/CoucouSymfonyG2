import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    initialize() {
        this._onPreConnect = this._onPreConnect.bind(this);
        this._onConnect = this._onConnect.bind(this);
    }
    connect() {
        this.element.addEventListener('autocomplete:pre-connect', this._onPreConnect);
        this.element.addEventListener('autocomplete:connect', this._onConnect);
    }

    disconnect() {
        this.element.removeEventListener('autocomplete:connect', this._onConnect);
        this.element.removeEventListener('autocomplete:pre-connect', this._onPreConnect);
    }
    _onPreConnect(event) {
        event.detail.options.render.option_create = function(data, escapeData) {
            return `<div class="create">Ajouter <strong>${escapeData(data.input)}</strong>&hellip;</div>`;
        };
        event.detail.options.onType = function(str){
            /* Security front side */
            let canCreate = str.length > 1 && str.length <= 60;
            if(canCreate)
                for (const tag in this.options) {
                    if(this.options[tag].text.toLowerCase() !== str.toLowerCase()) continue;
                    canCreate = false;
                    break;
                }
            this.settings.create = canCreate;
        }
    }
    _onConnect(event) {
    }
}