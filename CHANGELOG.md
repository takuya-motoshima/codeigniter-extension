# Changelog

## [4.0.4] - 2021-7-29

- Added directory path validation rules.

    ```php
    $this->form_validation
      ->set_data([
        'dir1' => '/', // ok
        'dir2' => '/abc', // ok
        'dir3' => '/sab_', // ok
        'dir4' => '/abc/abc/', // ng
        'dir5' => '/sad/dfsd', // ok
        'dir6' => 'null', // ng
        'dir7' => '/dsf/dfsdf/dsfsf/sdfds', // ok
        'dir8' => '/e3r/343/8437', // ok
        'dir9' => '/4333/32#' // ng
      ])
      ->set_rules('dir1', 'dir1', 'directory_path')
      ->set_rules('dir2', 'dir2', 'directory_path')
      ->set_rules('dir3', 'dir3', 'directory_path')
      ->set_rules('dir4', 'dir4', 'directory_path')
      ->set_rules('dir5', 'dir5', 'directory_path')
      ->set_rules('dir6', 'dir6', 'directory_path')
      ->set_rules('dir7', 'dir7', 'directory_path')
      ->set_rules('dir8', 'dir8', 'directory_path')
      ->set_rules('dir9', 'dir9', 'directory_path');
    if ($this->form_validation->run() != false) {
      // put your code here
      Logger::print('There are no errors.');
    } else {
      Logger::print('Error message: ', $this->form_validation->error_array());
      // Output: Array
      //         (
      //            [dir4] => The dir4 field must contain a valid directory path.
      //            [dir6] => The dir6 field must contain a valid directory path.
      //            [dir9] => The dir9 field must contain a valid directory path.
      //          )
    }
    ```

## [4.0.3] - 2021-6-30

