<?php

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'cookie'));
        $this->load->library(array('form_validation','session'));
        $this->load->model("user_model");
    }

    public function index()
    {
        $this->load->view("neon/home-page.html");
    }

    //登录界面
    public function login()
    {
        //检查输入的用户名及密码的合法性
        $this->form_validation->set_rules('username', '用户名', 'required|callback_username_exist',
            array('required' => '{field}不能为空'));
        $this->form_validation->set_rules('password', '密码', 'required',
            array('required' => '{field}不能为空'));
        $login_status = 'unknown';
        $data['login_status'] = $login_status;

        //如果用户名或密码不合法
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('neon/extra-login.html',$data);
        }
        //用户名和密码均合法
        else
        {
            //得到用户输入的用户名
            $username  = $this->input->post("username");
            //利用用户名及密码检查用户是否存在：数据库操作
            if ($this->user_model->validate($username, $this->input->post("password")))
            {
                // 成功登陆
                $login_status = 'success';
            }

            if ($login_status == 'success') {
                //$username_crypted = $this->user_model->crypt($username);
                //设置session key,之后可以通过$_SESSION['username']拿到这时输入的用户名
                $this->session->set_userdata("username", $username);
                $this->load->view("neon/home-page.html", $data);
            }
            else{
                $login_status = 'invalid';
                $data['login_status'] = $login_status;
                $this->load->view('neon/extra-login.html',$data);
            }
        }

    }

    //注册界面
    public function register(){
        //检查输入的用户名及密码的合法性
        //is_unique[user.username]表示在指定数据库的user表中，当前输入的username是唯一的，即这个用户名还没有被其他用户使用
        // $this->form_validation->set_rules('username', '用户名', 'required|callback_username_check|is_unique[LoginUser.user]',
        //     array('is_unique' => '{field}已存在'));
        $this->form_validation->set_rules('username', '用户名', 'required|callback_username_check|callback_is_unique[LoginUser.user]');
        $this->form_validation->set_rules('password', 'password', 'trim|required|callback_password_check');
        $this->form_validation->set_rules('password2', 'password2', 'required|matches[password]',
            array('required' => '两次输入的密码不一致', 'matches' => '两次输入的密码不一致'));

        //如果输入的用户名或密码不合法
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('neon/extra-register.html');
        }
        //输入的用户名和密码合法
        else
        {
            //将新的用户信息插入数据库
            $this->user_model->userRegister($this->input->post('username'),$this->input->post('password'));
            //设置session key, 加载到用户界面
            $this->session->set_userdata("username", $this->input->post('username'));
            $this->load->view("neon/home-page.html");
        }
    }

    //账户设置页面：修改用户密码
    public function set_info()
    {
        //检查输入的密码的合法性
        $this->form_validation->set_rules('password_old', '原密码', 'required',
            array('required' => '{field}不能为空'));
        $this->form_validation->set_rules('password_new', '新密码', 'required|min_length[6]|max_length[20]',
            array('required' => '{field}不能为空','max_length' => '密码应为6~20位','min_length' => '密码应为6~20位'));
        $this->form_validation->set_rules('password_confirm', '新密码确认', 'required|matches[password_new]',
            array('required' => '{field}不能为空','matches' => '两次输入的密码不一致'));

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('neon/set-info.html');
        }
        else
        {
             //从view拿到的原密码
            $password_old = $this->input->post("password_old");
            //从view拿到的新密码
            $password_new = $this->input->post("password_new");
             //从view拿到的新密码的确认
            $password_confirm = $this->input->post("password_confirm");
            //......

            //加载成功修改界面
            $this->load->view('neon/change-password-success.html');
        }

    }


    //修改资金账户页面
    public function bind_fund()
    {

        $this->form_validation->set_rules('fund_account', '资金账户', 'required',
            array('required' => '{field}不能为空'));
        $this->form_validation->set_rules('fund_password', '密码', 'required',
            array('required' => '{field}不能为空'));

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view("neon/bind-fund.html");
        }
        else
        {
             //从view拿到的资金账号
            $fund_account = $this->input->post("fund_account");
            //从view拿到的资金账号密码
            $fund_password = $this->input->post("fund_password");
            //......

            //加载成功修改界面
            $this->load->view('neon/change-password-success.html');
        }

    }
    //解绑资金账号
    public function unbind_fund()
    {
        //修改数据库的操作
        //....
        //加载成功修改界面
        $this->load->view('neon/change-password-success.html');
    }

    //查询股票信息
    public function query_stock()
    {
        //是否加载股票信息
        $data['load_stock'] = false;

        //输入的股票代码合法性检查
        $this->form_validation->set_rules('stockid', '股票代码', 'required',
            array('required' => '{field}不能为空'));

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view("neon/query-stock.html",$data);
        }
        else
        {
             //从view拿到的股票代码
            $stockid = $this->input->post("stockid");

            //......
            //查询得到的股票信息
            $stock = array("stockid"=>"123456","change"=>"-1.1","chg"=>"-0.8%","latestPrice"=>"98.7",
                           "todayTotalVolumn"=>"1001","latestVolumn"=>"12","openingPrice"=>"95.5",
                           "closingPrice"=>"95.3","latestBuyPrice"=>"98.7","latestSellPrice"=>"98.7");
            $data['load_stock'] = true;
            $data['stock'] = $stock;
            $this->load->view('neon/query-stock.html',$data);
        }


    }

    //查询资金情况
    public function query_money()
    {
        //数据库查询
        //......
        //资金账户信息
        $fund = array("accountId"=>"111111","balanceOfAccount"=>"10024","availableBalance"=>"9850",
                      "frozenBalance"=>"174");
        $data['fund'] = $fund;
        $this->load->view("neon/query-money.html", $data);
    }

    //查询持有股票信息
    public function query_own_stock()
    {
        //数据库查询
        //......
        //持有股票信息
        $own_stock = array(
            array("stock"=>"123456","quantity"=>"100","price"=>"87.6",
                  "cost"=>"8530","balance"=>"2.3"),
            array("stock"=>"123457","quantity"=>"100","price"=>"866",
                  "cost"=>"8420","balance"=>"2.2")
        );
        $data['own_stock'] = $own_stock;
        $this->load->view("neon/query-own-stock.html", $data);
    }

    //购买股票
    public function buy()
    {
        //推荐价格
        $data['recommend_price'] = "101";
        //可购买的最大数量（与资金账户内资金有关）
        $data['maximum_quantity'] = "2";

        $this->load->view("neon/buy.html",$data);
    }

    //出售股票
    public function sell()
    {
        //推荐价格
        $data['recommend_price'] = "101";
        //可出售的最大数量（持有股数）
        $data['maximum_quantity'] = "2";

        $this->load->view("neon/sell.html", $data);
    }

    //查询买卖记录
    public function query_instruction()
    {
        //数据库查询
        //......
        //买卖指令记录
        $instruction = array(
            array("stock"=>"123456","buyOrSell"=>"买入","price"=>"87.6",
                  "quantity"=>"23","time"=>"2017-5-11 15:32", "state"=>"成功"),
            array("stock"=>"123457","buyOrSell"=>"卖出","price"=>"86.6",
                  "quantity"=>"11","time"=>"2017-5-11 15:35", "state"=>"待定")
        );
        $data['instruction'] = $instruction;
        $this->load->view("neon/query-instruction.html", $data);
    }

    //检查注册时输入的用户名的合法性
    public function username_check($str)
    {
        if (strlen($str) < 6 || strlen($str) > 20)
        {
            $this->form_validation->set_message('username_check', '用户名应该由6~20个字符组成');
            return FALSE;
        }
        if(!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $str)){
            echo "***";
            $this->form_validation->set_message('username_check', '用户名应该由字母开头，并由字母、数字和下划线组成');
            return FALSE;
        }
        return TRUE;
    }

    //检查注册时输入的密码的合法性
    public function password_check($str)
    {
        if (strlen($str) < 6 || strlen($str) > 20)
        {
            $this->form_validation->set_message('password_check', '密码应该由6~20个字符组成，区分大小写');
            return FALSE;
        }
        return TRUE;
    }

    //检查用户名是否已存在
    public function is_unique($str)
    {
        if($this->user_model->existUserName($str))
        {
            $this->form_validation->set_message('is_unique', '用户名已存在');
            return FALSE;
        }
        return TRUE;
    }

    public function username_exist($str)
    {
        if(!$this->user_model->existUserName($str))
        {
            $this->form_validation->set_message('username_exist', '用户名不存在');
            return FALSE;
        }
        return TRUE;
    }
    /*
    public function register_check(){
        # Response Data Array
        $resp = array();
        $username   = $this->input->post("username");
        $email      = $this->input->post("email");
        $password   = $this->input->post("password");

        $query = $this->user_model->create($username, $password, $email);

        $resp['submitted_data'] = $_POST;

        echo json_encode($resp);
    }

    public function login_check()
    {
        $resp = array();

        $username = $this->input->post("username");
        $password = $this->input->post("password");


        $resp['submitted_data'] = $_POST;

        $login_status = 'invalid';

        if ($this->user_model->validate($username, $password)) {
            $login_status = 'success';
        }

        $resp['login_status'] = $login_status;

        if ($login_status == 'success') {
            $username_crypted = $this->user_model->crypt($username);

            $this->session->set_userdata("username", $username_crypted);
            set_cookie("username", $username_crypted);
            $resp['redirect_url'] = 'index';
        }

        echo json_encode($resp);
    }*/

}