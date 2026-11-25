import Chart from 'chart.js/auto';

/*
 * Dibuja todos los <canvas data-chart="{...}"> de la página con Chart.js.
 * El controlador envía el tipo, las etiquetas y los datos; aquí se aplican los colores
 * del casino y el formato de pesos colombianos.
 */

// Lee una variable CSS (--brand = «159 18 57») y la convierte en color
const cssColor = (name, alpha = 1) =>
    `rgb(${getComputedStyle(document.documentElement).getPropertyValue(name).trim()} / ${alpha})`;

// El primero es el color de marca; después dorado, verde tapete, azul, negro...
const palette = () => [cssColor('--brand'), '#d4af37', '#15803d', '#1d4ed8', '#111827', '#9333ea', '#ea580c', '#64748b'];

const money = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });
const moneyShort = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', notation: 'compact' });
const number = new Intl.NumberFormat('es-CO');

export function renderCharts() {
    Chart.defaults.font.family = 'Figtree, ui-sans-serif, system-ui, sans-serif';
    Chart.defaults.color = '#64748b';

    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        const config = JSON.parse(canvas.dataset.chart);
        const custom = Array.isArray(config.colors) && config.colors.length > 0;
        const colors = custom ? config.colors : palette();
        const round = ['doughnut', 'pie'].includes(config.type);
        const line = config.type === 'line';

        const datasets = config.datasets.map((dataset, i) => ({
            ...dataset,
            backgroundColor: round ? colors : line ? cssColor('--brand', 0.15) : custom ? colors : colors[i],
            borderColor: round ? '#ffffff' : line ? cssColor('--brand') : custom ? '#cbd5e1' : colors[i],
            borderWidth: round ? 3 : line ? 2.5 : custom ? 1 : 0,
            borderRadius: config.type === 'bar' ? 6 : 0,
            maxBarThickness: 42,
            fill: line,
            tension: 0.35,
            cubicInterpolationMode: 'monotone', // la curva no baja de cero entre días sin ventas
            pointRadius: line ? 0 : undefined,
            pointHoverRadius: line ? 5 : undefined,
        }));

        const format = (value) => (config.money ? money.format(value) : number.format(value));
        const formatAxis = (value) => (config.money ? moneyShort.format(value) : number.format(value));
        const valueAxis = { beginAtZero: true, grid: { color: '#eef2f7' }, border: { display: false }, ticks: { callback: formatAxis } };
        const labelAxis = { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } };

        new Chart(canvas, {
            type: config.type,
            data: { labels: config.labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: config.horizontal ? 'y' : 'x',
                cutout: config.type === 'doughnut' ? '65%' : undefined,
                plugins: {
                    legend: {
                        display: round || datasets.length > 1,
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 8, padding: 16 },
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const value = round ? ctx.parsed : config.horizontal ? ctx.parsed.x : ctx.parsed.y;
                                return `${ctx.dataset.label ? ctx.dataset.label + ': ' : ''}${format(value)}`;
                            },
                        },
                    },
                },
                scales: round ? {} : config.horizontal ? { x: valueAxis, y: labelAxis } : { x: labelAxis, y: valueAxis },
            },
        });
    });
}