<?php

class DbHandler
{
    private $conn;
    private $image_path = '../uploads/';

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }


    // ================   ADMIN    ==============================
    public function adminLogin($admin_email, $admin_password)
    {

        $sql_query = "CALL admin_login(?,?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ss', $admin_email, $admin_password);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Login successful",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Invalid email or password . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function editAdminProfile($admin_id, $user_name, $email, $contact_num, $gender, $DOB, $is_photo_set, $pro_image)
    {

        $sql_query = "CALL editAdminProfile(?,?,?,?,?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('isssss', $admin_id, $user_name, $email, $contact_num, $gender, $DOB);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();
        $result = array();

        if ($is_done == 1) {
            if ($is_photo_set) {
                if (!file_exists($this->image_path)) {
                    mkdir($this->image_path, 0777, true);
                }
                $extension = pathinfo($pro_image['name'], PATHINFO_EXTENSION);
                $user_name_trimmed = trim($user_name);
                $filename = time() . '_' . str_replace(' ', '', $user_name_trimmed) . '_img.' . $extension;
                $file = $this->image_path . $filename;
                if (move_uploaded_file($pro_image['tmp_name'], $file)) {

                    $stmt2 = $this->conn->query("UPDATE tbl_admin SET profile_image = '$filename' WHERE admin_id = $admin_id");
                    $result = array(
                        'success' => true,
                        'Message' => "Profile Updated successfully",
                        'Status' => "Success"
                    );
                } else {
                    $result = array(
                        'success' => true,
                        'Message' => 'Profile Updated successfully . but images are not uploaded due to some issues',
                        'Status' => "Success"
                    );
                }
            } else {
                $result = array(
                    'success' => false,
                    'Message' => "Failed to Update Profile . Image is Missing",
                    'Status' => "Error"
                );
            }
            return $result;
        }
    }

    public function adminChangePassword($admin_id, $password)
    {
        if ($password != "") {
            $password = md5($password);
        }

        $sql_query = "CALL adminChangePassword(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('is', $admin_id, $password);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Password updated successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Password change failed. Please make sure your new password meets the minimum requirements and try again",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function addSubadmin($user_name, $email, $password, $contact_num, $gender, $DOB, $pro_image = null)
    {
        $sql_query = "CALL addSubadmin(?,?,?,?,?,?,@is_done,@last_user_id)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('sssiss', $user_name, $email, $password, $contact_num, $gender, $DOB);
        $stmt->execute();
        $stmt->close();
        $stmt1 = $this->conn->prepare("SELECT @last_user_id AS last_user_id,@is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_user_id);
        $stmt1->fetch();
        $stmt1->close();
        $result = array();

        if ($is_done == 1) {
            if ($pro_image != null) {
                if (!file_exists($this->image_path)) {
                    mkdir($this->image_path, 0777, true);
                }
                $extension = pathinfo($pro_image['name'], PATHINFO_EXTENSION);
                $user_name_trimmed = trim($user_name);
                $filename = time() . '_' . str_replace(' ', '', $user_name_trimmed) . '_img.' . $extension;
                $file = $this->image_path . $filename;
                if (move_uploaded_file($pro_image['tmp_name'], $file)) {
                    $stmt2 = $this->conn->query("UPDATE tbl_admin SET profile_image = '$filename' WHERE admin_id = $last_user_id");
                    $result = array(
                        'success' => true,
                        'Message' => "Profile Updated successfully",
                        'sub_admin_id' => $last_user_id,
                        'Status' => "Success"
                    );
                } else {
                    $result = array(
                        'success' => true,
                        'Message' => 'Profile Updated successfully . but images are not uploaded due to some issues',
                        'sub_admin_id' => $last_user_id,
                        'Status' => "Success"
                    );
                }
            } else {
                $result = array(
                    'success' => true,
                    'Message' => 'Profile added successfully',
                    'sub_admin_id' => $last_user_id,
                    'Status' => "Success"
                );
            }
        } else {
            $result = array(
                'success' => false,
                'Message' => 'Failed to add Profile',
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function assignRoles($admin_id,$pro_add,$pro_up,$pro_del,$cat_add,$cat_upp,$cat_del,$cou_add,$cou_upp,$cou_del,$block_feed,$unblock_feed)
    {
        $sql_query = "CALL assignRoles(?,?,?,?,?,?,?,?,?,?,?,?,@is_done,@last_user_id)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('iiiiiiiiiiii',$admin_id,$pro_add,$pro_up,$pro_del,$cat_add,$cat_upp,$cat_del,$cou_add,$cou_upp,$cou_del,$block_feed,$unblock_feed);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done,@last_user_id AS last_user_id");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_user_id);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Role added successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to add role . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function blockFeedback($feedback_id)
    {
        $sql_query = "CALL blockFeedback(?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $feedback_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Feedback Blocked successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Block feedback . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function unblockFeedback($feedback_id)
    {
        $sql_query = "CALL unblockFeedback(?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $feedback_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Feedback Unblocked successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Unblock feedback . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function notification($content, $type)
    {
        $sql_query = "CALL notification(?,?,@is_done,@last_inserted)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ss', $content, $type);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done,@last_inserted AS last_inserted");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_inserted);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Notification send successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to send Notification . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchUnseenNotification()
    {
        $sql_query = "CALL fetchUnseenNotification()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Notification fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Notification. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }
    public function getNotificationCount()
    {
        $sql_query = "CALL getNotificationCount()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Notification fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Notification. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function updateIsRead()
    {

        $sql_query = "CALL updateIsRead(@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        // $stmt->bind_param();
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Notification Readed successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Read Notification . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function verifySubAdmin($notification_id, $sub_admin_id)
    {

        $sql_query = "CALL verifySubAdmin(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ii', $notification_id, $sub_admin_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "User verified ",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to verified User . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }
    
    public function verifyOrder($order_id, $delivery_date)
    {

        $sql_query = "CALL verifyOrder(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('is', $order_id, $delivery_date);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Order verified Successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to verified Order . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }
    
    public function subadminLogin($admin_id)
    {
        $sql_query = "CALL subadminLogin(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $admin_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "You have been verified by the admin",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "ooops admin verification is pending. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function blockOrderByAdmin($order_id)
    {

        $sql_query = "CALL blockOrderByAdmin(?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Order Blocked successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to block order . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }
    
    //  ================   Fatch/Search API's    ==============================
    public function getAllUserList()
    {

        $sql_query = "CALL getAllUserList()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "users fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch users. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function searchProduct($product_name)
    {
        $sql_query = "CALL searchProduct(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('s', $product_name);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Products retrieved successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "No products found with the given criteria. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function searchCatogory($category_name)
    {
        $sql_query = "CALL searchCatogory(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('s', $category_name);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Category retrieved successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "No Category found with the given criteria. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function searchUser($user_name)
    {
        $sql_query = "CALL searchUser(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('s', $user_name);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Users retrieved successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "No User found with the given criteria.Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fatchAllProduct()
    {
        $sql_query = "CALL fatchAllProduct()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Product fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Product. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fatchAllCategory()
    {
        $sql_query = "CALL fatchAllCategory()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Category fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Category. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchSubadmin()
    {
        $sql_query = "CALL fetchSubadmin()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Sub admin fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch sub admin details. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fatchSingleProduct($product_id)
    {
        $sql_query = "CALL fatchSingleProduct(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('s', $product_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Product retrieved successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "No Product found with the given criteria. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fatchSingleCategory($category_id)
    {
        $sql_query = "CALL fatchSingleCategory(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('s', $category_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Category retrieved successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "No Category found with the given criteria. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fatchAllCoupon()
    {
        $sql_query = "CALL fatchAllCoupon()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Coupon fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Coupon. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchSingleCoupon($coupon_id)
    {
        $sql_query = "CALL fetchSingleCoupon(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $coupon_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Coupon fetched successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Coupon. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchAllOrders()
    {
        $sql_query = "CALL fetchAllOrders()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Order fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch order. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchPendingOrder()
    {
        $sql_query = "CALL fetchPendingOrder()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Order fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch order. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchUserAddress($user_id)
    {
        $sql_query = "CALL fetchUserAddress(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Address fetched successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Address. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchAllFeedback()
    {
        $sql_query = "CALL fetchAllFeedback()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Feedback fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Feedback. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchBlockedFeedback()
    {
        $sql_query = "CALL fetchBlockedFeedback()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Feedback fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Feedback. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function FetchCategoryWiseProduct($category_id)
    {
        $sql_query = "CALL FetchCategoryWiseProduct(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $category_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Product retrieved successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "No Product found with the given criteria. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchAdminDetails()
    {
        $sql_query = "CALL fetchAdminDetails()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Admin Details fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch Admin Details. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fetchAllBlockOrder()
    {
        $sql_query = "CALL fetchAllBlockOrder()";
        $stmt = $this->conn->query($sql_query);
        $this->conn->next_result();
        $response = array();
        while ($row = $stmt->fetch_assoc()) {
            $response[] = $row;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Block order Details fetched successfully",
                'Status' => "Success",
                'Response' => $response
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch order Details. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function fatchOrder($order_id)
    {
        $sql_query = "CALL fatchOrder(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Order fetched successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch order. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }
    public function fetchSubadminRole($order_id)
    {
        $sql_query = "CALL fetchSubadminRole(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "subadmin Role fetched successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to fetch subadmin Role. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }
    

    // ================   CATEGORY    ==============================

    public function addCategory($cat_name, $cat_desc, $cat_creator_id, $cat_type, $is_photo_set, $pro_image)
    {
        $sql_query = "CALL addCategory(?,?,?,?,@is_done,@last_cat_id)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ssis', $cat_name, $cat_desc, $cat_creator_id, $cat_type);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @last_cat_id AS last_cat_id,@is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_cat_id);
        $stmt1->fetch();
        $stmt1->close();
        $result = array();
        if ($is_done == 1) {
            if ($is_photo_set) {
                if (!file_exists($this->image_path)) {
                    mkdir($this->image_path, 0777, true);
                }
                $extension = pathinfo($pro_image['name'], PATHINFO_EXTENSION);
                $cat_name_trimmed = trim($cat_name);
                $filename = time() . '_' . str_replace(' ', '', $cat_name_trimmed) . '_img.' . $extension;
                $file = $this->image_path . $filename;
                if (move_uploaded_file($pro_image['tmp_name'], $file)) {

                    $stmt2 = $this->conn->query("UPDATE tbl_category SET image = '$filename' WHERE cat_id = $last_cat_id");
                    $result = array(
                        'success' => true,
                        'Message' => "category added successfully",
                        'Status' => "Success"
                    );
                } else {
                    $result = array(
                        'success' => true,
                        'Message' => 'category added successfully . but images are not uploaded due to some issues',
                        'Status' => "Success"
                    );
                }
            } else {
                $result = array(
                    'success' => false,
                    'Message' => "Failed to add category . Internal server error",
                    'Status' => "Error"
                );
            }
            return $result;
        }
    }

    public function updateCategory($cat_id, $cat_name, $cat_desc, $cat_creator_id, $cat_type, $is_photo_set, $pro_image)
    {

        $sql_query = "CALL updateCategory(?,?,?,?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('issis', $cat_id, $cat_name, $cat_desc, $cat_creator_id, $cat_type);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        $result = array();
        if ($is_done == 1) {
            if ($is_photo_set) {
                if (!file_exists($this->image_path)) {
                    mkdir($this->image_path, 0777, true);
                }
                $extension = pathinfo($pro_image['name'], PATHINFO_EXTENSION);
                $cat_name_trimmed = trim($cat_name);
                $filename = time() . '_' . str_replace(' ', '', $cat_name_trimmed) . '_img.' . $extension;
                $file = $this->image_path . $filename;
                if (move_uploaded_file($pro_image['tmp_name'], $file)) {

                    $stmt2 = $this->conn->query("UPDATE tbl_category SET image = '$filename' WHERE cat_id = $cat_id");
                    $result = array(
                        'success' => true,
                        'Message' => "Category updated successfully",
                        'Status' => "Success"
                    );
                } else {
                    $result = array(
                        'success' => true,
                        'Message' => 'Category updated successfully . but images are not uploaded due to some issues',
                        'Status' => "Success"
                    );
                }
            } else {
                $result = array(
                    'success' => false,
                    'Message' => "Failed to update Category . Image is Missing",
                    'Status' => "Error"
                );
            }
            return $result;
        }
    }

    public function deleteCategory($c_cat_id, $c_admin_id)
    {
        $sql_query = "CALL deleteCategory(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ii', $c_cat_id, $c_admin_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Category Deleted Successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Delete category . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    // ================   PRODUCT    ==============================
    public function addProduct($pro_name, $pro_desc, $pro_price, $pro_qty, $pro_cate_id, $pro_creator_id, $nut_info, $origin_info, $season_info, $weight_info, $storage_info, $bb_date, $is_photo_set, $pro_image)
    {

        $sql_query = "CALL addProduct(?,?,?,?,?,?,?,?,?,?,?,?,@is_done,@last_pro_id)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ssiiiisssiss', $pro_name, $pro_desc, $pro_price, $pro_qty, $pro_cate_id, $pro_creator_id, $nut_info, $origin_info, $season_info, $weight_info, $storage_info, $bb_date);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @last_pro_id AS last_pro_id,@is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_pro_id);
        $stmt1->fetch();
        $stmt1->close();
        $result = array();

        if ($is_done == 1) {
            if ($is_photo_set) {
                if (!file_exists($this->image_path)) {
                    mkdir($this->image_path, 0777, true);
                }
                $extension = pathinfo($pro_image['name'], PATHINFO_EXTENSION);
                $pro_name_trimmed = trim($pro_name);
                $filename = time() . '_' . str_replace(' ', '', $pro_name_trimmed) . '_img.' . $extension;
                $file = $this->image_path . $filename;
                if (move_uploaded_file($pro_image['tmp_name'], $file)) {

                    $stmt2 = $this->conn->query("UPDATE tbl_product SET pro_image = '$filename' WHERE pro_id = $last_pro_id");
                    $result = array(
                        'success' => true,
                        'Message' => "product added successfully",
                        'Status' => "Success"
                    );
                } else {
                    $result = array(
                        'success' => true,
                        'Message' => 'product added successfully . but images are not uploaded due to some issues',
                        'Status' => "Success"
                    );
                }
            } else {
                $result = array(
                    'success' => false,
                    'Message' => "Failed to add product . Internal server error",
                    'Status' => "Error"
                );
            }
            return $result;
        }
    }

    public function updateProduct($prod_id, $product_name, $product_desc, $product_price, $product_qty, $pro_cate_id, $admin_id, $nut_info, $origin_info, $season_info, $weight_info, $storage_info, $bb_date, $is_photo_set, $pro_image)
    {

        $sql_query = "CALL updateProduct(?,?,?,?,?,?,?,?,?,?,?,?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('issiiiisssiss', $prod_id, $product_name, $product_desc, $product_price, $product_qty, $pro_cate_id, $admin_id, $nut_info, $origin_info, $season_info, $weight_info, $storage_info, $bb_date);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        $result = array();

        if ($is_done == 1) {
            if ($is_photo_set) {
                if (!file_exists($this->image_path)) {
                    mkdir($this->image_path, 0777, true);
                }
                $extension = pathinfo($pro_image['name'], PATHINFO_EXTENSION);
                $product_name_trimmed = trim($product_name);
                $filename = time() . '_' . str_replace(' ', '', $product_name_trimmed) . '_img.' . $extension;
                $file = $this->image_path . $filename;
                if (move_uploaded_file($pro_image['tmp_name'], $file)) {

                    $stmt2 = $this->conn->query("UPDATE tbl_product SET pro_image = '$filename' WHERE pro_id = $prod_id");
                    $result = array(
                        'success' => true,
                        'Message' => "product updated successfully",
                        'Status' => "Success"
                    );
                } else {
                    $result = array(
                        'success' => true,
                        'Message' => 'product updated successfully . but images are not uploaded due to some issues',
                        'Status' => "Success"
                    );
                }
            } else {
                $result = array(
                    'success' => false,
                    'Message' => "Failed to update product . Image is Missing",
                    'Status' => "Error"
                );
            }
            return $result;
        }
    }

    public function deleteProduct($p_prod_id, $p_admin_id)
    {
        $sql_query = "CALL deleteProduct(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ii', $p_prod_id, $p_admin_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Product Deleted Successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Delete Product . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    // ================  Coupon    ==============================

    public function addCoupon($coupon_code, $purchase_amount, $offer_price, $coupon_status, $expire_date, $is_used, $admin_id, $min_purchase_amount, $max_discount_amount, $user_restrictions, $instructions)
    {
        $sql_query = "CALL addCoupon(?,?,?,?,?,?,?,?,?,?,?,@is_done,@last_coupon_code)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('siiisiiiiss', $coupon_code, $purchase_amount, $offer_price, $coupon_status, $expire_date, $is_used, $admin_id, $min_purchase_amount, $max_discount_amount, $user_restrictions, $instructions);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done,@last_coupon_code AS last_coupon_code");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_coupon_code);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Coupon added successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to add Coupon . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function updateCoupon($coupon_id, $purchase_amount, $offer_price, $coupon_status, $expire_date, $admin_id, $min_purchase_amount, $max_discount_amount, $user_restrictions, $instructions)
    {

        $sql_query = "CALL updateCoupon(?,?,?,?,?,?,?,?,?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('iiiisiiiss', $coupon_id, $purchase_amount, $offer_price,$coupon_status, $expire_date, $admin_id, $min_purchase_amount, $max_discount_amount, $user_restrictions, $instructions);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Coupon Updated successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Update Coupon . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function deleteCoupon($coupon_id)
    {
        $sql_query = "CALL deleteCoupon(?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $coupon_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Coupon Deleted Successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Delete Coupon . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function applyCoupon($coupon_id)
    {
        $sql_query = "CALL applyCoupon(?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $coupon_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Coupon Apply successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Apply Coupon . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }


    // ================      USER         ==============================

    public function userSignUp($user_name, $contact_num, $email, $password)
    {
        if ($password != "") {
            $password = md5($password);
        }

        $sql_query = "CALL userSignUp(?,?,?,?,@is_done,@last_user_id)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ssss', $user_name, $contact_num, $email, $password);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done,@last_user_id AS last_user_id");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_user_id);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Sign Up successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Sign Up . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function userLogIn($user_name, $user_password)
    {
        if ($user_password != "") {
            $user_password = md5($user_password);
        }
        $sql_query = "CALL userLogIn(?,?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ss', $user_name, $user_password);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Login successful",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Invalid email or password . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function EditUserProfile($user_id, $user_name, $contact_num, $email, $gender, $DOB, $is_photo_set, $pro_image)
    {

        $sql_query = "CALL EditUserProfile(?,?,?,?,?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('isssss', $user_id, $user_name, $contact_num, $email, $gender, $DOB);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();
        $result = array();

        if ($is_done == 1) {
            if ($is_photo_set) {
                if (!file_exists($this->image_path)) {
                    mkdir($this->image_path, 0777, true);
                }
                $extension = pathinfo($pro_image['name'], PATHINFO_EXTENSION);
                $user_name_trimmed = trim($user_name);
                $filename = time() . '_' . str_replace(' ', '', $user_name_trimmed) . '_img.' . $extension;
                $file = $this->image_path . $filename;
                if (move_uploaded_file($pro_image['tmp_name'], $file)) {

                    $stmt2 = $this->conn->query("UPDATE tbl_user SET profile_image = '$filename' WHERE user_id = $user_id");
                    $result = array(
                        'success' => true,
                        'Message' => "Profile Updated successfully",
                        'Status' => "Success"
                    );
                } else {
                    $result = array(
                        'success' => true,
                        'Message' => 'Profile Updated successfully . but images are not uploaded due to some issues',
                        'Status' => "Success"
                    );
                }
            } else {
                $result = array(
                    'success' => false,
                    'Message' => "Failed to Update Profile . Image is Missing",
                    'Status' => "Error"
                );
            }
            return $result;
        }
    }

    public function changePassword($user_id, $password)
    {
        if ($password != "") {
            $password = md5($password);
        }

        $sql_query = "CALL changePassword(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('is', $user_id, $password);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Password updated successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Password change failed. Please make sure your new password meets the minimum requirements and try again",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function addAddress($user_id, $city, $state, $country, $add1, $add2, $zipCode)
    {
        $sql_query = "CALL addAddress(?,?,?,?,?,?,?,@is_done,@last_user_id)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('isssssi', $user_id, $city, $state, $country, $add1, $add2, $zipCode);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done,@last_user_id AS last_user_id");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_user_id);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Address added successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to add address . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function updateAddress($user_id, $city, $state, $country, $add1, $add2, $zipCode)
    {

        $sql_query = "CALL updateAddress(?,?,?,?,?,?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('isssssi', $user_id, $city, $state, $country, $add1, $add2, $zipCode);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Address Updated successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to update Address . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function addToCart($user_id, $product_id)
    {
        $sql_query = "CALL addToCart(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ii', $user_id, $product_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Product successfully added to cart",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "unable to add product to cart. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function getCartList($user_id)
    {
        $sql_query = "CALL getCartList(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Cart retrieved successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Cart is empty",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function removeFromCart($user_id, $product_id)
    {
        $sql_query = "CALL removeFromCart(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ii', $user_id, $product_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Product successfully removed from cart",
                'Status' => "Success",
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "unable to remove product from cart. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function giveFeedback($rating, $user_id, $prod_id, $feedback)
    {
        $sql_query = "CALL giveFeedback(?,?,?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('iiis', $rating, $user_id, $prod_id, $feedback);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Feedback added successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to add Feedback . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function addTofavourite($prod_id, $user_id)
    {
        $sql_query = "CALL addTofavourite(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ii', $prod_id, $user_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Product successfully added to favourite",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to add Product . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function getFavList($user_id)
    {
        $sql_query = "CALL getFavList(?)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $response = array();

        while ($record = $res->fetch_assoc()) {
            $response = $record;
        }

        $stmt->close();

        if (count($response) > 0) {
            $result = array(
                'success' => true,
                'Message' => "Favourit retrieved successfully",
                'Status' => "Success",
                'Response' => $response,
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Favourit list is empty",
                'Status' => "Error"
            );
        }
        return $result;
    }
    public function removeFromFavourite($user_id, $product_id)
    {
        $sql_query = "CALL removeFromFavourite(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('ii', $user_id, $product_id);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Product successfully removed from Favourite",
                'Status' => "Success",
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "unable to remove product from Favourite. Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function placeOrder($user_id, $pro_id, $pro_price, $pro_qty,$shipping_address, $payment_method, $total_cost, $discount_amount, $shipping_cost )
    {
        $sql_query = "CALL placeOrder(?,?,?,?,?,?,?,?,?,@is_done,@last_order_id)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('iiiissiii', $user_id, $pro_id, $pro_price, $pro_qty, $shipping_address, $payment_method, $total_cost, $discount_amount, $shipping_cost);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done,@last_order_id AS last_order_id");
        $stmt1->execute();
        $stmt1->bind_result($is_done, $last_order_id);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Order Placed successfully ...",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to Place Order . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }

    public function updateOrderStatus($order_id, $order_status)
    {

        $sql_query = "CALL updateOrderStatus(?,?,@is_done)";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bind_param('is', $order_id, $order_status);
        $stmt->execute();
        $stmt->close();

        $stmt1 = $this->conn->prepare("SELECT @is_done AS is_done");
        $stmt1->execute();
        $stmt1->bind_result($is_done);
        $stmt1->fetch();
        $stmt1->close();

        if ($is_done) {
            $result = array(
                'success' => true,
                'Message' => "Order status updated successfully",
                'Status' => "Success"
            );
        } else {
            $result = array(
                'success' => false,
                'Message' => "Failed to update Order status . Internal server error",
                'Status' => "Error"
            );
        }
        return $result;
    }
}
