<?php


/** ASTERISK COMMANDER 1.0
  *
  *   @version 1.0
  *   @author Oussama Hammami <oussama@voxtrot.com>
  *   @copyright 20011-2012 S CallOut Project
  *   @link http://voxtrot.com/
  */


error_reporting(0);
session_start();
                             /*=-- SETTINGS --=*\
                             \*=-- SETTINGS --=*/


//........................................................... GENERAL OPTIONS
$history_chars = 20;    // Maximal number of characters per line in displayed
                        // history dropdown

                         /*=-- GLOBAL VARIABLES --=*\
                         \*=-- GLOBAL VARIABLES --=*/

$self = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], "/") + 1);
$pr_host = "Asterisk IP:";
$pr_login = "Login:";
$pr_pass = "Password:";
$err = "Invalid login!";
$succ = "Successful login!";
$errip = "Invalid IP format!";

//............................................................. function ast_exec

require_once "telnet.php";

function ast_exec($com) {
$telnet = new PHPTelnet();
$result = $telnet->Connect($_SESSION['astcmd']['ip'],$_SESSION['astcmd']['user'],$_SESSION['astcmd']['pass']);

if ($result == 0) {
$telnet->DoCommand($com, $result);
$telnet->Disconnect();
return $result;
}
return 'Error!';
}

function cli_login() {
$telnet = new PHPTelnet();
$result = $telnet->Connect($_SESSION['astcmd']['ip'],$_SESSION['astcmd']['user'],$_SESSION['astcmd']['pass']);

if ($result == 0) {
$telnet->Disconnect();
return 1;
}
return 0;
}


//.............................................................

    //$_GET['cmd'] = gpc_clear_slashes($_GET['cmd']);


                          /*=-- AUTHENTICATION --=*\
                          \*=-- AUTHENTICATION --=*/

//............................................................. NOT LOGGED IN

