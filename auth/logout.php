<?php
session_start();
session_destroy();
echo "<script>alert('Berhasil logout dari PUSBANGPEG ASN!'); window.location='../index.php';</script>";