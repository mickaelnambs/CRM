function btnDelete(btn, modal, datatable) {
    $(datatable).on("click", btn, function (e) {
        var url = $(this).attr('data-href')
        showDeleteModal(modal, url);
    })
}

function showDeleteModal(modal, url) {
    $(modal).one('shown.bs.modal', async function (e) {
        e.preventDefault()
        $(this).find('.delete').attr('href', url);
    });
}