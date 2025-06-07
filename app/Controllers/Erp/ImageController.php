<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\AWS;
use Aws\S3\S3Client;


class ImageController extends Controller
{
    protected $aws;
    protected $s3Client;

    public function __construct()
    {
        require_once APPPATH . 'ThirdParty/aws/autoloader.php';

        $this->aws = new AWS();
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->aws->region,
            'credentials' => [
                'key' => $this->aws->key,
                'secret' => $this->aws->secret,
            ],
        ]);
    }

    public function index()
    {
        echo 'sjdskj';
        die;
        $objects = $this->s3Client->listObjectsV2([
            'Bucket' => $this->aws->bucket,
            'Prefix' => 'uploads/',
        ]);

        $fileUrls = [];
        foreach ($objects['Contents'] as $object) {
            $fileUrls[] = $this->s3Client->getObjectUrl($this->aws->bucket, $object['Key']);
        }

        return view('images/index', ['fileUrls' => $fileUrls]);
    }

    public function create()
    {
        return view('images/create');
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'image' => 'uploaded[image]|mime_in[image,image/jpg,image/jpeg,image/gif,image/png]|max_size[image,2048]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }

        $file = $this->request->getFile('image');
        $filePath = 'uploads/' . time() . '_' . $file->getClientName();

        try {
            $this->s3Client->putObject([
                'Bucket' => $this->aws->bucket,
                'Key' => $filePath,
                'Body' => fopen($file->getTempName(), 'r'),
                'ACL' => 'public-read',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect('/')->with('success', 'File uploaded successfully!');
    }

    public function edit($id)
    {
        $fileUrl = $this->s3Client->getObjectUrl($this->aws->bucket, 'uploads/' . $id);
        return view('images/edit', ['fileUrl' => $fileUrl, 'fileId' => $id]);
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'image' => 'uploaded[image]|mime_in[image,image/jpg,image/jpeg,image/gif,image/png]|max_size[image,2048]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }

        $file = $this->request->getFile('image');
        $filePath = 'uploads/' . $id;

        try {
            $this->s3Client->putObject([
                'Bucket' => $this->aws->bucket,
                'Key' => $filePath,
                'Body' => fopen($file->getTempName(), 'r'),
                'ACL' => 'public-read',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect('/')->with('success', 'File updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->aws->bucket,
                'Key' => 'uploads/' . $id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect('/')->with('success', 'File deleted successfully!');
    }
}
