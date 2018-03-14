<?php
class Service_FileUploader
{
    private static $instance;
    private static $uploadedFileName = '';
    private static $uploadedFilePath = '';
   	private static $errorDesc = '';

    private function __construct()
    {
    }
    /**
     * @return Service_FileUploader
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }
    public function generateUniqueName($dir, $fileName)
    {
        $i = 1;
        $newFileName = $fileName;
        $ext = "";
        $matches = array();
        if (preg_match('/\.(.*)$/', $newFileName, $matches) != FALSE) {
            $ext = $matches[1];
            while (1) {
                $file = $dir . $newFileName;
                if (file_exists($file)) {
                    $newFileName = preg_replace('/\.(.*)$/', '', $fileName) . "_" . $i . ".$ext";
                    $i++;
                } else {
                    break;
                }
            }
        }
        return $newFileName;
    }
    private function fileUploaded($_name)
    {
        $fileUploaded = false;
        if (isset ( $_FILES [$_name] ) && $_FILES [$_name] ['error'] == UPLOAD_ERR_OK) {
			$fileUploaded = true;
		}

        return $fileUploaded;
    }
    private function getErrorCode($_name)
    {
        if(isset($_FILES[$_name]))
        {
            return $_FILES[$_name]['error'];
        }
    }
    private function getErrorDesc($_name)
    {
        if(isset($_FILES[$_name]))
        {
            $error = $_FILES[$_name]['error'];
            $errorStr = "";
            switch($error)
            {
                case UPLOAD_ERR_INI_SIZE:
                    $errorStr = "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $errorStr = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorStr = "The uploaded file was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorStr = "No file was uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorStr = "Missing a temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorStr = "Failed to write file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION :
                    $errorStr = "A PHP extension stopped the file upload.";
                    break;
            }
           return $errorStr;
        }
    }


    public function getUploadedFileName()
    {
    	return self::$uploadedFileName;
    }

    public function getUploadedFilePath()
    {
    	return self::$uploadedFilePath;
    }

    public function getUploadError()
    {
    	return self::$errorDesc;
    }

    public function upload($fileInput, $filePath, $fileName='')
    {
    	//reset variables..
    	self::$uploadedFileName = '';
    	self::$uploadedFilePath = '';
    	self::$errorDesc = '';

    	if ($this->fileUploaded($fileInput)) {

    		$targetFileName = '';
    		if(empty($fileName))
    		{
    			$targetFileName =  basename($_FILES[$fileInput]['name']);
    		}
    		else
    		{
    			$tmp = $_FILES[$fileInput]['name'];
    			$info = pathinfo($tmp);
    			$targetFileName = $fileName .".". $info['extension'];
    		}

    		$uploadFileName = $this->generateUniqueName($filePath,$targetFileName);
        
    	  $uploadfile = $filePath . $uploadFileName;

    		if (@move_uploaded_file($_FILES[$fileInput]['tmp_name'], $uploadfile)) {

    			self::$uploadedFilePath = $uploadfile;
    			self::$uploadedFileName = $uploadFileName;

    			return true;

    		} else {

    			$error = error_get_last();

    			if(!empty($error))
    			{
    				self::$errorDesc = $error['message'];
    			}

    			return false;

    		}
    	}else {



    		self::$errorDesc = $this->getErrorDesc($fileInput);

    		return false;
    	}



    }

}
?>
