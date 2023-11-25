import {Controller} from '@hotwired/stimulus';
import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';
import '@ckeditor/ckeditor5-build-decoupled-document/build/translations/fr';

export default class extends Controller {
    static targets = ['hiddenTaskContent', 'toolbarContainer','editorContainer'];
    connect() {
        if (this.hiddenTaskContentTarget.value) {
            this.editorContainerTarget.innerHTML = this.hiddenTaskContentTarget.value
        }
        DecoupledEditor.create(this.editorContainerTarget, {
            language: 'fr',
            toolbar: [
                'heading',
                '|',
                'fontBackgroundColor',
                'fontColor',
                'fontFamily',
                'fontSize',
                '|',
                'bold',
                'italic',
                'strikethrough',
                'underline',
                'link',
                '|',
                'alignment',
                'blockQuote',
                'bulletedList',
                'numberedList',
                'outdent',
                'indent',
                '|',
                'insertTable',
                'undo',
                'redo'
            ],
            wordcount: {
                minCharCount: 500
            }
        })
        .then(editor => {
            this.toolbarContainerTarget.appendChild(editor.ui.view.toolbar.element);
            editor.model.document.on('change:data', () => {
                console.log(editor.getData())
                this.hiddenTaskContentTarget.value = editor.getData();
            });
        })
        .catch(error => {
            console.error("Error initializing CKEditor. Please try again later.");
        });
    }
}
