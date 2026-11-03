(function () {
  // menu mobile
  var toggle = document.getElementById('nav-toggle');
  if (toggle) {
    toggle.addEventListener('click', function () { document.body.classList.toggle('nav-open'); });
    document.addEventListener('click', function (e) {
      if (document.body.classList.contains('nav-open') && !e.target.closest('.sidebar') && !e.target.closest('#nav-toggle')) {
        document.body.classList.remove('nav-open');
      }
    });
  }

  // konfirmasi hapus
  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!window.confirm(f.getAttribute('data-confirm'))) e.preventDefault();
    });
  });

  // petunjuk hari & minggu pada input tanggal
  var HARI = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  var BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  var ROMAWI = ['', 'I', 'II', 'III', 'IV', 'V', 'VI'];
  var DAY = 86400000;

  function mondayOf(d) {
    var dow = (d.getUTCDay() + 6) % 7; // Senin = 0
    return new Date(d.getTime() - dow * DAY);
  }
  function weekOfMonth(d) {
    var first = new Date(Date.UTC(d.getUTCFullYear(), d.getUTCMonth(), 1));
    while (first.getUTCDay() === 0 || first.getUTCDay() === 6) first = new Date(first.getTime() + DAY);
    return Math.round((mondayOf(d) - mondayOf(first)) / (7 * DAY)) + 1;
  }

  document.querySelectorAll('input[data-day-hint]').forEach(function (input) {
    var hint = document.getElementById(input.getAttribute('data-day-hint'));
    if (!hint) return;
    function update() {
      hint.className = 'hint';
      if (!input.value) { hint.textContent = ''; return; }
      var p = input.value.split('-').map(Number);
      var d = new Date(Date.UTC(p[0], p[1] - 1, p[2]));
      var dow = d.getUTCDay();
      if (dow === 0 || dow === 6) {
        hint.className = 'hint bad';
        hint.textContent = HARI[dow] + ' — akhir pekan tidak dihitung. Pilih Senin–Jumat.';
        return;
      }
      hint.textContent = HARI[dow] + ', minggu ke-' + ROMAWI[weekOfMonth(d)] + ' bulan ' + BULAN[p[1] - 1] + ' ' + p[0];
    }
    input.addEventListener('input', update);
    input.addEventListener('change', update);
    update();
  });
})();
