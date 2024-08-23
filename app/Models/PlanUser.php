<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlanUser extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_end_at' => 'datetime',
    ];

    protected $attributes = [
        'benefits_used' => '{"publications": 0}'
    ];

     /**
     * Get payment made.
    */
    public function payment() {
        return $this->morphOne(PaymentHistory::class, 'payable');
    }

    public function plan() {
        return $this->belongsTo(Plan::class);
    }

    public function customer() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get promoted ad of the product.
     */
    public function promoted_ads( $adType )
    {
        switch( $adType ) {
            case 'VehicleAd':
                return $this->morphMany(Ad::class, 'promotable');
                break;
            case 'RentalAd':
                return $this->morphMany(RentalAd::class, 'promotable');
                break;
            case 'MechanicWorkshopAd':
                return $this->morphMany(MechanicWorkshopAd::class, 'promotable');
                break;
            case 'ReplacementAd':
                return $this->morphMany(ReplacementAd::class, 'promotable');
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * Get the ad publicated from this slot product.
     */
    public function published_ads( $adType ) {

        switch( $adType ) {
            case 'VehicleAd':
                return $this->morphMany(Ad::class, 'publicable');
                break;
            case 'RentalAd':
                return $this->morphMany(RentalAd::class, 'publicable');
                break;
            case 'MechanicWorkshopAd':
                return $this->morphMany(MechanicWorkshopAd::class, 'publicable');
                break;
            case 'ReplacementAd':
                return $this->morphMany(ReplacementAd::class, 'publicable');
                break;
            default:
                return null;
                break;
        }
    }

    public function sync_benefits_used( $itemUsed ) {

        if( is_null( $this->benefits_used ) ) {
            $this->benefits_used = json_encode( [ $itemUsed => 1 ] );
            return $this->update();
        }

        $benefits_used = json_decode( $this->benefits_used, true );

        if( isset( $benefits_used[ $itemUsed ] ) )
            $benefits_used[ $itemUsed ]++;
        else
            $benefits_used[ $itemUsed ] = 1;

        $this->benefits_used = json_encode( $benefits_used );

        return $this->update();
    }
}
