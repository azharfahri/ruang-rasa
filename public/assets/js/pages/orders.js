document.addEventListener('DOMContentLoaded', function () {
    const accordionParent = document.getElementById('productAccordion');

    if (accordionParent) {
        const collapseElements = accordionParent.querySelectorAll('.custom-accordion-item');

        // Tangkap event saat sebuah collapse mulai dibuka
        collapseElements.forEach(el => {
            el.addEventListener('show.bs.collapse', function () {
                // Cari semua collapse di dalam accordion ini dan tutup
                collapseElements.forEach(otherEl => {
                    if (otherEl !== el) {
                        // Gunakan API Bootstrap untuk menutup
                        const bsCollapse = bootstrap.Collapse.getInstance(otherEl);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        } else {
                            // Fallback jika instance belum ada (paksa lewat class)
                            otherEl.classList.remove('show');
                        }
                    }
                });
            });
        });
    }
});
