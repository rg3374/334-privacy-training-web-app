# 334-privacy-training-web-app

Training web app for COMS W4181: Security I at Columbia University.

- The app is deployed at <http://comp360.lovestoblog.com/>.
- The HTTPS version of the app is deployed at <https://comp360secure.lovestoblog.com/>. Note that free certificates are only valid for 90 days.

You can deploy the app on [InfinityFree](https://infinityfree.com/). Once you have an account, do the following:

1. Download the files in this repo and put them into your htdocs folder on InfinityFree. This is the server.

2. Add a `config.php` file to the htdocs directory with the following content:

   ```php
   <?php
   /* Database credentials */
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_NAME', 'users');

   /* Attempt to connect to MySQL database */
   $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

   // Check connection
   if($link === false){
       die("ERROR: Could not connect. " . mysqli_connect_error());
   }
   ?>
   ```

3. Look up the database credentials for `DB_SERVER`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_NAME` on InfinityFree and change them in your `config.php` according to yours.
