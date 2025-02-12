<?php

declare(strict_types=1);

namespace TelegramBot;

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\Message;
use OpenAI;
use RuntimeException;

final class Bot
{
    /** @var array<int, string> */
    private array $apiKeys = [];

    public function __construct(
        private readonly Telegram $telegram
    ) {}

    public function handleUpdate(array $update): void
    {
        $update = new Update($update);
        $message = $update->getMessage();
        
        if (!$message instanceof Message) {
            return;
        }

        $chatId = $message->getChat()->getId();
        $text = $message->getText();

        if ($text === null) {
            return;
        }

        match ($this->determineAction($chatId, $text)) {
            Action::START => $this->handleStart($chatId),
            Action::SAVE_API_KEY => $this->saveApiKey($chatId, $text),
            Action::REQUEST_API_KEY => $this->requestApiKey($chatId),
            Action::HANDLE_QUESTION => $this->handleQuestion($chatId, $text),
        };
    }

    private function determineAction(int $chatId, string $text): Action
    {
        if ($text === '/start') {
            return Action::START;
        }

        if (!isset($this->apiKeys[$chatId])) {
            return $this->isValidApiKey($text) 
                ? Action::SAVE_API_KEY 
                : Action::REQUEST_API_KEY;
        }

        return Action::HANDLE_QUESTION;
    }

    private function isValidApiKey(string $key): bool
    {
        return (bool) preg_match('/^sk-[a-zA-Z0-9]{48}$/', $key);
    }

    private function handleStart(int $chatId): void
    {
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => "Welcome! To start using this bot, please provide your OpenAI API key.\n\n" .
                     "You can get it from: https://platform.openai.com/api-keys\n\n" .
                     "Note: You'll need to provide your API key each time the bot restarts.",
        ]);
    }

    private function saveApiKey(int $chatId, string $apiKey): void
    {
        $this->apiKeys[$chatId] = $apiKey;
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Thank you! Your OpenAI API key has been saved. You can now start asking questions!',
        ]);
    }

    private function requestApiKey(int $chatId): void
    {
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Please provide your OpenAI API key to start using the bot. It should start with "sk-"',
        ]);
    }

    private function handleQuestion(int $chatId, string $question): void
    {
        try {
            $this->sendTypingAction($chatId);
            $answer = $this->getAnswerFromOpenAI($chatId, $question);
            $this->sendAnswer($chatId, $answer);
        } catch (RuntimeException $e) {
            $this->handleError($chatId, $e);
        }
    }

    private function sendTypingAction(int $chatId): void
    {
        Request::sendChatAction([
            'chat_id' => $chatId,
            'action' => 'typing',
        ]);
    }

    private function getAnswerFromOpenAI(int $chatId, string $question): string
    {
        $client = OpenAI::client($this->apiKeys[$chatId]);
        
        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $question],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    private function sendAnswer(int $chatId, string $answer): void
    {
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $answer,
            'parse_mode' => 'Markdown',
        ]);
    }

    private function handleError(int $chatId, RuntimeException $e): void
    {
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => "Error: " . $e->getMessage() . "\n\nIf this is an API key error, you can provide a new API key.",
        ]);
        unset($this->apiKeys[$chatId]);
    }
} 