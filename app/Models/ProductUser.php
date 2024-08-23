<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductUser extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Get payment made.
    */
    public function payment() {
        return $this->morphOne(PaymentHistory::class, 'payable');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function customer() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get promoted ad of the product.
     */
    public function promoted_ad( $adType )
    {
        switch( $adType ) {
            case 'VehicleAd':
                return $this->morphOne(Ad::class, 'promotable');
                break;
            case 'RentalAd':
                return $this->morphOne(RentalAd::class, 'promotable');
                break;
            case 'MechanicWorkshopAd':
                return $this->morphOne(MechanicWorkshopAd::class, 'promotable');
                break;
            case 'ReplacementAd':
                return $this->morphOne(ReplacementAd::class, 'promotable');
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * Get the ad publicated from this slot product.
     */
    public function published_ad( $adType ) {

        switch( $adType ) {
            case 'VehicleAd':
                return $this->morphOne(Ad::class, 'publicable');
                break;
            case 'RentalAd':
                return $this->morphOne(RentalAd::class, 'publicable');
                break;
            case 'MechanicWorkshopAd':
                return $this->morphOne(MechanicWorkshopAd::class, 'publicable');
                break;
            case 'ReplacementAd':
                return $this->morphOne(ReplacementAd::class, 'publicable');
                break;
            default:
                return null;
                break;
        }
    }

}
