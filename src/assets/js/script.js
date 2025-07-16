// Script sederhana untuk notifikasi atau validasi nanti
console.log("Inisidentia JS Loaded");

// Misal alert konfirmasi logout
document.querySelectorAll('.confirm-logout')?.forEach(el => {
    el.addEventListener('click', function (e) {
        if (!confirm("Yakin ingin logout?")) {
            e.preventDefault();
        }
    });
});
