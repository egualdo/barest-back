<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
  public function getCountries()
  {
    try {
      $countries = ['Spain', 'Germany', 'Italy', 'Romania'];

      $results = DB::table("countries")
                    ->select('id', 'name', 'phonecode', 'translations')
                    ->whereIn('name', $countries)
                    ->orderBy('name')
                    ->get();

      foreach ($results as $result) {
        $result->translations = json_decode($result->translations);
      }

      return $this->success($results);
    } catch (\Exception $e) {
      return $this->error($e->getMessage(), 404);
    }
  }

  public function getStates($country)
  {
    try {
      $results = DB::table("states")
                    ->select('id', 'name')
                    ->where('country_id', $country)
                    ->orderBy('name')
                    ->get();

      return $this->success($results);
    } catch (\Exception $e) {
      return $this->error($e->getMessage(), 404);
    }
  }

  public function getCities($state)
  {
    try {
      $results = DB::table("cities")
        ->select('id', 'name')
        ->where('state_id', $state)
        ->orderBy('name')
        ->get();

      return $this->success($results);
    } catch (\Exception $e) {
      return $this->error($e->getMessage(), 404);
    }
  }

  public function getCitiesByCountryCode($country_code)
  {
    try {
      $results = DB::table("cities")
                    ->select('id', 'name')
                    ->where('country_code', $country_code)
                    ->orderBy('name')
                    ->get();

      return $this->success($results);
    } catch (\Exception $e) {
      return $this->error($e->getMessage(), 404);
    }
  }

  /**
   * NOTE: This function only accepts "countries", "states", and "cities" as valid values for the $table parameter.
   */
  public function getNameById($id, $table)
  {
    try {
      $result = DB::table($table)
                  ->select('name')
                  ->where('id', $id)
                  ->first();

      return $result->name;
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   * NOTE: This function only accepts "countries", "states", and "cities" as valid values for the $table parameter.
   */
  public function getIdByName($name, $table)
  {
    try {
      $result = DB::table($table)
        ->select('id')
        ->where('name', 'like', '%' . $name . '%')
        ->first();

      return $result->id;
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function getPlacesAutocomplete(Request $request, $ISOCountryCode)
  {
    $place = $request->query('place');

    try {
      
      $whereQuery = fn(&$query) => $query->where('country_code', $ISOCountryCode)
                                        ->where('name', 'LIKE', "$place%")
                                        ->where('name', 'NOT LIKE', "provincia de %")
                                        ->orderBy('name','asc');

      $citiesQuery = DB::table("cities")->select( 'name as display_name', DB::raw('LOWER(name) as name') );
      $whereQuery( $citiesQuery );
      
      return $this->showAll(Collect( $citiesQuery->paginate(12) ));
    } catch (\Exception $e) {
      return $this->error($e->getMessage(), 500);
    }
  }
}
