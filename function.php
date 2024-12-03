<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "pusbangpegasn";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function insertPengaduan($data) {
    global $conn;
    date_default_timezone_set('Asia/Jakarta');

    $id = $data['id'];
    $np = htmlspecialchars($data["nama"]);
    $jp = htmlspecialchars($data["jabatan"]);
    $nb = htmlspecialchars($data["nama_barang"]);
    $lokasi = htmlspecialchars($data["lokasi"]);
    $ket = mysqli_real_escape_string($conn, $data["ket"]);
    $status = "Sedang diajukan";
    $ket_petugas = "-";
    $tgl_lapor = date("Y-m-d");

    if (isset($_FILES['foto_kerusakan']) && $_FILES['foto_kerusakan']['error'] === UPLOAD_ERR_OK) {
        $rand = rand();
        $ekstensi = array('png', 'jpg', 'jpeg');
        $filename = $_FILES['foto_kerusakan']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $ekstensi)) {
            $foto = $rand . '_' . $filename;
            $upload_path = 'upload_foto/' . $foto;
            if (!move_uploaded_file($_FILES['foto_kerusakan']['tmp_name'], $upload_path)) {
                die("Gagal memindahkan file foto.");
            }
        } else {
            die("Ekstensi file tidak diperbolehkan.");
        }
    } else {
        die("Tidak ada foto yang diunggah atau terjadi kesalahan upload: " . $_FILES['foto_kerusakan']['error']);
    }

    $stmt = $conn->prepare("INSERT INTO pengaduan (id, n_pelapor, j_pelapor, n_barang, foto_kerusakan, lokasi, ket, status, ket_petugas, tgl_lapor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param('ssssssssss', $id, $np, $jp, $nb, $foto, $lokasi, $ket, $status, $ket_petugas, $tgl_lapor);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    return $stmt->affected_rows;
}

function registrasi($data) {
    global $conn;

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $name = htmlspecialchars($data["name"]);
    $nip = htmlspecialchars($data["nip"]);
    $img = "default.jpg";
    $status = "0";

    $cek = mysqli_query($conn, "SELECT username, user_id FROM user WHERE username = '$username' OR user_id = '$nip'");

    if (mysqli_fetch_assoc($cek)) {
        echo "<script>alert('Username $username atau NIP $nip sudah terdaftar!');</script>";
        return false;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO user VALUES('$nip', '$username', '$password', '$name', '$img', '$status')");

    return mysqli_affected_rows($conn);
}

function updatePass($data) {
    global $conn;
    
    $id = $data['id'];
    $password_baru = mysqli_real_escape_string($conn, $data["password_baru"]);
    $password_baru = password_hash($password_baru, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE user SET password='$password_baru' WHERE user_id='$id'"); 

    return mysqli_affected_rows($conn);
}

function hapusFoto($filename, $upload_dir) {
    $file_path = $upload_dir . '/' . $filename;
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            return true;
        } else {
            die("Gagal menghapus file lama.");
        }
    } else {
        return true; // Jika file tidak ditemukan, anggap sukses
    }
}

function updatePengaduanDanFoto($data) {
    global $conn;
    $id = $data['id'];
    $status = $data['status'];
    $ket_petugas = $data['ket_petugas'];
    $foto = '';
    $upload_dir = '../upload_foto';
    $tgl_perbaikan = $data['tgl_perbaikan']; 

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $rand = rand();
        $ekstensi = array('png', 'jpg', 'jpeg');
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $ekstensi)) {
            $foto = $rand . '_' . $filename;
            $upload_path = $upload_dir . '/' . $foto;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                // File berhasil diunggah, lanjutkan dengan update database
                $stmt = $conn->prepare("UPDATE pengaduan SET status = ?, ket_petugas = ?, foto_perbaikan = ?, tgl_perbaikan = ? WHERE id = ?");
                if ($stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param('sssss', $status, $ket_petugas, $foto, $tgl_perbaikan, $id);
            } else {
                die("Gagal memindahkan file foto.");
            }
        } else {
            die("Ekstensi file tidak diperbolehkan. Hanya file png, jpg, dan jpeg yang diizinkan.");
        }
    } else {
        // Jika tidak ada file yang diunggah, tetap lakukan update status dan keterangan
        $stmt = $conn->prepare("UPDATE pengaduan SET status = ?, ket_petugas = ?, tgl_perbaikan = ? WHERE id = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('ssss', $status, $ket_petugas, $tgl_perbaikan, $id);
    }

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}



function deleteUser($id) {
    global $conn;
    mysqli_query($conn, "DELETE FROM user WHERE user_id = '$id'");
    return mysqli_affected_rows($conn);
}

function deletePengaduan($id) {
    global $conn;
    mysqli_query($conn, "DELETE FROM pengaduan WHERE id = '$id'");
    return mysqli_affected_rows($conn);
}

function searchPengaduan($keyword) {
    global $conn;
    $data = mysqli_query($conn, "SELECT * FROM pengaduan WHERE id = '$keyword'");
    return mysqli_affected_rows($conn);
}
?>
