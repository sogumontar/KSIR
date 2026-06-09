<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;

#[Layout('components.layouts.admin')]
#[Title('Admin Dashboard - Inventory Pro')]
class Dashboard extends Component
{
    public int $totalUsers = 0;
    public int $newThisWeek = 0;
    public int $activeSessions = 0;
    public float $userGrowth = 0.0;
    public string $chartFilter = 'daily';

    public function mount()
    {
        // Users registered this month
        $this->totalUsers = User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        // Growth compared to last month
        $lastMonthUsers = User::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
        if ($lastMonthUsers === 0) {
            $this->userGrowth = $this->totalUsers > 0 ? 100.0 : 0.0;
        } else {
            $this->userGrowth = round((($this->totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1);
        }

        // New users registered this week
        $this->newThisWeek = User::where('created_at', '>=', now()->startOfWeek())->count();

        // Active sessions tracking (within the last 5 minutes)
        $threshold = now()->subMinutes(5)->getTimestamp();
        $this->activeSessions = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('last_activity', '>=', $threshold)
            ->count();
    }

    public function setChartFilter(string $filter)
    {
        $this->chartFilter = $filter;
    }

    public function render()
    {
        $recentUsers = User::latest()->take(5)->get();
        return view('livewire.admin.dashboard', [
            'recentUsers' => $recentUsers,
        ]);
    }
}
