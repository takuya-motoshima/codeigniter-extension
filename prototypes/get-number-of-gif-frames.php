<?php
/**
 * ```sh
 * php prototypes/get-number-of-gif-frames.php
 * ```
 */
$im = new \Imagick(__DIR__ . '/input/sample.gif');
$numberOfFrames = $im->getNumberImages();
echo '$numberOfFrames=' . $numberOfFrames . PHP_EOL;