<?php 

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Tournament $tournament)
    {
        $teams = $tournament->teams;
        return view('teams.index', compact('tournament', 'teams'));
    }

    public function create(Tournament $tournament)
    {
        return view('teams.create', compact('tournament'));
    }

    public function store(Request $request, Tournament $tournament)
    {
        $request->validate([
            'teamName' => 'required|string|max:255',
        ]);

        $team = new Team([
            'teamName' => $request->input('teamName'),
        ]);

        $tournament->teams()->save($team);

        return redirect()->route('tournaments.teams.index', $tournament->id)->with('success', 'Team created successfully!');
    }

    public function edit(Tournament $tournament, Team $team)
    {
        return view('teams.edit', compact('tournament', 'team'));
    }

    public function update(Request $request, Tournament $tournament, Team $team)
    {
        $request->validate([
            'teamName' => 'required|string|max:255',
        ]);

        $team->update([
            'teamName' => $request->input('teamName'),
        ]);

        return redirect()->route('tournaments.teams.index', $tournament->id)->with('success', 'Team updated successfully!');
    }

    public function destroy(Tournament $tournament, Team $team)
    {
        $team->delete();

        return redirect()->route('tournaments.teams.index', $tournament->id)->with('success', 'Team deleted successfully!');
    }
}