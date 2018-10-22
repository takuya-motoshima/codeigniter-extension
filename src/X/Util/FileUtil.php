<?php

use \X\Util\Logger;
use \X\Util\Image;

/**
 * File util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class FileUtil
{

    /**
     * 
     * Make directory
     *
     * @throws RuntimeException
     * @param string $dirPath
     * @param int $mode
     * @return void
     */
    public static function make_direcoty(string $dirPath, int $mode = 0755)
    {
        if (file_exists($dirPath)) {
            return;
        }
        if (mkdir($dirPath, $mode, true) === false) {
            throw new \RuntimeException('Cant create directory ' . $dirPath);
        }
    }

    /**
     * 
     * Rename file
     * 
     * @throws RuntimeException
     * @param string $oldFilePath
     * @param string $newFilePath
     * @return void
     */
    public static function rename_file(string $oldFilePath, string $newFilePath)
    {
        if (!file_exists($oldFilePath)) {
            throw new \RuntimeException('Not found file ' . $oldFilePath);
        }
        self::make_direcoty(dirname($newFilePath));
        if (rename($oldFilePath, $newFilePath) === false) {
            throw new \RuntimeException('Can not rename from ' . $oldFilePath . ' to ' . $newFilePath);
        }
    }

    /**
     * 
     * Copy file
     * 
     * @throws RuntimeException
     * @param string $srcFilePath
     * @param string $dstFilePath
     * @return void
     */
    public static function copy_file(string $srcFilePath, string $dstFilePath)
    {
        if (!file_exists($srcFilePath)) {
            throw new \RuntimeException('Not found file ' . $srcFilePath);
        } else if (!is_file($srcFilePath)) {
            throw new \RuntimeException($srcFilePath . ' is not file');
        }
        self::make_direcoty(dirname($dstFilePath));
        if (copy($srcFilePath, $dstFilePath) === false) {
            throw new \RuntimeException('Can not copy from ' . $srcFilePath . ' to ' . $dstFilePath);
        }
    }

    /**
     * 
     * Copy directory
     *
     * @throws RuntimeException
     * @param string $srcDirPath
     * @param string $dstDirPath
     * @return void
     */
    public static function copy_directory(string $srcDirPath, string $dstDirPath)
    {
        if (!file_exists($srcDirPath)) {
            throw new \RuntimeException('Not found directory ' . $srcDirPath);
        } else if (!is_dir($srcDirPath)) {
            throw new \RuntimeException($srcDirPath . ' is not directory');
        }
        self::make_direcoty($dstDirPath);
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($srcDirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                self::make_direcoty($dstDirPath . '/' . $iterator->getSubPathName());
            } else {
                self::copy_file($file, $dstDirPath . '/' . $iterator->getSubPathName());
            }
        }
    }

    /**
     * 
     * Delete directory or file
     *
     * @param string[] $paths
     * @param bool $isRemoveRootDir
     */
    public static function delete(...$paths)
    // public static function delete(string ...$paths)
    {
        $isRemoveRootDir = true;
        if (is_bool(end($paths))) {
            $isRemoveRootDir = end($paths);
            unset($paths[count($paths) - 1]);
        }
        foreach ($paths as $path) {
            if (is_file($path)) {
                unlink($path);
                continue;
            }
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
            if ($isRemoveRootDir) {
                rmdir($path);
            }
        }
    }

    /**
     * 
     * Replace file content
     *
     * @param string $path
     * @return  void
     */
    public static function replace(string $path, array $replace)
    {
        $content = file_get_contents($path);
        $content = str_replace(array_keys($replace), array_values($replace), $content);
        file_put_contents($path, $content);
    }

    /**
     * 
     * Put image.
     *
     * @param string $url
     * @param string $path
     * @param string $replacementFilename
     * @return string
     */
    public static function put_image(string $url, string $path, string $replacementFilename = null): string
    {
        $fileName = basename($url);
        if (!empty($replacementFilename)) {
            $fileName = preg_replace('/..*(\...*)$/', $replacementFilename . '$1', $fileName);
        }
        self::make_direcoty($path);
        file_put_contents(rtrim($path, '/')  . '/' . $fileName, file_get_contents($url));
        return $fileName;
    }

    /**
     * 
     * Put base64 image.
     *
     * @param string $imageBase64
     * @param string $dirPath
     * @return array
     */
    public static function put_base64_image(string $imageBase64, string $dirPath, string $fileName): array
    {
        $blobInfo = Image::base64_to_byte($imageBase64);
        $baseName = $fileName . '.' . $blobInfo['extension'];
        self::make_direcoty($dirPath);
        file_put_contents(rtrim($dirPath, '/')  . '/' . $baseName, $blobInfo['source']);
        return [
            'extension' => $blobInfo['extension'],
            'file_name' => $fileName,
            'base_name' => $baseName,
        ];
    }

    /**
     * 
     * Read image
     *
     * @param string $imagePath
     * @return string
     */
    public static function read_image(string $imagePath): string
    {
        if (!file_exists($imagePath)) {
            throw new \RuntimeException('Image file does not exist. image_path=' . $imagePath);
        }
        $fp = fopen($imagePath, 'r');
        $image = fread($fp, filesize($imagePath));
        fclose($fp);
        return $image;
    }
}