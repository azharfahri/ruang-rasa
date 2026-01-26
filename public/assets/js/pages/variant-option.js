let index = 1;

document.getElementById('addRow').addEventListener('click', function () {
    const tbody = document.getElementById('optionRows');

    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${index + 1}</td>
        <td>
            <input type="text"
                   name="options[${index}][option_name]"
                   class="form-control"
                   required>
        </td>
        <td>
            <input type="number"
                   name="options[${index}][price_impact]"
                   class="form-control"
                   min="0"
                   required>
        </td>
        <td class="text-end">
            <button type="button"
                    class="btn btn-sm btn-danger remove-row">×</button>
        </td>
    `;

    tbody.appendChild(row);
    index++;
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
    }
});
