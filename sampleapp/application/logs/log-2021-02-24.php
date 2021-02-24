<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2021-02-24 08:49:27 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:27 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:27 --> Total execution time: 0.0293
DEBUG - 2021-02-24 08:49:27 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:27 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:27 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:27 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:29 --> #22993 application/controllers/batch/Batch.php(59):A: Start
DEBUG - 2021-02-24 08:49:29 --> #22995 application/controllers/batch/Batch.php(59):B: Start
DEBUG - 2021-02-24 08:49:32 --> #22993 application/controllers/batch/Batch.php(59):A: Completed
DEBUG - 2021-02-24 08:49:32 --> #22995 application/controllers/batch/Batch.php(59):B: Completed
DEBUG - 2021-02-24 08:49:32 --> Total execution time: 4.8195
DEBUG - 2021-02-24 08:49:32 --> Total execution time: 4.8270
DEBUG - 2021-02-24 08:49:46 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:46 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:46 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:46 --> Total execution time: 0.0256
DEBUG - 2021-02-24 08:49:46 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:46 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:46 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:46 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:46 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:46 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:48 --> #23052 application/controllers/batch/Batch.php(59):A: Start
DEBUG - 2021-02-24 08:49:48 --> #23054 application/controllers/batch/Batch.php(59):B: Start
DEBUG - 2021-02-24 08:49:48 --> #23054 application/controllers/batch/Batch.php(59):B: Exit because it is running in another process.
DEBUG - 2021-02-24 08:49:51 --> #23052 application/controllers/batch/Batch.php(59):A: Completed
DEBUG - 2021-02-24 08:49:51 --> Total execution time: 4.2782
DEBUG - 2021-02-24 08:49:53 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:53 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:53 --> Total execution time: 0.0292
DEBUG - 2021-02-24 08:49:53 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:53 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:49:53 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:49:53 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:49:55 --> #23059 application/controllers/batch/Batch.php(59):B: Start
DEBUG - 2021-02-24 08:49:55 --> #23057 application/controllers/batch/Batch.php(59):A: Start
DEBUG - 2021-02-24 08:49:55 --> #23057 application/controllers/batch/Batch.php(59):A: Exit because it is running in another process.
DEBUG - 2021-02-24 08:49:58 --> #23059 application/controllers/batch/Batch.php(59):B: Completed
DEBUG - 2021-02-24 08:49:58 --> Total execution time: 4.7741
DEBUG - 2021-02-24 08:55:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:55:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:55:05 --> Session: Initialization under CLI aborted.
ERROR - 2021-02-24 08:55:05 --> #23079 application/config/hooks.php(34):ArgumentCountError Object
(
    [message:protected] => Too few arguments to function Batch::run(), 0 passed in /var/www/html/codeigniter-extension/sampleapp/vendor/codeigniter/framework/system/core/CodeIgniter.php on line 532 and exactly 2 expected
    [string:Error:private] => 
    [code:protected] => 0
    [file:protected] => /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/Batch.php
    [line:protected] => 17
    [trace:Error:private] => Array
        (
            [0] => Array
                (
                    [file] => /var/www/html/codeigniter-extension/sampleapp/vendor/codeigniter/framework/system/core/CodeIgniter.php
                    [line] => 532
                    [function] => run
                    [class] => Batch
                    [type] => ->
                    [args] => Array
                        (
                        )

                )

            [1] => Array
                (
                    [file] => /var/www/html/codeigniter-extension/sampleapp/public/index.php
                    [line] => 315
                    [args] => Array
                        (
                            [0] => /var/www/html/codeigniter-extension/sampleapp/vendor/codeigniter/framework/system/core/CodeIgniter.php
                        )

                    [function] => require_once
                )

        )

    [previous:Error:private] => 
)

