<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SportsController extends Controller
{
    private $baseUrl = 'https://www.thesportsdb.com/api/v1/json/3';

        private function fetch($endpoint)
        {
            return Cache::remember($endpoint, 3600, function () use ($endpoint) {
                $response = Http::withoutVerifying()->get($this->baseUrl . $endpoint);
                return $response->json();
            });
        }

        public function leagues()
        {
            $leagues = [
                ['idLeague' => '4328', 'strLeague' => 'English Premier League', 'strSport' => 'Soccer', 'strLeagueAlternate' => 'Premier League, EPL, England'],
                ['idLeague' => '4335', 'strLeague' => 'Spanish La Liga', 'strSport' => 'Soccer', 'strLeagueAlternate' => 'LaLiga Santander, La Liga'],
                ['idLeague' => '4332', 'strLeague' => 'Italian Serie A', 'strSport' => 'Soccer', 'strLeagueAlternate' => 'Italy Serie A'],
                ['idLeague' => '4331', 'strLeague' => 'German Bundesliga', 'strSport' => 'Soccer', 'strLeagueAlternate' => 'Germany Bundesliga'],
                ['idLeague' => '4334', 'strLeague' => 'French Ligue 1', 'strSport' => 'Soccer', 'strLeagueAlternate' => 'Ligue 1 France'],
                ['idLeague' => '4337', 'strLeague' => 'Dutch Eredivisie', 'strSport' => 'Soccer', 'strLeagueAlternate' => 'Eredivisie'],
            ];

            return response()->json([
                'success' => true,
                'data'    => $leagues,
            ]);
        }
                public function teams($leagueId)
                {
                    $data = $this->fetch('/search_all_teams.php?l=' . urlencode($this->getLeagueName($leagueId)));
                    $teams = $data['teams'] ?? [];
                    
                    $mapped = array_map(function($team) {
                        return array_merge($team, [
                            'idTeam' => $team['idTeam'],
                        ]);
                    }, $teams);

                    return response()->json([
                        'success' => true,
                        'data'    => $mapped,
                    ]);
                }

            private function getLeagueName($leagueId)
            {
                $leagues = [
                    '4328' => 'English Premier League',
                    '4335' => 'Spanish La Liga',
                    '4332' => 'Italian Serie A',
                    '4331' => 'German Bundesliga',
                    '4334' => 'French Ligue 1',
                    '4337' => 'Dutch Eredivisie',
                ];
                return $leagues[$leagueId] ?? '';
            }

        public function teamDetail($teamId)
            {
                $leagueIds = ['4328', '4335', '4332', '4331', '4334', '4337'];
                
                foreach ($leagueIds as $leagueId) {
                    $data = $this->fetch('/search_all_teams.php?l=' . urlencode($this->getLeagueName($leagueId)));
                    $teams = $data['teams'] ?? [];
                    
                    foreach ($teams as $team) {
                        if ($team['idTeam'] === $teamId) {
                            return response()->json([
                                'success' => true,
                                'data'    => $team,
                            ]);
                        }
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Tim tidak ditemukan',
                ], 404);
            }

        public function previousMatches($teamId)
        {
            $data = $this->fetch('/eventslast.php?id=' . $teamId);
            return response()->json([
                'success' => true,
                'data'    => $data['results'] ?? [],
            ]);
        }

    public function standings($leagueId)
    {
        $season = date('Y') - 1 . '-' . date('Y');
        $data = $this->fetch('/lookuptable.php?l=' . $leagueId . '&s=' . $season);
        return response()->json([
            'success' => true,
            'data'    => $data['table'] ?? [],
        ]);
    }

    public function searchTeams(Request $request)
    {
        $query = $request->get('q', '');
        $data  = $this->fetch('/searchteams.php?t=' . urlencode($query));
        return response()->json([
            'success' => true,
            'data'    => $data['teams'] ?? [],
        ]);
    }
}