/* Kontrol popup generik.
   - Tombol pembuka : data-popup-open="idPopup"
   - Tombol penutup : data-popup-close
   - Klik area gelap atau tekan Esc juga menutup popup. */
(function () {
    function openPopup(el) {
        el.classList.add('active');
        document.body.classList.add('popup-open');
    }

    function closePopup(el) {
        el.classList.remove('active');
        if (!document.querySelector('.popup-overlay.active')) {
            document.body.classList.remove('popup-open');
        }
    }

    window.StellaPopup = { open: openPopup, close: closePopup };

    document.addEventListener('click', function (ev) {
        var opener = ev.target.closest('[data-popup-open]');
        if (opener) {
            var target = document.getElementById(opener.getAttribute('data-popup-open'));
            if (target) { openPopup(target); }
            return;
        }

        if (ev.target.closest('[data-popup-close]')) {
            var overlay = ev.target.closest('.popup-overlay');
            if (overlay) { closePopup(overlay); }
            return;
        }

        if (ev.target.classList && ev.target.classList.contains('popup-overlay')) {
            closePopup(ev.target);
        }
    });

    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape') {
            document.querySelectorAll('.popup-overlay.active').forEach(function (el) {
                closePopup(el);
            });
        }
    });
})();
