document.addEventListener('DOMContentLoaded', function () {
    const searchBar = document.getElementById('searchBar');
    
    searchBar.addEventListener('input', function () {
        const searchValue = searchBar.value.toLowerCase();
        const dataRows = document.querySelectorAll('.data-row');
        
        dataRows.forEach(function (row) {
            const rowData = row.textContent.toLowerCase();
            if (rowData.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
