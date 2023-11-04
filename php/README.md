# PHP HTTP Proxy Server
---

A simple HTTP Proxy Server made using PHP.

# How to Use
---

1. Install `PHP ^8.x`
   > Make sure that the PHP is included on PATH variable on your terminal
2. Change the current working directory
   ```
   cd /path/to/this-repo
   ```
3. Run this command on your console to start the application
   ```
   run
   ```
   or do it manually with this command
   ```
   php -S localhost:8000 index.php
   ```
   The application is accessible on `http://localhost:8000`
4. To make a proxy request, you can access any URLs like `http://localhost:8000/proxy/<REQUEST_URL>`
   
   ```
   <REQUEST_URL> = Any URL like https://mysite.com/some/resource
   ```
