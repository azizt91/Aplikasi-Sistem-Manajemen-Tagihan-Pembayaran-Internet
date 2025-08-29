<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    private $socket;
    private $connected = false;
    private $timeout = 10;
    private $attempts = 3;
    private $delay = 2;
    private $port = 8728;
    private $ssl = false;
    private $debug = false;
    private $error_no;
    private $error_str;

    /**
     * Connect to MikroTik router using proven RouterOS API method
     */
    public function connect($host, $port = 8728, $username, $password)
    {
        try {
            $this->port = $port;
            
            // Parse hostname:port format if provided
            $actualHost = $host;
            $actualPort = $port;
            
            // Check if host contains port (hostname:port format for VPN)
            if (strpos($host, ':') !== false) {
                $parts = explode(':', $host);
                if (count($parts) == 2) {
                    $actualHost = $parts[0];
                    $actualPort = $parts[1];
                    Log::info("VPN connection detected: {$host}, connecting to {$actualHost}:{$actualPort}");
                }
            }

            Log::info("Attempting connection to {$actualHost}:{$actualPort} with user: {$username}");

            // Try connection with retry mechanism
            for ($attempt = 1; $attempt <= $this->attempts; $attempt++) {
                $this->connected = false;
                $protocol = ($this->ssl ? 'ssl://' : '');
                
                // Create SSL context for better compatibility
                $context = stream_context_create([
                    'ssl' => [
                        'ciphers' => 'ADH:ALL',
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ]);

                Log::info("Connection attempt #{$attempt} to {$protocol}{$actualHost}:{$actualPort}");

                // Connect using stream_socket_client
                $this->socket = @stream_socket_client(
                    $protocol . $actualHost . ':' . $actualPort,
                    $this->error_no,
                    $this->error_str,
                    $this->timeout,
                    STREAM_CLIENT_CONNECT,
                    $context
                );

                if ($this->socket) {
                    stream_set_timeout($this->socket, $this->timeout);
                    
                    // Login process using RouterOS API method
                    $this->write('/login', false);
                    $this->write('=name=' . $username, false);
                    $this->write('=password=' . $password);
                    
                    $response = $this->read(false);
                    
                    if (isset($response[0])) {
                        if ($response[0] == '!done') {
                            if (!isset($response[1])) {
                                // Login method post-v6.43
                                $this->connected = true;
                                Log::info("Successfully connected using post-v6.43 login method");
                                break;
                            } else {
                                // Login method pre-v6.43
                                $matches = array();
                                if (preg_match_all('/[^=]+/i', $response[1], $matches)) {
                                    if ($matches[0][0] == 'ret' && strlen($matches[0][1]) == 32) {
                                        $this->write('/login', false);
                                        $this->write('=name=' . $username, false);
                                        $this->write('=response=00' . md5(chr(0) . $password . pack('H*', $matches[0][1])));
                                        
                                        $loginResponse = $this->read(false);
                                        if (isset($loginResponse[0]) && $loginResponse[0] == '!done') {
                                            $this->connected = true;
                                            Log::info("Successfully connected using pre-v6.43 login method");
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    // Close socket if login failed
                    fclose($this->socket);
                    $this->socket = null;
                }
                
                if ($attempt < $this->attempts) {
                    Log::warning("Connection attempt #{$attempt} failed, retrying in {$this->delay} seconds...");
                    sleep($this->delay);
                }
            }

            if ($this->connected) {
                Log::info("Successfully connected to MikroTik: {$actualHost}:{$actualPort}");
                return true;
            } else {
                $errorMsg = "Failed to connect after {$this->attempts} attempts";
                if ($this->error_str) {
                    $errorMsg .= " - Error: {$this->error_str}";
                }
                throw new Exception($errorMsg);
            }

        } catch (Exception $e) {
            Log::error("MikroTik connection failed: " . $e->getMessage());
            $this->disconnect();
            throw $e;
        }
    }

    /**
     * Disconnect from MikroTik
     */
    public function disconnect()
    {
        if ($this->socket && is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
            $this->connected = false;
            Log::info("Disconnected from MikroTik");
        }
    }

    /**
     * Encode length for RouterOS API
     */
    private function encodeLength($length)
    {
        if ($length < 0x80) {
            $length = chr($length);
        } elseif ($length < 0x4000) {
            $length |= 0x8000;
            $length = chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        } elseif ($length < 0x200000) {
            $length |= 0xC00000;
            $length = chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        } elseif ($length < 0x10000000) {
            $length |= 0xE0000000;
            $length = chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        } elseif ($length >= 0x10000000) {
            $length = chr(0xF0) . chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        }

        return $length;
    }

    /**
     * Write data to RouterOS
     */
    private function write($command, $param2 = true)
    {
        if (!$this->socket) {
            throw new Exception('Not connected to MikroTik');
        }

        if ($command) {
            $data = explode("\n", $command);
            foreach ($data as $com) {
                $com = trim($com);
                fwrite($this->socket, $this->encodeLength(strlen($com)) . $com);
                if ($this->debug) {
                    Log::debug("<<< [{" . strlen($com) . "}] {$com}");
                }
            }

            if (gettype($param2) == 'integer') {
                fwrite($this->socket, $this->encodeLength(strlen('.tag=' . $param2)) . '.tag=' . $param2 . chr(0));
                if ($this->debug) {
                    Log::debug("<<< [{" . strlen('.tag=' . $param2) . "}] .tag={$param2}");
                }
            } elseif (gettype($param2) == 'boolean') {
                fwrite($this->socket, ($param2 ? chr(0) : ''));
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Read data from RouterOS
     */
    private function read($parse = true)
    {
        if (!$this->socket) {
            throw new Exception('Not connected to MikroTik');
        }

        $response = array();
        $receiveddone = false;
        
        while (true) {
            // Read the first byte of input which gives us some or all of the length
            $byte = ord(fread($this->socket, 1));
            $length = 0;
            
            // Decode length according to RouterOS API protocol
            if ($byte & 128) {
                if (($byte & 192) == 128) {
                    $length = (($byte & 63) << 8) + ord(fread($this->socket, 1));
                } else {
                    if (($byte & 224) == 192) {
                        $length = (($byte & 31) << 8) + ord(fread($this->socket, 1));
                        $length = ($length << 8) + ord(fread($this->socket, 1));
                    } else {
                        if (($byte & 240) == 224) {
                            $length = (($byte & 15) << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                        } else {
                            $length = ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                        }
                    }
                }
            } else {
                $length = $byte;
            }

            $data = "";

            // If we have got more characters to read, read them in.
            if ($length > 0) {
                $data = "";
                $retlen = 0;
                while ($retlen < $length) {
                    $toread = $length - $retlen;
                    $data .= fread($this->socket, $toread);
                    $retlen = strlen($data);
                }
                $response[] = $data;
                if ($this->debug) {
                    Log::debug(">>> [{$retlen}/{$length}] bytes read.");
                }
            }

            // If we get a !done, make a note of it.
            if ($data == "!done") {
                $receiveddone = true;
            }

            $status = stream_get_meta_data($this->socket);
            if ($length > 0 && $this->debug) {
                Log::debug(">>> [{$length}] {$data}");
            }

            if ((!$this->connected && !$status['unread_bytes']) || 
                ($this->connected && !$status['unread_bytes'] && $receiveddone)) {
                break;
            }
        }

        if ($parse) {
            $response = $this->parseResponse($response);
        }

        return $response;
    }

    /**
     * Parse response from RouterOS
     */
    private function parseResponse($response)
    {
        if (is_array($response)) {
            $parsed = array();
            $current = null;
            $singlevalue = null;
            
            foreach ($response as $x) {
                if (is_string($x)) {
                    if (in_array($x, array('!fatal','!re','!trap'))) {
                        if ($x == '!re') {
                            $current =& $parsed[];
                        } else {
                            $current =& $parsed[$x][];
                        }
                    } elseif ($x != '!done') {
                        $matches = array();
                        if (preg_match_all('/[^=]+/i', $x, $matches)) {
                            if ($matches[0][0] == 'ret') {
                                $singlevalue = $matches[0][1];
                            }
                            $current[$matches[0][0]] = (isset($matches[0][1]) ? $matches[0][1] : '');
                        }
                    }
                }
            }

            if (empty($parsed) && !is_null($singlevalue)) {
                $parsed = $singlevalue;
            }

            return $parsed;
        } else {
            return array();
        }
    }

    /**
     * Send command to RouterOS
     */
    public function comm($command, $arguments = array())
    {
        if (!$this->connected) {
            throw new Exception('Not connected to MikroTik');
        }

        $count = count($arguments);
        $this->write($command, !$arguments);
        $i = 0;
        
        if (is_array($arguments) && !empty($arguments)) {
            foreach ($arguments as $k => $v) {
                switch ($k[0]) {
                    case "?":
                        $el = "$k=$v";
                        break;
                    case "~":
                        $el = "$k~$v";
                        break;
                    default:
                        $el = "=$k=$v";
                        break;
                }

                $last = ($i++ == $count - 1);
                $this->write($el, $last);
            }
        }

        return $this->read();
    }

    /**
     * Get netwatch status
     */
    public function getNetwatchStatus()
    {
        try {
            return $this->comm('/tool/netwatch/print');
        } catch (Exception $e) {
            Log::error("Failed to get netwatch status: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Add host to netwatch
     */
    public function addToNetwatch($host, $comment = '')
    {
        try {
            $params = ['host' => $host];
            if ($comment) {
                $params['comment'] = $comment;
            }
            
            return $this->comm('/tool/netwatch/add', $params);
        } catch (Exception $e) {
            Log::error("Failed to add to netwatch: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove host from netwatch
     */
    public function removeFromNetwatch($id)
    {
        try {
            return $this->comm('/tool/netwatch/remove', ['numbers' => $id]);
        } catch (Exception $e) {
            Log::error("Failed to remove from netwatch: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get system resource information
     */
    public function getSystemResource()
    {
        try {
            return $this->comm('/system/resource/print');
        } catch (Exception $e) {
            Log::error("Failed to get system resource: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get netwatch entries and their status
     */
    public function getNetwatchEntries()
    {
        if (!$this->connected) {
            throw new Exception('Not connected to MikroTik');
        }

        try {
            Log::info('Getting netwatch entries from MikroTik');
            
            // Send command to get netwatch entries
            $this->write('/tool/netwatch/print');
            $response = $this->read(false);
            
            $entries = [];
            $currentEntry = [];
            
            foreach ($response as $line) {
                if (strpos($line, '=host=') === 0) {
                    // Save previous entry if exists
                    if (!empty($currentEntry)) {
                        $entries[] = $currentEntry;
                    }
                    // Start new entry
                    $currentEntry = ['host' => substr($line, 6)];
                } elseif (strpos($line, '=status=') === 0) {
                    $currentEntry['status'] = substr($line, 8);
                } elseif (strpos($line, '=timeout=') === 0) {
                    $currentEntry['timeout'] = substr($line, 9);
                } elseif (strpos($line, '=interval=') === 0) {
                    $currentEntry['interval'] = substr($line, 10);
                } elseif (strpos($line, '=since=') === 0) {
                    $currentEntry['since'] = substr($line, 7);
                } elseif (strpos($line, '=comment=') === 0) {
                    $currentEntry['comment'] = substr($line, 9);
                } elseif (strpos($line, '=.id=') === 0) {
                    $currentEntry['id'] = substr($line, 5);
                }
            }
            
            // Add last entry
            if (!empty($currentEntry)) {
                $entries[] = $currentEntry;
            }
            
            Log::info('Retrieved ' . count($entries) . ' netwatch entries');
            
            return $entries;
            
        } catch (Exception $e) {
            Log::error('MikroTik getNetwatchEntries error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Add netwatch entry
     */
    public function addNetwatchEntry($host, $timeout = '1s', $interval = '1m', $comment = '')
    {
        if (!$this->connected) {
            throw new Exception('Not connected to MikroTik');
        }

        try {
            Log::info("Adding netwatch entry for host: {$host}");
            
            $this->write('/tool/netwatch/add', false);
            $this->write('=host=' . $host, false);
            $this->write('=timeout=' . $timeout, false);
            $this->write('=interval=' . $interval, false);
            
            if (!empty($comment)) {
                $this->write('=comment=' . $comment, false);
            }
            
            $this->write('');
            $response = $this->read(false);
            
            Log::info("Netwatch entry added for {$host}");
            return $response;
            
        } catch (Exception $e) {
            Log::error("Error adding netwatch entry for {$host}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove netwatch entry by ID
     */
    public function removeNetwatchEntry($id)
    {
        if (!$this->connected) {
            throw new Exception('Not connected to MikroTik');
        }

        try {
            Log::info("Removing netwatch entry with ID: {$id}");
            
            $this->write('/tool/netwatch/remove', false);
            $this->write('=.id=' . $id);
            $response = $this->read(false);
            
            Log::info("Netwatch entry {$id} removed");
            return $response;
            
        } catch (Exception $e) {
            Log::error("Error removing netwatch entry {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get interface statistics
     */
    public function getInterfaceStats()
    {
        if (!$this->connected) {
            throw new Exception('Not connected to MikroTik');
        }

        try {
            $this->write('/interface/print');
            $response = $this->read(false);
            return $response;
        } catch (Exception $e) {
            Log::error('MikroTik getInterfaceStats error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if connected
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}
