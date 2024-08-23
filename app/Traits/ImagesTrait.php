<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait ImagesTrait
{
    //  "imgs_autosmotos" -> S3
    protected $storage_image_path = "images";

    //  "s3"
    //  "public"
    protected $imageDisk = 's3';

    public function validateImages($images) {
        foreach ($images as $key => $image) {
            if( explode('/', $image->getMimeType())[0] !== 'image' )
                return FALSE;
        }

        return TRUE;
    }

    public function storeAdImages($ad, $images, $height = 960, $isCreating)
    {
        $stored = [];
        foreach( $images as $key => $image ){
            $imagePath = $image->file->getRealPath();
            $imageCover = $image->cover;

            $imgFile = Image::make( $imagePath );

            if( $imgFile->height() > $height )
                $imgFile->heighten( $height );

            $imgFile->orientate();
            $imgFile->encode('jpg');
            $hashedFile = hash_file( 'md5', $imagePath );

            $fileUrl = $this->getStoredImageUrl( "publications/{$ad->id}-{$hashedFile}.jpg" );

            $imageStored = Storage::disk( $this->imageDisk )->put( $fileUrl, $imgFile );

            if( $imageStored )
                $stored[$key]['path'] = $fileUrl;

            $stored[$key]['cover'] = $imageCover;

        }

        return $stored;
    }

    public function storeThumbnail( $ad, $image, $height = 300 )
    {
        if( $image ){

            if( $ad->thumbnail )
                $this->destroyStoredImage( $ad->thumbnail );

            if(is_file($image))
                $thumbnailFile = Image::make( $image->getRealPath() );
            else
                $thumbnailFile = Image::make( $image );


                $thumbnailFile->fit(300, 215);

            $thumbnailFile->orientate();
            $thumbnailFile->encode('jpg');
            $fileUrl = $this->getStoredImageUrl( "publications/{$ad->id}/thumbnail{$ad->id}.jpg" );

            $imageStored = Storage::disk( $this->imageDisk )->put( $fileUrl, $thumbnailFile );

            if( $imageStored ){
                // $imageStored[0]['cover'] = true;
                return $fileUrl;
            }

        }

        return FALSE;
    }

    public function storeImage( $model, $image, $path, $height = 960 )
    {
        if( $image ){

            if(is_file($image))
                $imgFile = Image::make( $image->getRealPath() );
            else
                $imgFile = Image::make( $image );


            if( $imgFile->height() > $height )
                $imgFile->heighten( $height );

            $imgFile->orientate();
            $imgFile->encode('jpg');

            if(is_file($image))
                $hashedFile = hash_file( 'md5', $image->getRealPath() );
            else
                $hashedFile = hash_file( 'md5', $image );

            $fileUrl = $this->getStoredImageUrl( "{$path}/{$model->id}-{$hashedFile}.jpg" );

            $imageStored = Storage::disk( $this->imageDisk )->put( $fileUrl, $imgFile );

            if( $imageStored )
                return $fileUrl;
        }

        return FALSE;
    }

    public function updateImage($model, $image)
    {
        if( $image ){

            $imagePath = $model->getRawOriginal()['picture'];
            $exploding= explode('/',$imagePath);

            $imageStored = Image::make( Storage::disk( $this->imageDisk )->get( $imagePath ) );

            $originalHeight = $imageStored->height();

            //$this->destroyStoredImage( $imagePath );

            $imgFile = Image::make( $image->getRealPath() );

            if( $imgFile->height() > $originalHeight )
                $imgFile->heighten( $originalHeight );

            $imgFile->orientate();
            $imgFile->encode('jpg');
            $hashedFile = hash_file( 'md5', $image->getRealPath() );

            $fileUrl = $this->getStoredImageUrl( "{$exploding[1]}/{$model->id}-{$hashedFile}.jpg" );

            $imageStored = Storage::disk( $this->imageDisk )->put( $fileUrl, $imgFile );

            if( $imageStored )
                return $fileUrl;
        }

        return FALSE;
    }

    public function destroyImage( $model )
    {
        if( $model->image ){
        	$model->image->delete();
            return TRUE;
        }

        return FALSE;
    }

    public function destroyStoredImage( $image )
    {
        $file_path = $this->parseImageUrl( $image );

        if( $image )
            return Storage::disk( $this->imageDisk )->delete( $file_path );

        return FALSE;
    }

    public function getStorageImageUrl($path) {
        $s3 = Storage::disk('s3')->getClient();
        return $s3->getObjectUrl(env('AWS_BUCKET'), $path);
    }

    public function parseImageUrl( $fileUrl ) {
        return filter_var($fileUrl, FILTER_VALIDATE_URL)
                    ? parse_url($fileUrl)['path']
                    : $fileUrl;
    }

    public function getStoredImageUrl( $filePath ) {

        $fileUrl;

        $fileUrl = $this->imageDisk !== 'public'
                        ? "{$this->storage_image_path}/{$filePath}"
                        : $filePath;

        return $fileUrl;
    }

}
