<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SmartSpend - Savings Goals</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        @include('partials.sidebar', ['active' => 'goals'])

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                @include('partials.topbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Savings Goals</h1>
                        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" type="button" data-toggle="modal"
                            data-target="#createGoalModal">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Create Savings Goal
                        </button>
                    </div>

                    @php
                        $totalSaved = $goals->sum('saved_amount');
                        $totalTarget = $goals->sum('target_amount');
                        $overallProgress = $totalTarget > 0 ? round(($totalSaved / $totalTarget) * 100) : 0;
                    @endphp

                    <!-- Summary stat row (same 4-card layout as the Cards template page) -->
                    <div class="row">

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Saved</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalSaved, 2) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Target</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalTarget, 2) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Active Goals</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $goals->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-track-changes fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Overall Progress</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallProgress }}%</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Goal cards (Dropdown Card pattern from the Cards template page) -->
                    <div class="row">

                        @forelse ($goals as $goal)
                            @php
                                $percent = $goal->target_amount > 0 ? min(100, round(($goal->saved_amount / $goal->target_amount) * 100)) : 0;
                            @endphp
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card shadow h-100">
                                    <div
                                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">{{ $goal->name }}</h6>
                                        <div class="dropdown no-arrow">
                                            <a class="dropdown-toggle" href="#" role="button" id="dropdownGoal{{ $goal->id }}"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                aria-labelledby="dropdownGoal{{ $goal->id }}">
                                                <div class="dropdown-header">Actions</div>
                                                <a class="dropdown-item" href="#">Edit Goal</a>
                                                <a class="dropdown-item text-danger" href="#">Delete Goal</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="small text-gray-500 mb-1">${{ number_format($goal->saved_amount, 2) }} saved of
                                            ${{ number_format($goal->target_amount, 2) }}</div>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <div class="text-right font-weight-bold text-gray-800 mb-3">{{ $percent }}%</div>
                                        <form method="POST" action="{{ route('goals.deposit', $goal) }}"
                                            class="input-group input-group-sm">
                                            @csrf
                                            <input type="number" name="amount" class="form-control"
                                                placeholder="Add savings amount ($)" min="0.01" step="0.01" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">Add Deposit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-gray-500">No savings goals yet. Click "Create Savings Goal" to add one.</p>
                            </div>
                        @endforelse

                    </div>

                                    </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SmartSpend 2026</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @include('partials.create-goal-modal')

    @include('partials.transaction-modal')

    @include('partials.logout-modal')

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
