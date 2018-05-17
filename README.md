# php-clamav-scan

A simple PHP class for scanning files using a LOCAL ClamAV/clamd install either via a socket file or network socket (windows).  Can either be used on its own or dropped into a Codeigniter app as a library.  The main reason this was created was because the legacy php-clamav module is not compatible with PHP 7 and all other options I found were either not drop in compatible with CodeIgniter or were designed for use with Composer.  Such a trivial task/library should not be that difficult.

As previously mentioned, the files are scanned using their full path so clamd needs to be run on the web server.  Even when using a network socket, the class does not handle the streaming of files over the network in a client/server model(yet). I didn't have a need to do scans over the network but support could easily be added.  That being said, it requires the PHP Sockets module be installed.  If not already present you must install it based on your system.

To use this on Windows, you must use the network socket option.  As far as I'm aware the Windows version of Clamd does not support sockets.

### Example standalone usage:
* Include the class
```
require 'Clamav.php';
```
* Instantiate an object.
```
$clamav = new Clamav();
```
* If you need to customize the Clamd socket path you can do so
```
$clamav = new Clamav(array('clamd_sock' => '/path/to/clamd.sock'));
```
* Scan a file
```
if($clamav->scan("/path/to/file/to/scan.txt")) {
    echo "YAY, file is safe\n";
} else {
    echo "BOO, file is a virus.  Message: " . $clamav->getMessage() . "\n";
}
```

### Example CodeIgniter Usage
* Download the file into your application/libraries directory
* Load the library where desired
```
$this->load->library('clamav');
```
* If you need to customize the Clamd socket path you can do so
```
$this->load->library('clamav', array('clamd_sock' => '/path/to/clamd.sock'));
```
* Scan a file
```
if($this->clamav->scan("/path/to/file/to/scan.txt")) {
    echo "YAY, file is safe\n";
} else {
    echo "BOO, file is a virus.  Message: " . $clamav->getMessage() . "\n";
}
```

### Example Network Socket Usage
Everything is the same to use a network socket other than you must pass in at least the IP when instantiating the object.  You can also include an optional port if you are using something other than the standard 3310.
```
$clamav = new Clamav(array('clamd_sock' => '/path/to/clamd.sock'));
```
