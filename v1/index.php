<?php

//require '.././libs/Slim/Slim.php';
require '../vendor/autoload.php';
include '../include/DbHandler.php';
// include '../models/Messages.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");

$config = ['settings' => [
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true
]];

$app = new \Slim\App($config);

function verifyRequiredParams($required_fields) {
        $error = false;
        $error_fields = "";
        $request_params = array();
        $request_params = $_REQUEST;

        foreach ($required_fields as $field) {
            if (!isset($request_params[$field])) {
                $error = true;
                $error_fields .= $field . ', ';
            }
        }

        if ($error) {
            $result = array();
            $result = array(
                'success' => false,
                'message' => 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing'
            );
            return $result;
        }
}


$app->post('/adminLogin',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('email','password'));   
        if($result == null){
                $db = new DbHandler();
                $email = $request->getParam('email');
                $password = $request->getParam('password');
                $result = $db->adminLogin($email,$password);
        }
        return $response->withJson($result);
});

$app->get('/getAllUserList',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->getAllUserList();
        return $response->withJson($result);
});

$app->post('/addCategory',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('category_name','description','adminID','category_type','season_information','nutritional_information','storage_description','quantity'));   
        if($result == null){
                $category_name = $request->getParam('category_name');
                $description = $request->getParam('description');
                $adminID = $request->getParam('adminID');
                $category_type = $request->getParam('category_type');
                $season_information = $request->getParam('season_information');
                $nutritional_information = $request->getParam('nutritional_information');
                $storage_description = $request->getParam('storage_description');
                $quantity = $request->getParam('quantity');

                $is_photo_set = false;
                $pro_image = null;

                if (isset($_FILES['pro_image'])) {
                        $is_photo_set = true;
                        $pro_image = $_FILES['pro_image'];
                }

                $db = new DbHandler();
                $result = $db->addCategory($category_name,$description,$adminID,$category_type,$season_information,$nutritional_information,$storage_description,$quantity,$is_photo_set,$pro_image);
        }
        return $response->withJson($result);
});

$app->post('/updateCategory',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('category_id','category_name','description','adminID','category_type','season_info','nutritional_information','storage_desc','quantity'));   
        if($result == null){
                $db = new DbHandler();
                $category_id = $request->getParam('category_id');
                $category_name = $request->getParam('category_name');
                $description = $request->getParam('description');
                $adminID = $request->getParam('adminID');
                $category_type = $request->getParam('category_type');
                $season_info = $request->getParam('season_info');
                $nutritional_information = $request->getParam('nutritional_information');
                $storage_desc = $request->getParam('storage_desc');
                $quantity = $request->getParam('quantity');
                
                $is_photo_set = false;
                $pro_image = null;

                if (isset($_FILES['pro_image'])) {
                        $is_photo_set = true;
                        $pro_image = $_FILES['pro_image'];
                }
                $db = new DbHandler();
                $result = $db->updateCategory($category_id,$category_name,$description,$adminID,$category_type,$season_info,$nutritional_information,$storage_desc,$quantity,$is_photo_set,$pro_image);
        }
        return $response->withJson($result);
});

$app->post('/deleteCategory',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('category_id','adminID'));   
        if($result == null){
                $db = new DbHandler();
                $category_id = $request->getParam('category_id');
                $adminID = $request->getParam('adminID');
                $result = $db->deleteCategory($category_id,$adminID);
        }
        return $response->withJson($result);
});

