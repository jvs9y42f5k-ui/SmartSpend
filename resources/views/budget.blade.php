<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SmartSpend - Budget Limit</title>

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

        @include('partials.sidebar', ['active' => 'budget'])

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                @include('partials.topbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Budget Limit</h1>
                    </div>

                    <!-- Summary stat row (same 4-card layout as the Cards template page) -->
                    <div class="row">

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Limit</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                ${{ number_format($totalLimit, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
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
                                                Total Spent</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                ${{ number_format($totalSpent, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Over Budget</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $overBudgetCount }} categories</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                                On Track</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $onTrackCount }} categories</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Budget category cards -->
                    <div class="row">

                        @foreach ($budgetData as $item)
                            <div class="col-lg-6">
                                <div class="card shadow mb-4">
                                    <div
                                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">{{ $item['category'] }}</h6>
                                        <div class="dropdown no-arrow">
                                            <a class="dropdown-toggle" href="#" role="button"
                                                id="dropdown{{ $loop->index }}" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                aria-labelledby="dropdown{{ $loop->index }}">
                                                <div class="dropdown-header">Actions</div>
                                                <a class="dropdown-item" href="/tables">View Transactions</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="small text-gray-500 mb-2">
                                            ${{ number_format($item['spent'], 2) }} /
                                            ${{ number_format($item['limit'], 2) }}
                                        </div>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar {{ $item['overBudget'] ? 'bg-danger' : ($item['percent'] >= 80 ? 'bg-warning' : 'bg-success') }}"
                                                role="progressbar" style="width: {{ $item['percent'] }}%"></div>
                                        </div>
                                        @if ($item['overBudget'])
                                            <span class="badge badge-danger">OVER BUDGET</span>
                                        @else
                                            <span class="small text-gray-600">{{ $item['percent'] }}% used</span>
                                        @endif

                                        <form method="POST" action="{{ route('budgets.store') }}"
                                            class="input-group input-group-sm mt-3">
                                            @csrf
                                            <input type="hidden" name="category" value="{{ $item['category'] }}">
                                            <input type="number" name="limit_amount" class="form-control"
                                                placeholder="Set limit ($)"
                                                value="{{ $item['limit'] > 0 ? $item['limit'] : '' }}" min="0"
                                                step="0.01" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">Set Limit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach

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
