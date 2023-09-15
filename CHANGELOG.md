# Changelog
All notable changes to this project will be documented in this file.

## [4.1.9] - 2023/9/15
### Changed
- Added a leading slash rejection option to the path validation function. The default is to allow leading slashes.

    ```php
    use \X\Util\Validation;

    // Allow leading slashes. Both return values of this function are true.
    Validation::is_path('/usr/lib');
    Validation::is_path('usr/lib');

    // Leading slashes are not allowed. The return value of this function is false.
    Validation::is_path('/usr/lib', true);

    // The return value of this function is true.
    Validation::is_path('usr/lib', true);

    // Form Validation.
    $this->form_validation
      ->set_data(['path' => '/usr/lib'])
      // Do not allow leading slashes. The result of form validation is false.
      ->set_rules('path', 'path', 'is_path[true]');
    if (!$this->form_validation->run())
      // Input error.
      ;
    ```

## [4.1.8] - 2023/9/15
### Changed
- Changed the file (directory) path validation function name from "directory_path" to "is_path".  
    Also, a bug in regular expressions that prevented path names with subdirectories from being validated correctly has been fixed.

    ```php
    use \X\Util\Validation;

    // Replace "directory_path" in validators with "is_path".
    Validation::is_path('/usr/lib');

    // Form Validation.
    $this->form_validation
      ->set_data(['path' => '/usr/lib'])
      ->set_rules('path', 'path', 'is_path');// Replace "directory_path" in validators with "is_path".
    if (!$this->form_validation->run())
      // Input error.
      ;
    ```

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
- Added a feature to convert PDF to image.  
    Syntax:
    ```php
    \X\Util\ImageHelper::pdf2Image(
      string $inputPath,
      string $outputPath,
      array $options = []
    ): void;
    ```

    Parameters:  
    - string $inputPath  
        PDF Path.
    - string $outputPath  
        Image path to output.
    - array $options = []  
        The following options can be specified.  
        |Item|Description|
        |--|--|
        |pageNumber|Page number to out. Default is null, which outputs all pages. Offset is zero.|
        |xResolution|The horizontal resolution. Default is 288.|
        |yResolution|The vertical resolution. Default is 288.|
        |width|Resize width. Default is no resizing (null).|
        |height|Resize Height. Default is no resizing (null).|

    Example:
    ```php
    use \X\Util\ImageHelper;

    // Write all pages of the PDF into the image.
    // sample-0.jpg, sample-1.jpg... will be written.
    ImageHelper::pdf2Image('sample.pdf', 'sample.jpg');

    // Only the first page of the PDF is written on the image.
    ImageHelper::pdf2Image('sample.pdf', 'sample.jpg', ['pageNumber' => 0]);
    ```

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
    ```php
    use \X\Util\ImageHelper;

    // Write the first frame of sample.gif to first-frame.gif.
    ImageHelper::extractFirstFrameOfGif('sample.gif', 'first-frame.gif');

    // Overwrite sample.gif with the first frame.
    ImageHelper::extractFirstFrameOfGif('sample.gif');
    ```
- Added a method to get the number of GIF frames in the "\XFCUtil\ImageHelper" class.
    ```php
    use \X\Util\ImageHelper;

    $numberOfFrames = ImageHelper::getNumberOfGifFrames('sample.gif');
    ```
### Changed
- Added unit __tests__ for the "\X\Util\ImageHelper" class.
    The test case class can be found [here](__tests__/ImageHelperTest.php).  
    ```sh
    composer test
    ```

