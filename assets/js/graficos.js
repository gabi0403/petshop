// ENGINE DE RENDERIZAÇÃO DE GRÁFICOS

document.addEventListener("DOMContentLoaded", function() {
    // Captura o elemento canvas do Dashboard
    const canvasFaturamento = document.getElementById('graficoFaturamento');
    
    // Proteção: Só executa o script se o canvas realmente existir na página atual
    if (canvasFaturamento) {
        const ctx = canvasFaturamento.getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                datasets: [{
                    label: 'Atendimentos Semanais',
                    data: [15, 22, 18, 29, 25, 38], // Massa de dados simulada para a evolução
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Curvatura elegante da linha
                    pointBackgroundColor: '#2980b9',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false // Esconde a legenda para um visual mais clean
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)' // Linhas de fundo bem suaves
                        }
                    },
                    x: {
                        grid: {
                            display: false // Remove as linhas verticais do fundo
                        }
                    }
                }
            }
        });
    }
});