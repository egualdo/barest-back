<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicationController extends Controller
{
    public function block(Post $publication) {
        //$this->authorize( 'update', $user );
        
        DB::beginTransaction();
        try {
            
            $publication->status = 'BLOCKED';
            $publication->update();

            $publication->reports()->update([ 'status' => 'ATTENDED' ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 400);
        }

        return $this->success('publication blocked.');
    }

    public function unblock(Post $publication) {
        //$this->authorize( 'update', $user );
        
        try {            
            $publication->status = 'ACTIVE';
            $publication->update();
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }

        return $this->success('publication unblocked.');
    }

    public function destroy(Post $publication) {
        //$this->authorize( 'destroy', $user );
        
        try {            
            $publication->delete();
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }

        return $this->success( true );
    }

}
