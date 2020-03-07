<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://github.com/yiisoft.png" height="100px">
    </a>
    <h1 align="center">Yii Files</h1>
    <br>
</p>

This package is the interface for working with the file system.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/files/v/stable.png)](https://packagist.org/packages/yiisoft/files)
[![Total Downloads](https://poser.pugx.org/yiisoft/files/downloads.png)](https://packagist.org/packages/yiisoft/files)
[![Build Status](https://travis-ci.com/yiisoft/files.svg?branch=master)](https://travis-ci.com/yiisoft/files)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/files/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/files/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/files/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/files/?branch=master)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiisoft/files
```

or add

```
"yiisoft/files": "*"
```

to the require section of your `composer.json` file.

### Directory Structure

```
config/                                                                             configuration
docs/                                                                               documentation
src/                                                                                source code
        Adapter/                                                                    Adapters
        DTO/                                                                        DTOs
        Exception/                                                                  Exceptions
        Helper/                                                                     Helpers
        Repository/                                                                 Repositories
tests/                                                                              tests
```

Basic Usage
-----------

The package basis of this extension is `flysystem` from `league`, you can read more about it in the documentation at the link below https://flysystem.thephpleague.com/

with this package you can upload your files to almost any type of server and manage and manage them, move and delete, get the list of downloaded files to the server and migrate them to another server, all files and the list of your servers in the system are stored in the database

first you need to fill in the local storage parameters

```php
return [
    'file.storage' => [
            'local' => [
                'root' => '/var/www/html/yoursite.domain/static',
                'public_url' => 'http://cdn.yoursite.domain/',
                [
                    'file' => [
                        'public' => 0644,
                        'private' => 0600,
                    ],
                    'dir' => [
                        'public' => 0755,
                        'private' => 0700,
                    ],
                ]
            ],
    ]
];
```

If we need to access the file, we use the dryness of the file; if we need access to the storage, we use the dryness of the storage that gets access using various adapters such as:

Adapters
----
FTP - `Yiisoft\Files\Adapter\FtpAdapter` in this package

Local - `Yiisoft\Files\Adapter\LocalAdapter` in this package

Null - `Yiisoft\Files\Adapter\NullAdapter` in this package

AWS S3 - `Yiisoft\Files\Aws3\Adapter\AwsS3Adapter` - `yiisoft/files-aws-s3`

Azure - `Yiisoft\Files\Azure\Adapter\AzureAdapter` - `yiisoft/files-azure`

Cached - `Yiisoft\Files\Cached\Adapter\CachedAdapter` - `yiisoft/files-cached`

Digital Ocean - `Yiisoft\Files\DigitalOcean\Adapter\DigitalOceanSpacesAdapter` - `yiisoft/files-digital-ocean`

Dropbox - `Yiisoft\Files\Dropbox\Adapter\DropboxAdapter` - `yiisoft/files-dropbox`

Gitlab - `Yiisoft\Files\Gitlab\Adapter\GitlabAdapter` - `yiisoft/files-gitlab`

Google Cloud Storage - `Yiisoft\Files\GoogleCloud\Adapter\GoogleStorageAdapter` - `yiisoft/files-google-cloud`

Memory - `Yiisoft\Yii\File\Adapter\MemoryAdapter` - `yiisoft/files-memory`

Rackspace - `Yiisoft\Files\Rackspace\Adapter\RackspaceAdapter` - `yiisoft/files-rackspace`

Replicate - `Yiisoft\Files\Replicate\Adapter\ReplicateAdapter` - `yiisoft/files-replicate`

Scaleway - `Yiisoft\Files\Scaleway\Adapter\ScalewayObjectStorageAdapter` - `yiisoft/files-scaleway`

SFTP - `Yiisoft\Files\Sftp\Adapter\SftpAdapter` - `yiisoft/files-sftp`

WebDAV - `Yiisoft\Files\Webdav\Adapter\WebDAVAdapter` - `yiisoft/files-webdav`

Zip - `Yiisoft\Files\Zip\Adapter\ZipArchiveAdapter` - `yiisoft/files-zip`

To get a copy of these adapters, you can get directly but the best use the factory `Yiisoft\Yii\File\Adapter\AdapterFactory`
he takes as an argument DTO.


File
----
 * Local - If your file is located locally or outside the local storage that you defined 
 * From - If your file is located in the storage, then you can use this method to access the file 
 * Stream - For streaming files, you can use this method                                   
 * Form - For convenience, you can use this method if you submit a file through the form 

### Local File
You need to use this method if the file you have is located on your local file system or outside the file storage directory
For example:
```php
use Yiisoft\Files\File;

$file = File::local(__DIR__ . "/src/assets/logo.png")->to()->put();
```
`File::to(Storage $storage)`: We should pass here to which storage we need to save the file. If we leave it empty, then by default there will be a local storage that is defined in the configuration

`file::put($dist)` - saving the file if we leave empty it will take from the name of the file itself

as you can see, we can write code in a chain, and line by line

```php
use Yiisoft\Files\File;

$file = File::local(__DIR__ . "/src/assets/logo.png")->to();
$file->put();
```

if we want to save it to a completely different storage then we just need to ask it, for example
```php
use Yiisoft\Files\Adapter\AdapterFactory;
use Yiisoft\Files\Dto\FtpAdapterDTO;
use Yiisoft\Files\Storage;
use Yiisoft\Files\File;

//adapter dto preparation
$dto = new FtpAdapterDTO();
$dto->host = "host";
$dto->username = "username";
$dto->password = "password";
$dto->root = "/www/html/cdn.site.domain/static";
//pass DTO to Factory for Get Adapter
$adapter = AdapterFactory::create($dto);
//Storage
$storage = new Storage($adapter);

//....

$file = File::local(__DIR__ . "/src/assets/logo.png")->to($storage)->put();
```
the file is uploaded via ftp to another storage server using a different adapter, you can see the list of all available adapters above

### Storaged File (from)
using this method you can access the file on any store available to us

for example, we need to access the remote file via ftp

```php
use Yiisoft\Files\Adapter\AdapterFactory;
use Yiisoft\Files\Dto\FtpAdapterDTO;
use Yiisoft\Files\Storage;
use Yiisoft\Files\File;

//adapter dto preparation
$dto = new FtpAdapterDTO();
$dto->host = "host";
$dto->username = "username";
$dto->password = "password";
$dto->root = "/www/html/cdn.site.domain/static";
//pass DTO to Factory for Get Adapter
$adapter = AdapterFactory::create($dto);
//Storage
$storage = new Storage($adapter);

//....

$file = File::from("logo.png",$storage)->to()->put("logo.png");
//if we do not specify storages then by default there will be local storage which we specified in the configuration
```

The example above, we got access to the file via ftp and saved it to ourselves in the local storage
in the same way you can access files and work between them to conduct different operations between different file systems

```php
use Yiisoft\Files\Adapter\AdapterFactory;
use Yiisoft\Files\Dto\FtpAdapterDTO;
use Yiisoft\Files\Storage;
use Yiisoft\Files\File;

//adapter dto preparation
$dto = new FtpAdapterDTO();
$dto->host = "host";
$dto->username = "username";
$dto->password = "password";
$dto->root = "/www/html/cdn.site.domain/static";
//pass DTO to Factory for Get Adapter
$adapter = AdapterFactory::create($dto);
//Storage
$storage1 = new Storage($adapter);

//adapter dto preparation
$dto2 = new FtpAdapterDTO();
$dto2->host = "host2";
$dto2->username = "username2";
$dto2->password = "password2";
$dto2->root = "/www/html/cdn2.site.domain/static";
//pass DTO to Factory for Get Adapter
$adapter2 = AdapterFactory::create($dto);
//Storage
$storage2 = new Storage($adapter);

//....

$file = File::from("logo.png",$storage1)->to($storage2)->put("logo.png");
//by the example above we transferred files between servers
```

####Stream File

As you can understand by name in this way, you can access the file stream, for example
```php
use Yiisoft\Files\Adapter\AdapterFactory;
use Yiisoft\Files\Dto\FtpAdapterDTO;
use Yiisoft\Files\Storage;
use Yiisoft\Files\File;

//....
$stream = fopen(__DIR__ . "/src/assets/logo.png","r+");
//....

//Put to local storage by Local Adapter
$file = File::stream($stream)->to()->put();
```

####Form file
This way you can easily access the file sent to you by the form, for example

Let's send us a user profile

HTML
```html
<form method="post" enctype="multipart/form-data">
    <input type="file" name="avatar">
    <input type="submit" value="Upload Image" name="submit">
</form>
```
We should save this file as we can do this for example like this.
```php
use Yiisoft\Files\File;
 
$file = File::form("avatar")->to()->put();
```

###
The above example was shown to you using different ways of accessing files, you can read the list of all available file operations below:
```php
use Yiisoft\Files\Adapter\AdapterFactory;
use Yiisoft\Files\Dto\FtpAdapterDTO;
use Yiisoft\Files\Storage;
use Yiisoft\Files\File;

$file = File::local(__DIR__ . "/src/assets/logo.png"); // get file by local
$file = File::from("logo.png",$storage);// get file by adapter
$file = File::stream($stream); //get file by stream
$file = File::form("avatar");//get file by form (HTML) file 

//Accepts storage where it is necessary to carry out operations on the received file, for example, where to save, where to delete, etc.
$file->to();// default Local Storage from configuration
$file->to($storage);// $storage is Storage for any adapter

//Put - Basically a method for saving a file
$file->put();//If left blank then save the name of the source file
$file->put("logo-mini.png");//Save as "logo-mini.png".

//Rename
$file->rename("logo-menu.png");//If you do not specify the full renaming path, then it will rename the file and save it in the same folder as the original file
$file->rename("/data/static/assets/logo-menu.png");//Save to specified path

//Copy
$file->copy("logo-new.png");//If you do not specify the full path, then save it to the folder where the source file
$file->copy("/data/static/assets/logo-new.png");//Save to specified path

//Delete
$file->delete();

//Exists
$file->exists();


//All the methods above, as we said, can be written in one line
$file->to($storage)
->put("source-file.png")
->rename("new-file-name.png")
->copy("newer-file.png")
->delete();


//get info
$file->getMimetype();//     image/jpeg
$file->getSize();//         23122321
$file->getTimestamp();//    12312312312
$file->getExtension();//    jpg
$file->getBasename();//     logo-name.png
$file->getFilename();//     logo-name
$file->getDirname();///     data/static/assets
```

#Storage

###template

A template for storage is a template of the path where files will be stored

for example, let's ask so that when saving a new file, let it store its creation in folders for example:

```bash
/data/static/2020/03/07/23/16/11/filename.png
```

if we want to make a template for the repository then we need to specify it using the method `setTemplate` or when we put file

```php
$file->to()->setTemplate("/:year/:month/:day/:hour/:minute/:second/")->put();
```

or in creation storage

```php
$dto = new FtpAdapterDTO();
$dto->host = "host";
$dto->username = "username";
$dto->password = "password";
$dto->root = "/www/html/cdn.site.domain/static";
$adapter = AdapterFactory::create($dto);
$storage = new Storage($adapter);

$storage->setTemplate("/:year/:month/:day/:hour/:minute/:second/");
```
