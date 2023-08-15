<?php 
namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::all();
        return view('tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('tournaments.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tournamentName' => 'required|string|max:255',
            'teamSize' => 'required|in:4,8',
        ]);

        Tournament::create($validatedData);

        return redirect()->route('tournaments.index')->with('success', 'Tournament created successfully!');
    }

    public function edit(Tournament $tournament)
    {
        return view('tournaments.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $validatedData = $request->validate([
            'tournamentName' => 'required|string|max:255',
            'teamSize' => 'required|in:4,8',
        ]);

        $tournament->update($validatedData);

        return redirect()->route('tournaments.index')->with('success', 'Tournament updated successfully!');
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();

        return redirect()->route('tournaments.index')->with('success', 'Tournament deleted successfully!');
    }
    public function result(Tournament $tournament, $teamSize)
    {
        $teams = $tournament->teams->take($teamSize)->pluck('teamName')->toArray();

        $rounds = log($teamSize, 2);

        $winners = [];

        for ($i = 0; $i < $teamSize / 2; $i++) {
            $winners[1][] = $this->getRandomWinner([$teams[$i * 2], $teams[$i * 2 + 1]]);
        }
 
        for ($round = 2; $round <= $rounds; $round++) {
            for ($i = 0; $i < count($winners[$round - 1]) / 2; $i++) {
                $winners[$round][] = $this->getRandomWinner([$winners[$round - 1][$i * 2], $winners[$round - 1][$i * 2 + 1]]);
            }
        } 
        $final_winner = $this->getRandomWinner($winners[$rounds]);

        return view('tournaments.result', compact('tournament', 'teamSize', 'teams', 'final_winner', 'winners'));
    }

    private function getRandomWinner($teams)
    {
        return $teams[rand(0, count($teams) - 1)];
    }


}
?>