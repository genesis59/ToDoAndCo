import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    redirect(event){
        window.location.href = event.currentTarget.dataset.url;
    }
}
