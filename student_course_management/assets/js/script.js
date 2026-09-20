document.querySelectorAll('.stat-number').forEach(el => {
    const target = parseInt(el.getAttribute('data-count'), 10) || 0;
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 40));
    const interval = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(interval);
        }
        el.textContent = current;
    }, 25);
});

const canvas = document.getElementById('enrollmentChart');
if (canvas) {
    const labels = JSON.parse(canvas.dataset.labels);
    const values = JSON.parse(canvas.dataset.values);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Students Enrolled',
                data: values,
                backgroundColor: '#4e73df',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}