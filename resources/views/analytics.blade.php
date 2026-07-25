<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SmartSpend - Analytics</title>

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

        @include('partials.sidebar', ['active' => 'analytics'])

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                @include('partials.topbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Analytics</h1>
                    <p class="mb-4">Chart.js is used to generate the charts below. For more customization options,
                        please visit the <a target="_blank" href="https://www.chartjs.org/docs/latest/">official
                            Chart.js documentation</a>.</p>

                    <!-- Content Row (same 8/4 column split as the Charts template page) -->
                    <div class="row">

                        <div class="col-xl-8 col-lg-7">

                            <!-- Area Chart: Income vs Expenses trend -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Income vs. Expenses Trend</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="trendAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Bar Chart: Expenses by Category -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Monthly Expenses by Category</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="expensesBarChart"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Donut Chart: Cumulative Expense Breakdown -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Cumulative Expense Breakdown</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4">
                                        <canvas id="cumulativeDonutChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
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

    <!-- Chart.js -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <script>
        var months = @json($monthLabels);

        // Income vs Expenses trend (area chart)
        var trendCtx = document.getElementById("trendAreaChart");
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: "Income",
                        data: @json($incomeData),
                        backgroundColor: "rgba(28, 200, 138, 0.2)",
                        borderColor: "#1cc88a",
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: "Expenses",
                        data: @json($expenseData),
                        backgroundColor: "rgba(231, 74, 59, 0.2)",
                        borderColor: "#e74a3b",
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                legend: { position: 'bottom' }
            }
        });

        // Expenses by category (stacked bar)
        var expCtx = document.getElementById("expensesBarChart");
        new Chart(expCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: @json($categoryDatasets)
            },
            options: {
                maintainAspectRatio: false,
                legend: { position: 'bottom' },
                scales: {
                    xAxes: [{ stacked: true }],
                    yAxes: [{ stacked: true }]
                }
            }
        });

        // Cumulative expense breakdown (doughnut)
        var cumCtx = document.getElementById("cumulativeDonutChart");
        new Chart(cumCtx, {
            type: 'doughnut',
            data: {
                labels: @json($cumulativeLabels),
                datasets: [{
                    data: @json($cumulativeData),
                    backgroundColor: @json($cumulativeColors),
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 70,
                legend: { position: 'bottom' }
            }
        });
    </script>

</body>

</html>
