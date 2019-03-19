<?php

/*
 * Clamav.php
 *
 * A simple PHP class for scanning files using ClamAV.  CodeIgniter friendly of course!
 *
 * Copyright (C) 2017 KISS IT Consulting <http://www.kissitconsulting.com/>
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above
 *    copyright notice, this list of conditions and the following
 *    disclaimer in the documentation and/or other materials
 *    provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL ANY
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

namespace KissitPHPClamavScan;

class Clamav {
    private $clamd_sock = "/var/run/clamav/clamd.sock";
    private $clamd_sock_len = 20000;
    private $clamd_ip = null;
    private $clamd_port = 3310;
    private $message = "";

    // Your basic constructor.
    // Pass in an array of options to change the default settings.  You probably will only ever need to change the socket
    public function __construct($opts = array()) {
        if(isset($opts['clamd_sock'])) {
            $this->clamd_sock = $opts['clamd_sock'];
        }
        if(isset($opts['clamd_sock_len'])) {
            $this->clamd_sock_len = $opts['clamd_sock_len'];
        }
        if(isset($opts['clamd_ip'])) {
            $this->clamd_ip = $opts['clamd_ip'];
        }
        if(isset($opts['clamd_port'])) {
            $this->clamd_port = $opts['clamd_port'];
        }
    }

    // Private function to open a socket to clamd based on the current options
    private function socket() {
        if(!empty($this->clamd_ip) && !empty($this->clamd_port)) {
            // Attempt to use a network based socket
            $socket = socket_create(AF_INET, SOCK_STREAM, 0);
            if(socket_connect($socket, $this->clamd_ip, $this->clamd_port)) {
                return $socket;
            }
        } else {
            // By default we just use the local socket
            $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
            if(socket_connect($socket, $this->clamd_sock)) {
                return $socket;
            }
        }
        return false;
    }

    // Get the last scan message
    public function getMessage() {
        return $this->message;
    }

    // Function to ping Clamd to make sure its functioning
    public function ping() {
        $ping = $this->send("PING");
        if($ping == "PONG") {
            return true;
        }
        return false;
    }

    // Function to scan the passed in file.  Returns true if safe, false otherwise.
    public function scan($file) {
        if(file_exists($file)) {
            $scan = $this->send("SCAN $file");
            $scan = substr(strrchr($scan, ":"), 1);
            if($scan !== false) {
                $this->message = trim($scan);
                if($this->message == "OK") {
                    return true;
                }
            } else {
                $this->message = "Scan failed";
            }
        } else {
            $this->message = "File not found";
        }
        return false;
    }

    // Function to send a command to the Clamd socket.  In case you need to send any other commands directly.
    public function send($command) {
        if(!empty($command)) {
            $socket = $this->socket();
            if($socket) {
                socket_send($socket, $command, strlen($command), 0);
                socket_recv($socket, $return, $this->clamd_sock_len, 0);
                socket_close($socket);
                return trim($return);
            }
        }
        return false;
    }
}
