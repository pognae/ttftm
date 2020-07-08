
<html>
<head>

    <?php
    import_request_variables("GP");
    $man_name = "admin";
    $man_pword = "admin";

    ?>
    <META http-equiv="Content-Style-Type" content="text/css">
    <link href="default.css" rel="stylesheet" type="text/css">

    <?
    if ($mode == "put"){ echo '<meta http-equiv="Refresh" Content="2; URL='.$self.'?mode=show">';}
    ?>

</head>
<BODY>
<?
// ----------------------------------------------------------------------- begin ast db show
if(!isset($mode)|| $mode == 'show') {
echo 'Asterisk Database Maintenance<P><a href="'.$self.'?mode=form">New Entry</a> - Or edit existing below<P>';

$socket = fsockopen("127.0.0.1","5038", $errno, $errstr, $timeout); // open a connection to the manager interface

fputs($socket, "Action: Login\r\n"); // login
fputs($socket, "UserName: $man_name\r\n"); // send username
fputs($socket, "Secret: $man_pword\r\n\r\n"); // send password

fputs($socket, "Action: Command\r\n"); // tell it a command is coming
fputs($socket, "Command: database show\r\n\r\n"); // ask for the contents of the database
fputs($socket, "Action: Logoff\r\n\r\n"); // logoff

while (!feof($socket)) {
    $dbentries .= fread($socket, 8192); // read the entire output into $dbentries
}
fclose($socket); // close the socket

$array = preg_split("/\n/",$dbentries, -1, PREG_SPLIT_NO_EMPTY); // split $dbentries on newline characters and store in $array
for ($i = 1; $i < 7; $i++) {
    array_shift($array); // get rid of 7 lines of useless garbage (need a test for this)
}

$bgcolor='white'; // set a background color for the table
$color_state=true; // use it when $color_state = true
?>
<table border="0" cellspacing="2">
    <tr bgcolor="#EEEEEE">
        <td>Family</td>
        <td>Key</td>
        <td>Value</td>
    </tr>
    <?
    foreach($array as $value) {
        if (strncmp($value, '--END COMMAND', 13) == 0) { // look for the end of the output
            break; // stop showing records at this point
        }
#print $value;
#preg_match("/^\/(.*)\/(\w*)\s*:\s*(\w\.:@\/*)/", $value, $temp); // isolate Family, Key and Value into $temp[]
        preg_match("/^\/(\w*)\/(.*): (.*)/", $value, $temp);
#print_r($temp);
        array_shift($temp); // shift off the matched portion
        $temp0=$temp[0];
        $temp1=$temp[1];
        $temp2=$temp[2];
        $querystring = 'mode=form&family='.urlencode($temp0).'&key='.urlencode($temp1).'&value='.urlencode($temp2); // build some of the URI for this value
        ?>
        <tr bgcolor=<? echo("$bgcolor");?>>
            <td><? echo '<a href="'.$self.'?'.htmlspecialchars($querystring).'">'.$temp0.'</a>';?></td>
            <td><? echo($temp1); ?></td>
            <td><? echo($temp2); ?></td>
        </tr>
        <?
        $color_state = !$color_state; // flip the color state
        if ($color_state) {
            $bgcolor = 'white'; // if true use white;
        } else {
            $bgcolor = '#EEEEEE'; // else use grey
        }
    }
    } //------------------------------------------------------------------------ end ast db show

    // ------------------------------------------------------------------------- begin ast db form

    if ($mode == "form") {
        ?>

        <P><P><P></P>
        <FORM ACTION="<? echo "$self"; ?>" METHOD="POST">
            <table border="0" width="60%">
                <tr>
                    <td width="20%" valign="top"><b>Family</b></td>
                    <td width="80%" valign="top"><INPUT TYPE="text" NAME="family" value="<? echo htmlspecialchars(stripslashes($family));?>" SIZE=40><br><br>
                    </td>
                </tr>
                <tr>
                    <td width="20%" valign="top"><b>Key</b></td>
                    <td width="80%" valign="top"><INPUT TYPE="text" NAME="key" value="<? echo htmlspecialchars(stripslashes($key));?>" SIZE=40><br><br>
                    </td>
                </tr>
                <tr>
                    <td width="20%" valign="top"><b>Value</b></td>
                    <td width="80%" valign="top"><INPUT TYPE="text" NAME="value" value="<? echo htmlspecialchars(stripslashes($value));?>" SIZE=40><br><br>
                    </td>
                </tr>
                <tr>
                    <td width="30%"></td>
                    <td width="80%"><INPUT TYPE="submit" name="verb" VALUE="Submit"></td>
                </tr>
                <tr>
                    <td width="30%"></td>
                    <td width="80%"><INPUT TYPE="submit" name="verb" VALUE="DELETE">
                        <input type="hidden" name="mode" value="put">
                </tr>
            </table>
        </FORM>

        <?
    } // ------------------------------------------------------------------------- end ast db form

    // --------------------------------------------------------------------------- begin ast db put

    if ($mode == "put") {
        if ($verb == "Submit") {
            $socket = fsockopen("127.0.0.1","5038", $errno, $errstr, $timeout);
            fputs($socket, "Action: Login\r\n"); // login
            fputs($socket, "UserName: $man_name\r\n"); // send username
            fputs($socket, "Secret: $man_pword\r\n\r\n"); // send password


            fputs($socket, "Action: Command\r\n");
            fputs($socket, "Command: database put $family $key $value\r\n\r\n");
            fputs($socket, "Action: Logoff\r\n\r\n");

            while (!feof($socket)) {
                $response .= fread($socket, 8192); // read the entire output into $response
            }
            fclose($socket); // close the socket
            if (strpos($response, 'Updated database successfully')) {
                print("Your data has been INSERTED.\r\n");
            } else print("INSERT Error....");
        }
        if ($verb == "DELETE") {

            $socket = fsockopen("127.0.0.1","5038", $errno, $errstr, $timeout);
            fputs($socket, "Action: Login\r\n");
            fputs($socket, "UserName: $man_name\r\n"); // send username
            fputs($socket, "Secret: $man_pword\r\n\r\n"); // send password

            fputs($socket, "Action: Command\r\n");
            fputs($socket, "Command: database del $family $key\r\n\r\n");
            fputs($socket, "Action: Logoff\r\n\r\n");

            while (!feof($socket)) {
                $response .= fread($socket, 8192); // read the entire output into $response
            }
            fclose($socket); // close the socket
            if (strpos($response, 'Database entry removed')) {
                print("Your data has been DELETED.\r\n");
            } else print("Delete Error....");
        }
    } // ------------------------------------------------------------------------ end ast db put
    ?>
</body>
</html>