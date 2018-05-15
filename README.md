
## Cellar ##
 
### Installation ###
 
Add Cellar to your composer.json file to require Scafold :
```
    require : {
        "oniti/cellar": "dev-master"
    }
```
 
Update Composer :
```
    composer update
```
 
The next required step is to add the service provider to config/app.php :
```
    Oniti\Cellar\CellarServiceProvider::class,
```
 
### Publish ###
 
The last required step is to publish views and assets in your application with :
```
    php artisan vendor:publish
```

### Env Requiered ###

```
CELLAR_ADDON_BUCKET= <You'r Bucket>
CELLAR_ADDON_HOST= <Host>
CELLAR_ADDON_KEY_ID= <You'r Key>
CELLAR_ADDON_KEY_SECRET=<You'r Secret>
```