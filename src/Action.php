<?php

declare(strict_types=1);

namespace TelegramBot;

enum Action
{
    case START;
    case SAVE_API_KEY;
    case REQUEST_API_KEY;
    case HANDLE_QUESTION;
} 