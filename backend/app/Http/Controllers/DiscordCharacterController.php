<?php

namespace App\Http\Controllers;

use App\DiscordCharacter;
use App\Ollyxpic\Character;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DiscordCharacterController extends ApiController
{

    /**
     * Add a character.
     */
    public function add()
    {
        $this->validate(request(), [
            'guild_id' => 'required',
            'name'     => 'required',
            'type'     => 'required'
        ]);

        try {
            $guild = (int) request('guild_id');
            $name = request('name');
            $type = request('type');
            $player = $this->searchPlayer($name);

            if (! isset($player['details'])) return;

            $recentDeath = Carbon::now()->timezone('America/New_York');

            if (isset($player['deaths']) && count($player['deaths']) > 0) {
                $recentDeath = Carbon::createFromFormat('Y-m-d H:i:s', $player['deaths'][0]['date'], 'Europe/Berlin')->timezone('America/New_York')->format('Y-m-d H:i:s');
            }

            $character = (new DiscordCharacter)->updateOrCreate([
                'guild_id' => $guild,
                'character' => $player['details']['name']
            ]);
            $character->level = $player['details']['level'];
            $character->vocation = $player['details']['vocation'];
            $character->world = $player['details']['world'];
            $character->type = $type;
            $character->last_death = $recentDeath;
            $character->save();

            return $this->respond($character->toArray());
        } catch (Exception $e) {
            return $this->respondInternalError($e);
        }
    }

    /**
     * Remove player from list.
     *
     * @return bool|mixed|null
     */
    public function remove()
    {
        $this->validate(request(), [
            'guild_id' => 'required',
            'name'     => 'required',
            'type'     => 'required'
        ]);

        $guild = (int) request('guild_id');
        $name = (string) str_replace('+', ' ', request('name'));
        $type = (string) request('type');

        try {
            return (new DiscordCharacter)
                ->where('discord_characters.guild_id', $guild)
                ->where('discord_characters.character', $name)
                ->where('discord_characters.type', $type)
                ->delete();
        } catch (Exception $e) {
            return $this->respondInternalError($e);
        }
    }

    /**
     * Get player from API.
     *
     * @param $name
     * @return mixed
     */
    private function searchPlayer($name)
    {
        return (new Character())->check($name);
    }
}
