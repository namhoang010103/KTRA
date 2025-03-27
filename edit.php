<?php
session_start();
include 'connect.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM NHANVIEN WHERE Ma_Nhan_Vien='$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ma_nv = $_POST['ma_nv'];
    $ten_nv = $_POST['ten_nv'];
    $dia_chi = $_POST['dia_chi'];
    $phong_ban = $_POST['phong_ban'];
    $luong = $_POST['luong'];
    $gioi_tinh = $_POST['gioi_tinh'];

    $hinh_anh = $row['Hinh_Anh'];
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
        $target_dir = "images/";
        $hinh_anh = $target_dir . basename($_FILES["hinh_anh"]["name"]);
        move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $hinh_anh);
    }

    $sql = "UPDATE NHANVIEN 
            SET Ma_Nhan_Vien='$ma_nv', Nguyen_Thi_Hai='$ten_nv', Ha_Noi='$dia_chi', 
                Tai_Chinh='$phong_ban', Luong=$luong, Hinh_Anh='$hinh_anh', Gioi_Tinh='$gioi_tinh' 
            WHERE Ma_Nhan_Vien='$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Nhân Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Sửa Nhân Viên</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="ma_nv" class="form-label">Mã Nhân Viên</label>
                <input type="text" class="form-control" id="ma_nv" name="ma_nv" value="<?php echo $row['Ma_Nhan_Vien']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="ten_nv" class="form-label">Tên Nhân Viên</label>
                <input type="text" class="form-control" id="ten_nv" name="ten_nv" value="<?php echo $row['Nguyen_Thi_Hai']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="dia_chi" class="form-label">Địa Chỉ</label>
                <input type="text" class="form-control" id="dia_chi" name="dia_chi" value="<?php echo $row['Ha_Noi']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="phong_ban" class="form-label">Phòng Ban</label>
                <select class="form-select" id="phong_ban" name="phong_ban" required>
                    <option value="Quản Trị" <?php if($row['Tai_Chinh'] == 'Quản Trị') echo 'selected'; ?>>Quản Trị</option>
                    <option value="Tài Chính" <?php if($row['Tai_Chinh'] == 'Tài Chính') echo 'selected'; ?>>Tài Chính</option>
                    <option value="Kỹ Thuật" <?php if($row['Tai_Chinh'] == 'Kỹ Thuật') echo 'selected'; ?>>Kỹ Thuật</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="luong" class="form-label">Lương</label>
                <input type="number" class="form-control" id="luong" name="luong" value="<?php echo $row['Luong']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="gioi_tinh" class="form-label">Giới Tính</label>
                <select class="form-select" id="gioi_tinh" name="gioi_tinh" required>
                    <option value="Nam" <?php if($row['Gioi_Tinh'] == 'Nam') echo 'selected'; ?>>Nam</option>
                    <option value="Nữ" <?php if($row['Gioi_Tinh'] == 'Nữ') echo 'selected'; ?>>Nữ</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập Nhật</button>
            <a href="index.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</body>
</html>