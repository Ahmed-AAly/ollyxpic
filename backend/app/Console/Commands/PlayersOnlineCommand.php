<?php

namespace App\Console\Commands;

use App\DiscordCharacter;
use App\DiscordGuild;
use App\Events\CharactersChangedEvent;
use App\Events\CharactersDiedEvent;
use App\Events\CharactersOnlineEvent;
use App\Jobs\CharactersChangedJob;
use App\Ollyxpic\TibiaData\WorldOnlinesAPI;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Goutte\Client;
use App\Ollyxpic\Crawlers\CharacterCrawler;

class PlayersOnlineCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ollyxpic:online {type=friends}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get online characters from discord.';

    /**
     * Players online.
     *
     * @var
     */
    protected $onlines;

    /**
     * PlayersOnlineCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $guilds = (new DiscordGuild)->get();
        $guilds->each(function ($guild) {
            $this->info("Guild: {$guild->name}");
            $this->onlines = [];
            $type = $this->argument('type');

            $characters = $guild->characters()->$type()->get()->toArray();
            $charactersName = $this->getCharactersNames($characters);
            $worlds = $this->getWorlds($characters);

            foreach ($worlds as $world) {
                $onlines = array_filter((new WorldOnlinesAPI($world))->get(), function ($character) use ($charactersName) {
                    return in_array($this->clearString($character['character']), $charactersName);
                });

                $this->onlines = array_merge($this->onlines, $onlines);
            }

            $onlines = $this->getCharactersNames($this->onlines);

            // This event will be queued.
            dispatch(new CharactersChangedJob($guild, $characters, $this->onlines, $type));
            $this->info('Emitted Event: @CharactersChangedJob');
            $this->info('___________');

            foreach ($this->onlines as $online) {
                $guild->characters()->$type()->where('character', $online['character'])->update([
                    'online' => 1
                ]);
            }

            $totalOnlines = count($this->onlines);
            $this->info("Onlines: {$totalOnlines}");
            $guild->characters()->$type()->whereNotIn('character', $onlines)->update(['online' => 0]);

            event(new CharactersOnlineEvent($guild->guild_id, $type));
            $this->info('Emitted Event: @CharactersOnlineEvent');
        });
    }

    /**
     * Get worlds names from charactes list.
     *
     * @param $characters
     * @return array
     */
    private function getWorlds($characters)
    {
        return array_unique(array_map(function ($character) {
            return $character['world'];
        }, $characters));
    }

    /**
     * Format the character list only showing character names.
     *
     * @param $characters
     * @return array
     */
    private function getCharactersNames($characters)
    {
        return array_map(function ($character) {
            return $character['character'];
        }, $characters);
    }

    /**
     * Remove invisible chars from string.
     *
     * @param $string
     * @return mixed
     */
    private function clearString($string)
    {
        $string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', trim($string));

        return preg_replace('/\s+/', ' ', $string);
    }
}
