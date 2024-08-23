<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\PoliciesNConditions\StoreRequest;
use App\Http\Requests\Admin\Setting\UpdateContactInfoRequest;
use App\Models\Setting;
use App\Enums\SettingsSectionEnum;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::get();

        return $this->showAll( $settings );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Store the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeLegalInfo(StoreRequest $request, SettingsSectionEnum $type)
    {   
        $data = $request->validated();

        try {
            
            Setting::updateOrInsert(
                        [ 'setting_name' => $type ],
                        [ 'fields_value' => json_encode( $data ) ]
                    );

        } catch (\Exception $exc) {
            return $this->error($exc->getMessage(), 401);
        }

        return $this->success( true );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateContactInfo(UpdateContactInfoRequest $request)
    {
        $data = $request->validated();

        try {
            
            Setting::updateOrInsert(
                        [ 'setting_name' => 'contact_info' ],
                        [ 'fields_value' => json_encode( $data ) ]
                    );

        } catch (\Exception $exc) {
            return $this->error($exc->getMessage(), 401);
        }

        return $this->success( true );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
