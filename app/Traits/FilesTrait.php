<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait FilesTrait
{
    //  "imgs_autosmotos" -> S3
    protected $storage_file_path = "files";

    //  "s3"
    //  "public"
    protected $fileDisk = 's3';
    
    public function getStorageFileUrl($path) {
        $s3 = Storage::disk('s3')->getClient();
        return $s3->getObjectUrl(env('AWS_BUCKET'), $path);
    }

    public function parseFileUrl( $fileUrl ) {
        return filter_var($fileUrl, FILTER_VALIDATE_URL)
                    ? parse_url($fileUrl)['path']
                    : $fileUrl;
    }

    public function getStoredFileUrl( $filePath ) {
            
        $fileUrl;
        
        $fileUrl = $this->fileDisk !== 'public'
                        ? "{$this->storage_file_path}/{$filePath}"
                        : $filePath;

        return $fileUrl;
    }

    //gestion de subida para cv 

     public function destroyStoredCv( $cv )
    {   
        $file_path = $this->parseFileUrl( $cv );

        if( $cv )
            return Storage::disk( $this->fileDisk )->delete( $file_path );

        return FALSE;
    }

     public function storeCv( $model, $file, $path )
    {          
        if( $file ){
            if(is_file($file)){

                $hashedFile = hash_file( 'md5', $file->getRealPath() );
                $extension=$file->getClientOriginalExtension();
                $fileUrl = $this->getStoredFileUrl( "{$path}/{$model->id}-{$hashedFile}.{$extension}");

                $cvStored = Storage::disk( $this->fileDisk )->put( $fileUrl, file_get_contents($file));
                
                if( $cvStored )
                    return $fileUrl;
            } 
        }

        return FALSE;
    }

    public function updateCv($model, $cv)
    {
        $cvPath = $model->resume->path;
        $exploding = explode('/', $cvPath);
        
        $this->destroyStoredCv( $cvPath );

        $hashedFile = hash_file( 'md5', $cv->getRealPath() );
        $extension = $cv->getClientOriginalExtension();
        
        $fileUrl = $this->getStoredFileUrl( "{$exploding[1]}/{$model->id}-{$hashedFile}.{$extension}" );
        $cvStored = Storage::disk( $this->fileDisk )->put( $fileUrl, file_get_contents($cv));
        
        if( $cvStored )
            return $fileUrl;
            
        return FALSE;
    }

    
}
