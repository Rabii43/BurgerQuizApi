<?php


namespace App\Service;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FileUploader
{
    private $path = ["image" => "/../../public/images/"];


    public function upload(Request $request, $index)
    {
        $result = [];
        $files = $request->files->all();

        foreach ($files as $key => $value) {

            list($fileName, $originalName) = $this->processFile($value, $index);
            if ($fileName) {
                $result[$key] = $fileName;
            }
//            if ($originalName) {
//                $result["originalName"] = $originalName;
//            }

        }
        return $result;
    }


    private function processFile(UploadedFile $file, string $index)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->guessClientExtension();
        $fileName = sha1(uniqid(mt_rand(), true)) . '.' . $ext;

        $file->move(__DIR__ . $this->path[$index], $fileName);

        return [$fileName, $originalFilename . "." . $ext];
    }


}
