<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;

class NotificationController extends Controller
{
   
        public function getNotificationByUser()
        {     
                try {
                        if(auth()->user()){
                                return $this->showAll(auth()->user()->notifications);
                        }
                        
                } catch (\Exception $th) {
                        return $this->error($th->getMessage(), 401);
                }
        }


        public function readed($id)//read one by one
        {
             try {
                        $notification = auth()->user()->notifications()->where('id', $id)->first();

                        if ($notification) {
                                $notification->markAsRead();
                        }

                        return $this->success("Notification successfully readed");

                } catch (\Exception $th) {
                        return $this->error($th->getMessage(), 401);
                }
              
        }

        public function markAllAsReaded(Request $request)
        {
                 try {
                        if(auth()->user()){
                                auth()->user()->unreadNotifications()->update(['read_at' => now()]);
                        }

                        return $this->success("All notifications successfully readed");

                } catch (\Exception $th) {
                        return $this->error($th->getMessage(), 401);
                }
        }

        public function destroy($id)
        {
                try{
                        $notification = auth()->user()->notifications()->where('id', $id)->first();
                        $notification->delete();

                        return $this->success('Notifications successfully deleted');
                }catch (\Exception $th) {
                        return $this->error($th->getMessage(),401);
                }
        }


        public function destroyAll()
        {
                try{
                        if(auth()->user()){
                                auth()->user()->notifications()->delete();
                        }
                        
                        return $this->success('Notifications successfully deleted');
                }catch (\Exception $th) {
                        return $this->error($th->getMessage(),401);
                }
        }

}
