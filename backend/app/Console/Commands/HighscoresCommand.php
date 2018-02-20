<?php

namespace App\Console\Commands;

use App\HighscoreMigration;
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
    protected $signature = 'ollyxpic:highscores';

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
     * Date.
     *
     * @var
     */
    protected $date;

    /**
     * Migration.
     *
     * @var
     */
    protected $migration;

    /**
     * Results.
     *
     * @var
     */
    protected $results;

    /**
     * Highscores constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->highscores = [];
        $this->results = 0;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->date = Carbon::today()->subDay();
        (new HighscoreMigration())
            ->where('type', 'experience')
            ->where('migration_date', $this->date)
            ->delete();

        $this->migration = HighscoreMigration::create([
            'type'           => 'experience',
            'results'        => 0,
            'active'         => 0,
            'migration_date' => $this->date
        ]);

        $vocations = ['knight', 'sorcerer', 'paladin', 'druid'];

        foreach ($vocations as $vocation) {
            $worlds = World::orderBy('name', 'asc')->get();
            $worlds->each(function ($world) use ($vocation) {
                $start = microtime(true);

                $highscores = file_get_contents("https://api.tibiadata.com/v2/highscores/{$world->name}/experience/{$vocation}.json");
                $highscores = json_decode($highscores);
                $highscores = $highscores->highscores->data;

                array_walk($highscores, function ($highscore) use ($world, $start) {
                    Highscores::create([
                        'rank'         => $highscore->rank,
                        'name'         => $highscore->name,
                        'vocation'     => $highscore->voc,
                        'experience'   => isset($highscore->points) ? $highscore->points : 0,
                        'level'        => isset($highscore->level) ? $highscore->level : 0,
                        'world_id'     => $world->id,
                        'updated_at'   => $this->date,
                        'migration_id' => $this->migration->id,
                        'active'       => 0,
                        'type'         => 'experience',
                    ]);

                    $this->results = $this->results + 1;
                    $percentage = ($this->results * 100) / 76800;
                    $time = microtime(true) - $start;
                    $remains = number_format((($time * 256) - ($percentage * ($time * 256)) / 100) / 60, 2);
                    $minutes = floor($remains);
                    $seconds = round(60 * ($remains - $minutes));
                    $remains = "{$minutes}:$seconds";

                    if (is_int($percentage) == 1) {
                        system('clear');
                        $this->info("{$percentage}% completed of 100%.");
                        $this->info("Time remains: {$remains} minutes.");
                    }
                });
            });
        }

        $this->migration->results = $this->results;
        $this->migration->active = 1;
        $this->migration->save();
        (new Highscores)
            ->where('active', 0)
            ->where('updated_at', $this->date)
            ->where('type', 'experience')
            ->update(['active' => 1]);

//        (new HighscoreMigration())
//            ->where('type', 'experience')
//            ->where('migration_date', '<', $this->date)
//            ->delete();
    }
}
