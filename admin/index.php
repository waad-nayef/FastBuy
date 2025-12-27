<!doctype html>
<html lang="en">
<?php
require_once "../config/db.php";
$db = Database::getInstance();
?>

<head>
  <meta charset="utf-8" />
  <title>FastBuy | Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />

  <!-- Fonts -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />

  <!-- OverlayScrollbars -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="./css/adminlte.css" />

  <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/8bb0a97d35.js" crossorigin="anonymous"></script>

  <!-- ApexCharts CSS (for revenue chart) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" />
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <?php
    include "../admin-components/navbar.php";
    include "../admin-components/sidebar.php";
    ?>

    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <h3 class="mb-0">Dashboard</h3>
            </div>
          </div>
        </div>
      </div>

      <?php include "cards.php"; ?>
    </main>

    <?php include "../admin-components/footer.php"; ?>
  </div>

  <!-- JS Plugins -->
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
  <script src="./js/adminlte.js"></script>

  <!-- OverlayScrollbars initialization -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector('.sidebar-wrapper');
      if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: 'os-theme-light',
            autoHide: 'leave',
            clickScroll: true
          }
        });
      }
    });
  </script>

  <!-- ApexCharts -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"></script>
  <script>
    <?php
    $topCategories = $db->query("
       SELECT c.name, SUM(p.amount) AS total_revenue
        FROM categories c
        JOIN products pr ON c.id = pr.category_id
        JOIN order_items oi ON pr.id = oi.product_id
         JOIN orders o ON oi.order_id = o.id
         JOIN payments p ON o.id = p.order_id
          WHERE p.status = 'paid'
         GROUP BY c.id
         ORDER BY total_revenue DESC
         LIMIT 5
")->fetchAll();

    $categoryNames = [];
    $categoryRevenue = [];

    foreach ($topCategories as $cat) {
      $categoryNames[] = $cat['name'];
      $categoryRevenue[] = (float)$cat['total_revenue'];
    }
    ?>
    const sales_chart_options = {
      series: [{
        name: 'Revenue',
        data: <?php echo json_encode($categoryRevenue); ?>
      }],
      chart: {
        height: 300,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          borderRadius: 2,
          horizontal: false
        }
      },
      xaxis: {
        categories: <?php echo json_encode($categoryNames); ?>
      },
      colors: ['#0d6efd'],
      dataLabels: {
        enabled: false
      },
      legend: {
        show: false
      },
      tooltip: {
        y: {
          formatter: function(val) {
            return '$' + val.toFixed(2);
          }
        }
      }
    };

    const sales_chart = new ApexCharts(document.querySelector('#revenue-chart'), sales_chart_options);
    sales_chart.render();
  </script>
</body>

</html>