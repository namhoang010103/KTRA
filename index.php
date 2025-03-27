<?php
session_start();
include 'connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$is_admin = ($_SESSION['role'] === 'admin');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhân Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .employee-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-3">
            <h2>Thông Tin Nhân Viên</h2>
            <div>
                <span>Xin chào, <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)</span>
                <a href="logout.php" class="btn btn-danger btn-sm">Đăng Xuất</a>
            </div>
        </div>

        <?php if ($is_admin) { ?>
            <a href="add.php" class="btn btn-primary mb-3">Thêm Nhân Viên</a>
        <?php } ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã Nhân Viên</th>
                    <th>Hình Ảnh</th>
                    <th>Tên Nhân Viên</th>
                    <th>Địa Chỉ</th>
                    <th>Phòng Ban</th>
                    <th>Lương</th>
                    <?php if ($is_admin) { ?>
                        <th>Hành Động</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Số lượng nhân viên mỗi trang
                $limit = 5;

                // Lấy trang hiện tại từ query string, mặc định là trang 1
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $start = ($page - 1) * $limit;

                // Đếm tổng số nhân viên
                $sql_count = "SELECT COUNT(*) AS total FROM NHANVIEN";
                $result_count = $conn->query($sql_count);
                $row_count = $result_count->fetch_assoc();
                $total_records = $row_count['total'];
                $total_pages = ceil($total_records / $limit);

                // Lấy dữ liệu cho trang hiện tại
                $sql = "SELECT Ma_Nhan_Vien, Nguyen_Thi_Hai, Ha_Noi, Tai_Chinh, Luong, Hinh_Anh, Gioi_Tinh 
                        FROM NHANVIEN 
                        LIMIT $start, $limit";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Xác định hình ảnh hiển thị
                        $image_path = $row["Hinh_Anh"];
                        if (empty($image_path) || $image_path === 'images/default.jpg') {
                            // Nếu không có hình ảnh, hiển thị hình mặc định dựa trên giới tính
                            $image_path = ($row["Gioi_Tinh"] === 'Nam') ? 'images/man.jpg' : 'images/woman.jpg';
                        }

                        echo "<tr>";
                        echo "<td>" . $row["Ma_Nhan_Vien"] . "</td>";
                        echo "<td><img src='" . $image_path . "' class='employee-img' alt='Hình ảnh nhân viên'></td>";
                        echo "<td>" . $row["Nguyen_Thi_Hai"] . "</td>";
                        echo "<td>" . $row["Ha_Noi"] . "</td>";
                        echo "<td>" . $row["Tai_Chinh"] . "</td>";
                        echo "<td>" . $row["Luong"] . "</td>";
                        if ($is_admin) {
                            echo "<td>
                                    <a href='edit.php?id=" . $row["Ma_Nhan_Vien"] . "' class='btn btn-warning btn-sm'>Sửa</a>
                                    <a href='delete.php?id=" . $row["Ma_Nhan_Vien"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Bạn có chắc muốn xóa?\")'>Xóa</a>
                                  </td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='" . ($is_admin ? 7 : 6) . "' class='text-center'>Không có dữ liệu</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <?php if ($total_records > $limit) { ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- Nút Previous -->
                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if ($page > 1) echo 'index.php?page=' . ($page - 1); else echo '#'; ?>">Previous</a>
                    </li>

                    <!-- Các trang -->
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>

                    <!-- Nút Next -->
                    <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if ($page < $total_pages) echo 'index.php?page=' . ($page + 1); else echo '#'; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>