import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['resultSearch']
    static values = {
        url: String,
        page: String,
        limit: String
    }
    async onSearchInputEvent(event){
        const params = new URLSearchParams({
            search: event.currentTarget.value,
            page: 1,
            limit: this.limitValue,
            preview: 1
        });
        const response = await fetch(`${this.urlValue}?${params.toString()}`)
        this.resultSearchTarget.innerHTML = await response.text();
    }
}
