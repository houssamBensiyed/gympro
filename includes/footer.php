            </div>
        </main>
    </div>
    
    <script>
        // Chart initialization
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart !== 'undefined' && window.chartData) {
                const chartColors = {
                    mint: '#22c55e',
                    dark: '#27272a',
                    gray: '#4a4a51',
                    text: '#868691'
                };
                
                Chart.defaults.color = chartColors.text;
                Chart.defaults.borderColor = chartColors.dark;
                
                // Courses by Category
                if (document.getElementById('coursesCategoryChart') && window.chartData.coursesByCategory) {
                    new Chart(document.getElementById('coursesCategoryChart'), {
                        type: 'doughnut',
                        data: {
                            labels: window.chartData.coursesByCategory.map(c => c.category),
                            datasets: [{
                                data: window.chartData.coursesByCategory.map(c => c.count),
                                backgroundColor: ['#22c55e', '#3f3f45', '#27272a', '#4a4a51', '#1c1c1f'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'right', labels: { boxWidth: 8, padding: 8 } } }
                        }
                    });
                }
                
                // Equipment by Type
                if (document.getElementById('equipmentTypeChart') && window.chartData.equipmentByType) {
                    new Chart(document.getElementById('equipmentTypeChart'), {
                        type: 'bar',
                        data: {
                            labels: window.chartData.equipmentByType.map(e => e.type),
                            datasets: [{
                                label: 'Count',
                                data: window.chartData.equipmentByType.map(e => e.count),
                                backgroundColor: chartColors.mint,
                                borderRadius: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, grid: { color: chartColors.dark } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            }
        });
        
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            });
        }
        
        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
        
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
        
        // Confirm dialogs
        document.querySelectorAll('[data-confirm]').forEach(el => {
            el.addEventListener('click', e => {
                if (!confirm(el.dataset.confirm)) e.preventDefault();
            });
        });
    </script>
</body>
</html>