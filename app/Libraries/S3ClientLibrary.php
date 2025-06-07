<?php

namespace App\Libraries;

require_once APPPATH . '/ThirdParty/aws/vendor/autoload.php';

use Aws\S3\S3Client;
use Config\AWS;


class S3ClientLibrary
{
    private $s3;

    public function __construct()
    {
        $awsConfig = new AWS();

        // Create an S3 client instance
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $awsConfig->region,
            'credentials' => [
                'key' => $awsConfig->key,
                'secret' => $awsConfig->secret,
            ],
            'http' => [
                'verify' => false,  // Disable SSL verification (for local debugging only)
            ],
        ]);
    }

    // List objects in a directory
    public function listObjects($prefix = '')
    {
        try {
            $result = $this->s3->listObjectsV2([
                'Bucket' => (new AWS())->bucket,
                'Prefix' => $prefix,
            ]);
            return $result['Contents'] ?? [];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Upload a file to S3
    public function uploadFile($filePath, $key = null)
    {

        try {
            $key = $key ?: basename($filePath);
            $result = $this->s3->putObject([
                'Bucket' => (new AWS())->bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
            ]);

            return [
                'status' => 'success',
                'url' => $result['ObjectURL'],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }



    // Download a file from S3
    public function downloadFile($fileKey)
    {
        try {
            $result = $this->s3->getObject([
                'Bucket' => (new AWS())->bucket,
                'Key' => $fileKey,
            ]);

            // Debug logging
            log_message('info', 'ContentType: ' . $result['ContentType']);
            log_message('info', 'File Size: ' . strlen($result['Body']->getContents()));

            return [
                'status' => 'success',
                'file' => $result['Body']->getContents(),
                'contentType' => $result['ContentType'],
                'name' => basename($fileKey),
            ];
        } catch (\Exception $e) {
            log_message('error', 'Download failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }



    // Delete a file from S3
    public function deleteFile($fileKey)
    {
        try {
            $this->s3->deleteObject([
                'Bucket' => (new AWS())->bucket,
                'Key' => $fileKey,
            ]);

            return [
                'status' => 'success',
                'message' => 'File deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    // Rename (or move) a file on S3
    public function renameFile($oldKey, $newKey)
    {
        try {
            // Copy the file to the new location (new name)
            $this->s3->copyObject([
                'Bucket' => (new AWS())->bucket,
                'Key' => $newKey,
                'CopySource' => "{$this->bucket}/{$oldKey}",
            ]);

            // Optionally, delete the old file after renaming
            $this->s3->deleteObject([
                'Bucket' => (new AWS())->bucket,
                'Key' => $oldKey,
            ]);

            return [
                'status' => 'success',
                'message' => 'File renamed successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getObjectUrl($key)
    {
        $awsConfig = new AWS();
        return 'https://' . $awsConfig->bucket . '.s3.' . $awsConfig->region . '.amazonaws.com/' . $key;
    }
}
