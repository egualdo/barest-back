<?php

namespace App\Providers;

use App\Models\User;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\MockObject\Rule\Parameters;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if (!Collection::hasMacro('paginate')) {

            Collection::macro('paginate',
                function ($perPage,$total=null, $page = null,$pageName = 'page') {
                    $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName) ;

                    return new LengthAwarePaginator( 
                        $this->forPage($page, $perPage)->values(),
                        $total?:$this->count(),
                        $perPage,
                        $page,
                        [
                                'path'=>LengthAwarePaginator::resolveCurrentPath(),
                                'pageName'=>$pageName,
                        ]
                    );   
                }
            );
        }

        //validator custom file size
        Validator::extend('max_mb', function ($attribute, $value, $parameters, $validator) {

            if ($value instanceof UploadedFile && ! $value->isValid()) {
                return false;
            }

            // SplFileInfo::getSize returns filesize in bytes
            $size = $value->getSize() / 1024 / 1024;

            return $size <= $parameters[0];

        });

        Validator::replacer('max_mb', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':' . $rule, $parameters[0], $message);
        });

        //validator custom nif,cif,nie

            Validator::extend('nif_correct', function ($attribute, $value, $parameters, $validator) {
                //validando cif-nif-nie

                $nif = strtoupper($value);
                $nif = preg_replace('/[_\W\s]+/','',strtoupper($nif));
                
                if(preg_match('/^(\d|[XYZ])\d{7}[A-Z]$/',$nif)) {
                   
                    preg_match('/\d+/',$nif,$num);
                   
                    $num = ($nif[0]!='Z'? $nif[0]!='Y'? 0: 1: 2).$num[0];
        
                    if($nif[8]=='TRWAGMYFPDXBNJZSQVHLCKE'[$num%23]) {
                       return true;
                    }

                }
                else if(preg_match('/^[ABCDEFGHJKLMNPQRSUVW]\d{7}[\dA-J]$/',$nif)) {
                    
                    for($sum=0,$i=1;$i<8;++$i) {
                        $num = $nif[$i]<<$i%2;
                        $sum += ((int)($num/10))+$num%10;
                    }
                    
                    $sum %= 10;
                    if ($sum !== 0 ) {
                        $digit = 10 - $sum;
                    } else {
                        $digit = $sum;
                    }

                    $letter = substr($nif,0, 1);//primer caracter
                    $digits = substr($nif,1, strlen($nif) - 2);//caracteres del medio 
                    $control = substr($nif,strlen($nif)-1);//ultimo caracter

                    $control_letter = 'JABCDEFGHI'[$digit];//variable de control para comparar

                    if(preg_match('/[KLMNPQRSW]/',$nif[0]) || ($nif[1].$nif[2])=='00') {
                        return true;
                    }
                    if( preg_match('/[ABEH]/',$nif[0])  ){
                        return (string)$digit === $control;
                    }
                    if (preg_match('/[NPQRSW]/',$nif[0]) ) {
                        return $letter === $control;
                    }
                    if( preg_match('/[CDFGJUV]/',$nif[0])||$nif[8]==$digit){
                        return true;
                    }
                    if($nif[8]=='JABCDEFGHI'[$digit] || $nif[8]==$digit) {
                        return true;
                    }
                }
                return false;
            });
    }
}