$app->post('/addProduct',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('product_name','description','product_price','pro_qty','category_id','adminID','nutrition_information','origin','season','weight','storage_instrustion','best_before_date'));   
        if($result == null){
                $product_name = $request->getParam('product_name');
                $description = $request->getParam('description');
                $product_price = $request->getParam('product_price');
                $pro_qty = $request->getParam('pro_qty');
                $cate_id = $request->getParam('category_id');
                $adminID = $request->getParam('adminID');
                $nutrition_information = $request->getParam('nutrition_information');
                $origin = $request->getParam('origin');
                $season = $request->getParam('season');
                $weight = $request->getParam('weight');
                $storage_instrustion = $request->getParam('storage_instrustion');
                $best_before_date = $request->getParam('best_before_date');

                $is_photo_set = false;
                $pro_image = null;

                if (isset($_FILES['pro_image'])) {
                        $is_photo_set = true;
                        $pro_image = $_FILES['pro_image'];
                }

                $db = new DbHandler();
                $result = $db->addProduct($product_name,$description,$product_price,$pro_qty,$cate_id,$adminID,$nutrition_information,$origin,$season,$weight,$storage_instrustion,$best_before_date,$is_photo_set,$pro_image);
        }
        return $response->withJson($result);
});

$app->post('/updateProduct',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('product_id','product_name','description','product_price','pro_qty','category_id','adminID','nutrition_information','origin','season','weight','storage_instrustion','best_before_date'));   
        if($result == null){
                $product_id = $request->getParam('product_id');
                $product_name = $request->getParam('product_name');
                $description = $request->getParam('description');
                $product_price = $request->getParam('product_price');
                $pro_qty = $request->getParam('pro_qty');
                $cate_id = $request->getParam('category_id');
                $adminID = $request->getParam('adminID');
                $nutrition_information = $request->getParam('nutrition_information');
                $origin = $request->getParam('origin');
                $season = $request->getParam('season');
                $weight = $request->getParam('weight');
                $storage_instrustion = $request->getParam('storage_instrustion');
                $best_before_date = $request->getParam('best_before_date');

                $is_photo_set = false;
                $pro_image = null;

                if (isset($_FILES['pro_image'])) {
                        $is_photo_set = true;
                        $pro_image = $_FILES['pro_image'];
                }

                $db = new DbHandler();
                $result = $db->updateProduct($product_id,$product_name,$description,$product_price,$pro_qty,$cate_id,$adminID,$nutrition_information,$origin,$season,$weight,$storage_instrustion,$best_before_date,$is_photo_set,$pro_image);
        }
        return $response->withJson($result);
});

$app->post('/deleteProduct',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('product_id','adminID'));   
        if($result == null){
                $db = new DbHandler();
                $product_id = $request->getParam('product_id');
                $adminID = $request->getParam('adminID');
                $result = $db->deleteProduct($product_id,$adminID);
        }
        return $response->withJson($result);
});

$app->get('/searchProduct',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('product_name'));   
        if($result == null){
                $db = new DbHandler();
                $product_name = $request->getParam('product_name');
                $result = $db->searchProduct($product_name);
        }
        return $response->withJson($result);
});

$app->get('/searchCatogory',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('category_name'));   
        if($result == null){
                $db = new DbHandler();
                $category_name = $request->getParam('category_name');
                $result = $db->searchCatogory($category_name);
        }
        return $response->withJson($result);
});

$app->get('/searchUser',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_name'));   
        if($result == null){
                $db = new DbHandler();
                $user_name = $request->getParam('user_name');
                $result = $db->searchUser($user_name);
        }
        return $response->withJson($result);
});

$app->get('/fatchAllProduct',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fatchAllProduct();
        return $response->withJson($result);
});

$app->get('/fatchAllCategory',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fatchAllCategory();
        return $response->withJson($result);
});

$app->get('/fatchSingleProduct',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('product_id'));   
        if($result == null){
                $db = new DbHandler();
                $product_id = $request->getParam('product_id');
                $result = $db->fatchSingleProduct($product_id);
        }
        return $response->withJson($result);
});

$app->get('/fatchSingleCategory',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('category_id'));   
        if($result == null){
                $db = new DbHandler();
                $category_id = $request->getParam('category_id');
                $result = $db->fatchSingleCategory($category_id);
        }
        return $response->withJson($result);
});

