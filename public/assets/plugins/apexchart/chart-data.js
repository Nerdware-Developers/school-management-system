'use strict';

document.addEventListener("DOMContentLoaded", function () {
    const chartDataEl = document.getElementById('chart-data');
    if (!chartDataEl) return;

    const studentData = JSON.parse(chartDataEl.dataset.students);
    const teacherData = JSON.parse(chartDataEl.dataset.teachers);
    const boys = parseInt(chartDataEl.dataset.boys);
    const girls = parseInt(chartDataEl.dataset.girls);
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // Line Chart
    if (document.querySelector("#apexcharts-area")) {
        const options = {
            chart: { height: 350, type: "line", toolbar: { show: false } },
            dataLabels: { enabled: false },
            stroke: { curve: "smooth" },
            series: [
                { name: "Teachers", color: '#3D5EE1', data: teacherData },
                { name: "Students", color: '#70C4CF', data: studentData }
            ],
            xaxis: { categories: months }
        };
        new ApexCharts(document.querySelector("#apexcharts-area"), options).render();
    }

    // Bar Chart
    if (document.querySelector("#bar")) {
        const optionsBar = {
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            plotOptions: { bar: { columnWidth: '55%', endingShape: 'rounded' } },
            series: [
                { name: "Boys", color: '#70C4CF', data: [boys] },
                { name: "Girls", color: '#3D5EE1', data: [girls] },
            ],
            xaxis: { categories: ['Gender Distribution'] },
        };
        new ApexCharts(document.querySelector("#bar"), optionsBar).render();
    }
});
