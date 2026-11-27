import { computed, type Ref } from "vue";

// Types
interface UserGrowth {
  date: string;
  day: string;
  job_seekers: number;
  employers: number;
}

interface JobStatus {
  status: string;
  total: number;
}

interface JobsByCategory {
  category: string;
  total: number;
}

interface MonthlyJob {
  date: string;
  month: string;
  total: number;
}

export function useDashboardCharts(
  userGrowth: Ref<UserGrowth[]>,
  jobStatus: Ref<JobStatus[]>,
  jobsByCategory: Ref<JobsByCategory[]>,
  monthlyJobs: Ref<MonthlyJob[]>,
) {
  // User Growth Line Chart
  const userGrowthChartData = computed(() => ({
    labels: userGrowth.value.map((item) => item.day),
    datasets: [
      {
        label: "Job Seekers",
        data: userGrowth.value.map((item) => item.job_seekers),
        borderColor: "#3B82F6",
        backgroundColor: "rgba(59, 130, 246, 0.1)",
        pointBackgroundColor: "#3B82F6",
        pointBorderColor: "#FFFFFF",
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        borderWidth: 2.5,
        tension: 0.4,
      },
      {
        label: "Employers",
        data: userGrowth.value.map((item) => item.employers),
        borderColor: "#10B981",
        backgroundColor: "rgba(16, 185, 129, 0.1)",
        pointBackgroundColor: "#10B981",
        pointBorderColor: "#FFFFFF",
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        borderWidth: 2.5,
        tension: 0.4,
      },
    ],
  }));

  const userGrowthChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: "top" as const,
        align: "end" as const,
        labels: {
          usePointStyle: true,
          pointStyle: "circle" as const,
          boxWidth: 8,
          padding: 18,
          color: "#64748B",
          font: { size: 12 },
        },
      },
      tooltip: {
        mode: "index" as const,
        intersect: false,
      },
    },
    interaction: {
      mode: "index" as const,
      intersect: false,
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { color: "#94A3B8", font: { size: 11 } },
      },
      y: {
        beginAtZero: true,
        ticks: { precision: 0, color: "#94A3B8", font: { size: 11 } },
        grid: { color: "#E2E8F0" },
      },
    },
  };

  // Job Status Doughnut Chart
  const jobStatusTotal = computed(() =>
    jobStatus.value.reduce((total, item) => total + Number(item.total), 0),
  );

  const jobStatusColors: Record<string, string> = {
    draft: "#94A3B8",
    pending: "#F59E0B",
    published: "#1B0981",
    approved: "#10B981",
    rejected: "#EF4444",
    closed: "#A855F7",
  };

  const jobStatusChartData = computed(() => ({
    labels: jobStatus.value.map((item) => item.status),
    datasets: [
      {
        data: jobStatus.value.map((item) => Number(item.total)),
        backgroundColor: jobStatus.value.map(
          (item) => jobStatusColors[item.status.toLowerCase()] ?? "#64748B",
        ),
        borderWidth: 0,
        hoverOffset: 5,
      },
    ],
  }));

  const jobStatusChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: "65%",
    plugins: {
      legend: { display: false },
      tooltip: { enabled: true },
    },
  };

  function getJobStatusPercentage(total: number): number {
    if (jobStatusTotal.value === 0) return 0;

    return Math.round((total / jobStatusTotal.value) * 100);
  }

  // Jobs By Category Pie Chart
  const jobsByCategoryChartData = computed(() => ({
    labels: jobsByCategory.value.map((item) => item.category),
    datasets: [
      {
        label: "Jobs",
        data: jobsByCategory.value.map((item) => Number(item.total)),
        backgroundColor: [
          "#3B82F6",
          "#10B981",
          "#F59E0B",
          "#8B5CF6",
          "#EC4899",
          "#06B6D4",
          "#F97316",
          "#64748B",
        ],
        borderWidth: 0,
        hoverOffset: 5,
      },
    ],
  }));

  const jobsByCategoryChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: "bottom" as const,
        labels: {
          usePointStyle: true,
          pointStyle: "circle" as const,
          padding: 15,
          color: "#64748B",
          font: { size: 11 },

          // Custom Legend Labels: Category Name + Job Count
          generateLabels(chart: any) {
            const dataset = chart.data.datasets[0];

            return chart.data.labels.map(
              (label: string, index: number) => ({
                text: `${label} (${dataset.data[index]})`,
                fillStyle: dataset.backgroundColor[index],
                strokeStyle: dataset.backgroundColor[index],
                lineWidth: 0,
                hidden: false,
                index,
                pointStyle: "circle",
              }),
            );
          },
        },
      },
      tooltip: { enabled: true },
    },
  };

  // Monthly Jobs Bar Chart
  const monthlyJobsChartData = computed(() => ({
    labels: monthlyJobs.value.map((item) => item.month),
    datasets: [
      {
        label: "Jobs Posted",
        data: monthlyJobs.value.map((item) => Number(item.total)),
        backgroundColor: "#3B82F6",
        borderRadius: 6,
        maxBarThickness: 42,
      },
    ],
  }));

  const monthlyJobsChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: { enabled: true },
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { color: "#94A3B8" },
      },
      y: {
        beginAtZero: true,
        ticks: { precision: 0, color: "#94A3B8" },
        grid: { color: "#E2E8F0" },
      },
    },
  };

  return {
    userGrowthChartData,
    userGrowthChartOptions,

    jobStatusTotal,
    jobStatusColors,
    jobStatusChartData,
    jobStatusChartOptions,
    getJobStatusPercentage,

    jobsByCategoryChartData,
    jobsByCategoryChartOptions,

    monthlyJobsChartData,
    monthlyJobsChartOptions,
  };
}
