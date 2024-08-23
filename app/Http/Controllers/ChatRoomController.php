<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\ChatRoomStoreRequest;
use App\Http\Requests\Landing\MessageRequest as LandingMessageRequest;
use App\Http\Requests\MessageRequest;
use App\Models\ChatRoom;
use App\Models\Postulation;
use App\Models\Message;
use App\Models\User;

class ChatRoomController extends Controller
{
    public function all()
    {
      $data = ChatRoom::activos()
                      ->with(['messages', 'userCreator', 'addedUser'])
                      ->get();
      
      return $this->showAll($data);
    }
    // se debe consultar el chat room creado por el usuario o al que fue añadido el usuario?
    public function getChatRoomByUser(User $user)
    {
      $data = ChatRoom::activos()
                      ->where('creator_id', $user->id)
                      ->orWhere('receiver_id', $user->id)
                      ->with(['messages', 'userCreator', 'addedUser'])
                      ->get();
      
      return $this->showAll( $data );
    }

    public function getChatRoomByReceiver(User $user)
    {
      $data = ChatRoom::activos()
                      ->orWhere('receiver_id', $user->id)
                      ->with(['messages', 'userCreator', 'addedUser'])
                      ->get();
      
      return $this->showAll( $data );
    }

    public function getMessagesByChatRoom(ChatRoom $chatRoom)
    {
      $chatRoom->load('messages');
            
      return $this->showAll( $chatRoom->messages );
    }
    
    public function sendMessage(LandingMessageRequest $request, ChatRoom $chatRoom)
    {
      $message = $request->validated();
      
      try {
        
        $chatRoom->messages()->create( $message );        
        
        $messages = Message::activos()->where('chat_room_id', $chatRoom->id)->with(['sender'])->get();

        return $this->success( $messages );
        
      } catch (\Exception $exception) {
        return $this->error($exception->getMessage(), 401);
      }    
    }
  
    public function deleteChatRoom(ChatRoom $chatRoom)
    {
      $chatRoom->delete();
  
      return $this->success(true);    
    }

   
}
