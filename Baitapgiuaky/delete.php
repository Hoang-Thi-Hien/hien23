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

// Kiểm tra nếu có yêu cầu xóa sinh viên
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Sử dụng prepared statement để xóa sinh viên
    $stmt = $conn->prepare("DELETE FROM table_student WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        echo "<script>alert('Sinh viên đã được xóa thành công!');</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }

    $stmt->close(); // Đóng statement
}
// Truy vấn lấy danh sách sinh viên
$sql = "SELECT * FROM table_student";
$result = $conn->query($sql);

?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Sinh viên</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Danh sách Sinh viên</h1>

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
                <td>" . $row['fullname'] . "</td>
                <td>" . $row['dob'] . "</td>
                <td>" . $gender . "</td>
                <td>" . $row['hometown'] . "</td>
                <td>" . $level . "</td>
                <td>" . $row['group'] . "</td>
                <td>
                    <a href='edit.php?id=" . $row['id'] . "' class='button edit'>Sửa</a>
                    <a href='?delete_id=" . $row['id'] . "' class='button delete' onclick='return confirm(\"Bạn có chắc muốn xóa sinh viên này không?\")'>Xóa</a>
                </td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "Không có sinh viên nào!";
}

// Đóng kết nối
$conn->close();
?>

</body>
</html>