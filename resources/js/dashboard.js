document.addEventListener("DOMContentLoaded", () => {

    const ctx = document.getElementById("chartBuku");

    if (!ctx) return;

    const labelAsli = window.bukuJudulAsli ?? [];
    const bukuValues = window.bukuValues ?? [];

    const bukuLabels = labelAsli.map(j =>
        j.length > 20 ? j.substring(0, 20) + "..." : j
    );

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bukuLabels,
            datasets: [{
                label: "Jumlah Dipinjam",
                data: bukuValues,
                backgroundColor: "#3b82f6",
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                tooltip: {
                    callbacks: {
                        title: (items) => labelAsli[items[0].dataIndex]
                    }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

});
