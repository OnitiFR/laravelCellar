<?php
namespace Oniti\Cellar;

class CellarS3Facade extends \Illuminate\Support\Facades\Facade
{
  protected static function getFacadeAccessor()
  {
    return 'CellarS3';
  }
}
