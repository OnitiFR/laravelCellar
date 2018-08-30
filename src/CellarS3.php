<?php
namespace Oniti\Cellar;

use AWS;
use Aws\S3\Model\MultipartUpload\UploadBuilder;
/**
*  class pour gérer les intéraction avec cellarS3 de cleverCloud
*/
class CellarS3
{
  private $s3Client;
  private $bucket;


  public function __construct(){
    $this->checkEnv();

    $this->s3Client = AWS::get('s3');

    $this->bucket = env('CELLAR_ADDON_BUCKET');
  }

  /**
  * Vérifie les clefs d'environement
  * @return [type] [description]
  */
  private function checkEnv(){
    $requireEnvs = ['CELLAR_ADDON_HOST','CELLAR_ADDON_KEY_ID','CELLAR_ADDON_KEY_SECRET','CELLAR_ADDON_BUCKET'];
    foreach ($requireEnvs as $env) {
      if(is_null(env($env))) throw new \Exception("Variable d'environement $env manquante !");
    }
  }
  /**
   * Met en ligne un fichier sur cellar S3
   * @param  [type] $streamFile    [description]
   * @param  [type] $key           [description]
   * @param  [type] $mime          [description]
   * @param  [type] $cacheControle [description]
   * @param  string $acl           [description]
   * @return [type]                [description]
   */
  public function upload($streamFile, $key,$mime,$cacheControle = null, $acl = 'public-read'){
    $this->s3Client->putObject([
        'Bucket'        => $this->bucket,
        'Key'           => $key,
        'Body'          => $streamFile,
        'ContentType'   => $mime,
        'ACL'           => $acl,
        'CacheControl'  => $cacheControle ? 'max-age='.$cacheControle : 'no-cache'
    ]);
  }
  /**
   * Upload multipart
   * @param  [type] $path          [description]
   * @param  [type] $key           [description]
   * @param  [type] $mime          [description]
   * @param  [type] $cacheControle [description]
   * @param  string $acl           [description]
   * @return [type]                [description]
   */
  public function multipartUpload($path, $key,$mime,$cacheControle = null, $acl = 'public-read'){
    $uploader = UploadBuilder::newInstance()
    ->setClient($this->s3Client)
    ->setSource($path)
    ->setBucket($this->bucket)
    ->setKey($key)
    ->setConcurrency(5)
    ->setMinPartSize(2 * 1024 * 1024)
    ->setOption('ContentType',$mime)
    ->setOption('ACL',$acl)
    ->setOption('CacheControl',$cacheControle ? 'max-age='.$cacheControle : 'no-cache')
    ->build()
    ->upload();
  }

  /**
  * met a jours les informations sur le document
  * @param  [type] $key  [description]
  * @param  [type] $data [description]
  * @return [type]       [description]
  */
  public function updateDatas($key,$data){
    $base = [
      'Bucket'        => $this->bucket,
      'Key'           => $key,
      'CopySource'    => "$this->bucket/$key"
    ];
    $data = array_merge($base,$data);
    $this->s3Client->copyObject($data);
  }
  /**
  * Récupère un fichier sur cellarS3
  * @param  [type] $key [description]
  * @return [type]      [description]
  */
  public function get($key,$saveLocal){
    $options = [
      'Key' => $key,
      'Bucket' => $this->bucket
    ];
    //Si on demande une sauvegarde en local
    if($saveLocal){
      $filename = explode('/', $key)[1];

      $dir = dirname(dirname(dirname(__DIR__))).'/tmp';
      if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
      }

      $path = $dir.'/'.$filename;
      $options['SaveAs'] = $path;
    }
    $response = $this->s3Client->getObject($options);

    return $response['Body'];
  }
  /**
  * Reourne une url présigné pour télécharger un document
  * @param  [type] $key [description]
  * @return [type]      [description]
  */
  public function getPresignedUrl($key, $duration = '+5 minutes'){
    $this->keyExist($key);

    $command = $this->s3Client->getCommand('GetObject', array(
      'Bucket' => $this->bucket,
      'Key' => $key
    ));

    return $command->createPresignedUrl($duration);
  }

  public function buildUrl($path){
    return 'http://'.getenv('CELLAR_ADDON_BUCKET').'.'.getenv('CELLAR_ADDON_HOST').'/'.$path;
  }

  /**
  * Supprimes des éléments du storage
  * @param  array  $keys [description]
  * @return [type]       [description]
  */
  public function deleteObjects(array $keys,$isDir = false){
    if($isDir && count($keys) > 0){
      $s3Keys = [];
      $result = $this->s3Client->listObjects([
        'Bucket' => $this->bucket,
        'Prefix' => $keys[0],
      ]);
      foreach ($result['Contents'] as $content) {
        $s3Keys[] = ['Key' => $content['Key']];
      }
    }else{
      $s3Keys = [];
      foreach ($keys as $key) {
        $s3Keys[] = ['Key' => $key];
      }
    }
    $this->s3Client->deleteObjects([
      'Bucket' => $this->bucket,
      'Objects' =>$s3Keys
    ]);
  }

  /**
  * Vérifie si la clef existe sur le buket
  * @param  [type] $key [description]
  * @return [type]      [description]
  */
  private function keyExist($key){
    if(!$this->s3Client->doesObjectExist($this->bucket,$key)) throw new \Exception("fichier $key non trouvé");
    return true;
  }
}
