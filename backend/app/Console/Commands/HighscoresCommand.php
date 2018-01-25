<?php

namespace App\Console\Commands;

use App\World;
use App\Highscores;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HighscoresCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ollyxpic:highscores {type=experience}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Highscores from all Tibia Worlds.';

    /**
     * Highscores.
     *
     * @var
     */
    protected $highscores;

    /**
     * Highscores constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->highscores = [];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        for ($i = 7; $i >= 1; $i--) {
            $worlds = World::orderBy('name', 'asc')->get();
            $worlds->each(function ($world) use($i) {
                $highscores = file_get_contents("https://api.tibiadata.com/v2/highscores/{$world->name}/{$this->argument('type')}.json");
                $highscores = json_decode($highscores);
                $highscores = $highscores->highscores->data;

                array_walk($highscores, function ($highscore, $index) use ($world, $i) {
                    $today = Carbon::today()->subDays($i);
                    $experience = intval($highscore->points * (($today->day / 100) + 1));
                    $older = Highscores::where('name', $highscore->name)->orderBy('updated_at', 'desc')->first();
                    $advance = $older ? intval($experience - $older->experience) : 0;

                    Highscores::create([
                        'rank'       => $index + 1,
                        'name'       => $highscore->name,
                        'vocation'   => $highscore->voc,
                        'experience' => $experience,
                        'level'      => $highscore->level,
                        'advance'    => $advance,
                        'world_id'   => $world->id,
                        'updated_at' => $today,
                        'type'       => $this->argument('type'),
                    ]);
                });
            });
        }
    }
}
