(function ($) {
    $(document).ready(function () {
        $("#dusky_chart_period").on("change", duskyDashboardChart);
        duskyDashboardChart();
    });
})(jQuery);

function duskyDashboardChart(event) {
    if (!document.querySelector("#dusky_chart_period")) {
        return;
    }
    wp.ajax
        .post("dusky_filter_dashboard_usage", {
            data: event ? event.target.value : 7,
        })
        .done(function ($response) {
            const [labels, values, style] = $response;
            const dusky_dark_mode_chart = document.getElementById(
                "dusky_dark_mode_chart",
            );

            const existingChart = Chart.getChart("dusky_dark_mode_chart");
            if (existingChart) {
                existingChart.destroy();
            }

            new Chart(dusky_dark_mode_chart, {
                type: style,
                data: {
                    labels,
                    datasets: [
                        {
                            label: "Dark Mode Users (%): ",
                            data: values,
                            borderWidth: 2,
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        })
        .fail(function () {
            console.log("fail");
        });
}
