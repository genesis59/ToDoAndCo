import 'bootstrap-datetime-picker/js/bootstrap-datetimepicker';
import 'bootstrap-datetime-picker/js/locales/bootstrap-datetimepicker.fr';
import 'bootstrap-datetime-picker/css/bootstrap-datetimepicker.min.css';

$(document).ready(function() {

    // Initialiser le datetimepicker
    $('#sandbox-container input.datetimepicker').datetimepicker({
        format: 'dd/mm/yyyy hh:mm', // Ajustez le format selon vos besoins
        locale: 'fr',
    });
});
