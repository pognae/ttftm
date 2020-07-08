<?php
/*******************************************************************************
* login.php
* 用户登入界面文件
* user login page
* 功能描述
	 首先载入所有元素，然后调用javascripr的init函数初始化页面上的文字信息

* Function Desc
	first load all elements, and then call javascript init function to initialize words on this page

* Page elements
* Form:							
									loginForm
* input field:					
									username			->	 username
									password			->	 password
									locate				->	 language
* hidden field:				
									onclickMsg			-> save message when user click login button
* button:						
									loginButton		-> user login button
* div:							
									titleDiv				->	 login form title
									usernameDiv		-> username
									passwordDiv		-> password
									locateDiv			-> locate
* javascript function:		
									loginSignup	
									init					 


* Revision 0.0443  2007/10/8 17:55:00  last modified by solo
* Desc: add a div to display copyright



* Revision 0.044  2007/09/7 17:55:00  last modified by solo
* Desc: add some comments
* 描述: 增加了一些注释信息
********************************************************************************/

require_once('login.common.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $locate->Translate("title")?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="skin/default/css/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="skin/default/css/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="skin/default/css/adminlte.css">

    <?php $xajax->printJavascript('include/'); ?>
		<script type="text/javascript">
		/**
		*  login function, launched when user click login button
		*
		* @param null
		* @return false
		*/
		function loginSignup()
		{
			//xajax.$('loginButton').disabled=true;
			//xajax.$('loginButton').value=xajax.$('onclickMsg').value;
			xajax_processForm(xajax.getFormValues("loginForm"));
			//return false;
		}
		
		function selectmode(msg)
		{
			if(confirm(msg)){
				window.location.href="portal.php";
				return true;
			}
			xajax_clearDynamicMode();
			return false;
		}

		/**
		*  init function, launched after page load
		*
		*  	@param null
		*	@return false
		*/
		function init() {
			xajax_init(xajax.getFormValues("loginForm"));
			return false;
		}

		function setlanguage() {
			xajax_setLang(xajax.getFormValues("loginForm"));			
			return false;
		}
		</script>
</head>

<body class="hold-transition login-page">

<div class="login-box">
    <div class="login-logo">
        <a href="../../index2.html"><b>TTF </b>TM</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form id="loginForm" action="javascript:loginSignup();" method="post">
                <div class="text01" id="logintip"></div>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="ID" name="username" id="username" value="test">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password" id="password" value="1111">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-8">
                        <div class="icheck-primary">
                            <!--
                            <input type="checkbox" value="forever" id="rememberme" name="rememberme">
                            <label for="remember">
                                Remember Me
                            </label>
                            -->
                        </div>
                    </div>

                    <!-- /.col -->
                    <div class="col-4">
                        <input type="hidden" name="locate" id="locate" value="ko_KR">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <p class="mb-1 small">
                <a href="forgot-password.html"><?php echo $locate->Translate("i_forgot_my_password")?></a>
            </p>
            <!--
            <p class="mb-0 small">
                <a href="register.html" class="text-center">Register a new membership</a>
            </p>
            -->
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="js/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="js/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="js/adminlte.js"></script>

</body>
</html>
