<?php

namespace App\Enums;

enum UserStatusEnum: string {
    case Activo = 'ACTIVE';
    case Pendiente = 'PENDANT';
    case Bloqueado = 'BLOCKED';
}