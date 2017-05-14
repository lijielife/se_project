<?php

/**
 * Created by PhpStorm.
 * User: yanhaopeng
 * Date: 17/4/1
 * Time: 下午3:59
 */
class User_model extends CI_Model
{
    private $salt = "se_proj";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * @param $username
     * @param $password
     * @return bool true：success， false： failed
     *
     * 验证用户登陆账号密码是否正确
     */
    public function validate($username, $password)
    {
        $sql = "SELECT * FROM LoginUser WHERE user=?";

        $query = $this->db->query($sql, array($username));
        $row = $query->row();

        /*
         * 对明文密码进行加密，然后与数据库中的密码进行对比
         * 若两者匹配，则返回true，表示登陆密码正确
         */
        if (crypt($password, $this->salt) == $row->password) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $username
     * @param $password
     * @param $email 暂时未加
     * @return mixed
     * 用户注册
     */
    public function userRegister($username, $password)
    {
        $sql = "INSERT INTO LoginUser(user,password) VALUES(?, ?)";
        $password_crypted = crypt($password, $this->salt);
        $query = $this->db->query($sql, array($username, $password_crypted));
        return $query;
    }

    /**
     * @param $str
     * @param null $salt
     * @param $str
     * 对crypt函数做一个封装，使之默认使用User_model里面的salt
     */
    public function crypt($str, $salt=null){
        if($salt == null){
            $salt = $this->salt;
        }
        return crypt($str, $salt);
    }

    /**
     * @param $name
     * @return 已存在 true；未存在 false
     * 用户名是否存在
     */
    function existUserName($name){
        $sql = "SELECT * FROM LoginUser WHERE user = '" . $name ."'" ;
        $query = $this->db->query($sql);

        if($query->num_rows() > 0)
            {
                return true;
            }
        return false;
    }
}