<?php

/**
 * @package     Support
 * @author      Ian Olson <me@ianolson.io>
 * @license     MIT
 * @copyright   2015, Ian Olson
 */

namespace IanOlson\Support\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait UploadTrait
{
    /**
     * Local filesystem URI
     */
    protected $localUri;

    /**
     * Cloud filesystem URI
     */
    protected $amazonUri;

    /**
     * Disk for the filesystem.
     *
     * @var string
     */
    protected $disk;

    /**
     * Filesystem instance.
     *
     * @var Storage
     */
    protected $filesystem;

    /**
     * File name for file.
     *
     * @var string
     */
    protected $filename;

    /**
     * Directory to upload file to.
     *
     * @var string
     */
    protected $directory;

    /**
     * File upload an image.
     *
     * @var bool
     */
    protected $image = false;

    /**
     * Thumbnail image width.
     *
     * @var int
     */
    protected $width = 50;

    /**
     * Thumbnail image height.
     *
     * @var int
     */
    protected $height = 50;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->disk       = config('filesystems.default');
        $this->filesystem = Storage::disk($this->disk);
        $this->cloudUri   = env('CLOUD_URI');
        $this->localUri   = env('LOCAL_URI');
    }

    /**
     * Upload the file.
     *
     * @param UploadedFile $file
     * @param array        $options
     * @param null         $return
     *
     * @return array
     */
    protected function upload(UploadedFile $file, array $options = [], $return = null)
    {
        // Reset variables.
        $this->resetUploadVariables();

        // Setup options.
        $this->setupUploadOptions($options);

        // If the upload is an image upload with a thumbnail.
        if ($this->image) {
            $upload = $this->uploadImage($file);

            // If Redactor is calling for the file upload, return JSON.
            if ($return == 'json') {
                return $upload['full'];
            }
        }

        // If the upload is not an image, just upload.
        if (!$this->image) {
            $upload = $this->uploadFile($file);
        }

        return $upload;
    }

    /**
     * Setup options for file upload.
     *
     * @option disk
     *         This will be if you want to use a different storage disk than default.
     *
     * @option filename
     *         Set the filename that you want, by default a random string is generated.
     *
     * @option extension
     *         Set the extension here, to str_replace on the filename that is passed through.
     *
     * @option image
     *         Set to true if you would like to have the upload be an image.
     *
     * @option directory
     *         Set a directory you would like the file to be uploaded to.
     *
     * @option img_width
     *         Set the thumbnail image width, default is 50px.
     *
     * @option img_height
     *         Set the thumbnail image height, default is 50px.
     *
     * @param array $options
     */
    protected function setupUploadOptions(array $options)
    {
        // Setup disk if set in options.
        if (isset($options['disk'])) {
            $this->disk       = $options['disk'];
            $this->filesystem = Storage::disk($this->disk);
        }

        // Remove extension from filename if its passed through.
        if (isset($options['filename']) && isset($options['extension'])) {
            $this->filename = str_replace($options['extension'], '', $options['filename']);
        }

        // Setup filename if set in options.
        if (isset($options['filename']) && !isset($options['extension'])) {
            $this->filename = $options['filename'];
        }

        // Setup image if set in options.
        if (isset($options['image'])) {
            $this->image = true;
        }

        // Setup directory if set in options.
        if (isset($options['directory'])) {
            $this->directory = $options['directory'];
            $this->filesystem->makeDirectory($this->directory);
        }

        // Setup width if set in options.
        if (isset($options['img_width'])) {
            $this->width = $options['img_width'];
        }

        // Setup height if set in options.
        if (isset($options['img_height'])) {
            $this->height = $options['img_height'];
        }
    }

    /**
     * Upload image file with thumbnail.
     *
     * @param UploadedFile $file
     *
     * @return array
     */
    protected function uploadImage(UploadedFile $file)
    {
        // Create image.
        $image = Image::make($file);

        // Randomly generate filename if one does not exist.
        if (!$this->filename) {
            $this->filename = Str::random() . time();
        }

        // Setup full file names with extensions.
        $full_filename      = "{$this->filename}.{$file->getClientOriginalExtension()}";
        $full_filenameThumb = "{$this->filename}_thumb.{$file->getClientOriginalExtension()}";

        // If directory is set, prepend it to the $full_filename & $full_filenameThumb strings.
        if (!empty($this->directory)) {
            $full_filename      = "{$this->directory}/{$full_filename}";
            $full_filenameThumb = "{$this->directory}/{$full_filename}";
        }

        // Put the full image onto the filesystem.
        $this->filesystem->put($full_filename, (string) $image->stream());

        // Resize the image to thumbnail size.
        $image->fit($this->width, $this->height);

        // Put the thumbnail image onto the filesystem.
        $this->filesystem->put($full_filenameThumb, (string) $image->stream());

        // Get the URI paths of the files.
        $full  = $this->getUploadUri($full_filename);
        $thumb = $this->getUploadUri($full_filenameThumb);

        return compact('full', 'thumb');
    }

    /**
     * Upload file to filesystem.
     *
     * @param UploadedFile $file
     *
     * @return array
     */
    protected function uploadFile(UploadedFile $file)
    {
        // Randomly generate filename if one does not exist.
        if (!$this->filename) {
            $this->filename = Str::random() . time();
        }

        // Setup full file names with extensions.
        $full_filename = "{$this->filename}.{$file->getClientOriginalExtension()}";

        // If directory is set, prepend it to the $full_filename string.
        if (!empty($this->directory)) {
            $full_filename = "{$this->directory}/{$full_filename}";
        }

        // Put the file onto the filesystem.
        $stream = file_get_contents($file->getRealPath());
        $this->filesystem->put($full_filename, $stream);

        // Get the URI paths of the files.
        $path = $this->getUploadUri($full_filename);

        return $path;
    }

    /**
     * Get the URI path of the file.
     *
     * @param string $filename
     *
     * @return null|string
     */
    protected function getUploadUri($filename)
    {
        // Setup the default variable value.
        $path = null;

        // If the file exists, get the URI for it.
        if ($this->filesystem->exists($filename)) {
            // If filesystem disk is Local, get the public_path of the file located locally.
            if ($this->disk == 'local') {
                $url  = config('app.url') . '/uploads';
                $path = "{$url}/{$filename}";
            }

            // If filesystem disk is Amazon S3, get the actual URI for the file located in the Amazon S3 bucket.
            if ($this->disk == 's3') {
                $path = $this->filesystem->getDriver()
                                         ->getAdapter()
                                         ->getClient()
                                         ->getObjectUrl(config('filesystems.disks.s3.bucket'), $filename);
            }
        }

        return $path;
    }

    /**
     * Delete a file from the filesystem.
     *
     * @param string $filename
     * @param array  $options
     *
     * @return bool
     */
    public function deleteUpload($filename, array $options = [])
    {
        // Reset variables.
        $this->resetUploadVariables();

        // Setup options.
        $this->setupUploadOptions($options);

        // Remove the Local URI from filename.
        if ($this->disk == 'local') {
            $filename = str_replace($this->localUri, '', $filename);
        }

        // Remove the Amazon S3 URI from filename.
        if ($this->disk == 's3') {
            $filename = str_replace($this->amazonUri, '', $filename);
        }

        // Delete file.
        $this->filesystem->delete($filename);

        return true;
    }

    /**
     * Reset upload variables.
     */
    protected function resetUploadVariables()
    {
        $this->filename  = null;
        $this->directory = null;
        $this->image     = false;
        $this->width     = 50;
        $this->height    = 50;
    }
}