if (isset($_GET['cmd']) && !isset($_SESSION['astcmd']['host'])) {


    if (preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $_GET['cmd'])) {
         //$_SESSION['astcmd']['host'] = substr($_SERVER["SERVER_NAME"],0,strpos($_SERVER["SERVER_NAME"],'.'));
         $_SESSION['astcmd']['host'] =  gethostbyaddr($_GET['cmd']);
         $_SESSION['astcmd']['ip'] = $_GET['cmd'];
         $output = "\n$pr_host {$_GET['cmd']}";
         $prompt = "$pr_login";
         ajax_dump($prompt, $output);
    } else {
         $output = "\n$errip\n";
         $prompt = "$pr_host";
         ajax_dump($prompt, $output);
    }

} elseif (isset($_GET['cmd']) && !isset($_SESSION['astcmd']['user'])) {

    //........................................... WE HAVE USERNAME & PASSWORD
    if (isset($_SESSION['astcmd']['login']) && isset($_GET['cmd'])) {
        $output = "\n$pr_pass";

        //................................................... USERNAME EXISTS

            $_SESSION['astcmd']['user'] = $_SESSION['astcmd']['login'];
            $_SESSION['astcmd']['pass'] = $_GET['cmd'];
            unset($_SESSION['astcmd']['login']);
            if (cli_login()) {
                $output .= "\n$succ\n";
                $prompt = set_prompt();
            } else {
                unset($_SESSION['astcmd']['host']);
                unset($_SESSION['astcmd']['user']);
                unset($_SESSION['astcmd']['pass']);
                $output .= "\n$err\n";
                $prompt = $pr_host;
    }


    //................................................. WE HAVE ONLY USERNAME
    } elseif (!isset($_SESSION['astcmd']['login'])) {
        $_SESSION['astcmd']['login'] = $_GET['cmd'];
        $output = "\n$pr_login {$_GET['cmd']}";
        $prompt = $pr_pass;
    }
    ajax_dump($prompt, $output);


                          /*=-- MEMBER'S AREA --=*\
                          \*=-- MEMBER'S AREA --=*/

} elseif (isset($_GET['cmd'])) {
//if (isset($_GET['cmd'])) {
    if ($_GET['cmd']== "exit")
        session_destroy();
    else
        $output = "\n$prompt{$_GET['cmd']}\n" . ast_exec("{$_GET['cmd']}"); //--
    $prompt = set_prompt();
    ajax_dump($prompt, $output);

} else {


                             /*=-- HTML PAGE --=*\
                             \*=-- HTML PAGE --=*/

?><HTML>
<HEAD>
  <TITLE>Asterisk Commander</TITLE>
  <STYLE TYPE="text/css">

INPUT, TEXTAREA, SELECT, OPTION, TD {
    color: #BBBBBB;
    background-color: #000000;
    font-family: Terminus, TTFminus, Fixedsys, Fixed, Terminal, Courier New, Courier;
    font-size: 16px;
}

TEXTAREA {
    overflow-y: auto;
    border-width: 0px;
    height: 100%;
    width: 100%;
    padding: 0px;
}

INPUT {
    border-width: 0px;
    height: 26px;
    width: 100%;
    padding-top: 5px;
}

SELECT, OPTION {
    color: #000000;
    background-color: #BBBBBB;
}

BODY {
    overflow-y: hidden;
    margin: 0;
}

</STYLE>
</HEAD>
<BODY onLoad="input_focus()" TOPMARGIN="0" LEFTMARGIN="0">
<SCRIPT LANGUAGE="JavaScript"><!--
var http_request;
var input_cmd;
var focus_id = "<?= (!isset($_SESSION['astcmd']['user']) && isset($_SESSION['astcmd']['login'])) ? "passw" : "input" ?>";

function httpRequest() {

    http_req = false;

    if (window.XMLHttpRequest) { // Mozilla

        http_req = new XMLHttpRequest();

        if (http_req.overrideMimeType)
            http_req.overrideMimeType('text/plain');

    } else if (window.ActiveXObject) { // IE

        try {
            http_req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                http_req = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }

    return http_req;
}

function update_page() {
    if (http_request.readyState == 4) {
        if (http_request.status == 200) {

            ret = http_request.responseText.split("\r");

            //out = ret[1] + ret[2]; // command output
            //prm = ret[0]; // prompt
            prm = ret.shift();
            out = ret.join(""); // command output

            history_sel = document.getElementById('history_select');

            if (input_cmd &&
                (out.substr(1, <?= strlen($pr_host) ?>) != "<?= $pr_host ?>") &&
                (out.substr(1, <?= strlen($pr_login) ?>) != "<?= $pr_login ?>") &&
                (out.substr(1, <?= strlen($pr_pass) ?>) != "<?= $pr_pass ?>")
            ) {
                exists = false;
                for (i = 1; i < history_sel.length - 1; i++)
                    if (history_sel.options[i].value == input_cmd) {
                        exists = true;
                        break;
                    }

                if (!exists) {
                    hist_count = history_sel.length;
                    last_value = history_sel.options[hist_count - 1].value;
                    last_text = history_sel.options[hist_count - 1].text;
                    history_sel.length++;
                    history_sel.options[hist_count].value = last_value;
                    history_sel.options[hist_count].text = last_text;
                    history_sel.options[hist_count - 1].value = input_cmd;
                    history_sel.options[hist_count - 1].text =
                      (input_cmd.length > <?= $history_chars ?>)
                        ? input_cmd.substr(0, <?= ($history_chars - 3) ?>) + "..."
                        : input_cmd;
                }
            }

            first_word = input_cmd;
            if (first_word.indexOf(" ") > -1)
                first_word = first_word.substr(0, first_word.indexOf(" "));

            if ((first_word == "clear") || (first_word == "exit"))
                document.getElementById('output').value = "";
            else
                document.getElementById('output').value += out;

            document.getElementById('prompt').innerHTML = (first_word == "exit")
              ? "<?= $pr_host ?>" : prm;

            if (prm == "<?= $pr_pass ?>") {
                document.getElementById('div_pass').style.visibility = "visible";
                document.getElementById('input').value = "";
                focus_id = "passw";
            } else {
                document.getElementById('div_pass').style.visibility = "hidden";
                document.getElementById('passw').value = "";
                focus_id = "input";
            }

            if (first_word == "exit") {
                last_option = history_sel.options[history_sel.length - 1];
                history_sel.length = 2;
                history_sel.options[1] = last_option;
                history_sel.selectedIndex = 0;
            }

            document.getElementById('history_cell').style.visibility =
              ((prm == "<?= $pr_host ?>") || (prm == "<?= $pr_login ?>") || (prm == "<?= $pr_pass ?>") || (first_word == "exit"))
                ? "hidden" : "visible";
        }

        focus_id = focus_id ? focus_id : "input";

        document.getElementById('input').value = "";
        document.getElementById('ajax_loading').style.visibility = "hidden";
        document.getElementById(focus_id).focus();
        document.getElementById('output').scrollTop = document.getElementById('output').scrollHeight;
    }
}

function ajax_action(url, func) {
    http_request = httpRequest();

    if (!http_request) {
        alert('Giving up :( Cannot create an XMLHTTP instance');
        return false;
    }

    document.getElementById('ajax_loading').style.visibility = "visible";
    http_request.onreadystatechange = func;
    http_request.open("GET", url, true);
    http_request.setRequestHeader("Expires", "Mon, 26 Jul 1997 05:00:00 GMT");
    http_request.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    http_request.setRequestHeader("Pragma", "no-cache");
    http_request.send(null);
}

function input_focus() {
    document.getElementById('div_pass').style.visibility = (focus_id == "passw")
      ? "visible" : "hidden";
    document.getElementById(focus_id).focus();
}

function selection_to_clipboard() { // Auto copy selected text. IE only!
    if (window.clipboardData && document.selection)
        window.clipboardData.setData("Text", document.selection.createRange().text);
}

function execute_cmd(cmd_pass, cmd) {
    cmd = cmd_pass ? cmd_pass : cmd;
    cmd = cmd.replace(/\s+/g, " ").replace(/^\s+/g, "").replace(/\s+$/g, "");
    input_cmd = cmd;
    document.getElementById('output').focus();
    ajax_action("<?= $self ?>?cmd=" + escape(cmd), update_page);
    return false;
}

function get_from_history(history_sel) {
    option = history_sel.options[history_sel.selectedIndex];
    if (option.value) {

        // " " option value indicates that "clear history" selected
        if (option.value == " ") {
            last_option = history_sel.options[history_sel.length - 1];
            history_sel.length = 2;
            history_sel.options[1] = last_option;
            history_sel.selectedIndex = 0;
        } else {
            history_sel.selectedIndex = 0;
            document.getElementById('input').value = option.value;
            document.getElementById('input').focus();
        }
    }
}

if (window.clipboardData) // Right click pastes the clipboard. IE only
    document.oncontextmenu = new Function("document.getElementById('input').value = window.clipboardData.getData('Text'); input_focus(); return false");

--></SCRIPT>
<DIV ID="ajax_loading" STYLE="position:absolute; visibility:hidden; z-index:100; left:0; top:0; width:100%; height:100%; background-color:#FF9999; opacity:.30; filter:alpha(opacity=30)"></DIV>
<TABLE CELLPADDING="0" CELLSPACING="0" BORDER="0" HEIGHT="100%" WIDTH="100%">
<TR>
  <TD HEIGHT="100%" BGCOLOR="#000000" STYLE="padding-top: 5px; padding-left: 5px; padding-right: 5px; padding-bottom: 0px"><TEXTAREA ID="output" onSelect="selection_to_clipboard()" onClick="input_focus()" READONLY></TEXTAREA></TD>
</TR>
<TR>
  <TD BGCOLOR="#000000"><TABLE CELLPADDING="0" CELLSPACING="5" BORDER="0" WIDTH="100%">
    <TR>
    <FORM METHOD="POST" onSubmit="return execute_cmd(this.elements[0].value, this.elements[1].value)">
      <TD NOWRAP onClick="input_focus()" ID="prompt"><?= isset($_SESSION['astcmd']['user']) ? set_prompt() : (!isset($_SESSION['astcmd']['host']) ? $pr_host : (isset($_SESSION['astcmd']['login']) ? $pr_pass : $pr_login)) ?></TD>
      <TD WIDTH="100%"><DIV ID="div_pass" STYLE="position:absolute; visibility:hidden"><INPUT ID="passw" TYPE="PASSWORD" NAME="cmd"></DIV><INPUT ID="input" TYPE="TEXT" NAME="cmd"></TD>
      <TD><INPUT TYPE="SUBMIT" STYLE="width:1px; height:1px; border-width:0px"></TD>
    </FORM>
      <TD><DIV STYLE="visibility:<?= isset($_SESSION['astcmd']['user']) ? "visible" : "hidden" ?>" ID="history_cell"><SELECT ID="history_select" onChange="get_from_history(this)">
        <OPTION VALUE="">-=> HISTORY</OPTION>
        <OPTION VALUE=" ">-=> CLEAR HISTORY</OPTION></SELECT></DIV></TD>
    </TR>
  </TABLE></TD>
</TR>
</TABLE>

<SCRIPT LANGUAGE="JavaScript"><!--
document.getElementById('output').scrollTop = document.getElementById('output').scrollHeight;
--></SCRIPT>

</BODY>
</HTML>
<?php

}


                             /*=-- FUNCTIONS --=*\
                             \*=-- FUNCTIONS --=*/

function set_prompt() {
    return $_SESSION['astcmd']['host'] . "*CLI> ";
}

function first_word($str) {
    list($str) = preg_split('/[ ;]/', $str);
    return $str;
}

function ajax_dump($prompt, $output) {
    echo "$prompt\r$output";
}

function gpc_clear_slashes($sbj) {
    if (ini_get('magic_quotes_gpc'))
        $sbj = stripslashes($sbj);
    return $sbj;
}

?>