- Added key pair generation processing and public key OpenSSH encoding processing.

    Here is an example.  
    You can finetune the key generation (such as specifying the number of bits) using options. See [openssl_csr_new()](https://www.php.net/manual/en/function.openssl-csr-new.php) for more information about options.  

    ```php
    use \X\Util\Cipher;

    // Generate 4096bit long RSA key pair.
    Cipher::generateKeyPair($privKey, $pubKey, [
      'digest_alg' => 'sha512',
      'private_key_bits' => 4096,
      'private_key_type' => OPENSSL_KEYTYPE_RSA
    ]);

    // Debug private key.
    // Output: -----BEGIN PRIVATE KEY-----
    //         MIIJQgIBADANBgkqhkiG9w0BAQEFAASCCSwwggkoAgEAAoICAQCpvdXUNEfrA4T+
    //         ...
    //         -----END PRIVATE KEY-----
    echo 'Private key:'. PHP_EOL . $privKey;

    // Debug public key.
    // Output: -----BEGIN PUBLIC KEY-----
    //         MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAqb3V1DRH6wOE/oVhJWEo
    //         ...
    //         -----END PUBLIC KEY-----
    echo 'Public key:' . PHP_EOL. $pubKey;

    // OpenSSH encode the public key.
    // Output: ssh-rsa AAAAB3NzaC...
    $pubKey = Cipher::encodeOpenSshPublicKey($privKey);

    // Debug OpenSSH-encoded public key.
    echo 'OpenSSH-encoded public key:' . PHP_EOL . $pubKey;
    ```

## [4.0.2] - 2021-6-15

- Fixed a bug in the exists_by_id method of the \X\Model\Model class.

## [4.0.1] - 2021-5-25

- Added the ability to cache search query results in the model.
  
    Learn more about model caching <a href="https://www.codeigniter.com/userguide3/database/caching.html" target="_blank">here</a>.  

    This is an example of the setting (config/database.php).  

    ```php
    $db['default'] = array(
      'cachedir' => APPPATH . 'cache'
    );
    ```

    This is an example of caching.

    ```php
    // Cache the results of this search query.
    // The cache is saved in the directory specified in cachedir in "config/database.php".
    $this->UserModel->cache_on();

    // Find user.
    // If there is no cache yet, "QueryCacheTest+index/7f2b1a5f6e58f60d11f06c1635f55c17" will be created in the cache directory, and the contents will be as follows.
    // O:12:"CI_DB_result":8:{s:7:"conn_id";N;s:9:"result_id";N;s:12:"result_array";a:1:{i:0;a:2:{s:2:"id";s:1:"1";s:4:"name";s:5:"Robin";}}s:13:"result_object";a:1:{i:0;O:8:"stdClass":2:{s:2:"id";s:1:"1";s:4:"name";s:5:"Robin";}}s:20:"custom_result_object";a:0:{}s:11:"current_row";i:0;s:8:"num_rows";i:1;s:8:"row_data";N;}
    $user = $this->UserModel
      ->select('id, name')
      ->where('id', 1)
      ->get()
      ->row_array();

    // Disable the cache.
    $this->UserModel->cache_off();
    ```

    This is an example of deleting the cache.  
    The caching system saves your cache files to folders that correspond to the URI of the page you are viewing.  
    For example, if you are viewing a page at example.com/index.php/blog/comments, the caching system will put all cache files associated with it in a folder called blog+comments.  

    ```php
    $this->UserModel->cache_delete('blog', 'comments');
    ```

    This is an example of deleting all caches.

    ```php
    $this->UserModel->cache_delete_all();
    ```

## [4.0.0] - 2021-5-6

- Added dotenv reading process to sample application (./sample).

    ./sample/application/config/constants.php:  

    ```php
    // Directory with ".env" file
    const ENV_DIR = APPPATH . '..';
    ```

    ./sample/application/config/hooks.php:  

    ```php
    // pre_system callback.
    $hook['pre_system'] = function () {
      // Load environment variables.
      $dotenv = Dotenv\Dotenv::createImmutable(ENV_DIR);
      $dotenv->load();

      // Check for uncaught exceptions.
      set_exception_handler(function ($e) {
        Logger::error($e);
      });
    };
    ```

- Changed to pass the option of Amazon Rekognition (\X\Rekognition\Client) as an array.

    ```php
    use \X\Rekognition\Client;
    $client = new Client([
      'region'          => 'ap-northeast-1',
      'key'             => 'Your AWS access key ID',
      'secret'          => 'Your AWS secret access key',
      'connect_timeout' => 5,
      'debug'           => false
    ]);
    ```

    <table>
      <thead>
        <tr>
          <th>Option name</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>region</td>
          <td>AWS Region to connect to.The default is &quot;ap-northeast-1&quot;.</td>
        </tr>
        <tr>
          <th>key</th>
          <th>AWS access key ID.This is required.</th>
        </tr>
        <tr>
          <th>secret</th>
          <th>AWS secret access key.This is required.</th>
        </tr>
        <tr>
          <th>connect_timeout</th>
          <th>A float describing the number of seconds to wait while trying to connect to a server. The default is 5 (seconds).</th>
        </tr>
        <tr>
          <th>debug</th>
          <th>Specify true to output the result of Rekognition to the debug log.The default is false and no debug log is output.</th>
        </tr>
      </tbody>
    </table>

## [3.9.9] - 2021-4-15

- Fixed README typo.

## [3.9.8] - 2021-4-15

- Added form validation rule according to the [email address proposed in HTML5](https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address).

## [3.9.7] - 2021-4-9

- Added the process to automatically set $\_SESSION to the template variable session.

## [3.9.6] - 2021-4-8

- Fix the issue text not recognized after '&' in case of PUT request.

## [3.9.5] - 2021-4-8

- Refactor sample application and skeleton.

## [3.9.4] - 2021-4-7

- Fix create-project error.

## [3.9.3] - 2021-3-26

- Added a function to the Date helper that returns the date of the specified month.

    ```php
    use \X\Util\DateHelper;

    // Get the date of March 2021.
    DateHelper::getDaysInMonth(2021, 3, 'Y-m-d');
    // ["2021-03-01", "2021-03-02", "2021-03-03", "2021-03-04", "2021-03-05", "2021-03-06", "2021-03-07", "2021-03-08", "2021-03-09", "2021-03-10", "2021-03-11", "2021-03-12", "2021-03-13", "2021-03-14", "2021-03-15", "2021-03-16", "2021-03-17", "2021-03-18", "2021-03-19", "2021-03-20", "2021-03-21", "2021-03-22", "2021-03-23", "2021-03-24", "2021-03-25", "2021-03-26", "2021-03-27", "2021-03-28", "2021-03-29", "2021-03-30", "2021-03-31"]
    ```

## [3.9.2] - 2021-3-24

- Resolved an error where the return type of the email function of the email subclass (/X/Util/Email) did not match the definition.

## [3.9.1] - 2021-3-15

- Added a method that returns a table string of an array.

    ```php
    use \X\Util\ArrayHelper;
    
    $arr = [
      [
        'firstname' => 'John',
        'lastname' => 'Mathew',
        'email' => 'John.Mathew@xyz.com'
      ],
      [
        'firstname' => 'Jim',
        'lastname' => 'Parker',
        'email' => 'Jim.Parker@xyz.com'
      ]
    ];
    echo ArrayHelper::toTable($arr);
    ┌───────────┬──────────┬─────────────────────┐
    │ FIRSTNAME │ LASTNAME │        EMAIL        │
    ├───────────┼──────────┼─────────────────────┤
    │ John      │ Mathew   │ John.Mathew@xyz.com │
    │ Jim       │ Parker   │ Jim.Parker@xyz.com  │
    └───────────┴──────────┴─────────────────────┘
    ```

## [3.9.0] - 2021-3-15

- Added a log function that does not output path information.

    ```php
    use \X\Util\Logger;

    Logger::printHidepath('I told you so');
    ```

## [3.8.9] - 2021-2-24

- Added batch exclusive control sample program for file lock and advisory lock to the sample application.
    
    Description of the added file.  

    <table>
      <thead>
        <tr>
          <th>File</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>sampleapp/application/controllers/batch/RunMultipleBatch.php</td>
          <td>An entry point that launches multiple batches at the same time.</td>
        </tr>
        <tr>
          <td>sampleapp/application/controllers/batch/FileLockBatch.php</td>
          <td>Batch with file locking.This is called from RunMultipleBatch.</td>
        </tr>
        <tr>
          <td>sampleapp/application/controllers/batch/AdvisoryLockBatch.php</td>
          <td>Batch with advisory lock.This is called from RunMultipleBatch.</td>
        </tr>
      </tbody>
    </table>

    How to do it.  

    Run a batch that prohibits multiple launches using file locks.  

    ```sh
    cd /var/www/html/sampleapp;
    CI_ENV=development php public/index.php batch/runMultipleBatch/run/filelock;
    ```

    Run a batch that prohibits multiple launches using advisory locks.  

    ```sh
    cd /var/www/html/sampleapp;
    CI_ENV=development php public/index.php batch/runMultipleBatch/run/advisorylock;
    ```

## [3.8.8] - 2021-2-23

- Organized readme and added batch lock test program.

## [3.8.7] - 2021-2-19

- Added a method to the file helper that returns a file size with units.

    ```php
    use \X\Util\FileHelper;

    FileHelper::humanFilesize('/var/somefile.txt', 0);// 12B
    FileHelper::humanFilesize('/var/somefile.txt', 4);// 1.1498GB
    FileHelper::humanFilesize('/var/somefile.txt', 1);// 117.7MB
    FileHelper::humanFilesize('/var/somefile.txt', 5);// 11.22833TB
    FileHelper::humanFilesize('/var/somefile.txt', 3);// 1.177MB
    FileHelper::humanFilesize('/var/somefile.txt');// 120.56KB
    ```

## [3.8.6] - 2021-2-18

- Fixed changelog typos.

## [3.8.5] - 2021-2-18

- Added HTTP / CLI access control to controller public method annotation.

    Step 1: Add access control to the hook(application/config/hooks.php).  

    ```php
    use \X\Annotation\AnnotationReader;

    // Add access control to hooks.
    $hook['post_controller_constructor'] = function() {
      $ci =& get_instance();

      // Get access from annotations.
      $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);

      // Whether you are logged in.
      $islogin = !empty($_SESSION['user']);

      // Whether it is HTTP access.
      $ishttp = !is_cli();

      // Request URL.
      $requesturl = $ci->router->directory . $ci->router->class . '/' . $ci->router->method;

      // When accessed by HTTP.
      if ($ishttp) {
        // Returns an error if HTTP access is not allowed.
        if (!$accessibility->allow_http) throw new \RuntimeException('HTTP access is not allowed.');

        // When the logged-in user calls a request that only the log-off user can access, redirect to the dashboard.
        // It also redirects to the login page when the log-off user calls a request that only the logged-in user can access.
        if ($islogin && !$accessibility->allow_login) redirect('/dashboard');
        else if (!$islogin && !$accessibility->allow_logoff) redirect('/login');
      } else {
        // When executed with CLI.
      }
    };
    ```

    Step 2: Define annotations for public methods on each controller.  

    ```php
    use \X\Annotation\Access;

    /**
     * Only log-off users can access it.
     *
     * @Access(allow_login=false, allow_logoff=true)
     */
    public function login() {}

    /**
     * Only logged-in users can access it.
     *
     * @Access(allow_login=true, allow_logoff=false)
     */
    public function dashboard() {}

    /**
     * It can only be done with the CLI.
     *
     * @Access(allow_http=false)
     */
    public function batch() {}
    ```


## [3.8.4] - 2021-2-17

- Changed to return SES mail sending result object.(\X\Util\AmazonSesClient).

## [3.8.3] - 2021-2-11

- Added form validation class.The reason I added it is that I want to validate it with the model(\X\Util\Validation).

    Define the SES "access key" and "secret" in sampleapp/.env.  

    ```php
    use \X\Util\AmazonSesClient;

    // SES client.
    $ses = new AmazonSesClient([
      'credentials' => [
        'key' => $_ENV['SES_ACCESS_KEY'],
        'secret' => $_ENV['SES_SECRET_KEY'],
      ],
      'configuration' => $_ENV['SES_CONFIGURATION'],
      'region' => $_ENV['SES_REGION']
    ]);

    // Send email.
    $result = $ses
      ->from('from@example.com')
      ->to('to@example.com')
      ->subject('Test email')
      ->message('Hello, World!')
      ->send();
    $messageId = $result->get('MessageId');
    Logger::print("Email sent! Message ID: $messageId");
    ```

## [3.8.2] - 2021-2-10

- Fixed README.

## [3.8.1] - 2021-2-10

- Added an empty judgment method for characters trimmed with left and right spaces(\X\Util\StringHelper).

    ```php
    use \X\Util\StringHelper;

    StringHelper::empty(' ');// true
    StringHelper::empty(1);// false
    StringHelper::empty('');// true
    StringHelper::empty(0);// true
    StringHelper::empty('0');// true
    StringHelper::empty(null);// true
    StringHelper::empty([]);// true
    ```

## [3.8.0] - 2021-2-10

- Added nginxn configuration sample file to REAME.

## [3.7.9] - 2021-2-9

- Added the following rules to form validation.

    <table>
      <thead>
        <tr>
          <th>Rule</th>
          <th>Parameter</th>
          <th>Description</th>
          <th>Example</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>datetime</td>
          <td>Yes</td>
          <td>Returns FALSE if the form element does not contain a valid Datetime.</td>
          <td>datetime[Y-m-d H:i:s]</td>
        </tr>
        <tr>
          <td>hostname</td>
          <td>No</td>
          <td>Returns FALSE if the form element does not contain a valid host name.</td>
          <td></td>
        </tr>
        <tr>
          <td>ipaddress</td>
          <td>No</td>
          <td>Returns FALSE if the form element does not contain a valid IP address.</td>
          <td></td>
        </tr>
        <tr>
          <td>hostname_or_ipaddress</td>
          <td>No</td>
          <td>Returns FALSE if the form element does not contain a valid host name or IP address.</td>
          <td></td>
        </tr>
        <tr>
          <td>unix_username</td>
          <td>No</td>
          <td>Returns FALSE if the form element does not contain a valid UNIX user name.</td>
          <td></td>
        </tr>
        <tr>
          <td>port</td>
          <td>No</td>
          <td>Returns FALSE if the form element does not contain a valid port number.</td>
          <td></td>
        </tr>
      </tbody>
    </table>


    ```php
    $this->form_validation
      ->set_data([
        // Datetime custom validation.
        'datetime' => '2021-02-03 17:46:00',// ok

        // Host name custom validation.
        'hostname1' => 'external.asd1230-123.asd_internal.asd.gm-_ail.com',// ok
        'hostname2' => 'domain.com',// ok
        'hostname3' => 'example.domain.com',// ok
        'hostname4' => 'example.domain-hyphen.com',// ok
        'hostname5' => 'www.domain.com',// ok
        'hostname6' => 'example.museum',// ok
        'hostname7' => 'http://example.com',// ng
        'hostname8' => 'subdomain.-example.com',// ng
        'hostname9' => 'example.com/parameter',// ng
        'hostname10' => 'example.com?anything',// ng

        // IP address custom validation.
        'ipaddress1' => '000.0000.00.00',// ng
        'ipaddress2' => '192.168.1.1',// ok
        'ipaddress3' => '912.456.123.123',// ng

        // Host name or ip address custom validation.
        'hostname_or_ipaddress1' => 'external.asd1230-123.asd_internal.asd.gm-_ail.com',// ok
        'hostname_or_ipaddress2' => 'domain.com',// ok
        'hostname_or_ipaddress3' => 'example.domain.com',// ok
        'hostname_or_ipaddress4' => 'example.domain-hyphen.com',// ok
        'hostname_or_ipaddress5' => 'www.domain.com',// ok
        'hostname_or_ipaddress6' => 'example.museum',// ok
        'hostname_or_ipaddress7' => 'http://example.com',// ng
        'hostname_or_ipaddress8' => 'subdomain.-example.com',// ng
        'hostname_or_ipaddress9' => 'example.com/parameter',// ng
        'hostname_or_ipaddress10' => 'example.com?anything',// ng
        'hostname_or_ipaddress11' => '000.0000.00.00',// ng
        'hostname_or_ipaddress12' => '192.168.1.1',// ok
        'hostname_or_ipaddress13' => '912.456.123.123',// ng

        // UNix user name custom validation.
        'unix_username1' => 'abcd',// ok
        'unix_username2' => 'a123',// ok
        'unix_username3' => 'abc-',// ok
        'unix_username4' => 'a-bc',// ok
        'unix_username5' => 'abc$',// ok
        'unix_username7' => 'a-b$',// ok
        'unix_username8' => '1234',// ng
        'unix_username9' => '1abc',// ng
        'unix_username10' => '-abc',// ng
        'unix_username11' => '$abc',// ng
        'unix_username12' => 'a$bc',// ng

        // Port number custom validation.
        'port1' => '-1',// ng
        'port2' => '0',// ok
        'port3' => '1',// ok
        'port4' => '',// ok
        'port5' => '65534',// ok
        'port6' => '65535',// ok
        'port7' => '65536',// ng
      ])
      ->set_rules('datetime', 'datetime', 'required|datetime[Y-m-d H:i:s]')
      ->set_rules('hostname1', 'hostname1', 'hostname')
      ->set_rules('hostname2', 'hostname2', 'hostname')
      ->set_rules('hostname3', 'hostname3', 'hostname')
      ->set_rules('hostname4', 'hostname4', 'hostname')
      ->set_rules('hostname5', 'hostname5', 'hostname')
      ->set_rules('hostname6', 'hostname6', 'hostname')
      ->set_rules('hostname7', 'hostname7', 'hostname')
      ->set_rules('hostname8', 'hostname8', 'hostname')
      ->set_rules('hostname9', 'hostname9', 'hostname')
      ->set_rules('hostname10', 'hostname10', 'hostname')
      ->set_rules('ipaddress1', 'ipaddress1', 'ipaddress')
      ->set_rules('ipaddress2', 'ipaddress2', 'ipaddress')
      ->set_rules('ipaddress3', 'ipaddress3', 'ipaddress')
      ->set_rules('hostname_or_ipaddress1', 'hostname_or_ipaddress1', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress2', 'hostname_or_ipaddress2', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress3', 'hostname_or_ipaddress3', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress4', 'hostname_or_ipaddress4', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress5', 'hostname_or_ipaddress5', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress6', 'hostname_or_ipaddress6', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress7', 'hostname_or_ipaddress7', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress8', 'hostname_or_ipaddress8', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress9', 'hostname_or_ipaddress9', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress10', 'hostname_or_ipaddress10', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress11', 'hostname_or_ipaddress11', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress12', 'hostname_or_ipaddress12', 'hostname_or_ipaddress')
      ->set_rules('hostname_or_ipaddress13', 'hostname_or_ipaddress13', 'hostname_or_ipaddress')
      ->set_rules('unix_username1', 'unix_username1', 'unix_username')
      ->set_rules('unix_username2', 'unix_username2', 'unix_username')
      ->set_rules('unix_username3', 'unix_username3', 'unix_username')
      ->set_rules('unix_username4', 'unix_username4', 'unix_username')
      ->set_rules('unix_username5', 'unix_username5', 'unix_username')
      ->set_rules('unix_username6', 'unix_username6', 'unix_username')
      ->set_rules('unix_username7', 'unix_username7', 'unix_username')
      ->set_rules('unix_username8', 'unix_username8', 'unix_username')
      ->set_rules('unix_username9', 'unix_username9', 'unix_username')
      ->set_rules('unix_username10', 'unix_username10', 'unix_username')
      ->set_rules('unix_username11', 'unix_username11', 'unix_username')
      ->set_rules('unix_username12', 'unix_username12', 'unix_username')
      ->set_rules('port1', 'port1', 'port')
      ->set_rules('port2', 'port2', 'port')
      ->set_rules('port3', 'port3', 'port')
      ->set_rules('port4', 'port4', 'port')
      ->set_rules('port5', 'port5', 'port')
      ->set_rules('port6', 'port6', 'port')
      ->set_rules('port7', 'port7', 'port');
    if ($this->form_validation->run() != false) {
      // put your code here
      Logger::print('There are no errors.');
    } else {
      Logger::print('Error message: ', $this->form_validation->error_array());
    }
    ```

## [3.7.8] - 2021-2-6

- Added a method to group associative arrays by key to ArrayHelper.(\X\Util\ArrayHelper).

    ```php
    use \X\Util\ArrayHelper;

    $foods = [
      ['name' => 'Apple',       'category' => 'fruits'],
      ['name' => 'Strawberry',  'category' => 'fruits'],
      ['name' => 'Tomato',      'category' => 'vegetables'],
      ['name' => 'Carot',       'category' => 'vegetables'],
      ['name' => 'water',       'category' => 'drink'],
      ['name' => 'beer',        'category' => 'drink'],
    ];

    ArrayHelper::grouping($foods, 'category');
    // [
    //   'fruits' => [
    //     ['name' => 'Apple',       'category' => 'fruits'],
    //     ['name' => 'Strawberry',  'category' => 'fruits']
    //   ],
    //   'vegetables' => [
    //     ['name' => 'Tomato',      'category' => 'vegetables'],
    //     ['name' => 'Carot',       'category' => 'vegetables']
    //   ],
    //   'drink' => [
    //     ['name' => 'water',       'category' => 'drink'],
    //     ['name' => 'beer',        'category' => 'drink']
    //   ]
    // ]
    ```

## [3.7.7] - 2021-2-3

- Create a form validation class and add a datetime validation method(\X\Library\FormValidation).

    Override form validation.  
    application/libraries/AppForm_validation.php:  

    ```php
    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    use X\Library\FormValidation;

    class AppForm_validation extends FormValidation {}
    ```

    This is an example of Datetime verification.  

    ```php
    $this->form_validation
      ->set_data(['datetime' => '2021-02-03 17:46:00'])
      ->set_rules('datetime', 'datetime', 'required|datetime[Y-m-d H:i:s]');
    if ($this->form_validation->run() != false) {
      // put your code here
    } else {
      echo validation_errors();
    }
    ```

## [3.7.6] - 2021-1-27

- Delete debug log.

## [3.7.5] - 2021-1-22

- Fixed a bug that Annotation could not be read.

## [3.7.4] - 2021-1-22

- Change image resizing features(\X\Util\ImageHelper).

    ```php
    use \X\Util\ImageHelper;

    // resize only the width of the image
    ImageHelper::resize('img.jpg', 'thumb.jpg', 100, null, false);

    // resize only the height of the image
    ImageHelper::resize('img.jpg', 'thumb.jpg', null, 100, false);

    // resize the image to a width of 100 and constrain aspect ratio (auto height)
    ImageHelper::resize('img.jpg', 'thumb.jpg', 100, null, true);

    // resize the image to a height of 100 and constrain aspect ratio (auto width)
    ImageHelper::resize('img.jpg', 'thumb.jpg', null, 100, true);
    ```

## [3.7.3] - 2020-12-25

- Added search options to file search(\X\Util\FileHelper).

    ```php
    use \X\Util\FileHelper;

    // When searching only image files.
    FileHelper::find('/img/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    ```

## [3.7.2] - 2020-11-17

- Remove unused paginate method from Model class.

## [3.7.1] - 2020-11-17

- Fixed a bug in the project creation command.

## [3.7.0] - 2020-11-17

- Fix skeleton.

## [3.6.9] - 2020-11-17

- Fix README.md.

## [3.6.8] - 2020-11-17

- Fix project creation process.

## [3.6.7] - 2020-11-16

- Prepend a slash to the PID of the log(\X\Util\Logger).

    Here is an example of a log.

    ```php
    DEBUG - 2020-11-16 10:04:38 --> #7567 application/controllers/Sample.php(20):Message here.
    ```

## [3.6.6] - 2020-11-10

- Add PID to log message(\X\Util\Logger).

## [3.6.5] - 2020-11-9

- Fixed to ignore directory creation error (\X\Util\FileHelper::makeDirectory).

## [3.6.4] - 2020-11-6

- Remove class and function names from the log(\X\Util\Logger).

## [3.6.3] - 2020-11-2

- Changed to be able to specify multiple Amazon SES email destinations in an array.(\X\Util\AmazonSesClient)

    ```php
    use \X\Util\AmazonSesClient;

    $client = new AmazonSesClient([
      'credentials' => [
        'key' => $_ENV['AMAZON_SES_ACCESS_KEY'],
        'secret' => $_ENV['AMAZON_SES_SECRET_KEY']
      ],
      'configuration' => $_ENV['AMAZON_SES_CONFIGURATION'],
      'region' => 'us-west-2',
    ]);

    $client
      ->from('me@example.com')
      ->to([
        'foo@example.jp',
        'bar@example.jp',
      ])
      ->subject('Test email')
      ->message('Hello, World.')
      ->send();
    ```

## [3.6.2] - 2020-10-29

- Fixed OpenSSL encryption/decryption method.

    ```php
    use \X\Util\Cipher;

    // Get the initialization vector. This should be changed every time to make it difficult to predict.
    $iv = Cipher::generateInitialVector();

    // Plaintext.
    $plaintext = 'Hello, World.';

    // Encrypted password.
    $password = 'password';
    // Encrypt.
    $encrypted = Cipher::encrypt($plaintext, $password, $iv);// UHLY5PckT7Da02e42g==

    // Decrypt.
    $decrypted = Cipher::decrypt($encrypted, $password, $iv);// Hello, World.
    ```

## [3.6.1] - 2020-10-23

- Added IP utility class(\X\Util\IpUtils). And since \X\Util\HttpSecurity has moved to IPUtils, I deleted it.

    ```php
    // Get client ip.
    IpUtils::getClientIpFromXFF();//  202.210.220.78

    // IP range check.
    // 202.210.220.64/28
    IpUtils::inRange('202.210.220.63', '202.210.220.64/28');// false
    IpUtils::inRange('202.210.220.64', '202.210.220.64/28');// true
    IpUtils::inRange('202.210.220.65', '202.210.220.64/28');// true
    IpUtils::inRange('202.210.220.78', '202.210.220.64/28');// true
    IpUtils::inRange('202.210.220.79', '202.210.220.64/28');// true
    IpUtils::inRange('202.210.220.80', '202.210.220.64/28');// false

    // 192.168.1.0/24
    IpUtils::inRange('192.168.0.255', '192.168.1.0/24'); // false
    IpUtils::inRange('192.168.1.0', '192.168.1.0/24'); // true
    IpUtils::inRange('192.168.1.1', '192.168.1.0/24'); // true
    IpUtils::inRange('192.168.1.244', '192.168.1.0/24'); // true
    IpUtils::inRange('192.168.1.255', '192.168.1.0/24'); // true
    IpUtils::inRange('192.168.2.0', '192.168.1.0/24'); // false

    // 118.238.251.130
    IpUtils::inRange('118.238.251.129', '118.238.251.130'); // false
    IpUtils::inRange('118.238.251.130', '118.238.251.130'); // true
    IpUtils::inRange('118.238.251.131', '118.238.251.130'); // false

    // 118.238.251.130/32
    IpUtils::inRange('118.238.251.129', '118.238.251.130/32'); // false
    IpUtils::inRange('118.238.251.130', '118.238.251.130/32'); // true
    IpUtils::inRange('118.238.251.131', '118.238.251.130/32'); // false

    // 2001:4860:4860::8888/32
    IpUtils::inRange('2001:4859:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', '2001:4860:4860::8888/32');// false
    IpUtils::inRange('2001:4860:4860:0000:0000:0000:0000:8888', '2001:4860:4860::8888/32');// true
    IpUtils::inRange('2001:4860:4860:0000:0000:0000:0000:8889', '2001:4860:4860::8888/32');// true
    IpUtils::inRange('2001:4860:FFFF:FFFF:FFFF:FFFF:FFFF:FFFE', '2001:4860:4860::8888/32');// true
    IpUtils::inRange('2001:4860:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', '2001:4860:4860::8888/32');// true
    IpUtils::inRange('2001:4861:0000:0000:0000:0000:0000:0000', '2001:4860:4860::8888/32');// false

    // 2404:7a81:b0a0:9100::/64
    IpUtils::inRange('2404:7A81:B0A0:90FF:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// false
    IpUtils::inRange('2404:7A81:B0A0:9100:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:0000:0000:0000:0001', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:A888:5EE2:EA92:B618', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:D03:959E:7F47:9B77', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFE', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFF', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9101:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// false

    // IPv4 format check.
    IpUtils::isIPv4('234.192.0.2');// true
    IpUtils::isIPv4('234.198.51.100');// true
    IpUtils::isIPv4('234.203.0.113');// true
    IpUtils::isIPv4('0000:0000:0000:0000:0000:ffff:7f00:0001');// false
    IpUtils::isIPv4('::1');// false

    // IPv6 format check.
    IpUtils::isIPv6('234.192.0.2');// false
    IpUtils::isIPv6('234.198.51.100');// false
    IpUtils::isIPv6('234.203.0.113');// false
    IpUtils::isIPv6('0000:0000:0000:0000:0000:ffff:7f00:0001');// true
    IpUtils::isIPv6('::1');// true

    ```

## [3.6.0] - 2020-10-20

- Add a time stamp to the log message output to the CLI(\X\Util\Logger#printWithoutPath).

## [3.5.9] - 2020-10-19

- Added log output method without file path(\X\Util\Logger#printWithoutPath).

## [3.5.8] - 2020-10-16

- Fixed a bug that IP acquisition fails when XFF is empty(\X\Util\HttpSecurity#getIpFromXFF).

## [3.5.7] - 2020-10-15

- Added method to get IP from XFF(\X\Util\HttpSecurity#getIpFromXFF).

## [3.5.5] - 2020-6-4

- Added a method to AA that returns the size of all files in a directory.

    ```php
    // Returns the total size of all files in a directory
    FileHelper::getDirectorySize('/var/log');

    // Returns the total size of all files in multiple directories
    FileHelper::getDirectorySize([ '/var/log/php-fpm' '/var/log/nginx' ]);
    ```

## [3.5.4] - 2020-6-4

- Add encryption key to the parameter of hash conversion method

    ```php
    use \X\Util\Cipher;

    $password = 'password';
    Cipher::encode_sha256('tiger', $password);// c30675022a22cf76c622b7982e8894dd5ac03c4bb2f17ac13a5da01a76acbe6c
    ```

## [3.5.3] - 2020-5-20

- Added a process to log out a user who is logged in with the same ID on another device when logging in

    config/hooks.php:  

    ```php
    use \X\Annotation\AnnotationReader;

    $hook['post_controller_constructor'] = function() {
      isset($_SESSION['user']) ? handlingLoggedIn() : handlingLogOff();
    };

    /**
     * Process for logged-in user
     */
    function handlingLoggedIn() {
      $ci =& get_instance();
      // If it is BAN, call the logoff process
      $ci->load->model('UserService');
      if ($ci->UserService->isBanUser(session_id())) {
        // Sign out
        $ci->UserService->signout();
        // Set ban message display flag
        $ci->load->helper('cookie');
        set_cookie('show_ban_message', true, 10);
        // To logoff processing
        return handlingLogOff();
      }
      // Check if the request URL has access privileges
      $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
      if (!$accessibility->allow_login || ($accessibility->allow_role && $accessibility->allow_role !== $session['role'])) {
        // In case of access prohibition action, redirect to the dashboard page
        redirect('/dashboard');
      }
    }

    /**
     * Process for logoff user
     */
    function handlingLogOff() {
      $ci =& get_instance();
      // Check if the request URL has access privileges
      $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
      if (!$accessibility->allow_logoff) {
        // In case of access prohibition action, redirect to the login page
        redirect('/signin');
      }
    }
    ```

    models/UserService.php:  

    ```php
    class UserService extends \AppModel {

      protected $model = [
        'UserModel',
        'SessionModel'
      ];

      public function signin(string $username, string $password): bool {
        // Find data matching ID and password
        $user = $this->UserModel->getUserByUsernameAndPassword($username, $password);
        if (empty($user)) {
          return false;
        }
        unset($user['password']);
        // Change the BAN flag of other logged-in users to on
        $this->SessionModel->updateSessionBanFlagOn($username, session_id());
        // Store login user data in session
        $_SESSION['user'] = $user;
        return true;
      }

      public function signout() {
        session_destroy();
      }

      public function isBanUser(string $sessionId) {
        return $this->SessionModel->isBanById(session_id());
      }
    }
    ```

    models/SessionModel.php:  

    ```php
    class SessionModel extends \AppModel {

      const TABLE = 'session';

      public function updateSessionBanFlagOn($username, string $id) {
        parent
          ::set('ban', 1)
          ::where('username', $username)
          ::where('id !=', $id)
          ->update();
      }

      public function isBanById(string $id): bool {
        return parent
          ::where('id', $id)
          ::where('ban', 1)
          ::count_all_results() > 0;
      }
    }
    ```

    controllers/api/User.php:  

    ```php
    use \X\Annotation\Access;
    use const \X\Constant\HTTP_BAD_REQUEST;
    use const \X\Constant\HTTP_CREATED;
    use const \X\Constant\HTTP_NO_CONTENT;

    class User extends AppController {

      protected $model = [
        'UserService',
        'UserModel',
      ];

      /**
       * @Access(allow_login=false, allow_logoff=true)
       */
      public function signin() {
        try {
          $this->form_validation
            ->set_data($this->input->post())
            ->set_rules('username', 'username', 'required|max_length[30]')
            ->set_rules('password', 'password', 'required|max_length[30]');
          if (!$this->form_validation->run()) {
            return parent::error(print_r($this->form_validation->error_array(), true), HTTP_BAD_REQUEST);
          }
          $result = $this->UserService->signin($this->input->post('username'), $this->input->post('password'));
          parent
            ::set($result)
            ::json();
        } catch (\Throwable $e) {
          parent::error($e->getMessage(), HTTP_BAD_REQUEST);
        }
      }

      /**
       * @Access(allow_login=true, allow_logoff=false)
       */
      public function signout() {
        try {
          $this->UserService->signout();
          redirect('/signin');
        } catch (\Throwable $e) {
          parent::error($e->getMessage(), HTTP_BAD_REQUEST);
        }
      }
    }
    ```

    public/assets/signin.js:

    ```js
    (() => {
      /**
       * Set up login form
       *
       * @return {void}
       */
      function setupLoginForm() {
        const validator = $('#signupForm').validate({
          submitHandler: async (form, event) => {
            event.preventDefault();
            const response = await $.ajax({
              url: 'api/user/signin',
              type: 'POST',
              data: new FormData(form),
              processData: false,
              contentType: false
            });
            console.log('response=', response);
            if (!response) {
              return void validator.showErrors({ username: 'Wrong username or password' });
            }
            location.href = '/';
          }
        });
      }

      // Set up login form
      setupLoginForm();

      // Display BAN message
      if (Cookies.get('show_ban_message')) {
        Cookies.remove('show_ban_message')
        alert('Logged out because it was logged in on another terminal.');
      }
    })();
    ````

## [3.5.0] - 2020-5-19

- Fixed a bug that DB class does not inherit \X\Database\QueryBuilder when making session DB

## [3.4.8] - 2020-4-28

- Make the IP range check method of "\X\Util\HttpSecurity" class do correct check when subnet mask is 32.

    ```php
    use \X\Util\HttpSecurity;

    HttpSecurity::isAllowIp('202.210.220.64',   '202.210.220.64/28');// false
    HttpSecurity::isAllowIp('202.210.220.65',   '202.210.220.64/28');// true
    HttpSecurity::isAllowIp('202.210.220.66',   '202.210.220.64/28');// true
    HttpSecurity::isAllowIp('202.210.220.78',   '202.210.220.64/28');// true
    HttpSecurity::isAllowIp('202.210.220.79',   '202.210.220.64/28');// false
    HttpSecurity::isAllowIp('202.210.220.80',   '202.210.220.64/28');// false
    HttpSecurity::isAllowIp('192.168.0.0',      '192.168.1.0/24');// false
    HttpSecurity::isAllowIp('192.168.1.0',      '192.168.1.0/24');// false
    HttpSecurity::isAllowIp('192.168.1.1',      '192.168.1.0/24');// true
    HttpSecurity::isAllowIp('192.168.1.254',    '192.168.1.0/24');// true
    HttpSecurity::isAllowIp('192.168.1.255',    '192.168.1.0/24');// false
    HttpSecurity::isAllowIp('118.238.251.130',  '118.238.251.130');// true
    HttpSecurity::isAllowIp('118.238.251.131',  '118.238.251.130');// false
    HttpSecurity::isAllowIp('118.238.251.130',  '118.238.251.130/32');// true
    HttpSecurity::isAllowIp('118.238.251.131',  '118.238.251.130/32');// false
    ```

## [3.4.7] - 2020-4-27

- Added feature to face detector to find multiple faces from collection

    ```php
    use \X\Rekognition\Client;
    $client = new Client('Your AWS access key ID', 'Your AWS secret access key');

    // Find a single face in a collection
    $collectionId = '8e5e6f4e99f380b';
    $faceImage = 'data:image/png;base64,...';
    $threshold = 80;
    $detection = $client->getFaceFromCollectionByImage($collectionId, $faceImage, $threshold);
    // Array
    // (
    //     [faceId] => b3fcb4ed-5891-4bc3-bb1c-6b3f90f159d1
    //     [similarity] => 99.7
    // )

    // Find multiple faces in a collection
    $collectionId = '8e5e6f4e99f380b';
    $faceImage = 'data:image/png;base64,...';
    $threshold = 80;
    $maxFaces = 4096;
    $detections = $client->getMultipleFacesFromCollectionByImage($collectionId, $faceImage, $threshold, $maxFaces);
    // Array
    // (
    //     [0] => Array
    //         (
    //             [faceId] => e12c40d2-445d-4b12-bfad-db46a2f611dc
    //             [similarity] => 99.7
    //         )
    //
    //     [1] => Array
    //         (
    //             [faceId] => b3fcb4ed-5891-4bc3-bb1c-6b3f90f159d1
    //             [similarity] => 99.3
    //         )
    //
    // )
    ```

## [3.4.6] - 2020-4-23

- Added a feature to add arbitrary columns to the session table

    Set the columns you want to add to the session table in "application/confi /config.php".
    The example adds the username column to the session table.

    <p class="alert">Be sure to allow NULL for your own extra columns. This is because the session created when you are not logged in has no extra column values.</p>

    ```php
    // Session table additional column.
    // A session field with the same name as the additional column name is saved in the table.
    $config['sess_table_additional_columns'] = ['username'];
    ```

    Create a session table.

    ```sql
    CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `id` varchar(128) NOT NULL,
        `username` varchar(255) DEFAULT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
    );
    ```

    Create a session database class (application/libraries/Session/drivers/AppSession_database_driver.php) in your application.

    ```php
    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    use X\Library\SessionDatabaseDriver;

    class AppSession_database_driver extends SessionDatabaseDriver {}
    ```

    The user name of the logged-in user will be added to the session table.

    ```sql
    SELECT * FROM session WHERE username='admin'\G;

    *************************** 1. row ***************************
            id: 78g8c230pe8onb93jkpbbkatcii3h7ss
      username: admin
    ip_address: 172.31.38.40
     timestamp: 1587646280
          data: ...
    1 rows in set (0.000 sec)
    ```

## [3.4.5] - 2020-4-10

- Changed to return an empty string when there is no key value to get from the config with "\X\Utils\Loader::config()".

## [3.4.2] - 2020-3-16

- Added setting of template cache in application config (application/config/config.php).

    You can configure the template cache in "application/config/config.php".

    ```php
    /*
    |--------------------------------------------------------------------------
    | Template settings
    |--------------------------------------------------------------------------
    |
    | 'csrf_token_name' = Directory path to store template cache. Set FALSE when not caching. The default is FALSE.
    */
    $config['cache_templates'] = false;
    ```

## [3.3.9] - 2020-3-16

- Added client class that summarizes face detection processing. Remove old face detection class.

    ```php
    use \X\Rekognition\Client;
    $client = new Client('Your AWS access key ID', 'Your AWS secret access key');

    // Create new face collection
    $collectionId = $client->generateCollectionId(FCPATH . 'protected');
    $client->addCollection($collectionId);

    // Add a face photo in data URL format to the collection
    $faceImage = 'data:image/png;base64,...';
    $faceId = $client->addFaceToCollection($collectionId, $faceImage);

    // You can also add to the collection from the photo file path.
    $faceImageFile = './face.png';
    $faceId = $client->addFaceToCollection($collectionId, $faceImageFile);
    ```

## [3.3.8] - 2020-3-14

- Added insert_on_duplicate_update.

    ```php
    // Here is an example of insert_on_duplicate_update.
    $SampleModel
    ->set([
    'key' => '1',
    'title' => 'My title',
    'name' => 'My Name'
    ])
    ->insert_on_duplicate_update();

    // You can also
    $SampleModel
      ->set('key', '1')
      ->set('title', 'My title')
      ->set('name', 'My Name')
      ->insert_on_duplicate_update();
    ```

- Added insert_on_duplicate_update_batch.

    ```php
    // Here is an example of insert_on_duplicate_update_batch
    $SampleModel
      ->set_insert_batch([
        [
          'key' => '1',
          'title' => 'My title',
          'name' => 'My Name'
        ],
        [
          'key' => '2',
          'title' => 'Another title',
          'name' => 'Another Name'
        ]
      ])
      ->insert_on_duplicate_update_batch();
    ```