## [4.1.2] - 2023/2/10
### Added
- Added a method to create an associative array, or an array of only the elements of any key from an associative array list (\X\Util\ArrayHelper#filteringElements()).
    ```php
    use \X\Util\ArrayHelper;

    $staffs = [
      ['name' => 'Derek Emmanuel', 'regno' => 'FE/30304', 'email' => 'derekemmanuel@gmail.com'],
      ['name' => 'Rubecca Michealson', 'regno' => 'FE/20003', 'email' => 'rmichealsongmail.com'],
      ['name' => 'Frank Castle', 'regno' => 'FE/10002', 'email' => 'fcastle86@gmail.com'],
    ];
    $staffs = ArrayHelper::filteringElements($staffs, 'name', 'email');
    print_r($staffs);
    // Array
    // (
    //     [0] => Array
    //         (
    //             [name] => Derek Emmanuel
    //             [email] => derekemmanuel@gmail.com
    //         )
    //     [1] => Array
    //         (
    //             [name] => Rubecca Michealson
    //             [email] => rmichealsongmail.com
    //         )
    //     [2] => Array
    //         (
    //             [name] => Frank Castle
    //             [email] => fcastle86@gmail.com
    //         )
    // )

    $staff = ['name' => 'Derek Emmanuel', 'regno' => 'FE/30304', 'email' => 'derekemmanuel@gmail.com'];
    $staff = ArrayHelper::filteringElements($staff, 'name', 'email');
    print_r($staff);
    // Array
    // (
    //     [name] => Derek Emmanuel
    //     [email] => derekemmanuel@gmail.com
    // )
    ```

### Fixed
- Fixed a bug in which the REST client class (\X\Util\RestClient)was referencing a method of the deleted logger class.

## [4.1.1] - 2023/1/20
### Added
- Added utility class to read request data(\X\Util\HttpInput).  
    Usage example:
    ```php
    use \X\Util\HttpInput;

    // Get the entire put data in an associative array.
    $data = HttpInput::put();

    // Get a value whose key name is "name" from put data.
    $name = HttpInput::put('name');

    //  Apply XSS filtering.
    $xssClean = true;
    $name = HttpInput::put('name', $xssClean);
    ```

## [4.1.0] - 2023/1/20
### Changed
- Updated dependent CodeIgniter framework version from 3.1.11 to 3.1.13.  
    The following files in the skeleton have been updated with the CodeIgniter update.  
    - skeleton/application/config/config.php  
        Add the following item.
        ```php
        $config['sess_samesite'] = 'Lax';
        $config['cookie_samesite'] 	= 'Lax';
        ```
    - skeleton/application/config/mimes.php  
        Add the following item.
        ```php
        'heic' 	=>	'image/heic',
        'heif' 	=>	'image/heif',
        ```

        Change the following items with the following content
        ```php
        'svg'	=>	array('image/svg+xml', 'image/svg', 'application/xml', 'text/xml'),
        ```
    - skeleton/application/config/user_agents.php  
        Add the following item.
        ```php
        'huawei'        => 'Huawei',
        'xiaomi'        => 'Xiaomi',
        'oppo'          => 'Oppo',
        'vivo'          => 'Vivo',
        'infinix'       => 'Infinix',
        'UptimeRobot'   => 'UptimeRobot',
        ```

## [4.0.25] - 2022/12/26
### Fixed
- Reset validation rules, etc. before performing SES outbound validation(\X\Util\AmazonSesClient).

## [4.0.24] - 2022/12/26
### Changed
- The following response headers have been added to JSON responses to mitigate the risk of XSS and RFD.
    - X-Content-Type-Options: nosniff
    - Content-Disposition: attachment; filename="{Base name of request URL}.json"  
        For example, if the request URL is "https://sample.com/api/users/123", the attached file name will be "123.json".

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
    ```php
    use \X\Util\FileHelper;

    // Delete all files and folders in "/ path"..
    FileHelper::delete('/test');

    // Delete all files and folders in the "/ path" folder and also in the "/ path" folder.
    $deleteSelf = true;
    FileHelper::delete('/test', $deleteSelf);

    // Lock before deleting, Locks are disabled by default.
    $deleteSelf = true;
    $enableLock = true;
    FileHelper::delete('/test', $deleteSelf, $enableLock);
    ```

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
    ```php
    $this->form_validation
      ->set_data([
        'ip1' => '192.168.0.1',      // valid
        'ip2' => '192.168.0.1/',     // invalid
        'ip3' => '192.168.0.1/0',    // valid
        'ip4' => '192.168.0.1/1',    // valid  
        'ip5' => '192.168.0.1/2',    // valid  
        'ip6' => '192.168.0.1/3',    // valid  
        'ip7' => '192.168.0.1/4',    // valid  
        'ip8' => '192.168.0.1/5',    // valid  
        'ip9' => '192.168.0.1/6',    // valid  
        'ip10' => '192.168.0.1/7',   // valid  
        'ip11' => '192.168.0.1/8',   // valid  
        'ip12' => '192.168.0.1/9',   // valid  
        'ip13' => '192.168.0.1/10',  // valid  
        'ip14' => '192.168.0.1/11',  // valid  
        'ip15' => '192.168.0.1/12',  // valid  
        'ip16' => '192.168.0.1/13',  // valid  
        'ip17' => '192.168.0.1/14',  // valid  
        'ip18' => '192.168.0.1/15',  // valid  
        'ip19' => '192.168.0.1/16',  // valid  
        'ip20' => '192.168.0.1/17',  // valid  
        'ip21' => '192.168.0.1/18',  // valid  
        'ip22' => '192.168.0.1/19',  // valid  
        'ip23' => '192.168.0.1/20',  // valid  
        'ip24' => '192.168.0.1/21',  // valid  
        'ip25' => '192.168.0.1/22',  // valid  
        'ip26' => '192.168.0.1/23',  // valid  
        'ip27' => '192.168.0.1/24',  // valid  
        'ip28' => '192.168.0.1/25',  // valid  
        'ip29' => '192.168.0.1/26',  // valid  
        'ip30' => '192.168.0.1/27',  // valid  
        'ip31' => '192.168.0.1/28',  // valid  
        'ip32' => '192.168.0.1/29',  // valid  
        'ip33' => '192.168.0.1/30',  // valid  
        'ip34' => '192.168.0.1/31',  // valid  
        'ip35' => '192.168.0.1/32',  // valid  
        'ip36' => '192.168.0.1/33',  // invalid
        'ip37' => '192.168.0.1/34',  // invalid
        'ip38' => '192.168.0.1/asd', // invalid
        'ip39' => '192.168.0.1/01',  // invalid
        'ip40' => '192.168.0.1/00',  // invalid;
      ])
      ->set_rules('ip1', 'ip1', 'ipaddress_or_cidr')
      ->set_rules('ip2', 'ip2', 'ipaddress_or_cidr')
      ->set_rules('ip3', 'ip3', 'ipaddress_or_cidr')
      ->set_rules('ip4', 'ip4', 'ipaddress_or_cidr')
      ->set_rules('ip5', 'ip5', 'ipaddress_or_cidr')
      ->set_rules('ip6', 'ip6', 'ipaddress_or_cidr')
      ->set_rules('ip7', 'ip7', 'ipaddress_or_cidr')
      ->set_rules('ip8', 'ip8', 'ipaddress_or_cidr')
      ->set_rules('ip9', 'ip9', 'ipaddress_or_cidr')
      ->set_rules('ip10', 'ip10', 'ipaddress_or_cidr')
      ->set_rules('ip11', 'ip11', 'ipaddress_or_cidr')
      ->set_rules('ip12', 'ip12', 'ipaddress_or_cidr')
      ->set_rules('ip13', 'ip13', 'ipaddress_or_cidr')
      ->set_rules('ip14', 'ip14', 'ipaddress_or_cidr')
      ->set_rules('ip15', 'ip15', 'ipaddress_or_cidr')
      ->set_rules('ip16', 'ip16', 'ipaddress_or_cidr')
      ->set_rules('ip17', 'ip17', 'ipaddress_or_cidr')
      ->set_rules('ip18', 'ip18', 'ipaddress_or_cidr')
      ->set_rules('ip19', 'ip19', 'ipaddress_or_cidr')
      ->set_rules('ip20', 'ip20', 'ipaddress_or_cidr')
      ->set_rules('ip21', 'ip21', 'ipaddress_or_cidr')
      ->set_rules('ip22', 'ip22', 'ipaddress_or_cidr')
      ->set_rules('ip23', 'ip23', 'ipaddress_or_cidr')
      ->set_rules('ip24', 'ip24', 'ipaddress_or_cidr')
      ->set_rules('ip25', 'ip25', 'ipaddress_or_cidr')
      ->set_rules('ip26', 'ip26', 'ipaddress_or_cidr')
      ->set_rules('ip27', 'ip27', 'ipaddress_or_cidr')form_validation->run
      ->set_rules('ip28', 'ip28', 'ipaddress_or_cidr')
      ->set_rules('ip29', 'ip29', 'ipaddress_or_cidr')
      ->set_rules('ip30', 'ip30', 'ipaddress_or_cidr')
      ->set_rules('ip31', 'ip31', 'ipaddress_or_cidr')
      ->set_rules('ip32', 'ip32', 'ipaddress_or_cidr')
      ->set_rules('ip33', 'ip33', 'ipaddress_or_cidr')
      ->set_rules('ip34', 'ip34', 'ipaddress_or_cidr')
      ->set_rules('ip35', 'ip35', 'ipaddress_or_cidr')
      ->set_rules('ip36', 'ip36', 'ipaddress_or_cidr')
      ->set_rules('ip37', 'ip37', 'ipaddress_or_cidr')
      ->set_rules('ip38', 'ip38', 'ipaddress_or_cidr')
      ->set_rules('ip39', 'ip39', 'ipaddress_or_cidr')
      ->set_rules('ip40', 'ip40', 'ipaddress_or_cidr');
    if (!$this->form_validation->run())
      // Input error.
      ;    
    ```

## [4.0.7] - 2021/9/16
### Changed
- Random character generation function name changed to camel case.
    ```php
    use \X\Util\Cipher;
    use \X\Util\Logger;
    
    Logger::print(Cipher::randStr());// YnqHuuG1VZJ1YXJC14RLmcVjg9uaa8jCyq8S8wd5uY7ox7PXEVzck2YTWGE7aftz
    Logger::print(Cipher::randStr(10));// f1eXb3OLWq
    Logger::print(Cipher::randStr(10, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._~+/')); // 0e-k3qRu9z
    Logger::print(Cipher::randToken68());// 1C63SpTuQfYlNs1IAvCclo~R2xgtrdNsNSa_U28G88mEFsrbz4yu3hn6_vIP7mS=
    Logger::print(Cipher::randToken68(10));// OSVhnIAlJ=
    ```

## [4.0.6] - 2021/9/16
### Added
- Added random string generation function.
    ```php
    use \X\Util\Cipher;
    use \X\Util\Logger;

    Logger::print(Cipher::rand_str());// YnqHuuG1VZJ1YXJC14RLmcVjg9uaa8jCyq8S8wd5uY7ox7PXEVzck2YTWGE7aftz
    Logger::print(Cipher::rand_str(10));// f1eXb3OLWq
    Logger::print(Cipher::rand_str(10, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._~+/'));// 0e-k3qRu9z
    Logger::print(Cipher::rand_token68());// 1C63SpTuQfYlNs1IAvCclo~R2xgtrdNsNSa_U28G88mEFsrbz4yu3hn6_vIP7mS=
    Logger::print(Cipher::rand_token68(10));// OSVhnIAlJ=
    ```

## [4.0.5] - 2021/8/10
### Changed
- The file move method can now set groups and owners for the moved file.
    ```php
    use \X\Util\FileHelper;

    // Move files.
    FileHelper::move('/folder/file.txt', 'newfile.txt');

    // Specify the group and owner of the moved file.
    $group = 'nginx';
    $user = 'nginx';
    FileHelper::move('/folder/file.txt', 'newfile.txt', $group, $user);

    // Specify the group of files after moving.
    FileHelper::move('/folder/file.txt', 'newfile.txt', $group);

    // Specify the owner of the moved file
    FileHelper::move('/folder/file.txt', 'newfile.txt', null, $user);
    ```

- The file copy method can now set groups and owners for the moved file.
    ```php
    use \X\Util\FileHelper;

    // Copy files.
    FileHelper::copyFile('/folder/file.txt', 'newfile.txt');

    // Specify the group and owner of the copied file.
    $group = 'nginx';
    $user = 'nginx';
    FileHelper::copyFile('/folder/file.txt', 'newfile.txt', $group, $user);

    // Specify the group of files after copying.
    FileHelper::copyFile('/folder/file.txt', 'newfile.txt', $group);

    // Specify the owner of the copied file
    FileHelper::copyFile('/folder/file.txt', 'newfile.txt', null, $user);

## [4.0.4] - 2021/7/29
### Added
- Added directory path validation rules.
    ```php
    $this->form_validation
      ->set_data([
        'path1' => '/', // valid
        'path2' => '/abc', // valid
        'path3' => '/sab_', // valid
        'path4' => '/abc/abc/', // invalid
        'path5' => '/sad/dfsd', // valid
        'path6' => 'null', // invalid
        'path7' => '/dsf/dfsdf/dsfsf/sdfds', // valid
        'path8' => '/e3r/343/8437', // valid
        'path9' => '/4333/32#' // invalid
      ])
      ->set_rules('path1', 'path1', 'directory_path')
      ->set_rules('path2', 'path2', 'directory_path')
      ->set_rules('path3', 'path3', 'directory_path')
      ->set_rules('path4', 'path4', 'directory_path')
      ->set_rules('path5', 'path5', 'directory_path')
      ->set_rules('path6', 'path6', 'directory_path')
      ->set_rules('path7', 'path7', 'directory_path')
      ->set_rules('path8', 'path8', 'directory_path')
      ->set_rules('path9', 'path9', 'directory_path');
    if (!$this->form_validation->run())
      // Input error.
      ;
    ```

## [4.0.3] - 2021/6/30
### Added
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
- Added dotenv reading process to sample application (./sample).  
    ./sample/application/config/constants.php:  
    ```php
    const ENV_DIR = APPPATH . '..';
    ```

    ./sample/application/config/hooks.php:  
    ```php
    $hook['pre_system'] = function () {
      $dotenv = Dotenv\Dotenv::createImmutable(ENV_DIR);
      $dotenv->load();
      set_exception_handler(function ($e) {
        Logger::error($e);
      });
    };
    ```
### Changed
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
- Refactor sample application and skeleton.

## [3.9.4] - 2021/4/7
### Fixed
- Fix create-project error.

## [3.9.3] - 2021/3/26
### Added
- Added a function to the Date helper that returns the date of the specified month.
    ```php
    use \X\Util\DateHelper;

    // Get the date of March 2021.
    DateHelper::getDaysInMonth(2021, 3, 'Y-m-d');
    // ["2021-03-01", "2021-03-02", "2021-03-03", "2021-03-04", "2021-03-05", "2021-03-06", "2021-03-07", "2021-03-08", "2021-03-09", "2021-03-10", "2021-03-11", "2021-03-12", "2021-03-13", "2021-03-14", "2021-03-15", "2021-03-16", "2021-03-17", "2021-03-18", "2021-03-19", "2021-03-20", "2021-03-21", "2021-03-22", "2021-03-23", "2021-03-24", "2021-03-25", "2021-03-26", "2021-03-27", "2021-03-28", "2021-03-29", "2021-03-30", "2021-03-31"]
    ```

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
    ```php
    use \X\Util\Logger;

    Logger::printHidepath('I told you so');
    ```

## [3.8.9] - 2021/2/24
### Added
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
    ```php
    use \X\Util\FileHelper;

    FileHelper::humanFilesize('/var/somefile.txt', 0);// 12B
    FileHelper::humanFilesize('/var/somefile.txt', 4);// 1.1498GB
    FileHelper::humanFilesize('/var/somefile.txt', 1);// 117.7MB
    FileHelper::humanFilesize('/var/somefile.txt', 5);// 11.22833TB
    FileHelper::humanFilesize('/var/somefile.txt', 3);// 1.177MB
    FileHelper::humanFilesize('/var/somefile.txt');// 120.56KB
    ```

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
      $isLogin = !empty($_SESSION['user']);
      if (!$meta->allow_http)
        throw new \RuntimeException('HTTP access is not allowed.');
      if ($isLogin && !$meta->allow_login)
        redirect('/users/index');
      else if (!$isLogin && !$meta->allow_logoff)
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
    Define the SES "access key" and "secret" in myapp/.env.  
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

## [3.8.2] - 2021/2/10
### Changed
- Fixed README.

## [3.8.1] - 2021/2/10
### Added
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

    ```php
    $this->form_validation
      ->set_data([
        // Datetime custom validation.
        'datetime' => '2021-02-03 17:46:00',// valid

        // Host name custom validation.
        'hostname1' => 'external.asd1230-123.asd_internal.asd.gm-_ail.com',// valid
        'hostname2' => 'domain.com',// valid
        'hostname3' => 'example.domain.com',// valid
        'hostname4' => 'example.domain-hyphen.com',// valid
        'hostname5' => 'www.domain.com',// valid
        'hostname6' => 'example.museum',// valid
        'hostname7' => 'http://example.com',// invalid
        'hostname8' => 'subdomain.-example.com',// invalid
        'hostname9' => 'example.com/parameter',// invalid
        'hostname10' => 'example.com?anything',// invalid

        // IP address custom validation.
        'ipaddress1' => '000.0000.00.00',// invalid
        'ipaddress2' => '192.168.1.1',// valid
        'ipaddress3' => '912.456.123.123',// invalid

        // Host name or ip address custom validation.
        'hostname_or_ipaddress1' => 'external.asd1230-123.asd_internal.asd.gm-_ail.com',// valid
        'hostname_or_ipaddress2' => 'domain.com',// valid
        'hostname_or_ipaddress3' => 'example.domain.com',// valid
        'hostname_or_ipaddress4' => 'example.domain-hyphen.com',// valid
        'hostname_or_ipaddress5' => 'www.domain.com',// valid
        'hostname_or_ipaddress6' => 'example.museum',// valid
        'hostname_or_ipaddress7' => 'http://example.com',// invalid
        'hostname_or_ipaddress8' => 'subdomain.-example.com',// invalid
        'hostname_or_ipaddress9' => 'example.com/parameter',// invalid
        'hostname_or_ipaddress10' => 'example.com?anything',// invalid
        'hostname_or_ipaddress11' => '000.0000.00.00',// invalid
        'hostname_or_ipaddress12' => '192.168.1.1',// valid
        'hostname_or_ipaddress13' => '912.456.123.123',// invalid

        // UNix user name custom validation.
        'unix_username1' => 'abcd',// valid
        'unix_username2' => 'a123',// valid
        'unix_username3' => 'abc-',// valid
        'unix_username4' => 'a-bc',// valid
        'unix_username5' => 'abc$',// valid
        'unix_username7' => 'a-b$',// valid
        'unix_username8' => '1234',// invalid
        'unix_username9' => '1abc',// invalid
        'unix_username10' => '-abc',// invalid
        'unix_username11' => '$abc',// invalid
        'unix_username12' => 'a$bc',// invalid

        // Port number custom validation.
        'port1' => '-1',// invalid
        'port2' => '0',// valid
        'port3' => '1',// valid
        'port4' => '',// valid
        'port5' => '65534',// valid
        'port6' => '65535',// valid
        'port7' => '65536',// invalid
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
    if (!$this->form_validation->run())
      // Input error.
      ;
    ```

## [3.7.8] - 2021/2/6
### Added
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

## [3.7.7] - 2021/2/3
### Added
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

## [3.7.3] - 2020/12/25
### Added
- Added search options to file search(\X\Util\FileHelper).
    ```php
    use \X\Util\FileHelper;

    // When searching only image files.
    FileHelper::find('/img/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    ```

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
    ```php
    use \X\Util\AmazonSesClient;

    $client = new AmazonSesClient([
      'credentials' => [
        'key' => $_ENV['AMS_SES_ACCESS_KEY'],
        'secret' => $_ENV['AMS_SES_SECRET_KEY']
      ],
      'configuration' => $_ENV['AMS_SES_CONFIGURATION'],
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

## [3.6.2] - 2020/10/29
### Changed
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

## [3.6.1] - 2020/10/23
### Added
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
    ```php
    // Returns the total size of all files in a directory
    FileHelper::getDirectorySize('/var/log');

    // Returns the total size of all files in multiple directories
    FileHelper::getDirectorySize([ '/var/log/php-fpm' '/var/log/nginx' ]);
    ```

## [3.5.4] - 2020/6/4
### Added
- Add encryption key to the parameter of hash conversion method
    ```php
    use \X\Util\Cipher;

    $password = 'password';
    Cipher::encode_sha256('tiger', $password);// c30675022a22cf76c622b7982e8894dd5ac03c4bb2f17ac13a5da01a76acbe6c
    ```

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

## [3.4.7] - 2020/4/27
### Added
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

## [3.3.8] - 2020/3/14
### Added
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