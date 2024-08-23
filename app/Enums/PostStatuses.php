<?php

namespace App\Enums;

enum PostStatuses: string {
    case Activo = 'ACTIVE';
    case Pendiente = 'PENDANT';
    case Bloqueado = 'BLOCKED';
    case Finalizado = 'STOPPED';
}