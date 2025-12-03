/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Charts JavaScript File
 * ============================================
 */

(function() {
    'use strict';
    
    // ============================================
    // CHART COLORS
    // ============================================
    const chartColors = {
        primary: '#4F46E5',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        info: '#3B82F6',
        secondary: '#6B7280',
        purple: '#8B5CF6',
        pink: '#EC4899',
        teal: '#14B8A6',
        orange: '#F97316'
    };
    
    const colorPalette = [
        chartColors.primary,
        chartColors.success,
        chartColors.warning,
        chartColors.danger,
        chartColors.info,
        chartColors.purple,
        chartColors.pink,
        chartColors.teal,
        chartColors.orange,
        chartColors.secondary
    ];
    
    // ============================================
    // CHART DEFAULT OPTIONS
    // ============================================
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6B7280';
    Chart.defaults.plugins.legend.position = 'bottom';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 20;
    
    // ============================================
    // DOM READY
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.chartData !== 'undefined') {
            initCoursesCategoryChart();
            initEquipmentTypeChart();
            initEquipmentConditionChart();
            initCourseStatusChart();
        }
    });
    
    // ============================================
    // COURSES BY CATEGORY CHART (Doughnut)
    // ============================================
    function initCoursesCategoryChart() {
        const ctx = document.getElementById('coursesCategoryChart');
        if (!ctx) return;
        
        const data = window.chartData.coursesByCategory;
        
        if (!data || data.length === 0) {
            showNoData(ctx);
            return;
        }
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(item => item.category),
                datasets: [{
                    data: data.map(item => item.count),
                    backgroundColor: colorPalette.slice(0, data.length),
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            generateLabels: function(chart) {
                                const datasets = chart.data.datasets;
                                return chart.data.labels.map(function(label, i) {
                                    return {
                                        text: `${label} (${datasets[0].data[i]})`,
                                        fillStyle: datasets[0].backgroundColor[i],
                                        strokeStyle: datasets[0].backgroundColor[i],
                                        lineWidth: 0,
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // ============================================
    // EQUIPMENT BY TYPE CHART (Bar)
    // ============================================
    function initEquipmentTypeChart() {
        const ctx = document.getElementById('equipmentTypeChart');
        if (!ctx) return;
        
        const data = window.chartData.equipmentByType;
        
        if (!data || data.length === 0) {
            showNoData(ctx);
            return;
        }
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.type),
                datasets: [{
                    label: 'Equipment Count',
                    data: data.map(item => item.count),
                    backgroundColor: colorPalette.slice(0, data.length),
                    borderWidth: 0,
                    borderRadius: 6,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const item = data[context.dataIndex];
                                return `Total Quantity: ${item.total_quantity}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // ============================================
    // EQUIPMENT CONDITION CHART (Polar Area)
    // ============================================
    function initEquipmentConditionChart() {
        const ctx = document.getElementById('equipmentConditionChart');
        if (!ctx) return;
        
        const data = window.chartData.equipmentByCondition;
        
        if (!data || data.length === 0) {
            showNoData(ctx);
            return;
        }
        
        const conditionLabels = {
            'new': 'New',
            'good': 'Good',
            'fair': 'Fair',
            'poor': 'Poor',
            'maintenance': 'Maintenance'
        };
        
        const conditionColors = {
            'new': chartColors.success,
            'good': chartColors.primary,
            'fair': chartColors.warning,
            'poor': chartColors.danger,
            'maintenance': chartColors.secondary
        };
        
        new Chart(ctx, {
            type: 'polarArea',
            data: {
                labels: data.map(item => conditionLabels[item.condition] || item.condition),
                datasets: [{
                    data: data.map(item => item.count),
                    backgroundColor: data.map(item => {
                        const color = conditionColors[item.condition] || chartColors.secondary;
                        return color + '99'; // Add transparency
                    }),
                    borderColor: data.map(item => conditionColors[item.condition] || chartColors.secondary),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    
    // ============================================
    // COURSE STATUS CHART (Pie)
    // ============================================
    function initCourseStatusChart() {
        const ctx = document.getElementById('courseStatusChart');
        if (!ctx) return;
        
        const stats = window.chartData.courseStats;
        
        if (!stats) {
            showNoData(ctx);
            return;
        }
        
        const data = [
            { label: 'Scheduled', value: stats.scheduled, color: chartColors.primary },
            { label: 'Completed', value: stats.completed, color: chartColors.success },
            { label: 'Cancelled', value: stats.cancelled, color: chartColors.danger }
        ].filter(item => item.value > 0);
        
        if (data.length === 0) {
            showNoData(ctx);
            return;
        }
        
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.map(item => item.label),
                datasets: [{
                    data: data.map(item => item.value),
                    backgroundColor: data.map(item => item.color),
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // ============================================
    // HELPER: SHOW NO DATA MESSAGE
    // ============================================
    function showNoData(canvas) {
        const container = canvas.parentElement;
        container.innerHTML = `
            <div class="empty-state" style="height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                <i class="fas fa-chart-bar" style="font-size: 2rem; color: #9CA3AF; margin-bottom: 0.5rem;"></i>
                <p style="color: #9CA3AF; font-size: 0.875rem;">No data available</p>
            </div>
        `;
    }
    
})();