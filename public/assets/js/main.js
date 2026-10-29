/**
 * SABORLOCAL - SISTEMA DE GESTIÓN DE PEDIDOS
 * Lógica de cliente, Gráficos Chart.js e Interactividad
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Alternar Sidebar en móviles
    const sidebarToggle = document.getElementById('sidebarToggle');
    const appSidebar = document.getElementById('appSidebar');

    if (sidebarToggle && appSidebar) {
        sidebarToggle.addEventListener('click', function () {
            appSidebar.classList.toggle('show');
        });

        // Cerrar al hacer clic fuera en móviles
        document.addEventListener('click', function (e) {
            if (window.innerWidth < 992 && !appSidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                appSidebar.classList.remove('show');
            }
        });
    }

    // 2. Atajo de teclado (Cmd+K o Ctrl+K) para enfocar el buscador
    document.addEventListener('keydown', function (e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('globalSearchInput');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // 3. Reloj en tiempo real
    function updateLiveClock() {
        const clockEl = document.getElementById('liveClock');
        if (!clockEl) return;
        const now = new Date();
        const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'];

        const dayName = days[now.getDay()];
        const dayNum = String(now.getDate()).padStart(2, '0');
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();

        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // Formato 12 horas
        const hoursStr = String(hours).padStart(2, '0');

        clockEl.textContent = `${dayName}, ${dayNum} ${monthName} ${year} | ${hoursStr}:${minutes} ${ampm}`;
    }
    setInterval(updateLiveClock, 30000);

    // 4. Inicialización de Gráficos Chart.js si estamos en el Dashboard
    if (window.chartData) {
        initDashboardCharts(window.chartData);
    }
});

/**
 * Configuración de Gráficos del Dashboard
 */
function initDashboardCharts(data) {
    // A. Gráfico de Línea: Ventas por día
    const salesCanvas = document.getElementById('salesLineChart');
    if (salesCanvas) {
        const ctx = salesCanvas.getContext('2d');

        // Crear gradiente suave debajo de la curva
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.28)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.sales.labels,
                datasets: [{
                    label: 'Ventas (S/)',
                    data: data.sales.values,
                    borderColor: '#2563eb',
                    backgroundColor: gradient,
                    borderWidth: 2.8,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4.5,
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#94a3b8',
                        bodyColor: '#ffffff',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return ' Ventas: S/ ' + Number(context.raw).toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { family: "'Plus Jakarta Sans', sans-serif", size: 11.5 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11.5 },
                            callback: function (val) { return 'S/ ' + val; }
                        }
                    }
                }
            }
        });
    }

    // B. Gráfico Dona: Pedidos por estado
    const doughnutCanvas = document.getElementById('ordersDoughnutChart');
    if (doughnutCanvas) {
        const m = data.metrics;
        new Chart(doughnutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Confirmado', 'En preparación', 'Listo para entregar', 'En camino', 'Entregado', 'Cancelado'],
                datasets: [{
                    data: [
                        m.pendientes || 0,
                        m.confirmados || 0,
                        m.en_preparacion || 0,
                        m.listos || 0,
                        m.en_camino || 0,
                        m.entregados || 0,
                        m.cancelados || 0
                    ],
                    backgroundColor: [
                        '#f59e0b', // Pendiente (naranja)
                        '#d97706', // Confirmado (ámbar)
                        '#8b5cf6', // En preparación (púrpura)
                        '#06b6d4', // Listo para entregar (cyan)
                        '#2563eb', // En camino (azul)
                        '#10b981', // Entregado (verde)
                        '#ef4444'  // Cancelado (rojo)
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                return ` ${ctx.label}: ${ctx.raw} pedidos`;
                            }
                        }
                    }
                }
            }
        });
    }
}

/**
 * Confirmación con SweetAlert2 para acciones de cancelación o eliminación
 */
function confirmAction(message, onConfirmCallback) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Está seguro(a)?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary px-4 me-2',
                cancelButton: 'btn btn-secondary px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof onConfirmCallback === 'function') {
                    onConfirmCallback();
                }
            }
        });
    } else {
        if (confirm(message)) {
            if (typeof onConfirmCallback === 'function') {
                onConfirmCallback();
            }
        }
    }
}