DEBUG - 2021-02-24 08:55:33 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:55:33 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:55:33 --> Session: Initialization under CLI aborted.
ERROR - 2021-02-24 08:55:33 --> Severity: Warning --> time_sleep_until(): Sleep until to time is less than current time /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/Batch.php 23
DEBUG - 2021-02-24 08:55:33 --> #23080 application/controllers/batch/Batch.php(72):A: Start
DEBUG - 2021-02-24 08:55:36 --> #23080 application/controllers/batch/Batch.php(72):A: Completed
DEBUG - 2021-02-24 08:55:36 --> Total execution time: 3.0323
DEBUG - 2021-02-24 08:56:12 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:56:12 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:56:12 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:56:12 --> #23081 application/controllers/batch/Batch.php(72):A: Start
DEBUG - 2021-02-24 08:56:13 --> #23081 application/controllers/batch/Batch.php(46):>>>>>>>>>>>>>SIGINT
DEBUG - 2021-02-24 08:56:24 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:56:24 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:56:24 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:56:24 --> #23090 application/controllers/batch/Batch.php(72):A: Start
DEBUG - 2021-02-24 08:56:24 --> #23090 application/controllers/batch/Batch.php(72):A: Exit because it is running in another process.
DEBUG - 2021-02-24 08:56:43 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:56:43 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:56:43 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:56:43 --> #23091 application/controllers/batch/Batch.php(72):A: Start
DEBUG - 2021-02-24 08:58:17 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 08:58:17 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 08:58:17 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 08:58:17 --> #23092 application/controllers/batch/Batch.php(72):A: Start
DEBUG - 2021-02-24 08:58:18 --> #23092 application/controllers/batch/Batch.php(41):>>>>>>>>>>>>>SIGQUIT
DEBUG - 2021-02-24 09:07:35 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:07:35 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:07:35 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:07:35 --> #23196 application/controllers/batch/Batch.php(79):A: Start
DEBUG - 2021-02-24 09:07:35 --> #23196 application/controllers/batch/Batch.php(79):A: Exit because it is running in another process.
DEBUG - 2021-02-24 09:07:47 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:07:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:07:47 --> Session: Initialization under CLI aborted.
ERROR - 2021-02-24 09:07:47 --> #23197 application/config/hooks.php(34):ArgumentCountError Object
(
    [message:protected] => Too few arguments to function Batch::run(), 0 passed in /var/www/html/codeigniter-extension/sampleapp/vendor/codeigniter/framework/system/core/CodeIgniter.php on line 532 and at least 1 expected
    [string:Error:private] => 
    [code:protected] => 0
    [file:protected] => /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/Batch.php
    [line:protected] => 17
    [trace:Error:private] => Array
        (
            [0] => Array
                (
                    [file] => /var/www/html/codeigniter-extension/sampleapp/vendor/codeigniter/framework/system/core/CodeIgniter.php
                    [line] => 532
                    [function] => run
                    [class] => Batch
                    [type] => ->
                    [args] => Array
                        (
                        )

                )

            [1] => Array
                (
                    [file] => /var/www/html/codeigniter-extension/sampleapp/public/index.php
                    [line] => 315
                    [args] => Array
                        (
                            [0] => /var/www/html/codeigniter-extension/sampleapp/vendor/codeigniter/framework/system/core/CodeIgniter.php
                        )

                    [function] => require_once
                )

        )

    [previous:Error:private] => 
)

