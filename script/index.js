const barChartOptions = {
  series: [
    {
      data: [12, 10, 18],
    },
  ],
  chart: {
    type: 'bar',
    height: 350,
    toolbar: {
      show: false,
    },
  },
  colors: ['#246dec', '#cc3c43', '#18392B'],
  plotOptions: {
    bar: {
      distributed: true,
      borderRadius: 5,
      horizontal: false,
      columnWidth: '60%',
    },
  },
  dataLabels: {
    enabled: false,
  },
  legend: {
    show: false,
  },
  xaxis: {
    categories: ['Full Grooming ', 'Basic Grooming', 'Individual Services'],
  },
  yaxis: {
    title: {
      text: '',
    },
  },
};

const barChart = new ApexCharts(
  document.querySelector('#bar-chart'),
  barChartOptions
);
barChart.render();