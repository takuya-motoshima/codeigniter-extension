<?php
/**
 * ```sh
 * php prototypes/extract-first-frame-of-gif.php
 * ```
 */
$im = new \Imagick(__DIR__ . '/input/sample.gif');

// Write the first frame as an image.
$im = $im->coalesceImages();
$im->setIteratorIndex(0);
$im->writeImage(__DIR__ . '/output/sample_0.gif');

// Destroy resources.
$im->clear();