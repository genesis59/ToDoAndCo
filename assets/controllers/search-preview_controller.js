import { Controller } from '@hotwired/stimulus';
import {useDebounce} from "stimulus-use";

export default class extends Controller {

    static targets = ['resultSearch','searchInput']
    static debounces = ['onSearchInputEvent']
    static values = {
        url: String,
        page: String,
        limit: String
    }

    connect() {
        super.connect();
        useDebounce(this,{wait:200});
    }

    async onSearchInputEvent(){
        const params = new URLSearchParams({
            search: this.searchInputTarget.value,
            page: 1,
            limit: this.limitValue,
            preview: 1
        });
        const response = await fetch(`${this.urlValue}?${params.toString()}`)
        this.resultSearchTarget.innerHTML = await response.text();
    }
}