$app->get('/fetchUserAddress',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $result = $db->fetchUserAddress($user_id);
        }
        return $response->withJson($result);
});

$app->post('/userSignUp',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_name','contact_num','email','password'));   
        if($result == null){
                $db = new DbHandler();
                $user_name = $request->getParam('user_name');
                $contact_num = $request->getParam('contact_num');
                $email = $request->getParam('email');
                $password = $request->getParam('password');
                $result = $db->userSignUp($user_name,$contact_num,$email,$password);
        }
        return $response->withJson($result);
});

$app->post('/userLogIn',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_name','password'));   
        if($result == null){
                $db = new DbHandler();
                $user_name = $request->getParam('user_name');
                $password = $request->getParam('password');
                $result = $db->userLogIn($user_name,$password);
        }
        return $response->withJson($result);
});

$app->post('/EditUserProfile',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id','user_name','contact_number','email','gender','DOB',));   
        if($result == null){
                $user_id = $request->getParam('user_id');
                $user_name = $request->getParam('user_name');
                $contact_number = $request->getParam('contact_number');
                $email = $request->getParam('email');
                $gender = $request->getParam('gender');
                $DOB = $request->getParam('DOB');

                $is_photo_set = false;
                $pro_image = null;

                if (isset($_FILES['pro_image'])) {
                        $is_photo_set = true;
                        $pro_image = $_FILES['pro_image'];
                }

                $db = new DbHandler();
                $result = $db->EditUserProfile($user_id,$user_name,$contact_number,$email,$gender,$DOB,$is_photo_set,$pro_image);
        }
        return $response->withJson($result);
});

$app->post('/changePassword',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id','password'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $password = $request->getParam('password');
                $result = $db->changePassword($user_id,$password);
        }
        return $response->withJson($result);
});

$app->post('/addAddress',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id','city','state','country','add1','add2','zipCode'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $city = $request->getParam('city');
                $state = $request->getParam('state');
                $country = $request->getParam('country');
                $add1 = $request->getParam('add1');
                $add2 = $request->getParam('add2');
                $zipCode = $request->getParam('zipCode');
                $result = $db->addAddress($user_id,$city,$state,$country,$add1,$add2,$zipCode);
        }
        return $response->withJson($result);
});

$app->post('/addToCart',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id','product_id'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $product_id = $request->getParam('product_id');
                $result = $db->addToCart($user_id,$product_id);
        }
        return $response->withJson($result);
});

$app->post('/getCartList',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $result = $db->getCartList($user_id);
        }
        return $response->withJson($result);
});

$app->post('/removeFromCart',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id','product_id'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $product_id = $request->getParam('product_id');
                $result = $db->removeFromCart($user_id,$product_id);
        }
        return $response->withJson($result);
});

$app->post('/addCoupon',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('coupon_code','purchase_amount','offer_price','category_id','product_id','coupon_type','coupon_status','expire_date','is_used','admin_id','min_purchase_amount','max_discount_amount','usage_count','usage_limit','user_restrictions','instructions'));   
        if($result == null){
                $db = new DbHandler();
                $coupon_code = $request->getParam('coupon_code');
                $purchase_amount = $request->getParam('purchase_amount');
                $offer_price = $request->getParam('offer_price');
                $category_id = $request->getParam('category_id');
                $product_id = $request->getParam('product_id');
                $coupon_type = $request->getParam('coupon_type');
                $coupon_status = $request->getParam('coupon_status');
                $expire_date = $request->getParam('expire_date');
                $is_used = $request->getParam('is_used');
                $admin_id = $request->getParam('admin_id');
                $min_purchase_amount = $request->getParam('min_purchase_amount');
                $max_discount_amount = $request->getParam('max_discount_amount');
                $usage_count = $request->getParam('usage_count');
                $usage_limit = $request->getParam('usage_limit');
                $user_restrictions = $request->getParam('user_restrictions');
                $instructions = $request->getParam('instructions');
                $result = $db->addCoupon($coupon_code,$purchase_amount,$offer_price,$category_id,$product_id,$coupon_type,$coupon_status,$expire_date,$is_used,$admin_id,$min_purchase_amount,$max_discount_amount,$usage_count,$usage_limit,$user_restrictions,$instructions);
        }
        return $response->withJson($result);
});

