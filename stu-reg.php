<?php
if (isset($_POST['stu-reg-submit'])) {
    require "conn.php";

    $truename = $_POST['truename'];
    $sex = $_POST['sex'];
    $paperno = $_POST['paper_no'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $school = $_POST['school'];
    $id = null;

    $sex_array = ['male', 'female'];

    if (empty($truename) || empty($sex) || empty($paperno) || empty($tel) || empty($email) || empty($school)) {
        header("location:stu-reg.html?error=sthempty");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location:stu-reg.html?error=invalidemail");
        exit();
    } else if (!in_array($sex, $sex_array)) {
        header("location:stu-reg.html?error=wrongsex");
        exit();
    } else {
        $sql_insert = "INSERT INTO sys.stu_info (stu_no,stu_name,sex,paper_no,tel,email,school) VALUES (?,?,?,?,?,?,?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql_insert)) {
            header("location:stu-reg.html?error=sqlerror");
            exit();
        } else {
            $get_id = "SELECT stu_no FROM sys.stu_no_reg WHERE status=1 ORDER BY  RAND() LIMIT 1;";


            $result1 = mysqli_query($conn,$get_id);
            if ($result1) {
                $row1 = mysqli_fetch_assoc($result1);
                $id = $row1['stu_no'];
//                if (is_object($id)) {
//                    echo "id是数字或数字组成的字符串型";
//                }
//                else {
//                    echo "id是其他类型";
//                }
                echo $id;
                $status_sql = "UPDATE sys.stu_no_reg SET status = 0 WHERE stu_no = $id;";
                $update_status = mysqli_query($conn,$status_sql);
                if (!$update_status) {
                    echo "修改status失败";
                }else {
                    echo ("将要开始判断是否更新了status");
                    if ($update_status) {
                        echo "成功修改status";
                        mysqli_stmt_bind_param($stmt,"sssssss",$id,$truename,$sex,$paperno,$tel,$email,$school);
                        mysqli_stmt_execute($stmt);
                        echo ("成功了");
                        mysqli_stmt_close($stmt);
                    }else {
                        header("location:stu-reg.html?failtoupdate");
                        exit();
                    }
                }
            }
            else {
                header("location:stu-reg.html?error=getiderror");
                exit();
            }
        }
    }
    mysqli_close($conn);
}
else {
    header("location:stu-reg.html?nopost");
    exit();
}
