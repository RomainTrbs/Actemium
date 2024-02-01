$(document).ready(function() {
    var url = window.location.href;

    // Iterate through each navbar link
    $('.navbar-nav a').each(function() {
        // Check if the link's href matches the current URL
        if (url.indexOf(this.href) !== -1) {
            $(this).addClass('active');
        }
    });
});
$(document).ready(function() {
    $('#sortableTable').DataTable({
       "order": [[0, 'asc']] // Set initial sorting column (change 0 to the index of your desired column)
    });
 });
 // Ajoutez ce script dans votre template ou fichier JavaScript
$(document).ready(function() {
    $('input[name="form2[type]"]').change(function() {
        if ($(this).val() === 'collaborateur_specific') {
            $('select[name="form2[value]"]').show();
        } else {
            $('select[name="form2[value]"]').hide();
        }
    });
});