$app->post('/updateCoupon',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('coupon_id','purchase_amount','offer_price','category_id','product_id','coupon_type','coupon_status','expire_date','admin_id','min_purchase_amount','max_discount_amount','usage_count','usage_limit','user_restrictions','instructions'));   
        if($result == null){
                $db = new DbHandler();
                $coupon_id = $request->getParam('coupon_id');
                $purchase_amount = $request->getParam('purchase_amount');
                $offer_price = $request->getParam('offer_price');
                $category_id = $request->getParam('category_id');
                $product_id = $request->getParam('product_id');
                $coupon_type = $request->getParam('coupon_type');
                $coupon_status = $request->getParam('coupon_status');
                $expire_date = $request->getParam('expire_date');
                $admin_id = $request->getParam('admin_id');
                $min_purchase_amount = $request->getParam('min_purchase_amount');
                $max_discount_amount = $request->getParam('max_discount_amount');
                $usage_count = $request->getParam('usage_count');
                $usage_limit = $request->getParam('usage_limit');
                $user_restrictions = $request->getParam('user_restrictions');
                $instructions = $request->getParam('instructions');
                $result = $db->updateCoupon($coupon_id,$purchase_amount, $offer_price,$category_id,$product_id,$coupon_type,$coupon_status,$expire_date,$admin_id,$min_purchase_amount,$max_discount_amount,$usage_count,$usage_limit,$user_restrictions,$instructions);
        }
        return $response->withJson($result);
});

$app->post('/deleteCoupon',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('coupon_id'));   
        if($result == null){
                $db = new DbHandler();
                $coupon_id = $request->getParam('coupon_id');
                $result = $db->deleteCoupon($coupon_id);
        }
        return $response->withJson($result);
});

$app->post('/editAdminProfile',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('admin_id','user_name','email','contact_number','gender','DOB',));   
        if($result == null){
                $admin_id = $request->getParam('admin_id');
                $user_name = $request->getParam('user_name');
                $email = $request->getParam('email');
                $contact_number = $request->getParam('contact_number');
                $gender = $request->getParam('gender');
                $DOB = $request->getParam('DOB');

                $is_photo_set = false;
                $pro_image = null;

                if (isset($_FILES['pro_image'])) {
                        $is_photo_set = true;
                        $pro_image = $_FILES['pro_image'];
                }

                $db = new DbHandler();
                $result = $db->editAdminProfile($admin_id,$user_name,$email,$contact_number,$gender,$DOB,$is_photo_set,$pro_image);
        }
        return $response->withJson($result);
});

$app->post('/adminChangePassword',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('admin_id','password'));   
        if($result == null){
                $db = new DbHandler();
                $admin_id = $request->getParam('admin_id');
                $password = $request->getParam('password');
                $result = $db->adminChangePassword($admin_id,$password);
        }
        return $response->withJson($result);
});

$app->post('/giveFeedback',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('rating','user_id','product_id','feedback'));   
        if($result == null){
                $db = new DbHandler();
                $rating = $request->getParam('rating');
                $user_id = $request->getParam('user_id');
                $product_id = $request->getParam('product_id');
                $feedback = $request->getParam('feedback');
                $result = $db->giveFeedback($rating, $user_id, $product_id,$feedback);
        }
        return $response->withJson($result);
});

$app->post('/addTofavourite',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('product_id','user_id'));   
        if($result == null){
                $db = new DbHandler();
                $product_id = $request->getParam('product_id');
                $user_id = $request->getParam('user_id');
                $result = $db->addTofavourite($product_id,$user_id);
        }
        return $response->withJson($result);
});

