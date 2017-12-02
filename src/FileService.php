<?php 
namespace \PhpFileService;

class FileService
{

  protected $shardPath = '';
  protected $storagePath = '';

  public function setStoragePath($path)
  {
    $this->storagePath = $path;
  }

  public function getAbsoluteFilePath($path)
  {
    return $this->storagePath.'/'.$path;
  }

  public function setShardPath($path)
  {
    $this->shardPath = $path;
  }
  
  protected function getShardPath()
  {
    return $this->shardPath;
  }

  protected function getDatePath()
  {
    
    $year = date("Y");
    $month = date("m");
    $day = date("d");
    $hour = date("H");
    $minute = date("i");
    $datePath = $year."/".$month."/".$day."/".$hour."/".$minute."/";
    
    return $datePath;
  }
  

	public function storeFileByPath($sourceFilePath, $extraId = 'none')
	{
    $entiyFileName = $this->getShardPath().$this->getDatePath().uniqid()."_".mt_rand(100000,999999).'_id_'.$extraId.".zeitfaden.bin";
    $uniqueFileName = $this->getAbsoluteFilePath($entiyFileName);

    if(!is_dir(dirname($uniqueFileName)))
    {
      try {
        mkdir(dirname($uniqueFileName), 0777, true);
      }
      catch (\ErrorException $e){
        // it's probably ok. just a race-condition, someone else created this folder already.
        // in case you have no write-access, you wont know until you check.
        // maybe we can refine this later.
      }
    }
    
    if (file_exists($uniqueFileName))
    {
      error_log($uniqueFileName);
      throw new \ErrorException("we did not get a unique filename. thats not so good.");
    }
    
    if (!copy($sourceFilePath, $uniqueFileName))
    {
      throw new \ErrorException("we could not move the file.");
    }
        
    return $entiyFileName;    
	}

	
  public function getFileResource($itemPath)
  {
    $fileName = $this->getAbsoluteFilePath($itemPath);
    if (!file_exists($fileName))
    {
      throw new \Exception('could not find the file wanted in fileservice: '.$fileName); 
    }
    if (filesize($fileName) === 0)
    {
      throw new \Exception('this file has no contents: '.$fileName); 
    }
    
    $handle = fopen($fileName, 'rb');
    return $handle;
    
  }
	

  public function deleteFile($itemPath)
  {
    $absoluteFileName = $this->getAbsoluteFilePath($itemPath);
    unlink($absoluteFileName);
  }
  
	
}