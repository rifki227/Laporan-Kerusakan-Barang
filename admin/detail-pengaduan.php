<?php
include "templates/header.php";
include "templates/sidebar-pengaduan.php";

// Periksa apakah form disubmit
if (isset($_POST['submit'])) {
    // Pastikan fungsi updatePengaduanDanFoto ada
    if (function_exists('updatePengaduanDanFoto')) {
        if (updatePengaduanDanFoto($_POST) > 0) {
            echo "<script>alert('Update data successfully!'); window.location='data-pengaduan.php';</script>";
        } else {
            echo "<script>alert('Data update failed or you did not make any changes!'); window.location='data-pengaduan.php';</script>";
        }
    } else {
        echo "<script>alert('Update function not found!'); window.location='data-pengaduan.php';</script>";
    }
}

// Ambil ID dari URL
$id = $_GET['id'];

// Pastikan fungsi query ada dan berfungsi dengan baik
if (function_exists('query')) {
    $data = query("SELECT * FROM pengaduan WHERE id = '$id'");
} else {
    echo "<script>alert('Query function not found!'); window.location='data-pengaduan.php';</script>";
    exit;
}

// Tampilkan data pengaduan
foreach ($data as $d) :
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Detail Pengaduan <?= htmlspecialchars($d['id']); ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Report</a></li>
              <li class="breadcrumb-item active">Bulanan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-body">
          <form action="" method="POST" enctype="multipart/form-data">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-2"></div>    
              </div>
              
              <div class="row">
                <div class="col-md-2">
                  <label for="id">Nomor Pengaduan :</label>
                  <input type="text" name="id" id="id" class="form-control mb-3 bg-transparent" style="cursor: default;" value="<?= htmlspecialchars($d['id']); ?>" readonly>
                </div>
                <div class="col-md-2">
                  <label for="tgl">Tanggal Pengaduan:</label>
                  <input type="text" name="tgl" id="tgl" class="form-control mb-3 bg-transparent" style="cursor: default;" value="<?= htmlspecialchars($d['tgl_lapor']); ?>" readonly>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <label for="np">Nama Pelapor :</label>
                  <input type="text" name="np" id="np" class="form-control mb-3 bg-transparent" style="cursor: default;" value="<?= htmlspecialchars($d['n_pelapor']); ?>" readonly>
                </div>
                <div class="col-md-4">
                  <label for="jp">Jenis Pelapor :</label>
                  <input type="text" name="jp" id="jp" class="form-control mb-3 bg-transparent" style="cursor: default;" value="<?= htmlspecialchars($d['j_pelapor']); ?>" readonly>
                </div>
            </div>
              </div>
              <div class="row">
                    <div class="col-md-4">
                        <label for="foto_kerusakan">Foto Kerusakan :</label>
                        <?php
                        $fotoKerusakanPath = "../upload_foto/" . htmlspecialchars($d['foto_kerusakan']);
                        if (file_exists($fotoKerusakanPath)) {
                            echo "<img src='$fotoKerusakanPath' class='img-fluid' alt='Foto Kerusakan' />";
                        } else {
                            echo "<p>Foto tidak tersedia.</p>";
                        }
                        ?>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="foto_perbaikan">Foto Perbaikan :</label>
                        <?php
                        $fotoPerbaikanPath = "../upload_foto/" . htmlspecialchars($d['foto_perbaikan']);
                        if (file_exists($fotoPerbaikanPath)) {
                            echo "<img src='$fotoPerbaikanPath' class='img-fluid' alt='Foto Perbaikan' />";
                        } else {
                            echo "<p>Foto tidak tersedia.</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                  <label for="nb">Nama Barang :</label>
                  <input type="text" name="nb" id="nb" class="form-control mb-3 bg-transparent" style="cursor: default;" value="<?= htmlspecialchars($d['n_barang']); ?>" readonly>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label for="foto" class="form-label">Upload Foto perbaikan</label>
                  <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                </div>
                <div class="col-md-4">
                  <label for="lokasi">Lokasi :</label>
                  <input type="text" name="lokasi" id="lokasi" class="form-control mb-3 bg-transparent" style="cursor: default;" value="<?= htmlspecialchars($d['lokasi']); ?>" readonly>
                </div>
                <div class="col-md-4">
                  <label for="ket">Keterangan :</label>
                  <textarea name="ket" id="ket" class="form-control mb-3 bg-transparent" style="cursor: default;" readonly><?= htmlspecialchars($d['ket']); ?></textarea>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3" style="border: 1px solid #ced4da; border-radius: 5px; margin: 7px 7px; padding: 7px 10px;">
                  <p><b>Status :</b></p>
                  <?php
                  $statuses = ['Sedang diajukan', 'Sedang diproses', 'Selesai diproses'];
                  foreach ($statuses as $status) {
                      $checked = ($d['status'] == $status) ? 'checked' : '';
                      echo "<div class='custom-control custom-radio custom-control-inline'>
                              <input type='radio' value='$status' id='opt_$status' name='status' class='custom-control-input' $checked>
                              <label class='custom-control-label' for='opt_$status'>$status</label>
                            </div>";
                  }
                  ?>
                </div>
              </div>
              <div class="form-group">
                <label for="tgl_perbaikan" class="form-label">Tanggal Perbaikan:</label>
                <input type="date" name="tgl_perbaikan" id="tgl_perbaikan" class="form-control mb-3 bg-transparent" style="cursor: default;" value="<?= htmlspecialchars($d['tgl_perbaikan']); ?>">
                </div>
                <div class="row">
                <div class="col-md-8 mt-2">
                  <label for="ket_petugas">Catatan dari petugas :</label>
                  <textarea name="ket_petugas" id="ket_petugas" class="form-control mb-2"><?= htmlspecialchars($d['ket_petugas']); ?></textarea>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 mt-2">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                  <button type="submit" value="submit" name="submit" class="btn btn-outline-success mr-2" style="width: 100px;">
                    <span class="fas fa-check mr-2"></span>
                    Save
                  </button>
                  <button type="reset" value="reset" class="btn btn-outline-danger mr-2" style="width: 100px;">
                    <span class="fas fa-times mr-2"></span>
                    Reset
                  </button>
                  <a href="#" class="btn btn-outline-primary" onclick="history.back()" style="width: 100px;">
                    <span class="fas fa-arrow-left mr-2"></span>
                    Back
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
        <!-- /.card-body -->
        <?php endforeach; ?>
      </div>
      <!-- /.card -->
    </section>
    <!-- /.content -->
  </div>
<?php
include "templates/footer.php";
?>
