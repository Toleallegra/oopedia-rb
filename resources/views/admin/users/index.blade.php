<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="leaderboard" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <style>
        /* Reset default badge styles */
        .podium-wrapper {
            padding: 2rem 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(0, 78, 152, 0.05) 100%);
            border-radius: 15px;
            margin-top: 30px;
        }

        .podium-display {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 1.5rem;
            padding: 0 2rem;
            animation: fadeIn 1s ease-out;
        }

        .podium-item {
            position: relative;
        }

        .player-avatar {
            background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%);
            border-radius: 25px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 200px;
            margin-bottom: 1rem;
            border: 2px solid rgba(255, 215, 0, 0.3);
            transition: all 0.3s ease;
        }

        .player-avatar:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .first-place .player-avatar {
            padding-top: 40px;
            padding-bottom: 30px;
            min-width: 250px;
            position: relative;
            margin-top: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e6f2ff 100%);
            border: 2px solid rgba(0, 116, 217, 0.3);
            box-shadow: 0 8px 25px rgba(0, 116, 217, 0.2);
        }

        .second-place .player-avatar {
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f0f0 100%);
            border: 2px solid rgba(108, 117, 125, 0.3);
            min-width: 250px;
            /* Match first place width */
            padding-top: 30px;
            /* Similar padding to first place */
            padding-bottom: 30px;
        }

        .third-place .player-avatar {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff5e6 100%);
            border: 2px solid rgba(205, 127, 50, 0.3);
            min-width: 250px;
            /* Match first place width */
            padding-top: 30px;
            /* Similar padding to first place */
            padding-bottom: 30px;
        }

        .podium-base {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            border-radius: 10px;
            margin-top: 0;
            width: 100%;
        }

        .podium-base.first {
            height: 100px;
            background: linear-gradient(135deg, #0074D9 0%, #004E98 100%);
            box-shadow: 0 5px 15px rgba(0, 78, 152, 0.3);
        }

        .podium-base.second {
            height: 80px;
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2);
        }

        .podium-base.third {
            height: 60px;
            background: linear-gradient(135deg, #CD7F32 0%, #A06030 100%);
            box-shadow: 0 3px 10px rgba(205, 127, 50, 0.3);
        }

        .player-name {
            color: #000000;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }

        .score-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            align-items: center;
        }

        .score {
            font-size: 1.5rem;
            font-weight: 700;
            color: #344767;
            transition: all 0.3s ease;
        }

        .player-avatar:hover .score {
            transform: scale(1.1);
            color: var(--color-1);
        }

        /* Enhanced Leaderboard Card Animation */
        .leaderboard-card {
            animation: slideInUp 0.6s ease-out;
            transform-origin: center;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .level-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
            text-align: center;
        }

        /* Define specific badge colors */
        .level-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }

        .level-success {
            background-color: #28a745 !important;
            color: white !important;
        }

        .level-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .level-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }

        /* Override any conflicting styles */
        .podium-item .level-badge {
            margin-top: 5px;
        }

        /* Style untuk badge skor */
        .score-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 0.375rem;
            background-color: #3498db;
            color: white;
        }

        /* Style untuk skor di podium */
        .score-display {
            margin-top: 5px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 3px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        /* Skor pada peringkat pertama */
        .first-place .score-display {
            background-color: rgba(255, 215, 0, 0.3);
        }

        .podium-display {
            animation: fadeIn 1s ease-out;
        }

        .first-place {
            animation: podiumFirst 1s ease-out 0.2s backwards;
        }

        .second-place {
            animation: podiumSecond 1s ease-out 0.4s backwards;
        }

        .third-place {
            animation: podiumThird 1s ease-out 0.6s backwards;
        }

        @keyframes podiumFirst {
            from {
                transform: translateY(50px) scale(0.8);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes podiumSecond {
            from {
                transform: translateX(-50px) scale(0.8);
                opacity: 0;
            }

            to {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes podiumThird {
            from {
                transform: translateX(50px) scale(0.8);
                opacity: 0;
            }

            to {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        .medal-badge {
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        /* Enhanced Medal Badges */
        .medal-badge {
            display: inline-block;
            transform-origin: center;
            animation: medalFloat 3s ease-in-out infinite;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .medal-gold {
            animation-delay: 0s;
        }

        .medal-silver {
            animation-delay: 0.2s;
        }

        .medal-bronze {
            animation-delay: 0.4s;
        }

        @keyframes medalFloat {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            25% {
                transform: translateY(-5px) rotate(5deg);
            }

            75% {
                transform: translateY(5px) rotate(-5deg);
            }
        }

        .medal-badge:hover {
            animation: medalSpin 1s ease-in-out;
        }

        @keyframes medalSpin {
            0% {
                transform: scale(1) rotate(0deg);
            }

            50% {
                transform: scale(1.2) rotate(180deg);
            }

            100% {
                transform: scale(1) rotate(360deg);
            }
        }

        .leaderboard-decoration {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .medal-badge {
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        /* Crown styling */
        .crown-icon {
            position: absolute;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 48px;
            /* Larger size */
            color: #FFD700;
            text-shadow:
                0 0 5px rgba(255, 215, 0, 0.8),
                0 0 10px rgba(255, 140, 0, 0.5),
                0 1px 2px rgba(0, 0, 0, 0.7);
            filter:
                drop-shadow(0 3px 3px rgba(0, 0, 0, 0.3)) drop-shadow(0 0 5px rgba(255, 215, 0, 0.6));
            animation: floatCrown 3s ease-in-out infinite;
            z-index: 5;
        }

        /* More realistic floating animation */
        @keyframes floatCrown {
            0% {
                transform: translateX(-50%) translateY(0) rotate(-2deg);
            }

            50% {
                transform: translateX(-50%) translateY(-7px) rotate(2deg);
            }

            100% {
                transform: translateX(-50%) translateY(0) rotate(-2deg);
            }
        }

        /* First place level badge special styling */
        .first-place .level-badge {
            background: linear-gradient(45deg, #DC3545, #FF4757) !important;
            color: white !important;
            font-weight: 700;
            padding: 8px 16px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        /* Table row hover effect */
        .leaderboard-table tbody tr {
            transition: background-color 0.3s ease;
            position: relative;
        }

        .leaderboard-table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Highlight current user's row */
        .highlight-row {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(13, 110, 253, 0.1));
            border-left: 4px solid #0d6efd;
        }

        .highlight-row:hover {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.15)) !important;
        }

        /* Smooth transition for table cells */
        .leaderboard-table td {
            transition: all 0.3s ease;
        }

        .leaderboard-table tbody tr:hover td {
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .first-place .level-badge {
            background: linear-gradient(45deg, #DC3545, #FF4757) !important;
            color: white !important;
            font-weight: 700;
            padding: 8px 16px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .leaderboard-table tbody tr {
            transition: background-color 0.3s ease;
            position: relative;
        }
    </style>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Leaderboard" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4 leaderboard-card">


                        <div class="card-body px-0 pb-2">
                            <div class="card-header p-0 position-relative  mx-3 z-index-2">
                                <div
                                    class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                    <h6 class="text-white text-capitalize ps-3">Leaderboard</h6>
                                    <p class="text-dark text-sm mb-0 opacity-8">Peringkat Terbaik Mahasiswa</p>
                                    <div class="d-flex me-3">
                                        <div class="leaderboard-decoration">
                                            <span class="medal-badge medal-gold">🥇</span>
                                            <span class="medal-badge medal-silver">🥈</span>
                                            <span class="medal-badge medal-bronze">🥉</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="podium-wrapper mb-5">
                                <div class="podium-display">
                                    <div class="podium-item second-place">
                                        @if (isset($leaderboardData[1]) && $leaderboardData[1]->total_correct_questions > 0)
                                            <div class="player-avatar">
                                                <span class="medal-badge">🥈</span>
                                                <h5 class="player-name">{{ $leaderboardData[1]->name }}</h5>
                                                <span
                                                    class="level-badge level-{{ $leaderboardData[1]->badge_color }}">{{ $leaderboardData[1]->badge }}</span>
                                                <div class="score-display">
                                                    {{ $leaderboardData[1]->formatted_score }}
                                                    poin</div>
                                            </div>
                                            <div class="podium-base second">2</div>
                                        @endif
                                    </div>

                                    <div class="podium-item first-place">
                                        @if (isset($leaderboardData[0]) && $leaderboardData[0]->total_correct_questions > 0)
                                            <i class="fas fa-crown crown-icon"></i>
                                            <div class="player-avatar">
                                                <span class="medal-badge">🥇</span>
                                                <h5 class="player-name">{{ $leaderboardData[0]->name }}</h5>
                                                <span
                                                    class="level-badge level-{{ $leaderboardData[0]->badge_color }}">{{ $leaderboardData[0]->badge }}</span>
                                                <div class="score-display">
                                                    {{ $leaderboardData[0]->formatted_score }}
                                                    poin</div>
                                            </div>
                                            <div class="podium-base first">1</div>
                                        @endif
                                    </div>

                                    <div class="podium-item third-place">
                                        @if (isset($leaderboardData[2]) && $leaderboardData[2]->total_correct_questions > 0)
                                            <div class="player-avatar">
                                                <span class="medal-badge">🥉</span>
                                                <h5 class="player-name">{{ $leaderboardData[2]->name }}</h5>
                                                <span
                                                    class="level-badge level-{{ $leaderboardData[2]->badge_color }}">{{ $leaderboardData[2]->badge }}</span>
                                                <div class="score-display">
                                                    {{ $leaderboardData[2]->formatted_score }}
                                                    poin</div>
                                            </div>
                                            <div class="podium-base third">3</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive p-0 mx-3">
                                <div class="animated-border-table">
                                    <table class="table leaderboard-table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                                    <i class="fas fa-medal me-2"></i>PERINGKAT
                                                </th>
                                                <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                                    <i class="fas fa-user me-2"></i>MAHASISWA
                                                </th>
                                                <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                                    <i class="fas fa-star me-2"></i>LEVEL
                                                </th>
                                                <th
                                                    class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">
                                                    <i class="fas fa-calendar-check me-2"></i>TANGGAL SELESAI
                                                </th>
                                                <th
                                                    class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">
                                                    <i class="fas fa-chart-line me-2"></i>PROGRESS
                                                </th>
                                                <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                                    <i class="fas fa-dollar-sign me-2"></i>SKOR
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($leaderboardData as $data)
                                                @if ($data->total_correct_questions > 0)
                                                    <tr
                                                        class="leaderboard-row @if ($data->id === auth()->id()) highlight-row @endif">
                                                        <td>
                                                            <div class="d-flex px-3 py-2 justify-content-center">
                                                                @if ($data->rank <= 3)
                                                                    <div class="top-rank rank-{{ $data->rank }}">
                                                                        {{ $data->rank }}</div>
                                                                @else
                                                                    <span
                                                                        class="font-weight-bold">{{ $data->rank }}</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex px-2 py-2">
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm">{{ $data->name }}
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="px-2 py-2">
                                                                <span
                                                                    class="level-badge level-{{ $data->badge_color }}">{{ $data->badge }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span
                                                                class="completion-date {{ $data->completion_date ? 'completed' : 'not-completed' }}">
                                                                <i
                                                                    class="fas fa-{{ $data->completion_date ? 'calendar-check' : 'hourglass-half' }}"></i>
                                                                {{ $data->completion_date ? date('d M Y', strtotime($data->completion_date)) : 'Belum selesai' }}
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="progress-wrapper mx-auto">
                                                                <div class="progress leaderboard-progress">
                                                                    <div class="progress-bar bg-gradient-{{ $data->badge_color }}"
                                                                        role="progressbar"
                                                                        style="width: {{ $data->percentage }}%"
                                                                        aria-valuenow="{{ $data->percentage }}"
                                                                        aria-valuemin="0" aria-valuemax="100">
                                                                    </div>
                                                                </div>
                                                                <div class="text-sm text-center mt-1">
                                                                    {{ $data->percentage }}%</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="px-2 py-2">
                                                                <span class="score-badge">{{ $data->formatted_score }}
                                                                    poin</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfeti untuk peringkat teratas
        document.addEventListener('DOMContentLoaded', function() {
            @if ($currentUserRank && $currentUserRank->rank <= 3)
                // Konfeti untuk peringkat 1-3
                const colors = [
                    ['#004e98', '#0074d9'], // Dark blue - peringkat 1
                    ['#0074d9', '#3498db'], // Medium blue - peringkat 2
                    ['#3498db', '#4fc3f7'] // Light blue - peringkat 3
                ];

                const selectedColors = colors[{{ $currentUserRank->rank - 1 }}];

                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: {
                        y: 0.6
                    },
                    colors: selectedColors,
                    startVelocity: 30,
                    gravity: 0.5,
                    ticks: 200,
                    shapes: ['square', 'circle'],
                    zIndex: 1000
                });
            @endif
        });

        function showFeedback(result, score, attemptNumber) {
            // Kode yang sudah dimodifikasi di atas
        }
    </script>
    <x-admin.tutorial />

</x-layout>

{{-- @push('head')
    <link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
    <style>
        /* Reset default badge styles */
        .level-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
            text-align: center;
        }

        /* Define specific badge colors */
        .level-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }

        .level-success {
            background-color: #28a745 !important;
            color: white !important;
        }

        .level-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .level-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }

        /* Override any conflicting styles */
        .podium-item .level-badge {
            margin-top: 5px;
        }

        /* Style untuk badge skor */
        .score-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 0.375rem;
            background-color: #3498db;
            color: white;
        }

        /* Style untuk skor di podium */
        .score-display {
            margin-top: 5px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 3px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        /* Skor pada peringkat pertama */
        .first-place .score-display {
            background-color: rgba(255, 215, 0, 0.3);
        }
    </style>
@endpush --}}

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfeti untuk peringkat teratas
        document.addEventListener('DOMContentLoaded', function() {
            @if ($currentUserRank && $currentUserRank->rank <= 3)
                // Konfeti untuk peringkat 1-3
                const colors = [
                    ['#004e98', '#0074d9'], // Dark blue - peringkat 1
                    ['#0074d9', '#3498db'], // Medium blue - peringkat 2
                    ['#3498db', '#4fc3f7'] // Light blue - peringkat 3
                ];

                const selectedColors = colors[{{ $currentUserRank->rank - 1 }}];

                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: {
                        y: 0.6
                    },
                    colors: selectedColors,
                    startVelocity: 30,
                    gravity: 0.5,
                    ticks: 200,
                    shapes: ['square', 'circle'],
                    zIndex: 1000
                });
            @endif
        });

        function showFeedback(result, score, attemptNumber) {
            // Kode yang sudah dimodifikasi di atas
        }
    </script>
@endpush
