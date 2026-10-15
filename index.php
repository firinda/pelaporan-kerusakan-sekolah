
<?php
include "koneksi.php";

$pesan = "";

if (isset($_POST['submit'])) {
    $nama_pelapor = trim($_POST['nama_pelapor'] ?? '');
    $nama_fasilitas = trim($_POST['nama_fasilitas'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $id_kategori = (int) ($_POST['id_kategori'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $prioritas = $_POST['prioritas'] ?? '';

    if (
        $nama_pelapor === '' ||
        $nama_fasilitas === '' ||
        $lokasi === '' ||
        $deskripsi === '' ||
        $id_kategori < 1 ||
        !in_array($prioritas, ['Rendah', 'Sedang', 'Tinggi'], true)
    ) {
        $pesan = "Semua data wajib diisi dengan benar.";
    } else {
        // Periksa kategori
        $cek = $conn->prepare(
            "SELECT id_kategori
             FROM kategori_fasilitas
             WHERE id_kategori = ?"
        );

        $cek->execute([$id_kategori]);

        if (!$cek->fetch()) {
            $pesan = "Kategori fasilitas tidak valid.";
        } else {
            // Simpan laporan menggunakan PDO
            $sql = "INSERT INTO laporan_kerusakan
                    (nama_pelapor, nama_fasilitas, lokasi,
                     id_kategori, deskripsi, prioritas)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                $nama_pelapor,
                $nama_fasilitas,
                $lokasi,
                $id_kategori,
                $deskripsi,
                $prioritas
            ]);

            header("Location: index.php?berhasil=1#daftar-laporan");
            exit;
        }
    }
}
?>

<?php
$sql = "SELECT
            l.id_laporan,
            l.nama_fasilitas,
            l.lokasi,
            l.nama_pelapor,
            l.prioritas,
            l.status,
            l.tanggal_laporan,
            k.nama_kategori
        FROM laporan_kerusakan l
        INNER JOIN kategori_fasilitas k
            ON l.id_kategori = k.id_kategori
        ORDER BY l.tanggal_laporan DESC";

$stmt = $conn->query($sql);
$laporan = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelaporan Kerusakan Fasilitas Sekolah</title>

    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>
    <!-- Bagian ini merupakan starter HTML. Belum ada koneksi database ataupun query. -->
    <header>
        <h1>Sistem Pelaporan Kerusakan Fasilitas Sekolah</h1>
        <p>Catat dan pantau laporan kerusakan fasilitas sekolah.</p>
        <nav aria-label="Navigasi utama">
            <a href="#form-laporan">Buat Laporan</a>
            <a href="#daftar-laporan">Daftar Laporan</a>
        </nav>
    </header>
    
    <?php if (isset($_GET['berhasil'])): ?>
        <p class="pesan">
            Laporan berhasil disimpan!
        </p>
    <?php endif; ?>
    
    <?php if ($pesan !== ""): ?>
        <p class="pesan pesan-error">
            <?= htmlspecialchars($pesan) ?>
        </p>
    <?php endif; ?>

    <main>
        <section id="form-laporan" aria-labelledby="judul-form">
            <h2 id="judul-form">Form Pelaporan Kerusakan</h2>
            <p>Lengkapi data berikut untuk melaporkan fasilitas yang rusak.</p>

            <!-- TANTANGAN BONUS CREATE: Buat proses PHP agar POST menyimpan data. -->
            <form action="" method="post">
                <fieldset>
                    <legend>Data Pelaporan</legend>

                    <p>
                        <label for="nama_pelapor">Nama pelapor</label>
                        <input type="text" id="nama_pelapor" name="nama_pelapor" maxlength="100" required>
                    </p>

                    <p>
                        <label for="nama_fasilitas">Nama fasilitas</label>
                        <input type="text" id="nama_fasilitas" name="nama_fasilitas" maxlength="100" required>
                    </p>

                    <p>
                        <label for="lokasi">Lokasi kerusakan</label>
                        <input type="text" id="lokasi" name="lokasi" maxlength="100" required>
                    </p>

                    <p>
                        <label for="id_kategori">Kategori fasilitas</label>
                        <select id="id_kategori" name="id_kategori" required>
                            <option value="">-- Pilih Kategori --</option>

                            <?php
                            $kategori = $conn->query(
                                "SELECT id_kategori, nama_kategori
                                FROM kategori_fasilitas
                                ORDER BY id_kategori"
                            );
                            
                            while ($kat = $kategori->fetch()):
                            ?>
                                
                                <option value="<?= $kat['id_kategori'] ?>">
                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                    </p>

                    <p>
                        <label for="deskripsi">Deskripsi kerusakan</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea>
                    </p>

                    <p>
                        <label for="prioritas">Prioritas</label>
                        <select id="prioritas" name="prioritas" required>
                            <option value="">-- Pilih prioritas --</option>
                            <option value="Rendah">Rendah</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Tinggi">Tinggi</option>
                        </select>
                    </p>

                    <p><button type="submit" name="submit">Kirim Laporan</button></p>
                </fieldset>
            </form>
            <p><small>Catatan: tombol Kirim Laporan belum menyimpan apa pun. Fitur Create merupakan tantangan bonus.</small></p>
        </section>

        <section id="daftar-laporan" aria-labelledby="judul-daftar">
            <h2 id="judul-daftar">Daftar Laporan Kerusakan</h2>
            <p>Seluruh laporan yang tercatat pada database ditampilkan di bagian ini.</p>

            <!-- TANTANGAN WAJIB:
                 1. Buat sendiri koneksi PHP ke MySQL.
                 2. Jalankan query SELECT untuk mengambil data dari laporan_kerusakan.
                 3. Ganti baris placeholder pada tbody dengan perulangan hasil query.
                 4. Jangan mengetik data database secara manual di HTML.
            -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Fasilitas</th>
                        <th scope="col">Lokasi</th>
                        <th scope="col">Pelapor</th>
                        <th scope="col">Prioritas</th>
                        <th scope="col">Status</th>
                        <th scope="col">Tanggal</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php if (count($laporan) > 0): ?>
                        <?php $no = 1; ?>
                        
                        <?php foreach ($laporan as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    
                                    <?= htmlspecialchars($row['nama_fasilitas']) ?>
                                    <br>
                                    <small>
                                        <?= htmlspecialchars($row['nama_kategori']) ?>
                                    </small>
                                </td>
                                <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                <td><?= htmlspecialchars($row['nama_pelapor']) ?></td>
                                <td><?= htmlspecialchars($row['prioritas']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_laporan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Belum ada laporan kerusakan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Pelaporan Kerusakan Fasilitas Sekolah.</p>
    </footer>
</body>
</html>