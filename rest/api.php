<?php
    /* File : api.php
     * Author : Amitkumar Dudhat
    */
require_once("Rest.inc.php");
class API extends REST{
    
    public $data = "";
    
    const DB_SERVER = "localhost";
    const DB_USER = "root";
    const DB_PASSWORD = "root";
    const DB = "restdemo";
    
    private $db = NULL;
    
    public function __construct(){
        parent::__construct(); // Init parent contructor
        $this->dbConnect(); // Initiate Database connection
    }
    
    /*
     *  Database connection 
     */
    private function dbConnect(){
        $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD,self::DB);
    }
    
    
    /*
     * Public method for access api.
     * This method dynmically call the method based on the query string
     *
     */
    public function processApi(){
        @$func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));
        if ((int) method_exists($this, $func) > 0)
            $this->$func();
        else
            $this->responseData(0,404,"function not found","");// If the method not exist with in this class, response would be "Page not found".
    }
    
    /*
     *    Encode array into JSON
     */
    private function json($data){
        if (is_array($data)) {
            return json_encode($data);
        }
    }
    
    private function connectDB() {
        $conn = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD,self::DB);
        return $conn;
    }

    private function executeQuery($query) {
        $conn = $this->connectDB();    
        $result = mysqli_query($conn, $query);
        if (!$result) {
            //check for duplicate entry
            if($conn->errno == 1062) {
                return false;
            } else {
                trigger_error (mysqli_error($conn),E_USER_NOTICE);
            }
        }        
        $affectedRows = mysqli_affected_rows($conn);
        $res['affectedRows'] = $affectedRows;
        $res['lastID'] = $conn->insert_id;
        return $res;
    }
    
    private function executeSelectQuery($query) {
        $conn = $this->connectDB();  
        $result = mysqli_query($conn,$query);
        while($row=mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        if(!empty($resultset))
            return $resultset;
    }
    
    private function responseData($status,$statuscode,$message,$data){
        $response['status'] = $status;
        $response['message'] = $message;
        if(isset($data) && $data != ""){
            $response['data'] = $data;
        }
        $this->response($response, $statuscode);
        exit();
    }
    
    private function crypto_rand_secure($min, $max){
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }
    
    private function genToken($length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited
        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
        }
        return $token;
    }
    
    private function CheckEmailExist($email){
        $query = 'SELECT * FROM users WHERE email LIKE "%' .$email. '%"';
        $result = $this->executeSelectQuery($query);
        if(count($result) > 0){
            return 0;
        }else{
            return 1;
        }
    }
    
    private function Checkuser($user_id,$token){
        $query = "SELECT * FROM users WHERE user_token = '".$token."' AND id = '".$user_id."' ";
        $result = $this->executeSelectQuery($query);
        if(count($result) > 0){
            return 1;
        }else{
            return 0;
        }
    }
    
     private function userData($user_id){
        $query = "SELECT * FROM users WHERE id = '".$user_id."' ";
        $result = $this->executeSelectQuery($query);
        if(count($result) > 0){
            return $result;
        }else{
            return [];
        }
    }
    
    /* 
     *    Simple login API
     *  Login must be POST method
     *  email : <USER EMAIL>
     *  pwd : <USER PASSWORD>
     */
    
    
    private function haytest(){
//        $last_inserted_ID = 17;
//        $res_query = "SELECT * FROM users WHERE id = $last_inserted_ID";
//        $res_result = $this->executeSelectQuery($res_query);
//        print_r($res_result);
        
        $last_inserted_ID = 3;
        echo $res_query = "SELECT * FROM imagedata WHERE image_id = $last_inserted_ID";
        $res_result = $this->executeSelectQuery($res_query);
        print_r($res_result);
        
        $response            = array();
        $response['status']  = 1;
        $response['message'] = "Hay It's Working";
        $response['data'] = $res_result;
        $this->response($response, 200);
    }
    
    private function RegUser(){
        $response = array();
        $ParamData =$this->_request;
        if ($this->get_request_method() != "POST") {
            $this->responseData(0,406,"Invalid Method","");
        }
        if(!isset($ParamData['data']['user_fullname']) && empty($ParamData['data']['user_fullname'])){
            $this->responseData(0,200,"user_fullname parameter not found","");
        }
        if(!isset($ParamData['data']['email']) && empty($ParamData['data']['email'])){
            $this->responseData(0,200,"email parameter not found","");
        }
        if(!isset($ParamData['data']['password']) && empty($ParamData['data']['password'])){
            $this->responseData(0,200,"password parameter not found","");
        }
        if(!isset($ParamData['data']['user_image']) && empty($ParamData['data']['user_image'])){
            $this->responseData(0,200,"user_image parameter not found","");
        }
        
        if($this->CheckEmailExist($ParamData['data']['email']) == 0){
            $this->responseData(0,200,"Email already Exist","");
        }
        
        //$user_token = $this->genToken(128);
        $user_token = "";
        $query = "insert into users (user_fullname,email,password,user_image,user_token) values ('" . $ParamData['data']['user_fullname'] ."','". $ParamData['data']['email'] ."','" . MD5($ParamData['data']['password']) ."','" . $ParamData['data']['user_image'] ."','" . $user_token ."')";
        $result = $this->executeQuery($query);
        if($result['affectedRows'] > 0){
            $last_inserted_ID = $result['lastID'];
            
            $res_query = "SELECT * FROM users WHERE id = '" .$last_inserted_ID. "'";
            $res_result = $this->executeSelectQuery($res_query);
//            print_r($res_result);
            if(count($res_result) > 0){
                $response['data']['user_id'] = $res_result[0]['id'];
                $response['data']['user_fullname'] = $res_result[0]['user_fullname'];
                $response['data']['email'] = $res_result[0]['email'];
                $response['data']['user_image'] = $res_result[0]['user_image'];
                $response['data']['email'] = $res_result[0]['email'];
                $response['data']['user_token'] = $res_result[0]['user_token'];
                $this->responseData(1,200,"User Registered successfully",$response['data']);
            }else{
                $this->responseData(0,200,"User not Registered","");
            }
        }else{
            $this->responseData(0,200,"User not Registered","");
        }
    }
    
    private function login(){
        $response = array();
        $ParamData =$this->_request;
        if ($this->get_request_method() != "POST") {
            $this->responseData(0,406,"Invalid Method","");
        }
        if(!isset($ParamData['data']['email']) && empty($ParamData['data']['email'])){
            $this->responseData(0,200,"email parameter not found","");
        }
        if(!isset($ParamData['data']['password']) && empty($ParamData['data']['password'])){
            $this->responseData(0,200,"password parameter not found","");
        }
        $query = "SELECT * FROM users WHERE email = '" .$ParamData['data']['email']. "' AND password = '" .MD5($ParamData['data']['password']). "' AND user_status=1";
        $result = $this->executeSelectQuery($query);
        if(!empty($result)){
            if($result[0]['email'] == $ParamData['data']['email'] && $result[0]['password'] == MD5($ParamData['data']['password'])){
                $user_token = $this->genToken(128);
                $update_user_query = "UPDATE users SET user_token = '".$user_token."' WHERE id = ".$result[0]['id'];
                $update_userresult = $this->executeQuery($update_user_query);
                if($update_userresult['affectedRows'] > 0){
                    $response['data']['user_id'] = $result[0]['id'];
                    $response['data']['user_fullname'] = $result[0]['user_fullname'];
                    $response['data']['email'] = $result[0]['email'];
                    $response['data']['user_image'] = $result[0]['user_image'];
                    $response['data']['user_token'] = $user_token;
                    $this->responseData(1,200,"User login successfully",$response['data']);
                }else{
                    $this->responseData(0,200,"Invalid Credential, please try again.","");
                }
            }else{
                $this->responseData(0,200,"Invalid Credential, please try again.","");
            }
        }else{
            $this->responseData(0,200,"Invalid Credential, please try again.","");
        }
    }
    
    private function logout(){
        $response = array();
        $ParamData =$this->_request;
        if ($this->get_request_method() != "POST") {
            $this->responseData(0,406,"Invalid Method");
        }
        if(!isset($ParamData['data']['user_id']) && empty($ParamData['data']['user_id'])){
            $this->responseData(0,200,"user_id parameter not found");
        }
        if(!isset($ParamData['data']['user_token']) && empty($ParamData['data']['user_token'])){
            $this->responseData(0,200,"user_token parameter not found");
        }
        $query = "SELECT * FROM users WHERE id = '" .$ParamData['data']['user_id']. "' AND user_token = '" .$ParamData['data']['user_token']. "' AND user_status=1";
        $result = $this->executeSelectQuery($query);
        if(count($result) > 0){
            $user_token = "";
            $update_user_query = "UPDATE users SET user_token = '".$user_token."' WHERE id = ".$result[0]['id'];
            $update_userresult = $this->executeQuery($update_user_query);
            if($update_userresult['affectedRows'] > 0){
                $response['data']['user_id'] = $result[0]['id'];
                $response['data']['user_fullname'] = $result[0]['user_fullname'];
                $response['data']['email'] = $result[0]['email'];
                $response['data']['user_image'] = $result[0]['user_image'];
                $response['data']['user_token'] = $user_token;
                $this->responseData(1,200,"User logout successfully",$response['data']);
            }else{
                $this->responseData(0,200,"Invalid token.","");
            }
        }else{
            $this->responseData(0,200,"Invalid token.","");
        }
    }
    
    private function InsertUserImage(){
        $response = array();
        $ParamData =$this->_request;
        if ($this->get_request_method() != "POST") {
            $this->responseData(0,406,"Invalid Method","");
        }
        if(!isset($ParamData['data']['user_id']) && empty($ParamData['data']['user_id'])){
            $this->responseData(0,200,"user_id parameter not found","");
        }
        if(!isset($ParamData['data']['user_token']) && empty($ParamData['data']['user_token'])){
            $this->responseData(0,200,"user_token parameter not found","");
        }
        if(!isset($ParamData['data']['images']) && empty($ParamData['data']['images'])){
            $this->responseData(0,200,"images parameter not found","");
        }
        if(!isset($ParamData['data']['image_name']) && empty($ParamData['data']['image_name'])){
            $this->responseData(0,200,"image_name parameter not found","");
        }
        if(!isset($ParamData['data']['image_description']) && empty($ParamData['data']['image_description'])){
            $this->responseData(0,200,"image_description parameter not found","");
        }
        $isValid = $this->Checkuser($ParamData['data']['user_id'],$ParamData['data']['user_token']);
        if($isValid == 1){
            $query = "insert into imagedata (user_id,images,image_name,image_description) values ('" . $ParamData['data']['user_id'] ."','". $ParamData['data']['images'] ."','" . $ParamData['data']['image_name'] ."','" . $ParamData['data']['image_description'] ."')";
            $result = $this->executeQuery($query);
            if($result['affectedRows'] > 0){
                $last_inserted_ID = $result['lastID'];

                $res_query = "SELECT * FROM imagedata WHERE image_id = $last_inserted_ID";
                $res_result = $this->executeSelectQuery($res_query);
                if(count($res_result) > 0){
                    $userData = $this->userData($res_result[0]['user_id']);
                    $response['data']['image_id'] = $res_result[0]['image_id'];
                    $response['data']['images'] = $res_result[0]['images'];
                    $response['data']['image_name'] = $res_result[0]['image_name'];
                    $response['data']['image_description'] = $res_result[0]['image_description'];
                    $response['data']['created_date'] = $res_result[0]['created_date'];
                    $response['data']['user']['user_id'] = ($userData[0]['id']) ? ($userData[0]['id']) : 0;
                    $response['data']['user']['user_fullname'] = ($userData[0]['user_fullname']) ? ($userData[0]['user_fullname']) : 0;
                    $response['data']['user']['user_image'] = ($userData[0]['user_image']) ? ($userData[0]['user_image']) : 0;
                    $this->responseData(1,200,"UserImage Upload successfully",$response['data']);
                    exit();
                }else{
                    $this->responseData(0,200,"UserImage Upload fail","");
                }
            }else{
                $this->responseData(0,200,"UserImage Upload fail","");
            }
        }else{
            $this->responseData(0,200,"Invalid token.","");
        }
    }
    
    private function ownUserImage(){
        $dataarray = array();
        $ParamData =$this->_request;
        if ($this->get_request_method() != "POST") {
            $this->responseData(0,406,"Invalid Method","");
        }
        if(!isset($ParamData['data']['user_id']) && empty($ParamData['data']['user_id'])){
            $this->responseData(0,200,"user_id parameter not found","");
        }
        if(!isset($ParamData['data']['user_token']) && empty($ParamData['data']['user_token'])){
            $this->responseData(0,200,"user_token parameter not found","");
        }
        $isValid = $this->Checkuser($ParamData['data']['user_id'],$ParamData['data']['user_token']);
        if($isValid == 1){
            $res_query = "SELECT * FROM imagedata WHERE user_id = '".$ParamData['data']['user_id']."'";
            $res_result = $this->executeSelectQuery($res_query);
            if(count($res_result) > 0){
                $dataarray = array();
                foreach ($res_result as $key => $value){
                    $userData = $this->userData($value['user_id']);
                    $dataarray[$key]['image_id'] = $value['image_id'];
                    $dataarray[$key]['images'] = $value['images'];
                    $dataarray[$key]['image_name'] = $value['image_name'];
                    $dataarray[$key]['image_description'] = $value['image_description'];
                    $dataarray[$key]['created_date'] = $value['created_date'];
                    $dataarray[$key]['user']['user_id'] = ($userData[0]['id']) ? ($userData[0]['id']) : 0;
                    $dataarray[$key]['user']['user_fullname'] = ($userData[0]['user_fullname']) ? ($userData[0]['user_fullname']) : 0;
                    $dataarray[$key]['user']['user_image'] = ($userData[0]['user_image']) ? ($userData[0]['user_image']) : 0;
                }
                $this->responseData(1,200,"UserImage get successfully",$dataarray);
            }else{
                $this->responseData(0,200,"UserImage not found","");
            }
        }else{
            $this->responseData(0,200,"Invalid token.","");
        }
    }
    
    private function allUserImage(){
        $dataarray = array();
        $ParamData =$this->_request;
        if ($this->get_request_method() != "POST") {
            $this->responseData(0,406,"Invalid Method","");
        }
        if(!isset($ParamData['data']['user_id']) && empty($ParamData['data']['user_id'])){
            $this->responseData(0,200,"user_id parameter not found","");
        }
        if(!isset($ParamData['data']['user_token']) && empty($ParamData['data']['user_token'])){
            $this->responseData(0,200,"user_token parameter not found","");
        }
        $isValid = $this->Checkuser($ParamData['data']['user_id'],$ParamData['data']['user_token']);
        if($isValid == 1){
            $res_query = "SELECT * FROM imagedata";
            $res_result = $this->executeSelectQuery($res_query);
            if(count($res_result) > 0){
                $dataarray = array();
                foreach ($res_result as $key => $value){
                    
                    $userData = $this->userData($value['user_id']);
                    $dataarray[$key]['image_id'] = $value['image_id'];
                    $dataarray[$key]['images'] = $value['images'];
                    $dataarray[$key]['image_name'] = $value['image_name'];
                    $dataarray[$key]['image_description'] = $value['image_description'];
                    $dataarray[$key]['created_date'] = $value['created_date'];
                    $dataarray[$key]['user']['user_id'] = ($userData[0]['id']) ? ($userData[0]['id']) : 0;
                    $dataarray[$key]['user']['user_fullname'] = ($userData[0]['user_fullname']) ? ($userData[0]['user_fullname']) : 0;
                    $dataarray[$key]['user']['user_image'] = ($userData[0]['user_image']) ? ($userData[0]['user_image']) : 0;
                }
                $this->responseData(1,200,"UserImage get successfully",$dataarray);
            }else{
                $this->responseData(0,200,"UserImage not found","");
            }
        }else{
            $this->responseData(0,200,"Invalid token.","");
        }
    }
}
// Initiiate Library
$api = new API;
$api->processApi();
?>