$app->post('/getFavList',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $result = $db->getFavList($user_id);
        }
        return $response->withJson($result);
});

$app->post('/updateAddress',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id','city','state','country','add1','add2','zipCode'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $city = $request->getParam('city');
                $state = $request->getParam('state');
                $country = $request->getParam('country');
                $add1 = $request->getParam('add1');
                $add2 = $request->getParam('add2');
                $zipCode = $request->getParam('zipCode');
                $result = $db->updateAddress($user_id,$city,$state,$country,$add1,$add2,$zipCode);
        }
        return $response->withJson($result);
});

$app->post('/removeFromFavourite',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id','product_id'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $product_id = $request->getParam('product_id');
                $result = $db->removeFromFavourite($user_id,$product_id);
        }
        return $response->withJson($result);
});

$app->post('/placeOrder',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_id','pro_id','pro_name','pro_price','pro_qty','order_status','shipping_address','payment_method','total_cost','discount_amount','shipping_cost','billing_address','delivery_date_time','pickup_date','pickup_time','gift_order_id','gift_message','payment_status','payment_date_time','refund_status','refund_amount','return_status'));   
        if($result == null){
                $db = new DbHandler();
                $user_id = $request->getParam('user_id');
                $pro_id = $request->getParam('pro_id');
                $pro_name = $request->getParam('pro_name');
                $pro_price = $request->getParam('pro_price');
                $pro_qty = $request->getParam('pro_qty');
                $order_status = $request->getParam('order_status');
                $shipping_address = $request->getParam('shipping_address');
                $payment_method = $request->getParam('payment_method');
                $total_cost = $request->getParam('total_cost');
                $discount_amount = $request->getParam('discount_amount');
                $shipping_cost = $request->getParam('shipping_cost');
                $billing_address = $request->getParam('billing_address');
                $delivery_date_time = $request->getParam('delivery_date_time');
                $pickup_date = $request->getParam('pickup_date');
                $pickup_time = $request->getParam('pickup_time');
                $gift_order_id = $request->getParam('gift_order_id');
                $gift_message = $request->getParam('gift_message');
                $payment_status = $request->getParam('payment_status');
                $payment_date_time = $request->getParam('payment_date_time');
                $refund_status = $request->getParam('refund_status');
                $refund_amount = $request->getParam('refund_amount');
                $return_status = $request->getParam('return_status');

                $result = $db->placeOrder($user_id, $pro_id,$pro_name,$pro_price,$pro_qty,$order_status,$shipping_address,$payment_method,$total_cost,$discount_amount,$shipping_cost,$billing_address,$delivery_date_time,$pickup_date,$pickup_time,$gift_order_id,$gift_message,$payment_status,$payment_date_time,$refund_status,$refund_amount,$return_status);
        }
        return $response->withJson($result);
});

$app->get('/fatchAllCoupon',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fatchAllCoupon();
        return $response->withJson($result);
});

$app->post('/fetchSingleCoupon',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('coupon_id'));   
        if($result == null){
                $db = new DbHandler();
                $coupon_id = $request->getParam('coupon_id');
                $result = $db->fetchSingleCoupon($coupon_id);
        }
        return $response->withJson($result);
});

$app->get('/fatchAllOrders',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fatchAllOrders();
        return $response->withJson($result);
});

$app->get('/fetchAllFeedback',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fetchAllFeedback();
        return $response->withJson($result);
});

$app->post('/blockFeedback',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('feedback_id'));   
        if($result == null){
                $db = new DbHandler();
                $feedback_id = $request->getParam('feedback_id');
                $result = $db->blockFeedback($feedback_id);
        }
        return $response->withJson($result);
});

$app->get('/fetchBlockedFeedback',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fetchBlockedFeedback();
        return $response->withJson($result);
});

