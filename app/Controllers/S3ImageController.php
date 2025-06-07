<?php
namespace App\Controllers;

use App\Libraries\S3ClientLibrary;
use CodeIgniter\Controller;

class S3ImageController extends Controller
{
    public function index()
    {
        $s3Library = new S3ClientLibrary();
        // $files = $s3Library->listObjects('uploads/');
        $files = $s3Library->listObjects();

        return view('images_list', ['files' => $files]);
    }

    public function upload()
    {
        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            try {
                // Generate a unique name for the file
                $uniqueName = uniqid() . '_' . $file->getName();
                $filePath = WRITEPATH . 'uploads/' . $uniqueName;

                // Move the file to the temporary directory
                $file->move(WRITEPATH . 'uploads/', $uniqueName);

                // Upload the file to S3
                $s3Library = new S3ClientLibrary();
                $result = $s3Library->uploadFile($filePath, 'uploads/' . $uniqueName);

                // Delete the temporary file
                unlink($filePath);

                // Handle the response
                if ($result['status'] === 'success') {
                    return redirect()->to(site_url('/s3-images'))->with('message', 'File uploaded successfully.');
                } else {
                    return redirect()->back()->with('error', $result['message']);
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error during upload: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'There was an error uploading the file.');
    }

    public function edit($encodedKey)
    {
        $key = base64_decode($encodedKey);
        $data['key'] = $key;
        $data['file_url'] = "https://grookr.s3.us-west-2.amazonaws.com/{$key}";
        return view('edit', $data);
    }

    public function update($encodedKey)
    {
        $key = base64_decode($encodedKey);
        log_message('debug', 'Update Key: ' . $key);

        $file = $this->request->getFile('file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $filePath = WRITEPATH . 'uploads/' . $file->getName();
            $file->move(WRITEPATH . 'uploads/');

            $s3Library = new S3ClientLibrary();
            $deleteResult = $s3Library->deleteFile($key);
            $uploadResult = $s3Library->uploadFile($filePath, $key);
            unlink($filePath);

            if ($uploadResult['status'] === 'success') {
                return redirect()->to(site_url('/s3-images'))->with('message', 'Image updated successfully.');
            } else {
                return redirect()->back()->with('error', $uploadResult['message']);
            }
        }

        return redirect()->back()->with('error', 'There was an error updating the image.');
    }



    public function download($encodedKey)
    {
        $fileKey = base64_decode($encodedKey);

        $s3Library = new S3ClientLibrary();
        $result = $s3Library->downloadFile($fileKey);

        if ($result['status'] === 'success') {
            return $this->response->setHeader('Content-Type', $result['contentType'])
                ->setHeader('Content-Disposition', 'attachment; filename="' . $result['name'] . '"')
                ->setHeader('Content-Transfer-Encoding', 'binary')
                ->setHeader('Content-Length', strlen($result['file']))
                ->setBody($result['file']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }


    public function delete($encodedKey)
    {
        $key = base64_decode($encodedKey);
        $s3Library = new S3ClientLibrary();
        $result = $s3Library->deleteFile($key);

        if ($result['status'] === 'success') {
            return redirect()->to(site_url('/s3-images'))->with('message', 'File deleted successfully.');
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
}
