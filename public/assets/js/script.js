$(document).ready(function() {
    $('#sortableTable').DataTable({
       "order": [[0, 'asc']] // Set initial sorting column (change 0 to the index of your desired column)
    });
 });