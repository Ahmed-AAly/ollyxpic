<?php

namespace App\Http\Controllers;

use App\HighscoreMigration;
use App\Highscores;
use App\World;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HighscoresController extends ApiController
{

    /**
     * Return highscores by experience.
     *
     * @return mixed
     */
    public function experience()
    {
        $migration = HighscoreMigration::where('active', 1)->orderBy('id', 'desc')->first();
        $world = request('world') ? World::where('name', request('world'))->first()->id : null;

        $highscores = (new Highscores)
            ->with('world')
            ->with('weekExperience')
            ->where('migration_id', $migration->id)
            ->whereIn('vocation', $this->getVocation())
            ->where(function ($query) use ($world) {
                if ($world)
                    $query->where('world_id', $world);
            })
            ->orderBy('experience', 'desc')
            ->take(300)
            ->get();

        return $this->respond($highscores->toArray());
    }

    /**
     * Get highscores by skill.
     *
     * @return mixed
     */
    public function skills()
    {
        $date = (new Highscores)
            ->select(DB::raw("max(updated_at) as date"))
            ->where('active', 1)
            ->where('type', request('skill'))
            ->first()
            ->date;
        $world = request('world') ? World::where('name', request('world'))->first()->id : null;

        $highscores = (new Highscores)
            ->with('world')
            ->where('type', request('skill'))
            ->where('active', 1)
            ->where(function ($query) use ($world) {
                if ($world)
                    $query->where('world_id', $world);
            })
            ->where('updated_at', $date)
            ->orderBy('level', 'desc')
            ->orderBy('name', 'asc')
            ->take(300)
            ->get();

        return $this->respond($highscores->toArray());
    }

    /**
     * Get the requested vocation.
     *
     * @return array|bool
     */
    private function getVocation()
    {
        switch (request('vocation')) {
            case 'knight':
                return ['Knight', 'Elite Knight'];
            case 'sorcerer':
                return ['Sorcerer', 'Master Sorcerer'];
            case 'druid':
                return ['Druid', 'Elder Druid'];
            case 'paladin':
                return ['Paladin', 'Royal Paladin'];
            default:
                return ['Knight', 'Elite Knight', 'Sorcerer', 'Master Sorcerer', 'Paladin', 'Royal Paladin', 'Druid', 'Elder Druid'];
        }
    }
}
