<?php
// Kết nối đến cơ sở dữ liệu MySQL
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "qlsv_hoangthihien"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Khai báo biến tìm kiếm
$search_name = '';
$search_hometown = '';

// Kiểm tra nếu có yêu cầu tìm kiếm
if (isset($_POST['search'])) {
    $search_name = $_POST['search_name'];
    $search_hometown = $_POST['search_hometown'];
}

// Tạo câu truy vấn tìm kiếm với prepared statements
$sql = "SELECT * FROM table_student WHERE fullname LIKE ? AND hometown LIKE ?";

// Sử dụng prepared statement để tránh SQL injection
$stmt = $conn->prepare($sql);

// Thêm dấu % vào biến tìm kiếm để tìm kiếm mọi phần trong tên và quê quán
$search_name = "%" . $search_name . "%";
$search_hometown = "%" . $search_hometown . "%";

// Liên kết các tham số vào câu truy vấn
$stmt->bind_param("ss", $search_name, $search_hometown);

// Thực thi câu truy vấn
$stmt->execute();

// Lấy kết quả
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Sinh viên</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Danh sách Sinh viên</h1>

<!-- Form tìm kiếm -->
<form method="POST" action="" class="search-form">
    <input type="text" name="search_name" placeholder="Tìm theo tên" value="<?php echo htmlspecialchars($search_name); ?>">
    <input type="text" name="search_hometown" placeholder="Tìm theo quê quán" value="<?php echo htmlspecialchars($search_hometown); ?>">
    <button type="submit" name="search">Tìm kiếm</button>
    <a href="add.php" class="btn-add">Thêm sinh viên</a>
</form>

<?php
// Kiểm tra nếu có dữ liệu sinh viên
if ($result->num_rows > 0) {
    echo "<table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ và tên</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Quê quán</th>
                    <th>Trình độ học vấn</th>
                    <th>Nhóm</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>";

    $index = 1;
    // Lặp qua các sinh viên và hiển thị thông tin
    while($row = $result->fetch_assoc()) {
        // Chuyển đổi giá trị giới tính
        $gender = ($row['gender'] == 1) ? 'Nam' : 'Nữ';

        // Chuyển đổi trình độ học vấn
        switch ($row['level']) {
            case 0:
                $level = "Cử nhân";
                break;
            case 1:
                $level = "Thạc sĩ";
                break;
            case 2:
                $level = "Tiến sĩ";
                break;
            default:
                $level = "Khác";
                break;
        }

        // Hiển thị dữ liệu sinh viên
        echo "<tr>
                <td>" . $index++ . "</td>
                <td>" . htmlspecialchars($row['fullname']) . "</td>
                <td>" . htmlspecialchars($row['dob']) . "</td>
                <td>" . htmlspecialchars($gender) . "</td>
                <td>" . htmlspecialchars($row['hometown']) . "</td>
                <td>" . htmlspecialchars($level) . "</td>
                <td>" . htmlspecialchars($row['group']) . "</td>
                <td>
                    <a href='edit.php?id=" . $row['id'] . "' class='button edit'>Sửa</a>
                    <a href='delete.php?id=" . $row['id'] . "' class='button delete'>Xóa</a>
                </td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p class='no-data'>Không có sinh viên nào!</p>";
}

// Đóng kết nối
$conn->close();
?>

</body>
</html>