DEBUG - 2021-02-24 09:08:04 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:08:04 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:08:04 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:08:04 --> #23198 application/controllers/batch/Batch.php(79):A: Start
DEBUG - 2021-02-24 09:08:07 --> #23198 application/controllers/batch/Batch.php(79):A: Completed
DEBUG - 2021-02-24 09:08:07 --> Total execution time: 3.0230
DEBUG - 2021-02-24 09:08:13 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:08:13 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:08:13 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:08:13 --> #23199 application/controllers/batch/Batch.php(79):A: Start
DEBUG - 2021-02-24 09:08:13 --> #23199 application/controllers/batch/Batch.php(65):>>>>>>>>>>>>>SIGINT
DEBUG - 2021-02-24 09:09:28 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:09:28 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:09:28 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:09:28 --> #23204 application/controllers/batch/Batch.php(80):A: Start
DEBUG - 2021-02-24 09:09:29 --> #23204 application/controllers/batch/Batch.php(80):A: Completed
DEBUG - 2021-02-24 09:09:29 --> Total execution time: 1.0559
DEBUG - 2021-02-24 09:21:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:21:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:21:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:21:22 --> #23255 application/controllers/batch/Batch.php(81):A: Start
DEBUG - 2021-02-24 09:21:23 --> #23255 application/controllers/batch/Batch.php(81):A: The batch was interrupted.
DEBUG - 2021-02-24 09:23:52 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:23:52 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:23:52 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:23:52 --> Total execution time: 0.0468
DEBUG - 2021-02-24 09:23:52 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:23:52 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:23:52 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:23:52 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:23:52 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:23:52 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:23:52 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:23:52 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:23:52 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:23:54 --> #23263 application/controllers/batch/Batch.php(82):B: Start
DEBUG - 2021-02-24 09:23:54 --> #23261 application/controllers/batch/Batch.php(82):A: Start
DEBUG - 2021-02-24 09:23:54 --> #23263 application/controllers/batch/Batch.php(82):B: Exit because it is running in another process.
DEBUG - 2021-02-24 09:23:54 --> #23265 application/controllers/batch/Batch.php(82):C: Start
DEBUG - 2021-02-24 09:23:54 --> #23265 application/controllers/batch/Batch.php(82):C: Exit because it is running in another process.
DEBUG - 2021-02-24 09:23:57 --> #23261 application/controllers/batch/Batch.php(82):A: Completed
DEBUG - 2021-02-24 09:23:57 --> Total execution time: 4.5293
DEBUG - 2021-02-24 09:37:02 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:37:02 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:37:02 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:37:02 --> Total execution time: 0.0561
DEBUG - 2021-02-24 09:37:02 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:37:02 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:37:02 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:37:02 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:37:02 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:37:02 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:37:02 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:37:02 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:37:02 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:37:06 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:37:06 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:37:06 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:37:06 --> Total execution time: 0.0462
DEBUG - 2021-02-24 09:37:06 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:37:06 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:37:06 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:37:06 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:37:06 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:37:06 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:37:06 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:37:06 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:37:06 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:40:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:40:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:40:05 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:40:05 --> Total execution time: 0.0464
DEBUG - 2021-02-24 09:40:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:40:05 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:40:05 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:40:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:40:05 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:40:05 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:40:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:40:05 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:40:05 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:40:14 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:40:14 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:40:14 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:40:14 --> Total execution time: 0.0423
DEBUG - 2021-02-24 09:40:14 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:40:14 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:40:14 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:40:14 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:40:14 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:40:14 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:40:14 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:40:14 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:40:14 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:41:00 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:00 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:41:00 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:41:00 --> #23399 application/controllers/batch/RunMultipleBatch.php(17):>>>>>>>>>>>>$locktype=file
DEBUG - 2021-02-24 09:41:00 --> Total execution time: 0.0517
DEBUG - 2021-02-24 09:41:00 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:00 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:41:00 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:41:00 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:00 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:41:00 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:41:00 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:00 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:41:00 --> Not Found: batch/Batch/run
DEBUG - 2021-02-24 09:41:29 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:41:29 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:41:29 --> #23406 application/controllers/batch/RunMultipleBatch.php(17):>>>>>>>>>>>>$locktype=file
DEBUG - 2021-02-24 09:41:29 --> Total execution time: 0.0504
DEBUG - 2021-02-24 09:41:29 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:29 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:41:29 --> Not Found: batch/File/run
DEBUG - 2021-02-24 09:41:29 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:29 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:41:29 --> Not Found: batch/File/run
DEBUG - 2021-02-24 09:41:29 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:29 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:41:29 --> Not Found: batch/File/run
DEBUG - 2021-02-24 09:41:55 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:55 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:41:55 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:41:55 --> #23417 application/controllers/batch/RunMultipleBatch.php(17):>>>>>>>>>>>>$locktype=file
DEBUG - 2021-02-24 09:41:55 --> Total execution time: 0.0407
DEBUG - 2021-02-24 09:41:55 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:55 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:41:55 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:41:55 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:55 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:41:55 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:41:55 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:41:55 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:41:55 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:41:57 --> #23423 application/controllers/batch/FileLockBatch.php(82):c: Start
DEBUG - 2021-02-24 09:41:57 --> #23421 application/controllers/batch/FileLockBatch.php(82):b: Start
DEBUG - 2021-02-24 09:41:57 --> #23421 application/controllers/batch/FileLockBatch.php(82):b: Exit because it is running in another process.
DEBUG - 2021-02-24 09:41:57 --> #23419 application/controllers/batch/FileLockBatch.php(82):a: Start
DEBUG - 2021-02-24 09:41:57 --> #23419 application/controllers/batch/FileLockBatch.php(82):a: Exit because it is running in another process.
DEBUG - 2021-02-24 09:42:00 --> #23423 application/controllers/batch/FileLockBatch.php(82):c: Completed
DEBUG - 2021-02-24 09:42:00 --> Total execution time: 4.3034
DEBUG - 2021-02-24 09:42:57 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:42:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:42:57 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:42:57 --> Total execution time: 0.0499
DEBUG - 2021-02-24 09:42:57 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:42:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:42:57 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:42:57 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:42:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:42:57 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:42:57 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:42:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:42:57 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:42:59 --> #23438 application/controllers/batch/FileLockBatch.php(82):c: Start
DEBUG - 2021-02-24 09:42:59 --> #23434 application/controllers/batch/FileLockBatch.php(82):a: Start
DEBUG - 2021-02-24 09:42:59 --> #23436 application/controllers/batch/FileLockBatch.php(82):b: Start
DEBUG - 2021-02-24 09:42:59 --> #23436 application/controllers/batch/FileLockBatch.php(82):b: Exit because it is running in another process.
DEBUG - 2021-02-24 09:43:01 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:43:01 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:43:01 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:43:01 --> Total execution time: 0.0475
DEBUG - 2021-02-24 09:43:01 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:43:01 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:43:01 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:43:01 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:43:01 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:43:01 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:43:01 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:43:01 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:43:01 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:43:02 --> #23438 application/controllers/batch/FileLockBatch.php(82):c: Completed
DEBUG - 2021-02-24 09:43:02 --> #23434 application/controllers/batch/FileLockBatch.php(82):a: Completed
DEBUG - 2021-02-24 09:43:02 --> Total execution time: 4.7074
ERROR - 2021-02-24 09:43:02 --> Severity: Warning --> unlink(/var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/.lock): No such file or directory /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/FileLockBatch.php 49
DEBUG - 2021-02-24 09:43:02 --> Total execution time: 4.7491
DEBUG - 2021-02-24 09:43:03 --> #23451 application/controllers/batch/FileLockBatch.php(82):b: Start
DEBUG - 2021-02-24 09:43:03 --> #23453 application/controllers/batch/FileLockBatch.php(82):c: Start
DEBUG - 2021-02-24 09:43:03 --> #23453 application/controllers/batch/FileLockBatch.php(82):c: Exit because it is running in another process.
DEBUG - 2021-02-24 09:43:03 --> #23449 application/controllers/batch/FileLockBatch.php(82):a: Start
DEBUG - 2021-02-24 09:43:03 --> #23449 application/controllers/batch/FileLockBatch.php(82):a: Exit because it is running in another process.
DEBUG - 2021-02-24 09:43:06 --> #23451 application/controllers/batch/FileLockBatch.php(82):b: Completed
DEBUG - 2021-02-24 09:43:06 --> Total execution time: 4.4714
DEBUG - 2021-02-24 09:43:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:43:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:43:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:43:22 --> Total execution time: 0.0414
DEBUG - 2021-02-24 09:43:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:43:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:43:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:43:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:43:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:43:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:43:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:43:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:43:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:43:24 --> #23460 application/controllers/batch/FileLockBatch.php(82):c: Start
DEBUG - 2021-02-24 09:43:24 --> #23458 application/controllers/batch/FileLockBatch.php(82):b: Start
DEBUG - 2021-02-24 09:43:24 --> #23458 application/controllers/batch/FileLockBatch.php(82):b: Exit because it is running in another process.
DEBUG - 2021-02-24 09:43:24 --> #23456 application/controllers/batch/FileLockBatch.php(82):a: Start
DEBUG - 2021-02-24 09:43:24 --> #23456 application/controllers/batch/FileLockBatch.php(82):a: Exit because it is running in another process.
DEBUG - 2021-02-24 09:43:27 --> #23460 application/controllers/batch/FileLockBatch.php(82):c: Completed
DEBUG - 2021-02-24 09:43:27 --> Total execution time: 4.8637
DEBUG - 2021-02-24 09:54:29 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:54:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:54:29 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:54:29 --> Total execution time: 0.0414
DEBUG - 2021-02-24 09:54:29 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:54:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:54:29 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:54:29 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:54:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:54:29 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:54:29 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:54:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:54:29 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:54:31 --> #23504 application/controllers/batch/FileLockBatch.php(105):a: Start
DEBUG - 2021-02-24 09:54:31 --> #23506 application/controllers/batch/FileLockBatch.php(105):b: Start
DEBUG - 2021-02-24 09:54:31 --> #23508 application/controllers/batch/FileLockBatch.php(105):c: Start
DEBUG - 2021-02-24 09:54:31 --> #23508 application/controllers/batch/FileLockBatch.php(105):c: Exit because it is running in another process.
DEBUG - 2021-02-24 09:54:34 --> #23506 application/controllers/batch/FileLockBatch.php(105):b: Completed
DEBUG - 2021-02-24 09:54:34 --> Total execution time: 4.6489
DEBUG - 2021-02-24 09:54:34 --> #23504 application/controllers/batch/FileLockBatch.php(105):a: Completed
DEBUG - 2021-02-24 09:54:34 --> Total execution time: 4.6484
DEBUG - 2021-02-24 09:54:44 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:54:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:54:44 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:54:44 --> Total execution time: 0.0425
DEBUG - 2021-02-24 09:54:44 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:54:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:54:44 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:54:44 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:54:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:54:44 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:54:44 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:54:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:54:44 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:54:46 --> #23513 application/controllers/batch/FileLockBatch.php(105):b: Start
DEBUG - 2021-02-24 09:54:46 --> #23515 application/controllers/batch/FileLockBatch.php(105):c: Start
DEBUG - 2021-02-24 09:54:46 --> #23515 application/controllers/batch/FileLockBatch.php(105):c: Exit because it is running in another process.
DEBUG - 2021-02-24 09:54:46 --> #23511 application/controllers/batch/FileLockBatch.php(105):a: Start
DEBUG - 2021-02-24 09:54:46 --> #23511 application/controllers/batch/FileLockBatch.php(105):a: Exit because it is running in another process.
DEBUG - 2021-02-24 09:54:49 --> #23513 application/controllers/batch/FileLockBatch.php(105):b: Completed
DEBUG - 2021-02-24 09:54:49 --> Total execution time: 4.4516
DEBUG - 2021-02-24 09:55:23 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:23 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:23 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:23 --> Total execution time: 0.0424
DEBUG - 2021-02-24 09:55:23 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:23 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:55:23 --> Severity: Compile Error --> Cannot use try without catch or finally /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/FileLockBatch.php 25
DEBUG - 2021-02-24 09:55:23 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:23 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:55:23 --> Severity: Compile Error --> Cannot use try without catch or finally /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/FileLockBatch.php 25
DEBUG - 2021-02-24 09:55:23 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:23 --> Global POST, GET and COOKIE data sanitized
ERROR - 2021-02-24 09:55:23 --> Severity: Compile Error --> Cannot use try without catch or finally /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/FileLockBatch.php 25
DEBUG - 2021-02-24 09:55:45 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:45 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:45 --> Total execution time: 0.0543
DEBUG - 2021-02-24 09:55:45 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:45 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:45 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:45 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:45 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:45 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:47 --> #23570 application/controllers/batch/FileLockBatch.php(103):b: Start
DEBUG - 2021-02-24 09:55:47 --> #23568 application/controllers/batch/FileLockBatch.php(103):a: Start
DEBUG - 2021-02-24 09:55:47 --> #23568 application/controllers/batch/FileLockBatch.php(103):a: Exit because it is running in another process.
DEBUG - 2021-02-24 09:55:47 --> #23572 application/controllers/batch/FileLockBatch.php(103):c: Start
DEBUG - 2021-02-24 09:55:47 --> #23572 application/controllers/batch/FileLockBatch.php(103):c: Exit because it is running in another process.
DEBUG - 2021-02-24 09:55:50 --> #23570 application/controllers/batch/FileLockBatch.php(103):b: Completed
DEBUG - 2021-02-24 09:55:50 --> Total execution time: 4.5112
DEBUG - 2021-02-24 09:55:50 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:50 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:50 --> Total execution time: 0.0413
DEBUG - 2021-02-24 09:55:50 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:50 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:50 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:50 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:50 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 09:55:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 09:55:50 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 09:55:52 --> #23577 application/controllers/batch/FileLockBatch.php(103):b: Start
DEBUG - 2021-02-24 09:55:52 --> #23575 application/controllers/batch/FileLockBatch.php(103):a: Start
DEBUG - 2021-02-24 09:55:52 --> #23575 application/controllers/batch/FileLockBatch.php(103):a: Exit because it is running in another process.
DEBUG - 2021-02-24 09:55:52 --> #23579 application/controllers/batch/FileLockBatch.php(103):c: Start
DEBUG - 2021-02-24 09:55:52 --> #23579 application/controllers/batch/FileLockBatch.php(103):c: Exit because it is running in another process.
DEBUG - 2021-02-24 09:55:55 --> #23577 application/controllers/batch/FileLockBatch.php(103):b: Completed
DEBUG - 2021-02-24 09:55:55 --> Total execution time: 4.5256
DEBUG - 2021-02-24 10:13:56 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:13:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:13:56 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:13:56 --> Total execution time: 0.0511
DEBUG - 2021-02-24 10:13:56 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:13:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:13:56 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:13:56 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:13:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:13:56 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:13:56 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:13:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:13:56 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:13:58 --> #23653 application/controllers/batch/FileLockBatch.php(103):c: Start
DEBUG - 2021-02-24 10:13:58 --> #23651 application/controllers/batch/FileLockBatch.php(103):b: Start
DEBUG - 2021-02-24 10:13:58 --> #23649 application/controllers/batch/FileLockBatch.php(103):a: Start
DEBUG - 2021-02-24 10:13:58 --> #23649 application/controllers/batch/FileLockBatch.php(103):a: Unable lock.
DEBUG - 2021-02-24 10:14:01 --> #23651 application/controllers/batch/FileLockBatch.php(103):b: Completed
DEBUG - 2021-02-24 10:14:01 --> Total execution time: 4.8822
ERROR - 2021-02-24 10:14:01 --> Severity: Warning --> unlink(/var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/.lock): No such file or directory /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/FileLockBatch.php 65
DEBUG - 2021-02-24 10:14:01 --> #23653 application/controllers/batch/FileLockBatch.php(103):c: Completed
DEBUG - 2021-02-24 10:14:01 --> Total execution time: 4.8408
DEBUG - 2021-02-24 10:14:08 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:08 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:08 --> Total execution time: 0.0523
DEBUG - 2021-02-24 10:14:08 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:08 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:08 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:08 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:08 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:08 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:10 --> #23658 application/controllers/batch/FileLockBatch.php(103):b: Start
DEBUG - 2021-02-24 10:14:10 --> #23656 application/controllers/batch/FileLockBatch.php(103):a: Start
DEBUG - 2021-02-24 10:14:10 --> #23656 application/controllers/batch/FileLockBatch.php(103):a: Unable lock.
DEBUG - 2021-02-24 10:14:10 --> #23660 application/controllers/batch/FileLockBatch.php(103):c: Start
DEBUG - 2021-02-24 10:14:10 --> #23660 application/controllers/batch/FileLockBatch.php(103):c: Unable lock.
DEBUG - 2021-02-24 10:14:13 --> #23658 application/controllers/batch/FileLockBatch.php(103):b: Completed
DEBUG - 2021-02-24 10:14:13 --> Total execution time: 4.1215
DEBUG - 2021-02-24 10:14:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:22 --> Total execution time: 0.0473
DEBUG - 2021-02-24 10:14:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:22 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:22 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:24 --> #23667 application/controllers/batch/FileLockBatch.php(103):c: Start
DEBUG - 2021-02-24 10:14:24 --> #23665 application/controllers/batch/FileLockBatch.php(103):b: Start
DEBUG - 2021-02-24 10:14:24 --> #23663 application/controllers/batch/FileLockBatch.php(103):a: Start
DEBUG - 2021-02-24 10:14:24 --> #23663 application/controllers/batch/FileLockBatch.php(103):a: Unable lock.
DEBUG - 2021-02-24 10:14:27 --> #23665 application/controllers/batch/FileLockBatch.php(103):b: Completed
DEBUG - 2021-02-24 10:14:27 --> Total execution time: 4.3349
ERROR - 2021-02-24 10:14:27 --> Severity: Warning --> unlink(/var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/.lock): No such file or directory /var/www/html/codeigniter-extension/sampleapp/application/controllers/batch/FileLockBatch.php 65
DEBUG - 2021-02-24 10:14:27 --> #23667 application/controllers/batch/FileLockBatch.php(103):c: Completed
DEBUG - 2021-02-24 10:14:27 --> Total execution time: 4.2821
DEBUG - 2021-02-24 10:14:41 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:41 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:41 --> Total execution time: 0.0428
DEBUG - 2021-02-24 10:14:41 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:41 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:41 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:41 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:41 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:14:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:14:41 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:14:43 --> #23670 application/controllers/batch/FileLockBatch.php(103):a: Start
DEBUG - 2021-02-24 10:14:43 --> #23672 application/controllers/batch/FileLockBatch.php(103):b: Start
DEBUG - 2021-02-24 10:14:43 --> #23672 application/controllers/batch/FileLockBatch.php(103):b: Unable lock.
DEBUG - 2021-02-24 10:14:43 --> #23674 application/controllers/batch/FileLockBatch.php(103):c: Start
DEBUG - 2021-02-24 10:14:43 --> #23674 application/controllers/batch/FileLockBatch.php(103):c: Unable lock.
DEBUG - 2021-02-24 10:14:46 --> #23670 application/controllers/batch/FileLockBatch.php(103):a: Completed
DEBUG - 2021-02-24 10:14:46 --> Total execution time: 4.7523
DEBUG - 2021-02-24 10:15:41 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:15:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:15:41 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:15:41 --> Total execution time: 0.0427
DEBUG - 2021-02-24 10:15:41 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:15:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:15:41 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:15:41 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:15:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:15:41 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:15:41 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:15:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:15:41 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:15:43 --> #23678 application/controllers/batch/AdvisoryLockBatch.php(75):a: Start
DEBUG - 2021-02-24 10:15:43 --> #23680 application/controllers/batch/AdvisoryLockBatch.php(75):b: Start
DEBUG - 2021-02-24 10:15:43 --> #23680 application/controllers/batch/AdvisoryLockBatch.php(75):b: Unable lock.
DEBUG - 2021-02-24 10:15:43 --> #23682 application/controllers/batch/AdvisoryLockBatch.php(75):c: Start
DEBUG - 2021-02-24 10:15:43 --> #23682 application/controllers/batch/AdvisoryLockBatch.php(75):c: Unable lock.
DEBUG - 2021-02-24 10:15:46 --> #23678 application/controllers/batch/AdvisoryLockBatch.php(75):a: Completed
DEBUG - 2021-02-24 10:15:46 --> Total execution time: 4.5628
DEBUG - 2021-02-24 10:15:47 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:15:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:15:47 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:15:47 --> Total execution time: 0.0539
DEBUG - 2021-02-24 10:15:47 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:15:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:15:47 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:15:47 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:15:47 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:15:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:15:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:15:47 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:15:47 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:15:49 --> #23689 application/controllers/batch/AdvisoryLockBatch.php(75):c: Start
DEBUG - 2021-02-24 10:15:49 --> #23687 application/controllers/batch/AdvisoryLockBatch.php(75):b: Start
DEBUG - 2021-02-24 10:15:49 --> #23687 application/controllers/batch/AdvisoryLockBatch.php(75):b: Unable lock.
DEBUG - 2021-02-24 10:15:49 --> #23685 application/controllers/batch/AdvisoryLockBatch.php(75):a: Start
DEBUG - 2021-02-24 10:15:49 --> #23685 application/controllers/batch/AdvisoryLockBatch.php(75):a: Unable lock.
DEBUG - 2021-02-24 10:15:52 --> #23689 application/controllers/batch/AdvisoryLockBatch.php(75):c: Completed
DEBUG - 2021-02-24 10:15:52 --> Total execution time: 4.3139
DEBUG - 2021-02-24 10:23:19 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:23:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:23:19 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:23:19 --> Total execution time: 0.0507
DEBUG - 2021-02-24 10:23:19 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:23:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:23:19 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:23:19 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:23:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:23:19 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:23:19 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:23:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:23:19 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:23:21 --> #23759 application/controllers/batch/FileLockBatch.php(91):a: Start
DEBUG - 2021-02-24 10:23:21 --> #23761 application/controllers/batch/FileLockBatch.php(91):b: Start
DEBUG - 2021-02-24 10:23:21 --> #23763 application/controllers/batch/FileLockBatch.php(91):c: Start
DEBUG - 2021-02-24 10:23:24 --> #23761 application/controllers/batch/FileLockBatch.php(91):b: Completed
DEBUG - 2021-02-24 10:23:24 --> #23759 application/controllers/batch/FileLockBatch.php(91):a: Completed
DEBUG - 2021-02-24 10:23:24 --> Total execution time: 4.8265
DEBUG - 2021-02-24 10:23:24 --> Total execution time: 4.8267
DEBUG - 2021-02-24 10:23:24 --> #23763 application/controllers/batch/FileLockBatch.php(91):c: Completed
DEBUG - 2021-02-24 10:23:24 --> Total execution time: 4.7834
DEBUG - 2021-02-24 10:25:38 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:25:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:25:38 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:25:38 --> Total execution time: 0.0491
DEBUG - 2021-02-24 10:25:38 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:25:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:25:38 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:25:38 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:25:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:25:38 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:25:38 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:25:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:25:38 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:25:40 --> #23771 application/controllers/batch/FileLockBatch.php(93):b: Start
DEBUG - 2021-02-24 10:25:40 --> #23773 application/controllers/batch/FileLockBatch.php(93):c: Start
DEBUG - 2021-02-24 10:25:40 --> #23773 application/controllers/batch/FileLockBatch.php(93):c: Unable lock.
DEBUG - 2021-02-24 10:25:40 --> #23769 application/controllers/batch/FileLockBatch.php(93):a: Start
DEBUG - 2021-02-24 10:25:40 --> #23769 application/controllers/batch/FileLockBatch.php(93):a: Unable lock.
DEBUG - 2021-02-24 10:25:43 --> #23771 application/controllers/batch/FileLockBatch.php(93):b: Completed
DEBUG - 2021-02-24 10:25:43 --> Total execution time: 4.5180
DEBUG - 2021-02-24 10:25:44 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:25:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:25:44 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:25:44 --> Total execution time: 0.0458
DEBUG - 2021-02-24 10:25:44 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:25:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:25:44 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:25:44 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:25:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:25:44 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:25:44 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:25:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:25:44 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:25:46 --> #23776 application/controllers/batch/FileLockBatch.php(93):a: Start
DEBUG - 2021-02-24 10:25:46 --> #23778 application/controllers/batch/FileLockBatch.php(93):b: Start
DEBUG - 2021-02-24 10:25:46 --> #23778 application/controllers/batch/FileLockBatch.php(93):b: Unable lock.
DEBUG - 2021-02-24 10:25:46 --> #23780 application/controllers/batch/FileLockBatch.php(93):c: Start
DEBUG - 2021-02-24 10:25:46 --> #23780 application/controllers/batch/FileLockBatch.php(93):c: Unable lock.
DEBUG - 2021-02-24 10:25:49 --> #23776 application/controllers/batch/FileLockBatch.php(93):a: Completed
DEBUG - 2021-02-24 10:25:49 --> Total execution time: 4.1824
DEBUG - 2021-02-24 10:26:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:26:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:26:05 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:26:05 --> Total execution time: 0.0524
DEBUG - 2021-02-24 10:26:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:26:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:26:05 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:26:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:26:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:26:05 --> UTF-8 Support Enabled
DEBUG - 2021-02-24 10:26:05 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:26:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2021-02-24 10:26:05 --> Session: Initialization under CLI aborted.
DEBUG - 2021-02-24 10:26:07 --> #23787 application/controllers/batch/FileLockBatch.php(93):c: Start
DEBUG - 2021-02-24 10:26:07 --> #23783 application/controllers/batch/FileLockBatch.php(93):a: Start
DEBUG - 2021-02-24 10:26:07 --> #23785 application/controllers/batch/FileLockBatch.php(93):b: Start
DEBUG - 2021-02-24 10:26:07 --> #23787 application/controllers/batch/FileLockBatch.php(93):c: Unable lock.
DEBUG - 2021-02-24 10:26:07 --> #23785 application/controllers/batch/FileLockBatch.php(93):b: Unable lock.
DEBUG - 2021-02-24 10:26:10 --> #23783 application/controllers/batch/FileLockBatch.php(93):a: Completed
DEBUG - 2021-02-24 10:26:10 --> Total execution time: 4.8163
