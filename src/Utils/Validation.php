<?php

namespace Powon\Utils;


use Slim\Http\UploadedFile;

class Validation
{
    /**
     * Given some parameter names, it checks if those parameters exist and are not empty in the input.
     * @param $names [string]
     * @param $input [string:string]
     * @return bool True when validation is successful, false otherwise.
     */
    public static function validateParametersExist($names, $input)
    {
        if (!$names || !$input)
            return false;

        foreach ($names as &$name) {
           if (!isset($input[$name]) || empty($input[$name]))
               return false;
        }
        return true;
    }

    /**
     * @param target_file string path to target_file
     * @return array: "success" = True when successful, "message" gives error message
     */
    public static function validateImageUpload($target_file, $up_file){
        $uploadOk = true;
        $message = '';
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($up_file->file);
            if($check !== false) {
                $message = "File is an image - " . $check["mime"] . ".";
            } else {
                $message = "File is not an image.";
                $uploadOk = false;
            }
        }
        //Check if file already exists
        if (file_exists($target_file)) {
            $message = "Sorry, file already exists.";
            $uploadOk = false;
        }
        // Check file size
        if ($up_file->getSize() > 500000) {
            $message = "Sorry, your file is too large.";
            $uploadOk = false;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = false;
        }
        $response = array('success' => $uploadOk,
                         'message' => $message);

        return $response;
    }

    /**
     * @param $file UploadedFile
     * @return array ['success' => bool, 'message' => string]
     */
    public static function validateImageOnly($file) {
        $message = '';
        $isValid = false;
        $imageFileType = strtolower(pathinfo($file->getClientFilename(),PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" )
        {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $isValid = false;
        }
        if(isset($_POST["submit"])) {
            $check = getimagesize($file->file);
            if($check !== false) {
                $message = "File is an image - " . $check["mime"] . ".";
            } else {
                $message = "File is not an image.";
                $isValid = false;
            }
        }
        // Check file size
        if ($file->getSize() > 500000) {
            $message = "Sorry, your file is too large.";
            $isValid = false;
        }
        return ['success' => $isValid, 'message' => $message];
    }
}
