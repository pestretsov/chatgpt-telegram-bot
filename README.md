# Telegram ChatGPT Bot

This is a Telegram bot that acts as a proxy between users and OpenAI's ChatGPT. Each user needs to provide their own OpenAI API key to use the bot.

## Requirements

- PHP 8.2 or higher
- Composer
- SSL certificate (for webhook)

## Installation

1. Clone this repository:
```bash
git clone https://github.com/yourusername/telegram-bot-chatgpt.git
cd telegram-bot-chatgpt
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file and configure it:
```bash
cp .env.example .env
```

4. Edit the `.env` file and add your Telegram bot token and username:
```
TELEGRAM_BOT_TOKEN=your_telegram_bot_token_here
TELEGRAM_BOT_USERNAME=your_bot_username_here
```

## Setting up the Telegram Bot

1. Create a new bot through [@BotFather](https://t.me/BotFather) on Telegram
2. Get the bot token and username
3. Update the `.env` file with these details

## Setting up the Webhook

The bot needs to be accessible via HTTPS. Set up your webhook URL with Telegram:

```
https://api.telegram.org/bot<YourBotToken>/setWebhook?url=https://your-domain.com/path-to-bot/public/webhook.php
```

## Usage

1. Start a chat with your bot on Telegram
2. The bot will ask for your OpenAI API key
3. Provide your OpenAI API key (starts with 'sk-')
4. Start asking questions! The bot will forward them to ChatGPT and return the responses

Note: You'll need to provide your OpenAI API key each time the bot restarts, as it's stored only in memory.

## Security Notes

- API keys are stored only in memory and are cleared when the bot restarts
- Make sure your server is secure and has proper access controls
- Never share your API keys with others

## License

MIT License 