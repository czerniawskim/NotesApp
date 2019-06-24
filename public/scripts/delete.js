const notes = document.getElementById("notes");

if (notes) {
    notes.addEventListener('click', e => {
        if (e.target.className === 'del') {
            if (confirm('Are you sure?')) {
                const id = e.target.getAttribute('data-id');

                fetch('/delete/' + id, {
                    method: 'POST'
                }).then(res => window.location.reload());
            }
        }
    })
}