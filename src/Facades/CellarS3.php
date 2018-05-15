<?php
namespace Oniti\Cellar\Facades;

class CellarS3 extends \Illuminate\Support\Facades\Facade
{
  protected static function getFacadeAccessor()
  {
    return 'CellarS3';
  }
}
