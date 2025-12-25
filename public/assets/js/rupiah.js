function formatRupiah(angka) {
    var parts = angka.toString().split('.');   // Pisahkan bagian desimal jika ada
    var reverse = parts[0].split('').reverse().join('');
    var ribuan = reverse.match(/\d{1,3}/g);
    var result = ribuan.join('.').split('').reverse().join('');
    result = 'Rp ' + result;
    
    // Jika ada bagian desimal, tambahkan setelah koma
    if (parts[1]) {
        result += ',' + parts[1];
    }
    
    return result;
}