document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.categoria-row.font-bold').forEach(row => {
        row.addEventListener('click', function() {
            const id = this.dataset.id;
            const nivel = parseInt(this.dataset.nivel);
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-right');

            let nextRow = this.nextElementSibling;
            let isHiding = icon.classList.contains('fa-chevron-right');

            while (nextRow && parseInt(nextRow.dataset.nivel) > nivel) {
                if (parseInt(nextRow.dataset.nivel) === nivel + 1) {
                    nextRow.classList.toggle('hidden');
                } else if (isHiding) {
                    nextRow.classList.add('hidden');
                }

                if (isHiding) {
                    let childIcon = nextRow.querySelector('i');
                    if (childIcon) {
                        childIcon.classList.remove('fa-chevron-down');
                        childIcon.classList.add('fa-chevron-right');
                    }
                }

                nextRow = nextRow.nextElementSibling;
            }
        });
    });

    const table = document.querySelector('table');
    if (table) {
        table.addEventListener('mouseover', function(e) {
            if (e.target.tagName === 'TD') {
                e.target.parentElement.classList.add('hover-active');
            }
        });
        table.addEventListener('mouseout', function(e) {
            if (e.target.tagName === 'TD') {
                e.target.parentElement.classList.remove('hover-active');
            }
        });
    }
});