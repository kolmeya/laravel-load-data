## Laravel MySQL Load Data

The `kolmeya/laravel-load-data` package for Laravel adds the functionality of importing records into Eloquent models using Load Data from a CSV file. 

## Installation
```
composer require kolmeya/laravel-load-data
```


Or add to `composer.json`:

    "require": {
      "kolmeya/laravel-load-data": "^1.0"
    }

Update Composer from the Terminal:

    composer update

Add the trait to your Eloquent Model

    Kolmeya\LoadData\MySqlLoadData

## Usage

Use the trait on model. E.g:

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    Kolmeya\LoadData\MySqlLoadData;

    class Post extends Model
    {
      use MySqlLoadData;
    }

Importing the data from a CSV file:

    Post::import( storage_path( 'app/posts.csv' ) );
