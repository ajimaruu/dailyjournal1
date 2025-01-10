<div class="container">
    <!-- Button trigger modal -->
<button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-plus-lg"></i> Tambah Gallery
</button>
    <div class="row">
        <div class="table-responsive" id="gallery_data">
            
        <script>
$(document).ready(function(){
    load_data();
    function load_data(hlm){
        $.ajax({
            url : "gallery_data.php",
            method : "POST",
            data : {
					            hlm: hlm
				           },
            success : function(data){
                    $('#gallery_data').html(data);
            }
        })
    } 
    $(document).on('click', '.halaman', function(){
    var hlm = $(this).attr("id");
    load_data(hlm);
});
});
</script>

        </div>
    </div>
</div>
<!-- Awal Modal Tambah-->
<div class="modal fade" id="modalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Gallery</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" value="simpan" name="simpan" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Akhir Modal Tambah-->
 
<?php
include "upload_foto.php";

if (isset($_POST['simpan'])) {
    $tanggal = date("Y-m-d H:i:s");
    $username = $_SESSION['username'];
    $gambar = '';
    $nama_gambar = $_FILES['gambar']['name'];

    // Jika ada file gambar baru
    if ($nama_gambar != '') {
        // Panggil fungsi upload_foto untuk memvalidasi file baru
        $cek_upload = upload_foto($_FILES["gambar"]);

        if ($cek_upload['status']) {
            $gambar = $cek_upload['message']; // Nama file gambar baru
        } else {
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                document.location='admin.php?page=gallery';
            </script>";
            die;
        }
    }

    // Cek apakah form menyertakan ID untuk update
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        if ($nama_gambar == '') {
            // Jika tidak ada gambar baru, gunakan gambar lama
            $gambar = $_POST['gambar_lama'];
        } else {
            // Jika ada gambar baru, hapus gambar lama
            if (!empty($_POST['gambar_lama'])) {
                unlink("img/" . $_POST['gambar_lama']);
            }
        }

        // Update data dengan ID
        $stmt = $conn->prepare("UPDATE gallery
                                SET 
                                gambar = ?,
                                tanggal = ?,
                                username = ?
                                WHERE id = ?");
        $stmt->bind_param("sssi", $gambar, $tanggal, $username, $id);
        $simpan = $stmt->execute();
    } else {
        // Insert data baru jika ID tidak ada
        $stmt = $conn->prepare("INSERT INTO gallery (gambar,tanggal,username)
                                VALUES (?,?,?)");
        $stmt->bind_param("sss", $gambar, $tanggal, $username);
        $simpan = $stmt->execute();
    }

    // Feedback hasil simpan atau update
    if ($simpan) {
        echo "<script>
            alert('Data berhasil disimpan');
            document.location='admin.php?page=gallery';
        </script>";
    } else {
        echo "<script>
            alert('Data gagal disimpan');
            document.location='admin.php?page=gallery';
        </script>";
    }

    $stmt->close();
    $conn->close();
}

//jika tombol hapus diklik
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    $gambar = $_POST['gambar'];

    if ($gambar != '') {
        //hapus file gambar
        unlink("img/" . $gambar);
    }

    $stmt = $conn->prepare("DELETE FROM gallery WHERE id =?");

    $stmt->bind_param("i", $id);
    $hapus = $stmt->execute();

    if ($hapus) {
        echo "<script>
            alert('Hapus data sukses');
            document.location='admin.php?page=gallery';
        </script>";
    } else {
        echo "<script>
            alert('Hapus data gagal');
            document.location='admin.php?page=gallery';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>