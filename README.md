# Instagram Updates Bot

Instagram updates bot is a in-development Laravel-based Telegram bot
to send updates from instagram profiles.

## How to install
You will need:
  * php >= 7.1.3
  * composer
  * yarn
  * MySQL or MariaDB
  * A Telegram bot

You have to clone the repository and cd in the cloned directory:

    git clone https://github.com/LBreda/instagram_updates_bot.git
    cd instagram_updates_bot

Then you can copy `.env.example` to `.env`, and setup the bot. The file is pretty self-explanatory. You can avoid to set the `APP_KEY`

Once you set up the application, you can install it:

    composer install
    php artisan key:generate
    php artisan migrate
    php artisan igud:set-webhook

To run the instagram profiles check, you have to add the command `php artisan igud:check-send` to your crontab. For example, you can check for new posts every 15 minutes by adding to your crontab:

    */15 * * * * cd /path/to/cloned/directory && /path/to/php artisan igud:check-send

You also have to make the /public direcory available on a web server.

Have fun!
