<?php
/**
 * ```sh
 * php prototypes/get-file-owner.php
 * ```
 * 
 * All usernames and IDs can be checked with the following command
 * ```sh
 * cut -d: -f1,3 /etc/passwd
 * # root:0
 * # sshd:74
 * # postfix:89
 * # ec2-user:1000
 * # nginx:995
 * # apache:48
 * # mysql:994
 * ```
 */
$filePath = __FILE__;
echo '$filePath=' . $filePath . PHP_EOL;

$owner = fileowner($filePath);
echo '$owner=' . $owner . PHP_EOL;

$group = filegroup($filePath);
echo '$group=' . $group . PHP_EOL;

$permissions = substr(decoct(fileperms($filePath)), 3);
echo '$permissions=(' . gettype($permissions) .')' . $permissions . PHP_EOL;