<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\MessageRequest;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
      
  public function all()
  {
    $messages = Message::activos()->get();

    return $this->showAll($messages);
  }

  public function getMessagesByUser(User $user)
  { // En que caso de uso se van a consultar todos los mensajes enviados por un usuario
    // sin importar el chat?
    $messages = Message::activos()->where('creator_id', $user->id)->with(['sender'])->get();

    return $this->showAll($messages);
  }

  public function getMessagesByChatRoom(ChatRoom $chatRoom)
  {
    $messages = $chatRoom->messages()->activos()->with('sender')->get();
    
    return $this->showAll($messages);
  }

  public function destroy(Message $message)
  {    
    $message->delete();
      
    return $this->success(true);
  }

  public function readed(Message $message)
  {
    $message->markAsRead();

    return $this->success($message->load(['sender']));          
  }
}
