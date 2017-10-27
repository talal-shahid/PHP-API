This project is built in CodeIgniter. Clone this project in a folder named vehicles. To run this project you need to have xampp installed on your system.

**Required changes**:  
In the xampp go inside the directory where xampp is installed i.e. "/path/to/CI/folder"
On  **_windows_** go to xampp\apache\conf\original and open **httpd.conf** file. (might be on different location on your system then find the file in the xampp directory)
On **_ubuntu_** go to root\opt\xampp\etc\ and open httpd.conf file. (might be on different location on your system then find the file in the xampp directory)

Search DocumentRoot in the httpd.conf file and  
On **_windows_** find and replace *DocumentRoot "c:/Apache24/htdocs"* with *DocumentRoot "c:/Apache24/htdocs/vehicles"*, *<Directory "c:/Apache24/htdocs/">* with DocumentRoot *<Directory "c:/Apache24/htdocs/vehicles">* and *Listen 80* with *Listen 8080*
On **_ubuntu_** find and replace *DocumentRoot "/opt/lampp/htdocs"* with 'DocumentRoot "/opt/lampp/htdocs/vehicles"', *<Directory "/opt/lampp/htdocs">* with DocumentRoot *<Directory "/opt/lampp/htdocs/vehicles">* and *Listen 80* with *Listen 8080*

