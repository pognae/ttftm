// based on an example at https://www.voip-info.org/asterisk-manager-example-php/

<?php

/**
 * Connect to Asterisk Manager Interface (AMI).
 *
 * Setup an AMI user in /etc/asterisk/manager.conf
 */
class AsteriskManager {

        // the Asterisk host
        private $host;
        // the AMI port
        private $port;
        // the AMI username
        private $username;
        // the AMI password
        private $password;
        // the AMI socket
        private $socket;
        // the error set by the last AMI command that was executed
        private $error;

        // the size of the response buffer
        const BUF_SIZE = 4096;


        function AsteriskManager($host, $port, $username, $password) {
                $this->host = $host;
                $this->port = $port;
                $this->username = $username;
                $this->password = $password;
                $this->socket = FALSE;
                $this->error = "";
        }


        /**
         * Login to AMI.
         */
        function Login() {
                $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, 1);
                if(!$this->socket) {
                        $this->error = "Failed to connect: $errstr ($errno)";
                        return FALSE;
                } else {
                        stream_set_timeout($this->socket, 1);

                        $response = $this->Request("Action: Login\r\nUserName: $this->username\r\nSecret: $this->password\r\nEvents: off\r\n\r\n");

                        if (strpos($response, "Message: Authentication accepted") != FALSE) {
                                return TRUE;
                        } else {
                                $this->error = "Failed to authenticate";
                                fclose($this->socket);
                                $this->socket = FALSE;
                                return FALSE;
                        }
                }
        }


        /**
         * Logout from AMI.
         */
        function Logout(){
                if ($this->socket) {
                        $response = "";

                        fputs($this->socket, "Action: Logoff\r\n\r\n");
                        while (!feof($this->socket)) {
                                $response .= fread($this->socket, self::BUF_SIZE);
                        }
                        fclose($this->socket);
                        $this->socket = "FALSE";
                }
        }

        /**
         * Get a value from the Asterisk database.
         */
        function GetDB($family, $key) {
                $response = $this->Command("database get $family $key");
                echo $response . "\n";
                if (strpos($response, "Database entry not found") != FALSE) {
                        $this->error =  "Database entry does not exist";
                        return FALSE;
                }

                return $this->ParseDatabaseValue($response);
        }


        /**
         * Put a value into the Asterisk database.
         */
        function PutDB($family, $key, $value) {
                $response = $this->Command("database put $family $key $value");

                if (strpos($response, "Updated database successfully") != FALSE){
                        return TRUE;
                }
                $this->error =  "Failed to update database";
                return FALSE;
        }


        /**
         * Delete a value from the Asterisk database.
         */
        function DelDB($family, $key) {
                $response = $this->Command("database del $family $key");

                if (strpos($response, "Database entry removed.") != FALSE){
                        return TRUE;
                }
                $this->error =  "Database entry does not exist";
                return FALSE;
        }


        /**
         * Send a command to AMI.
         */
        function Command($command) {
                return $this->Request("Action: Command\r\nCommand: $command\r\n\r\n");
        }


        /**
         * Send a request to AMI.
         */
        function Request($query){
                if ($this->socket === FALSE) {
                        return FALSE;
                }

                $response = "";
                fputs($this->socket, $query);
                do {
                        $line = fgets($this->socket, self::BUF_SIZE);
                        $response .= $line;
                        $info = stream_get_meta_data($this->socket);
                } while ($line != "\r\n" && $info['timed_out'] == FALSE );

                return $response;
        }


        /**
         * Get the value of the error variable.
         */
        function GetError(){
                return $this->error;
        }


        /**
         * Parse the database value from the AMI response.
         */
        function ParseDatabaseValue($response) {
                if($response) {
                        $value = "";
                        $start = strpos($response, "Value: ") + 7;
                        $end = strpos($response, "\n", $start);
                        if ($start > 8) {
                                $value = substr($response, $start, $end - $start);
                        }
                }
                return $value;
        }
}

?>