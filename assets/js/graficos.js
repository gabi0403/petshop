// ============================================================================
// ENGINE DE RENDERIZAÇÃO: GRÁFICO DE DEMANDA (PIZZA / ROSCA)
// ============================================================================

document.addEventListener("DOMContentLoaded", function() {
    // 1. Captura o elemento onde o gráfico será renderizado
    const canvasServicos = document.getElementById('graficoServicos');
    
    // Proteção de segurança: Só executa se o elemento existir nesta tela
    if (canvasServicos) {
        const ctx = canvasServicos.getContext('2d');
        
        // 2. Criamos o gráfico usando a biblioteca Chart.js
        new Chart(ctx, {
            type: 'doughnut', // gráfico em estilo pizza 
            data: {
                // As etiquetas vêm das categorias do banco (Clínica, Estética, Outros)
                labels: window.dadosGraficoLabels || ['Clínica', 'Estética', 'Outros'],
                datasets: [{
                    label: 'Quantidade de Serviços',
                    // Os valores reais calculados pelo SQL no PHP
                    data: window.dadosGraficoValores || [0, 0, 0], 
                    backgroundColor: [
                        '#3498db', // Azul pra clínica
                        '#2ecc71', // Verde pra estética
                        '#9b59b6'  // Roxo pra outros
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff' // Espaçamento branco entre as fatias
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Permite que ele se ajuste ao tamanho do card
                plugins: {
                    legend: {
                        position: 'bottom', // Move as legendas para baixo para dar mais espaço
                        labels: {
                            font: { size: 13 },
                            usePointStyle: true // Transforma os quadrados da legenda em círculos
                        }
                    }
                }
            }
        });
    }
});