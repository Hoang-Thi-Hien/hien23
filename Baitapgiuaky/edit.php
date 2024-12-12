<?php
// Kết nối đến cơ sở dữ liệu MySQL
$conn = new mysqli('localhost', 'root', '', 'qlsv_hoangthihien');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy ID sinh viên từ URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ép kiểu số nguyên để tránh lỗi SQL Injection

    // Lấy thông tin sinh viên
    $sql = "SELECT * FROM table_student WHERE id = $id"; // Dùng query trực tiếp
    $result = $conn->query($sql);

    // Kiểm tra nếu sinh viên tồn tại
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "<script>alert('Không tìm thấy sinh viên với ID này.'); window.location.href = 'index.php';</script>";
        exit();
    }
} else {
    die("Không có ID sinh viên được cung cấp.");
}

// Kiểm tra khi người dùng nhấn nút "Cập nhật"
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Lấy thông tin từ form
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $dob = htmlspecialchars(trim($_POST['dob']));
    $gender = intval($_POST['gender']);
    $hometown = htmlspecialchars(trim($_POST['hometown']));
    $level = intval($_POST['level']);
    $group = htmlspecialchars(trim($_POST['group'])); // Đổi group_id thành group

    // Kiểm tra dữ liệu nhập vào
    if (empty($fullname) || empty($dob) || empty($hometown) || empty($group)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin.');</script>";
    } else {
        // Sử dụng query để cập nhật dữ liệu
        $sql = "UPDATE table_student SET fullname = '$fullname', dob = '$dob', gender = $gender, hometown = '$hometown', level = $level, `group` = $group WHERE id = $id";

        // Thực thi câu lệnh
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Cập nhật thông tin sinh viên thành công!'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            echo "Lỗi cập nhật: " . $conn->error;
        }
    }
}

$conn->close(); // Đóng kết nối cơ sở dữ liệu
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sinh viên</title>
    <link rel="stylesheet" href="style3.css"> <!-- Liên kết đến file CSS -->
</head>
<body>

    <h1>Sửa thông tin sinh viên</h1>
    
    <form  action="edit.php?id=<?= $id ?>" method="POST">
        <!-- Trường nhập Họ và tên -->
        <label for="fullname">Họ và tên:</label>
        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($student['fullname']) ?>" required>

        <!-- Trường nhập Ngày sinh -->
        <label for="dob">Ngày sinh:</label>
        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($student['dob']) ?>" required>

        <!-- Trường chọn Giới tính -->
        <label>Giới tính:</label><br>
        <input type="radio" id="male" name="gender" value="1" <?= $student['gender'] == 1 ? 'checked' : '' ?>> Nam
        <input type="radio" id="female" name="gender" value="0" <?= $student['gender'] == 0 ? 'checked' : '' ?>> Nữ

        <!-- Trường nhập Quê quán -->
        <label for="hometown">Quê quán:</label>
        <input type="text" id="hometown" name="hometown" value="<?= htmlspecialchars($student['hometown']) ?>" required>

        <!-- Trường chọn Trình độ học vấn -->
        <label for="level">Trình độ học vấn:</label>
        <select id="level" name="level" required>
            <option value="0" <?= $student['level'] == 0 ? 'selected' : '' ?>>Cử nhân</option>
            <option value="1" <?= $student['level'] == 1 ? 'selected' : '' ?>>Thạc sĩ</option>
            <option value="2" <?= $student['level'] == 2 ? 'selected' : '' ?>>Tiến sĩ</option>
            <option value="3" <?= $student['level'] == 3 ? 'selected' : '' ?>>Khác</option>
        </select>

        <!-- Trường nhập Nhóm -->
        <label for="group">Nhóm:</label>
        <input type="number" id="group" name="group" value="<?= htmlspecialchars($student['group']) ?>" required>

        <!-- Nút gửi form -->
        <button type="submit">Cập nhật</button>
    </form>

</body>
</html>
