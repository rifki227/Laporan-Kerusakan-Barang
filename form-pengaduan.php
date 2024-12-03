<?php
include 'templates/header2.php';
require 'function.php';
if (isset($_POST['submit'])) {
    $id=$_POST["id"];
    if (insertPengaduan($_POST) > 0) {
        echo "<script>alert('Catat Nomor pengaduan ini. $id'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Data pengaduan Anda gagal terkirim.'); window.location='form-pengaduan.php';</script>";
    }
}
$query = mysqli_query($conn, "SELECT max(id) as kodeTerbesar FROM pengaduan");
$r = mysqli_fetch_array($query);
$kodeBarang = $r['kodeTerbesar'];

// mengambil angka dari kode barang terbesar, menggunakan fungsi substr
// dan diubah ke integer dengan (int)
$urutan = (int) substr($kodeBarang, 4, 4);

// bilangan yang diambil ini ditambah 1 untuk menentukan nomor urut berikutnya
$urutan++;

// membentuk kode barang baru
// perintah sprintf("%03s", $urutan); berguna untuk membuat string menjadi 3 karakter
// misalnya perintah sprintf("%03s", 15); maka akan menghasilkan '015'
// angka yang diambil tadi digabungkan dengan kode huruf yang kita inginkan, misalnya BRG 
$huruf = "NP";
$kodeBarang = $huruf . sprintf("%04s", $urutan);
?>      
<link rel="stylesheet" href="templates/styles.css">
      <b><h1 style="margin-top: 30px"; >Form Pengaduan Kerusakan Barang</h1></b>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-row p-3">
          <div class="form-group">
              <label for="id" style="color: white;">Nomor Pengaduan</label>
              <input type="text" name="id" id="id" class="form-control" value="<?= $kodeBarang; ?>" style="cursor: default;" readonly>
              <p class="text-sm"><h5><span style="color: red;">*</span><b style="color: white">Harap catat kode ini untuk melakukan pengecekan sendiri melalui kolom pencarian.</b></h5></p>
          <div>
          <div class="form-group">
              <label for="nama" style="color: white;">Nama Pelapor</label>
              <input type="text" name="nama" id="nama" class="form-control"  required>
          <div>
          <div class="form-group">
              <label for="jabatan" style="color: white;">Jenis Pelapor</label>
              <select id="jabatan" name="jabatan">
              <option value="pilih_jenis">Pilih jenis</option> 
              <option value="pegawai">Pegawai</option>
              <option value="peserta">Peserta</option>
              </select><br><br> 
          <div>
          <div class="form-group">
              <label for="nama_barang" style="color: white;">Nama Barang</label>
              <select id="nama_barang" name="nama_barang">
              <option value="pilih_barang">Pilih Barang</option>
              <option value="ac">AC</option>
              <option value="keran_air">Keran Air</option>
              <option value="lampu">Lampu</option>
              <option value="lemari">Lemari</option>
              <option value="tv">TV</option>
              </select><br><br> 
          <div>
          <div class="mb-3">
                <label for="foto_kerusakan" class="form-label" style="color: white;">Upload Foto Kerusakan</label>
                <input type="file" class="form-control" id="foto_kerusakan" name="foto_kerusakan" accept="image/*">
            </div>

            <div class="form-group">
              <label for="lokasi" style="color: white;">Lokasi</label>
              <input type="text" name="lokasi" id="lokasi" class="form-control" required>
          <div>
          <div class="form-group">
              <label for="ket" style="color: white;">Deskripsi Kerusakan</label>
              <textarea name="ket" id="ket" class="form-control" required></textarea>
          <div> 
          <button class="btn btn-outline-success mt-3 mr-3" type="submit" name="submit" style="width: 100px;"><span class="fas fa-paper-plane mr-2"></span>Kirim</button>
          <button class="btn btn-outline-danger mt-3" type="reset" name="reset" style="width: 130px;"><span class="fas fa-undo mr-2"></span>Reset Form</button>
        </div>
      </form>
      
<?php
include 'templates/footer.php';
?>