$app->post('/unblockFeedback',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('feedback_id'));   
        if($result == null){
                $db = new DbHandler();
                $feedback_id = $request->getParam('feedback_id');
                $result = $db->unblockFeedback($feedback_id);
        }
        return $response->withJson($result);
});

$app->post('/addSubadmin',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('user_name','email','password','contact_num','gender','DOB'));   
        if($result == null){
                $user_name = $request->getParam('user_name');
                $email = $request->getParam('email');
                $password = $request->getParam('password');
                $contact_num = $request->getParam('contact_num');
                $gender = $request->getParam('gender');
                $DOB = $request->getParam('DOB');

                $is_photo_set = false;
                $pro_image = null;

                if (isset($_FILES['pro_image'])) {
                        $is_photo_set = true;
                        $pro_image = $_FILES['pro_image'];
                }

                $db = new DbHandler();
                $result = $db->addSubadmin($user_name,$email,$password,$contact_num,$gender,$DOB,$is_photo_set,$pro_image);
        }
        return $response->withJson($result);
});

$app->post('/FetchCategoryWiseProduct',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('category_id'));   
        if($result == null){
                $db = new DbHandler();
                $category_id = $request->getParam('category_id');
                $result = $db->FetchCategoryWiseProduct($category_id);
        }
        return $response->withJson($result);
});

$app->post('/notification',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('notificaton_content','notification_type'));   
        if($result == null){
                $db = new DbHandler();
                $notificaton_content = $request->getParam('notificaton_content');
                $notification_type = $request->getParam('notification_type');
                $result = $db->notification($notificaton_content,$notification_type);
        }
        return $response->withJson($result);
});

$app->post('/verifySubAdmin',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('notificaton_id','subAdmin_id'));   
        if($result == null){
                $db = new DbHandler();
                $notificaton_id = $request->getParam('notificaton_id');
                $subAdmin_id = $request->getParam('subAdmin_id');
                $result = $db->verifySubAdmin($notificaton_id,$subAdmin_id);
        }
        return $response->withJson($result);
});

$app->get('/fetchAdminDetails',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fetchAdminDetails();
        return $response->withJson($result);
});

$app->get('/fetchUnseenNotification',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fetchUnseenNotification();
        return $response->withJson($result);
});

$app->get('/fetchSubadmin',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->fetchSubadmin();
        return $response->withJson($result);
});

$app->get('/getNotificationCount',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->getNotificationCount();
        return $response->withJson($result);
});

$app->get('/updateIsRead',function($request, $response, $args) use ($app) {      
        $db = new DbHandler();
        $result = $db->updateIsRead();
        return $response->withJson($result);
});

$app->post('/assignRoles',function($request, $response, $args) use ($app) {   
        $result = verifyRequiredParams(array('subAdmin_id','pro_add','pro_up','pro_del','cat_add','cat_upp','cat_del','cou_add','cou_upp','cou_del','block_feed','unblock_feed'));   
        if($result == null){
                $db = new DbHandler();
                $subAdmin_id = $request->getParam('subAdmin_id');
                $pro_add = $request->getParam('pro_add');
                $pro_up = $request->getParam('pro_up');
                $pro_del = $request->getParam('pro_del');
                $cat_add = $request->getParam('cat_add');
                $cat_upp = $request->getParam('cat_upp');
                $cat_del = $request->getParam('cat_del');
                $cou_add = $request->getParam('cou_add');
                $cou_upp = $request->getParam('cou_upp');
                $cou_del = $request->getParam('cou_del');
                $block_feed = $request->getParam('block_feed');
                $unblock_feed = $request->getParam('unblock_feed');
                $result = $db->assignRoles($subAdmin_id,$pro_add,$pro_up,$pro_del,$cat_add,$cat_upp,$cat_del,$cou_add,$cou_upp,$cou_del,$block_feed,$unblock_feed);
        }
        return $response->withJson($result);
});
$app->run();
?>