# Changelog
All notable changes to this project will be documented in this file.

## [5.0.0] - 2024/5/13
### Changed
- PHP8 support. PHP8 or higher is required.  
    To support PHP8, extend the core class of codeigniter-extension in your application.  
    |application/core/|PHP|
    |--|--|
    |AppController.php|`abstract class AppController extends \X\Controller\Controller {}`|
    |AppController.php|`abstract class AppController extends \X\Controller\Controller {}`|
    |AppInput.php|`class AppInput extends \X\Library\Input {}`|
    |AppLoader.php|`class AppLoader extends \X\Core\Loader {}`|
    |AppModel.php|`abstract class AppModel extends \X\Model\Model {}`|
    |AppRouter.php|`class AppRouter extends \X\Core\Router {}`|
    |AppURI.php|`class AppURI extends \X\Core\URI {}`|

    <!-- [https://github.com/bcit-ci/CodeIgniter/pull/6173](https://github.com/bcit-ci/CodeIgniter/pull/6173) was very helpful. -->

## [4.2.0] - 2024/5/13
### Changed
- Removed the `$baseDir` argument from the `generateCollectionId` method of the `X\Rekognition\Client` class.
- Deprecated methods `message_from_template`, `message_from_xml`, `set_mailtype` and `attachment_cid` have been removed from the `\X\Util\EMail` class.  
    Please use `messageFromTemplate`, `messageFromXml`, `setMailType` and `attachmentCid` instead.
- Changed to appropriate method name.
    |before|after|
    |--|--|
    |ImageHelper::putBase64|ImageHelper::writeDataURLToFile|
    |ImageHelper::putBlob|ImageHelper::writeBlobToFile|
    |ImageHelper::readAsBase64|ImageHelper::readAsDataURL|
    |ImageHelper::isBase64|ImageHelper::isDataURL|
    |ImageHelper::convertBase64ToBlob|ImageHelper::dataURL2Blob|
    |ImageHelper::read|ImageHelper::readAsBlob|
    |VideoHelper::putBase64|VideoHelper::writeDataURLToFile|
    |VideoHelper::isBase64|VideoHelper::isDataURL|
    |VideoHelper::convertBase64ToBlob|VideoHelper::dataURL2Blob|

## [4.1.9] - 2023/9/15
### Changed
- Added a leading slash rejection option to the path validation function. The default is to allow leading slashes (`\X\Util\Validation#is_path`).

## [4.1.8] - 2023/9/15
### Changed
- Changed the file (directory) path validation function name from "directory_path" to "is_path".  

## [4.1.7] - 2023/8/29
### Changed
- The directory creation method is a fix that returns true if the directory creation succeeds and false if it fails.  
    Also changed the log type of error messages in case of failure from error to info.(\X\Util\FileHelper::makeDirectory)

## [4.1.6] - 2023/8/9
### Changed
- Face Comparison (<code>\X\RekognitionClient#compareFaces()</code>) previously returned a RuntimeException if there were no faces in the image, but now returns zero as the similarity rate.
- Recursive directory deletion (<code>\X\Util\FileHelper#delete()</code>) now clears the file state cache (<code>clearstatcache</code>) before deleting its self directory.

## [4.1.5] - 2023/5/25
### Added
- Added a feature to convert PDF to image (`\X\Util\ImageHelper::pdf2Image`).  

## [4.1.4] - 2023/5/11
### Changed
- Added unit test for face recognition class (\X\Rekognition\Client).
- Refactor Util\RestClient member variable names.
    |Before|After|
    |--|--|
    |public $option|public $options|
    |public $response_source|public $responseRaw|
    |public $headers|public $responseHeaders|
- Changed unit test directory from tests to __tests__.

## [4.1.3] - 2023/2/28
### Added
- Added a method to extract the first frame of a GIF in the class "\X\Util\ImageHelper".
- Added a method to get the number of GIF frames in the "\XFCUtil\ImageHelper" class.

### Changed
- Added unit __tests__ for the "\X\Util\ImageHelper" class.

## [4.1.2] - 2023/2/10
### Added
- Added a method to create an associative array, or an array of only the elements of any key from an associative array list (\X\Util\ArrayHelper#filteringElements()).

### Fixed
- Fixed a bug in which the REST client class (\X\Util\RestClient)was referencing a method of the deleted logger class.

## [4.1.1] - 2023/1/20
### Added
- Added utility class to read request data(\X\Util\HttpInput).  

## [4.1.0] - 2023/1/20
### Changed
- Updated dependent CodeIgniter framework version from 3.1.11 to 3.1.13.  

## [4.0.25] - 2022/12/26
### Fixed
- Reset validation rules, etc. before performing SES outbound validation(\X\Util\AmazonSesClient).

## [4.0.24] - 2022/12/26
### Changed
- The following response headers have been added to JSON responses to mitigate the risk of XSS and RFD.
    - X-Content-Type-Options: nosniff
    - Content-Disposition: attachment; filename="{Base name of request URL}.json"  
        For example, if the request URL is "https://example.com/api/users/123", the attached file name will be "123.json".

## [4.0.23] - 2022/12/26
### Changed
- Internal redirect response methods now set the appropriate response content type(\X\Controller\Controller#internalRedirect()). 

## [4.0.22] - 2022/12/13
### Changed
- A forced JSON response option has been added to the controller's error response method.  
    If this option is true, the content type of the responder is returned as application/json.  
    Response data can also be set in the error response with the set method.  
    ```php
    class User extends AppController {
      public function deliberateError() {
        $forceJsonResponse = true;
        parent
          ::set('error', 'Deliberate error')
          ::error('Deliberate error', 400, $forceJsonResponse);
      }
    }
    ```

## [4.0.21] - 2022/12/9
### Changed
- Fixed email address validation rules.
    <table>
      <tr><th>Email Address</th><th>Before</th><th>After</th><th>Changed</th></tr>
      <tr><td>email@domain.com</td><td>valid</td><td>valid</td></tr>
      <tr><td>firstname.lastname@domain.com</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@subdomain.domain.com</td><td>valid</td><td>valid</td></tr>
      <tr><td>firstname+lastname@domain.com</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@123.123.123.123</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@[123.123.123.123]</td><td>invalid</td><td>invalid</td></tr>
      <tr><td>“email”@domain.com</td><td>invalid</td><td>valid</td><td>Changed</td></tr>
      <tr><td>1234567890@domain.com</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@domain-one.com</td><td>valid</td><td>valid</td></tr>
      <tr><td>_______@domain.com</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@domain.name</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@domain.co.jp</td><td>valid</td><td>valid</td></tr>
      <tr><td>firstname-lastname@domain.com</td><td>valid</td><td>valid</td></tr>
      <tr><td>#@%^%#$@#$@#.com</td><td>invalid</td><td>invalid</td></tr>
      <tr><td>@domain.com</td><td>invalid</td><td>invalid</td></tr>
      <tr><td>Joe Smith <email@domain.com></td><td>invalid</td><td>invalid</td></tr>
      <tr><td>email.domain.com</td><td>invalid</td><td>invalid</td></tr>
      <tr><td>email@domain@domain.com</td><td>invalid</td><td>invalid</td></tr>
      <tr><td>.email@domain.com</td><td>valid</td><td>invalid</td><td>Changed</td></tr>
      <tr><td>email.@domain.com</td><td>valid</td><td>invalid</td><td>Changed</td></tr>
      <tr><td>email..email@domain.com</td><td>valid</td><td>invalid</td><td>Changed</td></tr>
      <tr><td>あいうえお@domain.com</td><td>invalid</td><td>valid</td><td>Changed</td></tr>
      <tr><td>email@domain.com (Joe Smith)</td><td>invalid</td><td>invalid</td></tr>
      <tr><td>email@domain</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@-domain.com</td><td>invalid</td><td>invalid</td></tr>
      <tr><td>email@domain.web</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@111.222.333.44444</td><td>valid</td><td>valid</td></tr>
      <tr><td>email@domain..com</td><td>invalid</td><td>invalid
    </table>
## [4.0.20] - 2022/9/26
### Fixed
- Fixed a warning about loading put data in "\X\Library\Input".

## [4.0.19] - 2022/9/25
### Changed
- Delete PID from log output from "\X\Util\Logger".
- Delete printWithoutPath method from "\X\Util\Logger".
- Changed "print" method name in "\X\Util\Logger" to "display".
- Deleted unused files in the skeleton.
- Removed printHidepath method from "\X\Util\Logger". Use "display" method instead.
- Changed $config['log_file_permissions'] in the sample and skeleton from 0644 to 0666.

## [4.0.18] - 2022/9/24
### Changed
- Fix README.md.

## [4.0.17] - 2022/9/23
### Added
- Add form_validation_test action to the sample test controller.

## [4.0.16] - 2022/9/23
### Fixed
- Fixed a bug in the installer.

## [4.0.15] - 2022/9/23
### Changed
- Update skeleton .gitignore.

## [4.0.14] - 2022/9/23
### Changed
- The hostname and hostname_or_ipaddress validations now allow the string "localhost".

## [4.0.13] - 2022/6/6
### Changed
- Methods for omitting long strings (\X\UtilStringHelper#ellipsis) have been fixed to support Unicode.

## [4.0.12] - 2021/11/10
### Fixed
- Fixed a bug in the file deletion function.

## [4.0.11] - 2021/11/10
### Changed
- Allows you to specify whether lock is enabled or disabled when deleting a file.

## [4.0.10] - 2021/10/20
### Changed
- Added a process to clear the file status cache before getting the file size.
  
## [4.0.9] - 2021/9/27
### Fixed
- Changed
    ```php
    use \X\Util\Logger;
    $users = $this->UserModel->select('id, name')->get()->result_array();
    $query = $this->UserModel->last_query();
    Logger::print($query);// SELECT `id`, `name` FROM `user`
    ```

## [4.0.8] - 2021/9/22
### Added
- Added ip address or CIDR validation rules.

## [4.0.7] - 2021/9/16
### Changed
- Random character generation function name changed to camel case.

## [4.0.6] - 2021/9/16
### Added
- Added random string generation function.

## [4.0.5] - 2021/8/10
### Changed
- The file move method can now set groups and owners for the moved file.
- The file copy method can now set groups and owners for the moved file.

## [4.0.4] - 2021/7/29
### Added
- Added directory path validation rules.

## [4.0.3] - 2021/6/30
### Added
- Added key pair generation processing and public key OpenSSH encoding processing.  

## [4.0.2] - 2021/6/15
### Fixed
- Fixed a bug in the exists_by_id method of the \X\Model\Model class.

## [4.0.1] - 2021/5/25
### Added
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

## [4.0.0] - 2021/5/6
### Added
- Added dotenv reading process to demo application (./demo).  

### Changed
- Changed to pass the option of Amazon Rekognition (\X\Rekognition\Client) as an array.

## [3.9.9] - 2021/4/15
### Changed
- Fixed README typo.

## [3.9.8] - 2021/4/15
### Added
- Added form validation rule according to the [email address proposed in HTML5](https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address).

## [3.9.7] - 2021/4/9
### Added
- Added the process to automatically set $\_SESSION to the template variable session.

## [3.9.6] - 2021/4/8
### Fixed
- Fix the issue text not recognized after '&' in case of PUT request.

## [3.9.5] - 2021/4/8
### Changed
- Refactor demo application and skeleton.

## [3.9.4] - 2021/4/7
### Fixed
- Fix create-project error.

## [3.9.3] - 2021/3/26
### Added
- Added a function to the Date helper that returns the date of the specified month.

## [3.9.2] - 2021/3/24
### Fixed
- Resolved an error where the return type of the email function of the email subclass (/X/Util/Email) did not match the definition.

## [3.9.1] - 2021/3/15
### Added
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

## [3.9.0] - 2021/3/15
### Added
- Added a log function that does not output path information.

## [3.8.9] - 2021/2/24
### Added
- Added batch exclusive control sample program for file lock and advisory lock to the demo application.  
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
          <td>myapp/application/controllers/batch/RunMultipleBatch.php</td>
          <td>An entry point that launches multiple batches at the same time.</td>
        </tr>
        <tr>
          <td>myapp/application/controllers/batch/FileLockBatch.php</td>
          <td>Batch with file locking.This is called from RunMultipleBatch.</td>
        </tr>
        <tr>
          <td>myapp/application/controllers/batch/AdvisoryLockBatch.php</td>
          <td>Batch with advisory lock.This is called from RunMultipleBatch.</td>
        </tr>
      </tbody>
    </table>

    How to do it.  
    Run a batch that prohibits multiple launches using file locks.  
    ```sh
    cd /var/www/html/myapp;
    CI_ENV=development php public/index.php batch/runMultipleBatch/run/filelock;
    ```

    Run a batch that prohibits multiple launches using advisory locks.  
    ```sh
    cd /var/www/html/myapp;
    CI_ENV=development php public/index.php batch/runMultipleBatch/run/advisorylock;
    ```

## [3.8.8] - 2021/2/23
### Changed
- Organized readme and added batch lock test program.

## [3.8.7] - 2021/2/19
### Added
- Added a method to the file helper that returns a file size with units.

## [3.8.6] - 2021/2/18
### Changed
- Fixed changelog typos.

## [3.8.5] - 2021/2/18
### Added
- Added HTTP / CLI access control to controller public method annotation.  
    Step 1: Add access control to the hook(application/config/hooks.php).  
    ```php
    use \X\Annotation\AnnotationReader;

    $hook['post_controller_constructor'] = function() {
      if (is_cli())
        return;
      $CI =& get_instance();
      $meta = AnnotationReader::getAccessibility($CI->router->class, $CI->router->method);
      $loggedin = !empty($_SESSION['user']);
      if (!$meta->allow_http)
        throw new \RuntimeException('HTTP access is not allowed.');
      if ($loggedin && !$meta->allow_login)
        redirect('/users/index');
      else if (!$loggedin && !$meta->allow_logoff)
        redirect('/users/login');
    };
    ```

    Step 2: Define annotations for public methods on each controller.  
    ```php
    use \X\Annotation\Access;

    /**
     * Only log-off users can access it.
     * @Access(allow_login=false, allow_logoff=true)
     */
    public function login() {}

    /**
     * Only logged-in users can access it.
     * @Access(allow_login=true, allow_logoff=false)
     */
    public function dashboard() {}

    /**
     * It can only be done with the CLI.
     * @Access(allow_http=false)
     */
    public function batch() {}
    ```

## [3.8.4] - 2021/2/17
### Changed
- Changed to return SES mail sending result object.(\X\Util\AmazonSesClient).

## [3.8.3] - 2021/2/11
### Added
- Added form validation class.The reason I added it is that I want to validate it with the model(\X\Util\Validation).  

## [3.8.2] - 2021/2/10
### Changed
- Fixed README.

## [3.8.1] - 2021/2/10
### Added
- Added an empty judgment method for characters trimmed with left and right spaces(\X\Util\StringHelper).  

## [3.8.0] - 2021/2/10
### Added
- Added nginxn configuration sample file to REAME.

## [3.7.9] - 2021/2/9
### Added
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

## [3.7.8] - 2021/2/6
### Added
- Added a method to group associative arrays by key to ArrayHelper.(\X\Util\ArrayHelper).

## [3.7.7] - 2021/2/3
### Added
- Create a form validation class and add a datetime validation method(\X\Library\FormValidation).  
    Override form validation.  
    application/libraries/AppForm_validation.php:  
    ```php
    <?php
    use X\Library\FormValidation;

    class AppForm_validation extends FormValidation {}
    ```

    This is an example of Datetime verification.  
    ```php
    $this->form_validation
      ->set_data(['datetime' => '2021-02-03 17:46:00'])
      ->set_rules('datetime', 'datetime', 'required|datetime[Y-m-d H:i:s]');
    if (!$this->form_validation->run())
      // Input error.
      ;
    ```

## [3.7.6] - 2021/1/27
### Changed
- Delete debug log.

## [3.7.5] - 2021/1/22
### Fixed
- Fixed a bug that Annotation could not be read.

## [3.7.4] - 2021/1/22
### Changed
- Change image resizing features(\X\Util\ImageHelper).

## [3.7.3] - 2020/12/25
### Added
- Added search options to file search(\X\Util\FileHelper).

## [3.7.2] - 2020/11/17
### Changed
- Remove unused paginate method from Model class.

## [3.7.1] - 2020/11/17
### Fixed
- Fixed a bug in the project creation command.

## [3.7.0] - 2020/11/17
### Changed
- Fix skeleton.

## [3.6.9] - 2020/11/17
### Changed
- Fix README.md.

## [3.6.8] - 2020/11/17
### Changed
- Fix project creation process.

## [3.6.7] - 2020/11/16
### Changed
- Prepend a slash to the PID of the log(\X\Util\Logger).  
    Here is an example of a log.
    ```php
    DEBUG - 2020/11/16 10:04:38 --> #7567 application/controllers/Sample.php(20):Message here.
    ```

## [3.6.6] - 2020/11/10
### Changed
- Add PID to log message(\X\Util\Logger).

## [3.6.5] - 2020/11/9
### Fixed
- Fixed to ignore directory creation error (\X\Util\FileHelper::makeDirectory).

## [3.6.4] - 2020/11/6
### Changed
- Remove class and function names from the log(\X\Util\Logger).

## [3.6.3] - 2020/11/2
### Changed
- Changed to be able to specify multiple Amazon SES email destinations in an array.(\X\Util\AmazonSesClient)

## [3.6.2] - 2020/10/29
### Changed
- Fixed OpenSSL encryption/decryption method.

## [3.6.1] - 2020/10/23
### Added
- Added IP utility class(\X\Util\IpUtils). And since \X\Util\HttpSecurity has moved to IPUtils, I deleted it.

## [3.6.0] - 2020/10/20
### Added
- Add a time stamp to the log message output to the CLI(\X\Util\Logger#printWithoutPath).

## [3.5.9] - 2020/10/19
### Added
- Added log output method without file path(\X\Util\Logger#printWithoutPath).

## [3.5.8] - 2020/10/16
### Fixed
- Fixed a bug that IP acquisition fails when XFF is empty(\X\Util\HttpSecurity#getIpFromXFF).

## [3.5.7] - 2020/10/15
### Added
- Added method to get IP from XFF(\X\Util\HttpSecurity#getIpFromXFF).

## [3.5.5] - 2020/6/4
### Added
- Added a method to AA that returns the size of all files in a directory.

## [3.5.4] - 2020/6/4
### Added
- Add encryption key to the parameter of hash conversion method

## [3.5.3] - 2020/5/20
### Added
- Added a process to log out a user who is logged in with the same ID on another device when logging in  
    config/hooks.php:  
    ```php
    use \X\Annotation\AnnotationReader;
    $hook['post_controller_constructor'] = function() {
      isset($_SESSION['user']) ? handlingLoggedIn() : handlingLogOff();
    };

    function handlingLoggedIn() {
      $CI =& get_instance();
      $CI->load->model('UserModel');
      if ($CI->UserModel->isBanUser(session_id())) {
        // Sign out
        $CI->UserModel->signout();
        $CI->load->helper('cookie');
        set_cookie('show_ban_message', true, 10);
        // To logoff processing
        return handlingLogOff();
      }
      $meta = AnnotationReader::getAccessibility($CI->router->class, $CI->router->method);
      if (!$meta->allow_login || ($meta->allow_role && $meta->allow_role !== $session['role'])) {
        redirect('/users/index');
      }
    }

    function handlingLogOff() {
      $CI =& get_instance();
      $meta = AnnotationReader::getAccessibility($CI->router->class, $CI->router->method);
      if (!$meta->allow_logoff) {
        redirect('/signin');
      }
    }
    ```

    models/UserModel.php:  
    ```php
    class UserModel extends \AppModel {
      protected $model = 'SessionModel';

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

    class User extends AppController {
      protected $model = 'UserModel';

      /**
       * @Access(allow_login=false, allow_logoff=true)
       */
      public function signin() {
        try {
          $this->form_validation
            ->set_data($this->input->post())
            ->set_rules('username', 'username', 'required|max_length[30]')
            ->set_rules('password', 'password', 'required|max_length[30]');
          if (!$this->form_validation->run())
            // Input error.
            return parent::error(print_r($this->form_validation->error_array(), true), 400);
          $result = $this->UserModel->signin($this->input->post('username'), $this->input->post('password'));
          parent
            ::set($result)
            ::json();
        } catch (\Throwable $e) {
          parent::error($e->getMessage(), 400);
        }
      }

      /**
       * @Access(allow_login=true, allow_logoff=false)
       */
      public function signout() {
        try {
          $this->UserModel->signout();
          redirect('/signin');
        } catch (\Throwable $e) {
          parent::error($e->getMessage(), 400);
        }
      }
    }
    ```

    public/assets/signin.js:
    ```js
    (() => {
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

## [3.5.0] - 2020/5/19
### Fixed
- Fixed a bug that DB class does not inherit \X\Database\QueryBuilder when making session DB

## [3.4.8] - 2020/4/28
### Fixed
- Make the IP range check method of "\X\Util\HttpSecurity" class do correct check when subnet mask is 32.

## [3.4.7] - 2020/4/27
### Added
- Added feature to face detector to find multiple faces from collection

## [3.4.6] - 2020/4/23
### Added
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

## [3.4.5] - 2020/4/10
### Changed
- Changed to return an empty string when there is no key value to get from the config with "\X\Utils\Loader::config()".

## [3.4.2] - 2020/3/16
### Added
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

## [3.3.9] - 2020/3/16
### Added
- Added client class that summarizes face detection processing. Remove old face detection class.

## [3.3.8] - 2020/3/14
### Added
- Added insert_on_duplicate_update.
- Added insert_on_duplicate_update_batch.

[3.3.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v1.0.0...v3.3.8
[3.3.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.3.8...v3.3.9
[3.4.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.3.9...v3.4.2
[3.4.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.4.2...v3.4.6
[3.4.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.4.6...v3.4.7
[3.4.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.4.7...v3.4.8
[3.5.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.4.8...v3.5.0
[3.5.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.0...v3.5.3
[3.5.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.3...v3.5.4
[3.5.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.4...v3.5.5
[3.5.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.5...v3.5.7
[3.5.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.7...v3.5.8
[3.5.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.8...v3.5.9
[3.6.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.9...v3.6.0
[3.6.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.0...v3.6.1
[3.6.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.1...v3.6.2
[3.6.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.2...v3.6.3
[3.6.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.3...v3.6.4
[3.6.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.4...v3.6.5
[3.6.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.5...v3.6.6
[3.6.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.6...v3.6.7
[3.6.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.7...v3.6.8
[3.6.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.8...v3.6.9
[3.7.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.9...v3.7.0
[3.7.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.0...v3.7.1
[3.7.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.1...v3.7.2
[3.7.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.2...v3.7.3
[3.7.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.3...v3.7.4
[3.7.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.4...v3.7.5
[3.7.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.5...v3.7.6
[3.7.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.6...v3.7.7
[3.7.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.7...v3.7.8
[3.7.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.8...v3.7.9
[3.8.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.9...v3.8.0
[3.8.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.0...v3.8.1
[3.8.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.1...v3.8.2
[3.8.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.2...v3.8.3
[3.8.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.3...v3.8.4
[3.8.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.4...v3.8.5
[3.8.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.5...v3.8.6
[3.8.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.6...v3.8.7
[3.8.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.7...v3.8.8
[3.8.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.8...v3.8.9
[3.9.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.9...v3.9.0
[3.9.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.0...v3.9.1
[3.9.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.1...v3.9.2
[3.9.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.2...v3.9.3
[3.9.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.3...v3.9.4
[3.9.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.4...v3.9.5
[3.9.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.5...v3.9.6
[3.9.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.6...v3.9.7
[3.9.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.7...v3.9.8
[3.9.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.8...v3.9.9
[4.0.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.9...v4.0.0
[4.0.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.0...v4.0.1
[4.0.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.1...v4.0.2
[4.0.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.2...v4.0.3
[4.0.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.3...v4.0.4
[4.0.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.4...v4.0.5
[4.0.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.5...v4.0.6
[4.0.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.6...v4.0.7
[4.0.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.7...v4.0.8
[4.0.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.8...v4.0.9
[4.0.10]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.9...v4.0.10
[4.0.11]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.10...v4.0.11
[4.0.12]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.11...v4.0.12
[4.0.13]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.12...v4.0.13
[4.0.14]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.13...v4.0.14
[4.0.15]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.14...v4.0.15
[4.0.16]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.15...v4.0.16
[4.0.17]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.16...v4.0.17
[4.0.18]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.17...v4.0.18
[4.0.19]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.18...v4.0.19
[4.0.20]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.19...v4.0.20
[4.0.21]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.20...v4.0.21
[4.0.22]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.21...v4.0.22
[4.0.23]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.22...v4.0.23
[4.0.24]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.23...v4.0.24
[4.0.25]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.24...v4.0.25
[4.1.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.25...v4.1.0
[4.1.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.0...v4.1.1
[4.1.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.1...v4.1.2
[4.1.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.2...v4.1.3
[4.1.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.3...v4.1.4
[4.1.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.4...v4.1.5
[4.1.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.5...v4.1.6
[4.1.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.6...v4.1.7
[4.1.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.7...v4.1.8
[4.1.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.8...v4.1.9
[4.2.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.9...v4.2.0
<!-- [5.0.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.9...v5.0.0 -->